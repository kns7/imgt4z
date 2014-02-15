<?php
/**
 * Description of CategoriesManager
 *
 * @author miams
 */
class CategoriesManager {
	private $_db;
	
	public function __construct($db){
		$this->setDb($db);
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT id, name FROM categories WHERE id = :id");
		$q->bindValue(':id',$id);
		$q->execute();
		
		return new Categorie($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public function getList(){
		$categories = array();
		$q = $this->_db->query("SELECT id, name FROM categories ORDER BY name ASC");
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$categories[] = new Categorie($datas);
		}
		return $categories;
	}
}

?>
