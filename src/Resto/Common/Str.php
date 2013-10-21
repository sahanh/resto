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
	 * Convert a string to snake case.
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	public static function snake($value, $delimiter = '_')
	{
		$replace = '$1'.$delimiter.'$2';

		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
	}

	/**
	 * Convert a value to camel case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function camel($value)
	{
		return lcfirst(static::studly($value));
	}
	
	/**
	 * Generate collection path from a class name
	 * @param  string $class
	 * @return string       
	 */
	public static function collectionPath($class)
	{
		$class = static::classBasename($class);
		$class = static::snake($class);
		$class = static::plural($class);
		
		return strtolower($class);
	}

	/**
	 * Get class base
	 */
	public static function classBasename($class)
	{
		if (is_object($class)) $class = get_class($class);

		return basename(str_replace('\\', '/', $class));
	}

	/**
	 * Get namespace from a FQN
	 * @param  string $class
	 * @return bool|
	 */
	public static function classNamespace($class)
	{
		if (strpos($class, '\\') === false)
			return false;

		$actual_class = Str::classBasename($class);
		return str_replace("\\{$actual_class}", '', $class);
	}

}