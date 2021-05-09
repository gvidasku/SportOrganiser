<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\sportevent;
use Illuminate\Queue\Events\sporteventFailed;
use Illuminate\Queue\Events\sporteventProcessed;
use Illuminate\Queue\Events\sporteventProcessing;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Carbon;

class WorkCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'queue:work
                            {connection? : The name of the queue connection to work}
                            {--name=default : The name of the worker}
                            {--queue= : The names of the queues to work}
                            {--daemon : Run the worker in daemon mode (Deprecated)}
                            {--once : Only process the next sportevent on the queue}
                            {--stop-when-empty : Stop when the queue is empty}
                            {--delay=0 : The number of seconds to delay failed sportevents (Deprecated)}
                            {--backoff=0 : The number of seconds to wait before retrying a sportevent that encountered an uncaught exception}
                            {--max-sportevents=0 : The number of sportevents to process before stopping}
                            {--max-time=0 : The maximum number of seconds the worker should run}
                            {--force : Force the worker to run even in maintenance mode}
                            {--memory=128 : The memory limit in megabytes}
                            {--sleep=3 : Number of seconds to sleep when no sportevent is available}
                            {--timeout=60 : The number of seconds a child process can run}
                            {--tries=1 : Number of times to attempt a sportevent before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start processing sportevents on the queue as a daemon';

    /**
     * The queue worker instance.
     *
     * @var \Illuminate\Queue\Worker
     */
    protected $worker;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new queue work command.
     *
     * @param  \Illuminate\Queue\Worker  $worker
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return void
     */
    public function __construct(Worker $worker, Cache $cache)
    {
        parent::__construct();

        $this->cache = $cache;
        $this->worker = $worker;
    }

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if ($this->downForMaintenance() && $this->option('once')) {
            return $this->worker->sleep($this->option('sleep'));
        }

        // We'll listen to the processed and failed events so we can write information
        // to the console as sportevents are processed, which will let the developer watch
        // which sportevents are coming through a queue and be informed on its progress.
        $this->listenForEvents();

        $connection = $this->argument('connection')
                        ?: $this->laravel['config']['queue.default'];

        // We need to get the right queue for the connection which is set in the queue
        // configuration file for the application. We will pull it based on the set
        // connection being run for the queue operation currently being executed.
        $queue = $this->getQueue($connection);

        return $this->runWorker(
            $connection, $queue
        );
    }

    /**
     * Run the worker instance.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @return int|null
     */
    protected function runWorker($connection, $queue)
    {
        return $this->worker->setName($this->option('name'))
                     ->setCache($this->cache)
                     ->{$this->option('once') ? 'runNextsportevent' : 'daemon'}(
            $connection, $queue, $this->gatherWorkerOptions()
        );
    }

    /**
     * Gather all of the queue worker options as a single object.
     *
     * @return \Illuminate\Queue\WorkerOptions
     */
    protected function gatherWorkerOptions()
    {
        $backoff = $this->hasOption('backoff')
                    ? $this->option('backoff')
                    : $this->option('delay');

        return new WorkerOptions(
            $this->option('name'),
            $backoff,
            $this->option('memory'),
            $this->option('timeout'),
            $this->option('sleep'),
            $this->option('tries'),
            $this->option('force'),
            $this->option('stop-when-empty'),
            $this->option('max-sportevents'),
            $this->option('max-time')
        );
    }

    /**
     * Listen for the queue events in order to update the console output.
     *
     * @return void
     */
    protected function listenForEvents()
    {
        $this->laravel['events']->listen(sporteventProcessing::class, function ($event) {
            $this->writeOutput($event->sportevent, 'starting');
        });

        $this->laravel['events']->listen(sporteventProcessed::class, function ($event) {
            $this->writeOutput($event->sportevent, 'success');
        });

        $this->laravel['events']->listen(sporteventFailed::class, function ($event) {
            $this->writeOutput($event->sportevent, 'failed');

            $this->logFailedsportevent($event);
        });
    }

    /**
     * Write the status output for the queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  string  $status
     * @return void
     */
    protected function writeOutput(sportevent $sportevent, $status)
    {
        switch ($status) {
            case 'starting':
                return $this->writeStatus($sportevent, 'Processing', 'comment');
            case 'success':
                return $this->writeStatus($sportevent, 'Processed', 'info');
            case 'failed':
                return $this->writeStatus($sportevent, 'Failed', 'error');
        }
    }

    /**
     * Format the status output for the queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  string  $status
     * @param  string  $type
     * @return void
     */
    protected function writeStatus(sportevent $sportevent, $status, $type)
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s",
            Carbon::now()->format('Y-m-d H:i:s'),
            $sportevent->getsporteventId(),
            str_pad("{$status}:", 11), $sportevent->resolveName()
        ));
    }

    /**
     * Store a failed sportevent event.
     *
     * @param  \Illuminate\Queue\Events\sporteventFailed  $event
     * @return void
     */
    protected function logFailedsportevent(sporteventFailed $event)
    {
        $this->laravel['queue.failer']->log(
            $event->connectionName,
            $event->sportevent->getQueue(),
            $event->sportevent->getRawBody(),
            $event->exception
        );
    }

    /**
     * Get the queue name for the worker.
     *
     * @param  string  $connection
     * @return string
     */
    protected function getQueue($connection)
    {
        return $this->option('queue') ?: $this->laravel['config']->get(
            "queue.connections.{$connection}.queue", 'default'
        );
    }

    /**
     * Determine if the worker should run in maintenance mode.
     *
     * @return bool
     */
    protected function downForMaintenance()
    {
        return $this->option('force') ? false : $this->laravel->isDownForMaintenance();
    }
}
