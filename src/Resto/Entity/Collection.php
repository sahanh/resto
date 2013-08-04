<?php
/**
 * Collection for Entity\Model s
 */
namespace Resto\Entity;

use Resto\Common\Helpers as H;

class Collection
{
	protected $items;

	protected $meta = array();

	public function __construct($items, $meta = false)
	{
		$this->items = $items;
		$this->meta  = $meta;
	}

	/**
	 * Get meta data, key support dot notation levels
	 * @param  string $key
	 * @return mixed
	 */
	public function getMeta($key = null)
	{
		if (!is_array($this->meta))
			return $this->meta;
		else
			return H::arrayGet($this->meta, $key);	
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