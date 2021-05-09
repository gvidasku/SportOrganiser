<?php

namespace Illuminate\Contracts\Queue;

interface Queue
{
    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null);

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string|object  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($sportevent, $data = '', $queue = null);

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string  $queue
     * @param  string|object  $sportevent
     * @param  mixed  $data
     * @return mixed
     */
    public function pushOn($queue, $sportevent, $data = '');

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []);

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string|object  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $sportevent, $data = '', $queue = null);

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  string  $queue
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string|object  $sportevent
     * @param  mixed  $data
     * @return mixed
     */
    public function laterOn($queue, $delay, $sportevent, $data = '');

    /**
     * Push an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function bulk($sportevents, $data = '', $queue = null);

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    public function pop($queue = null);

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name);
}
