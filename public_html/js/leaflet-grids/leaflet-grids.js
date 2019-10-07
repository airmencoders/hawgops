/*
 *
 * Inspired by Leaflet.Grid: https://github.com/jieter/Leaflet.Grid
 */

L.Grids = L.LayerGroup.extend({
    options: {
        redraw: 'move', // or moveend depending when the grids is refreshed
        groups: [],
        lineStyle: {
            stroke: true,
            color: '#ff9000',
            opacity: 1,
            weight: 1,
            clickable: false
        },
        zoneStyle: {
                stroke: true,
                color: '#ff0000',
                opacity: 0.6,
                weight: 4,
                clickable: false
        },
        label: true,
    },

    initialize: function (options) {
        L.LayerGroup.prototype.initialize.call(this);
        L.Util.setOptions(this, options);
    },

    onAdd: function (map) {
        this._map = map;
        var grid = this.redraw();

        // Create a listener to redraw the map when it's moving
        this._map.on('viewreset ' + this.options.redraw, function () {
            grid.redraw();
        });
    },

    onRemove: function(map) {
        this._map = map;

        // Remove listener and grids
        this._map.off('viewreset ' + this.options.redraw);
        this.eachLayer(this.removeLayer, this);
    },

    redraw: function () {
        this._lngCoords = [],
        this._latCoords = [],
        this._gridLabels =  [],
        this._mapZoom = this._map.getZoom();
        this._bounds =  this._map.getBounds(); //.pad(0.5);
        this._gridSize = this._gridSpacing();

        var gridLines = this._gridLines();
        var gridGroup = L.layerGroup(); 

        for (i in gridLines){
            try {
                gridGroup.addLayer(gridLines[i]);
            }
            catch (err)
            {
                console.log(err);
                console.log("*******");
                console.log(gridLines[i]);
            }
        }

        if (this.options.label) {
            for (i in this._gridLabels) {
                gridGroup.addLayer(this._gridLabels[i]);
            }
        }
        // First, remove old layer before drawing the new one
        this.eachLayer(this.removeLayer, this); 
        // Second, add the new grid
        gridGroup.addTo(this);
        return this;
    },
    
    _gridSpacing: function () {
        var zoom = this._map.getZoom();
        if (zoom > 18) {zoom = 18}
        return this.options.coordinateGridSpacing[zoom];
    },
        
    _gridLines: function () {
        var lines = [];
        var labelPt, labelText
        var labelBounds = this._map.getBounds().pad(-0.03);
        var labelNorth = labelBounds.getNorth();
        var labelWest = labelBounds.getWest();
        var latCoord = this._snap(this._bounds.getSouth());
        var northBound = this._bounds.getNorth();
        while (latCoord < northBound) {
            lines.push(this._horizontalLine(latCoord));
            labelPt = L.latLng(latCoord, labelWest) 
            labelText = this._labelFormat(latCoord, 'lat');
            this._gridLabels.push(this._label(labelPt, labelText,'lat'));
            latCoord += this._gridSize;
        }
        var lngCoord = this._snap(this._bounds.getWest());
        var eastBound = this._bounds.getEast();

        while (lngCoord < eastBound) {
            lines.push(this._verticalLine(lngCoord));
            labelPt = L.latLng(labelNorth, lngCoord);
            labelText = this._labelFormat(lngCoord, 'lng');
            this._gridLabels.push(this._label(labelPt, labelText, 'lng'));
            lngCoord += this._gridSize;
        }
        return lines;
    },

    _snap: function (num) {
        return Math.floor(num / this._gridSize) * this._gridSize;
    },

    _snapTo: function (num, snap) {
        return Math.floor(num / snap) * snap;
    },

    _verticalLine: function (lng, options) {
        var upLimit, 
            downLimit, 
            style; 
        if (options){
            upLimit = options.upLimit ? options.upLimit : this._bounds.getNorth();
            downLimit = options.downLimit ? options.downLimit : this._bounds.getSouth();
            style = options.style ? options.style : this.options.lineStyle;
        }else{
            upLimit = this._bounds.getNorth();
            downLimit = this._bounds.getSouth();
            style =  this.options.lineStyle;
        }
        return L.polyline([
                [upLimit, lng],
                [downLimit, lng]
            ], style);
    },

    _horizontalLine: function (lat, options) {
        return L.polyline([
                [lat, this._bounds.getWest()],
                [lat, this._bounds.getEast()]
            ], options ? options : this.options.lineStyle);
    },

    _label: function (latLng, labelText, cssClass) {
        if (cssClass === undefined) { cssClass = ''; }
        return L.marker(latLng, {
                icon: L.divIcon({
                    className: 'leaflet-grids-label',
                    iconAnchor: L.point(30, 0),
                    html: '<div class="grid-label ' + cssClass + '">' + labelText+ '</div>'
                })
        });
    }
});

