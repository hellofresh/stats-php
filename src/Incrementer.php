<?php

namespace HelloFresh\Stats;


interface Incrementer
{
    /**
     * Increments metric.
     *
     * @param string $metric
     * @param int $n
     *
     * @return self
     */
    public function increment($metric, $n = 1);

    /**
     * Increments all metrics for given bucket.
     *
     * @param Bucket $bucket
     * @param int $n
     *
     * @return void
     */
    public function incrementAll(Bucket $bucket, $n = 1);
}
