<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
	
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
				
		$scenario = getScenario($_GET["scenario"]);
		if(is_int($scenario)) {
			header("Location: /cas?s=$scenario");
			closeLogs();
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
						loadScenario(JSON.stringify(<?php echo $scenario; ?>));
					</script>
					<?php } ?>
				</div>
			</div>
			<div class="card-deck mb-3" style="margin-top:8rem;">
				<div class="card">
					<h3 class="card-header">Friendlies</h3>
					<div class="card-body">
						<table class="table table-sm">
							<thead>
								<tr>
									<th scope="col">Title</th>
									<th scope="col">MGRS</th>
								</tr>
							</thead>
							<tbody id="friendly-table">
							</tbody>
						</table>
					</div>
				</div>
				<div class="card">
					<h3 class="card-header">Hostiles</h3>
					<div class="card-body">
						<table class="table table-sm">
							<thead>
								<tr>
									<th scope="col">Title</th>
									<th scope="col">MGRS</th>
								</tr>
							</thead>
							<tbody id="hostile-table">
							</tbody>
						</table>
					</div>
				</div>
				<div class="card">
					<h3 class="card-header">Threats</h3>
					<div class="card-body">
						<table class="table table-sm">
							<thead>
								<tr>
									<th scope="col">Title</th>
									<th scope="col">Soverignty</th>
									<th scope="col">Threat Type</th>
									<th scope="col">MGRS</th>
								</tr>
							</thead>
							<tbody id="threat-table">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</body>
</html>
<?php closeLogs(); ?>