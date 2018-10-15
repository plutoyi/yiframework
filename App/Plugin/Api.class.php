<?php


class Api {

	private $name;

	public function show(){   
        return '这是一个数据请求';   
	} 

	public function setName($name){
		$this->name = $name;
	}

	public function getName(){
		return $this->name;
	}
	
	
}
?>