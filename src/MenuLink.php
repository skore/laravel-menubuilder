<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;

class MenuLink implements Arrayable
{
    use Makeable;
    use Macroable;

    /**
     * @var mixed
     */
    protected $title;

    /**
     * @var mixed
     */
    protected $uri;

    /**
     * @var mixed
     */
    protected $params;

    /**
     * @var mixed
     */
    protected $icon;

    /**
     * @var mixed
     */
    protected $meta;

    /**
     * Instantiate menu link class.
     *
     * @param mixed $title
     * @param mixed $uri
     * @param array $params
     * @param array $meta
     *
     * @return void
     */
    public function __construct($title, $uri, $params = [], $meta = [])
    {
        $this->title = $title;
        $this->uri = $uri;
        $this->params = $params;
        $this->meta = $meta;
    }

    /**
     * Set icon for the link.
     *
     * @param mixed $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param mixed|null $key
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return $this
     */
    public function asTranslated($key = null)
    {
        $this->title = __($key ?: $this->title);

        return $this;
    }

    /**
     * @param bool $condition
     *
     * @return $this
     */
    public function disable($condition = true)
    {
        $this->meta['disabled'] = $condition;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'route' => $this->buildRoute(),
            'icon'  => $this->icon,
            'meta'  => (object) $this->meta,
        ];
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    protected function buildRoute()
    {
        return route($this->path, $this->params);
    }
}
