<?php

namespace Illuminate\Support\Testing\Fakes;

use BadMethodCallException;
use Closure;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

class QueueFake extends QueueManager implements Queue
{
    use ReflectsClosures;

    /**
     * All of the sportevents that have been pushed.
     *
     * @var array
     */
    protected $sportevents = [];

    /**
     * Assert if a sportevent was pushed based on a truth-test callback.
     *
     * @param  string|\Closure  $sportevent
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertPushed($sportevent, $callback = null)
    {
        if ($sportevent instanceof Closure) {
            [$sportevent, $callback] = [$this->firstClosureParameterType($sportevent), $sportevent];
        }

        if (is_numeric($callback)) {
            return $this->assertPushedTimes($sportevent, $callback);
        }

        PHPUnit::assertTrue(
            $this->pushed($sportevent, $callback)->count() > 0,
            "The expected [{$sportevent}] sportevent was not pushed."
        );
    }

    /**
     * Assert if a sportevent was pushed a number of times.
     *
     * @param  string  $sportevent
     * @param  int  $times
     * @return void
     */
    protected function assertPushedTimes($sportevent, $times = 1)
    {
        $count = $this->pushed($sportevent)->count();

        PHPUnit::assertSame(
            $times, $count,
            "The expected [{$sportevent}] sportevent was pushed {$count} times instead of {$times} times."
        );
    }

    /**
     * Assert if a sportevent was pushed based on a truth-test callback.
     *
     * @param  string  $queue
     * @param  string|\Closure  $sportevent
     * @param  callable|null  $callback
     * @return void
     */
    public function assertPushedOn($queue, $sportevent, $callback = null)
    {
        if ($sportevent instanceof Closure) {
            [$sportevent, $callback] = [$this->firstClosureParameterType($sportevent), $sportevent];
        }

        return $this->assertPushed($sportevent, function ($sportevent, $pushedQueue) use ($callback, $queue) {
            if ($pushedQueue !== $queue) {
                return false;
            }

            return $callback ? $callback(...func_get_args()) : true;
        });
    }

    /**
     * Assert if a sportevent was pushed with chained sportevents based on a truth-test callback.
     *
     * @param  string  $sportevent
     * @param  array  $expectedChain
     * @param  callable|null  $callback
     * @return void
     */
    public function assertPushedWithChain($sportevent, $expectedChain = [], $callback = null)
    {
        PHPUnit::assertTrue(
            $this->pushed($sportevent, $callback)->isNotEmpty(),
            "The expected [{$sportevent}] sportevent was not pushed."
        );

        PHPUnit::assertTrue(
            collect($expectedChain)->isNotEmpty(),
            'The expected chain can not be empty.'
        );

        $this->isChainOfObjects($expectedChain)
                ? $this->assertPushedWithChainOfObjects($sportevent, $expectedChain, $callback)
                : $this->assertPushedWithChainOfClasses($sportevent, $expectedChain, $callback);
    }

    /**
     * Assert if a sportevent was pushed with an empty chain based on a truth-test callback.
     *
     * @param  string  $sportevent
     * @param  callable|null  $callback
     * @return void
     */
    public function assertPushedWithoutChain($sportevent, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->pushed($sportevent, $callback)->isNotEmpty(),
            "The expected [{$sportevent}] sportevent was not pushed."
        );

