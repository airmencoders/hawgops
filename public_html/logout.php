<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	// Check to see if a user is logged in
	if(isLoggedIn()) {
		logout();
	}
	
	header("Location: ./login");
	closeLogs();
?>