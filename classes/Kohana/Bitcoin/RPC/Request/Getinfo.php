<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Request Getinfo
 *
 * @package    Kohana-bitcoin
 * @category   Helper
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
class Kohana_Bitcoin_RPC_Request_Getinfo extends Bitcoin_RPC_Request {

	/**
	 * This request is cacheable
	 */
	protected $_is_cacheable = TRUE;

} // End Kohana Bitcoin RPC Request Getinfo
