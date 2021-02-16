<?php

namespace SkoreLabs\LaravelMenuBuilder;

use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Traits\Macroable;

class MenuGroup implements Responsable
{
    use Makeable;
    use Macroable;

    /**
     * @var mixed
     */
    protected $title;

    /**
     * @var Illuminate\Support\Collection
     */
    protected $items;

    /**
     *
     * @param array $items
     * @return void
     */
    public function __construct($title = 'default', array $items = [])
    {
        $this->title = $title;

        $this->items = Collection::make($items);
    }

    /**
     *
     * @param mixed $title
     * @param mixed $uri
     * @param mixed $params
     * @param bool $permission
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function addLink($title, $uri, $params = [], $model = null, $meta = [])
    {
        $this->items->add(
            MenuLink::make(...func_get_args())
        );

        return $this;
    }

    /**
     *
     * @param mixed $item
     * @return $this
     */
    public function add($item)
    {
        $this->items->add($item);

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return Response::json($this->items->toArray());
    }
}
