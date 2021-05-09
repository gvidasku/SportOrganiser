<?php

namespace Illuminate\Queue\Events;

class sporteventExceptionOccurred
{
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The sportevent instance.
     *
     * @var \Illuminate\Contracts\Queue\sportevent
     */
    public $sportevent;

    /**
     * The exception instance.
     *
     * @var \Throwable
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $exception
     * @return void
     */
    public function __construct($connectionName, $sportevent, $exception)
    {
        $this->sportevent = $sportevent;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }
}
