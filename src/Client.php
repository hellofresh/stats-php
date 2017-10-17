<?php

namespace HelloFresh\Stats;


use HelloFresh\Stats\Bucket\MetricOperation;
use Psr\Http\Message\RequestInterface;

interface Client
{
    /**
     * Builds timer to track metric timings.
     *
     * @return Timer
     */
    public function buildTimer();

    /**
     * Tracks HTTP Request stats.
     *
     * @param RequestInterface $request
     * @param Timer $timer
     * @param bool $success
     *
     * @return self
     */
    public function trackRequest(RequestInterface $request, Timer $timer, $success);

    /**
     * Tracks custom operation with n diff.
     *
     * @param string $section
     * @param MetricOperation $operation
     * @param bool $success
     * @param Timer|null $timer
     * @param int $n
     *
     * @return self
     */
    public function trackOperation($section, MetricOperation $operation, $success, Timer $timer = null, $n = 1);

    /**
     * Tracks custom metric, w/out ok/fail additional sections.
     *
     * @param string $section
     * @param MetricOperation $operation
     * @param Timer|null $timer
     * @param int $n
     *
     * @return mixed
     */
    public function trackMetric($section, MetricOperation $operation, Timer $timer = null, $n = 1);

    /**
     * Tracks metric absolute value.
     *
     * @param string $section
     * @param MetricOperation $operation
     * @param int $value
     *
     * @return self
     */
    public function trackState($section, MetricOperation $operation, $value);

    /**
     * @param HTTPMetricAlterCallback $callback
     *
     * @return self
     */
    public function setHTTPMetricAlterCallback(HTTPMetricAlterCallback $callback);

    /**
     * @return HTTPMetricAlterCallback
     */
    public function getHTTPMetricAlterCallback();

    /**
     * Sets metric section for HTTP Request metrics
     *
     * @param string $section
     *
     * @return self
     */
    public function setHTTPRequestSection($section);

    /**
     * Resets metric section for HTTP Request metrics to default value that is "request".
     *
     * @return self
     */
    public function resetHTTPRequestSection();
}
