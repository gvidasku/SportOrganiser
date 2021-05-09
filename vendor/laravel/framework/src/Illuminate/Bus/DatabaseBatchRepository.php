<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class DatabaseBatchRepository implements BatchRepository
{
    /**
     * The batch factory instance.
     *
     * @var \Illuminate\Bus\BatchFactory
     */
    protected $factory;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The database table to use to store batch information.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new batch repository instance.
     *
     * @param  \Illuminate\Bus\BatchFactory  $factory
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     */
    public function __construct(BatchFactory $factory, Connection $connection, string $table)
    {
        $this->factory = $factory;
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Retrieve a list of batches.
     *
     * @param  int  $limit
     * @param  mixed  $before
     * @return \Illuminate\Bus\Batch[]
     */
    public function get($limit = 50, $before = null)
    {
        return $this->connection->table($this->table)
                            ->orderByDesc('id')
                            ->take($limit)
                            ->when($before, function ($q) use ($before) {
                                return $q->where('id', '<', $before);
                            })
                            ->get()
                            ->map(function ($batch) {
                                return $this->toBatch($batch);
                            })
                            ->all();
    }

    /**
     * Retrieve information about an existing batch.
     *
     * @param  string  $batchId
     * @return \Illuminate\Bus\Batch|null
     */
    public function find(string $batchId)
    {
        $batch = $this->connection->table($this->table)
                            ->where('id', $batchId)
                            ->first();

        if ($batch) {
            return $this->toBatch($batch);
        }
    }

    /**
     * Store a new pending batch.
     *
     * @param  \Illuminate\Bus\PendingBatch  $batch
     * @return \Illuminate\Bus\Batch
     */
    public function store(PendingBatch $batch)
    {
        $id = (string) Str::orderedUuid();

        $this->connection->table($this->table)->insert([
            'id' => $id,
            'name' => $batch->name,
            'total_sportevents' => 0,
            'pending_sportevents' => 0,
            'failed_sportevents' => 0,
            'failed_sportevent_ids' => '[]',
            'options' => serialize($batch->options),
            'created_at' => time(),
            'cancelled_at' => null,
            'finished_at' => null,
        ]);

        return $this->find($id);
    }

    /**
     * Increment the total number of sportevents within the batch.
     *
     * @param  string  $batchId
     * @param  int  $amount
     * @return void
     */
    public function incrementTotalsportevents(string $batchId, int $amount)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'total_sportevents' => new Expression('total_sportevents + '.$amount),
            'pending_sportevents' => new Expression('pending_sportevents + '.$amount),
            'finished_at' => null,
        ]);
    }

    /**
     * Decrement the total number of pending sportevents for the batch.
     *
     * @param  string  $batchId
     * @param  string  $sporteventId
     * @return \Illuminate\Bus\UpdatedBatchsporteventCounts
     */
    public function decrementPendingsportevents(string $batchId, string $sporteventId)
    {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($sporteventId) {
            return [
                'pending_sportevents' => $batch->pending_sportevents - 1,
                'failed_sportevents' => $batch->failed_sportevents,
                'failed_sportevent_ids' => json_encode(array_values(array_diff(json_decode($batch->failed_sportevent_ids, true), [$sporteventId]))),
            ];
        });

        return new UpdatedBatchsporteventCounts(
            $values['pending_sportevents'],
            $values['failed_sportevents']
        );
    }

    /**
     * Increment the total number of failed sportevents for the batch.
     *
     * @param  string  $batchId
     * @param  string  $sporteventId
     * @return \Illuminate\Bus\UpdatedBatchsporteventCounts
     */
    public function incrementFailedsportevents(string $batchId, string $sporteventId)
    {
        $values = $this->updateAtomicValues($batchId, function ($batch) use ($sporteventId) {
            return [
                'pending_sportevents' => $batch->pending_sportevents,
                'failed_sportevents' => $batch->failed_sportevents + 1,
                'failed_sportevent_ids' => json_encode(array_values(array_unique(array_merge(json_decode($batch->failed_sportevent_ids, true), [$sporteventId])))),
            ];
        });

        return new UpdatedBatchsporteventCounts(
            $values['pending_sportevents'],
            $values['failed_sportevents']
        );
    }

    /**
     * Update an atomic value within the batch.
     *
     * @param  string  $batchId
     * @param  \Closure  $callback
     * @return int|null
     */
    protected function updateAtomicValues(string $batchId, Closure $callback)
    {
        return $this->connection->transaction(function () use ($batchId, $callback) {
            $batch = $this->connection->table($this->table)->where('id', $batchId)
                        ->lockForUpdate()
                        ->first();

            return is_null($batch) ? [] : tap($callback($batch), function ($values) use ($batchId) {
                $this->connection->table($this->table)->where('id', $batchId)->update($values);
            });
        });
    }

    /**
     * Mark the batch that has the given ID as finished.
     *
     * @param  string  $batchId
     * @return void
     */
    public function markAsFinished(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'finished_at' => time(),
        ]);
    }

    /**
     * Cancel the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function cancel(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->update([
            'cancelled_at' => time(),
            'finished_at' => time(),
        ]);
    }

    /**
     * Delete the batch that has the given ID.
     *
     * @param  string  $batchId
     * @return void
     */
    public function delete(string $batchId)
    {
        $this->connection->table($this->table)->where('id', $batchId)->delete();
    }

    /**
     * Execute the given Closure within a storage specific transaction.
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function transaction(Closure $callback)
    {
        return $this->connection->transaction(function () use ($callback) {
            return $callback();
        });
    }

    /**
     * Convert the given raw batch to a Batch object.
     *
     * @param  object  $batch
     * @return \Illuminate\Bus\Batch
     */
    protected function toBatch($batch)
    {
        return $this->factory->make(
            $this,
            $batch->id,
            $batch->name,
            (int) $batch->total_sportevents,
            (int) $batch->pending_sportevents,
            (int) $batch->failed_sportevents,
            json_decode($batch->failed_sportevent_ids, true),
            unserialize($batch->options),
            CarbonImmutable::createFromTimestamp($batch->created_at),
            $batch->cancelled_at ? CarbonImmutable::createFromTimestamp($batch->cancelled_at) : $batch->cancelled_at,
            $batch->finished_at ? CarbonImmutable::createFromTimestamp($batch->finished_at) : $batch->finished_at
        );
    }
}
