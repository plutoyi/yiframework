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
 * 核心YiRoute类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiRoute{

	public static $routeType;

	public static $uri;
	
	public static $param;

	public static $permittedUriChars;
	
	public static $instance = null;
	
		
	public function __construct(){
		self::$uri = self::parseRequestUri();
		self::setUriString();
		if(self::getRouteType() == 1){
			self::setRouteByGet();
		}else{
			self::setRouteByPathInfo();
		}
		Yi::app()->setController(self::$param['controller']);
		Yi::app()->setAction(self::$param['action']);
		Yi::app()->setModule(self::$param['group']);
		Yi::app()->setModulePath();
	}
	
	public static function getInstance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function getRouteType(){
		if(Yi::app()->config('url_model') == null){
			Yi::app()->config('url_model',1);
		}
		return Yi::app()->config('url_model');
	}
	
	/**
	 * 重组GET 获取相应参数
	 *
	 * @return void
	 * @access private
	 */
	public static function setRouteByGet(){
		if(!isset($_GET['m']) && !empty($_GET['m'])){
			$params['m'] = $_GET['m'];
		}else{
			$params['m'] = null;
		}
		if(!isset($_GET['c']) && !empty($_GET['c'])){
			$params['c'] = $_GET['c'];
		}else{
			$params['c'] = Yi::app()->config('default_controller');
		}
		if(!isset($_GET['a']) && !empty($_GET['a'])){
			$params['a'] = $_GET['a'];
		}else{
			$params['a'] = Yi::app()->config('default_action');
		}
		return self::$param = $params;
	}
	
	/**
	 * 重组PATH_INFO 获取相应参数
	 *
	 * @return void
	 * @access private
	 */
	public static function setRouteByPathInfo(){
		if(($requestFix = Yi::app()->config('requestFix')) == null){
			$requestFix = '/';
		}
		if(($groups = Yi::app()->config('groups')) == null){
			$groups = array();
		}else{
			$groups = explode(",",$groups);
		}
		if(self::$uri == '' || self::$uri == '/'){
			return array();
		}
		$params = explode($requestFix , trim(self::$uri,"/"));
		$varGroup = explode("/",$params[0]);
		$group = lcfirst(array_shift($varGroup));		
		if(in_array($group,$groups)){
			if(!empty($varGroup)){
				$params[0] = implode("/",$varGroup);
				$params['group'] = $group;
			}else{
				array_shift($params);
				$params['group'] = $group;
			}
		}
		if(isset($params['group'])){
			self::$param['group'] = $params['group'];
			unset($params['group']);
		}else{
			self::$param['group'] = null;
		}
		if(count($params) % 2 != 1){array_push($params,'');}
		self::$param['controller'] = isset($params[0])&&(!empty($params[0])) ? $params[0] : Yi::app()->config('default_controller');
		self::$param['action'] = isset($params[1])&&(!empty($params[1])) ? $params[1] : Yi::app()->config('default_action');
		//参数重组
		$count = count($params);
		for ($i=3;$i<=$count-1;$i++) {
			self::$param[@$params[$i]] = @$params[++$i];
		}
		self::$param = $_GET = array_merge(self::$param,$_GET);
		return self::$param;
	} 
	
	/**
	 * 解析URI路径
	 *
	 * @return void
	 * @access public
	 */
	public static function parseRequestUri(){
		if ( ! isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])){
			return '';
		}
		$uri = parse_url($_SERVER['REQUEST_URI']);
		$query = isset($uri['query']) ? $uri['query'] : '';
		$uri = isset($uri['path']) ? rawurldecode($uri['path']) : '';
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0){
			$uri = (string) substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0){
			$uri = (string) substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}
		if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0){
			$query = explode('?', $query, 2);
			$uri = rawurldecode($query[0]);
			$_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
		}else{
			$_SERVER['QUERY_STRING'] = $query;
		}
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		if ($uri === '/' OR $uri === ''){
			return '/';
		}
		return $uri;
	}
	
	public static function setUriString(){
		if(self::$uri != ''){
			if($slen = strlen(Yi::app()->config('requestSuffix'))){
				if(substr(self::$uri,-$slen) == Yi::app()->config('requestSuffix')){
					self::$uri = substr(self::$uri,0,-$slen);
				}
			}
		}
		return self::$uri;
	}
	
	/**
	 * 过滤URL路径
	 *
	 * @return void
	 * @access public
	 */
	public static function filterUri($str){
		if (!empty($str) && ! empty(self::$permittedUriChars) && ! preg_match('/^['.self::$permittedUriChars.']+$/iu', $str)){
			throw new YiException('The URI you submitted has disallowed characters.');
		}
		return str_replace(
			array('$',     '(',     ')',     '%28',   '%29'),	// Bad
			array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;'),	// Good
			$str
		);
	} 
	
	/**
	 * 生成url
	 *
	 * @access public
	 * @return string 
	 */
	public static function createUrl($args){
		$n = count($args);
		if($n == 1){
			$controller = Yi::app()->getController();
			$action = $args[0];
		}else if($n == 2){
			$controller = Yi::app()->getController();
			$action = $args[0];
			$params = is_array($args[1]) ? $args[1] : (array)$args[1];
		}else if($n == 3){
			$controller = $args[0];
			$action = $args[1];
			$params = is_array($args[2]) ? $args[2] : (array)$args[2];
		}else if($n == 4){
			$module = $args[0];
			$controller = $args[1];
			$action = $args[2];
			$params = is_array($args[3]) ? $args[3] : (array)$args[3];
		}
		$controller = isset($controller) ? $controller : Yi::app()->config('default_controller');
		$action = isset($action) ? $action :Yi::app()->config('default_action');
		$params = isset($params) ? $params : array();
		if(Yi::app()->config('url_model') == 2){
			$path = self::createUrlByPathInfo($module,$controller,$action,$params);
		}
		if(Yi::app()->config('url_model') == 1){
			$path = self::createUrlByGet($module,$controller,$action,$params); 
		}
		return isset($path) ? $path : ''; 
	} 
	
	/**
	 * 生成PathInfo格式url
	 *
	 * @param string $controller	url对应的controller名称
	 * @param string $action	url对应的action名称
	 * @param array $params	url对应的参数数组
	 * @access public
	 * @return string 生成的url
	 */
	public static function createUrlByPathInfo($module=null,$controller,$action,$params){
		$pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['SCRIPT_NAME'].'/': dirname($_SERVER['SCRIPT_NAME']) . '/';
		if(($requestFix = Yi::app()->config('requestFix')) == null){
			$requestFix = '/';
		}
		$path = $controller . $requestFix . $action;
		if($module != null){
			$path = $module . $requestFix . $path;
		}
		foreach($params as $key => $value){
			$path .= $requestFix . $key . $requestFix . $value;
		}
		return $path = $pathinfo . $path . Yi::app()->config('requestSuffix');
	}
	
	/**
	 * 生成Get格式url
	 *
	 * @param string $controller	url对应的controller名称
	 * @param string $action	url对应的action名称
	 * @param array $params	url对应的参数数组
	 * @access public
	 * @return string 生成的url
	 */
	public static function createUrlByGet($module,$controller,$action,$params){
		$pathinfo = (Yi::app()->config('urlrewrite')) ? dirname($_SERVER['SCRIPT_NAME']) . '/' : $_SERVER['SCRIPT_NAME'];
		$params = array_merge(array('m'=>$module,'c'=>$controller,'a'=>$action),$params);
		if($module == null){
			unset($params['m']);
		}
		return $path = $pathinfo .  '?' . http_build_query($params); 
	}
	
	/**
	 * URL重定向
	 *
	 * @param string $url	跳转url地址
	 * @param int $time	跳转时间间隔
	 * @param string $msg	跳转提示信息
	 * @access public
	 * @return void
	 */
	public function redirect($url,$time=0,$msg=''){
		//多行URL地址支持
		$url = str_replace(array("\n", "\r"), '', $url);
		if (!headers_sent()) {
			// redirect
			if(0===$time) {
				header("Location: ".$url);
			}else {
				header("refresh:{$time};url={$url}");
				echo($msg);
			}
			exit();
		} else {
			$str	= "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
			if($time!=0)
				$str   .=   $msg;
			exit($str);
		}
	}
}
