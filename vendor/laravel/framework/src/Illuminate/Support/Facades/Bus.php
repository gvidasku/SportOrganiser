<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Testing\Fakes\BusFake;

/**
 * @method static \Illuminate\Bus\Batch|null findBatch(string $batchId)
 * @method static \Illuminate\Bus\PendingBatch batch(array $sportevents)
 * @method static \Illuminate\Contracts\Bus\Dispatcher map(array $map)
 * @method static \Illuminate\Contracts\Bus\Dispatcher pipeThrough(array $pipes)
 * @method static \Illuminate\Foundation\Bus\PendingChain chain(array $sportevents)
 * @method static bool hasCommandHandler($command)
 * @method static bool|mixed getCommandHandler($command)
 * @method static mixed dispatch($command)
 * @method static mixed dispatchNow($command, $handler = null)
 * @method static void assertDispatched(string $command, callable|int $callback = null)
 * @method static void assertDispatchedTimes(string $command, int $times = 1)
 * @method static void assertNotDispatched(string $command, callable|int $callback = null)
 *
 * @see \Illuminate\Contracts\Bus\Dispatcher
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $sporteventsToFake
     * @return \Illuminate\Support\Testing\Fakes\BusFake
     */
    public static function fake($sporteventsToFake = [])
    {
        static::swap($fake = new BusFake(static::getFacadeRoot(), $sporteventsToFake));

        return $fake;
    }

    /**
     * Dispatch the given chain of sportevents.
     *
     * @param  array|mixed  $sportevents
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchChain($sportevents)
    {
        $sportevents = is_array($sportevents) ? $sportevents : func_get_args();

        return (new PendingChain(array_shift($sportevents), $sportevents))
                    ->dispatch();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}
