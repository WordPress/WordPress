/*!
 * jQuery UI Effects Fold 1.13.0
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(m){"use strict";return m.effects.define("fold","hide",function(i,e){var t=m(this),c=i.mode,n="show"===c,f="hide"===c,s=i.size||15,o=/([0-9]+)%/.exec(s),a=!!i.horizFirst?["right","bottom"]:["bottom","right"],u=i.duration/2,l=m.effects.createPlaceholder(t),r=t.cssClip(),p={clip:m.extend({},r)},d={clip:m.extend({},r)},h=[r[a[0]],r[a[1]]],c=t.queue().length;o&&(s=parseInt(o[1],10)/100*h[f?0:1]),p.clip[a[0]]=s,d.clip[a[0]]=s,d.clip[a[1]]=0,n&&(t.cssClip(d.clip),l&&l.css(m.effects.clipToBox(d)),d.clip=r),t.queue(function(e){l&&l.animate(m.effects.clipToBox(p),u,i.easing).animate(m.effects.clipToBox(d),u,i.easing),e()}).animate(p,u,i.easing).animate(d,u,i.easing).queue(e),m.effects.unshift(t,c,4)})});