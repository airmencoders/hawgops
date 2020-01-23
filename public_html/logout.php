<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	// Check to see if a user is logged in
	if(isLoggedIn()) {
		logout();
	}
	
	header("Location: ./login");
	closeLogs();
?>