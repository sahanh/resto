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
	 * Execute query and return data
	 * @return Resto\Entity\Collection
	 */
	public function get()
	{
		$parsed_data = $this->execute();

		$models = array();
		foreach ($parsed_data->getData() as $model_data) {
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

	protected function execute()
	{
		$request = $this->request;
		$request->setMethod(Request::METHOD_GET);
		$request->setPath($this->path);

		return $this->getModelParser($request->execute());
	}

	protected function getModelParser($response)
	{
		return call_user_func_array("{$this->model}::getParser", array($response));
	}
}