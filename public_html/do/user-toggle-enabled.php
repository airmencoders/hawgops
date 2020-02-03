<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "user-toggle-enabled", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "user-toggle-enabled", "User not an administrator", "[".$_SESSION["id"]."]");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_POST["user"]) || $_POST["user"] == "") {
		createLog("warning", $DO_TOGGLE_ENABLED_USER_NOT_RECEIVED, "DO", "user-toggle-enabled", "Data not received", "User ID");
		header("Location: /admin-users?s=$DO_TOGGLE_ENABLED_USER_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["action"]) || $_POST["action"] == "") {
		createLog("warning", $DO_TOGGLE_ENABLED_ACTION_NOT_RECEIVED, "DO", "user-toggle-enabled", "Data not received", "Action");
		header("Location: /admin-users?s=$DO_TOGGLE_ENABLED_ACTION_NOT_RECEIVED");
		closeLogs();
	}
	
	if($_POST["action"] == "enable") {
		$api_response = enableAccount($_POST["user"]);
		header("Location: /admin-users?s=$api_response");
		closeLogs();
	} else if($_POST["action"] == "disable") {
		$api_response = disableAccount($_POST["user"]);
		header("Location: /admin-users?s=$api_response");
		closeLogs();
	} else {
		createLog("warning", $DO_TOGGLE_ENABLED_INVALID_ACTION, "DO", "user-toggle-enabled", "Invalid action received", $_POST["action"]);
		header("Location: /admin-users?s=$DO_TOGGLE_ENABLED_INVALID_ACTION");
		closeLogs();
	}
?>