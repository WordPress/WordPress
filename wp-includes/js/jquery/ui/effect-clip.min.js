/*!
 * jQuery UI Effects Clip 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/clip-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.clip=function(b,c){var d,e,f,g=a(this),h=["position","top","bottom","left","right","height","width"],i=a.effects.setMode(g,b.mode||"hide"),j="show"===i,k=b.direction||"vertical",l="vertical"===k,m=l?"height":"width",n=l?"top":"left",o={};a.effects.save(g,h),g.show(),d=a.effects.createWrapper(g).css({overflow:"hidden"}),e="IMG"===g[0].tagName?d:g,f=e[m](),j&&(e.css(m,0),e.css(n,f/2)),o[m]=j?f:0,o[n]=j?0:f/2,e.animate(o,{queue:!1,duration:b.duration,easing:b.easing,complete:function(){j||g.hide(),a.effects.restore(g,h),a.effects.removeWrapper(g),c()}})}});