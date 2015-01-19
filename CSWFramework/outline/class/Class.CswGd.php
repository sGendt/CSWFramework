<?php

class CswGd
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
	private $imageExit = null;
	private $x = 0;
	private $y = 0;
	private $width = 0;
	private $heihgt = 0;
	private $imageWidth = 0;
	private $imageHeihgt = 0;
	private $type = PROPORTIONALRESIZE;
	private $extension = null;


	public function resize($imagePath, $imageName)
	{
		$this->imagePath = $imagePath;
		$this->imageName = $imageName;
		$this->basename = basename($this->imagePath);

		

		if(empty($this->extension))
		{
			$extension = strrchr($this->basename, '.');
			$this->extension = substr($extension, 1); 
		}

		list($this->imageWidth, $this->imageHeight) = GetImageSize($this->imagePath);
		list($this->width, $this->height) = $this->sizes;
		
		switch($this->type)
		{
			case PROPORTIONALRESIZE:
				$this->proportional();
			break;

			case THUMBNAILRESIZE:
				$this->thumbnail();
			break;

			case RESIZEBYHEIGHT:
				$this->height();
			break;

			case RESIZEBYWIDTH:
				$this->width();
			break;
		}

		if($this->type != THUMBNAILRESIZE)
			$this->imageCreate();

		$this->imageExit = ImageCreateTrueColor($this->width, $this->height);

		imagealphablending($this->imageExit, FALSE);
		imagesavealpha($this->imageExit, TRUE);

		//die($this->width. '-' .$this->height);
		
		imagecopyresampled
		(
			$this->imageExit,
			$this->source,
			0,
			0,
			$this->x,
			$this->y, 
			$this->width, 
			$this->height, 
			$this->imageWidth, 
			$this->imageHeight
		);	
	}

	private function thumbnail()
	{
		$source =  new CswGd();
		$source->setSizes(array($this->width, $this->height));
		$source->setExtension($this->extension);

		$proportion = $this->imageWidth / $this->imageHeight;
		if($proportion >= 1)
	      	$source->setType(RESIZEBYHEIGHT);
	    else
	      	$source->setType(RESIZEBYWIDTH);
		
		$source->resize($this->imagePath, 'tmp');

		$this->x = ($source->getWidth() - $this->width) / 2;
      	$this->y = ($source->getHeight() - $this->height) / 2;


		$source = $source->getGdImage();
		$this->source = $source;


		$this->imageWidth = $this->width;
		$this->imageHeight = $this->height;
	}

	private function proportional()
	{
		$proportion = $this->imageWidth / $this->imageHeight;

		//die($proportion);

	    if($proportion >= 1)
	      	$coef = $this->width / $this->imageWidth;
	    else
	      	$coef = $this->height / $this->imageHeight;


	    $this->width = $this->imageWidth * $coef;
	    $this->height = $this->imageHeight * $coef;
	}

	private function imageCreate()
	{
		switch($this->extension) 
		{
			case 'jpg':
			case 'jpeg':
				$this->source = imagecreatefromjpeg($this->imagePath);
			break;

			case 'gif':
				$this->source = imagecreatefromgif($this->imagePath);
			break;

			case 'png':
				$this->source = imagecreatefrompng($this->imagePath);
			break;
			default:
				echo 'L\'image n\'est pas dans un format reconnu. Extensions autoris?es : jpg/jpeg, gif, png';
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

	public function save()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		if($this->prefix)
			$this->prefix .= '_';

		if($this->sufix)
			$this->sufix = '_' . $this->sufix;

		ImagePng($this->imageExit, $this->savePath . $this->prefix . $this->imageName . $this->sufix . '.png');
	}

	public function savePng()
	{
		$this->save();
	}

	public function saveJpg()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		ImageJpeg($this->imageExit, $this->savePath . $this->imageName.'.jpg');
	}

	public function saveGif()
	{
		if($this->grayscale)
			imagefilter($this->imageExit, IMG_FILTER_GRAYSCALE);

		ImageGif($this->imageExit, $this->savePath . $this->imageName.'.gif');
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function clear()
	{
		ImageDestroy($this->source);
	}

	public function height()
	{
	    $coef = $this->height / $this->imageHeight;

	    $this->width = $this->imageWidth * $coef;
	    $this->height = $this->imageHeight * $coef;
	}

	public function width()
	{
	    $coef = $this->width / $this->imageWidth;

	    $this->width = $this->imageWidth * $coef;
	    $this->height = $this->imageHeight * $coef;
	}

	public function getGdImage()
	{
		return $this->imageExit;
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function setExtension($extension)
	{
		$this->extension = $extension;
	}
}

?>