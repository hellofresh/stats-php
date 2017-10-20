<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testInstances()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $statsClient = new Log($logger);
        $this->assertInstanceOf(Stats\Timer\Log::class, $statsClient->buildTimer());
    }
}