L.grids = {};

/* 
 * Mercator grid base class
 * shared by UTM and MGRS
 */

L.Grids.Mercator = L.Grids.extend({
    options: {
        mgrs: false, 
        utm: false,
    },

    _gridSpacing: function () {
        if ( this._mapZoom < 10 ) {
            return 100000;
        };
        if ( this._mapZoom < 14 ) {
            return 10000;
        };
        if ( this._mapZoom < 17 ) {
            return 1000;
        };
        if ( this._mapZoom <= 20 ) {
            return 100;
        };
        return NaN;
    },

    _gridLines: function () {
        /*
        * THIS FIRST CODE PORTION IS RESPONSIBLE FOR DRAWING 6 x 8 GRID-ZONE LINES + RESPECTIVE LABELS
        */
        // No grid for lowest map zoom
        if (this._mapZoom < 3){
            return null;
        }
        var lines = [];

        this._bounds =  this._map.getBounds().pad(0.5); // Adding 1/2 of the current view in each direction
        var latCoord = this._snapTo(this._bounds.getSouth(), 8.0);
        if (latCoord < -80.0){
            latCoord = -80.0;
        }

        var northBound = this._bounds.getNorth();
        var southBound = this._bounds.getSouth();
        var eastBound = this._bounds.getEast();
        var westBound = this._bounds.getWest();

        var longMGRS = [];
        var latMGRS = [];

        while (latCoord < northBound && latCoord <= 84) {
            this._latCoords.push(latCoord);
            if(latCoord==72.0){
                latMGRS.push(latCoord + 6.0);
                latCoord += 12.0; // Zone X is "higher" than the rest
            }else{
                latMGRS.push(latCoord + 4.0);
                latCoord += 8.0;
            }
        }
        var zoneBreaks = [];
        var zoneBreaks = [westBound];
        var lngCoord = this._snapTo(westBound, 6.0) + 6.0;

        var labelBounds = this._map.getBounds().pad(-0.03);
        var northLabel = labelBounds.getNorth();
        var westLabel = labelBounds.getWest();

        while (lngCoord < eastBound ) {
            if(this.options.utm){
                labelPt = L.latLng(northLabel, lngCoord);
                labelUTM = mgrs.LLtoUTM({lat: labelPt.lat, lon: labelPt.lng + .1});
                labelText = "Zone " + labelUTM.zoneNumber;
                this._gridLabels.push(this._label(labelPt, labelText, 'lng'));
            }
            zoneBreaks.push(lngCoord);
            lngCoord += 6.0;
        }
        zoneBreaks.push(eastBound);

        var options = {
            style: this.options.zoneStyle,
            upLimit: null,
            downLimit: -80.0,
        }
        for (var i=1; i < zoneBreaks.length-1; i++ ) {
            // Region of the world with no vertical grid exception
            if (zoneBreaks[i] <= 0.0 || zoneBreaks[i] >= 42.0){ 
                options.upLimit = 84;
                lines.push(this._verticalLine(zoneBreaks[i], options));
                longMGRS.push(zoneBreaks[i-1]+3);
            // Region to make Norway & Svagard happy
            }else{
                options.upLimit = 56;
                lines.push(this._verticalLine(zoneBreaks[i], options));

            }
        }

        var superThis = this;
        var labelPt;
        var handleSpecialZones = function(longArray, options){
            var centerLat = options.downLimit + Math.abs(options.upLimit - options.downLimit)/2.0;
            for (i in longArray){
                lines.push(superThis._verticalLine(longArray[i], options));
                if(superThis.options.mgrs){
                    previous = longArray[i-1] ? longArray[i-1] : 0.0;
                    labelPt = L.latLng(centerLat, previous+((longArray[i]-previous)/2.0));
                    gridLabel = mgrs.LLtoUTM({lat:labelPt.lat,lon:labelPt.lng});
                    superThis._gridLabels.push(superThis._label(labelPt, gridLabel.zoneNumber + gridLabel.zoneLetter));
                }
            }
        }

        // For Norway special case
        var longArray = [3.0, 12.0, 18.0, 24.0, 30.0, 36.0];
        options.upLimit = 64.0; 
        options.downLimit = 56.0;
        handleSpecialZones(longArray, options);

        // For Svagard special case 
        longArray = [9.0, 21.0, 33.0]; 
        options.upLimit = 84.0; 
        options.downLimit = 72.0; 
        handleSpecialZones(longArray, options);
        
        // For the zone in between 
        longArray = [6.0, 12.0, 18.0, 24.0, 30.0, 36.0]; 
        options.upLimit = 72.0; 
        options.downLimit = 64.0; 
        handleSpecialZones(longArray, options);

        var previousLat, 
            previousLong;
        for (i in this._latCoords) {
            lines.push(this._horizontalLine(this._latCoords[i], this.options.zoneStyle));
            // For the zone below the irregularity zone
            if(this.options.mgrs && this._latCoords[i] <= 56.0 && this._latCoords[i] > -80.0){
                for (j in longArray) {
                    if(this._latCoords[i-1] === 0){
                        previousLat = 0; 
                    }else{
                        previousLat = this._latCoords[i-1] ? this._latCoords[i-1] : -80.0;
                    }
                    centerLat = previousLat + Math.abs(this._latCoords[i]-previousLat)/2.0;
                    previousLong = longArray[j-1] ? longArray[j-1] : 0.0;
                    labelPt = L.latLng(centerLat, previousLong+((longArray[j]-previousLong)/2.0));
                    gridLabel = mgrs.LLtoUTM({lat:labelPt.lat,lon:labelPt.lng});
                    this._gridLabels.push(this._label(labelPt, gridLabel.zoneNumber + gridLabel.zoneLetter));
                }
            }
        }

        var mapBounds = this._map.getBounds(); // show just the zone boundaries if zoomed out too far
        if ( Math.abs(mapBounds.getWest() - mapBounds.getEast()) > 8 ) {
            if (this.options.mgrs){
                for(var u=0;u<longMGRS.length-1;u++){
                    for(var v=0;v<latMGRS.length-1;v++){
                        labelPt = L.latLng(latMGRS[v],longMGRS[u]);
                        gridLabel = mgrs.LLtoUTM({lat:labelPt.lat,lon:labelPt.lng});
                        this._gridLabels.push(this._label(labelPt, gridLabel.zoneNumber + gridLabel.zoneLetter));
                    }
                }
            }
            return lines;
        };

        /*
        * THIS SECOND CODE PORTION USES UTM GRID-ZONE LINES + RESPECTIVE LABELS
        */
        var gridSize = this._gridSize; // depends on the zoom level
        var fFactor = .000001; // keeps calculations at zone boundaries inside the zone
        this._bounds =  this._map.getBounds().pad(0.1); // Adding 1/10 of the current view in each direction

        // Invisible gridLines for labels positionning
        var horzLines = [];
        var vertLines = [];
        var drawnFlag = false;

		var horzLineLabels = [];
		
        // Empty the labels list for the MGRS grid
        // Keep them around for UTM to better see the zoneNumber
        if(this.options.mgrs){
            this._gridLabels = [];
        }
        var labelText, 
            labelLatUTM = [], 
            labelLongUTM = [];

        for (var i=0; i < zoneBreaks.length-1; i++) {
            // Map corners and center
            var northWestLL = L.latLng( northBound, zoneBreaks[i] + fFactor );
            var southEastLL = L.latLng( southBound, zoneBreaks[i+1] - fFactor );
            var centerLL = L.latLngBounds(northWestLL,southEastLL).getCenter();
            var center = mgrs.LLtoUTM({lon:centerLL.lng, lat:centerLL.lat});
            var southEast = mgrs.LLtoUTM({lon:southEastLL.lng, lat:southEastLL.lat});
            var northWest = mgrs.LLtoUTM({lon:northWestLL.lng, lat:northWestLL.lat});

            var buffer;

            // draw "horizontal" lines + labels horizontal positionning
            var latCoord = this._snap(southEast.northing);
            while (latCoord < northWest.northing) {
                var leftPointUTM = {
                    northing: latCoord,
                    easting: northWest.easting,
                    zoneLetter: center.zoneLetter,
                    zoneNumber: center.zoneNumber
                };
                var leftPointLL = mgrs.UTMtoLL(leftPointUTM);
                leftPointUTM.northing += gridSize/2; 
                var leftPointLabel = mgrs.UTMtoLL(leftPointUTM);

                var rightPointUTM = {
                    northing: latCoord,
                    easting: southEast.easting,
                    zoneLetter:center.zoneLetter,
                    zoneNumber:center.zoneNumber
                };
                var rightPointLL = mgrs.UTMtoLL(rightPointUTM);
                rightPointUTM.northing += gridSize/2; 
                var rightPointLabel = mgrs.UTMtoLL(rightPointUTM);
 
                lines.push(this._cleanLine(L.polyline([leftPointLL,rightPointLL], this.options.lineStyle), zoneBreaks[i], zoneBreaks[i+1]));

				horzLines.push(this._cleanLine(L.polyline([leftPointLabel,rightPointLabel], this.options.lineStyle), zoneBreaks[i], zoneBreaks[i+1]));
				
				
								
                if (this.options.utm && i == 0){ // avoiding duplicate latitudes (because of zoneBreaks)
                    labelLatUTM.push([leftPointLL.lat,latCoord]); // Latitudes for utm labels
                }

                latCoord += gridSize;
            }

            // draw "vertical" lines + labels vertical positionning
            var lonCoord = this._snap(northWest.easting - gridSize);
            while (lonCoord < southEast.easting){
                var bottomPointUTM = {
                    northing: southEast.northing,
                    easting: lonCoord,
                    zoneLetter: center.zoneLetter,
                    zoneNumber:center.zoneNumber
                };
                var bottomPointLL = mgrs.UTMtoLL(bottomPointUTM);
                bottomPointUTM.easting += gridSize/2;
                bottomPointLabel = mgrs.UTMtoLL(bottomPointUTM);

                var topPointUTM = {
                    northing: northWest.northing,
                    easting: lonCoord,
                    zoneLetter:center.zoneLetter,
                    zoneNumber:center.zoneNumber
                };
                var topPointLL = mgrs.UTMtoLL(topPointUTM);
                // For the mgrs labelling
                topPointUTM.easting += gridSize/2;
                topPointLabel = mgrs.UTMtoLL(topPointUTM);

                lines.push(this._cleanVert(L.polyline([bottomPointLL,topPointLL], this.options.lineStyle), zoneBreaks[i], zoneBreaks[i+1]));
                vertLines.push(this._cleanVert(L.polyline([bottomPointLabel,topPointLabel], this.options.lineStyle), zoneBreaks[i], zoneBreaks[i+1]));

                // As the vertical lines are "cleaned" -> we need to put the labels accordingly (+ buffer around zoneBreaks vertical lines) 
                buffer = Math.abs(topPointLL.lon - topPointLabel.lon);
                if(this.options.utm && topPointLL.lon > (zoneBreaks[i] + buffer) && topPointLL.lon < (zoneBreaks[i+1] - buffer)){
                    labelLongUTM.push([topPointLL.lon,lonCoord]);
                }

                lonCoord += gridSize;
            }
        }

        //Display the MGRS labels centered in each zone
        if(this.options.mgrs){
			if(this._MGRSAccuracy() == 0) {
				for (x in horzLines){
					for (y in vertLines){
						labelPt = this._lineIntersect(horzLines[x], vertLines[y]);
						if(labelPt && this._bounds.contains(labelPt)){
							gridLabel = mgrs.forward([labelPt.lng, labelPt.lat], this._MGRSAccuracy());
							
							if(this._mapZoom >= 8) {
								gridLabel = gridLabel.substr(4);
							}
							this._gridLabels.push(this._label(labelPt, gridLabel));
						}        
					}
				}
			} else {
				
				// Draws the grid labels on the western border
				for (x in horzLines){
					drawnFlag = false;

						for (y in vertLines){
							labelPt = this._lineIntersect(horzLines[x], vertLines[y]);
							if(labelPt && this._bounds.contains(labelPt) && !drawnFlag){
								gridLabel = mgrs.forward([labelPt.lng, labelPt.lat], this._MGRSAccuracy());
								gridLabel = gridLabel.substr(7)
								var gridArray = gridLabel.split(" ");
								gridLabel = gridArray[1];

								var currentBounds = this._map.getBounds().pad(-0.05);
								var labelLng = currentBounds.getWest();
								labelPt.lng = labelLng;
								
								gridLabel = "<span class=\"west-label\">" + gridLabel + "</span>";

								this._gridLabels.push(this._label(labelPt, gridLabel));
								drawnFlag = true;
							}        
						}
					
				}
				
				// Draws the grid labels on the southern border
				for (y in vertLines){
					drawnFlag = false;
					for (x in horzLines){
						labelPt = this._lineIntersect(horzLines[x], vertLines[y]);
						if(labelPt && this._bounds.contains(labelPt) && !drawnFlag){
							gridLabel = mgrs.forward([labelPt.lng, labelPt.lat], this._MGRSAccuracy());
							gridLabel = gridLabel.substr(7)
							var gridArray = gridLabel.split(" ");
							gridLabel = gridArray[0];
							
							var currentBounds = this._map.getBounds().pad(-0.05);
							var labelLat = currentBounds.getSouth();
							labelPt.lat = labelLat;
							
							gridLabel = "<span class=\"south-label\">" + gridLabel + "</span>";

							this._gridLabels.push(this._label(labelPt, gridLabel));
							drawnFlag = true;
						}        
					}
				}
			}
        }

        return lines;
    },

    /* This function takes an "horizontal" line and 2 bounds (left and right)
     * It returns a new line with the same slope but bounded
     * A line is defined by y = slope * x + b
     */
    _cleanLine: function(line, leftLng, rightLng) {
    	// Get the line equation
    	var pts = line.getLatLngs(),
    		options = line.options,
    		pt1 = pts[0],
    		pt2 = pts[pts.length-1],
    		slope = (pt1.lat-pt2.lat)/(pt1.lng-pt2.lng),
    		b = pt1.lat - slope*pt1.lng;

    		var newLeftLat = slope*leftLng + b, 
    		newPt1 = L.latLng(newLeftLat, leftLng);

    		var newRightLat = slope*rightLng + b,
    		newPt2 = L.latLng(newRightLat, rightLng);

    	var newLine = L.polyline([newPt1, newPt2], options);
		return newLine;
    },

    /* This function takes a "vertical" line and 2 bounds (left and right)
     * It returns a new line with the same slope but bounded
     * A line is defined by y = slope * x + b
     * The only difference here is testing first to see if bounds cut the line
     */
    _cleanVert: function (line, leftLng, rightLng) {
       var pts = line.getLatLngs(), 
	       options = line.options,
	       pt1 = pts[0],
	       pt2 = pts[pts.length-1],
	       slope = (pt1.lat-pt2.lat)/(pt1.lng-pt2.lng);

       if (pt2.lng > rightLng) {
           var newLat = pt1.lat + (slope * (rightLng - pt1.lng));
           pt2 = L.latLng(newLat,rightLng);
       } 
       if (pt2.lng < leftLng) {
           var newLat = pt1.lat + (slope * (leftLng - pt1.lng));
           pt2 = L.latLng(newLat,leftLng);
       } 
       return L.polyline([pt1, pt2], options);
    },

    /* Find the intersection point of two lines
     * based on line equations
     */    
    _lineIntersect: function(line1, line2) {
        // Get the first and last point of the two given segments
        var line1Pts = line1.getLatLngs();
        var line2Pts = line2.getLatLngs();
        var pt1 = line1Pts[0];
        var pt2 = line1Pts[line1Pts.length - 1];
        var pt3 = line2Pts[0];
        var pt4 = line2Pts[line2Pts.length - 1];
        var x1 = pt1.lng;
        var y1 = pt1.lat;
        var x2 = pt2.lng;
        var y2 = pt2.lat;
        var x3 = pt3.lng;
        var y3 = pt3.lat;
        var x4 = pt4.lng;
        var y4 = pt4.lat;

        // Lines equation 
        var slope1 = (y2-y1)/(x2-x1); 
        var b1 = y1 - slope1*x1;
        var slope2 = (y4-y3)/(x4-x3); 
        var b2 = y3 - slope2*x3;

        // Intersection point of 2 lines
        if (slope1 != slope2){
            var x = (b2-b1)/(slope1-slope2);
        }else{
            return false; // Lines are parallels
        }

        var y = slope1 * x + b1; 

        // line1 and line2 are segments not lines so :
        // (x,y) must belong to the x-domain of the two segments
        if (x > Math.min(x1,x2) && x < Math.max(x1,x2) && x > Math.min(x3,x4) && x < Math.max(x3,x4)){
            return L.latLng(y,x);
        }else{
            return false; // segments do not intersect
        }
    },
});

/*
  MILITARY GRID REFERENCE SYSTEM GRIDS
 */

L.Grids.MGRS = L.Grids.Mercator.extend({
    options: {
        mgrs: true,
    },

    initialize: function (options) {
        L.LayerGroup.prototype.initialize.call(this);
        L.Util.setOptions(this, options);
    },

    _MGRSAccuracy: function () {
        if ( this._mapZoom < 10 ) {
            return 0;
        };
        if ( this._mapZoom < 14 ) {
            return 1;
        };
        if ( this._mapZoom < 17 )  {
            return 2;
        };
        if ( this._mapZoom <= 20 )  {
            return 3;
        };
        return NaN;
    },

});

L.grids.mgrs = function (options) {
    return new L.Grids.MGRS(options);
};