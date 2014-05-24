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
		$q = $this->_db->prepare("INSERT INTO images (timestamp,orientation,albumid,userid,title,dateadd,width,height) VALUES(:timestamp,:orientation,:albumid,:userid,:title,:dateadd,:width,:height)");
		$q->bindValue(":timestamp",$image->timestamp(), PDO::PARAM_STR);
		$q->bindValue(":orientation",$image->orientation(), PDO::PARAM_INT);
		$q->bindValue(":albumid",$image->albumid(), PDO::PARAM_INT);
		$q->bindValue(":userid",$_SESSION['user_id'], PDO::PARAM_INT);
		$q->bindValue(":title",$image->title(), PDO::PARAM_STR);
		$q->bindValue(":dateadd",date("Y-m-d H:i:s"),PDO::PARAM_STR);
		$q->bindValue(":width",$image->width(), PDO::PARAM_INT);
		$q->bindValue(":height",$image->height(), PDO::PARAM_INT);
		
		return $q->execute();
		
	}
	
	public function delete(Image $image){
		if(unlink('../storage/'.$image->userid().'/'.$image->timestamp().'.jpg')){
			$this->_db->exec("DELETE FROM images WHERE id = ". $image->id());
			return 1;
		}else{
			return 0;
		}
	}
	
	public function deleteFromAlbum($albumid){
		$images = array();
		$q = $this->_db->prepare("SELECT images.id as id, timestamp, orientation, albumid, albums.name as album, userid, title, dateadd, width, height FROM images Inner Join albums ON images.albumid = albums.id WHERE albumid = :albumid ORDER BY dateadd DESC");
		$q->bindValue(':albumid',$userid);
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$images[] = new Image($datas);
		}
		foreach($images as $img){
			$this->delete($img);
		}
	}
	
	public function count($userid){
		$q = $this->_db->prepare("SELECT COUNT(id) as elements FROM images WHERE userid = :userid");
		$q->bindValue(':userid',$userid);
		$q->execute();
		
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT images.id as id, timestamp, orientation, albumid, albums.name as album, userid, title, dateadd, width, height FROM images Inner Join albums ON images.albumid = albums.id WHERE images.id = :id");
		$q->bindValue(':id',$id);
		$q->execute();
		
		return new Image($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList($userid){
		$images = array();
		$q = $this->_db->prepare("SELECT images.id as id, timestamp, orientation, albumid, albums.name as album, userid, title, dateadd, width, height FROM images Inner Join albums ON images.albumid = albums.id WHERE userid = :userid ORDER BY album ASC, dateadd DESC");
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
			SELECT images.id as id, timestamp, orientation, albumid, albums.name as album, userid, title, dateadd, width, height 
			FROM images Inner Join albums ON images.albumid = albums.id 
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
		$q = $this->_db->prepare("UPDATE images SET albumid = :albumid, title = :title WHERE id = :id");
		$q->bindValue(':albumid', $image->albumid(), PDO::PARAM_INT);
		$q->bindValue(':title', $image->title(), PDO::PARAM_STR);
		$q->bindValue(':id', $image->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function updateOrientation(Image $image){
		if($image->orientation() == 1){ $orientation = 0; }else{ $orientation = 1; }
		$q = $this->_db->prepare("UPDATE images SET orientation = :orientation, width = :width, height = :height WHERE id = :id");
		$q->bindValue(":orientation",$orientation, PDO::PARAM_INT);
		$q->bindValue(":width",$image->height(),PDO::PARAM_INT);
		$q->bindValue(":height",$image->width(),PDO::PARAM_INT);
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
