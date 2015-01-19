<?php

/**
 *
 */

abstract class CswVar
{
	private static $vars = array();

	/**
	 *
	 */

	static function v($name, $value = null, $conditions = array())
	{
		$names = explode('::', $name);
		$length = count($names);

		$name = $names[$length - 1];
		$prefix = ($length == 2) ? $names[0] : null;

		if($value != null)
		{
			self::insertVar($name, $value, $prefix, $conditions);
		}
		else
		{
			return self::selectVar($name, $prefix);
		}

	}

	/**
	 *
	 */

	private static function insertVar($name, $value, $prefix, $conditions = array())
	{
		if($prefix == SESSION)
		{
			$_SESSION[$name] = $value;
		}

		elseif($prefix == COOKIE)
		{
			self::cookie($name, $value, $conditions);
		}

		elseif($prefix == POST)
		{
			$_POST[$name] = $value;
		}

		elseif($prefix == GET)
		{
			$_GET[$name] = $value;
		}

		elseif($prefix == null || $prefix == VARS)
		{
			self::$vars = array_merge
			(
				self::$vars,
				array
				(
					$name => $value
				)
			);
		}
	}

	/**
	 *
	 */

	private static function selectVar($name, $prefix)
	{	
		$return = null;

		if($prefix == SESSION)
		{
			$return = $_SESSION[$name];
		}

		elseif($prefix == COOKIE)
		{
			$return = $_COOKIE[$name];
		}

		elseif($prefix == POST)
		{
			$return = $_POST[$name];
		}

		elseif($prefix == GET)
		{
			$return = $_GET[$name];
		}

		elseif($prefix == VARS)
		{
			$return = self::$vars[$name];
		}
		else
		{
			if(!empty(self::$vars[$name]))
			{
				$return = self::$vars[$name];
			}

			if(!empty($_COOKIE[$name]))
			{
				$return = $_COOKIE[$name];
			}

			if(!empty($_SESSION[$name]))
			{
				$return = $_SESSION[$name];
			}

			if(!empty($_FILES[$name]))
			{
				$return = $_FILES[$name];
			}

			if(!empty($_GET[$name]))
			{
				$return = $_GET[$name];
			}

			if(!empty($_POST[$name]))
			{
				$return = $_POST[$name];
			}
		}

		

		//$return = self::sanitize($return);

		return $return;
	}

	/**
	 *
	 */

	private static function sanitize($value)
	{
		if(is_array($value) == true)
		{
			foreach($value as $key => $val)
			{
				$return[$key] = self::sanitize($val);
			}
		}
		else
		{
			$return = stripslashes(htmlentities($value));
		}

		return $return;
	}

	/**
	 *
	 */

	private static function cookie($name, $value, $conditions = array())
	{
		$defaultConditions = array
		(
			'name' => $name,
			'value' => $value,
			'expire' => 0,
			'path' => '/',
			'domain' => DOMAIN_NAME,
			'secure' => false,
			'httponly' => true
		);

		$conditions = array_merge($defaultConditions, $conditions);

		setcookie
		(
			$conditions['name'],
			$conditions['value'],
			$conditions['expire'],
			$conditions['path'],
			$conditions['domain'],
			$conditions['secure'],
			$conditions['httponly']
		);
	}

}

?>
