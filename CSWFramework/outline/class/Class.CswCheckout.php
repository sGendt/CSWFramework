<?php

class CswCheckout
{
	private $customParams = array
	(
		'bpspid',
		'bpuid',
		'bpiid'
	);

	private $params = array
	(
		'commandId',
		'productId',
		'price'
	);

	public function landing()
	{
		$this->setVerifCode();
		$this->diplayImage();
	}

	public function verifLanding()
	{
		$this->setVerifCode();
		$this->diplayImage();
	}

	public function payment()
	{
		$this->setVerifCode();

		$tracking = new Tracking();

		// Finir l'insertion des données après un paiement, ajouter l'identifiant, l'id de commande et le prix de vente site dans Tracking
		// Lors de l'import de fichier ajouter l'identifiant interne

		$tracking->setUserId(CswVar::v('bpuid'));
		$tracking->setSellerProductId(CswVar::v('bpspid'));

		$sellerProduct = new SellerProduct();
		$sellerProduct->load(CswVar::v('bpspid'));

		$tracking->setEan($sellerProduct->getEan());
		$tracking->setPrice($sellerProduct->getPrice());			
		$tracking->setTitle($sellerProduct->getTitle());			
		$tracking->setCategoryId($sellerProduct->getCategoryId());	

		$category = new Category();
		$category->load($sellerProduct->getCategoryId());
		

		$tracking->setCategory($category->getName());
		$tracking->setClic(0);
		$tracking->setSale(1);
		$tracking->setComandId(CswVar::v('orderId'));
		$tracking->setInternalIdentifier(CswVar::v('bpiid'));
		$tracking->setInternalPrice(CswVar::v('price'));
		$tracking->setTime(time());

		$tracking->publicSave();

		$this->diplayImage();
	}

	public function verifPayment()
	{
		$this->setVerifCode();
		$this->diplayImage();
	}

	public function setVerifCode()
	{
		try
		{
			$action = CswVar::v('action');
			$user = new User();
			$user->loadByWebsiteUrl(CswVar::v('hostname'));

			if
			(
				$action == 'verifLanding' ||
				$action == 'landing'
			)
				$user->setLandingTrackingActive(time());
					
			if
			(
				$action == 'verifPayment' ||
				$action == 'payment'
			)
				$user->setPaymentTrackingActive(time());

			$user->publicUpdate();
		}
		catch(Exception $e){}
	}

	public function diplayImage()
	{
		CswString::p(file_get_contents(PIXURL), true);
	}
}

?>
