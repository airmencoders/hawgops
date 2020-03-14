<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");

	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		createLog("warning", $DO_RESET_PASSWORD_EMAIL_NOT_RECEIVED, "DO", "reset-password-do", "Data not received", "Email Address");
		header("Location: /recover-account?s=$DO_RESET_PASSWORD_EMAIL_NOT_RECEIVED");
		closeLogs();
	}

	if(!isset($_POST["token"]) || $_POST["token"] == "") {
		createLog("warning", $DO_RESET_PASSWORD_TOKEN_NOT_RECEIVED, "DO", "reset-password-do", "Data not received", "Token");
		header("Location: /recover-account?s=$DO_RESET_PASSWORD_TOKEN_NOT_RECEIVED");
		closeLogs();
	}

	if(!isset($_POST["new-password"]) || $_POST["new-password"] == "") {
		createLog("warning", $DO_RESET_PASSWORD_NEW_PASSWORD_NOT_RECEIVED, "DO", "reset-password-do", "Data not received", "New Password");
		header("Location: /reset-password?email=".$_POST["email"]."&token=".$_POST["token"]."&s=$DO_RESET_PASSWORD_NEW_PASSWORD_NOT_RECEIVED");
		closeLogs();
	}

	if(!isset($_POST["confirm-password"]) || $_POST["confirm-password"] == "") {
		createLog("warning", $DO_RESET_PASSWORD_CONFIRM_PASSWORD_NOT_RECEIVED, "DO", "reset-password-do", "Data not received", "Confirm Password");
		header("Location: /reset-password?email=".$_POST["email"]."&token=".$_POST["token"]."&s=$DO_RESET_PASSWORD_CONFIRM_PASSWORD_NOT_RECEIVED");
		closeLogs();
	}

	if($_POST["new-password"] != $_POST["confirm-password"]) {
		createLog("warning", $DO_RESET_PASSWORD_PASSWORDS_DO_NOT_MATCH, "DO", "reset-password-do", "Passwords don't match", "-");
		header("Location: /reset-password?email=".$_POST["email"]."&token=".$_POST["token"]."&s=$DO_RESET_PASSWORD_PASSWORDS_DO_NOT_MATCH");
		closeLogs();
	}

	if(strlen($_POST["new-password"]) < 8) {
		createLog("warning", $DO_RESET_PASSWORD_PASSWORD_TOO_SHORT, "DO", "reset-password-do", "Password too short", "-");
		header("Location: /reset-password?email=".$_POST["email"]."&token=".$_POST["token"]."&s=$DO_RESET_PASSWORD_PASSWORD_TOO_SHORT");
		closeLogs();
	}

	// Enforce password complexity
	$upper = preg_match("/[A-Z]+/", $_POST["new-password"]);
	$lower = preg_match("/[a-z]+/", $_POST["new-password"]);
	$number = preg_match("/[0-9]+/", $_POST["new-password"]);
	$special = preg_match("/[!@#$%^&*]+/", $_POST["new-password"]);

	if(!$upper || !$lower || !$number || !$special) {
		createLog("warning", $DO_RESET_PASSWORD_PASSWORD_TOO_SIMPLE, "DO", "reset-password-do", "Password too simple", "-");
		header("Location: /reset-password?email=".$_POST["email"]."&token=".$_POST["token"]."&s=$DO_RESET_PASSWORD_PASSWORD_TOO_SIMPLE");
		closeLogs();
	}

	$api_response = changePassword(null, $_POST["email"], $_POST["token"], $_POST["new-password"]);
	if($api_response == $API_CHANGE_PASSWORD_PASSWORD_CHANGED) {
		// Send email 
		$user_name = getUserNameByEmail($_POST["email"]);
		
		$crlf = "\r\n";
		$from = "hawg.ops@gmail.com";
		$to = $_POST["email"];

		// Get the message and replace variables with what we got from the database
		$full_message = file_get_contents("../../req/emails/account-change-template.php");
		$full_message = str_replace("__USERNAME__", $user_name["fname"], $full_message);

		// Set headers
		$headers = array("From" => $from, "To" => $to, "Subject" => "Hawg Ops | Account Details Changed", "Content-Type" => "text/html; charset=UTF-8");

		// Create the email
		$mime = new Mail_mime(array("eol" => $crlf, "text_charset" => "UTF-8", "html_charset" => "UTF-8", "head_charset" => "UTF-8"));
		$mime->setHTMLBody($full_message);

		$body = $mime->get();
		$headers = $mime->headers($headers);

		$smtp = Mail::factory("smtp",
			array("host"=>$host,
				"port"=>$port,
				"auth"=>true,
				"username"=>$gUser,
				"password"=>$gPass));

		// Send the email
		$mail = $smtp->send($to, $headers, $body);
	}
	header("Location: /login?s=$api_response");
	closeLogs();
?>