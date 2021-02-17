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
     * @var array
     */
    protected static $active = [];

    /**
     * Activate the given menus.
     *
     * @param array $menus
     *
     * @return static
     */
    public static function activate(array $menus)
    {
        static::$active = array_merge_recursive(static::$active, $menus);

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

        static::$menus = Collection::make($menus)
            ->sort()
            ->mapWithKeys(fn ($class) => [$class => $class::$routes])
            ->all();
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
    public static function dataInject(Request $request)
    {
        if (static::enabled('json') && $request->wantsJson()) {
            // TODO:
        }

        if (static::enabled('blade') && !$request->wantsJson()) {
            Session::flash(static::prefix(), static::$active);
        }

        if (static::enabled('inertia') && !$request->wantsJson()) {
            inertia()->share(static::prefix(), static::$active);
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
