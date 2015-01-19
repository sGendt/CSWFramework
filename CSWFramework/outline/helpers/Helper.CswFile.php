<?php

abstract class CswFile
{

	protected static $file = null;
	protected static $fileInfos = null;

	public static function setFile($file)
	{
		self::$file = $file;
		self::$fileInfos = pathinfo($file['name']);
	}
	

	public static function getExtension()
	{
		return strtolower(self::$fileInfos['extension']);
	}


	public static function getName()
	{
		return self::$file['name'];
	}


	public static function getFilename()
	{
		return self::$fileInfos['filename'];
	}


	public static function getSize()
	{
		return self::$file['size'];
	}

	public static function getSizes()
	{
		return getImageSize(self::$file['tmp_name']);
	}


	public static function getError()
	{
		return self::$file['error'];
	}


	public static function getTmpFile()
	{
		return self::$file['tmp_name'];
	}


	public static function getType()
	{
		return self::$file['type'];
	}


	public static function isAutorized($extensions = array())
	{
		if(in_array(self::getExtension(), $extensions))
			return true;
		else
			return false;
	}

	public static function formateFiles($_files = array())
	{
	    $files = array();

	    foreach($_files as $key => $values)
	    {
	    	$i = 0;
            foreach($values as $value)
            {
                $files[$i][$key] = $value;
                $i++;
            }
	    }

	    return $files;
	}

	public static function getAndSave($filePath, $savePath, $name = null)
	{
		list($currentName, $extension) = explode('.', self::getName());

		$authorizedImages = CswPref::pref('authorizedImages');

		$file = file_get_contents($filePath);

		if($name == null)
			$name = $currentName;

		if(file_put_contents($savePath . $name . '.' . $extension, $file))
			return $name . '.' . $extension;
		else
			return false;
	}

	public static function thumbnail($filePath, $savePath, $sizes = array(), $tType = THUMBNAILRESIZE, $prefix = PREFIX, $sufix = SUFIX, $force = false)
	{
		$pre = null;
		$suf = null;

		//CDNPRODUCTIMAGE.$this->getEan().'/'
		$name = basename($filePath);
		list($name, $extension) = explode('.', $name);

		if($prefix)
			$pre .= $prefix . '_';

		if($sufix)
			$suf = '_' . $sufix;

		if(file_exists($savePath.$pre.$name.$suf.'.png') && !$force)
			return (!empty($returnPath)) ? $returnPath.$pre.$name.$suf.'.png' : $savePath.$pre.$name.$suf.'.png';

		if(!is_dir($savePath))
			CswDirectory::folder($savePath);

		$gd = new CswGd();

		$gd->setSavePath($savePath);
		$gd->setPrefix($prefix);
		$gd->setSufix($sufix);
		$gd->setType($tType);
		$gd->setExtension(pathinfo($filePath, PATHINFO_EXTENSION));

		$imageSizes = CswPref::pref('imageSizes');

		$gd->setSizes($sizes);
		$gd->resize($filePath, $name);
		$gd->save();

		return $pre.$name.$suf.'.png';
	}
}

?>