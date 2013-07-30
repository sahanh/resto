<?php
/**
 * Facade for HTTP communications
 */
namespace Resto\Common;

class Request
{
	const FORMAT_JSON = 'json';

	/**
	 * API endpoint
	 * @var string
	 */
	protected $base;

	/**
	 * API response format, ie:- json
	 */
	protected $format;
}