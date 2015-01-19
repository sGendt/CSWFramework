<?php

Class User extends CswModel
{
	protected $id 					= array('required' => false, 'value' => null);
	protected $login 				= array('required' => true, 'value' => null);
	protected $password 			= array('required' => true, 'value' => null);
	protected $authId 				= array('required' => true, 'value' => null);

	protected $userSession 			= null;
	protected $exchangeBdd 			= null;
	protected $escapedProperties	= array('escapedProperties', 'userSession', 'exchangeBdd');


	public function __construct()
	{
		global $exchangeBdd;
		$this->exchangeBdd = $exchangeBdd;
	}

	public function login($login = null, $password = null)
	{

		$login = (empty($login)) ? CswVar::v('login') : $login;
		$password = (empty($password)) ? CswString::ashString(CswVar::v('password')) : CswString::ashString($password);

		$values = array
		(
			'login' => $login,
			'password' => $password
		);

		$request = 'SELECT 
						user.*, 
						auth.auth, 
						auth.slug 
					FROM 
						user, 
						auth 
					WHERE 
						user.authId = auth.id AND 
						user.login = :login AND 
						user.password = :password';

		try
		{
			$query = $this->exchangeBdd->getConnection()->prepare($request);

			$query->execute($values);

			$rows = $query->fetchAll();
			$nbrows = count($rows);

			if($nbrows > 0)
			{
				$_SESSION['userSession'] = $rows[0];
				
				$this->returnState
				(
					ALERTSUCCES, 
					SUCCESLOGUSER
				);
			}
			else
			{
				$_SESSION['userSession'] = false;

				$this->returnState
				(
					ALERTWARNING, 
					NOLOGUSER
				);
			}
		}
		catch(Exception $e)
		{
			$this->returnState
			(
				ALERTWARNING, 
				ERRORLOGUSER
			);
		}
	}

	public function logout()
	{
		$_SESSION['userSession'] = false;
	}


	public function userExist($mail = null)
	{
		$mail = ($mail) ? $mail : $this->getMail();
		$values = array
		(
			'mail' => $mail
		);

		$request = 'SELECT * FROM ' . strtolower(get_class($this)) . ' WHERE mail = :mail';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);
		$rows = $query->fetchAll();

		$nbrows = count($rows);

		if($nbrows > 0)
			return true;
		else
			return false;
	}

	public function exist($login = null)
	{
		$login = ($login) ? $login : $this->getLogin();
		$values = array
		(
			'login' => $login
		);

		$request = 'SELECT * FROM ' . strtolower(get_class($this)) . ' WHERE login = :login';

		$query = $this->exchangeBdd->getConnection()->prepare($request);

		$query->execute($values);
		$rows = $query->fetchAll();

		$nbrows = count($rows);

		if($nbrows > 0)
			return true;
		else
			return false;
	}

	public function password()
	{
		$mail = CswVar::v('mail');

		if($this->userExist($mail))
		{
			$newPassword = CswString::genPassword(10);

			$values = array
			(
				'password' => CswString::ashString($newPassword),
				'mail' => $mail
			);

			$request = 'UPDATE user SET password = :password WHERE mail = :mail';
			$query = $this->exchangeBdd->getConnection()->prepare($request);
			$query->execute($values);

			$cswMail = new CswMail();
			$cswMail->send(array($mail), 'Demande de mot de passe', 'Votre nouveau mot de passe est: '. $newPassword);

			$this->returnState
			(
				ALERTSUCCES, 
				SUCCESNEWPASSWORD
			);

		}
		else
		{
			$this->returnState
			(
				ALERTWARNING, 
				ERRORNOUSER
			);
		}
	}
}

?>
