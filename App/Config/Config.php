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
 +------------------------------------------------------------------------------
 * YiFramework配置文件
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Config
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
return array(
	
	//基本配置
	'appPath'				=> dirname(dirname(__FILE__)) . '/',
	'url_model'				=> 2,				//URL模式，1为普通模式，2为PATH_INFO模式
	'default_controller'	=> 'user',			//默认控制器
	'default_action'		=> 'index',			//默认动作
	'urlrewrite'			=> false,			//是否开启urlrewrite(隐藏index.php) 建议开启
	'timezone'				=> 'Asia/Chongqing',//时区
	'charset'				=> 'utf-8',			//文档编码
	'autofilter'			=> true,
	'debug'					=> true,			//是否开启页面报错
	'log_record'			=> true,
	'log_file_size'			=> 2097152,			//日志文件大小限制
	'gzip'					=> true,			//是否启用gzip页面压缩
	'show_page_trace'		=> true,			//启用页面trace
	'log_record_level'      => array('EMERG','ALERT','CRIT','ERR','WARN','NOTIC','INFO','DEBUG','SQL'),  // 允许记录的日志级别
	'requestSuffix'			=> '.html',
	'requestFix'			=> '/',
	'groups'				=> 'admin,front',
	'db'					=> array(
		'class'				=> 'mysql',
		'host'				=> '127.0.0.1',		//mysql主机
		'username'			=> 'root',			//mysql用户
		'password'			=> 'root',			//mysql密码
		'charset'			=> 'utf8',			//字符集
		'database'			=> 'testuser',		//使用的数据库
		'charset'			=> 'UTF8',			//数据库编码
		'pre'				=> '',				//数据表前缀
		'pconnect'			=> true,			//是否打开长连接
	),
	'cache'					=> array(
		'class'				=> 'file',
		'servers'			=> array(
			array(
				'host'				=> 'localhost',     //Memcached 主机
				'port'				=> 11211,			//Memcached 端口
				'persistent'		=> true,			//Memcached 长连接
				'weight'			=> 1,				//Memcached 权重
				'timeout'			=> 1,				//Memcached 连接时间
				'compression'		=> true,			//Memcached 压缩
            ),
			array(
				'host'				=> 'server2',
				'host'				=> 'localhost',     //Memcached 主机
				'port'				=> 11211,			//Memcached 端口
				'persistent'		=> true,			//Memcached 长连接
				'weight'			=> 1,				//Memcached 权重
				'timeout'			=> 1,				//Memcached 连接时间
				'compression'		=> true,			//Memcached 压缩
            ),
        ),
    ),
);	
