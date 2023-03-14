<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use League\StatsD\Client;
use League\StatsD\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;

class ExposedClientStatsD extends StatsD
{
    public function getClient()
    {
        return $this->client;
    }
}

class StatsDTest extends TestCase
{
    public function testBuildOptions()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        $statsClient = $this->getMockBuilder('\HelloFresh\Stats\Client\StatsD')
            ->disableOriginalConstructor()
            ->getMock();

        $reflection = new ReflectionClass($statsClient);
        $methodBuildOptions = $reflection->getMethod('buildOptions');
        $methodBuildOptions->setAccessible(true);
        $this->assertEquals(
            [
                'host' => 'stats.local',
                'port' => 1234,
                'namespace' => 'prefix.ns',
                'timeout' => 2.5,
                'throwConnectionExceptions' => false,
            ],
            $methodBuildOptions->invoke($statsClient, 'statsd://stats.local:1234/prefix.ns?timeout=2.5&error=0')
        );
    }

    /**
     * @throws ConfigurationException
     */
    public function testDefaultClientInstance()
    {
        $dns = 'statsd://stats.local:1234/prefix.ns?timeout=2.5&error=0';
        $statsD = new ExposedClientStatsD($dns);
        $instantiatedClient = $statsD->getClient();

        $this->assertInstanceOf(Stats\StatsD\SilentClient::class, $instantiatedClient);
    }

    /**
     * @throws ConfigurationException
     */
    public function testOptionalClientInstance()
    {
        $dns = 'statsd://stats.local:1234/prefix.ns?timeout=2.5&error=0';
        $statsD = new ExposedClientStatsD($dns, Client::class);
        $instantiatedClient = $statsD->getClient();

        $this->assertInstanceOf(Client::class, $instantiatedClient);
    }

    public function testInstances()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        $statsClient = new StatsD('statsd://');
        $this->assertInstanceOf(Stats\Timer\StatsD::class, $statsClient->buildTimer());
    }

    public function testHTTPRequestSection()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        $section = uniqid('section', true);

        /** @var PHPUnit_Framework_MockObject_MockObject|Client $statsd */
        $statsd = $this->getMockBuilder('\League\StatsD\Client')->setMethods(['gauge'])->getMock();

        $statsClient = new StatsD($statsd);

        $reflection = new ReflectionClass($statsClient);
        $reflectionProperty = $reflection->getProperty('httpRequestSection');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));

        $statsClient->setHTTPRequestSection($section);
        $this->assertEquals($section, $reflectionProperty->getValue($statsClient));

        $statsClient->resetHTTPRequestSection();
        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));
    }
}
