<?php

namespace jsoner\transformer;

class SingleElementTransformer extends AbstractTransformer
{
	public function transformZero( $options ) {

		// TODO: Implement transformZero() method.
	}

	public function transformOne( $json, $options ) {

		$valueToSelect = $options;

		if ( is_array( $json[0] ) ) {
			return $json[0][$valueToSelect];
		}

		return $json[$valueToSelect];

	}

	public function transformMultiple( $json, $options ) {

		// TODO: Implement transformMultiple() method.
	}
}
