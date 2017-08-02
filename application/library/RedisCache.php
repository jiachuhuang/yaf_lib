<?php

class RedisCache implements CacheInterface{

	private $_redis;

	public function __construct()
	{
		if( !$this->_redis )
		{
			$this->_redis = load_class('RedisProxy');
		}
	}

	public function set($key, $val, $ttl = -1)
	{
		return $this->_redis->setCache($key, $val, $ttl);
	}

	public function get($key)
	{
		return $this->_redis->getCache($key);
	}

	public function del($key)
	{
		return $this->_redis->delCache($key);
	}
}