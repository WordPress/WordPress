/*!
 * jQuery UI Effects Fade 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(t){return t.effects.define("fade","toggle",function(e,n){var i="show"===e.mode;t(this).css("opacity",i?0:1).animate({opacity:i?1:0},{queue:!1,duration:e.duration,easing:e.easing,complete:n})})});