<?php
use Resto\Parser\Request\DefaultParser;
use Resto\Common\Request;

class DefaultRequestParserTest extends PHPUnit_Framework_TestCase
{
	public $request;

	public function setUp()
	{
		$this->request = new Request('/');
	}

	public function testGetDataFormatting()
	{
		//$this->
	}
}