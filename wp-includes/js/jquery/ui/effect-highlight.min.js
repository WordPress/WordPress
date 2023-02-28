/*!
 * jQuery UI Effects Highlight 1.13.2
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(i){"use strict";return i.effects.define("highlight","show",function(e,n){var o=i(this),t={backgroundColor:o.css("backgroundColor")};"hide"===e.mode&&(t.opacity=0),i.effects.saveStyle(o),o.css({backgroundImage:"none",backgroundColor:e.color||"#ffff99"}).animate(t,{queue:!1,duration:e.duration,easing:e.easing,complete:n})})});