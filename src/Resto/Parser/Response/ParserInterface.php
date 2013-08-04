<?php
namespace Resto\Parser\Response;

use Guzzle\Http\Message\Response;

interface ParserInterface
{
	/**
	 * Set Guzzle response object
	 * @param object $response
	 */
	public function setResponse(Response $response);

	/**
	 * Get DATA portion from the response
	 * @return array
	 */
	public function getData();

	/**
	 * Get MetaData from response
	 * @return array
	 */
	public function getMeta();

}