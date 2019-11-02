<?php
	require("../req/all/codes.php");
	require("../req/all/api-v1.php");
	
	createLog("info", "-", $_SERVER["REQUEST_URI"], "-", "Navigation", "-");
?>
<!DOCTYPE html>
<html lang="en">
	<head><?php require("../req/head/head.php"); ?></head>
	<body id="bg">
		<?php require("../req/structure/navbar.php")?>
		
		<noscript><?php require("../req/structure/js-alert.php"); ?></noscript>
		<div id="body-container" class="container">
			<div class="card my-5">
				<h4 class="card-header">
					Attribution
				</h4>
				<div class="card-body">
					<h4>Hawg Ops</h4>
					<p>Hawg Ops is created and owned by Porkins and is licensed under the GNU General Public License v3.0</p>
					<h4>Framework</h4>
					<p><a href="https://getbootstrap.com" target="_blank">Bootstrap (MIT / CC BY 3.0)</a></p>
					<p><a href="https://fontawesome.com" target="_blank">Font Awesome (CC BY 4.0 / SIL OFL 1.1 / MIT)</a></p>
					<p><a href="https://github.com/chrisveness/geodesy" target="_blank">Geodesy by chrisveness (MIT)</a></p>
					<p><a href="https://github.com/bgrins/spectrum" target="_blank">Spectrum Color Picker by bgrins (MIT)</a></p>
					<h4>Maps Provided by</h4>
					<p><a href="https://leafletjs.com" target="_blank">Leaflet JS (BSD-2)</a></p>
					<p><a href="https://esri.github.io/esri-leaflet" target="_blank">ESRI Leaflet (Apache 2.0)</a></p>
					<h4>Plugins</h4>
					<p><a href="https://github.com/PowerPan/leaflet.mouseCoordinate" target="_blank">Leaflet Mouse Coordinates by PowerPan (MIT)</a></p>
					<p><a href="https://github.com/trailbehind/leaflet-grids" target="_blank">Leaflet Grids (Modified by Porkins) by trailbehind (MIT)</a></p>
					<p><a href="https://github.com/jdfergason/Leaflet.Ellipse" target="_blank">Leaflet Ellipse by jdfergason (Apache 2.0)</a></p>
					<p><a href="https://github.com/gokertanrisever/leaflet-ruler" target="_blank">Leaflet Ruler by gokertanrisever (MIT)</a></p>
					<p><a href="https://github.com/Leaflet/Leaflet.draw" target="_blank">Leaflet Draw (Modified by Porkins) by Leaflet (MIT)</a></p>
					<p><a href="https://github.com/makinacorpus/Leaflet.GeometryUtil" target="_blank">Leaflet Geometry Util by makinacorpus (BSD-3-Clause)</a></p>
					<p><a href="https://github.com/Leaflet/Leaflet.fullscreen" target="_blank">Leaflet Fullscreen by Leaflet (ISC)</a></p>
				</div>
			</div>
		</div>
	</body>	
</html>
<?php closeLogs(); ?>