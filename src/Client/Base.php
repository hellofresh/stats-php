<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Bucket\MetricOperation;
use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer;
use Psr\Http\Message\RequestInterface;

abstract class Base implements Client
{
    /** @var string */
    protected $httpRequestSection;
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricAlterCallback;

    /**
     * @return Incrementer
     */
    abstract protected function getIncrementer();

    /**
     * @return State
     */
    abstract protected function getState();

    /**
     * @inheritdoc
     */
    public function trackRequest(RequestInterface $request, Timer $timer, $success)
    {
        $bucket = new Bucket\HTTPRequest(
            $this->httpRequestSection, $request, $success, $this->getHTTPMetricAlterCallback()
        );
        $incrementer = $this->getIncrementer();

        $timer->finish($bucket->metric());
        $incrementer->incrementAll($bucket);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackOperation($section, MetricOperation $operation, $success, Timer $timer = null, $n = 1)
    {
        $bucket = new Bucket\Plain($section, $operation, $success);
        $incrementer = $this->getIncrementer();

        if (null !== $timer) {
            $timer->finish($bucket->metricWithSuffix());
        }
        $incrementer->incrementAll($bucket, $n);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackMetric($section, MetricOperation $operation, Timer $timer = null, $n = 1)
    {
        $bucket = new Bucket\Plain($section, $operation, true);
        $incrementer = $this->getIncrementer();

        if (null !== $timer) {
            $timer->finish($bucket->metricWithSuffix());
        }
        $incrementer->increment($bucket->metric());
        $incrementer->increment($bucket->metricTotal());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function trackState($section, MetricOperation $operation, $value)
    {
        $bucket = new Bucket\Plain($section, $operation, true);
        $state = $this->getState();

        $state->set($bucket->metric(), $value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHTTPMetricAlterCallback(HTTPMetricAlterCallback $callback)
    {
        $this->httpMetricAlterCallback = $callback;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHTTPMetricAlterCallback()
    {
        return $this->httpMetricAlterCallback;
    }

    /**
     * @inheritdoc
     */
    public function setHTTPRequestSection($section)
    {
        $this->httpRequestSection = $section;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resetHTTPRequestSection()
    {
        return $this->setHTTPRequestSection(Bucket::DEFAULT_HTTP_REQUEST_SECTION);
    }
}
