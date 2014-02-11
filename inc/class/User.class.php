<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author nicolas.kapfer
 */
class User {
	private $_id;
	private $_name;
	private $_admin = 0;
	
	public function __construct($id,$name,$admin){
		$this->_admin = $admin;
		$this->_id = $id;
		$this->_name = $name;
		$this->login();
	}
	
	public function getId(){ return $this->_id; }
	public function getName(){ return $this->_name; }
	public function getAdmin(){ return $this->_admin; }
	
	public function setId($val){ $this->_id = $val; }
	public function setName($val){ $this->_name = $val; }
	public function setAdmin($val){ if($val == 1) { $this->_admin = true; }else{ $this->_admin = false;} }
	
	private function login(){
		$_SESSION['user_id'] = $this->_id;
		$_SESSION['user_name'] = $this->_name;
		$_SESSION['user_admin'] = $this->_admin;
	}
	public function logout($id){
		session_destroy();
	}
}

?>
