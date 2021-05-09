<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent as sporteventContract;
use Illuminate\Queue\RedisQueue;

class Redissportevent extends sportevent implements sporteventContract
{
    /**
     * The Redis queue instance.
     *
     * @var \Illuminate\Queue\RedisQueue
     */
    protected $redis;

    /**
     * The Redis raw sportevent payload.
     *
     * @var string
     */
    protected $sportevent;

    /**
     * The JSON decoded version of "$sportevent".
     *
     * @var array
     */
    protected $decoded;

    /**
     * The Redis sportevent payload inside the reserved queue.
     *
     * @var string
     */
    protected $reserved;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Queue\RedisQueue  $redis
     * @param  string  $sportevent
     * @param  string  $reserved
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, RedisQueue $redis, $sportevent, $reserved, $connectionName, $queue)
    {
        // The $sportevent variable is the original sportevent JSON as it existed in the ready queue while
        // the $reserved variable is the raw JSON in the reserved queue. The exact format
        // of the reserved sportevent is required in order for us to properly delete its data.
        $this->sportevent = $sportevent;
        $this->redis = $redis;
        $this->queue = $queue;
        $this->reserved = $reserved;
        $this->container = $container;
        $this->connectionName = $connectionName;

        $this->decoded = $this->payload();
    }

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->sportevent;
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->redis->deleteReserved($this->queue, $this);
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

        $this->redis->deleteAndRelease($this->queue, $this, $delay);
    }

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return ($this->decoded['attempts'] ?? null) + 1;
    }

    /**
     * Get the sportevent identifier.
     *
     * @return string|null
     */
    public function getsporteventId()
    {
        return $this->decoded['id'] ?? null;
    }

    /**
     * Get the underlying Redis factory implementation.
     *
     * @return \Illuminate\Queue\RedisQueue
     */
    public function getRedisQueue()
    {
        return $this->redis;
    }

    /**
     * Get the underlying reserved Redis sportevent.
     *
     * @return string
     */
    public function getReservedsportevent()
    {
        return $this->reserved;
    }
}
