<?php
session_start();
/* Include Config File */
include('config.php');
/* Include Classes */
include('class/CategoriesManager.class.php');
include('class/ImagesManager.class.php');
include('class/User.class.php');
include('class/Categorie.class.php');
include('class/Image.class.php');

/* Create PDO Connector for Images */
$imagesManager = new ImagesManager($conn_img);
/* Create PDO Connector for Categories */
$categoriesManager = new CategoriesManager($conn_img);

/* Check if User is logged */
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_POST['action'])){
	$rArray = array();
	switch($_POST['action']){
		case "initload":
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
					$rArray['images'][$i]['categorie'] = $image->categorieid();
					$rArray['images'][$i]['dateadd'] = $image->dateadd();
					$rArray['images'][$i]['userid'] = $image->userid();
					$i++;
				}
			}
			$categories = $categoriesManager->getList();
			if(!empty($categories)){
				$i = 0;
				foreach($categories as $categorie){
					$rArray['categories'][$i]['id'] = $categorie->id();
					$rArray['categories'][$i]['name'] = $categorie->name();
					$i++;
				}
			}
			$rArray['help']['upload'] = "<p>Via ce formulaire, tu peux envoyer une image sur T4Zone Images.<br/>Les formats d'images acceptés sont les suivants: JPEG, PNG et GIF.</p><p>L'image sera automatiquement redimensionnée en 800x600 (ou 600x800), un Watermark (Tatouage Numérique) sera ajouté à l'image et enfin un lien sera automatiquement généré pour pouvoir la poster sur le forum.<br/>Si l'image est mal orientée, il te sera possible une fois envoyée, de la faire tourner pour la mettre dans le bon sens!</p><p><em>S'il devait y avoir un problème lors de l'envoi, merci de contacter les admins du Forum T4Zone <a href='mailto:admin@t4zone.org?subject=Erreur Upload T4Zone Images'>admins@t4zone.org</a></em></p>";
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
			/* Get Categories List */
			$categories = $categoriesManager->getList();
			if(!empty($categories)){
				$i = 0;
				foreach($categories as $categorie){
					$rArray['categories'][$i]['id'] = $categorie->id();
					$rArray['categories'][$i]['name'] = $categorie->name();
					$i++;
				}
			}
			$categories = $categoriesManager->getList();
			if(!empty($categories)){
				$i = 0;
				foreach($categories as $categorie){
					$rArray['categories'][$i]['id'] = $categorie->id();
					$rArray['categories'][$i]['name'] = $categorie->name();
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
					$rArray['images'][$i]['categorie'] = $image->categorieid();
					$rArray['images'][$i]['dateadd'] = $image->dateadd();
					$rArray['images'][$i]['userid'] = $image->userid();
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
				$rArray['image']['categorie'] = $image->categorie();
				$rArray['image']['categorieid'] = $image->categorieid();
				$rArray['image']['dateadd'] = $image->dateadd();
				$rArray['image']['userid'] = $image->userid();
			}
			/* Get Categories List */
			$categories = $categoriesManager->getList();
			if(!empty($categories)){
				$i = 0;
				foreach($categories as $categorie){
					$rArray['categories'][$i]['id'] = $categorie->id();
					$rArray['categories'][$i]['name'] = $categorie->name();
					$i++;
				}
			}
		break;
		
		case "btn":
			if($_POST['imgid'] != "" && !empty($_POST['imgid']) && isset($_POST['imgid'])){ 
				$image = $imagesManager->get($_POST['imgid']);
			}
			switch($_POST['what']){
				case "rotatel":
				case "rotater":
					if($_POST['what'] == "rotatel"){ $rotate = "L"; }else{ $rotate = "R"; }
					$orientation = $image->rotate($rotate,$imagesManager);
					if($orientation == 1){
						$rArray['width'] = 300;
						$rArray['height'] = 400;
					}else{
						$rArray['width'] = 400;
						$rArray['height'] = 300;
					}
					$rArray['url'] = "storage/".$image->userid()."/".$image->timestamp().".jpg";
				break;
				
				case "delete":
					$rArray['result'] = $imagesManager->delete($image);
				break;
			}
		break;
	
		case "save":
			if($_POST['imgid'] != "" && !empty($_POST['imgid']) && isset($_POST['imgid'])){ 
				$image = $imagesManager->get($_POST['imgid']);
			}
			switch($_POST['what']){
				case "imagecategorie":
					$image->setCategorieid($_POST['val']);
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
	}
	echo json_encode($rArray);
}
?>
