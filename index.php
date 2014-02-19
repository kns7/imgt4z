<?php
session_start();
/* Include Config File */
include('inc/config.php');
/* Include Classes */
include('inc/class/UsersManager.class.php');
include('inc/class/CategoriesManager.class.php');
include('inc/class/ImagesManager.class.php');
include('inc/class/User.class.php');
include('inc/class/Image.class.php');
include('inc/class/Categorie.class.php');
$usersManager = new UsersManager($conn_img);
/* Check if User is logged */
if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){ 
	include('inc/auth.php');
}else{
	$user = $usersManager->get($_SESSION['user_id']);
}
?>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="user-scalable=no, initial-scale = 1, minimum-scale = 1, maximum-scale = 1, width=device-width">
		<meta name=apple-mobile-web-app-capable content=yes>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
		<meta name="robots" content="noindex,nofollow,noarchive">
		<meta name="google" content="noimageindex">
		<meta name="msnbot" content="noimageindex">
		<meta name="bingbot" content="noimageindex">
		<meta name="Slurp" content="noimageindex">
		<link rel="shortcut icon" href="favicon.ico"/>
		<link rel="icon" href="favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="css/style.css" media="screen"/>
		<title>T4Zone Images</title>
	</head>
	<body>
		<div id="showmenu"></div>
		<nav>
			<ul>
				<li class='menu' rel='back'>CACHER MENU</li>
				<li class='menu active' rel='home'>ACCUEIL</li>
				<li class='menu' rel='upload'>UPLOADER</li>
				<li class='menu' rel='images'>MES IMAGES</li>
				<li class='menu' rel='logout'>DECONNEXION</li>
			</ul>
		</nav>
		<section id='global'>
			
		</section>
		<div class='loader'>Chargement...</div>
		<div class='overlay'></div>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/jquery.form.min.js'></script>
	<script type='text/javascript' src='inc/imgt4z.main.js'></script>
</html>