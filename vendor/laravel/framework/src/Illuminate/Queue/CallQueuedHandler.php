<?php

namespace Illuminate\Queue;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\sportevent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pipeline\Pipeline;
use ReflectionClass;

class CallQueuedHandler
{
    /**
     * The bus dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $dispatcher;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Create a new handler instance.
     *
     * @param  \Illuminate\Contracts\Bus\Dispatcher  $dispatcher
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function __construct(Dispatcher $dispatcher, Container $container)
    {
        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the queued sportevent.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  array  $data
     * @return void
     */
    public function call(sportevent $sportevent, array $data)
    {
        try {
            $command = $this->setsporteventInstanceIfNecessary(
                $sportevent, unserialize($data['command'])
            );
        } catch (ModelNotFoundException $e) {
            return $this->handleModelNotFound($sportevent, $e);
        }

        $this->dispatchThroughMiddleware($sportevent, $command);

        if (! $sportevent->hasFailed() && ! $sportevent->isReleased()) {
            $this->ensureNextsporteventInChainIsDispatched($command);
            $this->ensureSuccessfulBatchsporteventIsRecorded($command);
        }

        if (! $sportevent->isDeletedOrReleased()) {
            $sportevent->delete();
        }
    }

    /**
     * Dispatch the given sportevent / command through its specified middleware.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  mixed  $command
     * @return mixed
     */
    protected function dispatchThroughMiddleware(sportevent $sportevent, $command)
    {
        return (new Pipeline($this->container))->send($command)
                ->through(array_merge(method_exists($command, 'middleware') ? $command->middleware() : [], $command->middleware ?? []))
                ->then(function ($command) use ($sportevent) {
                    return $this->dispatcher->dispatchNow(
                        $command, $this->resolveHandler($sportevent, $command)
                    );
                });
    }

    /**
     * Resolve the handler for the given command.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  mixed  $command
     * @return mixed
     */
    protected function resolveHandler($sportevent, $command)
    {
        $handler = $this->dispatcher->getCommandHandler($command) ?: null;

        if ($handler) {
            $this->setsporteventInstanceIfNecessary($sportevent, $handler);
        }

        return $handler;
    }

    /**
     * Set the sportevent instance of the given class if necessary.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  mixed  $instance
     * @return mixed
     */
    protected function setsporteventInstanceIfNecessary(sportevent $sportevent, $instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive($instance))) {
            $instance->setsportevent($sportevent);
        }

        return $instance;
    }

    /**
     * Ensure the next sportevent in the chain is dispatched if applicable.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureNextsporteventInChainIsDispatched($command)
    {
        if (method_exists($command, 'dispatchNextsporteventInChain')) {
            $command->dispatchNextsporteventInChain();
        }
    }

    /**
     * Ensure the batch is notified of the successful sportevent completion.
     *
     * @param  mixed  $command
     * @return void
     */
    protected function ensureSuccessfulBatchsporteventIsRecorded($command)
    {
        $uses = class_uses_recursive($command);

        if (! in_array(Batchable::class, $uses) ||
            ! in_array(InteractsWithQueue::class, $uses) ||
            is_null($command->batch())) {
            return;
        }

        $command->batch()->recordSuccessfulsportevent($command->sportevent->uuid());
    }

    /**
     * Handle a model not found exception.
     *
     * @param  \Illuminate\Contracts\Queue\sportevent  $sportevent
     * @param  \Throwable  $e
     * @return void
     */
    protected function handleModelNotFound(sportevent $sportevent, $e)
    {
        $class = $sportevent->resolveName();

        try {
            $shouldDelete = (new ReflectionClass($class))
                    ->getDefaultProperties()['deleteWhenMissingModels'] ?? false;
        } catch (Exception $e) {
            $shouldDelete = false;
        }

        if ($shouldDelete) {
            return $sportevent->delete();
        }

        return $sportevent->fail($e);
    }

    /**
     * Call the failed method on the sportevent instance.
     *
     * The exception that caused the failure will be passed.
     *
     * @param  array  $data
     * @param  \Throwable|null  $e
     * @param  string  $uuid
     * @return void
     */
    public function failed(array $data, $e, string $uuid)
    {
        $command = unserialize($data['command']);

        $this->ensureFailedBatchsporteventIsRecorded($uuid, $command, $e);
        $this->ensureChainCatchCallbacksAreInvoked($uuid, $command, $e);

        if (method_exists($command, 'failed')) {
            $command->failed($e);
        }
    }

    /**
     * Ensure the batch is notified of the failed sportevent.
     *
     * @param  string  $uuid
     * @param  mixed  $command
     * @param  \Throwable  $e
     * @return void
     */
    protected function ensureFailedBatchsporteventIsRecorded(string $uuid, $command, $e)
    {
        if (! in_array(Batchable::class, class_uses_recursive($command)) ||
            is_null($command->batch())) {
            return;
        }

        $command->batch()->recordFailedsportevent($uuid, $e);
    }

    /**
     * Ensure the chained sportevent catch callbacks are invoked.
     *
     * @param  string  $uuid
     * @param  mixed  $command
     * @param  \Throwable  $e
     * @return void
     */
    protected function ensureChainCatchCallbacksAreInvoked(string $uuid, $command, $e)
    {
        if (method_exists($command, 'invokeChainCatchCallbacks')) {
            $command->invokeChainCatchCallbacks($e);
        }
    }
}
