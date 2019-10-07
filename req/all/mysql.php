<?php
	/*************************************************************

	/*************************************************************/
	
	//=======================
	// Database configuration
	//=======================
	$mysql_db = "opsdesktools_v1.0";
	$mysql_host = "127.0.0.1:3306";
	$mysql_username = "PorkinsDev";
	$mysql_password = "!w2USAFA4mud&m!c$";
	$mysql_prefix = "";

	//=======================
	// Database tables
	//=======================
	$odt_log = "log";
	$odt_user = "user";

	//=======================
	// Database columns
	//=======================
	// Table Log
	//-----------------------
	$odt_log_id = $odt_log.".id";
	$odt_log_level = $odt_log.".level";
	$odt_log_enabled = $odt_log.".enabled";

	// Table User
	//-----------------------
	$odt_user_id = $odt_user.".id";
	$odt_user_email = $odt_user.".email";
	$odt_user_password = $odt_user.".password";
	$odt_user_date_joined = $odt_user.".date_joined";
	$odt_user_admin = $odt_user.".admin";

	//=============================
	// Create a database connection
	//=============================
	$db = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_db);
	if(mysqli_connect_errno() != 0) {
		log_errormsg("PHP failed to connect to database");
		log_errormsg("Error: ".mysqli_connect_error()." (".mysqli_connect_errno().")");
		closelogs();		
	}
?>