<?php
namespace HelloFresh\Stats\Bucket;

use PHPUnit\Framework\TestCase;

class HTTPRequestTest extends TestCase
{
    /**
     * @dataProvider metrics
     *
     * @param string $method
     * @param string $path
     * @param string $section
     * @param string $metric
     */
    public function testHTTPRequestTest($method, $path, $section, $metric)
    {
        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue($path));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $bucket = new HTTPRequest($section, $request, true, null);
        $this->assertEquals($metric, $bucket->metric());
    }

    public function testHTTPMetricAlterCallback()
    {
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

        /** @var \PHPUnit_Framework_MockObject_MockObject|\HelloFresh\Stats\HTTPMetricAlterCallback $callback */
        $callback = $this->getMockBuilder('\HelloFresh\Stats\HTTPMetricAlterCallback')->getMock();

        $callback->expects($this->once())
            ->method('__invoke')
            ->with($this->isInstanceOf(MetricOperation::class), $this->equalTo($request))
            ->will($this->returnValue(new MetricOperation(['new', 'metric', 'here'])));

        $bucket = new HTTPRequest('baz', $request, true, $callback);
        $this->assertEquals('baz.new.metric.here', $bucket->metric());
    }

    public function metrics()
    {
        return [
            ['POST', '/', 'foo', 'foo.post.-.-'],
            ['GET', '/hello', 'bar', 'bar.get.hello.-'],
            ['GET', '/hello/', 'baz', 'baz.get.hello.-'],
            ['GET', '/hello/world', 'baz', 'baz.get.hello.world'],
        ];
    }
}
