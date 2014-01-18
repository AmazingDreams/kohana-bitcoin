<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Bitcoin_RPC_Request_Getinfo extends Bitcoin_RPC_Request {

	protected $_command = 'getinfo';

	protected $_is_cacheable = TRUE;
}
