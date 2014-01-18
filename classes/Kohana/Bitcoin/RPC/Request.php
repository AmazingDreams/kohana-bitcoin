<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Request
 *
 * @package    Kohana-bitcoin
 * @category   Helper
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
abstract class Kohana_Bitcoin_RPC_Request extends Request {

	/**
	 * @var  Bitcoin  Reference to the 'caller' of this request
	 */
	protected $_bitcoin = NULL;

	/**
	 * @var  Boolean  Whether this request is cacheable or not
	 */
	protected $_is_cacheable = FALSE;

	/**
	 * @var  Array  Parameters to give to the actual call
	 */
	protected $_parameters = array();

	/**
	 * Create a new request
	 *
	 * @param   String               Method to call on the server side
	 * @param   Bitcoin              Reference to the caller of this request
	 * @return  Bitcoin_RPC_Request  The request
	 */
	public static function create($method, $instance)
	{
		$class = 'Bitcoin_RPC_Request_'.ucfirst($method);

		return new $class($instance);
	}

	/**
	 * Initialize the object
	 *
	 * @param  Bitcoin  Reference to the caller of this request
	 */
	public function __construct($instance)
	{
		$this->_bitcoin = $instance;

		parent::__construct($this->_get_url());

		$this->headers('Content-Type', 'application/json')
			->method('post');
	}

	/**
	 * Get the command, derived from the filename using reflection
	 *
	 * @return  String  Command
	 */
	private function _command()
	{
		$reflection = new ReflectionClass($this);

		return strtolower(str_replace(EXT, '', basename($reflection->getFileName())));
	}

	/**
	 * Set the parameters to pass to the server
	 *
	 * @chaineable
	 * @param   Array                Parameters to pass
	 * @return  Bitcoin_RPC_Request  This object
	 */
	public function parameters($parameters = array())
	{
		$this->_parameters = $parameters;

		return $this;
	}

	/**
	 * Construct the url where this request must go
	 *
	 * @return  String  Url that should lead to the server
	 */
	protected function _get_url()
	{
		$url = 'http://<username>:<password>@<hostname>:<port>/';
		$url = str_replace('<username>', $this->_bitcoin->config('rpcuser'), $url);
		$url = str_replace('<password>', $this->_bitcoin->config('rpcpassword'), $url);
		$url = str_replace('<hostname>', $this->_bitcoin->config('host'), $url);
		$url = str_replace('<port>',     $this->_bitcoin->config('port'), $url);

		return $url;
	}

	/**
	 * Execute the request
	 *
	 * @return  Bitcoin_RPC_Response  Object
	 */
	public function execute()
	{
		// Check if cache is enabled
		if($this->_bitcoin->cache_enabled())
		{
			// Get the cached value
			$response = Bitcoin_RPC_Cache::instance($this->_bitcoin)->get($this->_method);
		}

		// If there was no response actually execute the request
		if( ! isset($response) OR $response === NULL)
		{
			$this->body(json_encode(array(
				'method' => $this->_command(),
				'params' => $this->_parameters,
				'id'     => 'jsonrpc',
			)));

			$response = json_decode(parent::execute());
		}

		// If cache is enabled and the request is cacheable, cache it
		if($this->_bitcoin->cache_enabled() AND $this->_is_cacheable)
		{
			Bitcoin_RPC_Cache::instance($this->_bitcoin)->set($this->_method, $response);
		}

		return $response;
	}

} // End Kohana Bitcoin RPC Request
