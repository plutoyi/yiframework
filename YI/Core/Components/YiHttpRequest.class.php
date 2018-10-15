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
 * HttpRequest控制基类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 Class YiHttpRequest{
	/**
	 * 设置参数并进行过滤
	 *
	 * @param array $request
	 */

	public function __construct(){
		
	}
	
	public function getParam($name,$defaultValue = null){
		return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}
	
	public function getQuery($name,$defaultValue=null){
		return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}
	
	public function getQueryString(){
		return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING']:'';
	}
	
	public function getPost($name,$defaultValue=null){
		return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
	}
	
	public function getIsSecureConnection(){
		return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS']==1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
	}
	
	public function getRequestType(){
		if(isset($_POST['_method'])){
			return strtoupper($_POST['_method']);
		}
		if(isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])){
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		}
		return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
	}
	
	public function getUrl(){
		$pageurl = 'http';
		if(isset($_SERVER['https']) && $_SERVER['https'] == "on"){
			$pageurl .= 's';
		}
		$pageurl .= "://";
		if($_SERVER['SERVER_PORT'] != 80){
			$pageurl .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		}else {
			$pageurl .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageurl;
	}
	
	public function getIsAjaxRequest(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}
 }