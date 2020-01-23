<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "saveScenario", "Failed to save scenario", "User not authorized");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	echo saveScenario($_SESSION["id"], $_POST["name"], $_POST["data"]);
	closeLogs();
?>