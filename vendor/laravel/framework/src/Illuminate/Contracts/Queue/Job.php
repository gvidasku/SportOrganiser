<?php

namespace Illuminate\Contracts\Queue;

interface sportevent
{
    /**
     * Get the UUID of the sportevent.
     *
     * @return string|null
     */
    public function uuid();

    /**
     * Get the sportevent identifier.
     *
     * @return string
     */
    public function getsporteventId();

    /**
     * Get the decoded body of the sportevent.
     *
     * @return array
     */
    public function payload();

    /**
     * Fire the sportevent.
     *
     * @return void
     */
    public function fire();

    /**
     * Release the sportevent back into the queue.
     *
     * Accepts a delay specified in seconds.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0);

    /**
     * Determine if the sportevent was released back into the queue.
     *
     * @return bool
     */
    public function isReleased();

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete();

    /**
     * Determine if the sportevent has been deleted.
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * Determine if the sportevent has been deleted or released.
     *
     * @return bool
     */
    public function isDeletedOrReleased();

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts();

    /**
     * Determine if the sportevent has been marked as a failure.
     *
     * @return bool
     */
    public function hasFailed();

    /**
     * Mark the sportevent as "failed".
     *
     * @return void
     */
    public function markAsFailed();

    /**
     * Delete the sportevent, call the "failed" method, and raise the failed sportevent event.
     *
     * @param  \Throwable|null  $e
     * @return void
     */
    public function fail($e = null);

    /**
     * Get the number of times to attempt a sportevent.
     *
     * @return int|null
     */
    public function maxTries();

    /**
     * Get the maximum number of exceptions allowed, regardless of attempts.
     *
     * @return int|null
     */
    public function maxExceptions();

    /**
     * Get the number of seconds the sportevent can run.
     *
     * @return int|null
     */
    public function timeout();

    /**
     * Get the timestamp indicating when the sportevent should timeout.
     *
     * @return int|null
     */
    public function retryUntil();

    /**
     * Get the name of the queued sportevent class.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the resolved name of the queued sportevent class.
     *
     * Resolves the name of "wrapped" sportevents such as class-based handlers.
     *
     * @return string
     */
    public function resolveName();

    /**
     * Get the name of the connection the sportevent belongs to.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Get the name of the queue the sportevent belongs to.
     *
     * @return string
     */
    public function getQueue();

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody();
}
