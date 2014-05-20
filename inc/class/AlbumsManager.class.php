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
}

?>
