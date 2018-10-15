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
 * EAccelerator缓存类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Cache
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class CacheEAccelerator extends Cache {
	
	/**
	 * 构造器
	 * 检测eAccelerator扩展是否开启
	 *
	 * @access public
	 */
	public function __construct() {
		if (!function_exists('eaccelerator_put')) {
            throw new YiException('eAccelerator扩展没有开启!');
        }
	}

	/**
	 * 设置一个缓存变量
	 *
	 * @param String $key    缓存Key
	 * @param mixed $value   缓存内容
	 * @param int $expire    缓存时间(秒)
	 * @return boolean       是否缓存成功
	 * @access public
	 * @abstract
	 */
    public function set($key, $value, $expire = 60) {
		return eaccelerator_put ($key, $value, $expire);
	}

	/**
	 * 获取一个已经缓存的变量
	 *
	 * @param String $key  缓存Key
	 * @return mixed       缓存内容
	 * @access public
	 */
	public function get($key) {
		return eaccelerator_get($key);
	}

	/**
	 * 删除一个已经缓存的变量
	 *
	 * @param  $key
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function del($key) {
		return eaccelerator_rm($key);
	}

	/**
	 * 删除全部缓存变量
	 *
	 * @return boolean       是否删除成功
	 * @access public
	 */
	public function delAll() {
		eaccelerator_clean();
		return true;
	}

	/**
	 * 检测是否存在对应的缓存
	 *
	 * @param string $key   缓存Key
	 * @return boolean      是否存在key
	 * @access public
	 */
	public function has($key) {
		return (eaccelerator_get($key) === NULL ? false : true);
	}
}