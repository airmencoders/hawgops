/**
 * Geodesy representation conversion functions
 *
 * www.movable-type.co.uk/scripts/latlong.html
 * www.movable-type.co.uk/scripts/latlong-convert-coords.html
 * www.movable-type.co.uk/scripts/latlong-utm-mgrs.html
 *
 * www.movable-type.co.uk/scripts/geodesy-library.html#vector3d
 * www.movable-type.co.uk/scripts/js/geodesy/geodesy-library.html#dms
 * www.movable-type.co.uk/scripts/geodesy-library.html#latlon-ellipsoidal
 * www.movable-type.co.uk/scripts/geodesy-library.html#latlon-ellipsoidal-datum
 * www.movable-type.co.uk/scripts/geodesy-library.html#mgrs
 * www.movable-type.co.uk/scripts/geodesy-library.html#utm
 *
 * (c) Chris Veness 2002-2019  
 * MIT Licence 
 */
//==========================================================================================
// vector3d.js
//==========================================================================================
/**
 * Library of 3-d vector manipulation routines.
 *
 * @module vector3d
 */

/* Vector3d - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Functions for manipulating generic 3-d vectors.
 *
 * Functions return vectors as return results, so that operations can be chained.
 *
 * @example
 *   const v = v1.cross(v2).dot(v3) // ≡ v1×v2⋅v3
 */
class Vector3d {

    /**
     * Creates a 3-d vector.
     *
     * @param {number} x - X component of vector.
     * @param {number} y - Y component of vector.
     * @param {number} z - Z component of vector.
     *
     * @example
     *   import Vector3d from '/js/geodesy/vector3d.js';
     *   const v = new Vector3d(0.267, 0.535, 0.802);
     */
    constructor(x, y, z) {
        if (isNaN(x) || isNaN(x) || isNaN(x)) throw new TypeError(`invalid vector [${x},${y},${z}]`);

        this.x = Number(x);
        this.y = Number(y);
        this.z = Number(z);
    }


    /**
     * Length (magnitude or norm) of ‘this’ vector.
     *
     * @returns {number} Magnitude of this vector.
     */
    get length() {
        return Math.sqrt(this.x * this.x + this.y * this.y + this.z * this.z);
    }


    /**
     * Adds supplied vector to ‘this’ vector.
     *
     * @param   {Vector3d} v - Vector to be added to this vector.
     * @returns {Vector3d} Vector representing sum of this and v.
     */
    plus(v) {
        if (!(v instanceof Vector3d)) throw new TypeError('v is not Vector3d object');

        return new Vector3d(this.x + v.x, this.y + v.y, this.z + v.z);
    }


    /**
     * Subtracts supplied vector from ‘this’ vector.
     *
     * @param   {Vector3d} v - Vector to be subtracted from this vector.
     * @returns {Vector3d} Vector representing difference between this and v.
     */
    minus(v) {
        if (!(v instanceof Vector3d)) throw new TypeError('v is not Vector3d object');

        return new Vector3d(this.x - v.x, this.y - v.y, this.z - v.z);
    }


    /**
     * Multiplies ‘this’ vector by a scalar value.
     *
     * @param   {number}   x - Factor to multiply this vector by.
     * @returns {Vector3d} Vector scaled by x.
     */
    times(x) {
        if (isNaN(x)) throw new TypeError(`invalid scalar value ‘${x}’`);

        return new Vector3d(this.x * x, this.y * x, this.z * x);
    }


    /**
     * Divides ‘this’ vector by a scalar value.
     *
     * @param   {number}   x - Factor to divide this vector by.
     * @returns {Vector3d} Vector divided by x.
     */
    dividedBy(x) {
        if (isNaN(x)) throw new TypeError(`invalid scalar value ‘${x}’`);

        return new Vector3d(this.x / x, this.y / x, this.z / x);
    }


    /**
     * Multiplies ‘this’ vector by the supplied vector using dot (scalar) product.
     *
     * @param   {Vector3d} v - Vector to be dotted with this vector.
     * @returns {number}   Dot product of ‘this’ and v.
     */
    dot(v) {
        if (!(v instanceof Vector3d)) throw new TypeError('v is not Vector3d object');

        return this.x * v.x + this.y * v.y + this.z * v.z;
    }


    /**
     * Multiplies ‘this’ vector by the supplied vector using cross (vector) product.
     *
     * @param   {Vector3d} v - Vector to be crossed with this vector.
     * @returns {Vector3d} Cross product of ‘this’ and v.
     */
    cross(v) {
        if (!(v instanceof Vector3d)) throw new TypeError('v is not Vector3d object');

        const x = this.y * v.z - this.z * v.y;
        const y = this.z * v.x - this.x * v.z;
        const z = this.x * v.y - this.y * v.x;

        return new Vector3d(x, y, z);
    }


    /**
     * Negates a vector to point in the opposite direction.
     *
     * @returns {Vector3d} Negated vector.
     */
    negate() {
        return new Vector3d(-this.x, -this.y, -this.z);
    }


    /**
     * Normalizes a vector to its unit vector
     * – if the vector is already unit or is zero magnitude, this is a no-op.
     *
     * @returns {Vector3d} Normalised version of this vector.
     */
    unit() {
        const norm = this.length;
        if (norm == 1) return this;
        if (norm == 0) return this;

        const x = this.x / norm;
        const y = this.y / norm;
        const z = this.z / norm;

        return new Vector3d(x, y, z);
    }


    /**
     * Calculates the angle between ‘this’ vector and supplied vector atan2(|p₁×p₂|, p₁·p₂) (or if
     * (extra-planar) ‘n’ supplied then atan2(n·p₁×p₂, p₁·p₂).
     *
     * @param   {Vector3d} v - Vector whose angle is to be determined from ‘this’ vector.
     * @param   {Vector3d} [n] - Plane normal: if supplied, angle is signed +ve if this->v is
     *                     clockwise looking along n, -ve in opposite direction.
     * @returns {number}   Angle (in radians) between this vector and supplied vector (in range 0..π
     *                     if n not supplied, range -π..+π if n supplied).
     */
    angleTo(v, n=undefined) {
        if (!(v instanceof Vector3d)) throw new TypeError('v is not Vector3d object');
        if (!(n instanceof Vector3d || n == undefined)) throw new TypeError('n is not Vector3d object');

        // q.v. stackoverflow.com/questions/14066933#answer-16544330, but n·p₁×p₂ is numerically
        // ill-conditioned, so just calculate sign to apply to |p₁×p₂|

        // if n·p₁×p₂ is -ve, negate |p₁×p₂|
        const sign = n==undefined || this.cross(v).dot(n)>=0 ? 1 : -1;

        const sinθ = this.cross(v).length * sign;
        const cosθ = this.dot(v);

        return Math.atan2(sinθ, cosθ);
    }


    /**
     * Rotates ‘this’ point around an axis by a specified angle.
     *
     * @param   {Vector3d} axis - The axis being rotated around.
     * @param   {number}   angle - The angle of rotation (in degrees).
     * @returns {Vector3d} The rotated point.
     */
    rotateAround(axis, angle) {
        if (!(axis instanceof Vector3d)) throw new TypeError('axis is not Vector3d object');

        const θ = angle.toRadians();

        // en.wikipedia.org/wiki/Rotation_matrix#Rotation_matrix_from_axis_and_angle
        // en.wikipedia.org/wiki/Quaternions_and_spatial_rotation#Quaternion-derived_rotation_matrix
        const p = this.unit();
        const a = axis.unit();

        const s = Math.sin(θ);
        const c = Math.cos(θ);
        const t = 1-c;
        const x = a.x, y = a.y, z = a.z;

        const r = [ // rotation matrix for rotation about supplied axis
            [ t*x*x + c,   t*x*y - s*z, t*x*z + s*y ],
            [ t*x*y + s*z, t*y*y + c,   t*y*z - s*x ],
            [ t*x*z - s*y, t*y*z + s*x, t*z*z + c   ],
        ];

        // multiply r × p
        const rp = [
            r[0][0]*p.x + r[0][1]*p.y + r[0][2]*p.z,
            r[1][0]*p.x + r[1][1]*p.y + r[1][2]*p.z,
            r[2][0]*p.x + r[2][1]*p.y + r[2][2]*p.z,
        ];
        const p2 = new Vector3d(rp[0], rp[1], rp[2]);

        return p2;
        // qv en.wikipedia.org/wiki/Rodrigues'_rotation_formula...
    }

    /**
     * String representation of vector.
     *
     * @param   {number} [dp=3] - Number of decimal places to be used.
     * @returns {string} Vector represented as [x,y,z].
     */
    toString(dp=3) {
        return `[${this.x.toFixed(dp)},${this.y.toFixed(dp)},${this.z.toFixed(dp)}]`;
    }
}

// Extend Number object with methods to convert between degrees & radians
/*Number.prototype.toRadians = function() { return this * Math.PI / 180; };
Number.prototype.toDegrees = function() { return this * 180 / Math.PI; };
export default Vector3d;*/
//==========================================================================================
// latlon-ellipsoidal.js
//==========================================================================================
/*import Dms      from './dms.js';
import Vector3d from './vector3d.js';*/


/**
 * A latitude/longitude point defines a geographic location on or above/below the earth’s surface,
 * measured in degrees from the equator & the International Reference Meridian and in metres above
 * the ellipsoid, and based on a given datum.
 *
 * As so much modern geodesy is based on WGS-84 (as used by GPS), this module includes WGS-84
 * ellipsoid parameters, and it has methods for converting geodetic (latitude/longitude) points to/from
 * geocentric cartesian points; the latlon-ellipsoidal-datum and latlon-ellipsoidal-referenceframe
 * modules provide transformation parameters for converting between historical datums and between
 * modern reference frames.
 *
 * This module is used for both trigonometric geodesy (eg latlon-ellipsoidal-vincenty) and n-vector
 * geodesy (eg latlon-nvector-ellipsoidal), and also for UTM/MGRS mapping.
 *
 * @module latlon-ellipsoidal
 */

/*
 * Ellipsoid parameters; exposed through static getter below.
 *
 * The only ellipsoid defined is WGS84, for use in utm/mgrs, vincenty, nvector.
 */
/*const ellipsoids = {
    WGS84: { a: 6378137, b: 6356752.314245, f: 1/298.257223563 },
};*/


/*
 * Datums; exposed through static getter below.
 *
 * The only datum defined is WGS84, for use in utm/mgrs, vincenty, nvector.
 */
/*const datums = {
    WGS84: { ellipsoid: ellipsoids.WGS84 },
};*/

/*
 * Ellipsoid parameters; exposed through static getter below.
 */
const ellipsoids = {
    WGS84:         { a: 6378137,     b: 6356752.314245, f: 1/298.257223563 },
    Airy1830:      { a: 6377563.396, b: 6356256.909,    f: 1/299.3249646   },
    AiryModified:  { a: 6377340.189, b: 6356034.448,    f: 1/299.3249646   },
    Bessel1841:    { a: 6377397.155, b: 6356078.962818, f: 1/299.1528128   },
    Clarke1866:    { a: 6378206.4,   b: 6356583.8,      f: 1/294.978698214 },
    Clarke1880IGN: { a: 6378249.2,   b: 6356515.0,      f: 1/293.466021294 },
    GRS80:         { a: 6378137,     b: 6356752.314140, f: 1/298.257222101 },
    Intl1924:      { a: 6378388,     b: 6356911.946,    f: 1/297           }, // aka Hayford
    WGS72:         { a: 6378135,     b: 6356750.5,      f: 1/298.26        },
};


/*
 * Datums; exposed through static getter below.
 */
