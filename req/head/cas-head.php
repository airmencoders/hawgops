<!-- Vendor CSS -->
<!-- Leaflet -->
<!--<link rel="stylesheet" href="./js/leaflet/leaflet.css">-->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
<!-- ESRI -->
<link rel="stylesheet" href="https://js.arcgis.com/4.11/esri/themes/light/main.css">

<link rel="stylesheet" href="./css/leaflet.mousecoordinate.css">
<link rel="stylesheet" href="./css/spectrum.css">
<link rel="stylesheet" href="./css/leaflet-ruler.css">
<link rel="stylesheet" href="./js/leaflet-plugins/leaflet-draw/leaflet.draw.css">
<link rel="stylesheet" href="./css/leaflet.fullscreen.css">
<link rel="stylesheet" href="./js/leaflet-plugins/easybutton/easy-button.css">

<!-- Vendor jQuery / Popper.js / Bootstrap JS / Leaflet JS -->

<!-- Leaflet -->
<!--<script src="./js/leaflet/leaflet-src.js"></script>-->
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

<!-- Leaflet Grids -->
<script src="./js/leaflet-grids/mgrs.js"></script>
<script src="./js/leaflet-grids/leaflet-grids.js"></script>

<!-- Geodesy -->
<script src="./js/geodesy/dms.js" type="module"></script>
<script src="./js/geodesy/mgrs.js" type="module"></script>
<script src="./js/geodesy/utm.js" type="module"></script>

<!-- Leaflet Plugins -->
<script src="./js/leaflet-plugins/leaflet.mousecoordinate.js"></script>
<script src="./js/leaflet-plugins/l.ellipse.js"></script>
<script src="./js/spectrum.js"></script>
<script src="./js/leaflet-plugins/leaflet-ruler.js"></script>
<script src="./js/leaflet-plugins/leaflet.geometryutil.js"></script>
<script src="./js/leaflet-plugins/Leaflet.fullscreen.js"></script>
<script src="./js/leaflet-plugins/easybutton/easy-button.js"></script>

<!-- Leaflet Draw -->
<script src="./js/leaflet-plugins/leaflet-draw/Leaflet.draw.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/Leaflet.Draw.Event.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/Toolbar.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/Tooltip.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/Control.Draw.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/ext/GeometryUtil.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/ext/LatLngUtil.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/ext/LineUtil.Intersect.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/ext/Polyline.Intersect.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/ext/TouchEvents.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/DrawToolbar.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Feature.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.SimpleShape.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Polyline.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Flot.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Marker.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Circle.js" type="module"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.CircleMarker.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Polygon.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/draw/handler/Draw.Rectangle.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/EditToolbar.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/EditToolbar.Edit.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/EditToolbar.Delete.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.Poly.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.SimpleShape.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.Rectangle.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.Marker.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.CircleMarker.js"></script>
<script src="./js/leaflet-plugins/leaflet-draw/edit/handler/Edit.Circle.js"></script>

<!-- Esri-Leaflet-->
<script src="https://unpkg.com/esri-leaflet@2.3.0/dist/esri-leaflet.js" integrity="sha512-1tScwpjXwwnm6tTva0l0/ZgM3rYNbdyMj5q6RSQMbNX6EUMhYDE3pMRGZaT41zHEvLoWEK7qFEJmZDOoDMU7/Q==" Crossorigin=""></script>

<!-- Airspace -->
<script src="./js/cas-airspace.js"></script>

<!-- Custom CSS -->
<style>
	body {
		margin: 0;
		padding: 0;
	}
	#map {
		position: absolute;
		top: 20px;
		bottom: 0;
		right: 0;
		left: 0;
		height: 90vh;
		width: 100%;
	}
	.grid-label {
		border: 1px solid black;
		padding-top: 2px;
		padding-right: 2px;
		padding-bottom: 2px;
		padding-left: 2px;
		margin: 0px;
		background-color: #FFCCCC;
		font-size: 12px;
		font-weight:bold;
	}
	#center {
		margin-bottom: 10px;
	}
</style>
<script>
// Create a layer group for markers
	var layer_markers = L.layerGroup();
	var layer_bldg_markers = L.layerGroup();
	var layer_friendly_markers = L.layerGroup();
	var layer_hostile_markers = L.layerGroup();
	var layer_survivor_markers = L.layerGroup();
	var layer_master_threats = L.layerGroup();
	var layer_threat_markers = L.layerGroup();
	var layer_threats = L.layerGroup();
	var layer_caps = L.layerGroup();
	//var layer_drawings = new L.FeatureGroup();
	var layer_lines = new L.FeatureGroup();
	var layer_polygons = new L.FeatureGroup();
	var layer_eas = new L.FeatureGroup();
	var layer_rozs = new L.FeatureGroup();
	
	layer_threats.addTo(layer_master_threats);
	layer_threat_markers.addTo(layer_master_threats);
</script>