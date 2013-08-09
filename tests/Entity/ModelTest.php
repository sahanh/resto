<?php
use Resto\Common\Module as Resource;

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
		$this->assertInstanceOf('Foo\\SampleModelConfigured', $query->getModelTemplate());
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

	public function testInModelRelations()
	{
		$attr = array(
			'id' => 2,
			'first_name'    => 'Sahan',
			'last_name'     => 'H',
			'sample_models' => array(
				array('first_name' => 'Sheldon', 'last_name' => 'Cooper'),
				array('first_name' => 'Leonard', 'last_name' => 'Hofstadter')	
			),
 			'sample_model'  => array('first_name' => 'Penny')
		);

		$model = new Foo\SampleModelWithRelation;
		$model->fillRaw($attr);

		$this->assertInstanceOf('Foo\\SampleModel', $model->sample_model);
		$this->assertAttributeEquals($attr['sample_model'], 'attributes', $model->sample_model);

		$this->assertContainsOnlyInstancesOf('Foo\\SampleModel', $model->sample_models);
		$this->assertAttributeEquals($attr['sample_models'][0], 'attributes', $model->sample_models[0]);
		$this->assertAttributeEquals($attr['sample_models'][1], 'attributes', $model->sample_models[1]);
	}
}