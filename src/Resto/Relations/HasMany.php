<?php
namespace Resto\Relations;

use Resto\Common\Module as Resource;
use Resto\Common\Str;
use Resto\Entity\Collection;
use Resto\Entity\Model;

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
		$related_model  = $this->getRelatingModel();
		$related_model  = new $related_model;
		$relatives_path = $related_model->getCollectionPath();

		//ie:- users/1/posts
		return implode('/', array($caller_path, $relatives_path));
	}

	public function getFromModel($attribute)
	{
		return new Collection($this->getInModelData($attribute));
	}

	/**
	 * Insert a related model
	 * @return [type] [description]
	 */
	public function insert(Model $model)
	{
		$model->setCollectionPath($this->buildQueryPath());
		return $model->save();
	}
}