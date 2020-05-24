/**
 * Import Statements
 */
import Mgrs, { LatLon } from "./geodesy/mgrs.js";
import Dms from "./geodesy/dms.js";
import * as togeojson from "./leaflet-plugins/togeojson/index.js";

/**
 * Site Options
 */
var chit_scale = 3;
var tht_scale = 3;
var bldg_label_scale = 10;
var minor_axis = 2 * 926;
var tht_ring_dash_array = "12,12";

/**
 * Initialize the marker_id
 */
var marker_id = 1;

/**
 * Declare various options for map and map elements
 */
var map_options = {
	worldCopyJump: true,
	fullscreenControl: true
}

var mouse_coord_options = {
	gps: false,
	gpsLong: false,
	utmref: true
};

var nm_ruler_options = {
	position: "topleft",
	lengthUnit: {
		factor: 0.539956803,
		display: "NM",
		decimal: 2,
		label: "NM:"
	}
};

var draw_control_options = {
	draw: {
		flot: true,
		marker: false,
		circlemarker: false
	}
};

/**
 * Declare Airspace line options
 */
// ATCAAs
var atcaa_options = {
	stroke: true,
	color: "#ffff00",
	weight: 2,
	fill: false,
	clickable: false
}
// Blue MOAs (High)
var moa_options = {
	stroke: true,
	color: "#00ffff",
	weight: 2,
	fill: false,
	clickable: false
};

// Green MOAs (Low)
var moa_low_options = {
	stroke: true,
	color: "#00ff00",
	weight: 2,
	fill: false,
	clickable: true
};

// Ranges (Red)
var range_options = {
	stroke: true,
	color: "#ff0000",
	weight: 2,
	fill: false,
	clickable: false
};

// Warning Areas (Orange)
var warning_options = {
	stroke: true,
	color: "#ff9000",
	weight: 2,
	fill: false,
	clickable: false
};

var aar_options = {
	stroke: true,
	color: "#070080",
	weight: 2,
	fill: false,
	clickable: false
};

// Low Level Zones (Purple)
var llz_options = {
	stroke: true,
	color: "#ff00ff",
	weight: 2,
	fill: false,
	clickable: false
};

// Korean NFL (Thick Black)
var nfl_options = {
	stroke: true,
	color: "#000000",
	weight: 4,
	fill: false,
	clickable: false,
	dashArray: "20,10,10,10"
};

// Korean NFL Buffer (Thin Red)
var nfl_buffer_options = {
	stroke: true,
	color: "#ff0000",
	weight: 2,
	fill: false,
	clickable: false,
	dashArray: "20,10,10,10"
};

// Korean P-518 border (Thick Red)
var p518_options = {
	stroke: true,
	color: "#ff0000",
	weight: 4,
	fill: false,
	clickable: false
};

/**
 * Initialize the LeafletJS Map and map plugins
 */
var map = L.map("map", map_options).setView([35.77, -93.34], 5);
var scale = L.control.scale();
var mouse_coords = L.control.mouseCoordinate(mouse_coord_options);
var nm_ruler = L.control.ruler(nm_ruler_options);
var drawControl = new L.Control.Draw(draw_control_options);

/**
 * Drawing layers are initialized globally in cas-head.php
 * Add them to the map here
 */
layer_markers.addTo(map);
layer_bldg_markers.addTo(map);
layer_friendly_markers.addTo(map);
layer_hostile_markers.addTo(map);
layer_survivor_markers.addTo(map);
layer_master_threats.addTo(map);
layer_caps.addTo(map);
layer_lines.addTo(map);
layer_polygons.addTo(map);
layer_eas.addTo(map);
layer_rozs.addTo(map);
scale.addTo(map);
nm_ruler.addTo(map);
map.addControl(drawControl);

/**
 * Initialize basemap and labels
 */
var basemap_clarity = L.esri.basemapLayer("ImageryClarity");
var basemap_firefly = L.esri.basemapLayer("ImageryFirefly");
var labels_imagery = L.esri.basemapLayer("ImageryLabels");
var labels_roads = L.esri.basemapLayer("ImageryTransportation");
var labels_airspace = L.layerGroup();
var labels_aars = L.layerGroup();
var labels_atcaas = L.layerGroup();
var labels_kml = L.layerGroup();
var mgrs_grids = L.grids.mgrs();
var labels_old_bmgr = L.layerGroup();
var labels_new_bmgr = L.layerGroup();

/**
 * Add airspace to the airspace layer
 */
L.polygon(korea_llzs, llz_options).addTo(labels_airspace);
L.polygon(low_moas, moa_low_options).addTo(labels_airspace);
L.polygon(moas, moa_options).addTo(labels_airspace);
L.polygon(warning_areas, warning_options).addTo(labels_airspace);
L.polygon(restricted_areas, range_options).addTo(labels_airspace);
L.polygon(aars, aar_options).addTo(labels_aars);
L.polygon(atcaas, atcaa_options).addTo(labels_atcaas)
L.polyline(p518_border, p518_options).addTo(labels_airspace);
L.polyline(korea_nfl_buffer, nfl_buffer_options).addTo(labels_airspace);
L.polyline(korea_nfl, nfl_options).addTo(labels_airspace);
L.polygon(old_bmgr, range_options).addTo(labels_old_bmgr);
L.polygon(new_bmgr, range_options).addTo(labels_new_bmgr);



/**
 * Create object that holds the basemaps and the labels
 */
var basemap_layers = {
	"ESRI Aerial Clarity": basemap_clarity,
	"ESRI Aerial Firefly": basemap_firefly
};

var label_layers = {
	"Map Labels": labels_imagery,
	"Road Labels": labels_roads,
	"Airspace": labels_airspace,
	"Current BMGR": labels_old_bmgr,
	"New BMGR": labels_new_bmgr,
	"AAR Tracks": labels_aars,
	"ATCAAs": labels_atcaas,
	"KML": labels_kml,
	"MGRS Grids": mgrs_grids,
	"Threat Rings": layer_master_threats,
	"Friendly Chits": layer_friendly_markers,
	"Hostile Chits": layer_hostile_markers,
	"Survivor Chits": layer_survivor_markers,
	"IPs": layer_markers,
	"Bldg Labels": layer_bldg_markers,
	"CAPs": layer_caps,
	"FEBA/FLOTs": layer_lines,
	"Polygons": layer_polygons,
	"Engagement Areas": layer_eas,
	"ROZs": layer_rozs
};
/**
 * Add default basemap, labels, and MGRS grid to map
 * Add Layer control to map
 */
basemap_firefly.addTo(map);
labels_imagery.addTo(map);
labels_aars.addTo(map);
labels_airspace.addTo(map);
labels_atcaas.addTo(map);
labels_kml.addTo(map);
labels_old_bmgr.addTo(map);
//mgrs_grids.addTo(map);
L.control.layers(basemap_layers, label_layers).addTo(map);

/**
 * Callback function allowing shapes created with Draw Control
 * to be added to their appropriate layers
 */
map.on(L.Draw.Event.CREATED, function (event) {
	var layer = event.layer;

	switch (event.layerType) {
		case "polyline":
			layer_lines.addLayer(layer);
			break;
		case "polygon":
			layer_polygons.addLayer(layer);
			break;
		case "rectangle":
			layer_eas.addLayer(layer);
			break;
		case "circle":
			layer_rozs.addLayer(layer);
			break;
	}
});

/**
 * Map listeners for various functions
 */
map.on("click", mapClick);
map.on("zoomend", resizeChits);
map.on("popupclose", stopListeningToChits);

/**
 * Adds a Building label at the given lat,long for the provided options
 */
