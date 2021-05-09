<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\sportevent as sporteventContract;

trait InteractsWithQueue
{
    /**
     * The underlying queue sportevent instance.
     *
     * @var \Illuminate\Contracts\Queue\sportevent
     */
    public $sportevent;

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->sportevent ? $this->sportevent->attempts() : 1;
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        if ($this->sportevent) {
            return $this->sportevent->delete();
        }
    }

    /**
     * Fail the sportevent from the queue.
     *
     * @param  \Throwable|null  $exception
     * @return void
     */
    public function fail($exception = null)
    {
        if ($this->sportevent) {
            $this->sportevent->fail($exception);
        }
    }

    /**
     * Release the sportevent back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        if ($this->sportevent) {
            return $this->sportevent->release($delay);
        }
    }

    /**
     * Set the base queue sportevent instance.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return $this
     */
    public function setsportevent(sporteventContract $sportevent)
    {
        $this->sportevent = $sportevent;

        return $this;
    }
}
