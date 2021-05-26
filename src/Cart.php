<?php

namespace Thecodebunny\PWA;

use Thecodebunny\Checkout\Repositories\CartRepository;
use Thecodebunny\Checkout\Repositories\CartItemRepository;
use Thecodebunny\Checkout\Repositories\CartAddressRepository;
use Thecodebunny\Customer\Repositories\CustomerRepository;
use Thecodebunny\Product\Repositories\ProductRepository;
use Thecodebunny\Tax\Repositories\TaxCategoryRepository;
use Thecodebunny\Checkout\Models\CartPayment;
use Thecodebunny\Customer\Repositories\WishlistRepository;
use Thecodebunny\Customer\Repositories\CustomerAddressRepository;
use Thecodebunny\PWA\Helpers\Price;
use Thecodebunny\Checkout\Cart as BaseCart;

/**
 * Class Cart.
 *
 */
class Cart extends BaseCart
{

    /**
     * CartRepository instance
     *
     * @var mixed
     */
    protected $cart;

    /**
     * CartItemRepository instance
     *
     * @var mixed
     */
    protected $cartItem;

    /**
     * CustomerRepository instance
     *
     * @var mixed
     */
    protected $customer;

    /**
     * CartAddressRepository instance
     *
     * @var mixed
     */
    protected $cartAddress;

    /**
     * ProductRepository instance
     *
     * @var mixed
     */
    protected $product;

    /**
     * TaxCategoryRepository instance
     *
     * @var mixed
     */
    protected $taxCategory;

    /**
     * WishlistRepository instance
     *
     * @var mixed
     */
    protected $wishlist;

    /**
     * CustomerAddressRepository instance
     *
     * @var mixed
     */
    protected $customerAddress;

    /**
     * Suppress the session flash messages
    */
    protected $suppressFlash;

    /**
     * Product price helper instance
    */
    protected $price;

    /**
     * Create a new controller instance.
     *
     * @param  Thecodebunny\Checkout\Repositories\CartRepository  $cart
     * @param  Thecodebunny\Checkout\Repositories\CartItemRepository  $cartItem
     * @param  Thecodebunny\Checkout\Repositories\CartAddressRepository  $cartAddress
     * @param  Thecodebunny\Customer\Repositories\CustomerRepository  $customer
     * @param  Thecodebunny\Product\Repositories\ProductRepository  $product
     * @param  Thecodebunny\Product\Repositories\TaxCategoryRepository  $taxCategory
     * @param  Thecodebunny\Product\Repositories\CustomerAddressRepository  $customerAddress
     * @param  Thecodebunny\Product\Repositories\CustomerAddressRepository  $customerAddress
     * @param  Thecodebunny\Discount\Repositories\CartRuleRepository  $cartRule
     * @param  Thecodebunny\Helpers\Discount  $discount
     * @return void
     */
    public function __construct(
        CartRepository $cart,
        CartItemRepository $cartItem,
        CartAddressRepository $cartAddress,
        CustomerRepository $customer,
        ProductRepository $product,
        TaxCategoryRepository $taxCategory,
        WishlistRepository $wishlist,
        CustomerAddressRepository $customerAddress,
        Price $price
    )
    {
        parent::__construct(
            $cart,
            $cartItem,
            $cartAddress,
            $product,
            $taxCategory,
            $wishlist,
            $customerAddress
        );

        $this->product = $product;
    }
}
