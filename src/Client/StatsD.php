<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer\StatsD as StatsDTimer;
use League\StatsD\Client as StatsDClient;

class StatsD extends Base implements Client
{
    /** @var string */
    protected $httpRequestSection;
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricAlterCallback;
    /** @var StatsDClient */
    protected $client;

    /**
     * StatsD constructor.
     *
     * @param string $dsn statsd connection dsn
     */
    public function __construct($dsn)
    {
        $url = parse_url($dsn);

        $params = parse_str(empty($url['query']) ? '' : $url['query']);
        $options = [
            'host' => $url['host'],
            'port' => $url['port'],
            'namespace' => empty($params['ns']) ? '' : $params['ns'],
            'timeout' => empty($params['timeout']) ? null : (float)$params['timeout'],
            'throwConnectionExceptions' => empty($params['error']) ? true : (bool)$params['error'],
        ];

        $this->client = (new StatsDClient())->configure($options);
    }

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new StatsDTimer($this->client);
    }

    /**
     * @inheritdoc
     */
    protected function getIncrementer()
    {
        return new Incrementer\StatsD($this->client);
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        return new State\StatsD($this->client);
    }
}
