<?php
namespace Resto\Common;

class Str {
	/**
	 * Convert a value to studly caps case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function studly($value)
	{
		$value = ucwords(str_replace(array('-', '_'), ' ', $value));

		return str_replace(' ', '', $value);
	}
	
	public static function plural($value, $count = 2)
	{
		return Pluralizer::plural($value, $count);
	}

	public static function singular($value)
	{
		return Pluralizer::singular($value);
	}

	/**
	 * Generate collection path from a class name
	 * @param  string $class
	 * @return string       
	 */
	public static function collectionPath($class)
	{
		return strtolower(static::plural(static::classBasename($class)));
	}

	/**
	 * Get class base
	 */
	public static function classBasename($class)
	{
		if (is_object($class)) $class = get_class($class);

		return basename(str_replace('\\', '/', $class));
	}

}