function addBldgLabel(e) {
	var ll_posit = e.data.ll_posit;
	var lat_posit = Dms.parse(ll_posit.lat);
	var lng_posit = Dms.parse(ll_posit.lng);
	var ll = LatLon.parse(lat_posit, lng_posit);
	var mgrs = ll.toUtm().toMgrs();

	var label = $("#chit-description").val();
	$("#chit-description").val("");

	if (label != "") {
		var icon = L.divIcon({
			type: "div",
			html: "<div class=\"bldg-label-divicon-text\">" + label + "</div>",
			iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()],
			className: "bldg-label-divicon"
		});

		var marker_options = {
			type: "div",
			icon: icon,
			title: label,
			riseOnHover: true,
			latlng: ll_posit,
			mgrs: mgrs + "",
			elevation: "",
			data: null
		}

		var marker = L.marker(ll_posit, marker_options).addTo(layer_bldg_markers);

		// Attempt to get elevation data
		// https://nationalmap.gov/epqs/
		// https://www.usgs.gov/core-science-systems/ngp/3dep/about-3dep-products-services
		// Only works in USA, if lat/lng are outside of USA, then returns -1000000	
		$.get("https://nationalmap.gov/epqs/pqs.php?x=" + marker.options.latlng.lng + "&y=" + marker.options.latlng.lat + "&units=Feet&output=json", function (data) {
			var response = Math.round(data.USGS_Elevation_Point_Query_Service.Elevation_Query.Elevation);
			if (response != "-1000000") {
				marker.options.elevation = response + " ft";
			}

			marker.bindPopup(label + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");

			$(".bldg-label-divicon").css("font-size", (chit_scale * map.getZoom()) / 2);
			$(".bldg-label-divicon").css("line-height", ((chit_scale * map.getZoom())) + "px");

			marker.on("popupopen", bldgLabelClicked);
		}).fail(function () {
			marker.bindPopup(label + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");

			$(".bldg-label-divicon").css("font-size", (chit_scale * map.getZoom()) / 2);
			$(".bldg-label-divicon").css("line-height", ((chit_scale * map.getZoom())) + "px");

			marker.on("popupopen", bldgLabelClicked);
		});

		// Prevent making more than one chit at a time
		stopListeningToChits();
		map.closePopup();
	}
}

/**
 * Adds a CAP/Hold at the given lat,long for the provided options
 */
function addCap(e) {
	var ll_posit = e.data.ll_posit;
	var lat_posit = Dms.parse(ll_posit.lat);
	var lng_posit = Dms.parse(ll_posit.lng);
	var ll = LatLon.parse(lat_posit, lng_posit);
	var mgrs = ll.toUtm().toMgrs();

	// Show the options modal
	$("#cap-modal").modal("show");

	// Listen for the create CAP Button
	$("#btn-create-cap").click(function () {

		// Get user options
		var color = $("#cap-color").spectrum("get").toHexString();
		var label = $("#cap-label").val();
		var length = $("#cap-length").val();
		var angle = $("#cap-angle").val();

		if (label == "") {
			label = "CAP";
		}

		// Default length is 10NM (926m = 0.5NM)
		if (length == "") {
			length = 10 * 926;
		} else {
			length = length * 926;
		}

		var minor = length / 2;

		if (minor > minor_axis) {
			minor = minor_axis;
		}

		// Default angle is North (90deg from west)
		if (angle == "") {
			angle = 90;
		} else {
			angle = parseInt(angle) + 90;
		}

		var ellipse_options = {
			type: "CAP",
			title: label,
			latlng: ll_posit,
			color: color,
			fill: false,
			weight: 5,
			mgrs: mgrs + "",
		};

		var ellipse = L.ellipse(ll_posit, [length, minor], angle, ellipse_options).addTo(layer_caps);
		ellipse.bindPopup(label + "<br/>Center: " + mgrs + "<hr/><button class=\"btn btn-sm btn-info btn-cap-edit\">Edit</button><button class=\"btn btn-sm btn-danger btn-cap-del\">Delete</button>");
		ellipse.on("popupopen", capClicked);

		// Close the modal
		$("#cap-modal").modal("hide");

		// Reset the modal
		resetCapModal();

		// Stop listening
		stopListeningToModals();
	});

	// Prevent making more than one chit at a time
	stopListeningToChits();
	map.closePopup();
}

/**
 * Adds a chit at the given lat,long for the provided image source
 */
function addChit(img_src, ll_posit) {
	var name = $("#chit-description").val();
	$("#chit-description").val("");

	if (name == "") {
		// https://stackoverflow.com/questions/8807877/how-to-get-an-image-name-using-jquery
		name = img_src.replace(/^.*?([^\/]+)\..+?$/, "$1").toUpperCase();
	}

	var icon = L.icon({
		type: "img",
		iconUrl: "../" + img_src,
		iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()]
	});

	var lat_posit = Dms.parse(ll_posit.lat);
	var lon_posit = Dms.parse(ll_posit.lng);
	var ll = LatLon.parse(lat_posit, lon_posit);
	var mgrs = ll.toUtm().toMgrs();

	var marker_options = {
		id: marker_id,
		icon: icon,
		title: name,
		riseOnHover: true,
		latlng: ll_posit,
		mgrs: mgrs + "",
		elevation: "",
		data: null
	};

	marker_id++;

	switch (img_src) {
		case "chits/friendly/srv.svg":
			marker_options.type = "srv";
			break;
		case "chits/friendly/airborne.svg":
		case "chits/friendly/airborne-infantry.svg":
		case "chits/friendly/air-defense.svg":
		case "chits/friendly/anti-armor.svg":
		case "chits/friendly/armor.svg":
		case "chits/friendly/artillery.svg":
		case "chits/friendly/aviation.svg":
		case "chits/friendly/cbrne.svg":
		case "chits/friendly/engineer.svg":
		case "chits/friendly/horse-recce.svg":
		case "chits/friendly/infantry.svg":
		case "chits/friendly/maintenance.svg":
		case "chits/friendly/mech-infantry.svg":
		case "chits/friendly/medical.svg":
		case "chits/friendly/missile.svg":
		case "chits/friendly/recce.svg":
		case "chits/friendly/self-propelled-artillery.svg":
		case "chits/friendly/signals.svg":
		case "chits/friendly/supply.svg":
		case "chits/friendly/unit.svg":
			marker_options.type = "friendly";
			break;
		case "chits/ip/tgt.svg":
		case "chits/threats/ada.svg":
		case "chits/threats/missile.svg":
		case "chits/hostile/airborne.svg":
		case "chits/hostile/airborne-infantry.svg":
		case "chits/hostile/air-defense.svg":
		case "chits/hostile/anti-armor.svg":
		case "chits/hostile/armor.svg":
		case "chits/hostile/artillery.svg":
		case "chits/hostile/aviation.svg":
		case "chits/hostile/cbrne.svg":
		case "chits/hostile/engineer.svg":
		case "chits/hostile/horse-recce.svg":
		case "chits/hostile/infantry.svg":
		case "chits/hostile/maintenance.svg":
		case "chits/hostile/mech-infantry.svg":
		case "chits/hostile/medical.svg":
		case "chits/hostile/missile.svg":
		case "chits/hostile/recce.svg":
		case "chits/hostile/self-propelled-artillery.svg":
		case "chits/hostile/signals.svg":
		case "chits/hostile/supply.svg":
		case "chits/hostile/unit.svg":
			marker_options.type = "hostile";
			break;
		default:
			marker_options.type = "chit";
	}

	if (marker_options.type == "friendly") {
		var marker = L.marker(ll_posit, marker_options).addTo(layer_friendly_markers);
	} else if (marker_options.type == "hostile") {
		var marker = L.marker(ll_posit, marker_options).addTo(layer_hostile_markers);
	} else if (marker_options.type == "srv") {
		var marker = L.marker(ll_posit, marker_options).addTo(layer_survivor_markers);
	} else {
		// These are the IPs, no reason to really change it right now
		var marker = L.marker(ll_posit, marker_options).addTo(layer_markers);
	}

	// Attempt to get elevation data
	// https://nationalmap.gov/epqs/
	// https://www.usgs.gov/core-science-systems/ngp/3dep/about-3dep-products-services
	// Only works in USA, if lat/lng are outside of USA, then returns -1000000

	$.get("https://nationalmap.gov/epqs/pqs.php?x=" + marker.options.latlng.lng + "&y=" + marker.options.latlng.lat + "&units=Feet&output=json", function (data) {
		var response = Math.round(data.USGS_Elevation_Point_Query_Service.Elevation_Query.Elevation);
		if (response != "-1000000") {
			marker.options.elevation = response + " ft";
		}

		// Threats and Hostile chits - Add 9-Line
		if (marker.options.type == "hostile") {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
		}
		// SRV chit - Add 15-Line
		else if (marker.options.type == "srv") {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
		}
		// Other chits - Can't add data
		else {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
		}

		marker.on("popupopen", chitClicked);

	}).fail(function () {
		marker.options.elevation = "";

		// Threats and Hostile chits - Add 9-Line
		if (marker.options.type == "hostile") {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
		}
		// SRV chit - Add 15-Line
		else if (marker.options.type == "srv") {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
		}
		// Other chits - Can't add data
		else {
			marker.bindPopup(name + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
		}

		marker.on("popupopen", chitClicked);
	});

	// Add to the tables at the bottom
	var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.mgrs + "</td>");
	if (marker.options.type == "hostile") {
		$("#hostile-table").append(table_text);
	} else {
		$("#friendly-table").append(table_text);
	}

	// Prevent making more than one chit at a time
	stopListeningToChits();
	map.closePopup();
}

/**
 * Adds a threat ring composed of a marker chit and a ring of desired radius 
 */
function addThtRing(e) {
	var ll_posit = e.data.ll_posit;
	var lat_posit = Dms.parse(ll_posit.lat);
	var lng_posit = Dms.parse(ll_posit.lng);
	var ll = LatLon.parse(lat_posit, lng_posit);
	var mgrs = ll.toUtm().toMgrs();

	// Show the options modal
	$("#tht-ring-modal").modal("show");

	$("#msn-tht").change(function () {
		var threat = $("#msn-tht").val();
		var options = { label: threat, radius: "" };

		switch (threat) {
			case "SA-2B/F":
				options.radius = "18.4";
				break;
			case "SA-2D/E":
				options.radius = "23.2";
				break;
			case "SA-3":
				options.radius = "13";
				break;
			case "SA-5":
				options.radius = "162";
				break;
			case "SA-6":
				options.radius = "13.4";
				break;
			case "SA-8":
				options.radius = "5.5";
				break;
			case "SA-10A/B":
				options.radius = "40";
				break;
			case "SA-11":
				options.radius = "17";
				break;
			case "SA-12A":
				options.radius = "50";
				break;
			case "SA-12B":
				options.radius = "80";
				break;
			case "SA-15":
				options.radius = "6.5";
				break;
			case "SA-17":
				options.radius = "17";
				break;
			case "SA-19":
				options.radius = "6.5";
				break;
			case "SA-20":
				options.radius = "80";
				break;
			case "SA-21":
				options.radius = "64.8";
				break;
			case "SA-22":
				options.radius = "10.8";
				break;
			case "CROTALE/FM-80":
				options.radius = "15";
				break;
			case "I-HAWK":
				options.radius = "21.6";
				break;
			case "PATRIOT":
				options.radius = "86.4";
				break;
			case "RAPIER":
				options.radius = "3.8";
				break;
			case "ROLAND-II":
				options.radius = "4.3";
				break;
			case "SA-9":
				options.radius = "2.3";
				break;
			case "SA-13":
				options.radius = "2.7";
				break;
			default:
				options.label = "";
				break;
		}

		setThtRingOptions(options);
	});

	/**
	 * Listens to the select menu for when the user is creating a threat
	 * If the user changes the units, then update the placeholder
	 */
	$("#msn-tht-units").change(function () {
		var units = $("#msn-tht-units").val();

		if (units == "m") {
			$("#tht-ring-radius").prop("placeholder", "3000");
		} else {
			$("#tht-ring-radius").prop("placeholder", "3");
		}
	});

	// Listen for the create CAP Button
	$("#btn-create-tht-ring").click(function () {
		// Get user options
		var color = $("#tht-ring-color").spectrum("get").toHexString();
		var msn_tht = $("#msn-tht").val();
		var label = $("#tht-ring-label").val();
		var radius = $("#tht-ring-radius").val();
		var units = $("#msn-tht-units").val();
		var radius_label = "";

		// Set default radius
		if (!$.isNumeric(radius) || radius == "") {
			if (units == "m") {
				radius = 3000;
			} else {
				radius = 3;
			}
		}

		var soverignty = "";
		var img_name = "";

		if (color == "#ff0000") {
			soverignty = "HOS";
		} else if (color == "#ffff00") {
			soverignty = "SUS";
		} else if (color == "#ffffff") {
			soverignty = "UNK";
		} else if (color == "#00ff00") {
			soverignty = "FND";
		}

		if (msn_tht == "custom") {
			var icon = L.divIcon({
				type: "div",
				html: "<div class=\"threat-divicon-text threat-" + soverignty.toLowerCase() + "\">" + label + "</div>",
				iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()],
				className: "threat-divicon"
			});
		} else {
			img_name = msn_tht.replace("/", "").toLowerCase();
			img_name = "../chits/threats/" + img_name + "-" + soverignty.toLowerCase() + ".svg";

			var icon = L.icon({
				type: "img",
				iconUrl: img_name,
				iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()]
			});
		}

		// Default radius is 3NM (1852m = 1NM)
		if (units == "NM") {
			radius_label = radius;
			radius = radius * 1852;
		}
		// Metric units
		else {
			radius_label = radius;
			if (units == "km") {
				radius = radius * 1000;
			}
		}

		var circle_options = {
			radius: radius,
			color: color,
			fill: false,
			dashArray: tht_ring_dash_array,
			weight: 5
		};

		var circle = L.circle(ll_posit, circle_options).addTo(layer_threats);

		var marker_options = {
			id: marker_id,
			type: "threat",
			msnThreat: msn_tht,
			soverignty: soverignty,
			icon: icon,
			title: label,
			riseOnHover: true,
			ring: circle,
			radius: radius,
			units: units,
			latlng: ll_posit,
			mgrs: mgrs + "",
			data: null
		};

		marker_id++;

		var msn_tht_label = msn_tht;
		if (msn_tht_label == "custom") {
			msn_tht_label = "Custom";
		}

		var marker = L.marker(ll_posit, marker_options).addTo(layer_threat_markers);

		// Attempt to get elevation data
		// https://nationalmap.gov/epqs/
		// https://www.usgs.gov/core-science-systems/ngp/3dep/about-3dep-products-services
		// Only works in USA, if lat/lng are outside of USA, then returns -1000000
		$.get("https://nationalmap.gov/epqs/pqs.php?x=" + marker.options.latlng.lng + "&y=" + marker.options.latlng.lat + "&units=Feet&output=json", function (data) {
			var response = Math.round(data.USGS_Elevation_Point_Query_Service.Elevation_Query.Elevation);
			if (response != "-1000000") {
				marker.options.elevation = response + " ft";
			}

			marker.bindPopup(label + " (" + soverignty + ")<br/>Type: " + msn_tht_label + "<br/>Range: " + radius_label + " " + units + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");

			marker.on("click", thtClicked);

			if (msn_tht == "custom") {
				$(".threat-divicon").css("font-size", (tht_scale * map.getZoom()) / 2);
				$(".threat-divicon").css("line-height", ((tht_scale * map.getZoom())) + "px");
			}
		}).fail(function () {
			marker.options.elevation = "";

			marker.bindPopup(label + " (" + soverignty + ")<br/>Type: " + msn_tht_label + "<br/>Range: " + radius_label + " " + units + "<br/>" + mgrs + "<br/>" + marker.options.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");

			marker.on("click", thtClicked);

			if (msn_tht == "custom") {
				$(".threat-divicon").css("font-size", (tht_scale * map.getZoom()) / 2);
				$(".threat-divicon").css("line-height", ((tht_scale * map.getZoom())) + "px");
			}
		});

		// Add threat to the bottom table
		var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.soverignty + "</td><td>" + marker.options.msnThreat + "</td><td>" + marker.options.mgrs + "</td>");
		$("#threat-table").append(table_text);

		// Close the modal
		$("#tht-ring-modal").modal("hide");

		// Reset the modal form
		resetThtModal();

		// Stop listening
		stopListeningToModals();

	});

	// Prevent making more than one chit at a time
	stopListeningToChits();
	map.closePopup();
}

