<?php
namespace Resto\Relations;

use Resto\Common\Resource;
use Resto\Common\Str;

class HasOne extends Relation
{
	public function buildQueryPath()
	{
		//ie:- users/1
		$caller_path    = $this->getCallingModel()->getEntityPath();

		//ie:- posts
		$relatives_path = $this->getRelatingModel()->getCollectionPath();
		//since it's one to one, relation path should be singular
		$relatives_path = Str::singular($relatives_path);

		//ie:- users/1/post
		return implode('/', array($caller_path, $relatives_path));
	}
}