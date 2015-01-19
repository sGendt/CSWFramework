<?php

function cookie($name, $value, $conditions = array())
{
	$defaultConditions = array
	(
		'expire' => 0,
		'path' => '/',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => false,
		'httponly' => true
	);

	$conditions = array_merge($defaultConditions, $conditions);

	setcookie
	(
		$name,
		$value,
		$conditions['expire'],
		$conditions['path'],
		$conditions['domain'],
		$conditions['secure'],
		$conditions['httponly']
	);
}

function removeCookie($name)
{
	$conditions = array
	(
		'expire' => time() - 3600,
		'path' => '/',
		'domain' => $_SERVER['HTTP_HOST'],
		'secure' => false,
		'httponly' => true
	);

	setcookie
	(
		$name,
		'',
		$conditions['expire'],
		$conditions['path'],
		$conditions['domain'],
		$conditions['secure'],
		$conditions['httponly']
	);
}

?>
