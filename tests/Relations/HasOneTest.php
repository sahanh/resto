<?php
use Resto\Common\Module as Resource;

class HasOne extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$resource = Resource::register('Foo');
	}

	public function testGeneratedRelationPath()
	{
		$model     = new Foo\SampleModelWithRelation;
		$relation  = $model->sampleModel();
		$this->assertEquals('sample_model_with_relations/1/sample_model', $relation->getQueryPath());
	}

	public function testCustomRelationPath()
	{
		$model     = new Foo\SampleModelWithRelation;
		$relation  = $model->sampleModelCustomPath();
		$this->assertEquals('samplemodels/1/custompath2', $relation->getQueryPath());
	}
}