<?php
/**
 * Description of Image
 * Image Object
 */
class Image {
	private $_id;
	private $_userid;
	private $_title = "";
	private $_timestamp;
	private $_permanent;
	private $_extension;
	private $_dateadd;
	
	/**
	 * Build a new Image object
	 * @param array	$datas Array of collected Datas (from PDO)
	 */
	public function __construct(array $datas) {
		$this->hydrate($datas);
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
	
	/* Getters */
	public function id(){ return $this->_id; }
	public function userid(){ return $this->_userid; }
	public function timestamp(){ return $this->_timestamp; }
	public function title(){ return $this->_title; }
	public function permanent(){ return $this->_permanent; }
	public function extension(){ return $this->_extension; }
	public function dateadd(){ return $this->_dateadd; }
	
	/* Setters */
	public function setId($val){
		$val = (int) $val;
		if($val > 0){ 
			$this->_id = $val; 
		}
	}
	public function setTimestamp($val){
		if(is_string($val)){
			$this->_timestamp = $val; 
		}
	}
	public function setTitle($val){
		if(is_string($val)){
			$this->_title = $val; 
		}
	}
	public function setUserid($val){
		$val = (int) $val;
		if(is_int($val)){
			$this->_userid = $val;
		}
	}
	public function setPermanent($val){
		$val = (int) $val;
		if(is_int($val)){
			$this->_permanent = $val;
		}
	}
	public function setExtension($val){
		if(is_string($val)){
			$this->_extension = $val; 
		}
	}
	
	public function setDateadd($val){
		$val = new Datetime($val);
		$this->_dateadd = $val->format("d/m/Y H:i:s");
	}
	
	public function png2jpg(){
		$image = imagecreatefrompng($filePath);
		$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
		imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
		imagealphablending($bg, TRUE);
		imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagedestroy($image);
		$quality = 92; // 0 = worst / smaller file, 100 = better / bigger file 
		imagejpeg($bg, $filePath . ".jpg", $quality);
		ImageDestroy($bg);
	}
	
	public function resize(){
		
	}
	
	public function rotate(){
		
	}
}

?>
