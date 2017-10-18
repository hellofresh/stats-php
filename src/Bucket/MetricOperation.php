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
            $this->offsetSet($key, $value);
        }

        $opLength = count($operations);
        for ($i = $opLength; $i < static::LENGTH; $i++) {
            $this->offsetSet($i, Bucket::METRIC_EMPTY_PLACEHOLDER);
        }
    }
}
