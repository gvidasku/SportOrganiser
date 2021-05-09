<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\sporteventFailed;
use Illuminate\Queue\ManuallyFailedException;
use Illuminate\Support\InteractsWithTime;

abstract class sportevent
{
    use InteractsWithTime;

    /**
     * The sportevent handler instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Indicates if the sportevent has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Indicates if the sportevent has been released.
     *
     * @var bool
     */
    protected $released = false;

    /**
     * Indicates if the sportevent has failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The name of the connection the sportevent belongs to.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * The name of the queue the sportevent belongs to.
     *
     * @var string
     */
    protected $queue;

    /**
     * Get the sportevent identifier.
     *
     * @return string
     */
    abstract public function getsporteventId();

    /**
     * Get the raw body of the sportevent.
     *
     * @return string
     */
    abstract public function getRawBody();

    /**
     * Get the UUID of the sportevent.
     *
     * @return string|null
     */
    public function uuid()
    {
        return $this->payload()['uuid'] ?? null;
    }

    /**
     * Fire the sportevent.
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->payload();

        [$class, $method] = sporteventName::parse($payload['sportevent']);

        ($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * Determine if the sportevent has been deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Release the sportevent back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        $this->released = true;
    }

    /**
     * Determine if the sportevent was released back into the queue.
     *
     * @return bool
     */
    public function isReleased()
    {
        return $this->released;
    }

    /**
     * Determine if the sportevent has been deleted or released.
     *
     * @return bool
     */
    public function isDeletedOrReleased()
    {
        return $this->isDeleted() || $this->isReleased();
    }

    /**
     * Determine if the sportevent has been marked as a failure.
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->failed;
    }

    /**
     * Mark the sportevent as "failed".
     *
     * @return void
     */
    public function markAsFailed()
    {
        $this->failed = true;
    }

    /**
     * Delete the sportevent, call the "failed" method, and raise the failed sportevent event.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    public function fail($e = null)
    {
        $this->markAsFailed();

        if ($this->isDeleted()) {
            return;
        }

        try {
            // If the sportevent has failed, we will delete it, call the "failed" method and then call
            // an event indicating the sportevent has failed so it can be logged if needed. This is
            // to allow every developer to better keep monitor of their failed queue sportevents.
            $this->delete();

            $this->failed($e);
        } finally {
            $this->resolve(Dispatcher::class)->dispatch(new sporteventFailed(
                $this->connectionName, $this, $e ?: new ManuallyFailedException
            ));
        }
    }

    /**
     * Process an exception that caused the sportevent to fail.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    protected function failed($e)
    {
        $payload = $this->payload();

        [$class, $method] = sporteventName::parse($payload['sportevent']);

        if (method_exists($this->instance = $this->resolve($class), 'failed')) {
            $this->instance->failed($payload['data'], $e, $payload['uuid']);
        }
    }

    /**
     * Resolve the given class.
     *
     * @param  string  $class
     * @return mixed
     */
    protected function resolve($class)
    {
        return $this->container->make($class);
    }

    /**
     * Get the resolved sportevent handler instance.
     *
     * @return mixed
     */
    public function getResolvedsportevent()
    {
        return $this->instance;
    }

    /**
     * Get the decoded body of the sportevent.
     *
     * @return array
     */
    public function payload()
    {
        return json_decode($this->getRawBody(), true);
    }

    /**
     * Get the number of times to attempt a sportevent.
     *
     * @return int|null
     */
    public function maxTries()
    {
        return $this->payload()['maxTries'] ?? null;
    }

    /**
     * Get the number of times to attempt a sportevent after an exception.
     *
     * @return int|null
     */
    public function maxExceptions()
    {
        return $this->payload()['maxExceptions'] ?? null;
    }

    /**
     * The number of seconds to wait before retrying a sportevent that encountered an uncaught exception.
     *
     * @return int|null
     */
    public function backoff()
    {
        return $this->payload()['backoff'] ?? $this->payload()['delay'] ?? null;
    }

    /**
     * Get the number of seconds the sportevent can run.
     *
     * @return int|null
     */
    public function timeout()
    {
        return $this->payload()['timeout'] ?? null;
    }

    /**
     * Get the timestamp indicating when the sportevent should timeout.
     *
     * @return int|null
     */
    public function retryUntil()
    {
        return $this->payload()['retryUntil'] ?? $this->payload()['timeoutAt'] ?? null;
    }

    /**
     * Get the name of the queued sportevent class.
     *
     * @return string
     */
    public function getName()
    {
        return $this->payload()['sportevent'];
    }

    /**
     * Get the resolved name of the queued sportevent class.
     *
     * Resolves the name of "wrapped" sportevents such as class-based handlers.
     *
     * @return string
     */
    public function resolveName()
    {
        return sporteventName::resolve($this->getName(), $this->payload());
    }

    /**
     * Get the name of the connection the sportevent belongs to.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Get the name of the queue the sportevent belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get the service container instance.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
