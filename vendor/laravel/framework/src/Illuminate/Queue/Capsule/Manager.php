<?php

namespace Illuminate\Queue\Capsule;

use Illuminate\Container\Container;
use Illuminate\Queue\QueueManager;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Support\Traits\CapsuleManagerTrait;

/**
 * @mixin \Illuminate\Queue\QueueManager
 * @mixin \Illuminate\Contracts\Queue\Queue
 */
class Manager
{
    use CapsuleManagerTrait;

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Queue\QueueManager
     */
    protected $manager;

    /**
     * Create a new queue capsule manager.
     *
     * @param  \Illuminate\Container\Container|null  $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->setupContainer($container ?: new Container);

        // Once we have the container setup, we will setup the default configuration
        // options in the container "config" bindings. This just makes this queue
        // manager behave correctly since all the correct binding are in place.
        $this->setupDefaultConfiguration();

        $this->setupManager();

        $this->registerConnectors();
    }

    /**
     * Setup the default queue configuration options.
     *
     * @return void
     */
    protected function setupDefaultConfiguration()
    {
        $this->container['config']['queue.default'] = 'default';
    }

    /**
     * Build the queue manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $this->manager = new QueueManager($this->container);
    }

    /**
     * Register the default connectors that the component ships with.
     *
     * @return void
     */
    protected function registerConnectors()
    {
        $provider = new QueueServiceProvider($this->container);

        $provider->registerConnectors($this->manager);
    }

    /**
     * Get a connection instance from the global manager.
     *
     * @param  string|null  $connection
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public static function connection($connection = null)
    {
        return static::$instance->getConnection($connection);
    }

    /**
     * Push a new sportevent onto the queue.
     *
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return mixed
     */
    public static function push($sportevent, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->push($sportevent, $data, $queue);
    }

    /**
     * Push a new an array of sportevents onto the queue.
     *
     * @param  array  $sportevents
     * @param  mixed  $data
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return mixed
     */
    public static function bulk($sportevents, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->bulk($sportevents, $data, $queue);
    }

    /**
     * Push a new sportevent onto the queue after a delay.
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  string  $sportevent
     * @param  mixed  $data
     * @param  string|null  $queue
     * @param  string|null  $connection
     * @return mixed
     */
    public static function later($delay, $sportevent, $data = '', $queue = null, $connection = null)
    {
        return static::$instance->connection($connection)->later($delay, $sportevent, $data, $queue);
    }

    /**
     * Get a registered connection instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getConnection($name = null)
    {
        return $this->manager->connection($name);
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array  $config
     * @param  string  $name
     * @return void
     */
    public function addConnection(array $config, $name = 'default')
    {
        $this->container['config']["queue.connections.{$name}"] = $config;
    }

    /**
     * Get the queue manager instance.
     *
     * @return \Illuminate\Queue\QueueManager
     */
    public function getQueueManager()
    {
        return $this->manager;
    }

    /**
     * Pass dynamic instance methods to the manager.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->manager->$method(...$parameters);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::connection()->$method(...$parameters);
    }
}
