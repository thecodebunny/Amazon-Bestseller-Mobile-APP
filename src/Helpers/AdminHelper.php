<?php

namespace Thecodebunny\PWA\Helpers;

use Illuminate\Support\Facades\Storage;
use Thecodebunny\Category\Repositories\CategoryRepository;

class AdminHelper
{
    /**
     * CategoryRepository object
     *
     * @var \Thecodebunny\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Create a new helper instance.
     *
     * @param  \Thecodebunny\Category\Repositories\CategoryRepository  $categoryRepository
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository =  $categoryRepository;
    }

    /**
     * @param  \Thecodebunny\Category\Contracts\Category  $category
     * @return \Thecodebunny\Category\Contracts\Category
     */
    public function storeCategoryIcon($category)
    {
        $data = request()->all();

        if (! $category instanceof \Thecodebunny\Category\Contracts\Category) {
            $category = $this->categoryRepository->findOrFail($category);
        }

        $category->category_product_in_pwa = ($data['add_in_pwa'] ?? 0) == "1" ? 1 : 0;
        $category->save();

        return $category;
    }
}