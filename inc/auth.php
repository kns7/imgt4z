<?php
/*
 * Authentication File:
 * If user is not logged, he gets the Login fields
 */
if(isset($_POST) && !empty($_POST['user'])){
	switch($conf[auth][type]){
		case "local":
			$login = array(
				'auth' => $conf[auth][type],
				'username' => $_POST['user'],
				'password' => md5($_POST['pwd'])
			);
		break;
	
		case "phpbb":
			define('IN_PHPBB', true);
			$phpEx = substr(strrchr(__FILE__, '.'), 1);
			include($conf[phpbb][path] . 'common.php');
			// Start session management
			$userphpbb->session_begin();
			$auth->acl($user->data);
			$user->setup();
			// If user is not registered or does not have access to a specific Forum goto Login
			if (!$user->data['is_registered'] || !isset($user->data['is_registered']) || !$auth->acl_get('f_read', $conf[phpbb][forum_allowed_id])){
				login_box($conf[site][path]);
				exit();
			}else{
				$admin = ($auth->acl_get('a_')) ? "1":"0";
				$login = array(
					'auth' => $conf[auth][type],
					'phpbbid' => $userphpbb->data['user_id'],
					'phpbbname' => $userphpbb->data['username'],
					'admin' => $admin,
					'autocreate' => $conf[phpbb][autocreate_user]
				);
			}
		break;
		
		default:
			$error = "Mismatch in Configuration, please contact administrator!";
		break;
	}
	$user = $usersManager->login($login);
	if($user == "error"){
		$error = "Utilisateur et/ou mot de passe incorrects!";
	}elseif($user == "notcreated"){
		$error = "Demandez d'abord un accès sur le Forum!";
	}else{
		if(!file_exists("storage/".$user->id())){
			mkdir("storage/".$user->id(), 775);
			echo "create directory";
		}
		header("Location: /");
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
		<link rel="stylesheet" type="text/css" href="styles/<?php echo $conf[site][style];?>/<?php echo $conf[site][style];?>.css" media="screen"/>
		<title><?php echo $conf[site][title];?> - Authentification</title>
	</head>
	<body>
		<section class="auth-form">
			<div class="auth-logo"></div>
			<form id="login" method="post">
				<p>Plateforme d'hébergement d'images KNS7. L'accès est réservé aux membres.</p>
				<input type='text' id='user' name='user' value="nom d'utilisateur"/><br/>
				<input type='password' id='pwd' name='pwd' value="Password"/><br/>
				<input type='submit' class='btn large' value="Connexion"/>
			</form>
			<?php
			if(isset($error)){
				?><div class='auth-error'><?php echo $error;?></div><?php
			}
			?>
		</section>
	</body>
	<script type='text/javascript' src='inc/jquery-1.11.0.min.js'></script>
	<script type='text/javascript' src='inc/img.logon.js'></script>
</html>
<?php
exit();
?>
