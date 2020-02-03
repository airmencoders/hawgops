<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");	
	
	if(isset($_GET["scenario"]) && $_GET["scenario"] != "" && isset($_GET["share"]) && $_GET["share"] == "1") {
		$referText = "?scenario=".$_GET["scenario"]."&share=1";
	} else {
		$referText = "";
	}
	
	if(isLoggedIn()) {
		if($referText == "") {
			header("Location: /my-scenarios");
			closeLogs();
		} else {
			header("Location: /cas".$referText);
			closeLogs();
		}
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
				<h4 class="card-header text-center">Login to Hawg Ops</h4>
				<div class="card-body">
					<form method="POST" action="./do/login-do.php<?php echo $referText; ?>" onsubmit="return validateLoginForm()">
						<div class="form-group">
							<label for="email">Email Address</label>
							<input type="email" class="form-control" id="email" name="email">
							<div class="invalid-feedback">Email Address is required.</div>
						</div>
						<div class="form-group">
							<label for="password">Password</label>
							<input type="password" class="form-control" id="password" name="password">
							<div class="invalid-feedback">Password is required.</div>
						</div>
						<button type="submit" class="btn btn-block btn-primary mt-2">Login</button>
					</form>
					<!--<div class="text-center mt-2">
						<a href="/forgot-password">Forgot Password?</a>
					</div>-->
				</div>
				<div class="card-footer">
					<a class="btn btn-block btn-success" href="/create-account" role="button">Create Account</a>
				</div>
			</div>
			<div class="card card-sm mx-auto mt-3">
				<div class="card-body">
					<p class="card-text">An account is <strong>NOT</strong> required, however it will allow you to save CAS Scenarios to your account so you no longer have to save the generated text to send to yourself.</p>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>