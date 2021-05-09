<?php

namespace Illuminate\Bus;

use Closure;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Support\Arr;
use RuntimeException;

trait Queueable
{
    /**
     * The name of the connection the sportevent should be sent to.
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the sportevent should be sent to.
     *
     * @var string|null
     */
    public $queue;

    /**
     * The name of the connection the chain should be sent to.
     *
     * @var string|null
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
     *
     * @var string|null
     */
    public $chainQueue;

    /**
     * The callbacks to be executed on chain failure.
     *
     * @var array|null
     */
    public $chainCatchCallbacks;

    /**
     * The number of seconds before the sportevent should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    public $delay;

    /**
     * The middleware the sportevent should be dispatched through.
     *
     * @var array
     */
    public $middleware = [];

    /**
     * The sportevents that should run if this sportevent is successful.
     *
     * @var array
     */
    public $chained = [];

    /**
     * Set the desired connection for the sportevent.
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->connection = $connection;

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
        $this->queue = $queue;

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
        $this->chainConnection = $connection;
        $this->connection = $connection;

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
        $this->chainQueue = $queue;
        $this->queue = $queue;

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
        $this->delay = $delay;

        return $this;
    }

    /**
     * Specify the middleware the sportevent should be dispatched through.
     *
     * @param  array|object  $middleware
     * @return $this
     */
    public function through($middleware)
    {
        $this->middleware = Arr::wrap($middleware);

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
        $this->chained = collect($chain)->map(function ($sportevent) {
            return $this->serializesportevent($sportevent);
        })->all();

        return $this;
    }

    /**
     * Serialize a sportevent for queuing.
     *
     * @param  mixed  $sportevent
     * @return string
     */
    protected function serializesportevent($sportevent)
    {
        if ($sportevent instanceof Closure) {
            if (! class_exists(CallQueuedClosure::class)) {
                throw new RuntimeException(
                    'To enable support for closure sportevents, please install the illuminate/queue package.'
                );
            }

            $sportevent = CallQueuedClosure::create($sportevent);
        }

        return serialize($sportevent);
    }

    /**
     * Dispatch the next sportevent on the chain.
     *
     * @return void
     */
    public function dispatchNextsporteventInChain()
    {
        if (! empty($this->chained)) {
            dispatch(tap(unserialize(array_shift($this->chained)), function ($next) {
                $next->chained = $this->chained;

                $next->onConnection($next->connection ?: $this->chainConnection);
                $next->onQueue($next->queue ?: $this->chainQueue);

                $next->chainConnection = $this->chainConnection;
                $next->chainQueue = $this->chainQueue;
                $next->chainCatchCallbacks = $this->chainCatchCallbacks;
            }));
        }
    }

    /**
     * Invoke all of the chain's failed sportevent callbacks.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function invokeChainCatchCallbacks($e)
    {
        collect($this->chainCatchCallbacks)->each(function ($callback) use ($e) {
            $callback instanceof SerializableClosure ? $callback->__invoke($e) : call_user_func($callback, $e);
        });
    }
}
