<?php

namespace Thecodebunny\PWA\Http\Controllers;

use Thecodebunny\Checkout\Facades\Cart;
use Thecodebunny\Sales\Repositories\OrderRepository;

/**
 * Paypal Standard controller
 *
 * @author    Jitendra Singh <jitendra@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class StandardController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Thecodebunny\Attribute\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Cancel payment from paypal.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', 'Paypal payment has been canceled.');

        return redirect('/mobile/checkout/cart');
    }

    /**
     * Success payment
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        $url = '/mobile/checkout/success'.'/'.$order->id;

        return redirect($url);
    }
}