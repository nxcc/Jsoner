<?php

namespace jsoner;

class Resolver
{
	private $config;

	/**
	 * Resolver constructor.
	 * @param \jsoner\Config $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	public function resolve( $url ) {
		$ch = curl_init();
		try {
			$user = $this->config->getItem( "User" );
			$pass = $this->config->getItem( "Pass" );
			curl_setopt( $ch, CURLOPT_USERPWD, "$user:$pass" );
		} catch ( \ConfigException $ex ) {
			wfDebugLog( 'JSONer', 'Either $jsonerUser or $jsonerPass were not set.'
				. 'Trying unauthenticated.' );
		}

		curl_setopt_array( $ch, [
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => ["Accept: application/json",],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
		] );

		$response = curl_exec( $ch );
		$error_message = curl_error( $ch );
		$error_code = curl_errno( $ch );

		curl_close( $ch );

		if ( $response === false ) {
			throw new CurlException( $error_message, $error_code );
		}

		return $response;
	}
}
