/*!
 * jQuery UI Effects Drop 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/drop-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(a){return a.effects.effect.drop=function(e,t){var o,i=a(this),f=["position","top","bottom","left","right","opacity","height","width"],n=a.effects.setMode(i,e.mode||"hide"),s="show"===n,c=e.direction||"left",p="up"===c||"down"===c?"top":"left",r="up"===c||"left"===c?"pos":"neg",d={opacity:s?1:0};a.effects.save(i,f),i.show(),a.effects.createWrapper(i),o=e.distance||i["top"==p?"outerHeight":"outerWidth"](!0)/2,s&&i.css("opacity",0).css(p,"pos"==r?-o:o),d[p]=(s?"pos"==r?"+=":"-=":"pos"==r?"-=":"+=")+o,i.animate(d,{queue:!1,duration:e.duration,easing:e.easing,complete:function(){"hide"===n&&i.hide(),a.effects.restore(i,f),a.effects.removeWrapper(i),t()}})}});