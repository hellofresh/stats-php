<?php

namespace HelloFresh\Stats\Timer;


use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testFinish()
    {
        $metric = uniqid('metric', true);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->once())
            ->method('debug')
            ->willReturnCallback(function ($message, array $context) use ($metric) {
                $this->assertEquals('Stats timer finished', $message);
                $this->assertArrayHasKey('metric', $context);
                $this->assertEquals($metric, $context['metric']);
                $this->assertArrayHasKey('elapsed', $context);
            });

        $timer = new Log($logger);
        $timer->start();
        $timer->finish($metric);
    }
}
