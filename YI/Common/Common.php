<?php
// +----------------------------------------------------------------------
// | YiFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011 
// +----------------------------------------------------------------------
// | Licensed (  )
// +----------------------------------------------------------------------
// | Author: Devin.yang <yi.pluto@163.com>
// +----------------------------------------------------------------------
/**
 *+------------------------------------------------------------------------------
 * YiFramework公共函数库
 *+------------------------------------------------------------------------------
 * @category   Yi
 * @package  Common
 * @author   devin.yang <yi.pluto@163.com>
 * @version  $Id$
 *+------------------------------------------------------------------------------
 */	
/**
 * 比较PHP版本
 *
 * @param string $version
 * @return boolean
 */
if ( ! function_exists('is_php')){
	function is_php($version = '5.0.0'){
		static $_is_php;
		$version = (string)$version;
		if(!isset($_is_php[$version])){
			$_is_php[$version] = version_compare(PHP_VERSION , $version , '>') ? true : false;
		}
		return $_is_php[$version];
	}	
}

if ( ! function_exists('is_cli')){
	
	function is_cli(){
		return (PHP_SAPI === 'cli');
	}
}
	
/**
 * 获取用户IP
 * @category   Yi
 * @package  Common
 * @return string
 */
if ( ! function_exists('get_user_ip')){
	function get_user_ip(){
		static $userIp;
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$userIp = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWORDED_FOR'])){
			$userIp = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}elseif(!empty($_SERVER['REMOTE_ADDR'])){
			$userIp = $_SERVER['REMOTE_ADDR'];
		}else{
			$uesrIp = 'unKnown';
		}
		return $userIp;
	}
}

	
/**
 * 获取浏览器版本
 *
 * @category   Yi
 * @package  Common
 * @return array
 */
if ( ! function_exists('get_browser')){
	function get_browser(){
		return	$browser = get_browser(null,true);
	}
}

/**
 *获取$_SERVER['REQUEST_URI']值的通用解决方案
 *
 *@category   Yi
 *@package  Common
 *@return string
 */
if ( ! function_exists('request_uri')){
	function request_uri(){
		if(isset($_SERVER['REQUEST_URI'])){
			$uri = $_SERVER['REQUEST_URI'];
		}else{
			if(isset($_SERVER['argv'])){
				$uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'];
			}else{
				$uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
			}
		}
		return $uri;
	}
}

/**
 * 字符串截取，支持中文和其他编码
 * @param  [string]  $str     [字符串]
 * @param  integer $start   [起始位置]
 * @param  integer $length  [截取长度]
 * @param  string  $charset [字符串编码]
 * @param  boolean $suffix  [是否有省略号]
 * @return [type]           [description]
 */
if ( ! function_exists('m_substr')){
	function m_substr($str, $start=0, $length=15, $charset="utf-8", $suffix=true) {
		if(function_exists("mb_substr")) {
			return mb_substr($str, $start, $length, $charset);
		} elseif(function_exists('iconv_substr')) {
			return iconv_substr($str,$start,$length,$charset);
		}
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
		if($suffix) {
			return $slice."…";
		}
		return $slice;
	}
}
 

/**
 * 输出变量的内容，通常用于调试 浏览器友好的变量输出
 *
 * @category   Yi
 * @package  Common
 * @param mixed $vars 要输出的变量
 * @param string $label
 * @param boolean $return
 */
if ( ! function_exists('dump')){
	function dump($vars , $label = '',$return = false){
		if(ini_get('html_errors')){
			$content = "<pre>\n";
			if($label != ''){
				$content .= "<strong>{$label}:</strong>\n";
			}
			$content .= htmlspecialchars(print_r($vars,true));
			$content .= "\n<pre>\n";
		}else{
			$content = $label . ":\n" . print_r($vars,true);
		}
		if($return){
			return $content;
		}else{
			echo $content;
			return null;
		}
	}
}

/**
 * 扫描目标路径下所有文件和目录
 *
 * @category   Yi
 * @package  Common
 * @param string $path
 * @access public
 * @return array $files
 */	
if ( ! function_exists('scan')){
	function scan($path,$diff = array('.','..','.svn')){
		if(is_dir($path)){
			$files = scandir($path);
			$files = array_diff($files , $diff);
		}else{
			$files = array();
		}
		return $files;
	}
}

/**
 * 记录和统计内存使用量(btye)
 *
 * @category   Yi
 * @package  Common
 * @param string $class 类名
 * @param string $return 要设置的变量的值
 */
if ( ! function_exists('get_memory')){
	function get_memory($start,$end=''){
		static $_memory = array();
		if(!empty($end)){
			if(!isset($_memory[$end])){
				$_memory[$end] = memory_get_usage();
			}
			return number_format(($_memory[$end] - $_memory[$start])/1024) . 'kb';
		}else{
			return $_memory[$start] = memory_get_usage();
		}
	}
}