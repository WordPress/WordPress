/*!
 * jQuery UI Effects Slide 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){return r.effects.define("slide","show",function(e,t){var i,o,n=r(this),c={up:["bottom","top"],down:["top","bottom"],left:["right","left"],right:["left","right"]},f=e.mode,l=e.direction||"left",p="up"===l||"down"===l?"top":"left",s="up"===l||"left"===l,u=e.distance||n["top"==p?"outerHeight":"outerWidth"](!0),d={};r.effects.createPlaceholder(n),i=n.cssClip(),o=n.position()[p],d[p]=(s?-1:1)*u+o,d.clip=n.cssClip(),d.clip[c[l][1]]=d.clip[c[l][0]],"show"===f&&(n.cssClip(d.clip),n.css(p,d[p]),d.clip=i,d[p]=o),n.animate(d,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});