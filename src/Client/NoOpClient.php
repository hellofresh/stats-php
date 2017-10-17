<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Bucket\MetricOperation;
use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Timer;
use HelloFresh\Stats\Timer\Memory;
use Psr\Http\Message\RequestInterface;

class NoOp implements Client
{
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricCallback;

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new Memory();
    }

    /**
     * @inheritdoc
     */
    public function trackRequest(RequestInterface $request, Timer $timer, $success)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackOperation($section, MetricOperation $operation, $success, Timer $timer = null, $n = 1)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackMetric($section, MetricOperation $operation, Timer $timer = null, $n = 1)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackState($section, MetricOperation $operation, $value)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHTTPMetricAlterCallback(HTTPMetricAlterCallback $callback)
    {
        $this->httpMetricCallback = $callback;
    }

    /**
     * @inheritdoc
     */
    public function getHTTPMetricAlterCallback()
    {
        return $this->httpMetricCallback;
    }

    /**
     * @inheritdoc
     */
    public function setHTTPRequestSection($section)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetHTTPRequestSection()
    {
        return $this;
    }
}
