# hellofresh/stats-php

[![codecov](https://codecov.io/gh/hellofresh/stats-php/branch/master/graph/badge.svg)](https://codecov.io/gh/hellofresh/stats-php)

> Generic Stats library written in PHP

This is generic stats library that we at HelloFresh use in our projects to collect services' stats and then create monitoring
dashboards to track activity and problems.

## Supported PHP versions

`stats-php` version `1.x` supports the following PHP version: `7.0`, `7.1`, `7.2`.
If you are going to use the library with newer PHP versions - consider using version `2.x+`.

Since version `2.x`, this library start using the built-in `intl` PHP extension instead of `behat/transliterator`

## Key Features

* Several stats backends:
  * `log` for development environment
  * `statsd` for production
  * `memory` for testing purpose, to track stats operations in unit tests
  * `noop` for environments that do not require any stats gathering
* Fixed metric sections count for all metrics to allow easy monitoring/alerting setup in `grafana`
* Easy to build HTTP requests metrics - timing and count
* Generalise or modify HTTP Requests metric - e.g. skip ID part

## Dependencies

### Version `1.x`

- `php: >= 7.0`
- `behat/transliterator: ^1.2`

### Version `2.x`

- `php: >= 7.3`
- `ext-intl: >= 2.0`

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

// php parse_url does not support url with only schema part set
$logClient = Factory::build('log://log', $logger);

// php parse_url does not support url with only schema part set
$noopClient = Factory::build('noop://noop', $logger);

// php parse_url does not support url with only schema part set
$memoryClient = Factory::build('memory://memory', $logger);

// php parse_url does not support url with only schema part set
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

### Generalise resources by type and stripping resource ID

In some cases you do not need to collect metrics for all unique requests, but a single metric for requests of the similar type,
e.g. access time to concrete users pages does not matter a lot, but average access time is important.
`hellofresh/stats-php` allows HTTP Request metric modification and supports ID filtering out of the box, so
you can get generic metric `get.users.-id-` instead thousands of metrics like `get.users.1`, `get.users.13`,
`get.users.42` etc. that may make your `graphite` suffer from overloading.

To use metric generalisation by second level path ID, you can pass
`HelloFresh\Stats\HTTPMetricAlterCallback\HasIDAtSecondLevel` instance to
`HelloFresh\Stats\Client::setHTTPMetricAlterCallback()`. Also there is a builder method
`HelloFresh\Stats\HTTPMetricAlterCallback\HasIDAtSecondLevel::createFromStringMap()`
that builds a callback instance from string map, so you can get these values from config.
It accepts a list of sections with test callback in the following format: `<section>:<test-callback-name>`.
You can use either double colon or new line character as section-callback pairs separator, so all of the following
forms are correct:

* `<section-0>:<test-callback-name-0>:<section-1>:<test-callback-name-1>:<section-2>:<test-callback-name-2>`
* `<section-0>:<test-callback-name-0>\n<section-1>:<test-callback-name-1>\n<section-2>:<test-callback-name-2>`
* `<section-0>:<test-callback-name-0>:<section-1>:<test-callback-name-1>\n<section-2>:<test-callback-name-2>`

Currently the following test callbacks are implemented:

* `true` - second path level is always treated as ID,
  e.g. `/users/13` -> `users.-id-`, `/users/search` -> `users.-id-`, `/users` -> `users.-id-`
* `numeric` - only numeric second path level is interpreted as ID,
  e.g. `/users/13` -> `users.-id-`, `/users/search` -> `users.search`
* `not_empty` - only not empty second path level is interpreted as ID,
  e.g. `/users/13` -> `users.-id-`, `/users` -> `users.-`

You can register your own test callback functions using the
`HelloFresh\Stats\HTTPMetricAlterCallback\HasIDAtSecondLevel::registerSectionTest()` instance method
or the second parameter of builder method - builder method validates test callback functions against the registered list.

```php
<?php

use HelloFresh\Stats\Factory;
use HelloFresh\Stats\HTTPMetricAlterCallback\HasIDAtSecondLevel;

$statsClient = Factory::build(getenv('STATS_DSN'), $logger);
// STATS_IDS=users:numeric:search:not_empty
$callback = HasIDAtSecondLevel::createFromStringMap(getenv('STATS_IDS'));
$statsClient->setHTTPMetricAlterCallback($callback);

$timer = $statsClient->buildTimer()->start();

// GET /users/42 -> get.users.-id-
// GET /users/edit -> get.users.edit
// POST /users -> post.users.-
// GET /search -> get.search.-
// GET /search/friday%20beer -> get.search.-id-
$statsClient->trackRequest($request, $timer, true);
```
