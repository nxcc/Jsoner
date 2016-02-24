<?php

namespace jsoner;


use jsoner\exceptions\CurlException;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    private $emptyConfig;

    protected function setUp()
    {
        $this->emptyConfig = new Config();
    }

    public function testThatInvalidFullUrlsThrowException()
    {
        $this->expectException(CurlException::class);

        $resolver = new Resolver($this->emptyConfig);
        $resolver->resolve("http://example.invalid/");
    }

    public function testThatInvalidUrlsThrowException()
    {
        $this->expectException(CurlException::class);

        $resolver = new Resolver(new Config(["BaseUrl" => "https://example.invalid/"]));
        $resolver->resolve("this/path/is/invalid/");
    }
}
