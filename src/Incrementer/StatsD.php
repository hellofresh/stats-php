<?php

namespace HelloFresh\Stats\Incrementer;


use HelloFresh\Stats\Incrementer;
use League\StatsD\Client;

class StatsD extends Base implements Incrementer
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
    public function increment($metric, $n = 1)
    {
        $this->client->increment($metric, $n);
    }
}
