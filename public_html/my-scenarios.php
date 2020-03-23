<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	if(!isLoggedIn()) {
		createLog("warning", $ERROR_UNAUTHORIZED, "my-scenarios", "-", "User not logged in", "-");
		//logErrorMsg("User is not logged in. ($ERROR_UNAUTHORIZED)");
		header("Location: /?s=$ERROR_UNAUTHORIZED");
		closeLogs();
	}
	
	$scenarioArray = getUserScenarios($_SESSION["id"]);
	if(is_int($scenarioArray)) {
		//logErrorMsg("There was an error while getting user scenarios for user [".$_SESSION["id"]."]");
		header("Location: /?s=$scenarioArray");
		closeLogs();
	}
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
				<h3 class="card-header">My Scenarios</h3>
				<div class="card-body">
					<?php if(count($scenarioArray) == 0) { ?>
					<h4>You don't have any saved scenarios.</h4>
					<?php } else { ?>
					<ul class="list-group">
						<?php foreach($scenarioArray as $scenario) { ?>
						<li class="list-group-item d-flex flex-wrap justify-content-between">
							<?php echo $scenario["name"]." (Created On ".date("d M, Y", strtotime($scenario["date"]))." at ".date("H:i", strtotime($scenario["date"]))." Z)"; ?> 
							<div id="buttons">
								<a class="btn btn-success" href="/cas?scenario[]=<?php echo $scenario["id"];?>" role="button">Load</a>
								<button type="button" class="btn btn-primary btn-share-scenario" data-name="<?php echo $scenario["name"]; ?>" data-id="<?php echo $scenario["id"]; ?>" data-toggle="modal" data-target="#share-scenario-modal">Share</button>
								<button type="button" class="btn btn-danger btn-del" data-name="<?php echo $scenario["name"]; ?>" data-id="<?php echo $scenario["id"]; ?>" data-toggle="modal" data-target="#del-scenario-modal">Delete</button>
							</div>
						</li>
						<?php } ?>
					</ul>
					<?php } ?>
				</div>
			</div>
			<?php if(count($scenarioArray) > 0) { ?>
			<form method="GET" action="/cas">
				<div class="card my-5">
					<h3 class="card-header">Load Combined Scenario</h3>
					<div class="card-body">
						<ul class="list-group">
							<?php foreach($scenarioArray as $scenario) { ?>
								<li class="list-group-item d-flex">
									<div class="custom-control custom-switch">
										<input type="checkbox" class="custom-control-input" id="<?php echo $scenario["id"]; ?>" name="scenario[]" value="<?php echo $scenario["id"]; ?>">
										<label class="custom-control-label" for="<?php echo $scenario["id"]; ?>"><?php echo $scenario["name"]." (Created On ".date("d M, Y", strtotime($scenario["date"]))." at ".date("H:i", strtotime($scenario["date"]))." Z)"; ?></label>
									</div>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="card-footer">
						<button type="submit" class="btn btn-block btn-primary">Load Combined Scenario</button>
					</div>
				</div>
			</form>
			<?php } ?>
		</div>
    </body>
</html>
<?php closeLogs(); ?>