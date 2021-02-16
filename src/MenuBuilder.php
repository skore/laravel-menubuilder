<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Inertia\ServiceProvider;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class MenuBuilder
{
    /**
     * @var array
     */
    protected static $menus;

    /**
     * Register the given menus.
     *
     * @param array $menus
     *
     * @return static
     */
    public static function menus(array $menus)
    {
        static::$menus = array_merge(static::$menus, $menus);

        return new static();
    }

    /**
     * Register all of the menu classes in the given directory.
     *
     * @param string $directory
     *
     * @return void
     */
    public static function menusIn($directory)
    {
        $namespace = app()->getNamespace();

        $menus = [];

        foreach ((new Finder())->in($directory)->files() as $menu) {
            $menu = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($menu->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (
                is_subclass_of($menu, Menu::class) &&
                !(new ReflectionClass($menu))->isAbstract()
            ) {
                $menus[] = $menu;
            }
        }

        static::menus(
            collect($menus)->sort()->all()
        );
    }

    /**
     * Check if app is under InertiaJS.
     *
     * @return bool
     */
    public static function inInertia()
    {
        return (function_exists('inertia')
            ?: App::bound(ServiceProvider::class)
            ?: Request::hasMacro('inertia')
        ) && Request::inertia();
    }
}
