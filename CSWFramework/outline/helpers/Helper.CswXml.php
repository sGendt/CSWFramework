<?php

abstract class CswXml
{
	private static $url = null;
	private static $version = '1.0';
	private static $encoding = 'UTF-8';
	private static $xml = null;
	private static $currentNode = null;


	/**
	 *
	 */

	public static function newXml()
	{
		self::$xml = new DOMDocument(self::$version, self::$encoding);
		self::$xml->formatOutput = true;
		self::$currentNode = null;
	}


	/**
	 *
	 */

	public static function create($nodes = array(), $recursive = false, $parentNode = null)
	{
		foreach($nodes as $name => $datas)
		{
			if(is_array($datas))
			{
				$node = self::$xml->createElement($name);

				if(empty($parentNode))
					self::$xml->appendChild($node);
				else
					$parentNode->appendChild($node);
				
				if(isset($datas['attributes']) && is_array($datas['attributes']))
				{
					foreach($datas['attributes'] as $key => $value)
					{
						$attribute = self::$xml->createAttribute($key);
						$attribute->value = $value;
						$node->appendChild($attribute);
					}
				}

				if(isset($datas['nodes']) && is_array($datas['nodes']))
				{
					$isAssoc = CswArray::isAssoc($datas['nodes']);
					if(!$isAssoc)
					{
						foreach($datas['nodes'] as $key => $values)
						{
							if($key)
							{
								$node = self::$xml->createElement($name);
								$parentNode->appendChild($node);
							}

							self::create($values, true, $node);	
						}
					}
					else
						self::create($datas['nodes'], true, $node);
				}
				elseif(isset($datas['nodes']) && !is_array($datas['nodes']))
					$node->appendChild(self::$xml->createTextNode($datas['nodes']));
			}
			else
			{
				$node = self::$xml->createElement($name);
				$node->appendChild(self::$xml->createTextNode($datas));

				if($recursive == false)
					self::$xml->appendChild($node);
				else
					$parentNode->appendChild($node);
			}
		}
	}


	/**
	 *
	 */

	public static function save($url)
	{
		self::$xml->save($url);
	}


	/**
	 *
	 */

	public static function get()
	{
		return self::$xml->saveXML();
	}
}

?>
