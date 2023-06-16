<?php

namespace jsoner;

use ArrayAccess;
use Iterator;
use Countable;

/**
 * Config Class
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @author Jonas Gröger <jonas.groeger@noris.de>
 */
class Config implements ArrayAccess, Iterator, Countable
{
	/**
	 * @var array  Configuration Settings
	 */
	private $configSettings = [];

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
	public function __construct( $defaults = [] ) {

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
	public function __get( $item ):mixed {

		return ( isset( $this->configSettings[$item] ) ) ? $this->configSettings[$item] : false;
	}

	/**
	 * Magic Method for setting a configuration setting
	 *
	 * @param $key string The item key
	 * @param $value mixed The item value
	 * @return void
	 */
	function __set( $key, $value ):void {

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
	public function getItem( $item, $defaultValue = null ):mixed {

		if ( isset( $this->configSettings[$item] ) ) {

			return $this->configSettings[$item];

		}
		return $defaultValue;
	}

	/**
	 * Set a configuration item
	 *
	 * @param $key string The item key
	 * @param $value mixed The item value
	 */
	public function setItem( $key, $value ):void {

		$this->configSettings[$key] = $value;
	}

	public function hasItem( $key ):bool {

		return array_key_exists( $key, $this->configSettings );
	}

	// --------------------------------------------------------------

	/**
	 * Returns configuration items (or all items) as an array
	 *
	 * @param  string|array   Array of items or single item
	 * @return array
	 */
	public function getItems( $items = null ):array {

		if ( $items ) {
			if ( ! is_array( $items ) ) {
				$items = [ $items ];
			}

			$output = [];
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
	public function setItems( $items = [] ):void {

		foreach ( $items as $key => $value ) {
			$this->configSettings[$key] = $value;
		}
	}

	// --------------------------------------------------------------

	/*
	 * Iterator Interface
	 */

	public function rewind():void {

		$this->iteratorCount = 0;
	}

	public function current():mixed {

		$vals = array_values( $this->configSettings );

		return $vals[$this->iteratorCount];
	}

	public function key():mixed {

		$keys = array_keys( $this->configSettings );

		return $keys[$this->iteratorCount];
	}

	public function next():void {

		$this->iteratorCount++;
	}

	public function valid():bool {

		$vals = array_values( $this->configSettings );

		return ( isset( $vals[$this->iteratorCount] ) );
	}

	/*
	 * Count Interface
	 */

	public function count():int {

		return count( $this->configSettings );
	}

	/*
	 * ArrayAccess Interface
	 */

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
	public function offsetExists( $offset ):bool {

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
	public function offsetGet( $offset ):mixed {

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
	public function offsetSet( $offset, $value ):void {

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
	public function offsetUnset( $offset ):void {

		unset( $this->configSettings[$offset] );
	}
}
