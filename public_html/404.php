<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/all/api-v1.php");
	
	createLog("warning", "HTTP/404", $_SERVER["REQUEST_URI"], "-", "Page Not Found", "-");
?>
<!DOCTYPE html>
<html lang="en">
	<head><?php require("../req/head/head.php"); ?></head>
	<body id="bg">
		<?php // Navigation / Header ?>
		<?php require("../req/structure/navbar.php"); ?>
				
		<div id="body-container" class="container">
		
			<?php // HTML Error Notice ?>
			<div class="card text-center mx-auto" style="max-width: 35rem; margin-top: 2rem;">
				<div class="card-body">
					<h5 class="card-title">Page Not Found (404)</h5>
					<h5 class="card-title"><i class="fas fa-unlink fa-5x"></i></h5>
					<p class="card-text">Sorry, but the page <b><?php echo $_SERVER["REQUEST_URI"]; ?></b> was not found on this server.</p>
				</div>
			</div>
		</div>
	</body>
</html>
<?php closeLogs(); ?>