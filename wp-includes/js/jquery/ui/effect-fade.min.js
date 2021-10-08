/*!
 * jQuery UI Effects Fade 1.13.0
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(n){"use strict";return n.effects.define("fade","toggle",function(e,t){var i="show"===e.mode;n(this).css("opacity",i?0:1).animate({opacity:i?1:0},{queue:!1,duration:e.duration,easing:e.easing,complete:t})})});