/*!
 * jQuery UI Effects Shake 1.11.2
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/shake-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.shake=function(b,c){var d,e=a(this),f=["position","top","bottom","left","right","height","width"],g=a.effects.setMode(e,b.mode||"effect"),h=b.direction||"left",i=b.distance||20,j=b.times||3,k=2*j+1,l=Math.round(b.duration/k),m="up"===h||"down"===h?"top":"left",n="up"===h||"left"===h,o={},p={},q={},r=e.queue(),s=r.length;for(a.effects.save(e,f),e.show(),a.effects.createWrapper(e),o[m]=(n?"-=":"+=")+i,p[m]=(n?"+=":"-=")+2*i,q[m]=(n?"-=":"+=")+2*i,e.animate(o,l,b.easing),d=1;j>d;d++)e.animate(p,l,b.easing).animate(q,l,b.easing);e.animate(p,l,b.easing).animate(o,l/2,b.easing).queue(function(){"hide"===g&&e.hide(),a.effects.restore(e,f),a.effects.removeWrapper(e),c()}),s>1&&r.splice.apply(r,[1,0].concat(r.splice(s,k+1))),e.dequeue()}});