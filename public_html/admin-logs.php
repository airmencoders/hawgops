<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-logs", "-", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-logs", "-", "User not admin", "Email: ".getUserEmailByID($_SESSION["id"]));
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}

	$site_logs_array = glob("../logs/*.txt");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php require("../req/head/head.php"); ?>
		<script src="https://www.google.com/recaptcha/api.js?render=<?php echo $site_key; ?>"></script>
		<script>
			$(document).ready(function() {
				grecaptcha.ready(function() {
					grecaptcha.execute("<?php echo $site_key; ?>", {action: "admin_logs"}).then(function(token) {
						$.ajax({
							url: "/do/recaptcha.php",
							method: "POST",
							data: {
								"token": token,
								"refer": "admin_logs"
							},
							success: function(data, textStatus, jqXHR) {
								// data is the API return value
								if(parseFloat(data) <= <?php echo $thresh_admin_logs; ?>) {
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
			<div class="card card-sm my-5 mx-auto">
				<h2 class="card-header text-center">
					Site Logs
				</h2>
				<div class="card-body">
					<ul class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto; -webkit-overflow-scrolling: touch;">

						<?php
							foreach(array_reverse($site_logs_array) as $site_log) {
								$site_log = pathinfo($site_log)["filename"];
						?>
						<a class="list-group-item list-group-item-action" href="admin-view-log?log=<?php echo $site_log; ?>"><?php echo $site_log; ?></a>
						<?php
							}
						?>
					</ul>
				</div>
			</div>
		</div>
	</body>
</html>