<?php

namespace HelloFresh\Stats\State;


use HelloFresh\Stats\State;

class Memory implements State
{
    protected $metrics = [];

    /**
     * @inheritdoc
     */
    public function set($metric, $state)
    {
        $this->metrics[$metric] = $state;
    }

    /**
     * Returns all stored states array.
     * Key - metric name, value - metric value.
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }
}
