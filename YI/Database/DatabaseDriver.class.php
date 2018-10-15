<?php

class DatabaseDriver {
    /**
	 * 数据库实例
	 *
	 * @var object
	 * @static
	 */
	protected static $instance = null;

	/**
	 * 取得缓存实例
	 *
	 * @return objeact
	 * @access public
	 * @static
	 */
	public static function getInstance() {
		$class = 'Database' . Yi::app()->config('dbclass');

		if (is_null(self::$instance)) {
			self::$instance = new $class();
		}
		return self::$instance;
	}
}