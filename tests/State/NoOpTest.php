<?php
namespace HelloFresh\Stats\State;

use PHPUnit\Framework\TestCase;

class NoOpTest extends TestCase
{
    public function setUp()
    {
        mt_srand(time());
    }

    public function testSet()
    {
        $metric = uniqid('metric', true);
        $state = mt_rand();

        $instance = new NoOp();
        $instance->set($metric, $state);
    }
}
