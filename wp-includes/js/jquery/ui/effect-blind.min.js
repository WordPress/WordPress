/*!
 * jQuery UI Effects Blind 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/blind-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.blind=function(b,c){var d,e,f,g=a(this),h=/up|down|vertical/,i=/up|left|vertical|horizontal/,j=["position","top","bottom","left","right","height","width"],k=a.effects.setMode(g,b.mode||"hide"),l=b.direction||"up",m=h.test(l),n=m?"height":"width",o=m?"top":"left",p=i.test(l),q={},r="show"===k;g.parent().is(".ui-effects-wrapper")?a.effects.save(g.parent(),j):a.effects.save(g,j),g.show(),d=a.effects.createWrapper(g).css({overflow:"hidden"}),e=d[n](),f=parseFloat(d.css(o))||0,q[n]=r?e:0,p||(g.css(m?"bottom":"right",0).css(m?"top":"left","auto").css({position:"absolute"}),q[o]=r?f:e+f),r&&(d.css(n,0),p||d.css(o,f+e)),d.animate(q,{duration:b.duration,easing:b.easing,queue:!1,complete:function(){"hide"===k&&g.hide(),a.effects.restore(g,j),a.effects.removeWrapper(g),c()}})}});