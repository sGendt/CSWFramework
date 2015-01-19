<?php
/**
 * CswPref is a class that allows to set and get preferences of an application.
 * The context is defined by the application Core.CswApplication.php.
 *
 * @package		Csw Framework
 * @category	Helper
 */

abstract class CswPref
{
	private static $preferences = array();

	/**
	 * Define a new preference or return the value, according to the context (local, debug, prod).
	 *
	 * @param 	string 	$name 		the name of preference
	 * @param 	string 	$value 		the value of the preference
	 * @return  string 	$return 	the value of the preference
	 */

	public static function pref($name, $value = null)
	{
		if(!empty($value))
		{
			self::$preferences[$name] = $value;
		}
		
		$return = (isset(self::$preferences[$name])) ? self::$preferences[$name] : '';

		return $return;
	}


	/**
	 * Return all the preferences of the context (local, debug, prod).
	 *
	 * @return  array 	$return 	All the preferences
	 */

	public static function allPref()
	{
		$return = self::$preferences;

		return $return;
	}
}

?>
