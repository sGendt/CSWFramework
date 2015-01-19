<?php
//on vérifie si l'appel au script est paramétré et si les paramétres ne sont pas nulles
if(isset($_GET['src'])AND isset($_GET['width'])AND isset($_GET['height']) 
AND $_GET['src'] != NULL AND $_GET['width'] != NULL AND $_GET['height'] != NULL)
	{
	//on stock les paramétres dans des variables
	$src = $_GET['src'];
	$width = $_GET['width'];
	$height = $_GET['height'];
	
	//on extrait l'extention de l'image de la variable $src qui correspond à l'image à travailler
	$extension = substr($src, -3);
	$extend = strtolower($extension);

	//on cré une image gd avec la fonction appropriée en fonction du type de l'image
	switch ($extend) 
		{
		case "jpg":
		case "peg": //pour le cas où l'extension est "jpeg"
			$imgSource = imagecreatefromjpeg($src);
		break;

		case "gif":
			$imgSource = imagecreatefromgif($src);
		break;

		case "png":
			$imgSource = imagecreatefrompng($src);
		break;
		default:
			echo "L'image n'est pas dans un format reconnu. Extensions autorisées : jpg/jpeg, gif, png";
		break;
		}
	
	//on récupère les informations sur les dimensions de l'image pour pouvoir la réduire en respectant les proportions
	$sizeSource = GetImageSize($src);
	//la largeur de l'image source
	$widthSource = $sizeSource[0];
	//la hauteur de l'image source
	$heightSource = $sizeSource[1];
	
	//ici on vérifie que l'image soit bien plus grande que les dimensions souhaitées//
	if($widthSource > $width or $heightSource > $height)
		{
		//si les dimensions sont bien inférieur on effectue un controle pour en déterminer le type d'image
		//Carré - horizontale - verticlale
		$control = "";
		
		//ici pour image horizontale
		if($widthSource > $heightSource)
			{ $control .= "1"; }
		//ici pour image verticale
		if($widthSource < $heightSource)
			{ $control .= "2"; }
		//ici pour image carré
		if($widthSource == $heightSource)
			{ $control .= "3"; }
		
		//Si la variable control à pris pour valeur 1 on effectuera les modifications pour une image horizontale
		if($control == "1")
			{
			//la valeur width est le paramètre qui définie la largeur maximale dans le script d'appel 
			//on la nomme "$widthMaximal" pour une meilleure lecture
			$widthMaximal = $width; 
			
			//ici on déduit la nouvelle hauteur qu'on nommera "$heightMaximal" en fonction de la largeur imposée
			$heightMaximal = round(($widthMaximal / $widthSource) * $heightSource);
			
			//on cré une image gd qui servira de cadre, on y inséra notre image source resizée
			$imgDestination = ImageCreateTrueColor($widthMaximal, $heightMaximal);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire à un .png
			imagesavealpha($imgSource, true);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthMaximal,$heightMaximal,$widthSource,$heightSource);	
			
			//ici on récupère la largeur de notre nouvelle image resizée bien que égale à $widthMaximal
			$width = imagesx($imgDestination);
			
			//ici on récupère la hauteur de notre nouvelle image resizée bien que égale à $heightMaximal
			$height = imagesy($imgDestination);
			
			//on cré une nouvelle image pour stocker la symétrie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la symétrie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			
			//on définie l'opacité de départ du dégradé transparent
			$transparenceDepart = 80;
			//on définie l'opacité de fin du dégradé transparent
			$transparenceFin = 0;
			
			//**//ici on définie que notre nouvelle image symétrique est égale à notre image resizé//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre négatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enlève le méchant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cré le dégradé de la transparence
			for ($i = 0; $i <= $height; $i++)
				{
				$coef = $i / $height;

				if ($transparenceDepart > $transparenceFin)
					{
					$alpha = (int) ($transparenceDepart - ($coef * $intervalTransparence));
					}
				else
					{
					$alpha = (int) ($transparenceDepart + ($coef * $intervalTransparence));
					}
        
				$transparence = 127 - $alpha;

				//on insère notre image dégradé dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);		
			}
		//Si la variable control à pris pour valeur 2 ou 3 on effectuera les modifications pour une image verticale et carré
		if($control == "2" or $control == "3")
			{
			//la valeur height est le paramètre qui définie la hauteur maximale dans le script d'appel 
			//on la nomme "$heightMaximal" pour une meilleure lecture
			$heightMaximal = $height; 
			
			//ici on déduit la nouvelle largeur qu'on nommera "$widthMaximal" en fonction de la largeur imposée
			$widthMaximal = round(($heightMaximal / $heightSource) * $widthSource);
			
			//on cré une image gd qui servira de cadre, on y inséra notre image source resizée
			$imgDestination = ImageCreateTrueColor($widthMaximal, $heightMaximal);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire à un .png
			imagesavealpha($imgSource, true);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthMaximal,$heightMaximal,$widthSource,$heightSource);
			
			//ici on récupère la largeur de notre nouvelle image resizée bien que égale à $widthMaximal
			$width = imagesx($imgDestination);
			
			//ici on récupère la hauteur de notre nouvelle image resizée bien que égale à $heightMaximal
			$height = imagesy($imgDestination);
			
			//on cré une nouvelle image pour stocker la symétrie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la symétrie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			//on définie l'opacité de départ du dégradé transparent
			$transparenceDepart = 80;
			//on définie l'opacité de fin du dégradé transparent
			$transparenceFin = 0;
			
			//**//ici on définie que notre nouvelle image symétrique est égale à notre image resizé//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre négatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enlève le méchant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cré le dégradé de la transparence
			for ($i = 0; $i <= $height; $i++)
				{
				$coef = $i / $height;

				if ($transparenceDepart > $transparenceFin)
					{
					$alpha = (int) ($transparenceDepart - ($coef * $intervalTransparence));
					}
				else
					{
					$alpha = (int) ($transparenceDepart + ($coef * $intervalTransparence));
					}
        
				$transparence = 127 - $alpha;

				//on insère notre image dégradé dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);
			}
		}
	//si l'image est plus petite que les dimensions souhaitées
	else
		{
		//si les dimensions sont bien inférieur on effectue un controle pour en déterminer le type d'image
		//Carré - horizontale - verticlale
		$control = "";
		
		//ici pour image horizontale
		if($widthSource > $heightSource)
			{ $control .= "1"; }
		//ici pour image verticale
		if($widthSource < $heightSource)
			{ $control .= "2"; }
		//ici pour image carré
		if($widthSource == $heightSource)
			{ $control .= "3"; }
		
		//Si la variable control à pris pour valeur 1 on effectuera les modifications pour une image horizontale
		if($control == "1")
			{		
			
			//on cré une image gd qui servira de cadre, on y inséra notre image source resizée
			$imgDestination = ImageCreateTrueColor($widthSource, $heightSource);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire à un .png
			imagesavealpha($imgSource, true);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthSource,$heightSource,$widthSource,$heightSource);	
			
			//ici on récupère la largeur de notre nouvelle image resizée bien que égale à $widthSource
			$width = imagesx($imgDestination);
			
			//ici on récupère la hauteur de notre nouvelle image resizée bien que égale à $heightSource
			$height = imagesy($imgDestination);
			
			//on cré une nouvelle image pour stocker la symétrie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la symétrie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			
			//on définie l'opacité de départ du dégradé transparent
			$transparenceDepart = 80;
			//on définie l'opacité de fin du dégradé transparent
			$transparenceFin = 0;
			
			//**//ici on définie que notre nouvelle image symétrique est égale à notre image resizé//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre négatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enlève le méchant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cré le dégradé de la transparence
			for ($i = 0; $i <= $height; $i++)
				{
				$coef = $i / $height;

				if ($transparenceDepart > $transparenceFin)
					{
					$alpha = (int) ($transparenceDepart - ($coef * $intervalTransparence));
					}
				else
					{
					$alpha = (int) ($transparenceDepart + ($coef * $intervalTransparence));
					}
        
				$transparence = 127 - $alpha;

				//on insère notre image dégradé dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);		
			}
		//Si la variable control à pris pour valeur 2 ou 3 on effectuera les modifications pour une image verticale et carré
		if($control == "2" or $control == "3")
			{
			
			//on cré une image gd qui servira de cadre, on y inséra notre image source resizée
			$imgDestination = ImageCreateTrueColor($widthSource, $heightSource);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire à un .png
			imagesavealpha($imgSource, true);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthSource,$heightSource,$widthSource,$heightSource);
			
			//ici on récupère la largeur de notre nouvelle image resizée bien que égale à $widthSource
			$width = imagesx($imgDestination);
			
			//ici on récupère la hauteur de notre nouvelle image resizée bien que égale à $heightSource
			$height = imagesy($imgDestination);
			
			//on cré une nouvelle image pour stocker la symétrie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on désactive le mode alpha blending qui à pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la symétrie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			//on définie l'opacité de départ du dégradé transparent
			$transparenceDepart = 80;
			//on définie l'opacité de fin du dégradé transparent
			$transparenceFin = 0;
			
			//**//ici on définie que notre nouvelle image symétrique est égale à notre image resizé//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre négatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enlève le méchant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cré le dégradé de la transparence
			for ($i = 0; $i <= $height; $i++)
				{
				$coef = $i / $height;

				if ($transparenceDepart > $transparenceFin)
					{
					$alpha = (int) ($transparenceDepart - ($coef * $intervalTransparence));
					}
				else
					{
					$alpha = (int) ($transparenceDepart + ($coef * $intervalTransparence));
					}
        
				$transparence = 127 - $alpha;

				//on insère notre image dégradé dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);
			}
		}
	}
?>