<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;

class ForgetFailedCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'queue:forget {id : The ID of the failed sportevent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a failed queue sportevent';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->laravel['queue.failer']->forget($this->argument('id'))) {
            $this->info('Failed sportevent deleted successfully!');
        } else {
            $this->error('No failed sportevent matches the given ID.');
        }
    }
}
