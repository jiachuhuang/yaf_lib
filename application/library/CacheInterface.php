<?php

interface CacheInterface {

	public function set($key, $val, $ttl = -1);

	public function get($key);

	public function del($key);
}