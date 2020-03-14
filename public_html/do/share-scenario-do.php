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
	
	$from = "hawg.ops@gmail.com";
	$fromName = getUserNameByID($_SESSION["id"]);
	
	if($fromName["fname"] == "") {
		createLog("warning", "-", "DO", "shareScenario", "Unable to get name of sender", "-");
		$fromName["fname"] = "A user";
	}
	
	$toEmail = $_POST["email-share"];
	$toName = getUserNameByEmail($toEmail);
	
	if($toName["fname"] == "") {
		createLog("warning", "-", "DO", "shareScenario", "Unable to get name of recipient", "-");
		$toName["fname"] = "Greetings";
	}

	$full_message = file_get_contents("../../req/emails/share-scenario-template.php");
	$full_message = str_replace("__SHAREDTO__", $toName["fname"], $full_message);
	$full_message = str_replace("__SHAREDBY__", $fromName["fname"], $full_message);
	$full_message = str_replace("__SCENARIOID__", $_POST["share-scenario-id"], $full_message);
	$full_message = str_replace("__SCENARIONAME__", $_POST["scenario-name"], $full_message);
	
	$headers = array("From" => $from, "To" => $toEmail, "Subject" => "Hawg Ops | Scenario Shared With You", "Content-Type" => "text/html; charset=UTF-8");
	
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
			
	$mail = $smtp->send($toEmail, $headers, $body);
	
	if(PEAR::isError($mail)) {
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_EMAIL_NOT_SENT");
	} else {
		header("Location: /my-scenarios?s=$DO_SHARE_SCENARIO_EMAIL_SENT");
	}
	closeLogs();
?>