const datums = {
    // transforms: t in metres, s in ppm, r in arcseconds              tx       ty        tz       s        rx        ry        rz
    ED50:       { ellipsoid: ellipsoids.Intl1924,      transform: [   89.5,    93.8,    123.1,    -1.2,     0.0,      0.0,      0.156    ] }, // epsg.io/1311
    ETRS89:     { ellipsoid: ellipsoids.GRS80,         transform: [    0,       0,        0,       0,       0,        0,        0        ] }, // epsg.io/1149; @ 1-metre level
    Irl1975:    { ellipsoid: ellipsoids.AiryModified,  transform: [ -482.530, 130.596, -564.557,  -8.150,   1.042,    0.214,    0.631    ] }, // epsg.io/1954
    NAD27:      { ellipsoid: ellipsoids.Clarke1866,    transform: [    8,    -160,     -176,       0,       0,        0,        0        ] },
    NAD83:      { ellipsoid: ellipsoids.GRS80,         transform: [    0.9956, -1.9103,  -0.5215, -0.00062, 0.025915, 0.009426, 0.011599 ] },
    NTF:        { ellipsoid: ellipsoids.Clarke1880IGN, transform: [  168,      60,     -320,       0,       0,        0,        0        ] },
    OSGB36:     { ellipsoid: ellipsoids.Airy1830,      transform: [ -446.448, 125.157, -542.060,  20.4894, -0.1502,  -0.2470,  -0.8421   ] }, // epsg.io/1314
    Potsdam:    { ellipsoid: ellipsoids.Bessel1841,    transform: [ -582,    -105,     -414,      -8.3,     1.04,     0.35,    -3.08     ] },
    TokyoJapan: { ellipsoid: ellipsoids.Bessel1841,    transform: [  148,    -507,     -685,       0,       0,        0,        0        ] },
    WGS72:      { ellipsoid: ellipsoids.WGS72,         transform: [    0,       0,       -4.5,    -0.22,    0,        0,        0.554    ] },
    WGS84:      { ellipsoid: ellipsoids.WGS84,         transform: [    0.0,     0.0,      0.0,     0.0,     0.0,      0.0,      0.0      ] },
};

// freeze static properties
Object.freeze(ellipsoids.WGS84);
Object.freeze(datums.WGS84);


/* LatLonEllipsoidal - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

/**
 * Latitude/longitude points on an ellipsoidal model earth, with ellipsoid parameters and methods
 * for converting points to/from cartesian (ECEF) coordinates.
 *
 * This is the core class, which will usually be used via LatLonEllipsoidal_Datum or
 * LatLonEllipsoidal_ReferenceFrame.
 */
class LatLonEllipsoidal {

    /**
     * Creates a geodetic latitude/longitude point on a (WGS84) ellipsoidal model earth.
     *
     * @param  {number} lat - Latitude (in degrees).
     * @param  {number} lon - Longitude (in degrees).
     * @param  {number} [height=0] - Height above ellipsoid in metres.
     * @throws {TypeError} Invalid lat/lon/height.
     *
     * @example
     *   import LatLon from '/js/geodesy/latlon-ellipsoidal.js';
     *   const p = new LatLon(51.47788, -0.00147, 17);
     */
    constructor(lat, lon, height=0) {
        if (isNaN(lat)) throw new TypeError(`invalid lat ‘${lat}’`);
        if (isNaN(lon)) throw new TypeError(`invalid lon ‘${lon}’`);
        if (isNaN(height)) throw new TypeError(`invalid height ‘${height}’`);

        this._lat = Dms.wrap90(Number(lat));
        this._lon = Dms.wrap180(Number(lon));
        this._height = Number(height);
    }


    /**
     * Latitude in degrees north from equator (including aliases lat, latitude): can be set as
     * numeric or hexagesimal (deg-min-sec); returned as numeric.
     */
    get lat()       { return this._lat; }
    get latitude()  { return this._lat; }
    set lat(lat) {
        this._lat = isNaN(lat) ? Dms.wrap90(Dms.parse(lat)) : Dms.wrap90(Number(lat));
        if (isNaN(this._lat)) throw new TypeError(`invalid lat ‘${lat}’`);
    }
    set latitude(lat) {
        this._lat = isNaN(lat) ? Dms.wrap90(Dms.parse(lat)) : Dms.wrap90(Number(lat));
        if (isNaN(this._lat)) throw new TypeError(`invalid latitude ‘${lat}’`);
    }

    /**
     * Longitude in degrees east from international reference meridian (including aliases lon, lng,
     * longitude): can be set as numeric or hexagesimal (deg-min-sec); returned as numeric.
     */
    get lon()       { return this._lon; }
    get lng()       { return this._lon; }
    get longitude() { return this._lon; }
    set lon(lon) {
        this._lon = isNaN(lon) ? Dms.wrap180(Dms.parse(lon)) : Dms.wrap180(Number(lon));
        if (isNaN(this._lon)) throw new TypeError(`invalid lon ‘${lon}’`);
    }
    set lng(lon) {
        this._lon = isNaN(lon) ? Dms.wrap180(Dms.parse(lon)) : Dms.wrap180(Number(lon));
        if (isNaN(this._lon)) throw new TypeError(`invalid lng ‘${lon}’`);
    }
    set longitude(lon) {
        this._lon = isNaN(lon) ? Dms.wrap180(Dms.parse(lon)) : Dms.wrap180(Number(lon));
        if (isNaN(this._lon)) throw new TypeError(`invalid longitude ‘${lon}’`);
    }

    /**
     * Height in metres above ellipsoid.
     */
    get height()       { return this._height; }
    set height(height) { this._height = Number(height); if (isNaN(this._height)) throw new TypeError(`invalid height ‘${height}’`); }


    /**
     * Datum.
     *
     * Note this is replicated within LatLonEllipsoidal in order that a LatLonEllipsoidal object can
     * be monkey-patched to look like a LatLonEllipsoidal_Datum, for Vincenty calculations on
     * different ellipsoids.
     *
     * @private
     */
    get datum()      { return this._datum; }
    set datum(datum) { this._datum = datum; }


    /**
     * Ellipsoids with their parameters; this module only defines WGS84 parameters a = 6378137, b =
     * 6356752.314245, f = 1/298.257223563.
     *
     * @example
     *   const a = LatLon.ellipsoids.WGS84.a; // 6378137
     */
    static get ellipsoids() {
        return ellipsoids;
    }

    /**
     * Datums; this module only defines WGS84 datum, hence no datum transformations.
     *
     * @example
     *   const a = LatLon.datums.WGS84.ellipsoid.a; // 6377563.396
     */
    static get datums() {
        return datums;
    }


    /**
     * Parses a latitude/longitude point from a variety of formats.
     *
     * Latitude & longitude (in degrees) can be supplied as two separate parameters, as a single
     * comma-separated lat/lon string, or as a single object with { lat, lon } or GeoJSON properties.
     *
     * The latitude/longitude values may be numeric or strings; they may be signed decimal or
     * deg-min-sec (hexagesimal) suffixed by compass direction (NSEW); a variety of separators are
     * accepted. Examples -3.62, '3 37 12W', '3°37′12″W'.
     *
     * Thousands/decimal separators must be comma/dot; use Dms.fromLocale to convert locale-specific
     * thousands/decimal separators.
     *
     * @param   {number|string|Object} lat|latlon - Latitude (in degrees), or comma-separated lat/lon, or lat/lon object.
     * @param   {number}               [lon]      - Longitude (in degrees).
     * @param   {number}               [height=0] - Height above ellipsoid in metres.
     * @returns {LatLon} Latitude/longitude point on WGS84 ellipsoidal model earth.
     * @throws  {TypeError} Invalid coordinate.
     *
     * @example
     *   const p1 = LatLon.parse(51.47788, -0.00147);              // numeric pair
     *   const p2 = LatLon.parse('51°28′40″N, 000°00′05″W', 17);   // dms string + height
     *   const p3 = LatLon.parse({ lat: 52.205, lon: 0.119 }, 17); // { lat, lon } object numeric + height
     */
    static parse(...args) {
        if (args.length == 0) throw new TypeError('invalid (empty) point');

        let lat=undefined, lon=undefined, height=undefined;

        // single { lat, lon } object
        if (typeof args[0]=='object' && (args.length==1 || !isNaN(parseFloat(args[1])))) {
            const ll = args[0];
            if (ll.type == 'Point' && Array.isArray(ll.coordinates)) { // GeoJSON
                [ lon, lat, height ] = ll.coordinates;
                height = height || 0;
            } else { // regular { lat, lon } object
                if (ll.latitude  != undefined) lat = ll.latitude;
                if (ll.lat       != undefined) lat = ll.lat;
                if (ll.longitude != undefined) lon = ll.longitude;
                if (ll.lng       != undefined) lon = ll.lng;
                if (ll.lon       != undefined) lon = ll.lon;
                if (ll.height    != undefined) height = ll.height;
                lat = Dms.wrap90(Dms.parse(lat));
                lon = Dms.wrap180(Dms.parse(lon));
            }
            if (args[1] != undefined) height = args[1];
            if (isNaN(lat) || isNaN(lon)) throw new TypeError(`invalid point ‘${JSON.stringify(args[0])}’`);
        }

        // single comma-separated lat/lon
        if (typeof args[0] == 'string' && args[0].split(',').length == 2) {
            [ lat, lon ] = args[0].split(',');
            lat = Dms.wrap90(Dms.parse(lat));
            lon = Dms.wrap180(Dms.parse(lon));
            height = args[1] || 0;
            if (isNaN(lat) || isNaN(lon)) throw new TypeError(`invalid point ‘${args[0]}’`);
        }

        // regular (lat, lon) arguments
        if (lat==undefined && lon==undefined) {
            [ lat, lon ] = args;
            lat = Dms.wrap90(Dms.parse(lat));
            lon = Dms.wrap180(Dms.parse(lon));
            height = args[2] || 0;
            if (isNaN(lat) || isNaN(lon)) throw new TypeError(`invalid point ‘${args.toString()}’`);
        }

        return new this(lat, lon, height); // 'new this' as may return subclassed types
    }


    /**
     * Converts ‘this’ point from (geodetic) latitude/longitude coordinates to (geocentric)
     * cartesian (x/y/z) coordinates.
     *
     * @returns {Cartesian} Cartesian point equivalent to lat/lon point, with x, y, z in metres from
     *   earth centre.
     */
    toCartesian() {
        // x = (ν+h)⋅cosφ⋅cosλ, y = (ν+h)⋅cosφ⋅sinλ, z = (ν⋅(1-e²)+h)⋅sinφ
        // where ν = a/√(1−e²⋅sinφ⋅sinφ), e² = (a²-b²)/a² or (better conditioned) 2⋅f-f²
        const ellipsoid = this.datum
            ? this.datum.ellipsoid
            : this.referenceFrame ? this.referenceFrame.ellipsoid : ellipsoids.WGS84;

        const φ = this.lat.toRadians();
        const λ = this.lon.toRadians();
        const h = this.height;
        const { a, f } = ellipsoid;

        const sinφ = Math.sin(φ), cosφ = Math.cos(φ);
        const sinλ = Math.sin(λ), cosλ = Math.cos(λ);

        const eSq = 2*f - f*f;                      // 1st eccentricity squared ≡ (a²-b²)/a²
        const ν = a / Math.sqrt(1 - eSq*sinφ*sinφ); // radius of curvature in prime vertical

        const x = (ν+h) * cosφ * cosλ;
        const y = (ν+h) * cosφ * sinλ;
        const z = (ν*(1-eSq)+h) * sinφ;

        return new Cartesian(x, y, z);
    }


