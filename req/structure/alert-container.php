<?php
	$level = "";
	$text = "";

	// s = status
	if(isset($_GET["s"]) && $_GET["s"] != "") {
		if($_GET["s"] >= 60000) {
			$level = "info";
		} else if($_GET["s"] >= 50000) {
			$level = "warning";
		} else if($_GET["s"] >= 40000) {
			$level = "danger";
		} else if($_GET["s"] >= 30000) {
			$level = "success";
		} else if($_GET["s"] >= 20000) {
			$level = "secondary";
		} else if($_GET["s"] >= 10000) {
			$level = "primary";
		}
		
		switch($_GET["s"]) {
			// 3xxxx SUCCESS
			// 301xx API
			case $API_CREATE_ACCOUNT_ACCOUNT_CREATED:
				$text = "Account created.";
				break;
			case $API_LOGIN_USER_AUTHENTICATED:
				$text = "";
				break;
			case $API_SAVE_SCENARIO_SCENARIO_SAVED:
				$text = "";
				break;
			case $API_DELETE_SCENARIO_SCENARIO_DELETED:
				$text = "Scenario deleted.";
				break;
			// 302xx CONTACT-DO.PHP
			case $DO_CONTACT_EMAIL_SENT:
				$text = "Messge sent to Porkins.";
				break;
			// 303xx SHARE-SCENARIO-DO.PHP
			case $DO_SHARE_SCENARIO_EMAIL_SENT:
				$text = "Scenario sent.";
				break;
			// 4xxxx DANGER
			// 400xx SHARED	
			case $ERROR_MYSQL:
				$text = "There was an unknown error.";
				break;
			case $ERROR_UNAUTHORIZED:
				$text = "You are not authorized to perform that action.";
				break;
			case $ERROR_ADMIN_SCENARIOS_USER_ID_NOT_RECEIVED:
			case $ERROR_IPLOG_USER_NOT_RECEIVED:
				$text = "User ID was not received.";
				break;
			// 401xx API
			// CREATE ACCOUNT
			case $API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED:
			case $API_LOGIN_EMAIL_NOT_RECEIVED:
				$text = "Email address was not received.";
				break;
			case $API_CREATE_ACCOUNT_FNAME_NOT_RECEIVED:
				$text = "First name was not received.";
				break;
			case $API_CREATE_ACCOUNT_LNAME_NOT_RECEIVED:
				$text = "Last name was not received.";
				break;
			case $API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED:
			case $API_LOGIN_PASSWORD_NOT_RECEIVED:
				$text = "Password was not received.";
				break;
			case $API_CREATE_ACCOUNT_ACCOUNT_EXISTS:
				$text = "An account already exists for the provided email address.";
				break;
			// LOGIN
			case $API_LOGIN_ACCOUNT_DOES_NOT_EXIST:
			case $API_LOGIN_INCORRECT_PASSWORD:
				$text = "Username/Password were incorrect, please try again.";
				break;
			case $API_LOGIN_ACCOUNT_DISABLED:
				$text = "Your account is disabled due to too many failed login attempts. Please <a href=\"contact\" class=\"alert-link\">contact us</a> to unlock your account.";
				break;
			// SAVE SCENARIO
			case $API_SAVE_SCENARIO_SCENARIO_ID_NOT_RECEIVED:
			case $API_SAVE_SCENARIO_DATA_NOT_RECEIVED:
				$text = "There was an error saving your scenario. <a href=\"contact\" target=\"_blank\" class=\"alert-link\">Contact us</a> if the problem persists.";
				break;
			// GET SCENARIO
			case $API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED:
			case $API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST:
				$text = "There was an error retrieving your requested scenario. <a href=\"contact\" class=\"alert-link\">Contact us</a> if the problem persists.";
				break;
			// DELETE SCENARIO
			case $API_DELETE_SCENARIO_ID_NOT_RECEIVED:
			case $API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST:
			case $DO_DEL_SCENARIO_ID_NOT_RECEIVED:
				$text = "There was an error deleting your scenario. <a href=\"contact\" class=\"alert-link\">Contact us</a> if the problem persists.";
				break;
			// 402xx CONTACT-DO.PHP
			case $DO_CONTACT_DATA_NOT_RECEIVED:
				$text = "Message data was not received.";
				break;
			case $DO_CONTACT_NAME_NOT_RECEIVED:
				$text = "Name was not received.";
				break;
			case $DO_CONTACT_EMAIL_NOT_RECEIVED:
				$text = "Email address was not received.";
				break;
			case $DO_CONTACT_MESSAGE_NOT_RECEIVED:
				$text = "Message body was not received.";
				break;
			case $DO_CONTACT_MESSAGE_NOT_SENT:
				$text = "There was an error sending the message. Message was not sent.";
				break;
			// 403xx CREATE-ACCOUNT-DO.PHP
			case $DO_CREATE_ACCOUNT_DATA_NOT_RECEIVED:
				$text = "Account data was not received.";
				break;
			case $DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED:
				$text = "First name was not received.";
				break;
			case $DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED:
				$text = "Last name was not received.";
				break;
			case $DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED:
				$text = "Email address was not received.";
				break;
			case $DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED:
				$text = "Password was not received.";
				break;
			case $DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED:
				$text = "Password confirmation was not received.";
				break;
			case $DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT:
				$text = "Your password was too short.";
				break;
			case $DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE:
				$text = "Your password was too simple.";
				break;
			case $DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH:
				$text = "Your passwords do not match.";
				break;
			// 405xx SHARE-SCENARIO-DO.PHP
			case $DO_SHARE_SCENARIO_NAME_NOT_RECEIVED:
				$text = "Scenario name was not received.";
				break;
			case $DO_SHARE_SCENARIO_ID_NOT_RECEIVED:
				$text = "Scenario ID was not received.";
				break;
			case $DO_SHARE_SCENARIO_EMAIL_NOT_RECEIVED:
				$text = "Recipient email address was not received.";
				break;
			case $DO_SHARE_SCENARIO_EMAIL_NOT_SENT:
				$text = "There was an error sharing your scenario, email was not sent.";
				break;
		}
	}

	if($text != "") {
?>
	<div class="alert alert-<?php echo $level; ?> alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<i class="fa fa-times"></i>
		</button>
		<?php echo $text; ?>
	</div>
<?php
	}
?>