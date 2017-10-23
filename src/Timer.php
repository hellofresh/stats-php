<?php
namespace HelloFresh\Stats;

interface Timer
{
    /**
     * Start timer
     *
     * @return self
     */
    public function start();

    /**
     * @param string $metric
     *
     * @return self
     */
    public function finish($metric);
}
