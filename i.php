<?php
/*
 * Generate the Image file from Timestamp and UserID infos
 *	URL Syntax: i.php?i=<timestamp>d<userID>
 *	If the file is not available or empty [i] var,
 *  display standard image "not found"
 * OLD Version

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
 * 
 */

/* Nouvelle Version:
 * Le Watermark y est apposé
 */
$notfound = false;
$notfound_path = 'storage/notfound.jpg'; 
$watermark = 'img/watermark.png';

if(isset($_GET['i']) && !empty($_GET['i'])){
	$img = explode('d',$_GET['i']);
	$path = 'storage/'.$img[1].'/'.$img[0].'.jpg';
	if(!file_exists($path)){
		$notfound = true;
	}
}else{
	$notfound = true;
}
header('Content-Type: image/jpeg');
if($notfound){
readfile($notfound_path);
}else{
	$img = imagecreatefromjpeg($path);
	if(!$img){
		readfile($notfound_path);
	}else{
		$size = getimagesize($path);
		$wamark = imagecreatefrompng($watermark);
		$wasize = getimagesize($watermark);
		$wax = $size[0] - $wasize[0] - 1;
		$way = $size[1] - $wasize[1] - 1;
		imagecopy($img,$wamark,$wax,$way,0,0,$wasize[0],$wasize[1]);
		imagejpeg($img,NULL,100);
	}
}