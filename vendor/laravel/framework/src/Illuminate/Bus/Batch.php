<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\SerializableClosure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;
use Throwable;

class Batch implements Arrayable, JsonSerializable
{
    /**
     * The queue factory implementation.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $queue;

    /**
     * The repository implementation.
     *
     * @var \Illuminate\Bus\BatchRepository
     */
    protected $repository;

    /**
     * The batch ID.
     *
     * @var string
     */
    public $id;

    /**
     * The batch name.
     *
     * @var string
     */
    public $name;

    /**
     * The total number of sportevents that belong to the batch.
     *
     * @var int
     */
    public $totalsportevents;

    /**
     * The total number of sportevents that are still pending.
     *
     * @var int
     */
    public $pendingsportevents;

    /**
     * The total number of sportevents that have failed.
     *
     * @var int
     */
    public $failedsportevents;

    /**
     * The IDs of the sportevents that have failed.
     *
     * @var array
     */
    public $failedsporteventIds;

    /**
     * The batch options.
     *
     * @var array
     */
    public $options;

    /**
     * The date indicating when the batch was created.
     *
     * @var \Carbon\CarbonImmutable
     */
    public $createdAt;

    /**
     * The date indicating when the batch was cancelled.
     *
     * @var \Carbon\CarbonImmutable|null
     */
    public $cancelledAt;

    /**
     * The date indicating when the batch was finished.
     *
     * @var \Carbon\CarbonImmutable|null
     */
    public $finishedAt;

    /**
     * Create a new batch instance.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $queue
     * @param  \Illuminate\Bus\BatchRepository  $repository
     * @param  string  $id
     * @param  string  $name
     * @param  int  $totalsportevents
     * @param  int  $pendingsportevents
     * @param  int  $failedsportevents
     * @param  array  $failedsporteventIds
     * @param  array  $options
     * @param  \Carbon\CarbonImmutable  $createdAt
     * @param  \Carbon\CarbonImmutable|null  $cancelledAt
     * @param  \Carbon\CarbonImmutable|null  $finishedAt
     * @return void
     */
    public function __construct(QueueFactory $queue,
                                BatchRepository $repository,
                                string $id,
                                string $name,
                                int $totalsportevents,
                                int $pendingsportevents,
                                int $failedsportevents,
                                array $failedsporteventIds,
                                array $options,
                                CarbonImmutable $createdAt,
                                ?CarbonImmutable $cancelledAt = null,
                                ?CarbonImmutable $finishedAt = null)
    {
        $this->queue = $queue;
        $this->repository = $repository;
        $this->id = $id;
        $this->name = $name;
        $this->totalsportevents = $totalsportevents;
        $this->pendingsportevents = $pendingsportevents;
        $this->failedsportevents = $failedsportevents;
        $this->failedsporteventIds = $failedsporteventIds;
        $this->options = $options;
        $this->createdAt = $createdAt;
        $this->cancelledAt = $cancelledAt;
        $this->finishedAt = $finishedAt;
    }

    /**
     * Get a fresh instance of the batch represented by this ID.
     *
     * @return self
     */
    public function fresh()
    {
        return $this->repository->find($this->id);
    }

    /**
     * Add additional sportevents to the batch.
     *
     * @param  \Illuminate\Support\Collection|array  $sportevents
     * @return self
     */
    public function add($sportevents)
    {
        $count = 0;

        $sportevents = Collection::wrap($sportevents)->map(function ($sportevent) use (&$count) {
            $sportevent = $sportevent instanceof Closure ? CallQueuedClosure::create($sportevent) : $sportevent;

            if (is_array($sportevent)) {
                $count += count($sportevent);

                return with($this->prepareBatchedChain($sportevent), function ($chain) {
                    return $chain->first()->chain($chain->slice(1)->values()->all());
                });
            } else {
                $sportevent->withBatchId($this->id);

                $count++;
            }

            return $sportevent;
        });

        $this->repository->transaction(function () use ($sportevents, $count) {
            $this->repository->incrementTotalsportevents($this->id, $count);

            $this->queue->connection($this->options['connection'] ?? null)->bulk(
                $sportevents->all(),
                $data = '',
                $this->options['queue'] ?? null
            );
        });

        return $this->fresh();
    }

    /**
     * Prepare a chain that exists within the sportevents being added.
     *
     * @param  array  $chain
     * @return \Illuminate\Support\Collection
     */
    protected function prepareBatchedChain(array $chain)
    {
        return collect($chain)->map(function ($sportevent) {
            $sportevent = $sportevent instanceof Closure ? CallQueuedClosure::create($sportevent) : $sportevent;

            return $sportevent->withBatchId($this->id);
        });
    }

    /**
     * Get the total number of sportevents that have been processed by the batch thus far.
     *
     * @return int
     */
    public function processedsportevents()
    {
        return $this->totalsportevents - $this->pendingsportevents;
    }

    /**
     * Get the percentage of sportevents that have been processed (between 0-100).
     *
     * @return int
     */
    public function progress()
    {
        return $this->totalsportevents > 0 ? round(($this->processedsportevents() / $this->totalsportevents) * 100) : 0;
    }

