<?php

namespace Thecodebunny\PWA\Http\Controllers\Shop;

use Thecodebunny\API\Http\Controllers\Shop\Controller;
use Thecodebunny\Checkout\Repositories\CartRepository;
use Thecodebunny\Checkout\Repositories\CartItemRepository;
use Thecodebunny\Shipping\Facades\Shipping;
use Thecodebunny\Payment\Facades\Payment;
use Thecodebunny\PWA\Http\Resources\Checkout\Cart as CartResource;
use Thecodebunny\API\Http\Resources\Checkout\CartShippingRate as CartShippingRateResource;
use Thecodebunny\PWA\Http\Resources\Sales\Order as OrderResource;
use Thecodebunny\Checkout\Http\Requests\CustomerAddressForm;
use Thecodebunny\Sales\Repositories\OrderRepository;
use Cart;

/**
 * Checkout controller
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class CheckoutController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * CartRepository object
     *
     * @var Object
     */
    protected $cartRepository;

    /**
     * CartItemRepository object
     *
     * @var Object
     */
    protected $cartItemRepository;

    /**
     * Controller instance
     *
     * @param Thecodebunny\Checkout\Repositories\CartRepository     $cartRepository
     * @param Thecodebunny\Checkout\Repositories\CartItemRepository $cartItemRepository
     * @param Thecodebunny\Sales\Repositories\OrderRepository       $orderRepository
     */
    public function __construct(
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        OrderRepository $orderRepository
    ) {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        
        // $this->middleware('auth:' . $this->guard);
        
        $this->_config = request('_config');

        $this->cartRepository = $cartRepository;

        $this->cartItemRepository = $cartItemRepository;

        $this->orderRepository = $orderRepository;
    }

    /**
     * Saves customer address.
     *
     * @param  \Thecodebunny\Checkout\Http\Requests\CustomerAddressForm $request
     * @return \Illuminate\Http\Response
    */
    public function saveAddress(CustomerAddressForm $request)
    {
        $data = request()->all();
        
        $data['billing']['address1'] = implode(PHP_EOL, array_filter($data['billing']['address1']));
        $data['shipping']['address1'] = implode(PHP_EOL, array_filter($data['shipping']['address1']));

        if (isset($data['billing']['id']) && str_contains($data['billing']['id'], 'address_')) {
            unset($data['billing']['id']);
            unset($data['billing']['address_id']);
        }

        if (isset($data['shipping']['id']) && str_contains($data['shipping']['id'], 'address_')) {
            unset($data['shipping']['id']);
            unset($data['shipping']['address_id']);
        }

        if (Cart::hasError() || ! Cart::saveCustomerAddress($data) || ! Shipping::collectRates())
            abort(400);

        $rates = [];

        foreach (Shipping::getGroupedAllShippingRates() as $code => $shippingMethod) {
            $rates[] = [
                'carrier_title' => $shippingMethod['carrier_title'],
                'rates' => CartShippingRateResource::collection(collect($shippingMethod['rates']))
            ];
        }

        $cart = Cart::getCart();

        if ($cart->haveStockableItems()) {
            $data = [
                'rates'     => $rates,
                'nextStep'  => "shipping",
            ];
        } else {
            $data = [
                'nextStep'  => "payment",
                'methods'   => Payment::getPaymentMethods(),
            ];
        }

        $data['cart'] = new CartResource(Cart::getCart());


        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Saves shipping method.
     *
     * @return \Illuminate\Http\Response
    */
    public function saveShipping()
    {
        $shippingMethod = request()->get('shipping_method');

        if (Cart::hasError() || !$shippingMethod || ! Cart::saveShippingMethod($shippingMethod))
            abort(400);

        Cart::collectTotals();

        return response()->json([
            'data' => [
                'methods' => Payment::getPaymentMethods(),
                'cart' => new CartResource(Cart::getCart())
            ]
        ]);
    }

    /**
     * Check Guest Checkout.
     *
     * @return \Illuminate\Http\Response
    */
    public function isGuestCheckout()
    {
        $cart = Cart::getCart();
        
        if (! auth()->guard('customer')->check()
            && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
            return response()->json([
                'data' => [
                    'status' => false
                ]
            ]);
        }

        if (! auth()->guard('customer')->check() && ! $cart->hasGuestCheckoutItems()) {
            return response()->json([
                'data' => [
                    'status' => false
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'status' => true
            ]
        ]);
    }

    /**
     * Saves payment method.
     *
     * @return \Illuminate\Http\Response
    */
    public function savePayment()
    {
        $payment = request()->get('payment');

        if (Cart::hasError() || ! $payment || ! Cart::savePaymentMethod($payment))
            abort(400);

        return response()->json([
            'data' => [
                'cart' => new CartResource(Cart::getCart())
            ]
        ]);
    }

    /**
     * Saves order.
     *
     * @return \Illuminate\Http\Response
    */
    public function saveOrder()
    {
        if (Cart::hasError()) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $this->validateOrder();

        $cart = Cart::getCart();

        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return response()->json([
                'success'      => true,
                'redirect_url' => $redirectUrl,
            ]);
        }

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return response()->json([
            'success'   => true,
            'order'     => [
                "id" => $order->increment_id
            ],
        ]);
    }

    /**
     * Validate order before creation
     *
     * @return mixed
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            throw new \Exception(trans('Please check shipping address.'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(trans('Please check billing address.'));
        }

        if ($cart->haveStockableItems() && ! $cart->selected_shipping_rate) {
            throw new \Exception(trans('Please specify shipping method.'));
        }

        if (! $cart->payment) {
            throw new \Exception(trans('Please specify payment method.'));
        }
    }
}