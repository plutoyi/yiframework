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
 * YiFramework init
 +------------------------------------------------------------------------------
 */
//xhprof_enable();
//记录和统计时间(微秒)
function G($start,$end='',$dec=3){
	static $_info = array();
	if(!empty($end)){
		if(!isset($_info[$end])){
			$_info[$end] = microtime(TRUE);
		}
		return number_format(($_info[$end] - $_info[$start]),$dec);
	}else{
		return $_info[$start] = microtime(TRUE);
	}
}

require(dirname(__FILE__) . '/YiBase.class.php');
	
class Yi extends YiBase{
}
?>