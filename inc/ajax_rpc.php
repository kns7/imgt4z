<?php
session_start();
/* Include Config File */
include('config.php');
/* Include Classes */
include('class/User.class.php');
include('class/Image.class.php');
/* Check if User is logged */
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_POST['action'])){
	switch($_POST['action']){
		case "logout":
			session_destroy();
		break;
	}
	echo json_encode($rArray);
}
?>
