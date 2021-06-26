/*!
 * jQuery UI Effects Explode 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(b){return b.effects.define("explode","hide",function(e,i){var t,o,s,n,f,d,a=e.pieces?Math.round(Math.sqrt(e.pieces)):3,c=a,l=b(this),h="show"===e.mode,p=l.show().css("visibility","hidden").offset(),r=Math.ceil(l.outerWidth()/c),u=Math.ceil(l.outerHeight()/a),v=[];function y(){v.push(this),v.length===a*c&&(l.css({visibility:"visible"}),b(v).remove(),i())}for(t=0;t<a;t++)for(n=p.top+t*u,d=t-(a-1)/2,o=0;o<c;o++)s=p.left+o*r,f=o-(c-1)/2,l.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-o*r,top:-t*u}).parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:r,height:u,left:s+(h?f*r:0),top:n+(h?d*u:0),opacity:h?0:1}).animate({left:s+(h?0:f*r),top:n+(h?0:d*u),opacity:h?1:0},e.duration||500,e.easing,y)})});