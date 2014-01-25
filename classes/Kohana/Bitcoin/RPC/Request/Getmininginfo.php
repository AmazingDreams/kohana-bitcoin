<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Request Getmininginfo
 *
 * @package    Kohana/Bitcoin
 * @category   RPC Request
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
class Kohana_Bitcoin_RPC_Request_Getmininginfo extends Bitcoin_RPC_Request {

	/**
	 * This request is cacheable
	 */
	protected $_is_cacheable = TRUE;

} // End Kohana Bitcoin RPC Request Getmininginfo
