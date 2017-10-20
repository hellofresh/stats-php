<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    public function testConfigure()
    {
        $statsClient = $this->getMockBuilder('\HelloFresh\Stats\Client\StatsD')
            ->disableOriginalConstructor()
            ->getMock();

        $statsdClient = $this->getMockBuilder('\League\StatsD\Client')->getMock();
        $statsdClient->expects($this->once())
            ->method('configure')
            ->with([
                'host' => 'stats.local',
                'port' => 1234,
                'namespace' => 'prefix.ns',
                'timeout' => 2.5,
                'throwConnectionExceptions' => false,
            ]);

        $reflection = new \ReflectionClass($statsClient);
        $propertyClient = $reflection->getProperty('client');
        $propertyClient->setAccessible(true);
        $propertyClient->setValue($statsClient, $statsdClient);

        $methodConfigure = $reflection->getMethod('configure');
        $methodConfigure->setAccessible(true);
        $methodConfigure->invoke($statsClient, 'statsd://stats.local:1234/prefix.ns?timeout=2.5&error=0');
    }

    public function testInstances()
    {
        $statsClient = new StatsD('statsd://');
        $this->assertInstanceOf(Stats\Timer\StatsD::class, $statsClient->buildTimer());
    }
}
