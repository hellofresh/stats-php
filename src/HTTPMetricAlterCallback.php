<?php

namespace HelloFresh\Stats;


use HelloFresh\Stats\Bucket\MetricOperation;
use Psr\Http\Message\RequestInterface;

interface HTTPMetricAlterCallback
{
    /**
     * @param MetricOperation $metricParts
     * @param RequestInterface $request
     *
     * @return MetricOperation
     */
    public function __invoke(MetricOperation $metricParts, RequestInterface $request);
}
