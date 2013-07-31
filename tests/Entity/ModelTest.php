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

	public function testSetAttributeWithMutator()
	{
		
	}

	public function testEntityPath()
	{
		$model = new SampleModel;
		$this->assertEquals($model->getEntityPath(), 'samplemodels');
	}
}