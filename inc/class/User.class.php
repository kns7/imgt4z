<?php
/**
 * Description of User
 * User Object, with user ID, Username and User rights (Admin or User)
 */
class User {
	private $_id;
	private $_name;
	private $_admin = 0;
	private $_step = 10;
	private $_field = "dateadd";
	private $_ordre = "DESC";
	
	/**
	 * Build a new User object
	 * @param array	$datas Array of collected Datas (from PDO)
	 */
	public function __construct(array $datas) {
		$this->hydrate($datas);
		$this->login();
	}
	/**
	 * Hydrate datas for a new object
	 * @param array $datas Array of datas 
	 */
	public function hydrate(array $datas){
		foreach ($datas as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	
	
	public function id(){ return $this->_id; }
	public function name(){ return $this->_name; }
	public function admin(){ return $this->_admin; }
	public function step(){ return $this->_step; }
	public function field(){ return $this->_field; }
	public function ordre(){ return $this->_ordre; }
	
	public function setId($val){ 
		$val = (int) $val;
		if(is_int($val)){
			$this->_id = $val; 
		}
	}
	public function setName($val){ 
		if(is_string($val)){
			$this->_name = $val; 
		}
	}
	public function setAdmin($val){ 
		$val = (int) $val;
		if(is_int($val)){
			$this->_admin = $val; 
		}
	}
	public function setStep($val){ 
		$val = (int) $val;
		if(is_int($val)){
			$this->_step = $val; 
		}
	}
	public function setField($val){ 
		if(is_string($val)){
			$this->_field = $val; 
		}
	}
	public function setAsc($val){ 
		if(is_string($val)){
			$this->_ordre = $val; 
		}
	}
	
	private function login(){
		$_SESSION['user_id'] = $this->_id;
		$_SESSION['user_name'] = $this->_name;
		$_SESSION['user_admin'] = $this->_admin;
		$_SESSION['user_field'] = $this->_field;
		$_SESSION['user_ordre'] = $this->_ordre;
		$_SESSION['user_step'] = $this->_step;
	}
	
	public function logout(){
		session_destroy();
	}
}
?>
