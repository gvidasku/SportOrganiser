<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\sportevents\Redissportevent;
use Illuminate\Support\Str;

class RedisQueue extends Queue implements QueueContract, ClearableQueue
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Contracts\Redis\Factory
     */
    protected $redis;

    /**
     * The connection name.
     *
     * @var string
     */
    protected $connection;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * The expiration time of a sportevent.
     *
     * @var int|null
     */
    protected $retryAfter = 60;

    /**
     * The maximum number of seconds to block for a sportevent.
     *
     * @var int|null
     */
    protected $blockFor = null;

    /**
     * Create a new Redis queue instance.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string  $default
     * @param  string|null  $connection
     * @param  int  $retryAfter
     * @param  int|null  $blockFor
     * @return void
     */
    public function __construct(Redis $redis, $default = 'default', $connection = null, $retryAfter = 60, $blockFor = null)
    {
        $this->redis = $redis;
        $this->default = $default;
        $this->blockFor = $blockFor;
        $this->connection = $connection;
        $this->retryAfter = $retryAfter;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $queue = $this->getQueue($queue);

        return $this->getConnection()->eval(
            LuaScripts::size(), 3, $queue, $queue.':delayed', $queue.':reserved'
        );
    }

    /**
     * Push an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return void
     */
    public function bulk($sportevents, $data = '', $queue = null)
    {
        $this->getConnection()->pipeline(function () use ($sportevents, $data, $queue) {
            $this->getConnection()->transaction(function () use ($sportevents, $data, $queue) {
                foreach ((array) $sportevents as $sportevent) {
                    $this->push($sportevent, $data, $queue);
                }
            });
        });
    }

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  object|string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($sportevent, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($sportevent, $this->getQueue($queue), $data), $queue);
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
        $this->getConnection()->eval(
            LuaScripts::push(), 2, $this->getQueue($queue),
            $this->getQueue($queue).':notify', $payload
        );

        return json_decode($payload, true)['id'] ?? null;
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  object|string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $sportevent, $data = '', $queue = null)
    {
        return $this->laterRaw($delay, $this->createPayload($sportevent, $this->getQueue($queue), $data), $queue);
    }

    /**
     * Push a raw sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $payload
     * @param  string|null  $queue
     * @return mixed
     */
    protected function laterRaw($delay, $payload, $queue = null)
    {
        $this->getConnection()->zadd(
            $this->getQueue($queue).':delayed', $this->availableAt($delay), $payload
        );

        return json_decode($payload, true)['id'] ?? null;
    }

    /**
     * Create a payload string from the given sportevent and data.
     *
     * @param  string  $sportevent
     * @param  string  $queue
     * @param  mixed  $data
     * @return array
     */
    protected function createPayloadArray($sportevent, $queue, $data = '')
    {
        return array_merge(parent::createPayloadArray($sportevent, $queue, $data), [
            'id' => $this->getRandomId(),
            'attempts' => 0,
        ]);
    }

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    public function pop($queue = null)
    {
        $this->migrate($prefixed = $this->getQueue($queue));

        if (empty($nextsportevent = $this->retrieveNextsportevent($prefixed))) {
            return;
        }

        [$sportevent, $reserved] = $nextsportevent;

        if ($reserved) {
            return new Redissportevent(
                $this->container, $this, $sportevent,
                $reserved, $this->connectionName, $queue ?: $this->default
            );
        }
    }

    /**
     * Migrate any delayed or expired sportevents onto the primary queue.
     *
     * @param  string  $queue
     * @return void
     */
    protected function migrate($queue)
    {
        $this->migrateExpiredsportevents($queue.':delayed', $queue);

        if (! is_null($this->retryAfter)) {
            $this->migrateExpiredsportevents($queue.':reserved', $queue);
        }
    }

    /**
     * Migrate the delayed sportevents that are ready to the regular queue.
     *
     * @param  string  $from
     * @param  string  $to
     * @return array
     */
    public function migrateExpiredsportevents($from, $to)
    {
        return $this->getConnection()->eval(
            LuaScripts::migrateExpiredsportevents(), 3, $from, $to, $to.':notify', $this->currentTime()
        );
    }

    /**
     * Retrieve the next sportevent from the queue.
     *
     * @param  string  $queue
     * @param  bool  $block
     * @return array
     */
    protected function retrieveNextsportevent($queue, $block = true)
    {
        $nextsportevent = $this->getConnection()->eval(
            LuaScripts::pop(), 3, $queue, $queue.':reserved', $queue.':notify',
            $this->availableAt($this->retryAfter)
        );

        if (empty($nextsportevent)) {
            return [null, null];
        }

        [$sportevent, $reserved] = $nextsportevent;

        if (! $sportevent && ! is_null($this->blockFor) && $block &&
            $this->getConnection()->blpop([$queue.':notify'], $this->blockFor)) {
            return $this->retrieveNextsportevent($queue, false);
        }

        return [$sportevent, $reserved];
    }

    /**
     * Delete a reserved sportevent from the queue.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\sportevents\Redissportevent  $sportevent
     * @return void
     */
    public function deleteReserved($queue, $sportevent)
    {
        $this->getConnection()->zrem($this->getQueue($queue).':reserved', $sportevent->getReservedsportevent());
    }

    /**
     * Delete a reserved sportevent from the reserved queue and release it.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\sportevents\Redissportevent  $sportevent
     * @param  int  $delay
     * @return void
     */
    public function deleteAndRelease($queue, $sportevent, $delay)
    {
        $queue = $this->getQueue($queue);

        $this->getConnection()->eval(
            LuaScripts::release(), 2, $queue.':delayed', $queue.':reserved',
            $sportevent->getReservedsportevent(), $this->availableAt($delay)
        );
    }

    /**
     * Delete all of the sportevents from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue)
    {
        $queue = $this->getQueue($queue);

        return $this->getConnection()->eval(
            LuaScripts::clear(), 4, $queue, $queue.':delayed',
            $queue.':reserved', $queue.':notify'
        );
    }

    /**
     * Get a random ID string.
     *
     * @return string
     */
    protected function getRandomId()
    {
        return Str::random(32);
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return 'queues:'.($queue ?: $this->default);
    }

    /**
     * Get the connection for the queue.
     *
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function getConnection()
    {
        return $this->redis->connection($this->connection);
    }

    /**
     * Get the underlying Redis instance.
     *
     * @return \Illuminate\Contracts\Redis\Factory
     */
    public function getRedis()
    {
        return $this->redis;
    }
}
