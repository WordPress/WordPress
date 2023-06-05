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
!function(t){var o,e,u,n;function c(o,t){var e,u;1<o.originalEvent.touches.length||(o.preventDefault(),e=o.originalEvent.changedTouches[0],(u=document.createEvent("MouseEvents")).initMouseEvent(t,!0,!0,window,1,e.screenX,e.screenY,e.clientX,e.clientY,!1,!1,!1,!1,0,null),o.target.dispatchEvent(u))}t.support.touch="ontouchend"in document,t.support.touch&&(o=t.ui.mouse.prototype,e=o._mouseInit,u=o._mouseDestroy,o._touchStart=function(o){!n&&this._mouseCapture(o.originalEvent.changedTouches[0])&&(n=!0,this._touchMoved=!1,c(o,"mouseover"),c(o,"mousemove"),c(o,"mousedown"))},o._touchMove=function(o){n&&(this._touchMoved=!0,c(o,"mousemove"))},o._touchEnd=function(o){n&&(c(o,"mouseup"),c(o,"mouseout"),this._touchMoved||c(o,"click"),n=!1)},o._mouseInit=function(){var o=this;o.element.on({touchstart:t.proxy(o,"_touchStart"),touchmove:t.proxy(o,"_touchMove"),touchend:t.proxy(o,"_touchEnd")}),e.call(o)},o._mouseDestroy=function(){var o=this;o.element.off({touchstart:t.proxy(o,"_touchStart"),touchmove:t.proxy(o,"_touchMove"),touchend:t.proxy(o,"_touchEnd")}),u.call(o)})}(jQuery);