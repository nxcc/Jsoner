<?php

namespace jsoner;

use jsoner\exceptions\NoSuchTransformerException;
use jsoner\transformer\JsonDumpTransformer;
use jsoner\transformer\SingleElementTransformer;
use jsoner\transformer\Transformer;
use jsoner\transformer\WikitextTableTransformer;

class TransformerRegistry
{
	private $config;
	private $options;

	private $transformers;

	public function __construct( $config, $options ) {

		$this->config = $config;
		$this->options = $options;
	}

	private static function getTransformerName($options)
	{
		foreach ($options as $key => $val) {
			if (strpos($key, 't-') === 0) {
				return $key;
			}
		}
		throw new NoSuchTransformerException("No transformer was specified by the user!");
	}

	public function registerBuiltinTransformers()
	{
		$this->transformers[JsonDumpTransformer::getKey()]
				= "\\jsoner\\transformer\\JsonDumpTransformer";

		$this->transformers[SingleElementTransformer::getKey()]
				= "\\jsoner\\transformer\\SingleElementTransformer";

		$this->transformers[WikitextTableTransformer::getKey()]
				= "\\jsoner\\transformer\\WikitextTableTransformer";
	}

	/**
	 * @param $options
	 * @return Transformer
	 * @throws NoSuchTransformerException If no transformer was found for the query.
	 */
	public function getTransformerByKey($options)
	{
		$transformerName = self::getTransformerName($options);

		if (isset($this->transformers[$transformerName])) {
			$r = new \ReflectionClass($this->transformers[$transformerName]);
			return $r->newInstance($this->config, $this->options);
		}

		throw new NoSuchTransformerException("Transformer '" . $transformerName . "' not found.");
	}
}
