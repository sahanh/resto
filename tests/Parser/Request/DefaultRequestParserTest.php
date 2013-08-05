<?php
use Resto\Parser\Request\DefaultParser as Parser;
use Resto\Common\Resource;
use Resto\Common\Query;

class DefaultRequestParserTest extends PHPUnit_Framework_TestCase
{
	public $query;

	public function setUp()
	{
		$this->query =  new Query(Resource::register('Foo'));
	}

	public function testDataFormatting()
	{
		$data  = array('foo' => 'bar', 'name' => 'john'); 
		$query = $this->query;
		$query->addParams($data);

		$query->setMethod('POST');
		$parser = new Parser($query);

		$query->setMethod('PUT');
		$this->assertEquals($data, $parser->getRequest()->getPostFields());

		$query->setMethod('DELETE');
		$this->assertEquals($data, $parser->getRequest()->getPostFields());

		$query->setMethod('GET');
		$this->assertEquals($data, $parser->getRequest()->getParams());
	}
}