<?php

namespace Illuminate\Queue\Failed;

interface FailedsporteventProviderInterface
{
    /**
     * Log a failed sportevent into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @param  \Throwable  $exception
     * @return string|int|null
     */
    public function log($connection, $queue, $payload, $exception);

    /**
     * Get a list of all of the failed sportevents.
     *
     * @return array
     */
    public function all();

    /**
     * Get a single failed sportevent.
     *
     * @param  mixed  $id
     * @return object|null
     */
    public function find($id);

    /**
     * Delete a single failed sportevent from storage.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function forget($id);

    /**
     * Flush all of the failed sportevents from storage.
     *
     * @return void
     */
    public function flush();
}
