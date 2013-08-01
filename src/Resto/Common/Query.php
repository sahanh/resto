<?php
namespace Resto\Common;

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
	 * Path
	 */
	protected $path;

	/**
	 * @param Resource $resource
	 */
	public function __construct($resource)
	{
		$this->resource = $resource;
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
		//get results
		//loop and created model
		return array();
	}

	public function first()
	{
		//get array pop
	}
}