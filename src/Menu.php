<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;

abstract class Menu implements Arrayable
{
    use Makeable;
    use Macroable;

    /**
     * @var array
     */
    public static $routes = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * Instantiate menu class.
     *
     * @return array
     */
    public function __construct()
    {
        if (Manager::inInertia()) {
            return inertia()->share(
                config('menus.inertia.key_prefix').'.'.$this->getUri(),
                $this->toArray()
            );
        }
    }

    /**
     * Get menu unique identifier.
     *
     * @return string
     */
    protected function getUri()
    {
        return Str::snake(class_basename($this::class));
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
    protected function addLink($title, $uri, $params = [], $meta = [])
    {
        return new MenuLink($title, $uri, $params, $meta);
    }

    /**
     * Add new menu group.
     *
     * @param mixed $title
     * @param array $items
     *
     * @return \SkoreLabs\LaravelMenuBuilder\MenuGroup
     */
    public function addGroup($title, $items = [])
    {
        return new MenuGroup($title, $items);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return Collection::make($this->items())->flatMap->toArray()->all();
    }
}
