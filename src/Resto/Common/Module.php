<?php
namespace Resto\Common;

use Closure;
use Resto\Exception\InvalidResourceException;
use Resto\Common\Helpers as H;
use Resto\Exception\Exception;

class Module
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
	 * Hold callbacks
	 * @var array
	 */
	protected $callbacks = array();

	/**
	 * Custom class mapping
	 */
	protected $classes = array(
		'Collection'     => 'Resto\\Entity\\Collection',
		'ResponseParser' => 'Resto\\Parser\\Response\\DefaultParser',
		'RequestParser'  => 'Resto\\Parser\\Request\\DefaultParser'
	);

	/**
	 * @param string $namespace
	 */
	protected function __construct($namespace)
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
		if (!static::exists($namespace))
			throw new InvalidResourceException("No registered resource found under for {$namespace}");

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

	public function getEndPoint()
	{
		return $this->endpoint;
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
	 * Get a new Resto\Common\Query instance
	 * Resource class will set base url etc
	 * @return Resto\Common\Query
	 */
	public function getQuery()
	{
		return new Query($this);
	}

	/**
	 * Get a Request object
	 * @return Resto\Common\Request
	 */
	public function getRequest()
	{
		return new Request($this->endpoint, array('callbacks' => $this->callbacks));
	}
	
	/**
	 * Set a callback
	 * @param string  $name
	 * @param Closure $callback
	 */
	public function setCallback($name, Closure $callback)
	{
		$this->callbacks[$name] = $callback;
		return $this;
	}

	/**
	 * Get callback
	 * @param  [type] $name
	 * @return Closure|bool
	 */
	public function getCallback($name)
	{
		return H::arrayGet($this->callbacks, $name, false);
	}

	/**
	 * Register a system wide class.
	 * Custom classes can be assigned for Model Collections,
	 * Parser
	 * @param  string $type
	 * @param  string $fqcn - Fully qualified class name
	 * @return void
	 */
	public function registerClass($type, $class)
	{		
		if ( !array_key_exists($type, $this->classes) )
			throw new Exception("{$type} is not a valid class type to register");

		$this->classes[$type] = $class;
	}

	/**
	 * Get registered class name
	 * @param  string $type
	 * @return void
	 */
	public function getRegisteredClass($type)
	{
		return H::arrayGet($this->classes, $type, false);
	}

	/**
	 * Register a custom response parser for the entire resource.
	 * This will be overridden by model's parser definitions
	 * @param  string $class
	 * @return void
	 */
	public function registerResponseParser($class)
	{
		$this->registerClass('ResponseParser', $class);
	}

	/**
	 * Register a custom request parser for the entire resource.
	 * This will be overridden by model's parser definitions
	 * @param  string $class
	 * @return void
	 */
	public function registerRequestParser($class)
	{
		$this->registerClass('RequestParser', $class);
	}
}