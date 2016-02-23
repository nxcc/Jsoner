<?php

namespace jsoner;

use ArrayAccess;
use Iterator;
use Countable;

/**
 * Config Class
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Config implements ArrayAccess, Iterator, Countable
{
	/**
	 * @var array  Configuration Settings
	 */
	private $configSettings = array();

	/**
	 * @var int  Iterator Access Counter
	 */
	private $iteratorCount = 0;

	// --------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array  $defaults   An optional list of defaults to fall back on, set at instantiation
	 */
	public function __construct( $defaults = array() ) {

		// Set the defaults
		$this->configSettings = $defaults;
	}

	// --------------------------------------------------------------

	/**
	 * Magic Method for getting a configuration setting
	 *
	 * @param  string $item The item to get
	 * @return mixed  The value
	 */
	public function __get( $item ) {

		return ( isset( $this->configSettings[$item] ) ) ? $this->configSettings[$item] : false;
	}

	/**
	 * Magic Method for setting a configuration setting
	 *
	 * @param $key string The item key
	 * @param $value mixed The item value
	 * @return void
	 */
	function __set( $key, $value ) {

		$this->configSettings[$key] = $value;
	}

	// --------------------------------------------------------------

	/**
	 * Return a configuration item
	 *
	 * @param  string $item         The configuration item to retrieve
	 * @param  mixed  $defaultValue The default value to return for a configuration item if no
	 *                              configuration item exists
	 * @return mixed  An array containing all configuration items, or a specific configuration
	 *                              item, or NULL
	 */
	public function getItem( $item, $defaultValue = null ) {

		if ( isset( $this->configSettings[$item] ) ) {

			return $this->configSettings[$item];

		} elseif ( strpos( $item, '.' ) !== false ) {

			$cs = $this->configSettings;
			$val = $this->getNestedVar( $cs, $item );
			if ( $val ) {
				return $val;
			}
		}
		return $defaultValue;
	}

	/**
	 * Set a configuration item
	 *
	 * @param $key string The item key
	 * @param $value mixed The item value
	 */
	public function setItem( $key, $value ) {

		$this->configSettings[$key] = $value;
	}

	// --------------------------------------------------------------

	/**
	 * Returns configuration items (or all items) as an array
	 *
	 * @param  string|array   Array of items or single item
	 * @return array
	 */
	public function getItems( $items = null ) {

		if ( $items ) {
			if ( ! is_array( $items ) ) {
				$items = array( $items );
			}

			$output = array();
			foreach ( $items as $item ) {
				$output[$item] = $this->getItem( $item );
			}

			return $output;
		} else {
			return $this->configSettings;
		}
	}

	/**
	 * @param array $items Array of items
	 */
	public function setItems( $items = array() ) {

		foreach ( $items as $key => $value ) {
			$this->configSettings[$key] = $value;
		}
	}

	// --------------------------------------------------------------

	/*
	 * Iterator Interface
	 */

	public function rewind() {

		$this->iteratorCount = 0;
	}

	public function current() {

		$vals = array_values( $this->configSettings );

		return $vals[$this->iteratorCount];
	}

	public function key() {

		$keys = array_keys( $this->configSettings );

		return $keys[$this->iteratorCount];
	}

	public function next() {

		$this->iteratorCount++;
	}

	public function valid() {

		$vals = array_values( $this->configSettings );

		return ( isset( $vals[$this->iteratorCount] ) );
	}

	/*
	 * Count Interface
	 */

	public function count() {

		return count( $this->configSettings );
	}

	// --------------------------------------------------------------

	/**
	 * Merge configuration arrays
	 *
	 * What I would wish that array_merge_recursive actually does...
	 * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
	 *
	 * @param  array $arr1 Array #2
	 * @param  array $arr2 Array #1
	 * @return array
	 */
	private function mergeConfigArrays( $arr1, $arr2 ) {

		foreach ( $arr2 as $key => $value ) {
			if ( array_key_exists( $key, $arr1 ) && is_array( $value ) ) {
				$arr1[$key] = $this->mergeConfigArrays( $arr1[$key], $arr2[$key] );
			} else {
				$arr1[$key] = $value;
			}
		}

		return $arr1;
	}

	// --------------------------------------------------------------

	/**
	 * Get nested variable using dot (val.subval.subsubval) syntax
	 *
	 * From: http://stackoverflow.com/questions/2286706/php-lookup-array-contents-with-dot-syntax
	 *
	 * @param  array  $context
	 * @param  string $name
	 * @return mixed
	 */
	private function getNestedVar( &$context, $name ) {

		$pieces = explode( '.', $name );

		foreach ( $pieces as $piece ) {
			if ( ! is_array( $context ) || ! array_key_exists( $piece, $context ) ) {
				// error occurred
				return null;
			}
			$context = &$context[$piece];
		}

		return $context;
	}

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {

		return isset( $this->configSettings[$offset] );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset ) {

		return isset( $this->configSettings[$offset] ) ? $this->configSettings[$offset] : null;
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value ) {

		if ( is_null( $offset ) ) {
			$this->configSettings[] = $value;
		} else {
			$this->configSettings[$offset] = $value;
		}
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset ) {

		unset( $this->configSettings[$offset] );
	}
}
