<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		createLog("warning", $DO_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED, "DO", "recover-account-do", "Failed to recover Account", "Email address not received");
		header("Location: /recover-account?s=$DO_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED");
		closeLogs();
	}
	
	$api_response = recoverAccount($_POST["email"]);

	// recoverAccount() will respond with either an error code or with the token ID to pass to the email 
	// is_numeric will work because all the codes are just numbers, but the key will be alphanumeric
	if(!is_numeric($api_response)) {
		// Get user's name
		$user_name = getUserNameByEmail($_POST["email"]);

		// Set some variables
		$crlf = "\r\n";
		$from = "hawg.ops@gmail.com";
		$to = $_POST["email"];

		// Get the message and replace variables with what we got from the database
		$full_message = file_get_contents("../../req/emails/recover-account-template.php");
		$full_message = str_replace("__USERNAME__", $user_name["fname"], $full_message);
		$full_message = str_replace("__TOKEN__", $api_response, $full_message);
		$full_message = str_replace("__EMAIL__", $_POST["email"], $full_message);

		// Set headers
		$headers = array("From" => $from, "To" => $to, "Subject" => "Hawg Ops | Recover Account", "Content-Type" => "text/html; charset=UTF-8");

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

		// Check for errors
		if(PEAR::isError($mail)) {
			header("Location: /recover-account?s=$DO_RECOVER_ACCOUNT_EMAIL_NOT_SENT");
		} else {
			header("Location: /recover-account?s=$DO_RECOVER_ACCOUNT_EMAIL_SENT");
		}
		closeLogs();
	} else {
		header("Location: /recover-account?s=$api_response");
		closeLogs();
	}
?>