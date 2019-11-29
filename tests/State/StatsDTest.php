<?php
namespace HelloFresh\Stats\State;

use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    protected function setUp(): void
    {
        mt_srand(time());
    }

    public function testSet()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

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
