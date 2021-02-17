<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Contracts\Arrayable;
use SkoreLabs\LaravelMenuBuilder\Traits\IsConditionallyRendered;
use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;

class MenuGroup implements Responsable, Arrayable
{
    use Makeable;
    use Macroable;
    use IsConditionallyRendered;

    /**
     * @var mixed
     */
    protected $title;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $items;

    /**
     * @param array $items
     *
     * @return void
     */
    public function __construct($title = 'default', array $items = [])
    {
        $this->title = $title;
        $this->items = Collection::make($items);
    }

    /**
     * Add menu link to the group.
     *
     * @param mixed $title
     * @param mixed $uri
     * @param mixed $params
     * @param bool  $permission
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return $this
     */
    public function addLink($title, $uri, $params = [], $model = null, $meta = [])
    {
        $this->items->add(
            MenuLink::make(...func_get_args())
        );

        return $this;
    }

    /**
     * Add multiple items to the group.
     *
     * @param mixed $item
     *
     * @return $this
     */
    public function add($items)
    {
        $this->items->merge(Arr::wrap($items));

        return $this;
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
        return [
            $this->title => $this->items
                ->each
                ->authorizedToSee($request)
                ->map
                ->toArray()
        ];
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Response::json($this->toArray($request));
    }
}
