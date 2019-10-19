<?php
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		logDoMsg("[DO | save-scenario-to-account-do.php] Unauthenticated user attempted to save a scenario. ($ERROR_UNAUTHORIZED)");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	echo saveScenario($_SESSION["id"], $_POST["name"], $_POST["data"]);
	closeLogs();
?>