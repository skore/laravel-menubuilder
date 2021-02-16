<?php

namespace Skorelabs\LaravelMenuBuilder\Traits;

trait Makeable
{
    /**
     * Create a new resource instance.
     *
     * @param mixed ...$parameters
     *
     * @return static
     */
    public static function make(...$parameters)
    {
        return new static(...$parameters);
    }
}
