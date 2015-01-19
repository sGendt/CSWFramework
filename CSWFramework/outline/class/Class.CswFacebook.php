<?php

/**
 *
 */

class CswFacebook
{
	private $appId = null;
	private $secret = null;
	public $accesToken = null;
	private $uid = null;
	private $facebook = null;
	private $user = null;
	public $loginUrl = null;
	public $logoutUrl = null;
	private $pages = null;
	
	
	/**
	 *
	 */

	public function __construct($appId, $secret, $token = null, $uid = null)
	{
		$this->appId = $appId;
		$this->secret = $secret;
		$this->accessToken = $token;
		$this->uid = $uid;
	}


	/**
	 *
	 */

	public function connect()
	{
		$this->facebook = new Facebook
		(
			array
			(
				'appId' => $this->appId,
				'secret' => $this->secret,
				'cookie' => false
			)
		);

		$this->user = $this->facebook->getUser();

		if(empty($this->user) == true)
		{
			$paramsLogin = array
			(
				'scope' => 'email, manage_pages, publish_stream',
				'locale' => 'fr_FR',
				'display' => 'popup'
			);

			try
			{
				$this->loginUrl = $this->facebook->getLoginUrl($paramsLogin);

				return false;

			}
			catch(Exception $e)
			{
				echo $e;
			}

		}
		else
		{
			try
			{
				$this->uid = $this->user;
				$this->user = $this->facebook->api('/me');
				$this->pages = $this->facebook->api('/me/accounts');
				$this->accessToken = $this->facebook->getAccessToken();

				return $this->user;
			}
			catch(Exception $e)
			{
				echo $e;
			}
		}
	}

	/**
	 *
	 */

	public function setMessage($message = 'message', $name = 'name', $caption = 'caption', $link = null, $description = 'description', $picture = 'http://www.unsimpleclic.com/wp-content/uploads/2011/06/110615_facebook_00.jpg', $wall = 'me', $actions = array())
	{
		$defaults = array('name' => 'send post', 'link' => 'http://'.$_SERVER['SERVER_NAME'].'/');
		$actions = array_merge($defaults, $actions);

		if(empty($link) == true)
			$link = 'http://'.$_SERVER['SERVER_NAME'].'/';
		

		$params = array
		(
			'access_token' => $this->accesToken,
			'message' => $message,
			'name' => $name,
			'caption' => $caption,
			'link' => $link,
			'description' => $description,
			'picture' => $picture,
			'actions' => array($actions)
		);

		try
		{
			return $this->facebook->api('/'.$wall.'/feed', 'post' ,$params);
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}


	/**
	 *
	 */

	public function getPicture($size = null)
	{
		if($size != null)
			$size = '_'.$size;

		$fql = 'select pic'.$size.' from user where uid = '.$this->uid;

		$params = array
		(
			'method' => 'fql.query',
			'query' => $fql,
			'callback' => ''
		);
		
		try
		{
			$rows = $this->facebook->api($params);
			return $rows[0]['pic'.$size];
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}


	/**
	 *
	 */

	public function getFriends()
	{
		try
		{
		 	return $this->facebook->api('/me/friends');
		}
		catch(Exception $e)
		{
			echo $e;
		}

	}

	/**
	 *
	 */

	public function getLogoutUrl($returnUrl)
	{
		$params = array('next' => $returnUrl);
		return $this->facebook->getLogoutUrl($params);
	}


	/**
	 *
	 */

	public function getLikes()
	{
		try
		{
			return $this->facebook->api('/me/likes');
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}


	/**
	 *
	 */

	public function getMessages($limit = 1)
	{
		/**
		 * posts can take params: since, until, limit and created_time,the last argument take unixtime value
		 */

		try
		{
			return $this->facebook->api('/me/posts', array('limit' => $limit));
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}


	/**
	 *
	 */

	public function getPages()
	{
		return $this->pages['data'];
	}


	/**
	 *
	 */

	public function getAccesToken()
	{
		return $this->facebook->getAccessToken();
	}
}

?>
