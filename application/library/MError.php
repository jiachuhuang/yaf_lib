<?php
/**
 * 错误输出类
 * @author JC
 */
class MError {

	/**
	 * 记录错误到日志
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @param    [type]     $errno   [description]
	 * @param    [type]     $errstr  [description]
	 * @param    [type]     $errfile [description]
	 * @param    [type]     $errline [description]
	 * @return   [type]              [description]
	 */
	public function logException($errno, $errstr, $errfile, $errline)
	{
		$msg  = '';
		$msg .= 'File: ' . $errfile . ' ';
		$msg .= 'Line: ' . $errline . ' ';
		$msg .= 'Info: ' . $errstr  . ' ';
		$msg .= 'Code: ' . $errno   . ' ';

		log_message('error', $msg);
	}

	/**
	 * 返回错误给客户端
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @param    [type]     $errno   [description]
	 * @param    [type]     $errstr  [description]
	 * @param    [type]     $errfile [description]
	 * @param    [type]     $errline [description]
	 * @return   [type]              [description]
	 */
	public function show($errno, $errstr, $errfile, $errline)
	{
		if( ! DISPLAY_ERRORS )
		{
			return FALSE;
		}

		$msg  = '';
		$msg .= '<b>File:</b> ' . $errfile . '<br>';
		$msg .= '<b>Line:</b> ' . $errline . '<br>';
		$msg .= '<b>Info:</b> ' . $errstr  . '<br>';
		$msg .= '<b>Code:</b> ' . $errno   . '<br>';		

		header('HTTP/1.1 500 Internal Server Error', TRUE, 500);

		echo $msg;
		exit;
	}
}