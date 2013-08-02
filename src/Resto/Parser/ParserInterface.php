<?php
namespace Resto\Parser;

interface ParserInterface
{
	/**
	 * Set Guzzle response object
	 * @param object $response
	 */
	public function setResponse($response);

	/**
	 * Get DATA portion from the response
	 * @return array
	 */
	public function getData();

	/**
	 * Get errors from response
	 * @return array
	 */
	public function getErrors();

	/**
	 * Get MetaData from response
	 * @return array
	 */
	public function getMeta();

}