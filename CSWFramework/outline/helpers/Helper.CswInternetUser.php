<?php

abstract class CswInternetUser
{
	public static function getIp()
	{
        return (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
}

?>
