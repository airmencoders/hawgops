<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/all/codes.php");
	
	$user_name = "";
	$user_email = "";
	$subject = "";
	$message = "";
	$crlf = "\r\n";
	
	if(!isset($_POST)) {
		header("Location: /talk-to-me?s=$DO_CONTACT_DATA_NOT_RECEIVED");
		exit;
	}
	
	if(!isset($_POST["user-name"]) || $_POST["user-name"] == "") {
		header("Location: /talk-to-me?s=$DO_CONTACT_NAME_NOT_RECEIVED");
		exit;
	} else {
		$user_name = $_POST["user-name"];
	}
	
	if(!isset($_POST["user-email"]) || $_POST["user-email"] == "") {
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_NOT_RECEIVED");
		exit;
	} else {
		$user_email = $_POST["user-email"];
	}
	
	if(!isset($_POST["subject"]) || $_POST["subject"] == "") {
		$subject = "Hawg Ops Feedback";
	} else {
		$subject = $_POST["subject"];
	}
	
	if(!isset($_POST["message"]) || $_POST["message"] == "") {
		header("Location: /talk-to-me?s=$DO_CONTACT_MESSAGE_NOT_RECEIVED");
		exit;
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
	exit;
?>