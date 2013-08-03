<?php

class SampleModelConfigured extends Resto\Entity\Model
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