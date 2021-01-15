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
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(c){return c.effects.effect.transfer=function(e,t){var i=c(this),n=c(e.to),f="fixed"===n.css("position"),o=c("body"),s=f?o.scrollTop():0,d=f?o.scrollLeft():0,o=n.offset(),o={top:o.top-s,left:o.left-d,height:n.innerHeight(),width:n.innerWidth()},n=i.offset(),r=c("<div class='ui-effects-transfer'></div>").appendTo(document.body).addClass(e.className).css({top:n.top-s,left:n.left-d,height:i.innerHeight(),width:i.innerWidth(),position:f?"fixed":"absolute"}).animate(o,e.duration,e.easing,function(){r.remove(),t()})}});