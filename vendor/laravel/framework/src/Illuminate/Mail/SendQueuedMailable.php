<?php

namespace Illuminate\Mail;

use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailable as MailableContract;

class SendQueuedMailable
{
    /**
     * The mailable message instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailable
     */
    public $mailable;

    /**
     * The number of times the sportevent may be attempted.
     *
     * @var int
     */
    public $tries;

    /**
     * The number of seconds the sportevent can run before timing out.
     *
     * @var int
     */
    public $timeout;

    /**
     * Create a new sportevent instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailable
     * @return void
     */
    public function __construct(MailableContract $mailable)
    {
        $this->mailable = $mailable;
        $this->tries = property_exists($mailable, 'tries') ? $mailable->tries : null;
        $this->timeout = property_exists($mailable, 'timeout') ? $mailable->timeout : null;
    }

    /**
     * Handle the queued sportevent.
     *
     * @param  \Illuminate\Contracts\Mail\Factory  $factory
     * @return void
     */
    public function handle(MailFactory $factory)
    {
        $this->mailable->send($factory);
    }

    /**
     * Get the display name for the queued sportevent.
     *
     * @return string
     */
    public function displayName()
    {
        return get_class($this->mailable);
    }

    /**
     * Call the failed method on the mailable instance.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed($e)
    {
        if (method_exists($this->mailable, 'failed')) {
            $this->mailable->failed($e);
        }
    }

    /**
     * Get number of seconds before a released mailable will be available.
     *
     * @return mixed
     */
    public function backoff()
    {
        if (! method_exists($this->mailable, 'backoff') && ! isset($this->mailable->backoff)) {
            return;
        }

        return $this->mailable->backoff ?? $this->mailable->backoff();
    }

    /**
     * Prepare the instance for cloning.
     *
     * @return void
     */
    public function __clone()
    {
        $this->mailable = clone $this->mailable;
    }
}
