<?php
/**
 * Configuration File
 */


$PARAM_host='localhost'; // le chemin vers le serveur
$PARAM_port='3306';
$PARAM_db_img='t4z_img'; // le nom de votre base de données "IMGT4Z"
$PARAM_db_forum='t4z_forum'; // le nom de votre base de données Forum
$PARAM_user='imguser'; // nom d'utilisateur pour se connecter
$PARAM_pwd='1h687C71i3kpA6O4'; // mot de passe de l'utilisateur pour se connecter

try {
	$conn_img = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_db_img, $PARAM_user, $PARAM_pwd);
	$conn_img->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}catch(Exception $e){
	echo "DB connection to IMGT4Z error: ".$e->getMessage();
	die();
}
try {
	$conn_forum = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_db_forum, $PARAM_user, $PARAM_pwd);
	$conn_forum->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
}catch(Exception $e){
	echo "DB connection to FORUM error: ".$e->getMessage();
	die();
}
?>
