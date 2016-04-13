<?php

namespace jsoner;

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

	public function testBasic() {
		$options = [
			'url' => TestUtil::makeIntegrationTestUrl( __FUNCTION__ . '.json' ),
			't-JsonDump' => null
		];
		$jsoner = new Jsoner( $this->config, $options );
		$output = $jsoner->run();

		$this->assertContains('test', $output);
	}

	public function testOrderOfSelect1() {
		$options = [
			'url' => TestUtil::makeIntegrationTestUrl( __FUNCTION__ . '.json' ),
			'f-SelectKeys=email,name' => null,
			't-JsonDump' => null
		];
		$jsoner = new Jsoner( $this->config, $options );
		$output = $jsoner->run();
		echo $output;
		$this->assertRegExp('/email.*name/s', $output);
	}

	public function testOrderOfSelect2() {
		$options = [
				'url' => TestUtil::makeIntegrationTestUrl( __FUNCTION__ . '.json' ),
				'f-SelectKeys=name,email' => null,
				't-JsonDump' => null
		];
		$jsoner = new Jsoner( $this->config, $options );
		$output = $jsoner->run();
		echo $output;
		$this->assertRegExp('/name.*email/s', $output);
	}
}

























