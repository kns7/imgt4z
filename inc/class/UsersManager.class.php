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
		$q = $this->_db->prepare("SELECT id, name, admin, step, field, ordre, logged, quota FROM users WHERE id = :id");
		$q->bindValue(':id',$id);
		if(!($q->execute())){
			/* Créer une entrée avec le nouvel utilisateur */
		}else{
			return new User($q->fetch(PDO::FETCH_ASSOC));
		}
	}
	
	public function getList(){
		$q = $this->_db->prepare("SELECT id, name, admin, step, field, ordre, logged, quota FROM users WHERE 1");
		$q->execute();
		
		while($datas = $q->fetch(PDO::FETCH_ASSOC)){
			$users[] = new User($datas);
		}
		return $users;
	}
	
	public function updateSettings(User $user){
		$q = $this->_db->prepare("UPDATE users SET step = :step, field = :field, ordre = :ordre WHERE id = :id");
		$q->bindValue(':step', $user->step(), PDO::PARAM_INT);
		$q->bindValue(':field', $user->field(), PDO::PARAM_STR);
		$q->bindValue(':ordre', $user->ordre(), PDO::PARAM_STR);
		$q->bindValue(':id', $user->id(), PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function updatePassword($password){
		$q = $this->_db->prepare("UPDATE users SET password = :pwd WHERE id = :id");
		$q->bindValue(':pwd', md5($password), PDO::PARAM_STR);
		$q->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		return $q->execute();
	}
	
	public function login($logininfos){
		$username = $logininfos['username'];
		$password = $logininfos['password'];
		switch($logininfos['auth']){
			case "local":
				$q = $this->_db->prepare("SELECT id, name, admin, step, field, ordre, logged, quota FROM users WHERE name = :name AND password = :password");
				$q->bindValue(':name',$username);
				$q->bindValue(':password',$password);
				$q->execute();
				$result = $q->fetch(PDO::FETCH_ASSOC);
				if(empty($result)){
					return "error";
				}else{
					$user = new User($result);
					$user->setLogged(date("Y-m-d H:i:s"));
					$this->updateLogged($user);
				}
			break;
			
			case "phpbb":
				$q = $this->_db->prepare("SELECT id, name, admin, step, field, ordre, logged, quota FROM users WHERE id = :id");
				$q->bindValue(':id',$logininfos['phpbbid']);
				$q->execute();
				$result = $q->fetch(PDO::FETCH_ASSOC);
				if(empty($result)){
					return "error";
				}else{
					$user = new User(array(
						'id' => $logininfos['phpbbid'],
						'name' => $logininfos['phpbbname'],
						'admin' => $logininfos['admin'],
						'step' => $user['step'],
						'field' => $user['field'],
						'ordre' => $user['ordre'],
						'quota' => $user['quota'],
						'logged' => $user['logged']
					));
				}
			break;
		}
		return $user;
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
