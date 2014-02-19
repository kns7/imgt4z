<?php
/*
 * Authentication File:
 * If user is not logged, he gets the Login fields
 */
if(isset($_POST) && !empty($_POST['user'])){
	/* Try to login user using phpBB Functions to hash password */
	/* => Bon ca me broute, j'y arrive pô... 
	
	include('phpbbinc.php');
	$query = "SELECT user_id, username, username_clean FROM for_users WHERE username_clean = ? AND user_password = ?";
	$prep = $conn_forum->prepare($query);
	$prep->bindValue(1,$_POST['user'],PDO::PARAM_STR);
	$prep->bindValue(1,phpbb_hash($_POST['pwd']),PDO::PARAM_STR);
	$prep->execute();
	$return = $prep->fetchAll();
	print_r($return);*/
	
	/*
	 * Du coup, mot de passe temporaire:
	 * lat4zslpf (Les Admins T4Zone Sont Les Plus Beaux) ;)
	 * 
	 * Et pour les zozos modos:
	 * User: modo
	 * Mdp : VivalaZone1
	 */
	if($_POST['user'] == "Serialg" && md5($_POST['pwd']) == "80f582c1082b49ae6335cadee4b92132"){
		$user = $usersManager->get('25549');
		header("Location: /");
	}elseif($_POST['user'] == "jeb" && md5($_POST['pwd']) == "80f582c1082b49ae6335cadee4b92132"){
		$user = $usersManager->get('2554');
		header("Location: /");
	}elseif($_POST['user'] == "modos" && md5($_POST['pwd']) == "6f85aa27462f8587a6bbb7beadb0e71b"){
		$user = $usersManager->get('25548');
		header("Location: /");
	}else{
		$error = "Utilisateur et/ou mot de passe incorrects!";
	}
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
		<link rel="stylesheet" href="css/style.css"/>
		<title>T4Zone Images - Authentification</title>
	</head>
	<body>
		<section class="auth-form">
			<div class="auth-logo"></div>
			<form id="login" method="post">
				<p>Merci de vous authentifier pour avoir acc&egrave;s &agrave; l'outil <strong>Images T4Zone</strong><br/>Vous devez pour cela, utiliser votre compte du <a href="http://t4zone.info/forum/">Forum T4zone</a>.</p>
				<input type='text' id='user' name='user' value="nom d'utilisateur"/><br/>
				<input type='password' id='pwd' name='pwd' value="Password"/><br/>
				<input type='submit' value="Connexion"/>
			</form>
			<?php
			if(isset($error)){
				?><div class='auth-error'><?php echo $error;?></div><?php
			}
			?>
		</section>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/imgt4z.logon.js'></script>
</html>
<?php
exit();
?>