<?php

namespace HelloFresh\Stats\State;


use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    public function setUp()
    {
        mt_srand(time());
    }

    public function testSet()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\League\StatsD\Client $statsd */
        $statsd = $this->getMockBuilder('\League\StatsD\Client')->setMethods(['gauge'])->getMock();

        $metric = uniqid('metric', true);
        $state = mt_rand();

        $statsd->expects($this->once())
            ->method('gauge')
            ->with($metric, $state);

        $instance = new StatsD($statsd);
        $instance->set($metric, $state);
    }
}