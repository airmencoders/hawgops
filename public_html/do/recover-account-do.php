<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/codes.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/api-v1.php");
	
	if(!isset($_POST["email"]) || $_POST["email"] == "") {
		createLog("warning", $DO_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED, "DO", "login", "Failed to login user", "Email address not received");
		header("Location: /recover-account?s=$DO_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED");
		closeLogs();
	}
	
	$api_response = recoverAccount($_POST["email"]);

	if($api_response == $API_RECOVER_ACCOUNT_ACCOUNT_EXISTS) {
		$user_name = getUserNameByEmail($_POST["email"]);
		$crlf = "\r\n";
		$from = "hawg.ops@gmail.com";
		$to = $_POST["email"];
		$full_message = file_get_contents("../../req/emails/recover-account-mail.php");
		$headers = array("From" => $from, "To" => $to, "Subject" => "Recover Hawg Ops Account");

		$mime = new Mail_mime(array("eol" => $crlf));
		$mime->setHTMLBody($full_message);

		$body = $mime->get();
		$headers = $mime->headers($headers);

		$smtp = Mail::factory("smtp",
			array("host"=>$host,
				"port"=>$port,
				"auth"=>true,
				"username"=>$gUser,
				"password"=>$gPass));

		$mail = $smtp->send($to, $headers, $body);

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