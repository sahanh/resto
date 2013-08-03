<?php
namespace Resto\Parser;

use Resto\Common\Helpers as H;
use Resto\Exception\ParserException as Exception;
use Guzzle\Http\Message\Response;

class DefaultParser implements ParserInterface
{
	/**
	 * The Guzzle response object
	 * @var object
	 */	
	protected $response;

	/**
	 * Converted body, running a single json decode on response object and store it
	 * so we don't have to do it over and over
	 * @var array
	 */
	protected $body;

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

		$body = json_decode($response->getBody(true));
		$this->setBody($body);
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

	public function getData()
	{

	}


	public function getErrors()
	{

	}


	public function getMeta()
	{

	}

	/**
	 * Set converted body, make sure the arraay has all the keys necessary that Query needs
	 * @param  arrays
	 */
	protected function setBody(array $body)
	{
		$this->body = $body;
		return $this;
	}

	protected function getDataFromBody($key)
	{
		return H::arrayGet($this->body, $key, false);
	}

	protected function setKey($type, $name)
	{
		$this->keys[$type] = $name;
		return $this;
	}
}