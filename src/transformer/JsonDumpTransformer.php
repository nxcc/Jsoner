<?php

namespace jsoner\transformer;

class JsonDumpTransformer extends AbstractTransformer
{
	public function transformZero( $config ) {

		$emptyJsonObject = "{}";
		$this->transformMultiple( $emptyJsonObject, $config );
	}

	public function transformOne( $json, $config ) {

		$this->transformMultiple( $json, $config );
	}

	public function transformMultiple( $json, $config ) {

		$json_encode_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		return "<pre>" . json_encode( $json, $json_encode_options ) . "</pre>";
	}
}
