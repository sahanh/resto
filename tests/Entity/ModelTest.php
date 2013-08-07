<?php
use Resto\Common\Resource;

class ModelTest extends PHPUnit_Framework_TestCase
{
	public function testSetAttributeWithoutMutator()
	{
		$model = new SampleModel;

		$model->id         = 2;
		$model->first_name = 'Sahan';
		$model->last_name  = 'H';

		$this->assertAttributeEquals(array('id' => 2, 'first_name' => 'Sahan', 'last_name' => 'H'), 'attributes', $model);
	}

	public function testFillRaw()
	{
		$data  = array('id' => 2, 'first_name' => 'Sahan', 'last_name' => 'H');
		$model = new SampleModel;
		$model->fillRaw($data);

		$this->assertAttributeEquals($data, 'attributes', $model);
	}

	public function testFill()
	{
		$data  = array('id' => 2, 'first_name' => 'Sahan', 'last_name' => 'H');
		$model = new SampleModel;
		$model->fillRaw($data);

		$this->assertAttributeEquals($data, 'attributes', $model);
	}

	public function testGetSetAttributeWithMutator()
	{
		$model = new Foo\SampleModelConfigured;
		$model->first_name = 'SAHAN';

		$this->assertAttributeEquals(array('first_name' => 'sahan'), 'attributes', $model);
		$this->assertEquals('SAHAN', $model->first_name);
	}

	public function testEntityPath()
	{
		$model = new SampleModel;
		$this->assertEquals($model->getEntityPath(), 'sample_models');
	}

	public function testQuery()
	{
		Resource::register('Foo');
		$query = Foo\SampleModelConfigured::query();
		$this->assertAttributeEquals('sample_model_configureds', 'path', $query);
		$this->assertAttributeEquals('Foo\\SampleModelConfigured', 'model', $query);
	}

	//check request object of query
	public function testQueryRequest()
	{
		Resource::register('Foo');
		$query   = Foo\SampleModelConfigured::query();

		//methods
		$this->assertAttributeEquals('GET', 'method', $query->getRequest());

		$query->setMethod('PUT');
		$this->assertAttributeEquals('PUT', 'method', $query->getRequest());

		//params
		$query->where('limit', 12)->where('email', 'sahan@sahanz.com');
		$this->assertAttributeEquals(array('limit' => 12, 'email' => 'sahan@sahanz.com'), 'params', $query);
	}

	public function testModelQuery()
	{
		Resource::register('Foo');
		$model = new Foo\SampleModelConfigured;
		$model->id = 2;
		$query = $model->getModelQuery();
		$this->assertAttributeEquals('sample_model_configureds/2', 'path', $query);
	}
}