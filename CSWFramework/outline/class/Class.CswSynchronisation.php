<?php

class CswSynchronisation
{
	private static $databaseConnectionManager = null;
	private $mysqlConnexion = array();

	private $networkDatas = array
	(
		'flag' => 1535465,
		'models' => array
		(
			'user' => array
			(
				array
				(
					'id' => 'sg13sdf1g35d4g153',
					'name' => 'Albert',
					'sexe' => 'm',
					'flag' => 1535465,
					'datetimeOfUpdate' => '2015-05-01 12:30:00'
				)
			),
			'country' => array
			(
				array
				(
					'id' => '153s1gd5v1s3dgsd3',
					'name' => 'US',
					'flag' => 1535465,
					'datetimeOfUpdate' => '2015-05-01 12:30:00'
				)
			),
		)
	);

	private $networkDatas = array();
	private $serverDatas = array();
	private $modelToSyncronized = array();
	

	public function __construct($models = array())
	{
		$this->getDatabaseConnectionManager();
	}


	/**
	 *
	 */

	public static function getDatabaseConnectionManager() 
	{
		if(is_null(self::$databaseConnectionManager)) 
			self::$databaseConnectionManager = $this->newConnection();

		return self::$databaseConnectionManager;
	}


	/**
     *
	 */

	public function getConnection($bdd = null)
	{
		if($bdd == null)
			$bdd = 'default';
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


	/**
     *
	 */

	public function start($networkDatas = array())
	{
		$this->networkDatas = $networkDatas;
		$this->getServerDatas($networkDatas);
	}


	public function getServerDatas($networkDatas)
	{
		foreach($networkDatas['models'] as $model => $values)
			$this->serverDatas[$model] = $this->query($model, $networkDatas['flag']);
	}


	public function compare()
	{
		asort()
	}


	public function compareDiff($arr1, $arr2)
	{

	}


	/**
     *
	 */

	public function query($model, $flag)
	{
		$request = 'SELECT * FROM '. strtolower($model) .' WHERE flag = :flag';
		$query = $this->getConnection()->prepare($request);
		$query->execute
		(
			array
			(
				'flag' => $flag
			)
		);

		return $query->fetchAll();
	}
}

?>
