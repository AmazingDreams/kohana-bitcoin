<?php defined('SYSPATH') or die('No direct script access');

abstract class Kohana_Bitcoin_RPC_Request extends Request {

	protected $_bitcoin = NULL;

	protected $_command = NULL;

	protected $_is_cacheable = FALSE;

	protected $_parameters = array();

	public static function create($method, $instance)
	{
		$class = 'Bitcoin_RPC_Request_'.ucfirst($method);

		return new $class($instance);
	}

	public function __construct($instance)
	{
		$this->_bitcoin = $instance;

		parent::__construct($this->_get_url());

		$this->headers('Content-Type', 'application/json')
			->method('post');
	}

	public function parameters($parameters = array())
	{
		$this->_parameters = $parameters;

		return $this;
	}

	protected function _get_url()
	{
		$url = 'http://<username>:<password>@<hostname>:<port>/';
		$url = str_replace('<username>', $this->_bitcoin->config('rpcuser'), $url);
		$url = str_replace('<password>', $this->_bitcoin->config('rpcpassword'), $url);
		$url = str_replace('<hostname>', $this->_bitcoin->config('host'), $url);
		$url = str_replace('<port>',     $this->_bitcoin->config('port'), $url);

		return $url;
	}

	public function execute()
	{
		if($this->_bitcoin->cache_enabled())
		{
			$response = Bitcoin_RPC_Cache::instance($this->_bitcoin)->get($this->_method);
		}

		if( ! isset($response) OR $response === NULL)
		{
			$this->body(json_encode(array(
				'method' => $this->_command,
				'params' => $this->_parameters,
				'id'     => 'jsonrpc',
			)));

			$response = json_decode(parent::execute());
		}

		if($this->_bitcoin->cache_enabled() AND $this->_is_cacheable)
		{
			Bitcoin_RPC_Cache::instance($this->_bitcoin)->set($this->_method, $response);
		}

		return $response;
	}
}
