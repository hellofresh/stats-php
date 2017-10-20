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
* Generalise or modify HTTP Requests metric - e.g. skip ID part

## Installation

```sh
composer require hellofresh/stats-php
```

TBD
