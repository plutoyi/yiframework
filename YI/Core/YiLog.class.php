<?php
// +----------------------------------------------------------------------
// | YiFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://yisong.sinaapp.com
// +----------------------------------------------------------------------
// | Licensed
// +----------------------------------------------------------------------
// | Author: Devin.yang<yi.pluto@163.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 日志处理类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 class YiLog{

	// 日志级别 从上到下，由低到高
    const EMERG   = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT   = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT    = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR     = 'ERR';  // 一般错误: 一般性错误
    const WARN    = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE  = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO    = 'INFO';  // 信息: 程序输出信息
    const DEBUG   = 'DEBUG';  // 调试: 调试信息
    const SQL     = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效
 
	//日记记录方式
	const SYSTEM = 0;
	const MAIL = 1;
	const TCP = 2;
	const FILE = 3;
	
	//		
	static $log = array();

	// 日期格式
    static $format =  '[ c ]';
	
	public function __construct(){

	}
	
	/**
     +----------------------------------------------------------
     * 记录日志 并且会过滤未经设置的级别
     *
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     +----------------------------------------------------------
     */
	static function record($message,$level = self::ERR,$record = false){
		if($record || in_array($level,Yi::app()->config('log_record_level'))){
			$now = date(self::$format);
			self::$log[] = "{$now}{$level}:{$message}\r\n";
		}
	}
	
	/**
     +----------------------------------------------------------
     * 日志保存
     *
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     * @return void
     +----------------------------------------------------------
     */
	static function save($type = self::FILE,$destination='',$extra=''){
		if(empty($destination)){
			$destination = Yi::app()->getModulePath() . '/Runtime/' . data('y_m_d') . ".log";
		}
		if(self::FILE == $type){
			if(is_file($destination) && floor(Yi::app()->config('log_file_size')) <= filesize($destination)){
				rename($destination,dirname($destination) . '/' . time() . '-' . basename($destination));
			}
			error_log(implode("",self::$log) , $type , $destination , $extra);
			self::$log = array();
		}
	}
	 
	//日志直接写入
	static function write($message,$level = self::ERR,$type=self::FILE,$destination = '',$extra=''){
		$now = date("y-m-d H:i:s");
		if(empty($destination)){
			if(!is_dir($destinationDir = Yi::app()->getModulePath() . '/Runtime/' . date("Y-m-d"))){
				mkdir($destinationDir,0777);
			}
			$destination = $destinationDir . '/' . date('Y_m_d') . '.log';
		}
		if(self::FILE == $type){
			if(is_file($destination) && floor(Yi::app()->config('log_file_size')) <= filesize($destination)){
				rename($destination,dirname($destination) . '/' . time() . '-' . basename($destination));
			}
			error_log("{$now} {$level}: {$message}\r\n", $type,$destination,$extra );
		}
	}
	
	public function getMemoryUsage(){
		if(function_exists(memory_get_usage)){
			return memory_get_usage();
		}else{
			$pid = getmypid(); 
			$output = array();
			if(strncmp(PHP_OS,'WIN',3)===0){ 
				exec('tasklist /FI "PID eq ' . $pid . '" /FO LIST', $output); 
				return preg_replace('/[^0-9]/', '', $output[5]) * 1024; 
			} else { 
				exec("ps -eo%mem,rss,pid | grep $pid", $output); 
				$output = explode(" ", $output[0]); 
				return $output[1] * 1024; 
			} 
		}
	}
	
	public function getExecutionTime(){
		return microtime(true) - YI_BEGIN_TIME;
	}
 }