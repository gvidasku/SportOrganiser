<?php

namespace Illuminate\Queue\Console;

use Illuminate\Bus\BatchRepository;
use Illuminate\Console\Command;

class RetryBatchCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'queue:retry-batch {id : The ID of the batch whose failed sportevents should be retried}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry the failed sportevents for a batch';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $batch = $this->laravel[BatchRepository::class]->find($this->argument('id'));

        if (! $batch) {
            $this->error("Unable to find a batch with ID [{$id}].");

            return 1;
        } elseif (empty($batch->failedsporteventIds)) {
            $this->error('The given batch does not contain any failed sportevents.');

            return 1;
        }

        foreach ($batch->failedsporteventIds as $failedsporteventId) {
            $this->call('queue:retry', ['id' => $failedsporteventId]);
        }
    }
}
