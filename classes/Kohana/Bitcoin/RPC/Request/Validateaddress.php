<?php defined('SYSPATH') or die('No direct script access');

/**
 * Class Kohana Bitcoin RPC Request Validateaddress
 *
 * @package    Kohana/Bitcoin
 * @category   RPC Request
 * @author     Dennis Ruhe
 * @copyright  (c) 2013 Dennis Ruhe
 */
class Kohana_Bitcoin_RPC_Request_Validateaddress extends Bitcoin_RPC_Request {

	/**
	 * This request is NOT cacheable
	 */
	protected $_is_cacheable = FALSE;

	public function execute()
	{
		$result = parent::execute();

		// If no error, return isvalid
		if($result->error == NULL)
			return $result->result->isvalid;

		return $result;
	}

} // End Kohana Bitcoin RPC Request Validateaddress
