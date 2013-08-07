<?php
namespace Resto\Relations;

use Resto\Common\Helpers as H;

abstract class Relation
{
	/**
	 * Query object
	 * @var [type]
	 */
	protected $query;

	/**
	 * If query path for relation is set manually
	 * @var string
	 */
	protected $custom_query_path;

	/**
	 * Related model
	 * @var string
	 */
	protected $relating_model;

	/**
	 * Calling model
	 * @var object
	 */
	protected $calling_model;

	/**
	 * [__construct description]
	 * @param string $relating_model Related model name
	 * @param object $calling_model Model that initiating relation
	 */
	public function __construct($relating_model, $calling_model, $path = null)
	{
		$this->setRelatingModel($relating_model);
		$this->setCallingModel($calling_model);

		if ($path)
			$this->setQueryPath($path);

		$query = $calling_model->getResource()->getQuery();
		$query->setModel($this->getRelatingModel());
		$query->setPath($this->getQueryPath());

		$this->query = $query;
	}

	/**
	 * Set relating model
	 * @param string $model
	 */
	public function setRelatingModel($model)
	{
		$this->relating_model = $model;
		return $this;
	}

	public function getRelatingModel()
	{
		return $this->relating_model;
	}

	/**
	 * Set model that initiating the relation
	 * @param object $model
	 */
	public function setCallingModel($model)
	{
		$this->calling_model = $model;
		return $this;
	}

	public function getCallingModel()
	{
		return $this->calling_model;
	}



	/**
	 * Set custom URL path for HTTP request
	 * @param string $path
	 * @return self
	 */
	public function setQueryPath($path)
	{
		$this->custom_query_path = $path;
		return $this;
	}

	/**
	 * Get URL path for current relationship request.
	 * if query_path is not set (::setQueryPath), ::buldQueryPath will be used to genarate
	 * @return string
	 */
	public function getQueryPath()
	{
		if ($this->custom_query_path)
			return $this->custom_query_path;
		else
			return $this->buildQueryPath();
	}

	/**
	 * Build relationship models using in model data
	 * @param  string $key attribute name
	 * @return array
	 */
	protected function getInModelData($key)
	{
		$model = $this->getCallingModel();

		if ($model->attributeExists($key)) {

			$data = $model->getRawAttribute($key);

			if (H::arrayIsAssoc($data))
				$data = array($data);

			return $this->query->buildModels($data);

		}

		return false;
	}

	public function getFromModel($attribute)
	{
		//implemented in child
	}

	/**
	 * Build URL path using calling and relating model
	 * @return string
	 */
	protected function buildQueryPath()
	{
		
	}

	public function __call($method, $args)
	{
		return call_user_func_array(array($this->query, $method), $args);
	}
}