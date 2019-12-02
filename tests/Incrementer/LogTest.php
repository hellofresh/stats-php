<?php
namespace HelloFresh\Stats\Incrementer;

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    protected function setUp(): void
    {
        mt_srand(time());
    }

    public function testMetrics()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $metric1 = uniqid('metric1', true);
        $metric2 = uniqid('metric2', true);
        $value1 = mt_rand(1, 100);
        $value2 = mt_rand(1, 100);

        $logger->expects($this->at(0))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $metric1, 'n' => 1]);
        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $metric2, 'n' => 1]);
        $logger->expects($this->at(2))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $metric1, 'n' => $value1]);
        $logger->expects($this->at(3))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $metric2, 'n' => $value2]);

        $i = new Log($logger);
        $i->increment($metric1);
        $i->increment($metric2);
        $i->increment($metric1, $value1);
        $i->increment($metric2, $value2);
    }
}