/**
 * Function for if a Bldg Label is clicked. Allows the object to be renamed or deleted.
 */
function bldgLabelClicked() {
	var tempMarker = this;

	$(".btn-chit-del").click(function () {
		layer_markers.removeLayer(tempMarker);
	});

	$(".btn-chit-rename").click(function () {
		var new_name = $(".chit-rename").val();

		if (new_name != "") {
			tempMarker._icon.title = new_name;
			tempMarker.options.title = new_name;

			var icon = L.divIcon({
				type: "div",
				html: "<div class=\"bldg-label-divicon-text\">" + new_name + "</div>",
				iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()],
				className: "bldg-label-divicon"
			});

			tempMarker.setIcon(icon);
			$(".bldg-label-divicon").css("font-size", (chit_scale * map.getZoom()) / 2);
			$(".bldg-label-divicon").css("line-height", ((chit_scale * map.getZoom())) + "px");

			tempMarker.closePopup();
			tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
		}
	});
}

/**
 * Function for if a CAP/Hold is clicked. Allows the object to be renamed or deleted
 */
function capClicked() {
	var tempCap = this;

	$(".btn-cap-edit").click(function () {

		$("#edit-cap-label").val(tempCap.options.title)
		$("#edit-cap-length").val(tempCap.getRadius().x / 926)
		$("#edit-cap-color").val(tempCap.options.color)
		$("#edit-cap-angle").val(tempCap.getTilt() - 90)

		$("#edit-cap-modal").modal("show");

		$("#btn-save-cap").click(function () {
			// Get Options
			var color = $("#edit-cap-color").spectrum("get").toHexString();
			var label = $("#edit-cap-label").val();
			var length = $("#edit-cap-length").val();
			var angle = $("#edit-cap-angle").val();

			if (label == "") {
				label = "CAP";
			}

			if (length == "") {
				length = 10 * 926;
			} else {
				length = length * 926;
			}

			var minor = length / 2;

			if (minor > minor_axis) {
				minor = minor_axis;
			}

			if (angle == "") {
				angle = 90;
			} else {
				angle = parseInt(angle) + 90;
			}

			var options = tempCap.options;

			options.title = label;
			options.color = color;

			layer_caps.removeLayer(tempCap);

			var ellipse = L.ellipse(tempCap.options.latlng, [length, minor], angle, options).addTo(layer_caps)

			ellipse.bindPopup(ellipse.options.title + "<br/>Center: " + ellipse.options.mgrs + "<hr/><button class=\"btn btn-sm btn-info btn-cap-edit\">Edit</button><button class=\"btn btn-sm btn-danger btn-cap-del\">Delete</button>");

			ellipse.on("popupopen", capClicked);

			$("#edit-cap-modal").modal("hide");
			stopListeningToModals();
			map.closePopup();
		})
	});

	$(".btn-cap-del").click(function () {
		layer_caps.removeLayer(tempCap);
	});

	$(".btn-cap-rename").click(function () {
		var new_name = $(".cap-rename").val();

		if (new_name != "") {
			var cap_ll = tempCap.getLatLng();
			var lat_posit = Dms.parse(cap_ll.lat);
			var lng_posit = Dms.parse(cap_ll.lng);
			var ll = LatLon.parse(lat_posit, lng_posit);
			var mgrs = ll.toUtm().toMgrs();

			tempCap.options.title = new_name;

			tempCap.closePopup();
			tempCap.setPopupContent(new_name + "<br/>Center: " + mgrs + "<hr/><input type=\"text\" class=\"form-control cap-rename\"><button class=\"btn btn-sm btn-warning btn-cap-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-cap-del\">Delete</button>");
		}
	});
}

/**
 * Function for if a chit is clicked. Allows the chit to be renamed or deleted
 */
