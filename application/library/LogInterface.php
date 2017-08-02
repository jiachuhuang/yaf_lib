<?php
/**
 * 日志接口
 * @author JC
 */
interface LogInterface {

	public function writeLog( $level = 'error', $message );

	public function flushLog();

	public function cleanLog();
}