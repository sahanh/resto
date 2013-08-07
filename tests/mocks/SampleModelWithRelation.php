<?php
namespace Foo;

use Resto\Entity\Model;

class SampleModelWithRelation extends Model
{
	protected $attributes = array(
		'id' => 1,
		'first_name' => 'foo'
	);

	//has many
	public function sampleModels()
	{
		return $this->hasMany('SampleModel');
	}

	//has many
	public function sampleModelsCustomPath()
	{
		return $this->hasMany('SampleModel', "samplemodels/{$this->id}/custompath");
	}

	//has one
	public function sampleModel()
	{
		return $this->hasOne('SampleModel');
	}

	public function sampleModelCustomPath()
	{
		return $this->hasOne('SampleModel', "samplemodels/{$this->id}/custompath2");
	}
}

class SampleModel extends Model
{

}