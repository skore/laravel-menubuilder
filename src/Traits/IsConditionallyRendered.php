<?php

namespace SkoreLabs\LaravelMenuBuilder\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait IsConditionallyRendered
{
    /**
     * The callback used to authorize viewing the filter.
     *
     * @var \Closure|null
     */
    protected $seeCallback;

    /**
     * Determine if the filter should be available for the given request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * Add item when the condition is truth.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function when(\Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
    }

    /**
     * Add item when the user has permissions.
     *
     * @param mixed $policy
     *
     * @return void
     */
    public function whenCan($ability, $arguments = [])
    {
        $arguments = Arr::wrap($arguments);

        return $this->when(function ($request) use ($ability, $arguments) {
            return $request->user()->can($ability, $arguments);
        });
    }
}
