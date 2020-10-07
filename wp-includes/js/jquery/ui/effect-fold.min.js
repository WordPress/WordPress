/*!
 * jQuery UI Effects Fold 1.12.1
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(g){return g.effects.define("fold","hide",function(i,e){var t=g(this),n=i.mode,c="show"===n,f="hide"===n,o=i.size||15,s=/([0-9]+)%/.exec(o),a=!!i.horizFirst?["right","bottom"]:["bottom","right"],l=i.duration/2,u=g.effects.createPlaceholder(t),p=t.cssClip(),d={clip:g.extend({},p)},r={clip:g.extend({},p)},h=[p[a[0]],p[a[1]]],m=t.queue().length;s&&(o=parseInt(s[1],10)/100*h[f?0:1]),d.clip[a[0]]=o,r.clip[a[0]]=o,r.clip[a[1]]=0,c&&(t.cssClip(r.clip),u&&u.css(g.effects.clipToBox(r)),r.clip=p),t.queue(function(e){u&&u.animate(g.effects.clipToBox(d),l,i.easing).animate(g.effects.clipToBox(r),l,i.easing),e()}).animate(d,l,i.easing).animate(r,l,i.easing).queue(e),g.effects.unshift(t,m,4)})});