<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats\Client;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer;

class Memory extends AbstractClient implements Client
{
    /** @var Incrementer\Memory */
    protected $incrementer;
    /** @var State\Memory */
    protected $state;
    /** @var Timer\Memory[] */
    protected $timers;

    /**
     * Memory constructor.
     */
    public function __construct()
    {
        $this->resetHTTPRequestSection();
    }

    /**
     * @inheritdoc
     */
    protected function getIncrementer()
    {
        if (null === $this->incrementer) {
            $this->incrementer = new Incrementer\Memory();
        }

        return $this->incrementer;
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        if (null === $this->state) {
            $this->state = new State\Memory();
        }

        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        $timer = new Timer\Memory();
        $this->timers[] = $timer;

        return $timer;
    }

    /**
     * @return Timer\Memory[]
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Returns all stored metrics array.
     * Key - metric name, value - metric value.
     *
     * @return array
     */
    public function getIncrementedMetrics()
    {
        return $this->getIncrementer()->getMetrics();
    }

    /**
     * Returns all stored states array.
     * Key - metric name, value - metric value.
     *
     * @return array
     */
    public function getStateMetrics()
    {
        return $this->getState()->getMetrics();
    }
}
