<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-users", "-", "User not logged in", "-");
		//logErrorMsg("Guest [".$_SERVER["REMOTE_ADDR"]."] attempted to view page while not logged in.");
		header("Location: /login?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-users", "-", "User not admin", "Email: ".getUserEmailByID($_SESSION["id"]));
		//logErrorMsg("User ID [".$_SESSION["id"]."] attempted to view an admin page without rights.");
		header("Location: /my-scenarios?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_GET["id"]) || $_GET["id"] == "") {
		createLog("warning", $ERROR_ADMIN_IPLOG_USER_NOT_RECEIVED, "admin-iplog", "-", "User ID not received", "-");
		header("Location: /admin-users?s=$ERROR_ADMIN_IPLOG_USER_NOT_RECEIVED");
		closeLogs();
	}
	
	$ipArray = getIPLogByUser($_GET["id"]);
	
	if(is_int($ipArray)) {
		createLog("danger", $ipArray, "admin-iplog", "-", "Error while getting user's authenticated IPs", "-");
		header("Location: /admin-users?s=$ipArray");
		closeLogs();
	}
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
		
		<div id="body-container" class="container-fluid">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			
			<table class="table table-striped table-light table-bordered my-5">
				<thead>
					<tr class="table-light">
						<th scope="col">#</th>
						<th scope="col">IP Address</th>
						<th scope="col">Most Recent Usage (Z)</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$ip_count = 1;
						foreach($ipArray as $ip) { 
					?>
						<tr>
							<td><?php echo $ip_count; $ip_count++; ?></td>
							<td><?php echo $ip["ip"]; ?></td>
							<td><?php echo $ip["date"]; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
    </body>
</html>
<?php closeLogs(); ?>