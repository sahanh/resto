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

		try {
			$this->setBody($response->json());
		} catch (Exception $e) {
			throw new ParserException($e->getMessage(), $e->getCode());
		}
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
		return $this->data;
	}

	public function getMeta()
	{
		return $this->meta;
	}

	/**
	 * Set converted body, make sure the arraay has all the keys necessary that Query needs
	 * @param  arrays
	 */
	protected function setBody(array $body)
	{
		//check and throw errors
		$this->validateErrorsInBody($body);

		$this->setDataFromBody($body);
		$this->setMetaFromBody($body);
	}

	/**
	 * Check the body using specified "data" key and return that data.
	 * If key doesn't exists, ParserException exception will be thrown
	 * TODO: refactor logic
	 * @param  array $body
	 * @return array
	 */
	protected function setDataFromBody($body)
	{
		//if array is not assoc, means it's collection of models. We can use it directly
		if (!H::arrayIsAssoc($body)) {
			$this->data = array($body);
		}
		//it has more data, better check with keys
		else {

			//response has one key, with one element
			if ($keys = array_keys($body) and count($keys) < 2) {
				$key  = array_shift($keys);
				
				//get the data out of that one element
				if ($data = H::arrayGet($body, $key) and H::arrayIsAssoc($data)) {
					$this->data = array($data);
					return;
				}	
			}
		
			if ($key = $this->getKey('data') and !array_key_exists($this->getKey('data'), $body))
				throw new ParserException("Couldn't find data under '{$key}', key doesn't exists in response.");
			
			$data = H::arrayGet($body, $this->getKey('data'));	

			//need to make sure $this->data is always an array no matter how many elements are there.
			//Resto\Query will handle single element data
			if (!is_array($data)) {
				$this->data = array($data);
			} else {
				$this->data = $data;
			}
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