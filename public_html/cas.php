<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
	$scenarioName = "";
	
	if(isset($_GET["scenario"])) {
		if(!isLoggedIn()) {
			createLog("warning", "-", "cas", "-", "Guest attempted to load scenario while not logged in", "-");
			//logInfoMsg("Guest [".$_SERVER["REMOTE_ADDR"]."] attempted to load a scenario while not logged in.");
			if(isset($_GET["share"]) && $_GET["share"] == "1") {
				header("Location: /login?scenario=".$_GET["scenario"]."&share=1");
				closeLogs();
			} else {
				header("Location: /cas");
				closeLogs();
			}
		}
				
		// Loop through the scenarios, get the JSON text, and add to an array.
		$scenarioArray = array();
		$error = 0;
		$numInvalidScenarios=0;
		foreach($_GET["scenario"] as $key => $value) {
			$tempScenario = getScenario($value);
			if(!is_int($tempScenario)) {
				array_push($scenarioArray, $tempScenario);
			} else {
				$numInvalidScenarios++;
				$error = $tempScenario;
			}
		}

		// There were no valid scenarios
		if(count($scenarioArray) == 0) {
			header("Location: /cas?s=$error");
		}

		/*$primaryScenario = getScenario($_GET["scenario"][0]);
		if(is_int($primaryScenario)) {
			header("Location: /cas?s=$primaryScenario");
			closeLogs();
		}*/

		if(count($_GET["scenario"]) > 1) {
			$scenarioName = "Combined Scenario";
		} else {
			$scenarioName = getScenarioName($_GET["scenario"][0]);
			if(is_int($scenarioName)) {
				$scenarioName = "";
			}
		}
		
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php 
			require("../req/head/head.php");
			require("../req/head/cas-head.php");
		?>

		<script>
			<?php if(isset($_GET["scenario"])) { ?>			
				var update = true;
			<?php } else { ?>
				var update = false;
			<?php } ?>
		</script>

		<?php 
			if($numInvalidScenarios > 0) { 
				if($numInvalidScenarios == 1) {
					$invalidScenarioText = "There was 1 invalid scenario that could not be loaded.";
				} else {
					$invalidScenarioText = "There were $numInvalidScenarios invalid scenarios that could not be loaded.";
				}
		?>
		<script>
			alert("<?php echo $invalidScenarioText; ?>");
		</script>
		<?php } ?>

	</head>
	<body>
		<?php require("../req/structure/navbar.php"); ?>
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>

		<div id="body-container" class="container-fluid flex-fill">
			<div id="alert-container" class="cas-alert mx-auto"><?php require("../req/structure/alert-container.php"); ?></div>
			<?php
				$ua = htmlentities($_SERVER["HTTP_USER_AGENT"], ENT_QUOTES, "UTF-8");
				if(preg_match("~MSIE|Internet Explorer~i", $ua) || preg_match("~edge~i", $ua) || (strpos($ua, "Trident/7.0") !== false && strpos($ua, "rv:11.0") !== false)) {
					require("../req/structure/ie-alert-cas.php");
				} else {
					require("../req/modals/cap-modal.php");
					require("../req/modals/tht-ring-modal.php");
					require("../req/modals/save-modal.php");
					require("../req/modals/load-modal.php");
					require("../req/modals/instructions-modal.php");
					require("../req/modals/add-15-line-modal.php");
					require("../req/modals/add-9-line-modal.php");
					require("../req/modals/chit-list-modal.php");
			?>
			<div class="row">
				<div class="col-lg-2" style="border-right: 1px;border-right-style: solid; border-right-color: rgba(0,0,0,.1);">
					<?php
						require("../req/cards/cas-fly-card.php");
						require("../req/cards/cas-chits-card.php");
					?>
				</div>
				<div class="col-lg-10" style="margin-top: -20px;">
					<div id="map"></div>
					<script src="./js/cas-leaflet.js" type="module"></script>
					<?php if(isset($_GET["scenario"])) { ?>
					<script type="module">
						import {loadScenario} from "./js/cas-leaflet.js";
						<?php foreach($scenarioArray as $scenario) { ?>
						loadScenario(JSON.stringify(<?php echo $scenario; ?>));
						<?php } ?>
					</script>
					<?php } ?>
				</div>
			</div>
			
			<?php } ?>
		</div>
	</body>
</html>
<?php closeLogs(); ?>