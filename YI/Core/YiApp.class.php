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
 * YiFramework应用程序类 执行应用过程管理
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiApp extends YiComponent{//类定义开始	
	
	private $_controller;
	private $_action;
	private $_module;
	
	/**
	 * 构造函数.
	 *
	 * @return void
	 * @access public
	 *
	 */
	public function __construct($config){
		Yi::setApp($this);
		if(is_string($config)){
			$config = require($config);
		}
		$this->configure($config);
		$this->initSystem($config);
		$this->initSystemHandlers();
		$this->registerCoreComponents();
	}
	
	/**
	 * 应用程序实例run函数.
	 *
	 * @access public
	 * @return void
	 */
	public function run(){
		G('startControllerTime');
		Yi::createApplication('YiController');
		G('startControllerTime','endControllerTime');
	}
	
	/**
	 * 获取配置文件键值.
	 *
	 * @param string $config 配置文件键值
	 * @return void
	 * @access public
	 */
	public function configure($config){
		if(is_array($config)){
			foreach($config as $key=>$value){
				$this->setMemberData($key,$value);
			}
		}
	}
	
	/**
	 * 获取,设置配置文件键值对.
	 *
	 * @param string $key 配置文件键
	 * @param mix $value 配置文件键所对应的值
	 * @access public
	 */
	public function config($key=null,$value=null){
		if(null == $value){
			return (null == $key) ? $this->getMemberDatas() : $this->getMemberData($key);
		}else{
			if(null == $key){
				return false;
			}
			$this->setMemberData($key,$value);
		}
	}
	
	/**
	 * 初始化系统配置.
	 *
	 * @return void
	 * @access public
	 */
	public function initSystem($config){
		if(isset($config['appPath'])){
			$this->setAppPath($config['appPath']);
		}else{
			 throw new YiException('The "basePath" configuration for the Application is required.');
		}
		if (isset($config['timezone'])) {
            $this->setTimeZone($config['timezone']);
        } elseif (!ini_get('date.timezone')) {
            $this->setTimeZone('UTC');
        }
		if(isset($config['charset'])){
			$this->setCharset($config['charset']);
		}
		if(isset($config['gzip'])){
			function_exists('ob_gzhandler') ? ob_start('ob_gzhandler') : ob_start();
		}else {
			ob_start();
		}
		if(isset($config['debug'])){
			error_reporting(E_ERROR | E_WARNING | E_PARSE);
		}else{
			error_reporting(0);
		}
		if(($htaccess = $config['appPath'].'.htaccess') && isset($config['urlrewrite'])){
			is_file($htaccess) || file_put_contents($htaccess, "RewriteEngine on\r\nRewriteBase / \r\nRewriteCond %{SCRIPT_FILENAME} !-f\r\nRewriteCond %{SCRIPT_FILENAME} !-d\r\nRewriteRule ^.*$ index.php", LOCK_EX);
		}else{
			is_file($htaccess) && unlink($htaccess);
		}
		$this->normalizeQuest();
	}
	
	/**
	 * 初始化系统错误和异常处理机制.
	 *
	 * @return void
	 * @access public
	 */
	public function initSystemHandlers(){
		if(YI_ENABLE_EXCEPTION_HANDLER){
			set_exception_handler(array($this,'handleException'));
		}
		if(YI_ENABLE_ERROR_HANDLER){
			set_error_handler(array($this,'handleError'));
		}
	}

	/**
	 * 解析获得模板
	 *
	 * @category   Yi
	 * @package  Core
	 * @param string $view 模板文件名
	 * @access public
	 * @return string
	 */
	public final function view($view = null){
		$tpl = YiTemplate::getInstance();
		$view = $_GET['CONTROLLER'] . '/' . (empty($view) ? $_GET['ACTION'] : $view);
		return $tpl->show($view);
	}

	/**
	 * 解析获得模型
	 *
	 * @category   Yi
	 * @package  Core
	 * @param string $modelName 模型文件名
	 * @access public
	 * @return object $model
	 */
	public final function model($modelName){
		$modelFile = $this->getModulePath() . '/Model/' . ucwords($modelName) . 'Model.php';
		!file_exists($modelFile) && exit('模型' . $modelName . '不存在');
		include($modelFile);
		$class = ucwords($modelName); //获得模型
		!class_exists($class) && exit('模型' . $modelName .'未定义');
		$model = new $class();
		return $model;
	}
	
	/**
	 * 解析获得插件
	 *
	 * @category   Yi
	 * @package  Core
	 * @param string $plugin 插件文件名
	 * @access public
	 * @return object 
	 */
	public final function plugin($plugin, $param = array(), $ext = '.class.php'){
		$file = $this->getAppPath() . '/Plugin/' . str_replace('.', '/', $plugin) . $ext;
		if(is_file($file)) {
			include_once($file);
			$plugin = str_replace('.', '', ($tmp = strrchr($plugin, '.')) === false ? $plugin : $tmp);
			!class_exists($plugin) && exit('插件类名' . $plugin . '和插件文件名不统一');
			return empty($param) ? new $plugin() : new $plugin($param);
		} else {
			throw new YiException('找不到Plugin文件:' . $file);
		}
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
		Yi::createComponent('YiException')->handleException($e);
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
		Yi::createComponent('YiError')->handleError($code,$message,$file,$line);
	}
	
	/**
	 * 设置控制器
	 * 
	 */
	public function setController($controller){
		$this->_controller = $controller;
	}
	
	/**
	 * 获取控制器
	 * 
	 * @access public
	 * @return string 控制器名称
	 */
	public function getController(){
		return $this->_controller;
	}
	
	/**
	 * 设置方法
	 * 
	 */
	public function setAction($action){
		$this->_action = $action;
	}
	
	/**
	 * 获取动作
	 * 
	 * @access public
	 * @return string 动作名称
	 */
	public function getAction(){
		return $this->_action;
	}
	
	/**
	 * 设置Module
	 * 
	 */
	public function setModule($module){
		$this->_module = $module;
	}
	
	/**
	 * 获取Module
	 * 
	 * @access public
	 * @return string Module名称
	 */
	public function getModule(){
		return $this->_module;
	}
	
	
	
	/**
	 * 设置应用程序编码
	 * 
	 * @param $value 编码
	 * @access public
	 * @return void
	 */
	public function setCharset($value){
		header ( "Content-type: text/html;charset=" . $value );
	}
	
	/**
	 * 获取时区
	 * 
	 * @access public
	 * @return string
	 */
	public function getTimeZone(){
        return date_default_timezone_get();
    }
	
	/**
	 * 设置时区
	 * 
	 * @param string $value 时区
	 * @access public
	 * @return void
	 */
    public function setTimeZone($value){
        date_default_timezone_set($value);
    }
	
	/**
	 * 获取appPath.
	 *
	 * @return string 获取appPath路径
	 * @access public
	 */
	public function getAppPath(){
		$appPath = Yi::getPathOfAlias('application');
		if(!isset($appPath)){
			throw new YiException("The appPath {path} is not a valid directory.");
		}
		return $appPath;
	}
	
	/**
	 * 设置basePath.
	 *
	 * @param string $path basePath路径
	 * @return void
	 * @access public
	 */
	public function setAppPath($path){
		if(($appPath = realpath($path)) === false || !is_dir($appPath))
			throw new YiException("The appPath {$path} is not a valid directory.");
		Yi::setPathOfAlias('application',$appPath);
	}
	
	/**
	 * 获取ModulePath.
	 *
	 * @return string ModulePath路径
	 * @access public
	 */
	public function getModulePath(){
		$modulePath = Yi::getPathOfAlias('module');
		if(!isset($modulePath)){
			$modulePath = $this->getAppPath();
		}
		return $modulePath;
	}
	
	/**
	 * 设置ModulePath.
	 *
	 * @param string $path ModulePath路径
	 * @return void
	 * @access public
	 */
	public function setModulePath(){
		if($this->getModule() == null){
			$path = $this->getAppPath();  
		}else{
			$path = $this->getAppPath() . '/Module/' . ucwords($this->getModule());
		}
		if(($modulePath = realpath($path)) === false || !is_dir($modulePath)) 
			throw new YiException("The modulePath {$path} is not a valid directory.");
		Yi::setPathOfAlias('module',$modulePath);
	}
	
	/**
	 * 获取扩展文件夹路径
	 * 
	 * @access public
	 * @return string 扩展所在文件夹路径
	 */
	public function getExtensionPath(){
		$extensionPath = Yi::getPathOfAlias('ext');
		if(!isset($extensionPath)){
			$this->setExtensionPath($this->getAppPath() . DIRECTORY_SEPARATOR . 'Plugin');
		}
		return Yi::getPathOfAlias('ext');
	}

	/**
	 * 设置扩展所在文件夹路径
	 * 
	 * @param string $path 路径
	 * @access public
	 * @return void
	 */
	public function setExtensionPath($path){
		if(($extensionPath = realpath($path)) === false || !is_dir($extensionPath))
			throw new YiException("The extension path {$path} does not exist.");
		Yi::setPathOfAlias('ext',$extensionPath);
	}
	
	/**
	 * 注册核心组件
	 * 
	 * @access protected
	 * @return void
	 */
	protected function registerCoreComponents(){
		$components = array(
			'session' 	=> array(
				'class' => 'YiSession',
			),
			'cookie' => array(
				'class' => 'YiCookie',
			),
			'request' 	=> array(
				'class' => 'YiHttpRequest',
			),
		);
		$this->setComponents($components);
	}
	
	/**
	 * 过滤请求参数
	 * 
	 * @access public
	 * @return void
	 */
	public function normalizeQuest(){
		if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){
			if(isset($_GET)){
				$_GET = $this->stripSlashes($_GET);
			}
			if(isset($_POST)){
				$_POST = $this->stripSlashes($_POST);
			}
			if(isset($_REQUEST)){
				$_REQUEST = $this->stripSlashes($_REQUEST);
			}
			if(isset($_COOKIE)){
				$_COOKIE = $this->stripSlashes($_COOKIE);
			}
		}
	}
	
	/**
	 * 过滤请求参数
	 * 
	 * @access public
	 * @return void
	 */
	public function stripSlashes($data){
		if(!is_array($data)){
			return stripslashes($data);
		}
		if(count($data) == 0){
			return $data;
		}
		$keys = array_map('stripslashes',array_keys($data));
		$data = array_combine($keys,array_values($data));
		return array_map(array($this,'stripSlashes'),$data);
	}
	
}//类定义结束