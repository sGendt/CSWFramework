<?php

$book = new Book();
$datas = new Datas();

$basketSession = CswVar::v('basketSession');
if(empty($basketSession))
	$basketSession = array();

$totalHT = 0;
$totalTTC = 0;
$qty = 0;
$vat = $datas->get('vat');

$basket = array();

foreach($basketSession as $value)
{
	$book->load($value['id']);

	$product = new stdClass;

	$product->id = $book->getId();
	$product->title = $book->getTitle();
	$product->priceHT = $book->getPrice();
	$product->vat = $vat;
	$product->priceTTC = CswString::getTTC(CswString::getVat($vat, $product->priceHT), $product->priceHT);
	$product->qty = $value['qty'];

	$totalHT += ($product->priceHT * $product->qty);
	$totalTTC += ($product->priceTTC * $product->qty);
	$qty += $product->qty;

	$basket[] = $product;
}

$coverUrl = CswPref::pref('pathCdn') . '/medias/cover/';

?>
