<?php

namespace jsoner;

class TransformerRegistry
{
	private $transformers;

	public function __construct( $transformers ) {

		$this->transformers = $transformers;
	}
}
