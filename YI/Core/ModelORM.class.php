<?php
require YIFW_PATH . 'vendor/autoload.php';
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Eloquent;
class ModelORM extends Eloquent{
	/**
	 * 构造函数 初始化
	 */
	public function  __construct() {
		$capsule = new Capsule;
		$capsule->addConnection(array(
			'driver'	=> Yi::app()->config('db.class'),
			'host'		=> Yi::app()->config('db.host'),
			'database'	=> Yi::app()->config('db.database'),
			'username'	=> Yi::app()->config('db.username'),
			'password'	=> Yi::app()->config('db.password'),
			'charset'	=> Yi::app()->config('db.charset'),
			'collation'	=> 'utf8_unicode_ci',
		));
		$capsule->bootEloquent();
	}
}