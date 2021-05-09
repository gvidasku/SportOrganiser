<?php

namespace Illuminate\Support\Facades;

use Illuminate\Queue\Worker;
use Illuminate\Support\Testing\Fakes\QueueFake;

/**
 * @method static \Illuminate\Contracts\Queue\sportevent|null pop(string $queue = null)
 * @method static \Illuminate\Contracts\Queue\Queue setConnectionName(string $name)
 * @method static int size(string $queue = null)
 * @method static mixed bulk(array $sportevents, mixed $data = '', string $queue = null)
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, string|object $sportevent, mixed $data = '', string $queue = null)
 * @method static mixed laterOn(string $queue, \DateTimeInterface|\DateInterval|int $delay, string|object $sportevent, mixed $data = '')
 * @method static mixed push(string|object $sportevent, mixed $data = '', $queue = null)
 * @method static mixed pushOn(string $queue, string|object $sportevent, mixed $data = '')
 * @method static mixed pushRaw(string $payload, string $queue = null, array $options = [])
 * @method static string getConnectionName()
 * @method static void assertNotPushed(string $sportevent, callable $callback = null)
 * @method static void assertNothingPushed()
 * @method static void assertPushed(string $sportevent, callable|int $callback = null)
 * @method static void assertPushedOn(string $queue, string $sportevent, callable|int $callback = null)
 * @method static void assertPushedWithChain(string $sportevent, array $expectedChain = [], callable $callback = null)
 * @method static void popUsing(string $workerName, callable $callback)
 *
 * @see \Illuminate\Queue\QueueManager
 * @see \Illuminate\Queue\Queue
 */
class Queue extends Facade
{
    /**
     * Register a callback to be executed to pick sportevents.
     *
     * @param  string  $workerName
     * @param  callable  $callback
     * @return void
     */
    public static function popUsing($workerName, $callback)
    {
        return Worker::popUsing($workerName, $callback);
    }

    /**
     * Replace the bound instance with a fake.
     *
     * @return \Illuminate\Support\Testing\Fakes\QueueFake
     */
    public static function fake()
    {
        static::swap($fake = new QueueFake(static::getFacadeApplication()));

        return $fake;
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queue';
    }
}
