<?php

namespace HelloFresh\Stats;


use Psr\Log\LoggerInterface;

class Factory
{
    const STATSD = 'statsd';
    const LOG = 'log';
    const NOOP = 'noop';
    const MEMORY = 'memory';

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
                return new Client\StatsD($dsn);

            case static::LOG:
                return new Client\Log($logger);

            case static::NOOP:
                return new Client\NoOp();

            case static::MEMORY:
                return new Client\Memory();
        }

        throw new \RuntimeException('Unknown client type');
    }
}
