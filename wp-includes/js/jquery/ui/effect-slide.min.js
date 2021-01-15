/*!
 * jQuery UI Effects Slide 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/slide-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(u){return u.effects.effect.slide=function(e,t){var f=u(this),i=["position","top","bottom","left","right","width","height"],o=u.effects.setMode(f,e.mode||"show"),n="show"===o,s=e.direction||"left",r="up"===s||"down"===s?"top":"left",c="up"===s||"left"===s,d={};u.effects.save(f,i),f.show(),s=e.distance||f["top"==r?"outerHeight":"outerWidth"](!0),u.effects.createWrapper(f).css({overflow:"hidden"}),n&&f.css(r,c?isNaN(s)?"-"+s:-s:s),d[r]=(n?c?"+=":"-=":c?"-=":"+=")+s,f.animate(d,{queue:!1,duration:e.duration,easing:e.easing,complete:function(){"hide"===o&&f.hide(),u.effects.restore(f,i),u.effects.removeWrapper(f),t()}})}});