<?php

namespace HelloFresh\Stats\Incrementer;


use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Incrementer;

abstract class Base implements Incrementer
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
        $this->increment($bucket->metric(), $n);
        $this->increment($bucket->metricWithSuffix(), $n);
        $this->increment($bucket->metricTotal(), $n);
        $this->increment($bucket->metricTotalWithSuffix(), $n);
    }
}
