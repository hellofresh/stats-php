<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use PHPUnit\Framework\TestCase;

class NoOpTest extends TestCase
{
    public function testInstances()
    {
        $statsClient = new NoOp();
        $this->assertInstanceOf(Stats\Timer\Memory::class, $statsClient->buildTimer());

        $reflection = new \ReflectionClass($statsClient);

        $methodIncrementer = $reflection->getMethod('getIncrementer');
        $methodIncrementer->setAccessible(true);
        $this->assertInstanceOf(Incrementer\NoOp::class, $methodIncrementer->invoke($statsClient));

        $methodState = $reflection->getMethod('getState');
        $methodState->setAccessible(true);
        $this->assertInstanceOf(State\NoOp::class, $methodState->invoke($statsClient));
    }

    public function testHTTPRequestSection()
    {
        $section = uniqid('section', true);

        $statsClient = new NoOp();

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
