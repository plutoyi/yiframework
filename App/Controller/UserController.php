<?php 
	
	class UserController extends YiBaseController{

		public function indexAction(){
			//$data = xhprof_disable(); 
			//print_r($data);exit;
			
			//include_once dirname(YIFW_PATH) . "/xhprof_lib/utils/xhprof_lib.php";  
			//include_once dirname(YIFW_PATH) . "/xhprof_lib/utils/xhprof_runs.php";  
			//$objXhprofRun = new XHProfRuns_Default(); 
			//$run_id = $objXhprofRun->save_run($data, "xhprof");
			//var_dump($run_id);
			
			echo 'createUrl方法测试:' . $this->createUrl('admin','user','index1',array('a'=>1,'b'=>1)) . "<br>";
			Yi::app()->session->set('user1','session测试1');
			echo Yi::app()->session->get('user1')."<br>";
			$cookie = Yi::app()->cookie;
			$cookie->set('user1','cookie测试');
			echo $cookie->get('user1') . "<br>";
			
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