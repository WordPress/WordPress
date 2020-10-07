/*!
 * jQuery UI Effects Scale 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-size"],e):e(jQuery)}(function(o){return o.effects.define("scale",function(e,f){var t=o(this),n=e.mode,i=parseInt(e.percent,10)||(0===parseInt(e.percent,10)?0:"effect"!==n?0:100),c=o.extend(!0,{from:o.effects.scaledDimensions(t),to:o.effects.scaledDimensions(t,i,e.direction||"both"),origin:e.origin||["middle","center"]},e);e.fade&&(c.from.opacity=1,c.to.opacity=0),o.effects.effect.size.call(this,c,f)})});