<?php

abstract class CswArray
{

	public static function arrayCombine($keys, $values)
	{
		$return = array();
		$values = array_values($values);

		$i = 0;

		foreach($keys as $value)
		{
			$return[$value] = (isset($values[$i]) == true) ? $values[$i] : null;
			$i++;
		}

		return $return;
	}

	public static function isAssoc($array)
	{
		return count(array_filter(array_keys($array),'is_string')) == count($array);
	}

}

?>