function chitClicked() {
	var tempMarker = this;

	$(".btn-chit-del").click(function () {
		layer_markers.removeLayer(tempMarker);
		layer_friendly_markers.removeLayer(tempMarker);
		layer_hostile_markers.removeLayer(tempMarker);
		layer_survivor_markers.removeLayer(tempMarker);
		$("#marker-" + tempMarker.options.id).remove();
	});

	$(".btn-chit-rename").click(function () {
		var new_name = $(".chit-rename").val();

		if (new_name != "") {
			tempMarker._icon.title = new_name;
			tempMarker.options.title = new_name;

			tempMarker.closePopup();
			// Marker type is Threat or Hostile
			if (tempMarker.options.type == "threat" || tempMarker.options.type == "hostile") {
				if (tempMarker.options.data == null) {
					tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
				} else {
					tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
				}
			}
			// Marker type is srv
			else if (tempMarker.options.type == "srv") {
				if (tempMarker.options.data == null) {
					tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
				} else {
					tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
				}
			} else {
				tempMarker.setPopupContent(new_name + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
			}

			$("#marker-" + tempMarker.options.id + "-title").text(new_name);
		}
	});

	$(".btn-add-15-line").click(function () {
		// Show the 15-Line modal
		$("#add-15-line-modal").modal("show");
		$("#line-1a").val(tempMarker.options.title);
		$("#line-3a").val(tempMarker.options.mgrs);
		$("#line-3b").val(tempMarker.options.elevation);

		// Listen to the add 15-Line data button
		$("#btn-add-15-line-data").click(function () {
			var data = {
				type: "15-line",
				callsign_freq: $("#line-1a").val() + "/" + $("#line-1b").val(),
				num_objectives: $("#line-2").val(),
				mgrs: $("#line-3a").val(),
				elevation: $("#line-3b").val(),
				dtg: $("#line-3c").val(),
				source: $("#line-3d").val(),
				condition: $("#line-4").val(),
				equipment: $("#line-5a").val(),
				pls_hhrid: $("#line-5b").val(),
				authentication: $("#line-6").val(),
				threats: $("#line-7").val(),
				pz_description: $("#line-8").val(),
				osc: $("#line-9").val(),
				rv_freq: $("#line-10").val(),
				ip_ingress: $("#line-11").val(),
				rescort: $("#line-12").val(),
				obj_gp: $("#line-13").val(),
				signal: $("#line-14").val(),
				egress_rte: $("#line-15").val()
			};

			tempMarker.options.data = data;
			tempMarker.options.title = $("#line-1a").val();
			tempMarker._icon.title = $("#line-1a").val();
			$("#add-15-line-modal").modal("hide");
			tempMarker.closePopup();

			tempMarker.setPopupContent("Callsign/Freq: " + tempMarker.options.data.callsign_freq + "<br/>Number of Objectives: " + tempMarker.options.data.num_objectives + "<br/>Location: " + tempMarker.options.data.mgrs + "<br/>Elevation: " + tempMarker.options.data.elevation + "<br/>Date/Time(Z): " + tempMarker.options.data.dtg + "<br/>Source: " + tempMarker.options.data.source + "<br/>Condition: " + tempMarker.options.data.condition + "<br/>Equipment: " + tempMarker.options.data.equipment + "<br/>PLS/HHRID: " + tempMarker.options.data.pls_hhrid + "<br/>Authentication: " + tempMarker.options.data.authentication + "<br/>Threats: " + tempMarker.options.data.threats + "<br/>PZ Description: " + tempMarker.options.data.pz_description + "<br/>On Scene CC: " + tempMarker.options.data.osc + "<br/>RV/Freq: " + tempMarker.options.data.rv_freq + "<br/>IP/Ingress: " + tempMarker.options.data.ip_ingress + "<br/>Rescort: " + tempMarker.options.data.rescort + "<br/>Objective Area Gameplan: " + tempMarker.options.data.obj_gp + "<br/>Recovery Signal: " + tempMarker.options.data.signal + "<br/>Egress Route: " + tempMarker.options.data.egress_rte + "<hr/><button class=\"btn btn-sm btn-warning btn-del-15-line\">Delete 15-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");

			$("#marker-" + tempMarker.options.id + "-title").text(tempMarker.options.title);

			stopListeningToModals();
		});
	});

	$(".btn-add-9-line").click(function () {
		$("#add-9-line-modal").modal("show");
		$("#9-line-5").val(tempMarker.options.title);
		$("#9-line-6").val(tempMarker.options.mgrs);
		$("#9-line-4").val(tempMarker.options.elevation);

		var friendly_count = 0;
		layer_markers.eachLayer(function (marker) {
			if (marker.options.type == "friendly" || marker.options.type == "srv") {
				friendly_count++;
			}
		});

		if (friendly_count > 0) {
			var closest_friendly_distance = 999999999999;
			var closest_friendly_direction = 0;

			var t_ll = tempMarker.options.latlng;
			layer_markers.eachLayer(function (marker) {
				if (marker.options.type == "friendly" || marker.options.type == "srv") {
					var f_ll = marker.options.latlng;
					var distance = Math.round(map.distance(t_ll, f_ll));

					if (distance < closest_friendly_distance) {
						closest_friendly_distance = distance;
						closest_friendly_direction = Math.round((360 + L.GeometryUtil.bearing(t_ll, f_ll)) % 360);
					}
				}
			});

			if ((closest_friendly_direction > 337.5 && closest_friendly_direction <= 360) || (closest_friendly_direction > 0 && closest_friendly_direction <= 22.5)) {
				closest_friendly_direction = "N";
			} else if (closest_friendly_direction > 22.5 && closest_friendly_direction <= 67.5) {
				closest_friendly_direction = "NE";
			} else if (closest_friendly_direction > 67.5 && closest_friendly_direction <= 112.5) {
				closest_friendly_direction = "E";
			} else if (closest_friendly_direction > 112.5 && closest_friendly_direction <= 157.5) {
				closest_friendly_direction = "SE";
			} else if (closest_friendly_direction > 157.5 && closest_friendly_direction <= 202.5) {
				closest_friendly_direction = "S";
			} else if (closest_friendly_direction > 202.5 && closest_friendly_direction <= 247.5) {
				closest_friendly_direction = "SW";
			} else if (closest_friendly_direction > 247.5 && closest_friendly_direction <= 292.5) {
				closest_friendly_direction = "W";
			} else {
				closest_friendly_direction = "NW";
			}

			$("#9-line-8").val(closest_friendly_distance + "m " + closest_friendly_direction);
		}

		// Listen to the add 9-Line data button
		$("#btn-add-9-line-data").click(function () {
			var data = {
				type: "9-line",
				gfc_intent: $("#9-line-gfci").val(),
				type_control: $("#9-line-type-control").val(),
				ip_hdg_dist: $("#9-line-1-2-3").val(),
				elevation: $("#9-line-4").val(),
				description: $("#9-line-5").val(),
				location_data: $("#9-line-6").val(),
				mark: $("#9-line-7").val(),
				friendlies: $("#9-line-8").val(),
				egress: $("#9-line-9").val(),
				remarks_restrictions: $("#9-line-rr").val(),
				tot: $("#9-line-tot").val()
			};

			tempMarker.options.data = data;
			$("#add-9-line-modal").modal("hide");
			tempMarker.closePopup();

			tempMarker.setPopupContent("GFC Intent: " + data.gfc_intent + "<br/>Type/Control: " + data.type_control + "<br/>IP/Heading/Distance: " + data.ip_hdg_dist + "<br/>Elevation: " + data.elevation + "<br/>Description: " + data.description + "<br/>Location: " + data.location_data + "<br/>Mark: " + data.mark + "<br/>Friendlies: " + data.friendlies + "<br/>Egress: " + data.egress + "<br/>Remarks/Restrictions: " + data.remarks_restrictions + "<br/>TOT: " + data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
		});
	});

	$(".btn-del-9-line").click(function () {
		tempMarker.options.data = null;
		tempMarker.closePopup();

		tempMarker.bindPopup(tempMarker.options.title + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
	});

	$(".btn-del-15-line").click(function () {
		tempMarker.options.data = null;
		tempMarker.closePopup();

		tempMarker.setPopupContent(tempMarker.options.title + "<br/>" + tempMarker.options.mgrs + "<br/>" + tempMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
	});
}

/**
 * Clears ALL chits from the map
 */
function clearMap() {
	layer_markers.clearLayers();
	layer_threat_markers.clearLayers();
	layer_caps.clearLayers();
	layer_threats.clearLayers();
	layer_lines.clearLayers();
	layer_polygons.clearLayers();
	layer_eas.clearLayers();
	layer_rozs.clearLayers();

	for (var i = 1; i <= marker_id; i++) {
		$("#marker-" + i).remove();
	}

	marker_id = 1;
}

/**
 * Function for if an EA is clicked. Allows the object to be renamed or deleted.
 */
function eaClicked() {
	var tempEa = this;

	$(".btn-ea-del").click(function () {
		layer_eas.removeLayer(tempEa);
	});

	$(".btn-ea-rename").click(function () {
		var new_name = $(".ea-rename").val();

		if (new_name != "") {
			tempEa.options.title = new_name;
			tempEa.closePopup();
			tempEa.setPopupContent(new_name + "<hr/><input type=\"text\" class=\"form-control ea-rename\"><button class=\"btn btn-sm btn-warning btn-ea-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-ea-del\">Delete</button>");
		}
	});
}

/**
 * Function for if FEBA is clicked. Allows the object to be renamed or deleted.
 */
function febaClicked() {
	var tempFeba = this;

	$(".btn-feba-del").click(function () {
		layer_lines.removeLayer(tempFeba);
	});

	$(".btn-feba-rename").click(function () {
		var new_name = $(".feba-rename").val();

		if (new_name != "") {
			tempFeba.options.title = new_name;
			tempFeba.closePopup();
			tempFeba.setPopupContent(new_name + "<hr/><input type=\"text\" class=\"form-control feba-rename\"><button class=\"btn btn-sm btn-warning btn-feba-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-feba-del\">Delete</button>");
		}
	});
}

/**
 * Function for if a FLOT is clicked. Allows the object to be renamed or deleted
 */
function flotClicked() {
	var tempFlot = this;

	$(".btn-flot-del").click(function () {
		layer_lines.removeLayer(tempFlot);
	});

	$(".btn-flot-rename").click(function () {
		var new_name = $(".flot-rename").val();

		if (new_name != "") {
			tempFlot.options.title = new_name;
			tempFlot.closePopup();
			tempFlot.setPopupContent(new_name + "<hr/><input type=\"text\" class=\"form-control flot-rename\"><button class=\"btn btn-sm btn-warning btn-flot-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-flot-del\">Delete</button>");
		}
	});
}

/**
 *
 */
function flyToCoordinates() {
	var center_mgrs = $("#center-map").val().toUpperCase();

	// Remove all spaces
	center_mgrs = center_mgrs.replace(/ /g, "");

	// If the second character is a letter, add a leading 0
	var second_char = center_mgrs.substr(1, 1);
	if (!$.isNumeric(second_char)) {
		center_mgrs = "0" + center_mgrs;
	}

	// Break apart the sections
	var digits = center_mgrs.substr(5);
	var zone_digits = center_mgrs.substr(0, 5);
	var first_digits = digits.substr(0, digits.length / 2);
	var second_digits = digits.substr(digits.length / 2);

	// Add trailing 0s
	while (first_digits.length < 5) {
		first_digits = first_digits + "0";
	}

	while (second_digits.length < 5) {
		second_digits = second_digits + "0";
	}

	// Recombine sections
	center_mgrs = zone_digits + first_digits + second_digits

	// Parse MGRS, Translate to UTM, Translate to Lat Lon
	var mgrs_ref = Mgrs.parse(center_mgrs);
	var utm = mgrs_ref.toUtm();
	var ll_posit = mgrs_ref.toUtm().toLatLon();

	var latDM = Dms.toLat(ll_posit.lat, "dm", 4);
	var longDM = Dms.toLon(ll_posit.lon, "dm", 4);

	// Fly to Lat Lon and zoom in
	map.flyTo([ll_posit.lat, ll_posit.lon], 10);

	var fly_popup = L.popup()
		.setLatLng([ll_posit.lat, ll_posit.lon])
		.setContent(zone_digits.substr(0, 3) + " " + zone_digits.substr(3) + " " + first_digits + " " + second_digits + "<br/>" + ll_posit + "<br/>" + latDM + ", " + longDM)
		.openOn(map);

	// Listen if the user wants to add a CAP
	$(".chit-bldg-label").click({ ll_posit }, addBldgLabel);
	$(".chit-cap").click({ ll_posit }, addCap);
	$(".chit-tht-ring").click({ ll_posit }, addThtRing);
	$(".chit-small").click(function () {
		var img_src = $(this).attr("src");
		addChit(img_src, ll_posit);
	});
	$(".chit-srv").click(function () {
		var img_src = $(this).attr("src");
		addChit(img_src, ll_posit);
	});
}

/**
 *
 */
function hideTitles() {
	layer_threat_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});

	layer_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});

	layer_bldg_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});

	layer_friendly_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});

	layer_hostile_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});

	layer_survivor_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
	});
}

function loadKML() {
	fetch("./kml/Korean Airspace.kml")
		.then(res => res.text())
		.then(kmltext => {
			const parser = new DOMParser();
			const kml = parser.parseFromString(kmltext, 'text/xml');
			//const overlays = new L.KML(kml);
			const overlays = togeojson.kml(kml);
			console.log(overlays);
			L.geoJSON(overlays, {
				style: function (feature) {
					return {
						color: feature.properties["stroke"],
						weight: feature.properties["stroke-width"],
						stroke: true,
						fillOpacity: feature.properties["fill-opacity"],
						fillColor: feature.properties["fill"]
					}
				}
			}).addTo(labels_kml);
		});
}

/**
 *
 */
