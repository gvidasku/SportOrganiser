<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class RetryCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'queue:retry
                            {id?* : The ID of the failed sportevent or "all" to retry all sportevents}
                            {--range=* : Range of sportevent IDs (numeric) to be retried}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry a failed queue sportevent';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->getsporteventIds() as $id) {
            $sportevent = $this->laravel['queue.failer']->find($id);

            if (is_null($sportevent)) {
                $this->error("Unable to find failed sportevent with ID [{$id}].");
            } else {
                $this->retrysportevent($sportevent);

                $this->info("The failed sportevent [{$id}] has been pushed back onto the queue!");

                $this->laravel['queue.failer']->forget($id);
            }
        }
    }

    /**
     * Get the sportevent IDs to be retried.
     *
     * @return array
     */
    protected function getsporteventIds()
    {
        $ids = (array) $this->argument('id');

        if (count($ids) === 1 && $ids[0] === 'all') {
            return Arr::pluck($this->laravel['queue.failer']->all(), 'id');
        }

        if ($ranges = (array) $this->option('range')) {
            $ids = array_merge($ids, $this->getsporteventIdsByRanges($ranges));
        }

        return array_values(array_filter(array_unique($ids)));
    }

    /**
     * Get the sportevent IDs ranges, if applicable.
     *
     * @param  array  $ranges
     * @return array
     */
    protected function getsporteventIdsByRanges(array $ranges)
    {
        $ids = [];

        foreach ($ranges as $range) {
            if (preg_match('/^[0-9]+\-[0-9]+$/', $range)) {
                $ids = array_merge($ids, range(...explode('-', $range)));
            }
        }

        return $ids;
    }

    /**
     * Retry the queue sportevent.
     *
     * @param  \stdClass  $sportevent
     * @return void
     */
    protected function retrysportevent($sportevent)
    {
        $this->laravel['queue']->connection($sportevent->connection)->pushRaw(
            $this->resetAttempts($sportevent->payload), $sportevent->queue
        );
    }

    /**
     * Reset the payload attempts.
     *
     * Applicable to Redis sportevents which store attempts in their payload.
     *
     * @param  string  $payload
     * @return string
     */
    protected function resetAttempts($payload)
    {
        $payload = json_decode($payload, true);

        if (isset($payload['attempts'])) {
            $payload['attempts'] = 0;
        }

        return json_encode($payload);
    }
}
