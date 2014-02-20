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
	private $_categorieid;
	private $_categorie;
	private $_orientation;
	private $_dateadd;
	private $_width;
	private $_height;
	
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
	public function categorieid(){ return $this->_categorieid; }
	public function categorie(){ return $this->_categorie; }
	public function orientation(){ return $this->_orientation; }
	public function dateadd(){ return $this->_dateadd; }
	public function width(){ return $this->_width; }
	public function height(){ return $this->_height; }
	
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
	public function setCategorieid($val){
		$val = (int) $val;
		if(is_int($val)){
			$this->_categorieid = $val;
		}
	}
	public function setCategorie($val){
		if(is_string($val)){
			$this->_categorie = $val;
		}
	}
	public function setOrientation($val){
		$val = (int) $val;
		if(is_int($val)){
			$this->_orientation = $val; 
		}
	}
	public function setDateadd($val){
		$val = new Datetime($val);
		$this->_dateadd = $val->format("d/m/Y H:i:s");
	}
	public function setWidth($val){
		$val = (int) $val;
		if($val > 0){ 
			$this->_width = $val; 
		}
	}
	public function setHeight($val){
		$val = (int) $val;
		if($val > 0){ 
			$this->_height = $val; 
		}
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
		$filename = "../storage/".$this->_userid."/".$this->_timestamp."d".$this->_userid.".jpg";
		
	}
	
	public function rotate($rotation,$db){
		$filename = "../storage/".$this->_userid."/".$this->_timestamp.".jpg";
		switch($rotation){
			case "L": $angle = 90; break;
			case "R": $angle = -90; break;
		}
		/* Create Image object from JPEG File */
		$source = imagecreatefromjpeg($filename);
		/* Rotate Image object */
		$rotate = imagerotate($source, $angle, 0);
		/* Save image object as JPEG */
		imagejpeg($rotate, $filename, 100);
		/* Destroy temp images */
		imagedestroy($source);
		imagedestroy($rotate);
		/* Update Image "Rotation" Info in DB */
		$return = $db->updateOrientation($this);
		return $return;
	}
}
?>