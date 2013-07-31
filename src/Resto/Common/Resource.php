<?php
namespace Resto\Common;

use Resto\Exception\InvalidResourceException;

class Resource
{
	/**
	 * Resource container
	 * @var array
	 */
	protected static $resources = array();

	/**
	 * API Endpoint
	 * @var [type]
	 */
	protected $endpoint = null;

	/**
	 * Holds namespace of the Resource instance
	 * @var [type]
	 */
	protected $namespace;

	/**
	 * @param string $namespace
	 */
	public function __construct($namespace)
	{
		$this->namespace = $namespace;
	}
	
	/**
	 * Register a new resources resolver
	 * @param  string $namespace
	 * @return self
	 */
	public static function register($namespace)
	{
		static::$resources[$namespace] = new static($namespace);
		return static::$resources[$namespace];
	}

	/**
	 * Resolve a registerd resource
	 * @param  string $namespace
	 * @return bool|self
	 */
	public static function resolve($namespace)
	{
		$namespace = static::extractNamespaceFromClass($namespace);

		if (!$namespace)
			throw new InvalidResourceException('Model must be inside a namespace.');

		if (!static::exists($namespace))
			throw new InvalidResourceException("No registered resource found. Use Resto\\Common\\Resource::register('{$namespace}')");

		return static::$resources[$namespace];
	}

	/**
	 * Check if resource exists for a given namespace
	 * @param  string $namespace
	 * @return bool
	 */
	public static function exists($namespace)
	{
		return isset(static::$resources[$namespace]);
	}

	/**
	 * Set API Endpoint URL
	 * @param [type] $url [description]
	 */
	public function setEndPoint($url)
	{
		$this->endpoint = rtrim($url, '/');
		return $this;
	}

	/**
	 * Get namespace of resource instance
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * Extract the namespace out of fully qualified class name
	 * @param  string $class
	 * @return mixed
	 */
	protected static function extractNamespaceFromClass($class)
	{
		if (strpos($class, '\\') === false)
			return false;

		$actual_class = Str::classBasename($class);
		return str_replace("\\{$actual_class}", '', $class);
	}
}