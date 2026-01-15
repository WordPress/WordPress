/*!
 * jQuery UI Effects Fold 1.13.3
 * https://jqueryui.com
 *
 * Copyright OpenJS Foundation and other contributors
 * Released under the MIT license.
 * https://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","../version","../effect"],e):e(jQuery)}(function(m){"use strict";return m.effects.define("fold","hide",function(i,e){var t=m(this),c=i.mode,n="show"===c,c="hide"===c,s=i.size||15,f=/([0-9]+)%/.exec(s),o=!!i.horizFirst?["right","bottom"]:["bottom","right"],a=i.duration/2,u=m.effects.createPlaceholder(t),l=t.cssClip(),r={clip:m.extend({},l)},p={clip:m.extend({},l)},d=[l[o[0]],l[o[1]]],h=t.queue().length;f&&(s=parseInt(f[1],10)/100*d[c?0:1]),r.clip[o[0]]=s,p.clip[o[0]]=s,p.clip[o[1]]=0,n&&(t.cssClip(p.clip),u&&u.css(m.effects.clipToBox(p)),p.clip=l),t.queue(function(e){u&&u.animate(m.effects.clipToBox(r),a,i.easing).animate(m.effects.clipToBox(p),a,i.easing),e()}).animate(r,a,i.easing).animate(p,a,i.easing).queue(e),m.effects.unshift(t,h,4)})});