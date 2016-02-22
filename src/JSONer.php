<?php

namespace jsoner;


use jsoner\transformer\HTMLTransformer;

class JSONer
{
    private $config;
    private $options;

    /**
     * @var \jsoner\filter\Filter[] filters
     */
    private $filters;

    /**
     * JSONer constructor.
     * @param \GlobalVarConfig $config
     * @param $options
     */
    public function __construct($config, $options)
    {
        $this->config = $config;
        $this->options = $options;
        $this->filters = $config->get('Filters');
    }

    function applyFilters($json)
    {
        foreach ($this->filters as $filter_class => $parameter_array) {
            $function = $filter_class . '::filter'; // Contract for ::filter in interface \jsoner\filter\Filter

            $json = call_user_func($function, $json, $parameter_array);
        }
        return $json;
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

            // Filter
            $json = self::applyFilters($json);

            // Transform
            $html = HTMLTransformer::transform($json);

            return "<pre>" . json_encode($json, JSON_PRETTY_PRINT) . "</pre><br />" . $html;

        } catch (CurlException $ce) {
            return $ce->getMessage();
        } finally {
            // Nothing
        }
    }
}
