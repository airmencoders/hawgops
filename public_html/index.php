<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
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
			<div id="alert-container"><?php require("../req/structure/alert-container.php"); ?></div>

			<div class="jumbotron my-5">
				<h1 class="display-4">Premier Close Air Support Mission Planning</h1>
				<p class="lead">Integration is essential to CAS. With Hawg Ops CAS Planner, mission planning takes less time, allows you to share scenarios &amp; GRGs with participating units, and use your scenario as a visual aid for flight briefs &amp; debriefs.</p>
				<hr class="my-4">
				<h4>What's new?</h4>
				<p>Now you have more control on what elements are shown on the map. Showing only friendly, hostile, or other elements and hiding the rest can declutter the map in order to emphasize crucial elements.</p>
				<p>Combine multiple scenarios into one map for IPOR debriefs and provide truth data to flight members.</p>
				<hr class="my-4">
				<p>We're always looking for more ideas on how to enhance the mission planning process, if you have any ideas to make this product better, let us know!</p> 
			</div>
		</div>
    </body>
</html>
<?php closeLogs(); ?>