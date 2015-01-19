<a href="<?php CswString::p($prevUrl); ?>" class="btn">Continuer mes achats</a>
<?php if(!empty($basket)): ?>
<table class="clear">
	<tr>
		<th colspan="2"></th>
		<th>Quantité</th>
		<th>Designation</th>
		<th>Prix unitaire HT</th>
		<th>Prix total HT</th>
		<th>TVA</th>
		<th>Prix total TTC</th>
	</tr>
<?php
		foreach($basket as $product):
?>
	<tr id="a<?php CswString::p($product->id); ?>">
		<td><a href="<?php CswString::p(URL . '/addBasket/?delete=true&id=' . $product->id); ?>"><img src="<?php CswString::p(CswPref::pref('pathCdn') . 'images/button_cancel.png'); ?>" /></a></td>
		<td><img src="<?php CswString::p($coverUrl . $product->id . '/cover.jpg'); ?>" style="width: 50px; height: 50px;" /></td>
		<td>
			<div class="increfield">
				<input id="field_qty" type="text" name="qty" value="<?php CswString::p($product->qty); ?>" />
				<div class="cmd">
					<a href="<?php CswString::p(URL . '/addBasket/?update=true&type=add&id=' . $product->id . '&location=#a' . $product->id); ?>" class="more">+</a>
					<a href="<?php CswString::p(URL . '/addBasket/?update=true&type=remove&id=' . $product->id . '&location=#a' . $product->id); ?>" class="less">-</a>
				</div>
			</div>
		</td>
		<td><?php CswString::p($product->title); ?></td>
		<td><?php CswString::pPrice($product->priceHT); ?></td>
		<td><?php CswString::pPrice($product->priceHT * $product->qty); ?></td>
		<td><?php CswString::p($product->vat); ?>%</td>
		<td><?php CswString::pPrice($product->priceTTC * $product->qty); ?></td>
	</tr>
<?php 
		endforeach;
?>
	<tr class="total">
		<th colspan="5"></th>
		<th>Total HT</th>
		<th>Total TVA</th>
		<th>Total TTC</th>
	</tr>
	<tr class="total">
		<td colspan="5"></td>
		<td><?php CswString::pPrice($totalHT); ?></td>
		<td><?php CswString::p($vat); ?>%</td>
		<td><?php CswString::pPrice($totalTTC); ?></td>
	</tr>
</table>
<a href="<?php CswString::p($nextUrl); ?>" class="btn">Valider mon panier</a>
<?php else: ?>
	<div class="no-data clear">Votre panier est vide, remplissez-le.</div>
<?php endif; ?>