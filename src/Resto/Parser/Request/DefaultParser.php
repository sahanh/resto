<?php
/**
 * Default request parser will configure the request to send params as either query string or post field.
 * (depends on the HTTP method)
 */
namespace Resto\Parser\Request;

use Resto\Common\Query;

class DefaultParser implements ParserInterface
{
	protected $query;

	public function __construct(Query $query)
	{
		$this->query   = $query;
		$this->request = $query->getRequest(); 
	}

	/**
	 * Set query params as query string
	 * Parser will call this method on GET requests
	 */
	protected function setGetData()
	{
		$this->request->addParams($this->query->getParams());
	}

	/**
	 * Set query params as form fields of the request
	 * Parser will call this method on POST requests
	 */
	protected function setPostData()
	{
		$this->request->addPostFields($this->query->getParams());
	}

	/**
	 * Parser will call this on DELETE requests
	 */
	protected function setDeleteData()
	{

	}

	/**
	 * Set query params as form fields of the request
	 * Parser will call this method on PUT requests
	 */
	protected function setPutData()
	{
		$this->request->addPostFields($this->query->getParams());
	}

	protected function callDataFormatter($http_method)
	{
		$http_method = ucfirst(strtolower($http_method));
		$method      = "set{$http_method}Data";
		
		if (method_exists($this, $method)) {
			$this->{$method}();
		}
	}

	public function getRequest()
	{
		$this->callDataFormatter($this->request->getMethod());

		$this->request->setPath($this->query->getPath());
		return $this->request;
	}
}