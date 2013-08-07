<?php
/**
 * Model, REST Resource entity
 */
namespace Resto\Entity;

use Resto\Common\Str;
use Resto\Common\Helpers as H;
use Resto\Common\Resource;
use Resto\Common\Request;
use Resto\Common\Query;
use Resto\Parser\Response\DefaultParser as DefaultResponseParser;
use Resto\Parser\Request\DefaultParser as DefaultRequestParser;

use Resto\Relations\HasMany;
use Resto\Relations\HasOne;
use Resto\Relations\BelongsTo;

class Model
{
	/**
	 * Collection Path
	 */
	protected $collection_path;

	/**
	 * Unique identifier key
	 * @var string
	 */
	protected static $key = 'id';
	
	/**
	 * Valid attributes for the entity.
	 * If specified, will be filtered before doing the API request
	 * @var [type]
	 */
	protected static $fillable   = array();
		
	protected $attributes = array();


	public static function all()
	{
		return static::query()->get();
	}

	public static function find($id)
	{
		$model = new static;
		$model->id = $id;

		$query = static::query();
		$query->setPath($model->getEntityPath());

		return $query->first();
	}

	/**
	 * Get value of PK
	 * @return mixed
	 */
	public function getKey()
	{
		return H::arrayGet($this->attributes, static::$key);
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
		$model = new static;
		$query = static::getResource()->getQuery();
		$query->setModel(get_called_class());
		$query->setPath($model->getCollectionPath());

		return $query;
	}

	/**
	 * static::query's default path is set to current model's collection
	 * getModelQuery will set the path using current entity
	 * used for entity related tasks, ie:- UPDATE/delete(oid)
	 * @return Query
	 */
	public function getModelQuery()
	{
		$query = static::getResource()->getQuery();
		$query->setModel(get_called_class());
		$query->setPath($this->getEntityPath());
		
		return $query;
	}

	/**
	 * Save or update a model
	 * POST/PUT
	 * @return bool
	 */
	public function save()
	{
		//existing update
		if ($this->getKey()) {

			$query = $this->getModelQuery();
			$query->setMethod(Request::METHOD_PUT);
			$query->addParams($this->attributes);
			$query->execute();

		} else {

			$query = static::query();
			$query->setMethod(Request::METHOD_POST);
			$query->addParams($this->attributes);
			$query->execute();

		}
	}

	/**
	 * Execute a DELETE to current entity path
	 * @return [type] [description]
	 */
	public function delete()
	{
		$this->getModelQuery()->delete();
		return true;
	}

	/**
	 * Fill and make a model
	 * @param  array $data
	 */
	public function fill(array $data)
	{
		foreach ($data as $key => $value)
			$this->setAttribute($key, $value);

		return $this;
	}

	public function fillRaw(array $array)
	{
		foreach($array as $key => $value) {
			$this->attributes[$key] = $value;
		}

		return $this;
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
			$this->$mutator_method($value);
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
		
		if (method_exists($this, $mutator_method)) {
			return $this->$mutator_method($value);
		} else {
			if (array_key_exists($key, $this->attributes))
				return $this->attributes[$key];
		}
			
	}

	/**
	 * Generate mutator method's name to set attribute. Used to check if method exists
	 * @param  string $key method name
	 * @return string
	 */
	protected function generateSetMutatorName($key)
	{
		$key    = Str::studly($key);
		return "set{$key}Attribute";
	}

	/**
	 * Generate mutator method's name to get attribute. Used to check if method exists
	 * @param  string $key method name
	 * @return string
	 */
	protected function generateGetMutatorName($key)
	{
		$key    = Str::studly($key);
		return "get{$key}Attribute";
	}

	/**
	 * Get Collection path for same type of entities. Typically generated by pluralizing class name.
	 * Used when creating new objects. ie:- POST /users
	 */
	public function getCollectionPath()
	{
		if (!$this->collection_path)
			return Str::collectionPath(get_called_class());
		else
			return $this->collection_path;
	}

	/**
	 * Path of an entity instance. If not provided, generated using collection path and current
	 * instance's id. Used to update an object.
	 * PUT/DELETE /tickets/{id}
	 * @return [type] [description]
	 */
	public function getEntityPath()
	{
		$parts   = array();
		$parts[] = $this->getCollectionPath();

		if ($entity_id = $this->{static::$key})
			$parts[] = $entity_id;

		return implode('/', $parts);
	}

	public function hasMany($class, $path = false)
	{
		return $this->generateRelation('HasMany', $class, $path);
	}

	public function hasOne($class, $path = false)
	{
		return $this->generateRelation('HasOne', $class, $path);
	}

	/**
	 * Return response parser
	 * @return Resto\Parser\Request\DefaultResponseParser
	 */
	public static function getResponseParser($response)
	{
		return new DefaultResponseParser($response);
	}

	/**
	 * Return request parser
	 * @return Resto\Parser\Request\DefaultRequestParser
	 */
	public static function getRequestParser(Query $query)
	{
		return new DefaultRequestParser($query);
	}

	/**
	 * Build a relation object, used by hasMany, hasOne, belongsTo helpers.
	 * On initiate, it will append the current namespace to relating class.
	 * @param  string $type  - type of the relation, the class name of Relation, HasOne, HasMany, Belongs to, under Resto\Relation\..
	 * @param  string $class - relating clas name
	 * @param  string $path  - custom query path
	 * @return Resto\Relation
	 */
	protected function generateRelation($type, $class, $path)
	{
		$namespace = static::getResource()->getNamespace();
		$fqclass   = "{$namespace}\\{$class}";

		switch ($type) {
			case 'HasMany':
					return new HasMany($fqclass, $this, $path);
				break;
			
			case 'HasOne':
					return new HasOne($fqclass, $this, $path);
				break;
		}
			
	}

	public function __set($property, $value)
	{
		return $this->setAttribute($property, $value);
	}

	public function __get($property)
	{
		return $this->getAttribute($property);
	}
}