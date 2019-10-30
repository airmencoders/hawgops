<?php
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");

	$fname = "";
	$lname = "";
	$email = "";
	$password = "";
	$confirm = "";

	if(!isset($_POST)) {
		createLog("warning", $DO_CREATE_ACCOUNT_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "Account data was not received");
		//logErrorMsg("Create Account data was not received. ($DO_CREATE_ACCOUNT_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_DATA_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["fname"]) || $_POST["fname"] == "") {
		createLog("warning", $DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "First name not received");
		//logErrorMsg("First name was not received. ($DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED");
		closeLogs();
	} else {
		$fname = $_POST["fname"];
	}

	if(!isset($_POST["lname"]) || $_POST["lname"] == "") {
		createLog("warning", $DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "Last name not received");
		//logErrorMsg("Last name was not received. ($DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED");
		closeLogs();
	} else {
		$lname = $_POST["lname"];
	}

	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		createLog("warning", $DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "Email address not received");
		//logErrorMsg("Email address was not received. ($DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED");
		closeLogs();
	} else {
		$email = $_POST["email"];
	}

	if(!isset($_POST["password"]) || $_POST["password"] == "") {
		createLog("warning", $DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "Password not received");
		//logErrorMsg("Password was not received. ($DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED");
		closeLogs();
	} else {
		$password = $_POST["password"];
	}

	if(!isset($_POST["confirm-password"]) || $_POST["confirm-password"] == "") {
		createLog("warning", $DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED, "DO", "createAccount", "Failed to create account", "Password not confirmed");
		//logErrorMsg("Confirm password was not received. ($DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED");
		closeLogs();
	} else {
		$confirm = $_POST["confirm-password"];
	}

	// Enforce password length
	if(strlen($password) < 8) {
		createLog("warning", $DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT, "DO", "createAccount", "Failed to create account", "Password too short");
		//logErrorMsg("Password is too short. ($DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT");
		closeLogs();
	}

	// Enforce password complexity
	$upper = preg_match("/[A-Z]+/", $password);
	$lower = preg_match("/[a-z]+/", $password);
	$number = preg_match("/[0-9]+/", $password);
	$special = preg_match("/[!@#$%^&*]+/", $password);
	
	if(!$upper || !$lower || !$number || !$special) {
		createLog("warning", $DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE, "DO", "createAccount", "Failed to create account", "Password too simple");
		//logErrorMsg("Password is too simple. ($DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE");
		closeLogs();
	}
	
	if($confirm != $password) {
		createLog("warning", $DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH, "DO", "createAccount", "Failed to create account", "Passwords do not match");
		//logErrorMsg("Passwords do not match. ($DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH");
		closeLogs();
	}

	$apiResponse = createAccount($fname, $lname, $email, $password);
	if($apiResponse == $API_CREATE_ACCOUNT_ACCOUNT_CREATED) {
		header("Location: /login?s=$apiResponse");
	} else {
		header("Location: /create-account?s=$apiResponse");
	}
	closeLogs();
?>