<?php

class CswVatNumber
{
	private $prefixs = array
	(
		'austria' 			=> 'AT',
		'belgium' 			=> 'BE',
		'bulgaria' 			=> 'BG',
		'cyprus' 			=> 'CY',
		'czech republic' 	=> 'CZ',
		'germany' 			=> 'DE',
		'denmark' 			=> 'DK',
		'estonia' 			=> 'EE',
		'greece' 			=> 'EL',
		'spain' 			=> 'ES',
		'finland' 			=> 'FI',
		'france' 			=> 'FR',
		'united kingdom' 	=> 'GB',
		'hungary' 			=> 'HU',
		'ireland' 			=> 'IE',
		'italy' 			=> 'IT',
		'lithuania' 		=> 'LT',
		'luxembourg' 		=> 'LU',
		'malta' 			=> 'MT',
		'the netherlands' 	=> 'NL',
		'poland' 			=> 'PL',
		'portugal' 			=> 'PT',
		'romania' 			=> 'RO',
		'sweden' 			=> 'SE',
		'slovenia' 			=> 'SI',
		'slovakia'			=> 'SK'
	);

	public function isVatNumber($values = array())
	{
		$values['vatNumber'] = empty($values['vatNumber']) ? null : $values['vatNumber'];
		$values['country'] = empty($values['country']) ? null : $values['country'];

		return $this->validateVat($values);
	}

 
	protected function validateVat($args = array()) 
	{
		$vatNumber 		= str_replace(array(' ', '.', '-', ',', ', '), '', $args['vatNumber']);
		$countryCode 	= substr($vatNumber, 0, 2);
		$vatNumber 		= substr($vatNumber, 2);

		$result = array();

		if($this->prefixs[strtolower($args['country'])] != $countryCode)
			return false;


		try
		{
			$client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl"); 
			$params = array('countryCode' => $countryCode, 'vatNumber' => $vatNumber);
			$result = $client->checkVat($params);

			//die(print_r($result));


			if ($result->valid)
			{
				$this->name = $result->name;
				$this->address = $result->address;

				return true;
			}
			else
				return false;
		}
		catch(Exception $e)
		{
			try
			{
				$ch = curl_init();
	 
				curl_setopt($ch, CURLOPT_URL, 'http://vatid.eu/check/'.$countryCode.'/'.$vatNumber);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				 
				$result = curl_exec($ch);
				$error = curl_error($ch);
				 
				curl_close($ch);

				$dom = new DomDocument();
				$dom->loadXML($result);

				$node = $dom->getElementsByTagName('valid');
				$valid = $node->item(0)->nodeValue;


				if($valid != 'false')
				{
					$node = $dom->getElementsByTagName('name');
					$this->name = $node->item(0)->nodeValue;

					$node = $dom->getElementsByTagName('address');
					$this->name = $node->item(0)->nodeValue;

					return true;
				}
				else
					return false;
			}
			catch(Exception $e)
			{
				return 'error';
			}
		}
	}
}

?>