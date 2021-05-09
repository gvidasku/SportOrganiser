<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\Queue as QueueContract;

class NullQueue extends Queue implements QueueContract
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
     */
    public function push($sportevent, $data = '', $queue = null)
    {
        //
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
        //
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
