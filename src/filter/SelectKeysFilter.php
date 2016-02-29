<?php

namespace jsoner\filter;

use jsoner\Config;

class SelectKeysFilter implements Filter
{
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct( $config ) {

		$this->config = $config;
	}

	public static function doFilter( $array, $params ) {

		$result = [];
		$alwaysInclude = ['id']; // TODO: Make configurable (constructor already there)
		$select_these_keys = array_unique( array_merge( $alwaysInclude, $params ) );

		foreach ( $array as $item ) {
			$result[] = array_intersect_key( $item, array_flip( $select_these_keys ) );
		}
		return $result;
	}
}
