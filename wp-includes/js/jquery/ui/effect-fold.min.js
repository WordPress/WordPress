/*!
 * jQuery UI Effects Fold 1.11.4
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 * http://api.jqueryui.com/fold-effect/
 */
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(p){return p.effects.effect.fold=function(e,t){var i=p(this),h=["position","top","bottom","left","right","height","width"],f=p.effects.setMode(i,e.mode||"hide"),n="show"===f,o="hide"===f,s=e.size||15,d=/([0-9]+)%/.exec(s),r=!!e.horizFirst,c=n!=r,a=c?["width","height"]:["height","width"],g=e.duration/2,w={},u={};p.effects.save(i,h),i.show(),f=p.effects.createWrapper(i).css({overflow:"hidden"}),c=c?[f.width(),f.height()]:[f.height(),f.width()],d&&(s=parseInt(d[1],10)/100*c[o?0:1]),n&&f.css(r?{height:0,width:s}:{height:s,width:0}),w[a[0]]=n?c[0]:s,u[a[1]]=n?c[1]:0,f.animate(w,g,e.easing).animate(u,g,e.easing,function(){o&&i.hide(),p.effects.restore(i,h),p.effects.removeWrapper(i),t()})}});