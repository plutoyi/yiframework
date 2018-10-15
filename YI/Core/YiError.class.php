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
 * 错误处理类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 class YiError{
 
	private $_error;
	
	/**
	 * 获取错误信息数组
	 * 
	 * @access public
	 * @return array 错误信息数组
	 */
	public function getError(){
		return $this->_error;
	}
	
	/**
	 * 处理错误的回调函数
	 * 回调函数必须为public
	 * 
	 * @param string $code
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @access public
	 * @return void
	 */
	public function handleError($code,$message,$file,$line){
		$trace = debug_backtrace();
		if(count($trace) > 3){
			$trace = array_slice($trace,3);
		}
		$traceString = '';
		foreach($trace as $k => $v){
			if(!isset($v['file'])){$v['file'] = 'unknown';}
			if(!isset($v['line'])){$v['line'] = 0;}
			if(!isset($v['function'])){$v['function'] = 'unknown';}
			$traceString .= "#$k {$v['file']}({$v['line']}):\n";
			if(isset($v['object']) && is_object($v['object'])){
				$traceString .= get_class($v['object']) . '->';
			}
			$traceString .= "{$v['function']}()<br>";
		}
		switch($code){
			case E_WARNING:
				$type = 'PHP warning';
				break;
			case E_NOTICE:
				$type = 'PHP notice';
				break;
			case E_USER_ERROR:
				$type = 'User error';
				break;
			case E_USER_WARNING:
				$type = 'User warning';
				break;
			case E_USER_NOTICE:
				$type = 'User notice';
				break;
			case E_RECOVERABLE_ERROR:
				$type = 'Recoverable error';
				break;
			default:
				$type = 'PHP error';
		}
		$this->_error = array(
			'code'		=>	500,
			'type'		=>	$type,
			'message'	=>	$message,
			'file'		=>	$file,
			'line'		=>	$line,
			'trace'		=>	$traceString,
		);
		if(!headers_sent()){
			header("HTTP/1.0 500 Internal Server Error");
		}
		YiLog::write($traceString);
		$this->displayError($code,$message,$file,$line);
	}
	
	/**
	 * 显示具体错误代码及其行数
	 * 
	 * @param string $file 错误文件
	 * @param string $errorLine 错误行数
	 * @param string $maxLines 显示具体多少行源代码
	 * @access public
	 * @return string 具体错误源代码及其行数
	 */
	public function rendSourceCode($file,$errorLine,$maxLines){
		if(!is_file($file) || ($lines = @file($file) === false) || ($lineCount=count($lines))<=$errorLine ){
			return '';
		}
		$halfLines = (int)($maxLines/2);
		$beginLine = $errorLine-$halfLines > 0 ? $errorLine-$halfLines : 0;
		$endLine = $errorLine+$halfLines < $lineCount ? $errorLine+$halfLines : $lineCount-1;
		$lineNumberWidth = strlen($endLine+1);
		$output='';
		for($i=$beginLine;$i<=$endLine;++$i){
			$isErrorLine = $i=== $errorLine;
			$code=sprintf("<span class=\"ln".($isErrorLine?' error-ln':'')."\">%0{$lineNumberWidth}d</span> %s",$i+1,CHtml::encode(str_replace("\t",'    ',$lines[$i])));
			if(!$isErrorLine)
				$output.=$code;
			else
				$output.='<span class="error">'.$code.'</span>';
		}
		return '<div class="code"><pre>'.$output.'</pre></div>';
	}
	
	/**
	 * 显示错误函数
	 * 
	 * @param string $code
	 * @param string $message
	 * @param string $file
	 * @param int $line
	 * @access public
	 * @return void
	 */
	public function displayError($code,$message,$file,$line){
		$this->html = file_get_contents(YIFW_PATH . 'Tpl/YiError.php');
		die(sprintf($this->html,  $file, $line, $code,urldecode($message),$this->_error['trace']));
	}
	
	/**
	 * 获取HTTP HEADER信息
	 * 
	 * @param int $httpCode httpheader code 信息
	 * @param string $replacement 替代信息
	 * @access public
	 * @return string http header信息
	 */
	public function getHttpHeader($httpCode, $replacement=''){
		$httpCodes = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			118 => 'Connection timed out',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			210 => 'Content Different',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			310 => 'Too many Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested range unsatisfiable',
			417 => 'Expectation failed',
			418 => 'I’m a teapot',
			422 => 'Unprocessable entity',
			423 => 'Locked',
			424 => 'Method failure',
			425 => 'Unordered Collection',
			426 => 'Upgrade Required',
			449 => 'Retry With',
			450 => 'Blocked by Windows Parental Controls',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway ou Proxy Error',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported',
			507 => 'Insufficient storage',
			509 => 'Bandwidth Limit Exceeded',
		);
		if(isset($httpCodes[$httpCode])){
			return $httpCodes[$httpCode];
		}else{
			return $replacement;
		}
	}
 }