<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Request Importprivkey
 *
 * @package    Kohana/Bitcoin
 * @category   RPC Request
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
class Kohana_Bitcoin_RPC_Request_Importprivkey extends Bitcoin_RPC_Request {

	/**
	 * This request is NOT cacheable
	 */
	protected $_is_cacheable = FALSE;

} // End Kohana Bitcoin RPC Request Importprivkey
