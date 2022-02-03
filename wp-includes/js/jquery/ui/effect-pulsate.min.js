/*!
 * jQuery UI Effects Pulsate 1.13.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(c){"use strict";return c.effects.define("pulsate","show",function(e,i){var t=c(this),n=e.mode,s="show"===n,f=2*(e.times||5)+(s||"hide"===n?1:0),u=e.duration/f,o=0,a=1,n=t.queue().length;for(!s&&t.is(":visible")||(t.css("opacity",0).show(),o=1);a<f;a++)t.animate({opacity:o},u,e.easing),o=1-o;t.animate({opacity:o},u,e.easing),t.queue(i),c.effects.unshift(t,n,1+f)})});