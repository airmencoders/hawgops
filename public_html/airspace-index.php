<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("../req/head/head.php"); ?>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container-fluid">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<div class="card mt-5">
				<h3 class="card-header">Airspace Index</h3>
				<div class="card-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">Type</th>
								<th scope="col">Name</th>
								<th scope="col">State</th>
								<th scope="col">Controlling Agency</th>
								<th scope="col">Using Agency</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<small class="card-footer">Please contact me if you would like to add your airspace to the CAS Planner.</small>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>