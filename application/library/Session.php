<?php

class Session {

	private $SESSION_NAME = 'PHPSESSID';
	private $SESSION_CACHE_KEY_PREFIX = 'phpsess_';
	private $SESSION_CACHE_TIMEOUT = 3600;

	private $COOKIE_PATH = '/';
	private $COOKIE_DOMAIN = '';

	private $SESSION_ID;

	private $SESSION = [];

	private $_cache_type = 'RedisCache';
	private $_cache;

	private $_cookies; 

	public function __construct()
	{
		$sessConfig = config_item('session');

		if( !empty($sessConfig['sessionname']) && is_string($sessConfig['sessionname']) )
		{
			$this->SESSION_NAME = $sessConfig['sessionname'];
		}

		if( is_numeric($sessConfig['cache_timeout']) && $sessConfig['cache_timeout'] >= 0 )
		{
			$this->SESSION_CACHE_TIMEOUT = $sessConfig['cache_timeout'];
		}		

		if( !empty($sessConfig['cache_key_prefix']) && is_string($sessConfig['cache_key_prefix']) )
		{
			$this->SESSION_CACHE_KEY_PREFIX = $sessConfig['cache_key_prefix'];
		}

		if( !empty($sessConfig['cookie_path']) && is_string($sessConfig['cookie_path']) )
		{
			$this->COOKIE_PATH = $sessConfig['cookie_path'];
		}		

		if( !empty($sessConfig['cookie_domain']) && is_string($sessConfig['cookie_domain']) )
		{
			$this->COOKIE_DOMAIN = $sessConfig['cookie_domain'];
		}	

		if(!empty($sessConfig['cache_type']) && is_string($sessConfig['cache_type']))
		{
			$this->_cache_type = $sessConfig['cache_type'];
		}

		$this->_cache = load_class($this->_cache_type);

		if( !$this->_cache OR !($this->_cache instanceof CacheInterface) )
		{
			show_error('Invalid Cache Type', __FILE__, __LINE__);
		}

		$this->_cookie = load_class('cookies');

		$this->start();	
	}


	/**
	 * 启动session
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @return   [type]     [description]
	 */
	public function start()
	{
		if( empty($this->SESSION_ID) )
		{
			$this->SESSION_ID = $this->_cookie->getCookie($this->SESSION_NAME);

			if( empty($this->SESSION_ID) )
			{
				$this->SESSION_ID = $this->createId();
				$this->_cookie->setCookie($this->SESSION_NAME, $this->SESSION_ID, time() + $this->SESSION_CACHE_TIMEOUT, $this->COOKIE_PATH, $this->COOKIE_DOMAIN);
			}

			$this->SESSION = $this->_cache->get($this->SESSION_ID);
			if( empty($this->SESSION) )
			{
				$this->SESSION = [];
			}
		}
	}

	/**
	 * 创建一个session id
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @return   [type]     [description]
	 */
	public function createId()
	{
		return md5(uniqid(rand()).time().rand(11111,99999)).rand(1111,9999);
	}

	/**
	 * 获取一个session值
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key [description]
	 * @return   [type]          [description]
	 */
	public function get($key)
	{
		if( $key && isset($this->SESSION[$key]) )
		{
			return $this->SESSION[$key];
		}
		return false;
	}

	/**
	 * 设置一个session值
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key [description]
	 * @param    [type]     $val [description]
	 */
	public function set($key, $val)
	{
		if( $key && $val )
		{
			$this->SESSION[$key] = $val;
			return $this->_cache->set($this->SESSION_ID, $this->SESSION, $this->SESSION_CACHE_TIMEOUT);
		}
		return false;
	}

	/**
	 * 删除一个session值
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key [description]
	 * @return   [type]          [description]
	 */
	public function delete($key)
	{
		if( $key )
		{
			unset($this->SESSION[$key]);
			return $this->_cache->set($this->SESSION_ID, $this->SESSION, $this->SESSION_CACHE_TIMEOUT);			
		}
		return false;
	}

	/**
	 * 清楚session对象
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @return   [type]     [description]
	 */
	public function clean()
	{

	}
}