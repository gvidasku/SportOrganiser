<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Queue\CallQueuedClosure;

class PendingChainFake extends PendingChain
{
    /**
     * The fake bus instance.
     *
     * @var \Illuminate\Support\Testing\Fakes\BusFake
     */
    protected $bus;

    /**
     * Create a new pending chain instance.
     *
     * @param  \Illuminate\Support\Testing\Fakes\BusFake  $bus
     * @param  mixed  $sportevent
     * @param  array  $chain
     * @return void
     */
    public function __construct(BusFake $bus, $sportevent, $chain)
    {
        $this->bus = $bus;
        $this->sportevent = $sportevent;
        $this->chain = $chain;
    }

    /**
     * Dispatch the sportevent with the given arguments.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatch()
    {
        if (is_string($this->sportevent)) {
            $firstsportevent = new $this->sportevent(...func_get_args());
        } elseif ($this->sportevent instanceof Closure) {
            $firstsportevent = CallQueuedClosure::create($this->sportevent);
        } else {
            $firstsportevent = $this->sportevent;
        }

        $firstsportevent->allOnConnection($this->connection);
        $firstsportevent->allOnQueue($this->queue);
        $firstsportevent->chain($this->chain);
        $firstsportevent->delay($this->delay);
        $firstsportevent->chainCatchCallbacks = $this->catchCallbacks();

        return $this->bus->dispatch($firstsportevent);
    }
}
