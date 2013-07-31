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
	 * API URL
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Path
	 */
	protected $path;

	public function __construct($url)
	{
		$this->endpoint = $url;
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