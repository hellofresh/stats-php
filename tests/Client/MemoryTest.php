<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    public function testInstances()
    {
        $statsClient = new Memory();
        $this->assertInstanceOf(Stats\Timer\Memory::class, $statsClient->buildTimer());
    }
}
