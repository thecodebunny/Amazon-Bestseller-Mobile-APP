<?php

namespace Thecodebunny\PWA\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

/**
 * Event service provider
 *
 * @author Thecodebunny Software Pvt. Ltd. <support@thecodebunny.com>
 * @copyright 2019 Thecodebunny Software Pvt Ltd (http://www.thecodebunny.com)
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('thecodebunny.admin.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('pwa::admin.layouts.style');
        });

        Event::listen('thecodebunny.shop.layout.head', 'Thecodebunny\PWA\Listeners\PWAListeners@redirectToPWA');

        Event::listen('core.configuration.save.after', 'Thecodebunny\PWA\Listeners\CoreConfig@generateManifestFile');

        Event::listen([
            'thecodebunny.admin.catalog.category.edit_form_accordian.general.controls.after',
            'thecodebunny.admin.catalog.category.create_form_accordian.general.controls.after',
        ], function($viewRenderEventManager) {
                $viewRenderEventManager->addTemplate(
                    'pwa::admin.catelog.categories.pwa'
                );
            }
        );

        Event::listen([
            'catalog.category.create.after',
            'catalog.category.update.after',
        ], 'Thecodebunny\PWA\Helpers\AdminHelper@storeCategoryIcon');
    }
}
