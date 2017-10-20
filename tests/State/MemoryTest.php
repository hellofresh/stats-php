<?php

namespace HelloFresh\Stats\State;


use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    protected function setUp()
    {
        mt_srand(time());
    }

    public function testMetrics()
    {
        $metric1 = uniqid('metric1', true);
        $metric2 = uniqid('metric2', true);
        $value1 = mt_rand();
        $value2 = mt_rand();

        $s = new Memory();
        $s->set($metric1, $value1);
        $s->set($metric2, $value2);

        $metrics = $s->getMetrics();

        $this->assertArrayHasKey($metric1, $metrics);
        $this->assertEquals($value1, $metrics[$metric1]);

        $this->assertArrayHasKey($metric2, $metrics);
        $this->assertEquals($value2, $metrics[$metric2]);

        $this->assertArrayNotHasKey(uniqid(), $metrics);
    }
}
