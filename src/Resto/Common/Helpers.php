<?php
namespace Resto\Common;

class Helpers
{
	public static function arrayGet($array, $key, $default = null)
	{
		if (is_null($key)) return $array;

		// To retrieve the array item using dot syntax, we'll iterate through
		// each segment in the key and look for that value. If it exists, we
		// will return it, otherwise we will set the depth of the array and
		// look for the next segment.
		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) or ! array_key_exists($segment, $array))
			{
				return static::value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}

	/**
	 * Set an array item to a given value using "dot" notation.
	 *
	 * If no key is given to the method, the entire array will be replaced.
	 *
	 * <code>
	 *		// Set the $array['user']['name'] value on the array
	 *		array_set($array, 'user.name', 'Taylor');
	 *
	 *		// Set the $array['user']['name']['first'] value on the array
	 *		array_set($array, 'user.name.first', 'Michael');
	 * </code>
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function arraySet(&$array, $key, $value)
	{
		if (is_null($key)) return $array = $value;

		$keys = explode('.', $key);

		// This loop allows us to dig down into the array to a dynamic depth by
		// setting the array value for each level that we dig into. Once there
		// is one key left, we can fall out of the loop and set the value as
		// we should be at the proper depth.
		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			// If the key doesn't exist at this depth, we will just create an
			// empty array to hold the next value, allowing us to create the
			// arrays to hold the final value.
			if ( ! isset($array[$key]) or ! is_array($array[$key]))
			{
				$array[$key] = array();
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;
	}

	/**
	 * Remove an array item from a given array using "dot" notation.
	 *
	 * <code>
	 *		// Remove the $array['user']['name'] item from the array
	 *		array_forget($array, 'user.name');
	 *
	 *		// Remove the $array['user']['name']['first'] item from the array
	 *		array_forget($array, 'user.name.first');
	 * </code>
	 *
	 * @param  array   $array
	 * @param  string  $key
	 * @return void
	 */
	public static function arrayForget(&$array, $key)
	{
		$keys = explode('.', $key);

		// This loop functions very similarly to the loop in the "set" method.
		// We will iterate over the keys, setting the array value to the new
		// depth at each iteration. Once there is only one key left, we will
		// be at the proper depth in the array.
		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			// Since this method is supposed to remove a value from the array,
			// if a value higher up in the chain doesn't exist, there is no
			// need to keep digging into the array, since it is impossible
			// for the final value to even exist.
			if ( ! isset($array[$key]) or ! is_array($array[$key]))
			{
				return;
			}

			$array =& $array[$key];
		}

		unset($array[array_shift($keys)]);
	}

	public static function value($value)
	{
		return (is_callable($value) and ! is_string($value)) ? call_user_func($value) : $value;
	}

	public static function arrayIsAssoc($arr)
	{
		 return !empty($arr) and array_keys($arr) !== range(0, count($arr) - 1);
	}

	public static function arrayIsMulti($array)
	{
		return !(count($array) == count($array, COUNT_RECURSIVE));	
	}
}