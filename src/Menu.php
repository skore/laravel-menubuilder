<?php

namespace SkoreLabs\LaravelMenuBuilder;

use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Contracts\Arrayable;
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
     * Instantiate menu class.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $resolved = $this->resolve($request);

        Manager::dataInject($request, $this->identifier(), $resolved)
            && $resolved;
    }

    /**
     * Menu resolver function, instantiate and cache it if specified.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function resolve(Request $request)
    {
        $resolver = function () use ($request) {
            return $this->toArray($request);
        };

        if ($cacheFor = $this->cacheFor()) {
            $cacheFor = is_numeric($cacheFor) ? new DateInterval(sprintf('PT%dS', $cacheFor * 60)) : $cacheFor;

            return Cache::remember(
                $this->cacheKey($request),
                $cacheFor,
                $resolver
            );
        }

        return $resolver();
    }

    /**
     * Get menu content items.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function items(Request $request)
    {
        return [];
    }

    /**
     * Get menu unique identifier.
     *
     * @return string
     */
    protected function identifier()
    {
        return Str::snake(class_basename($this::class));
    }

    /**
     * Cache this menu for the specified time period.
     *
     * @return \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        //
    }

    /**
     * Get the appropriate cache key for the menu.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function cacheKey(Request $request)
    {
        return config('menus.key_prefix', 'menus') . '.' . $this->identifier();
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
    public function addGroup($title = null, array $items = [])
    {
        return new MenuGroup($title, $items);
    }

    /**
     * Get the instance as an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray(Request $request)
    {
        return Collection::make($this->items($request))
            ->flatMap
            ->toArray($request)
            ->all();
    }
}
