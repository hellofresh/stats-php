<?php

namespace HelloFresh\Stats\Bucket;


use HelloFresh\Stats\HTTPMetricAlterCallback;
use Psr\Http\Message\RequestInterface;

class HTTPRequest extends Plain
{
    /** @var RequestInterface */
    protected $request;
    /** @var HTTPMetricAlterCallback */
    protected $httpMetricAlterCallback;

    /**
     * HTTPRequest constructor.
     *
     * @param string $section
     * @param RequestInterface $request
     * @param bool $success
     * @param HTTPMetricAlterCallback|null $callback
     */
    public function __construct($section, RequestInterface $request, $success, HTTPMetricAlterCallback $callback = null)
    {
        $this->request = $request;
        $this->httpMetricAlterCallback = $callback;

        parent::__construct($section, $this->buildMetricOperation(), $success);
    }

    /**
     * @return MetricOperation
     */
    public function buildMetricOperation()
    {
        $operation = new MetricOperation(strtolower($this->request->getMethod()));
        if ($this->request->getUri()->getPath() != '/') {
            $partsFilled = 1;
            foreach (explode('/', $this->request->getUri()->getPath()) as $fragment) {
                if ($fragment == '') {
                    continue;
                }

                $operation[$partsFilled] = $fragment;
                $partsFilled++;
                if ($partsFilled >= $operation::LENGTH) {
                    break;
                }
            }
        }

        if (null != $this->httpMetricAlterCallback) {
            $operation = call_user_func_array($this->httpMetricAlterCallback, [$operation, $this->request]);
        }

        return $operation;
    }
}
