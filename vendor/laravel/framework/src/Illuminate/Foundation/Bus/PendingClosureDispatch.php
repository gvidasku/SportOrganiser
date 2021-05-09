<?php

namespace Illuminate\Foundation\Bus;

use Closure;

class PendingClosureDispatch extends PendingDispatch
{
    /**
     * Add a callback to be executed if the sportevent fails.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function catch(Closure $callback)
    {
        $this->sportevent->onFailure($callback);

        return $this;
    }
}
