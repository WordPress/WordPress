/*!
 * jQuery UI Effects Explode 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(b){"use strict";return b.effects.define("explode","hide",function(e,i){var t,o,s,n,f,d,c=e.pieces?Math.round(Math.sqrt(e.pieces)):3,a=c,l=b(this),r="show"===e.mode,h=l.show().css("visibility","hidden").offset(),p=Math.ceil(l.outerWidth()/a),u=Math.ceil(l.outerHeight()/c),v=[];function y(){v.push(this),v.length===c*a&&(l.css({visibility:"visible"}),b(v).remove(),i())}for(t=0;t<c;t++)for(n=h.top+t*u,d=t-(c-1)/2,o=0;o<a;o++)s=h.left+o*p,f=o-(a-1)/2,l.clone().appendTo("body").wrap("<div></div>").css({position:"absolute",visibility:"visible",left:-o*p,top:-t*u}).parent().addClass("ui-effects-explode").css({position:"absolute",overflow:"hidden",width:p,height:u,left:s+(r?f*p:0),top:n+(r?d*u:0),opacity:r?0:1}).animate({left:s+(r?0:f*p),top:n+(r?0:d*u),opacity:r?1:0},e.duration||500,e.easing,y)})});