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
			<?php
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
				</div>
			</div>
		</div>
	</body>
</html>