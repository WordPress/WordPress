/*!
 * jQuery UI Effects Highlight 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(t){"use strict";return t.effects.define("highlight","show",function(e,n){var o=t(this),i={backgroundColor:o.css("backgroundColor")};"hide"===e.mode&&(i.opacity=0),t.effects.saveStyle(o),o.css({backgroundImage:"none",backgroundColor:e.color||"#ffff99"}).animate(i,{queue:!1,duration:e.duration,easing:e.easing,complete:n})})});