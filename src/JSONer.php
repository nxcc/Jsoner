<?php

namespace jsoner;

use Exception;
use jsoner\exceptions\FilterException;
use jsoner\exceptions\HttpUriFormatException;
use jsoner\exceptions\CurlException;
use jsoner\exceptions\TransformerException;
use jsoner\exceptions\ParserException;
use jsoner\filter\Filter;

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
				"CustomFilters" => $mwConfig->get( "CustomFilters" ),
				"CustomTransformers" => $mwConfig->get( "CustomTransformers" ),

		] );
		$this->options = $options;
	}

	/**
	 * Here be the plumbing.
	 * @return string
	 */
	public function run() {

		// Autoload the composer dependencies, since Mediawiki doesen't do it.
		self::doAutoload();

		$transformerRegistry = new TransformerRegistry( $this->config );
		# $filterRegistry = new FilterRegistry($this->config);

		try {
			// Resolve
			$resolver = new Resolver( $this->config, $this->options['url'] );
			$json = $resolver->resolve();

			// Parse
			$parser = new Parser( $this->config );
			$json = $parser->parse( $json );

			// Filter
			# $filterKeys = self::getFiltersFromOptions( $this->options );
			# $filters = $filterRegistry->getFiltersByKeys($filterKeys);

			// Resolve the user specified filters and filter params
			$filters_with_params = self::mapUserParametersToFiltersWithParams( $this->options );

			// Filter
			$json = self::applyFilters( $json, $filters_with_params );

			// Order the keys according to the config
			$json = self::orderJson( $json, $this->config );

			// Transform
			$transformerKey = self::getTransformerKeyFromOptions( $this->options );
			$transformer = $transformerRegistry->getTransformerByKey( $transformerKey );
			return $transformer->transform( $json, $this->options[$transformerKey] );

		} catch ( CurlException $ce ) {
			return Helper::errorMessage( $ce->getMessage() );
		} catch ( ParserException $pe ) {
			return Helper::errorMessage( $pe->getMessage() );
		} catch ( HttpUriFormatException $hufe ) {
			return Helper::errorMessage( $hufe->getMessage() );
		} catch ( TransformerException $nste ) {
			return Helper::errorMessage( $nste->getMessage() );
		} catch ( FilterException $fe ) {
			return Helper::errorMessage( $fe->getMessage() );
		} catch ( Exception $catchAll ) {
			return Helper::errorMessage( "Unexpected error: " . $catchAll->getMessage() );
		}
	}

	# ##########################################################################
	# Filter ###################################################################

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

	# ##########################################################################
	# Ordering #################################################################

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

	# ##########################################################################
	# Transformer ##############################################################

	private static function getTransformerKeyFromOptions( $options ) {
		$foundTransformers = [];
		foreach ( $options as $key => $val ) {
			if ( strpos( $key, 't-' ) === 0 ) {
				$foundTransformers[] = $key;
			}
		}

		$numFoundTransformers = count( $foundTransformers );
		if ( $numFoundTransformers == 1 ) {
			return $foundTransformers[0];
		}

		throw new TransformerException( "Must provide exactly one transformer. "
				. "$numFoundTransformers provided: " . implode( ', ', $foundTransformers ) );
	}

	# ##########################################################################
	# Misc #####################################################################

	private static function doAutoload() {
		if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
			require_once __DIR__ . '/../vendor/autoload.php';
		}
	}
}
