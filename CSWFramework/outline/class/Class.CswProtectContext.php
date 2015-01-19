<?php

/**
 * CswProtectContext is a class that used to create a context for class with static methods.
 *
 * @package		Csw Framework
 * @category	Class
 */

class CswProtectContext
{
	protected static $instance = null;


	/**
	 * Singleton to protect the context
	 */

	public static function getInstance() 
	{
		if(is_null(self::$instance)) 
		{
			$child = get_called_class();
			self::$instance = new $child();
		}

		$return = self::$instance;

		return $return;
	}
}

?>
