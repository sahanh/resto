<?php
/**
 * Facade for HTTP communications
 */
namespace Resto\Common;

class Request
{
	/**
	 * Return formats
	 */
	const FORMAT_JSON = 'json';

	/**
	 * HTTP methods
	 */
	const METHOD_GET     = 'GET';
	const METHOD_POST    = 'POST';
	const METHOD_PUT     = 'PUT';
	const METHOD_DELETE  = 'DELETE';


	/**
	 * API endpoint
	 * @var string
	 */
	protected $endpoint;

	/**
	 * API response format, ie:- json
	 */
	protected $format;

	/**
	 * HTTP Method for current request, GET/POST/PUT/DELETE
	 * @var string
	 */
	protected $method;

	/**
	 * Path to append to base url
	 * @var string
	 */
	protected $path;

	/**
	 * Query params
	 */
	protected $params = array();

	public function __construct($endpoint)
	{
		$this->endpoint = $endpoint;
	}

	/**
	 * Set path, will be amended with base path
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Set up an extension to request, ie: .json in api.twitter/tweets.json
	 * @param [type] $format [description]
	 */
	public function setPathExt($format)
	{
		$this->format = $format;
		return $this;
	}	

	/**
	 * Set HTTP method for current request
	 * @param string $method
	 */
	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * Add query parameters to current request
	 * @param mixed $key
	 * @param string $value
	 */
	public function addParam($key, $value)
	{
		$this->params[$key] = $value;
		return $this;
	}

	/**
	 * Remove a query parameter
	 * @param  mixed key
	 * @return bool
	 */
	public function removeParam($key)
	{
		if (isset($this->params[$key]))
			unset($this->params[$key]);
		else
			return false;
	}

	/**
	 * Add query parameters from an assoc array
	 * @param Array $params
	 */
	public function addParams(Array $params)
	{
		foreach ($params as $key => $val)
			$this->addParam($key, $val);

		return $this;
	}

	public function execute()
	{

	}

	/**
	 * Build final url for the request. (without query params)
	 * @return string
	 */
	protected function buildUrl()
	{
		return implode('/', array($this->endpoint, $this->path));
	}
}