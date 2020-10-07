/*!
 * jQuery UI Effects Bounce 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(w){return w.effects.define("bounce",function(e,t){var i,n,f,o=w(this),a=e.mode,c="hide"===a,u="show"===a,s=e.direction||"up",d=e.distance,r=e.times||5,p=2*r+(u||c?1:0),h=e.duration/p,m=e.easing,y="up"===s||"down"===s?"top":"left",l="up"===s||"left"===s,g=0,q=o.queue().length;for(w.effects.createPlaceholder(o),f=o.css(y),d=d||o["top"==y?"outerHeight":"outerWidth"]()/3,u&&((n={opacity:1})[y]=f,o.css("opacity",0).css(y,l?2*-d:2*d).animate(n,h,m)),c&&(d/=Math.pow(2,r-1)),(n={})[y]=f;g<r;g++)(i={})[y]=(l?"-=":"+=")+d,o.animate(i,h,m).animate(n,h,m),d=c?2*d:d/2;c&&((i={opacity:0})[y]=(l?"-=":"+=")+d,o.animate(i,h,m)),o.queue(t),w.effects.unshift(o,q,1+p)})});