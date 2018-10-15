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
 * Memcached缓存类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Cache
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class CacheMemcache extends Cache {

	private $_cache = null;
	private $_servers = array();
	
	public function __construct(){
		if (!extension_loaded('memcache')){
            throw new YiException('memcache扩展没有开启!');
        }
		$servers = $this->getServers();
		$cache = $this->getMemCache();
		if(count($servers)){
			foreach($servers as $server){
				$cache->addServer($server['host'],$server['port'],$server['persistent'],$server['weight'],$server['timeout'],$server['retryInterval'],$server['status']);
			}
		}else{
			$cache->addServer('localhost',11211);
		}
	}
	
	
	public function getMemCache(){
		if($this->_cache != null){
			return $this->_cache;
		}
		return $this->_cache = new Memcache;
	}
	
	
	public function getServers(){
		if(Yi::app()->config('cache.servers') != false){
			return $this->_servers[] = Yi::app()->config('cache.servers'); 
		}else{
			return array();
		}
	}
	
	
	public function setServers($config){
		if(isset($config) && is_array($config)){
			$this->_servers[] = $config;
		}else{
			$this->_servers[] = array();
		}
	}
	
	/**
	 * 获取一个已经缓存的变量
	 *
	 * @param String $key  缓存Key
	 * @return mixed       缓存内容
	 * @access public
	 */
	public function get($key){
		return $this->_cache->get($key);
	}
	
	/**
	 * 设置一个缓存变量
	 *
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否缓存成功
	 * @access public
	 */
	public function set($key,$value,$expire){
		if($expire > 0){
			$expire += time();
		}else{
			$expire = 0;
		}
		return  $this->_cache->set($key,$value,0,$expire);
	}
	
	/**
	 * 添加一个缓存变量
	 *
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否缓存成功
	 * @access public
	 */
	public function add($key,$value,$expire){
		if($expire > 0){
			$expire += time();
		}else{
			$expire = 0;
		}
		return  $this->_cache->add($key,$value,0,$expire);
	}
	
	/**
	 * 获取多个已经缓存的变量
	 *
	 */
	public function mget($keys){
		return $this->_cache->get($keys);
	}
	
	/**
	 * 删除一个已经缓存的变量
	 *
	 * @param  $key
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function del($key) {
		return $this->_cache->delete($key);
	}

	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function delAll() {
		return $this->_cache->flush();
	}

	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 */
	public function has($key) {
		return ($this->get($key) === false ? false : true);
	}
}