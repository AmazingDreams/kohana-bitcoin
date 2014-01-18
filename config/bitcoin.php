<?php defined('SYSPATH') or die('No direct script access');

return array(
	'default' => array(
		'host'           => 'localhost',
		'port'           => '8332',
		'rpcuser'        => 'bitcoin',
		'rpcpassword'    => 'password',

		/**
		 * Possible values:
		 * FALSE     =>  Do not cache at all
		 * TRUE      =>  Cache during request
		 * <string>  =>  Any string that describes an entry in config/cache.php
		 */
		'cache'          => FALSE,
		'cache_lifetime' => 300,
	),
);
