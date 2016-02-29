<?php

namespace jsoner\transformer;

class JsonDumpTransformer extends AbstractTransformer
{
	public function transformZero( ) {

		$emptyJsonObject = "{}";
		$this->transformMultiple( $emptyJsonObject );
	}

	public function transformOne( $json ) {

		$this->transformMultiple( $json );
	}

	public function transformMultiple( $json ) {

		$json_encode_options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		return "<pre>" . json_encode( $json, $json_encode_options ) . "</pre>";
	}

	public function getKey()
	{
		return "t-JsonDump";
	}
}
