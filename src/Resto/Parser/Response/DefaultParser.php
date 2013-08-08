<?php
namespace Resto\Parser\Response;

use Resto\Common\Helpers as H;
use Exception;
use Resto\Exception\ParserException;
use Resto\Exception\ResponseErrorException;
use Resto\Common\Request;
use Guzzle\Http\Message\Response;

class DefaultParser implements ParserInterface
{
	/**
	 * The Guzzle response object
	 * @var object
	 */	
	protected $response;

	/**
	 * Data from response body
	 * @var array|object
	 */
	protected $data;

	/**
	 * Resto request object
	 * @var Resto\Request
	 */
	protected $request;

	/**
	 * Meta (additional fields) from response body
	 * @var mixed
	 */
	protected $meta;

	/**
	 * Key references
	 */
	protected $keys = array('data' => false, 'errors' => 'errors');

	/**
	 * @param Response $response
	 * @param string   $format - format of the respose so we can dedcode accordingly
	 */
	public function __construct(Request $request)
	{
		$this->setRequest($request);
	}

	/**
	 * Response from the request
	 * @param Response $response
	 * @param Response  $format
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;
		return $this;
	}

	/**
	 * Request
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
		return $this;
	}

	/**
	 * Set the JSON key to grab data from response
	 * @param string $name
	 */
	public function setDataKey($name)
	{
		return $this->setKey('data', $name);
	}

	/**
	 * Set json key that represent API based errors in response
	 * @param string $name
	 */
	public function setErrorKey($name)
	{
		return $this->setKey('errors', $name);
	}

	/**
	 * Set json key that represent miscellaneous data in response
	 * @param string $name
	 */
	public function setMetaKey($name)
	{
		return $this->setKey('meta', $name);
	}

	/**
	 * Get data from the response
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get misc data
	 * @return string
	 */
	public function getMeta()
	{
		return $this->meta;
	}

	/**
	 * Execute request, parse response and return data
	 * @return mixed
	 */
	public function parse()
	{
		//we don't need to hit the API everytime parse is called
		if (!$this->response)
			$this->response = $this->request->execute();
		
		return $this->invokeParserMethod();	
	}

	/**
	 * A GET request probably means a data read, we're parsing the response and populate data and meta keys
	 * The caller can call getData and getMeta afterwards.
	 * @param  arrays
	 */
	protected function parseGetResponse()
	{
		$body = $this->decodedBody();

		//check and throw errors
		$this->validateErrorsInBody($body);

		$this->setDataFromBody($body);
		$this->setMetaFromBody($body);

		return true;
	}

	/**
	 * Since POST is an operation we're only validating errors.
	 * HTTP based errors will throw a response exception
	 * @return bool
	 */
	protected function parsePostResponse()
	{
		$this->validateErrorsInBody($this->decodedBody());
		return true;
	}

	/**
	 * Since PUT is an operation we're only validating errors.
	 * HTTP based errors will throw a response exception
	 * @return bool
	 */
	protected function parsePutResponse()
	{
		$this->validateErrorsInBody($this->decodedBody());
		return true;
	}

	/**
	 * Since DELETE is an operation we're only validating errors.
	 * HTTP based errors will throw a response exception
	 * @return bool
	 */
	protected function parseDeleteResponse()
	{
		$this->validateErrorsInBody($this->decodedBody());
		return true;
	}

	/**
	 * Check the body using specified "data" key and return that data.
	 * If key doesn't exists, ParserException exception will be thrown
	 * @param  array $body
	 * @return array
	 */
	protected function setDataFromBody($body)
	{	
		$return = false;
		
		//body only has a single key? prolly entities are grouped under one key
		//ie:- { tweets : [ {}, {}, {}] }
		if (H::arrayIsAssoc($body) and $keys = array_keys($body) and count($keys) < 2) {
			//get stuff under that key
			$key    = array_shift($keys);
			$return = H::arrayGet($body, $key);

		//if user has set a custom data key, check for that
		} elseif ($key = $this->getKey('data')) {
			
			//check with the key assoc to find which key has data
			if (!array_key_exists($this->getKey('data'), $body))
				throw new ParserException("Couldn't find data under '{$key}', key doesn't exists in response.");
			
			$return = H::arrayGet($body, $this->getKey('data'));	
		
		//nothing to do, just take it as it is	
		} else {
			$return = $body;
		}

		//assoc array, means a single entity
		if (H::arrayIsAssoc($return)) {
			$this->data = array($return);
		} else {
			$this->data = $return;
		}
	}

	/**
	 * Get meta data from body using meta key(s)
	 * @param  array $body
	 * @return mixed
	 */
	protected function setMetaFromBody($body)
	{
		if (!$this->getKey('meta'))
			return null;

		if (is_array($this->getKey('meta'))) { //if meta has multiple fields, user is expecting multiple

			$meta = array();

			foreach ($this->getKey('meta') as $key) {
				$meta[$key] = H::arrayGet($body, $key);
			}

			$this->meta = $meta;

		} else {
			$this->meta = H::arrayGet($body, $this->getKey('meta'), false);
		}
	}

	/**
	 * Check if errors exists in body, this is checked against key set for errors
	 * if found, ResponseErrorException will be thrown.
	 * @return void
	 */
	protected function validateErrorsInBody($body)
	{
		if ($errors = H::arrayGet($body, $this->getKey('errors'))) {
			
			if (is_array($errors))
				$errors = array_shift($errors);

			throw new ResponseErrorException($errors);	

		}
	}

	/**
	 * Call the appropriate parser method
	 * @param  string $http_method
	 * @return response
	 */
	protected function invokeParserMethod()
	{
		$http_method = ucfirst(strtolower($this->request->getMethod()));
		$method      = "parse{$http_method}Response";
		
		if (method_exists($this, $method)) {
			return $this->{$method}();
		}
	}

	/**
	 * Get decoded body (from json to array)
	 * @return array
	 */
	protected function decodedBody()
	{
		try {
			return $this->response->json();
		} catch (Exception $e) {
			throw new ParserException($e->getMessage(), $e->getCode());
		}
	}

	protected function setKey($type, $name)
	{
		$this->keys[$type] = $name;
		return $this;
	}

	protected function getKey($type)
	{
		return H::arrayGet($this->keys, $type);
	}
}