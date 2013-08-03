<?php

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
		$model = new SampleModelConfigured;
		$model->first_name = 'SAHAN';

		$this->assertAttributeEquals(array('first_name' => 'sahan'), 'attributes', $model);
		$this->assertEquals('SAHAN', $model->first_name);
	}

	public function testEntityPath()
	{
		$model = new SampleModel;
		$this->assertEquals($model->getEntityPath(), 'samplemodels');
	}
}