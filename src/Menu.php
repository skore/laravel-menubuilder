<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;

abstract class Menu
{
    use Makeable;
    use Macroable;

    /**
     * Instantiate menu class.
     *
     * @return array
     */
    public function __construct()
    {
        $response = $this->items();

        if (MenuBuilder::inInertia() && $this->view()) {
            return inertia()->share(config('menus.inertia.key_prefix').'.'.$this->getUri(), $response);
        }

        return $response;
    }

    /**
     * Show menu at view.
     *
     * @return bool
     */
    protected function view()
    {
        return true;
    }

    /**
     * Get menu unique identifier.
     *
     * @return string
     */
    protected function getUri()
    {
        return Str::snake(class_basename(self::class));
    }

    /**
     * Get menu content items.
     *
     * @return array
     */
    protected function items()
    {
        return [];
    }

    /**
     * Get new menu link class instance.
     *
     * @param mixed $title
     * @param mixed $uri
     * @param array $params
     * @param array $meta
     *
     * @return \SkoreLabs\LaravelMenuBuilder\MenuLink
     */
    protected function link($title, $uri, $params = [], $meta = [])
    {
        return new MenuLink($title, $uri, $params, $meta);
    }
}
