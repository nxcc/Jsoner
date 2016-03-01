<?php

namespace jsoner;

use jsoner\exceptions\FilterException;
use jsoner\filter\Filter;

class FilterRegistry
{
	private $config;

	private $filters = [
		'f-SelectKeys' =>    "\\jsoner\\filter\\JsonDumpTransformer",
		'f-SelectSubtree' => "\\jsoner\\filter\\SingleElementTransformer",
		'f-RemoveKeys' =>    "\\jsoner\\filter\\WikitextTableTransformer",
		'f-CensorKeys' =>    "\\jsoner\\filter\\CensorKeysFilter",
	];

	public function __construct( $config ) {
		$this->config = $config;
	}

	public function addFilter( $key, $fqcn ) {
		$this->filters[$key] = $fqcn;
	}

	/**
	 * @param $key
	 * @return Filter
	 * @throws FilterException
	 */
	public function getFilterByKey( $key ) {
		$filterKey = Helper::getArrayValueOrDefault( $this->filters, $key );

		if ( $filterKey !== null ) {
			return new $filterKey( $this->config );
		}

		throw new FilterException( "No such filter: '$key'." );
	}

	public function getFiltersByKeys( $filterKeys ) {

		$filters = [];
		foreach ( $filterKeys as $filter ) {
			$filters[] = $this->getFilterByKey( $filter );
		}
		return $filters;
	}
}
