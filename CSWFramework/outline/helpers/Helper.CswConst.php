<?php

/**
 *
 */

abstract class CswConst
{
	/**
	 * Define a new constant or return the value
	 * @param 	string 	$name 		the name of constant
	 * @param 	string 	$value 		the value of the constant
	 * @return  string 	$return 	the value of the constant
	 */

	static function c($name, $value = null)
	{
		if(!empty($value))
		{
			define(strtoupper($name), $value);
		}
		
		$return = constant(strtoupper($name));

		return $return;
	}
	
}

?>
