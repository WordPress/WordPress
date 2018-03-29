(function($, document, window) {
	if (!window['wordfenceAJAXWatcher']) {
		window['wordfenceAJAXWatcher'] = {
			blockWarningOpen: false,
			
			init: function() {
				$(document).ajaxError(function (event, jqXHR, ajaxSettings, thrownError) {
					if (wordfenceAJAXWatcher.blockWarningOpen) {
						return;
					}
					
					var requestURL = ajaxSettings.url;
					if (requestURL.length > 63) {
						requestURL = requestURL.substring(0, 30) + '...' + requestURL.substring(requestURL.length - 30);
					}
					var requestURLEscaped = $('<div/>').text(requestURL).html();
					var responseDOM = $(jqXHR.responseText);
					var formAction = responseDOM.filter('#whitelist-form').add(responseDOM.find('#whitelist-form')).attr('action');
					var inputs = responseDOM.filter('input[name]').add(responseDOM.find('input[name]'));
					var queryParams = {}; 
					for (var i = 0; i < inputs.length; i++) {
						queryParams[inputs[i].name] = inputs[i].value;
					}
					
					if (!(typeof formAction === "string")) { //Only progress if it's our plugin doing the blocking
						return;
					}

					wordfenceAJAXWatcher.blockWarningOpen = true;
					$.wordfenceBox({
						closeButton: false,
						width: '400px',
						html: "<h3>Background Request Blocked</h3><p>Wordfence Firewall blocked a background request to WordPress for the URL <code>" + requestURLEscaped + "</code>. If this occurred as a result of an intentional action, you may consider whitelisting the request to allow it in the future.</p><p class=\"wf-right\"><a href=\"https://www.wordfence.com/help/?query=ajax-blocked\" target=\"_blank\" rel=\"noopener noreferrer\" class=\"wfboxhelp\"></a><a href=\"#\" class=\"button\" id=\"background-block-whitelist\">Whitelist this action</a> <a href=\"#\" class=\"button\" id=\"background-block-dismiss\">Dismiss</a></p>",
						onComplete: function() {
							$('#background-block-dismiss').click(function(event) {
								event.preventDefault();
								event.stopPropagation();
								$.wordfenceBox.close();
							});
							
							$('#background-block-whitelist').click(function(event) {
								event.preventDefault();
								event.stopPropagation();

								if (confirm('Are you sure you want to whitelist this action?')) {
									$.ajax({
										method: 'POST',
										url: formAction,
										data: queryParams,
										global: false,
										success: function() {
											alert('The request has been whitelisted. Please try it again.');
											$.wordfenceBox.close();
										},
										error: function() {
											alert('An error occurred when adding the request to the whitelist.');
											$.wordfenceBox.close();
										}
									});
								}
							});
						},
						onClosed: function() {
							wordfenceAJAXWatcher.blockWarningOpen = false;
						}
					});
				});
			}
		}
	}
	$(function() {
		wordfenceAJAXWatcher.init();
	});

}(jQuery, document, window));

