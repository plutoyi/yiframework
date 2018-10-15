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
 * 缓存驱动类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Cache
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 class CacheDriver{
 
	static protected $instance = null;
	
	public static function getInstance(){
		$class = 'Cache' . Yi::app()->config('cache.class');
		if(is_null(self::$instance)){
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
}

