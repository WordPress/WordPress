/*!
 * jQuery UI Effects Clip 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],t):t(jQuery)}(function(f){"use strict";return f.effects.define("clip","hide",function(t,e){var i={},o=f(this),c=t.direction||"vertical",n="both"===c,r=n||"horizontal"===c,n=n||"vertical"===c,c=o.cssClip();i.clip={top:n?(c.bottom-c.top)/2:c.top,right:r?(c.right-c.left)/2:c.right,bottom:n?(c.bottom-c.top)/2:c.bottom,left:r?(c.right-c.left)/2:c.left},f.effects.createPlaceholder(o),"show"===t.mode&&(o.cssClip(i.clip),i.clip=c),o.animate(i,{queue:!1,duration:t.duration,easing:t.easing,complete:e})})});