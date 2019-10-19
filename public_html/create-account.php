<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("../req/head/head.php"); ?>
		<script src="./js/validation.js"></script>
		<script>
			$(document).ready(function() {
				$("#password").popover();
			});
		</script>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>			
			<div class="card card-sm mt-5 mx-auto">
				<h4 class="card-header text-center">Create Account</h4>
				<div class="card-body">
					<form method="POST" action="./do/create-account-do.php" onsubmit="return validateCreateAccountForm()">
						<div class="form-row">
							<div class="form-group col">
								<label for="fname">First Name</label>
								<input type="text" class="form-control" id="fname" name="fname">
								<div class="invalid-feedback">First Name is required.</div>
							</div>
							<div class="form-group col">
								<label for="lname">Last Name</label>
								<input type="text" class="form-control" id="lname" name="lname">
								<div class="invalid-feedback">Last Name is required.</div>
							</div>
						</div>
						<div class="form-group">
							<label for="email">Email Address</label>
							<input type="email" class="form-control" id="email" name="email">
							<div class="invalid-feedback">Email Address is required.</div>
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="password" name="password" data-html="true" data-toggle="popover" data-placement="right" data-trigger="focus" title="Password Requirements" data-content="8 Characters Minimum<br/>1 Upper case letter<br/>1 Lower case letter<br/>1 Number<br/>1 Special Character (!@#$%^&*)">
							<div class="invalid-feedback" id="password-feedback"></div>
						</div>
						<div class="form-group">
							<label for="confirm-password">Confirm Password</label>
							<input type="password" class="form-control" id="confirm-password" name="confirm-password">
							<div class="invalid-feedback" id="confirm-feedback"></div>
						</div>
						<div class="form-group">
							<div class="form-check custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="read-policies" name="read-policies">
								<label class="custom-control-label" for="read-policies">I have read and agree to the <a href="/terms" target="_blank">Terms of Use</a> and <a href="/privacy" target="_blank">Privacy Policy</a>.</label>
								<div class="invalid-feedback">You must agree to Terms of Use and Privacy Policy.</div>
							</div>
						</div>
						<button type="submit" class="btn btn-block btn-success mt-2">Create Account</button>
					</form>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>