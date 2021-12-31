<?php
require 'rb-mysql.php';
class RocketAuth{	
	public $auth_table = "default";

	// Authintifiacation constructor
	// Connecting with My-SQL DataBase
	public function __construct($host,$username,$password,$dbname,$table){
		R::setup('mysql:host='.$host.';dbname='.$dbname,$username,$password);
		if(!R::testConnection()){
			die("Connection failure");
		}else {
			$this->auth_table = $table;
			return 1;
		}
	}	
	public function custom($request,$dataArray)
	{
		$data = R::exec($request,$dataArray);
		return $data;
	}
	public function getAll($request,$dataArray){
		$data = R::getAll($request,$dataArray);
		return $data;
	}
	public function removeIt($cellName,$cellValue,$table){
		$bean = R::findOne($table,$cellName.'= ?',array($cellValue));
		R::trash($bean);
		return 1;
	}
	public function update($itemId,$cellName,$cellValue,$table){
		$data = R::load($table,$itemId);
		$data->$cellName = $cellValue;
		R::store($data);
	}
	public function updateCell($table,$item_id,$data_array){
		$data = R::load($table,$item_id);
		foreach($data_array as $key => $value){
			$data->$key = $value;
		}
		R::store($data);
	}
	public function getCount($table,$params,$dataArray)
	{
		 $numOfcells = R::count($table,$params,$dataArray);
		 return $numOfcells;
	}
	// Check input form for unique
	// Function gets assosiated array with cell name and its value
	public function checkDatas($dataArray){ // value_1, value_2 , value_3...
		$result = array();
		foreach ($dataArray as $key => $value) {
			$count = R::count($this->auth_table,$key.'= ?',array($value));
			if($count > 0){
				$stat = 1;
			}else $stat = 0;
			$result[] = $stat;
		}
		return $result;
	}
	// Check if user data is unique
	public function uniqueUser($status){
		if(in_array(0, $status)){ // If there is some value that exists in database
			return 0;
		}else return 1; //If tere is no similarity
	}
	// Just hashes password
	public function PWDhash($string){
		return password_hash($string, PASSWORD_DEFAULT);
	}

	// Verify the password, compares user written password with hashed password from Database
	public function PWDverify($cellKey,$cellValue, $userPassword){
		$user  = R::find($this->auth_table,$cellKey.'= ?',array($cellValue));
		foreach($user as $key => $value){
			$hashedPassword = $value->password;
		}
		if(isset($hashedPassword)){
			if(password_verify($userPassword, $hashedPassword)){
			return 1;
			}else return 0;
		}else return 0;
	}
	// Input several datas into database
	public function DBInput($formData,$table){
		if(!$table){
			$table = $this->auth_table;
		}
		$user = R::dispense($table);
		foreach($formData as $key => $value){
			$user->$key = $value;
		}
		$store = R::store($user);
		if($store){
			return 1;
		}else return 0;
	}
	// Get User Data
	public function getData($cellKey,$cellValue,$table = 0){
		if(!$table){
			$table = $this->auth_table;
		}
		$user  = R::findOne($table,$cellKey.'= ?',array($cellValue));
		if(isset($user)){
			$user_data = array();
	        foreach ($user as $key => $value) {
	            $user_data[$key] = $value;
	        }
			return $user_data;
		}else{
			return 0;
		}
	}
	public function getRow($id,$table){
		$return_data = array();
		$data = R::load($table,$id);
		foreach ($data as $key => $value) {
	            $return_data[$key] = $value;
	    }
	    return $return_data;

	}
	// Close connection with database
	public function DBClose(){
		R::close();
	}
}
?>