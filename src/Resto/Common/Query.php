<?php
namespace Resto\Common;

use Resto\Entity\Collection;

class Query
{
	/**
	 * Target model name, this model class will be used to build results
	 * @var string
	 */
	protected $model;

	/**
	 * Resource
	 * @var string
	 */
	protected $resource;

	/**
	 * Request
	 * @var string
	 */
	protected $request;

	/**
	 * Path
	 */
	protected $path;

	/**
	 * @param Resource $resource
	 */
	public function __construct($resource)
	{
		$this->resource = $resource;
		$this->request  = $resource->getRequest();
	}

	/**
	 * Get request object for this query
	 * @return Resto\Common\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function	setModel($model)
	{
		$this->model = $model;
		return $this;
	}

	/**
	 * Set the HTTP method for current query
	 * Request::METHOD_* constants can be used
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->request->setMethod($method);
		return $this;
	}

	/**
	 * Add a param to request query string
	 * @param  string $field
	 * @param  string $value
	 * @return self
	 */
	public function where($field, $value)
	{
		$this->request->addParam($field, $value);
		return $this;
	}

	/**
	 * Execute query and return data
	 * @return Resto\Entity\Collection
	 */
	public function get()
	{
		$parsed_data = $this->getModelParser($this->execute());

		$models = array();
		foreach ((array) $parsed_data->getData() as $model_data) {
			$model = new $this->model;
			$model->fillRaw($model_data);
			$models[] = $model;
		}

		return new Collection($models, $parsed_data->getMeta());
	}

	public function first()
	{
		return $this->get()->first();
	}

	/**
	 * Execute current request and return response
	 * @return Guzzle\Response
	 */
	protected function execute()
	{
		$request = $this->request;
		$request->setPath($this->path);
		return $request->execute();
	}

	protected function getModelParser($response)
	{
		return call_user_func_array("{$this->model}::getParser", array($response));
	}
}