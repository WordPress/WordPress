/*!
 * jQuery UI Effects Bounce 1.13.2
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(l){"use strict";return l.effects.define("bounce",function(e,t){var i,n,c=l(this),f=e.mode,o="hide"===f,f="show"===f,u=e.direction||"up",a=e.distance,s=e.times||5,r=2*s+(f||o?1:0),d=e.duration/r,p=e.easing,h="up"===u||"down"===u?"top":"left",m="up"===u||"left"===u,y=0,e=c.queue().length;for(l.effects.createPlaceholder(c),u=c.css(h),a=a||c["top"==h?"outerHeight":"outerWidth"]()/3,f&&((n={opacity:1})[h]=u,c.css("opacity",0).css(h,m?2*-a:2*a).animate(n,d,p)),o&&(a/=Math.pow(2,s-1)),(n={})[h]=u;y<s;y++)(i={})[h]=(m?"-=":"+=")+a,c.animate(i,d,p).animate(n,d,p),a=o?2*a:a/2;o&&((i={opacity:0})[h]=(m?"-=":"+=")+a,c.animate(i,d,p)),c.queue(t),l.effects.unshift(c,e,1+r)})});