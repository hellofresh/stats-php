<?php
namespace HelloFresh\Stats\Timer;

use PHPUnit\Framework\TestCase;

class MetricTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        mt_srand(time());
    }

    public function testGetters()
    {
        $metric = uniqid('metric', true);
        $elapsed = mt_rand();

        $instance = new Metric($metric, $elapsed);
        $this->assertEquals($metric, $instance->getMetric());
        $this->assertEquals($elapsed, $instance->getElapsed());
    }

    public function testGetElapsedFormatted()
    {
        $this->assertEquals('~ns', (new Metric('', 0.0000000001))->getElapsedFormatted());
        $this->assertEquals('12µs', (new Metric('', 0.0000124))->getElapsedFormatted());
        $this->assertEquals('123ms', (new Metric('', 0.1234))->getElapsedFormatted());
        $this->assertEquals('5.123s', (new Metric('', 5.1234))->getElapsedFormatted());
        $this->assertEquals('15s', (new Metric('', 15.1234))->getElapsedFormatted());
        $this->assertEquals('01:15', (new Metric('', 75.1234))->getElapsedFormatted());
        $this->assertEquals('01:02:55', (new Metric('', 3775.1234))->getElapsedFormatted());
    }

    public function testToString()
    {
        $this->assertEquals('metric: 15s', (string)(new Metric('metric', 15.1234)));
    }
}
