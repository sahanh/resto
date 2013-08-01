<?php
use Resto\Common\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
	public $request;

	public function setUp()
	{
		$this->request = new Request('http://api.example.come');
	}

	public function testSetPath()
	{
		$request = $this->request;
		$request->setPath('users/2/posts');
		
		$this->assertAttributeEquals('users/2/posts', 'path', $request);
	}

	public function testSetMethod()
	{
		$request = $this->request;
		
		$request->setMethod(Request::METHOD_GET);
		$this->assertAttributeEquals('GET', 'method', $request);

		$request->setMethod(Request::METHOD_POST);
		$this->assertAttributeEquals('POST', 'method', $request);

		$request->setMethod(Request::METHOD_PUT);
		$this->assertAttributeEquals('PUT', 'method', $request);

		$request->setMethod(Request::METHOD_DELETE);
		$this->assertAttributeEquals('DELETE', 'method', $request);
	}

	public function testParams()
	{
		$request = $this->request;
		$request->addParam('foo', 'bar')
				->addParams(array('name' => 'john', 'last' => 'doe'));

		$this->assertAttributeEquals(array('foo' => 'bar', 'name' => 'john', 'last' => 'doe'), 'params', $request);

		$request->removeParam('foo');

		$this->assertAttributeEquals(array('name' => 'john', 'last' => 'doe'), 'params', $request);
	}
}