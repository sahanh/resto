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
		$this->query = $query;
	}

	public function getRequest()
	{
		$query   = $this->query;
		$request = $query->getRequest();
		$request->setPath($query->getPath());
		$request->addParams($query->getParams());
		return $request;
	}
}