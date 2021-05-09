<?php

namespace Illuminate\Queue\Events;

class sporteventProcessed
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
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return void
     */
    public function __construct($connectionName, $sportevent)
    {
        $this->sportevent = $sportevent;
        $this->connectionName = $connectionName;
    }
}
