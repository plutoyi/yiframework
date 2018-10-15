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
 * 组件类
 +------------------------------------------------------------------------------
 * @category   Yi
 * @package  Yi
 * @subpackage  Core
 * @author    Devin.yang<yi.pluto@163.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class YiComponent{

	private $_components;
	private $_memberdata;
	
	/**
	 * 魔术方法_get 自动获取属性值
	 * 
	 * @param string $name 属性名称
	 * @access public
	 * @return void
	 */
	public function __get($name){
		$getname = 'get' . ucwords($name);
		$component = $this->getComponent($name);
		if(method_exists($this,$getname)){
			return call_user_func(array($this, $getname)); 
		}else if(isset($this->$name)){
			return $this->$name;
		}else if(isset($component)){
			return $component;
		}else{
			$class = get_class($this);
			throw new YiException("Property {$class}.{$name} is not defined.");
		}
	}
	
	/**
	 * 魔术方法_set 自动设置属性值
	 * 
	 * @param string $name 属性名称
	 * @param string $value 属性值
	 * @access public
	 * @return void
	 */
	public function __set($name,$value){
		$setname = 'set'.ucwords($name);
		if(method_exists($this,$setname)){
			return call_user_func(array($this, $setname),$value); 
		}else if(isset($this->$name)){
			return $this->$name = $value;
		}
		$class = get_class($this);
		if(method_exists($this,'get'.$name)){
			throw new YiException("Property {$class}.{$name} is read only.");
		}else{
			throw new YiException("Property {$class}.{$name} is not defined.");
		}
	}
	
	/**
	 * 魔术方法__isset 判断是否存在属性
	 * 
	 * @param string $name 属性名称
	 * @access public
	 * @return boolean true|false
	 */
	public function __isset($name){
		$getname = 'get' . $name;
		if(method_exists($this,$getname)){
			return call_user_func(array($this, $getname)) !== null;
		}elseif(isset($this->$name)){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * 魔术方法__call 调用不存在的方法时调用
	 * 
	 * @param string $func 方法名
	 * @param mix $args 参数
	 * @access public
	 */
	public function __call($func,$args){
		list($type,$name) = array(substr($func,0,3),lcfirst(substr($func,3)));
		if(!in_array($type,array('set','get')) || $name == ''){
			return ;
		}
		switch($type){
			case 'get':
				return $this->getMemberData($name);   
				break;
			case 'set':
				if(isset($args[1])){
					$this->setMemberData($name,$args[1]);
				}
				break;  
			default:
				throw new YiException('Function '.get_class($this).':'.$func . 'is not defined');
				break;
		}
	}
	
	/**
	 * 获取成员变量值
	 * 
	 * @param string $name 变量名
	 * @access public
	 * @return mix 变量值
	 */
	public function getMemberData($name){
		if(false != strpos($name,'.')){
			$params = explode('.',$name);
			//暂时只支持二维数组
			if(count($params) == 2){
				return isset($this->_memberdata[$params[0]][$params[1]]) ? $this->_memberdata[$params[0]][$params[1]] : "";
			}
		}
		return isset($this->_memberdata[$name]) ? $this->_memberdata[$name] : "";
	}
	
	/**
	 * 设置成员变量值
	 * 
	 * @param string $name 变量名
	 * @param mix $value 变量值
	 * @access public
	 * @return void
	 */
	public function setMemberData($name ,$value){
		if(false != strpos($name,'.')){
			$params = explode('.',$name);
			//暂时只支持二维数组
			if(count($params) == 2){
				$this->_memberdata[$params[0]][$params[1]] = $value;
			}
		}
		$this->_memberdata[$name] = $value;
	}
	
	/**
	 * 获取所有成员变量值
	 * 
	 * @access public
	 * @return array 所有成员变量值数组信息
	 */
	public function getMemberDatas(){
		return $this->_memberdata;
	}
	
	/**
	 * 设置应用程序组件
	 * 
	 * @param string $id 组件别名
	 * @param object $component 组件实例
	 * @access public
	 * @return void
	 */
	public function setComponent($id,$component){
		if($component == null){
			unset($this->_components[$id]);
			return ;
		}elseif(isset($this->_components[$id])){
			return;
		}
		$this->_components[$id] = Yi::createComponent($component);
	}
	
	/**
	 * 获取应用程序组件
	 * 
	 * @param string $id 组件别名
	 * @access public
	 * @return object 组件实例
	 */
	public function getComponent($id){
		if(isset($this->_components[$id])){
			return $this->_components[$id];
		}
	}
	
	/**
	 * 批量设置应用程序组件
	 * 
	 * @param array $components components数组
	 * @access public
	 * @return void
	 */
	public function setComponents($components){
		foreach($components as $id => $component)
			$this->setComponent($id,$component);
	}
	
	/**
	 * 获取所有应用程序组件
	 * 
	 * @access public
	 * @return array 所有应用程序组件数组
	 */
	public function getComponents(){
		return $this->_components;
	}
}