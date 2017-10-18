<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer\Memory;

class NoOp extends AbstractClient implements Client
{
    /** @var Incrementer\NoOp */
    protected $incrementer;
    /** @var State\NoOp */
    protected $state;

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new Memory();
    }

    /**
     * @inheritdoc
     */
    protected function getIncrementer()
    {
        if (null === $this->incrementer) {
            $this->incrementer = new Incrementer\NoOp();
        }
        return $this->incrementer;
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        if (null === $this->state) {
            $this->state = new State\NoOp();
        }
        return $this->state;
    }
}