    /**
     * Checks if another point is equal to ‘this’ point.
     *
     * @param   {LatLon} point - Point to be compared against this point.
     * @returns {bool} True if points have identical latitude, longitude, height, and datum/referenceFrame.
     * @throws  {TypeError} Invalid point.
     *
     * @example
     *   const p1 = new LatLon(52.205, 0.119);
     *   const p2 = new LatLon(52.205, 0.119);
     *   const equal = p1.equals(p2); // true
     */
    equals(point) {
        if (!(point instanceof LatLonEllipsoidal)) throw new TypeError(`invalid point ‘${point}’`);

        if (Math.abs(this.lat - point.lat) > Number.EPSILON) return false;
        if (Math.abs(this.lon - point.lon) > Number.EPSILON) return false;
        if (Math.abs(this.height - point.height) > Number.EPSILON) return false;
        if (this.datum != point.datum) return false;
        if (this.referenceFrame != point.referenceFrame) return false;
        if (this.epoch != point.epoch) return false;

        return true;
    }


    /**
     * Returns a string representation of ‘this’ point, formatted as degrees, degrees+minutes, or
     * degrees+minutes+seconds.
     *
     * @param   {string} [format=d] - Format point as 'd', 'dm', 'dms', or 'n' for signed numeric.
     * @param   {number} [dp=4|2|0] - Number of decimal places to use: default 4 for d, 2 for dm, 0 for dms.
     * @param   {number} [dpHeight=null] - Number of decimal places to use for height; default is no height display.
     * @returns {string} Comma-separated formatted latitude/longitude.
     * @throws  {RangeError} Invalid format.
     *
     * @example
     *   const greenwich = new LatLon(51.47788, -0.00147, 46);
     *   const d = greenwich.toString();                        // 51.4779°N, 000.0015°W
     *   const dms = greenwich.toString('dms', 2);              // 51°28′40″N, 000°00′05″W
     *   const [lat, lon] = greenwich.toString('n').split(','); // 51.4779, -0.0015
     *   const dmsh = greenwich.toString('dms', 0, 0);          // 51°28′40″N, 000°00′06″W +46m
     */
    toString(format='d', dp=undefined, dpHeight=null) {
        // note: explicitly set dp to undefined for passing through to toLat/toLon
        if (![ 'd', 'dm', 'dms', 'n' ].includes(format)) throw new RangeError(`invalid format ‘${format}’`);

        const height = (this.height>=0 ? ' +' : ' ') + this.height.toFixed(dpHeight) + 'm';
        if (format == 'n') { // signed numeric degrees
            if (dp == undefined) dp = 4;
            const lat = this.lat.toFixed(dp);
            const lon = this.lon.toFixed(dp);
            return `${lat}, ${lon}${dpHeight==null ? '' : height}`;
        }

        const lat = Dms.toLat(this.lat, format, dp);
        const lon = Dms.toLon(this.lon, format, dp);

        return `${lat}, ${lon}${dpHeight==null ? '' : height}`;
    }

}

/* Cartesian  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * ECEF (earth-centered earth-fixed) geocentric cartesian coordinates.
 *
 * @extends Vector3d
 */
class Cartesian extends Vector3d {

    /**
     * Creates cartesian coordinate representing ECEF (earth-centric earth-fixed) point.
     *
     * @param {number} x - X coordinate in metres (=> 0°N,0°E).
     * @param {number} y - Y coordinate in metres (=> 0°N,90°E).
     * @param {number} z - Z coordinate in metres (=> 90°N).
     *
     * @example
     *   import { Cartesian } from '/js/geodesy/latlon-ellipsoidal.js';
     *   const coord = new Cartesian(3980581.210, -111.159, 4966824.522);
     */
    constructor(x, y, z) {
        super(x, y, z); // arguably redundant constructor, but specifies units & axes
    }


    /**
     * Converts ‘this’ (geocentric) cartesian (x/y/z) coordinate to (geodetic) latitude/longitude
     * point on specified ellipsoid.
     *
     * Uses Bowring’s (1985) formulation for μm precision in concise form; ‘The accuracy of geodetic
     * latitude and height equations’, B R Bowring, Survey Review vol 28, 218, Oct 1985.
     *
     * @param   {LatLon.ellipsoids} [ellipsoid=WGS84] - Ellipsoid to use when converting point.
     * @returns {LatLon} Latitude/longitude point defined by cartesian coordinates, on given ellipsoid.
     * @throws  {TypeError} Invalid ellipsoid.
     *
     * @example
     *   const c = new Cartesian(4027893.924, 307041.993, 4919474.294);
     *   const p = c.toLatLon(); // 50.7978°N, 004.3592°E
     */
    toLatLon(ellipsoid=ellipsoids.WGS84) {
        // note ellipsoid is available as a parameter for when toLatLon gets subclassed to
        // Ellipsoidal_Datum / Ellipsoidal_Referenceframe.
        if (!ellipsoid || !ellipsoid.a) throw new TypeError(`invalid ellipsoid ‘${ellipsoid}’`);

        const { x, y, z } = this;
        const { a, b, f } = ellipsoid;

        const e2 = 2*f - f*f;           // 1st eccentricity squared ≡ (a²−b²)/a²
        const ε2 = e2 / (1-e2);         // 2nd eccentricity squared ≡ (a²−b²)/b²
        const p = Math.sqrt(x*x + y*y); // distance from minor axis
        const R = Math.sqrt(p*p + z*z); // polar radius

        // parametric latitude (Bowring eqn.17, replacing tanβ = z·a / p·b)
        const tanβ = (b*z)/(a*p) * (1+ε2*b/R);
        const sinβ = tanβ / Math.sqrt(1+tanβ*tanβ);
        const cosβ = sinβ / tanβ;

        // geodetic latitude (Bowring eqn.18: tanφ = z+ε²⋅b⋅sin³β / p−e²⋅cos³β)
        const φ = isNaN(cosβ) ? 0 : Math.atan2(z + ε2*b*sinβ*sinβ*sinβ, p - e2*a*cosβ*cosβ*cosβ);

        // longitude
        const λ = Math.atan2(y, x);

        // height above ellipsoid (Bowring eqn.7)
        const sinφ = Math.sin(φ), cosφ = Math.cos(φ);
        const ν = a / Math.sqrt(1-e2*sinφ*sinφ); // length of the normal terminated by the minor axis
        const h = p*cosφ + z*sinφ - (a*a/ν);

        const point = new LatLonEllipsoidal(φ.toDegrees(), λ.toDegrees(), h);

        return point;
    }


    /**
     * Returns a string representation of ‘this’ cartesian point.
     *
     * @param   {number} [dp=0] - Number of decimal places to use.
     * @returns {string} Comma-separated latitude/longitude.
     */
    toString(dp=0) {
        const x = this.x.toFixed(dp), y = this.y.toFixed(dp), z = this.z.toFixed(dp);
        return `[${x},${y},${z}]`;
    }

}

//export { LatLonEllipsoidal as default, Cartesian, Vector3d, Dms };

//==========================================================================================
// latlon-ellipsoidal-datum.js
//==========================================================================================
//import LatLonEllipsoidal, { Cartesian, Dms } from './latlon-ellipsoidal.js';

/**
 * Historical geodetic datums: a latitude/longitude point defines a geographic location on or
 * above/below the  earth’s surface, measured in degrees from the equator & the International
 * Reference Meridian and metres above the ellipsoid, and based on a given datum. The datum is
 * based on a reference ellipsoid and tied to geodetic survey reference points.
 *
 * Modern geodesy is generally based on the WGS84 datum (as used for instance by GPS systems), but
 * previously various reference ellipsoids and datum references were used.
 *
 * This module extends the core latlon-ellipsoidal module to include ellipsoid parameters and datum
 * transformation parameters, and methods for converting between different (generally historical)
 * datums.
 *
 * It can be used for UK Ordnance Survey mapping (OS National Grid References are still based on the
 * otherwise historical OSGB36 datum), as well as for historical purposes.
 *
 * q.v. Ordnance Survey ‘A guide to coordinate systems in Great Britain’ Section 6,
 * www.ordnancesurvey.co.uk/docs/support/guide-coordinate-systems-great-britain.pdf, and also
 * www.ordnancesurvey.co.uk/blog/2014/12/2.
 *
 * @module latlon-ellipsoidal-datum
 */

/*
 * Ellipsoid parameters; exposed through static getter below.
 *
const ellipsoids = {
    WGS84:         { a: 6378137,     b: 6356752.314245, f: 1/298.257223563 },
    Airy1830:      { a: 6377563.396, b: 6356256.909,    f: 1/299.3249646   },
    AiryModified:  { a: 6377340.189, b: 6356034.448,    f: 1/299.3249646   },
    Bessel1841:    { a: 6377397.155, b: 6356078.962818, f: 1/299.1528128   },
    Clarke1866:    { a: 6378206.4,   b: 6356583.8,      f: 1/294.978698214 },
    Clarke1880IGN: { a: 6378249.2,   b: 6356515.0,      f: 1/293.466021294 },
    GRS80:         { a: 6378137,     b: 6356752.314140, f: 1/298.257222101 },
    Intl1924:      { a: 6378388,     b: 6356911.946,    f: 1/297           }, // aka Hayford
    WGS72:         { a: 6378135,     b: 6356750.5,      f: 1/298.26        },
};


/*
 * Datums; exposed through static getter below.
 *
const datums = {
    // transforms: t in metres, s in ppm, r in arcseconds              tx       ty        tz       s        rx        ry        rz
    ED50:       { ellipsoid: ellipsoids.Intl1924,      transform: [   89.5,    93.8,    123.1,    -1.2,     0.0,      0.0,      0.156    ] }, // epsg.io/1311
    ETRS89:     { ellipsoid: ellipsoids.GRS80,         transform: [    0,       0,        0,       0,       0,        0,        0        ] }, // epsg.io/1149; @ 1-metre level
    Irl1975:    { ellipsoid: ellipsoids.AiryModified,  transform: [ -482.530, 130.596, -564.557,  -8.150,   1.042,    0.214,    0.631    ] }, // epsg.io/1954
    NAD27:      { ellipsoid: ellipsoids.Clarke1866,    transform: [    8,    -160,     -176,       0,       0,        0,        0        ] },
    NAD83:      { ellipsoid: ellipsoids.GRS80,         transform: [    0.9956, -1.9103,  -0.5215, -0.00062, 0.025915, 0.009426, 0.011599 ] },
    NTF:        { ellipsoid: ellipsoids.Clarke1880IGN, transform: [  168,      60,     -320,       0,       0,        0,        0        ] },
    OSGB36:     { ellipsoid: ellipsoids.Airy1830,      transform: [ -446.448, 125.157, -542.060,  20.4894, -0.1502,  -0.2470,  -0.8421   ] }, // epsg.io/1314
    Potsdam:    { ellipsoid: ellipsoids.Bessel1841,    transform: [ -582,    -105,     -414,      -8.3,     1.04,     0.35,    -3.08     ] },
    TokyoJapan: { ellipsoid: ellipsoids.Bessel1841,    transform: [  148,    -507,     -685,       0,       0,        0,        0        ] },
    WGS72:      { ellipsoid: ellipsoids.WGS72,         transform: [    0,       0,       -4.5,    -0.22,    0,        0,        0.554    ] },
    WGS84:      { ellipsoid: ellipsoids.WGS84,         transform: [    0.0,     0.0,      0.0,     0.0,     0.0,      0.0,      0.0      ] },
};*/
/* sources:
 * - ED50:       www.gov.uk/guidance/oil-and-gas-petroleum-operations-notices#pon-4
 * - Irl1975:    www.osi.ie/wp-content/uploads/2015/05/transformations_booklet.pdf
 * - NAD27:      en.wikipedia.org/wiki/Helmert_transformation
 * - NAD83:      www.uvm.edu/giv/resources/WGS84_NAD83.pdf [strictly, WGS84(G1150) -> NAD83(CORS96) @ epoch 1997.0]
 *               (note NAD83(1986) ≡ WGS84(Original); confluence.qps.nl/pages/viewpage.action?pageId=29855173)
 * - NTF:        Nouvelle Triangulation Francaise geodesie.ign.fr/contenu/fichiers/Changement_systeme_geodesique.pdf
 * - OSGB36:     www.ordnancesurvey.co.uk/docs/support/guide-coordinate-systems-great-britain.pdf
 * - Potsdam:    kartoweb.itc.nl/geometrics/Coordinate%20transformations/coordtrans.html
 * - TokyoJapan: www.geocachingtoolbox.com?page=datumEllipsoidDetails
 * - WGS72:      www.icao.int/safety/pbn/documentation/eurocontrol/eurocontrol wgs 84 implementation manual.pdf
 *
 * more transform parameters are available from earth-info.nga.mil/GandG/coordsys/datums/NATO_DT.pdf,
 * www.fieldenmaps.info/cconv/web/cconv_params.js
 */
