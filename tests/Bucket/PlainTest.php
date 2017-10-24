<?php
namespace HelloFresh\Stats\Bucket;

use PHPUnit\Framework\TestCase;

class PlainTest extends TestCase
{
    /**
     * @dataProvider metrics
     *
     * @param string          $section
     * @param MetricOperation $operation
     * @param bool            $success
     * @param string          $metric
     * @param string          $metricWithSuffix
     * @param string          $metricTotal
     * @param string          $metricTotalWithSuffix
     */
    public function testPlain(
        $section,
        MetricOperation $operation,
        $success,
        $metric,
        $metricWithSuffix,
        $metricTotal,
        $metricTotalWithSuffix
    ) {
        $bucket = new Plain($section, $operation, $success);

        $this->assertEquals($metric, $bucket->metric());
        $this->assertEquals($metricWithSuffix, $bucket->metricWithSuffix());
        $this->assertEquals($metricTotal, $bucket->metricTotal());
        $this->assertEquals($metricTotalWithSuffix, $bucket->metricTotalWithSuffix());
    }

    public function testSanitizeMetricName()
    {
        $this->assertEquals('-', Plain::sanitizeMetricName(''));

        $this->assertEquals('-u-iunikod', Plain::sanitizeMetricName('юникод'));
        $this->assertEquals('-u-Apollon', Plain::sanitizeMetricName('Ἀπόλλων'));
        $this->assertEquals('-u-acougue', Plain::sanitizeMetricName('açougue'));

        $this->assertEquals('metric', Plain::sanitizeMetricName('metric'));
        $this->assertEquals('metric_with_dots', Plain::sanitizeMetricName('metric.with.dots'));
        $this->assertEquals('metric__with__underscores', Plain::sanitizeMetricName('metric_with_underscores'));
        $this->assertEquals(
            'metric_with_dots__and__underscores',
            Plain::sanitizeMetricName('metric.with.dots_and_underscores')
        );

        $this->assertEquals('-u-iunikod_metrika', Plain::sanitizeMetricName('юникод.метрика'));
    }

    public function metrics()
    {
        return [
            [
                'foo',
                new MetricOperation(['bar', 'baz', 'qaz']),
                true,
                'foo.bar.baz.qaz',
                'foo-ok.bar.baz.qaz',
                'total.foo',
                'total.foo-ok',
            ],
            [
                'foo',
                new MetricOperation(['bar', 'baz']),
                false,
                'foo.bar.baz.-',
                'foo-fail.bar.baz.-',
                'total.foo',
                'total.foo-fail',
            ],
            [
                'foo',
                new MetricOperation(['bar']),
                true,
                'foo.bar.-.-',
                'foo-ok.bar.-.-',
                'total.foo',
                'total.foo-ok',
            ],
        ];
    }
}
