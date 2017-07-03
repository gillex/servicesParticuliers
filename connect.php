<?php
// Local path of folder project
define('WEBROOT', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

// Full path of folder project
define('PATH', "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

// Start session safely
sec_session_start();

// Login system
require 'includes/Auth.php';

/*
 *  Page system
 */
if(isset($_GET['p']))
	$params = explode("/", $_GET['p']);

if(!isset($params[0]))
	$params[0]='accueil';

if(!file_exists('pages/'.$params[0].'.php'))
	$params[0]='404';

ob_start();
	include 'pages/'.$params[0].'.php';
	$content = ob_get_contents();
ob_end_clean();