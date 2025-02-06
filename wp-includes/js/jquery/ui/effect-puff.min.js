/*!
 * jQuery UI Effects Puff 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect","./effect-scale"],e):e(jQuery)}(function(t){"use strict";return t.effects.define("puff","hide",function(e,f){e=t.extend(!0,{},e,{fade:!0,percent:parseInt(e.percent,10)||150});t.effects.effect.scale.call(this,e,f)})});