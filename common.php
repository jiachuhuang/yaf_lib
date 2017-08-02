<?php

/**
 * 封装加载library下的类
 * @param string $name
 * @return Object
 */
if( ! function_exists('load_class') ) 
{
	function load_class( $name ) 
	{
		if( empty($name) OR !is_string($name) ) 
		{
			return FALSE;
		}

		$obj = global_item( 'LIB_' . $name );
		
		if( $obj === null OR $obj === FALSE )
		{
			$obj = new $name();

			global_item( 'LIB_' . $name , $obj );
		}

		return $obj;
	}
}

/**
 * 封装 Yaf_Registry 的Get Set方法
 * @param string|int $name
 * @param $val 可选
 * @return 
 */
if( ! function_exists('global_item') ) 
{
	function global_item( $name, $val = null ) 
	{
		if( empty($name) OR ! (is_string($name) || is_numeric($name)) )
		{
			return FALSE;
		}

		if( $val === null )
		{
			return Yaf_Registry::get($name);
		}
		else
		{
			return Yaf_Registry::set($name, $val);
		}
	}
}

/**
 * 写日志方法
 * @param string $level
 * @param string $message
 * @return
 */
if( ! function_exists('log_message') ) 
{
	function log_message( $level = 'error', $message ) 
	{
		$logConfig = config_item('log');

		$logDriver = isset($logConfig['type'])? $logConfig['type']: 'FileLog';

		$log = load_class($logDriver);

		if( empty($log) || ! ($log instanceof LogInterface) )
		{
			return FLASE;
		}

		$log->writeLog( $level, $message );
	}
}

/**
 * 刷新日志缓存
 * @return
 */
if( ! function_exists('flush_log') )
{
	function flush_log()
	{
		$logConfig = config_item('log');

		$logDriver = isset($logConfig['type'])? $logConfig['type']: 'FileLog';

		$log = load_class($logDriver);

		if( empty($log) || ! ($log instanceof LogInterface) )
		{
			return FLASE;
		}

		$log->flushLog();		
	}
}

/**
 * 封装获取配置项
 * @param string $name
 * @return
 */
if( ! function_exists('config_item') ) 
{
	function config_item( $name ) 
	{
		if( empty($name) OR !is_string($name) )
		{
			return FALSE;
		}	

		$config = global_item('config');

		return 	isset($config[$name])? $config[$name]: FALSE;
	}
}

/**
 * 判断文件（目录）是否可写
 * @param string $file
 */
if ( ! function_exists('is_really_writable') )
{
	function is_really_writable($file)
	{
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

/**
 * 错误输出函数
 * @return
 */
if( ! function_exists('show_error') )
{
	function show_error($errstr, $errfile = '', $errline = '')
	{
		error_handler(E_ERROR, $errstr, $errfile, $errline);
	}
}

/**
 * 请求完后执行函数
 * @return 
 */
if( ! function_exists('shutdown_function') ) 
{
	function shutdown_function()
	{
		flush_log();
	}
}

/**
 * 错误处理函数
 * @return
 */
if( ! function_exists('error_handler') ) 
{
	function error_handler($errno, $errstr, $errfile, $errline)
	{
		$error = load_class('mError');

		$error->logException($errno, $errstr, $errfile, $errline);

		$error->show($errno, $errstr, $errfile, $errline);
	}
}

/**
 * 异常处理函数
 * @return 
 */
if( ! function_exists('exception_handler') ) 
{
	function exception_handler($e)
	{
		$error = load_class('mError');

		$error->logException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());

		$error->show($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
	}
}


























