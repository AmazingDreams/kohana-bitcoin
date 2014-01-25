<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Cache
 *
 * @package    Kohana/Bitcoin
 * @category   Helper
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
class Kohana_Bitcoin_RPC_Cache {

	/**
	 * @var  Array  Array of instances
	 */
	protected static $_instances = array();

	/**
	 * @var  Bitcoin  Reference to the bitcoin client
	 */
	protected $_bitcoin = NULL;

	/**
	 * @var  Mixed  Array if we're using request-based caching
	 *              Cache object if we're using any other caching method
	 */
	protected $_cache = array();

	/**
	 * Get a new instance of this object
	 *
	 * @param   Bitcoin            Reference to the bitcoin client
	 * @return  Bitcoin_RPC_Cache  Object
	 */
	public static function instance($instance)
	{
		$instance_name = $instance->instance_name();

		$self = Arr::get(self::$_instances, $instance_name);

		if($self === NULL)
		{
			$self = new self($instance);

			self::$_instances[$instance_name] = $self;
		}

		return $self;
	}

	/**
	 * Initialize the cache object
	 */
	protected function __construct($instance)
	{
		$this->_bitcoin = $instance;

		// If the cache config is a string we assume we're using a Cache object
		if(is_string($this->_bitcoin->config('cache')))
		{
			$this->_cache = Cache::instance($this->_bitcoin->config('cache'));
		}
	}

	/**
	 * Get a value from the cache
	 *
	 * @param   String  Key to the value
	 * @param   Mixed   Default value to return
	 * @return  Mixed   Value
	 */
	public function get($key, $default = NULL)
	{
		if(is_object($this->_cache))
		{
			return $this->_cache->get($key, $default);
		}

		return Arr::path($this->_cache, $key, $default);
	}

	/**
	 * Set a value in the cache
	 *
	 * @chaineable
	 * @param   String             Key to the value
	 * @param   Mixed              Value
	 * @return  Bitcoin_RPC_Cache  This object
	 */
	public function set($key, $value)
	{
		if(is_object($this->_cache))
		{
			return $this->_cache->set($key, $value, $this->_bitcoin->config('cache_lifetime'));
		}

		Arr::set_path($this->_cache, $key, $value);

		return $this;
	}
}
