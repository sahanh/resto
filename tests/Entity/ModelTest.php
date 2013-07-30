<?php

class ModelTest extends PHPUnit_Framework_TestCase
{

	public function testGetResourceSlug()
	{
		$model  = new SampleModel;
		$return = static::callProtectedMethod('SampleModel', $model, 'getResourceSlug');
		$this->assertEquals('samplemodels', $return);
	}

	protected static function callProtectedMethod($class, $object, $method_name, $params = array())
	{
		$reflection = new ReflectionClass($class);
		$reflection_method = $reflection->getMethod($method_name);
		$reflection_method->setAccessible(true);
		
		return $reflection_method->invokeArgs($object, $params);
	}
}