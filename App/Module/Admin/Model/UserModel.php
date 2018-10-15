<?php 
	
	class User extends ModelORM{
		protected $table = 'user';
		protected $primaryKey = 'uid';
		//protected $timestamps = false;
		
		public function insert(){
			$user = new User;
			$user->username = 'John';
			$user->sex = '1';
			$user->save();
		}
		
		public function del(){
			$user = User::find(5);
			$user->delete();
		}
		
		public function getbyid($id){
			$user = User::find($id);
			return $user->username;
		}
	}
?>