<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SkoreLabs\LaravelMenuBuilder\Console\MenuMakeCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/menus.php' => config_path('menus.php'),
            ], 'menubuilder-config');
        }

        MenuBuilder::menusIn(config('menus.path', ''));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MenuMakeCommand::class,
        ]);
    }
}
