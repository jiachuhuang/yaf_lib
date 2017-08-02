<?php
/**
 * 封装redis扩展操作
 * @author JC
 */
class RedisProxy {

	private $_config = [];
	private $_conn;

	private $_expire_default_ttl = 3600;

	public function __construct()
	{
		$redisConfig = config_item('redis');

		$this->_config = $redisConfig->toArray();
		
		$this->connect();
	}

	/**
	 * 链接redis
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @return   [type]     [description]
	 */
	public function connect()
	{
		try {
			$this->_conn;

			if( $this->_conn )
			{
				return TRUE;
			}

			if( !isset($this->_config['host']) )
			{
				show_error('Invalid Redis Host', __FILE__, __LINE__);
			}

			$this->_config['port'] = isset($this->_config['port'])? $this->_config['port']: '6379';

			$this->_config['timeout'] = isset($this->_config['timeout'])? $this->_config['timeout']: '0.5';

			$this->_conn = new Redis();

			if( isset($this->_config['pconnect']) && $this->_config['pconnect'] )
			{
				$this->_conn->pconnect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
			}
			else
			{
				$this->_conn->connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
			}

			if( isset($this->_config['passwd']) && $this->_config['passwd'] )
			{
				$auth = $this->_conn->auth($this->_config['passwd']);
				if( !$auth )
				{
					throw new Exception('Error Redis Password');
				}
			}

			if( isset($this->_config['database']) )
			{
				$this->_conn->select($this->_config['database']);
			}
		} catch ( Exception $e ) {

			show_error($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	/**
	 * redis方法调用
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @param    [type]     $method [description]
	 * @param    array      $args   [description]
	 * @return   [type]             [description]
	 */
	public function __call($method, $args = array())
	{
		$reconnect = FALSE;
		while(TRUE) 
		{
			try {

				$result = call_user_func_array(array($this->_conn, $method), $args);

			} catch (RedisException $e) {
				if($reconnect)	
				{
					throw $e;
				}
				$this->_conn->close();
				unset($this->_conn);
				$this->connect();
				$reconnect = TRUE;
				show_error($e->getMessage(), $e->getFile(), $e->getLine());
				continue;
			}

			return $result;
		}
	}

	/**
	 * 常用方法
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key     [description]
	 * @param    [type]     $val     [description]
	 * @param    integer    $timeout 过期时间
	 * @return
	 */
	public function setCache($key, $val, $timeout = -1)
	{
		try {

			if( empty($val) OR empty($key) OR !(is_string($key) OR is_numeric($key)) )
			{
				return FALSE;
			}

			if( $timeout < 0) 
			{
				$timeout = $this->_expire_default_ttl;
			}

			return $this->_conn->set($key, serialize($val), $timeout);

		} catch(RedisException $e) {

			show_error($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	/**
	 * 封装redis get方法
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key [description]
	 * @return   [type]          [description]
	 */
	public function getCache($key)
	{
		try {

			if( empty($key) OR !(is_string($key) OR is_numeric($key)) )
			{
				return FALSE;
			}

			return unserialize($this->_conn->get($key));

		} catch(RedisException $e) {

			show_error($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	/**
	 * 封装redis delete方法
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key [description]
	 * @return   [type]          [description]
	 */
	public function delCache($key)
	{
		try {

			if( empty($key) OR !(is_string($key) OR is_numeric($key)) )
			{
				return FALSE;
			}

			return $this->_conn->del($key);

		} catch(RedisException $e) {

			show_error($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}

	/**
	 * 设置过期时间
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key     [description]
	 * @param    integer    $timeout [description]
	 */
	public function setExpire($key, $timeout = -1)
	{
		try {

			if( empty($key) OR !(is_string($key) OR is_numeric($key)) )
			{
				return FALSE;
			}

			if( $timeout < 0) 
			{
				$timeout = $this->_expire_default_ttl;
			}

			$this->_conn->expire($key, $timeout);

		} catch(RedisException $e) {

			show_error($e->getMessage(), $e->getFile(), $e->getLine());
		}
	}
}
