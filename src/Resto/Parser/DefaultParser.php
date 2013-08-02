<?php
namespace Resto\Parser;

class DefaultParser implements ParserInterface
{
	/**
	 * The Guzzle response object
	 * @var object
	 */	
	protected $response;


	public function setResponse($response)
	{
		$this->response = $response;
		return $this;
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
}