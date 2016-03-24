<?php

namespace jsoner\filter;

use jsoner\Config;

class ReduceFilter implements Filter
{
	private $config;

	/**
	 * @param Config $config
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	public static function doFilter($array, $params) {

		$selector = $params[0];
		$new_variable = $params[1];

		foreach ( $array as &$item ) {

		}
	}
}
