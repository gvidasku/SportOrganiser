<?php

namespace Illuminate\Queue;

use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\sportevents\Beanstalkdsportevent;
use Pheanstalk\sportevent as Pheanstalksportevent;
use Pheanstalk\Pheanstalk;

class BeanstalkdQueue extends Queue implements QueueContract
{
    /**
     * The Pheanstalk instance.
     *
     * @var \Pheanstalk\Pheanstalk
     */
    protected $pheanstalk;

    /**
     * The name of the default tube.
     *
     * @var string
     */
    protected $default;

    /**
     * The "time to run" for all pushed sportevents.
     *
     * @var int
     */
    protected $timeToRun;

    /**
     * The maximum number of seconds to block for a sportevent.
     *
     * @var int
     */
    protected $blockFor;

    /**
     * Create a new Beanstalkd queue instance.
     *
     * @param  \Pheanstalk\Pheanstalk  $pheanstalk
     * @param  string  $default
     * @param  int  $timeToRun
     * @param  int  $blockFor
     * @return void
     */
    public function __construct(Pheanstalk $pheanstalk, $default, $timeToRun, $blockFor = 0)
    {
        $this->default = $default;
        $this->blockFor = $blockFor;
        $this->timeToRun = $timeToRun;
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * Get the size of the queue.
     *
     * @param  string|null  $queue
     * @return int
     */
    public function size($queue = null)
    {
        $queue = $this->getQueue($queue);

        return (int) $this->pheanstalk->statsTube($queue)->current_sportevents_ready;
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
        return $this->pushRaw($this->createPayload($sportevent, $this->getQueue($queue), $data), $queue);
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
        return $this->pheanstalk->useTube($this->getQueue($queue))->put(
            $payload, Pheanstalk::DEFAULT_PRIORITY, Pheanstalk::DEFAULT_DELAY, $this->timeToRun
        );
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
        $pheanstalk = $this->pheanstalk->useTube($this->getQueue($queue));

        return $pheanstalk->put(
            $this->createPayload($sportevent, $this->getQueue($queue), $data),
            Pheanstalk::DEFAULT_PRIORITY,
            $this->secondsUntil($delay),
            $this->timeToRun
        );
    }

    /**
     * Pop the next sportevent off of the queue.
     *
     * @param  string|null  $queue
     * @return \Illuminate\Contracts\Queue\sportevent|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $sportevent = $this->pheanstalk->watchOnly($queue)->reserveWithTimeout($this->blockFor);

        if ($sportevent instanceof Pheanstalksportevent) {
            return new Beanstalkdsportevent(
                $this->container, $this->pheanstalk, $sportevent, $this->connectionName, $queue
            );
        }
    }

    /**
     * Delete a message from the Beanstalk queue.
     *
     * @param  string  $queue
     * @param  string|int  $id
     * @return void
     */
    public function deleteMessage($queue, $id)
    {
        $queue = $this->getQueue($queue);

        $this->pheanstalk->useTube($queue)->delete(new Pheanstalksportevent($id, ''));
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null  $queue
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ?: $this->default;
    }

    /**
     * Get the underlying Pheanstalk instance.
     *
     * @return \Pheanstalk\Pheanstalk
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }
}
