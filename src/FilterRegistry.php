<?php

namespace jsoner;

class FilterRegistry
{
	private $filters;

	public function __construct( $filters ) {

		$this->filters = $filters;
	}
}
