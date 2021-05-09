<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent as sporteventContract;
use Illuminate\Queue\DatabaseQueue;

class Databasesportevent extends sportevent implements sporteventContract
{
    /**
     * The database queue instance.
     *
     * @var \Illuminate\Queue\DatabaseQueue
     */
    protected $database;

    /**
     * The database sportevent payload.
     *
     * @var \stdClass
     */
    protected $sportevent;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Illuminate\Queue\DatabaseQueue  $database
     * @param  \stdClass  $sportevent
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, DatabaseQueue $database, $sportevent, $connectionName, $queue)
    {
        $this->sportevent = $sportevent;
        $this->queue = $queue;
        $this->database = $database;
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

        $this->database->deleteAndRelease($this->queue, $this, $delay);
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->database->deleteReserved($this->queue, $this->sportevent->id);
    }

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->sportevent->attempts;
    }

    /**
     * Get the sportevent identifier.
     *
     * @return string
     */
    public function getsporteventId()
    {
        return $this->sportevent->id;
    }

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->sportevent->payload;
    }

    /**
     * Get the database sportevent record.
     *
     * @return \Illuminate\Queue\sportevents\DatabasesporteventRecord
     */
    public function getsporteventRecord()
    {
        return $this->sportevent;
    }
}
