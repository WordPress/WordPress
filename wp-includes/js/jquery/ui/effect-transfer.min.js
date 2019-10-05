/*!
 * jQuery UI Effects Transfer 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/transfer-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(u){return u.effects.effect.transfer=function(e,t){var i=u(this),n=u(e.to),f="fixed"===n.css("position"),o=u("body"),s=f?o.scrollTop():0,d=f?o.scrollLeft():0,r=n.offset(),c={top:r.top-s,left:r.left-d,height:n.innerHeight(),width:n.innerWidth()},a=i.offset(),l=u("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(e.className).css({top:a.top-s,left:a.left-d,height:i.innerHeight(),width:i.innerWidth(),position:f?"fixed":"absolute"}).animate(c,e.duration,e.easing,function(){l.remove(),t()})}});