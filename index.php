<?php
session_start();
/* Include Config File */
include('inc/config.php');
/* Include Classes */
include('inc/class/ImagesManager.class.php');
include('inc/class/User.class.php');
include('inc/class/Image.class.php');
/* Check if User is logged */
if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){ 
	include('inc/auth.php');
}else{
	$user = new User($_SESSION['user_id'],$_SESSION['user_name'],$_SESSION['user_admin']);
}
?>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="robots" content="noindex,nofollow,noarchive">
		<meta name="google" content="noimageindex">
		<meta name="msnbot" content="noimageindex">
		<meta name="bingbot" content="noimageindex">
		<meta name="Slurp" content="noimageindex">
		<link rel="shortcut icon" href="favicon.ico"/>
		<link rel="icon" href="favicon.ico"/>
		<link rel="stylesheet" href="css/style.css"/>
		<title>T4Zone Images</title>
	</head>
	<body>
		<nav>
			<ul>
				<li class='menu active' rel='home'>Accueil</li>
				<li class='menu' rel='upload'>Uploader</li>
				<li class='menu' rel='images'>Mes images</li>
				<li class='menu' rel='logout'>D&eacute;connexion</li>
			</ul>
		</nav>
		<section id='global'>
			
		</section>
		<div class='loader'>Chargement...</div>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/imgt4z.main.js'></script>
</html>