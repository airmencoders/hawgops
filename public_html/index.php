<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php 
			require("../req/head/head.php"); 
		?>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<div class="card my-5">
				<h3 class="card-header">Known CAS Planner Issues</h3>
				<div class="card-body">
					<ul class="list-group">
						<li class="list-group-item">Elevation service from nationalmap.gov is currently down - Unable to pull elevation</li>
						<li class="list-group-item">ESRI Aerial Clarity Imagery is currently INOP - Only able to use ESRI Aerial Firefly</li>
					</ul>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>