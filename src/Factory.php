<?php

namespace HelloFresh\Stats;


use HelloFresh\Stats\Client\Log;
use HelloFresh\Stats\Client\NoOp;
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
     * @param string $prefix
     * @param LoggerInterface $logger
     *
     * @return Client
     * @throws \Exception
     */
    public static function build($dsn, $prefix, LoggerInterface $logger)
    {
        $url = parse_url($dsn);
        switch ($url['scheme']) {
            case static::STATSD:
                return null;

            case static::LOG:
                return new Log($logger);

            case static::NOOP:
                return new NoOp();
        }

        throw new \RuntimeException('Unknown client type');
    }
}
