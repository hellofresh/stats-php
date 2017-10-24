<?php
namespace HelloFresh\Stats\State;

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function setUp()
    {
        mt_srand(time());
    }

    public function testSet()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $metric = uniqid('metric', true);
        $state = mt_rand();

        $logger->expects($this->once())
            ->method('debug')
            ->with('Stats state set', ['metric' => $metric, 'state' => $state]);

        $instance = new Log($logger);
        $instance->set($metric, $state);
    }
}
