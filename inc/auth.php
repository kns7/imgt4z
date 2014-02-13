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
	 */
	if($_POST['user'] == "Serialg" && md5($_POST['pwd']) == "80f582c1082b49ae6335cadee4b92132"){
		$user = new User('25549','Serialg','1');
		header("Location: /");
	}elseif($_POST['user'] == "jeb" && md5($_POST['pwd']) == "80f582c1082b49ae6335cadee4b92132"){
		$user = new User('2554','jeb','1');
		header("Location: /");
	}else{
		$error = "Utilisateur et/ou mot de passe incorrects!";
	}
}
?>
<html lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="shortcut icon" href="favicon.ico"/>
		<link rel="icon" href="favicon.ico"/>
		<link rel="stylesheet" href="css/style.css"/>
		<title>T4Zone Images - Authentification</title>
	</head>
	<body>
		<section class="auth-form">
			<div class="auth-logo"></div>
			<form id="login" method="post">
				<p>Merci de vous authentifier pour avoir acc&egrave;s &agrave; l'outil <strong>Images T4Zone</strong><br/>Vous devez pour cela, utiliser votre compte du <a href="http://t4zone.info/forum/">Forum T4zone</a>.</p>
				<input type='text' id='user' name='user' value="nom d'utilisateur"/>
				<input type='password' id='pwd' name='pwd' value="Password"/>
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