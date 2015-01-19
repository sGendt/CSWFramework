<?php

class DatabaseConnectionManager
{
	private static $databaseConnectionManager = null;
	private $mysqlConnexion = array();

	/**
	 *
	 */

	private function __construct()
	{
		// start bdd connection

		$this->newConnection(CswPref::pref('mysqlBdd'));
	}

	/**
	 *
	 */

	public static function getDatabaseConnectionManager() 
	{
		if(is_null(self::$databaseConnectionManager)) 
		{
			self::$databaseConnectionManager = new DatabaseConnectionManager();
		}

		return self::$databaseConnectionManager;
	}


	/**
     *
	 */

	public function getConnection($bdd = null)
	{
		if($bdd == null)
			$bdd = CswPref::pref('mysqlBdd');
		else
		{
			if(empty($this->mysqlConnection[$bdd]))
			{
				$this->newConnection($bdd);
			}
		}

		return $this->mysqlConnection[$bdd];
	}

	/**
     *
	 */

	public function newConnection($bdd)
	{
		$this->mysqlConnection[$bdd] = new PDO
		(
			'mysql:host=' . CswPref::pref('mysqlHost') . ';dbname=' . $bdd, 
			CswPref::pref('mysqlUser'), 
			CswPref::pref('mysqlPassword'),
			array
			(
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
			)
		);

		$this->mysqlConnection[$bdd]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->mysqlConnection[$bdd]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}
}

?>