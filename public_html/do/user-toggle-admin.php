<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "user-toggle-admin", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "user-toggle-admin", "User not an administrator", "[".$_SESSION["id"]."]");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_POST["user"]) || $_POST["user"] == "") {
		createLog("warning", $DO_TOGGLE_ADMIN_USER_NOT_RECEIVED, "DO", "user-toggle-admin", "Data not received", "User ID");
		header("Location: /admin-users?s=$DO_TOGGLE_ADMIN_USER_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["action"]) || $_POST["action"] == "") {
		createLog("warning", $DO_TOGGLE_ADMIN_ACTION_NOT_RECEIVED, "DO", "user-toggle-admin", "Data not received", "Action");
		header("Location: /admin-users?s=$DO_TOGGLE_ADMIN_ACTION_NOT_RECEIVED");
		closeLogs();
	}
	
	if($_POST["action"] == "grant") {
		$api_response = grantAdmin($_POST["user"]);
		header("Location: /admin-users?s=$api_response");
		closeLogs();
	} else if($_POST["action"] == "revoke") {
		$api_response = revokeAdmin($_POST["user"]);
		header("Location: /admin-users?s=$api_response");
		closeLogs();
	} else {
		createLog("warning", $DO_TOGGLE_ADMIN_INVALID_ACTION, "DO", "user-toggle-admin", "Invalid action received", $_POST["action"]);
		header("Location: /admin-users?s=$DO_TOGGLE_ADMIN_INVALID_ACTION");
		closeLogs();
	}
?>