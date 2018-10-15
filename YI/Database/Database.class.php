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
 * 数据库基类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Database
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
abstract class Database {

	public abstract function connect();

	public abstract function fulltablename($table);
	
	public abstract function query($sql);

	public abstract function mysql_result_data();

	public abstract function fetch_array();

	public abstract function fetch_assoc();

	public abstract function fetch_row();

	public abstract function fetch_object();

	public abstract function select($table,$columnName,$condition);

	public abstract function delete($table,$condition);

	public abstract function insert($table,$columnName,$value);

	public abstract function update($table,$mod_content,$condition);

	public abstract function insert_id();

	public abstract function free();

	public abstract function select_db($database);

	public abstract function startTrans();

	public abstract function commit();

	public abstract function rollback();
}