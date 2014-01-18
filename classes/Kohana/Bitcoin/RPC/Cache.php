<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Bitcoin_RPC_Cache {

	protected static $_instances = array();

	protected $_bitcoin = NULL;

	protected $_cache = array();

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

	protected function __construct($instance)
	{
		$this->_bitcoin = $instance;

		if(is_string($this->_bitcoin->config('cache')))
		{
			$this->_cache = Cache::instance($this->_bitcoin->config('cache'));
		}
	}

	public function get($key, $default = NULL)
	{
		if(is_object($this->_cache))
		{
			return $this->_cache->get($key, $default);
		}

		return Arr::path($this->_cache, $key, $default);
	}

	public function set($key, $value)
	{
		if(is_object($this->_cache))
		{
			return $this->_cache->set($key, $value, $this->_bitcoin->config('cache_lifetime'));
		}

		Arr::set_path($this->_cache, $key, $value);
	}
}
