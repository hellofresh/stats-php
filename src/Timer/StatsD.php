<?php
namespace HelloFresh\Stats\Timer;

use HelloFresh\Stats\Timer;
use League\StatsD\Client;

class StatsD implements Timer
{
    /** @var float */
    protected $startedAt;
    /** @var Client */
    protected $client;

    /**
     * StatsD constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function start()
    {
        $this->startedAt = microtime(true);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function finish($metric)
    {
        // must be in ms, so multiply seconds by 1k
        $time = round((microtime(true) - $this->startedAt) * 1000, 4);
        $this->client->timing($metric, $time);

        return $this;
    }
}