    /**
     * Record that a sportevent within the batch finished successfully, executing any callbacks if necessary.
     *
     * @param  string  $sporteventId
     * @return void
     */
    public function recordSuccessfulsportevent(string $sporteventId)
    {
        $counts = $this->decrementPendingsportevents($sporteventId);

        if ($counts->pendingsportevents === 0) {
            $this->repository->markAsFinished($this->id);
        }

        if ($counts->pendingsportevents === 0 && $this->hasThenCallbacks()) {
            $batch = $this->fresh();

            collect($this->options['then'])->each(function ($handler) use ($batch) {
                $this->invokeHandlerCallback($handler, $batch);
            });
        }

        if ($counts->allsporteventsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $batch = $this->fresh();

            collect($this->options['finally'])->each(function ($handler) use ($batch) {
                $this->invokeHandlerCallback($handler, $batch);
            });
        }
    }

    /**
     * Decrement the pending sportevents for the batch.
     *
     * @param  string  $sporteventId
     * @return \Illuminate\Bus\UpdatedBatchsporteventCounts
     */
    public function decrementPendingsportevents(string $sporteventId)
    {
        return $this->repository->decrementPendingsportevents($this->id, $sporteventId);
    }

    /**
     * Determine if the batch has finished executing.
     *
     * @return bool
     */
    public function finished()
    {
        return ! is_null($this->finishedAt);
    }

    /**
     * Determine if the batch has "success" callbacks.
     *
     * @return bool
     */
    public function hasThenCallbacks()
    {
        return isset($this->options['then']) && ! empty($this->options['then']);
    }

    /**
     * Determine if the batch allows sportevents to fail without cancelling the batch.
     *
     * @return bool
     */
    public function allowsFailures()
    {
        return Arr::get($this->options, 'allowFailures', false) === true;
    }

    /**
     * Determine if the batch has sportevent failures.
     *
     * @return bool
     */
    public function hasFailures()
    {
        return $this->failedsportevents > 0;
    }

    /**
     * Record that a sportevent within the batch failed to finish successfully, executing any callbacks if necessary.
     *
     * @param  string  $sporteventId
     * @param  \Throwable  $e
     * @return void
     */
    public function recordFailedsportevent(string $sporteventId, $e)
    {
        $counts = $this->incrementFailedsportevents($sporteventId);

        if ($counts->failedsportevents === 1 && ! $this->allowsFailures()) {
            $this->cancel();
        }

        if ($counts->failedsportevents === 1 && $this->hasCatchCallbacks()) {
            $batch = $this->fresh();

            collect($this->options['catch'])->each(function ($handler) use ($batch, $e) {
                $this->invokeHandlerCallback($handler, $batch, $e);
            });
        }

        if ($counts->allsporteventsHaveRanExactlyOnce() && $this->hasFinallyCallbacks()) {
            $batch = $this->fresh();

            collect($this->options['finally'])->each(function ($handler) use ($batch, $e) {
                $this->invokeHandlerCallback($handler, $batch, $e);
            });
        }
    }

    /**
     * Increment the failed sportevents for the batch.
     *
     * @param  string  $sporteventId
     * @return \Illuminate\Bus\UpdatedBatchsporteventCounts
     */
    public function incrementFailedsportevents(string $sporteventId)
    {
        return $this->repository->incrementFailedsportevents($this->id, $sporteventId);
    }

    /**
     * Determine if the batch has "catch" callbacks.
     *
     * @return bool
     */
    public function hasCatchCallbacks()
    {
        return isset($this->options['catch']) && ! empty($this->options['catch']);
    }

    /**
     * Determine if the batch has "then" callbacks.
     *
     * @return bool
     */
    public function hasFinallyCallbacks()
    {
        return isset($this->options['finally']) && ! empty($this->options['finally']);
    }

    /**
     * Cancel the batch.
     *
     * @return void
     */
    public function cancel()
    {
        $this->repository->cancel($this->id);
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function canceled()
    {
        return $this->cancelled();
    }

    /**
     * Determine if the batch has been cancelled.
     *
     * @return bool
     */
    public function cancelled()
    {
        return ! is_null($this->cancelledAt);
    }

    /**
     * Delete the batch from storage.
     *
     * @return void
     */
    public function delete()
    {
        $this->repository->delete($this->id);
    }

    /**
     * Invoke a batch callback handler.
     *
     * @param  \Illuminate\Queue\SerializableClosure|callable  $handler
     * @param  \Illuminate\Bus\Batch  $batch
     * @param  \Throwable|null  $e
     * @return void
     */
    protected function invokeHandlerCallback($handler, Batch $batch, Throwable $e = null)
    {
        return $handler instanceof SerializableClosure
                    ? $handler->__invoke($batch, $e)
                    : call_user_func($handler, $batch, $e);
    }

    /**
     * Convert the batch to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'totalsportevents' => $this->totalsportevents,
            'pendingsportevents' => $this->pendingsportevents,
            'processedsportevents' => $this->processedsportevents(),
            'progress' => $this->progress(),
            'failedsportevents' => $this->failedsportevents,
            'options' => $this->options,
            'createdAt' => $this->createdAt,
            'cancelledAt' => $this->cancelledAt,
            'finishedAt' => $this->finishedAt,
        ];
    }

    /**
     * Get the JSON serializable representation of the object.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
