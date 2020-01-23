<?php
	require("../../req/keys/mysql.php");
	require("../../req/keys/recaptcha.php");
	require("../../req/all/api-v1.php");
	
	if(!isset($_POST["token"]) || $_POST["token"] == "") {
		return;
	}
	
	verifyRecaptcha($_POST["token"]);

?>