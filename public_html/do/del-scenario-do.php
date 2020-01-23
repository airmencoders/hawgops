<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "deleteScenario", "Failed to delete scenario", "User is not logged in");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_POST["scenario-id"]) || $_POST["scenario-id"] == "") {
		createLog("warning", $DO_DEL_SCENARIO_ID_NOT_RECEIVED, "DO", "deleteScenario", "Failed to delete scenario", "Scenario ID not received");
		header("Location: /my-scenarios?s=$DO_DEL_SCENARIO_ID_NOT_RECEIVED");
		closeLogs();
	}
	
	$apiResponse = deleteScenario($_POST["scenario-id"]);
	header("Location: /my-scenarios?s=$apiResponse");
	closeLogs();
?>