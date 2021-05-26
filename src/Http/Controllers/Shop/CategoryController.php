<?php

namespace Thecodebunny\PWA\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Thecodebunny\API\Http\Controllers\Shop\Controller;
use Thecodebunny\Category\Repositories\CategoryRepository;
use Thecodebunny\PWA\Http\Resources\Catalog\Category as CategoryResource;

class CategoryController extends Controller
{
    /**
     * CategoryRepository object
     *
     * @var \Thecodebunny\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Thecodebunny\Category\Repositories\CategoryRepository  $categoryRepository
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CategoryResource::collection(
            $this->categoryRepository->getVisibleCategoryTree(request()->input('parent_id'))
        );
    }
}
