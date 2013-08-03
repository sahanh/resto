<?php
/**
 * Collection for Entity\Model s
 */
namespace Resto\Entity;

class Collection
{
	protected $items;

	public function __construct($items, $meta = false)
	{
		$this->items = $items;
	}

	/**
	 * Get the first item from the collection.
	 *
	 * @return mixed|null
	 */
	public function first()
	{
		return count($this->items) > 0 ? reset($this->items) : null;
	}
}