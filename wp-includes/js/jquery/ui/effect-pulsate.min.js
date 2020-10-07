/*!
 * jQuery UI Effects Pulsate 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(h){return h.effects.define("pulsate","show",function(e,i){var n=h(this),t=e.mode,f="show"===t,s=f||"hide"===t,o=2*(e.times||5)+(s?1:0),u=e.duration/o,a=0,c=1,d=n.queue().length;for(!f&&n.is(":visible")||(n.css("opacity",0).show(),a=1);c<o;c++)n.animate({opacity:a},u,e.easing),a=1-a;n.animate({opacity:a},u,e.easing),n.queue(i),h.effects.unshift(n,d,1+o)})});