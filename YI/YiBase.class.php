<?php 
// +----------------------------------------------------------------------
// | YiFramework
// +----------------------------------------------------------------------
// | Copyright (c)  http://yisong.sinaapp.com
// +----------------------------------------------------------------------
// | Licensed
// +----------------------------------------------------------------------
// | Author: Devin.yang<yi.pluto@163.com>
// +----------------------------------------------------------------------
//xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
//版本判断
if(version_compare(PHP_VERSION,'5.0.0','<'))  die('require PHP > 5.0 !');
//定义开始执行时间
define('YI_BEGIN_TIME',microtime(true));
//框架版本
define('YIFW_VERSION','1.0');
//定义核心文件夹名称
define('YIFRAMEWORK_NAME','YI');
//定义YIFW_PATH路径
define('YIFW_PATH', dirname(__FILE__) . '/');
//定义内存记录开始常量
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));
//定义异常处理机制开关
define('YI_ENABLE_EXCEPTION_HANDLER',true);
//定义错误处理机制开关
define('YI_ENABLE_ERROR_HANDLER',true);	
//定义runtime开关
define('YI_RUNTIME',false);	
/**
 +------------------------------------------------------------------------------
 * YiBase类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiBase{

	private static $_app;
	
	private static $_aliases = array('system'=>YIFW_PATH); 
	
	private static $_imports;
	
	private static $_code;
	
	private static $_instance;
	
	/**
	 * 构造函数，注册自动加载函数
	 *
	 * @return void
	 */
	public function __construct() {
		if(YI_RUNTIME){
			self::setRuntime();
		}
		spl_autoload_register (array($this , 'core'));
        spl_autoload_register (array($this , 'cache'));    
        spl_autoload_register (array($this , 'database')); 
		spl_autoload_register (array($this , 'model'));  		
	}
	
	/**
	 * 初始化方法，获取单例
	 *
	 * @return object 单例
	 */
	public static function init(){  
        if (self::$_instance == NULL)  
            self::$_instance = new self();  
  
        return self::$_instance;  
    }  
	/**
	 * 获取框架版本号
	 *
	 * @return string 框架版本
	 */
	public static function getVersion(){
		return YIFW_VERSION;
	}
	/**
	 * 获取框架路径
	 *
	 * @return string 框架路径
	 */
	public static function getFrameworkPath(){
		return YIFW_PATH;
	}
	
	/**
	 * 获取应用程序实例
	 *
	 * @return object 应用程序实例
	 */
	public static function app(){
		return self::$_app;
	}
	
	/**
	 * 设置应用程序实例
	 *
	 * @return void
	 */
	public static function setApp($app){
		if(self::$_app == null){
			self::$_app = $app;
		}else{
			throw new YiException("Yi application can only be created once.");
		}
	}
	
	/**
	 * 设置文件路径别名
	 *
	 * @param string $alias 路径别名
	 * @param string $path 文件路径
	 * @return void
	 */
	public static function setPathOfAlias($alias,$path){
		if(empty($path)){
			unset(self::$_aliases[$alias]);
		}else{
			self::$_aliases[$alias] = rtrim($path,'\\/');;
		}
	}
	
	/**
	 * 获取文件路径别名
	 *
	 * @param string $alias 路径别名
	 * @return void
	 */
	public static function getPathOfAlias($alias){
		if(isset(self::$_aliases[$alias])){
			return self::$_aliases[$alias];
		}
	}
	
	/**
	 * 是否开启核心框架编译功能
	 *
	 * @param boolean $bool 开启核心框架编译功能开关
	 * @access private
	 * @return void
	 */
	public static function setRuntime(){
		$runtime = YIFW_PATH . '~runtime.php';
		$root = YIFW_PATH . 'Core/';
		if(!is_file($runtime)){
			self::getCode($root);
			file_put_contents($runtime, '<?php' . str_replace(array("<?php", "\n","\r\n"), '', self::$_code));
		}
		require($runtime);
	}
	
	/**
	 * 获取runtime文件路径
	 *
	 * @return string $runtime runtime文件路径
	 */
	public static function getRuntimePath(){
		if(YI_RUNTIME && is_file($runtime = YIFW_PATH . '~runtime.php')){
			return $runtime;
		}
	}
	 
	/*
	 * 循环查找文件
	 *
	 * @param string $dir 目录
	 * @return void
	 */
	public static function getCode($dir) {
		$files = scandir($dir);
		$files = array_diff($files, array('.', '..', '.svn'));
		foreach($files as $file) {
			$file = $dir . $file;
			if(is_dir($file)) {
				self::getCode($file.'/');
			} else {
				self::$_code .= php_strip_whitespace($file);
			}
		}
	}
	
	/**
	 * 创建应用程序实例
	 *
	 * @param string $class 类名
	 * @param mix $config 配置参数
	 * @return string 框架版本
	 */
	public static function createApplication($class,$config=null){
		return new $class($config);
	}
	
	/**
	 * 创建应用程序组件
	 *
	 * @param array|string $config 组件相关配置
	 * @return string 框架版本
	 */
	//$config = array('class'=>'exception') or 'exception'
	public static function createComponent($config){
		if(is_string($config)){
			$class = $config;
			$config = array();
		}elseif(isset($config['class'])){
			$class = $config['class'];
			unset($config['class']);
		}else{
			throw new YiException("Object configuration must be an array containing a 'class' element");
		}
		if(!class_exists($class,false)){
			Yi::import($class);
		}
		$object = new $class();
		if(method_exists($object,'getInstance')){
			$object = call_user_func(array($object, 'getInstance'));
		}
		foreach($config as $k => $v){
			$object->$k = $v;
		}
		return $object;
	}
	
	public static function import($alias){
		//已经import过的 返回
		if(isset(self::$_imports[$alias])){
			return self::$_imports[$alias];
		}
		//已经存在的返回 false 不进行自动加载
		if(class_exists($alias,false)){
			return self::$_imports[$alias] = $alias;
		}
		//获取路径是否存在 ，存在则返回
		$aliasPath = self::getPathOfAlias($alias);
		if(isset($aliasPath)){
			require($aliasPath);
			return self::$_imports[$alias] = $alias;
		}
		if(is_file($filePath = YIFW_PATH . 'Core/Components/' . $alias . '.class.php')){
			require($filePath);
			return self::$_imports[$alias] = $alias;
		}
		return $alias;
	}
	
	/**
	 * 自动加载
	 *
	 * @return void
	 */
	public static function autoload(){
		spl_autoload_register (array($this , 'core'));
        spl_autoload_register (array($this , 'cache'));    
        spl_autoload_register (array($this , 'database'));  
	}
	
	/**
	 * 自动加载核心类文件
	 *
	 * @param string $class
	 * @return void
	 */
	public static function core($class){
		set_include_path (get_include_path() . PATH_SEPARATOR . YIFW_PATH . 'Core/');  
		set_include_path (get_include_path() . PATH_SEPARATOR . YIFW_PATH . 'Core/Components/');
        spl_autoload_extensions ('.class.php');    
        spl_autoload ($class);    
	}

	/**
	 * 自动加载缓存操作类文件
	 
	 * @return void
	 * @param string $class
	 */
	public static function cache($class){
		set_include_path(get_include_path() . PATH_SEPARATOR . YIFW_PATH . 'Cache/');
		spl_autoload_extensions('.class.php');
		spl_autoload($class);
	}

	/**
	 * 自动加载数据库操作类文件
	 *
	 * @return void
	 * @param string $class
	 */
	public static function database($class){
		set_include_path(get_include_path() . PATH_SEPARATOR . YIFW_PATH . 'Database/');
		spl_autoload_extensions('.class.php');
		spl_autoload($class);
	}
	
	/**
	 * 自动加模型类文件
	 *
	 * @return void
	 * @param string $class
	 */
	public static function model($class){
		set_include_path(get_include_path() . PATH_SEPARATOR . Yi::app()->getModulePath() . '/Model/');
		spl_autoload_extensions('.php');
		spl_autoload($class.'Model');
	}
}
YiBase::init();
?>