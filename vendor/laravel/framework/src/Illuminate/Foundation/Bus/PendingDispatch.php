<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Contracts\Bus\Dispatcher;

class PendingDispatch
{
    /**
     * The sportevent.
     *
     * @var mixed
     */
    protected $sportevent;

    /**
     * Indicates if the sportevent should be dispatched immediately after sending the response.
     *
     * @var bool
     */
    protected $afterResponse = false;

    /**
     * Create a new pending sportevent dispatch.
     *
     * @param  mixed  $sportevent
     * @return void
     */
    public function __construct($sportevent)
    {
        $this->sportevent = $sportevent;
    }

    /**
     * Set the desired connection for the sportevent.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->sportevent->onConnection($connection);

        return $this;
    }

    /**
     * Set the desired queue for the sportevent.
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->sportevent->onQueue($queue);

        return $this;
    }

    /**
     * Set the desired connection for the chain.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function allOnConnection($connection)
    {
        $this->sportevent->allOnConnection($connection);

        return $this;
    }

    /**
     * Set the desired queue for the chain.
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function allOnQueue($queue)
    {
        $this->sportevent->allOnQueue($queue);

        return $this;
    }

    /**
     * Set the desired delay for the sportevent.
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->sportevent->delay($delay);

        return $this;
    }

    /**
     * Set the sportevents that should run if this sportevent is successful.
     *
     * @param  array  $chain
     * @return $this
     */
    public function chain($chain)
    {
        $this->sportevent->chain($chain);

        return $this;
    }

    /**
     * Indicate that the sportevent should be dispatched after the response is sent to the browser.
     *
     * @return $this
     */
    public function afterResponse()
    {
        $this->afterResponse = true;

        return $this;
    }

    /**
     * Dynamically proxy methods to the underlying sportevent.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->sportevent->{$method}(...$parameters);

        return $this;
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->afterResponse) {
            app(Dispatcher::class)->dispatchAfterResponse($this->sportevent);
        } else {
            app(Dispatcher::class)->dispatch($this->sportevent);
        }
    }
}
