<?php
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

}