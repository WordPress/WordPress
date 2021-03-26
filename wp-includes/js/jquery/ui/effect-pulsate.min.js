/*!
 * jQuery UI Effects Pulsate 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/pulsate-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(p){return p.effects.effect.pulsate=function(e,i){var t,n=p(this),f=p.effects.setMode(n,e.mode||"show"),c="show"===f,o="hide"===f,s=2*(e.times||5)+(c||"hide"===f?1:0),u=e.duration/s,a=0,d=n.queue(),f=d.length;for(!c&&n.is(":visible")||(n.css("opacity",0).show(),a=1),t=1;t<s;t++)n.animate({opacity:a},u,e.easing),a=1-a;n.animate({opacity:a},u,e.easing),n.queue(function(){o&&n.hide(),i()}),1<f&&d.splice.apply(d,[1,0].concat(d.splice(f,1+s))),n.dequeue()}});