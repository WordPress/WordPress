/*!
 * jQuery UI Effects Scale 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/scale-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-size"],e):e(jQuery)}(function(a){return a.effects.effect.scale=function(e,t){var i=a(this),o=a.extend(!0,{},e),h=a.effects.setMode(i,e.mode||"effect"),f=parseInt(e.percent,10)||(0===parseInt(e.percent,10)?0:"hide"===h?0:100),r=e.direction||"both",c=e.origin,d={height:i.height(),width:i.width(),outerHeight:i.outerHeight(),outerWidth:i.outerWidth()},n="horizontal"!==r?f/100:1,u="vertical"!==r?f/100:1;o.effect="size",o.queue=!1,o.complete=t,"effect"!==h&&(o.origin=c||["middle","center"],o.restore=!0),o.from=e.from||("show"===h?{height:0,width:0,outerHeight:0,outerWidth:0}:d),o.to={height:d.height*n,width:d.width*u,outerHeight:d.outerHeight*n,outerWidth:d.outerWidth*u},o.fade&&("show"===h&&(o.from.opacity=0,o.to.opacity=1),"hide"===h&&(o.from.opacity=1,o.to.opacity=0)),i.effect(o)}});