<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Bitcoin {

	/**
	 * @var  Array  Holds the instances
	 */
	protected static $_instances = array();

	/**
	 * @var  Array  Holds the available rpc methods and information about them
	 */
	protected $_rpcmethods = array(
		'getbalance' => array(
			'cacheable' => TRUE,
		),
		'getinfo' => array(
			'cacheable' => TRUE,
		),
		'getmininginfo' => array(
			'cacheable' => TRUE,
		),
		'help' => array(
			'cacheable' => TRUE,
		),
	);

	/**
	 * @var  Array  Configurations and their default values, these will be overwritten by whatever is in config/bitcoin
	 */
	protected $_config = array(
		'host'           => 'localhost',
		'port'           => '8332',
		'rpcuser'        => 'bitcoin',
		'rpcpassword'    => 'password',
		'cache'          => FALSE,
		'cache_lifetime' => 300,
	);

	/**
	 * Get an instance of Bitcoin
	 *
	 * @param   Instance to get - represents an entry in config/bitcoin
	 * @return  Bitcoin
	 */
	public static function instance($conf = 'default')
	{
		$bitcoin = Arr::get(self::$_instances, $conf);

		if($bitcoin === NULL)
		{
			$bitcoin = new self(Kohana::$config->load("bitcoin.$conf"));

			self::$_instances[$conf] = $bitcoin;
		}

		return $bitcoin;
	}

	/**
	 * Call an RPC method, this was made so that we don't have to define every RPC method seperately
	 *
	 * @throws  Bitcoin_Invalid_Method_Exception
	 *
	 * @param   String                Method to call
	 * @param   Array                 Arguments for that method
	 * @return  Bitcoin_RPC_Response  Response object
	 */
	public function __call($method, $args)
	{
		// Check if a valid method was requested
		if( ! array_key_exists($method, $this->_rpcmethods))
		{
			throw new Bitcoin_Invalid_Method_Exception('Invalid method :method called', array(
				':method' => $method,
			));
		}

		// If we are in the development environment we wish to benchmark this
		if(Kohana::$environment === Kohana::DEVELOPMENT)
		{
			$benchmark = Profiler::start("Kohana-Bitcoin", $method);
		}

		// If caching is enabled
		if($this->_config('cache'))
		{
			$result = $this->_method_cache($method);
		}

		// If there's no result
		if($result === FALSE)
		{
			$result = $this->_run($method);

			// Set the method cache
			// There's a check if it should be set in the _method_cache
			$this->_method_cache($method, $result);
		}

		// Stop the benchmark if it was made
		if(isset($benchmark))
		{
			Profiler::stop($benchmark);
		}

		return $result;
	}

	/**
	 * Create a new instance of this class
	 *
	 * @param  Array  Configuration from config/bitcoin
	 */
	protected function __construct($config)
	{
		// Merge the given configuration with the default
		$this->_config = Arr::merge($this->_config, $config);
	}

	/**
	 * Get a config key, or the entire array
	 *
	 * @param   String  Key to get
	 * @return  Mixed   Whatever is in there
	 */
	protected function _config($key = NULL)
	{
		if($key === NULL)
		{
			return $this->_config;
		}

		return Arr::get($this->_config, $key);
	}

	/**
	 * Get or set the method cache
	 *
	 * @param   String  Method to get/set cache
	 * @param   Mixed   Whatever the value is
	 * @return  Mixed   Whatever the cached value is, or FALSE if no there was no chache
	 */
	protected function _method_cache($method, $value = NULL)
	{
		if( ! Arr::path($this->_rpcmethods, "$method.cacheable"))
		{
			return FALSE;
		}

		// Cache for this request
		if($this->_config('cache') === TRUE)
		{
			if($value !== NULL)
			{
				return Arr::set_path($this->_rpcmethods, "$method.cache", $value);
			}

			return Arr::path($this->_rpcmethods, "$method.cache", FALSE);
		}

		// Use given cache object
		if($value !== NULL)
		{
			return Cache::instance($this->_config('cache'))->set($method, $value, $this->_config('cache_lifetime'));
		}

		return Cache::instance($this->_config('cache'))->get($method);
	}

	/**
	 * Execute an rpc request
	 *
	 * @param   String                Command to run against the service
	 * @return  Bitcoin_RPC_Response  Response object
	 */
	protected function _run($command)
	{
		// Build the url
		$url = 'http://<username>:<password>@<hostname>:<port>/';
		$url = str_replace('<username>', $this->_config('rpcuser'), $url);
		$url = str_replace('<password>', $this->_config('rpcpassword'), $url);
		$url = str_replace('<hostname>', $this->_config('host'), $url);
		$url = str_replace('<port>',     $this->_config('port'), $url);

		// Build the request
		$request = Request::factory($url)
			->headers('Content-Type', 'application/json')
			->method('post')
			->body(json_encode(array(
				'method' => $command,
				'params' => array(),
				'id'     => 'jsonrpc',
			)));

		// Execute the request
		$result = $request->execute();

		return json_decode($result);
	}
}
