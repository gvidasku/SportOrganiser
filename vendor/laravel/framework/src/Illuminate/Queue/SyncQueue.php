<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\sportevent;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Events\sporteventExceptionOccurred;
use Illuminate\Queue\Events\sporteventProcessed;
use Illuminate\Queue\Events\sporteventProcessing;
use Illuminate\Queue\sportevents\Syncsportevent;
use Throwable;

class SyncQueue extends Queue implements QueueContract
{
    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return 0;
    }

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     *
     * @throws \Throwable
     */
    public function push($sportevent, $data = '', $queue = null)
    {
        $queuesportevent = $this->resolvesportevent($this->createPayload($sportevent, $queue, $data), $queue);

        try {
            $this->raiseBeforesporteventEvent($queuesportevent);

            $queuesportevent->fire();

            $this->raiseAftersporteventEvent($queuesportevent);
        } catch (Throwable $e) {
            $this->handleException($queuesportevent, $e);
        }

        return 0;
    }

    /**
     * Resolve a Sync sportevent instance.
     *
     * @param  string  $payload
     * @param  string  $queue
     * @return \Illuminate\Queue\sportevents\Syncsportevent
     */
    protected function resolvesportevent($payload, $queue)
    {
        return new Syncsportevent($this->container, $payload, $this->connectionName, $queue);
    }

    /**
     * Raise the before queue sportevent event.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return void
     */
    protected function raiseBeforesporteventEvent(sportevent $sportevent)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new sporteventProcessing($this->connectionName, $sportevent));
        }
    }

    /**
     * Raise the after queue sportevent event.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return void
     */
    protected function raiseAftersporteventEvent(sportevent $sportevent)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new sporteventProcessed($this->connectionName, $sportevent));
        }
    }

    /**
     * Raise the exception occurred queue sportevent event.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $e
     * @return void
     */
    protected function raiseExceptionOccurredsporteventEvent(sportevent $sportevent, Throwable $e)
    {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new sporteventExceptionOccurred($this->connectionName, $sportevent, $e));
        }
    }

    /**
     * Handle an exception that occurred while processing a sportevent.
     *
     * @param  \Illuminate\Queue\sportevents\sportevent  $queuesportevent
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleException(sportevent $queuesportevent, Throwable $e)
    {
        $this->raiseExceptionOccurredsporteventEvent($queuesportevent, $e);

        $queuesportevent->fail($e);

        throw $e;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        //
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $sportevent, $data = '', $queue = null)
    {
        return $this->push($sportevent, $data, $queue);
    }

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    public function pop($queue = null)
    {
        //
    }
}
