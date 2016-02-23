<?php

namespace jsoner\filter;

class RemoveKeysFilter implements Filter
{
	public static function doFilter( $array, $params ) {

		foreach ( $array as &$item ) {
			foreach ( $params as $key ) {
				unset( $item[$key] );
			}
		}
		return $array;
	}
}
