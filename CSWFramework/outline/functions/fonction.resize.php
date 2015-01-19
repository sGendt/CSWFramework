<?php

class Gd
{
	private $grayscale = false;
	private $sizes = array();
	private $savePath = null;
	private $prefix = null;
	private $sufix = null;
	private $imageName = null;
	private $imagePath = null;
	private $basename = null;
	private $source = null;
	private $destination = null;
	private $imageExit = null;

	public function resize($imagePath, $imageName)
	{
		$this->imagePath = $imagePath;
		$this->imageName = $imageName;
		$this->basename = basename($this->imagePath);

		$extension = strrchr($filename, '.');
		$extension = substr($extension, 1); 

		$this->imageCreate($extension);

		list($imageWidth, $imageHeight) = GetImageSize($this->imagePath);

		$proportion = $imageWidth / $imageHeight;

	    $coef = 1;

	    if($proportion >= 1)
	      	$coef = $sizes[0] / $imageWidth;
	    else
	      	$coef = $sizes[1] / $imageHeight;


	    $width = $imageWidth * $coef;
	    $height = $imageHeight * $coef;

		$this->imageExit = ImageCreateTrueColor($width, $height);

		imagealphablending($this->imageExit, FALSE);
		imagesavealpha($this->imageExit, TRUE);
		
		imagecopyresampled
		(
			$this->imageExit,
			$this->source,
			0,
			0,
			0,
			0, 
			$width, 
			$height, 
			$imageWidth, 
			$imageHeight
		);	
	}

	private function createImage($extension)
	{
		switch($extension) 
		{
			case 'jpg':
			case 'jpeg': //pour le cas où l'extension est "jpeg"
				$this->source = imagecreatefromjpeg($this->imagePath);
			break;

			case 'gif':
				$this->source = imagecreatefromgif($this->imagePath);
			break;

			case 'png':
				$this->source = imagecreatefrompng($this->imagePath);
			break;
			default:
				echo 'L\'image n\'est pas dans un format reconnu. Extensions autorisées : jpg/jpeg, gif, png';
			break;
		}
	}

	public function setGrayscale($grayscale = true)
	{
		$this->grayscale = $grayscale;
	}

	public function setSizes($sizes = array())
	{
		$this->sizes = $sizes;
	}

	public function setSavePath($savePath)
	{
		$this->savePath = $savePath;
	}

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	public function setSufix($sufix)
	{
		$this->sufix = $sufix;
	}

	public function setDestination($destination)
	{
		$this->destination = $destination;
	}

	public function save()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		ImagePng($this->imageExit, $this->destination . $this->imageName.'.png');
		ImageDestroy($this->source);
	}

	public function savePng()
	{
		$this->save();
	}

	public function saveJpg()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		ImageJpeg($this->imageExit, $this->destination . $this->imageName.'.jpg');
		ImageDestroy($this->source);
	}

	public function saveGif()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		ImageGif($this->imageExit, $this->destination . $this->imageName.'.gif');
		ImageDestroy($this->source);
	}
}
?>