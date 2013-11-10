;var svgPainter = ( function( $, window, document, undefined ) {

	'use strict';

	$(document).ready( function() {

		// detection for browser SVG capability
		if ( document.implementation.hasFeature( 'http://www.w3.org/TR/SVG11/feature#Image', '1.1' ) ) {
			document.body.className = document.body.className.replace( 'no-svg', 'svg' );
		}

		svgPainter.init();

	});

	return {

		elements : [],

		init : function() {

			this.selector = $( '#adminmenu .wp-menu-image, #wpadminbar .ab-item' );

			this.setColors();
			this.findElements();
			this.paint();

		},

		setColors : function( colors ) {

			if ( typeof colors === 'undefined' && typeof mp6_color_scheme !== 'undefined' ) {
				var colors = mp6_color_scheme;
			}

			this.colorscheme = colors;

		},

		findElements : function() {

			this.selector.each(function() {

				var bgimg = $(this).css( 'background-image' );

				if ( bgimg.indexOf( 'data:image/svg+xml;base64' ) != -1 ) {
					svgPainter.elements.push( $(this) );
				}

			});

		},

		paint : function() {

			// loop through all elements
			$.each( this.elements, function( index, $element ) {

				var $menuitem = $element.parent().parent();

				if ( $menuitem.hasClass( 'current' ) || $menuitem.hasClass( 'wp-has-current-submenu' ) ) {

					// paint icon in 'current' color
					svgPainter.paintElement( $element, svgPainter.colorscheme.icons.current );

				} else {

					// paint icon in base color
					svgPainter.paintElement( $element, svgPainter.colorscheme.icons.base );

					// set hover callbacks
					$menuitem.hover(
						function() { svgPainter.paintElement( $element, svgPainter.colorscheme.icons.focus ); },
						function() { svgPainter.paintElement( $element, svgPainter.colorscheme.icons.base ); }
					);

				}

			});

		},

		paintElement : function( $element, color ) {

			// only accept hex colors: #101 or #101010
			if ( ! color.match( /^(#[0-9a-f]{3}|#[0-9a-f]{6})$/i ) )
				return;

			var xml = $element.data( 'mp6-svg-' + color );

			if ( ! xml ) {

				var base64 = $element.css( 'background-image' ).match( /.+data:image\/svg\+xml;base64,(.+)\)/ );

				if ( ! base64 )
					return;

				try {
					var xml = window.atob( base64[1] );
				} catch ( e ) {
					var xml = $.base64.atob( base64[1] );
				}

				// replace `fill` attributes
				xml = xml.replace( /fill="(.+?)"/g, 'fill="' + color + '"');

				// replace `style` attributes
				xml = xml.replace( /style="(.+?)"/g, 'style="fill:' + color + '"');

				// replace `fill` properties in `<style>` tags
				xml = xml.replace( /fill:.*?;/g, 'fill: ' + color + ';');

				try {
					xml = window.btoa( xml );
				} catch ( e ) {
					xml = $.base64.btoa( xml );
				}

				$element.data( 'mp6-svg-' + color, xml );

			}

			$element.attr( 'style', "background-image: url('data:image/svg+xml;base64," + xml + "') !important;" );

		}

	};

})( jQuery, window, document );

/*!
 * Customized for MP6
 *
 * Based on jquery.base64.js 0.0.3 - https://github.com/yckart/jquery.base64.js
 *
 * Based upon: https://gist.github.com/Yaffle/1284012
 *
 * Copyright (c) 2012 Yannick Albert (http://yckart.com)
 * Licensed under the MIT license
 * http://www.opensource.org/licenses/mit-license.php
 **/
;(function($) {

    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
        a256 = '',
        r64 = [256],
        r256 = [256],
        i = 0;

    while(i < 256) {
        var c = String.fromCharCode(i);
        a256 += c;
        r256[i] = i;
        r64[i] = b64.indexOf(c);
        ++i;
    }

    function code(s, discard, alpha, beta, w1, w2) {
        s = String(s);
        var buffer = 0,
            i = 0,
            length = s.length,
            result = '',
            bitsInBuffer = 0;

        while(i < length) {
            var c = s.charCodeAt(i);
            c = c < 256 ? alpha[c] : -1;

            buffer = (buffer << w1) + c;
            bitsInBuffer += w1;

            while(bitsInBuffer >= w2) {
                bitsInBuffer -= w2;
                var tmp = buffer >> bitsInBuffer;
                result += beta.charAt(tmp);
                buffer ^= tmp << bitsInBuffer;
            }
            ++i;
        }
        if(!discard && bitsInBuffer > 0) result += beta.charAt(buffer << (w2 - bitsInBuffer));
        return result;
    }

    var Plugin = $.base64 = function(dir, input, encode) {
            return input ? Plugin[dir](input, encode) : dir ? null : this;
        };

    $.base64.btoa = function(plain) {
        plain = code(plain, false, r256, b64, 8, 6);
        return plain + '===='.slice((plain.length % 4) || 4);
    };

    $.base64.atob = function(coded, utf8decode) {
        coded = coded.replace(/[^A-Za-z0-9\+\/\=]/g, "");
        coded = String(coded).split('=');
        var i = coded.length;
        do {--i;
            coded[i] = code(coded[i], true, r64, a256, 6, 8);
        } while (i > 0);
        coded = coded.join('');
        return coded;
    };
}(jQuery));
