<?php

class CswAuth
{
	protected $exchangeBdd = null;

	public function __construct()
	{
		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;
	}

	public function isLoged()
	{
		if(empty($_SESSION['userSession']))
			return false;
		else
			return true;
	}

	public function allow($auth, $redirection = false, $url = null, $strict = false, $ajax = false)
	{
		$query = $this->exchangeBdd->getConnection()->prepare('SELECT slug, auth FROM auth');
		$query->execute();
		$rows = $query->fetchAll();

		foreach($rows as $row)
			$auths[$row->slug] = $row->auth;

		if($strict)
		{
			if($auths[$auth] != $this->user('auth'))
			{
				if($redirection)
				{
					if($ajax)
					{
						$callback = (empty($_GET['callback']) == false) ? $_GET['callback'] : false;
						$cont['datas'] = array('forbidden' => true);
						$json = json_encode($cont);

						if ($callback) 
						    echo $callback . "($json);";
						else 
						    echo $json;

						exit();
					}
					else
						$this->forbidden($url);
				}
				else
					return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			if($auths[$auth] > $this->user('auth'))
			{
				if($redirection)
				{
					if($ajax)
					{
						$callback = (empty($_GET['callback']) == false) ? $_GET['callback'] : false;
						$cont['datas'] = array('forbidden' => true);
						$json = json_encode($cont);

						if ($callback) 
						    echo $callback . "($json);";
						else 
						    echo $json;

						exit();
					}
					else
						$this->forbidden($url);
				}
				else
					return false;
			}
			else
			{
				return true;
			}
		}

	}

	public function user($data)
	{
		if(empty($_SESSION['userSession']->$data))
			return false;
		else
			return $_SESSION['userSession']->$data;
	}

	public function forbidden($url = null)
	{
		if($url == null)
			header('location:'. URL . '/'. CswPref::pref('forbiddenPage'));
		else
			header('location:'. URL . '/'. $url);

		exit();
	}
}

?>
