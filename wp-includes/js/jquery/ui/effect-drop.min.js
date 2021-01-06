/*!
 * jQuery UI Effects Drop 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(a){return a.effects.define("drop","hide",function(e,t){var i=a(this),n="show"===e.mode,o=e.direction||"left",f="up"===o||"down"===o?"top":"left",c="up"===o||"left"===o?"-=":"+=",d="+="==c?"-=":"+=",u={opacity:0};a.effects.createPlaceholder(i),o=e.distance||i["top"==f?"outerHeight":"outerWidth"](!0)/2,u[f]=c+o,n&&(i.css(u),u[f]=d+o,u.opacity=1),i.animate(u,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});