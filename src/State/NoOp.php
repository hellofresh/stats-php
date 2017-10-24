<?php
namespace HelloFresh\Stats\State;

use HelloFresh\Stats\State;

class NoOp implements State
{
    /**
     * @param string $metric
     * @param int    $state
     *
     * @return void
     */
    public function set($metric, $state)
    {
    }
}
