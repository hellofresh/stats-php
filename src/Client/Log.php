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
use Psr\Log\LoggerInterface;

class Log implements Client
{
    /** @var LoggerInterface */
    protected $logger;
    /** @var string */
    protected $httpRequestSection;
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricAlterCallback;

    /**
     * Log constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->resetHTTPRequestSection();
    }

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new Timer\Memory();
    }

    /**
     * @inheritdoc
     */
    public function trackRequest(RequestInterface $request, Timer $timer, $success)
    {
        $bucket = new Bucket\HTTPRequest(
            $this->httpRequestSection, $request, $success, $this->getHTTPMetricAlterCallback()
        );
        $incrementer = new Incrementer\Log($this->logger);

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
        $incrementer = new Incrementer\Log($this->logger);

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
        $incrementer = new Incrementer\Log($this->logger);

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
        $state = new State\Log($this->logger);

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
