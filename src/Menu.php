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
     * @var array
     */
    protected $items = [];

    /**
     * Instantiate menu class.
     *
     * @return array
     */
    public function __construct($items = [])
    {
        $this->items = $items ?: $this->items();

        if (MenuBuilder::inInertia() && $this->view()) {
            return inertia()->share(config('menus.inertia.key_prefix').'.'.$this->getUri(), $this->items);
        }

        return $this->items;
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
     * @return $this
     */
    protected function addLink($title, $uri, $params = [], $meta = [])
    {
        $this->items[] = new MenuLink($title, $uri, $params, $meta);

        return $this;
    }

    /**
     * Add new menu group
     *
     * @param mixed $title
     * @param array $items
     * @return $this
     */
    public function addGroup($title, $items = [])
    {
        $this->items[] = new MenuGroup($title, $items);

        return $this;
    }
}