function loadScenario(input) {
	var input_json = JSON.parse(input);
	var details = input_json.details;
	var markers = input_json.markers;
	var bldg_markers = input_json.bldg_markers;
	var friendly_markers = input_json.friendly_markers;
	var hostile_markers = input_json.hostile_markers;
	var survivor_markers = input_json.survivor_markers;
	var threat_markers = input_json.threat_markers;
	var circles = input_json.circles;
	var ellipses = input_json.ellipses;
	var lines = input_json.lines;
	var polygons = input_json.polygons;
	var eas = input_json.eas;
	var rozs = input_json.rozs;

	// Reset the marker id
	marker_id = 1;

	var centered = false;

	if (details.scenario_version == null || details.scenario_version != "4") {
		alert("You loaded an old version of a Hawg Ops CAS Scenario. Some functions may no longer work as desired. Please verify your scenario and re-save. Your version: " + details.scenario_version + ". Current version: 4");
	}

	if (threat_markers == null) {
		markers.forEach(function (ref) {

			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}
			// Make the Marker
			// Marker is a threat
			if (ref.type == "threat") {
				// Make the icon
				// Preset threat, icon is image
				if (ref.icon.type == "img") {
					var icon = L.icon({
						type: ref.icon.type,
						iconUrl: ref.icon.iconUrl,
						iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()]
					});
				}
				// Custom threat, icon is divIcon
				else {
					var icon = L.divIcon({
						type: ref.icon.type,
						html: "<div class=\"threat-divicon-text threat-" + ref.soverignty.toLowerCase() + "\">" + ref.title + "</div>",
						iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()],
						className: "threat-divicon"
					});
				}

				// Make the ring
				var circle_options = {
					radius: ref.radius,
					color: ref.color,
					fill: false,
					dashArray: tht_ring_dash_array,
					weight: 5
				};

				var circle = L.circle(ref.latlng, circle_options).addTo(layer_threats);

				// If there was no ID in the reference, use the predetermined marker ID
				// Increment to the next marker ID.
				// This should work so that the marker ID is always 1 higher than the max marker ID
				if (ref.id === undefined) {
					var id = marker_id;
					marker_id++;
				} else {
					var id = ref.id;
					if (ref.id >= marker_id) {
						marker_id = ref.id + 1;
					} else {
						id = marker_id;
						marker_id++;
					}
				}

				var marker_options = {
					id: id,
					type: ref.type,
					msnThreat: ref.msnThreat,
					soverignty: ref.soverignty,
					icon: icon,
					title: ref.title,
					riseOnHover: true,
					ring: circle,
					radius: ref.radius,
					units: ref.units,
					latlng: ref.latlng,
					mgrs: ref.mgrs,
					data: ref.data,
					elevation: ref.elevation
				};

				var msn_label = ref.msnThreat;
				if (msn_label == "custom") {
					msn_label = "Custom";
				}

				var radius_label = ref.radius;
				if (ref.units == "NM") {
					radius_label = ref.radius / 1852;
				} else if (ref.units == "km") {
					radius_label = ref.radius / 1000;
				}

				var marker = L.marker(ref.latlng, marker_options).addTo(layer_threat_markers);

				if (marker.options.data == null) {
					marker.bindPopup(ref.title + " (" + ref.soverignty + ")<br/>Type: " + ref.msnThreat + "<br/>Range: " + radius_label + " " + ref.units + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
				} else {
					marker.bindPopup("GFC Intent: " + ref.data.gfc_intent + "<br/>Type/Control: " + ref.data.type_control + "<br/>IP/Heading/Distance: " + ref.data.ip_hdg_dist + "<br/>Elevation: " + ref.data.elevation + "<br/>Description: " + ref.data.description + "<br/>Location: " + ref.data.location_data + "<br/>Mark: " + ref.data.mark + "<br/>Friendlies: " + ref.data.friendlies + "<br/>Egress: " + ref.data.egress + "<br/>Remarks/Restrictions: " + ref.data.remarks_restrictions + "<br/>TOT: " + ref.data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button>");
				}

				marker.on("popupopen", thtClicked);

				if (ref.msnThreat == "custom") {
					$(".threat-divicon").css("font-size", (tht_scale * map.getZoom()) / 2);
					$(".threat-divicon").css("line-height", ((tht_scale * map.getZoom())) + "px");
				}

				// Add threat to the bottom table
				var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.soverignty + "</td><td>" + marker.options.msnThreat + "</td><td>" + marker.options.mgrs + "</td>");
				$("#threat-table").append(table_text);
			}
		});

	} else {
		threat_markers.forEach(function (ref) {

			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}

			// Make the icon
			// Preset threat, icon is image
			if (ref.icon.type == "img") {
				var icon = L.icon({
					type: ref.icon.type,
					iconUrl: ref.icon.iconUrl,
					iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()]
				});
			}
			// Custom threat, icon is divIcon
			else {
				var icon = L.divIcon({
					type: ref.icon.type,
					html: "<div class=\"threat-divicon-text threat-" + ref.soverignty.toLowerCase() + "\">" + ref.title + "</div>",
					iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()],
					className: "threat-divicon"
				});
			}

			// Make the ring
			var circle_options = {
				radius: ref.radius,
				color: ref.color,
				fill: false,
				dashArray: tht_ring_dash_array,
				weight: 5
			};

			var circle = L.circle(ref.latlng, circle_options).addTo(layer_threats);

			// If there was no ID in the reference, use the predetermined marker ID
			// Increment to the next marker ID.
			// This should work so that the marker ID is always 1 higher than the max marker ID
			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			var marker_options = {
				id: id,
				type: ref.type,
				msnThreat: ref.msnThreat,
				soverignty: ref.soverignty,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				ring: circle,
				radius: ref.radius,
				units: ref.units,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				data: ref.data,
				elevation: ref.elevation
			};

			var msn_label = ref.msnThreat;
			if (msn_label == "custom") {
				msn_label = "Custom";
			}

			var radius_label = ref.radius;
			if (ref.units == "NM") {
				radius_label = ref.radius / 1852;
			} else if (ref.units == "km") {
				radius_label = ref.radius / 1000;
			}

			var marker = L.marker(ref.latlng, marker_options).addTo(layer_threat_markers);

			if (marker.options.data == null) {
				marker.bindPopup(ref.title + " (" + ref.soverignty + ")<br/>Type: " + ref.msnThreat + "<br/>Range: " + radius_label + " " + ref.units + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
			} else {
				marker.bindPopup("GFC Intent: " + ref.data.gfc_intent + "<br/>Type/Control: " + ref.data.type_control + "<br/>IP/Heading/Distance: " + ref.data.ip_hdg_dist + "<br/>Elevation: " + ref.data.elevation + "<br/>Description: " + ref.data.description + "<br/>Location: " + ref.data.location_data + "<br/>Mark: " + ref.data.mark + "<br/>Friendlies: " + ref.data.friendlies + "<br/>Egress: " + ref.data.egress + "<br/>Remarks/Restrictions: " + ref.data.remarks_restrictions + "<br/>TOT: " + ref.data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button>");
			}

			marker.on("popupopen", thtClicked);

			if (ref.msnThreat == "custom") {
				$(".threat-divicon").css("font-size", (tht_scale * map.getZoom()) / 2);
				$(".threat-divicon").css("line-height", ((tht_scale * map.getZoom())) + "px");
			}

			// Add threat to the bottom table
			var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.soverignty + "</td><td>" + marker.options.msnThreat + "</td><td>" + marker.options.mgrs + "</td>");
			$("#threat-table").append(table_text);
		});
	}

	// IPs (or all if using an older save format)
	markers.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlng);
			centered = true;
		}

		// Make the Marker
		// Marker is a threat
		if (ref.type == "threat") {

		}
		// Marker is a chit
		else {
			// Make the Icon
			if (ref.type == "div") {
				var icon = L.divIcon({
					type: ref.icon.type,
					html: "<div class=\"bldg-label-divicon-text\">" + ref.title + "</div>",
					iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()],
					className: "bldg-label-divicon"
				});
			} else {
				var icon = L.icon({
					type: ref.icon.type,
					iconUrl: ref.icon.iconUrl,
					iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()]
				});
			}

			// If there was no ID in the reference, use the predetermined marker ID
			// Increment to the next marker ID.
			// This should work so that the marker ID is always 1 higher than the max marker ID
			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			// Make the marker
			var marker_options = {
				id: id,
				type: ref.type,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				elevation: ref.elevation,
				data: ref.data
			};

			if (ref.type == "friendly") {
				var marker = L.marker(ref.latlng, marker_options).addTo(layer_friendly_markers);
			} else if (ref.type == "hostile") {
				var marker = L.marker(ref.latlng, marker_options).addTo(layer_hostile_markers);
			} else if (ref.type == "srv") {
				var marker = L.marker(ref.latlng, marker_options).addTo(layer_survivor_markers);
			} else if (ref.type == "bldg_label") {
				var marker = L.marker(ref.latlng, marker_options).addTo(layer_bldg_markers);
			} else {
				var marker = L.marker(ref.latlng, marker_options).addTo(layer_markers);
			}

			if (marker.options.type == "hostile") {
				if (marker.options.data == null) {
					marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
				} else {
					marker.bindPopup("GFC Intent: " + ref.data.gfc_intent + "<br/>Type/Control: " + ref.data.type_control + "<br/>IP/Heading/Distance: " + ref.data.ip_hdg_dist + "<br/>Elevation: " + ref.data.elevation + "<br/>Description: " + ref.data.description + "<br/>Location: " + ref.data.location_data + "<br/>Mark: " + ref.data.mark + "<br/>Friendlies: " + ref.data.friendlies + "<br/>Egress: " + ref.data.egress + "<br/>Remarks/Restrictions: " + ref.data.remarks_restrictions + "<br/>TOT: " + ref.data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
				}
			} else if (marker.options.type == "srv") {
				if (marker.options.data == null) {
					marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
				} else {
					marker.bindPopup("Callsign/Freq: " + marker.options.data.callsign_freq + "<br/>Number of Objectives: " + marker.options.data.num_objectives + "<br/>Location: " + marker.options.data.mgrs + "<br/>Elevation: " + marker.options.data.elevation + "<br/>Date/Time(Z): " + marker.options.data.dtg + "<br/>Source: " + marker.options.data.source + "<br/>Condition: " + marker.options.data.condition + "<br/>Equipment: " + marker.options.data.equipment + "<br/>PLS/HHRID: " + marker.options.data.pls_hhrid + "<br/>Authentication: " + marker.options.data.authentication + "<br/>Threats: " + marker.options.data.threats + "<br/>PZ Description: " + marker.options.data.pz_description + "<br/>On Scene CC: " + marker.options.data.osc + "<br/>RV/Freq: " + marker.options.data.rv_freq + "<br/>IP/Ingress: " + marker.options.data.ip_ingress + "<br/>Rescort: " + marker.options.data.rescort + "<br/>Objective Area Gameplan: " + marker.options.data.obj_gp + "<br/>Recovery Signal: " + marker.options.data.signal + "<br/>Egress Route: " + marker.options.data.egress_rte + "<hr/><button class=\"btn btn-sm btn-warning btn-del-15-line\">Delete 15-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
				}
			} else {
				marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
			}

			marker.on("popupopen", chitClicked);

			if (ref.type == "div") {
				$(".bldg-label-divicon").css("font-size", (chit_scale * map.getZoom()) / 2);
				$(".bldg-label-divicon").css("line-height", (chit_scale * map.getZoom()) + "px");
			}

			// Add to the tables at the bottom
			var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.mgrs + "</td>");
			if (marker.options.type == "hostile") {
				$("#hostile-table").append(table_text);
			} else {
				$("#friendly-table").append(table_text);
			}
		}
	});

	if (bldg_markers != null) {
		bldg_markers.forEach(function (ref) {

			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}

			// Make the Icon
			var icon = L.divIcon({
				type: ref.icon.type,
				html: "<div class=\"bldg-label-divicon-text\">" + ref.title + "</div>",
				iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()],
				className: "bldg-label-divicon"
			});

			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			// Make the marker
			var marker_options = {
				id: id,
				type: ref.type,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				elevation: ref.elevation,
				data: ref.data
			};

			// Add marker to the layer
			var marker = L.marker(ref.latlng, marker_options).addTo(layer_bldg_markers);

			// Create the popup
			marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");

			// Bind listener
			marker.on("popupopen", chitClicked);

			// Do some CSS formatting
			$(".bldg-label-divicon").css("font-size", (chit_scale * map.getZoom()) / 2);
			$(".bldg-label-divicon").css("line-height", (chit_scale * map.getZoom()) + "px");

			// DO NOT add Building labels to the chit tables
		});
	}

	if (friendly_markers != null) {
		friendly_markers.forEach(function (ref) {
			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}

			// Make the Icon
			var icon = L.icon({
				type: ref.icon.type,
				iconUrl: ref.icon.iconUrl,
				iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()]
			});

			// Set the ID
			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			// Make the marker
			var marker_options = {
				id: id,
				type: ref.type,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				elevation: ref.elevation,
				data: ref.data
			};

			// Add to the layer
			var marker = L.marker(ref.latlng, marker_options).addTo(layer_friendly_markers);

			// Create the popup
			marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");

			// Bind the listener
			marker.on("popupopen", chitClicked);

			// Add to the friendly table
			var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.mgrs + "</td>");

			$("#friendly-table").append(table_text);
		});
	}

	if (hostile_markers != null) {
		hostile_markers.forEach(function (ref) {

			// Center the map
			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}

			// Make the Icon
			var icon = L.icon({
				type: ref.icon.type,
				iconUrl: ref.icon.iconUrl,
				iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()]
			});

			// Set the ID
			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			// Make the marker
			var marker_options = {
				id: id,
				type: ref.type,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				elevation: ref.elevation,
				data: ref.data
			};

			// Add to the layer
			var marker = L.marker(ref.latlng, marker_options).addTo(layer_hostile_markers);

			// Set the popup
			if (marker.options.data == null) {
				marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
			} else {
				marker.bindPopup("GFC Intent: " + ref.data.gfc_intent + "<br/>Type/Control: " + ref.data.type_control + "<br/>IP/Heading/Distance: " + ref.data.ip_hdg_dist + "<br/>Elevation: " + ref.data.elevation + "<br/>Description: " + ref.data.description + "<br/>Location: " + ref.data.location_data + "<br/>Mark: " + ref.data.mark + "<br/>Friendlies: " + ref.data.friendlies + "<br/>Egress: " + ref.data.egress + "<br/>Remarks/Restrictions: " + ref.data.remarks_restrictions + "<br/>TOT: " + ref.data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
			}

			// Bind the popup listener
			marker.on("popupopen", chitClicked);

			// Add to the hostile table
			var table_text = $("<tr id=\"marker-" + marker.options.id + "\"></tr>").html("<td id=\"marker-" + marker.options.id + "-title\">" + marker.options.title + "</td><td>" + marker.options.mgrs + "</td>");
			$("#hostile-table").append(table_text);

		});
	}

	if (survivor_markers != null) {
		survivor_markers.forEach(function (ref) {

			// Center the map
			if (!centered) {
				map.setZoom(10);
				map.panTo(ref.latlng);
				centered = true;
			}

			// Make the Icon
			var icon = L.icon({
				type: ref.icon.type,
				iconUrl: ref.icon.iconUrl,
				iconSize: [chit_scale * map.getZoom(), chit_scale * map.getZoom()]
			});

			// Set the ID
			if (ref.id === undefined) {
				var id = marker_id;
				marker_id++;
			} else {
				var id = ref.id;
				if (ref.id >= marker_id) {
					marker_id = ref.id + 1;
				} else {
					id = marker_id;
					marker_id++;
				}
			}

			// Make the marker
			var marker_options = {
				id: id,
				type: ref.type,
				icon: icon,
				title: ref.title,
				riseOnHover: true,
				latlng: ref.latlng,
				mgrs: ref.mgrs,
				elevation: ref.elevation,
				data: ref.data
			};

			var marker = L.marker(ref.latlng, marker_options).addTo(layer_survivor_markers);

			// Create the popup
			if (marker.options.data == null) {
				marker.bindPopup(ref.title + "<br/>" + ref.mgrs + "<br/>" + ref.elevation + "<hr/><input type=\"text\" class=\"form-control chit-rename\"><button class=\"btn btn-sm btn-warning btn-chit-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-15-line\">Add 15-Line</button>");
			} else {
				marker.bindPopup("Callsign/Freq: " + marker.options.data.callsign_freq + "<br/>Number of Objectives: " + marker.options.data.num_objectives + "<br/>Location: " + marker.options.data.mgrs + "<br/>Elevation: " + marker.options.data.elevation + "<br/>Date/Time(Z): " + marker.options.data.dtg + "<br/>Source: " + marker.options.data.source + "<br/>Condition: " + marker.options.data.condition + "<br/>Equipment: " + marker.options.data.equipment + "<br/>PLS/HHRID: " + marker.options.data.pls_hhrid + "<br/>Authentication: " + marker.options.data.authentication + "<br/>Threats: " + marker.options.data.threats + "<br/>PZ Description: " + marker.options.data.pz_description + "<br/>On Scene CC: " + marker.options.data.osc + "<br/>RV/Freq: " + marker.options.data.rv_freq + "<br/>IP/Ingress: " + marker.options.data.ip_ingress + "<br/>Rescort: " + marker.options.data.rescort + "<br/>Objective Area Gameplan: " + marker.options.data.obj_gp + "<br/>Recovery Signal: " + marker.options.data.signal + "<br/>Egress Route: " + marker.options.data.egress_rte + "<hr/><button class=\"btn btn-sm btn-warning btn-del-15-line\">Delete 15-Line</button><button class=\"btn btn-sm btn-danger btn-chit-del\">Delete</button>");
			}

			// Bind the popup listener
			marker.on("popupopen", chitClicked);

			// Add to the friendly table
			$("#friendly-table").append(table_text);
		});
	}

	ellipses.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlng);
			centered = true;
		}

		var ellipse_options = {
			type: ref.type,
			title: ref.title,
			latlng: ref.latlng,
			mgrs: ref.mgrs,
			radii: ref.radii,
			tilt: ref.tilt,
			color: ref.color,
			fill: false,
			weight: 5
		};

		var ellipse = L.ellipse(ref.latlng, [ref.radii.x, ref.radii.y], ref.tilt, ellipse_options).addTo(layer_caps);
		ellipse.bindPopup(ref.title + "<br/>Center: " + ref.mgrs + "<hr/><input type=\"text\" class=\"form-control cap-rename\"><button class=\"btn btn-sm btn-warning btn-cap-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-cap-del\">Delete</button>");
		ellipse.on("popupopen", capClicked);
	});

	lines.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlngs[0]);
			centered = true;
		}

		var line_options = {
			type: ref.type,
			title: ref.title,
			latlngs: ref.latlngs,
			stroke: true,
			color: ref.color,
			weight: 5,
			fill: false,
			clickable: false,
			dashArray: "20,10,5,10,5,10"
		};

		var line = L.polyline(ref.latlngs, line_options).addTo(layer_lines);

		if (ref.type == "flot") {
			line.bindPopup(ref.title + "<hr/><input type=\"text\" class=\"form-control flot-rename\"><button class=\"btn btn-sm btn-warning btn-flot-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-flot-del\">Delete</button>");
			line.on("popupopen", flotClicked);
		} else {
			line.bindPopup(ref.title + "<hr/><input type=\"text\" class=\"form-control feba-rename\"><button class=\"btn btn-sm btn-warning btn-feba-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-feba-del\">Delete</button>");
			line.on("popupopen", febaClicked);
		}
	})

	polygons.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlngs[0]);
			centered = true;
		}

		var polygon_options = {
			type: ref.type,
			title: ref.title,
			latlngs: ref.latlngs,
			clickable: false,
			color: "#3388ff",
			stroke: true,
			weight: 5,
			fill: false
		};

		var polygon = L.polygon(ref.latlngs, polygon_options).addTo(layer_polygons);
		polygon.bindPopup(ref.title + "<hr/><input type=\"text\" class=\"form-control polygon-rename\"><button class=\"btn btn-sm btn-warning btn-polygon-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-polygon-del\">Delete</button>");
		polygon.on("popupopen", polygonClicked);
	});

	eas.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlngs[0][0]);
			centered = true;
		}

		var ea_options = {
			type: ref.type,
			title: ref.title,
			latlngs: ref.latlngs,
			stroke: true,
			color: "#ff0000",
			weight: 5,
			fill: false,
			clickable: false
		};

		var rectangle = L.rectangle(ref.latlngs, ea_options).addTo(layer_eas);
		rectangle.bindPopup(ref.title + "<hr/><input type=\"text\" class=\"form-control ea-rename\"><button class=\"btn btn-sm btn-warning btn-ea-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-ea-del\">Delete</button>");
		rectangle.on("popupopen", eaClicked);
	});

	rozs.forEach(function (ref) {

		if (!centered) {
			map.setZoom(10);
			map.panTo(ref.latlng);
			centered = true;
		}

		var roz_options = {
			type: ref.type,
			title: ref.title,
			latlng: ref.latlng,
			mgrs: ref.mgrs,
			radius: ref.radius,
			color: "#ff0000",
			stroke: true,
			dashArray: "20,10,5,10",
			fillOpacity: 0.2,
			weight: 5,
			clickable: false
		};

		var circle = L.circle(ref.latlng, roz_options).addTo(layer_rozs);
		circle.bindPopup(ref.title + "<br/>Radius: " + ref.radius.toFixed(2) + " NM<br/>" + ref.mgrs + "<hr/><input type=\"text\" class=\"form-control roz-rename\"><button class=\"btn btn-sm btn-warning btn-roz-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-roz-del\">Delete</button>");
		circle.on("popupopen", rozClicked);
	});
}

