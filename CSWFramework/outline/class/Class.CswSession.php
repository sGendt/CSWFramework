<?php

/**
 *
 */

class CswSession
{
	protected $sessionId;
	protected $name;
	protected $path;
	protected $module;
	protected $decode;
	protected $encode;
	protected $cookieParams;
	protected $cacheExpire;
	protected $cacheLimiter;

	/**
	 *
	 */

	public function __construct($start = false)
	{
		if($start == true)
		{
			$this->start();
		}
	}

	/**
	 *
	 */

	public function start()
	{
		return session_start();
	}

	/**
	 *
	 */

	public function unreg($name = null)
	{
		if($name != null) unset($_SESSION[$name]);
	}

	/**
	 *
	 */
		
	public function unregs()
	{
		session_unset();
	}

	/**
	 *
	 */
		
	public function close()
	{
		session_write_close();
	}

	/**
	 *
	 */
		
	public function end()
	{
		return session_destroy();
	}

	/**
	 *
	 */

	public function id($id = null)
	{
		if($id != null) 
		{
			session_id($id);
		}

		$this->sessionId = session_id();

		return $this->sessionId;
	}

	/**
	 *
	 */
		
	public function genId($delete = false)
	{
		return ($delete) ?  session_regenerate_id('delete_old_session') : session_regenerate_id();
	}

	/**
	 *
	 */
		
	public function name($name = null)
	{
		if($name != null) 
		{
			session_name($name);
		}

		$this->name = session_name();

		return $this->name;
	}

	/**
	 *
	 */

	public function path($path = null)
	{
		if($name != null) 
		{
			session_save_path($path);
		}

		$this->path = session_save_path();

		return $this->path;
	}

	/**
	 *
	 */
		
	public function module($module = null)
	{
		if($module != null)
		{
			session_module_name($module);
		}

		$this->module = session_module_name();

		return $this->module;
	}

	/**
	 *
	 */
		
	public function decode($datas = null)
	{
		if($datas != null) return $this->decode = session_decode($datas);
	}

	/**
	 *
	 */

	public function encode($datas = null)
	{
		if($datas != null) return $this->encode = session_encode($datas);
	}

	/**
	 *
	 */
		
	public function setCookieParams($lifetime = 180, $path = '/', $domain = null, $secure = 'false', $httponly = 'true')
	{
		if($domain == null) 
		{
			$domain = $_SERVER['HTTP_HOST'];
		}

		session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
	}

	/**
	 *
	 */
	
	//value: lifetime | path | domain | secure | httponly ***a mettre au finale dans la class cookie je pense***
	public function getCookieParams($value = null)
	{
		$this->cookieParams = session_get_cookie_params();

		return ($value != null) ? $this->cookieParams[$value] : $this->cookieParams;
	}

	/**
	 *
	 */

	public function cacheExpire($expire = null)
	{
		if($expire != null) 
		{
			session_cache_expire($expire);
		}

		$this->cacehExpire = session_cache_expire();

		return $this->cacheExpire;
	}

	/**
	 *
	 */
		
	//limit : public | protected_no_expire | protected | nocache

	public function cacheLimiter($limit = 'public')
	{
		session_cache_limiter($limit);

		$this->cacheLimiter = session_cache_limiter();

		return $this->cacheLimiter;
	}

	/**
	 *
	 */
		
	public function setCallbacks($open, $close, $read, $write, $destroy, $gc)
	{
		return session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
	}
}

?>