!function(t,e,i){function n(i,n,o){var r=e.createElement(i);return n&&(r.id=Z+n),o&&(r.style.cssText=o),t(r)}function o(){return i.innerHeight?i.innerHeight:t(i).height()}function r(e,i){i!==Object(i)&&(i={}),this.cache={},this.el=e,this.value=function(e){var n;return void 0===this.cache[e]&&(n=t(this.el).attr("data-wfbox-"+e),void 0!==n?this.cache[e]=n:void 0!==i[e]?this.cache[e]=i[e]:void 0!==X[e]&&(this.cache[e]=X[e])),this.cache[e]},this.get=function(e){var i=this.value(e);return t.isFunction(i)?i.call(this.el,this):i}}function h(t){var e=W.length,i=(A+t)%e;return 0>i?e+i:i}function a(t,e){return Math.round((/%/.test(t)?("x"===e?E.width():o())/100:1)*parseInt(t,10))}function s(t,e){return t.get("photo")||t.get("photoRegex").test(e)}function l(t,e){return t.get("retinaUrl")&&i.devicePixelRatio>1?e.replace(t.get("photoRegex"),t.get("retinaSuffix")):e}function d(t){"contains"in x[0]&&!x[0].contains(t.target)&&t.target!==v[0]&&(t.stopPropagation(),x.focus())}function c(t){c.str!==t&&(x.add(v).removeClass(c.str).addClass(t),c.str=t)}function g(e){A=0,e&&e!==!1&&"nofollow"!==e?(W=t("."+tt).filter(function(){var i=t.data(this,Y),n=new r(this,i);return n.get("rel")===e}),A=W.index(_.el),-1===A&&(W=W.add(_.el),A=W.length-1)):W=t(_.el)}function u(i){t(e).trigger(i),at.triggerHandler(i)}function f(i){var o;if(!G){if(o=t(i).data(Y),_=new r(i,o),g(_.get("rel")),!U){U=$=!0,c(_.get("className")),x.css({visibility:"hidden",display:"block",opacity:""}),I=n(st,"LoadedContent","width:0; height:0; overflow:hidden; visibility:hidden"),b.css({width:"",height:""}).append(I),j=T.height()+k.height()+b.outerHeight(!0)-b.height(),D=C.width()+H.width()+b.outerWidth(!0)-b.width(),N=I.outerHeight(!0),z=I.outerWidth(!0);var h=a(_.get("initialWidth"),"x"),s=a(_.get("initialHeight"),"y"),l=_.get("maxWidth"),f=_.get("maxHeight");_.w=Math.max((l!==!1?Math.min(h,a(l,"x")):h)-z-D,0),_.h=Math.max((f!==!1?Math.min(s,a(f,"y")):s)-N-j,0),I.css({width:"",height:_.h}),J.position(),u(et),_.get("onOpen"),O.add(F).hide(),x.focus(),_.get("trapFocus")&&e.addEventListener&&(e.addEventListener("focus",d,!0),at.one(rt,function(){e.removeEventListener("focus",d,!0)})),_.get("returnFocus")&&at.one(rt,function(){t(_.el).focus()})}var p=parseFloat(_.get("opacity"));v.css({opacity:p===p?p:"",cursor:_.get("overlayClose")?"pointer":"",visibility:"visible"}).show(),_.get("closeButton")?P.html(_.get("close")).appendTo(b):P.appendTo("<div/>"),w()}}function p(){x||(V=!1,E=t(i),x=n(st).attr({id:Y,"class":t.support.opacity===!1?Z+"IE":"",role:"dialog",tabindex:"-1"}).hide(),v=n(st,"Overlay").hide(),L=t([n(st,"LoadingOverlay")[0],n(st,"LoadingGraphic")[0]]),y=n(st,"Wrapper"),b=n(st,"Content").append(F=n(st,"Title"),R=n(st,"Current"),K=t('<button type="button"/>').attr({id:Z+"Previous"}),B=t('<button type="button"/>').attr({id:Z+"Next"}),S=t('<button type="button"/>').attr({id:Z+"Slideshow"}),L),P=t('<button type="button"/>').attr({id:Z+"Close"}),y.append(n(st).append(n(st,"TopLeft"),T=n(st,"TopCenter"),n(st,"TopRight")),n(st,!1,"clear:left").append(C=n(st,"MiddleLeft"),b,H=n(st,"MiddleRight")),n(st,!1,"clear:left").append(n(st,"BottomLeft"),k=n(st,"BottomCenter"),n(st,"BottomRight"))).find("div div").css({"float":"left"}),M=n(st,!1,"position:absolute; width:9999px; visibility:hidden; display:none; max-width:none;"),O=B.add(K).add(R).add(S)),e.body&&!x.parent().length&&t(e.body).append(v,x.append(y,M))}function m(){function i(t){t.which>1||t.shiftKey||t.altKey||t.metaKey||t.ctrlKey||(t.preventDefault(),f(this))}return x?(V||(V=!0,B.click(function(){J.next()}),K.click(function(){J.prev()}),P.click(function(){J.close()}),v.click(function(){_.get("overlayClose")&&J.close()}),t(e).bind("keydown."+Z,function(t){var e=t.keyCode;U&&_.get("escKey")&&27===e&&(t.preventDefault(),J.close()),U&&_.get("arrowKey")&&W[1]&&!t.altKey&&(37===e?(t.preventDefault(),K.click()):39===e&&(t.preventDefault(),B.click()))}),t.isFunction(t.fn.on)?t(e).on("click."+Z,"."+tt,i):t("."+tt).live("click."+Z,i)),!0):!1}function w(){var e,o,r,h=J.prep,d=++lt;if($=!0,q=!1,u(ht),u(it),_.get("onLoad"),_.h=_.get("height")?a(_.get("height"),"y")-N-j:_.get("innerHeight")&&a(_.get("innerHeight"),"y"),_.w=_.get("width")?a(_.get("width"),"x")-z-D:_.get("innerWidth")&&a(_.get("innerWidth"),"x"),_.mw=_.w,_.mh=_.h,_.get("maxWidth")&&(_.mw=a(_.get("maxWidth"),"x")-z-D,_.mw=_.w&&_.w<_.mw?_.w:_.mw),_.get("maxHeight")&&(_.mh=a(_.get("maxHeight"),"y")-N-j,_.mh=_.h&&_.h<_.mh?_.h:_.mh),e=_.get("href"),Q=setTimeout(function(){L.show()},100),_.get("inline")){var c=t(e).eq(0);r=t("<div>").hide().insertBefore(c),at.one(ht,function(){r.replaceWith(c)}),h(c)}else _.get("iframe")?h(" "):_.get("html")?h(_.get("html")):s(_,e)?(e=l(_,e),q=_.get("createImg"),t(q).addClass(Z+"Photo").bind("error."+Z,function(){h(n(st,"Error").html(_.get("imgError")))}).one("load",function(){d===lt&&setTimeout(function(){var e;_.get("retinaImage")&&i.devicePixelRatio>1&&(q.height=q.height/i.devicePixelRatio,q.width=q.width/i.devicePixelRatio),_.get("scalePhotos")&&(o=function(){q.height-=q.height*e,q.width-=q.width*e},_.mw&&q.width>_.mw&&(e=(q.width-_.mw)/q.width,o()),_.mh&&q.height>_.mh&&(e=(q.height-_.mh)/q.height,o())),_.h&&(q.style.marginTop=Math.max(_.mh-q.height,0)/2+"px"),W[1]&&(_.get("loop")||W[A+1])&&(q.style.cursor="pointer",t(q).bind("click."+Z,function(){J.next()})),q.style.width=q.width+"px",q.style.height=q.height+"px",h(q)},1)}),q.src=e):e&&M.load(e,_.get("data"),function(e,i){d===lt&&h("error"===i?n(st,"Error").html(_.get("xhrError")):t(this).contents())})}var v,x,y,b,T,C,H,k,W,E,I,M,L,F,R,S,B,K,P,O,_,j,D,N,z,A,q,U,$,G,Q,J,V,X={html:!1,photo:!1,iframe:!1,inline:!1,transition:"elastic",speed:300,fadeOut:300,width:!1,initialWidth:"600",innerWidth:!1,maxWidth:!1,height:!1,initialHeight:"450",innerHeight:!1,maxHeight:!1,scalePhotos:!0,scrolling:!0,opacity:.9,preloading:!0,className:!1,overlayClose:!0,escKey:!0,arrowKey:!0,top:!1,bottom:!1,left:!1,right:!1,fixed:!1,data:void 0,closeButton:!0,fastIframe:!0,open:!1,reposition:!0,loop:!0,slideshow:!1,slideshowAuto:!0,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",photoRegex:/\.(gif|png|jp(e|g|eg)|bmp|ico|webp|jxr|svg)((#|\?).*)?$/i,retinaImage:!1,retinaUrl:!1,retinaSuffix:"@2x.$1",current:"image {current} of {total}",previous:"previous",next:"next",close:"close",xhrError:"This content failed to load.",imgError:"This image failed to load.",returnFocus:!0,trapFocus:!0,onOpen:!1,onLoad:!1,onComplete:!1,onCleanup:!1,onClosed:!1,rel:function(){return this.rel},href:function(){return t(this).attr("href")},title:function(){return this.title},createImg:function(){var e=new Image,i=t(this).data("cbox-img-attrs");return"object"==typeof i&&t.each(i,function(t,i){e[t]=i}),e},createIframe:function(){var i=e.createElement("iframe"),n=t(this).data("cbox-iframe-attrs");return"object"==typeof n&&t.each(n,function(t,e){i[t]=e}),"frameBorder"in i&&(i.frameBorder=0),"allowTransparency"in i&&(i.allowTransparency="true"),i.name=(new Date).getTime(),i.allowFullscreen=!0,i}},Y="wordfenceBox",Z="wfbox",tt=Z+"Element",et=Z+"_open",it=Z+"_load",nt=Z+"_complete",ot=Z+"_cleanup",rt=Z+"_closed",ht=Z+"_purge",at=t("<a/>"),st="div",lt=0,dt={},ct=function(){function t(){clearTimeout(h)}function e(){(_.get("loop")||W[A+1])&&(t(),h=setTimeout(J.next,_.get("slideshowSpeed")))}function i(){S.html(_.get("slideshowStop")).unbind(s).one(s,n),at.bind(nt,e).bind(it,t),x.removeClass(a+"off").addClass(a+"on")}function n(){t(),at.unbind(nt,e).unbind(it,t),S.html(_.get("slideshowStart")).unbind(s).one(s,function(){J.next(),i()}),x.removeClass(a+"on").addClass(a+"off")}function o(){r=!1,S.hide(),t(),at.unbind(nt,e).unbind(it,t),x.removeClass(a+"off "+a+"on")}var r,h,a=Z+"Slideshow_",s="click."+Z;return function(){r?_.get("slideshow")||(at.unbind(ot,o),o()):_.get("slideshow")&&W[1]&&(r=!0,at.one(ot,o),_.get("slideshowAuto")?i():n(),S.show())}}();t[Y]||(t(p),J=t.fn[Y]=t[Y]=function(e,i){var n,o=this;return e=e||{},t.isFunction(o)&&(o=t("<a/>"),e.open=!0),o[0]?(p(),m()&&(i&&(e.onComplete=i),o.each(function(){var i=t.data(this,Y)||{};t.data(this,Y,t.extend(i,e))}).addClass(tt),n=new r(o[0],e),n.get("open")&&f(o[0])),o):o},J.position=function(e,i){function n(){T[0].style.width=k[0].style.width=b[0].style.width=parseInt(x[0].style.width,10)-D+"px",b[0].style.height=C[0].style.height=H[0].style.height=parseInt(x[0].style.height,10)-j+"px"}var r,h,s,l=0,d=0,c=x.offset();if(E.unbind("resize."+Z),x.css({top:-9e4,left:-9e4}),h=E.scrollTop(),s=E.scrollLeft(),_.get("fixed")?(c.top-=h,c.left-=s,x.css({position:"fixed"})):(l=h,d=s,x.css({position:"absolute"})),d+=_.get("right")!==!1?Math.max(E.width()-_.w-z-D-a(_.get("right"),"x"),0):_.get("left")!==!1?a(_.get("left"),"x"):Math.round(Math.max(E.width()-_.w-z-D,0)/2),l+=_.get("bottom")!==!1?Math.max(o()-_.h-N-j-a(_.get("bottom"),"y"),0):_.get("top")!==!1?a(_.get("top"),"y"):Math.round(Math.max(o()-_.h-N-j,0)/2),x.css({top:c.top,left:c.left,visibility:"visible"}),y[0].style.width=y[0].style.height="9999px",r={width:_.w+z+D,height:_.h+N+j,top:l,left:d},e){var g=0;t.each(r,function(t){return r[t]!==dt[t]?void(g=e):void 0}),e=g}dt=r,e||x.css(r),x.dequeue().animate(r,{duration:e||0,complete:function(){n(),$=!1,y[0].style.width=_.w+z+D+"px",y[0].style.height=_.h+N+j+"px",_.get("reposition")&&setTimeout(function(){E.bind("resize."+Z,J.position)},1),t.isFunction(i)&&i()},step:n})},J.resize=function(t){var e;U&&(t=t||{},t.width&&(_.w=a(t.width,"x")-z-D),t.innerWidth&&(_.w=a(t.innerWidth,"x")),I.css({width:_.w}),t.height&&(_.h=a(t.height,"y")-N-j),t.innerHeight&&(_.h=a(t.innerHeight,"y")),t.innerHeight||t.height||(e=I.scrollTop(),I.css({height:"auto"}),_.h=I.height()),I.css({height:_.h}),e&&I.scrollTop(e),J.position("none"===_.get("transition")?0:_.get("speed")))},J.prep=function(i){function o(){return _.w=_.w||I.width(),_.w=_.mw&&_.mw<_.w?_.mw:_.w,_.w}function a(){return _.h=_.h||I.height(),_.h=_.mh&&_.mh<_.h?_.mh:_.h,_.h}if(U){var d,g="none"===_.get("transition")?0:_.get("speed");I.remove(),I=n(st,"LoadedContent").append(i),I.hide().appendTo(M.show()).css({width:o(),overflow:_.get("scrolling")?"auto":"hidden"}).css({height:a()}).prependTo(b),M.hide(),t(q).css({"float":"none"}),c(_.get("className")),d=function(){function i(){t.support.opacity===!1&&x[0].style.removeAttribute("filter")}var n,o,a=W.length;U&&(o=function(){clearTimeout(Q),L.hide(),u(nt),_.get("onComplete")},F.html(_.get("title")).show(),I.show(),a>1?("string"==typeof _.get("current")&&R.html(_.get("current").replace("{current}",A+1).replace("{total}",a)).show(),B[_.get("loop")||a-1>A?"show":"hide"]().html(_.get("next")),K[_.get("loop")||A?"show":"hide"]().html(_.get("previous")),ct(),_.get("preloading")&&t.each([h(-1),h(1)],function(){var i,n=W[this],o=new r(n,t.data(n,Y)),h=o.get("href");h&&s(o,h)&&(h=l(o,h),i=e.createElement("img"),i.src=h)})):O.hide(),_.get("iframe")?(n=_.get("createIframe"),_.get("scrolling")||(n.scrolling="no"),t(n).attr({src:_.get("href"),"class":Z+"Iframe"}).one("load",o).appendTo(I),at.one(ht,function(){n.src="//about:blank"}),_.get("fastIframe")&&t(n).trigger("load")):o(),"fade"===_.get("transition")?x.fadeTo(g,1,i):i())},"fade"===_.get("transition")?x.fadeTo(g,0,function(){J.position(0,d)}):J.position(g,d)}},J.next=function(){!$&&W[1]&&(_.get("loop")||W[A+1])&&(A=h(1),f(W[A]))},J.prev=function(){!$&&W[1]&&(_.get("loop")||A)&&(A=h(-1),f(W[A]))},J.close=function(){U&&!G&&(G=!0,U=!1,u(ot),_.get("onCleanup"),E.unbind("."+Z),v.fadeTo(_.get("fadeOut")||0,0),x.stop().fadeTo(_.get("fadeOut")||0,0,function(){x.hide(),v.hide(),u(ht),I.remove(),setTimeout(function(){G=!1,u(rt),_.get("onClosed")},1)}))},J.remove=function(){x&&(x.stop(),t[Y].close(),x.stop(!1,!0).remove(),v.remove(),G=!1,x=null,t("."+tt).removeData(Y).removeClass(tt),t(e).unbind("click."+Z).unbind("keydown."+Z))},J.element=function(){return t(_.el)},J.settings=X)}(jQuery,document,window);