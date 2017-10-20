<?php

namespace HelloFresh\Stats\Incrementer;


use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Incrementer;

abstract class AbstractIncrementer implements Incrementer
{
    /**
     * @inheritdoc
     */
    abstract public function increment($metric, $n = 1);

    /**
     * @inheritdoc
     */
    public function incrementAll(Bucket $bucket, $n = 1)
    {
        $this->increment($bucket->metric(), $n)
            ->increment($bucket->metricWithSuffix(), $n)
            ->increment($bucket->metricTotal(), $n)
            ->increment($bucket->metricTotalWithSuffix(), $n);
    }
}
