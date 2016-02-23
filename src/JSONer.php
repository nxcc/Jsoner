<?php

namespace jsoner;

use jsoner\filter\Filter;
use jsoner\transformer\WikitextTransformer;

class JSONer
{
	private $config;
	private $options;

	/**
	 * JSONer constructor.
	 * @param \GlobalVarConfig $config Configuration for JSOner
	 * @param $options
	 */
	public function __construct( $config, $options ) {

		$this->config = $config;
		$this->options = $options;
	}

	/**
	 * Here be the plumbing.
	 * @return string
	 * @throws \ConfigException
	 */
	public function run() {

		$url = self::buildUrl( $this->config, $this->options );

		try {
			// Resolve
			$resolver = new Resolver( $this->config );
			$json = $resolver->resolve( $url );

			// Parse
			$json = Parser::parse( $json );

			// TODO: Implement FilterRegistry like this:
			// $filterRegistry = new FilterRegistry($this->options);
			// $filterRegistry.registerFiltersFromFromNamespace("\\jsoner\\filter\\");

			// Resolve the user specified filters and filter params
			$filters_with_params = self::mapUserParametersToFiltersWithParams( $this->options );

			// Filter
			$json = self::applyFilters( $json, $filters_with_params );

			// TODO: Implement TransformerRegistry like this:
			// $transformerRegistry = new TransformerRegistry($this->options);
			// $transformerRegistry->registerTransformersFromFromNamespace("\\jsoner\\transformer\\");

			// Transform
			$transformer = new WikitextTransformer( $url );
			return $transformer->transform( $json );

		} catch ( CurlException $ce ) { // TODO: NoSuchFilterException, NoSuchTransformerException
			return $ce->getMessage();
		} finally {
			// Nothing
		}
	}

	/**
	 * @param \GlobalVarConfig $config Configuration for JSOner
	 * @param array $options Provided by the user in a query
	 * @return string The url to query
	 */
	private static function buildUrl( $config, $options ) {

		$baseUrl = rtrim( $config->get( 'BaseUrl' ), '/' );
		$queryUrl = trim( $options['url'], '/' );
		return "$baseUrl/$queryUrl/";
	}

	private static function mapUserParametersToFiltersWithParams( $options ) {

		$filterMap = [
			'subtree' => ['SelectSubtreeFilter', 1], // 1 Argument
			'select' => ['SelectKeysFilter', -1],    // Varargs
			'remove' => ['RemoveKeysFilter', -1],    // Varargs
		];

		$filters = [];
		foreach ( $options as $filterTag => $filterParams ) {

			// Unknown filter
			if ( !array_key_exists( $filterTag, $filterMap ) ) { continue;
	  }

			// Empty filter args
			if ( empty( trim( $filterParams ) ) ) { continue;
	  }

			$filterName = $filterMap[$filterTag][0];
			$filterArgc = $filterMap[$filterTag][1];

			$filters[$filterName] = self::parseFilterParams( $filterParams, $filterArgc );
		}
		return $filters;
	}

	/**
	 * @param string $filterParams
	 * @param integer $filterArgc
	 * @return array An array
	 */
	private static function parseFilterParams( $filterParams, $filterArgc ) {

		if ( $filterArgc === 0 ) {
			return null;
		}

		if ( $filterArgc === 1 ) {
			// Single parameter only
			return $filterParams;
		}

		return explode( ',', $filterParams );
	}

	/**
	 * @param $json
	 * @param Filter[] $filters
	 * @return mixed
	 */
	private static function applyFilters( $json, $filters ) {

		foreach ( $filters as $filter_class => $parameter_array ) {
			$function = '\\jsoner\\filter\\' . $filter_class . '::doFilter';

			$json = call_user_func( $function, $json, $parameter_array );
		}
		return $json;
	}
}
