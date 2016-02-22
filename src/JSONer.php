<?php

namespace jsoner;

use jsoner\filter\Filter;
use jsoner\transformer\HTMLTransformer;

class JSONer
{
    private $config;
    private $options;

    /**
     * JSONer constructor.
     * @param \GlobalVarConfig $config
     * @param $options
     */
    public function __construct($config, $options)
    {
        $this->config = $config;
        $this->options = $options;
    }

    public function run()
    {
        $baseUrl = rtrim($this->config->get('BaseUrl'), '/');
        $queryUrl = ltrim($this->options['url'], '/');
        $url = "$baseUrl/$queryUrl";

        try {
            // Resolve
            $resolver = new Resolver($this->config);
            $json = $resolver->resolve($url);

            // Parse
            $json = Parser::parse($json);

            // Resolve the user specified filters and filter params
            $filters_with_params = self::mapUserParametersToFiltersWithParams($this->options);

            // Filter
            $json = self::applyFilters($json, $filters_with_params);

            // Transform
            $html = HTMLTransformer::transform($json);

            return "<pre>" . json_encode($json, JSON_PRETTY_PRINT) . "</pre><br />" . $html;
        } catch (CurlException $ce) {
            return $ce->getMessage();
        } finally {
            // Nothing
        }
    }

    /**
     * @param $json
     * @param Filter[] $filters
     * @return mixed
     */
    private static function applyFilters($json, $filters)
    {
        foreach ($filters as $filter_class => $parameter_array) {
            $function = '\\jsoner\\filter\\' . $filter_class . '::doFilter';

            $json = call_user_func($function, $json, $parameter_array);
        }
        return $json;
    }

    private static function mapUserParametersToFiltersWithParams($options)
    {
        $filterMap = [
            'subtree' => ['SelectSubtreeFilter', 1], // 1 arg
            'select' => ['SelectKeysFilter', -1], // varargs
        ];

        $filters = [];
        foreach ($options as $filterTag => $filterParams)
        {
            // Unknown filter
            if(!array_key_exists($filterTag, $filterMap)) continue;

            $filterName = $filterMap[$filterTag][0];
            $filterArgc = $filterMap[$filterTag][1];

            $filters[$filterName] = self::parseFilterParams($filterParams, $filterArgc);

        }

        return $filters;
    }

    /**
     * @param string $filterParams
     * @param integer $filterArgc
     * @return array An array
     */
    private static function parseFilterParams($filterParams, $filterArgc)
    {
        if ($filterArgc === 0) {
            return null;
        }

        if ($filterArgc === 1) {
            // Single parameter only
            return $filterParams;
        }

        return explode(',', $filterParams);
    }
}
