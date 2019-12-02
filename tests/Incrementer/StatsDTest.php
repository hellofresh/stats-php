<?php
namespace HelloFresh\Stats\Incrementer;

use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    protected function setUp(): void
    {
        mt_srand(time());
    }

    public function testMetrics()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        /** @var \PHPUnit_Framework_MockObject_MockObject|\League\StatsD\Client $statsd */
        $statsd = $this->getMockBuilder('\League\StatsD\Client')->setMethods(['increment'])->getMock();

        $metric1 = uniqid('metric1', true);
        $metric2 = uniqid('metric2', true);
        $value1 = mt_rand(1, 100);
        $value2 = mt_rand(1, 100);

        $statsd->expects($this->at(0))
            ->method('increment')
            ->with($metric1, 1);
        $statsd->expects($this->at(1))
            ->method('increment')
            ->with($metric2, 1);
        $statsd->expects($this->at(2))
            ->method('increment')
            ->with($metric1, $value1);
        $statsd->expects($this->at(3))
            ->method('increment')
            ->with($metric2, $value2);

        $i = new StatsD($statsd);
        $i->increment($metric1);
        $i->increment($metric2);
        $i->increment($metric1, $value1);
        $i->increment($metric2, $value2);
    }
}
