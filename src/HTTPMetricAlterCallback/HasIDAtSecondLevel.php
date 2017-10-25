<?php
namespace HelloFresh\Stats\HTTPMetricAlterCallback;

use HelloFresh\Stats\Bucket;
use HelloFresh\Stats\Bucket\MetricOperation;
use HelloFresh\Stats\HTTPMetricAlterCallback;
use Psr\Http\Message\RequestInterface;

/**
 * HTTPMetricAlterCallback implementation for filtering IDs on the second level of HTTP path,
 * e.g. to build for all requests like "GET /users/1", "GET /users/2", "GET /users/3" metric
 * like "get.users.-id-". See usage examples in README for the library.
 */
class HasIDAtSecondLevel implements HTTPMetricAlterCallback
{
    const SECTION_TEST_TRUE = 'true';
    const SECTION_TEST_IS_NUMERIC = 'numeric';
    const SECTION_TEST_IS_NOT_EMPTY = 'not_empty';
    const SECTIONS_DELIMITER = ':';

    /** @var array */
    protected $map = [];

    /** @var callable[] */
    protected $sectionsTest = [];

    /**
     * HasIDAtSecondLevel constructor.
     *
     * @param array $map sections test map with key as the first section of request path
     *                   and value as section test callback.
     */
    public function __construct(array $map)
    {
        $this->map = $map;

        $this->registerSectionTest(static::SECTION_TEST_TRUE, function ($pathSection) {
            return true;
        })->registerSectionTest(static::SECTION_TEST_IS_NUMERIC, function ($pathSection) {
            return is_numeric($pathSection);
        })->registerSectionTest(static::SECTION_TEST_IS_NOT_EMPTY, function ($pathSection) {
            return $pathSection != Bucket::METRIC_EMPTY_PLACEHOLDER;
        });
    }

    /**
     * Creates HasIDAtSecondLevel instance by building sections test map from string value.
     * Main use-case for this builder method is for settings loaded from config file or environment variable.
     *
     * @param  string $map
     * @param  array  $registerSectionTests section tests that must be registered for a new instance
     * @return self
     */
    public static function createFromStringMap($map, array $registerSectionTests = [])
    {
        $parts = [];
        foreach (explode("\n", $map) as $line) {
            $line = trim($line);
            if ($line !== '') {
                foreach (explode(static::SECTIONS_DELIMITER, $line) as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $parts[] = $part;
                    }
                }
            }
        }

        if (count($parts) % 2 !== 0) {
            throw new \InvalidArgumentException('Invalid sections format');
        }

        $instance = new static([]);
        foreach ($registerSectionTests as $name => $callback) {
            $instance->registerSectionTest($name, $callback);
        }

        $arrayMap = [];
        for ($i = 0; $i < count($parts); $i += 2) {
            $pathSection     = $parts[$i];
            $sectionTestName = $parts[$i + 1];
            if (!isset($instance->sectionsTest[$sectionTestName])) {
                throw new \InvalidArgumentException('Unknown section test callback name: ' . $sectionTestName);
            }
            $arrayMap[$pathSection] = $sectionTestName;
        }

        $instance->map = $arrayMap;

        return $instance;
    }

    /**
     * @param string   $name
     * @param callable $callback section test callback that accepts string test section as parameter and returns bool
     *                           if given parameter passes the test.
     *
     * @return $this
     */
    public function registerSectionTest($name, callable $callback)
    {
        $this->sectionsTest[$name] = $callback;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(MetricOperation $metricParts, RequestInterface $request)
    {
        $firstFragment = '/';
        foreach (explode('/', $request->getUri()->getPath()) as $fragment) {
            if ($fragment !== '') {
                $firstFragment = $fragment;
                break;
            }
        }

        if (isset($this->map[$firstFragment]) && isset($this->sectionsTest[$this->map[$firstFragment]])) {
            if (call_user_func($this->sectionsTest[$this->map[$firstFragment]], $metricParts[2])) {
                $metricParts[2] = Bucket::METRIC_ID_PLACEHOLDER;
            }
        }

        return $metricParts;
    }
}
