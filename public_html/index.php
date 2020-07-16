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
			<h4>NIPR Issues</h4>
			<p>The past few days, there have been issues with scenarios not loading and menus not working. NIPR was blocking some downloads that prevented site functionality. The issue should be fixed now, but if you run into any issues, please contact me.</p>
			<h4>Version 2!</h4>
			<p>Version 2 of Hawg Ops is currently in the works. It is packed with even more features to make the experience even more user friendly. If you have ideas that you would like to see incorporated into Version 2, let me know!</p>
			<h4>Features in Version 2</h4>
			<ol>
				<li>Greater options to customize building labels</li>
				<li>Edit 9 lines and 15 lines</li>
				<li>More options to customize drawing objects</li>
				<li>Edit Drawing Objects</li>
				<li>Export scenario (chits and airspace overlays) to KML for use with ATAK.</li>
				<li>Create / Export COF / IP Run Card</li>
				<li>Shift MGRS Gridlines onto lines</li>
				<li>Undo / Redo map actions</li>
				<li>More Ground Chits</li>
				<li>Change Chit size</li>
				<li>Go to point with either MGRS or LatLng</li>
				<li>Platform One SSO</li>
			</ol>
			<p>Partnering with Platform One will provide more features, but will restrict access to US members only. I plan on releasing an open version of Version 2, but will drop support for accounts altogether on the open version. I will ensure that users with saved scenarios will <strong>NOT</strong> lose their scenarios. If you have questions or concerns, feel free to let me know.</p>
			<hr class="my-4">
			<p>We're always looking for more ideas on how to enhance the mission planning process, if you have any ideas to make this product better, let us know!</p>
		</div>
	</div>
</body>

</html>
<?php closeLogs(); ?>