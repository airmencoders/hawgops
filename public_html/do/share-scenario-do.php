<?php
	require_once "PEAR.php";
	require_once "Mail.php";
	require_once "Mail/mime.php";
	require("../../req/keys/smtp.php");
	require("../../req/keys/mysql.php");
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	$crlf = "\r\n";

	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "DO", "shareScenario", "Unauthenticated user attempted to share a scenario", $_SERVER["REMOTE_ADDR"]);
		header("Location: /index?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}	
	
	if(!isset($_POST["scenario-name"]) || $_POST["scenario-name"] == "") {
		createLog("warning", $DO_SHARE_SCENARIO_NAME_NOT_RECEIVED, "DO", "shareScenario", "Failed to share scenario", "Scenario name not received");
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_NAME_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["share-scenario-id"]) || $_POST["share-scenario-id"] == "") {
		createLog("warning", $DO_SHARE_SCENARIO_ID_NOT_RECEIVED, "DO", "shareScenario", "Failed to share scenario", "Scenario ID not received");
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_ID_NOT_RECEIVED");
		closeLogs();
	}
	
	if(!isset($_POST["email-share"]) || $_POST["email-share"] == "") {
		createLog("warning", $DO_SHARE_SCENARIO_EMAIL_NOT_RECEIVED, "DO", "shareScenario", "Failed to share scenario", "Email address not received");
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_EMAIL_NOT_RECEIVED");
		closeLogs();
	}
	
	$sender = "hawg.ops@gmail.com";
	$fromEmail = getUserEmailByID($_SESSION["id"]);
	$fromName = getUserNameByEmail($fromEmail);
	
	if($fromName["fname"] == "") {
		createLog("warning", "-", "DO", "shareScenario", "Unable to get name of sender", "-");
		$fromName["fname"] = "Hawg Ops";
	}
	
	$toEmail = $_POST["email-share"];
	$toName = getUserNameByEmail($toEmail);
	
	if($toName["fname"] == "") {
		createLog("warning", "-", "DO", "shareScenario", "Unable to get name of recipient", "-");
		$toName["fname"] = "Hello";
	}
	$subject = $fromName["fname"]." shared a CAS Scenario with you";
	$message = "<p>".$toName["fname"].",<br/><br/>".$fromName["fname"]." shared their CAS scenario ".$_POST["scenario-name"]." with you. Click on or copy/paste the link below to view the scenario. Note that you must be logged into Hawg Ops in order to view the scenario.<br/><br/><a href=\"https://hawg-ops.com/cas?scenario=".$_POST["share-scenario-id"]."&share=1\">https://hawg-ops.com/cas?scenario=".$_POST["share-scenario-id"]."&share=1</a><br/><br/>Hawg Ops</p>";
	
	$headers = array("From" => $sender, "To" => $toEmail, "Subject" => $subject);
	
	$mime = new Mail_mime(array("eol" => $crlf));
	$mime->setHTMLBody($message);
	
	$body = $mime->get();
	$headers = $mime->headers($headers);
	
	$smtp = Mail::factory("smtp",
		array("host"=>$host,
			"port"=>$port,
			"auth"=>true,
			"username"=>$gUser,
			"password"=>$gPass));
			
	$mail = $smtp->send($toEmail, $headers, $body);
	
	if(PEAR::isError($mail)) {
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_EMAIL_NOT_SENT");
	} else {
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_EMAIL_SENT");
	}
	closeLogs();
?>