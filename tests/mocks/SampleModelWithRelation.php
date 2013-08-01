<?php
namespace Foo;

use Resto\Entity\Model;

class SampleModelWithRelation extends Model
{
	protected $attributes = array(
		'id' => 1,
		'first_name' => 'foo'
	);

	public function sampleModels()
	{
		return $this->hasMany('SampleModel');
	}

	public function sampleModelsCustomPath()
	{
		return $this->hasMany('SampleModel', "samplemodels/{$this->id}/custompath");
	}
}