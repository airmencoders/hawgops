<?php
	require("../../req/all/codes.php");
	require("../../req/all/api-v1.php");
	
	logDoMsg("Logging in [TESTING ONLY]");
	
	$api_response = login($_POST["email"], $_POST["password"]);
	if($api_response == $API_LOGIN_USER_AUTHENTICATED) {
		header("Location: /my-scenarios");
	} else {
		header("Location: /login?s=$api_response");
	}
	closeLogs();
?>