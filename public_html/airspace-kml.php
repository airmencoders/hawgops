<?php
	require("../req/all/codes.php");
	require("../req/keys/mysql.php");
	require("../req/keys/recaptcha.php");
	require("../req/all/api-v1.php");
	
	createLog("info", $HTTP_OK, $_SERVER["REQUEST_URI"], "-", "Navigation", "-");

	if(isset($_GET["download"]) && $_GET["download"] == "true") {
		$date = date("dMY-H:i:se", filemtime("./js/cas-airspace.js"));
		header("Content-Type: text/plain");
		header("Content-Disposition: attachment; filename=\"$date-Hawg Ops Airspace.kml\"");

		$kmlData= '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"><Document><name>'.$date.'-Hawg Ops Airspace</name><open>1</open><Style id="poly_red"><LineStyle><color>ff0000ff</color><width>5</width></LineStyle><PolyStyle><color>000000ff</color></PolyStyle></Style><Style id="poly_orange"><LineStyle><color>ff0090ff</color><width>5</width></LineStyle><PolyStyle><color>000090ff</color></PolyStyle></Style><Style id="poly_blue"><LineStyle><color>ffffff00</color><width>5</width></LineStyle><PolyStyle><color>00ffff00</color></PolyStyle></Style><Style id="poly_green"><LineStyle><color>ff00ff00</color><width>5</width></LineStyle><PolyStyle><color>0000ff00</color></PolyStyle></Style><Style id="poly_purple"><LineStyle><color>ffff00aa</color><width>5</width></LineStyle><PolyStyle><color>00ff00aa</color></PolyStyle></Style><Style id="thick_line_red"><LineStyle><color>ff0000ff</color><width>5</width></LineStyle><PolyStyle><color>000000ff</color></PolyStyle></Style><Style id="thin_line_red"><LineStyle><color>ff0000ff</color><width>2</width></LineStyle><PolyStyle><color>000000ff</color></PolyStyle></Style><Style id="thick_line_black"><LineStyle><color>ff000000</color><width>4</width></LineStyle><PolyStyle><color>00000000</color></PolyStyle></Style>';
		$closingData = '</Document></kml>';
		$closingPlacemark = '</coordinates></LinearRing></outerBoundaryIs></Polygon></Placemark>';
		
		// Get the airspace from my javascript
		$airspace = file_get_contents("./js/cas-airspace.js");

		// Change all the JavaScript "var" to PHP variables with a delimiter
		$airspace = str_replace("var ", "~~$", $airspace);

		// Explode out all the delimiters I made
		$airspaceExploded = explode("~~", $airspace);

		// Only evaluate anything that starts with a PHP variable $
		// This is becuase the first chunk is just a comment block
		foreach($airspaceExploded as $a) {
			if(substr($a, 0, 1) == "$") {		
				eval($a);
			}
		}

		// Add all the Korea LLZs
		$kmlData .= '<Folder id="Korea_LLZs"><name>Korea LLZs</name><open>1</open>';
		foreach($korea_llzs as $llz) {
			$kmlData .= '<Placemark><styleUrl>#poly_purple</styleUrl><Polygon><outerBoundaryIs><LinearRing><coordinates>';

			foreach($llz as $latlng) {
				$kmlData .= $latlng[1].",".$latlng[0]."\n";
			}

			$kmlData .= $closingPlacemark;
		}

		// Add all the Low MOAs 
		$kmlData .= '</Folder><Folder id="Low_MOAs"><name>Low MOAs</name><open>1</open>';
		foreach($low_moas as $lm) {
			$kmlData .= '<Placemark><styleUrl>#poly_green</styleUrl><Polygon><outerBoundaryIs><LinearRing><coordinates>';

			foreach($lm as $latlng) {
				$kmlData .= $latlng[1].",".$latlng[0]."\n";
			}

			$kmlData .= $closingPlacemark;
		}

		// Add all the MOAs
		$kmlData .= '</Folder><Folder id="MOAs"><name>MOAs</name><open>1</open>';
		foreach($moas as $m) {
			$kmlData .= '<Placemark><styleUrl>#poly_blue</styleUrl><Polygon><outerBoundaryIs><LinearRing><coordinates>';

			foreach($m as $latlng) {
				$kmlData .= $latlng[1].",".$latlng[0]."\n";
			}

			$kmlData .= $closingPlacemark;
		}

		// Add all the Warning Areas
		$kmlData .= '</Folder><Folder id="warning_areas"><name>Warning Areas</name><open>1</open>';
		foreach($warning_areas as $w) {
			$kmlData .= '<Placemark><styleUrl>#poly_orange</styleUrl><Polygon><outerBoundaryIs><LinearRing><coordinates>';

			foreach($w as $latlng) {
				$kmlData .= $latlng[1].",".$latlng[0]."\n";
			}

			$kmlData .= $closingPlacemark;
		}

		// Add all the Restricted Areas
		$kmlData .= '</Folder><Folder id="Restriced_areas"><name>Restricted Areas</name><open>1</open>';
		foreach($restricted_areas as $r) {
			$kmlData .= '<Placemark><styleUrl>#poly_red</styleUrl><Polygon><outerBoundaryIs><LinearRing><coordinates>';

			foreach($r as $latlng) {
				$kmlData .= $latlng[1].",".$latlng[0]."\n";
			}

			$kmlData .= $closingPlacemark;
		}

		// Add Korea No Fly Line
		$kmlData .= '</Folder><Folder id="Korea_NFL"><name>Korea NFL</name><open>1</open><Placemark><styleUrl>#thick_line_black</styleUrl><LineString><tessellate>1</tessellate><coordinates>';

		foreach($korea_nfl as $latlng) {
			$kmlData .= $latlng[1].",".$latlng[0]."\n";
		}

		$kmlData .= '</coordinates></LineString></Placemark>';

		// Add Korea 2NM Buffer
		$kmlData .= '</Folder><Folder id="2NM_Buffer"><name>2NM Buffer</name><open>1</open><Placemark><styleUrl>#thin_line_red</styleUrl><LineString><tessellate>1</tessellate><coordinates>';

		foreach($korea_nfl_buffer as $latlng) {
			$kmlData .= $latlng[1].",".$latlng[0]."\n";
		}

		$kmlData .= '</coordinates></LineString></Placemark>';

		// Add P-518 Border
		$kmlData .= '</Folder><Folder id="P518_Border"><name>P-518 Border</name><open>1</open><Placemark><styleUrl>#thick_line_red</styleUrl><LineString><tessellate>1</tessellate><coordinates>';
		foreach($p518_border as $latlng) {
			$kmlData .= $latlng[1].",".$latlng[0]."\n";
		}

		$kmlData .= '</coordinates></LineString></Placemark>';

		// Finish the Folders
		$kmlData .= '</Folder>'.$closingData;

		echo $kmlData;
	} else {
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
			<div class="card card-sm mt-5 mx-auto">
				<h3 class="card-header">Download Airspace Overlays KML</h3>
				<div class="card-body">
					<p>Click on the button below to download a KML version of the Airspace that is used within the Hawg Ops CAS Planner.</p>
					<small><code>cas-airspace</code> was last modified on <?php echo date("d M, Y H:i:s e", filemtime("./js/cas-airspace.js")); ?>.</small>
				</div>
				<div class="card-footer">
					<a href="/airspace-kml?download=true" class="btn btn-primary btn-block">Download KML</a>
					<hr/>
					<small>Google Earth is required to view a KML file. You can download Google Earth <a href="https://www.google.com/earth/versions/#download-pro" target="_blank">here</a>. Or, you can import the KML to Google Earth Web <a href="https://earth.google.com/web/" target="_blank">here</a>.</small>
				</div>
			</div>
		</div>
    </body>
</html>
<?php 
}
closeLogs(); 
?>