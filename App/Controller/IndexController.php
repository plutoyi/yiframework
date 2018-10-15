<?php 
	class IndexController extends BaseController{

		public function indexAction(){
			echo "this is indexAction<br>";
		}

		public function beforeAction(){
			echo "this is beforeAction<br>";	
		}

		public function afterAction(){
			echo "this is afterAction<br>";
		}

		public function beforeIndexAction(){
			echo "this is beforeIndexAction<br>";
		}

		public function afterIndexAction(){
			echo "this is afterIndexAction<br>";
		}

	}
?>