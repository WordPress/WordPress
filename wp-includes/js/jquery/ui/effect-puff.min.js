/*!
 * jQuery UI Effects Puff 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect","./effect-scale"],e):e(jQuery)}(function(n){return n.effects.define("puff","hide",function(e,f){e=n.extend(!0,{},e,{fade:!0,percent:parseInt(e.percent,10)||150});n.effects.effect.scale.call(this,e,f)})});