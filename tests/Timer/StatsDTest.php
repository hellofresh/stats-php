<?php

namespace HelloFresh\Stats\Timer;


use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    public function testFinish()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        /** @var \PHPUnit_Framework_MockObject_MockObject|\League\StatsD\Client $statsd */
        $statsd = $this->getMockBuilder('\League\StatsD\Client')->getMock();

        $statsd->expects($this->once())
            ->method('timing')
            ->willReturnCallback(function ($metric, $time) {
                $this->assertEquals('hello.world', $metric);
            });

        $timer = new StatsD($statsd);
        $timer->start();
        $timer->finish('hello.world');
    }
}
