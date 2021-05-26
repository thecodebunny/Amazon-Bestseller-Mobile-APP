<?php

namespace Thecodebunny\PWA\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Thecodebunny\API\Http\Controllers\Shop\Controller;
use Thecodebunny\Product\Repositories\SearchRepository;

/**
 * Review controller
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class ImageSearchController extends Controller
{
    /**
     * SearchRepository object
     *
     * @var \Thecodebunny\Core\Repositories\SearchRepository
    */
    protected $searchRepository;

    /**
     * Controller instance
     *
     * @param Thecodebunny\Product\Repositories\SearchRepository $searchRepository
     */
    public function __construct(SearchRepository $searchRepository)
    {
        $this->searchRepository = $searchRepository;
    }

    /**
     * Upload image for product search with machine learning
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        $url = $this->searchRepository->uploadSearchImage(request()->all());

        return $url; 
    }
}