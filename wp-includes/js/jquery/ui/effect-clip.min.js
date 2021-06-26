/*!
 * jQuery UI Effects Clip 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(t){"function"==typeof define&&define.amd?define(["jquery","./effect"],t):t(jQuery)}(function(r){return r.effects.define("clip","hide",function(t,e){var i={},o=r(this),n=t.direction||"vertical",c="both"===n,f=c||"horizontal"===n,c=c||"vertical"===n,n=o.cssClip();i.clip={top:c?(n.bottom-n.top)/2:n.top,right:f?(n.right-n.left)/2:n.right,bottom:c?(n.bottom-n.top)/2:n.bottom,left:f?(n.right-n.left)/2:n.left},r.effects.createPlaceholder(o),"show"===t.mode&&(o.cssClip(i.clip),i.clip=n),o.animate(i,{queue:!1,duration:t.duration,easing:t.easing,complete:e})})});