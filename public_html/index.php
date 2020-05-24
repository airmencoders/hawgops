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
			<p>You now have greater ability to edit CAPs after creating them, allowing you to edit the CAP rather than deleting and recreating the object.</p>
			<h4>Feature Roadmap</h4>
			<ol>
				<li>Edit 9 lines and 15 lines</li>
				<li>Editing ability to be added to polygons, threats, and other objects.</li>
				<li>Create and edit Phase lines.</li>
				<li>Export scenario (chits and airspace overlays) to KML for use with ATAK.</li>
				<li>Create / Export COF / IP Run Card</li>
				<li>Shift MGRS Gridlines onto lines</li>
			</ol>
			<hr class="my-4">
			<p>We're always looking for more ideas on how to enhance the mission planning process, if you have any ideas to make this product better, let us know!</p>
		</div>
	</div>
</body>

</html>
<?php closeLogs(); ?>