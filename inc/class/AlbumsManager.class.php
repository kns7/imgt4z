<?php
/**
 * Description of AlbumsManager
 *
 * @author miams
 */
class AlbumsManager {
	private $_db;
	
	public function __construct($db){
		$this->setDb($db);
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT id, name FROM albums WHERE id = :id");
		$q->bindValue(':id',$id);
		$q->execute();
		
		return new Album($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList($owner){
		$albums = array();
		$q = $this->_db->prepare("SELECT id, name FROM albums WHERE (ownerid = :ownerid OR ownerid = '0') ORDER BY name ASC");
		$q->bindValue(':ownerid',$owner);
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$albums[] = new Album($datas);
		}
		return $albums;
	}
		
	public function count($userid){
		$q = $this->_db->prepare("SELECT COUNT(id) as albums FROM albums WHERE ownerid = :ownerid");
		$q->bindValue(':ownerid',$userid);
		$q->execute();
		
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	public function add(Album $album){
		$q = $this->_db->prepare("INSERT INTO albums (name,ownerid) VALUES(:name,:ownerid)");
		$q->bindValue(":name",$album->name(), PDO::PARAM_STR);
		$q->bindValue(":ownerid",$album->ownerid(), PDO::PARAM_INT);
		
		return $q->execute();
	}
	
	public function update(Album $album){
		$q = $this->_db->prepare("UPDATE albums SET name = :name, ownerid = :ownerid WHERE id = :id");
		$q->bindValue(':name', $album->name(), PDO::PARAM_INT);
		$q->bindValue(':ownerid', $album->ownerid(), PDO::PARAM_STR);
		$q->bindValue(':id', $album->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function delete(Album $album,$deleteimages){
		if($this->_db->exec("DELETE FROM albums WHERE id = ". $album->id())){
			if($deleteimages == true){
				$imgManager = new ImagesManager($conn_img);
				/* Delete Images linked to this album */
				$imgManager->deleteFromAlbum($album->id());
				return 1;
			}else{
				/* Change Images Album ID to 1 */
				if($this->_db->exec("UPDATE images SET albumid = '1' WHERE albumid = ". $album->id())){
					return 1;
				}else{
					return 0;
				}
			}
		}else{
			return 0;
		}
	}
}

?>
