<?php

namespace jsoner;

class Parser
{
	/**
	 * @param $json_as_string String A JSON object as string
	 * @return mixed A PHP array structure that is equivalent to the provided JSON.
	 * @throws CurlException If $json_as_string is invalid JSON.
	 */
	public static function parse( $json_as_string ) {

		// Hide warning if there is one
		// See: http://stackoverflow.com/a/2348181/488265
		$decoded_json = @json_decode( $json_as_string, true );

		// PHP sucks
		if ( $decoded_json === null && json_last_error() !== JSON_ERROR_NONE ) {
			$error_message = json_last_error_msg();
			$error_code = json_last_error();
			throw new CurlException( $error_message, $error_code );
		}

		if (array_key_exists('_error', $decoded_json)) {
			throw new CurlException( $decoded_json['_error']['_message'], 42);
		}


		return $decoded_json;
	}
}
