<?php

namespace HelloFresh\Stats\Timer;


class Metric
{
    /** @var string */
    protected $metric;
    /** @var float */
    protected $elapsed;

    /**
     * Metric constructor.
     * @param string $metric
     * @param float $elapsed
     */
    public function __construct($metric, $elapsed)
    {
        $this->metric = $metric;
        $this->elapsed = $elapsed;
    }

    /**
     * @return string
     */
    public function getMetric()
    {
        return $this->metric;
    }

    /**
     * @return float
     */
    public function getElapsed()
    {
        return $this->elapsed;
    }

    /**
     * Converts elapsed interval to human-readable format.
     *
     * @return string
     */
    public function getElapsedFormatted()
    {
        if ($this->elapsed < 1.0) {
            if ($this->elapsed < 0.000001) {
                return '~ns';
            } elseif ($this->elapsed < 0.001) {
                return (int)($this->elapsed * 1000000) . 'µs';
            }
            return (int)($this->elapsed * 1000) . 'ms';
        }

        if ($this->elapsed < 10) {
            return round($this->elapsed, 3) . 's';
        }

        $elapsed = (int)$this->elapsed;
        $seconds = $elapsed % 60;

        $elapsed -= $seconds;
        if ($elapsed < 1) {
            return $seconds . 's';
        }

        $minutes = $elapsed / 60;
        if ($minutes < 60) {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }

        $hours = ($minutes - $minutes % 60) / 60;
        $minutes = $minutes % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function __toString()
    {
        return $this->getMetric() . ': ' . $this->getElapsedFormatted();
    }
}
