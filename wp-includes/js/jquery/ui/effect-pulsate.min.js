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
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.pulsate=function(b,c){var d,e=a(this),f=a.effects.setMode(e,b.mode||"show"),g="show"===f,h="hide"===f,i=g||"hide"===f,j=2*(b.times||5)+(i?1:0),k=b.duration/j,l=0,m=e.queue(),n=m.length;for(!g&&e.is(":visible")||(e.css("opacity",0).show(),l=1),d=1;d<j;d++)e.animate({opacity:l},k,b.easing),l=1-l;e.animate({opacity:l},k,b.easing),e.queue(function(){h&&e.hide(),c()}),n>1&&m.splice.apply(m,[1,0].concat(m.splice(n,j+1))),e.dequeue()}});