<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent as sporteventContract;

class Syncsportevent extends sportevent implements sporteventContract
{
    /**
     * The class name of the sportevent.
     *
     * @var string
     */
    protected $sportevent;

    /**
     * The queue message data.
     *
     * @var string
     */
    protected $payload;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  string  $payload
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, $payload, $connectionName, $queue)
    {
        $this->queue = $queue;
        $this->payload = $payload;
        $this->container = $container;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the sportevent back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);
    }

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return 1;
    }

    /**
     * Get the sportevent identifier.
     *
     * @return string
     */
    public function getsporteventId()
    {
        return '';
    }

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->payload;
    }

    /**
     * Get the name of the queue the sportevent belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return 'sync';
    }
}
