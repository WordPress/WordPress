/*!
 * jQuery UI Effects Drop 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(d){"use strict";return d.effects.define("drop","hide",function(e,t){var i,n=d(this),o="show"===e.mode,f=e.direction||"left",c="up"===f||"down"===f?"top":"left",f="up"===f||"left"===f?"-=":"+=",u="+="==f?"-=":"+=",r={opacity:0};d.effects.createPlaceholder(n),i=e.distance||n["top"==c?"outerHeight":"outerWidth"](!0)/2,r[c]=f+i,o&&(n.css(r),r[c]=u+i,r.opacity=1),n.animate(r,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});