<?php

abstract class CswString
{
	static function getUpperCase($string)
	{
		$upperCases = array();
		
		for ($i = 0 ; $i < strlen($string) ; $i++)
		{
			$letter = $string[$i];
			$code = ord($letter);

			if(($code >= 65) && ($code <= 90)) 
			{
				$upperCases[$i] = $letter;
			}
		}

		return $upperCases;
	}

	static function d($string)
	{
		CswString::p('<pre>', true);
		print_r($string);
		CswString::p('</pre>', true);

		die();
	}

	static function p($string, $brut = false)
	{
		if($brut == true)
		{
			$return = $string;
		}
		else
		{
			$return = htmlentities($string, ENT_QUOTES, "UTF-8");
		}

		echo $return;
	}

	static function pPrice($int, $language = 'FR_fr', $currency  = '€')
	{
		$int = str_replace(',', '.', $int);
		if($language == 'FR_fr')
		{
			$return = number_format($int, '2', ',', ' ') . $currency;
		}

		echo htmlentities($return, ENT_QUOTES, "UTF-8");
	}


	static function genUId()
	{
		return sha1(uniqid(microtime(true), true));
	}

	static function generateInformaticString($string)
	{
		$find = array
		(
			'(',')',':','/','--',';','.',',','&','à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý',' '
		);
		$replace = array
		(
			'','','','','-','','','','','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','-'
		);

		$string = strtolower(str_replace($find, $replace, $string));

		$find = array
		(
			'--'
		);
		$replace = array
		(
			'-'
		);
		return strtolower(str_replace($find, $replace, $string));
	}

	static function replaceDashBySpace($string)
	{
		$find = array
		(
			'-'
		);
		$replace = array
		(
			' '
		);
		return strtolower(str_replace($find, $replace, $string));
	}

	static function ashString($string)
	{
		return md5(SEL . $string . SEL);
	}

	static function genLogin($prefix, $suffix = SUFFIX)
	{
		return $prefix . rand(0, 10) . rand(0, 10) . rand(0, 10) . rand(0, 10) . rand(0, 10) . '-' . $suffix;
	}

	static function genPassword($length = PASSWORDLENGTH)
	{
	    $password = '';
	 
	    $chars = PASSWORDCHARS;
	 
	    $maxLength = strlen($chars);
	 
	    if($length > $maxLength)
	        $length = $maxLength;
	 
	    $i = 0;
	 
	    while($i < $length) 
	    {
	        $char = substr($chars, mt_rand(0, $maxLength-1), 1);
	 
	        if(!strstr($password, $char)) 
	        {
	            $password .= $char;
	            $i++;
	        }
	    }
	 
	    return $password;
	}


	static function truncate($string, $length)
	{
		$truncateString = $string;

		if (strlen($string) > $length)
		{
			$truncateString = substr($string, 0, $length);
			$space = strrpos($truncateString, ' ');
			$truncateString = substr($truncateString, 0, $space);
		}

		$points = (strlen($truncateString) < strlen($string)) ? TRUNCATEENDSTRING : '';
		return $truncateString . $points;
	}

	static function getReduction($amount, $reduction, $type = null)
	{
		switch ($type) 
		{
			case '%':
				$amount -= round($amount * $reduction / 100, 2);
			break;
			
			case '€':
				$amount -= $reduction;
			break;

			default:
				$amount -= $reduction;
			break;
		}

		return $amount;
	}

	static function formateNumerotation($number)
	{
		$num = '';

		$nLength = strlen($number);
		$length = strlen(DOCNUMEROTATIONLENGTH) - $nLength;

		if($length > 0)
			$num = substr(DOCNUMEROTATIONLENGTH, 0, $length);

		return $num.$number;
	}

	static function getVat($vat, $amount)
	{
		return ($amount * $vat) / 100;
	}

	static function getTTC($vat, $amount)
	{
		return $amount + $vat;
	}
}

?>
