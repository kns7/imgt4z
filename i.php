<?php
/*
 * Generate the Image file from Timestamp and UserID infos
 *	URL Syntax: i.php?i=<timestamp>d<userID>
 *	If the file is not available or empty [i] var,
 *  display standard image "not found"
 */
if(isset($_GET['i']) && !empty($_GET['i'])){
	$img = explode('d',$_GET['i']);
	$path = 'storage/'.$img[1].'/'.$img[0].'.jpg';
	if(!file_exists($path)){
		$path = 'storage/notfound.jpg';
	}
}else{
	$path = 'storage/notfound.jpg';
}
header('Content-Type: image/jpeg');
readfile($path);