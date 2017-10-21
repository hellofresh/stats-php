<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats;
use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Bucket\MetricOperation;
use HelloFresh\Stats\Bucket\Plain;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function setUp()
    {
        mt_srand(time());
    }

    public function testInstances()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $statsClient = new Log($logger);
        $this->assertInstanceOf(Stats\Timer\Log::class, $statsClient->buildTimer());

        $reflection = new \ReflectionClass($statsClient);

        $methodIncrementer = $reflection->getMethod('getIncrementer');
        $methodIncrementer->setAccessible(true);
        $this->assertInstanceOf(Incrementer\Log::class, $methodIncrementer->invoke($statsClient));

        $methodState = $reflection->getMethod('getState');
        $methodState->setAccessible(true);
        $this->assertInstanceOf(State\Log::class, $methodState->invoke($statsClient));
    }

    public function testTrackRequest()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->at(0))
            ->method('debug')
            ->willReturnCallback(function ($message, array $context) {
                $this->assertEquals('Stats timer finished', $message);
                $this->assertArrayHasKey('metric', $context);
                $this->assertEquals('request.get.hello.world', $context['metric']);
                $this->assertArrayHasKey('elapsed', $context);
            });
        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'request.get.hello.world', 'n' => 1]);
        $logger->expects($this->at(2))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'request-ok.get.hello.world', 'n' => 1]);
        $logger->expects($this->at(3))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.request', 'n' => 1]);
        $logger->expects($this->at(4))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.request-ok', 'n' => 1]);

        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('/hello/world'));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));
        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $statsClient = new Log($logger);

        $timer = $statsClient->buildTimer();
        $statsClient->trackRequest($request, $timer, true);
    }

    public function testTrackOperation()
    {
        $n = mt_rand();
        $section = uniqid('section', true);
        $sectionMetric = Plain::sanitizeMetricName($section);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->at(0))
            ->method('debug')
            ->willReturnCallback(function ($message, array $context) use ($sectionMetric) {
                $this->assertEquals('Stats timer finished', $message);
                $this->assertArrayHasKey('metric', $context);
                $this->assertEquals($sectionMetric . '-fail.foo.bar.baz', $context['metric']);
                $this->assertArrayHasKey('elapsed', $context);
            });
        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $sectionMetric . '.foo.bar.baz', 'n' => $n]);
        $logger->expects($this->at(2))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => $sectionMetric . '-fail.foo.bar.baz', 'n' => $n]);
        $logger->expects($this->at(3))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.' . $sectionMetric, 'n' => $n]);
        $logger->expects($this->at(4))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.' . $sectionMetric . '-fail', 'n' => $n]);

        $statsClient = new Log($logger);

        $timer = $statsClient->buildTimer();
        $statsClient->trackOperation($section, new MetricOperation(['foo', 'bar', 'baz']), false, $timer, $n);
    }

    public function testTrackState()
    {
        $state = mt_rand();
        $section = uniqid('section', true);
        $sectionMetric = Plain::sanitizeMetricName($section);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->once())
            ->method('debug')
            ->with('Stats state set', ['metric' => $sectionMetric . '.foo.bar.baz', 'state' => $state]);

        $statsClient = new Log($logger);
        $statsClient->trackState($section, new MetricOperation(['foo', 'bar', 'baz']), $state);
    }

    public function testHTTPRequestSection()
    {
        $section = uniqid('section', true);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $statsClient = new Log($logger);

        $reflection = new \ReflectionClass($statsClient);
        $reflectionProperty = $reflection->getProperty('httpRequestSection');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));

        $statsClient->setHTTPRequestSection($section);
        $this->assertEquals($section, $reflectionProperty->getValue($statsClient));

        $statsClient->resetHTTPRequestSection();
        $this->assertEquals(Bucket::DEFAULT_HTTP_REQUEST_SECTION, $reflectionProperty->getValue($statsClient));
    }

    public function testTrackRequest_HTTPMetricAlterCallback()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Log\LoggerInterface $logger */
        $logger = $this->getMockBuilder('\Psr\Log\LoggerInterface')->getMock();

        $logger->expects($this->at(0))
            ->method('debug')
            ->willReturnCallback(function ($message, array $context) {
                $this->assertEquals('Stats timer finished', $message);
                $this->assertArrayHasKey('metric', $context);
                $this->assertEquals('request.callback.altered.metric', $context['metric']);
                $this->assertArrayHasKey('elapsed', $context);
            });
        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'request.callback.altered.metric', 'n' => 1]);
        $logger->expects($this->at(2))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'request-ok.callback.altered.metric', 'n' => 1]);
        $logger->expects($this->at(3))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.request', 'n' => 1]);
        $logger->expects($this->at(4))
            ->method('debug')
            ->with('Stats counter incremented', ['metric' => 'total.request-ok', 'n' => 1]);

        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue('/hello/world'));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('GET'));
        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\HelloFresh\Stats\HTTPMetricAlterCallback $alterCallback */
        $alterCallback = $this->getMockBuilder('\HelloFresh\Stats\HTTPMetricAlterCallback')->getMock();

        $alterCallback->expects($this->once())
            ->method('__invoke')
            ->willReturn(new MetricOperation(['callback', 'altered', 'metric']));

        $statsClient = new Log($logger);
        $statsClient->setHTTPMetricAlterCallback($alterCallback);
        $this->assertEquals($alterCallback, $statsClient->getHTTPMetricAlterCallback());

        $timer = $statsClient->buildTimer();
        $statsClient->trackRequest($request, $timer, true);
    }
}
