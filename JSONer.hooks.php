<?php

use jsoner\Helper;
use jsoner\JSONer;

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
		$parser->disableCache();

		$config = self::getConfig();

		if ( !Helper::curlIsInstalled() ) {
			return Helper::errorMessage(wfMessage( 'jsoner-curl-not-installed' ));
		}

		$options = Helper::extractOptions(array_slice(func_get_args(), 1));

		$jsoner = new JSONer( $config, $options );
		return $jsoner->run();
	}

	private static function getConfig() {
		return ConfigFactory::getDefaultInstance()->makeConfig( self::$configPrefix );
	}
}
