<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use PHPUnit\Framework\TestCase;

class StatsDTest extends TestCase
{
    public function testConfigure()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

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
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        $statsClient = new StatsD('statsd://');
        $this->assertInstanceOf(Stats\Timer\StatsD::class, $statsClient->buildTimer());

        $reflection = new \ReflectionClass($statsClient);

        $methodIncrementer = $reflection->getMethod('getIncrementer');
        $methodIncrementer->setAccessible(true);
        $this->assertInstanceOf(Incrementer\StatsD::class, $methodIncrementer->invoke($statsClient));

        $methodState = $reflection->getMethod('getState');
        $methodState->setAccessible(true);
        $this->assertInstanceOf(State\StatsD::class, $methodState->invoke($statsClient));
    }

    public function testHTTPRequestSection()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        $section = uniqid('section', true);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\League\StatsD\Client $statsd */
        $statsd = $this->getMockBuilder('\League\StatsD\Client')->setMethods(['gauge'])->getMock();

        $statsClient = new StatsD($statsd);

        $reflection = new \ReflectionClass($statsClient);
        $reflectionProperty = $reflection->getProperty('httpRequestSection');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));

        $statsClient->setHTTPRequestSection($section);
        $this->assertEquals($section, $reflectionProperty->getValue($statsClient));

        $statsClient->resetHTTPRequestSection();
        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));
    }
}
