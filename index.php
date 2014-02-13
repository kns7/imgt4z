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
			<article class='help'>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi sit amet eleifend velit. Suspendisse sagittis vehicula sapien sit amet interdum. In felis tortor, porttitor in tempor ut, auctor vel nisl. Vestibulum quis dignissim justo. Integer at placerat lacus. Praesent at metus quis nisl fringilla semper. Duis fringilla commodo enim, ut vestibulum purus sodales nec. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur molestie, elit in imperdiet facilisis, lorem ligula condimentum justo, sed tristique sem massa eget nunc.</p>
			</article>
		</section>
		<div class='loader'>Chargement...</div>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/imgt4z.main.js'></script>
</html>