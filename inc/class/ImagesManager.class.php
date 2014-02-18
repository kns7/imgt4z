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
		if(unlink('../storage/'.$image->userid().'/'.$image->timestamp().'.jpg')){
			$this->_db->exec("DELETE FROM images WHERE id = ". $image->id());
			return 1;
		}else{
			return 0;
		}
	}
	
	public function count($userid){
		$q = $this->_db->prepare("SELECT COUNT(id) as total FROM images WHERE userid = :userid");
		$q->bindValue(':userid',$userid);
		$q->execute();
		
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT images.id as id, timestamp, orientation, categorieid, categories.name as categorie, userid, title, dateadd FROM images Inner Join categories ON images.categorieid = categories.id WHERE images.id = :id");
		$q->bindValue(':id',$id);
		$q->execute();
		
		return new Image($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList($userid){
		$images = array();
		$q = $this->_db->prepare("SELECT images.id as id, timestamp, orientation, categorieid, categories.name as categorie, userid, title, dateadd FROM images Inner Join categories ON images.categorieid = categories.id WHERE userid = :userid ORDER BY dateadd DESC");
		$q->bindValue(':userid',$userid);
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$images[] = new Image($datas);
		}
		return $images;
	}
	
	public function getListPaginated($userid,$start=0){
		$limit = 10;
		$images = array();
		$q = $this->_db->prepare("
			SELECT images.id as id, timestamp, orientation, categorieid, categories.name as categorie, userid, title, dateadd 
			FROM images Inner Join categories ON images.categorieid = categories.id 
			WHERE userid = :userid 
			ORDER BY dateadd DESC 
			LIMIT $start, $limit");
		$q->bindValue(':userid',$userid);
		/*$q->bindValue(':start',$start);
		$q->bindValue(':limit',$limit);*/
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$images[] = new Image($datas);
		}
		return $images;
	}
	
	public function update(Image $image){
		$q = $this->_db->prepare("UPDATE images SET categorieid = :categorieid, title = :title WHERE id = :id");
		$q->bindValue(':categorieid', $image->categorieid(), PDO::PARAM_INT);
		$q->bindValue(':title', $image->title(), PDO::PARAM_STR);
		$q->bindValue(':id', $image->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function updateOrientation(Image $image){
		if($image->orientation() == 1){ $orientation = 0; }else{ $orientation = 1; }
		$q = $this->_db->prepare("UPDATE images SET orientation = :orientation WHERE id = :id");
		$q->bindValue(":orientation",$orientation, PDO::PARAM_INT);
		$q->bindValue(":id",$image->id(),PDO::PARAM_INT);
		if($q->execute()){
			return $orientation;
		}
		
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
}

?>
