/*!
 * jQuery UI Effects Explode 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/explode-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(b){return b.effects.effect.explode=function(e,i){var t,o,s,f,n,d,c=e.pieces?Math.round(Math.sqrt(e.pieces)):3,a=c,h=b(this),l="show"===b.effects.setMode(h,e.mode||"hide"),p=h.show().css("visibility","hidden").offset(),u=Math.ceil(h.outerWidth()/a),r=Math.ceil(h.outerHeight()/c),v=[];function y(){v.push(this),v.length===c*a&&function(){h.css({visibility:"visible"}),b(v).remove(),l||h.hide();i()}()}for(t=0;t<c;t++)for(f=p.top+t*r,d=t-(c-1)/2,o=0;o<a;o++)s=p.left+o*u,n=o-(a-1)/2,h.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-o*u,top:-t*r}).parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:u,height:r,left:s+(l?n*u:0),top:f+(l?d*r:0),opacity:l?0:1}).animate({left:s+(l?0:n*u),top:f+(l?0:d*r),opacity:l?1:0},e.duration||500,e.easing,y)}});