<?php

namespace Thecodebunny\PWA\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Thecodebunny\PWA\Models\PWALayout::class,
        \Thecodebunny\PWA\Models\PushNotification::class,
    ];
}