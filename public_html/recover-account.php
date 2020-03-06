<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");	
	
	if(isLoggedIn()) {
		header("Location: /my-scenarios");
		closeLogs();
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
				<?php if(isset($_GET["s"])) { ?>
					<div class="card-body">
						<p class="card-text">If your account exists, an email will be sent to you with further instructions for recovering your account.</p>
					</div>
				<?php } else { ?>
					<div class="card-body">
						<form method="POST" action="./do/recover-account-do.php" onsubmit="return validateEnableAccount()">
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