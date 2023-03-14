<?php
namespace HelloFresh\Stats\Client;

use HelloFresh\Stats\Client;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\StatsD\SilentClient as StatsDClient;
use HelloFresh\Stats\Timer;
use League\StatsD\Exception\ConfigurationException;

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
     * @param  string                 $dsn statsd connection dsn
     * @throws ConfigurationException
     */
    public function __construct($dsn)
    {
        $this->client = new StatsDClient();
        $this->client->configure($this->buildOptions($dsn));
        $this->resetHTTPRequestSection();
    }

    /**
     * @param string $dsn
     *
     * @return array
     */
    protected function buildOptions($dsn)
    {
        $url = (array)parse_url($dsn);

        parse_str(empty($url['query']) ? '' : $url['query'], $params);

        return [
            'host' => empty($url['host']) ? 'localhost' : $url['host'],
            'port' => empty($url['port']) ? 8125 : $url['port'],
            'namespace' => empty($url['path']) ? '' : trim($url['path'], '/'),
            'timeout' => empty($params['timeout']) ? null : (float)$params['timeout'],
            'throwConnectionExceptions' => isset($params['error']) ? (bool)$params['error'] : true,
        ];
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
