<?php

/**
 *
 */

abstract class CswPath
{

	/**
	 *
	 */

	static function url($path)
	{

	}

	/**
	 *
	 */

	static function uri($path)
	{
		return OUTLINE . $path;
	}

	/**
	 *
	 */

	static function getUrlFragments()
	{
		$uri = substr(URI, 1);

		if(empty($uri) == true) 
		{
			$uri = CswPref::pref('defaultPage');
		}
		else
		{
			$lastChar = substr($uri, -1);

			if($lastChar != '/')
			{
				$uri .= '/';
			}
		}

		

		$urlFragments = explode('/', $uri);
		array_pop($urlFragments);
		

		$nbParts = count($urlFragments);
		$lastPart = $nbParts - 1;

		if(empty($urlFragments[$lastPart]) == true)
		{
			unset($urlFragments[$lastPart]);
			$nbParts--;
		}

		$urlFragments['class'] = ucfirst($urlFragments[0]);
		unset($urlFragments[0]);
		$urlFragments['datas'] = $urlFragments;
		unset($urlFragments['datas']['class']);
		$urlFragments['datas'] = array_values($urlFragments['datas']);

		return $urlFragments;
	}

	public static function cdn($path)
	{
		return CswPref::pref('pathCdn') . '/' . $path;
	}

}


?>
