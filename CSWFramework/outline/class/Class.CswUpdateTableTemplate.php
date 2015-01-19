<?php

class CswUpdateTableTemplate
{
	/*
		UPDATE [LOW_PRIORITY] [IGNORE] tbl_name
	    SET col_name1=expr1 [, col_name2=expr2 ...]
	    [WHERE where_definition]
	    [ORDER BY ...]
	    [LIMIT row_count]
	*/

	/*
		UPDATE mapremieretable as mpte LEFT JOIN madeuxiemetable as mdte ON mpte.id = mdte.id SET mpte.name = 'toto', mdte.phone = '0602' WHERE mpte.id = 1
	*/

	protected $className = null;
	protected $tables = array();
	protected $properties = array();


	/**
	 *
	 */

	public function __construct()
	{
		$this->className = get_class($this);
	}


	/**
	 *
	 */

	public function setTable($table = array())
	{
		$defaultOptions = array
		(
			'table' => null,
			'orderBy' => null,
			'limit' => null
		);

		$table = array_merge($defaultOptions, $table);

		$this->tables[] = $table;
	}


	/**
	 *
	 */

	public function setProperties($properties = array())
	{
		$defaultOptions = array
		(
			'tableName' => array
			(
				array('champ', 'valeur', 'tableValeur'),
				array('champ', 'valeur', 'tableValeur'),
				array('champ', 'valeur', 'tableValeur'),
			),
			'tableName2' => array
			(
				array('champ', 'valeur', 'tableValeur'),
				array('champ', 'valeur'),
				array('champ', 'valeur'),
			)
		);

		$properties = array_merge($defaultOptions, $properties);

		$this->properties[] = $properties; 
	}

	/**
	 *
	 */

	public function formateTemplate()
	{

	}
}

?>
