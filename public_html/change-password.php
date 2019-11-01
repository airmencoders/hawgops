<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("../req/head/head.php"); ?>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-contianer.php"); ?></div>			
			<div class="card card-sm mt-5 mx-auto">
				<h4 class="card-header text-center">Change Password</h4>
				<div class="card-body">
					<form method="POST" action="./do/change-password.php" onsubmit="return validateChangePasswordForm()">
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
						<button type="submit" class="btn btn-block btn-primary mt-2">Change Password</button>
					</form>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>