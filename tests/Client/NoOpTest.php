<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use PHPUnit\Framework\TestCase;

class NoOpTest extends TestCase
{
    public function testInstances()
    {
        $statsClient = new NoOp();
        $this->assertInstanceOf(Stats\Timer\Memory::class, $statsClient->buildTimer());
    }
}
