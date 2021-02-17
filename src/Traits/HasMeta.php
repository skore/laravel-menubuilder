<?php

namespace SkoreLabs\LaravelMenuBuilder\Traits;

trait HasMeta
{
    /**
     * @var mixed
     */
    protected $meta = [];

    /**
     * Add meta to the link output.
     *
     * @param array $meta
     *
     * @return $this
     */
    public function withMeta(array $meta = [])
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }
}
