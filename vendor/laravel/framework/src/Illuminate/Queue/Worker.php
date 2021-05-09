<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Factory as QueueManager;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Queue\Events\sporteventExceptionOccurred;
use Illuminate\Queue\Events\sporteventProcessed;
use Illuminate\Queue\Events\sporteventProcessing;
use Illuminate\Queue\Events\Looping;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Carbon;
use Throwable;

class Worker
{
    use DetectsLostConnections;

    const EXIT_SUCCESS = 0;
    const EXIT_ERROR = 1;
    const EXIT_MEMORY_LIMIT = 12;

    /**
     * The name of the worker.
     *
     * @var string
     */
    protected $name;

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $manager;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * The callback used to determine if the application is in maintenance mode.
     *
     * @var callable
     */
    protected $isDownForMaintenance;

    /**
     * Indicates if the worker should exit.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    public $paused = false;

    /**
     * The callbacks used to pop sportevents from queues.
     *
     * @var callable[]
     */
    protected static $popCallbacks = [];

    /**
     * Create a new queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $manager
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptions
     * @param  callable  $isDownForMaintenance
     * @return void
     */
    public function __construct(QueueManager $manager,
                                Dispatcher $events,
                                ExceptionHandler $exceptions,
                                callable $isDownForMaintenance)
    {
        $this->events = $events;
        $this->manager = $manager;
        $this->exceptions = $exceptions;
        $this->isDownForMaintenance = $isDownForMaintenance;
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $lastRestart = $this->getTimestampOfLastQueueRestart();

        [$startTime, $sporteventsProcessed] = [hrtime(true) / 1e9, 0];

        while (true) {
            // Before reserving any sportevents, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
            if (! $this->daemonShouldRun($options, $connectionName, $queue)) {
                $status = $this->pauseWorker($options, $lastRestart);

                if (! is_null($status)) {
                    return $this->stop($status);
                }

                continue;
            }

            // First, we will attempt to get the next sportevent off of the queue. We will also
            // register the timeout handler and reset the alarm for this sportevent so it is
            // not stuck in a frozen state forever. Then, we can fire off this sportevent.
            $sportevent = $this->getNextsportevent(
                $this->manager->connection($connectionName), $queue
            );

            if ($this->supportsAsyncSignals()) {
                $this->registerTimeoutHandler($sportevent, $options);
            }

            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this sportevent for processing. Otherwise, we will need to sleep the
            // worker so no more sportevents are processed until they should be processed.
            if ($sportevent) {
                $sporteventsProcessed++;

                $this->runsportevent($sportevent, $connectionName, $options);
            } else {
                $this->sleep($options->sleep);
            }

            if ($this->supportsAsyncSignals()) {
                $this->resetTimeoutHandler();
            }

            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
            $status = $this->stopIfNecessary(
                $options, $lastRestart, $startTime, $sporteventsProcessed, $sportevent
            );

            if (! is_null($status)) {
                return $this->stop($status);
            }
        }
    }

    /**
     * Register the worker timeout handler.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent|null  $sportevent
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function registerTimeoutHandler($sportevent, WorkerOptions $options)
    {
        // We will register a signal handler for the alarm signal so that we can kill this
        // process if it is running too long because it has frozen. This uses the async
        // signals supported in recent versions of PHP to accomplish it conveniently.
        pcntl_signal(SIGALRM, function () use ($sportevent, $options) {
            if ($sportevent) {
                $this->marksporteventAsFailedIfWillExceedMaxAttempts(
                    $sportevent->getConnectionName(), $sportevent, (int) $options->maxTries, $e = $this->maxAttemptsExceededException($sportevent)
                );

                $this->marksporteventAsFailedIfWillExceedMaxExceptions(
                    $sportevent->getConnectionName(), $sportevent, $e
                );
            }

            $this->kill(static::EXIT_ERROR);
        });

        pcntl_alarm(
            max($this->timeoutForsportevent($sportevent, $options), 0)
        );
    }

    /**
     * Reset the worker timeout handler.
     *
     * @return void
     */
    protected function resetTimeoutHandler()
    {
        pcntl_alarm(0);
    }

