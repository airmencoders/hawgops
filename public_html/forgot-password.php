<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
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
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<div class="card card-sm mt-5 mx-auto">
				<h4 class="card-header text-center">Recover Password</h4>
				<div class="card-body">
					<form method="POST" action="./do/forgot-password.php" onsubmit="return validateLoginForm()">
						<div class="form-group">
							<label for="email">Email Address</label>
							<input type="email" class="form-control" id="email" name="email">
							<div class="invalid-feedback">Email Address is required.</div>
						</div>
						<button type="submit" class="btn btn-block btn-primary mt-2">Recover Password</button>
					</form>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>