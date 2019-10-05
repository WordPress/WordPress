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
!function(e){"function"==typeof define&&define.amd?define(["jquery","./effect"],e):e(jQuery)}(function(v){return v.effects.effect.fold=function(e,t){var i,h,f=v(this),n=["position","top","bottom","left","right","height","width"],o=v.effects.setMode(f,e.mode||"hide"),s="show"===o,d="hide"===o,r=e.size||15,c=/([0-9]+)%/.exec(r),a=!!e.horizFirst,g=s!=a,w=g?["width","height"]:["height","width"],u=e.duration/2,p={},m={};v.effects.save(f,n),f.show(),i=v.effects.createWrapper(f).css({overflow:"hidden"}),h=g?[i.width(),i.height()]:[i.height(),i.width()],c&&(r=parseInt(c[1],10)/100*h[d?0:1]),s&&i.css(a?{height:0,width:r}:{height:r,width:0}),p[w[0]]=s?h[0]:r,m[w[1]]=s?h[1]:0,i.animate(p,u,e.easing).animate(m,u,e.easing,function(){d&&f.hide(),v.effects.restore(f,n),v.effects.removeWrapper(f),t()})}});