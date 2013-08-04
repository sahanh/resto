<?php
namespace Resto\Parser;

use Resto\Common\Helpers as H;
use Exception;
use Resto\Exception\ParserException;
use Resto\Exception\ResponseErrorException;
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
	 * Meta (additional fields) from response body
	 * @var mixed
	 */
	protected $meta;

	/**
	 * Key references
	 */
	protected $keys = array('data' => 'data', 'errors' => 'errors');

	/**
	 * @param Response $response
	 * @param string   $format - format of the respose so we can dedcode accordingly
	 */
	public function __construct(Response $response)
	{
		$this->setResponse($response);
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

	public function setDataKey($name)
	{
		return $this->setKey('data', $name);
	}

	public function setErrorKey($name)
	{
		return $this->setKey('errors', $name);
	}

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
		if (empty($this->data))
			$this->populateBody();

		return $this->data;
	}

	public function getMeta()
	{
		if (empty($this->data))
			$this->populateBody();

		return $this->meta;
	}

	/**
	 * Populate content from body, data will be extracted from set keys and stored in
	 * respective properties
	 * @param  arrays
	 */
	protected function populateBody()
	{
		try {
			$body = $this->response->json();
		} catch (Exception $e) {
			throw new ParserException($e->getMessage(), $e->getCode());
		}

		//check and throw errors
		$this->validateErrorsInBody($body);

		$this->setDataFromBody($body);
		$this->setMetaFromBody($body);
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

		//has more than single key, means it has meta + data
		} elseif (count($keys) > 2) {
			
			//check with the key assoc to find which key has data
			if ($key = $this->getKey('data') and !array_key_exists($this->getKey('data'), $body))
				throw new ParserException("Couldn't find data under '{$key}', key doesn't exists in response.");
			
			$return = H::arrayGet($body, $this->getKey('data'));	
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
	public function setMetaFromBody($body)
	{
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