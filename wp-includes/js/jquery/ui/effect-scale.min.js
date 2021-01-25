/*!
 * jQuery UI Effects Scale 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-size"],e):e(jQuery)}(function(i){return i.effects.define("scale",function(e,f){var t=i(this),n=e.mode,n=parseInt(e.percent,10)||(0===parseInt(e.percent,10)||"effect"!==n?0:100),n=i.extend(!0,{from:i.effects.scaledDimensions(t),to:i.effects.scaledDimensions(t,n,e.direction||"both"),origin:e.origin||["middle","center"]},e);e.fade&&(n.from.opacity=1,n.to.opacity=0),i.effects.effect.size.call(this,n,f)})});