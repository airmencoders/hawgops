<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "updateScenario", "Failed to update scenario", "User not authorized");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	echo updateScenario($_SESSION["id"], $_POST["id"], $_POST["name"], $_POST["data"]);
	closeLogs();
?>