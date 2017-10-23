<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use PHPUnit\Framework\TestCase;

class NoOpTest extends TestCase
{
    public function testInstances()
    {
        $statsClient = new NoOp();
        $this->assertInstanceOf(Stats\Timer\Memory::class, $statsClient->buildTimer());
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
