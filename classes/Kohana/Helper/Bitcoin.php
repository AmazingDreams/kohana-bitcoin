<?php defined('SYSPATH') or die('No direct script access');

class Kohana_Helper_Bitcoin {

	public static function to_satoshi($value)
	{
		return round($value * 1e8);
	}

	public static function from_satoshi($value)
	{
		return $value / 1e8;
	}

}
