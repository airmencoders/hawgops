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
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>"></script>
		<script>
			$(document).ready(function() {
				grecaptcha.ready(function() {
					grecaptcha.execute("<?php echo $site_key; ?>", {action: "google"}).then(function(token) {
						$.ajax({
							url: "/do/recaptcha.php",
							method: "POST",
							data: {
								"token": token,
								"refer": "google"
							},
							success: function(data, textStatus, jqXHR) {
								// data is the API return value
								if(parseFloat(data) <= <?php echo $thresh_google; ?>) {
									window.location.replace("/failed-rc");
								}
							}
						});
					});
				});
			});
		</script>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<div class="card my-5 mx-auto">
				<div class="card-body">
					<h1>Hawg Ops is protected by reCAPTCHA</h1>
					<p>The Google <a href="https://policies.google.com/privacy">Privacy Policy</a> and <a href="https://policies.google.com/terms">Terms of Service</a> apply.</p>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>