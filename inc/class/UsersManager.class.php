<?php
/**
 * Description of UsersManager
 *
 * @author nicolas.kapfer
 */
class UsersManager {
	private $_db;
	
	public function __construct($db){
		$this->setDb($db);
	}
	
	public function get($id){
		$id = (int) $id;
		$q = $this->_db->prepare("SELECT id, name, admin, step, field, ordre, logged FROM users WHERE id = :id");
		$q->bindValue(':id',$id);
		if(!($q->execute())){
			/* Créer une entrée avec le nouvel utilisateur */
		}else{
			return new User($q->fetch(PDO::FETCH_ASSOC));
		}
	}
	
	public function update(User $user){
		$q = $this->_db->prepare("UPDATE users SET step = :step, field = :field, ordre = :ordre WHERE id = :id");
		$q->bindValue(':step', $user->step(), PDO::PARAM_INT);
		$q->bindValue(':field', $user->field(), PDO::PARAM_STR);
		$q->bindValue(':ordre', $user->ordre(), PDO::PARAM_STR);
		$q->bindValue(':id', $user->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function updateLogged(User $user){
		$q = $this->_db->prepare("UPDATE users SET logged = :logged WHERE id = :id");
		$q->bindValue(':logged', $user->logged());
		$q->bindValue(':id', $user->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
}

?>
