/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
(function(e){function s(e,t){if(e.originalEvent.touches.length>1){return}e.preventDefault();var n=e.originalEvent.changedTouches[0],r=document.createEvent("MouseEvents");r.initMouseEvent(t,true,true,window,1,n.screenX,n.screenY,n.clientX,n.clientY,false,false,false,false,0,null);e.target.dispatchEvent(r)}e.support.touch="ontouchend"in document;if(!e.support.touch){return}var t=e.ui.mouse.prototype,n=t._mouseInit,r=t._mouseDestroy,i;t._touchStart=function(e){var t=this;if(i||!t._mouseCapture(e.originalEvent.changedTouches[0])){return}i=true;t._touchMoved=false;s(e,"mouseover");s(e,"mousemove");s(e,"mousedown")};t._touchMove=function(e){if(!i){return}this._touchMoved=true;s(e,"mousemove")};t._touchEnd=function(e){if(!i){return}s(e,"mouseup");s(e,"mouseout");if(!this._touchMoved){s(e,"click")}i=false};t._mouseInit=function(){var t=this;t.element.bind({touchstart:e.proxy(t,"_touchStart"),touchmove:e.proxy(t,"_touchMove"),touchend:e.proxy(t,"_touchEnd")});n.call(t)};t._mouseDestroy=function(){var t=this;t.element.unbind({touchstart:e.proxy(t,"_touchStart"),touchmove:e.proxy(t,"_touchMove"),touchend:e.proxy(t,"_touchEnd")});r.call(t)}})(jQuery)
