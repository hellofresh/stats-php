<?php
namespace HelloFresh\Stats\Timer;

use HelloFresh\Stats\Timer;
use Psr\Log\LoggerInterface;

class Log implements Timer
{
    /** @var LoggerInterface */
    protected $logger;
    /** @var float */
    protected $startedAt;

    /**
     * Memory constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        $this->logger->debug(
            'Stats timer finished',
            [
                'metric' => $metric,
                'elapsed' => (new Metric('', microtime(true) - $this->startedAt))->getElapsedFormatted(),
            ]
        );

        return $this;
    }
}
