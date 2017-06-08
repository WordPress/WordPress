/*
 * Horparallax
 *
 * @version 1.0
 *
 * Copyright 2013-2016, UpSolution
 */

!function($){

	/*
	 ********* Horparallax class definition ***********/
	var Horparallax = function(container, options){
		// Context
		var that = this;
		this.$window = $(window);
		this.container = $(container);
		// Apply options
		if (container.onclick != undefined){
			options = $.extend({}, container.onclick() || {}, typeof options == 'object' && options);
			this.container.removeProp('onclick');
		}
		options = $.extend({}, $.fn.horparallax.defaults, typeof options == 'object' && options);
		this.options = options;
		this.bg = this.container.find(options.bgSelector);
		// Count sizes
		this.containerWidth = this.container.outerWidth();
		this.containerHeight = this.container.outerHeight();
		this.bgWidth = this.bg.outerWidth();
		this.windowHeight = this.$window.height();
		// Count frame rate
		this._frameRate = Math.round(1000 / this.options.fps);
		// To fix IE bug that handles mousemove before mouseenter
		this.mouseInside = false;
		// Mouse events for desktop browsers
		if ( ! ('ontouchstart' in window) || ! ('DeviceOrientationEvent' in window)){
			this.container
				.mouseenter(function(e){
					// To fix IE bug that handles mousemove before mouseenter
					that.mouseInside = true;
					var offset = that.container.offset(),
						coord = (e.pageX - offset.left) / that.containerWidth;
					that.cancel();
					that._hoverAnimation = true;
					that._hoverFrom = that.now;
					that._hoverTo = coord;
					that.start(that._hoverTo);
				})
				.mousemove(function(e){
					// To fix IE bug that handles mousemove before mouseenter
					if ( ! that.mouseInside) return;
					// Reducing processor load for too frequent event calls
					if (that._lastFrame + that._frameRate > Date.now()) return;
					var offset = that.container.offset(),
						coord = (e.pageX - offset.left) / that.containerWidth;
					// Handle hover animation
					if (that._hoverAnimation){
						that._hoverTo = coord;
						return;
					}
					that.set(coord);
					that._lastFrame = Date.now();
				})
				.mouseleave(function(e){
					that.mouseInside = false;
					that.cancel();
					that.start(that.options.basePoint);
				});
		}
		// Handle resize
		this.$window.resize(function(){ that.handleResize(); });
		// Device orientation events for touch devices
		this._orientationDriven = ('ontouchstart' in window && 'DeviceOrientationEvent' in window);
		if (this._orientationDriven){
			// Check if container is visible
			this._checkIfVisible();
			window.addEventListener("deviceorientation", function(e){
				// Reducing processor load for too frequent event calls
				if ( ! that.visible || that._lastFrame + that._frameRate > Date.now()) return;
				that._deviceOrientationChange(e);
				that._lastFrame = Date.now();
			});
			this.$window.resize(function(){ that._checkIfVisible(); });
			this.$window.scroll(function(){ that._checkIfVisible(); });
		}
		// Set to basepoint
		this.set(this.options.basePoint);
		this._lastFrame = Date.now();
	};

	Horparallax.prototype = {

		/**
		 * Event to fire on deviceorientation change
		 * @private
		 */
		_deviceOrientationChange: function(e){
			var gamma = e.gamma,
				beta = e.beta,
				x, y;
			switch (window.orientation){
				case -90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (beta + 45) / 90;
					break;
				case 90:
					beta = Math.max(-45, Math.min(45, beta));
					x = (45 - beta) / 90;
					break;
				case 180:
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (gamma + 45) / 90;
					break;
				case 0:
				default:
					// Upside down
					if (gamma < -90 || gamma > 90) gamma = Math.abs(e.gamma)/e.gamma * (180 - Math.abs(e.gamma));
					gamma = Math.max(-45, Math.min(45, gamma));
					x = (45 - gamma) / 90;
					break;
			}
			this.set(x);
		},

		/**
		 * Handle container resize
		 */
		handleResize: function()
		{
			this.containerWidth = this.container.outerWidth();
			this.containerHeight = this.container.outerHeight();
			this.bgWidth = this.bg.outerWidth();
			this.windowHeight = this.$window.height();
			this.set(this.now);
		},

		/**
		 * Update container visibility status (to prevent unnessesary rendering)
		 * @private
		 */
		_checkIfVisible: function()
		{
			var scrollTop = this.$window.scrollTop(),
				containerTop = this.container.offset().top;
			this.visible = (containerTop + this.containerHeight > scrollTop && containerTop < scrollTop + this.windowHeight);
		},


		/**
		 * Render horparallax frame.
		 * @param {Array} x is ranged in [0, 1]
		 */
		set: function(x)
		{
			this.bg.css('left', (this.containerWidth - this.bgWidth) * x);
			this.now = x;
			return this;
		},

		/**
		 * Step value computing function, read more at http://mootools.net/docs/core/Fx/Fx
		 * @param {Number} from
		 * @param {Number} to
		 * @param {Number} delta
		 * @return {Number}
		 */
		compute: function(from, to, delta)
		{
			if (this._hoverAnimation) return (this._hoverTo - this._hoverFrom) * delta + this._hoverFrom;
			return (to - from) * delta + from;
		},

		/**
		 * Start animation to certain point
		 * @param {Array} to
		 * @return {Horparallax}
		 */
		start: function(to)
		{
			var from = this.now,
				that = this;
			this.container
				.css('delta', 0)
				.animate({
					delta: 1
				}, {
					duration: this.options.duration,
					easing: this.options.easing,
					complete: function(){
						that._hoverAnimation = false;
					},
					step: function(delta){
						that.set(that.compute(from, to, delta));
					},
					queue: false
				});
			return this;
		},

		/**
		 * Cancel animation
		 * @return {Horparallax}
		 */
		cancel: function()
		{
			this._hoverAnimation = false;
			this.container.stop(true, false);
			return this;
		}


	};

	// EaseOutElastic easing
	if ($.easing.easeOutElastic == undefined){
		/**
		 * Original function by George McGinley Smith
		 * @link http://gsgd.co.uk/sandbox/jquery/easing/
		 */
		$.easing.easeOutElastic = function (x, t, b, c, d) {
			var s = 1.70158, p = 0, a = c;
			if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
			if (a < Math.abs(c)) { a=c; var s=p/4; }
			else var s = p/(2*Math.PI) * Math.asin (c/a);
			return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
		};
	}

	$.fn.horparallax = function(options){
		return this.each(function(){
			var $this = $(this),
				data = $this.data('horparallax');
			if ( ! data) $this.data('horparallax', (data = new Horparallax(this, options)))
		});
	};

	$.fn.horparallax.defaults = {
		/**
		 * @var {Number} Frame per second limit for rendering
		 */
		fps: 60,

		/**
		 * @var {Number} Point for basic position (after the cursor moves out of the container)
		 */
		basePoint: .5,

		/**
		 * @var {Number} Return to base point duration
		 */
		duration: 500,

		/**
		 * @var {String} Background layer selector
		 */
		bgSelector: '.l-section-img, .l-titlebar-img',

		/**
		 * @var {Function} Returning-to-basepoint easing
		 */
		easing: 'swing'// 'easeOutElastic'
	};

	$.fn.horparallax.Constructor = Horparallax;

	$(function(){
		jQuery('.parallax_hor').horparallax();
	});

}(jQuery);

