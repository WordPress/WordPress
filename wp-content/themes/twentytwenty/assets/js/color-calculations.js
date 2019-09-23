/* global Color */
/* eslint no-unused-vars: off */
/**
 * Color Calculations.
 *
 * @since 1.0.0
 *
 * @param {string} backgroundColor - The background color.
 * @param {number} accentHue - The hue for our accent color.
 *
 * @return {Object} - this
 */
function _twentyTwentyColor( backgroundColor, accentHue ) {
	// Set the object properties.
	this.backgroundColor = backgroundColor;
	this.accentHue = accentHue;
	this.bgColorObj = new Color( backgroundColor );
	this.textColorObj = this.bgColorObj.getMaxContrastColor();
	this.textColor = this.textColorObj.toCSS();
	this.isDark = 0.5 > this.bgColorObj.toLuminosity();
	this.isLight = ! this.isDark;

	// Return the object.
	return this;
}

/**
 * Builds an array of Color objects based on the accent hue.
 * For improved performance we only build half the array
 * depending on dark/light background-color.
 *
 * @since 1.0.0
 *
 * @return {Object} - this
 */
_twentyTwentyColor.prototype.setAccentColorsArray = function() {
	var self = this,
		minSaturation = 55,
		maxSaturation = 90,
		minLightness = 25,
		maxLighness = 75,
		stepSaturation = 2.5,
		stepLightness = 2.5,
		pushColor = function() {
			var colorObj = new Color( {
					h: self.accentHue,
					s: s,
					l: l,
				} ),
				item;

			item = {
				color: colorObj,
				contrastBackground: colorObj.getDistanceLuminosityFrom( self.bgColorObj ),
				contrastText: colorObj.getDistanceLuminosityFrom( self.textColorObj ),
			};

			// Check a minimum of 4.5:1 contrast with the background and 3:1 with surrounding text.
			if ( 4.5 > item.contrastBackground || 3 > item.contrastText ) {
				return;
			}

			// Get a score for this color by multiplying the 2 contrasts.
			// We'll use that to sort the array.
			item.score = item.contrastBackground * item.contrastText;

			self.accentColorsArray.push( item );
		},
		s, l, aaa;

	this.accentColorsArray = [];

	// We're using `for` loops here because they perform marginally better than other loops.
	for ( s = minSaturation; s <= maxSaturation; s += stepSaturation ) {
		for ( l = minLightness; l <= maxLighness; l += stepLightness ) {
			pushColor( s, l );
		}
	}

	// Check if we have colors that are AAA compliant.
	aaa = this.accentColorsArray.filter( function( color ) {
		return 7 <= color.contrastBackground;
	} );

	// If we have AAA-compliant colors, alpways prefer them.
	if ( aaa.length ) {
		this.accentColorsArray = aaa;
	}

	// Sort colors by contrast.
	this.accentColorsArray.sort( function( a, b ) {
		return b.score - a.score;
	} );
	return this;
};

/**
 * Get accessible text-color.
 *
 * @since 1.0.0
 *
 * @return {Color} - Returns a Color object.
 */
_twentyTwentyColor.prototype.getTextColor = function() {
	return this.textColor;
};

/**
 * Get accessible color for the defined accent-hue and background-color.
 *
 * @since 1.0.0
 *
 * @return {Color} - Returns a Color object.
 */
_twentyTwentyColor.prototype.getAccentColor = function() {
	var fallback;

	// If we have colors returns the 1st one - it has the highest score.
	if ( this.accentColorsArray[0] ) {
		return this.accentColorsArray[0].color;
	}

	// Fallback.
	fallback = new Color( 'hsl(' + this.accentHue + ',75%,50%)' );
	return fallback.getReadableContrastingColor( this.bgColorObj, 4.5 );
};

/**
 * Return a new instance of the _twentyTwentyColor object.
 *
 * @since 1.0.0
 * @param {string} backgroundColor - The background color.
 * @param {number} accentHue - The hue for our accent color.
 * @return {Object} - this
 */
function twentyTwentyColor( backgroundColor, accentHue ) {
	var color = new _twentyTwentyColor( backgroundColor, accentHue );
	color.setAccentColorsArray();
	return color;
}
