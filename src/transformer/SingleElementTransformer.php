<?php

namespace jsoner\transformer;


class SingleElementTransformer extends AbstractTransformer
{
	private $valueToSelect;

	public function __construct($config, $options)
	{
		parent::__construct($config, $options);
		$this->valueToSelect = $this->options[$this->getKey()];
	}

	public function transformZero()
	{
		// TODO: Implement transformZero() method.
	}

	public function transformOne($json)
	{
		if (is_array($json[0])) {
			return $json[0][$this->valueToSelect];
		}

		return $json[$this->valueToSelect];

	}

	public function transformMultiple($json)
	{
		// TODO: Implement transformMultiple() method.
	}

	public function getKey()
	{
		return "t-SingleElement";
	}
}
