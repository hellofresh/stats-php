<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer;
use Psr\Log\LoggerInterface;

class Log extends Base implements Client
{
    /** @var LoggerInterface */
    protected $logger;

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
    protected function getIncrementer()
    {
        return new Incrementer\Log($this->logger);
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        return new State\Log($this->logger);
    }
}
