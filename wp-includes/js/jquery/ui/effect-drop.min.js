/*!
 * jQuery UI Effects Drop 1.13.0
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){"use strict";return r.effects.define("drop","hide",function(e,t){var i=r(this),n="show"===e.mode,o=e.direction||"left",f="up"===o||"down"===o?"top":"left",c="up"===o||"left"===o?"-=":"+=",u="+="==c?"-=":"+=",d={opacity:0};r.effects.createPlaceholder(i),o=e.distance||i["top"==f?"outerHeight":"outerWidth"](!0)/2,d[f]=c+o,n&&(i.css(d),d[f]=u+o,d.opacity=1),i.animate(d,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});