/**
 *
 */
function loadScenarioHelper() {
	$("#load-modal").modal("show");

	$("#btn-add-scenario-to-map").click(function () {
		clearMap();

		var input_text = $("#scenario-input").val();

		if (input_text == "") {
			$("#load-modal").modal("hide");
		} else {
			loadScenario(input_text);
			$("#scenario-input").val("");
			$("#btn-add-scenario-to-map").off("click");
			$("#load-modal").modal("hide");
		}
	});
}

/**
 * Function for if the map is clicked.
 * Gets the lat,long of the mouse click and displays a popup of the location.
 * Listens for the various Chits/CAP/Threat buttons to add if clicked
 * Only does this if the ruler is NOT active
 */
function mapClick(e) {
	// Do nothing if ruler is active
	if ($(".leaflet-ruler").hasClass("leaflet-ruler-clicked") || $(".leaflet-draw-toolbar").children().hasClass("leaflet-draw-toolbar-button-enabled")) {

	} else {
		var ll_posit = e.latlng;
		var lat_posit = Dms.parse(ll_posit.lat);
		var lon_posit = Dms.parse(ll_posit.lng);
		var ll = LatLon.parse(lat_posit, lon_posit);
		var mgrs = ll.toUtm().toMgrs();

		var latDM = Dms.toLat(lat_posit, "dm", 4);
		var longDM = Dms.toLon(lon_posit, "dm", 4);

		var popup = L.popup()
			.setLatLng(ll_posit)
			.setContent(mgrs + "<br/>" + ll + "<br/>" + latDM + ", " + longDM)
			.openOn(map);

		$(".chit-bldg-label").click({ ll_posit }, addBldgLabel);
		$(".chit-cap").click({ ll_posit }, addCap);
		$(".chit-srv").click(function () {
			var img_src = $(this).attr("src");
			addChit(img_src, ll_posit);
		});
		$(".chit-tht-ring").click({ ll_posit }, addThtRing);
		$(".chit-small").click(function () {
			var img_src = $(this).attr("src");
			addChit(img_src, ll_posit);
		});
	}
}

/**
 * Function for if polygon is clicked. Allows the object to be renamed or deleted.
 */
function polygonClicked() {
	var tempPolygon = this;

	$(".btn-polygon-del").click(function () {
		layer_polygons.removeLayer(tempPolygon);
	});

	$(".btn-polygon-rename").click(function () {
		var new_name = $(".polygon-rename").val();

		if (new_name != "") {
			tempPolygon.options.title = new_name;
			tempPolygon.closePopup();
			tempPolygon.setPopupContent(new_name + "<hr/><input type=\"text\" class=\"form-control polygon-rename\"><button class=\"btn btn-sm btn-warning btn-polygon-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-polygon-del\">Delete</button>");
		}
	});
}

function printAirspaceKML() {
	var airspaceGeoJSON = labels_airspace.toGeoJSON();
	var KML = tokml(airspaceGeoJSON, {
		name: 'name',
		description: 'description',
		DocumentName: 'Hawg Ops Airspace',
		DocumentDescription: 'MOAs, Restricted Areas, and Warning Areas for use with CAS Planners.'
	});
	return KML;
}

/**
 * Resets the Add 9-Line modal
 */
function reset9lineModal() {
	$("#9-line-gfci").val("");
	$("#9-line-type-control").val("");
	$("#9-line-1-2-3").val("");
	$("#9-line-4").val("");
	$("#9-line-5").val("");
	$("#9-line-6").val("");
	$("#9-line-7").val("");
	$("#9-line-8").val("");
	$("#9-line-9").val("");
	$("#9-line-rr").val("");
	$("#9-line-tot").val("");
}

