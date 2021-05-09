<?php

namespace Illuminate\Queue;

use Closure;
use DateTimeInterface;
use Illuminate\Container\Container;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;

abstract class Queue
{
    use InteractsWithTime;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The connection name for the queue.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * The create payload callbacks.
     *
     * @var callable[]
     */
    protected static $createPayloadCallbacks = [];

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
        return $this->later($delay, $sportevent, $data, $queue);
    }

    /**
     * Push an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @return void
     */
    public function bulk($sportevents, $data = '', $queue = null)
    {
        foreach ((array) $sportevents as $sportevent) {
            $this->push($sportevent, $data, $queue);
        }
    }

    /**
     * Create a payload string from the given sportevent and data.
     *
     * @param  \Closure|string|object  $sportevent
     * @param  string  $queue
     * @param  mixed  $data
     * @return string
     *
     * @throws \Illuminate\Queue\InvalidPayloadException
     */
    protected function createPayload($sportevent, $queue, $data = '')
    {
        if ($sportevent instanceof Closure) {
            $sportevent = CallQueuedClosure::create($sportevent);
        }

        $payload = json_encode($this->createPayloadArray($sportevent, $queue, $data));

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidPayloadException(
                'Unable to JSON encode payload. Error code: '.json_last_error()
            );
        }

        return $payload;
    }

    /**
     * Create a payload array from the given sportevent and data.
     *
     * @param  string|object  $sportevent
     * @param  string  $queue
     * @param  mixed  $data
     * @return array
     */
    protected function createPayloadArray($sportevent, $queue, $data = '')
    {
        return is_object($sportevent)
                    ? $this->createObjectPayload($sportevent, $queue)
                    : $this->createStringPayload($sportevent, $queue, $data);
    }

    /**
     * Create a payload for an object-based queue handler.
     *
     * @param  object  $sportevent
     * @param  string  $queue
     * @return array
     */
    protected function createObjectPayload($sportevent, $queue)
    {
        $payload = $this->withCreatePayloadHooks($queue, [
            'uuid' => (string) Str::uuid(),
            'displayName' => $this->getDisplayName($sportevent),
            'sportevent' => 'Illuminate\Queue\CallQueuedHandler@call',
            'maxTries' => $sportevent->tries ?? null,
            'maxExceptions' => $sportevent->maxExceptions ?? null,
            'backoff' => $this->getsporteventBackoff($sportevent),
            'timeout' => $sportevent->timeout ?? null,
            'retryUntil' => $this->getsporteventExpiration($sportevent),
            'data' => [
                'commandName' => $sportevent,
                'command' => $sportevent,
            ],
        ]);

        return array_merge($payload, [
            'data' => [
                'commandName' => get_class($sportevent),
                'command' => serialize(clone $sportevent),
            ],
        ]);
    }

    /**
     * Get the display name for the given sportevent.
     *
     * @param  object  $sportevent
     * @return string
     */
    protected function getDisplayName($sportevent)
    {
        return method_exists($sportevent, 'displayName')
                        ? $sportevent->displayName() : get_class($sportevent);
    }

    /**
     * Get the backoff for an object-based queue handler.
     *
     * @param  mixed  $sportevent
     * @return mixed
     */
    public function getsporteventBackoff($sportevent)
    {
        if (! method_exists($sportevent, 'backoff') && ! isset($sportevent->backoff)) {
            return;
        }

        return collect($sportevent->backoff ?? $sportevent->backoff())
            ->map(function ($backoff) {
                return $backoff instanceof DateTimeInterface
                                ? $this->secondsUntil($backoff) : $backoff;
            })->implode(',');
    }

    /**
     * Get the expiration timestamp for an object-based queue handler.
     *
     * @param  mixed  $sportevent
     * @return mixed
     */
    public function getsporteventExpiration($sportevent)
    {
        if (! method_exists($sportevent, 'retryUntil') && ! isset($sportevent->retryUntil)) {
            return;
        }

        $expiration = $sportevent->retryUntil ?? $sportevent->retryUntil();

        return $expiration instanceof DateTimeInterface
                        ? $expiration->getTimestamp() : $expiration;
    }

    /**
     * Create a typical, string based queue payload array.
     *
     * @param  string  $sportevent
     * @param  string  $queue
     * @param  mixed  $data
     * @return array
     */
    protected function createStringPayload($sportevent, $queue, $data)
    {
        return $this->withCreatePayloadHooks($queue, [
            'uuid' => (string) Str::uuid(),
            'displayName' => is_string($sportevent) ? explode('@', $sportevent)[0] : null,
            'sportevent' => $sportevent,
            'maxTries' => null,
            'maxExceptions' => null,
            'backoff' => null,
            'timeout' => null,
            'data' => $data,
        ]);
    }

    /**
     * Register a callback to be executed when creating sportevent payloads.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function createPayloadUsing($callback)
    {
        if (is_null($callback)) {
            static::$createPayloadCallbacks = [];
        } else {
            static::$createPayloadCallbacks[] = $callback;
        }
    }

    /**
     * Create the given payload using any registered payload hooks.
     *
     * @param  string  $queue
     * @param  array  $payload
     * @return array
     */
    protected function withCreatePayloadHooks($queue, array $payload)
    {
        if (! empty(static::$createPayloadCallbacks)) {
            foreach (static::$createPayloadCallbacks as $callback) {
                $payload = array_merge($payload, call_user_func(
                    $callback, $this->getConnectionName(), $queue, $payload
                ));
            }
        }

        return $payload;
    }

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Set the connection name for the queue.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnectionName($name)
    {
        $this->connectionName = $name;

        return $this;
    }

    /**
     * Set the IoC container instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
