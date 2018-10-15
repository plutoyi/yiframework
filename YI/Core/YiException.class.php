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
 * 异常处理类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiException extends Exception {
	/**
	 * 优化异常页面
	 * 
	 * @var string
	 */
	private $TraceAsString;
	private $html;
	
	/**
	 * 构造器
	 * 
	 * @param string $message
	 * @param int $code
	 * @access public
	 */
    public function __construct($message = 'Unknown Error', $code = 0) {
        parent::__construct($message, $code);
    }
	
	/**
	 * 处理异常的回调函数
	 * 回调函数必须为public
	 * 
	 * @param object $e
	 * @access public
	 * @return void
	 */
	public function handleException($e) {
		restore_error_handler();
		restore_exception_handler();
		$message = $e->getMessage();
		if(isset($_SERVER['REQUEST_URI'])){
			$message .= "\nREQUEST_URI=".$_SERVER['REQUEST_URI'];
		}
		if(isset($_SERVER['HTTP_REFERER'])){
			$message .= "\nHTTP_REFERER=".$_SERVER['HTTP_REFERER'];
		}
		$message .= "\n---";
		YiLog::write($e->getMessage());
		$e->displayError();
	}
	
	/**
	 * 输出异常信息
	 *
	 * @return void
	 * @access public
	 */
    public function displayError() {
		$this->html = file_get_contents(YIFW_PATH . 'Tpl/YiException.php');
		$this->TraceAsString = implode("<br/>",array_filter(explode("#" , $this->getTraceAsString())));
		die(sprintf($this->html,  $this->getFile(), $this->getLine(), $this->getCode(),urldecode($this->getMessage()),$this->TraceAsString));
    }
}