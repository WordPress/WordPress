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
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(m){return m.effects.effect.blind=function(e,t){var s,i,o,f=m(this),n=["position","top","bottom","left","right","height","width"],c=m.effects.setMode(f,e.mode||"hide"),r=e.direction||"up",a=/up|down|vertical/.test(r),p=a?"height":"width",d=a?"top":"left",u=/up|left|vertical|horizontal/.test(r),h={},l="show"===c;f.parent().is(".ui-effects-wrapper")?m.effects.save(f.parent(),n):m.effects.save(f,n),f.show(),i=(s=m.effects.createWrapper(f).css({overflow:"hidden"}))[p](),o=parseFloat(s.css(d))||0,h[p]=l?i:0,u||(f.css(a?"bottom":"right",0).css(a?"top":"left","auto").css({position:"absolute"}),h[d]=l?o:i+o),l&&(s.css(p,0),u||s.css(d,o+i)),s.animate(h,{duration:e.duration,easing:e.easing,queue:!1,complete:function(){"hide"===c&&f.hide(),m.effects.restore(f,n),m.effects.removeWrapper(f),t()}})}});