<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");	
	
	if(isLoggedIn()) {
		header("Location: /my-scenarios");
		closeLogs();
	}

	// If resetting a password
	if(isset($_GET["email"]) && $_GET["email"] != "" && isset($_GET["token"]) && $_GET["token"] != "" ) {
		// Check password validity
		$showResetForm = false;
		$api_response = validateRecoveryToken($_GET["email"], $_GET["token"]);

		if($api_response != $API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID) {
			header("Location: ./recover-account?s=$api_response");
			closeLogs();
		} else {
			$showResetForm = true;
		}
	} else {
		$showResetForm = false;
	}
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("../req/head/head.php"); ?>
		<script src="./js/validation.js"></script>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<div class="card card-sm mt-5 mx-auto">
				<h4 class="card-header text-center">Recover Account</h4>
				<?php 
					// SHOW RESET FORM IF VALID TOKEN
					if($showResetForm) { 
				?>
					<div class="card-body">
						<form method="POST" action="./do/reset-password-do.php" onsubmit="return validateRecoverAccountForm()">
							<input type="hidden" id="email" name="email" value="<?php echo $_GET["email"]; ?>">
							<input type="hidden" id="token" name="token" value="<?php echo $_GET["token"]; ?>">
							<div class="form-group">
								<label for="password">New Password</label>
								<input type="password" class="form-control" id="new-password" name="new-password" data-html="true" data-toggle="popover" data-placement="right" data-trigger="focus" title="Password Requirements" data-content="8 Characters Minimum<br/>1 Upper case letter<br/>1 Lower case letter<br/>1 Number<br/>1 Special Character (!@#$%^&*)">
								<div class="invalid-feedback" id="new-password-feedback"></div>
							</div>	
							<div class="form-group">
								<label for="confirm-password">Confirm Password</label>
								<input type="password" class="form-control" id="confirm-password" name="confirm-password">
								<div class="invalid-feedback" id="confirm-password-feedback"></div>
							</div>
							<button type="submit" class="btn btn-block btn-primary mt-2">Change Password</button>
						</form>
					</div>
				<?php 
					} 
					// SHOW TEXT SAYING EMAIL IS SENT
					else if (isset($_GET["s"]) && $_GET["s"] == $DO_RECOVER_ACCOUNT_EMAIL_SENT) { 
				?>
					<div class="card-body">
						<p class="card-text">If your account exists, an email will be sent to you with further instructions for recovering your account.</p>
					</div>
				<?php 
					// BASIC PAGE
					} else { 
				?>
					<div class="card-body">
						<form method="POST" action="./do/recover-account-do.php" onsubmit="return validateForgotPasswordForm()">
							<div class="form-group">
								<label for="email">Email Address</label>
								<input type="email" class="form-control" id="email" name="email">
								<div class="invalid-feedback">Email Address is required.</div>
							</div>
							<button type="submit" class="btn btn-block btn-primary mt-2">Recover Account</button>
						</form>
					</div>
				<?php } ?>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>