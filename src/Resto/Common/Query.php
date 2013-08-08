<?php
namespace Resto\Common;

use Resto\Exception\Exception as RestoException;
use Resto\Entity\Collection;
use Resto\Parser\Response\ParserInterface as ResponseParserInterface;
use Resto\Parser\Request\ParserInterface as RequestParserInterface;

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
	 * Collection path
	 */
	protected $model_collection_path;

	/**
	 * Additional query params
	 * @var array
	 */
	protected $params = array();

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

	/**
	 * Set path to query the api
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Get path
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set FQN for models
	 * @param string $model
	 */
	public function	setModel($model)
	{
		$this->model = $model;
		return $this;
	}

	/**
	 * Set custom collection path, this will be applied to
	 * newly created models
	 * @param string $path
	 */
	public function setModelCollectionPath($path)
	{
		$this->model_collection_path = $path;
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
	 * Get request method
	 * @return string
	 */
	public function getMethod()
	{
		return $this->request->getMethod();
	}

	/**
	 * Get Params
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Add a param to request query string
	 * @param  string $field
	 * @param  string $value
	 * @return self
	 */
	public function where($field, $value)
	{
		$this->params[$field] = $value;
		return $this;
	}

	/**
	 * Add params from an array
	 * @param  array $params
	 */
	public function addParams(array $params)
	{
		$this->params = array_merge($this->params, $params);
		return $this;
	}

	/**
	 * Execute query and return data
	 * @return Resto\Entity\Collection
	 */
	public function get()
	{
		$parsed_data = $this->execute();
		$models      = $this->buildModels($parsed_data->getData());
		return new Collection($models, $parsed_data->getMeta());
	}

	public function first()
	{
		return $this->get()->first();
	}

	public function delete()
	{
		$this->setMethod(Request::METHOD_DELETE);
		return $this->execute()->parse();
	}

	/**
	 * Execute current request and return response
	 * @return Guzzle\Response
	 */
	public function execute()
	{
		$this->request->setPath($this->path);

		//parse request
		$request  = $this->getRequestParser($this)->getRequest();

		//parse response
		$response = $this->getResponseParser($request);
		
		//execute the parsed request and populate data
		$response->parse();

		return $response;
	}

	/**
	 * Build model objects from attribute array
	 * @param  array  $data array of attributes
	 * @return Model
	 */
	public function buildModels(array $data)
	{
		$models = array();
		foreach ((array) $data as $model_data) {
			$model = new $this->model;
			$model->fillRaw($model_data);

			if ($this->model_collection_path)
				$model->setCollectionPath($this->model_collection_path);

			$models[] = $model;
		}

		return $models;
	}

	/**
	 * Get parser instance to format the response
	 * Checking model for getResponseParser and fallback to default
	 * @param  Guzzle\Response $response
	 */
	protected function getResponseParser($request)
	{
		$parser = call_user_func_array("{$this->model}::getResponseParser", array($request));
		
		if (!$parser instanceof ResponseParserInterface) {
			throw new RestoException("Not a valid parser, must implement Resto\Parser\Response\ParserInterface");
		}

		return $parser;
	}

	/**
	 * Get parser instance to format the request
	 * Checking model for getRequestParser and fallback to default
	 * @param  Guzzle\Response $response
	 */
	protected function getRequestParser($request)
	{
		$parser = call_user_func_array("{$this->model}::getRequestParser", array($request));

		if (!$parser instanceof RequestParserInterface) {
			throw new RestoException("Not a valid parser, must implement Resto\Parser\Request\ParserInterface");
		}

		return $parser;
	}
}