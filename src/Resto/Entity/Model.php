<?php
/**
 * Model, REST Resource entity
 */
namespace Resto\Entity;

use Resto\Common\Str;
use Resto\Common\Resource;

class Model
{
	/**
	 * Resrouce name without the base url. ie:- users in http://api.domain.com/users
	 * @var string
	 */
	protected static $resource_slug;

	/**
	 * Unique identifier key
	 * @var string
	 */
	protected $id = 'id';

	/**
	 * Valid attributes for the entity.
	 * If specified, will be filtered before doing the API request
	 * @var [type]
	 */
	protected $attributes = array();

	public static function find()
	{

	}

	/**
	 * Get the resource instance for this model
	 * @return Resto\Common\Resource
	 */
	public static function getResource()
	{
		return Resource::resolve(get_called_class());
	}

	/**
	 * Get HTTP request object to add more params.
	 * @return [type] [description]
	 */
	public static function query()
	{

	}

	/**
	 * Fill and make a model
	 * @param  array $data
	 */
	protected function fill(Array $data)
	{
		foreach ($data as $key => $value)
			$this->setAttribute($key, $value);
	}

	/**
	 * Set attribute to attribute array.
	 * @param string $key
	 * @param string|int $value
	 */
	public function setAttribute($key, $value)
	{	
		$mutator_method = $this->generateSetMutatorName($key);
		
		if (method_exists($this, $mutator_method))
			$this->$mutator_method($key, $value);
		else
			$this->attributes[$key] = $value;	
	}

	/**
	 * Get an attribute
	 * @param  string $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{	
		$mutator_method = $this->generateGetMutatorName($key);
		
		if (method_exists($this, $mutator_method))
			return $this->$mutator_method($key);
		else
			return $this->attributes[$key];	
	}

	/**
	 * Generate mutator method's name to set attribute. Used to check if method exists
	 * @param  string $key method name
	 * @return string
	 */
	protected function generateSetMutatorName($key)
	{
		$key    = Str::studly($attribute);
		return "set{$key}Attribute";
	}

	/**
	 * Generate mutator method's name to get attribute. Used to check if method exists
	 * @param  string $key method name
	 * @return string
	 */
	protected function generateGetMutatorName($key)
	{
		$key    = Str::studly($attribute);
		return "get{$key}Attribute";
	}

	/**
	 * Get resource of the model
	 * @return string
	 */
	protected static function getResourceSlug()
	{
		if (!static::$resource_slug)
			return strtolower(Str::plural(Str::classBasename(get_called_class())));
		else
			return static::$resource_slug;
	}

	public function __set($property, $value)
	{
		return $this->setAttribute($propery, $value);
	}

	public function __get($property)
	{
		return $this->getAttribute($propery);
	}
}