<?php

namespace Illuminate\Queue\sportevents;

use Aws\Sqs\SqsClient;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\sportevent as sporteventContract;

class Sqssportevent extends sportevent implements sporteventContract
{
    /**
     * The Amazon SQS client instance.
     *
     * @var \Aws\Sqs\SqsClient
     */
    protected $sqs;

    /**
     * The Amazon SQS sportevent instance.
     *
     * @var array
     */
    protected $sportevent;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Aws\Sqs\SqsClient  $sqs
     * @param  array  $sportevent
     * @param  string  $connectionName
     * @param  string  $queue
     * @return void
     */
    public function __construct(Container $container, SqsClient $sqs, array $sportevent, $connectionName, $queue)
    {
        $this->sqs = $sqs;
        $this->sportevent = $sportevent;
        $this->queue = $queue;
        $this->container = $container;
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

        $this->sqs->changeMessageVisibility([
            'QueueUrl' => $this->queue,
            'ReceiptHandle' => $this->sportevent['ReceiptHandle'],
            'VisibilityTimeout' => $delay,
        ]);
    }

    /**
     * Delete the sportevent from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->sqs->deleteMessage([
            'QueueUrl' => $this->queue, 'ReceiptHandle' => $this->sportevent['ReceiptHandle'],
        ]);
    }

    /**
     * Get the number of times the sportevent has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return (int) $this->sportevent['Attributes']['ApproximateReceiveCount'];
    }

    /**
     * Get the sportevent identifier.
     *
     * @return string
     */
    public function getsporteventId()
    {
        return $this->sportevent['MessageId'];
    }

    /**
     * Get the raw body string for the sportevent.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->sportevent['Body'];
    }

    /**
     * Get the underlying SQS client instance.
     *
     * @return \Aws\Sqs\SqsClient
     */
    public function getSqs()
    {
        return $this->sqs;
    }

    /**
     * Get the underlying raw SQS sportevent.
     *
     * @return array
     */
    public function getSqssportevent()
    {
        return $this->sportevent;
    }
}
