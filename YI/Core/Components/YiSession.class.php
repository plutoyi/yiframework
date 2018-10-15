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
 * Session控制基类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiSession{

	public $autoStart=true;
		
	public function __construct(){
		if($this->autoStart){
			$this->open();
		}
		register_shutdown_function(array($this,'close'));
	}	
	
	public function open(){
		@session_start();
	}
	
	public function close(){
		if(session_id()!==''){
			@session_write_close();
		}
	}

	public function destroy(){
		if(session_id()!==''){
			@session_unset();
			@session_destroy();
		}
	}
	
	/**
     * 获取Session id
     */
	public function getSessionID(){
		return session_id();
	}
	
	/**
     * 设置Session id
     * @param string $id session id
     */
	public function setSessionID($id){
		session_id($id);
	}
	
	/**
     * 获取Session name
     * @param string $name session名称
     */
	public function getSessionName(){
		return session_name();
	}
	
	/**
     * 设置Session name
     * @param string $name session名称
     */
	public function setSessionName($name){
		session_name($name);
	}
	
	/**
     * 获取当前Session保存路径
     */
	public function getSavePath(){
		return session_save_path();
	}
	
	/**
     * 设置当前Session保存路径
	 *
	 * @param string $path session save路径
     */
	public function setSavePath($path){
		if(is_dir($path)){
			session_save_path($path);
		}else{
			throw new YiException('savePath "{$path}" is not a valid directory.');
		}
	}
	
	/**
     * 获取cookieParams
     */
	public function getCookieParams(){
		return session_get_cookie_params();
	}
	
	/**
     * 清空Session
     */
	public function clear(){
		$_SESSION = array();
	}
	
	/**
     * 设置session
	 *
	 * @param string $name session名称
	 * @param mix $value session值
     */
	public function set($name,$value){
		$_SESSION[$name] = $value;
	}
	
	/**
     * 获取session
	 *
	 * @param string $name session名称
	 * @return string|int
     */
	public function get($name){
		return $_SESSION[$name];
	}
	/**
     * 检查Session 值是否已经设置
     */
    public function is_set($name){
        return isset($_SESSION[$name]);
    }
	
	
	/**
     * 设置Session cookie_domain
     * 返回之前设置
    */
    public function setCookieDomain($sessionDomain = null){
        $return = ini_get('session.cookie_domain');
        if(!empty($sessionDomain)) {
            ini_set('session.cookie_domain', $sessionDomain);//跨域访问Session
        }
        return $return;
    }
	
	/**
     * 设置Session gc_maxlifetime值
     * 返回之前设置
     */
    public function setGcMaxLifetime($gcMaxLifetime = null){
        $return = ini_get('session.gc_maxlifetime');
        if (isset($gcMaxLifetime) && is_int($gcMaxLifetime) && $gcMaxLifetime >= 1) {
            ini_set('session.gc_maxlifetime', $gcMaxLifetime);
        }
        return $return;
    }
	
	/**
     * 设置Session gc_probability 值
     * 返回之前设置
     */
    public function setGcProbability($gcProbability = null){
        $return = ini_get('session.gc_probability');
        if (isset($gcProbability) && is_int($gcProbability) && $gcProbability >= 1 && $gcProbability <= 100) {
            ini_set('session.gc_probability', $gcProbability);
        }
        return $return;
    }
	
	/**
     * 当前Session文件名
     */
    public function getFilename(){
        return $this->getSavePath().'/sess_'.session_id();
    }
	
	/**
     * 设置Session 过期时间
     */
    public function setExpire($time, $add = false){
        if ($add) {
            if (!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
                $_SESSION['__HTTP_Session_Expire_TS'] = time() + $time;
            }
   
            // update session.gc_maxlifetime
            $currentGcMaxLifetime = Session::setGcMaxLifetime(null);
            $this->setGcMaxLifetime($currentGcMaxLifetime + $time);
   
        } elseif (!isset($_SESSION['__HTTP_Session_Expire_TS'])) {
            $_SESSION['__HTTP_Session_Expire_TS'] = $time;
        }
    }
	
	/**
     * 检查Session 是否过期
     */
    public function isExpired(){
        if (isset($_SESSION['__HTTP_Session_Expire_TS']) && $_SESSION['__HTTP_Session_Expire_TS'] < time()) {
            return true;
        } else {
            return false;
        }
    }
	
	/**
     * 设置Session 对象反序列化时候的回调函数
     * 返回之前设置
     */
    public function setCallback($callback = null){
        $return = ini_get('unserialize_callback_func');
        if (!empty($callback)) {
            ini_set('unserialize_callback_func',$callback);
        }
        return $return;
    }
	
}