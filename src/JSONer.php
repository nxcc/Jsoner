<?php

namespace jsoner;

use jsoner\exceptions\CurlException;
use jsoner\exceptions\ParserException;
use jsoner\filter\Filter;
use jsoner\transformer\WikitextTransformer;
use Tracy\Debugger;

class JSONer
{
	/**
	 * @var \jsoner\Config The configuration for JSONer (global)
	 */
	private $config;

	/**
	 * @var array User provided options in the #jsoner call (per request)
	 */
	private $options;

	/**
	 * JSONer constructor.
	 * @param \Config $mwConfig Configuration for JSONer in a MediaWiki data structure.
	 * @param $options
	 */
	public function __construct( $mwConfig, $options ) {

		$this->config = new Config( [
				"BaseUrl" => $mwConfig->get( "BaseUrl" ),
				"User" => $mwConfig->get( "User" ),
				"Pass" => $mwConfig->get( "Pass" ),
				"Parser-ErrorKey" => '_error',
				"ElementOrder" => ["id"], // TODO: Make configurable in $options? or $mwConfig?
				"SubSelectKeysTryOrder" => ["_title", 'id'], // TODO: Also make configurable?
				"Debug" => $mwConfig->get("Debug"),
		] );
		$this->options = $options;
	}

	/**
	 * Here be the plumbing.
	 * @return string
	 */
	public function run() {
		$queryUrl = self::buildUrl( $this->config['BaseUrl'], $this->options['url'] );
		$this->config->setItem( "QueryUrl", $queryUrl );

		if ($this->config->hasItem("Debug"))
		{
			if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
				require_once __DIR__ . '/../vendor/autoload.php';
				$logDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'jsoner-tracy-log-' . uniqid();
				mkdir($logDir, 0777, true);
				Debugger::enable(Debugger::DETECT, $logDir);

				Debugger::$maxLen = 0; // No limit
			}
		}

		try {
			// Resolve
			$resolver = new Resolver( $this->config );
			$json = $resolver->resolve( $queryUrl );

			// Parse
			$parser = new Parser($this->config);
			$json = $parser->parse( $json );

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

			$json = self::orderJson( $json, $this->config );

			// Transform
			$transformer = new WikitextTransformer( $this->config, $this->options);
			return $transformer->transform( $json );

		} catch ( CurlException $ce ) {
			return Helper::errorMessage($ce->getMessage());
		} catch (ParserException $pe) {
			return Helper::errorMessage($pe->getMessage());
		} finally {
			// Nothing
		}

		// TODO: NoSuchFilterException, NoSuchTransformerException
	}

	/**
	 * Builds the URL that is used to resolve the JSON data.
	 *
	 * @param string $baseUrl A base url that is prepended to the queryUrl.
	 * 						  null if not set in LocalSettings.php
	 * @param string $queryUrl The URL part after the baseUrl or a full URL if baseUrl is null.
	 * @return string A full URL
	 */
	private static function buildUrl( $baseUrl, $queryUrl ) {

		if ($baseUrl === null) {
			return $queryUrl;
		}
		$baseUrl = rtrim( $baseUrl, '/' );
		return "$baseUrl/$queryUrl";
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
			if ( !array_key_exists( $filterTag, $filterMap ) ) {
				continue;
			}

			// Empty filter args
			if ( empty( trim( $filterParams ) ) ) {
				continue;
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

	/**
	 * @param $json
	 * @param \jsoner\Config $config
	 * @return array An ordered array according to the configuration
	 */
	private static function orderJson( $json, $config ) {

		$ordering = $config->getItem( "ElementOrder" );

		foreach ( $json as $key => $value ) {
			$json[$key] = array_merge( array_flip( $ordering ), $value );
		}

		return $json;
	}
}
