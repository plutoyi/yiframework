<?php

 /**
  * @example
  * class testAction extends Action {
  *     public function execute() {
  *         $page = $this->p('Pages');
  * 		$page->setTotal(1000);
  * 		$page->setPrepage(20);
  * 		echo $page->show();
  *     }
  * }
  */
class Pages {

	/**
	 * 每页的记录数
	 *
	 * @var int
	 */
	private $perpage= 20;

	/**
	 * 总记录数
	 *
	 * @var int
	 */
	private $total;

	/**
	 * 当前页
	 *
	 * @var int
	 */
	private $currentPage = 1;

	/**
	 * 总页数
	 *
	 * @var int
	 */
	private $pages;

	/**
	 * url参数
	 *
	 * @var string
	 */
	private $param;

	/**
	 * 构造器
	 *
	 */
	public function __construct() {
		$this->getCurrentPage();
		$this->getParam();
	}

	/**
	 * 设置总记录数
	 *
	 * @param int $total
	 */
	public function setTotal($total) {
		$this->total = (int)$total;
	}

	/**
	 * 设置每页的记录数
	 *
	 * @param int $prepage
	 */
	public function setPrepage($prepage) {
		$this->perpage = (int)$prepage;
	}

	/**
	 * 获取当前页数
	 *
	 */
	private function getCurrentPage() {
		isset($_GET['page'])  ? $page = (int)$_GET['page'] : $page = 1;
		$page >= 1 && $this->currentPage = $page;
	}

	/**
	 * 获取当前页数
	 * 
	 * @return int
	 */
	public function getNowPage() {
		return $this->currentPage;
	}

	/**
	 * 获取MySQL分页SQL的LIMIT语句
	 * 
	 * @return string
	 */
	public function getSqlLimit() {
		return ($this->currentPage - 1) * $this->perpage . ',' . $this->perpage;
	}


	/**
	 * 获取url参数
	 *
	 */
	private function getParam() {
		unset($_GET['page']);
		if(isset($_GET) && !empty($_GET)){
			$str = array();
			foreach($_GET as $key => $value){
				if(isset($value) && ($key != "CONTROLLER") && ($key != "ACTION")){
					$str[] = $key . "=" . $value;
				}
			}
			$str = "&" . implode("&",$str);
		}else{
			$str ='';
		}
		return $str;
	}
	/**
	 * 创建连接
	 *
	 * @param int $page
	 * @return string
	 */
	/*private function getLink($page) {
		return $this->param . 'page/' . $page;
	}*/

	/**
	 * 获取第一页
	 *
	 * @return string
	 */
	private function getFirstPage() {
		if($this->currentPage == 1) {
			return " ";
		} else {
			return ' <a href="' .ROOT.'index.php/trip/index?page=1' .$this->getParam() . '"  class="btn btn-white"><i class="fa fa-angle-double-left"></i></a> ';
		}
	}

	/**
	 * 获取最后一页
	 *
	 * @return string
	 */
	private function getLastPage() {
		//if($this->currentPage == $this->pages) {
			//return " 尾页 ";
		//} else {
			return ' <a href="' .ROOT.'index.php/trip/index?page='.$this->pages . $this->getParam() .'" class="btn btn-white"><i class="fa fa-angle-double-right"></i></a> ';
		//}
	}

	/**
	 * 获取上一页
	 *
	 * @return string
	 */
	private function getPrePage() {
		return ' <a href="' . ROOT . 'index.php/trip/index?page=' .($this->currentPage-1) .$this->getParam() . '"  class="btn btn-white"><i class="fa fa-angle-left"></i></a> ';
	}

	/**
	 * 获取下一页
	 *
	 * @return string
	 */
	private function getNextPage() {
		return ' <a href="' . ROOT . 'index.php/trip/index?page=' . ($this->currentPage+1) .$this->getParam() . '"  class="btn btn-white"><i class="fa fa-angle-right"></i></a> ';
	}

	/**
	 * 显示分页
	 *
	 * @return <string>
	 */
	public function show() {
		if (!isset($this->total)) {
			throw new YiException('无法找到总记录数!');
		}
		
		$this->pages = ceil($this->total / $this->perpage);
		if ($this->currentPage < 5) {
			$begin = 1;
			$end = 10;
		} else if ($this->currentPage > $this->pages - 10) {
			$begin = $this->pages - 10;
			$end = $this->pages;
		} else {
			$begin = $this->currentPage - 4;
			$end = $this->currentPage + 5;
		}
		$begin < 1 && $begin = 1;
		$end > $this->pages && $end = $this->pages;
		if($this->total > $this->perpage) {
			$page = $this->getFirstPage();
			if($this->currentPage > 1) {
				$page .=  $this->getPrePage();
			}
			for($i = $begin; $i <= $end; $i++) {
				if($i == $this->currentPage) {
					$page .=  ' <a href="' . ROOT . 'index.php/trip/index?page=' . $i . $this->getParam() .'"  class="btn btn-white active">' . $i .'</a> ';
				} else {
					$page .= ' <a href="' . ROOT . 'index.php/trip/index?page=' . $i . $this->getParam() .'">' . $i .'</a> ';
				}
			}
			if($this->currentPage < $this->pages) {
				$page .= $this->getNextPage();
				$page .= $this->getLastPage();
			}
		} else {
			$page = $this->getFirstPage();
		}
		return $page;
	}

}