<?php
namespace HelloFresh\Stats\Incrementer;

use HelloFresh\Stats\Incrementer;

class Memory extends AbstractIncrementer implements Incrementer
{
    protected $metrics = [];

    /**
     * @inheritdoc
     */
    public function increment($metric, $n = 1)
    {
        $this->metrics[$metric] = empty($this->metrics[$metric]) ? $n : $this->metrics[$metric] + $n;

        return $this;
    }

    /**
     * Returns all stored metrics array.
     * Key - metric name, value - metric value.
     *
     * @return array
     */
    public function getMetrics()
    {
        return $this->metrics;
    }
}
