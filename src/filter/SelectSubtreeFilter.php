<?php

namespace jsoner\filter;

/**
 *
 * Does not modify the array if there is no such subtree.
 *
 */
class SelectSubtreeFilter implements Filter
{
	public static function doFilter( $array, $subtree ) {

		if ( array_key_exists( $subtree, $array ) ) {
			$array = $array[$subtree];
		}
		return $array;
	}
}



