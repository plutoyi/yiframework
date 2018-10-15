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
 * 核心控制器类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiController extends YiComponent{

	/**
	 * URL参数	Info参数
	 *
	 * @param array $param;
	 * @param array $info
	 */
	//private $_url;
	//private $_param;

	/**
	 * 构造函数.
	 *
	 * @return void
	 * @access public
	 *
	 */
	public function __construct() {
		$this->parsePath();
		$this->runPath();
	}
	
	/**
	 * 解析URL路径
	 *
	 * @return void
	 * @access private
	 */
	private function parsePath(){
		YiRoute::getInstance();
	}
	
	/**
	 * 根据解析的URL获取Controller文件
	 * 根据Controller文件名获取Controller类名并且执行
	 *
	 * @return void
	 * @access private
	 *
	 */
	private function runPath(){
		$controllerFile = Yi::app()->getModulePath() . "/Controller/" . ucwords(Yi::app()->getController()) . 'Controller.php';
		if(is_file($controllerFile)) {
			include($controllerFile);
		} else {
			throw new YiException("错误的请求，找不到Controller文件:[$controllerFile]");
		}
		$class = ucwords(Yi::app()->getController()) . "Controller"; //将控制器名称中的每个单词首字母大写，来当做控制器的类名
		if(!class_exists($class)){
			throw new YiException("错误的请求，找不到Model:[$class]");
		}
		$action = Yi::app()->getAction() . "Action";
		$instance = new $class;
		//判断实例$instance中是否存在$action方法，不存在提示错误
		if(!method_exists($instance,$action)){
			throw new YiException("错误的请求，找不到Action:[$action]");	
		}
		$beforeActionNameAction = "before" . ucwords(Yi::app()->getAction()) . "Action";
		$afterActionNameAction = "after" . ucwords(Yi::app()->getAction()) . "Action";
		method_exists($instance, 'beforeAction') && call_user_func(array(&$instance, 'beforeAction'));
		method_exists($instance, $beforeActionNameAction) && call_user_func(array(&$instance, $beforeActionNameAction));
		call_user_func(array(&$instance, $action));
		method_exists($instance, $afterActionNameAction) && call_user_func(array(&$instance, $afterActionNameAction));
		method_exists($instance, 'afterAction') && call_user_func(array(&$instance, 'afterAction'));
		if(Yi::app()->config('show_page_trace'))$instance->showTrace();
	}

	/**
	 * 判断第一个字符是否为字母
	 *
	 * @param string $char
	 * @return boolean
	 */
	private function isLetter($char) {
		$ascii = ord($char{0});
		return ($ascii >= 65 && $ascii <= 90) || ($ascii >= 97 && $ascii <= 122);
	}
}