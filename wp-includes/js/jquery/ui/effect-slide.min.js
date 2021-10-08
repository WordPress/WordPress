/*!
 * jQuery UI Effects Slide 1.13.0
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(d){"use strict";return d.effects.define("slide","show",function(e,t){var i,o,c=d(this),n={up:["bottom","top"],down:["top","bottom"],left:["right","left"],right:["left","right"]},s=e.mode,f=e.direction||"left",l="up"===f||"down"===f?"top":"left",p="up"===f||"left"===f,u=e.distance||c["top"==l?"outerHeight":"outerWidth"](!0),r={};d.effects.createPlaceholder(c),i=c.cssClip(),o=c.position()[l],r[l]=(p?-1:1)*u+o,r.clip=c.cssClip(),r.clip[n[f][1]]=r.clip[n[f][0]],"show"===s&&(c.cssClip(r.clip),c.css(l,r[l]),r.clip=i,r[l]=o),c.animate(r,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});