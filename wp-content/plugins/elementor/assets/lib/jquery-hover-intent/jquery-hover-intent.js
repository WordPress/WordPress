/*
 * Based on tristen hoverintent plugin - https://github.com/tristen/hoverintent
 */

(function( $ ) {
	'use strict';

	var hoverIntent = function( el, onOver, onOut ) {
		var x, y, pX, pY,
			h = {},
			state = 0,
			timer = 0;

		var options = {
			sensitivity: 7,
			interval: 100,
			timeout: 0
		};

		function delay( el, e ) {
			if ( timer )
				timer = clearTimeout( timer );

			state = 0;

			return onOut ? onOut.call( el, e ) : null;
		}

		function tracker( e ) {
			x = e.clientX;
			y = e.clientY;
		}

		function compare( el, e ) {
			if ( timer )
				timer = clearTimeout( timer );

			if ( (Math.abs( pX - x ) + Math.abs( pY - y )) < options.sensitivity ) {
				state = 1;

				return onOver ? onOver.call( el, e ) : null;
			} else {
				pX = x;
				pY = y;

				timer = setTimeout( function() {
					compare( el, e );
				}, options.interval );
			}
		}

		// Public methods
		h.options = function( opt ) {
			options = $.extend( {}, options, opt );

			return h;
		};

		function dispatchOver( e ) {
			if ( timer )
				timer = clearTimeout( timer );

			el.removeEventListener( 'mousemove', tracker );

			if ( state !== 1 ) {
				pX = e.clientX;
				pY = e.clientY;

				el.addEventListener( 'mousemove', tracker );

				timer = setTimeout( function() {
					compare( el, e );
				}, options.interval );
			}

			return this;
		}

		function dispatchOut( e ) {
			if ( timer )
				timer = clearTimeout( timer );

			el.removeEventListener( 'mousemove', tracker );

			if ( state === 1 ) {
				timer = setTimeout( function() {
					delay( el, e );
				}, options.timeout );
			}

			return this;
		}

		h.remove = function() {
			el.removeEventListener( 'mouseover', dispatchOver );
			el.removeEventListener( 'mouseleave', dispatchOut );
		};

		el.addEventListener( 'mouseover', dispatchOver );

		el.addEventListener( 'mouseleave', dispatchOut );

		return h;
	};

	$.fn.hoverIntent = function( over, out, options ) {
		return this.each( function() {
			hoverIntent( this, over, out ).options( options || {} );
		} );
	};

})( jQuery );