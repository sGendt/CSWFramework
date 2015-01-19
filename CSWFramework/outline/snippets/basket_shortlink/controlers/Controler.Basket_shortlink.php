<?php

$basket = new stdClass;
$basket->qty = 0;
$basket->price = 0;

$basketSession = CswVar::v('basketSession');

if(!empty($basketSession))
{
	foreach($basketSession as $value) 
	{
		$basket->price += $value['qty'] * $value['price'];
		$basket->qty += $value['qty'];
	}
}

?>