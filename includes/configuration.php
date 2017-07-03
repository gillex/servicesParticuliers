<?php
	error_reporting(-1);
  	ini_set('display_errors','On');
	mb_internal_encoding('UTF-8');
	date_default_timezone_set("Europe/Paris");

	define("WEB_TITLE", "CHANGE ME");

	/*
	 * DATABASE
	 */
	define("HOST", "localhost");
	define("USER", "root"); 
	define("PASSWORD", "");
	define("DATABASE", "CHANGE ME");
	 
	define("CAN_REGISTER", "any");
	define("DEFAULT_ROLE", "member");
	 
	define("SECURE", FALSE);

	/*
	 * DATABASE
	 */
	require_once ('SPDO.php');
	$pdo = SPDO::getInstance();