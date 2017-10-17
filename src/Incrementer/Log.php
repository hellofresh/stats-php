<?php

namespace HelloFresh\Stats\Incrementer;


use HelloFresh\Stats\Incrementer;
use Psr\Log\LoggerInterface;

class Log extends Base implements Incrementer
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
    public function increment($metric, $n = 1)
    {
        $this->logger->debug('Stats counter increment', ['metric' => $metric, 'n' => $n]);
    }
}
