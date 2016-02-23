<?php

namespace jsoner\transformer;

/**
 * When implementing your own Transformer, use this interface
 * to add your super-custom transformer.
 * Interface Transformer
 * @package jsoner\transformer
 */
interface Transformer
{
	public function transform( $json );
}

/**
 * When implementing your own Transformer, you can use this
 * abstract class. Its very thin and just calls different methods
 * depending on the number of arguments. This is since json with
 * only on element might be displayed differently.
 *
 * Class AbstractTransformer
 * @package jsoner\transformer
 */
abstract class AbstractTransformer implements Transformer
{
	public function transform( $json ) {
		$numberOfElements = count( $json );

		\MWDebug::log( "Using Transformer → " . get_class( $this )
				. " with $numberOfElements elements." );

		if ( $numberOfElements === 1 ) {
			return $this->transformOne( $json );
		}

		if ( $numberOfElements >= 1 ) {
			return $this->transformMultiple( $json );
		}

		return $this->transformZero();
	}

	abstract public function transformZero();

	abstract public function transformOne( $json );

	abstract public function transformMultiple( $json );
}
