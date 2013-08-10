<?php
namespace Resto\Relations;

use Resto\Common\Module as Resource;
use Resto\Common\Str;
use Resto\Entity\Collection;

class HasMany extends Relation
{
	/**
	 * Build URL path using calling and relating model
	 * @return string
	 */
	protected function buildQueryPath()
	{
		//ie:- users/1
		$caller_path    = $this->getCallingModel()->getEntityPath();

		//ie:- posts
		$relatives_path = Str::collectionPath($this->getRelatingModel());

		//ie:- users/1/posts
		return implode('/', array($caller_path, $relatives_path));
	}

	public function getFromModel($attribute)
	{
		return new Collection($this->getInModelData($attribute));
	}
}