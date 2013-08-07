<?php
use Guzzle\Http\Message\Response;
use Resto\Parser\Response\DefaultParser as Parser;

class DefaultResponseParserTest extends PHPUnit_Framework_TestCase
{
	public $data;

	public $assoc_data;

	public function setUp()
	{
		$this->data = array(
			array('id' => 1, 'first_name' => 'Penny', 'gender' => 'female'),
			array('id' => 2, 'first_name' => 'Leonard', 'gender' => 'male'),
			array('id' => 3, 'first_name' => 'Sheldon', 'gender' => 'male'),
			array('id' => 4, 'first_name' => 'Raj', 'gender' => 'male')
		);

		$this->assoc_data                = array();
		$this->assoc_data['bbg_cast']    = $this->data;
		$this->assoc_data['awesomeness'] = 8;
	}

	/**
	 * @expectedException Resto\Exception\ParserException
	 */
	public function testInvalidJsonParse()
	{
		$response = new Response(200, null, 'bazinga');
		$parser   = new Parser($response);
		$this->assertEquals($this->data, $parser->getData());
		$this->assertEquals(null, $parser->getMeta());
	}

	public function testNonAssocDataSetParse()
	{
		$response = new Response(200, null, json_encode($this->data));
		$parser   = new Parser($response);
		$this->assertEquals($this->data, $parser->getData());
		$this->assertEquals(null, $parser->getMeta());
	}

	public function testOneKeyDeepDataSetParse()
	{
		$data = array();
		$data['bbg_cast'] = $this->data;

		$response = new Response(200, null, json_encode($data));
		$parser   = new Parser($response);

		$this->assertEquals($this->data, $parser->getData());
		$this->assertEquals('', $parser->getMeta());
	}

	/**
	 * @expectedException Resto\Exception\ParserException
	 * @expectedExceptionMessage Couldn't find data under 'data', key doesn't exists in response.
	 */
	public function testDataSetWithMultipleKeys()
	{
		$response = new Response(200, null, json_encode($this->assoc_data));
		$parser   = new Parser($response);
		$parser->setDataKey('data');
		$parser->getData();
	}

	public function testDataSetWithConfiguredMultipleKeys()
	{
		$response = new Response(200, null, json_encode($this->assoc_data));
		$parser   = new Parser($response);
		
		$parser->setDataKey('bbg_cast');
		$parser->setMetaKey('awesomeness');

		$this->assertEquals($this->data, $parser->getData());
		$this->assertEquals($this->assoc_data['awesomeness'], $parser->getMeta());
	}

	public function testDataSetWithArrayMetaKeys()
	{
		$data            = $this->assoc_data;
		$data['seasons'] = array('total' => 8, 'episodes' => 120);

		$response = new Response(200, null, json_encode($data));
		$parser   = new Parser($response);
		
		$parser->setDataKey('bbg_cast');
		$parser->setMetaKey(array('awesomeness', 'seasons'));

		$this->assertEquals($this->data, $parser->getData());

		$final_meta = array(
			'awesomeness' => $this->assoc_data['awesomeness'],
			'seasons'     => $data['seasons']
		);

		$this->assertEquals($final_meta, $parser->getMeta());
	}

	/**
	 * @expectedException Resto\Exception\ResponseErrorException
	 * @expectedExceptionMessage Some error occurred
	 */
	public function testSingleError()
	{
		$response = new Response(200, null, json_encode(array('errors' => 'Some error occurred')));
		$parser   = new Parser($response);
		$parser->getData();
	}

	/**
	 * @expectedException Resto\Exception\ResponseErrorException
	 * @expectedExceptionMessage error1
	 */
	public function testMultipleErrors()
	{
		$response = new Response(200, null, json_encode(array('errors' => array('error1', 'error2') )));
		$parser   = new Parser($response);
		$parser->getData();
	}

	/**
	 * @expectedException Resto\Exception\ResponseErrorException
	 * @expectedExceptionMessage error1
	 */
	public function testErrorsInConfiguredKey()
	{
		$response = new Response(200, null, json_encode(array('badstuff' => array('error1', 'error2') )));
		$parser   = new Parser($response);
		$parser->setErrorKey('badstuff');
		$parser->getData();
	}

	public function testSingleAssocData()
	{
		$data     = array_shift($this->data);
		$response = new Response(200, null, json_encode($data));
		$parser   = new Parser($response);

		$this->assertEquals(array($data), $parser->getData());
	}

	//some entities come with assoc data
	public function testSingleAssocDataMultiLevel()
	{
		$data         = array_shift($this->data);
		$data['cars'] = array('Nissan', 'Toyota', 'Cadillac');

		$response = new Response(200, null, json_encode($data));
		$parser   = new Parser($response);

		$this->assertEquals(array($data), $parser->getData());
	}

}