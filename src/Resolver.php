<?php

namespace jsoner;

use jsoner\exceptions\CurlException;

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

		// Authenticate if User and Pass are provided
		$user = $this->config->getItem( "User");
		$pass = $this->config->getItem( "Pass" );
		if ($user != null && $pass != null) {
			curl_setopt( $ch, CURLOPT_USERPWD, "$user:$pass" );
		}

		$url = str_replace(' ', '%20', $url);

		curl_setopt_array( $ch, [
			CURLOPT_URL => $url,
			CURLOPT_HTTPHEADER => ["Accept: application/json",],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FAILONERROR => true,
			CURLOPT_QUOTE
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
