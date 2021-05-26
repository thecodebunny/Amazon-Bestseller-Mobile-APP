<?php

namespace Thecodebunny\PWA\Helpers;

use Thecodebunny\Attribute\Repositories\AttributeOptionRepository as AttributeOption;
use Thecodebunny\Product\Helpers\ProductImage;
use Thecodebunny\PWA\Helpers\Price;
use Thecodebunny\Product\Models\Product;
use Thecodebunny\Product\Helpers\ConfigurableOption;

class PwaConfigurableOption extends ConfigurableOption
{
    /**
     * AttributeOptionRepository object
     *
     * @var array
     */
    protected $attributeOption;

    /**
     * ProductImage object
     *
     * @var array
     */
    protected $productImage;

    /**
     * Price object
     *
     * @var array
     */
    protected $price;

    /**
     * Create a new controller instance.
     *
     * @param  Thecodebunny\Attribute\Repositories\AttributeOptionRepository $attributeOption
     * @param  Thecodebunny\Product\Helpers\ProductImage                     $productImage
     * @param  Thecodebunny\Product\Helpers\Price                            $price
     * @return void
     */
    public function __construct(
        AttributeOption $attributeOption,
        ProductImage $productImage,
        Price $price
    )
    {
        $this->attributeOption = $attributeOption;

        $this->productImage = $productImage;

        $this->price = $price;
    }

    /**
     * Returns the allowed variants JSON
     *
     * @param Product $product
     * @return float
     */
    public function getConfigurationConfig($product)
    {
        $options = $this->getOptions($product, $this->getAllowedProducts($product));

        $config = [
            'attributes' => $this->getAttributesData($product, $options),
            'index' => isset($options['index']) ? $options['index'] : [],
            'regular_price' => [
                'formated_price' => core()->currency($this->price->getMinimalPrice($product)),
                'price' => $this->price->getMinimalPrice($product)
            ],
            'variant_prices' => $this->getVariantPrices($product),
            'variant_images' => $this->getVariantImages($product),
            'chooseText' => trans('shop::app.products.choose-option')
        ];

        return $config;
    }

    /**
     * Get product prices for configurable variations
     *
     * @param Product $product
     * @return array
     */
    protected function getVariantPrices($product)
    {
        $prices = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Thecodebunny\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $prices[$variantId] = [
                'regular_price' => [
                    'formated_price' => core()->currency($variant->price),
                    'price' => $variant->price
                ],
                'final_price' => [
                    'formated_price' => core()->currency($this->price->getMinimalPrice($variant)),
                    'price' => $this->price->getMinimalPrice($variant)
                ]
            ];
        }

        return $prices;
    }
}