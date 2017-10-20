<?php

namespace HelloFresh\Stats\Incrementer;


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
}
