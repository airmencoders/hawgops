<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/keys/mysql.php");
	require("../../req/keys/recaptcha.php");
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	$user_name = "";
	$user_email = "";
	$subject = "";
	$message = "";
	$crlf = "\r\n";
	
	if(!isset($_POST)) {
		createLog("danger", $DO_CONTACT_DATA_NOT_RECEIVED, "DO", "contact-do", "Data not received", "POST");
		header("Location: /talk-to-me?s=$DO_CONTACT_DATA_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["user-name"]) || $_POST["user-name"] == "") {
		createLog("danger", $DO_CONTACT_NAME_NOT_RECEIVED, "DO", "contact-do", "Data not received", "Name");
		header("Location: /talk-to-me?s=$DO_CONTACT_NAME_NOT_RECEIVED");
		closeLogs();
	} else {
		$user_name = $_POST["user-name"];
	}
	
	if(!isset($_POST["user-email"]) || $_POST["user-email"] == "") {
		createLog("danger", $DO_CONTACT_EMAIL_NOT_RECEIVED, "DO", "contact-do", "Data not received", "Email");
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_NOT_RECEIVED");
		closeLogs();
	} else {
		$user_email = $_POST["user-email"];
	}
	
	if(!isset($_POST["subject"]) || $_POST["subject"] == "") {
		$subject = "Hawg Ops Feedback";
	} else {
		$subject = $_POST["subject"];
	}
	
	if(!isset($_POST["message"]) || $_POST["message"] == "") {
		createLog("danger", $DO_CONTACT_MESSAGE_NOT_RECEIVED, "DO", "contact-do", "Data not received", "Message");
		header("Location: /talk-to-me?s=$DO_CONTACT_MESSAGE_NOT_RECEIVED");
		closeLogs();
	} else {
		$message = trim($_POST["message"]);
	}

	$to = "hawg.ops@gmail.com";
	
	$full_message = "<p>$message</p><br/><p><strong>$user_name</strong></p><p>$user_email</p>";
	
	$headers = array("From" => $from, "To" => $to, "Subject" => $subject);
	
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
		// Log here!
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_NOT_SENT");
	} else {
		// Log here!
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_SENT");
	}
	// closelogs here!!
	closeLogs();
?>