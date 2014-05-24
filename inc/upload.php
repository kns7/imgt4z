<?php
session_start();
/* Include Config File */
include('config.php');
/* Include Classes */
include('class/UsersManager.class.php');
include('class/AlbumsManager.class.php');
include('class/ImagesManager.class.php');
include('class/User.class.php');
include('class/Album.class.php');
include('class/Image.class.php');

/* Create PDO Connector for Images */
$imagesManager = new ImagesManager($conn_img);
/* Create PDO Connector for Albums */
$albumsManager = new AlbumsManager($conn_img);
/* Create PDO Connector for Users */
$usersManager = new UsersManager($conn_img);

/* Check if User is logged */
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){
	$user = $usersManager->get($_SESSION['user_id']);
	$trigger_error = false;
	$fileElementName = 'file';
	$timestamp = time();
	$storagefilename = "../storage/".$_SESSION['user_id']."/".$timestamp.".jpg";
	$watermark = '../img/watermark.png';
	
	/* Si le répertoire utilisateur n'est pas présent, on le créé */
	if(!file_exists("../storage/".$_SESSION['user_id'])){
		mkdir("../storage/".$_SESSION['user_id'], '777');
	}
	
	if(isset($_FILES[$fileElementName])){
		if(!empty($_FILES[$fileElementName]['error'])){
			switch($_FILES[$fileElementName]['error']){
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
				break;
				case '4':
					$error = 'No file was uploaded.';
				break;
				case '6':
					$error = 'Missing a temporary folder';
				break;
				case '7':
					$error = 'Failed to write file to disk';
				break;
				case '8':
					$error = 'File upload stopped by extension';
				break;
				case '999':
				default:
					$error = 'Unknown Error';
				break;
			}
			$trigger_error = true;
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
		{
			$trigger_error = true;
			$error = 'No file was uploaded..';
		}
		switch(strtolower($_FILES[$fileElementName]['type'])){
			//allowed file types
			case 'image/png': 
			case 'image/gif': 
			case 'image/jpeg':

			break;
			default:
				$error = "Type de fichier non accepté!";
				$trigger_error = true;
			break;
		}
		if(!$trigger_error){
			/* Pas d'erreurs, on continue... */
			$trigger_resize = false;
			/* Test de la taille de l'image */
			$size = getimagesize($_FILES[$fileElementName]['tmp_name']);
			if($size[0] > $size[1]){
				/* Horizontale */
				$orientation = "H";
				$width = 800;
			}elseif($size[1] > $size[0]){
				/* Verticale */
				$orientation = "V";
				$width = 600;
			}else{
				/* Carrée */
				$orientation = "C";
				$width = 800;
			}
			if($orientation == "H" && $size[0] > 800){ $trigger_resize = true; }
			if($orientation == "V" && $size[1] > 800){ $trigger_resize = true; }
			if($orientation == "C" && $size[0] > 800){ $trigger_resize = true; }
			
			if($trigger_resize){
				/* Calcul de la nouvelle hauteur */
				$red = (($width * 100)/$size[0]);
				$height = (($size[1] * $red) / 100);
			}else{
				/* L'image garde ses dimensions d'origine */
				$width = $size[0];
				$height = $size[1];
			}
			
			/* Creation des objets image */
			$newimage = imagecreatetruecolor($width, $height);
			switch($_FILES[$fileElementName]['type']){
				case "image/png": $uploadimage = imagecreatefrompng($_FILES[$fileElementName]['tmp_name']); $trigger_conversion = true; break;
				case "image/gif": $uploadimage = imagecreatefromgif($_FILES[$fileElementName]['tmp_name']);	$trigger_conversion = true; break;
				case "image/jpeg": $uploadimage = imagecreatefromjpeg($_FILES[$fileElementName]['tmp_name']); $trigger_conversion = false; break;
			}
			
			if($trigger_conversion){
				/* Conversion de la transparence en blanc */
				imagefill($newimage, 0, 0, imagecolorallocate($newimage, 255, 255, 255));
				imagealphablending($newimage, TRUE);
			}
			if($trigger_resize){
				/* Redimensionnement de l'image */
				imagecopyresampled($newimage, $uploadimage, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			}else{
				if($trigger_conversion){
					/* Copie de l'image uploadée (PNG/GIF) vers la nouvelle image */
					imagecopy($newimage, $uploadimage, 0, 0, 0, 0, $width, $height);
				}
			}
			/* Pose du watermark (Le Watermark ne sera posé que lorsque l'image sera affichée via l'URL (i.php) */
			/*
			$wamark = imagecreatefrompng($watermark);
			$wasize = getimagesize($watermark);
			$wax = $width - $wasize[0] - 1;
			$way = $height - $wasize[1] - 1;
			imagecopy($newimage,$wamark,$wax,$way,0,0,$wasize[0],$wasize[1]);
			 */
			
			/* Enregistrement de la nouvelle image JPEG */
			imagejpeg($newimage,$storagefilename,85);
			imagedestroy($uploadimage);
			imagedestroy($newimage);
			//imagedestroy($wamark);
			
			$rArray['width'] = $width;
			$rArray['height'] = $height;
			$rArray['timestamp'] = $timestamp;
			if($orientation == "V"){ $rArray['orientation'] = 1; }else{ $orientation = 0; }
		}else{
			/* On renvoie l'erreur */
			$rArray['result'] = 1;
			$rArray['error'] = $error;
		}
	}else{
		$rArray['result'] = 1;
		$rArray['error'] = "Temp file not found";
	}
	echo json_encode($rArray);
}
?>
