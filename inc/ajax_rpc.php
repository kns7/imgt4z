<?php
session_start();
/* Include Config File */
include('config.php');
/* Include Classes */
include('class/UsersManager.class.php');
include('class/AlbumsManager.class.php');
include('class/ImagesManager.class.php');
include('class/User.class.php');
include('class/Album.class.php');
include('class/Image.class.php');

/* Create PDO Connector for Images */
$imagesManager = new ImagesManager($conn_img);
/* Create PDO Connector for Albums */
$albumsManager = new AlbumsManager($conn_img);
/* Create PDO Connector for Users */
$usersManager = new UsersManager($conn_img);

/* Check if User is logged */
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_POST['action'])){
	$user = $usersManager->get($_SESSION['user_id']);
	$rArray = array();
	switch($_POST['action']){
		case "initload":
			/* Albums */
			$albums = $albumsManager->getList($_SESSION['user_id']);
			$count_albums = $albumsManager->count($_SESSION['user_id']);
			/* Images */
			$images = $imagesManager->getList($_SESSION['user_id']);
			$count_images = $imagesManager->count($_SESSION['user_id']);
			
			/* Build JSON Array */
			$rArray['storage']['images'] = $count_images;
			$rArray['storage']['albums'] = $count_albums;
			$rArray['settings']['auth'] = $auth_type;
			if(!empty($albums)){
				$i = 0;
				foreach($albums as $album){
					$rArray['albums'][$i]['id'] = $album->id();
					$rArray['albums'][$i]['name'] = $album->name();
					$j = 0;
					foreach($images as $image){
						if($image->albumid() == $album->id()){
							$rArray['albums'][$i]['images'][$j]['id'] = $image->id();
							$rArray['albums'][$i]['images'][$j]['timestamp'] = $image->timestamp();
							$rArray['albums'][$i]['images'][$j]['title'] = $image->title();
							$rArray['albums'][$i]['images'][$j]['orientation'] = $image->orientation();
							$rArray['albums'][$i]['images'][$j]['dateadd'] = $image->dateadd();
							$rArray['albums'][$i]['images'][$j]['userid'] = $image->userid();
							$rArray['albums'][$i]['images'][$j]['width'] = $image->width();
							$rArray['albums'][$i]['images'][$j]['height'] = $image->height();
							$j++;
						}
					}
					$i++;
				}
			}
			
			/* Textes d'aide */
			$rArray['help']['upload'] = "<p>Via ce formulaire, tu peux envoyer une image sur T4Zone Images.<br/>Les formats d'images acceptés sont les suivants: JPEG, PNG et GIF.</p><p>L'image sera automatiquement redimensionnée en 800x600 (ou 600x800), un Watermark (Tatouage Numérique) sera ajouté à l'image et enfin un lien sera automatiquement généré pour pouvoir la poster sur le forum.<br/>Si l'image est mal orientée, il te sera possible une fois envoyée, de la faire tourner pour la mettre dans le bon sens!</p><p><em>S'il devait y avoir un problème lors de l'envoi, merci de contacter les admins du Forum T4Zone <a href='mailto:admin@t4zone.org?subject=Erreur Upload T4Zone Images'>admins@t4zone.org</a></em></p>";
			
			/* User Settings */
			$rArray['user']['field'] = $_SESSION['user_field'];
			$rArray['user']['ordre'] = $_SESSION['user_ordre'];
			$rArray['user']['step'] = $_SESSION['user_step'];
			
			/* Comptage des images */
			$storage = opendir("../storage/".$_SESSION['user_id']);
			$size = 0;
			while($file = readdir($storage)){
				if(!in_array($file, Array("..","."))){
					if(!is_dir("../storage/".$_SESSION['user_id']."/$file")){
						$size += filesize("../storage/".$_SESSION['user_id']."/$file");
					}
				}
			}
			$rArray['storage']['size'] = $size;
			$rArray['storage']['quota'] = $_SESSION['user_quota'];
		break;
		
		case "logout":
			session_destroy();
		break;
		
		case "home":
			/* Define in Array what to do */
			$rArray['template'] = "home";
		break;
	
		case "upload":
			/* Define in Array what to do */
			$rArray['template'] = "upload";
			$rArray['help'] = "<p>Via ce formulaire, tu peux envoyer une image sur T4Zone Images.<br/>Les formats d'images acceptés sont les suivants: JPEG, PNG et GIF.</p><p>L'image sera automatiquement redimensionnée en 800x600 (ou 600x800), un Watermark (Tatouage Numérique) sera ajouté à l'image et enfin un lien sera automatiquement généré pour pouvoir la poster sur le forum.<br/>Si l'image est mal orientée, il te sera possible une fois envoyée, de la faire tourner pour la mettre dans le bon sens!</p><p><em>S'il devait y avoir un problème lors de l'envoi, merci de contacter les admins du Forum T4Zone <a href='mailto:admin@t4zone.org?subject=Erreur Upload T4Zone Images'>admins@t4zone.org</a></em></p>";
			/* Get Albums List */
			$albums = $albumsManager->getList();
			if(!empty($albums)){
				$i = 0;
				foreach($albums as $album){
					$rArray['albums'][$i]['id'] = $album->id();
					$rArray['albums'][$i]['name'] = $album->name();
					$i++;
				}
			}
			$albums = $albumsManager->getList();
			if(!empty($albums)){
				$i = 0;
				foreach($albums as $album){
					$rArray['albums'][$i]['id'] = $album->id();
					$rArray['albums'][$i]['name'] = $album->name();
					$i++;
				}
			}
		break;
	
		case "images":
			if(!isset($_POST['pagingstart'])){
				$start = 0;
			}else{
				$start = $_POST['pagingstart'];
			}
			/* Define in Array what to do */
			$rArray['template'] = "images";
			/* Get All Images per User */
			$images = $imagesManager->getList($_SESSION['user_id']);
			$count = $imagesManager->count($_SESSION['user_id']);
			$rArray['count'] = $count;
			if(!empty($images)){
				$i = 0;
				foreach($images as $image){
					$rArray['images'][$i]['id'] = $image->id();
					$rArray['images'][$i]['timestamp'] = $image->timestamp();
					$rArray['images'][$i]['title'] = $image->title();
					$rArray['images'][$i]['orientation'] = $image->orientation();
					$rArray['images'][$i]['album'] = $image->albumid();
					$rArray['images'][$i]['dateadd'] = $image->dateadd();
					$rArray['images'][$i]['userid'] = $image->userid();
					$rArray['images'][$i]['width'] = $image->width();
					$rArray['images'][$i]['height'] = $image->height();
					$i++;
				}
			}
		break;
		
		case "image":
			/* Define in Array what to do */
			$rArray['template'] = "image";
			/* Get Image per ID */
			$image = $imagesManager->get($_POST['id']);
			if(!empty($image)){
				$rArray['image']['id'] = $image->id();
				$rArray['image']['timestamp'] = $image->timestamp();
				$rArray['image']['title'] = $image->title();
				$rArray['image']['orientation'] = $image->orientation();
				$rArray['image']['album'] = $image->album();
				$rArray['image']['albumid'] = $image->albumid();
				$rArray['image']['dateadd'] = $image->dateadd();
				$rArray['image']['userid'] = $image->userid();
				$rArray['image']['width'] = $image->width();
				$rArray['image']['height'] = $image->height();
			}
			/* Get Albums List */
			$albums = $albumsManager->getList();
			if(!empty($albums)){
				$i = 0;
				foreach($albums as $album){
					$rArray['albums'][$i]['id'] = $album->id();
					$rArray['albums'][$i]['name'] = $album->name();
					$i++;
				}
			}
		break;
		
		case "btn":
			if($_POST['imgid'] != "" && !empty($_POST['imgid']) && isset($_POST['imgid'])){ 
				$image = $imagesManager->get($_POST['imgid']);
			}
			if($_POST['albumid'] != "" && !empty($_POST['albumid']) && isset($_POST['albumid'])){ 
				$album = $albumsManager->get($_POST['albumid']);
			}
			switch($_POST['what']){
				case "rotatel":
				case "rotater":
					if($_POST['what'] == "rotatel"){ $rotate = "L"; }else{ $rotate = "R"; }
					$orientation = $image->rotate($rotate,$imagesManager);
					if($orientation == 1){
						$rArray['orientation'] = 'vertical';
					}else{
						$rArray['orientation'] = 'horizontal';
					}
					$rArray['url'] = "storage/".$image->userid()."/".$image->timestamp().".jpg";
				break;
				
				case "delete":
					$rArray['result'] = $imagesManager->delete($image);
				break;
			
				case "deletealbum":
					if($albumsManager->delete($album) == 1){
						switch($_POST['deleteimages']){
							case "2":
								$rArray['result'] = 1;
							break;
						
							case "1":
								$imagesManager->deleteFromAlbum($album->id());
								$rArray['result'] = 1;
							break;
						
							case "0":
								$imagesManager->moveFromAlbum($album->id());
								$rArray['result'] = 1;
							break;
						}
					}else{
						$rArray['result'] = 0;
					}
				break;
			}
		break;
	
		case "save":
			switch($_POST['what']){
				case "album":
					if($_POST['albumid'] != "" && !empty($_POST['albumid']) && isset($_POST['albumid'])){ 
						/* Update Album */
						$album = $albumsManager->get($_POST['albumid']);
						$album->setName($_POST['albumtitle']);
						$album->setOwnerid($_SESSION['user_id']);
						if($albumsManager->update($album)){
							$rArray['result'] = 1;
						}else{
							$rArray['result'] = 0;
						}
					}else{
						/* Add Album */
						$album = new Album(array(
							'name' => $_POST['albumtitle'],
							'ownerid' => $_SESSION['user_id']
						));
						if($albumsManager->add($album)){
							$rArray['result'] = 1;
						}else{
							$rArray['result'] = 0;
						}
					}
				break;
			
				case "image":
					if($_POST['imgid'] != "" && !empty($_POST['imgid']) && isset($_POST['imgid'])){ 
					$image = $imagesManager->get($_POST['imgid']);
					}
					switch($_POST['field']){
						case "imagealbum":
							$image->setAlbumid($_POST['val']);
						break;

						case "imagetitle":
							$image->setTitle($_POST['val']);
						break;
					}
					if($imagesManager->update($image)){
						$rArray['result'] = 1;
					}else{
						$rArray['result'] = 0;
					}
				break;
				
				case "user":
					$user = $usersManager->get($_SESSION['user_id']);
					switch($_POST['field']){
						case "password":
							if($usersManager->updatePassword($_POST['passwd'])){
								$rArray['result'] = 1;
							}else{
								$rArray['result'] = 0;
							}
						break;
					
						case "settings":
							$user->setStep($_POST['step']);
							$user->setField($_POST['champ']);
							$user->setAsc($_POST['ordre']);
							if($usersManager->updateSettings($user)){
								$rArray['result'] = 1;
							}else{
								$rArray['result'] = 0;
							}
						break;
					}
				break;
			}
		break;
		
		case "newimage":
			$now = date("Y-m-d H:i:s");
			$image = new Image(array(
				'timestamp' => $_POST['timestamp'],
				'orientation' => $_POST['orientation'],
				'albumid' => $_POST['albumid'],
				'userid' => $_SESSION['user_id'],
				'title' => $_POST['title'],
				'dateadd' => $now,
				'width' => $_POST['width'],
				'height' => $_POST['height']
			));
			$imagesManager->add($image);
		break;
		
		case "usersettings":
			if(isset($_POST['field'])){ $user->setField($_POST['field']); $_SESSION['user_field'] = $_POST['field']; }
			if(isset($_POST['ordre'])){ $user->setAsc($_POST['ordre']); $_SESSION['user_ordre'] = $_POST['ordre']; }
			if(isset($_POST['step'])){ $user->setStep($_POST['step']); $_SESSION['user_step'] = $_POST['step']; }
			$usersManager->update($user);
		break;
	}
	echo json_encode($rArray);
}
?>
