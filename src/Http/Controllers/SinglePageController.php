<?php

namespace Thecodebunny\PWA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jenssegers\Agent\Agent;

/**
 * Home page controller
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2018 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class SinglePageController extends Controller
{
    /**
     * loads the home page for the storefront
     */
    public function home()
    {
        $agent = new Agent();

        if ($agent->isMobile() || $agent->isTablet())
            return redirect('/mobile');

        $currentChannel = core()->getCurrentChannel();

        $sliderData = app('Thecodebunny\Core\Repositories\SliderRepository')->findByField('channel_id', $currentChannel->id)->toArray();

        return view('shop::home.index', compact('sliderData'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $parsedUrl = parse_url(config('app.url'));

        $urlPath = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

        return view('pwa::master', compact('urlPath'));
    }
}
