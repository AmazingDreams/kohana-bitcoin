<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Bitcoin {

	protected static $_instances = array();

	protected $_config = array();

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

	protected function __construct($config)
	{
		$this->_config = $config;
	}

	protected function _config($key = NULL)
	{
		if($key === NULL)
		{
			return $this->_config;
		}

		return Arr::get($this->_config, $key);
	}

	public function getinfo()
	{
		return $this->_run('getinfo');
	}

	protected function _run($command)
	{
		$url = 'http://<username>:<password>@<hostname>:<port>/';
		$url = str_replace('<username>', $this->_config('rpcuser'), $url);
		$url = str_replace('<password>', $this->_config('rpcpassword'), $url);
		$url = str_replace('<hostname>', $this->_config('host'), $url);
		$url = str_replace('<port>',     $this->_config('port'), $url);

		$request = Request::factory($url)
			->headers('Content-Type', 'application/json')
			->method('post')
			->body(json_encode(array(
				'method' => $command,
				'params' => array(),
				'id'     => 'jsonrpc',
			)));

		$result = $request->execute();

		return json_decode($result);
	}
}
