<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer\Memory;

class NoOp extends Base implements Client
{
    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new Memory();
    }

    /**
     * @return Incrementer
     */
    protected function getIncrementer()
    {
        return new Incrementer\NoOp();
    }

    /**
     * @return State
     */
    protected function getState()
    {
        return new State\NoOp();
    }
}
