<?php

function returnJsonToJquery()
{
	global $json, $callback;

	if ($callback) 
	    echo $callback . "($json);";
	else 
	    echo $json;

	exit();
}

?>
