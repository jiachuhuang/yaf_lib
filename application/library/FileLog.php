<?php

/**
 * 文件日志
 * @author JC
 */
class FileLog implements LogInterface
{
	private $_enable_log = TRUE;
	private $_threshold = 1;
	private $_log_path;
	private $_date_fmt = 'Y-m-d H:i:s';
	private $_log_queue = [];
	private $_log_queue_limit = 10;
	private $_levels =  array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

	/**
	 * 文件日志构造函数
	 * @Author   JC
	 * @DateTime 2017-07-27
	 */
	public function __construct()
	{
		$logConfig = config_item('log');

		$this->_log_path = isset($logConfig['log_path'])? rtrim($logConfig['log_path'], '/\\'): APPLICATION_PATH . '/tmp/logs';

		if( !is_dir($this->_log_path) || !is_really_writable($this->_log_path) ) 
		{
			$this->_enable_log = FALSE;
		}

		is_numeric($logConfig['threshold']) && $this->_threshold = is_numeric($logConfig['threshold']);

		if( is_numeric($logConfig['queue_limit']) && $logConfig['queue_limit'] > 0)
		{
			$this->_log_queue_limit = $logConfig['queue_limit'];
		}
	}

	/**
	 * 写日志文件
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @param    string     $level   日志等级
	 * @param    string     $message 日志信息
	 * @return
	 */
	public function writeLog( $level = 'error', $message )
	{
		if( $this->_enable_log === FALSE )
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if( !isset($this->_levels[$level]) || $this->_levels[$level] > $this->_threshold )
		{
			return FLASE;
		}

		if(count($this->_log_queue) >= $this->_log_queue_limit) 
		{
			$this->flushLog();
		}

		$message = $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$message;

		$this->_log_queue[] = $message;
	}

	/**
	 * 刷新日志队列的日志到文件
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @return
	 */
	public function flushLog()
	{
		if($this->_enable_log === FALSE)
		{
			return FALSE;
		}

		if(empty($this->_log_queue))
		{
			return TRUE;
		}

		$filepath = $this->_log_path . DIRECTORY_SEPARATOR . 'log-' . date('Ymd', time()) . '.log';

		if( !$fp = @fopen($filepath, 'ab') )
		{
			return FALSE;
		}

		$msg = implode(PHP_EOL, $this->_log_queue);

		$this->_log_queue = [];

		flock($fp, LOCK_EX);
		fwrite($fp, $msg);
		flock($fp, LOCK_UN);
		fclose($fp);

		@chmod($filepath, 0666);

		return TRUE;
	}

	/**
	 * 清空日志队列
	 * @Author   JC
	 * @DateTime 2017-07-27
	 * @return 
	 */
	public function cleanLog()
	{
		$this->_log_queue = [];
	}
}