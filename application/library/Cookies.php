<?php

class Cookies {

	/**
	 * 设置cookie值
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $key           [description]
	 * @param    [type]     $val           [description]
	 * @param    [type]     $expireAt      [description]
	 * @param    [type]     $cookie_path   [description]
	 * @param    [type]     $cookie_domain [description]
	 */
	public function setCookie($name, $val, $expireAt = -1, $cookie_path = '/', $cookie_domain = '')
	{
		if( $name && $val )
		{
			setcookie($name, $val, $expireAt, $cookie_path, $cookie_domain);
			return true;
		}
		return false;
	}

	/**
	 * 获取cookie
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $name [description]
	 * @return   [type]           [description]
	 */
	public function getCookie($name)
	{
		if( $name )
		{
			return isset($_COOKIE[$name])? $_COOKIE[$name]: false;
		}
		return false;
	}

	/**
	 * 删除cookie
	 * @Author   JC
	 * @DateTime 2017-07-28
	 * @param    [type]     $name [description]
	 * @return   [type]           [description]
	 */
	public function delCookie($name)
	{
		if( $name )
		{
			setcookie($name, '', -1);
			return true;
		}
		return false;
	}
}