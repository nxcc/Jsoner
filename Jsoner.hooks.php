<?php

use jsoner\Helper;
use jsoner\Jsoner;

/**
 * Hooks for the Jsoner extension.
 *
 * @ingroup Extensions
 */
class JsonerHooks
{
	private static $configPrefix = 'jsoner';

	public static function onParserSetup( &$parser ) {
		$parser->setFunctionHook( 'jsoner', 'JsonerHooks::run' );

		return true; // Always return true, in order not to stop MW's hook processing!
	}

	/**
	 * Provides a callback for configuration in extension.json
	 * @return GlobalVarConfig The configuration for the Jsoner extension
	 */
	public static function buildConfig() {
		return new GlobalVarConfig( self::$configPrefix );
	}

	public static function run( \Parser &$parser ) {
		$parser->disableCache();

		$config = self::getConfig();

		try {
			Helper::assertExtensionsInstalled( ['curl', 'intl', 'fileinfo', 'mbstring'] );
		} catch ( Exception $e ) {
			return Helper::errorMessage( $e->getMessage() );
		}

		$options = Helper::extractOptions( array_slice( func_get_args(), 1 ) );

		$jsoner = new Jsoner( $config, $options );
		return [$jsoner->run(), 'noparse' => false];
	}

	private static function getConfig() {
		return ConfigFactory::getDefaultInstance()->makeConfig( self::$configPrefix );
	}
}
