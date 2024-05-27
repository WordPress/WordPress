/*!
 * jQuery UI Effects Slide 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(d){"use strict";return d.effects.define("slide","show",function(e,t){var i,o,n=d(this),c={up:["bottom","top"],down:["top","bottom"],left:["right","left"],right:["left","right"]},s=e.mode,f=e.direction||"left",l="up"===f||"down"===f?"top":"left",p="up"===f||"left"===f,r=e.distance||n["top"==l?"outerHeight":"outerWidth"](!0),u={};d.effects.createPlaceholder(n),i=n.cssClip(),o=n.position()[l],u[l]=(p?-1:1)*r+o,u.clip=n.cssClip(),u.clip[c[f][1]]=u.clip[c[f][0]],"show"===s&&(n.cssClip(u.clip),n.css(l,u[l]),u.clip=i,u[l]=o),n.animate(u,{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});