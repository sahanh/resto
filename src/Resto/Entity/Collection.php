<?php
/**
 * Collection for Entity\Model s
 */
namespace Resto\Entity;

use Resto\Common\Helpers as H;
use Closure;
use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class Collection implements ArrayAccess, Countable, IteratorAggregate
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

	//-----------------------------------------------------------
	// ArrayAccess
	//-----------------------------------------------------------

	public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    //-----------------------------------------------------------
	// Countable
	//-----------------------------------------------------------
	
    public function count()
    {
    	return count($this->items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function toArray()
    {
    	return array_map(function($value)
    	{
    		return is_callable([$value, 'toArray']) ? $value->toArray() : $value;
    	}, $this->items);
    }
}