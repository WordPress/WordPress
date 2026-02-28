/**
 * CSS conic-gradient() polyfill
 * By Lea Verou — http://lea.verou.me
 * MIT license
 */

(function(){

var π = Math.PI;
var τ = 2 * π;
var ε = .00001;
var deg = π/180;

var dummy = document.createElement("div");
document.head.appendChild(dummy);

var _ = self.ConicGradient = function(o) {
	var me = this;
	_.all.push(this);

	o = o || {};

	this.canvas = document.createElement("canvas");
	this.context = this.canvas.getContext("2d");

	this.repeating = !!o.repeating;

	this.size = o.size || Math.max(innerWidth, innerHeight);

	this.canvas.width = this.canvas.height = this.size;

	var stops = o.stops;

	this.stops = (stops || "").split(/\s*,(?![^(]*\))\s*/); // commas that are not followed by a ) without a ( first

	this.from = 0;

	for (var i=0; i<this.stops.length; i++) {
		if (this.stops[i]) {
			var stop = this.stops[i] = new _.ColorStop(this, this.stops[i]);

			if (stop.next) {
				this.stops.splice(i+1, 0, stop.next);
				i++;
			}
		}
		else {
			this.stops.splice(i, 1);
			i--;
		}
	}

	if (this.stops[0].color.indexOf('from') == 0) {
		this.from = this.stops[0].pos*360;
		this.stops.shift();
	}
	// Normalize stops

	// Add dummy first stop or set first stop’s position to 0 if it doesn’t have one
	if (this.stops[0].pos === undefined) {
			this.stops[0].pos = 0;
		}
	else if (this.stops[0].pos > 0) {
		var first = this.stops[0].clone();
		first.pos = 0;
		this.stops.unshift(first);
	}

	// Add dummy last stop or set last stop’s position to 100% if it doesn’t have one
	if (this.stops[this.stops.length - 1].pos === undefined) {
		this.stops[this.stops.length - 1].pos = 1;
	}
	else if (!this.repeating && this.stops[this.stops.length - 1].pos < 1) {
		var last = this.stops[this.stops.length - 1].clone();
		last.pos = 1;
		this.stops.push(last);
	}

	this.stops.forEach(function(stop, i){
		if (stop.pos === undefined) {
			// Evenly space color stops with no position
			for (var j=i+1; this[j]; j++) {
				if (this[j].pos !== undefined) {
					stop.pos = this[i-1].pos + (this[j].pos - this[i-1].pos)/(j-i+1);
					break;
				}
			}
		}
		else if (i > 0) {
			// Normalize color stops whose position is smaller than the position of the stop before them
			stop.pos = Math.max(stop.pos, this[i-1].pos);
		}
	}, this.stops);

	if (this.repeating) {
		// Repeat color stops until >= 1
		var stops = this.stops.slice();
		var lastStop = stops[stops.length-1];
		var difference = lastStop.pos - stops[0].pos;

		for (var i=0; this.stops[this.stops.length-1].pos < 1 && i<10000; i++) {
			for (var j=0; j<stops.length; j++) {
				var s = stops[j].clone();
				s.pos += (i+1)*difference;

				this.stops.push(s);
			}
		}
	}

	this.paint();
};

_.all = [];

_.prototype = {
	toString: function() {
		return "url('" + this.dataURL + "')";
	},

	get dataURL() {
		// IE/Edge only render data-URI based background-image when the image data
		// is escaped.
		// Ref: https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/7157459/#comment-3
		return "data:image/svg+xml," + encodeURIComponent(this.svg);
	},

	get blobURL() {
		// Warning: Flicker when updating due to slow blob: URL resolution :(
		// TODO cache this and only update it when color stops change
		return URL.createObjectURL(new Blob([this.svg], {type: "image/svg+xml"}));
	},

	get svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="none">' +
			'<svg viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice">' +
			'<image width="100" height="100%" xlink:href="' + this.png + '" /></svg></svg>';
	},

	get png() {
		return this.canvas.toDataURL();
	},

	get r() {
		return Math.sqrt(2) * this.size / 2;
	},

	// Paint the conical gradient on the canvas
	paint: function() {
		var c = this.context;

		var radius = this.r;
		var half = this.size / 2;
		var x = half;
		var scale = this.stops[this.stops.length - 1].pos;

		var tinycanvas = document.createElement("canvas");
		tinycanvas.width = 360*scale;
		var tinyctx = tinycanvas.getContext("2d");
		var tinygradient = tinyctx.createLinearGradient(0,0,360*scale,0);
		this.stops.forEach(function(stop){
			tinygradient.addColorStop(stop.pos/scale, 'rgba(' + stop.color.join(",") + ')');
		});
		tinyctx.fillStyle = tinygradient;
		tinyctx.fillRect(0,0,360,1);

		// Transform coordinate system so that angles start from the top left, like in CSS
		c.translate(half, half);
		c.rotate(-90*deg);
		c.rotate(this.from*deg);
		c.translate(-half, -half);

		// Draw a series of arcs, 1deg each
		for (var i = 0; i < 360; i++) {

			var data = tinyctx.getImageData(i, 0, 1, 1).data;

			c.fillStyle = 'rgba(' + data[0] + ', ' + data[1] +
             ', ' + data[2] + ', ' + (data[3] / 255) + ')';
			c.beginPath();
			c.moveTo(x, x);

			var beginArg = i*deg;
			beginArg = Math.min(360*deg, beginArg);

			// .02: To prevent empty blank line and corresponding moire
			// only non-alpha colors are cared now
			var endArg = (i+1)*deg + ( data[3] < 255 ? 0 : 0.02);
			endArg = Math.min(360*deg, endArg);

			c.arc(x, x, radius, beginArg, endArg);

			c.closePath();
			c.fill();

		}
	}
};

_.ColorStop = function(gradient, stop) {
	this.gradient = gradient;

	if (stop) {
		var parts = stop.match(/^(.+?)(?:\s+([\d.]+)(%|deg|turn|grad|rad)?)?(?:\s+([\d.]+)(%|deg|turn|grad|rad)?)?\s*$/);

		this.color = _.ColorStop.colorToRGBA(parts[1]);

		if (parts[2]) {
			var unit = parts[3];

			if (unit == "%" || parts[2] === "0" && !unit) {
				this.pos = parts[2]/100;
			}
			else if (unit == "turn") {
				this.pos  = +parts[2];
			}
			else if (unit == "deg") {
				this.pos  = parts[2] / 360;
			}
			else if (unit == "grad") {
				this.pos  = parts[2] / 400;
			}
			else if (unit == "rad") {
				this.pos  = parts[2] / τ;
			}
		}

		if (parts[4]) {
			this.next = new _.ColorStop(gradient, parts[1] + " " + parts[4] + parts[5]);
		}
	}
}

_.ColorStop.prototype = {
	clone: function() {
		var ret = new _.ColorStop(this.gradient);
		ret.color = this.color;
		ret.pos = this.pos;

		return ret;
	},

	toString: function() {
		return "rgba(" + this.color.join(", ") + ") " + this.pos * 100 + "%";
	}
};

_.ColorStop.colorToRGBA = function(color) {
	if (!Array.isArray(color) && color.indexOf("from") == -1) {
		dummy.style.color = color;

		var rgba = getComputedStyle(dummy).color.match(/rgba?\(([\d.]+), ([\d.]+), ([\d.]+)(?:, ([\d.]+))?\)/);

		if (rgba) {
			rgba.shift();
			rgba = rgba.map(function(a) { return +a });
			rgba[3] = isNaN(rgba[3])? 1 : rgba[3];
		}

		return rgba || [0,0,0,0];
	}

	return color;
};

})();

if (self.StyleFix) {
	// Test if conic gradients are supported first:
	(function(){
		var dummy = document.createElement("p");
		dummy.style.backgroundImage = "conic-gradient(white, black)";
		dummy.style.backgroundImage = PrefixFree.prefix + "conic-gradient(white, black)";

		if (!dummy.style.backgroundImage) {
			// Not supported, use polyfill
			StyleFix.register(function(css, raw, el) {
				if (css.indexOf("conic-gradient") > -1) {
					css = css.replace(/(?:repeating-)?conic-gradient\(\s*((?:\([^()]+\)|[^;()}])+?)\)/g, function(gradient, stops) {
						return new ConicGradient({
							stops: stops,
							repeating: gradient.indexOf("repeating-") > -1,
							size: raw ? null : el.width
						});
					});
				}

				return css;
			});
		}
	})();
}
