/*! jQuery UI - v1.10.0 - 2013-01-17
* http://jqueryui.com
* Includes: jquery.ui.effect-fade.js
* Copyright 2013 jQuery Foundation and other contributors; Licensed MIT */
(function(e,t){e.effects.effect.fade=function(t,n){var r=e(this),i=e.effects.setMode(r,t.mode||"toggle");r.animate({opacity:i},{queue:!1,duration:t.duration,easing:t.easing,complete:n})}})(jQuery);