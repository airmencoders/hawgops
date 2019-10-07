/**
 * @class L.Draw.Rectangle
 * @aka Draw.Rectangle
 * @inherits L.Draw.SimpleShape
 */
L.Draw.Rectangle = L.Draw.SimpleShape.extend({
	statics: {
		TYPE: 'rectangle'
	},

	options: {
		shapeOptions: {
			stroke: true,
			color: '#ff0000',
			weight: 5,
			fill: false,
			clickable: false,
		},
		showArea: false, //Whether to show the area in the tooltip
		metric: false // Whether to use the metric measurement system or imperial
	},

	// @method initialize(): void
	initialize: function (map, options) {
		// Save the type so super can fire, need to do this as cannot do this.TYPE :(
		this.type = L.Draw.Rectangle.TYPE;

		this._initialLabelText = L.drawLocal.draw.handlers.rectangle.tooltip.start;

		L.Draw.SimpleShape.prototype.initialize.call(this, map, options);
	},

	// @method disable(): void
	disable: function () {
		if (!this._enabled) {
			return;
		}

		this._isCurrentlyTwoClickDrawing = false;
		L.Draw.SimpleShape.prototype.disable.call(this);
	},

	_onMouseUp: function (e) {
		if (!this._shape && !this._isCurrentlyTwoClickDrawing) {
			this._isCurrentlyTwoClickDrawing = true;
			return;
		}

		// Make sure closing click is on map
		if (this._isCurrentlyTwoClickDrawing && !_hasAncestor(e.target, 'leaflet-pane')) {
			return;
		}

		L.Draw.SimpleShape.prototype._onMouseUp.call(this);
	},

	_drawShape: function (latlng) {
		if (!this._shape) {
			this._shape = new L.Rectangle(new L.LatLngBounds(this._startLatLng, latlng), this.options.shapeOptions);
			this._map.addLayer(this._shape);
		} else {
			this._shape.setBounds(new L.LatLngBounds(this._startLatLng, latlng));
		}
	},

	_fireCreatedEvent: function () {
		var label = $("#chit-description").val();
		$("#chit-description").val("");
		if(label == "") {
			label = "EA";
		}
		
		this.options.shapeOptions.type = "ea";
		this.options.shapeOptions.title = label;
		this.options.shapeOptions.latlngs = this._shape.getLatLngs();
		
		var rectangle = new L.Rectangle(this._shape.getBounds(), this.options.shapeOptions);
		
		rectangle.bindPopup(label + "<hr/><input type=\"text\" class=\"form-control ea-rename\"><button class=\"btn btn-sm btn-warning btn-ea-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-ea-del\">Delete</button>");
		rectangle.on("click", eaClicked);
		
		L.Draw.SimpleShape.prototype._fireCreatedEvent.call(this, rectangle);
	},

	_getTooltipText: function () {
		var tooltipText = L.Draw.SimpleShape.prototype._getTooltipText.call(this),
			shape = this._shape,
			showArea = this.options.showArea,
			latLngs, area, subtext;

		if (shape) {
			latLngs = this._shape._defaultShape ? this._shape._defaultShape() : this._shape.getLatLngs();
			area = L.GeometryUtil.geodesicArea(latLngs);
			subtext = showArea ? L.GeometryUtil.readableArea(area, this.options.metric) : '';
		}

		return {
			text: tooltipText.text,
			subtext: subtext
		};
	}
});

function _hasAncestor(el, cls) {
	while ((el = el.parentElement) && !el.classList.contains(cls)) {
		;
	}
	return el;
}

function eaClicked() {
	var tempEa = this;
	
	$(".btn-ea-del").click(function() {
		layer_eas.removeLayer(tempEa);
	});
	
	$(".btn-ea-rename").click(function() {
		var new_name = $(".ea-rename").val();
		
		if(new_name != "") {
			tempEa.options.title = new_name;
			tempEa.closePopup();
			tempEa.setPopupContent(new_name + "<hr/><input type=\"text\" class=\"form-control ea-rename\"><button class=\"btn btn-sm btn-warning btn-ea-rename\">Rename</button><button class=\"btn btn-sm btn-danger btn-ea-del\">Delete</button>");
		}
	});
}
