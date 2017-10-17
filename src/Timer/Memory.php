<?php

namespace HelloFresh\Stats\Timer;


use HelloFresh\Stats\Timer;

class Memory implements Timer
{
    /** @var string */
    protected $metric;
    /** @var float */
    protected $startedAt;
    /** @var float */
    protected $elapsed;

    /**
     * @inheritdoc
     */
    public function start()
    {
        $this->startedAt = microtime(true);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finish($metric)
    {
        $this->elapsed = microtime(true) - $this->startedAt;
        $this->metric = $metric;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * @return Metric
     */
    public function elapsed()
    {
        return new Metric($this->metric, $this->elapsed);
    }
}
