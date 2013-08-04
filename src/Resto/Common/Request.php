<?php
/**
 * Facade for HTTP communications
 */
namespace Resto\Common;

use Closure;
use Exception;
use Resto\Exception\RequestException;
use Guzzle\Http\Client as HttpClient;

class Request
{

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
	 * HTTP Method for current request, GET/POST/PUT/DELETE
	 * @var string
	 */
	protected $method = 'GET';

	/**
	 * Path to append to base url
	 * @var string
	 */
	protected $path;

	/**
	 * Path ext, will be amended to final path, ie:- api.com/users.json
	 * @var string
	 */
	protected $path_ext;

	/**
	 * Guzzle client
	 */
	protected $client;

	/**
	 * Callbacks
	 */
	protected $callbacks = array(
		'initiateHttpClient' => false,
		'beforeRequest'      => false,
		'afterRequest'       => false
	);

	/**
	 * URL query params
	 */
	protected $params = array();

	public function __construct($endpoint, $options = array())
	{
		$this->endpoint = $endpoint;
		$this->client   = new HttpClient($this->endpoint);
		
		if (isset($options['callbacks'])) {
			$this->setCallbacks($options['callbacks']);
		}
	}
	
	/**
	 * Set path, will be amended with base path
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = ltrim($path, '/');
		return $this;
	}


	/**
	 * Set up an extension to request, ie: .json in api.twitter/tweets.json
	 * @param [type] $format [description]
	 */
	public function setPathExt($path_ext)
	{
		$this->path_ext = '.'.ltrim($path_ext, '.');
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
		try {

			$http_method = strtolower($this->method);

			$client  = $this->getClient();

			$this->invokeCallback('initiateHttpClient', array($this, $client));

			$request     = $client->$http_method($this->buildPath());
			$request->getQuery()->merge($this->params);

			$this->invokeCallback('beforeRequest', array($this, $request));

			$response    = $request->send();

			$this->invokeCallback('afterRequest', array($this, $response));

			return $response;

		} catch (Exception $e) {
			throw new RequestException($e->getMessage(), $e->getCode());
		}
	}

	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Set callbacks from an array
	 * @param array $callbacks
	 */
	public function setCallbacks($callbacks)
	{
		$this->callbacks = $callbacks;
		return $this;
	}


	protected function invokeCallback($method, $params = array())
	{
		if (isset($this->callbacks[$method]) and $this->callbacks[$method] instanceOf Closure) {
			call_user_func_array($this->callbacks[$method], $params);
		} else {
			return false;
		}
	}


	/**
	 * Build final url for the request. (without query params)
	 * @return string
	 */
	protected function buildPath()
	{
		return implode('', array($this->path, $this->path_ext));
	}
}