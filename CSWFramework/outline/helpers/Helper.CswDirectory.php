<?php

abstract class CswDirectory
{
	static function folder($folder, $action = 'create', $destination = false)
	{
		if($action == 'create') self::createFolder($folder);
		if($action == 'delete') self::deleteFolder($folder);
		if($action == 'copy') self::copyFolder($folder, $destination);
	}

	static function deleteFolder($folder)
	{
		if(is_file($folder)) unlink($folder);
		else
		{
			if(file_exists($folder))
			{
				$contents = scandir($folder);

				foreach($contents AS $file)
				{
					$path = $folder . '/' . $file;
					if($file != '..' && $file != '.')
					{
						if(is_file($path)) unlink($path);
						else self::deleteFolder($path);
					}
				}
			}
		}

		if(file_exists($folder))
		rmdir($folder);
	}

	static function copyFolder($folder, $destination)
	{
		if(is_file($folder)) copy($folder, $destination . '/' . basename($folder));
		else
		{
			$contents = scandir($folder);

			foreach($contents AS $file)
			{
				$path = $folder . '/' . $file;
				if($file != '..' && $file != '.')
				{
					if(is_file($path))
						copy($path, $destination . '/' . basename($path));
					else
					{
						self::folder($destination . '/' . $file);
						self::copyFolder($path, $destination . '/' . $file);
					}
				}
			}
		}
	}

	static function createFolder($folder)
	{
		$path = '';
		$parts = explode('/', $folder);
		foreach($parts AS $folder)
		{
			$path .= $folder.'/';
			if($folder != '..' && $folder != '.') @mkdir($path);
		}
	}

	static function write($file, $content)
	{
		$file = fopen($file,'w+');
		$writed = fputs($file,$content);
		fclose($file);

		return $writed;
	}

	static function writeAfter($file, $content)
	{
		$file = fopen($file,'a+');
		fputs($file,$content);
		fclose($file);
	}

	static function writeBefore($file, $content)
	{
		$file = fopen($file,'r+');
		fputs($file,$content);
		fclose($file);
	}

	static function download($path)
	{
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/download");
		header("Content-Disposition: attachment; filename=".basename($path).";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($path));
		set_time_limit(0);
		readfile($path);
	}
}

?>
