<?php

namespace HelloFresh\Stats;


interface Bucket
{
    const TOTAL_BUCKET = 'total';

    const SUFFIX_STATUS_OK = 'ok';
    const SUFFIX_STATUS_FAIL = 'fail';

    const PREFIX_UNICODE = '-u-';

    const METRIC_EMPTY_PLACEHOLDER = '-';
    const METRIC_ID_PLACEHOLDER = '-id-';

    const DEFAULT_HTTP_REQUEST_SECTION = 'request';

    /**
     * @return string
     */
    public function metric();

    /**
     * @return string
     */
    public function metricWithSuffix();

    /**
     * @return string
     */
    public function metricTotal();

    /**
     * @return string
     */
    public function metricTotalWithSuffix();
}
