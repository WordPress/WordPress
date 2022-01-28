/*!
 * jQuery UI Effects Bounce 1.13.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(l){"use strict";return l.effects.define("bounce",function(e,t){var i,n,c=l(this),f=e.mode,o="hide"===f,u="show"===f,a=e.direction||"up",s=e.distance,r=e.times||5,f=2*r+(u||o?1:0),d=e.duration/f,p=e.easing,h="up"===a||"down"===a?"top":"left",m="up"===a||"left"===a,y=0,e=c.queue().length;for(l.effects.createPlaceholder(c),a=c.css(h),s=s||c["top"==h?"outerHeight":"outerWidth"]()/3,u&&((n={opacity:1})[h]=a,c.css("opacity",0).css(h,m?2*-s:2*s).animate(n,d,p)),o&&(s/=Math.pow(2,r-1)),(n={})[h]=a;y<r;y++)(i={})[h]=(m?"-=":"+=")+s,c.animate(i,d,p).animate(n,d,p),s=o?2*s:s/2;o&&((i={opacity:0})[h]=(m?"-=":"+=")+s,c.animate(i,d,p)),c.queue(t),l.effects.unshift(c,e,1+f)})});