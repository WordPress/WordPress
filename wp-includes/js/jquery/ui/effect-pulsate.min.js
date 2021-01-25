/*!
 * jQuery UI Effects Pulsate 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(c){return c.effects.define("pulsate","show",function(e,i){var n=c(this),t=e.mode,f="show"===t,t=f||"hide"===t,s=2*(e.times||5)+(t?1:0),o=e.duration/s,u=0,a=1,t=n.queue().length;for(!f&&n.is(":visible")||(n.css("opacity",0).show(),u=1);a<s;a++)n.animate({opacity:u},o,e.easing),u=1-u;n.animate({opacity:u},o,e.easing),n.queue(i),c.effects.unshift(n,t,1+s)})});