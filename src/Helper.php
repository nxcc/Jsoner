<?php

namespace jsoner;


class Helper
{
    /**
     * Converts an array of values in form [0] => "name=value" into a real
     * associative array in form [name] => value
     *
     * @link https://www.mediawiki.org/wiki/Manual:Parser_functions/de#Named_parameters
     * @param array $options
     * @return array $results
     */
    public static function extractOptions( array $options ) {

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

    /**
     * @param string $errorMessage The message to style.
     * @return string The message as HTML, styled in „error colors“.
     */
    public static function errorMessage($errorMessage)
    {
        return '<span style="color:#FFFFFF; background:#8B0000">' . $errorMessage . '</span>';
    }

    /**
     * @return bool True, if PHP cURL is installed. False otherwise.
     */
    public static function curlIsInstalled() {
        return function_exists( 'curl_version' );
    }
}
