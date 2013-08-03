<?php
use Closure;
use Resto\Common\Resource;
use Foo\Bar\SampleModel as NamespacedModel;

class CommonResourceTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException Resto\Exception\InvalidResourceException
	 * @expectedExceptionMessage Model must be inside a namespace.
	 * Without namespace
	 */
	public function testInvalidNamespaceResourceResolve()
	{
		SampleModel::getResource();
	}

	/**
	 * @expectedException Resto\Exception\InvalidResourceException
	 * @expectedExceptionMessage No registered resource found. Use Resto\Common\Resource::register('Foo\Bar')
	 */
	public function testUnregisteredResourceResolve()
	{
		Foo\Bar\SampleModel::getResource();
	}

	
	public function testValidResourceResolve()
	{
		$expected = Resource::register('Foo\\Bar');
		$resource = Foo\Bar\SampleModel::getResource();

		$this->assertEquals($expected, $resource);	
	}

	public function testAliasedNamespaceResourceResolve()
	{
		//NamespacedModel is aliased, but should respect to Foo\Bar resource
		$expected = Resource::register('Foo\\Bar');
		$resource = NamespacedModel::getResource();

		$this->assertEquals($expected, $resource);	
	}

	/**
	 * Checking Resource::getNamespace()
	 * @return [type] [description]
	 */
	public function testResourceGetNamespace()
	{
		$res1 = Resource::register('Foo\\Bar');
		$res2 = Resource::register('Bar');

		$this->assertEquals('Foo\\Bar', $res1->getNamespace());
		$this->assertEquals('Bar', $res2->getNamespace());
	}

	public function testCallbackGetSet()
	{
		$res = Resource::register('Foo\\Bar');
		$res->setCallback('initiateRequest', function(){
			return 'foo';	
		});

		$this->assertInstanceOf('Closure', $res->getCallback('initiateRequest'));
	}

	public function testNonExistCallbackGet()
	{
		$res = Resource::register('Foo\\Bar');
		$this->assertEquals(false, $res->getCallback('initiateRequest'));
		$this->assertEquals(false, $res->getCallback('blahblah'));
	}

	public function testGetRequest()
	{
		$res = Resource::register('Foo\\Bar');
		$res->setEndPoint('https://example.com/');

		$req = $res->getRequest();

		$this->assertAttributeEquals('https://example.com', 'endpoint', $req); //endpoint should remove end /
	}

}