/*!
 * jQuery UI Effects Drop 1.13.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){"use strict";return r.effects.define("drop","hide",function(e,t){var i,n=r(this),o="show"===e.mode,f=e.direction||"left",c="up"===f||"down"===f?"top":"left",f="up"===f||"left"===f?"-=":"+=",u="+="==f?"-=":"+=",d={opacity:0};r.effects.createPlaceholder(n),i=e.distance||n["top"==c?"outerHeight":"outerWidth"](!0)/2,d[c]=f+i,o&&(n.css(d),d[c]=u+i,d.opacity=1),n.animate(d,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});