/**
 * Resets the Add 15-Line modal
 */
function reset15lineModal() {
	$("#line-1").val("");
	$("#line-2").val("");
	$("#line-3").val("");
	$("#line-4").val("");
	$("#line-5a").val("");
	$("#line-5b").val("");
	$("#line-6").val("");
	$("#line-7").val("");
	$("#line-8").val("");
	$("#line-9").val("");
	$("#line-10").val("");
	$("#line-11").val("");
	$("#line-12").val("");
	$("#line-13").val("");
	$("#line-14").val("");
	$("#line-15").val("");
}

/**
 * Resets the Create a CAP modal
 */
function resetCapModal() {
	$("#cap-label").val("");
	$("#cap-length").val("");
	$("#cap-angle").val("");
	$("#cap-color").spectrum("set", "#3388ff");
}

/**
 * Resets the Save modal
 */
function resetSaveModal() {
	$("#scenario-output").val("");
	if (!update) {
		$("#scenario-name").val("");
	}
	$("#btn-copy-to-clipboard").html("Copy To Clipboard");
	$("#btn-copy-to-clipboard").attr("disabled", false);
}

/**
 * Resets the Create a Threat modal
 */
function resetThtModal() {
	$("#msn-tht").val("custom");
	setThtRingOptions({ label: "", radius: "" });
	$("#tht-ring-color").spectrum("set", "#f00");
}

/**
 * Called on "zoomend"
 * Resizes markers according to their type
 */
function resizeChits() {
	// Threat markers (8, 2B/F, etc...)
	layer_threat_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();
		var map_zoom = map.getZoom();

		$(".threat-divicon").css("font-size", (tht_scale * map_zoom) / 2);
		$(".threat-divicon").css("line-height", ((tht_scale * map_zoom)) + "px");
		marker_icon.options.iconSize = [tht_scale * map_zoom, tht_scale * map_zoom];

		marker.setIcon(marker_icon);
	});

	// Building Markers
	layer_bldg_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();
		var map_zoom = map.getZoom();

		$(".bldg-label-divicon").css("font-size", (chit_scale * map_zoom) / 2);
		$(".bldg-label-divicon").css("line-height", ((chit_scale * map_zoom)) + "px");
		marker_icon.options.iconSize = [chit_scale * map_zoom, chit_scale * map_zoom];

		marker.setIcon(marker_icon);
	});

	// Friendly Chits
	layer_friendly_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();
		var map_zoom = map.getZoom();

		marker.options.iconSize = [chit_scale * map_zoom, chit_scale * map_zoom];
		marker.setIcon(marker_icon);
	});

	// Hostile Chits
	layer_hostile_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();
		var map_zoom = map.getZoom();

		marker_icon.options.iconSize = [chit_scale * map_zoom, chit_scale * map_zoom];
		marker.setIcon(marker_icon);
	});

	// Survivor chits
	layer_survivor_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();
		var map_zoom = map.getZoom();

		marker_icon.options.iconSize = [chit_scale * map_zoom, chit_scale * map_zoom];
		marker.setIcon(marker_icon);
	});
}

/**
 * Function for if a ROZ is clicked. Allows the object to be renamed or deleted
 */
function rozClicked() {
	var tempRoz = this;

	$(".btn-roz-del").click(function () {
		layer_rozs.removeLayer(tempRoz);
	});

	$(".btn-roz-rename").click(function () {
		var new_name = $(".roz-rename").val();

		if (new_name != "") {
			var radius = tempRoz.getRadius() / 1852;
			var ll_posit = tempRoz.getLatLng();
			var lat = Dms.parse(ll_posit.lat);
			var lng = Dms.parse(ll_posit.lng);
			var ll = LatLon.parse(lat, lng);
			var mgrs = ll.toUtm().toMgrs();

			tempRoz.options.title = new_name;

			tempRoz.closePopup();
			tempRoz.setPopupContent(new_name + "<br/>Radius: " + radius.toFixed(2) + " NM<br/>" + mgrs + "<hr/><input type=\"text\" class=\"form-control roz-rename\"><button class=\"btn btn-sm btn-warning btn-roz-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-roz-del\">Delete</button>");
		}
	});
}

/**
 *
 */
function saveScenario() {
	var today = new Date().toUTCString();
	var scenario_details = {
		classification: "UNCLASSIFIED",
		date: today,
		scenario_version: "4"
	};

	// IPs
	var scenario_markers = [];
	var scenario_bldg_markers = [];
	var scenario_friendly_markers = [];
	var scenario_hostile_markers = [];
	var scenario_survivor_markers = [];
	var scenario_threat_markers = [];
	var scenario_circles = [];
	var scenario_ellipses = [];
	var scenario_lines = [];
	var scenario_polygons = [];
	var scenario_eas = [];
	var scenario_rozs = [];

	layer_threat_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			msnThreat: marker.options.msnThreat,
			soverignty: marker.options.soverignty,
			color: marker.options.ring.options.color,
			radius: marker.options.radius,
			units: marker.options.units,
			icon: icon,
			data: marker.options.data
		};

		scenario_threat_markers.push(marker_ref);

	});

	layer_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		// Make the marker reference
		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			icon: icon,
			data: marker.options.data
		};

		scenario_markers.push(marker_ref);
	});

	layer_bldg_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		// Make the marker reference
		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			icon: icon,
			data: marker.options.data
		};

		scenario_bldg_markers.push(marker_ref);
	});

	layer_friendly_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		// Make the marker reference
		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			icon: icon,
			data: marker.options.data
		};

		scenario_friendly_markers.push(marker_ref);
	});

	layer_hostile_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		// Make the marker reference
		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			icon: icon,
			data: marker.options.data
		};

		scenario_hostile_markers.push(marker_ref);
	});

	layer_survivor_markers.eachLayer(function (marker) {
		var marker_icon = marker.getIcon();

		// Make the icon reference
		if (marker_icon.options.type == "img") {
			var icon = {
				type: marker_icon.options.type,
				iconUrl: marker_icon.options.iconUrl
				// iconSize default
			};
		} else {
			var icon = {
				type: marker_icon.options.type
				// html uses label
				// iconSize default
				// className default
			};
		}

		// Make the marker reference
		var marker_ref = {
			id: marker.options.id,
			type: marker.options.type,
			title: marker.options.title,
			latlng: marker.getLatLng(),
			mgrs: marker.options.mgrs,
			elevation: marker.options.elevation,
			icon: icon,
			data: marker.options.data
		};

		scenario_survivor_markers.push(marker_ref);
	});

	layer_caps.eachLayer(function (ellipse) {
		var ellipse_ref = {
			type: ellipse.options.type,
			title: ellipse.options.title,
			latlng: ellipse.options.latlng,
			mgrs: ellipse.options.mgrs,
			radii: ellipse.getRadius(),
			tilt: ellipse.getTilt(),
			color: ellipse.options.color
			// fill: false
			// weight: 5
		};
		scenario_ellipses.push(ellipse_ref);
	});

	layer_lines.eachLayer(function (line) {
		var line_ref = {
			type: line.options.type,
			title: line.options.title,
			latlngs: line.options.latlngs,
			color: line.options.color
			// stroke: true
			// color: 000000
			// weight: 5
			// fill: false
			// clickable: false
			// dashArray: 20,10,5,10,5,10
		};
		scenario_lines.push(line_ref);
	});

	layer_polygons.eachLayer(function (polygon) {
		var polygon_ref = {
			type: polygon.options.type,
			title: polygon.options.title,
			latlngs: polygon.options.latlngs
			// stroke: true
			// color: 3388ff
			// weight: 5
			// clickable: false
			// fill: false
		};
		scenario_polygons.push(polygon_ref);
	});

	layer_eas.eachLayer(function (ea) {
		var ea_ref = {
			type: ea.options.type,
			title: ea.options.title,
			latlngs: ea.options.latlngs
			// stroke: true
			// color: ff0000
			// weight: 5
			// fill: false
			// clickable: false
		};
		scenario_eas.push(ea_ref);
	});

	layer_rozs.eachLayer(function (roz) {
		var roz_ref = {
			type: roz.options.type,
			title: roz.options.title,
			latlng: roz.options.latlng,
			mgrs: roz.options.mgrs,
			radius: roz.options.radius,
			//color: roz.options.color
			// stroke: true
			// color: ff0000
			// dash array: 20,10,5,10
			// weight 5
			// fill true
			// fill opacity: 0.2
		};
		scenario_rozs.push(roz_ref);
	});

	var scenario = {
		details: scenario_details,
		threat_markers: scenario_threat_markers,
		markers: scenario_markers,
		bldg_markers: scenario_bldg_markers,
		friendly_markers: scenario_friendly_markers,
		hostile_markers: scenario_hostile_markers,
		survivor_markers: scenario_survivor_markers,
		ellipses: scenario_ellipses,
		lines: scenario_lines,
		polygons: scenario_polygons,
		eas: scenario_eas,
		rozs: scenario_rozs
	};

	scenario = JSON.stringify(scenario);

	$("#scenario-output").val(scenario);
	$("#save-modal").modal("show");

	$("#btn-copy-to-clipboard").click(function () {
		var output_text = $("#scenario-output");
		output_text.select();
		document.execCommand("copy");
		$("#btn-copy-to-clipboard").html("Copied!");
		$("#btn-copy-to-clipboard").attr("disabled", true);
	});

	$("#btn-save-to-account").click(function () {
		var name = $("#scenario-name").val();

		$.ajax({
			url: "/do/save-scenario-to-account-do.php",
			method: "POST",
			data: {
				"name": name,
				"data": $("#scenario-output").val()
			},
			success: function (data, textStatus, jqXHR) {
				var responseText = "";
				var responseLevel = "";
				if (data == "30102") {
					responseLevel = "success";
					responseText = "Scenario " + name + " saved to your account.";
				} else {
					responseLevel = "danger";
					responseText = "There was an error saving the scenario to your account. (" + data + ")";
				}

				$("#alert-container").html("<div class=\"alert alert-" + responseLevel + " alert-dismissible fade show\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label\"Close\"><i class=\"fa fa-times\"></i></button>" + responseText + "</div>");

				$("#save-modal").modal("hide");
			}
		});

		$("#btn-save-to-account").off("click");
	});

	$("#btn-update-scenario").click(function () {
		var name = $("#scenario-name").val();

		$.ajax({
			url: "/do/update-scenario-do.php",
			method: "POST",
			data: {
				"id": $("#scenario-id").val(),
				"name": name,
				"data": $("#scenario-output").val()
			},
			success: function (data, textStatus, jqXHR) {
				var responseText = "";
				var responseLevel = "";
				if (data == "30110") {
					responseLevel = "success";
					responseText = "Scenario " + name + " updated in your account.";
				} else {
					responseLevel = "danger";
					responseText = "There was an error updating the scenario in your account. (" + data + ")";
				}

				$("#alert-container").html("<div class=\"alert alert-" + responseLevel + " alert-dismissible fade show\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label\"Close\"><i class=\"fa fa-times\"></i></button>" + responseText + "</div>");

				$("#save-modal").modal("hide");
			}
		});
	});
}

/**
 * Function to set threat ring options depending on what the user clicks on the modal
 * Automatically sets the Label and radius for preset threats
 * Enables/Disables threat radius input depending if custom threat or preset
 */
