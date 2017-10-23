<?php
namespace HelloFresh\Stats\Bucket;

use HelloFresh\Stats\HTTPMetricAlterCallback;
use Psr\Http\Message\RequestInterface;

class HTTPRequest extends Plain
{
    /**
     * HTTPRequest constructor.
     *
     * @param string                       $section
     * @param RequestInterface             $request
     * @param bool                         $success
     * @param HTTPMetricAlterCallback|null $callback
     */
    public function __construct($section, RequestInterface $request, $success, HTTPMetricAlterCallback $callback = null)
    {
        parent::__construct($section, static::buildMetricOperation($request, $callback), $success);
    }

    /**
     * @param RequestInterface             $request
     * @param HTTPMetricAlterCallback|null $callback
     *
     * @return MetricOperation
     */
    public static function buildMetricOperation(RequestInterface $request, HTTPMetricAlterCallback $callback = null)
    {
        $operation = new MetricOperation([strtolower($request->getMethod())]);
        if ($request->getUri()->getPath() !== '/') {
            $partsFilled = 1;
            foreach (explode('/', $request->getUri()->getPath()) as $fragment) {
                if ($fragment === '') {
                    continue;
                }

                $operation[$partsFilled] = $fragment;
                $partsFilled++;
                if ($partsFilled >= $operation::LENGTH) {
                    break;
                }
            }
        }

        if (null !== $callback) {
            $operation = call_user_func_array($callback, [$operation, $request]);
        }

        return $operation;
    }
}
