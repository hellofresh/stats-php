<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer\StatsD as StatsDTimer;
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
        $url = (array)parse_url($dsn);

        $params = parse_str(empty($url['query']) ? '' : $url['query']);
        $options = [
            'host' => empty($url['host']) ? 'localhost' : '',
            'port' => empty($url['port']) ? $url['port'] : 8125,
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
