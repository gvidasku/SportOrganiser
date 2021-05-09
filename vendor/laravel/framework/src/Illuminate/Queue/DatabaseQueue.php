<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\ClearableQueue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Database\Connection;
use Illuminate\Queue\sportevents\Databasesportevent;
use Illuminate\Queue\sportevents\DatabasesporteventRecord;
use Illuminate\Support\Carbon;
use PDO;

class DatabaseQueue extends Queue implements QueueContract, ClearableQueue
{
    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The database table that holds the sportevents.
     *
     * @var string
     */
    protected $table;

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
     * Create a new database queue instance.
     *
     * @param  \Illuminate\Database\Connection  $database
     * @param  string  $table
     * @param  string  $default
     * @param  int  $retryAfter
     * @return void
     */
    public function __construct(Connection $database, $table, $default = 'default', $retryAfter = 60)
    {
        $this->table = $table;
        $this->default = $default;
        $this->database = $database;
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
        return $this->database->table($this->table)
                    ->where('queue', $this->getQueue($queue))
                    ->count();
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
        return $this->pushToDatabase($queue, $this->createPayload(
            $sportevent, $this->getQueue($queue), $data
        ));
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
        return $this->pushToDatabase($queue, $payload);
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return void
     */
    public function later($delay, $sportevent, $data = '', $queue = null)
    {
        return $this->pushToDatabase($queue, $this->createPayload(
            $sportevent, $this->getQueue($queue), $data
        ), $delay);
    }

    /**
     * Push an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function bulk($sportevents, $data = '', $queue = null)
    {
        $queue = $this->getQueue($queue);

        $availableAt = $this->availableAt();

        return $this->database->table($this->table)->insert(collect((array) $sportevents)->map(
            function ($sportevent) use ($queue, $data, $availableAt) {
                return $this->buildDatabaseRecord($queue, $this->createPayload($sportevent, $this->getQueue($queue), $data), $availableAt);
            }
        )->all());
    }

    /**
     * Release a reserved sportevent back onto the queue.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\sportevents\DatabasesporteventRecord  $sportevent
     * @param  int  $delay
     * @return mixed
     */
    public function release($queue, $sportevent, $delay)
    {
        return $this->pushToDatabase($queue, $sportevent->payload, $delay, $sportevent->attempts);
    }

    /**
     * Push a raw payload to the database with a given delay.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  int  $attempts
     * @return mixed
     */
    protected function pushToDatabase($queue, $payload, $delay = 0, $attempts = 0)
    {
        return $this->database->table($this->table)->insertGetId($this->buildDatabaseRecord(
            $this->getQueue($queue), $payload, $this->availableAt($delay), $attempts
        ));
    }

    /**
     * Create an array to insert for the given sportevent.
     *
     * @param  string|null  $queue
     * @param  string  $payload
     * @param  int  $availableAt
     * @param  int  $attempts
     * @return array
     */
    protected function buildDatabaseRecord($queue, $payload, $availableAt, $attempts = 0)
    {
        return [
            'queue' => $queue,
            'attempts' => $attempts,
            'reserved_at' => null,
            'available_at' => $availableAt,
            'created_at' => $this->currentTime(),
            'payload' => $payload,
        ];
    }

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     *
     * @throws \Throwable
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        return $this->database->transaction(function () use ($queue) {
            if ($sportevent = $this->getNextAvailablesportevent($queue)) {
                return $this->marshalsportevent($queue, $sportevent);
            }
        });
    }

    /**
     * Get the next available sportevent for the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Queue\sportevents\DatabasesporteventRecord|null
     */
    protected function getNextAvailablesportevent($queue)
    {
        $sportevent = $this->database->table($this->table)
                    ->lock($this->getLockForPopping())
                    ->where('queue', $this->getQueue($queue))
                    ->where(function ($query) {
                        $this->isAvailable($query);
                        $this->isReservedButExpired($query);
                    })
                    ->orderBy('id', 'asc')
                    ->first();

        return $sportevent ? new DatabasesporteventRecord((object) $sportevent) : null;
    }

    /**
     * Get the lock required for popping the next sportevent.
     *
     * @return string|bool
     */
    protected function getLockForPopping()
    {
        $databaseEngine = $this->database->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        $databaseVersion = $this->database->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if ($databaseEngine == 'mysql' && ! strpos($databaseVersion, 'MariaDB') && version_compare($databaseVersion, '8.0.1', '>=') ||
            $databaseEngine == 'pgsql' && version_compare($databaseVersion, '9.5', '>=')) {
            return 'FOR UPDATE SKIP LOCKED';
        }

        return true;
    }

    /**
     * Modify the query to check for available sportevents.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return void
     */
    protected function isAvailable($query)
    {
        $query->where(function ($query) {
            $query->whereNull('reserved_at')
                  ->where('available_at', '<=', $this->currentTime());
        });
    }

    /**
     * Modify the query to check for sportevents that are reserved but have expired.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return void
     */
    protected function isReservedButExpired($query)
    {
        $expiration = Carbon::now()->subSeconds($this->retryAfter)->getTimestamp();

        $query->orWhere(function ($query) use ($expiration) {
            $query->where('reserved_at', '<=', $expiration);
        });
    }

    /**
     * Marshal the reserved sportevent into a Databasesportevent instance.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\sportevents\DatabasesporteventRecord  $sportevent
     * @return \Illuminate\Queue\sportevents\Databasesportevent
     */
    protected function marshalsportevent($queue, $sportevent)
    {
        $sportevent = $this->marksporteventAsReserved($sportevent);

        return new Databasesportevent(
            $this->container, $this, $sportevent, $this->connectionName, $queue
        );
    }

    /**
     * Mark the given sportevent ID as reserved.
     *
     * @param  \Illuminate\Queue\sportevents\DatabasesporteventRecord  $sportevent
     * @return \Illuminate\Queue\sportevents\DatabasesporteventRecord
     */
    protected function marksporteventAsReserved($sportevent)
    {
        $this->database->table($this->table)->where('id', $sportevent->id)->update([
            'reserved_at' => $sportevent->touch(),
            'attempts' => $sportevent->increment(),
        ]);

        return $sportevent;
    }

    /**
     * Delete a reserved sportevent from the queue.
     *
     * @param  string  $queue
     * @param  string  $id
     * @return void
     *
     * @throws \Throwable
     */
    public function deleteReserved($queue, $id)
    {
        $this->database->transaction(function () use ($id) {
            if ($this->database->table($this->table)->lockForUpdate()->find($id)) {
                $this->database->table($this->table)->where('id', $id)->delete();
            }
        });
    }

    /**
     * Delete a reserved sportevent from the reserved queue and release it.
     *
     * @param  string  $queue
     * @param  \Illuminate\Queue\sportevents\Databasesportevent  $sportevent
     * @param  int  $delay
     * @return void
     */
    public function deleteAndRelease($queue, $sportevent, $delay)
    {
        $this->database->transaction(function () use ($queue, $sportevent, $delay) {
            if ($this->database->table($this->table)->lockForUpdate()->find($sportevent->getsporteventId())) {
                $this->database->table($this->table)->where('id', $sportevent->getsporteventId())->delete();
            }

            $this->release($queue, $sportevent->getsporteventRecord(), $delay);
        });
    }

    /**
     * Delete all of the sportevents from the queue.
     *
     * @param  string  $queue
     * @return int
     */
    public function clear($queue)
    {
        return $this->database->table($this->table)
                    ->where('queue', $this->getQueue($queue))
                    ->delete();
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying database instance.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
