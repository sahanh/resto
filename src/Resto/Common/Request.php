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
	 * Guzzle client
	 */
	protected $client;

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
	 * URL query params
	 * @var array
	 */
	protected $params = array();

	/**
	 * POST fields
	 */
	protected $post_fields = array();

	/**
	 * Raw Body
	 * @var string
	 */
	protected $body;
 	
 	/**
 	 * Headers
 	 * @var array
 	 */
	protected $headers = array();

	/**
	 * Callbacks
	 */
	protected $callbacks = array(
		'initiateHttpClient' => false,
		'beforeRequest'      => false,
		'afterRequest'       => false
	);


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
	 * Add headers
	 * @param string $key
	 * @param string $value
	 */
	public function addHeader($key, $value)
	{
		$this->headers[$key]  = $value;
		return $this;
	}

	/**
	 * Set raw body
	 * @param  string $body
	 */
	public function setBody($body)
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * Get RAW Body
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
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
	 * Get path ext
	 * @return string|null
	 */
	public function getPathExt()
	{
		return $this->path_ext;
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
	 * Get HTTP Method
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * Get params
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
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
	 * Add query parameters from an assoc array
	 * @param Array $params
	 */
	public function addParams(Array $params)
	{
		foreach ($params as $key => $val)
			$this->addParam($key, $val);

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
	 * Get post fields
	 * @return array
	 */
	public function getPostFields()
	{
		return $this->post_fields;	
	}

	/**
	 * Add post fields to current request
	 * @param mixed $key
	 * @param string $value
	 */
	public function addPostField($key, $value)
	{
		$this->post_fields[$key] = $value;
		return $this;
	}

	/**
	 * Add psot fields from an assoc array
	 * @param Array $params
	 */
	public function addPostFields(Array $params)
	{
		foreach ($params as $key => $val)
			$this->addPostField($key, $val);

		return $this;
	}

	/**
	 * Remove a post field
	 * @param  mixed key
	 * @return bool
	 */
	public function removePostField($key)
	{
		if (isset($this->post_fields[$key]))
			unset($this->post_fields[$key]);
		else
			return false;
	}


	public function execute()
	{
		try {

			$http_method = strtolower($this->method);

			$client  = $this->getClient();

			$this->invokeCallback('initiateHttpClient', array($this, $client));

			$request     = $client->$http_method($this->buildPath());

			//headers
			foreach ((array) $this->headers as $name => $value) {
				$request->setHeader($name, $value);
			}

			//params
			if ($params = $this->getParams())
				$request->getQuery()->merge($params);

			//post fields
			if ($post_fields = $this->getPostFields())
				$request->addPostFields($post_fields);

			//raw body
			if ($body = $this->getBody())
				$request->setBody($body);

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