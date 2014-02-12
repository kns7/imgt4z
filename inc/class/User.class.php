<?php
/**
 * Description of User
 * User Object, with user ID, Username and User rights (Admin or User)
 */
class User {
	private $_id;
	private $_name;
	private $_admin = 0;
	
	/**
	 * Build a new User object
	 * @param int $id		User ID
	 * @param string $name	Username
	 * @param int $admin	1: is admin, 0: is not admin
	 */
	public function __construct($id,$name,$admin){
		$this->_admin = $admin;
		$this->_id = $id;
		$this->_name = $name;
		$this->login();
	}
	/**
	 * Get User ID
	 * @return int User ID
	 */
	public function getId(){ return $this->_id; }
	/**
	 * Get Username 
	 * @return string Username
	 */
	public function getName(){ return $this->_name; }
	/**
	 * Get if User is admin or not
	 * @return boolean true: Admin, false: notadmin
	 */
	public function getAdmin(){ return $this->_admin; }
	
	/**
	 * Set User ID
	 * @param int $val UserID
	 */
	public function setId($val){ $this->_id = $val; }
	/**
	 * Set Username
	 * @param string $val Username
	 */
	public function setName($val){ $this->_name = $val; }
	/**
	 * Set user Admin or not
	 * @param int $val 1: admin, 0: not admin
	 */
	public function setAdmin($val){ if($val == 1) { $this->_admin = true; }else{ $this->_admin = false;} }
	
	/**
	 * Set $_SESSION vars with users infos (ID, Username, Admin)
	 */
	private function login(){
		$_SESSION['user_id'] = $this->_id;
		$_SESSION['user_name'] = $this->_name;
		$_SESSION['user_admin'] = $this->_admin;
	}
	/**
	 * Logout User, destroy $_SESSION variable
	 */
	public function logout(){
		session_destroy();
	}
}
?>
