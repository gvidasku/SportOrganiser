<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Support\InteractsWithTime;

class DatabasesporteventRecord
{
    use InteractsWithTime;

    /**
     * The underlying sportevent record.
     *
     * @var \stdClass
     */
    protected $record;

    /**
     * Create a new sportevent record instance.
     *
     * @param  \stdClass  $record
     * @return void
     */
    public function __construct($record)
    {
        $this->record = $record;
    }

    /**
     * Increment the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function increment()
    {
        $this->record->attempts++;

        return $this->record->attempts;
    }

    /**
     * Update the "reserved at" timestamp of the sportevent.
     *
     * @return int
     */
    public function touch()
    {
        $this->record->reserved_at = $this->currentTime();

        return $this->record->reserved_at;
    }

    /**
     * Dynamically access the underlying sportevent information.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->record->{$key};
    }
}
