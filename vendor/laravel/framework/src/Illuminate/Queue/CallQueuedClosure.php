<?php

namespace Illuminate\Queue;

use Closure;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ReflectionFunction;

class CallQueuedClosure implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The serializable Closure instance.
     *
     * @var \Illuminate\Queue\SerializableClosure
     */
    public $closure;

    /**
     * The callbacks that should be executed on failure.
     *
     * @var array
     */
    public $failureCallbacks = [];

    /**
     * Indicate if the sportevent should be deleted when models are missing.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Queue\SerializableClosure  $closure
     * @return void
     */
    public function __construct(SerializableClosure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Create a new sportevent instance.
     *
     * @param  \Closure  $sportevent
     * @return self
     */
    public static function create(Closure $sportevent)
    {
        return new self(new SerializableClosure($sportevent));
    }

    /**
     * Execute the sportevent.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function handle(Container $container)
    {
        $container->call($this->closure->getClosure(), ['sportevent' => $this]);
    }

    /**
     * Add a callback to be executed if the sportevent fails.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function onFailure($callback)
    {
        $this->failureCallbacks[] = $callback instanceof Closure
                        ? new SerializableClosure($callback)
                        : $callback;

        return $this;
    }

    /**
     * Handle a sportevent failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Exception $e)
    {
        foreach ($this->failureCallbacks as $callback) {
            call_user_func($callback instanceof SerializableClosure ? $callback->getClosure() : $callback, $e);
        }
    }

    /**
     * Get the display name for the queued sportevent.
     *
     * @return string
     */
    public function displayName()
    {
        $reflection = new ReflectionFunction($this->closure->getClosure());

        return 'Closure ('.basename($reflection->getFileName()).':'.$reflection->getStartLine().')';
    }
}