    /**
     * Get the appropriate timeout for the given sportevent.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent|null  $sportevent
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    protected function timeoutForsportevent($sportevent, WorkerOptions $options)
    {
        return $sportevent && ! is_null($sportevent->timeout()) ? $sportevent->timeout() : $options->timeout;
    }

    /**
     * Determine if the daemon should process on this iteration.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  string  $connectionName
     * @param  string  $queue
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options, $connectionName, $queue)
    {
        return ! ((($this->isDownForMaintenance)() && ! $options->force) ||
            $this->paused ||
            $this->events->until(new Looping($connectionName, $queue)) === false);
    }

    /**
     * Pause the worker for the current loop.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @return int|null
     */
    protected function pauseWorker(WorkerOptions $options, $lastRestart)
    {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);

        return $this->stopIfNecessary($options, $lastRestart);
    }

    /**
     * Determine the exit code to stop the process if necessary.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @param  int  $startTime
     * @param  int  $sporteventsProcessed
     * @param  mixed  $sportevent
     * @return int|null
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart, $startTime = 0, $sporteventsProcessed = 0, $sportevent = null)
    {
        if ($this->shouldQuit) {
            return static::EXIT_SUCCESS;
        } elseif ($this->memoryExceeded($options->memory)) {
            return static::EXIT_MEMORY_LIMIT;
        } elseif ($this->queueShouldRestart($lastRestart)) {
            return static::EXIT_SUCCESS;
        } elseif ($options->stopWhenEmpty && is_null($sportevent)) {
            return static::EXIT_SUCCESS;
        } elseif ($options->maxTime && hrtime(true) / 1e9 - $startTime >= $options->maxTime) {
            return static::EXIT_SUCCESS;
        } elseif ($options->maxsportevents && $sporteventsProcessed >= $options->maxsportevents) {
            return static::EXIT_SUCCESS;
        }
    }

    /**
     * Process the next sportevent on the queue.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function runNextsportevent($connectionName, $queue, WorkerOptions $options)
    {
        $sportevent = $this->getNextsportevent(
            $this->manager->connection($connectionName), $queue
        );

        // If we're able to pull a sportevent off of the stack, we will process it and then return
        // from this method. If there is no sportevent on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing sportevents after sleep.
        if ($sportevent) {
            return $this->runsportevent($sportevent, $connectionName, $options);
        }

        $this->sleep($options->sleep);
    }

    /**
     * Get the next sportevent from the queue connection.
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $connection
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    protected function getNextsportevent($connection, $queue)
    {
        $popsporteventCallback = function ($queue) use ($connection) {
            return $connection->pop($queue);
        };

        try {
            if (isset(static::$popCallbacks[$this->name])) {
                return (static::$popCallbacks[$this->name])($popsporteventCallback, $queue);
            }

            foreach (explode(',', $queue) as $queue) {
                if (! is_null($sportevent = $popsporteventCallback($queue))) {
                    return $sportevent;
                }
            }
        } catch (Throwable $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);

            $this->sleep(1);
        }
    }

    /**
     * Process the given sportevent.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function runsportevent($sportevent, $connectionName, WorkerOptions $options)
    {
        try {
            return $this->process($connectionName, $sportevent, $options);
        } catch (Throwable $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);
        }
    }

    /**
     * Stop the worker if we have lost connection to a database.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function stopWorkerIfLostConnection($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->shouldQuit = true;
        }
    }

    /**
     * Process the given sportevent from the queue.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     *
     * @throws \Throwable
     */
    public function process($connectionName, $sportevent, WorkerOptions $options)
    {
        try {
            // First we will raise the before sportevent event and determine if the sportevent has already ran
            // over its maximum attempt limits, which could primarily happen when this sportevent is
            // continually timing out and not actually throwing any exceptions from itself.
            $this->raiseBeforesporteventEvent($connectionName, $sportevent);

            $this->marksporteventAsFailedIfAlreadyExceedsMaxAttempts(
                $connectionName, $sportevent, (int) $options->maxTries
            );

            if ($sportevent->isDeleted()) {
                return $this->raiseAftersporteventEvent($connectionName, $sportevent);
            }

            // Here we will fire off the sportevent and let it process. We will catch any exceptions so
            // they can be reported to the developers logs, etc. Once the sportevent is finished the
            // proper events will be fired to let any listeners know this sportevent has finished.
            $sportevent->fire();

            $this->raiseAftersporteventEvent($connectionName, $sportevent);
        } catch (Throwable $e) {
            $this->handlesporteventException($connectionName, $sportevent, $options, $e);
        }
    }

    /**
     * Handle an exception that occurred while the sportevent was running.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    protected function handlesporteventException($connectionName, $sportevent, WorkerOptions $options, Throwable $e)
    {
        try {
            // First, we will go ahead and mark the sportevent as failed if it will exceed the maximum
            // attempts it is allowed to run the next time we process it. If so we will just
            // go ahead and mark it as failed now so we do not have to release this again.
            if (! $sportevent->hasFailed()) {
                $this->marksporteventAsFailedIfWillExceedMaxAttempts(
                    $connectionName, $sportevent, (int) $options->maxTries, $e
                );

                $this->marksporteventAsFailedIfWillExceedMaxExceptions(
                    $connectionName, $sportevent, $e
                );
            }

            $this->raiseExceptionOccurredsporteventEvent(
                $connectionName, $sportevent, $e
            );
        } finally {
            // If we catch an exception, we will attempt to release the sportevent back onto the queue
            // so it is not lost entirely. This'll let the sportevent be retried at a later time by
            // another listener (or this same one). We will re-throw this exception after.
            if (! $sportevent->isDeleted() && ! $sportevent->isReleased() && ! $sportevent->hasFailed()) {
                $sportevent->release($this->calculateBackoff($sportevent, $options));
            }
        }

        throw $e;
    }

    /**
     * Mark the given sportevent as failed if it has exceeded the maximum allowed attempts.
     *
     * This will likely be because the sportevent previously exceeded a timeout.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  int  $maxTries
     * @return void
     *
     * @throws \Throwable
     */
    protected function marksporteventAsFailedIfAlreadyExceedsMaxAttempts($connectionName, $sportevent, $maxTries)
    {
        $maxTries = ! is_null($sportevent->maxTries()) ? $sportevent->maxTries() : $maxTries;

        $retryUntil = $sportevent->retryUntil();

        if ($retryUntil && Carbon::now()->getTimestamp() <= $retryUntil) {
            return;
        }

        if (! $retryUntil && ($maxTries === 0 || $sportevent->attempts() <= $maxTries)) {
            return;
        }

        $this->failsportevent($sportevent, $e = $this->maxAttemptsExceededException($sportevent));

        throw $e;
    }

    /**
     * Mark the given sportevent as failed if it has exceeded the maximum allowed attempts.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  int  $maxTries
     * @param  \Throwable  $e
     * @return void
     */
    protected function marksporteventAsFailedIfWillExceedMaxAttempts($connectionName, $sportevent, $maxTries, Throwable $e)
    {
        $maxTries = ! is_null($sportevent->maxTries()) ? $sportevent->maxTries() : $maxTries;

        if ($sportevent->retryUntil() && $sportevent->retryUntil() <= Carbon::now()->getTimestamp()) {
            $this->failsportevent($sportevent, $e);
        }

        if ($maxTries > 0 && $sportevent->attempts() >= $maxTries) {
            $this->failsportevent($sportevent, $e);
        }
    }

    /**
     * Mark the given sportevent as failed if it has exceeded the maximum allowed attempts.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $e
     * @return void
     */
    protected function marksporteventAsFailedIfWillExceedMaxExceptions($connectionName, $sportevent, Throwable $e)
    {
        if (! $this->cache || is_null($uuid = $sportevent->uuid()) ||
            is_null($maxExceptions = $sportevent->maxExceptions())) {
            return;
        }

        if (! $this->cache->get('sportevent-exceptions:'.$uuid)) {
            $this->cache->put('sportevent-exceptions:'.$uuid, 0, Carbon::now()->addDay());
        }

        if ($maxExceptions <= $this->cache->increment('sportevent-exceptions:'.$uuid)) {
            $this->cache->forget('sportevent-exceptions:'.$uuid);

            $this->failsportevent($sportevent, $e);
        }
    }

    /**
     * Mark the given sportevent as failed and raise the relevant event.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $e
     * @return void
     */
    protected function failsportevent($sportevent, Throwable $e)
    {
        return $sportevent->fail($e);
    }

    /**
     * Calculate the backoff for the given sportevent.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    protected function calculateBackoff($sportevent, WorkerOptions $options)
    {
        $backoff = explode(
            ',',
            method_exists($sportevent, 'backoff') && ! is_null($sportevent->backoff())
                        ? $sportevent->backoff()
                        : $options->backoff
        );

        return (int) ($backoff[$sportevent->attempts() - 1] ?? last($backoff));
    }

    /**
     * Raise the before queue sportevent event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return void
     */
    protected function raiseBeforesporteventEvent($connectionName, $sportevent)
    {
        $this->events->dispatch(new sporteventProcessing(
            $connectionName, $sportevent
        ));
    }

    /**
     * Raise the after queue sportevent event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return void
     */
    protected function raiseAftersporteventEvent($connectionName, $sportevent)
    {
        $this->events->dispatch(new sporteventProcessed(
            $connectionName, $sportevent
        ));
    }

    /**
     * Raise the exception occurred queue sportevent event.
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $e
     * @return void
     */
    protected function raiseExceptionOccurredsporteventEvent($connectionName, $sportevent, Throwable $e)
    {
        $this->events->dispatch(new sporteventExceptionOccurred(
            $connectionName, $sportevent, $e
        ));
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param  int|null  $lastRestart
     * @return bool
     */
    protected function queueShouldRestart($lastRestart)
    {
        return $this->getTimestampOfLastQueueRestart() != $lastRestart;
    }

    /**
     * Get the last queue restart timestamp, or null.
     *
     * @return int|null
     */
    protected function getTimestampOfLastQueueRestart()
    {
        if ($this->cache) {
            return $this->cache->get('illuminate:queue:restart');
        }
    }

    /**
     * Enable async signals for the process.
     *
     * @return void
     */
    protected function listenForSignals()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, function () {
            $this->shouldQuit = true;
        });

        pcntl_signal(SIGUSR2, function () {
            $this->paused = true;
        });

        pcntl_signal(SIGCONT, function () {
            $this->paused = false;
        });
    }

    /**
     * Determine if "async" signals are supported.
     *
     * @return bool
     */
    protected function supportsAsyncSignals()
    {
        return extension_loaded('pcntl');
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int  $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @param  int  $status
     * @return int
     */
    public function stop($status = 0)
    {
        $this->events->dispatch(new WorkerStopping($status));

        return $status;
    }

    /**
     * Kill the process.
     *
     * @param  int  $status
     * @return void
     */
    public function kill($status = 0)
    {
        $this->events->dispatch(new WorkerStopping($status));

        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        exit($status);
    }

    /**
     * Create an instance of MaxAttemptsExceededException.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @return \Illuminate\Queue\MaxAttemptsExceededException
     */
    protected function maxAttemptsExceededException($sportevent)
    {
        return new MaxAttemptsExceededException(
            $sportevent->resolveName().' has been attempted too many times or run too long. The sportevent may have previously timed out.'
        );
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int|float  $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Set the cache repository implementation.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return $this
     */
    public function setCache(CacheContract $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Set the name of the worker.
     *
     * @param  string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Register a callback to be executed to pick sportevents.
     *
     * @param  string  $workerName
     * @param  callable  $callback
     * @return void
     */
    public static function popUsing($workerName, $callback)
    {
        if (is_null($callback)) {
            unset(static::$popCallbacks[$workerName]);
        } else {
            static::$popCallbacks[$workerName] = $callback;
        }
    }

    /**
     * Get the queue manager instance.
     *
     * @return \Illuminate\Queue\QueueManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set the queue manager instance.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $manager
     * @return void
     */
    public function setManager(QueueManager $manager)
    {
        $this->manager = $manager;
    }
}
