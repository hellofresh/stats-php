<?php
namespace HelloFresh\Stats\Incrementer;

use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    protected function setUp(): void
    {
        mt_srand(time());
    }

    public function testMetrics()
    {
        $metric1 = uniqid('metric1', true);
        $metric2 = uniqid('metric2', true);
        $value1 = mt_rand(1, 100);
        $value2 = mt_rand(1, 100);

        $i = new Memory();
        $i->increment($metric1);
        $i->increment($metric2);
        $i->increment($metric1, $value1);
        $i->increment($metric2, $value2);

        $metrics = $i->getMetrics();

        $this->assertArrayHasKey($metric1, $metrics);
        $this->assertEquals($value1 + 1, $metrics[$metric1]);

        $this->assertArrayHasKey($metric2, $metrics);
        $this->assertEquals($value2 + 1, $metrics[$metric2]);

        $this->assertArrayNotHasKey(uniqid(), $metrics);
    }

    public function testIncrementAll()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\HelloFresh\Stats\Bucket $bucket */
        $bucket = $this->getMockBuilder('\HelloFresh\Stats\Bucket')->getMock();

        $n = mt_rand();
        $metric = uniqid('metric', true);
        $metricWithSuffix = uniqid('metricWithSuffix', true);
        $metricTotal = uniqid('metricTotal', true);
        $metricTotalWithSuffix = uniqid('metricTotalWithSuffix', true);

        $bucket->expects($this->once())
            ->method('metric')
            ->will($this->returnValue($metric));
        $bucket->expects($this->once())
            ->method('metricWithSuffix')
            ->will($this->returnValue($metricWithSuffix));
        $bucket->expects($this->once())
            ->method('metricTotal')
            ->will($this->returnValue($metricTotal));
        $bucket->expects($this->once())
            ->method('metricTotalWithSuffix')
            ->will($this->returnValue($metricTotalWithSuffix));

        $i = new Memory();
        $i->incrementAll($bucket, $n);

        $metrics = $i->getMetrics();

        $this->assertArrayHasKey($metric, $metrics);
        $this->assertEquals($n, $metrics[$metric]);

        $this->assertArrayHasKey($metricWithSuffix, $metrics);
        $this->assertEquals($n, $metrics[$metricWithSuffix]);

        $this->assertArrayHasKey($metricTotal, $metrics);
        $this->assertEquals($n, $metrics[$metricTotal]);

        $this->assertArrayHasKey($metricTotalWithSuffix, $metrics);
        $this->assertEquals($n, $metrics[$metricTotalWithSuffix]);
    }
}
