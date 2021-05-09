<?php

namespace Illuminate\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallQueuedListener implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * The listener class name.
     *
     * @var string
     */
    public $class;

    /**
     * The listener method.
     *
     * @var string
     */
    public $method;

    /**
     * The data to be passed to the listener.
     *
     * @var array
     */
    public $data;

    /**
     * The number of times the sportevent may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds to wait before retrying a sportevent that encountered an uncaught exception.
     *
     * @var int
     */
    public $backoff;

    /**
     * The timestamp indicating when the sportevent should timeout.
     *
     * @var int
     */
    public $retryUntil;

    /**
     * The number of seconds the sportevent can run before timing out.
     *
     * @var int
     */
    public $timeout;

    /**
     * Create a new sportevent instance.
     *
     * @param  string  $class
     * @param  string  $method
     * @param  array  $data
     * @return void
     */
    public function __construct($class, $method, $data)
    {
        $this->data = $data;
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * Handle the queued sportevent.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function handle(Container $container)
    {
        $this->prepareData();

        $handler = $this->setsporteventInstanceIfNecessary(
            $this->sportevent, $container->make($this->class)
        );

        call_user_func_array(
            [$handler, $this->method], $this->data
        );
    }

    /**
     * Set the sportevent instance of the given class if necessary.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  mixed  $instance
     * @return mixed
     */
    protected function setsporteventInstanceIfNecessary(sportevent $sportevent, $instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive($instance))) {
            $instance->setsportevent($sportevent);
        }

        return $instance;
    }

    /**
     * Call the failed method on the sportevent instance.
     *
     * The event instance and the exception will be passed.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed($e)
    {
        $this->prepareData();

        $handler = Container::getInstance()->make($this->class);

        $parameters = array_merge($this->data, [$e]);

        if (method_exists($handler, 'failed')) {
            call_user_func_array([$handler, 'failed'], $parameters);
        }
    }

    /**
     * Unserialize the data if needed.
     *
     * @return void
     */
    protected function prepareData()
    {
        if (is_string($this->data)) {
            $this->data = unserialize($this->data);
        }
    }

    /**
     * Get the display name for the queued sportevent.
     *
     * @return string
     */
    public function displayName()
    {
        return $this->class;
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->data = array_map(function ($data) {
            return is_object($data) ? clone $data : $data;
        }, $this->data);
    }
}
