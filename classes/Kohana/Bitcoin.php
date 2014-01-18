<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Bitcoin {

	/**
	 * @var  Array  Holds the instances
	 */
	protected static $_instances = array();

	/**
	 * @var  Array  Holds the instance name
	 */
	protected $_instance_name = 'default';

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
			$bitcoin = new self($conf);

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
		// If we are in the development environment we wish to benchmark this
		if(Kohana::$environment === Kohana::DEVELOPMENT)
		{
			$benchmark = Profiler::start("Kohana-Bitcoin", $method);
		}

		// If there's no result
		$result = $this->_run($method);

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
		$this->_instance_name = $config;

		// Merge the given configuration with the default
		$this->_config = Arr::merge($this->_config, Kohana::$config->load("bitcoin.$config"));
	}

	/**
	 * Get a config key, or the entire array
	 *
	 * @param   String  Key to get
	 * @return  Mixed   Whatever is in there
	 */
	public function config($key = NULL)
	{
		if($key === NULL)
		{
			return $this->_config;
		}

		return Arr::get($this->_config, $key);
	}

	public function cache_enabled()
	{
		return $this->config('cache') !== FALSE;
	}

	public function instance_name()
	{
		return $this->_instance_name;
	}

	/**
	 * Execute an rpc request
	 *
	 * @param   String                Command to run against the service
	 * @return  Bitcoin_RPC_Response  Response object
	 */
	protected function _run($command, $parameters = array())
	{
		// Build the url
		return Bitcoin_RPC_Request::create($command, $this)
			->parameters($parameters)
			->execute();
	}
}