        $this->assertPushedWithChainOfClasses($sportevent, [], $callback);
    }

    /**
     * Assert if a sportevent was pushed with chained sportevents based on a truth-test callback.
     *
     * @param  string  $sportevent
     * @param  array  $expectedChain
     * @param  callable|null  $callback
     * @return void
     */
    protected function assertPushedWithChainOfObjects($sportevent, $expectedChain, $callback)
    {
        $chain = collect($expectedChain)->map(function ($sportevent) {
            return serialize($sportevent);
        })->all();

        PHPUnit::assertTrue(
            $this->pushed($sportevent, $callback)->filter(function ($sportevent) use ($chain) {
                return $sportevent->chained == $chain;
            })->isNotEmpty(),
            'The expected chain was not pushed.'
        );
    }

    /**
     * Assert if a sportevent was pushed with chained sportevents based on a truth-test callback.
     *
     * @param  string  $sportevent
     * @param  array  $expectedChain
     * @param  callable|null  $callback
     * @return void
     */
    protected function assertPushedWithChainOfClasses($sportevent, $expectedChain, $callback)
    {
        $matching = $this->pushed($sportevent, $callback)->map->chained->map(function ($chain) {
            return collect($chain)->map(function ($sportevent) {
                return get_class(unserialize($sportevent));
            });
        })->filter(function ($chain) use ($expectedChain) {
            return $chain->all() === $expectedChain;
        });

        PHPUnit::assertTrue(
            $matching->isNotEmpty(), 'The expected chain was not pushed.'
        );
    }

    /**
     * Determine if the given chain is entirely composed of objects.
     *
     * @param  array  $chain
     * @return bool
     */
    protected function isChainOfObjects($chain)
    {
        return ! collect($chain)->contains(function ($sportevent) {
            return ! is_object($sportevent);
        });
    }

    /**
     * Determine if a sportevent was pushed based on a truth-test callback.
     *
     * @param  string|\Closure  $sportevent
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotPushed($sportevent, $callback = null)
    {
        if ($sportevent instanceof Closure) {
            [$sportevent, $callback] = [$this->firstClosureParameterType($sportevent), $sportevent];
        }

        PHPUnit::assertCount(
            0, $this->pushed($sportevent, $callback),
            "The unexpected [{$sportevent}] sportevent was pushed."
        );
    }

    /**
     * Assert that no sportevents were pushed.
     *
     * @return void
     */
    public function assertNothingPushed()
    {
        PHPUnit::assertEmpty($this->sportevents, 'sportevents were pushed unexpectedly.');
    }

    /**
     * Get all of the sportevents matching a truth-test callback.
     *
     * @param  string  $sportevent
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function pushed($sportevent, $callback = null)
    {
        if (! $this->hasPushed($sportevent)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->sportevents[$sportevent])->filter(function ($data) use ($callback) {
            return $callback($data['sportevent'], $data['queue']);
        })->pluck('sportevent');
    }

    /**
     * Determine if there are any stored sportevents for a given class.
     *
     * @param  string  $sportevent
     * @return bool
     */
    public function hasPushed($sportevent)
    {
        return isset($this->sportevents[$sportevent]) && ! empty($this->sportevents[$sportevent]);
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param  mixed  $value
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connection($value = null)
    {
        return $this;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return collect($this->sportevents)->flatten(1)->filter(function ($sportevent) use ($queue) {
            return $sportevent['queue'] === $queue;
        })->count();
    }

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function push($sportevent, $data = '', $queue = null)
    {
        $this->sportevents[is_object($sportevent) ? get_class($sportevent) : $sportevent][] = [
            'sportevent' => $sportevent,
            'queue' => $queue,
        ];
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string  $payload
     * @param  string|null  $queue
     * @param  array  $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        //
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function later($delay, $sportevent, $data = '', $queue = null)
    {
        return $this->push($sportevent, $data, $queue);
    }

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string  $queue
     * @param  string  $sportevent
     * @param  mixed  $data
     * @return mixed
     */
    public function pushOn($queue, $sportevent, $data = '')
    {
        return $this->push($sportevent, $data, $queue);
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  string  $queue
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $sportevent
     * @param  mixed  $data
     * @return mixed
     */
    public function laterOn($queue, $delay, $sportevent, $data = '')
    {
        return $this->push($sportevent, $data, $queue);
    }

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    public function pop($queue = null)
    {
        //
    }

    /**
     * Push an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return mixed
     */
    public function bulk($sportevents, $data = '', $queue = null)
    {
        foreach ($sportevents as $sportevent) {
            $this->push($sportevent, $data, $queue);
        }
    }

    /**
     * Get the sportevents that have been pushed.
     *
     * @return array
     */
    public function pushedsportevents()
    {
        return $this->sportevents;
    }

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName()
    {
        //
    }

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name)
    {
        return $this;
    }

    /**
     * Override the QueueManager to prevent circular dependency.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $method
        ));
    }
}
