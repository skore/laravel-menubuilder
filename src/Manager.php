<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Manager
{
    /**
     * @var array
     */
    public static $menus = [];

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
        $namespace = App::getNamespace();

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
            Collection::make($menus)->sort()->mapWithKeys(fn ($class) => [$class => $class::$routes])->all()
        );
    }

    /**
     * Check if named integration is enabled.
     *
     * @param mixed $integration
     *
     * @return bool
     */
    public static function enabled($integration)
    {
        return config("menus.integrations.${integration}", false);
    }

    /**
     * Inject data into all enabled integrations.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $key
     * @param mixed                    $data
     *
     * @return void
     */
    public static function dataInject(Request $request, $key, $data)
    {
        if (static::enabled('inertia') && $request->inertia()) {
            inertia()->share($key, array_merge(inertia()->getShared($key), $data));
        }

        if (static::enabled('json') && $request->wantsJson()) {
            // TODO:
        }

        if (static::enabled('blade') && !$request->wantsJson()) {
            Session::flash(static::prefix(), array_merge(Session::get(static::prefix(), []), $data));
        }
    }

    /**
     * Get the prefix for the package interactions.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return mixed
     */
    public static function prefix()
    {
        return config('menus.key_prefix', 'menuBuilder');
    }
}
