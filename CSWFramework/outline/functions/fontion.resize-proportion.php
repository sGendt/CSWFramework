<?php
function resizeProp($img, $destination, $time)
	{
	$dimension = 400;
	
	$src = $img;
	$infosfichier = pathinfo($src['name']);
	$extension = $infosfichier['extension'];

	$extend = strtolower($extension);
	

	switch ($extend) 
		{
		case "jpg":
		case "peg": //pour le cas où l'extension est "jpeg"
			$imgSource = imagecreatefromjpeg($src['tmp_name']);
		break;

		case "gif":
			$imgSource = imagecreatefromgif($src['tmp_name']);
		break;

		case "png":
			$imgSource = imagecreatefrompng($src['tmp_name']);
		break;
		default:
			echo "L'image n'est pas dans un format reconnu. Extensions autorisées : jpg/jpeg, gif, png";
		break;
		}

	$sizeSource = GetImageSize($src['tmp_name']);
	$widthSource = $sizeSource[0];//4000
	$heightSource = $sizeSource[1];//3000
	
	if($widthSource > $heightSource)
		{			
		$heightMaximal = $dimension; 

		$widthMaximal = round($widthSource/($heightSource / $heightMaximal));

		$imgDestination = ImageCreateTrueColor($widthMaximal, $dimension);
		
		imagecopyresampled($imgDestination,$imgSource,0,0,0,0,$widthMaximal,$dimension,$widthSource,$heightSource);
		

		ImageJpeg($imgDestination, '../images/annonces/en_cours/'.$destination.'/'.$time.'.jpg');
		ImageDestroy($imgSource);	
		}
	else
		{
		$widthMaximal = $dimension; 

		$heightMaximal = round($heightSource/($widthSource / $widthMaximal));

		$imgDestination = ImageCreateTrueColor($dimension, $heightMaximal);
		
		imagecopyresampled($imgDestination,$imgSource,0,0,0,0,$dimension,$heightMaximal,$widthSource,$heightSource);
		
		ImageJpeg($imgDestination, '../images/annonces/en_cours/'.$destination.'/'.$time.'.jpg');
		ImageDestroy($imgSource);
		}
	}
?>