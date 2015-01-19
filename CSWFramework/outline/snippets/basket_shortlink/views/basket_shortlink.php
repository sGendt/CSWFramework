<a href="<?php CswString::p(URL . '/' . $link . '/'); ?>">
	<div class="basket_shortlink">
		<span class="title">Mon panier</span>
		<div class="price"><?php CswString::pPrice($basket->price); ?></div>
		<span class="qty">(<span><?php CswString::p($basket->qty); ?></span> article/s)
	</div>
</a>