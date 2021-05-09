<?php

namespace Illuminate\Bus;

class UpdatedBatchsporteventCounts
{
    /**
     * The number of pending sportevents remaining for the batch.
     *
     * @var int
     */
    public $pendingsportevents;

    /**
     * The number of failed sportevents that belong to the batch.
     *
     * @var int
     */
    public $failedsportevents;

    /**
     * Create a new batch sportevent counts object.
     *
     * @param  int  $pendingsportevents
     * @param  int  $failedsportevents
     * @return void
     */
    public function __construct(int $pendingsportevents = 0, int $failedsportevents = 0)
    {
        $this->pendingsportevents = $pendingsportevents;
        $this->failedsportevents = $failedsportevents;
    }

    /**
     * Determine if all sportevents have ran exactly once.
     *
     * @return bool
     */
    public function allsporteventsHaveRanExactlyOnce()
    {
        return ($this->pendingsportevents - $this->failedsportevents) === 0;
    }
}
