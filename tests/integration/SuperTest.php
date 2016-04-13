<?php

namespace jsoner;

class FakeMWConfig
{
	private $store;

	public function __construct( $initial_values = [] ) {

		$this->store = $initial_values;
	}

	public function get( $name ) {

		if ( array_key_exists( $name, $this->store ) ) {
			return $this->store[$name];
		}
		throw new \UnexpectedValueException( $name . " not in config." );
	}
}

class SuperTest extends \PHPUnit_Framework_TestCase
{
	private $config;

	protected function setUp() {
		$this->config = new FakeMWConfig( [
			"BaseUrl" => null,
			"User" => null,
			"Pass" => null,
		] );
	}

	public function testJsonerConstruct() {
		$options = [];
		$jsoner = new Jsoner( $this->config, $options );
		$this->assertNotNull( $jsoner );
	}

	public function testBasicWebserver() {
		$options = [
			'url' => self::makeUrl( 'testBasicWebserver.json' ),
			't-JsonDump' => null
		];
		$jsoner = new Jsoner( $this->config, $options );
		$out = $jsoner->run();
		echo $out;
	}

	public static function makeUrl( $query ) {
		return sprintf( "http://%s:%d/$query",
				WEB_SERVER_HOST,
				WEB_SERVER_PORT
		);
	}
}

























