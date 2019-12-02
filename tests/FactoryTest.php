<?php
namespace HelloFresh\Stats;

use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    public function testBuild()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $this->assertInstanceOf(Client\Log::class, Factory::build('log://log', $logger));
        $this->assertInstanceOf(Client\NoOp::class, Factory::build('noop://noop', $logger));
        $this->assertInstanceOf(Client\Memory::class, Factory::build('memory://memory', $logger));
    }

    public function testBuildStatsD()
    {
        if (!class_exists('\League\StatsD\Client')) {
            $this->markTestSkipped('Missing league/statsd package');
        }

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $this->assertInstanceOf(Client\StatsD::class, Factory::build('statsd://qwe:1234/asd', $logger));
    }

    public function testUnknownClient()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown client type');

        Factory::build(uniqid(), $logger);
    }
}