/* note:
 * - ETRS89 reference frames are coincident with WGS-84 at epoch 1989.0 (ie null transform) at the one metre level.
 */


// freeze static properties
Object.keys(ellipsoids).forEach(e => Object.freeze(ellipsoids[e]));
Object.keys(datums).forEach(d => { Object.freeze(datums[d]); Object.freeze(datums[d].transform); });

/* LatLon - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Latitude/longitude points on an ellipsoidal model earth, with ellipsoid parameters and methods
 * for converting between datums and to geocentric (ECEF) cartesian coordinates.
 *
 * @extends LatLonEllipsoidal
 */
class LatLonEllipsoidal_Datum extends LatLonEllipsoidal {

    /**
     * Creates a geodetic latitude/longitude point on an ellipsoidal model earth using given datum.
     *
     * @param {number} lat - Latitude (in degrees).
     * @param {number} lon - Longitude (in degrees).
     * @param {number} [height=0] - Height above ellipsoid in metres.
     * @param {LatLon.datums} datum - Datum this point is defined within.
     *
     * @example
     *   import LatLon from '/js/geodesy/latlon-ellipsoidal-datum.js';
     *   const p = new LatLon(53.3444, -6.2577, 17, LatLon.datums.Irl1975);
     */
    constructor(lat, lon, height=0, datum=datums.WGS84) {
        if (!datum || datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);

        super(lat, lon, height);

        this._datum = datum;
    }


    /**
     * Datum this point is defined within.
     */
    get datum() {
        return this._datum;
    }


    /**
     * Ellipsoids with their parameters; semi-major axis (a), semi-minor axis (b), and flattening (f).
     *
     * Flattening f = (a−b)/a; at least one of these parameters is derived from defining constants.
     *
     * @example
     *   const a = LatLon.ellipsoids.Airy1830.a; // 6377563.396
     */
    static get ellipsoids() {
        return ellipsoids;
    }


    /**
     * Datums; with associated ellipsoid, and Helmert transform parameters to convert from WGS-84
     * into given datum.
     *
     * Note that precision of various datums will vary, and WGS-84 (original) is not defined to be
     * accurate to better than ±1 metre. No transformation should be assumed to be accurate to
     * better than a metre, for many datums somewhat less.
     *
     * This is a small sample of commoner datums from a large set of historical datums. I will add
     * new datums on request.
     *
     * @example
     *   const a = LatLon.datums.OSGB36.ellipsoid.a;                    // 6377563.396
     *   const tx = LatLon.datums.OSGB36.transform;                     // [ tx, ty, tz, s, rx, ry, rz ]
     *   const availableDatums = Object.keys(LatLon.datums).join(', '); // ED50, Irl1975, NAD27, ...
     */
    static get datums() {
        return datums;
    }


    // note instance datum getter/setters are in LatLonEllipsoidal


    /**
     * Parses a latitude/longitude point from a variety of formats.
     *
     * Latitude & longitude (in degrees) can be supplied as two separate parameters, as a single
     * comma-separated lat/lon string, or as a single object with { lat, lon } or GeoJSON properties.
     *
     * The latitude/longitude values may be numeric or strings; they may be signed decimal or
     * deg-min-sec (hexagesimal) suffixed by compass direction (NSEW); a variety of separators are
     * accepted. Examples -3.62, '3 37 12W', '3°37′12″W'.
     *
     * Thousands/decimal separators must be comma/dot; use Dms.fromLocale to convert locale-specific
     * thousands/decimal separators.
     *
     * @param   {number|string|Object} lat|latlon - Geodetic Latitude (in degrees) or comma-separated lat/lon or lat/lon object.
     * @param   {number}               [lon] - Longitude in degrees.
     * @param   {number}               [height=0] - Height above ellipsoid in metres.
     * @param   {LatLon.datums}        [datum=WGS84] - Datum this point is defined within.
     * @returns {LatLon} Latitude/longitude point on ellipsoidal model earth using given datum.
     * @throws  {TypeError} Unrecognised datum.
     *
     * @example
     *   const p = LatLon.parse('51.47736, 0.0000', 0, LatLon.datums.OSGB36);
     */
    static parse(...args) {
        let datum = datums.WGS84;

        // if the last argument is a datum, use that, otherwise use default WGS-84
        if (args.length==4 || (args.length==3 && typeof args[2] == 'object')) datum = args.pop();

        if (!datum || datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);

        const point = super.parse(...args);

        point._datum = datum;

        return point;
    }


