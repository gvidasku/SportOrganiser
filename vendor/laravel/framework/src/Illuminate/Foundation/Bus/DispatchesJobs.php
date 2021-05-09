<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Contracts\Bus\Dispatcher;

trait Dispatchessportevents
{
    /**
     * Dispatch a sportevent to its appropriate handler.
     *
     * @param  mixed  $sportevent
     * @return mixed
     */
    protected function dispatch($sportevent)
    {
        return app(Dispatcher::class)->dispatch($sportevent);
    }

    /**
     * Dispatch a sportevent to its appropriate handler in the current process.
     *
     * @param  mixed  $sportevent
     * @return mixed
     */
    public function dispatchNow($sportevent)
    {
        return app(Dispatcher::class)->dispatchNow($sportevent);
    }
}
