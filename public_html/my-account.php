<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "my-scenarios", "-", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		header("Location: /my-scenarios");
	}
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
			<div class="card-deck mt-5">
				<div class="card">
					<h4 class="card-header text-center">Account Details</h4>
					<form method="POST" action="./do/change-account-details" onsubmit=" return validateChangeDetailsForm()">
						<div class="card-body">
							<div class="form-group">
								<label for="first-name">First Name</label>
								<input type="text" class="form-control" id="first-name" name="first-name">
								<div class="invalid-feedback">First name is required.</div>
							</div>
							<div class="form-group">
								<label for="last-name">Last Name</label>
								<input type="text" class="form-control" id="last-name" name="last-name">
								<div class="invalid-feedback">Last name is required.</div>
							</div>
							<div class="form-group">	
								<label for="email">Email Address</label>
								<input type="email" class="form-control" id="email" name="email">
								<div class="invalid-feedback">A valid email address is required.</div>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-block btn-primary">Save Changes</button>
						</div>
					</form>
				</div>
				<div class="card">
					<h4 class="card-header text-center">Change Password</h4>
					<form method="POST" action="./do/change-password" onsubmit="return validateChangePasswordForm()">
						<div class="card-body">
							<div class="form-group">
								<label for="old-password">Old Password</label>
								<input type="password" class="form-control" id="old-password" name="old-password">
								<div class="invalid-feedback">Old Password is required.</div>
							</div>
							<div class="form-group">
								<label for="new-password">New Password</label>
								<input type="password" class="form-control" id="new-password" name="new-password">
								<div class="invalid-feedback">New Password is required.</div>
							</div>
							<div class="form-group">
								<label for="confirm-password">Confirm New Password</label>
								<input type="password" class="form-control" id="confirm-password" name="confirm-password">
								<div class="invalid-feedback" id="confirm-feedback"></div>
							</div>
						</div>
						<div class="card-footer">
							<button type="submit" class="btn btn-block btn-primary">Change Password</button>
						</div>
					</form>
				</div>
				<div class="card">
					<h4 class="card-header text-center">Delete Account</h4>
					<div class="card-body">
						<p class="card-text"><strong>WARNING:</strong> Deleting an account is immediate and permanent. All saved CAS scenarios and settings will be lost and any users with whom you have shared CAS scenarios with will no longer be able to access them. If you are sure you no longer wish to have an account with Hawg Ops, then click the link below. If you wish to save scenarios to an account in the future, you will be required to re-create an account.</p>
					</div>
					<div class="card-footer">
						<a class="btn btn-danger btn-block" href="./do/delete-account" role="button">Delete Account</a>
					</div>
					</div>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>