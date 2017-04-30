/*
 * TipTip
 * Copyright 2010 Drew Wilson
 * www.drewwilson.com
 * code.drewwilson.com/entry/tiptip-jquery-plugin
 *
 * Version 1.3   -   Updated: Mar. 23, 2010
 *
 * This Plug-In will create a custom tooltip to replace the default
 * browser tooltip. It is extremely lightweight and very smart in
 * that it detects the edges of the browser window and will make sure
 * the tooltip stays within the current window size. As a result the
 * tooltip will adjust itself to be displayed above, below, to the left
 * or to the right depending on what is necessary to stay within the
 * browser window. It is completely customizable as well via CSS.
 *
 * This TipTip jQuery plug-in is dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */(function(e){e.fn.tipTip=function(t){var n={activation:"hover",keepAlive:!1,maxWidth:"200px",edgeOffset:3,defaultPosition:"bottom",delay:400,fadeIn:200,fadeOut:200,attribute:"title",content:!1,enter:function(){},exit:function(){}},r=e.extend(n,t);if(e("#tiptip_holder").length<=0){var i=e('<div id="tiptip_holder" style="max-width:'+r.maxWidth+';"></div>'),s=e('<div id="tiptip_content"></div>'),o=e('<div id="tiptip_arrow"></div>');e("body").append(i.html(s).prepend(o.html('<div id="tiptip_arrow_inner"></div>')))}else var i=e("#tiptip_holder"),s=e("#tiptip_content"),o=e("#tiptip_arrow");return this.each(function(){var t=e(this);if(r.content)var n=r.content;else var n=t.attr(r.attribute);if(n!=""){r.content||t.removeAttr(r.attribute);var u=!1;if(r.activation=="hover"){t.hover(function(){a()},function(){r.keepAlive||f()});r.keepAlive&&i.hover(function(){},function(){f()})}else if(r.activation=="focus")t.focus(function(){a()}).blur(function(){f()});else if(r.activation=="click"){t.click(function(){a();return!1}).hover(function(){},function(){r.keepAlive||f()});r.keepAlive&&i.hover(function(){},function(){f()})}function a(){r.enter.call(this);s.html(n);i.hide().removeAttr("class").css("margin","0");o.removeAttr("style");var a=parseInt(t.offset().top),f=parseInt(t.offset().left),l=parseInt(t.outerWidth()),c=parseInt(t.outerHeight()),h=i.outerWidth(),p=i.outerHeight(),d=Math.round((l-h)/2),v=Math.round((c-p)/2),m=Math.round(f+d),g=Math.round(a+c+r.edgeOffset),y="",b="",w=Math.round(h-12)/2;r.defaultPosition=="bottom"?y="_bottom":r.defaultPosition=="top"?y="_top":r.defaultPosition=="left"?y="_left":r.defaultPosition=="right"&&(y="_right");var E=d+f<parseInt(e(window).scrollLeft()),S=h+f>parseInt(e(window).width());if(E&&d<0||y=="_right"&&!S||y=="_left"&&f<h+r.edgeOffset+5){y="_right";b=Math.round(p-13)/2;w=-12;m=Math.round(f+l+r.edgeOffset);g=Math.round(a+v)}else if(S&&d<0||y=="_left"&&!E){y="_left";b=Math.round(p-13)/2;w=Math.round(h);m=Math.round(f-(h+r.edgeOffset+5));g=Math.round(a+v)}var x=a+c+r.edgeOffset+p+8>parseInt(e(window).height()+e(window).scrollTop()),T=a+c-(r.edgeOffset+p+8)<0;if(x||y=="_bottom"&&x||y=="_top"&&!T){y=="_top"||y=="_bottom"?y="_top":y+="_top";b=p;g=Math.round(a-(p+5+r.edgeOffset))}else if(T|(y=="_top"&&T)||y=="_bottom"&&!x){y=="_top"||y=="_bottom"?y="_bottom":y+="_bottom";b=-12;g=Math.round(a+c+r.edgeOffset)}if(y=="_right_top"||y=="_left_top")g+=5;else if(y=="_right_bottom"||y=="_left_bottom")g-=5;if(y=="_left_top"||y=="_left_bottom")m+=5;o.css({"margin-left":w+"px","margin-top":b+"px"});i.css({"margin-left":m+"px","margin-top":g+"px"}).attr("class","tip"+y);u&&clearTimeout(u);u=setTimeout(function(){i.stop(!0,!0).fadeIn(r.fadeIn)},r.delay)}function f(){r.exit.call(this);u&&clearTimeout(u);i.fadeOut(r.fadeOut)}}})}})(jQuery);