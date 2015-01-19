<?php
//on v�rifie si l'appel au script est param�tr� et si les param�tres ne sont pas nulles
if(isset($_GET['src'])AND isset($_GET['width'])AND isset($_GET['height']) 
AND $_GET['src'] != NULL AND $_GET['width'] != NULL AND $_GET['height'] != NULL)
	{
	//on stock les param�tres dans des variables
	$src = $_GET['src'];
	$width = $_GET['width'];
	$height = $_GET['height'];
	
	//on extrait l'extention de l'image de la variable $src qui correspond � l'image � travailler
	$extension = substr($src, -3);
	$extend = strtolower($extension);

	//on cr� une image gd avec la fonction appropri�e en fonction du type de l'image
	switch ($extend) 
		{
		case "jpg":
		case "peg": //pour le cas o� l'extension est "jpeg"
			$imgSource = imagecreatefromjpeg($src);
		break;

		case "gif":
			$imgSource = imagecreatefromgif($src);
		break;

		case "png":
			$imgSource = imagecreatefrompng($src);
		break;
		default:
			echo "L'image n'est pas dans un format reconnu. Extensions autoris�es : jpg/jpeg, gif, png";
		break;
		}
	
	//on r�cup�re les informations sur les dimensions de l'image pour pouvoir la r�duire en respectant les proportions
	$sizeSource = GetImageSize($src);
	//la largeur de l'image source
	$widthSource = $sizeSource[0];
	//la hauteur de l'image source
	$heightSource = $sizeSource[1];
	
	//ici on v�rifie que l'image soit bien plus grande que les dimensions souhait�es//
	if($widthSource > $width or $heightSource > $height)
		{
		//si les dimensions sont bien inf�rieur on effectue un controle pour en d�terminer le type d'image
		//Carr� - horizontale - verticlale
		$control = "";
		
		//ici pour image horizontale
		if($widthSource > $heightSource)
			{ $control .= "1"; }
		//ici pour image verticale
		if($widthSource < $heightSource)
			{ $control .= "2"; }
		//ici pour image carr�
		if($widthSource == $heightSource)
			{ $control .= "3"; }
		
		//Si la variable control � pris pour valeur 1 on effectuera les modifications pour une image horizontale
		if($control == "1")
			{
			//la valeur width est le param�tre qui d�finie la largeur maximale dans le script d'appel 
			//on la nomme "$widthMaximal" pour une meilleure lecture
			$widthMaximal = $width; 
			
			//ici on d�duit la nouvelle hauteur qu'on nommera "$heightMaximal" en fonction de la largeur impos�e
			$heightMaximal = round(($widthMaximal / $widthSource) * $heightSource);
			
			//on cr� une image gd qui servira de cadre, on y ins�ra notre image source resiz�e
			$imgDestination = ImageCreateTrueColor($widthMaximal, $heightMaximal);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire � un .png
			imagesavealpha($imgSource, true);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthMaximal,$heightMaximal,$widthSource,$heightSource);	
			
			//ici on r�cup�re la largeur de notre nouvelle image resiz�e bien que �gale � $widthMaximal
			$width = imagesx($imgDestination);
			
			//ici on r�cup�re la hauteur de notre nouvelle image resiz�e bien que �gale � $heightMaximal
			$height = imagesy($imgDestination);
			
			//on cr� une nouvelle image pour stocker la sym�trie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la sym�trie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			
			//on d�finie l'opacit� de d�part du d�grad� transparent
			$transparenceDepart = 80;
			//on d�finie l'opacit� de fin du d�grad� transparent
			$transparenceFin = 0;
			
			//**//ici on d�finie que notre nouvelle image sym�trique est �gale � notre image resiz�//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre n�gatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enl�ve le m�chant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cr� le d�grad� de la transparence
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

				//on ins�re notre image d�grad� dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);		
			}
		//Si la variable control � pris pour valeur 2 ou 3 on effectuera les modifications pour une image verticale et carr�
		if($control == "2" or $control == "3")
			{
			//la valeur height est le param�tre qui d�finie la hauteur maximale dans le script d'appel 
			//on la nomme "$heightMaximal" pour une meilleure lecture
			$heightMaximal = $height; 
			
			//ici on d�duit la nouvelle largeur qu'on nommera "$widthMaximal" en fonction de la largeur impos�e
			$widthMaximal = round(($heightMaximal / $heightSource) * $widthSource);
			
			//on cr� une image gd qui servira de cadre, on y ins�ra notre image source resiz�e
			$imgDestination = ImageCreateTrueColor($widthMaximal, $heightMaximal);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire � un .png
			imagesavealpha($imgSource, true);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthMaximal,$heightMaximal,$widthSource,$heightSource);
			
			//ici on r�cup�re la largeur de notre nouvelle image resiz�e bien que �gale � $widthMaximal
			$width = imagesx($imgDestination);
			
			//ici on r�cup�re la hauteur de notre nouvelle image resiz�e bien que �gale � $heightMaximal
			$height = imagesy($imgDestination);
			
			//on cr� une nouvelle image pour stocker la sym�trie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la sym�trie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			//on d�finie l'opacit� de d�part du d�grad� transparent
			$transparenceDepart = 80;
			//on d�finie l'opacit� de fin du d�grad� transparent
			$transparenceFin = 0;
			
			//**//ici on d�finie que notre nouvelle image sym�trique est �gale � notre image resiz�//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre n�gatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enl�ve le m�chant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cr� le d�grad� de la transparence
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

				//on ins�re notre image d�grad� dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);
			}
		}
	//si l'image est plus petite que les dimensions souhait�es
	else
		{
		//si les dimensions sont bien inf�rieur on effectue un controle pour en d�terminer le type d'image
		//Carr� - horizontale - verticlale
		$control = "";
		
		//ici pour image horizontale
		if($widthSource > $heightSource)
			{ $control .= "1"; }
		//ici pour image verticale
		if($widthSource < $heightSource)
			{ $control .= "2"; }
		//ici pour image carr�
		if($widthSource == $heightSource)
			{ $control .= "3"; }
		
		//Si la variable control � pris pour valeur 1 on effectuera les modifications pour une image horizontale
		if($control == "1")
			{		
			
			//on cr� une image gd qui servira de cadre, on y ins�ra notre image source resiz�e
			$imgDestination = ImageCreateTrueColor($widthSource, $heightSource);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire � un .png
			imagesavealpha($imgSource, true);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthSource,$heightSource,$widthSource,$heightSource);	
			
			//ici on r�cup�re la largeur de notre nouvelle image resiz�e bien que �gale � $widthSource
			$width = imagesx($imgDestination);
			
			//ici on r�cup�re la hauteur de notre nouvelle image resiz�e bien que �gale � $heightSource
			$height = imagesy($imgDestination);
			
			//on cr� une nouvelle image pour stocker la sym�trie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la sym�trie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			
			//on d�finie l'opacit� de d�part du d�grad� transparent
			$transparenceDepart = 80;
			//on d�finie l'opacit� de fin du d�grad� transparent
			$transparenceFin = 0;
			
			//**//ici on d�finie que notre nouvelle image sym�trique est �gale � notre image resiz�//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre n�gatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enl�ve le m�chant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cr� le d�grad� de la transparence
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

				//on ins�re notre image d�grad� dans un rectangle ligne de pixels par ligne de pixels
				imagefilledrectangle($imgDestination, 0, $i, $width, $i, imagecolorallocatealpha($imgDestination, 127, 127, 127, $transparence));
				}
			
			header('Content-Type: image/png'); 
			
			imagepng($imgDestination);
			ImageDestroy($dest);
			ImageDestroy($imgDestination);
			ImageDestroy($imgSource);		
			}
		//Si la variable control � pris pour valeur 2 ou 3 on effectuera les modifications pour une image verticale et carr�
		if($control == "2" or $control == "3")
			{
			
			//on cr� une image gd qui servira de cadre, on y ins�ra notre image source resiz�e
			$imgDestination = ImageCreateTrueColor($widthSource, $heightSource);
			
			//on sauvegarde le canal alpha ne notre image source pour ne pas le perdre lors de la copie si toute
			//fois on a faire � un .png
			imagesavealpha($imgSource, true);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($imgDestination, false);
			
			//on sauvegarde le canal alpha ne notre image de destination pour conserver la transparence
			imagesavealpha($imgDestination, true);
			
			//on assemble notre image source avec l'image de destination aux dimensions voulues
			ImageCopyResampled($imgDestination,$imgSource,0,0,0,0,$widthSource,$heightSource,$widthSource,$heightSource);
			
			//ici on r�cup�re la largeur de notre nouvelle image resiz�e bien que �gale � $widthSource
			$width = imagesx($imgDestination);
			
			//ici on r�cup�re la hauteur de notre nouvelle image resiz�e bien que �gale � $heightSource
			$height = imagesy($imgDestination);
			
			//on cr� une nouvelle image pour stocker la sym�trie qui va s'effectuer
			$dest = imagecreatetruecolor($width, $height);
			
			//on d�sactive le mode alpha blending qui � pour effet de transformer les pixels transparents en pixels opaques
			imagealphablending($dest, false);
			
			//on sauvegarde le canal alpha ne notre nouvelle image de destination pour conserver la transparence
			imagesavealpha($dest, true);
			
			for($i=0;$i<$height;$i++)
				{
				//si la sym�trie voulue est verticale on copie ligne de pixels par ligne de pixels de bas en haut
				imagecopy($dest, $imgDestination, 0, ($height - $i - 1), 0, $i, $width, 1);
				}

			//on d�finie l'opacit� de d�part du d�grad� transparent
			$transparenceDepart = 80;
			//on d�finie l'opacit� de fin du d�grad� transparent
			$transparenceFin = 0;
			
			//**//ici on d�finie que notre nouvelle image sym�trique est �gale � notre image resiz�//**//JE NE SAIS PAS POURQUOI MAIS SINON CA NE MARCHE PAS//**//
			$imgDestination = $dest;
			
			//on retourne la valeur absolue pour ne pas avoir un nombre n�gatif
			$intervalTransparence = abs($transparenceDepart - $transparenceFin);
			
			//ici on enl�ve le m�chant fond gris de notre image lorsque l'on active la transparence
			imagelayereffect($imgDestination, IMG_EFFECT_OVERLAY);
			
			//on lance la boucle qui cr� le d�grad� de la transparence
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

				//on ins�re notre image d�grad� dans un rectangle ligne de pixels par ligne de pixels
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