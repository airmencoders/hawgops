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

	$message = filter_var($message, FILTER_SANITIZE_EMAIL);

	$full_message = file_get_contents("../../req/emails/contact-template.php");
	$full_message = str_replace("__NAME__", $user_name, $full_message);
	$full_message = str_replace("__EMAIL__", $user_email, $full_message);
	$full_message = str_replace("__SUBJECT__", $subject, $full_message);
	$full_message = str_replace("__ALERTLEVEL__", $alertLevel, $full_message);
	$full_message = str_replace("__ALERTTEXT__", $alertText, $full_message);
	$full_message = str_replace("__ALERTCOLOR__", $alertColor, $full_message);
	$full_message = str_replace("__TEXT__", $message, $full_message);
	
	//$full_message = "<p>$message</p><br/><p><strong>$user_name</strong></p><p>$user_email</p>";
	
	$headers = array("From" => $from, "To" => $to, "Subject" => $subject, "Reply-To" => $user_email, "Content-Type" => "text/html; charset=UTF-8");
	
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
			
	$mail = $smtp->send($to, $headers, $body);

	if(PEAR::isError($mail)) {
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_NOT_SENT");
	} else {
		header("Location: /talk-to-me?s=$DO_CONTACT_EMAIL_SENT");
	}
	closeLogs();
?>