<?php
session_start();
/* Include Config File */
include('config.php');
/* Include Classes */
include('class/ImagesManager.class.php');
include('class/User.class.php');
include('class/Image.class.php');

/* Create PDO Connector for Images */
$manager = new ImagesManager($conn_img);

/* Check if User is logged */
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_POST['action'])){
	$rArray = array();
	switch($_POST['action']){
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
		break;
	
		case "images":
			/* Define in Array what to do */
			$rArray['template'] = "images";
			/* Get All Images per User */
			$images = $manager->getList($_SESSION['user_id']);
			if(!empty($images)){
				$i = 0;
				foreach($images as $image){
					$rArray['image'][$i]['id'] = $image->id();
					$rArray['image'][$i]['timestamp'] = $image->timestamp();
					$rArray['image'][$i]['title'] = $image->title();
					$rArray['image'][$i]['extension'] = $image->extension();
					$rArray['image'][$i]['permanent'] = $image->permanent();
					$rArray['image'][$i]['dateadd'] = $image->dateadd();
					$rArray['image'][$i]['userid'] = $image->userid();
					$i++;
				}
			}
		break;
	}
	echo json_encode($rArray);
}
?>