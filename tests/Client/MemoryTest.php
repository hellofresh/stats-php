<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    protected function setUp(): void
    {
        mt_srand(time());
    }

    public function testInstances()
    {
        $statsClient = new Memory();
        $this->assertInstanceOf(Stats\Timer\Memory::class, $statsClient->buildTimer());
    }

    public function testHTTPRequestSection()
    {
        $section = uniqid('section', true);

        $statsClient = new Memory();

        $reflection = new \ReflectionClass($statsClient);
        $reflectionProperty = $reflection->getProperty('httpRequestSection');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));

        $statsClient->setHTTPRequestSection($section);
        $this->assertEquals($section, $reflectionProperty->getValue($statsClient));

        $statsClient->resetHTTPRequestSection();
        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));
    }

    public function testGetTimers()
    {
        $metric1 = uniqid('metric1', true);
        $metric2 = uniqid('metric2', true);

        $statsClient = new Memory();

        $timer1 = $statsClient->buildTimer()->start();
        $timer1->finish($metric1);

        $timer2 = $statsClient->buildTimer()->start();
        $timer2->finish($metric2);

        $timers = $statsClient->getTimers();
        $this->assertEquals(2, count($timers));

        $this->assertEquals($timer1, $timers[0]);
        $this->assertEquals($timer2, $timers[1]);

        $this->assertEquals($metric1, $timers[0]->getMetric());
        $this->assertEquals($metric2, $timers[1]->getMetric());
    }

    public function testGetIncrementedMetrics()
    {
        $section1 = uniqid('section1', true);
        $operation1 = uniqid('operation1', true);
        $metric1 = Bucket\Plain::sanitizeMetricName($section1) . '.' . Bucket\Plain::sanitizeMetricName($operation1) . '.-.-';
        $metric1Total = 'total.' . Bucket\Plain::sanitizeMetricName($section1);
        $section2 = uniqid('section2', true);
        $operation2 = uniqid('operation2', true);
        $metric2 = Bucket\Plain::sanitizeMetricName($section2) . '.' . Bucket\Plain::sanitizeMetricName($operation2) . '.-.-';
        $metric2Total = 'total.' . Bucket\Plain::sanitizeMetricName($section2);

        $n11 = mt_rand(10, 100);
        $n12 = mt_rand(10, 100);
        $n21 = mt_rand(10, 100);
        $n22 = mt_rand(10, 100);

        $statsClient = new Memory();

        $timer11 = $statsClient->buildTimer()->start();
        $timer12 = $statsClient->buildTimer()->start();
        $timer21 = $statsClient->buildTimer()->start();
        $timer22 = $statsClient->buildTimer()->start();

        $statsClient->trackMetric($section1, new Bucket\MetricOperation([$operation1]), $timer11, $n11);
        $statsClient->trackMetric($section1, new Bucket\MetricOperation([$operation1]), $timer12, $n12);
        $statsClient->trackMetric($section2, new Bucket\MetricOperation([$operation2]), $timer21, $n21);
        $statsClient->trackMetric($section2, new Bucket\MetricOperation([$operation2]), $timer22, $n22);

        $timers = $statsClient->getTimers();
        $this->assertEquals(4, count($timers));

        $metrics = $statsClient->getIncrementedMetrics();
        $this->assertEquals(4, count($metrics));

        $this->assertArrayHasKey($metric1, $metrics);
        $this->assertEquals($n11 + $n12, $metrics[$metric1]);
        $this->assertEquals($n11 + $n12, $metrics[$metric1Total]);

        $this->assertArrayHasKey($metric2, $metrics);
        $this->assertEquals($n21 + $n22, $metrics[$metric2]);
        $this->assertEquals($n21 + $n22, $metrics[$metric2Total]);
    }

    public function testGetStateMetrics()
    {
        $section1 = uniqid('section1', true);
        $operation1 = uniqid('operation1', true);
        $metric1 = Bucket\Plain::sanitizeMetricName($section1) . '.' . Bucket\Plain::sanitizeMetricName($operation1) . '.-.-';
        $section2 = uniqid('section2', true);
        $operation2 = uniqid('operation2', true);
        $metric2 = Bucket\Plain::sanitizeMetricName($section2) . '.' . Bucket\Plain::sanitizeMetricName($operation2) . '.-.-';

        $n11 = mt_rand(10, 100);
        $n12 = mt_rand(10, 100);
        $n21 = mt_rand(10, 100);
        $n22 = mt_rand(10, 100);

        $statsClient = new Memory();

        $statsClient->trackState($section1, new Bucket\MetricOperation([$operation1]), $n11);
        $statsClient->trackState($section1, new Bucket\MetricOperation([$operation1]), $n12);
        $statsClient->trackState($section2, new Bucket\MetricOperation([$operation2]), $n21);
        $statsClient->trackState($section2, new Bucket\MetricOperation([$operation2]), $n22);

        $states = $statsClient->getStateMetrics();
        $this->assertEquals(2, count($states));

        $this->assertArrayHasKey($metric1, $states);
        $this->assertEquals($n12, $states[$metric1]);

        $this->assertArrayHasKey($metric2, $states);
        $this->assertEquals($n22, $states[$metric2]);
    }
}
