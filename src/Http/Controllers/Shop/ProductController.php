<?php

namespace Thecodebunny\PWA\Http\Controllers\Shop;

use Thecodebunny\API\Http\Controllers\Shop\Controller;
use Thecodebunny\Product\Repositories\ProductRepository;
use Thecodebunny\Product\Repositories\ProductFlatRepository;
use Thecodebunny\PWA\Http\Resources\Catalog\Product as ProductResource;

/**
 * Product controller
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var array
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Thecodebunny\Product\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = request()->all();

        if (isset ($data['new'])) {
            $result = app(ProductFlatRepository::class)->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());
    
                $locale = request()->get('locale') ?: app()->getLocale();
    
                return $query->distinct()
                             ->addSelect('product_flat.*')
                             ->where('product_flat.status', 1)
                             ->where('product_flat.visible_individually', 1)
                             ->where('product_flat.new', 1)
                             ->where('product_flat.channel', $channel)
                             ->where('product_flat.locale', $locale)
                             ->orderBy('product_id', 'desc');
            })->paginate($data['count'] ?? '4');
    
            $result;
        } else if (isset($data['featured'])) {
            $result = app(ProductFlatRepository::class)->scopeQuery(function($query) {
                $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());
    
                $locale = request()->get('locale') ?: app()->getLocale();
    
                return $query->distinct()
                             ->addSelect('product_flat.*')
                             ->where('product_flat.status', 1)
                             ->where('product_flat.visible_individually', 1)
                             ->where('product_flat.featured', 1)
                             ->where('product_flat.channel', $channel)
                             ->where('product_flat.locale', $locale)
                             ->orderBy('product_id', 'desc');
            })->paginate($data['count'] ?? '4');
    
            $result;
        } else {
            $result = $this->productRepository->getAll(request()->input('category_id'));
        }

        return ProductResource::collection($result);
    }

    /**
     * Returns a individual resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        return new ProductResource(
                $this->productRepository->findOrFail($id)
            );
    }

    /**
     * Returns product's additional information.
     *
     * @return \Illuminate\Http\Response
     */
    public function configurableConfig($id)
    {
        return response()->json([
                'data' => app('Thecodebunny\PWA\Helpers\PwaConfigurableOption')->getConfigurationConfig($this->productRepository->findOrFail($id))
            ]);
    }
}
