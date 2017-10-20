<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer;
use League\StatsD\Client as StatsDClient;

class StatsD extends AbstractClient implements Client
{
    /** @var string */
    protected $httpRequestSection;
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricAlterCallback;
    /** @var StatsDClient */
    protected $client;

    /** @var Incrementer\StatsD */
    protected $incrementer;
    /** @var State\StatsD */
    protected $state;

    /**
     * StatsD constructor.
     *
     * @param string $dsn statsd connection dsn
     */
    public function __construct($dsn)
    {
        $this->client = new StatsDClient();
        $this->configure($dsn);
        $this->resetHTTPRequestSection();
    }

    /**
     * @param string $dsn
     */
    protected function configure($dsn)
    {
        $url = (array)parse_url($dsn);

        parse_str(empty($url['query']) ? '' : $url['query'], $params);
        $options = [
            'host' => empty($url['host']) ? 'localhost' : $url['host'],
            'port' => empty($url['port']) ? 8125 : $url['port'],
            'namespace' => empty($url['path']) ? '' : trim($url['path'], '/'),
            'timeout' => empty($params['timeout']) ? null : (float)$params['timeout'],
            'throwConnectionExceptions' => isset($params['error']) ? (bool)$params['error'] : true,
        ];

        $this->client->configure($options);
    }

    /**
     * @inheritdoc
     */
    public function buildTimer()
    {
        return new Timer\StatsD($this->client);
    }

    /**
     * @inheritdoc
     */
    protected function getIncrementer()
    {
        if (null === $this->incrementer) {
            $this->incrementer = new Incrementer\StatsD($this->client);
        }
        return $this->incrementer;
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        if (null === $this->state) {
            $this->state = new State\StatsD($this->client);
        }
        return $this->state;
    }
}
