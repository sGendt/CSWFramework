<?php

$root_dir = dirname(__FILE__);

$path = ini_get('include_path');

$sep = ':';

if (substr(php_uname(), 0, 7) == 'Windows')
{
	$sep = ';';
}

ini_set('include_path', $path . $sep . $root_dir . $sep . $root_dir . '/outline/');

?>