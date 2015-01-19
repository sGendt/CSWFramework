<?php

Class Datas extends CswModel
{
	protected $id 		= array('required' => false, 'value' => null);
	protected $slug 	= array('required' => true, 'value' => null);
	protected $value 	= array('required' => false, 'value' => null);

	protected $userSession 			= null;
	protected $exchangeBdd 			= null;
	protected $escapedProperties	= array('escapedProperties', 'userSession', 'exchangeBdd');


	public function __construct()
	{
		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;
	}

	public function create()
	{
		// ajouter la verification de doublon
		$this->save();
	}

	public function save()
	{
		// ajouter la verification de doublon
		$values = array
		(
			'slug' => CswVar::v('slug'),
			'value' => CswVar::v('value')
		);

		$request = 'UPDATE datas SET value = :value WHERE slug = :slug';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);
	}

	public function get($slug)
	{
		$values = array
		(
			'slug' => $slug
		);

		$request = 'SELECT * FROM '. strtolower(get_class($this)) .' WHERE slug = :slug';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);

		$rows = $query->fetchAll();
		return $rows[0]->value;
	}
}

?>