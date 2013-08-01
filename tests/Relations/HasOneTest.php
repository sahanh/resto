<?php
use Resto\Common\Resource;

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
		$this->assertEquals('samplemodelwithrelations/1/samplemodel', $relation->getQueryPath());
	}

	public function testCustomRelationPath()
	{
		$model     = new Foo\SampleModelWithRelation;
		$relation  = $model->sampleModelCustomPath();
		$this->assertEquals('samplemodels/1/custompath2', $relation->getQueryPath());
	}
}