<?php

namespace Illuminate\Contracts\Queue;

interface Monitor
{
    /**
     * Register a callback to be executed on every iteration through the queue loop.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function looping($callback);

    /**
     * Register a callback to be executed when a sportevent fails after the maximum amount of retries.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function failing($callback);

    /**
     * Register a callback to be executed when a daemon queue is stopping.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function stopping($callback);
}
