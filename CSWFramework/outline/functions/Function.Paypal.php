<?php

/*https://www.sandbox.paypal.com/cgi-bin/webscr
https://api-3t.paypal.com/nvp
https://www.paypal.com/cgi-bin/webscr*/

// Info avant paiement

function SetExpressCheckout
(
	$amount,
	$desc,
	$returnUrl = PAYPALRETURNURL, 
	$cancelUrl = PAYPALCANCELURL,
	$plateforme = PAYPALAPIURL, 
	$API_username = PAYPALUSER, 
	$API_password = PAYPALPWD, 
	$API_signature = PAYPALSIGNATURE
)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$plateforme);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);

	$nvpreq = "USER=".$API_username
	."&PWD=".$API_password
	."&SIGNATURE=".$API_signature
	."&VERSION=63.0&AMT=".$amount
	."&RETURNURL=".urlencode($returnUrl)
	."&CANCELURL=".urlencode($cancelUrl)
	."&DESC=".urlencode($desc)
	."&METHOD=SetExpressCheckout&CURRENCYCODE=EUR&LOCALECODE=FR&HDRIMG=".urlencode(PAYPALHDRIMG);

	curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

	$reponse = curl_exec($ch);

	$RepArray = deformatNVP($reponse);

	return $RepArray;
}
	
	
// effectue le paiement depuis page RETURN
function DoExpressCheckoutPayment
(
	$AMT,
	$PAYERID, 
	$TOKEN, 
	$plateforme = PAYPALAPIURL, 
	$API_username = PAYPALUSER, 
	$API_password = PAYPALPWD, 
	$API_signature = PAYPALSIGNATURE, 
	$PAYMENTACTION = PAYPALPAYMENTACTION
)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$plateforme);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);

	$nvpreq = "USER=".$API_username."&PWD=".$API_password."&SIGNATURE=".$API_signature."&VERSION=63.0&PAYMENTACTION=".$PAYMENTACTION."&PAYERID=".$PAYERID."&TOKEN=".$TOKEN."&AMT=".$AMT."&CURRENCYCODE=EUR&METHOD=DoExpressCheckoutPayment";

	curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

	$reponse = curl_exec($ch);

	$RepArray = deformatNVP($reponse);

	return $RepArray;
}

// retourne les informations sur l'acheteur après le paiement

function GetExpressCheckoutDetails
(
	$token,
	$plateforme = PAYPALAPIURL, 
	$API_username = PAYPALUSER, 
	$API_password = PAYPALPWD, 
	$API_signature = PAYPALSIGNATURE
)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,$plateforme);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POST, 1);


	$nvpreq = "USER=".$API_username."&PWD=".$API_password."&SIGNATURE=".$API_signature."&VERSION=63.0&TOKEN=".$token."&METHOD=GetExpressCheckoutDetails";

	curl_setopt($ch,CURLOPT_POSTFIELDS,$nvpreq);

	$reponse = curl_exec($ch);

	$RepArray = deformatNVP($reponse);

	return $RepArray;
}

function deformatNVP($nvpstr)
{
	$intial=0;
	$nvpArray = array();

	while(strlen($nvpstr))
	{
		$keypos= strpos($nvpstr,'=');
		$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);
		$keyval=substr($nvpstr,$intial,$keypos);
		$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
		$nvpArray[urldecode($keyval)] =urldecode( $valval);
		$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	}
		
	return $nvpArray;
}

?>