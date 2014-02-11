<?php
session_start();
include('inc/config.php');
include('inc/class/User.class.php');
if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
include('inc/auth.php');
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
		<?php print_r($_SESSION);?>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/imgt4z.main.js'></script>
</html>