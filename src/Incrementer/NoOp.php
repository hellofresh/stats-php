<?php

namespace HelloFresh\Stats\Incrementer;


use HelloFresh\Stats\Incrementer;

class NoOp extends Base implements Incrementer
{
    /**
     * @inheritdoc
     */
    public function increment($metric, $n = 1)
    {
    }
}
