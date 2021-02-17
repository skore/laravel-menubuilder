<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;
use SkoreLabs\LaravelMenuBuilder\Console\MenuMakeCommand;
use Symfony\Component\Finder\Finder;

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

        $menusPath = config('menus.path', '');

        if ((new Finder())->in($menusPath)->count() > 0) {
            Manager::menusIn($menusPath);
        }
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

        if (Manager::enabled('blade')) {
            Blade::directive('menus', function () {
                return Session::get(Manager::prefix());
            });
        }

        Event::listen(RouteMatched::class, static function (RouteMatched $event) {
            /** @var \SkoreLabs\LaravelMenuBuilder\Menu $menu */
            foreach (Manager::$menus as $menu => $routes) {
                Str::is($routes, $event->route->getName())
                    ? $menu::make($event->request)
                    : null;
            }
        });
    }
}
