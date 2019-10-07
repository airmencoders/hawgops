//Import statements
import Mgrs, { LatLon } from "../../../../geodesy/mgrs.js";
import Dms from "../../../../geodesy/dms.js";

/**
 * @class L.Draw.Circle
 * @aka Draw.Circle
 * @inherits L.Draw.SimpleShape
 */
L.Draw.Circle = L.Draw.SimpleShape.extend({
	statics: {
		TYPE: 'circle'
	},
	options: {
		shapeOptions: {
			stroke: true,
			color: "#ff0000",
			dashArray: "20,10,5,10",
			weight: 5,
			fill: true,
			fillColor: null, //same as color by default
			fillOpacity: 0.2,
			clickable: false
		},
		showRadius: true,
		metric: false, // Whether to use the metric measurement system or imperial
		feet: false, // When not metric, use feet instead of yards for display
		nautic: true // When not metric, not feet use nautic mile for display
	},

	// @method initialize(): void
	initialize: function (map, options) {
		// Save the type so super can fire, need to do this as cannot do this.TYPE :(
		this.type = L.Draw.Circle.TYPE;

		this._initialLabelText = L.drawLocal.draw.handlers.circle.tooltip.start;

		L.Draw.SimpleShape.prototype.initialize.call(this, map, options);
	},

	_drawShape: function (latlng) {
		// Calculate the distance based on the version
		if (L.GeometryUtil.isVersion07x()) {
			var distance = this._startLatLng.distanceTo(latlng);
		} else {
			var distance = this._map.distance(this._startLatLng, latlng);
		}

		if (!this._shape) {
			this._shape = new L.Circle(this._startLatLng, distance, this.options.shapeOptions);
			this._map.addLayer(this._shape);
		} else {
			this._shape.setRadius(distance);
		}
	},

	_fireCreatedEvent: function () {
		
		
		var lat = Dms.parse(this._startLatLng.lat);
		var lng = Dms.parse(this._startLatLng.lng);
		var ll = LatLon.parse(lat, lng);
		var mgrs = ll.toUtm().toMgrs();
		
		var label = $("#chit-description").val();
		$("#chit-description").val("");
		if(label == "") {
			label = "ROZ";
		}
		
		this.options.shapeOptions.type = "roz";
		this.options.shapeOptions.latlng = this._startLatLng;
		this.options.shapeOptions.mgrs = mgrs + "";
		this.options.shapeOptions.title = label;
		
		var circle = new L.Circle(this._startLatLng, this._shape.getRadius(), this.options.shapeOptions);
		
		var radius = this._shape.getRadius()/1852;
		circle.bindPopup(label + "<br/>Radius: " + radius.toFixed(2) + "NM<br/>" + mgrs + "<hr/><input type=\"text\" class=\"form-control roz-rename\" style=\"margin-bottom: 5px;\"><button class=\"btn btn-sm btn-warning btn-roz-rename\" style=\"margin-right: 5px;\">Rename</button><button class=\"btn btn-sm btn-danger btn-roz-del\">Delete</button>");
		
		circle.on("click", rozClicked);
		
		L.Draw.SimpleShape.prototype._fireCreatedEvent.call(this, circle);
	},

	_onMouseMove: function (e) {
		var latlng = e.latlng,
			showRadius = this.options.showRadius,
			useMetric = this.options.metric,
			radius;

		this._tooltip.updatePosition(latlng);
		if (this._isDrawing) {
			this._drawShape(latlng);

			// Get the new radius (rounded to 1 dp)
			radius = this._shape.getRadius().toFixed(1);

			var subtext = '';
			if (showRadius) {
				subtext = L.drawLocal.draw.handlers.circle.radius + ': ' +
					L.GeometryUtil.readableDistance(radius, useMetric, this.options.feet, this.options.nautic);
			}
			this._tooltip.updateContent({
				text: this._endLabelText,
				subtext: subtext
			});
		}
	}
});

function rozClicked() {
	var tempRoz = this;
	
	$(".btn-roz-del").click(function() {
		layer_rozs.removeLayer(tempRoz);
	});
	
	$(".btn-roz-rename").click(function() {
		var new_name = $(".roz-rename").val();
		
		if(new_name != "") {
			var radius = tempRoz.getRadius()/1852;
			var ll_posit = tempRoz.getLatLng();
			var lat = Dms.parse(ll_posit.lat);
			var lng = Dms.parse(ll_posit.lng);
			var ll = LatLon.parse(lat, lng);
			var mgrs = ll.toUtm().toMgrs();
			
			tempRoz.options.title = new_name;
			tempRoz.closePopup();
			tempRoz.setPopupContent(new_name + "<br/>Radius: " + radius.toFixed(2) + "NM<br/>" + mgrs + "<hr/><input type=\"text\" class=\"form-control roz-rename\"><button class=\"btn btn-sm btn-warning btn-roz-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-roz-del\">Delete</button>");
		}		
	});
}
