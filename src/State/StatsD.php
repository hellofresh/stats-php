<?php

namespace HelloFresh\Stats\State;


use HelloFresh\Stats\State;
use League\StatsD\Client;

class StatsD implements State
{
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
    public function set($metric, $state)
    {
        $this->client->gauge($metric, $state);
    }
}
