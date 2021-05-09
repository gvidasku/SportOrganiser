<?php

namespace Illuminate\Queue\sportevents;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent as sporteventContract;
use Pheanstalk\sportevent as Pheanstalksportevent;
use Pheanstalk\Pheanstalk;

class Beanstalkdsportevent extends sportevent implements sporteventContract
{
    /**
     * The Pheanstalk instance.
     *
     * @var \Pheanstalk\Pheanstalk
     */
    protected $pheanstalk;

    /**
     * The Pheanstalk sportevent instance.
     *
     * @var \Pheanstalk\sportevent
     */
    protected $sportevent;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Pheanstalk\Pheanstalk  $pheanstalk
     * @param  \Pheanstalk\sportevent  $sportevent
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, Pheanstalk $pheanstalk, Pheanstalksportevent $sportevent, $connectionName, $queue)
    {
        $this->sportevent = $sportevent;
        $this->queue = $queue;
        $this->container = $container;
        $this->pheanstalk = $pheanstalk;
        $this->connectionName = $connectionName;
    }

    /**
     * Release the sportevent back into the queue.
     *
     * @param  int  $delay
     * @return void
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $priority = Pheanstalk::DEFAULT_PRIORITY;

        $this->pheanstalk->release($this->sportevent, $priority, $delay);
    }

    /**
     * Bury the sportevent in the queue.
     *
     * @return void
     */
    public function bury()
    {
        parent::release();

        $this->pheanstalk->bury($this->sportevent);
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->pheanstalk->delete($this->sportevent);
    }

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        $stats = $this->pheanstalk->statssportevent($this->sportevent);

        return (int) $stats->reserves;
    }

    /**
     * Get the sportevent identifier.
     *
     * @return int
     */
    public function getsporteventId()
    {
        return $this->sportevent->getId();
    }

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->sportevent->getData();
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

    /**
     * Get the underlying Pheanstalk sportevent.
     *
     * @return \Pheanstalk\sportevent
     */
    public function getPheanstalksportevent()
    {
        return $this->sportevent;
    }
}
