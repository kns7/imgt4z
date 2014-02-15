<?php
/**
 * Description of Categorie
 *
 * @author miams
 */
class Categorie {
	private $_id;
	private $_name;
	
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
}

?>
