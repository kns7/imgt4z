<?php
/**
 * Description of Album
 *
 * @author miams
 */
class Album {
	private $_id;
	private $_name;
	private $_ownerid;
	
	public function __construct(array $datas) {
		$this->hydrate($datas);
	}
	
	public function hydrate(array $datas){
		foreach ($datas as $key => $value){
			$method = 'set'.ucfirst($key);
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}
	
	/* Getters */
	public function id(){ return $this->_id; }
	public function name(){ return $this->_name; }
	public function ownerid(){ return $this->_ownerid; }
	
	/* Setters */
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
	public function setOwnerid($val){
		$val = (int) $val;
		if(is_int($val)){
			$this->_ownerid = $val;
		}
	}
}

?>
