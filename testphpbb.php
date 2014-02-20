<?php
define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
$phpbb_root_path = './../forum/';
$website_root_path = './../';
include($phpbb_root_path . 'common.php');
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if(!$user->data['is_registered'] || !isset($user->data['is_registered'])){
       login_box($website_root_path);
       exit();
}


?>
