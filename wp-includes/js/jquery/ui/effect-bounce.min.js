/*!
 * jQuery UI Effects Bounce 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(l){"use strict";return l.effects.define("bounce",function(e,t){var i,n,o=l(this),c=e.mode,f="hide"===c,c="show"===c,u=e.direction||"up",s=e.distance,a=e.times||5,r=2*a+(c||f?1:0),d=e.duration/r,p=e.easing,h="up"===u||"down"===u?"top":"left",m="up"===u||"left"===u,y=0,e=o.queue().length;for(l.effects.createPlaceholder(o),u=o.css(h),s=s||o["top"==h?"outerHeight":"outerWidth"]()/3,c&&((n={opacity:1})[h]=u,o.css("opacity",0).css(h,m?2*-s:2*s).animate(n,d,p)),f&&(s/=Math.pow(2,a-1)),(n={})[h]=u;y<a;y++)(i={})[h]=(m?"-=":"+=")+s,o.animate(i,d,p).animate(n,d,p),s=f?2*s:s/2;f&&((i={opacity:0})[h]=(m?"-=":"+=")+s,o.animate(i,d,p)),o.queue(t),l.effects.unshift(o,e,1+r)})});