/*!
 * jQuery UI Effects Bounce 1.11.2
 * http://jqueryui.com
 *
 * Copyright 2014 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/bounce-effect/
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./effect"],a):a(jQuery)}(function(a){return a.effects.effect.bounce=function(b,c){var d,e,f,g=a(this),h=["position","top","bottom","left","right","height","width"],i=a.effects.setMode(g,b.mode||"effect"),j="hide"===i,k="show"===i,l=b.direction||"up",m=b.distance,n=b.times||5,o=2*n+(k||j?1:0),p=b.duration/o,q=b.easing,r="up"===l||"down"===l?"top":"left",s="up"===l||"left"===l,t=g.queue(),u=t.length;for((k||j)&&h.push("opacity"),a.effects.save(g,h),g.show(),a.effects.createWrapper(g),m||(m=g["top"===r?"outerHeight":"outerWidth"]()/3),k&&(f={opacity:1},f[r]=0,g.css("opacity",0).css(r,s?2*-m:2*m).animate(f,p,q)),j&&(m/=Math.pow(2,n-1)),f={},f[r]=0,d=0;n>d;d++)e={},e[r]=(s?"-=":"+=")+m,g.animate(e,p,q).animate(f,p,q),m=j?2*m:m/2;j&&(e={opacity:0},e[r]=(s?"-=":"+=")+m,g.animate(e,p,q)),g.queue(function(){j&&g.hide(),a.effects.restore(g,h),a.effects.removeWrapper(g),c()}),u>1&&t.splice.apply(t,[1,0].concat(t.splice(u,o+1))),g.dequeue()}});