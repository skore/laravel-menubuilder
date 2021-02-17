<?php

namespace SkoreLabs\LaravelMenuBuilder\Contracts;

use Illuminate\Http\Request;

interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request);
}
