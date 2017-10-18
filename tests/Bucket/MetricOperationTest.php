<?php

namespace HelloFresh\Stats\Bucket;


use PHPUnit\Framework\TestCase;

class MetricOperationTest extends TestCase
{
    /**
     * @dataProvider operations
     *
     * @param array $result
     * @param array $initial
     */
    public function testMetricOperation(array $result, array $initial)
    {
        $this->assertEquals($result, (new MetricOperation($initial))->toArray());
    }

    public function operations()
    {
        return [
            [
                ['-', '-', '-'],
                [],
            ],
            [
                ['foo', '-', '-'],
                ['foo'],
            ],
            [
                ['foo', 'bar', '-'],
                ['foo', 'bar'],
            ],
            [
                ['foo', 'bar', 'baz'],
                ['foo', 'bar', 'baz'],
            ],
            [
                ['foo', 'bar', 'baz'],
                ['foo', 'bar', 'baz', 'qux'],
            ],
            [
                ['foo', 'bar', 'baz'],
                ['one' => 'foo', 'two' => 'bar', 'three' => 'baz', 'four' => 'qux'],
            ],
        ];
    }
}
