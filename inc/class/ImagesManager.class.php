<?php
/**
 * Description of ImagesManager
 * @author nicolas.kapfer
 */
class ImagesManager{
	private $_db;
	
	public function __construct($db){
		$this->setDb($db);
	}
	
	public function add(Image $image){
		
	}
	
	public function delete(Image $image){
		$this->_db->exec("DELETE FROM images WHERE id = ". $image->id());
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT id, timestamp, orientation, permanent, userid, title, dateadd FROM images WHERE id = :id");
		$q->bindValue(':id',$id);
		$q->execute();
		
		return new Image($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList($userid){
		$images = array();
		$q = $this->_db->prepare("SELECT id, timestamp, orientation, permanent, userid, title, dateadd FROM images WHERE userid = :userid");
		$q->bindValue(':userid',$userid);
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$images[] = new Image($datas);
		}
		return $images;
	}
	
	public function update(Image $image){
		$q = $this->_db->prepare("UPDATE images SET permanent = :permanent, title = :title WHERE id = :id");
		$q->bindValue(':permanent', $image->permanent(), PDO::PARAM_INT);
		$q->bindValue(':title', $image->title(), PDO::PARAM_STR);
		$q->execute();
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
}

?>
