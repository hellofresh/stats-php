<?php

namespace HelloFresh\Stats;


interface State
{
    /**
     * @param string $metric
     * @param int $state
     *
     * @return void
     */
    public function set($metric, $state);
}
