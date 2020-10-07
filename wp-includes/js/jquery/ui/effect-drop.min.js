/*!
 * jQuery UI Effects Drop 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){return r.effects.define("drop","hide",function(e,t){var i,n=r(this),o="show"===e.mode,f=e.direction||"left",c="up"===f||"down"===f?"top":"left",d="up"===f||"left"===f?"-=":"+=",u="+="==d?"-=":"+=",a={opacity:0};r.effects.createPlaceholder(n),i=e.distance||n["top"==c?"outerHeight":"outerWidth"](!0)/2,a[c]=d+i,o&&(n.css(a),a[c]=u+i,a.opacity=1),n.animate(a,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});