<?php
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		createLog("warning", $DO_LOGIN_EMAIL_NOT_RECEIVED, "DO", "login", "Failed to login user", "Email address not received");
		return $DO_LOGIN_EMAIL_NOT_RECEIVED;
	}
	
	if(!isset($_POST["password"]) || $_POST["password"] == "") {
		createLog("warning", $DO_LOGIN_PASSWORD_NOT_RECEIVED, "DO", "login", "Failed to login user", "Password not received");
		return $DO_LOGIN_PASSWORD_NOT_RECEIVED;
	}
	
	if(isset($_GET["scenario"]) && $_GET["scenario"] != "" && isset($_GET["share"]) && $_GET["share"] == "1") {
		createLog("info", "-", "DO", "login", "User logging in to view a shared scenario", "Scenario ID: [".$_GET["scenario"]."]");
		$referText = "?scenario=".$_GET["scenario"];
	} else {
		$referText = "";
	}
	
	$api_response = login($_POST["email"], $_POST["password"]);
	if($api_response == $API_LOGIN_USER_AUTHENTICATED) {
		if($referText == "") {
			header("Location: /my-scenarios");
		} else {
			header("Location: /cas".$referText);
		}
	} else {
		header("Location: /login?s=$api_response");
	}
	closeLogs();
?>