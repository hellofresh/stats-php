<?php

namespace HelloFresh\Stats\Timer;


use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testFinish()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->once())
            ->method('debug')
            ->willReturnCallback(function ($message, array $context) {
                $this->assertEquals('Stats timer finished', $message);
                $this->assertArrayHasKey('elapsed', $context);
            });

        $timer = new Log($logger);
        $timer->start();
        $timer->finish('hello.world');
    }
}
