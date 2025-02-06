/*!
 * jQuery UI Effects Blind 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(s){"use strict";return s.effects.define("blind","hide",function(e,t){var i={up:["bottom","top"],vertical:["bottom","top"],down:["top","bottom"],left:["right","left"],horizontal:["right","left"],right:["left","right"]},o=s(this),n=e.direction||"up",c=o.cssClip(),f={clip:s.extend({},c)},r=s.effects.createPlaceholder(o);f.clip[i[n][0]]=f.clip[i[n][1]],"show"===e.mode&&(o.cssClip(f.clip),r&&r.css(s.effects.clipToBox(f)),f.clip=c),r&&r.animate(s.effects.clipToBox(f),e.duration,e.easing),o.animate(f,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});