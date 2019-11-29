<?php
namespace HelloFresh\Stats\HTTPMetricAlterCallback;

use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Bucket\MetricOperation;
use PHPUnit\Framework\TestCase;

class HasIDAtSecondLevelTest extends TestCase
{
    /**
     * @dataProvider defaultSectionTestsProvider
     *
     * @param MetricOperation $inputMetric
     * @param MetricOperation $result
     * @param array           $map
     */
    public function testDefaultSectionTests(MetricOperation $inputMetric, MetricOperation $result, array $map)
    {
        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue(sprintf('/%s/%s', $inputMetric[1], $inputMetric[2])));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $callback = new HasIDAtSecondLevel($map);
        $this->assertEquals($result->toArray(), $callback($inputMetric, $request)->toArray());
    }

    public function testRegisterSectionTest()
    {
        $inputMetric = new MetricOperation(['get', 'users', 'edit']);

        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->atLeastOnce())
            ->method('getPath')
            ->will($this->returnValue(sprintf('/%s/%s', $inputMetric[1], $inputMetric[2])));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $callback = new HasIDAtSecondLevel(['users' => 'edit']);
        $this->assertEquals($inputMetric->toArray(), $callback($inputMetric, $request)->toArray());

        $callback->registerSectionTest('edit', function ($pathSection) {
            return $pathSection == 'edit';
        });
        $this->assertEquals(
            (new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]))->toArray(),
            $callback($inputMetric, $request)->toArray()
        );
    }

    /**
     * @dataProvider createFromStringMapProvider
     *
     * @param string $map
     */
    public function testCreateFromStringMap_InvalidFormat($map)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid sections format');
        HasIDAtSecondLevel::createFromStringMap($map);
    }

    public function testCreateFromStringMap_UnknownSectionTest()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown section test callback name: foo');
        HasIDAtSecondLevel::createFromStringMap('users:foo');
    }

    public function testCreateFromStringMap()
    {
        $inputMetric1 = new MetricOperation(['get', 'users', '1']);
        $inputMetric2 = new MetricOperation(['get', 'users', 'foo']);

        $uri = $this->getMockBuilder('\Psr\Http\Message\UriInterface')->getMock();

        $uri->expects($this->at(0))
            ->method('getPath')
            ->will($this->returnValue(sprintf('/%s/%s', $inputMetric1[1], $inputMetric1[2])));
        $uri->expects($this->at(1))
            ->method('getPath')
            ->will($this->returnValue(sprintf('/%s/%s', $inputMetric2[1], $inputMetric2[2])));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Psr\Http\Message\RequestInterface $request */
        $request = $this->getMockBuilder('\Psr\Http\Message\RequestInterface')->getMock();

        $request->expects($this->atLeastOnce())
            ->method('getUri')
            ->will($this->returnValue($uri));

        $callback = HasIDAtSecondLevel::createFromStringMap('users:foo', ['foo' => function ($pathSection) {
            return $pathSection == 'foo';
        }]);

        $this->assertEquals(
            $inputMetric1->toArray(),
            $callback($inputMetric1, $request)->toArray()
        );
        $this->assertEquals(
            (new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]))->toArray(),
            $callback($inputMetric2, $request)->toArray()
        );
    }

    public function defaultSectionTestsProvider()
    {
        return [
            // GET /users/1
            [
                new MetricOperation(['get', 'users', '1']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_TRUE],
            ],
            [
                new MetricOperation(['get', 'users', '1']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NUMERIC],
            ],
            [
                new MetricOperation(['get', 'users', '1']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NOT_EMPTY],
            ],
            // GET /users/edit
            [
                new MetricOperation(['get', 'users', 'edit']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_TRUE],
            ],
            [
                new MetricOperation(['get', 'users', 'edit']),
                new MetricOperation(['get', 'users', 'edit']),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NUMERIC],
            ],
            [
                new MetricOperation(['get', 'users', 'edit']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NOT_EMPTY],
            ],
            // GET /users
            [
                new MetricOperation(['get', 'users']),
                new MetricOperation(['get', 'users', Bucket::METRIC_ID_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_TRUE],
            ],
            [
                new MetricOperation(['get', 'users']),
                new MetricOperation(['get', 'users', Bucket::METRIC_EMPTY_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NUMERIC],
            ],
            [
                new MetricOperation(['get', 'users']),
                new MetricOperation(['get', 'users', Bucket::METRIC_EMPTY_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_IS_NOT_EMPTY],
            ],
            // does not match
            [
                new MetricOperation(['get', 'clients']),
                new MetricOperation(['get', 'clients', Bucket::METRIC_EMPTY_PLACEHOLDER]),
                ['users' => HasIDAtSecondLevel::SECTION_TEST_TRUE],
            ],
        ];
    }

    public function createFromStringMapProvider()
    {
        return [
            ['foo'],
            ['foo:bar:baz'],
            ["foo\n"],
            ["foo:bar\nbaz"],
        ];
    }
}
