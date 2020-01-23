<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-view-log", "-", "User not logged in", "-");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-view-log", "-", "User not admin", "Email: ".getUserEmailByID($_SESSION["id"]));
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_GET["log"])) {
		createLog("warning", "-", "admin-view-log", "-", "Log name not received", "-");
		header("Location: /admin-logs?s=$ADMIN_VIEW_LOG_NAME_NOT_RECEIVED");
		closeLogs();
	}
	
	$logs = file_get_contents("../logs/".$_GET["log"].".txt");
	$logs = rtrim($logs, ",");
	$logs = "[".$logs."]";
	$logs = json_decode($logs, true);
	
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
			
			<table class="table table-bordered my-5">
				<thead>
					<tr class="table-light">
						<th scope="col">Date/Time</th>
						<th scope="col">User</th>
						<th scope="col">IP</th>
						<th scope="col">Caller</th>
						<th scope="col">Function</th>					
						<th scope="col">Code</th>
						<th scope="col">Activity</th>
						<th scope="col">Details</th>						
						<th scope="col">Location</th>
						<th scope="col">Lat</th>
						<th scope="col">Lng</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($logs as $log) { ?>
					
					<tr class="table-<?php echo $log["level"]; ?>">
						<td><?php echo $log["datetime"]; ?></td>
						<td><?php echo $log["user"]; ?></td>
						<td><a href="https://whatismyipaddress.com/ip/<?php echo $log["ip"]; ?>" target="_blank"><?php echo $log["ip"]; ?></td>
						<td><?php echo $log["caller"]; ?></td>
						<td><?php echo $log["function"]; ?></td>
						<td><?php echo $log["code"]; ?></td>
						<td><?php echo $log["activity"]; ?></td>
						<td><?php echo $log["details"]; ?></td>
						<td><?php echo $log["location"]; ?></td>
						<td><?php echo $log["lat"]; ?></td>
						<td><?php echo $log["lng"]; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
    </body>
</html>
<?php closeLogs(); ?>