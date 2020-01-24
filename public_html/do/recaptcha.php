<?php
	require("../../req/keys/mysql.php");
	require("../../req/keys/recaptcha.php");
	require("../../req/all/api-v1.php");
	
	if(!isset($_POST["token"]) || $_POST["token"] == "") {
		createLog("warning", "-", "DO", "recaptcha", "reCAPTCHA token not received", "Refer: ".$_POST["refer"]);
		header("Location: /");
		closeLogs();
	}
	
	echo verifyRecaptcha($_POST["token"]);
	closeLogs();
?>