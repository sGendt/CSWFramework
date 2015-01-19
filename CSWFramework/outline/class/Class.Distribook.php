<?php

Class Distribook
{
	protected $item; // number of the command
	protected $organization;
	protected $to; // to the attention of
	protected $streetOrganization;
	protected $streetBookstore;
	protected $date;
	protected $filename; 
	protected $pages; 
	protected $qty; 
	protected $bookTitle; 

	protected $datas 				= null;

	protected $userSession 			= null;
	protected $exchangeBdd 			= null;
	protected $escapedProperties	= array('escapedProperties', 'userSession', 'exchangeBdd');


	public function __construct($item, $bookstoreName, $streetBookstore, $to, $organization, $streetOrganization, $qty, $bookTitle, $pages, $filename)
	{
		// Database connexion

		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;


		// Set params

		$this->item 				= $item;
		$this->bookstoreName 		= $bookstoreName; 
		$this->streetBookstore 		= $streetBookstore; 
		$this->to 					= $to;
		$this->organization 		= $organization;
		$this->streetOrganization 	= $streetOrganization;
		$this->qty 					= $qty;
		$this->bookTitle 			= $bookTitle;
		$this->pages 				= $pages;
		$this->filename 			= $filename;


		// Set date

		$this->date 				= date('Y-m-d H:i:s', time());


		$this->setDatas();
	}

	public function setDatas()
	{
		$this->datas = array
		(
			'GENCOD EMETTEUR' 			=> $this->bookstoreName,	
			'REFERENCE COMMANDE' 		=> $this->item,
			'NOM EMETTEUR' 				=> $this->bookstoreName,
			'ADRESSE EMETTEUR' 			=> $this->streetBookstore,
			'GENCOD LIVRAISON' 			=> $this->bookstoreName,
			'NOM LIVRAISON'				=> $this->organization . ' - ' . $this->to,
			'ADRESSE 1 LIVRAISON'		=> $this->streetOrganization,
			'ADRESSE 2 LIVRAISON'		=> null,
			'DATE EMISSION' 			=> $this->date,	
			'CODE NOTATION' 			=> null,
			'MODE EXPEDITION' 			=> DELIVERYTYPE,	
			'DATE LIVRAISON'			=> null,
			'MARQUE (DATE)' 			=> $this->date,	
			'MARQUE (HEURE)' 			=> null,
			'NUMERO DE LOT'				=> null,
			'DATE RECEPTION COMMANDE' 	=> null, 
			'HEURE RECEPTION COMMANDE' 	=> null,
			'ART: NUMERO LIGNE' 		=> null,
			'ART: REFERENCE' 			=> $this->item . '-' . str_replace('.pdf', '', $this->filename),
			'ART: QTE' 					=> $this->qty,
			'ART: TITRE' 				=> $this->bookTitle,
			'ART: AUTEUR'				=> $this->organization,
			'ART: COLLECTION'			=> $this->bookstoreName,
			'ART: CODE ARTICLE' 		=> null,
			'ART: PRIX TTC' 			=> null,
			'CET: FILLER'				=> null,
			'CET: TYPE CMDE'			=> null,
			'CET: CODE CLIENT'			=> null,	
			'CET: TYPE CODE CLIENT'		=> null,	
			'CET: COMPLT MODE EXP'		=> null,	
			'CET: COND CMDE'			=> null,	
			'CET: REF OPERATION'		=> null,
			'CET: TAUX REMISE'			=> null,	
			'CET: REGLE GESTION REMISES'=> null,	
			'CDT: SOUS PAR COMBIEN' 	=> null,	
			'CDT: PAR COMBIEN'			=> null,	
			'CDT: VL'					=> null,	
			'CDT: VA'					=> null,	
			'CDT: QTE DECIMALE'			=> null,	
			'CDT: PRIX UNIT HT NET'		=> null,	
			'CDT: CODE NOTATION LIGNE'	=> null,	
			'CDT: REF LIGNE'			=> null,	
			'CDT: PRESENTATION'			=> null,	
			'LIGNE 141: REMISE LIGNE'	=> null,	
			'LIGNE 141: FILLER 1'		=> null,
			'LIGNE 141: FILLER 2'		=> null,
			'FILENAME' 					=> $this->filename,
			'NBPAGES' 					=> $this->pages
		);
	}

	public function create()
	{
		$path = CswPref::pref('folderCdn') . 'medias/distribook/' . $this->item .'/';
		$src = $path . $this->item . '.csv';

		CswDirectory::deleteFolder($path);
		CswDirectory::folder($path);

		$cswParser = new CswParser();
		
		if($cswParser->formateDatasToCsv($this->datas, $src))
			return $src;
		else
			return false;
	}
}

?>