    /**
     * Converts ‘this’ lat/lon coordinate to new coordinate system.
     *
     * @param   {LatLon.datums} toDatum - Datum this coordinate is to be converted to.
     * @returns {LatLon} This point converted to new datum.
     * @throws  {TypeError} Unrecognised datum.
     *
     * @example
     *   const pWGS84 = new LatLon(51.47788, -0.00147, 0, LatLon.datums.WGS84);
     *   const pOSGB = pWGS84.convertDatum(LatLon.datums.OSGB36); // 51.4773°N, 000.0001°E
     */
    convertDatum(toDatum) {
        if (!toDatum || toDatum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${toDatum}’`);

        const oldCartesian = this.toCartesian();                 // convert geodetic to cartesian
        const newCartesian = oldCartesian.convertDatum(toDatum); // convert datum
        const newLatLon = newCartesian.toLatLon();               // convert cartesian back to geodetic

        return newLatLon;
    }


    /**
     * Converts ‘this’ point from (geodetic) latitude/longitude coordinates to (geocentric) cartesian
     * (x/y/z) coordinates, based on the same datum.
     *
     * Shadow of LatLonEllipsoidal.toCartesian(), returning Cartesian augmented with
     * LatLonEllipsoidal_Datum methods/properties.
     *
     * @returns {Cartesian} Cartesian point equivalent to lat/lon point, with x, y, z in metres from
     *   earth centre, augmented with reference frame conversion methods and properties.
     */
    toCartesian() {
        const cartesian = super.toCartesian();
        const cartesianDatum = new Cartesian_Datum(cartesian.x, cartesian.y, cartesian.z, this.datum);
        return cartesianDatum;
    }

}

/* Cartesian  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Augments Cartesian with datum the cooordinate is based on, and methods to convert between datums
 * (using Helmert 7-parameter transforms) and to convert cartesian to geodetic latitude/longitude
 * point.
 *
 * @extends Cartesian
 */
class Cartesian_Datum extends Cartesian {

    /**
     * Creates cartesian coordinate representing ECEF (earth-centric earth-fixed) point, on a given
     * datum. The datum will identify the primary meridian (for the x-coordinate), and is also
     * useful in transforming to/from geodetic (lat/lon) coordinates.
     *
     * @param  {number} x - X coordinate in metres (=> 0°N,0°E).
     * @param  {number} y - Y coordinate in metres (=> 0°N,90°E).
     * @param  {number} z - Z coordinate in metres (=> 90°N).
     * @param  {LatLon.datums} [datum] - Datum this coordinate is defined within.
     * @throws {TypeError} Unrecognised datum.
     *
     * @example
     *   import { Cartesian } from '/js/geodesy/latlon-ellipsoidal-datum.js';
     *   const coord = new Cartesian(3980581.210, -111.159, 4966824.522);
     */
    constructor(x, y, z, datum=undefined) {
        if (datum && datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);

        super(x, y, z);

        if (datum) this._datum = datum;
    }


    /**
     * Datum this point is defined within.
     */
    get datum() {
        return this._datum;
    }
    set datum(datum) {
        if (!datum || datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);
        this._datum = datum;
    }


    /**
     * Converts ‘this’ (geocentric) cartesian (x/y/z) coordinate to (geodetic) latitude/longitude
     * point (based on the same datum, or WGS84 if unset).
     *
     * Shadow of Cartesian.toLatLon(), returning LatLon augmented with LatLonEllipsoidal_Datum
     * methods convertDatum, toCartesian, etc.
     *
     * @returns {LatLon} Latitude/longitude point defined by cartesian coordinates.
     * @throws  {TypeError} Unrecognised datum
     *
     * @example
     *   const c = new Cartesian(4027893.924, 307041.993, 4919474.294);
     *   const p = c.toLatLon(); // 50.7978°N, 004.3592°E
     */
    toLatLon(deprecatedDatum=undefined) {
        if (deprecatedDatum) {
            console.info('datum parameter to Cartesian_Datum.toLatLon is deprecated: set datum before calling toLatLon()');
            this.datum = deprecatedDatum;
        }
        const datum = this.datum || datums.WGS84;
        if (!datum || datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);

        const latLon = super.toLatLon(datum.ellipsoid); // TODO: what if datum is not geocentric?
        const point = new LatLonEllipsoidal_Datum(latLon.lat, latLon.lon, latLon.height, this.datum);
        return point;
    }


    /**
     * Converts ‘this’ cartesian coordinate to new datum using Helmert 7-parameter transformation.
     *
     * @param   {LatLon.datums} toDatum - Datum this coordinate is to be converted to.
     * @returns {Cartesian} This point converted to new datum.
     * @throws  {Error} Undefined datum.
     *
     * @example
     *   const c = new Cartesian(3980574.247, -102.127, 4966830.065, LatLon.datums.OSGB36);
     *   c.convertDatum(LatLon.datums.Irl1975); // [??,??,??]
     */
    convertDatum(toDatum) {
        // TODO: what if datum is not geocentric?
        if (!toDatum || toDatum.ellipsoid == undefined) throw new TypeError(`unrecognised datum ‘${toDatum}’`);
        if (!this.datum) throw new TypeError('cartesian coordinate has no datum');

        let oldCartesian = null;
        let transform = null;

        if (this.datum == undefined || this.datum == datums.WGS84) {
            // converting from WGS 84
            oldCartesian = this;
            transform = toDatum.transform;
        }
        if (toDatum == datums.WGS84) {
            // converting to WGS 84; use inverse transform
            oldCartesian = this;
            transform = this.datum.transform.map(p => -p);
        }
        if (transform == null) {
            // neither this.datum nor toDatum are WGS84: convert this to WGS84 first
            oldCartesian = this.convertDatum(datums.WGS84);
            transform = toDatum.transform;
        }

        const newCartesian = oldCartesian.applyTransform(transform);
        newCartesian.datum = toDatum;

        return newCartesian;
    }


    /**
     * Applies Helmert 7-parameter transformation to ‘this’ coordinate using transform parameters t.
     *
     * This is used in converting datums (geodetic->cartesian, apply transform, cartesian->geodetic).
     *
     * @private
     * @param   {number[]} t - Transformation to apply to this coordinate.
     * @returns {Cartesian} Transformed point.
     */
    applyTransform(t)   {
        // this point
        const { x: x1, y: y1, z: z1 } = this;

        // transform parameters
        const tx = t[0];                    // x-shift in metres
        const ty = t[1];                    // y-shift in metres
        const tz = t[2];                    // z-shift in metres
        const s  = t[3]/1e6 + 1;            // scale: normalise parts-per-million to (s+1)
        const rx = (t[4]/3600).toRadians(); // x-rotation: normalise arcseconds to radians
        const ry = (t[5]/3600).toRadians(); // y-rotation: normalise arcseconds to radians
        const rz = (t[6]/3600).toRadians(); // z-rotation: normalise arcseconds to radians

        // apply transform
        const x2 = tx + x1*s  - y1*rz + z1*ry;
        const y2 = ty + x1*rz + y1*s  - z1*rx;
        const z2 = tz - x1*ry + y1*rx + z1*s;

        return new Cartesian_Datum(x2, y2, z2);
    }
}

//export { LatLonEllipsoidal_Datum as default, Cartesian_Datum as Cartesian, datums, Dms };

//==========================================================================================
// dms.js
//==========================================================================================
// eslint no-irregular-whitespace: [2, { skipComments: true }]


/**
 * Latitude/longitude points may be represented as decimal degrees, or subdivided into sexagesimal
 * minutes and seconds. This module provides methods for parsing and representing degrees / minutes
 * / seconds.
 *
 * @module dms
 */


// Degree-minutes-seconds (& cardinal directions) separator character
let dmsSeparator = '\u202f'; // U+202F = 'narrow no-break space'


/**
 * Functions for parsing and representing degrees / minutes / seconds.
 */
class Dms {

    // note Unicode Degree = U+00B0. Prime = U+2032, Double prime = U+2033

    /**
     * Separator character to be used to separate degrees, minutes, seconds, and cardinal directions.
     *
     * Default separator is U+202F ‘narrow no-break space’.
     *
     * To change this (e.g. to empty string or full space), set Dms.separator prior to invoking
     * formatting.
     *
     * @example
     *   import LatLon, { Dms } from '/js/geodesy/latlon-spherical.js';
     *   const p = new LatLon(51.2, 0.33).toString('dms');  // 51° 12′ 00″ N, 000° 19′ 48″ E
     *   Dms.separator = '';                                // no separator
     *   const pʹ = new LatLon(51.2, 0.33).toString('dms'); // 51°12′00″N, 000°19′48″E
     */
    static get separator()     { return dmsSeparator; }
    static set separator(char) { dmsSeparator = char; }


    /**
     * Parses string representing degrees/minutes/seconds into numeric degrees.
     *
     * This is very flexible on formats, allowing signed decimal degrees, or deg-min-sec optionally
     * suffixed by compass direction (NSEW); a variety of separators are accepted. Examples -3.62,
     * '3 37 12W', '3°37′12″W'.
     *
     * Thousands/decimal separators must be comma/dot; use Dms.fromLocale to convert locale-specific
     * thousands/decimal separators.
     *
     * @param   {string|number} dms - Degrees or deg/min/sec in variety of formats.
     * @returns {number}        Degrees as decimal number.
     *
     * @example
     *   const lat = Dms.parse('51° 28′ 40.37″ N');
     *   const lon = Dms.parse('000° 00′ 05.29″ W');
     *   const p1 = new LatLon(lat, lon); // 51.4779°N, 000.0015°W
     */
    static parse(dms) {
        // check for signed decimal degrees without NSEW, if so return it directly
        if (!isNaN(parseFloat(dms)) && isFinite(dms)) return Number(dms);

        // strip off any sign or compass dir'n & split out separate d/m/s
        const dmsParts = String(dms).trim().replace(/^-/, '').replace(/[NSEW]$/i, '').split(/[^0-9.,]+/);
        if (dmsParts[dmsParts.length-1]=='') dmsParts.splice(dmsParts.length-1);  // from trailing symbol

        if (dmsParts == '') return NaN;

        // and convert to decimal degrees...
        let deg = null;
        switch (dmsParts.length) {
            case 3:  // interpret 3-part result as d/m/s
                deg = dmsParts[0]/1 + dmsParts[1]/60 + dmsParts[2]/3600;
                break;
            case 2:  // interpret 2-part result as d/m
                deg = dmsParts[0]/1 + dmsParts[1]/60;
                break;
            case 1:  // just d (possibly decimal) or non-separated dddmmss
                deg = dmsParts[0];
                // check for fixed-width unseparated format eg 0033709W
                //if (/[NS]/i.test(dmsParts)) deg = '0' + deg;  // - normalise N/S to 3-digit degrees
                //if (/[0-9]{7}/.test(deg)) deg = deg.slice(0,3)/1 + deg.slice(3,5)/60 + deg.slice(5)/3600;
                break;
            default:
                return NaN;
        }
        if (/^-|[WS]$/i.test(String(dms).trim())) deg = -deg; // take '-', west and south as -ve

        return Number(deg);
    }


    /**
     * Converts decimal degrees to deg/min/sec format
     *  - degree, prime, double-prime symbols are added, but sign is discarded, though no compass
     *    direction is added.
     *  - degrees are zero-padded to 3 digits; for degrees latitude, use .slice(1) to remove leading
     *    zero.
     *
     * @private
     * @param   {number} deg - Degrees to be formatted as specified.
     * @param   {string} [format=d] - Return value as 'd', 'dm', 'dms' for deg, deg+min, deg+min+sec.
     * @param   {number} [dp=4|2|0] - Number of decimal places to use – default 4 for d, 2 for dm, 0 for dms.
     * @returns {string} Degrees formatted as deg/min/secs according to specified format.
     */
    static toDms(deg, format='d', dp=undefined) {
        if (isNaN(deg)) return null;  // give up here if we can't make a number from deg
        if (typeof deg == 'string' && deg.trim() == '') return null;
        if (typeof deg == 'boolean') return null;
        if (deg == Infinity) return null;
        if (deg == null) return null;

        // default values
        if (dp === undefined) {
            switch (format) {
                case 'd':   case 'deg':         dp = 4; break;
                case 'dm':  case 'deg+min':     dp = 2; break;
                case 'dms': case 'deg+min+sec': dp = 0; break;
                default:          format = 'd'; dp = 4; break; // be forgiving on invalid format
            }
        }

        deg = Math.abs(deg);  // (unsigned result ready for appending compass dir'n)

        let dms = null, d = null, m = null, s = null;
        switch (format) {
            default: // invalid format spec!
            case 'd': case 'deg':
                d = deg.toFixed(dp);                       // round/right-pad degrees
                if (d<100) d = '0' + d;                    // left-pad with leading zeros (note may include decimals)
                if (d<10) d = '0' + d;
                dms = d + '°';
                break;
            case 'dm': case 'deg+min':
                d = Math.floor(deg);                       // get component deg
                m = ((deg*60) % 60).toFixed(dp);           // get component min & round/right-pad
                if (m == 60) { m = (0).toFixed(dp); d++; } // check for rounding up
                d = ('000'+d).slice(-3);                   // left-pad with leading zeros
                if (m<10) m = '0' + m;                     // left-pad with leading zeros (note may include decimals)
                dms = d + '°'+Dms.separator + m + '′';
                break;
            case 'dms': case 'deg+min+sec':
                d = Math.floor(deg);                       // get component deg
                m = Math.floor((deg*3600)/60) % 60;        // get component min
                s = (deg*3600 % 60).toFixed(dp);           // get component sec & round/right-pad
                if (s == 60) { s = (0).toFixed(dp); m++; } // check for rounding up
                if (m == 60) { m = 0; d++; }               // check for rounding up
                d = ('000'+d).slice(-3);                   // left-pad with leading zeros
                m = ('00'+m).slice(-2);                    // left-pad with leading zeros
                if (s<10) s = '0' + s;                     // left-pad with leading zeros (note may include decimals)
                dms = d + '°'+Dms.separator + m + '′'+Dms.separator + s + '″';
                break;
        }

        return dms;
    }


    /**
     * Converts numeric degrees to deg/min/sec latitude (2-digit degrees, suffixed with N/S).
     *
     * @param   {number} deg - Degrees to be formatted as specified.
     * @param   {string} [format=d] - Return value as 'd', 'dm', 'dms' for deg, deg+min, deg+min+sec.
     * @param   {number} [dp=4|2|0] - Number of decimal places to use – default 4 for d, 2 for dm, 0 for dms.
     * @returns {string} Degrees formatted as deg/min/secs according to specified format.
     *
     * @example
     *   const lat = Dms.toLat(-3.62, 'dms'); // 3°37′12″S
     */
    static toLat(deg, format, dp) {
        const lat = Dms.toDms(Dms.wrap90(deg), format, dp);
        return lat===null ? '–' : lat.slice(1) + Dms.separator + (deg<0 ? 'S' : 'N');  // knock off initial '0' for lat!
    }


    /**
     * Convert numeric degrees to deg/min/sec longitude (3-digit degrees, suffixed with E/W).
     *
     * @param   {number} deg - Degrees to be formatted as specified.
     * @param   {string} [format=d] - Return value as 'd', 'dm', 'dms' for deg, deg+min, deg+min+sec.
     * @param   {number} [dp=4|2|0] - Number of decimal places to use – default 4 for d, 2 for dm, 0 for dms.
     * @returns {string} Degrees formatted as deg/min/secs according to specified format.
     *
     * @example
     *   const lon = Dms.toLon(-3.62, 'dms'); // 3°37′12″W
     */
    static toLon(deg, format, dp) {
        const lon = Dms.toDms(Dms.wrap180(deg), format, dp);
        return lon===null ? '–' : lon + Dms.separator + (deg<0 ? 'W' : 'E');
    }


    /**
     * Converts numeric degrees to deg/min/sec as a bearing (0°..360°).
     *
     * @param   {number} deg - Degrees to be formatted as specified.
     * @param   {string} [format=d] - Return value as 'd', 'dm', 'dms' for deg, deg+min, deg+min+sec.
     * @param   {number} [dp=4|2|0] - Number of decimal places to use – default 4 for d, 2 for dm, 0 for dms.
     * @returns {string} Degrees formatted as deg/min/secs according to specified format.
     *
     * @example
     *   const lon = Dms.toBrng(-3.62, 'dms'); // 356°22′48″
     */
    static toBrng(deg, format, dp) {
        const brng =  Dms.toDms(Dms.wrap360(deg), format, dp);
        return brng===null ? '–' : brng.replace('360', '0');  // just in case rounding took us up to 360°!
    }


    /**
     * Converts DMS string from locale thousands/decimal separators to JavaScript comma/dot separators
     * for subsequent parsing.
     *
     * Both thousands and decimal separators must be followed by a numeric character, to facilitate
     * parsing of single lat/long string (in which whitespace must be left after the comma separator).
     *
     * @param   {string} str - Degrees/minutes/seconds formatted with locale separators.
     * @returns {string} Degrees/minutes/seconds formatted with standard Javascript separators.
     *
     * @example
     *   const lat = Dms.fromLocale('51°28′40,12″N');                          // '51°28′40.12″N' in France
     *   const p = new LatLon(Dms.fromLocale('51°28′40,37″N, 000°00′05,29″W'); // '51.4779°N, 000.0015°W' in France
     */
    static fromLocale(str) {
        const locale = (123456.789).toLocaleString();
        const separator = { thousands: locale.slice(3, 4), decimal: locale.slice(7, 8) };
        return str.replace(separator.thousands, '⁜').replace(separator.decimal, '.').replace('⁜', ',');
    }


    /**
     * Converts DMS string from JavaScript comma/dot thousands/decimal separators to locale separators.
     *
     * Can also be used to format standard numbers such as distances.
     *
     * @param   {string} str - Degrees/minutes/seconds formatted with standard Javascript separators.
     * @returns {string} Degrees/minutes/seconds formatted with locale separators.
     *
     * @example
     *   const Dms.toLocale('123,456.789');                   // '123.456,789' in France
     *   const Dms.toLocale('51°28′40.12″N, 000°00′05.31″W'); // '51°28′40,12″N, 000°00′05,31″W' in France
     */
    static toLocale(str) {
        const locale = (123456.789).toLocaleString();
        const separator = { thousands: locale.slice(3, 4), decimal: locale.slice(7, 8) };
        return str.replace(/,([0-9])/, '⁜$1').replace('.', separator.decimal).replace('⁜', separator.thousands);
    }


    /**
     * Returns compass point (to given precision) for supplied bearing.
     *
     * @param   {number} bearing - Bearing in degrees from north.
     * @param   {number} [precision=3] - Precision (1:cardinal / 2:intercardinal / 3:secondary-intercardinal).
     * @returns {string} Compass point for supplied bearing.
     *
     * @example
     *   const point = Dms.compassPoint(24);    // point = 'NNE'
     *   const point = Dms.compassPoint(24, 1); // point = 'N'
     */
    static compassPoint(bearing, precision=3) {
        if (![ 1, 2, 3 ].includes(Number(precision))) throw new RangeError(`invalid precision ‘${precision}’`);
        // note precision could be extended to 4 for quarter-winds (eg NbNW), but I think they are little used

        bearing = Dms.wrap360(bearing); // normalise to range 0..360°

        const cardinals = [
            'N', 'NNE', 'NE', 'ENE',
            'E', 'ESE', 'SE', 'SSE',
            'S', 'SSW', 'SW', 'WSW',
            'W', 'WNW', 'NW', 'NNW' ];
        const n = 4 * 2**(precision-1); // no of compass points at req’d precision (1=>4, 2=>8, 3=>16)
        const cardinal = cardinals[Math.round(bearing*n/360)%n * 16/n];

        return cardinal;
    }


    /**
     * Constrain degrees to range 0..360 (e.g. for bearings); -1 => 359, 361 => 1.
     *
     * @private
     * @param {number} degrees
     * @returns degrees within range 0..360.
     */
    static wrap360(degrees) {
        if (0<=degrees && degrees<360) return degrees; // avoid rounding due to arithmetic ops if within range
        return (degrees%360+360) % 360; // sawtooth wave p:360, a:360
    }

    /**
     * Constrain degrees to range -180..+180 (e.g. for longitude); -181 => 179, 181 => -179.
     *
     * @private
     * @param {number} degrees
     * @returns degrees within range -180..+180.
     */
    static wrap180(degrees) {
        if (-180<degrees && degrees<=180) return degrees; // avoid rounding due to arithmetic ops if within range
        return (degrees+540)%360-180; // sawtooth wave p:180, a:±180
    }

    /**
     * Constrain degrees to range -90..+90 (e.g. for latitude); -91 => -89, 91 => 89.
     *
     * @private
     * @param {number} degrees
     * @returns degrees within range -90..+90.
     */
    static wrap90(degrees) {
        if (-90<=degrees && degrees<=90) return degrees; // avoid rounding due to arithmetic ops if within range
        return Math.abs((degrees%360 + 270)%360 - 180) - 90; // triangle wave p:360 a:±90 TODO: fix e.g. -315°
    }

}


// Extend Number object with methods to convert between degrees & radians
Number.prototype.toRadians = function() { return this * Math.PI / 180; };
Number.prototype.toDegrees = function() { return this * 180 / Math.PI; };

//export default Dms;

//==========================================================================================
// utm.js
//==========================================================================================
/* eslint-disable indent */

//import LatLonEllipsoidal, { Dms } from './latlon-ellipsoidal-datum.js';

/**
 * The Universal Transverse Mercator (UTM) system is a 2-dimensional Cartesian coordinate system
 * providing locations on the surface of the Earth.
 *
 * UTM is a set of 60 transverse Mercator projections, normally based on the WGS-84 ellipsoid.
 * Within each zone, coordinates are represented as eastings and northings, measures in metres; e.g.
 * ‘31 N 448251 5411932’.
 *
 * This method based on Karney 2011 ‘Transverse Mercator with an accuracy of a few nanometers’,
 * building on Krüger 1912 ‘Konforme Abbildung des Erdellipsoids in der Ebene’.
 *
 * @module utm
 */

/* Utm  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * UTM coordinates, with functions to parse them and convert them to LatLon points.
 */
class Utm {

    /**
     * Creates a Utm coordinate object comprising zone, hemisphere, easting, northing on a given
     * datum (normally WGS84).
     *
     * @param  {number}        zone - UTM 6° longitudinal zone (1..60 covering 180°W..180°E).
     * @param  {string}        hemisphere - N for northern hemisphere, S for southern hemisphere.
     * @param  {number}        easting - Easting in metres from false easting (-500km from central meridian).
     * @param  {number}        northing - Northing in metres from equator (N) or from false northing -10,000km (S).
     * @param  {LatLon.datums} [datum=WGS84] - Datum UTM coordinate is based on.
     * @param  {number}        [convergence=null] - Meridian convergence (bearing of grid north
     *                         clockwise from true north), in degrees.
     * @param  {number}        [scale=null] - Grid scale factor.
     * @params {boolean=true}  verifyEN - Check easting/northing is within 'normal' values (may be
     *                         suppressed for extended coherent coordinates or alternative datums
     *                         e.g. ED50 (epsg.io/23029).
     * @throws {TypeError} Invalid UTM coordinate.
     *
     * @example
     *   import Utm from '/js/geodesy/utm.js';
     *   const utmCoord = new Utm(31, 'N', 448251, 5411932);
     */
    constructor(zone, hemisphere, easting, northing, datum=LatLonEllipsoidal.datums.WGS84, convergence=null, scale=null, verifyEN=true) {
        if (!(1<=zone && zone<=60)) throw new RangeError(`invalid UTM zone ‘${zone}’`);
        if (zone != parseInt(zone)) throw new RangeError(`invalid UTM zone ‘${zone}’`);
        if (typeof hemisphere != 'string' || !hemisphere.match(/[NS]/i)) throw new RangeError(`invalid UTM hemisphere ‘${hemisphere}’`);
        if (verifyEN) { // range-check E/N values
            if (!(0<=easting && easting<=1000e3)) throw new RangeError(`invalid UTM easting ‘${easting}’`);
            if (hemisphere.toUpperCase()=='N' && !(0<=northing && northing<9328094)) throw new RangeError(`invalid UTM northing ‘${northing}’`);
            if (hemisphere.toUpperCase()=='S' && !(1118414<northing && northing<=10000e3)) throw new RangeError(`invalid UTM northing ‘${northing}’`);
        }
        if (!datum || datum.ellipsoid==undefined) throw new TypeError(`unrecognised datum ‘${datum}’`);

        this.zone = Number(zone);
        this.hemisphere = hemisphere.toUpperCase();
        this.easting = Number(easting);
        this.northing = Number(northing);
        this.datum = datum;
        this.convergence = convergence===null ? null : Number(convergence);
        this.scale = scale===null ? null : Number(scale);
    }


    /**
     * Converts UTM zone/easting/northing coordinate to latitude/longitude.
     *
     * Implements Karney’s method, using Krüger series to order n⁶, giving results accurate to 5nm
     * for distances up to 3900km from the central meridian.
     *
     * @param   {Utm} utmCoord - UTM coordinate to be converted to latitude/longitude.
     * @returns {LatLon} Latitude/longitude of supplied grid reference.
     *
     * @example
     *   const grid = new Utm(31, 'N', 448251.795, 5411932.678);
     *   const latlong = grid.toLatLon(); // 48°51′29.52″N, 002°17′40.20″E
     */
    toLatLon() {
        const { zone: z, hemisphere: h } = this;

        const falseEasting = 500e3, falseNorthing = 10000e3;

        const { a, f } = this.datum.ellipsoid; // WGS-84: a = 6378137, f = 1/298.257223563;

        const k0 = 0.9996; // UTM scale on the central meridian

        const x = this.easting - falseEasting;                            // make x ± relative to central meridian
        const y = h=='S' ? this.northing - falseNorthing : this.northing; // make y ± relative to equator

        // ---- from Karney 2011 Eq 15-22, 36:

        const e = Math.sqrt(f*(2-f)); // eccentricity
        const n = f / (2 - f);        // 3rd flattening
        const n2 = n*n, n3 = n*n2, n4 = n*n3, n5 = n*n4, n6 = n*n5;

        const A = a/(1+n) * (1 + 1/4*n2 + 1/64*n4 + 1/256*n6); // 2πA is the circumference of a meridian

        const η = x / (k0*A);
        const ξ = y / (k0*A);

        const β = [ null, // note β is one-based array (6th order Krüger expressions)
            1/2*n - 2/3*n2 + 37/96*n3 -    1/360*n4 -   81/512*n5 +    96199/604800*n6,
                   1/48*n2 +  1/15*n3 - 437/1440*n4 +   46/105*n5 - 1118711/3870720*n6,
                            17/480*n3 -   37/840*n4 - 209/4480*n5 +      5569/90720*n6,
                                     4397/161280*n4 -   11/504*n5 -  830251/7257600*n6,
                                                   4583/161280*n5 -  108847/3991680*n6,
                                                                 20648693/638668800*n6 ];

        let ξʹ = ξ;
        for (let j=1; j<=6; j++) ξʹ -= β[j] * Math.sin(2*j*ξ) * Math.cosh(2*j*η);

        let ηʹ = η;
        for (let j=1; j<=6; j++) ηʹ -= β[j] * Math.cos(2*j*ξ) * Math.sinh(2*j*η);

        const sinhηʹ = Math.sinh(ηʹ);
        const sinξʹ = Math.sin(ξʹ), cosξʹ = Math.cos(ξʹ);

        const τʹ = sinξʹ / Math.sqrt(sinhηʹ*sinhηʹ + cosξʹ*cosξʹ);

        let δτi = null;
        let τi = τʹ;
        do {
            const σi = Math.sinh(e*Math.atanh(e*τi/Math.sqrt(1+τi*τi)));
            const τiʹ = τi * Math.sqrt(1+σi*σi) - σi * Math.sqrt(1+τi*τi);
            δτi = (τʹ - τiʹ)/Math.sqrt(1+τiʹ*τiʹ)
                * (1 + (1-e*e)*τi*τi) / ((1-e*e)*Math.sqrt(1+τi*τi));
            τi += δτi;
        } while (Math.abs(δτi) > 1e-12); // using IEEE 754 δτi -> 0 after 2-3 iterations
        // note relatively large convergence test as δτi toggles on ±1.12e-16 for eg 31 N 400000 5000000
        const τ = τi;

        const φ = Math.atan(τ);

        let λ = Math.atan2(sinhηʹ, cosξʹ);

        // ---- convergence: Karney 2011 Eq 26, 27

        let p = 1;
        for (let j=1; j<=6; j++) p -= 2*j*β[j] * Math.cos(2*j*ξ) * Math.cosh(2*j*η);
        let q = 0;
        for (let j=1; j<=6; j++) q += 2*j*β[j] * Math.sin(2*j*ξ) * Math.sinh(2*j*η);

        const γʹ = Math.atan(Math.tan(ξʹ) * Math.tanh(ηʹ));
        const γʺ = Math.atan2(q, p);

        const γ = γʹ + γʺ;

        // ---- scale: Karney 2011 Eq 28

        const sinφ = Math.sin(φ);
        const kʹ = Math.sqrt(1 - e*e*sinφ*sinφ) * Math.sqrt(1 + τ*τ) * Math.sqrt(sinhηʹ*sinhηʹ + cosξʹ*cosξʹ);
        const kʺ = A / a / Math.sqrt(p*p + q*q);

        const k = k0 * kʹ * kʺ;

        // ------------

        const λ0 = ((z-1)*6 - 180 + 3).toRadians(); // longitude of central meridian
        λ += λ0; // move λ from zonal to global coordinates

        // round to reasonable precision
        const lat = Number(φ.toDegrees().toFixed(14)); // nm precision (1nm = 10^-14°)
        const lon = Number(λ.toDegrees().toFixed(14)); // (strictly lat rounding should be φ⋅cosφ!)
        const convergence = Number(γ.toDegrees().toFixed(9));
        const scale = Number(k.toFixed(12));

        const latLong = new LatLon_Utm(lat, lon, 0, this.datum);
        // ... and add the convergence and scale into the LatLon object ... wonderful JavaScript!
        latLong.convergence = convergence;
        latLong.scale = scale;

        return latLong;
    }


    /**
     * Parses string representation of UTM coordinate.
     *
     * A UTM coordinate comprises (space-separated)
     *  - zone
     *  - hemisphere
     *  - easting
     *  - northing.
     *
     * @param   {string} utmCoord - UTM coordinate (WGS 84).
     * @param   {Datum}  [datum=WGS84] - Datum coordinate is defined in (default WGS 84).
     * @returns {Utm} Parsed UTM coordinate.
     * @throws  {TypeError} Invalid UTM coordinate.
     *
     * @example
     *   const utmCoord = Utm.parse('31 N 448251 5411932');
     *   // utmCoord: {zone: 31, hemisphere: 'N', easting: 448251, northing: 5411932 }
     */
    static parse(utmCoord, datum=LatLonEllipsoidal.datums.WGS84) {
        // match separate elements (separated by whitespace)
        utmCoord = utmCoord.trim().match(/\S+/g);

        if (utmCoord==null || utmCoord.length!=4) throw new Error(`invalid UTM coordinate ‘${utmCoord}’`);

        const zone = utmCoord[0], hemisphere = utmCoord[1], easting = utmCoord[2], northing = utmCoord[3];

        return new this(zone, hemisphere, easting, northing, datum); // 'new this' as may return subclassed types
    }


    /**
     * Returns a string representation of a UTM coordinate.
     *
     * To distinguish from MGRS grid zone designators, a space is left between the zone and the
     * hemisphere.
     *
     * Note that UTM coordinates get rounded, not truncated (unlike MGRS grid references).
     *
     * @param   {number} [digits=0] - Number of digits to appear after the decimal point (3 ≡ mm).
     * @returns {string} A string representation of the coordinate.
     *
     * @example
     *   const utm = new Utm('31', 'N', 448251, 5411932).toString(4);  // 31 N 448251.0000 5411932.0000
     */
    toString(digits=0) {

        const z = this.zone.toString().padStart(2, '0');
        const h = this.hemisphere;
        const e = this.easting.toFixed(digits);
        const n = this.northing.toFixed(digits);

        return `${z} ${h} ${e} ${n}`;
    }

}

/* LatLon_Utm - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Extends LatLon with method to convert LatLon points to UTM coordinates.
 *
 * @extends LatLon
 */
class LatLon_Utm extends LatLonEllipsoidal {

    /**
     * Converts latitude/longitude to UTM coordinate.
     *
     * Implements Karney’s method, using Krüger series to order n⁶, giving results accurate to 5nm
     * for distances up to 3900km from the central meridian.
     *
     * @param   {number} [zoneOverride] - Use specified zone rather than zone within which point lies;
     *          note overriding the UTM zone has the potential to result in negative eastings, and
     *          perverse results within Norway/Svalbard exceptions.
     * @returns {Utm} UTM coordinate.
     * @throws  {TypeError} Latitude outside UTM limits.
     *
     * @example
     *   const latlong = new LatLon(48.8582, 2.2945);
     *   const utmCoord = latlong.toUtm(); // 31 N 448252 5411933
     */
    toUtm(zoneOverride=undefined) {
        if (!(-80<=this.lat && this.lat<=84)) throw new RangeError(`latitude ‘${this.lat}’ outside UTM limits`);

        const falseEasting = 500e3, falseNorthing = 10000e3;

        let zone = zoneOverride || Math.floor((this.lon+180)/6) + 1; // longitudinal zone
        let λ0 = ((zone-1)*6 - 180 + 3).toRadians(); // longitude of central meridian

        // ---- handle Norway/Svalbard exceptions
        // grid zones are 8° tall; 0°N is offset 10 into latitude bands array
        const mgrsLatBands = 'CDEFGHJKLMNPQRSTUVWXX'; // X is repeated for 80-84°N
        const latBand = mgrsLatBands.charAt(Math.floor(this.lat/8+10));
        // adjust zone & central meridian for Norway
        if (zone==31 && latBand=='V' && this.lon>= 3) { zone++; λ0 += (6).toRadians(); }
        // adjust zone & central meridian for Svalbard
        if (zone==32 && latBand=='X' && this.lon<  9) { zone--; λ0 -= (6).toRadians(); }
        if (zone==32 && latBand=='X' && this.lon>= 9) { zone++; λ0 += (6).toRadians(); }
        if (zone==34 && latBand=='X' && this.lon< 21) { zone--; λ0 -= (6).toRadians(); }
        if (zone==34 && latBand=='X' && this.lon>=21) { zone++; λ0 += (6).toRadians(); }
        if (zone==36 && latBand=='X' && this.lon< 33) { zone--; λ0 -= (6).toRadians(); }
        if (zone==36 && latBand=='X' && this.lon>=33) { zone++; λ0 += (6).toRadians(); }

        const φ = this.lat.toRadians();      // latitude ± from equator
        const λ = this.lon.toRadians() - λ0; // longitude ± from central meridian

        // allow alternative ellipsoid to be specified
        const ellipsoid = this.datum ? this.datum.ellipsoid : LatLonEllipsoidal.ellipsoids.WGS84;
        const { a, f } = ellipsoid; // WGS-84: a = 6378137, f = 1/298.257223563;

        const k0 = 0.9996; // UTM scale on the central meridian

        // ---- easting, northing: Karney 2011 Eq 7-14, 29, 35:

        const e = Math.sqrt(f*(2-f)); // eccentricity
        const n = f / (2 - f);        // 3rd flattening
        const n2 = n*n, n3 = n*n2, n4 = n*n3, n5 = n*n4, n6 = n*n5;

        const cosλ = Math.cos(λ), sinλ = Math.sin(λ), tanλ = Math.tan(λ);

        const τ = Math.tan(φ); // τ ≡ tanφ, τʹ ≡ tanφʹ; prime (ʹ) indicates angles on the conformal sphere
        const σ = Math.sinh(e*Math.atanh(e*τ/Math.sqrt(1+τ*τ)));

        const τʹ = τ*Math.sqrt(1+σ*σ) - σ*Math.sqrt(1+τ*τ);

        const ξʹ = Math.atan2(τʹ, cosλ);
        const ηʹ = Math.asinh(sinλ / Math.sqrt(τʹ*τʹ + cosλ*cosλ));

        const A = a/(1+n) * (1 + 1/4*n2 + 1/64*n4 + 1/256*n6); // 2πA is the circumference of a meridian

        const α = [ null, // note α is one-based array (6th order Krüger expressions)
            1/2*n - 2/3*n2 + 5/16*n3 +   41/180*n4 -     127/288*n5 +      7891/37800*n6,
                  13/48*n2 -  3/5*n3 + 557/1440*n4 +     281/630*n5 - 1983433/1935360*n6,
                           61/240*n3 -  103/140*n4 + 15061/26880*n5 +   167603/181440*n6,
                                   49561/161280*n4 -     179/168*n5 + 6601661/7257600*n6,
                                                     34729/80640*n5 - 3418889/1995840*n6,
                                                                  212378941/319334400*n6 ];

        let ξ = ξʹ;
        for (let j=1; j<=6; j++) ξ += α[j] * Math.sin(2*j*ξʹ) * Math.cosh(2*j*ηʹ);

        let η = ηʹ;
        for (let j=1; j<=6; j++) η += α[j] * Math.cos(2*j*ξʹ) * Math.sinh(2*j*ηʹ);

        let x = k0 * A * η;
        let y = k0 * A * ξ;

        // ---- convergence: Karney 2011 Eq 23, 24

        let pʹ = 1;
        for (let j=1; j<=6; j++) pʹ += 2*j*α[j] * Math.cos(2*j*ξʹ) * Math.cosh(2*j*ηʹ);
        let qʹ = 0;
        for (let j=1; j<=6; j++) qʹ += 2*j*α[j] * Math.sin(2*j*ξʹ) * Math.sinh(2*j*ηʹ);

        const γʹ = Math.atan(τʹ / Math.sqrt(1+τʹ*τʹ)*tanλ);
        const γʺ = Math.atan2(qʹ, pʹ);

        const γ = γʹ + γʺ;

        // ---- scale: Karney 2011 Eq 25

        const sinφ = Math.sin(φ);
        const kʹ = Math.sqrt(1 - e*e*sinφ*sinφ) * Math.sqrt(1 + τ*τ) / Math.sqrt(τʹ*τʹ + cosλ*cosλ);
        const kʺ = A / a * Math.sqrt(pʹ*pʹ + qʹ*qʹ);

        const k = k0 * kʹ * kʺ;

        // ------------

        // shift x/y to false origins
        x = x + falseEasting;             // make x relative to false easting
        if (y < 0) y = y + falseNorthing; // make y in southern hemisphere relative to false northing

        // round to reasonable precision
        x = Number(x.toFixed(9)); // nm precision
        y = Number(y.toFixed(9)); // nm precision
        const convergence = Number(γ.toDegrees().toFixed(9));
        const scale = Number(k.toFixed(12));

        const h = this.lat>=0 ? 'N' : 'S'; // hemisphere

        return new Utm(zone, h, x, y, this.datum, convergence, scale, !!zoneOverride);
    }
}

//export { Utm as default, LatLon_Utm as LatLon, Dms };

//==========================================================================================
// mgrs.js
//==========================================================================================

//import Utm, { LatLon as LatLonEllipsoidal, Dms } from './utm.js';

/**
 * Military Grid Reference System (MGRS/NATO) grid references provides geocoordinate references
 * covering the entire globe, based on UTM projections.
 *
 * MGRS references comprise a grid zone designator, a 100km square identification, and an easting
 * and northing (in metres); e.g. ‘31U DQ 48251 11932’.
 *
 * Depending on requirements, some parts of the reference may be omitted (implied), and
 * eastings/northings may be given to varying resolution.
 *
 * qv www.fgdc.gov/standards/projects/FGDC-standards-projects/usng/fgdc_std_011_2001_usng.pdf
 *
 * @module mgrs
 */

/*
 * Latitude bands C..X 8° each, covering 80°S to 84°N
 */
const latBands = 'CDEFGHJKLMNPQRSTUVWXX'; // X is repeated for 80-84°N

/*
 * 100km grid square column (‘e’) letters repeat every third zone
 */
const e100kLetters = [ 'ABCDEFGH', 'JKLMNPQR', 'STUVWXYZ' ];

/*
 * 100km grid square row (‘n’) letters repeat every other zone
 */
const n100kLetters = [ 'ABCDEFGHJKLMNPQRSTUV', 'FGHJKLMNPQRSTUVABCDE' ];

/* Mgrs - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Military Grid Reference System (MGRS/NATO) grid references, with methods to parse references, and
 * to convert to UTM coordinates.
 */
class Mgrs {

    /**
     * Creates an Mgrs grid reference object.
     *
     * @param  {number} zone - 6° longitudinal zone (1..60 covering 180°W..180°E).
     * @param  {string} band - 8° latitudinal band (C..X covering 80°S..84°N).
     * @param  {string} e100k - First letter (E) of 100km grid square.
     * @param  {string} n100k - Second letter (N) of 100km grid square.
     * @param  {number} easting - Easting in metres within 100km grid square.
     * @param  {number} northing - Northing in metres within 100km grid square.
     * @param  {LatLon.datums} [datum=WGS84] - Datum UTM coordinate is based on.
     * @throws {RangeError}  Invalid MGRS grid reference.
     *
     * @example
     *   import Mgrs from '/js/geodesy/mgrs.js';
     *   const mgrsRef = new Mgrs(31, 'U', 'D', 'Q', 48251, 11932); // 31U DQ 48251 11932
     */
    constructor(zone, band, e100k, n100k, easting, northing, datum=LatLonEllipsoidal.datums.WGS84) {
        if (!(1<=zone && zone<=60)) throw new RangeError(`invalid MGRS zone ‘${zone}’`);
        if (zone != parseInt(zone)) throw new RangeError(`invalid MGRS zone ‘${zone}’`);
        const errors = []; // check & report all other possible errors rather than reporting one-by-one
        if (band.length!=1 || latBands.indexOf(band) == -1) errors.push(`invalid MGRS band ‘${band}’`);
        if (e100k.length!=1 || e100kLetters[(zone-1)%3].indexOf(e100k) == -1) errors.push(`invalid MGRS 100km grid square column ‘${e100k}’ for zone ${zone}`);
        if (n100k.length!=1 || n100kLetters[0].indexOf(n100k) == -1) errors.push(`invalid MGRS 100km grid square row ‘${n100k}’`);
        if (isNaN(Number(easting))) errors.push(`invalid MGRS easting ‘${easting}’`);
        if (isNaN(Number(northing))) errors.push(`invalid MGRS northing ‘${northing}’`);
        if (!datum || datum.ellipsoid==undefined) errors.push(`unrecognised datum ‘${datum}’`);
        if (errors.length > 0) throw new RangeError(errors.join(', '));

        this.zone = Number(zone);
        this.band = band;
        this.e100k = e100k;
        this.n100k = n100k;
        this.easting = Number(easting);
        this.northing = Number(northing);
        this.datum = datum;
    }


    /**
     * Converts MGRS grid reference to UTM coordinate.
     *
     * Grid references refer to squares rather than points (with the size of the square indicated
     * by the precision of the reference); this conversion will return the UTM coordinate of the SW
     * corner of the grid reference square.
     *
     * @returns {Utm} UTM coordinate of SW corner of this MGRS grid reference.
     *
     * @example
     *   const mgrsRef = Mgrs.parse('31U DQ 48251 11932');
     *   const utmCoord = mgrsRef.toUtm(); // 31 N 448251 5411932
     */
    toUtm() {
        const hemisphere = this.band>='N' ? 'N' : 'S';

        // get easting specified by e100k (note +1 because eastings start at 166e3 due to 500km false origin)
        const col = e100kLetters[(this.zone-1)%3].indexOf(this.e100k) + 1;
        const e100kNum = col * 100e3; // e100k in metres

        // get northing specified by n100k
        const row = n100kLetters[(this.zone-1)%2].indexOf(this.n100k);
        const n100kNum = row * 100e3; // n100k in metres

        // get latitude of (bottom of) band
        const latBand = (latBands.indexOf(this.band)-10)*8;

        // get northing of bottom of band, extended to include entirety of bottom-most 100km square
        const nBand = Math.floor(new LatLonEllipsoidal(latBand, 0).toUtm().northing/100e3)*100e3;

        // 100km grid square row letters repeat every 2,000km north; add enough 2,000km blocks to
        // get into required band
        let n2M = 0; // northing of 2,000km block
        while (n2M + n100kNum + this.northing < nBand) n2M += 2000e3;

        return new Utm_Mgrs(this.zone, hemisphere, e100kNum+this.easting, n2M+n100kNum+this.northing, this.datum);
    }


    /**
     * Parses string representation of MGRS grid reference.
     *
     * An MGRS grid reference comprises (space-separated)
     *  - grid zone designator (GZD)
     *  - 100km grid square letter-pair
     *  - easting
     *  - northing.
     *
     * @param   {string} mgrsGridRef - String representation of MGRS grid reference.
     * @returns {Mgrs}   Mgrs grid reference object.
     * @throws  {Error}  Invalid MGRS grid reference.
     *
     * @example
     *   const mgrsRef = Mgrs.parse('31U DQ 48251 11932');
     *   const mgrsRef = Mgrs.parse('31UDQ4825111932');
     *   //  mgrsRef: { zone:31, band:'U', e100k:'D', n100k:'Q', easting:48251, northing:11932 }
     */
    static parse(mgrsGridRef) {
        if (!mgrsGridRef) throw new Error(`invalid MGRS grid reference ‘${mgrsGridRef}’`);

        // check for military-style grid reference with no separators
        if (!mgrsGridRef.trim().match(/\s/)) {
            if (!Number(mgrsGridRef.slice(0, 2))) throw new Error(`invalid MGRS grid reference ‘${mgrsGridRef}’`);
            let en = mgrsGridRef.trim().slice(5); // get easting/northing following zone/band/100ksq
            en = en.slice(0, en.length/2)+' '+en.slice(-en.length/2); // separate easting/northing
            mgrsGridRef = mgrsGridRef.slice(0, 3)+' '+mgrsGridRef.slice(3, 5)+' '+en; // insert spaces
        }

        // match separate elements (separated by whitespace)
        const ref = mgrsGridRef.match(/\S+/g);

        if (ref==null || ref.length!=4) throw new Error(`invalid MGRS grid reference ‘${mgrsGridRef}’`);

        // split gzd into zone/band
        const gzd = ref[0];
        const zone = gzd.slice(0, 2);
        const band = gzd.slice(2, 3);

        // split 100km letter-pair into e/n
        const en100k = ref[1];
        const e100k = en100k.slice(0, 1);
        const n100k = en100k.slice(1, 2);

        let e = ref[2], n = ref[3];

        // standardise to 10-digit refs - ie metres) (but only if < 10-digit refs, to allow decimals)
        e = e.length>=5 ?  e : (e+'00000').slice(0, 5);
        n = n.length>=5 ?  n : (n+'00000').slice(0, 5);

        return new Mgrs(zone, band, e100k, n100k, e, n);
    }


    /**
     * Returns a string representation of an MGRS grid reference.
     *
     * To distinguish from civilian UTM coordinate representations, no space is included within the
     * zone/band grid zone designator.
     *
     * Components are separated by spaces: for a military-style unseparated string, use
     *   Mgrs.toString().replace(/ /g, '');
     *
     * Note that MGRS grid references get truncated, not rounded (unlike UTM coordinates); grid
     * references indicate a bounding square, rather than a point, with the size of the square
     * indicated by the precision - a precision of 10 indicates a 1-metre square, a precision of 4
     * indicates a 1,000-metre square (hence 31U DQ 48 11 indicates a 1km square with SW corner at
     * 31 N 448000 5411000, which would include the 1m square 31U DQ 48251 11932).
     *
     * @param   {number}     [digits=10] - Precision of returned grid reference (eg 4 = km, 10 = m).
     * @returns {string}     This grid reference in standard format.
     * @throws  {RangeError} Invalid precision.
     *
     * @example
     *   const mgrsStr = new Mgrs(31, 'U', 'D', 'Q', 48251, 11932).toString(); // 31U DQ 48251 11932
     */
    toString(digits=10) {
        if (![ 2, 4, 6, 8, 10 ].includes(Number(digits))) throw new RangeError(`invalid precision ‘${digits}’`);

        const { zone, band, e100k, n100k, easting, northing } = this;

        // truncate to required precision
        const eRounded = Math.floor(easting/Math.pow(10, 5-digits/2));
        const nRounded = Math.floor(northing/Math.pow(10, 5-digits/2));

        // ensure leading zeros
        const zPadded = zone.toString().padStart(2, '0');
        const ePadded = eRounded.toString().padStart(digits/2, '0');
        const nPadded = nRounded.toString().padStart(digits/2, '0');

        return `${zPadded}${band} ${e100k}${n100k} ${ePadded} ${nPadded}`;
    }
}

/* Utm_Mgrs - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

/**
 * Extends Utm with method to convert UTM coordinate to MGRS reference.
 *
 * @extends Utm
 */
class Utm_Mgrs extends Utm {

    /**
     * Converts UTM coordinate to MGRS reference.
     *
     * @returns {Mgrs}
     * @throws  {TypeError} Invalid UTM coordinate.
     *
     * @example
     *   const utmCoord = new Utm(31, 'N', 448251, 5411932);
     *   const mgrsRef = utmCoord.toMgrs(); // 31U DQ 48251 11932
     */
    toMgrs() {
        // MGRS zone is same as UTM zone
        const zone = this.zone;

        // convert UTM to lat/long to get latitude to determine band
        const latlong = this.toLatLon();
        // grid zones are 8° tall, 0°N is 10th band
        const band = latBands.charAt(Math.floor(latlong.lat/8+10)); // latitude band

        // columns in zone 1 are A-H, zone 2 J-R, zone 3 S-Z, then repeating every 3rd zone
        const col = Math.floor(this.easting / 100e3);
        // (note -1 because eastings start at 166e3 due to 500km false origin)
        const e100k = e100kLetters[(zone-1)%3].charAt(col-1);

        // rows in even zones are A-V, in odd zones are F-E
        const row = Math.floor(this.northing / 100e3) % 20;
        const n100k = n100kLetters[(zone-1)%2].charAt(row);

        // truncate easting/northing to within 100km grid square
        let easting = this.easting % 100e3;
        let northing = this.northing % 100e3;

        // round to nm precision
        easting = Number(easting.toFixed(6));
        northing = Number(northing.toFixed(6));

        return new Mgrs(zone, band, e100k, n100k, easting, northing);
    }

}

/**
 * Extends LatLonEllipsoidal adding toMgrs() method to the Utm object returned by LatLon.toUtm().
 *
 * @extends LatLonEllipsoidal
 */
class Latlon_Utm_Mgrs extends LatLonEllipsoidal {

    /**
     * Converts latitude/longitude to UTM coordinate.
     *
     * Shadow of LatLon.toUtm, returning Utm augmented with toMgrs() method.
     *
     * @param   {number} [zoneOverride] - Use specified zone rather than zone within which point lies;
     *          note overriding the UTM zone has the potential to result in negative eastings, and
     *          perverse results within Norway/Svalbard exceptions (this is unlikely to be relevant
     *          for MGRS, but is needed as Mgrs passes through the Utm class).
     * @returns {Utm}   UTM coordinate.
     * @throws  {Error} If point not valid, if point outside latitude range.
     *
     * @example
     *   const latlong = new LatLon(48.8582, 2.2945);
     *   const utmCoord = latlong.toUtm(); // 31 N 448252 5411933
     */
    toUtm(zoneOverride=undefined) {
        const utm = super.toUtm(zoneOverride);
        return new Utm_Mgrs(utm.zone, utm.hemisphere, utm.easting, utm.northing, utm.datum, utm.convergence, utm.scale);
    }

}

//export { Mgrs as default, Utm_Mgrs as Utm, Latlon_Utm_Mgrs as LatLon, Dms };