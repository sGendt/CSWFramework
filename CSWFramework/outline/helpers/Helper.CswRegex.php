<?php

abstract class CswRegex
{
	static function isMail($string)
	{
		$regex = "#^[a-zA-Z0-9\.-_]{2,}[@]{1}[a-zA-Z0-9\.-_]{2,}[\.]{1}[a-z]{2,}$#";
		return preg_match($regex, $string);
	}

	static function isPhone($string)
	{
		$regex = "#^0[1-9][0-9]{8}$#";
		return preg_match($regex, $string);
	}

	static function isUrl($string)
	{
		return filter_var($string, FILTER_VALIDATE_URL);
	}

	static function isName($string)
	{
		$regex = "#^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]{2,}$#";
		return preg_match($regex, $string);
	}

	static function isStreet($string)
	{
		$regex = "#^[0-9,]{1,}[ ]{1}[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ]{2,}[ ]{1}[a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ' -]{2,}$#";
		return preg_match($regex, $string);
	}

	static function isGeneralStreet($string)
	{
		$regex = "#^[a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ' -]{2,}$#";
		return preg_match($regex, $string);
	}

	static function isCity($string)
	{
		$regex = "#^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ' -]{3,}$#";
		return preg_match($regex, $string);
	}

	static function isZipcode($string)
	{
		$regex = "#^[0-9]{5}$#";
		return preg_match($regex, $string);
	}

	static function isCountry($string)
	{
		$regex = "#^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ' -]{2,}$#";
		return preg_match($regex, $string);
	}

	static function isShortZipcode($string)
	{
		$regex = "#^[0-9]{2}$#";
		return preg_match($regex, $string);
	}

	/*static function isEan($string)
	{
		$regex = "#^[0-9]{2}$#";
		return preg_match($regex, $string);
	}*/

	static function isPrice($string)
	{
		$regex = "#^[0-9 ]{1,}[,|\.]{1}[0-9]{2}$#";
		return preg_match($regex, $string);
	}

	static function isInt($int)
	{
		$regex = "#^[0-9]+$#";
		return preg_match($regex, $int);
	}

	static function isTimestamp($time)
	{
    	return ((int) (string) $time === $time) && ($time <= PHP_INT_MAX) && ($time >= ~PHP_INT_MAX);
	}

	static function isVatNumber($string, $country = 'France', $siren = false)
	{
		if($siren)
			$regex = "#^(AT|BE|BG|CY|CZ|DE|DK|EE|EL|ES|FI|FR|GB|HU|IE|IT|LT|LU|MT|NL|PL|PT|RO|SE|SI|SK){1}[0-9]{2}".$siren."$#";
		else	
			$regex = "#^(AT|BE|BG|CY|CZ|DE|DK|EE|EL|ES|FI|FR|GB|HU|IE|IT|LT|LU|MT|NL|PL|PT|RO|SE|SI|SK){1}[0-9]{11}$#";

		$return = preg_match($regex, $string);

		if($return)
		{
			$vatNumber = new CswVatNumber();

			$valid = $vatNumber->isVatNumber
			(
				array
				(
					'vatNumber' => $string,
					'country' => $country
				)
			);

			if($valid != 'error')
				$return = $valid;
		}

		return $return;
	}


	static function isSiren($string)
	{
		$sum = 0;
		$string = str_replace(array(' ', '.', '-', '_', ',', ', '), '', $string);

		if (strlen($string) != 9) 
			return false;
		if (!is_numeric($string)) 
			return false;

		for ($index = 0; $index < 9; $index ++)
		{
			$number = (int) $string[$index];

			if (($index % 2) != 0) 
				if (($number *= 2) > 9) 
					$number -= 9; 

			$sum += $number;
		}
		
		if (($sum % 10) == 0) 
			return true; 
		else 
			return false;
	}
	
	static function isSiret($string)
	{
		$sum = 0;
		$string = str_replace(array(' ', '.', '-', '_', ',', ', '), '', $string);

		if(strlen($string) != 14) 
			return false;
		if(!is_numeric($string)) 
			return false;
		
		for($index = 0; $index < 14; $index ++)
		{

			$number = (int) $string[$index];

			if(($index % 2) == 0) 
				if(($number *= 2) > 9) 
					$number -= 9; 

			$sum += $number;

		}
		
		if(($sum % 10) == 0) 
			return true; 
		else 
			return false;
	}


	/**
	 *  Bar code number authorized :
	 *	(8) EAN-8, UCC-8, GTIN-8 
	 *  (12) UPC-E, UPC-A, GTIN-12
	 *	(13) EAN-13, UCC-13, GTIN-13, ISBN-13
	 * 	(14) EAN-14, UCC-14, GTIN-14
	 */

	static function isEan($string)
	{
		$sum = 0;
		$string = str_replace(array(' ', '.', '-', '_', ',', ', '), '', $string);

		$length = strlen($string);

		if 
		(
			$length != 8 && 
			$length != 12 && 
			$length != 13 & 
			$length != 14
		) 
			return false;
		if(!is_numeric($string)) 
			return false;
		

		for($index = 0; $index < ($length - 1); $index ++)
		{
			$number = (int) $string[$index];

			if (($index % 2) == 0) 
				$number *= 3;

			$sum += $number;
		}

		$key = $string[$length - 1];
		
		if(10 - ($sum % 10) == $key) 
			return true; 
		else 
			return false;
	} 
}

?>