function setThtRingOptions(options) {
	if (options.label == "") {
		$("#tht-ring-radius").prop("disabled", false);
		$("#msn-tht-units").prop("disabled", false);
		$("#tht-ring-radius").prop("placeholder", "3");
	} else {
		$("#tht-ring-radius").prop("disabled", true);
		$("#msn-tht-units").prop("disabled", true);
	}

	$("#msn-tht-units").val("NM");
	$("#tht-ring-label").val(options.label);
	$("#tht-ring-radius").val(options.radius);
}

/**
 *
 */
function showTitles() {
	layer_threat_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});

	layer_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});

	layer_bldg_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});

	layer_friendly_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});

	layer_hostile_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});

	layer_survivor_markers.eachLayer(function (marker) {
		marker.unbindTooltip();
		marker.bindTooltip(marker.options.title, { permanent: true, opacity: 1.0 }).openTooltip();
	});
}

/**
 * Called on "popupclose"
 * Also called by other functions
 * Stops listening to all chit buttons. This serves to not let more
 * than one chit be created per click of the map
 */
function stopListeningToChits() {
	$(".chit-bldg-label").off("click");
	$(".chit-cap").off("click");
	$(".chit-tht-ring").off("click");
	$(".chit-small").off("click");
	$(".chit-srv").off("click");
}

/**
 * Called when modals are opened.
 * Stops listening to previous modals.
 * This is a failsafe if a user opens a modal and clicks out of it.
 * Then opens the modal again and creates a CAP/Threat ring it could potentially create erroneous objects
 */
function stopListeningToModals() {
	$("#btn-create-tht-ring").off("click");
	$("#btn-create-cap").off("click");
	$("#btn-add-15-line-data").off("click");
	$("#btn-add-9-line-data").off("click");
}

/**
 * Function for if a threat ring marker is clicked. Allows the object to be renamed or deleted
 * TODO: Potential to reset the soverignty (color) as well
 */
function thtClicked() {
	var thtMarker = this;
	var thtRing = this.options.ring;

	$(".btn-tht-del").click(function () {
		layer_threats.removeLayer(thtRing);
		layer_threat_markers.removeLayer(thtMarker);

		$("#marker-" + thtMarker.options.id).remove();
	});

	$(".btn-tht-rename").click(function () {

		var new_name = $(".tht-rename").val();

		if (new_name != "") {

			if (thtMarker.options.msnThreat == "custom") {
				var icon = L.divIcon({
					type: "threat",
					html: "<div class=\"threat-divicon-text threat-" + thtMarker.options.soverignty.toLowerCase() + "\">" + new_name + "</div>",
					iconSize: [tht_scale * map.getZoom(), tht_scale * map.getZoom()],
					className: "threat-divicon"
				});

				thtMarker.setIcon(icon);
				$(".threat-divicon").css("font-size", (tht_scale * map.getZoom()) / 2);
				$(".threat-divicon").css("line-height", ((tht_scale * map.getZoom())) + "px");
			}

			var og_content = thtMarker.getPopup().getContent();
			var content_array = og_content.split("(");
			thtMarker._icon.title = new_name;
			thtMarker.options.title = new_name;

			thtMarker.closePopup();

			// Default radius is 3NM (1852m = 1NM)
			if (thtMarker.options.units == "NM") {
				var radius_label = thtMarker.options.radius / 1852;
			}
			// Metric units
			else {
				var radius_label = thtMarker.options.radius;
				if (units == "km") {
					radius_label = radius_label / 1000;
				}
			}

			thtMarker.setPopupContent(new_name + " (" + thtMarker.options.soverignty + ")<br/>Type: " + thtMarker.options.msnThreat + "<br/>Range: " + radius_label + " " + thtMarker.options.units + "<br/>" + thtMarker.options.mgrs + "<br/>" + thtMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");

			$("#marker-" + thtMarker.options.id + "-title").text(new_name);
		}
	});

	$(".btn-add-9-line").click(function () {
		$("#add-9-line-modal").modal("show");
		$("#9-line-5").val(thtMarker.options.msnThreat);
		$("#9-line-6").val(thtMarker.options.mgrs);
		$("#9-line-4").val(thtMarker.options.elevation);

		var friendly_count = 0;
		layer_markers.eachLayer(function (marker) {
			if (marker.options.type == "friendly" || marker.options.type == "srv") {
				friendly_count++;
			}
		});

		if (friendly_count > 0) {
			var closest_friendly_distance = 999999999999;
			var closest_friendly_direction = 0;

			// Get distance/direction to closest friendly
			var t_ll = thtMarker.options.latlng;
			layer_markers.eachLayer(function (marker) {
				if (marker.options.type == "friendly" || marker.options.type == "srv") {
					var f_ll = marker.options.latlng;
					var distance = Math.round(map.distance(t_ll, f_ll));

					if (distance < closest_friendly_distance) {
						closest_friendly_distance = distance;
						closest_friendly_direction = Math.round((360 + L.GeometryUtil.bearing(t_ll, f_ll)) % 360);
					}
				}
			});

			if ((closest_friendly_direction > 337.5 && closest_friendly_direction <= 360) || (closest_friendly_direction > 0 && closest_friendly_direction <= 22.5)) {
				closest_friendly_direction = "N";
			} else if (closest_friendly_direction > 22.5 && closest_friendly_direction <= 67.5) {
				closest_friendly_direction = "NE";
			} else if (closest_friendly_direction > 67.5 && closest_friendly_direction <= 112.5) {
				closest_friendly_direction = "E";
			} else if (closest_friendly_direction > 112.5 && closest_friendly_direction <= 157.5) {
				closest_friendly_direction = "SE";
			} else if (closest_friendly_direction > 157.5 && closest_friendly_direction <= 202.5) {
				closest_friendly_direction = "S";
			} else if (closest_friendly_direction > 202.5 && closest_friendly_direction <= 247.5) {
				closest_friendly_direction = "SW";
			} else if (closest_friendly_direction > 247.5 && closest_friendly_direction <= 292.5) {
				closest_friendly_direction = "W";
			} else {
				closest_friendly_direction = "NW";
			}

			$("#9-line-8").val(closest_friendly_distance + "m " + closest_friendly_direction);
		}

		// Listen to the add 9-Line data button
		$("#btn-add-9-line-data").click(function () {
			var data = {
				type: "9-line",
				gfc_intent: $("#9-line-gfci").val(),
				type_control: $("#9-line-type-control").val(),
				ip_hdg_dist: $("#9-line-1-2-3").val(),
				elevation: $("#9-line-4").val(),
				description: $("#9-line-5").val(),
				location_data: $("#9-line-6").val(),
				mark: $("#9-line-7").val(),
				friendlies: $("#9-line-8").val(),
				egress: $("#9-line-9").val(),
				remarks_restrictions: $("#9-line-rr").val(),
				tot: $("#9-line-tot").val()
			};

			thtMarker.options.data = data;
			$("#add-9-line-modal").modal("hide");
			thtMarker.closePopup();

			thtMarker.setPopupContent("GFC Intent: " + data.gfc_intent + "<br/>Type/Control: " + data.type_control + "<br/>IP/Heading/Distance: " + data.ip_hdg_dist + "<br/>Elevation: " + data.elevation + "<br/>Description: " + data.description + "<br/>Location: " + data.location_data + "<br/>Mark: " + data.mark + "<br/>Friendlies: " + data.friendlies + "<br/>Egress: " + data.egress + "<br/>Remarks/Restrictions: " + data.remarks_restrictions + "<br/>TOT: " + data.tot + "<hr/><button class=\"btn btn-sm btn-warning btn-del-9-line\">Delete 9-Line</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button>");
		});
	});

	$(".btn-del-9-line").click(function () {
		thtMarker.options.data = null;
		thtMarker.closePopup();

		// Default radius is 3NM (1852m = 1NM)
		if (thtMarker.options.units == "NM") {
			var radius_label = thtMarker.options.radius / 1852;
		}
		// Metric units
		else {
			var radius_label = thtMarker.options.radius;
			if (units == "km") {
				radius_label = radius_label / 1000;
			}
		}

		thtMarker.bindPopup(thtMarker.options.title + " (" + thtMarker.options.soverignty + ")<br/>Type: " + thtMarker.options.msnThreat + "<br/>Range: " + radius_label + " " + thtMarker.options.units + "<br/>" + thtMarker.options.mgrs + "<br/>" + thtMarker.options.elevation + "<hr/><input type=\"text\" class=\"form-control tht-rename\"><button class=\"btn btn-sm btn-warning btn-tht-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-tht-del\">Delete</button><button class=\"btn btn-sm btn-block btn-info btn-add-9-line\">Add 9-Line</button>");
	});
}

/**
 * Various listeners that run all the time
 */
$(document).ready(function () {

	// Initialize CAP Modal color picker
	$("#cap-color").spectrum({
		preferredFormat: "name",
		color: "#3388ff",
		showPalette: true,
		showPaletteOnly: true,
		hideAfterPaletteSelect: true,
		palette: ["#3388ff", "#ff0000", "#ff9000", "#ffff00", "#00ff00", "#00ffff", "#0000ff", "#9000ff", "#ff00ff"]
	});

	$("#edit-cap-color").spectrum({
		preferredFormat: "name",
		color: "#3388ff",
		showPalette: true,
		showPaletteOnly: true,
		hideAfterPaletteSelect: true,
		palette: ["#3388ff", "#ff0000", "#ff9000", "#ffff00", "#00ff00", "#00ffff", "#0000ff", "#9000ff", "#ff00ff"]
	});

	// Initialize THT Ring color picker
	$("#tht-ring-color").spectrum({
		preferredFormat: "name",
		color: "#ff0000",
		showPalette: true,
		showPaletteOnly: true,
		hideAfterPaletteSelect: true,
		palette: ["#ff0000", "#ffff00", "#ffffff", "#00ff00"]
	});

	// Listen to buttons
	$("#fly-button").click(flyToCoordinates);
	$("#clear-chits").click(clearMap);
	$("#btn-save-scenario").click(saveScenario);
	$("#btn-load-scenario").click(loadScenarioHelper);
	$("#btn-show-titles").click(showTitles);
	$("#btn-hide-titles").click(hideTitles);

	// Stop listening to modals when you create a modal. This prevents erroneous addition of objects
	$("#tht-ring-modal").on("show.bs.modal", stopListeningToModals);
	$("#cap-modal").on("show.bs.modal", stopListeningToModals);
	$("#add-15-line-modal").on("show.bs.modal", stopListeningToModals);
	$("#add-9-line-modal").on("show.bs.modal", stopListeningToModals);

	// Resets form values when modals are closed without CAP/Threat being added
	$("#save-modal").on("hidden.bs.modal", resetSaveModal);
	$("#cap-modal").on("hidden.bs.modal", resetCapModal);
	$("#tht-ring-modal").on("hidden.bs.modal", resetThtModal);
	$("#add-15-line-modal").on("hidden.bs.modal", reset15lineModal);
	$("#add-9-line-modal").on("hidden.bs.modal", reset9lineModal);

	// Close alert if user clicks the body
	$("body").click(function () {
		$(".alert").alert("close");
	});
});

export { loadScenario, loadKML, printAirspaceKML };