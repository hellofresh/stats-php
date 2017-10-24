<?php
namespace HelloFresh\Stats\State;

use HelloFresh\Stats\State;
use Psr\Log\LoggerInterface;

class Log implements State
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * Log constructor.
     *
     * @param $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function set($metric, $state)
    {
        $this->logger->debug('Stats state set', ['metric' => $metric, 'state' => $state]);
    }
}
