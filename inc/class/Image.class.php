<?php
/**
 * Description of Image
 * Image Object
 */
class Image {
	private $_id;
	private $_userid;
	private $_tempimg;
	private $_url;
	private $_extension;
	
	/**
	 * Build a new Image object
	 * @param type $id			Image ID
	 * @param type $userid		User ID
	 * @param type $tempimg		1: if image is temporary, 0: permanent
	 * @param type $url			Image URL
	 * @param type $extension	Image Extension
	 */
	public function __construct($id,$userid,$tempimg,$url,$extension) {
		$this->_id = $id;
		$this->_userid = $userid;
		$this->_tempimg = $tempimg;
		$this->_url = $url;
		$this->_extension = $extension;
	}
	/**
	 * get Image ID
	 * @return int Image ID
	 */
	public function getId(){ return $this->_id; }
	/**
	 * get User ID
	 * @return int User ID
	 */
	public function getUserId(){ return $this->_userid; }
	/**
	 * 
	 * @return type
	 */
	public function getTempImg(){ return $this->_tempimg; }
	
	public function getUrl(){ return $this->_url; }
	
	public function getExtension(){ return $this->_extension; }
	
	
	public function setId($val){ $this->_id = $val; }
	
	public function setUserId($val){ $this->_userid = $val; }
	
	public function setTempImg($val){ if ($val == 1){ $this->_tempimg = true; }else{ $this->_tempimg = false; } }
	
	public function setUrl($val){ $this->_url = $val; }
	
	public function setExtension($val){ $this->_extension = $val; }
}

?>
