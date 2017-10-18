<?php

namespace HelloFresh\Stats;


use HelloFresh\Stats\Client\Log;
use HelloFresh\Stats\Client\NoOp;
use HelloFresh\Stats\Client\StatsD;
use Psr\Log\LoggerInterface;

class Factory
{
    const STATSD = 'statsd';
    const LOG = 'log';
    const NOOP = 'noop';

    /**
     * Builds Stats Client instance.
     *
     * @param string $dsn
     * @param LoggerInterface $logger
     *
     * @return Client
     * @throws \Exception
     */
    public static function build($dsn, LoggerInterface $logger)
    {
        switch (parse_url($dsn, PHP_URL_SCHEME)) {
            case static::STATSD:
                return new StatsD($dsn);

            case static::LOG:
                return new Log($logger);

            case static::NOOP:
                return new NoOp();
        }

        throw new \RuntimeException('Unknown client type');
    }
}
