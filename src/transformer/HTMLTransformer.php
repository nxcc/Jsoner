<?php

namespace jsoner\transformer;

class HTMLTransformer implements Transformer
{
	public static function transform( $json ) {

		return "<code>WHAT();</code>";
	}
}
