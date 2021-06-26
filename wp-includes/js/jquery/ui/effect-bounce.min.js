/*!
 * jQuery UI Effects Bounce 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(l){return l.effects.define("bounce",function(e,t){var i,n,f=l(this),o=e.mode,a="hide"===o,c="show"===o,u=e.direction||"up",s=e.distance,d=e.times||5,o=2*d+(c||a?1:0),r=e.duration/o,p=e.easing,h="up"===u||"down"===u?"top":"left",m="up"===u||"left"===u,y=0,e=f.queue().length;for(l.effects.createPlaceholder(f),u=f.css(h),s=s||f["top"==h?"outerHeight":"outerWidth"]()/3,c&&((n={opacity:1})[h]=u,f.css("opacity",0).css(h,m?2*-s:2*s).animate(n,r,p)),a&&(s/=Math.pow(2,d-1)),(n={})[h]=u;y<d;y++)(i={})[h]=(m?"-=":"+=")+s,f.animate(i,r,p).animate(n,r,p),s=a?2*s:s/2;a&&((i={opacity:0})[h]=(m?"-=":"+=")+s,f.animate(i,r,p)),f.queue(t),l.effects.unshift(f,e,1+o)})});