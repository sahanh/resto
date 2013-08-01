<?php
use Resto\Common\Resource;

class HasManyTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$resource = Resource::register('Foo');
	}

	public function testGeneratedRelationPath()
	{
		$model     = new Foo\SampleModelWithRelation;
		$relation  = $model->sampleModels();
		$this->assertEquals('samplemodelwithrelations/1/samplemodels', $relation->getQueryPath());
	}

	public function testCustomRelationPath()
	{
		$model     = new Foo\SampleModelWithRelation;
		$relation  = $model->sampleModelsCustomPath();
		$this->assertEquals('samplemodels/1/custompath', $relation->getQueryPath());
	}
}