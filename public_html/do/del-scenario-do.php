<?php
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	$logPrefix = "[DO | del-scenario-do] ";
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "deleteScenario", "Failed to delete scenario", "User is not logged in");
		//logErrorMsg($logPrefix."User is not logged in. ($ERROR_UNAUTHORIZED)");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_POST["scenario-id"]) || $_POST["scenario-id"] == "") {
		createLog("warning", $DO_DEL_SCENARIO_ID_NOT_RECEIVED, "DO", "deleteScenario", "Failed to delete scenario", "Scenario ID not received");
		//logErrorMsg($logPrefix."Scenario ID was not received. ($DO_DEL_SCENARIO_ID_NOT_RECEIVED)");
		header("Location: /my-scenarios?s=$DO_DEL_SCENARIO_ID_NOT_RECEIVED");
		closeLogs();
	}
	
	$apiResponse = deleteScenario($_POST["scenario-id"]);
	header("Location: /my-scenarios?s=$apiResponse");
	closeLogs();
?>