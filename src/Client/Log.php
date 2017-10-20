<?php

namespace HelloFresh\Stats\Client;


use HelloFresh\Stats\Client;
use HelloFresh\Stats\Incrementer;
use HelloFresh\Stats\State;
use HelloFresh\Stats\Timer;
use Psr\Log\LoggerInterface;

class Log extends AbstractClient implements Client
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var Incrementer\Log */
    protected $incrementer;
    /** @var State\Log */
    protected $state;

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
        return new Timer\Log($this->logger);
    }

    /**
     * @inheritdoc
     */
    protected function getIncrementer()
    {
        if (null === $this->incrementer) {
            $this->incrementer = new Incrementer\Log($this->logger);
        }
        return $this->incrementer;
    }

    /**
     * @inheritdoc
     */
    protected function getState()
    {
        if (null === $this->state) {
            $this->state = new State\Log($this->logger);
        }
        return $this->state;
    }
}
