/*!
 * jQuery UI Effects Blind 1.13.2
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(r){"use strict";return r.effects.define("blind","hide",function(e,t){var i={up:["bottom","top"],vertical:["bottom","top"],down:["top","bottom"],left:["right","left"],horizontal:["right","left"],right:["left","right"]},o=r(this),c=e.direction||"up",n=o.cssClip(),f={clip:r.extend({},n)},l=r.effects.createPlaceholder(o);f.clip[i[c][0]]=f.clip[i[c][1]],"show"===e.mode&&(o.cssClip(f.clip),l&&l.css(r.effects.clipToBox(f)),f.clip=n),l&&l.animate(r.effects.clipToBox(f),e.duration,e.easing),o.animate(f,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});