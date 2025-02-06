/*!
 * jQuery UI Effects Shake 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(h){"use strict";return h.effects.define("shake",function(e,t){var n=1,i=h(this),a=e.direction||"left",f=e.distance||20,s=e.times||3,u=2*s+1,c=Math.round(e.duration/u),r="up"===a||"down"===a?"top":"left",a="up"===a||"left"===a,o={},d={},m={},g=i.queue().length;for(h.effects.createPlaceholder(i),o[r]=(a?"-=":"+=")+f,d[r]=(a?"+=":"-=")+2*f,m[r]=(a?"-=":"+=")+2*f,i.animate(o,c,e.easing);n<s;n++)i.animate(d,c,e.easing).animate(m,c,e.easing);i.animate(d,c,e.easing).animate(o,c/2,e.easing).queue(t),h.effects.unshift(i,g,1+u)})});