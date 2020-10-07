/*!
 * jQuery UI Effects Clip 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(t){"function"==typeof define&&define.amd?define(["jquery","./effect"],t):t(jQuery)}(function(a){return a.effects.define("clip","hide",function(t,e){var i,o={},n=a(this),c=t.direction||"vertical",f="both"===c,r=f||"horizontal"===c,l=f||"vertical"===c;i=n.cssClip(),o.clip={top:l?(i.bottom-i.top)/2:i.top,right:r?(i.right-i.left)/2:i.right,bottom:l?(i.bottom-i.top)/2:i.bottom,left:r?(i.right-i.left)/2:i.left},a.effects.createPlaceholder(n),"show"===t.mode&&(n.cssClip(o.clip),o.clip=i),n.animate(o,{queue:!1,duration:t.duration,easing:t.easing,complete:e})})});