/*!
 * jQuery UI Effects Scale 1.13.0
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-size"],e):e(jQuery)}(function(n){"use strict";return n.effects.define("scale",function(e,t){var f=n(this),i=e.mode,i=parseInt(e.percent,10)||(0===parseInt(e.percent,10)||"effect"!==i?0:100),i=n.extend(!0,{from:n.effects.scaledDimensions(f),to:n.effects.scaledDimensions(f,i,e.direction||"both"),origin:e.origin||["middle","center"]},e);e.fade&&(i.from.opacity=1,i.to.opacity=0),n.effects.effect.size.call(this,i,t)})});