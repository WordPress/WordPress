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
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-size"],a):a(jQuery)}(function(a){return a.effects.effect.scale=function(b,c){var d=a(this),e=a.extend(!0,{},b),f=a.effects.setMode(d,b.mode||"effect"),g=parseInt(b.percent,10)||(0===parseInt(b.percent,10)?0:"hide"===f?0:100),h=b.direction||"both",i=b.origin,j={height:d.height(),width:d.width(),outerHeight:d.outerHeight(),outerWidth:d.outerWidth()},k={y:"horizontal"!==h?g/100:1,x:"vertical"!==h?g/100:1};e.effect="size",e.queue=!1,e.complete=c,"effect"!==f&&(e.origin=i||["middle","center"],e.restore=!0),e.from=b.from||("show"===f?{height:0,width:0,outerHeight:0,outerWidth:0}:j),e.to={height:j.height*k.y,width:j.width*k.x,outerHeight:j.outerHeight*k.y,outerWidth:j.outerWidth*k.x},e.fade&&("show"===f&&(e.from.opacity=0,e.to.opacity=1),"hide"===f&&(e.from.opacity=1,e.to.opacity=0)),d.effect(e)}});