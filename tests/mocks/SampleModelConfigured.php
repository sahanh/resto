<?php
namespace Foo;
use Resto\Entity\Model;

class SampleModelConfigured extends Model
{
	protected static $fillable = array(
		'first_name',
		'last_name',
		'email'
	);

	public function setFirstNameAttribute($name)
	{
		$this->attributes['first_name'] = strtolower($name);
	}

	public function getFirstNameAttribute($name)
	{
		return strtoupper($this->attributes['first_name']);
	}
}