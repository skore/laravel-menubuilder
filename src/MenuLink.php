<?php

namespace SkoreLabs\LaravelMenuBuilder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;
use SkoreLabs\LaravelMenuBuilder\Traits\HasMeta;
use SkoreLabs\LaravelMenuBuilder\Traits\IsConditionallyRendered;
use SkoreLabs\LaravelMenuBuilder\Traits\Makeable;

class MenuLink implements Arrayable
{
    use Makeable;
    use Macroable;
    use HasMeta;
    use IsConditionallyRendered;

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
     * Instantiate menu link class.
     *
     * @param mixed $title
     * @param mixed $uri
     * @param array $params
     *
     * @return void
     */
    public function __construct($title, $uri, $params = [])
    {
        $this->title = $title;
        $this->uri = $uri;
        $this->params = $params;
    }

    /**
     * Set icon for the menu link.
     *
     * @param mixed $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->meta['icon'] = $icon;

        return $this;
    }

    /**
     * Translate link with Laravel built-in localization.
     *
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
     * Disable menu link (appearance).
     *
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
     * Compose Laravel app route.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return string
     */
    protected function route()
    {
        return route($this->uri, $this->params);
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
            'route' => $this->route(),
            'meta'  => (object) $this->meta,
        ];
    }
}
