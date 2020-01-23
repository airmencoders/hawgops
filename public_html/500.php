<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/all/api-v1.php");
	
	createLog("danger", "HTTP/500", $_SERVER["REQUEST_URI"], "-", "Internal Server Error", "-");
?>
<!DOCTYPE html>
<html lang="en">
	<head><?php require("../req/head/head.php"); ?></head>
	<body id="bg">
		<?php // Navigation ?>
		<?php require("../req/structure/navbar.php"); ?>
				
		<div id="body-container" class="container">

			<?php // HTML Error Notice ?>
			<div class="card text-center mx-auto" style="max-width: 35rem; margin-top: 2rem;">
				<div class="card-body">
					<h5 class="card-title">Internal Server Error (500)</h5>
					<h5 class="card-title"><i class="fas fa-exclamation-triangle fa-5x"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-server fa-5x"></i></h5>
					<p class="card-text">Sorry, but the page <b><?php echo $_SERVER["REQUEST_URI"]; ?></b> is not currently working. Contact the administrator if you continue to see this error.</p>
				</div>
			</div>
		</div>
	</body>
</html>
<?php closeLogs(); ?>