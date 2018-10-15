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
 * 核心控制器基类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiBaseController{

	/**
	 * 系统变量
	 *
	 * @var Template
	 */
	//protected $_vars = array();
	/**
	 * 模板对象
	 *
	 * @var Template
	 */
	protected $_tpl;
	/**
	 * 页面trace变量
	 *
	 * @var array
	 */
	protected $_trace = array(); 

	/**
	 * 构造函数 初始化
	 *
	 * @access public
	 */
	public function __construct() {
		$this->_tpl = $this->getTemplate();
	}	

	/**
	 * 设置模板变量
	 *
	 * @param string $key   模板页面变量
	 * @param mixed $value  对应程序中的变量
	 * @access public
	 * @return void
	 */
	public final function assign() {
		$key = @func_get_arg(0);
		$value = @func_get_arg(1);
		$this->_tpl->assign($key, $value);
		return $this;
	}

	/**
	 * 获取模板的值
	 *
	 * @param string $key
	 * @return mixed
	 */
	public final function value($key) {
		return $this->_tpl->getValue($key);
	}
	
	/**
	 * 返回所有action名称
	 *
	 * @return string
	 * @access public
	 */
	public final function getAllActions(){
		return get_class_methods($this);
	}

	/**
	 * 加载模板页
	 *
	 * @param string $view	模板名称
	 * @return void
	 * @access public
	 */

	public final function V($view = null){
		return Yi::app()->view($view);
	}

	/**
	 * 获取Model对象
	 *
	 * @param string $model
	 * @return object
	 */
	public final function M($model) {
		return Yi::app()->model($model);
	}


	/**
	 * 获取插件
	 *
	 * @param string $plugin
	 * @return object
	 */
	public final function P($plugin) {
		return Yi::app()->plugin($plugin);
	}


	/**
	 * 加载JavaScript文件
	 *
	 * @param string $js JavaScript文件路径
	 * @return void
	 * @access public
	 * @static
	 */
	public final function js($js) {
		$html = '<script type="text/javascript" src="%s"></script>';
		return sprintf($html, Yi::app()->getModulePath() . '/Media/js/' . $js) . "\n";
	}

	/**
	 * 加载CSS文件
	 *
	 * @param string $css CSS文件路径
	 * @return void
	 * @access public
	 * @static
	 */
	public final function css($css) {
		$html = '<link type="text/css" rel="stylesheet" href="%s" />';
		return sprintf($html, Yi::app()->getModulePath() . '/Media/css/' . $css) . "\n";
	}

	/**
	 * 获取某个文件的路径
	 *
	 * @param string $file 文件路径
	 * @return void
	 * @access public
	 * @static
	 */
	public final function file($file) {
		$file = str_replace(array('\\', '//', '\\\\'), '/', $file);
		return ROOT_PATH . $file;
	}

	/**
	 * 获取缓存实例
	 *
	 * @return object
	 * @access public
	 * @static
	 */
	 public final function getCache(){
		return CacheDriver::getInstance();
	 }

	 /**
	 * 获取数据库操作实例
	 *
	 * @return object
	 * @access public
	 * @static
	 */
	  public final function getDatabase(){
		return DatabaseDriver::getInstance();
	 }

	 /**
	 * 获取模板引擎实例
	 *
	 * @return object
	 * @access public
	 * @static
	 */
	public final function getTemplate() {
		return YiTemplate::getInstance();
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
		YiRoute::redirect($url,$time=0,$msg='');
	}
	
	/**
	 * 生成url
	 *
	 * @access public
	 * @return string 
	 */
	public function createUrl(){
		$args = func_get_args();
		return YiRoute::createUrl($args);
	}

	/**
	 * 显示页面Trace信息
	 *
	 * @access public
	 */
    public function showTrace(){
        // 显示页面Trace信息 读取Trace定义文件
        // 定义格式 return array('当前页面'=>$_SERVER['PHP_SELF'],'通信协议'=>$_SERVER['SERVER_PROTOCOL'],...);
		$traceFile  =  YIFW_PATH . '/' . 'Config/Trace.php';
        $trace =   is_file($traceFile)? include $traceFile : array();
         // 系统默认显示信息
        $this->trace('当前页面' , $_SERVER['REQUEST_URI']);
        $this->trace('请求方法' , $_SERVER['REQUEST_METHOD']);
        $this->trace('通信协议' , $_SERVER['SERVER_PROTOCOL']);
        $this->trace('请求时间' , date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
        $this->trace('用户代理' , $_SERVER['HTTP_USER_AGENT']);
        $this->trace('会 话 ID' , session_id());
		$this->trace('使用时间' , G('startControllerTime','endControllerTime'));
        $log    =   YiLog::$log;
        $this->trace('日志记录',count($log)?count($log).'条日志<br/>'.implode('<br/>',$log):'无日志记录');
        $files =  get_included_files();
        $this->trace('加载文件',    count($files).str_replace("\n",'<br/>',substr(substr(print_r($files,true),7),0,-2)));
		//$this->trace('加载文件时间',  G('loadTime','loadTimeEnd'));
        $_trace =   array_merge($trace,$this->_trace);
        // 调用Trace页面模板
        include YIFW_PATH . 'Tpl/PageTrace.php';
    }
	
	/**
	 * 添加相关信息到trace数组中
	 *
	 * @param string $title trace数组中对应的信息title
	 * @param mix $value trace数组中相应信息title对应的value
	 * @access public
	 * @return void
	 */
	public function trace($title,$value='') {
        if(is_array($title))
            $this->_trace   =  array_merge($this->_trace,$title);
        else
            $this->_trace[$title] = $value;
    }

}
