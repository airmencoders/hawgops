<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php require("../req/head/head.php"); ?>
	</head>
	<body id="bg">
		<?php require("../req/structure/navbar.php"); ?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		
		<div id="body-container" class="container">
			
			<?php require("../req/cards/loading-card.php"); ?>
			
			<?php
				if(!isset($_GET["homestation"]) || $_GET["homestation"] == "") {
					require("../req/cards/wx-form-card.php");
				} else {
					require("../req/cards/wx-details-card.php");
				}
			?>
		</div>
	</body>
</html>
<?php closeLogs(); ?>