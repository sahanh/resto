<?php
namespace Resto\Parser\Response;

use Resto\Common\Request;
use Guzzle\Http\Message\Response;

interface ParserInterface
{
	/**
	 * Set Guzzle response object
	 * @param object $response
	 */
	public function setRequest(Request $request);

	/**
	 * Get DATA portion from the response
	 * array of attributes for model collection
	 * @return array
	 */
	public function getData();

	/**
	 * Get MetaData from response
	 * @return array
	 */
	public function getMeta();

	/**
	 * Execute the request and parse the data
	 * @return mixed
	 */
	public function parse();
}