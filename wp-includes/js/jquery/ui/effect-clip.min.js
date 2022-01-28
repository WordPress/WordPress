/*!
 * jQuery UI Effects Clip 1.13.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],t):t(jQuery)}(function(r){"use strict";return r.effects.define("clip","hide",function(t,e){var i={},o=r(this),c=t.direction||"vertical",n="both"===c,f=n||"horizontal"===c,n=n||"vertical"===c,c=o.cssClip();i.clip={top:n?(c.bottom-c.top)/2:c.top,right:f?(c.right-c.left)/2:c.right,bottom:n?(c.bottom-c.top)/2:c.bottom,left:f?(c.right-c.left)/2:c.left},r.effects.createPlaceholder(o),"show"===t.mode&&(o.cssClip(i.clip),i.clip=c),o.animate(i,{queue:!1,duration:t.duration,easing:t.easing,complete:e})})});