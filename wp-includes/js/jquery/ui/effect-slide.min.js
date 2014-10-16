/*!
 * jQuery UI Effects Slide 1.11.2
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/slide-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.slide=function(b,c){var d,e=a(this),f=["position","top","bottom","left","right","width","height"],g=a.effects.setMode(e,b.mode||"show"),h="show"===g,i=b.direction||"left",j="up"===i||"down"===i?"top":"left",k="up"===i||"left"===i,l={};a.effects.save(e,f),e.show(),d=b.distance||e["top"===j?"outerHeight":"outerWidth"](!0),a.effects.createWrapper(e).css({overflow:"hidden"}),h&&e.css(j,k?isNaN(d)?"-"+d:-d:d),l[j]=(h?k?"+=":"-=":k?"-=":"+=")+d,e.animate(l,{queue:!1,duration:b.duration,easing:b.easing,complete:function(){"hide"===g&&e.hide(),a.effects.restore(e,f),a.effects.removeWrapper(e),c()}})}});