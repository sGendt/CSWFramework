<?php

/**
 *
 */
abstract class CswClassLoader 
{
	/**
	 *
	 */

	static $directory = array
	(
		'class' => 'Class',
		'configuration' => 'Config',
		'helpers' => 'Helper',
		'models' => 'Model',
		'views' => 'Page'

	);

	/**
	 *
	 */

    static function load($className)
    {
    	$load = false;

    	foreach(self::$directory as $folder => $prefix)
    	{
    		$classPath =  $folder . '/' . $prefix . '.' . $className . '.php';

    		if(file_exists('../outline/'.$classPath))
	    	{
	        	require_once $classPath;

	        	$load = true;
	    	}
    	}
    }
}

//

spl_autoload_register(array('CswClassLoader', 'load'));

?>