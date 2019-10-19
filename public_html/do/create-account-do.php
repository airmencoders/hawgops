<?php
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");

	logDoMsg("Creating user account.");

	$fname = "";
	$lname = "";
	$email = "";
	$password = "";
	$confirm = "";

	if(!isset($_POST)) {
		logErrorMsg("Create Account data was not received. ($DO_CREATE_ACCOUNT_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_DATA_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["fname"]) || $_POST["fname"] == "") {
		logErrorMsg("First name was not received. ($DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED");
		closeLogs();
	} else {
		$fname = $_POST["fname"];
	}

	if(!isset($_POST["lname"]) || $_POST["lname"] == "") {
		logErrorMsg("Last name was not received. ($DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED");
		closeLogs();
	} else {
		$lname = $_POST["lname"];
	}

	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		logErrorMsg("Email address was not received. ($DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED");
		closeLogs();
	} else {
		$email = $_POST["email"];
	}

	if(!isset($_POST["password"]) || $_POST["password"] == "") {
		logErrorMsg("Password was not received. ($DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED");
		closeLogs();
	} else {
		$password = $_POST["password"];
	}

	if(!isset($_POST["confirm-password"]) || $_POST["confirm-password"] == "") {
		logErrorMsg("Confirm password was not received. ($DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED");
		closeLogs();
	} else {
		$confirm = $_POST["confirm-password"];
	}

	// Enforce password length
	if(strlen($password) < 8) {
		logErrorMsg("Password is too short. ($DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT");
		closeLogs();
	}

	// Enforce password complexity
	$upper = preg_match("/[A-Z]+/", $password);
	$lower = preg_match("/[a-z]+/", $password);
	$number = preg_match("/[0-9]+/", $password);
	$special = preg_match("/[!@#$%^&*]+/", $password);
	
	if(!$upper || !$lower || !$number || !$special) {
		logErrorMsg("Password is too simple. ($DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE");
		closeLogs();
	}
	
	if($confirm != $password) {
		logErrorMsg("Passwords do not match. ($DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH)");
		header("Location: /create-account?s=$DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH");
		closeLogs();
	}

	logDoMsg("Sending information to API.");
	$apiResponse = createAccount($fname, $lname, $email, $password);
	logDoMsg("Response received from API: $apiResponse");
	if($apiResponse == $API_CREATE_ACCOUNT_ACCOUNT_CREATED) {
		header("Location: /login?s=$apiResponse");
	} else {
		header("Location: /create-account?s=$apiResponse");
	}
	closeLogs();
?>