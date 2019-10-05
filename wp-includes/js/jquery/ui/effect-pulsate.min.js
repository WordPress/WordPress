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
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){return r.effects.effect.pulsate=function(e,i){var t,n=r(this),f=r.effects.setMode(n,e.mode||"show"),c="show"===f,o="hide"===f,s=c||"hide"===f,u=2*(e.times||5)+(s?1:0),a=e.duration/u,d=0,p=n.queue(),h=p.length;for(!c&&n.is(":visible")||(n.css("opacity",0).show(),d=1),t=1;t<u;t++)n.animate({opacity:d},a,e.easing),d=1-d;n.animate({opacity:d},a,e.easing),n.queue(function(){o&&n.hide(),i()}),1<h&&p.splice.apply(p,[1,0].concat(p.splice(h,1+u))),n.dequeue()}});