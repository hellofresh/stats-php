<?php
namespace HelloFresh\Stats;

/**
 * Describes a bucket instance, responsible for building different types of metrics for an operation.
 */
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
     * Builds simple metric name in the form "<section>.<operation-0>.<operation-1>.<operation-2>".
     *
     * @return string
     */
    public function metric();

    /**
     * Builds metric name with success suffix in the form "<section>-ok|fail.<operation-0>.<operation-1>.<operation-2>".
     *
     * @return string
     */
    public function metricWithSuffix();

    /**
     * Builds simple total metric name in the form total.<section>".
     *
     * @return string
     */
    public function metricTotal();

    /**
     * Builds total metric name with success suffix in the form total-ok|fail.<section>"
     *
     * @return string
     */
    public function metricTotalWithSuffix();
}
