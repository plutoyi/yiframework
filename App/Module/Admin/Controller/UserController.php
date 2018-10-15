<?php 
	
	class UserController extends YiBaseController{

		public function indexAction(){
			//print_r($this->getMemberDatas());exit;
			echo 'createUrl方法测试:' . $this->createUrl() . "<br>";
			
			Yi::app()->session->set('user1','session测试1');
			echo Yi::app()->session->get('user1')."<br>";
			$cookie = Yi::app()->cookie;
			$cookie->set('user1','cookie测试');
			echo $cookie->get('user1') . "<br>";
			$time_start = $this->getmicrotime();
			$data = User::find(2);
			print_r($data);
			$time_end = $this->getmicrotime();//结束
			echo $time = $time_end - $time_start;//输出运行总时间
		}
		
		private function getmicrotime(){
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}
		
		public function ipAction(){
			$p = $this->P('Ip.Ip');
			$ip = $p->getIp();
			echo $ip;
		}

	}
?>