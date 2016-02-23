<?php

/**
 * Hooks for the JSONer extension.
 *
 * @ingroup Extensions
 */
class JSONerHooks
{
	private static $configPrefix = 'jsoner';

	public static function onParserSetup( &$parser ) {

		$parser->setFunctionHook( 'jsoner', 'JSONerHooks::run' );

		MWDebug::log( "JSONer: Registering parser extension" );

		return true; // Always return true, in order not to stop MW's hook processing!
	}

	/**
	 * Provides a callback for configuration in extension.json
	 * @return GlobalVarConfig The configuration for the JSONer extension
	 */
	public static function buildConfig() {
		return new GlobalVarConfig( self::$configPrefix );
	}

	public static function run( \Parser &$parser ) {
		// Since this extension calls an external
		$parser->disableCache();

		$config = self::getConfig();

		// TODO: Add i18n
		if ( !self::curlIsInstalled() ) {
			return "<span style='color: red;'>PHP extension cURL not installed.</span>";
		}

		$opts = array();
		$num_args = func_num_args();
		// Argument 0 is $parser, so begin iterating at 1
		for ( $i = 1; $i < $num_args; $i++ ) {
			$opts[] = func_get_arg( $i );
		}
		$options = self::extractOptions( $opts );

		$jsoner = new jsoner\JSONer( $config, $options );
		return $jsoner->run();
	}

	private static function getConfig() {
		$config = ConfigFactory::getDefaultInstance()->makeConfig( self::$configPrefix );
		MWDebug::log( "JSONer: Loaded configuration -> " . print_r( $config, true ) );
		return $config;
	}

	private static function curlIsInstalled() {
		return function_exists( 'curl_version' );
	}

	/**
	 * Converts an array of values in form [0] => "name=value" into a real
	 * associative array in form [name] => value
	 *
	 * @link https://www.mediawiki.org/wiki/Manual:Parser_functions/de#Named_parameters
	 * @param array $options
	 * @return array $results
	 */
	private static function extractOptions( array $options ) {

		$results = [];
		foreach ( $options as $option ) {
			$pair = explode( '=', $option, 2 );
			if ( count( $pair ) == 2 ) {
				$name = trim( $pair[0] );
				$value = trim( $pair[1] );
				$results[$name] = $value;
			}
		}
		// Now you've got an array that looks like this:
		// [foo] => bar
		// [apple] => orange
		return $results;
	}
}
