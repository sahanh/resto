<?php
namespace Resto\Parser\Request;

use Resto\Common\Query;

interface ParserInterface 
{
	public function __construct(Query $query);

	public function getRequest();
}