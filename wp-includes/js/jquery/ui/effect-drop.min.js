/*!
 * jQuery UI Effects Drop 1.11.2
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/drop-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.drop=function(b,c){var d,e=a(this),f=["position","top","bottom","left","right","opacity","height","width"],g=a.effects.setMode(e,b.mode||"hide"),h="show"===g,i=b.direction||"left",j="up"===i||"down"===i?"top":"left",k="up"===i||"left"===i?"pos":"neg",l={opacity:h?1:0};a.effects.save(e,f),e.show(),a.effects.createWrapper(e),d=b.distance||e["top"===j?"outerHeight":"outerWidth"](!0)/2,h&&e.css("opacity",0).css(j,"pos"===k?-d:d),l[j]=(h?"pos"===k?"+=":"-=":"pos"===k?"-=":"+=")+d,e.animate(l,{queue:!1,duration:b.duration,easing:b.easing,complete:function(){"hide"===g&&e.hide(),a.effects.restore(e,f),a.effects.removeWrapper(e),c()}})}});