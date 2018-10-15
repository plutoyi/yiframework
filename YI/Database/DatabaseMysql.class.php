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
 * mysql操作类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Database
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class DatabaseMysql extends Database {

	private $db_host;		//数据库主机
	private $db_username;	//数据库用户名
	private $db_password;	//数据库密码
	private $db_table;		//数据库表
	private $db_conn;		//数据库连接标识;
	private $result;		//执行query命令的结果资源标识
	private $sql;			//sql执行语句 
	private $pre;			//数据库表前缀
	private $coding;		//数据库编码，GBK,UTF8,gb2312
	private $dbconfig;

	/**
	* 构造函数
	*
	* 初始化数据库配置文件
	*
	*@author Devin.yang
	*@access public
	*@return void
	*/
	public function __construct(){  
		
		$this->dbconfig = Yi::app()->config('dbconfig');
		//控制异常
		set_exception_handler(array($this, 'exception'));
		$this->db_host			= Yi::app()->config('db.host');    
		$this->db_username		= Yi::app()->config('db.username');    
		$this->db_password		= Yi::app()->config('db.password');
		$this->db_table			= Yi::app()->config('db.database');
		$this->pre				= Yi::app()->config('db.pre');   
		$this->coding			= Yi::app()->config('db.charset');	
		$this->connect();
		
	} 

	/**
	 * 控制异常的回调函数
	 * 回调函数必须为public
	 * 
	 * @param object $e
	 * @access public
	 */
	public final function exception($e) {
		if(Yi::app()->config('log_record')) 
			YiLogLog::write($e->getMessage());
		$e->getError();
	}

	/**
     +----------------------------------------------------------
     * 数据库调试 记录当前SQL
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     */
    protected function debug() {
        // 记录操作结束时间
		G('queryEndTime');
        YiLog::record($this->sql." [ RunTime:".G('queryStartTime','queryEndTime',6)."s ]",YiLog::SQL);
    }

	/**
	*加上前缀的数据表
	*
	*@param		string $table
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/  
	public function fulltablename($table){    
		
		return $table = $this->pre.$table;    
	
	}

	/**
	*链接数据库
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function connect(){
		$connectStyle = $this->dbconfig['pconnect']? 'mysql_pconnect' : 'mysql_connect';
		$this->db_conn = $connectStyle($this->db_host,$this->db_username,$this->db_password,true);
		if(!mysql_select_db($this->db_table,$this->db_conn)){
			exit( "没有找到数据库表：" . $this->db_table );
		}
		$this->query("SET NAMES $this->coding");
	}
	
	/**
	*执行SQL语句的函数
	*
	*@param		string $sql
	*@author	Devin.yang
	*@access	public
	*@return	string
	*/
	public function query($sql){
		empty($sql) && $this->show_error("你的sql语句不能为空!");
		$this->sql = $sql; 
		// 记录开始执行时间
        G('queryStartTime');
		$result = @mysql_query($this->sql,$this->db_conn);
		$this->debug();
		if(!$result){
			if($this->show_error){
				$this->show_error("错误Sql语句:" , $this->sql);
			}
		}else{
			$this->result = $result;
		}
		return $this->result;
	}
	
	/**
	*创建添加新的数据库
	*
	*@param		string $database_name
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function create_database($database_name){
		$sqlDatabase = 'create database '.$database_name;
		$this->query($sqlDatabase);
	}
	
	/**
	*获取结果数据集
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	array
	*/
	public function mysql_result_data(){
		return @mysql_result($this->result);
	}
	
	/**
	*取得记录集,获取数组-索引和关联,使用$row['content']
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	array
	*/
	public function fetch_array(){
		return @mysql_fetch_array($this->result);
	}
	
	/**
	*获取关联数组,使用$row['字段名']
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	array
	*/
	public function fetch_assoc(){
		return @mysql_fetch_assoc($this->result);
	}
	
	/**
	*获取数字索引数组,使用$row[0],$row[1],$row[2]
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	array
	*/
	public function fetch_row(){
		return @mysql_fetch_row($this->result); 
	}
	
	/**
	*获取对象数组,使用$row->content 
	*
	*@param		void
	*@author	Devin.yang
	*@access	public
	*@return	object
	*/
	public function fetch_object(){
		return @mysql_fetch_object($this->result);
	}
	
	
	/**
	*简化查询select 
	*
	*@param		string $table,$coumnName,$condition
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function select($table,$columnName,$condition){
		$table = $this->fulltablename($table);
		$columnName = empty($columnName) ? "*" : $columnName;
		$condition = empty($condition) ? "" : "WHERE " . $condition;
		$rs = $this->query("SELECT $columnName FROM $table $condition");
		return $rs;

	}

	/**
	*简化删除del 
	*
	*@param		string $table,$condition
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function delete($table,$condition){ 
		$table = $this->fulltablename($table);
		$rs = $this->query("DELETE FROM $table WHERE $condition");
		return $rs;
	} 
 
	/**
	*简化插入insert 
	*
	*@param		string $table,$columnName,$condition
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function insert($table,$columnName,$value){
		$table = $this->fulltablename($table);
		$rs = $this->query("INSERT INTO $table ($columnName) VALUES ($value)");
		return $rs;
	} 

	public function insertMany($table,$columnName,$value){
		$table = $this->fulltablename($table);
		$sql = "INSERT INTO $table ($columnName) VALUES $value";
		$rs = $this->query("INSERT INTO $table ($columnName) VALUES $value");
		return $rs;
	} 
	
	/**
	*简化修改update 
	*
	*@param		string $table,$mod_content,$condition
	*@author	Devin.yang
	*@access	public
	*@return	void
	*/
	public function update($table,$mod_content,$condition){ 
		$table = $this->fulltablename($table);
		$rs = $this->query("UPDATE $table SET $mod_content WHERE $condition");
		return $rs;
	}
		
	/**
	*取得上一步 INSERT 操作产生的 ID 
	*
	*@access	public
	*@return	int
	*/
	public function insert_id(){
		return @mysql_insert_id();
	}
	
	/**
	*释放结果集 
	*
	*@access	public
	*@return	void
	*/
	public function free(){ 
		@mysql_free_result($this->result); 
	}
	
	/**
	*数据库选择 
	*
	*@access	public
	*@param		string $db_database
	*@return	void
	*/
	public function select_db($db_database){ 
		return @mysql_select_db($db_database);
	}

	/**
	 * 开始事务
	 * 
	 * @return bool
	 */
	public function startTrans() {
		return $this->query('BEGIN');
	}

	/**
	 * 提交事务
	 * 
	 * @return bool
	 */
	public function commit() {
		return $this->query('COMMIT');
	}

	/**
	 * 回滚事务
	 * 
	 * @return bool
	 */
	public function rollback() {
		return $this->query('ROLLBACK');
	}

	/**
	 *获取最近一次查询的sql语句 
	 *
	 *@access	public
	 *@param	string $db_database
	 *@return	void
	 */
	public function getLastSql() {
        return $this->sql;
    }

	/**
	*析构函数，自动关闭数据库,垃圾回收机制 
	*
	*@access	public
	*@param		void
	*@return	void
	*/
	public function __destruct(){
		if(!empty($this->result)){ 
			$this->free();
		}
		@mysql_close($this->conn);
	}

	public function show_error($msg=''){

		echo("<br>");
		echo("<font color='#red'><strong>出现错误：</strong></font>");
		echo("<font color='#blue'>".$msg."</font>");
		exit();

	}
}