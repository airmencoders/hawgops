<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	if(!isLoggedIn()) {
		logErrorMsg("Guest [".$_SERVER["REMOTE_ADDR"]."] attempted to view page while not logged in.");
		header("Location: /login?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		logErrorMsg("User ID [".$_SESSION["id"]."] attempted to view an admin page without rights.");
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
			<div class="card my-5">
				<div class="card-body">
					<ul class="list-group">
						<?php foreach($userArray as $user) { ?>
						<li class="list-group-item d-flex justify-content-between">
							<?php echo $user["fname"]." ".$user["lname"]." [".$user["id"]."] [".$user["email"]."] Joined on ".date("d M, Y", strtotime($user["joined"]))." at ".date("H:i", strtotime($user["joined"]))." Z"; ?>
							<div id="buttons" class="d-inline">
								<a role="button" href="/admin-scenarios?id=<?php echo $user["id"]; ?>" class="btn btn-primary">View Scenarios</a>
								<?php 
									// Don't allow owner to be deleted/revoked, etc...
									if($user["id"] == "17a11b7b97ecfa0b356e31c6dd75c6bf5a713ecc7932b5361de2913c0bf76080") { 
								?>
								<button type="button" class="btn btn-<?php echo ($user["disabled"]) ? "success" : "warning"; ?>" <?php echo ($user["disabled"]) ? "" : "disabled"; ?>><?php echo ($user["disabled"]) ? "Enable" : "Disable"; ?></button>
								<button type="button" class="btn btn-<?php echo ($user["admin"]) ? "danger" : "success"; ?>" disabled><?php echo ($user["admin"]) ? "Revoke Admin" : "Grant Admin"; ?></button>
								<button type="button" class="btn btn-danger" disabled>Delete User</button>
								<?php } else { ?>
								<button type="button" class="btn btn-<?php echo ($user["disabled"]) ? "success" : "warning"; ?>"><?php echo ($user["disabled"]) ? "Enable" : "Disable"; ?></button>
								<button type="button" class="btn btn-<?php echo ($user["admin"]) ? "danger" : "success"; ?>"><?php echo ($user["admin"]) ? "Revoke Admin" : "Grant Admin"; ?></button>
								<button type="button" class="btn btn-danger">Delete User</button>
								<?php } ?>
								
							</div>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>