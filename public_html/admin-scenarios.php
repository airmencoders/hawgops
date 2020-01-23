<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-scenarios", "-", "User not logged in", "-");
		//logErrorMsg("User is not logged in. ($ERROR_UNAUTHORIZED)");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isAdmin()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "admin-scenarios", "-", "User not admin", "Email: ".getUserEmailByID($_SESSION["id"]));
		//logErrorMsg("User ID [".$_SESSION["id"]."] attempted to view an Admin page without proper rights.");
		header("Location: /my-scenarios?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	if(!isset($_GET["id"]) || $_GET["id"] == "") {
		createLog("warning", $ERROR_ADMIN_SCENARIOS_USER_ID_NOT_RECIEVED, "admin-scenarios", "-", "User ID not received", "-");
		//logErrorMsg("User ID was not received.");
		header("Location: /admin-users?s=$ERROR_ADMIN_SCENARIOS_USER_ID_NOT_RECEIVED");
		closeLogs();
	}
	
	$scenarioArray = getUserScenarios($_GET["id"]);
	if(is_int($scenarioArray)) {
		//logErrorMsg("There was an error while getting user scenarios for user [".$_GET["id"]."]");
		header("Location: /admin-users?s=$scenarioArray");
		closeLogs();
	}
	
	$userName = getUserNameByID($_GET["id"]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require("../req/head/head.php"); ?>
		<script src="./js/validation.js"></script>
		<script>
			$(document).ready(function() {
				$(".btn-del").click(function() {
					$("#del-scenario-name").text($(this).attr("data-name"));
					$("#scenario-id").val($(this).attr("data-id"));
					$("#btn-del-scenario-confirm").text("Delete \"" + $(this).attr("data-name") + "\"");
				});
				
				$(".btn-share-scenario").click(function() {
					$("#share-scenario-name").text($(this).attr("data-name"));
					$("#share-scenario-id").val($(this).attr("data-id"));
					$("#scenario-name").val($(this).attr("data-name"));
				});
			});
		</script>
    </head>
    <body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>
			<?php
				require("../req/modals/del-scenario-modal.php");
				require("../req/modals/share-scenario-modal.php");
			?>
			<div class="card mt-5">
				<h3 class="card-header"><?php echo $userName["fname"]." ".$userName["lname"]; ?>'s Scenarios</h3>
				<div class="card-body">
					<?php if(count($scenarioArray) == 0) { ?>
					<h4><?php echo $userName["fname"]." ".$userName["lname"]; ?> doesn't have any saved scenarios.</h4>
					<?php } else { ?>
					<ul class="list-group">
						<?php
							foreach($scenarioArray as $scenario) {
						?>
						<li class="list-group-item d-flex justify-content-between">
							<?php echo $scenario["name"]." (Created On ".date("d M, Y", strtotime($scenario["date"]))." at ".date("H:i", strtotime($scenario["date"]))." Z)"; ?> 
							<div id="buttons" class="d-inline">
								<a class="btn btn-success" href="/cas?scenario=<?php echo $scenario["id"];?>" role="button">Load</a>
								<button type="button" class="btn btn-primary btn-share-scenario" data-name="<?php echo $scenario["name"]; ?>" data-id="<?php echo $scenario["id"]; ?>" data-toggle="modal" data-target="#share-scenario-modal">Share</button>
								<button type="button" class="btn btn-danger btn-del" data-name="<?php echo $scenario["name"]; ?>" data-id="<?php echo $scenario["id"]; ?>" data-toggle="modal" data-target="#del-scenario-modal">Delete</button>
							</div>
						</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</div>
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>