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

	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	public function getPath()
	{
		return $this->path;
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
		$this->params[$field] = $value;
		return $this;
	}

	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Execute query and return data
	 * @return Resto\Entity\Collection
	 */
	public function get()
	{
		$parsed_data = $this->getResponseParser($this->execute());

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
		$request = $this->getRequestParser($this)->getRequest();
		return $request->execute();
	}

	/**
	 * Get parser instance to format the response
	 * Checking model for getResponseParser and fallback to default
	 * @param  Guzzle\Response $response
	 */
	protected function getResponseParser($response)
	{
		$parser = call_user_func_array("{$this->model}::getResponseParser", array($response));
		
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