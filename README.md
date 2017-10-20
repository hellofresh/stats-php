<p align="center">
  <a href="https://hellofresh.com">
    <img width="120" src="https://www.hellofresh.de/images/hellofresh/press/HelloFresh_Logo.png">
  </a>
</p>

# hellofresh/stats-php

[![Build Status](https://travis-ci.org/hellofresh/stats-php.svg?branch=master)](https://travis-ci.org/hellofresh/stats-php)
[![Coverage Status](https://codecov.io/gh/hellofresh/stats-php/branch/master/graph/badge.svg)](https://codecov.io/gh/hellofresh/stats-php)

> Generic Stats library written in PHP

This is generic stats library that we at HelloFresh use in our projects to collect services' stats and then create monitoring
dashboards to track activity and problems.

## Key Features

* Several stats backends:
  * `log` for development environment
  * `statsd` for production
  * `memory` for testing purpose, to track stats operations in unit tests
  * `noop` for environments that do not require any stats gathering
* Fixed metric sections count for all metrics to allow easy monitoring/alerting setup in `grafana`
* Easy to build HTTP requests metrics - timing and count

## Installation

```sh
composer require hellofresh/stats-php
```

## Usage

### Instance creation

Connection DSN has the following format: `<type>://<connection params>/<connection path>?<connection options>`.

* `<type>` - one of supported backends: `log`, `statsd`, `memory`, `noop`
* `<connection params>` - used for `statsd` backend only, to defining host and port
* `<connection path>` - used for `statsd` backend only, to define prefix/namespace
* `<connection options>` - user for `statsd` backend only:
  * `timeout` - statsd request timeout in seconds, if not set `ini_get('default_socket_timeout')` is used 
  * `error` - throw connection error exception, default value is `true`

```php
<?php

use HelloFresh\Stats\Factory;

$statsdClient = Factory::build('statsd://statsd-host:8125/prefix?timeout=2.5&error=1', $logger);

$logClient = Factory::build('log://', $logger);

$noopClient = Factory::build('noop://', $logger);

$memoryClient = Factory::build('memory://', $logger);

$statsClient = Factory::build(getenv('STATS_DSN'), $logger);
```

### Count metrics manually

```php
<?php

use HelloFresh\Stats\Bucket\MetricOperation;
use HelloFresh\Stats\Factory;

$statsClient = Factory::build(getenv('STATS_DSN'), $logger);

$section = 'ordering';
$timer = $statsClient->buildTimer()->start();
$operation = new MetricOperation(['orders', 'order', 'create']);

try {
    OrdersService::create(...);
    $statsClient->trackOperation($section, $operation, true, $timer);
} catch (\Exception $e) {
    $statsClient->trackOperation($section, $operation, false, $timer);
}

$statsClient->trackMetric('requests', $operation);

$ordersInTheLast24Hours = OrdersService::count(60 * 60 * 24);
$statsClient->trackState($section, $operation, $ordersInTheLast24Hours);
```

## TODO

* [ ] Generalise or modify HTTP Requests metric - e.g. skip ID part
