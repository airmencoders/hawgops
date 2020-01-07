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
	
	$userArray = getAllUsers();
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
						<th scope="col">Name</th>
						<th scope="col">ID (Click to view authenticated IPs)</th>
						<th scope="col">Email</th>
						<th scope="col">Date Joined (Z)</th>
						<th scope="col">Last Login (Z)</th>
						<th scope="col">Scenarios</th>
						<th scope="col">Disabled</th>
						<th scope="col">Admin</th>
						<th scope="col">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$user_count = 1;
						foreach($userArray as $user) { 
						
							$owner = false;
							if($user["id"] == "17a11b7b97ecfa0b356e31c6dd75c6bf5a713ecc7932b5361de2913c0bf76080") {
								$owner = true;
							}
							
							$scenarioCount = getNumberOfScenariosByUser($user["id"]);
					?>
						<tr>
							<td><?php echo $user_count; $user_count++; ?></td>
							<td><?php echo $user["fname"]." ".$user["lname"]; ?></td>
							<td><a href="/admin-iplog?id=<?php echo $user["id"]; ?>"><?php echo $user["id"]; ?></a></td>
							<td><?php echo $user["email"]; ?></td>
							<td><?php echo $user["joined"]; ?></td>
							<td><?php echo $user["lastLogin"]; ?></td>
							<td><a role="button" class="btn btn-block btn-primary" href="/admin-scenarios?id=<?php echo $user["id"]; ?>" >Scenarios (<?php echo $scenarioCount; ?>)</a></td>
							<td><button type="button" class="btn btn-block btn-<?php echo ($user["disabled"]) ? "success" : "warning"; ?>" <?php echo ($owner) ? "disabled" : ""; ?>><?php echo ($user["disabled"]) ? "Enable" : "Disable"; ?></button></td>
							<td><button type="button" class="btn btn-block btn-<?php echo ($user["admin"]) ? "danger" : "success"; ?>" <?php echo ($owner) ? "disabled" : ""; ?>><?php echo ($user["admin"]) ? "Revoke" : "Grant"; ?></button></td>
							<td><button type="button" class="btn btn-block btn-danger" <?php echo ($owner) ? "disabled" : ""; ?>>Delete</button></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
    </body>
</html>
<?php closeLogs(); ?>