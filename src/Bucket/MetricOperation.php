<?php

namespace HelloFresh\Stats\Bucket;


use HelloFresh\Stats\Bucket;

class MetricOperation extends \SplFixedArray
{
    const LENGTH = 3;

    /**
     * MetricOperation constructor.
     *
     * @param array $operations
     */
    public function __construct(array $operations = [])
    {
        parent::__construct(static::LENGTH);

        // ensure that operations is not bigger than allowed
        array_splice($operations, static::LENGTH);

        foreach (array_values($operations) as $key => $value) {
            $this->offsetSet($key, $this->valueToString($value));
        }

        $opLength = count($operations);
        for ($i = $opLength; $i < static::LENGTH; $i++) {
            $this->offsetSet($i, Bucket::METRIC_EMPTY_PLACEHOLDER);
        }
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function valueToString($value)
    {
        if (!is_scalar($value) && $value !== null && (is_object($value) && !method_exists($value, '__toString'))) {
            return Bucket::METRIC_EMPTY_PLACEHOLDER;
        }

        $str = (string)$value;
        return empty($str) ? Bucket::METRIC_EMPTY_PLACEHOLDER : $str;
    }
}
