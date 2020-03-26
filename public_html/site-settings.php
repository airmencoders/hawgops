<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, basename(__FILE__), "-", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, basename(__FILE__), "-", "User not authorized", "-");
		header("Location: /my-scenarios?s=$ERROR_UNAUTHORIZED");
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
			<form method="POST">
				<div class="card my-5">
					<h3 class="card-header">Maintenance Mode</h3>
					<div class="card-body">
						<p>Toggle the switch below to put the site into maintenance mode. Only administrators will be authorized to access the site. In order to log into the site, visit the log in page at <a href="https://hawg-ops.com/login">https://hawg-ops.com/login</a></p>
						<div class="custom-control custom-switch">
							<input type="checkbox" class="custom-control-input" id="mxMode" name="mxMode">
							<label class="custom-control-label" for="mxMode">Enable Maintenance Mode</label>
						</div>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-block btn-success">Save Settings</button>
					</div>
				</div>
			</form>
		</div>
    </body>
</html>
<?php closeLogs(); ?>