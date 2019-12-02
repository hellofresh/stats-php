<?php
namespace HelloFresh\Stats\Bucket;

use HelloFresh\Stats\Bucket;

class Plain implements Bucket
{
    /** @var string */
    protected $section;
    /** @var string */
    protected $operation;
    /** @var bool */
    protected $success;

    protected static $status = [true => Bucket::SUFFIX_STATUS_OK, false => Bucket::SUFFIX_STATUS_FAIL];

    /**
     * Plain constructor.
     *
     * @param string          $section
     * @param MetricOperation $operation
     * @param bool            $success
     */
    public function __construct($section, MetricOperation $operation, $success)
    {
        $this->section = static::sanitizeMetricName($section);
        $this->success = $success;

        $operationSanitized = [];
        foreach ($operation as $op) {
            $operationSanitized[] = static::sanitizeMetricName($op);
        }
        $this->operation = implode('.', $operationSanitized);
    }

    /**
     * @inheritdoc
     */
    public function metric()
    {
        return sprintf('%s.%s', $this->section, $this->operation);
    }

    /**
     * @inheritdoc
     */
    public function metricWithSuffix()
    {
        return sprintf('%s-%s.%s', $this->section, static::$status[$this->success], $this->operation);
    }

    /**
     * @inheritdoc
     */
    public function metricTotal()
    {
        return sprintf('%s.%s', Bucket::TOTAL_BUCKET, $this->section);
    }

    /**
     * @inheritdoc
     */
    public function metricTotalWithSuffix()
    {
        return sprintf('%s.%s-%s', Bucket::TOTAL_BUCKET, $this->section, static::$status[$this->success]);
    }

    /**
     * @param string $metric
     *
     * @return string
     */
    public static function sanitizeMetricName($metric)
    {
        if ($metric === '') {
            return Bucket::METRIC_EMPTY_PLACEHOLDER;
        }

        // convert unicode symbols to ASCII
        $asciiMetric = transliterator_transliterate('Any-Latin; Latin-ASCII;', $metric);
        if ($asciiMetric != $metric) {
            $metric = Bucket::PREFIX_UNICODE . $asciiMetric;
        }

        // replace underscores with double underscores
        // and dots with single underscore
        return str_replace(
            '.',
            '_',
            str_replace('_', '__', $metric)
        );
    }
}
