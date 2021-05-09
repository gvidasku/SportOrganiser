<?php

namespace Illuminate\Events;

use Closure;
use Illuminate\Queue\SerializableClosure;

class QueuedClosure
{
    /**
     * The underlying Closure.
     *
     * @var \Closure
     */
    public $closure;

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
     * The number of seconds before the sportevent should be made available.
     *
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    public $delay;

    /**
     * All of the "catch" callbacks for the queued closure.
     *
     * @var array
     */
    public $catchCallbacks = [];

    /**
     * Create a new queued closure event listener resolver.
     *
     * @param  \Closure  $closure
     * @return void
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

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
     * Specify a callback that should be invoked if the queued listener sportevent fails.
     *
     * @param  \Closure  $closure
     * @return $this
     */
    public function catch(Closure $closure)
    {
        $this->catchCallbacks[] = $closure;

        return $this;
    }

    /**
     * Resolve the actual event listener callback.
     *
     * @return \Closure
     */
    public function resolve()
    {
        return function (...$arguments) {
            dispatch(new CallQueuedListener(InvokeQueuedClosure::class, 'handle', [
                'closure' => new SerializableClosure($this->closure),
                'arguments' => $arguments,
                'catch' => collect($this->catchCallbacks)->map(function ($callback) {
                    return new SerializableClosure($callback);
                })->all(),
            ]))->onConnection($this->connection)->onQueue($this->queue)->delay($this->delay);
        };
    }
}
