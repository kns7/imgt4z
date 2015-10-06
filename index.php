<?php
session_start();
/* Read Configuration File (config.ini) */
$conf = parse_ini_file("config.ini", true);

/* Connect to DB */
try {
	$conn_img = new PDO('mysql:host='.$conf['mysql']['host'].';port='.$conf['mysql']['port'].';dbname='.$conf['mysql']['db_img'], $conf['mysql']['user'], $conf['mysql']['pwd']);
	$conn_img->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}catch(Exception $e){
	echo "DB connection to IMGT4Z error: ".$e->getMessage();
	die();
}
try {
	$conn_forum = new PDO('mysql:host='.$conf['mysql']['host'].';port='.$conf['mysql']['port'].';dbname='.$conf['mysql']['db_forum'], $conf['mysql']['user'], $conf['mysql']['pwd']);
	$conn_forum->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}catch(Exception $e){
	echo "DB connection to FORUM error: ".$e->getMessage();
	die();
}

/* Include Classes */
include('inc/class/UsersManager.class.php');
include('inc/class/AlbumsManager.class.php');
include('inc/class/ImagesManager.class.php');
include('inc/class/User.class.php');
include('inc/class/Image.class.php');
include('inc/class/Album.class.php');
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
		<link rel="icon" href="favicon_32.png" sizes="32x32">
		<link rel="icon" href="favicon_48.png" sizes="48x48">
		<link rel="icon" href="favicon_64.png" sizes="64x64">
		<link rel="icon" href="favicon_72.png" sizes="72x72">
		<link rel="icon" href="favicon.png" sizes="128x128">
		<link rel="apple-touch-icon" href="favicon_48.png" />
		<link rel="stylesheet" type="text/css" href="css/style.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="styles/<?php echo $conf[site][style];?>/<?php echo $conf[site][style];?>.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="css/smartphones.css" media="screen"/>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<title><?php echo $conf[site][title];?></title>
	</head>
	<body>
		<div id="showmenu"></div>
		<nav>
			<ul>
				<li class='menu' rel='back'>CACHER MENU</li>
				<li class='menu active' rel='albums'>MES IMAGES</li>
				<li class='menu' rel='upload'>UPLOADER</li>
				<li class='menu' rel='compte'>MON COMPTE</li>
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
	<script type='text/javascript' src='inc/img.main.js'></script>
	<script type='text/javascript' src='inc/img.user.js'></script>
</html>
