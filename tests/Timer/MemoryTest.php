<?php
namespace HelloFresh\Stats\Timer;

use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    public function testFinish()
    {
        $timer = new Memory();
        $timer->start();
        $timer->finish('hello.world');

        $this->assertEquals('hello.world', $timer->getMetric());
        $this->assertEquals('hello.world', $timer->getMetric());

        $metric = $timer->elapsed();
        $this->assertInstanceOf(Metric::class, $metric);
        $this->assertEquals('hello.world', $metric->getMetric());
    }
}
