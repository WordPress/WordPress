/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 6411:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	autosize 4.0.4
	license: MIT
	http://www.jacklmoore.com/autosize
*/
(function (global, factory) {
	if (true) {
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [module, exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else { var mod; }
})(this, function (module, exports) {
	'use strict';

	var map = typeof Map === "function" ? new Map() : function () {
		var keys = [];
		var values = [];

		return {
			has: function has(key) {
				return keys.indexOf(key) > -1;
			},
			get: function get(key) {
				return values[keys.indexOf(key)];
			},
			set: function set(key, value) {
				if (keys.indexOf(key) === -1) {
					keys.push(key);
					values.push(value);
				}
			},
			delete: function _delete(key) {
				var index = keys.indexOf(key);
				if (index > -1) {
					keys.splice(index, 1);
					values.splice(index, 1);
				}
			}
		};
	}();

	var createEvent = function createEvent(name) {
		return new Event(name, { bubbles: true });
	};
	try {
		new Event('test');
	} catch (e) {
		// IE does not support `new Event()`
		createEvent = function createEvent(name) {
			var evt = document.createEvent('Event');
			evt.initEvent(name, true, false);
			return evt;
		};
	}

	function assign(ta) {
		if (!ta || !ta.nodeName || ta.nodeName !== 'TEXTAREA' || map.has(ta)) return;

		var heightOffset = null;
		var clientWidth = null;
		var cachedHeight = null;

		function init() {
			var style = window.getComputedStyle(ta, null);

			if (style.resize === 'vertical') {
				ta.style.resize = 'none';
			} else if (style.resize === 'both') {
				ta.style.resize = 'horizontal';
			}

			if (style.boxSizing === 'content-box') {
				heightOffset = -(parseFloat(style.paddingTop) + parseFloat(style.paddingBottom));
			} else {
				heightOffset = parseFloat(style.borderTopWidth) + parseFloat(style.borderBottomWidth);
			}
			// Fix when a textarea is not on document body and heightOffset is Not a Number
			if (isNaN(heightOffset)) {
				heightOffset = 0;
			}

			update();
		}

		function changeOverflow(value) {
			{
				// Chrome/Safari-specific fix:
				// When the textarea y-overflow is hidden, Chrome/Safari do not reflow the text to account for the space
				// made available by removing the scrollbar. The following forces the necessary text reflow.
				var width = ta.style.width;
				ta.style.width = '0px';
				// Force reflow:
				/* jshint ignore:start */
				ta.offsetWidth;
				/* jshint ignore:end */
				ta.style.width = width;
			}

			ta.style.overflowY = value;
		}

		function getParentOverflows(el) {
			var arr = [];

			while (el && el.parentNode && el.parentNode instanceof Element) {
				if (el.parentNode.scrollTop) {
					arr.push({
						node: el.parentNode,
						scrollTop: el.parentNode.scrollTop
					});
				}
				el = el.parentNode;
			}

			return arr;
		}

		function resize() {
			if (ta.scrollHeight === 0) {
				// If the scrollHeight is 0, then the element probably has display:none or is detached from the DOM.
				return;
			}

			var overflows = getParentOverflows(ta);
			var docTop = document.documentElement && document.documentElement.scrollTop; // Needed for Mobile IE (ticket #240)

			ta.style.height = '';
			ta.style.height = ta.scrollHeight + heightOffset + 'px';

			// used to check if an update is actually necessary on window.resize
			clientWidth = ta.clientWidth;

			// prevents scroll-position jumping
			overflows.forEach(function (el) {
				el.node.scrollTop = el.scrollTop;
			});

			if (docTop) {
				document.documentElement.scrollTop = docTop;
			}
		}

		function update() {
			resize();

			var styleHeight = Math.round(parseFloat(ta.style.height));
			var computed = window.getComputedStyle(ta, null);

			// Using offsetHeight as a replacement for computed.height in IE, because IE does not account use of border-box
			var actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(computed.height)) : ta.offsetHeight;

			// The actual height not matching the style height (set via the resize method) indicates that 
			// the max-height has been exceeded, in which case the overflow should be allowed.
			if (actualHeight < styleHeight) {
				if (computed.overflowY === 'hidden') {
					changeOverflow('scroll');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			} else {
				// Normally keep overflow set to hidden, to avoid flash of scrollbar as the textarea expands.
				if (computed.overflowY !== 'hidden') {
					changeOverflow('hidden');
					resize();
					actualHeight = computed.boxSizing === 'content-box' ? Math.round(parseFloat(window.getComputedStyle(ta, null).height)) : ta.offsetHeight;
				}
			}

			if (cachedHeight !== actualHeight) {
				cachedHeight = actualHeight;
				var evt = createEvent('autosize:resized');
				try {
					ta.dispatchEvent(evt);
				} catch (err) {
					// Firefox will throw an error on dispatchEvent for a detached element
					// https://bugzilla.mozilla.org/show_bug.cgi?id=889376
				}
			}
		}

		var pageResize = function pageResize() {
			if (ta.clientWidth !== clientWidth) {
				update();
			}
		};

		var destroy = function (style) {
			window.removeEventListener('resize', pageResize, false);
			ta.removeEventListener('input', update, false);
			ta.removeEventListener('keyup', update, false);
			ta.removeEventListener('autosize:destroy', destroy, false);
			ta.removeEventListener('autosize:update', update, false);

			Object.keys(style).forEach(function (key) {
				ta.style[key] = style[key];
			});

			map.delete(ta);
		}.bind(ta, {
			height: ta.style.height,
			resize: ta.style.resize,
			overflowY: ta.style.overflowY,
			overflowX: ta.style.overflowX,
			wordWrap: ta.style.wordWrap
		});

		ta.addEventListener('autosize:destroy', destroy, false);

		// IE9 does not fire onpropertychange or oninput for deletions,
		// so binding to onkeyup to catch most of those events.
		// There is no way that I know of to detect something like 'cut' in IE9.
		if ('onpropertychange' in ta && 'oninput' in ta) {
			ta.addEventListener('keyup', update, false);
		}

		window.addEventListener('resize', pageResize, false);
		ta.addEventListener('input', update, false);
		ta.addEventListener('autosize:update', update, false);
		ta.style.overflowX = 'hidden';
		ta.style.wordWrap = 'break-word';

		map.set(ta, {
			destroy: destroy,
			update: update
		});

		init();
	}

	function destroy(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.destroy();
		}
	}

	function update(ta) {
		var methods = map.get(ta);
		if (methods) {
			methods.update();
		}
	}

	var autosize = null;

	// Do nothing in Node.js environment and IE8 (or lower)
	if (typeof window === 'undefined' || typeof window.getComputedStyle !== 'function') {
		autosize = function autosize(el) {
			return el;
		};
		autosize.destroy = function (el) {
			return el;
		};
		autosize.update = function (el) {
			return el;
		};
	} else {
		autosize = function autosize(el, options) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], function (x) {
					return assign(x, options);
				});
			}
			return el;
		};
		autosize.destroy = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], destroy);
			}
			return el;
		};
		autosize.update = function (el) {
			if (el) {
				Array.prototype.forEach.call(el.length ? el : [el], update);
			}
			return el;
		};
	}

	exports.default = autosize;
	module.exports = exports['default'];
});

/***/ }),

/***/ 4403:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
	Copyright (c) 2018 Jed Watson.
	Licensed under the MIT License (MIT), see
	http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;
	var nativeCodeString = '[native code]';

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString !== Object.prototype.toString && !arg.toString.toString().includes('[native code]')) {
					classes.push(arg.toString());
					continue;
				}

				for (var key in arg) {
					if (hasOwn.call(arg, key) && arg[key]) {
						classes.push(key);
					}
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ 4827:
/***/ (function(module) {

// This code has been refactored for 140 bytes
// You can see the original here: https://github.com/twolfson/computedStyle/blob/04cd1da2e30fa45844f95f5cb1ac898e9b9ef050/lib/computedStyle.js
var computedStyle = function (el, prop, getComputedStyle) {
  getComputedStyle = window.getComputedStyle;

  // In one fell swoop
  return (
    // If we have getComputedStyle
    getComputedStyle ?
      // Query it
      // TODO: From CSS-Query notes, we might need (node, null) for FF
      getComputedStyle(el) :

    // Otherwise, we are in IE and use currentStyle
      el.currentStyle
  )[
    // Switch to camelCase for CSSOM
    // DEV: Grabbed from jQuery
    // https://github.com/jquery/jquery/blob/1.9-stable/src/css.js#L191-L194
    // https://github.com/jquery/jquery/blob/1.9-stable/src/core.js#L593-L597
    prop.replace(/-(\w)/gi, function (word, letter) {
      return letter.toUpperCase();
    })
  ];
};

module.exports = computedStyle;


/***/ }),

/***/ 8981:
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;//download.js v4.2, by dandavis; 2008-2016. [MIT] see http://danml.com/download.html for tests/usage
// v1 landed a FF+Chrome compat way of downloading strings to local un-named files, upgraded to use a hidden frame and optional mime
// v2 added named files via a[download], msSaveBlob, IE (10+) support, and window.URL support for larger+faster saves than dataURLs
// v3 added dataURL and Blob Input, bind-toggle arity, and legacy dataURL fallback was improved with force-download mime and base64 support. 3.1 improved safari handling.
// v4 adds AMD/UMD, commonJS, and plain browser support
// v4.1 adds url download capability via solo URL argument (same domain/CORS only)
// v4.2 adds semantic variable names, long (over 2MB) dataURL support, and hidden by default temp anchors
// https://github.com/rndme/download

(function (root, factory) {
	if (true) {
		// AMD. Register as an anonymous module.
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}(this, function () {

	return function download(data, strFileName, strMimeType) {

		var self = window, // this script is only for browsers anyway...
			defaultMime = "application/octet-stream", // this default mime also triggers iframe downloads
			mimeType = strMimeType || defaultMime,
			payload = data,
			url = !strFileName && !strMimeType && payload,
			anchor = document.createElement("a"),
			toString = function(a){return String(a);},
			myBlob = (self.Blob || self.MozBlob || self.WebKitBlob || toString),
			fileName = strFileName || "download",
			blob,
			reader;
			myBlob= myBlob.call ? myBlob.bind(self) : Blob ;
	  
		if(String(this)==="true"){ //reverse arguments, allowing download.bind(true, "text/xml", "export.xml") to act as a callback
			payload=[payload, mimeType];
			mimeType=payload[0];
			payload=payload[1];
		}


		if(url && url.length< 2048){ // if no filename and no mime, assume a url was passed as the only argument
			fileName = url.split("/").pop().split("?")[0];
			anchor.href = url; // assign href prop to temp anchor
		  	if(anchor.href.indexOf(url) !== -1){ // if the browser determines that it's a potentially valid url path:
        		var ajax=new XMLHttpRequest();
        		ajax.open( "GET", url, true);
        		ajax.responseType = 'blob';
        		ajax.onload= function(e){ 
				  download(e.target.response, fileName, defaultMime);
				};
        		setTimeout(function(){ ajax.send();}, 0); // allows setting custom ajax headers using the return:
			    return ajax;
			} // end if valid url?
		} // end if url?


		//go ahead and download dataURLs right away
		if(/^data:([\w+-]+\/[\w+.-]+)?[,;]/.test(payload)){
		
			if(payload.length > (1024*1024*1.999) && myBlob !== toString ){
				payload=dataUrlToBlob(payload);
				mimeType=payload.type || defaultMime;
			}else{			
				return navigator.msSaveBlob ?  // IE10 can't do a[download], only Blobs:
					navigator.msSaveBlob(dataUrlToBlob(payload), fileName) :
					saver(payload) ; // everyone else can save dataURLs un-processed
			}
			
		}else{//not data url, is it a string with special needs?
			if(/([\x80-\xff])/.test(payload)){			  
				var i=0, tempUiArr= new Uint8Array(payload.length), mx=tempUiArr.length;
				for(i;i<mx;++i) tempUiArr[i]= payload.charCodeAt(i);
			 	payload=new myBlob([tempUiArr], {type: mimeType});
			}		  
		}
		blob = payload instanceof myBlob ?
			payload :
			new myBlob([payload], {type: mimeType}) ;


		function dataUrlToBlob(strUrl) {
			var parts= strUrl.split(/[:;,]/),
			type= parts[1],
			decoder= parts[2] == "base64" ? atob : decodeURIComponent,
			binData= decoder( parts.pop() ),
			mx= binData.length,
			i= 0,
			uiArr= new Uint8Array(mx);

			for(i;i<mx;++i) uiArr[i]= binData.charCodeAt(i);

			return new myBlob([uiArr], {type: type});
		 }

		function saver(url, winMode){

			if ('download' in anchor) { //html5 A[download]
				anchor.href = url;
				anchor.setAttribute("download", fileName);
				anchor.className = "download-js-link";
				anchor.innerHTML = "downloading...";
				anchor.style.display = "none";
				document.body.appendChild(anchor);
				setTimeout(function() {
					anchor.click();
					document.body.removeChild(anchor);
					if(winMode===true){setTimeout(function(){ self.URL.revokeObjectURL(anchor.href);}, 250 );}
				}, 66);
				return true;
			}

			// handle non-a[download] safari as best we can:
			if(/(Version)\/(\d+)\.(\d+)(?:\.(\d+))?.*Safari\//.test(navigator.userAgent)) {
				if(/^data:/.test(url))	url="data:"+url.replace(/^data:([\w\/\-\+]+)/, defaultMime);
				if(!window.open(url)){ // popup blocked, offer direct download:
					if(confirm("Displaying New Document\n\nUse Save As... to download, then click back to return to this page.")){ location.href=url; }
				}
				return true;
			}

			//do iframe dataURL download (old ch+FF):
			var f = document.createElement("iframe");
			document.body.appendChild(f);

			if(!winMode && /^data:/.test(url)){ // force a mime that will download:
				url="data:"+url.replace(/^data:([\w\/\-\+]+)/, defaultMime);
			}
			f.src=url;
			setTimeout(function(){ document.body.removeChild(f); }, 333);

		}//end saver




		if (navigator.msSaveBlob) { // IE10+ : (has Blob, but not a[download] or URL)
			return navigator.msSaveBlob(blob, fileName);
		}

		if(self.URL){ // simple fast and modern way using Blob and URL:
			saver(self.URL.createObjectURL(blob), true);
		}else{
			// handle non-Blob()+non-URL browsers:
			if(typeof blob === "string" || blob.constructor===toString ){
				try{
					return saver( "data:" +  mimeType   + ";base64,"  +  self.btoa(blob)  );
				}catch(y){
					return saver( "data:" +  mimeType   + "," + encodeURIComponent(blob)  );
				}
			}

			// Blob but not URL support:
			reader=new FileReader();
			reader.onload=function(e){
				saver(this.result);
			};
			reader.readAsDataURL(blob);
		}
		return true;
	}; /* end download() */
}));


/***/ }),

/***/ 5619:
/***/ (function(module) {

"use strict";


// do not edit .js files directly - edit src/index.jst


  var envHasBigInt64Array = typeof BigInt64Array !== 'undefined';


module.exports = function equal(a, b) {
  if (a === b) return true;

  if (a && b && typeof a == 'object' && typeof b == 'object') {
    if (a.constructor !== b.constructor) return false;

    var length, i, keys;
    if (Array.isArray(a)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (!equal(a[i], b[i])) return false;
      return true;
    }


    if ((a instanceof Map) && (b instanceof Map)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      for (i of a.entries())
        if (!equal(i[1], b.get(i[0]))) return false;
      return true;
    }

    if ((a instanceof Set) && (b instanceof Set)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      return true;
    }

    if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (a[i] !== b[i]) return false;
      return true;
    }


    if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
    if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
    if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) return false;

    for (i = length; i-- !== 0;)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

    for (i = length; i-- !== 0;) {
      var key = keys[i];

      if (!equal(a[key], b[key])) return false;
    }

    return true;
  }

  // true if both NaN, false otherwise
  return a!==a && b!==b;
};


/***/ }),

/***/ 9894:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// Load in dependencies
var computedStyle = __webpack_require__(4827);

/**
 * Calculate the `line-height` of a given node
 * @param {HTMLElement} node Element to calculate line height of. Must be in the DOM.
 * @returns {Number} `line-height` of the element in pixels
 */
function lineHeight(node) {
  // Grab the line-height via style
  var lnHeightStr = computedStyle(node, 'line-height');
  var lnHeight = parseFloat(lnHeightStr, 10);

  // If the lineHeight did not contain a unit (i.e. it was numeric), convert it to ems (e.g. '2.3' === '2.3em')
  if (lnHeightStr === lnHeight + '') {
    // Save the old lineHeight style and update the em unit to the element
    var _lnHeightStyle = node.style.lineHeight;
    node.style.lineHeight = lnHeightStr + 'em';

    // Calculate the em based height
    lnHeightStr = computedStyle(node, 'line-height');
    lnHeight = parseFloat(lnHeightStr, 10);

    // Revert the lineHeight style
    if (_lnHeightStyle) {
      node.style.lineHeight = _lnHeightStyle;
    } else {
      delete node.style.lineHeight;
    }
  }

  // If the lineHeight is in `pt`, convert it to pixels (4px for 3pt)
  // DEV: `em` units are converted to `pt` in IE6
  // Conversion ratio from https://developer.mozilla.org/en-US/docs/Web/CSS/length
  if (lnHeightStr.indexOf('pt') !== -1) {
    lnHeight *= 4;
    lnHeight /= 3;
  // Otherwise, if the lineHeight is in `mm`, convert it to pixels (96px for 25.4mm)
  } else if (lnHeightStr.indexOf('mm') !== -1) {
    lnHeight *= 96;
    lnHeight /= 25.4;
  // Otherwise, if the lineHeight is in `cm`, convert it to pixels (96px for 2.54cm)
  } else if (lnHeightStr.indexOf('cm') !== -1) {
    lnHeight *= 96;
    lnHeight /= 2.54;
  // Otherwise, if the lineHeight is in `in`, convert it to pixels (96px for 1in)
  } else if (lnHeightStr.indexOf('in') !== -1) {
    lnHeight *= 96;
  // Otherwise, if the lineHeight is in `pc`, convert it to pixels (12pt for 1pc)
  } else if (lnHeightStr.indexOf('pc') !== -1) {
    lnHeight *= 16;
  }

  // Continue our computation
  lnHeight = Math.round(lnHeight);

  // If the line-height is "normal", calculate by font-size
  if (lnHeightStr === 'normal') {
    // Create a temporary node
    var nodeName = node.nodeName;
    var _node = document.createElement(nodeName);
    _node.innerHTML = '&nbsp;';

    // If we have a text area, reset it to only 1 row
    // https://github.com/twolfson/line-height/issues/4
    if (nodeName.toUpperCase() === 'TEXTAREA') {
      _node.setAttribute('rows', '1');
    }

    // Set the font-size of the element
    var fontSizeStr = computedStyle(node, 'font-size');
    _node.style.fontSize = fontSizeStr;

    // Remove default padding/border which can affect offset height
    // https://github.com/twolfson/line-height/issues/4
    // https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/offsetHeight
    _node.style.padding = '0px';
    _node.style.border = '0px';

    // Append it to the body
    var body = document.body;
    body.appendChild(_node);

    // Assume the line height of the element is the height
    var height = _node.offsetHeight;
    lnHeight = height;

    // Remove our child from the DOM
    body.removeChild(_node);
  }

  // Return the calculated height
  return lnHeight;
}

// Export lineHeight
module.exports = lineHeight;


/***/ }),

/***/ 9756:
/***/ (function(module) {

/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {Function} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {F & MemizeMemoizedFunction} Memoized function.
 */
function memize( fn, options ) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ ( node.prev ).next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ ( head ).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args ),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === /** @type {MemizeOptions} */ ( options ).maxSize ) {
			tail = /** @type {MemizeCacheNode} */ ( tail ).prev;
			/** @type {MemizeCacheNode} */ ( tail ).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function() {
		head = null;
		tail = null;
		size = 0;
	};

	if ( false ) {}

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}

module.exports = memize;


/***/ }),

/***/ 5372:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = __webpack_require__(9567);

function emptyFunction() {}
function emptyFunctionWithReset() {}
emptyFunctionWithReset.resetWarningCache = emptyFunction;

module.exports = function() {
  function shim(props, propName, componentName, location, propFullName, secret) {
    if (secret === ReactPropTypesSecret) {
      // It is still safe when called from React.
      return;
    }
    var err = new Error(
      'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
      'Use PropTypes.checkPropTypes() to call them. ' +
      'Read more at http://fb.me/use-check-prop-types'
    );
    err.name = 'Invariant Violation';
    throw err;
  };
  shim.isRequired = shim;
  function getShim() {
    return shim;
  };
  // Important!
  // Keep this list in sync with production version in `./factoryWithTypeCheckers.js`.
  var ReactPropTypes = {
    array: shim,
    bigint: shim,
    bool: shim,
    func: shim,
    number: shim,
    object: shim,
    string: shim,
    symbol: shim,

    any: shim,
    arrayOf: getShim,
    element: shim,
    elementType: shim,
    instanceOf: getShim,
    node: shim,
    objectOf: getShim,
    oneOf: getShim,
    oneOfType: getShim,
    shape: getShim,
    exact: getShim,

    checkPropTypes: emptyFunctionWithReset,
    resetWarningCache: emptyFunction
  };

  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ 2652:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (false) { var throwOnDirectAccess, ReactIs; } else {
  // By explicitly using `prop-types` you are opting into new production behavior.
  // http://fb.me/prop-types-in-prod
  module.exports = __webpack_require__(5372)();
}


/***/ }),

/***/ 9567:
/***/ (function(module) {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ 5438:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || Object.assign || function(t) {
    for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
            t[p] = s[p];
    }
    return t;
};
var __rest = (this && this.__rest) || function (s, e) {
    var t = {};
    for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
        t[p] = s[p];
    if (s != null && typeof Object.getOwnPropertySymbols === "function")
        for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) if (e.indexOf(p[i]) < 0)
            t[p[i]] = s[p[i]];
    return t;
};
exports.__esModule = true;
var React = __webpack_require__(9196);
var PropTypes = __webpack_require__(2652);
var autosize = __webpack_require__(6411);
var _getLineHeight = __webpack_require__(9894);
var getLineHeight = _getLineHeight;
var RESIZED = "autosize:resized";
/**
 * A light replacement for built-in textarea component
 * which automaticaly adjusts its height to match the content
 */
var TextareaAutosizeClass = /** @class */ (function (_super) {
    __extends(TextareaAutosizeClass, _super);
    function TextareaAutosizeClass() {
        var _this = _super !== null && _super.apply(this, arguments) || this;
        _this.state = {
            lineHeight: null
        };
        _this.textarea = null;
        _this.onResize = function (e) {
            if (_this.props.onResize) {
                _this.props.onResize(e);
            }
        };
        _this.updateLineHeight = function () {
            if (_this.textarea) {
                _this.setState({
                    lineHeight: getLineHeight(_this.textarea)
                });
            }
        };
        _this.onChange = function (e) {
            var onChange = _this.props.onChange;
            _this.currentValue = e.currentTarget.value;
            onChange && onChange(e);
        };
        return _this;
    }
    TextareaAutosizeClass.prototype.componentDidMount = function () {
        var _this = this;
        var _a = this.props, maxRows = _a.maxRows, async = _a.async;
        if (typeof maxRows === "number") {
            this.updateLineHeight();
        }
        if (typeof maxRows === "number" || async) {
            /*
              the defer is needed to:
                - force "autosize" to activate the scrollbar when this.props.maxRows is passed
                - support StyledComponents (see #71)
            */
            setTimeout(function () { return _this.textarea && autosize(_this.textarea); });
        }
        else {
            this.textarea && autosize(this.textarea);
        }
        if (this.textarea) {
            this.textarea.addEventListener(RESIZED, this.onResize);
        }
    };
    TextareaAutosizeClass.prototype.componentWillUnmount = function () {
        if (this.textarea) {
            this.textarea.removeEventListener(RESIZED, this.onResize);
            autosize.destroy(this.textarea);
        }
    };
    TextareaAutosizeClass.prototype.render = function () {
        var _this = this;
        var _a = this, _b = _a.props, onResize = _b.onResize, maxRows = _b.maxRows, onChange = _b.onChange, style = _b.style, innerRef = _b.innerRef, children = _b.children, props = __rest(_b, ["onResize", "maxRows", "onChange", "style", "innerRef", "children"]), lineHeight = _a.state.lineHeight;
        var maxHeight = maxRows && lineHeight ? lineHeight * maxRows : null;
        return (React.createElement("textarea", __assign({}, props, { onChange: this.onChange, style: maxHeight ? __assign({}, style, { maxHeight: maxHeight }) : style, ref: function (element) {
                _this.textarea = element;
                if (typeof _this.props.innerRef === 'function') {
                    _this.props.innerRef(element);
                }
                else if (_this.props.innerRef) {
                    _this.props.innerRef.current = element;
                }
            } }), children));
    };
    TextareaAutosizeClass.prototype.componentDidUpdate = function () {
        this.textarea && autosize.update(this.textarea);
    };
    TextareaAutosizeClass.defaultProps = {
        rows: 1,
        async: false
    };
    TextareaAutosizeClass.propTypes = {
        rows: PropTypes.number,
        maxRows: PropTypes.number,
        onResize: PropTypes.func,
        innerRef: PropTypes.any,
        async: PropTypes.bool
    };
    return TextareaAutosizeClass;
}(React.Component));
exports.TextareaAutosize = React.forwardRef(function (props, ref) {
    return React.createElement(TextareaAutosizeClass, __assign({}, props, { innerRef: ref }));
});


/***/ }),

/***/ 773:
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";
var __webpack_unused_export__;

__webpack_unused_export__ = true;
var TextareaAutosize_1 = __webpack_require__(5438);
exports.Z = TextareaAutosize_1.TextareaAutosize;


/***/ }),

/***/ 9196:
/***/ (function(module) {

"use strict";
module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "PluginMoreMenuItem": function() { return /* reexport */ plugin_more_menu_item; },
  "PluginSidebar": function() { return /* reexport */ PluginSidebarEditSite; },
  "PluginSidebarMoreMenuItem": function() { return /* reexport */ PluginSidebarMoreMenuItem; },
  "initializeEditor": function() { return /* binding */ initializeEditor; },
  "reinitializeEditor": function() { return /* binding */ reinitializeEditor; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "disableComplementaryArea": function() { return disableComplementaryArea; },
  "enableComplementaryArea": function() { return enableComplementaryArea; },
  "pinItem": function() { return pinItem; },
  "setDefaultComplementaryArea": function() { return setDefaultComplementaryArea; },
  "setFeatureDefaults": function() { return setFeatureDefaults; },
  "setFeatureValue": function() { return setFeatureValue; },
  "toggleFeature": function() { return toggleFeature; },
  "unpinItem": function() { return unpinItem; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getActiveComplementaryArea": function() { return getActiveComplementaryArea; },
  "isFeatureActive": function() { return isFeatureActive; },
  "isItemPinned": function() { return isItemPinned; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-site/build-module/store/actions.js
var store_actions_namespaceObject = {};
__webpack_require__.r(store_actions_namespaceObject);
__webpack_require__.d(store_actions_namespaceObject, {
  "__experimentalSetPreviewDeviceType": function() { return __experimentalSetPreviewDeviceType; },
  "addTemplate": function() { return addTemplate; },
  "closeGeneralSidebar": function() { return closeGeneralSidebar; },
  "openGeneralSidebar": function() { return openGeneralSidebar; },
  "openNavigationPanelToMenu": function() { return openNavigationPanelToMenu; },
  "removeTemplate": function() { return removeTemplate; },
  "revertTemplate": function() { return revertTemplate; },
  "setEditedPostContext": function() { return setEditedPostContext; },
  "setHomeTemplateId": function() { return setHomeTemplateId; },
  "setIsInserterOpened": function() { return setIsInserterOpened; },
  "setIsListViewOpened": function() { return setIsListViewOpened; },
  "setIsNavigationPanelOpened": function() { return setIsNavigationPanelOpened; },
  "setIsSaveViewOpened": function() { return setIsSaveViewOpened; },
  "setNavigationPanelActiveMenu": function() { return setNavigationPanelActiveMenu; },
  "setPage": function() { return setPage; },
  "setTemplate": function() { return setTemplate; },
  "setTemplatePart": function() { return setTemplatePart; },
  "switchEditorMode": function() { return switchEditorMode; },
  "toggleFeature": function() { return actions_toggleFeature; },
  "updateSettings": function() { return updateSettings; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-site/build-module/store/private-actions.js
var private_actions_namespaceObject = {};
__webpack_require__.r(private_actions_namespaceObject);
__webpack_require__.d(private_actions_namespaceObject, {
  "setCanvasMode": function() { return setCanvasMode; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-site/build-module/store/selectors.js
var store_selectors_namespaceObject = {};
__webpack_require__.r(store_selectors_namespaceObject);
__webpack_require__.d(store_selectors_namespaceObject, {
  "__experimentalGetInsertionPoint": function() { return __experimentalGetInsertionPoint; },
  "__experimentalGetPreviewDeviceType": function() { return __experimentalGetPreviewDeviceType; },
  "__unstableGetPreference": function() { return __unstableGetPreference; },
  "getCanUserCreateMedia": function() { return getCanUserCreateMedia; },
  "getCurrentTemplateNavigationPanelSubMenu": function() { return getCurrentTemplateNavigationPanelSubMenu; },
  "getCurrentTemplateTemplateParts": function() { return getCurrentTemplateTemplateParts; },
  "getEditedPostContext": function() { return getEditedPostContext; },
  "getEditedPostId": function() { return getEditedPostId; },
  "getEditedPostType": function() { return getEditedPostType; },
  "getEditorMode": function() { return getEditorMode; },
  "getHomeTemplateId": function() { return getHomeTemplateId; },
  "getNavigationPanelActiveMenu": function() { return getNavigationPanelActiveMenu; },
  "getPage": function() { return getPage; },
  "getReusableBlocks": function() { return getReusableBlocks; },
  "getSettings": function() { return getSettings; },
  "isFeatureActive": function() { return selectors_isFeatureActive; },
  "isInserterOpened": function() { return isInserterOpened; },
  "isListViewOpened": function() { return isListViewOpened; },
  "isNavigationOpened": function() { return isNavigationOpened; },
  "isSaveViewOpened": function() { return isSaveViewOpened; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-site/build-module/store/private-selectors.js
var private_selectors_namespaceObject = {};
__webpack_require__.r(private_selectors_namespaceObject);
__webpack_require__.d(private_selectors_namespaceObject, {
  "getCanvasMode": function() { return getCanvasMode; }
});

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: external ["wp","blockLibrary"]
var external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
var external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","coreData"]
var external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// CONCATENATED MODULE: external ["wp","editor"]
var external_wp_editor_namespaceObject = window["wp"]["editor"];
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function extends_extends() {
  extends_extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return extends_extends.apply(this, arguments);
}
// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__(4403);
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","primitives"]
var external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js


/**
 * WordPress dependencies
 */

const check = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ var library_check = (check);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js


/**
 * WordPress dependencies
 */

const starFilled = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ var star_filled = (starFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js


/**
 * WordPress dependencies
 */

const starEmpty = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ var star_empty = (starEmpty);

;// CONCATENATED MODULE: external ["wp","viewport"]
var external_wp_viewport_namespaceObject = window["wp"]["viewport"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js


/**
 * WordPress dependencies
 */

const closeSmall = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ var close_small = (closeSmall);

;// CONCATENATED MODULE: external ["wp","preferences"]
var external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/actions.js
/**
 * WordPress dependencies
 */


/**
 * Set a default complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */

const setDefaultComplementaryArea = (scope, area) => ({
  type: 'SET_DEFAULT_COMPLEMENTARY_AREA',
  scope,
  area
});
/**
 * Enable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 */

const enableComplementaryArea = (scope, area) => _ref => {
  let {
    registry,
    dispatch
  } = _ref;

  // Return early if there's no area.
  if (!area) {
    return;
  }

  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (!isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', true);
  }

  dispatch({
    type: 'ENABLE_COMPLEMENTARY_AREA',
    scope,
    area
  });
};
/**
 * Disable the complementary area.
 *
 * @param {string} scope Complementary area scope.
 */

const disableComplementaryArea = scope => _ref2 => {
  let {
    registry
  } = _ref2;
  const isComplementaryAreaVisible = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible');

  if (isComplementaryAreaVisible) {
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'isComplementaryAreaVisible', false);
  }
};
/**
 * Pins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 *
 * @return {Object} Action object.
 */

const pinItem = (scope, item) => _ref3 => {
  let {
    registry
  } = _ref3;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems'); // The item is already pinned, there's nothing to do.

  if ((pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) === true) {
    return;
  }

  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: true
  });
};
/**
 * Unpins an item.
 *
 * @param {string} scope Item scope.
 * @param {string} item  Item identifier.
 */

const unpinItem = (scope, item) => _ref4 => {
  let {
    registry
  } = _ref4;

  // Return early if there's no item.
  if (!item) {
    return;
  }

  const pinnedItems = registry.select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, 'pinnedItems', { ...pinnedItems,
    [item]: false
  });
};
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function toggleFeature(scope, featureName) {
  return function (_ref5) {
    let {
      registry
    } = _ref5;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).toggleFeature`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).toggle`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).toggle(scope, featureName);
  };
}
/**
 * Returns an action object used in signalling that a feature should be set to
 * a true or false value
 *
 * @param {string}  scope       The feature scope (e.g. core/edit-post).
 * @param {string}  featureName The feature name.
 * @param {boolean} value       The value to set.
 *
 * @return {Object} Action object.
 */

function setFeatureValue(scope, featureName, value) {
  return function (_ref6) {
    let {
      registry
    } = _ref6;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureValue`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).set`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).set(scope, featureName, !!value);
  };
}
/**
 * Returns an action object used in signalling that defaults should be set for features.
 *
 * @param {string}                  scope    The feature scope (e.g. core/edit-post).
 * @param {Object<string, boolean>} defaults A key/value map of feature names to values.
 *
 * @return {Object} Action object.
 */

function setFeatureDefaults(scope, defaults) {
  return function (_ref7) {
    let {
      registry
    } = _ref7;
    external_wp_deprecated_default()(`dispatch( 'core/interface' ).setFeatureDefaults`, {
      since: '6.0',
      alternative: `dispatch( 'core/preferences' ).setDefaults`
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).setDefaults(scope, defaults);
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/selectors.js
/**
 * WordPress dependencies
 */



/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string | null | undefined} The complementary area that is active in the given scope.
 */

const getActiveComplementaryArea = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope) => {
  var _state$complementaryA;

  const isComplementaryAreaVisible = select(external_wp_preferences_namespaceObject.store).get(scope, 'isComplementaryAreaVisible'); // Return `undefined` to indicate that the user has never toggled
  // visibility, this is the vanilla default. Other code relies on this
  // nuance in the return value.

  if (isComplementaryAreaVisible === undefined) {
    return undefined;
  } // Return `null` to indicate the user hid the complementary area.


  if (!isComplementaryAreaVisible) {
    return null;
  }

  return state === null || state === void 0 ? void 0 : (_state$complementaryA = state.complementaryAreas) === null || _state$complementaryA === void 0 ? void 0 : _state$complementaryA[scope];
});
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */

const isItemPinned = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, item) => {
  var _pinnedItems$item;

  const pinnedItems = select(external_wp_preferences_namespaceObject.store).get(scope, 'pinnedItems');
  return (_pinnedItems$item = pinnedItems === null || pinnedItems === void 0 ? void 0 : pinnedItems[item]) !== null && _pinnedItems$item !== void 0 ? _pinnedItems$item : true;
});
/**
 * Returns a boolean indicating whether a feature is active for a particular
 * scope.
 *
 * @param {Object} state       The store state.
 * @param {string} scope       The scope of the feature (e.g. core/edit-post).
 * @param {string} featureName The name of the feature.
 *
 * @return {boolean} Is the feature enabled?
 */

const isFeatureActive = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, scope, featureName) => {
  external_wp_deprecated_default()(`select( 'core/interface' ).isFeatureActive( scope, featureName )`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get( scope, featureName )`
  });
  return !!select(external_wp_preferences_namespaceObject.store).get(scope, featureName);
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

function complementaryAreas() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_DEFAULT_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action; // If there's already an area, don't overwrite it.

        if (state[scope]) {
          return state;
        }

        return { ...state,
          [scope]: area
        };
      }

    case 'ENABLE_COMPLEMENTARY_AREA':
      {
        const {
          scope,
          area
        } = action;
        return { ...state,
          [scope]: area
        };
      }
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  complementaryAreas
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Store definition for the interface namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["wp","plugins"]
var external_wp_plugins_namespaceObject = window["wp"]["plugins"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-context/index.js
/**
 * WordPress dependencies
 */

/* harmony default export */ var complementary_area_context = ((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-toggle/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function ComplementaryAreaToggle(_ref) {
  let {
    as = external_wp_components_namespaceObject.Button,
    scope,
    identifier,
    icon,
    selectedIcon,
    name,
    ...props
  } = _ref;
  const ComponentToUse = as;
  const isSelected = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  return (0,external_wp_element_namespaceObject.createElement)(ComponentToUse, extends_extends({
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    }
  }, props));
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-header/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const ComplementaryAreaHeader = _ref => {
  let {
    smallScreenTitle,
    children,
    className,
    toggleButtonProps
  } = _ref;
  const toggleButton = (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, extends_extends({
    icon: close_small
  }, toggleButtonProps));
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/action-item/index.js



/**
 * WordPress dependencies
 */



const noop = () => {};

function ActionItemSlot(_ref) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.ButtonGroup,
    fillProps = {},
    bubblesVirtually,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (!external_wp_element_namespaceObject.Children.toArray(fills).length) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/WordPress/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_wp_element_namespaceObject.Children.forEach(fills, _ref2 => {
      let {
        props: {
          __unstableExplicitMenuItem,
          __unstableTarget
        }
      } = _ref2;

      if (__unstableTarget && __unstableExplicitMenuItem) {
        initializedByPlugins.push(__unstableTarget);
      }
    });
    const children = external_wp_element_namespaceObject.Children.map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return (0,external_wp_element_namespaceObject.createElement)(Component, props, children);
  });
}

function ActionItem(_ref3) {
  let {
    name,
    as: Component = external_wp_components_namespaceObject.Button,
    onClick,
    ...props
  } = _ref3;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: name
  }, _ref4 => {
    let {
      onClick: fpOnClick
    } = _ref4;
    return (0,external_wp_element_namespaceObject.createElement)(Component, extends_extends({
      onClick: onClick || fpOnClick ? function () {
        (onClick || noop)(...arguments);
        (fpOnClick || noop)(...arguments);
      } : undefined
    }, props));
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const PluginsMenuItem = _ref => {
  let {
    // Menu item is marked with unstable prop for backward compatibility.
    // They are removed so they don't leak to DOM elements.
    // @see https://github.com/WordPress/gutenberg/issues/14457
    __unstableExplicitMenuItem,
    __unstableTarget,
    ...restProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, restProps);
};

function ComplementaryAreaMoreMenuItem(_ref2) {
  let {
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, extends_extends({
    as: toggleProps => {
      return (0,external_wp_element_namespaceObject.createElement)(action_item, extends_extends({
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`
      }, toggleProps));
    },
    role: "menuitemcheckbox",
    selectedIcon: library_check,
    name: target,
    scope: scope
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function PinnedItems(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, extends_extends({
    name: `PinnedItems/${scope}`
  }, props));
}

function PinnedItemsSlot(_ref2) {
  let {
    scope,
    className,
    ...props
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, extends_extends({
    name: `PinnedItems/${scope}`
  }, props), fills => (fills === null || fills === void 0 ? void 0 : fills.length) > 0 && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */








function ComplementaryAreaSlot(_ref) {
  let {
    scope,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Slot, extends_extends({
    name: `ComplementaryArea/${scope}`
  }, props));
}

function ComplementaryAreaFill(_ref2) {
  let {
    scope,
    children,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Fill, {
    name: `ComplementaryArea/${scope}`
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const shouldOpenWhenNotSmall = (0,external_wp_element_namespaceObject.useRef)(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // If the complementary area is active and the editor is switching from a big to a small window size.
    if (isActive && isSmall && !previousIsSmall.current) {
      // Disable the complementary area.
      disableComplementaryArea(scope); // Flag the complementary area to be reopened when the window size goes from small to big.

      shouldOpenWhenNotSmall.current = true;
    } else if ( // If there is a flag indicating the complementary area should be enabled when we go from small to big window size
    // and we are going from a small to big window size.
    shouldOpenWhenNotSmall.current && !isSmall && previousIsSmall.current) {
      // Remove the flag indicating the complementary area should be enabled.
      shouldOpenWhenNotSmall.current = false; // Enable the complementary area.

      enableComplementaryArea(scope, identifier);
    } else if ( // If the flag is indicating the current complementary should be reopened but another complementary area becomes active,
    // remove the flag.
    shouldOpenWhenNotSmall.current && activeArea && activeArea !== identifier) {
      shouldOpenWhenNotSmall.current = false;
    }

    if (isSmall !== previousIsSmall.current) {
      previousIsSmall.current = isSmall;
    }
  }, [isActive, isSmall, scope, identifier, activeArea]);
}

function ComplementaryArea(_ref3) {
  let {
    children,
    className,
    closeLabel = (0,external_wp_i18n_namespaceObject.__)('Close plugin'),
    identifier,
    header,
    headerClassName,
    icon,
    isPinnable = true,
    panelClassName,
    scope,
    name,
    smallScreenTitle,
    title,
    toggleShortcut,
    isActiveByDefault,
    showIconLabels = false
  } = _ref3;
  const {
    isActive,
    isPinned,
    activeArea,
    isSmall,
    isLarge
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getActiveComplementaryArea,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_wp_viewport_namespaceObject.store).isViewportMatch('< medium'),
      isLarge: select(external_wp_viewport_namespaceObject.store).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isPinnable && (0,external_wp_element_namespaceObject.createElement)(pinned_items, {
    scope: scope
  }, isPinned && (0,external_wp_element_namespaceObject.createElement)(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    label: title,
    icon: showIconLabels ? library_check : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, (0,external_wp_element_namespaceObject.createElement)(complementary_area_header, {
    className: headerClassName,
    closeLabel: closeLabel,
    onClose: () => disableComplementaryArea(scope),
    smallScreenTitle: smallScreenTitle,
    toggleButtonProps: {
      label: closeLabel,
      shortcut: toggleShortcut,
      scope,
      identifier
    }
  }, header || (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("strong", null, title), isPinnable && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled : star_empty,
    label: isPinned ? (0,external_wp_i18n_namespaceObject.__)('Unpin from toolbar') : (0,external_wp_i18n_namespaceObject.__)('Pin to toolbar'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Panel, {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/navigable-region/index.js



/**
 * External dependencies
 */

function NavigableRegion(_ref) {
  let {
    children,
    className,
    ariaLabel,
    as: Tag = 'div',
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Tag, extends_extends({
    className: classnames_default()('interface-navigable-region', className),
    "aria-label": ariaLabel,
    role: "region",
    tabIndex: "-1"
  }, props), children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function useHTMLClass(className) {
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const element = document && document.querySelector(`html:not(.${className})`);

    if (!element) {
      return;
    }

    element.classList.toggle(className);
    return () => {
      element.classList.toggle(className);
    };
  }, [className]);
}

function InterfaceSkeleton(_ref, ref) {
  let {
    isDistractionFree,
    footer,
    header,
    editorNotices,
    sidebar,
    secondarySidebar,
    notices,
    content,
    actions,
    labels,
    className,
    enableRegionNavigation = true,
    // Todo: does this need to be a prop.
    // Can we use a dependency to keyboard-shortcuts directly?
    shortcuts
  } = _ref;
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the top bar landmark region. */
    header: (0,external_wp_i18n_namespaceObject.__)('Header'),

    /* translators: accessibility text for the content landmark region. */
    body: (0,external_wp_i18n_namespaceObject.__)('Content'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: (0,external_wp_i18n_namespaceObject.__)('Block Library'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: (0,external_wp_i18n_namespaceObject.__)('Settings'),

    /* translators: accessibility text for the publish landmark region. */
    actions: (0,external_wp_i18n_namespaceObject.__)('Publish'),

    /* translators: accessibility text for the footer landmark region. */
    footer: (0,external_wp_i18n_namespaceObject.__)('Footer')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  const headerVariants = {
    hidden: isDistractionFree ? {
      opacity: 0
    } : {
      opacity: 1
    },
    hover: {
      opacity: 1,
      transition: {
        type: 'tween',
        delay: 0.2,
        delayChildren: 0.2
      }
    }
  };
  return (0,external_wp_element_namespaceObject.createElement)("div", extends_extends({}, enableRegionNavigation ? navigateRegionsProps : {}, {
    ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, enableRegionNavigation ? navigateRegionsProps.ref : undefined]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && isDistractionFree && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    as: external_wp_components_namespaceObject.__unstableMotion.div,
    className: "interface-interface-skeleton__header",
    "aria-label": mergedLabels.header,
    initial: isDistractionFree ? 'hidden' : 'hover',
    whileHover: "hover",
    variants: headerVariants,
    transition: {
      type: 'tween',
      delay: 0.8
    }
  }, header), !!header && !isDistractionFree && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__header",
    ariaLabel: mergedLabels.header
  }, header), isDistractionFree && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__header"
  }, editorNotices), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__secondary-sidebar",
    ariaLabel: mergedLabels.secondarySidebar
  }, secondarySidebar), !!notices && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__content",
    ariaLabel: mergedLabels.body
  }, content), !!sidebar && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__sidebar",
    ariaLabel: mergedLabels.sidebar
  }, sidebar), !!actions && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__actions",
    ariaLabel: mergedLabels.actions
  }, actions))), !!footer && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "interface-interface-skeleton__footer",
    ariaLabel: mergedLabels.footer
  }, footer));
}

/* harmony default export */ var interface_skeleton = ((0,external_wp_element_namespaceObject.forwardRef)(InterfaceSkeleton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js


/**
 * WordPress dependencies
 */

const moreVertical = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ var more_vertical = (moreVertical);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function MoreMenuDropdown(_ref) {
  let {
    as: DropdownComponent = external_wp_components_namespaceObject.DropdownMenu,
    className,

    /* translators: button label text should, if possible, be under 16 characters. */
    label = (0,external_wp_i18n_namespaceObject.__)('Options'),
    popoverProps,
    toggleProps,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical,
    label: label,
    popoverProps: {
      placement: 'bottom-end',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps === null || popoverProps === void 0 ? void 0 : popoverProps.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal/index.js


/**
 * WordPress dependencies
 */


function PreferencesModal(_ref) {
  let {
    closeModal,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "interface-preferences-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Preferences'),
    onRequestClose: closeModal
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon(_ref) {
  let {
    icon,
    size = 24,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ var build_module_icon = (Icon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js


/**
 * WordPress dependencies
 */

const chevronLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ var chevron_left = (chevronLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right.js


/**
 * WordPress dependencies
 */

const chevronRight = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
}));
/* harmony default export */ var chevron_right = (chevronRight);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-tabs/index.js


/**
 * WordPress dependencies
 */





const PREFERENCES_MENU = 'preferences-menu';
function PreferencesModalTabs(_ref) {
  let {
    sections
  } = _ref;
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium'); // This is also used to sync the two different rendered components
  // between small and large viewports.

  const [activeMenu, setActiveMenu] = (0,external_wp_element_namespaceObject.useState)(PREFERENCES_MENU);
  /**
   * Create helper objects from `sections` for easier data handling.
   * `tabs` is used for creating the `TabPanel` and `sectionsContentMap`
   * is used for easier access to active tab's content.
   */

  const {
    tabs,
    sectionsContentMap
  } = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let mappedTabs = {
      tabs: [],
      sectionsContentMap: {}
    };

    if (sections.length) {
      mappedTabs = sections.reduce((accumulator, _ref2) => {
        let {
          name,
          tabLabel: title,
          content
        } = _ref2;
        accumulator.tabs.push({
          name,
          title
        });
        accumulator.sectionsContentMap[name] = content;
        return accumulator;
      }, {
        tabs: [],
        sectionsContentMap: {}
      });
    }

    return mappedTabs;
  }, [sections]);
  const getCurrentTab = (0,external_wp_element_namespaceObject.useCallback)(tab => sectionsContentMap[tab.name] || null, [sectionsContentMap]);
  let modalContent; // We render different components based on the viewport size.

  if (isLargeViewport) {
    modalContent = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
      className: "interface-preferences__tabs",
      tabs: tabs,
      initialTabName: activeMenu !== PREFERENCES_MENU ? activeMenu : undefined,
      onSelect: setActiveMenu,
      orientation: "vertical"
    }, getCurrentTab);
  } else {
    modalContent = (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorProvider, {
      initialPath: "/",
      className: "interface-preferences__provider"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
      path: "/"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
      isBorderless: true,
      size: "small"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, tabs.map(tab => {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
        key: tab.name,
        path: tab.name,
        as: external_wp_components_namespaceObject.__experimentalItem,
        isAction: true
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
        justify: "space-between"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalTruncate, null, tab.title)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
      }))));
    }))))), sections.length && sections.map(section => {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
        key: `${section.name}-menu`,
        path: section.name
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
        isBorderless: true,
        size: "large"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardHeader, {
        isBorderless: false,
        justify: "left",
        size: "small",
        gap: "6"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorBackButton, {
        icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous view')
      }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
        size: "16"
      }, section.tabLabel)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, section.content)));
    }));
  }

  return modalContent;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-section/index.js


const Section = _ref => {
  let {
    description,
    title,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("fieldset", {
    className: "interface-preferences-modal__section"
  }, (0,external_wp_element_namespaceObject.createElement)("legend", {
    className: "interface-preferences-modal__section-legend"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "interface-preferences-modal__section-title"
  }, title), description && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "interface-preferences-modal__section-description"
  }, description)), children);
};

/* harmony default export */ var preferences_modal_section = (Section);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/preferences-modal-base-option/index.js


/**
 * WordPress dependencies
 */


function BaseOption(_ref) {
  let {
    help,
    label,
    isChecked,
    onChange,
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "interface-preferences-modal__option"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToggleControl, {
    __nextHasNoMarginBottom: true,
    help: help,
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var preferences_modal_base_option = (BaseOption);

;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/index.js














;// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/index.js



;// CONCATENATED MODULE: external ["wp","widgets"]
var external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// CONCATENATED MODULE: external ["wp","hooks"]
var external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// CONCATENATED MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/hooks/components.js
/**
 * WordPress dependencies
 */


(0,external_wp_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-site/components/media-upload', () => external_wp_mediaUtils_namespaceObject.MediaUpload);

;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","notices"]
var external_wp_notices_namespaceObject = window["wp"]["notices"];
;// CONCATENATED MODULE: ./node_modules/colord/index.mjs
var r={grad:.9,turn:360,rad:360/(2*Math.PI)},t=function(r){return"string"==typeof r?r.length>0:"number"==typeof r},n=function(r,t,n){return void 0===t&&(t=0),void 0===n&&(n=Math.pow(10,t)),Math.round(n*r)/n+0},e=function(r,t,n){return void 0===t&&(t=0),void 0===n&&(n=1),r>n?n:r>t?r:t},u=function(r){return(r=isFinite(r)?r%360:0)>0?r:r+360},a=function(r){return{r:e(r.r,0,255),g:e(r.g,0,255),b:e(r.b,0,255),a:e(r.a)}},o=function(r){return{r:n(r.r),g:n(r.g),b:n(r.b),a:n(r.a,3)}},i=/^#([0-9a-f]{3,8})$/i,s=function(r){var t=r.toString(16);return t.length<2?"0"+t:t},h=function(r){var t=r.r,n=r.g,e=r.b,u=r.a,a=Math.max(t,n,e),o=a-Math.min(t,n,e),i=o?a===t?(n-e)/o:a===n?2+(e-t)/o:4+(t-n)/o:0;return{h:60*(i<0?i+6:i),s:a?o/a*100:0,v:a/255*100,a:u}},b=function(r){var t=r.h,n=r.s,e=r.v,u=r.a;t=t/360*6,n/=100,e/=100;var a=Math.floor(t),o=e*(1-n),i=e*(1-(t-a)*n),s=e*(1-(1-t+a)*n),h=a%6;return{r:255*[e,i,o,o,s,e][h],g:255*[s,e,e,i,o,o][h],b:255*[o,o,s,e,e,i][h],a:u}},g=function(r){return{h:u(r.h),s:e(r.s,0,100),l:e(r.l,0,100),a:e(r.a)}},d=function(r){return{h:n(r.h),s:n(r.s),l:n(r.l),a:n(r.a,3)}},f=function(r){return b((n=(t=r).s,{h:t.h,s:(n*=((e=t.l)<50?e:100-e)/100)>0?2*n/(e+n)*100:0,v:e+n,a:t.a}));var t,n,e},c=function(r){return{h:(t=h(r)).h,s:(u=(200-(n=t.s))*(e=t.v)/100)>0&&u<200?n*e/100/(u<=100?u:200-u)*100:0,l:u/2,a:t.a};var t,n,e,u},l=/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,p=/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,v=/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,m=/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,y={string:[[function(r){var t=i.exec(r);return t?(r=t[1]).length<=4?{r:parseInt(r[0]+r[0],16),g:parseInt(r[1]+r[1],16),b:parseInt(r[2]+r[2],16),a:4===r.length?n(parseInt(r[3]+r[3],16)/255,2):1}:6===r.length||8===r.length?{r:parseInt(r.substr(0,2),16),g:parseInt(r.substr(2,2),16),b:parseInt(r.substr(4,2),16),a:8===r.length?n(parseInt(r.substr(6,2),16)/255,2):1}:null:null},"hex"],[function(r){var t=v.exec(r)||m.exec(r);return t?t[2]!==t[4]||t[4]!==t[6]?null:a({r:Number(t[1])/(t[2]?100/255:1),g:Number(t[3])/(t[4]?100/255:1),b:Number(t[5])/(t[6]?100/255:1),a:void 0===t[7]?1:Number(t[7])/(t[8]?100:1)}):null},"rgb"],[function(t){var n=l.exec(t)||p.exec(t);if(!n)return null;var e,u,a=g({h:(e=n[1],u=n[2],void 0===u&&(u="deg"),Number(e)*(r[u]||1)),s:Number(n[3]),l:Number(n[4]),a:void 0===n[5]?1:Number(n[5])/(n[6]?100:1)});return f(a)},"hsl"]],object:[[function(r){var n=r.r,e=r.g,u=r.b,o=r.a,i=void 0===o?1:o;return t(n)&&t(e)&&t(u)?a({r:Number(n),g:Number(e),b:Number(u),a:Number(i)}):null},"rgb"],[function(r){var n=r.h,e=r.s,u=r.l,a=r.a,o=void 0===a?1:a;if(!t(n)||!t(e)||!t(u))return null;var i=g({h:Number(n),s:Number(e),l:Number(u),a:Number(o)});return f(i)},"hsl"],[function(r){var n=r.h,a=r.s,o=r.v,i=r.a,s=void 0===i?1:i;if(!t(n)||!t(a)||!t(o))return null;var h=function(r){return{h:u(r.h),s:e(r.s,0,100),v:e(r.v,0,100),a:e(r.a)}}({h:Number(n),s:Number(a),v:Number(o),a:Number(s)});return b(h)},"hsv"]]},N=function(r,t){for(var n=0;n<t.length;n++){var e=t[n][0](r);if(e)return[e,t[n][1]]}return[null,void 0]},x=function(r){return"string"==typeof r?N(r.trim(),y.string):"object"==typeof r&&null!==r?N(r,y.object):[null,void 0]},I=function(r){return x(r)[1]},M=function(r,t){var n=c(r);return{h:n.h,s:e(n.s+100*t,0,100),l:n.l,a:n.a}},H=function(r){return(299*r.r+587*r.g+114*r.b)/1e3/255},$=function(r,t){var n=c(r);return{h:n.h,s:n.s,l:e(n.l+100*t,0,100),a:n.a}},j=function(){function r(r){this.parsed=x(r)[0],this.rgba=this.parsed||{r:0,g:0,b:0,a:1}}return r.prototype.isValid=function(){return null!==this.parsed},r.prototype.brightness=function(){return n(H(this.rgba),2)},r.prototype.isDark=function(){return H(this.rgba)<.5},r.prototype.isLight=function(){return H(this.rgba)>=.5},r.prototype.toHex=function(){return r=o(this.rgba),t=r.r,e=r.g,u=r.b,i=(a=r.a)<1?s(n(255*a)):"","#"+s(t)+s(e)+s(u)+i;var r,t,e,u,a,i},r.prototype.toRgb=function(){return o(this.rgba)},r.prototype.toRgbString=function(){return r=o(this.rgba),t=r.r,n=r.g,e=r.b,(u=r.a)<1?"rgba("+t+", "+n+", "+e+", "+u+")":"rgb("+t+", "+n+", "+e+")";var r,t,n,e,u},r.prototype.toHsl=function(){return d(c(this.rgba))},r.prototype.toHslString=function(){return r=d(c(this.rgba)),t=r.h,n=r.s,e=r.l,(u=r.a)<1?"hsla("+t+", "+n+"%, "+e+"%, "+u+")":"hsl("+t+", "+n+"%, "+e+"%)";var r,t,n,e,u},r.prototype.toHsv=function(){return r=h(this.rgba),{h:n(r.h),s:n(r.s),v:n(r.v),a:n(r.a,3)};var r},r.prototype.invert=function(){return w({r:255-(r=this.rgba).r,g:255-r.g,b:255-r.b,a:r.a});var r},r.prototype.saturate=function(r){return void 0===r&&(r=.1),w(M(this.rgba,r))},r.prototype.desaturate=function(r){return void 0===r&&(r=.1),w(M(this.rgba,-r))},r.prototype.grayscale=function(){return w(M(this.rgba,-1))},r.prototype.lighten=function(r){return void 0===r&&(r=.1),w($(this.rgba,r))},r.prototype.darken=function(r){return void 0===r&&(r=.1),w($(this.rgba,-r))},r.prototype.rotate=function(r){return void 0===r&&(r=15),this.hue(this.hue()+r)},r.prototype.alpha=function(r){return"number"==typeof r?w({r:(t=this.rgba).r,g:t.g,b:t.b,a:r}):n(this.rgba.a,3);var t},r.prototype.hue=function(r){var t=c(this.rgba);return"number"==typeof r?w({h:r,s:t.s,l:t.l,a:t.a}):n(t.h)},r.prototype.isEqual=function(r){return this.toHex()===w(r).toHex()},r}(),w=function(r){return r instanceof j?r:new j(r)},S=[],k=function(r){r.forEach(function(r){S.indexOf(r)<0&&(r(j,y),S.push(r))})},E=function(){return new j({r:255*Math.random(),g:255*Math.random(),b:255*Math.random()})};

;// CONCATENATED MODULE: ./node_modules/colord/plugins/a11y.mjs
var a11y_o=function(o){var t=o/255;return t<.04045?t/12.92:Math.pow((t+.055)/1.055,2.4)},a11y_t=function(t){return.2126*a11y_o(t.r)+.7152*a11y_o(t.g)+.0722*a11y_o(t.b)};/* harmony default export */ function a11y(o){o.prototype.luminance=function(){return o=a11y_t(this.rgba),void 0===(r=2)&&(r=0),void 0===n&&(n=Math.pow(10,r)),Math.round(n*o)/n+0;var o,r,n},o.prototype.contrast=function(r){void 0===r&&(r="#FFF");var n,a,i,e,v,u,d,c=r instanceof o?r:new o(r);return e=this.rgba,v=c.toRgb(),u=a11y_t(e),d=a11y_t(v),n=u>d?(u+.05)/(d+.05):(d+.05)/(u+.05),void 0===(a=2)&&(a=0),void 0===i&&(i=Math.pow(10,a)),Math.floor(i*n)/i+0},o.prototype.isReadable=function(o,t){return void 0===o&&(o="#FFF"),void 0===t&&(t={}),this.contrast(o)>=(e=void 0===(i=(r=t).size)?"normal":i,"AAA"===(a=void 0===(n=r.level)?"AA":n)&&"normal"===e?7:"AA"===a&&"large"===e?3:4.5);var r,n,a,i,e}}

;// CONCATENATED MODULE: external ["wp","privateApis"]
var external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/private-apis.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/edit-site');

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/hooks.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


const {
  useGlobalSetting
} = unlock(external_wp_blockEditor_namespaceObject.privateApis); // Enable colord's a11y plugin.

k([a11y]);
const ROOT_BLOCK_SUPPORTS = ['background', 'backgroundColor', 'color', 'linkColor', 'buttonColor', 'fontFamily', 'fontSize', 'fontStyle', 'fontWeight', 'lineHeight', 'textDecoration', 'padding', 'contentSize', 'wideSize', 'blockGap'];
function getSupportedGlobalStylesPanels(name) {
  var _blockType$supports, _blockType$supports$s, _blockType$supports2, _blockType$supports2$, _blockType$supports3, _blockType$supports3$, _blockType$supports3$2, _blockType$supports3$3, _blockType$supports4;

  if (!name) {
    return ROOT_BLOCK_SUPPORTS;
  }

  const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(name);

  if (!blockType) {
    return [];
  }

  const supportKeys = []; // Check for blockGap support.
  // Block spacing support doesn't map directly to a single style property, so needs to be handled separately.
  // Also, only allow `blockGap` support if serialization has not been skipped, to be sure global spacing can be applied.

  if (blockType !== null && blockType !== void 0 && (_blockType$supports = blockType.supports) !== null && _blockType$supports !== void 0 && (_blockType$supports$s = _blockType$supports.spacing) !== null && _blockType$supports$s !== void 0 && _blockType$supports$s.blockGap && (blockType === null || blockType === void 0 ? void 0 : (_blockType$supports2 = blockType.supports) === null || _blockType$supports2 === void 0 ? void 0 : (_blockType$supports2$ = _blockType$supports2.spacing) === null || _blockType$supports2$ === void 0 ? void 0 : _blockType$supports2$.__experimentalSkipSerialization) !== true && !(blockType !== null && blockType !== void 0 && (_blockType$supports3 = blockType.supports) !== null && _blockType$supports3 !== void 0 && (_blockType$supports3$ = _blockType$supports3.spacing) !== null && _blockType$supports3$ !== void 0 && (_blockType$supports3$2 = _blockType$supports3$.__experimentalSkipSerialization) !== null && _blockType$supports3$2 !== void 0 && (_blockType$supports3$3 = _blockType$supports3$2.some) !== null && _blockType$supports3$3 !== void 0 && _blockType$supports3$3.call(_blockType$supports3$2, spacingType => spacingType === 'blockGap'))) {
    supportKeys.push('blockGap');
  } // check for shadow support


  if (blockType !== null && blockType !== void 0 && (_blockType$supports4 = blockType.supports) !== null && _blockType$supports4 !== void 0 && _blockType$supports4.shadow) {
    supportKeys.push('shadow');
  }

  Object.keys(external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY).forEach(styleName => {
    if (!external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[styleName].support) {
      return;
    } // Opting out means that, for certain support keys like background color,
    // blocks have to explicitly set the support value false. If the key is
    // unset, we still enable it.


    if (external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[styleName].requiresOptOut) {
      if (external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[styleName].support[0] in blockType.supports && (0,external_lodash_namespaceObject.get)(blockType.supports, external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[styleName].support) !== false) {
        return supportKeys.push(styleName);
      }
    }

    if ((0,external_lodash_namespaceObject.get)(blockType.supports, external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[styleName].support, false)) {
      return supportKeys.push(styleName);
    }
  });
  return supportKeys;
}
function useColorsPerOrigin(name) {
  const [customColors] = useGlobalSetting('color.palette.custom', name);
  const [themeColors] = useGlobalSetting('color.palette.theme', name);
  const [defaultColors] = useGlobalSetting('color.palette.default', name);
  const [shouldDisplayDefaultColors] = useGlobalSetting('color.defaultPalette');
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const result = [];

    if (themeColors && themeColors.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Theme', 'Indicates this palette comes from the theme.'),
        colors: themeColors
      });
    }

    if (shouldDisplayDefaultColors && defaultColors && defaultColors.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Default', 'Indicates this palette comes from WordPress.'),
        colors: defaultColors
      });
    }

    if (customColors && customColors.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Custom', 'Indicates this palette is created by the user.'),
        colors: customColors
      });
    }

    return result;
  }, [customColors, themeColors, defaultColors]);
}
function useGradientsPerOrigin(name) {
  const [customGradients] = useGlobalSetting('color.gradients.custom', name);
  const [themeGradients] = useGlobalSetting('color.gradients.theme', name);
  const [defaultGradients] = useGlobalSetting('color.gradients.default', name);
  const [shouldDisplayDefaultGradients] = useGlobalSetting('color.defaultGradients');
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const result = [];

    if (themeGradients && themeGradients.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Theme', 'Indicates this palette comes from the theme.'),
        gradients: themeGradients
      });
    }

    if (shouldDisplayDefaultGradients && defaultGradients && defaultGradients.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Default', 'Indicates this palette comes from WordPress.'),
        gradients: defaultGradients
      });
    }

    if (customGradients && customGradients.length) {
      result.push({
        name: (0,external_wp_i18n_namespaceObject._x)('Custom', 'Indicates this palette is created by the user.'),
        gradients: customGradients
      });
    }

    return result;
  }, [customGradients, themeGradients, defaultGradients]);
}
function useColorRandomizer(name) {
  const [themeColors, setThemeColors] = useGlobalSetting('color.palette.theme', name);

  function randomizeColors() {
    /* eslint-disable no-restricted-syntax */
    const randomRotationValue = Math.floor(Math.random() * 225);
    /* eslint-enable no-restricted-syntax */

    const newColors = themeColors.map(colorObject => {
      const {
        color
      } = colorObject;
      const newColor = w(color).rotate(randomRotationValue).toHex();
      return { ...colorObject,
        color: newColor
      };
    });
    setThemeColors(newColors);
  }

  return window.__experimentalEnableColorRandomizer ? [randomizeColors] : [];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/hooks/push-changes-to-global-styles/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */



const {
  GlobalStylesContext
} = unlock(external_wp_blockEditor_namespaceObject.privateApis); // TODO: Temporary duplication of constant in @wordpress/block-editor. Can be
// removed by moving PushChangesToGlobalStylesControl to
// @wordpress/block-editor.

const STYLE_PATH_TO_CSS_VAR_INFIX = {
  'color.background': 'color',
  'color.text': 'color',
  'elements.link.color.text': 'color',
  'elements.link.:hover.color.text': 'color',
  'elements.link.typography.fontFamily': 'font-family',
  'elements.link.typography.fontSize': 'font-size',
  'elements.button.color.text': 'color',
  'elements.button.color.background': 'color',
  'elements.button.typography.fontFamily': 'font-family',
  'elements.button.typography.fontSize': 'font-size',
  'elements.heading.color': 'color',
  'elements.heading.color.background': 'color',
  'elements.heading.typography.fontFamily': 'font-family',
  'elements.heading.gradient': 'gradient',
  'elements.heading.color.gradient': 'gradient',
  'elements.h1.color': 'color',
  'elements.h1.color.background': 'color',
  'elements.h1.typography.fontFamily': 'font-family',
  'elements.h1.color.gradient': 'gradient',
  'elements.h2.color': 'color',
  'elements.h2.color.background': 'color',
  'elements.h2.typography.fontFamily': 'font-family',
  'elements.h2.color.gradient': 'gradient',
  'elements.h3.color': 'color',
  'elements.h3.color.background': 'color',
  'elements.h3.typography.fontFamily': 'font-family',
  'elements.h3.color.gradient': 'gradient',
  'elements.h4.color': 'color',
  'elements.h4.color.background': 'color',
  'elements.h4.typography.fontFamily': 'font-family',
  'elements.h4.color.gradient': 'gradient',
  'elements.h5.color': 'color',
  'elements.h5.color.background': 'color',
  'elements.h5.typography.fontFamily': 'font-family',
  'elements.h5.color.gradient': 'gradient',
  'elements.h6.color': 'color',
  'elements.h6.color.background': 'color',
  'elements.h6.typography.fontFamily': 'font-family',
  'elements.h6.color.gradient': 'gradient',
  'color.gradient': 'gradient',
  'typography.fontSize': 'font-size',
  'typography.fontFamily': 'font-family'
}; // TODO: Temporary duplication of constant in @wordpress/block-editor. Can be
// removed by moving PushChangesToGlobalStylesControl to
// @wordpress/block-editor.

const STYLE_PATH_TO_PRESET_BLOCK_ATTRIBUTE = {
  'color.background': 'backgroundColor',
  'color.text': 'textColor',
  'color.gradient': 'gradient',
  'typography.fontSize': 'fontSize',
  'typography.fontFamily': 'fontFamily'
};

function getChangesToPush(name, attributes) {
  return getSupportedGlobalStylesPanels(name).flatMap(key => {
    if (!external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[key]) {
      return [];
    }

    const {
      value: path
    } = external_wp_blocks_namespaceObject.__EXPERIMENTAL_STYLE_PROPERTY[key];
    const presetAttributeKey = path.join('.');
    const presetAttributeValue = attributes[STYLE_PATH_TO_PRESET_BLOCK_ATTRIBUTE[presetAttributeKey]];
    const value = presetAttributeValue ? `var:preset|${STYLE_PATH_TO_CSS_VAR_INFIX[presetAttributeKey]}|${presetAttributeValue}` : (0,external_lodash_namespaceObject.get)(attributes.style, path);
    return value ? [{
      path,
      value
    }] : [];
  });
}

function cloneDeep(object) {
  return !object ? {} : JSON.parse(JSON.stringify(object));
}

function PushChangesToGlobalStylesControl(_ref) {
  let {
    name,
    attributes,
    setAttributes
  } = _ref;
  const changes = (0,external_wp_element_namespaceObject.useMemo)(() => getChangesToPush(name, attributes), [name, attributes]);
  const {
    user: userConfig,
    setUserConfig
  } = (0,external_wp_element_namespaceObject.useContext)(GlobalStylesContext);
  const {
    __unstableMarkNextChangeAsNotPersistent
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const pushChanges = (0,external_wp_element_namespaceObject.useCallback)(() => {
    if (changes.length === 0) {
      return;
    }

    const {
      style: blockStyles
    } = attributes;
    const newBlockStyles = cloneDeep(blockStyles);
    const newUserConfig = cloneDeep(userConfig);

    for (const {
      path,
      value
    } of changes) {
      (0,external_lodash_namespaceObject.set)(newBlockStyles, path, undefined);
      (0,external_lodash_namespaceObject.set)(newUserConfig, ['styles', 'blocks', name, ...path], value);
    } // @wordpress/core-data doesn't support editing multiple entity types in
    // a single undo level. So for now, we disable @wordpress/core-data undo
    // tracking and implement our own Undo button in the snackbar
    // notification.


    __unstableMarkNextChangeAsNotPersistent();

    setAttributes({
      style: newBlockStyles
    });
    setUserConfig(() => newUserConfig, {
      undoIgnore: true
    });
    createSuccessNotice((0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Title of the block e.g. 'Heading'.
    (0,external_wp_i18n_namespaceObject.__)('%s styles applied.'), (0,external_wp_blocks_namespaceObject.getBlockType)(name).title), {
      type: 'snackbar',
      actions: [{
        label: (0,external_wp_i18n_namespaceObject.__)('Undo'),

        onClick() {
          __unstableMarkNextChangeAsNotPersistent();

          setAttributes({
            style: blockStyles
          });
          setUserConfig(() => userConfig, {
            undoIgnore: true
          });
        }

      }]
    });
  }, [changes, attributes, userConfig, name]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.BaseControl, {
    className: "edit-site-push-changes-to-global-styles-control",
    help: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Title of the block e.g. 'Heading'.
    (0,external_wp_i18n_namespaceObject.__)('Apply this block’s typography, spacing, dimensions, and color styles to all %s blocks.'), (0,external_wp_blocks_namespaceObject.getBlockType)(name).title)
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.BaseControl.VisualLabel, null, (0,external_wp_i18n_namespaceObject.__)('Styles')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    disabled: changes.length === 0,
    onClick: pushChanges
  }, (0,external_wp_i18n_namespaceObject.__)('Apply globally')));
}

const withPushChangesToGlobalStyles = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(BlockEdit, props), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.InspectorAdvancedControls, null, (0,external_wp_element_namespaceObject.createElement)(PushChangesToGlobalStylesControl, props))));
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/edit-site/push-changes-to-global-styles', withPushChangesToGlobalStyles);

;// CONCATENATED MODULE: ./node_modules/history/index.js


/**
 * Actions represent the type of change to a location value.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#action
 */
var Action;

(function (Action) {
  /**
   * A POP indicates a change to an arbitrary index in the history stack, such
   * as a back or forward navigation. It does not describe the direction of the
   * navigation, only that the current index changed.
   *
   * Note: This is the default action for newly created history objects.
   */
  Action["Pop"] = "POP";
  /**
   * A PUSH indicates a new entry being added to the history stack, such as when
   * a link is clicked and a new page loads. When this happens, all subsequent
   * entries in the stack are lost.
   */

  Action["Push"] = "PUSH";
  /**
   * A REPLACE indicates the entry at the current index in the history stack
   * being replaced by a new one.
   */

  Action["Replace"] = "REPLACE";
})(Action || (Action = {}));

var readOnly =  false ? 0 : function (obj) {
  return obj;
};

function warning(cond, message) {
  if (!cond) {
    // eslint-disable-next-line no-console
    if (typeof console !== 'undefined') console.warn(message);

    try {
      // Welcome to debugging history!
      //
      // This error is thrown as a convenience so you can more easily
      // find the source for a warning that appears in the console by
      // enabling "pause on exceptions" in your JavaScript debugger.
      throw new Error(message); // eslint-disable-next-line no-empty
    } catch (e) {}
  }
}

var BeforeUnloadEventType = 'beforeunload';
var HashChangeEventType = 'hashchange';
var PopStateEventType = 'popstate';
/**
 * Browser history stores the location in regular URLs. This is the standard for
 * most web apps, but it requires some configuration on the server to ensure you
 * serve the same app at multiple URLs.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createbrowserhistory
 */

function createBrowserHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options = options,
      _options$window = _options.window,
      window = _options$window === void 0 ? document.defaultView : _options$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _window$location = window.location,
        pathname = _window$location.pathname,
        search = _window$location.search,
        hash = _window$location.hash;
    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation[0],
          nextLocation = _getIndexAndLocation[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           false ? 0 : void 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop);
  var action = Action.Pop;

  var _getIndexAndLocation2 = getIndexAndLocation(),
      index = _getIndexAndLocation2[0],
      location = _getIndexAndLocation2[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState(extends_extends({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  } // state defaults to `null` because `window.history.state` does


  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(extends_extends({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation3 = getIndexAndLocation();

    index = _getIndexAndLocation3[0];
    location = _getIndexAndLocation3[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr[0],
          url = _getHistoryStateAndUr[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr2 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr2[0],
          url = _getHistoryStateAndUr2[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Hash history stores the location in window.location.hash. This makes it ideal
 * for situations where you don't want to send the location to the server for
 * some reason, either because you do cannot configure it or the URL space is
 * reserved for something else.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createhashhistory
 */

function createHashHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options2 = options,
      _options2$window = _options2.window,
      window = _options2$window === void 0 ? document.defaultView : _options2$window;
  var globalHistory = window.history;

  function getIndexAndLocation() {
    var _parsePath = parsePath(window.location.hash.substr(1)),
        _parsePath$pathname = _parsePath.pathname,
        pathname = _parsePath$pathname === void 0 ? '/' : _parsePath$pathname,
        _parsePath$search = _parsePath.search,
        search = _parsePath$search === void 0 ? '' : _parsePath$search,
        _parsePath$hash = _parsePath.hash,
        hash = _parsePath$hash === void 0 ? '' : _parsePath$hash;

    var state = globalHistory.state || {};
    return [state.idx, readOnly({
      pathname: pathname,
      search: search,
      hash: hash,
      state: state.usr || null,
      key: state.key || 'default'
    })];
  }

  var blockedPopTx = null;

  function handlePop() {
    if (blockedPopTx) {
      blockers.call(blockedPopTx);
      blockedPopTx = null;
    } else {
      var nextAction = Action.Pop;

      var _getIndexAndLocation4 = getIndexAndLocation(),
          nextIndex = _getIndexAndLocation4[0],
          nextLocation = _getIndexAndLocation4[1];

      if (blockers.length) {
        if (nextIndex != null) {
          var delta = index - nextIndex;

          if (delta) {
            // Revert the POP
            blockedPopTx = {
              action: nextAction,
              location: nextLocation,
              retry: function retry() {
                go(delta * -1);
              }
            };
            go(delta);
          }
        } else {
          // Trying to POP to a location with no index. We did not create
          // this location, so we can't effectively block the navigation.
           false ? 0 : void 0;
        }
      } else {
        applyTx(nextAction);
      }
    }
  }

  window.addEventListener(PopStateEventType, handlePop); // popstate does not fire on hashchange in IE 11 and old (trident) Edge
  // https://developer.mozilla.org/de/docs/Web/API/Window/popstate_event

  window.addEventListener(HashChangeEventType, function () {
    var _getIndexAndLocation5 = getIndexAndLocation(),
        nextLocation = _getIndexAndLocation5[1]; // Ignore extraneous hashchange events.


    if (createPath(nextLocation) !== createPath(location)) {
      handlePop();
    }
  });
  var action = Action.Pop;

  var _getIndexAndLocation6 = getIndexAndLocation(),
      index = _getIndexAndLocation6[0],
      location = _getIndexAndLocation6[1];

  var listeners = createEvents();
  var blockers = createEvents();

  if (index == null) {
    index = 0;
    globalHistory.replaceState(_extends({}, globalHistory.state, {
      idx: index
    }), '');
  }

  function getBaseHref() {
    var base = document.querySelector('base');
    var href = '';

    if (base && base.getAttribute('href')) {
      var url = window.location.href;
      var hashIndex = url.indexOf('#');
      href = hashIndex === -1 ? url : url.slice(0, hashIndex);
    }

    return href;
  }

  function createHref(to) {
    return getBaseHref() + '#' + (typeof to === 'string' ? to : createPath(to));
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(_extends({
      pathname: location.pathname,
      hash: '',
      search: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function getHistoryStateAndUrl(nextLocation, index) {
    return [{
      usr: nextLocation.state,
      key: nextLocation.key,
      idx: index
    }, createHref(nextLocation)];
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction) {
    action = nextAction;

    var _getIndexAndLocation7 = getIndexAndLocation();

    index = _getIndexAndLocation7[0];
    location = _getIndexAndLocation7[1];
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr3 = getHistoryStateAndUrl(nextLocation, index + 1),
          historyState = _getHistoryStateAndUr3[0],
          url = _getHistoryStateAndUr3[1]; // TODO: Support forced reloading
      // try...catch because iOS limits us to 100 pushState calls :/


      try {
        globalHistory.pushState(historyState, '', url);
      } catch (error) {
        // They are going to lose state here, but there is no real
        // way to warn them about it since the page will refresh...
        window.location.assign(url);
      }

      applyTx(nextAction);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      var _getHistoryStateAndUr4 = getHistoryStateAndUrl(nextLocation, index),
          historyState = _getHistoryStateAndUr4[0],
          url = _getHistoryStateAndUr4[1]; // TODO: Support forced reloading


      globalHistory.replaceState(historyState, '', url);
      applyTx(nextAction);
    }
  }

  function go(delta) {
    globalHistory.go(delta);
  }

  var history = {
    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      var unblock = blockers.push(blocker);

      if (blockers.length === 1) {
        window.addEventListener(BeforeUnloadEventType, promptBeforeUnload);
      }

      return function () {
        unblock(); // Remove the beforeunload listener so the document may
        // still be salvageable in the pagehide event.
        // See https://html.spec.whatwg.org/#unloading-documents

        if (!blockers.length) {
          window.removeEventListener(BeforeUnloadEventType, promptBeforeUnload);
        }
      };
    }
  };
  return history;
}
/**
 * Memory history stores the current location in memory. It is designed for use
 * in stateful non-browser environments like tests and React Native.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#creatememoryhistory
 */

function createMemoryHistory(options) {
  if (options === void 0) {
    options = {};
  }

  var _options3 = options,
      _options3$initialEntr = _options3.initialEntries,
      initialEntries = _options3$initialEntr === void 0 ? ['/'] : _options3$initialEntr,
      initialIndex = _options3.initialIndex;
  var entries = initialEntries.map(function (entry) {
    var location = readOnly(_extends({
      pathname: '/',
      search: '',
      hash: '',
      state: null,
      key: createKey()
    }, typeof entry === 'string' ? parsePath(entry) : entry));
     false ? 0 : void 0;
    return location;
  });
  var index = clamp(initialIndex == null ? entries.length - 1 : initialIndex, 0, entries.length - 1);
  var action = Action.Pop;
  var location = entries[index];
  var listeners = createEvents();
  var blockers = createEvents();

  function createHref(to) {
    return typeof to === 'string' ? to : createPath(to);
  }

  function getNextLocation(to, state) {
    if (state === void 0) {
      state = null;
    }

    return readOnly(_extends({
      pathname: location.pathname,
      search: '',
      hash: ''
    }, typeof to === 'string' ? parsePath(to) : to, {
      state: state,
      key: createKey()
    }));
  }

  function allowTx(action, location, retry) {
    return !blockers.length || (blockers.call({
      action: action,
      location: location,
      retry: retry
    }), false);
  }

  function applyTx(nextAction, nextLocation) {
    action = nextAction;
    location = nextLocation;
    listeners.call({
      action: action,
      location: location
    });
  }

  function push(to, state) {
    var nextAction = Action.Push;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      push(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      index += 1;
      entries.splice(index, entries.length, nextLocation);
      applyTx(nextAction, nextLocation);
    }
  }

  function replace(to, state) {
    var nextAction = Action.Replace;
    var nextLocation = getNextLocation(to, state);

    function retry() {
      replace(to, state);
    }

     false ? 0 : void 0;

    if (allowTx(nextAction, nextLocation, retry)) {
      entries[index] = nextLocation;
      applyTx(nextAction, nextLocation);
    }
  }

  function go(delta) {
    var nextIndex = clamp(index + delta, 0, entries.length - 1);
    var nextAction = Action.Pop;
    var nextLocation = entries[nextIndex];

    function retry() {
      go(delta);
    }

    if (allowTx(nextAction, nextLocation, retry)) {
      index = nextIndex;
      applyTx(nextAction, nextLocation);
    }
  }

  var history = {
    get index() {
      return index;
    },

    get action() {
      return action;
    },

    get location() {
      return location;
    },

    createHref: createHref,
    push: push,
    replace: replace,
    go: go,
    back: function back() {
      go(-1);
    },
    forward: function forward() {
      go(1);
    },
    listen: function listen(listener) {
      return listeners.push(listener);
    },
    block: function block(blocker) {
      return blockers.push(blocker);
    }
  };
  return history;
} ////////////////////////////////////////////////////////////////////////////////
// UTILS
////////////////////////////////////////////////////////////////////////////////

function clamp(n, lowerBound, upperBound) {
  return Math.min(Math.max(n, lowerBound), upperBound);
}

function promptBeforeUnload(event) {
  // Cancel the event.
  event.preventDefault(); // Chrome (and legacy IE) requires returnValue to be set.

  event.returnValue = '';
}

function createEvents() {
  var handlers = [];
  return {
    get length() {
      return handlers.length;
    },

    push: function push(fn) {
      handlers.push(fn);
      return function () {
        handlers = handlers.filter(function (handler) {
          return handler !== fn;
        });
      };
    },
    call: function call(arg) {
      handlers.forEach(function (fn) {
        return fn && fn(arg);
      });
    }
  };
}

function createKey() {
  return Math.random().toString(36).substr(2, 8);
}
/**
 * Creates a string URL path from the given pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#createpath
 */


function createPath(_ref) {
  var _ref$pathname = _ref.pathname,
      pathname = _ref$pathname === void 0 ? '/' : _ref$pathname,
      _ref$search = _ref.search,
      search = _ref$search === void 0 ? '' : _ref$search,
      _ref$hash = _ref.hash,
      hash = _ref$hash === void 0 ? '' : _ref$hash;
  if (search && search !== '?') pathname += search.charAt(0) === '?' ? search : '?' + search;
  if (hash && hash !== '#') pathname += hash.charAt(0) === '#' ? hash : '#' + hash;
  return pathname;
}
/**
 * Parses a string URL path into its separate pathname, search, and hash components.
 *
 * @see https://github.com/remix-run/history/tree/main/docs/api-reference.md#parsepath
 */

function parsePath(path) {
  var parsedPath = {};

  if (path) {
    var hashIndex = path.indexOf('#');

    if (hashIndex >= 0) {
      parsedPath.hash = path.substr(hashIndex);
      path = path.substr(0, hashIndex);
    }

    var searchIndex = path.indexOf('?');

    if (searchIndex >= 0) {
      parsedPath.search = path.substr(searchIndex);
      path = path.substr(0, searchIndex);
    }

    if (path) {
      parsedPath.pathname = path;
    }
  }

  return parsedPath;
}



;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/utils/history.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


const history_history = createBrowserHistory();
const originalHistoryPush = history_history.push;
const originalHistoryReplace = history_history.replace;

function push(params, state) {
  const currentArgs = (0,external_wp_url_namespaceObject.getQueryArgs)(window.location.href);
  const currentUrlWithoutArgs = (0,external_wp_url_namespaceObject.removeQueryArgs)(window.location.href, ...Object.keys(currentArgs));
  const newUrl = (0,external_wp_url_namespaceObject.addQueryArgs)(currentUrlWithoutArgs, params);
  return originalHistoryPush.call(history_history, newUrl, state);
}

function replace(params, state) {
  const currentArgs = (0,external_wp_url_namespaceObject.getQueryArgs)(window.location.href);
  const currentUrlWithoutArgs = (0,external_wp_url_namespaceObject.removeQueryArgs)(window.location.href, ...Object.keys(currentArgs));
  const newUrl = (0,external_wp_url_namespaceObject.addQueryArgs)(currentUrlWithoutArgs, params);
  return originalHistoryReplace.call(history_history, newUrl, state);
}

history_history.push = push;
history_history.replace = replace;
/* harmony default export */ var utils_history = (history_history);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/routes/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const RoutesContext = (0,external_wp_element_namespaceObject.createContext)();
const HistoryContext = (0,external_wp_element_namespaceObject.createContext)();
function useLocation() {
  return (0,external_wp_element_namespaceObject.useContext)(RoutesContext);
}
function useHistory() {
  return (0,external_wp_element_namespaceObject.useContext)(HistoryContext);
}

function getLocationWithParams(location) {
  const searchParams = new URLSearchParams(location.search);
  return { ...location,
    params: Object.fromEntries(searchParams.entries())
  };
}

function Routes(_ref) {
  let {
    children
  } = _ref;
  const [location, setLocation] = (0,external_wp_element_namespaceObject.useState)(() => getLocationWithParams(utils_history.location));
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    return utils_history.listen(_ref2 => {
      let {
        location: updatedLocation
      } = _ref2;
      setLocation(getLocationWithParams(updatedLocation));
    });
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(HistoryContext.Provider, {
    value: utils_history
  }, (0,external_wp_element_namespaceObject.createElement)(RoutesContext.Provider, {
    value: location
  }, children));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/routes/link.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function useLink() {
  let params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let state = arguments.length > 1 ? arguments[1] : undefined;
  let shouldReplace = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
  const history = useHistory();

  function onClick(event) {
    event.preventDefault();

    if (shouldReplace) {
      history.replace(params, state);
    } else {
      history.push(params, state);
    }
  }

  const currentArgs = (0,external_wp_url_namespaceObject.getQueryArgs)(window.location.href);
  const currentUrlWithoutArgs = (0,external_wp_url_namespaceObject.removeQueryArgs)(window.location.href, ...Object.keys(currentArgs));
  const newUrl = (0,external_wp_url_namespaceObject.addQueryArgs)(currentUrlWithoutArgs, params);
  return {
    href: newUrl,
    onClick
  };
}
function Link(_ref) {
  let {
    params = {},
    state,
    replace: shouldReplace = false,
    children,
    ...props
  } = _ref;
  const {
    href,
    onClick
  } = useLink(params, state, shouldReplace);
  return (0,external_wp_element_namespaceObject.createElement)("a", extends_extends({
    href: href,
    onClick: onClick
  }, props), children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/hooks/template-part-edit.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




function EditTemplatePartMenuItem(_ref) {
  let {
    attributes
  } = _ref;
  const {
    theme,
    slug
  } = attributes;
  const {
    params
  } = useLocation();
  const templatePart = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_coreData_namespaceObject.store).getEntityRecord('postType', 'wp_template_part', // Ideally this should be an official public API.
    `${theme}//${slug}`);
  }, [theme, slug]);
  const linkProps = useLink({
    postId: templatePart === null || templatePart === void 0 ? void 0 : templatePart.id,
    postType: templatePart === null || templatePart === void 0 ? void 0 : templatePart.type
  }, {
    fromTemplateId: params.postId
  });

  if (!templatePart) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockControls, {
    group: "other"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarButton, extends_extends({}, linkProps, {
    onClick: event => {
      linkProps.onClick(event);
    }
  }), (0,external_wp_i18n_namespaceObject.__)('Edit')));
}

const withEditBlockControls = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => {
  const {
    attributes,
    name
  } = props;
  const isDisplayed = name === 'core/template-part' && attributes.slug;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(BlockEdit, props), isDisplayed && (0,external_wp_element_namespaceObject.createElement)(EditTemplatePartMenuItem, {
    attributes: attributes
  }));
}, 'withEditBlockControls');
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/edit-site/template-part-edit-button', withEditBlockControls);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/hooks/index.js
/**
 * Internal dependencies
 */




;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/reducer.js
/**
 * WordPress dependencies
 */

/**
 * Reducer returning the editing canvas device type.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function deviceType() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'Desktop';
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_PREVIEW_DEVICE_TYPE':
      return action.deviceType;
  }

  return state;
}
/**
 * Reducer returning the settings.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function settings() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'UPDATE_SETTINGS':
      return { ...state,
        ...action.settings
      };
  }

  return state;
}
/**
 * Reducer keeping track of the currently edited Post Type,
 * Post Id and the context provided to fill the content of the block editor.
 *
 * @param {Object} state  Current edited post.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function editedPost() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_EDITED_POST':
      return {
        postType: action.postType,
        id: action.id,
        context: action.context
      };

    case 'SET_EDITED_POST_CONTEXT':
      return { ...state,
        context: action.context
      };
  }

  return state;
}
/**
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the navigation and list view panels reducers
 * to make sure that only one of the three panels is open at the same time.
 *
 * @param {boolean|Object} state  Current state.
 * @param {Object}         action Dispatched action.
 */

function blockInserterPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;

    case 'SET_IS_INSERTER_OPENED':
      return action.value;

    case 'SET_CANVAS_MODE':
      return false;
  }

  return state;
}
/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the navigation and inserter panels reducers
 * to make sure that only one of the three panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function listViewPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value ? false : state;

    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen;

    case 'SET_CANVAS_MODE':
      return false;
  }

  return state;
}
/**
 * Reducer to set the save view panel open or closed.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function saveViewPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_SAVE_VIEW_OPENED':
      return action.isOpen;

    case 'SET_CANVAS_MODE':
      return false;
  }

  return state;
}
/**
 * Reducer used to track the site editor canvas mode (edit or view).
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function canvasMode() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'init';
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_CANVAS_MODE':
      return action.mode;
  }

  return state;
}

/* harmony default export */ var store_reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  deviceType,
  settings,
  editedPost,
  blockInserterPanel,
  listViewPanel,
  saveViewPanel,
  canvasMode
}));

;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","a11y"]
var external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const constants_STORE_NAME = 'core/edit-site';
const TEMPLATE_PART_AREA_HEADER = 'header';
const TEMPLATE_PART_AREA_FOOTER = 'footer';
const TEMPLATE_PART_AREA_SIDEBAR = 'sidebar';
const TEMPLATE_PART_AREA_GENERAL = 'uncategorized';

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/utils/is-template-revertable.js
/**
 * Check if a template is revertable to its original theme-provided template file.
 *
 * @param {Object} template The template entity to check.
 * @return {boolean} Whether the template is revertable.
 */
function isTemplateRevertable(template) {
  if (!template) {
    return false;
  }
  /* eslint-disable camelcase */


  return (template === null || template === void 0 ? void 0 : template.source) === 'custom' && (template === null || template === void 0 ? void 0 : template.has_theme_file);
  /* eslint-enable camelcase */
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/actions.js
/**
 * WordPress dependencies
 */











/**
 * Internal dependencies
 */



/**
 * Dispatches an action that toggles a feature flag.
 *
 * @param {string} featureName Feature name.
 */

function actions_toggleFeature(featureName) {
  return function (_ref) {
    let {
      registry
    } = _ref;
    external_wp_deprecated_default()("select( 'core/edit-site' ).toggleFeature( featureName )", {
      since: '6.0',
      alternative: "select( 'core/preferences').toggle( 'core/edit-site', featureName )"
    });
    registry.dispatch(external_wp_preferences_namespaceObject.store).toggle('core/edit-site', featureName);
  };
}
/**
 * Action that changes the width of the editing canvas.
 *
 * @param {string} deviceType
 *
 * @return {Object} Action object.
 */

function __experimentalSetPreviewDeviceType(deviceType) {
  return {
    type: 'SET_PREVIEW_DEVICE_TYPE',
    deviceType
  };
}
/**
 * Action that sets a template, optionally fetching it from REST API.
 *
 * @param {number} templateId   The template ID.
 * @param {string} templateSlug The template slug.
 * @return {Object} Action object.
 */

const setTemplate = (templateId, templateSlug) => async _ref2 => {
  let {
    dispatch,
    registry
  } = _ref2;

  if (!templateSlug) {
    try {
      const template = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecord('postType', 'wp_template', templateId);
      templateSlug = template === null || template === void 0 ? void 0 : template.slug;
    } catch (error) {}
  }

  dispatch({
    type: 'SET_EDITED_POST',
    postType: 'wp_template',
    id: templateId,
    context: {
      templateSlug
    }
  });
};
/**
 * Action that adds a new template and sets it as the current template.
 *
 * @param {Object} template The template.
 *
 * @return {Object} Action object used to set the current template.
 */

const addTemplate = template => async _ref3 => {
  let {
    dispatch,
    registry
  } = _ref3;
  const newTemplate = await registry.dispatch(external_wp_coreData_namespaceObject.store).saveEntityRecord('postType', 'wp_template', template);

  if (template.content) {
    registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', 'wp_template', newTemplate.id, {
      blocks: (0,external_wp_blocks_namespaceObject.parse)(template.content)
    }, {
      undoIgnore: true
    });
  }

  dispatch({
    type: 'SET_EDITED_POST',
    postType: 'wp_template',
    id: newTemplate.id,
    context: {
      templateSlug: newTemplate.slug
    }
  });
};
/**
 * Action that removes a template.
 *
 * @param {Object} template The template object.
 */

const removeTemplate = template => async _ref4 => {
  let {
    registry
  } = _ref4;

  try {
    await registry.dispatch(external_wp_coreData_namespaceObject.store).deleteEntityRecord('postType', template.type, template.id, {
      force: true
    });
    const lastError = registry.select(external_wp_coreData_namespaceObject.store).getLastEntityDeleteError('postType', template.type, template.id);

    if (lastError) {
      throw lastError;
    }

    registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: The template/part's name. */
    (0,external_wp_i18n_namespaceObject.__)('"%s" deleted.'), template.title.rendered), {
      type: 'snackbar'
    });
  } catch (error) {
    const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while deleting the template.');
    registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(errorMessage, {
      type: 'snackbar'
    });
  }
};
/**
 * Action that sets a template part.
 *
 * @param {string} templatePartId The template part ID.
 *
 * @return {Object} Action object.
 */

function setTemplatePart(templatePartId) {
  return {
    type: 'SET_EDITED_POST',
    postType: 'wp_template_part',
    id: templatePartId
  };
}
/**
 * @deprecated
 */

function setHomeTemplateId() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).setHomeTemplateId", {
    since: '6.2',
    version: '6.4'
  });
  return {
    type: 'NOTHING'
  };
}
/**
 * Set's the current block editor context.
 *
 * @param {Object} context The context object.
 *
 * @return {number} The resolved template ID for the page route.
 */

function setEditedPostContext(context) {
  return {
    type: 'SET_EDITED_POST_CONTEXT',
    context
  };
}
/**
 * Resolves the template for a page and displays both. If no path is given, attempts
 * to use the postId to generate a path like `?p=${ postId }`.
 *
 * @param {Object} page         The page object.
 * @param {string} page.type    The page type.
 * @param {string} page.slug    The page slug.
 * @param {string} page.path    The page path.
 * @param {Object} page.context The page context.
 *
 * @return {number} The resolved template ID for the page route.
 */

const setPage = page => async _ref5 => {
  var _page$context;

  let {
    dispatch,
    registry
  } = _ref5;

  if (!page.path && (_page$context = page.context) !== null && _page$context !== void 0 && _page$context.postId) {
    const entity = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).getEntityRecord('postType', page.context.postType || 'post', page.context.postId); // If the entity is undefined for some reason, path will resolve to "/"

    page.path = (0,external_wp_url_namespaceObject.getPathAndQueryString)(entity === null || entity === void 0 ? void 0 : entity.link);
  }

  const template = await registry.resolveSelect(external_wp_coreData_namespaceObject.store).__experimentalGetTemplateForLink(page.path);

  if (!template) {
    return;
  }

  dispatch({
    type: 'SET_EDITED_POST',
    postType: 'wp_template',
    id: template.id,
    context: { ...page.context,
      templateSlug: template.slug
    }
  });
  return template.id;
};
/**
 * Action that sets the active navigation panel menu.
 *
 * @deprecated
 *
 * @return {Object} Action object.
 */

function setNavigationPanelActiveMenu() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).setNavigationPanelActiveMenu", {
    since: '6.2',
    version: '6.4'
  });
  return {
    type: 'NOTHING'
  };
}
/**
 * Opens the navigation panel and sets its active menu at the same time.
 *
 * @deprecated
 */

function openNavigationPanelToMenu() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).openNavigationPanelToMenu", {
    since: '6.2',
    version: '6.4'
  });
  return {
    type: 'NOTHING'
  };
}
/**
 * Sets whether the navigation panel should be open.
 *
 * @deprecated
 */

function setIsNavigationPanelOpened() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).setIsNavigationPanelOpened", {
    since: '6.2',
    version: '6.4'
  });
  return {
    type: 'NOTHING'
  };
}
/**
 * Opens or closes the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @return {Object} Action object.
 */

function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}
/**
 * Returns an action object used to update the settings.
 *
 * @param {Object} settings New settings.
 *
 * @return {Object} Action object.
 */

function updateSettings(settings) {
  return {
    type: 'UPDATE_SETTINGS',
    settings
  };
}
/**
 * Sets whether the list view panel should be open.
 *
 * @param {boolean} isOpen If true, opens the list view. If false, closes it.
 *                         It does not toggle the state, but sets it directly.
 */

function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Sets whether the save view panel should be open.
 *
 * @param {boolean} isOpen If true, opens the save view. If false, closes it.
 *                         It does not toggle the state, but sets it directly.
 */

function setIsSaveViewOpened(isOpen) {
  return {
    type: 'SET_IS_SAVE_VIEW_OPENED',
    isOpen
  };
}
/**
 * Reverts a template to its original theme-provided file.
 *
 * @param {Object}  template            The template to revert.
 * @param {Object}  [options]
 * @param {boolean} [options.allowUndo] Whether to allow the user to undo
 *                                      reverting the template. Default true.
 */

const revertTemplate = function (template) {
  let {
    allowUndo = true
  } = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  return async _ref6 => {
    let {
      registry
    } = _ref6;

    if (!isTemplateRevertable(template)) {
      registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice((0,external_wp_i18n_namespaceObject.__)('This template is not revertable.'), {
        type: 'snackbar'
      });
      return;
    }

    try {
      var _fileTemplate$content;

      const templateEntityConfig = registry.select(external_wp_coreData_namespaceObject.store).getEntityConfig('postType', template.type);

      if (!templateEntityConfig) {
        registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice((0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error. Please reload.'), {
          type: 'snackbar'
        });
        return;
      }

      const fileTemplatePath = (0,external_wp_url_namespaceObject.addQueryArgs)(`${templateEntityConfig.baseURL}/${template.id}`, {
        context: 'edit',
        source: 'theme'
      });
      const fileTemplate = await external_wp_apiFetch_default()({
        path: fileTemplatePath
      });

      if (!fileTemplate) {
        registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice((0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error. Please reload.'), {
          type: 'snackbar'
        });
        return;
      }

      const serializeBlocks = _ref7 => {
        let {
          blocks: blocksForSerialization = []
        } = _ref7;
        return (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(blocksForSerialization);
      };

      const edited = registry.select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', template.type, template.id); // We are fixing up the undo level here to make sure we can undo
      // the revert in the header toolbar correctly.

      registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', template.type, template.id, {
        content: serializeBlocks,
        // Required to make the `undo` behave correctly.
        blocks: edited.blocks,
        // Required to revert the blocks in the editor.
        source: 'custom' // required to avoid turning the editor into a dirty state

      }, {
        undoIgnore: true // Required to merge this edit with the last undo level.

      });
      const blocks = (0,external_wp_blocks_namespaceObject.parse)(fileTemplate === null || fileTemplate === void 0 ? void 0 : (_fileTemplate$content = fileTemplate.content) === null || _fileTemplate$content === void 0 ? void 0 : _fileTemplate$content.raw);
      registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', template.type, fileTemplate.id, {
        content: serializeBlocks,
        blocks,
        source: 'theme'
      });

      if (allowUndo) {
        const undoRevert = () => {
          registry.dispatch(external_wp_coreData_namespaceObject.store).editEntityRecord('postType', template.type, edited.id, {
            content: serializeBlocks,
            blocks: edited.blocks,
            source: 'custom'
          });
        };

        registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Template reverted.'), {
          type: 'snackbar',
          actions: [{
            label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
            onClick: undoRevert
          }]
        });
      } else {
        registry.dispatch(external_wp_notices_namespaceObject.store).createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Template reverted.'));
      }
    } catch (error) {
      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('Template revert failed. Please reload.');
      registry.dispatch(external_wp_notices_namespaceObject.store).createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
    }
  };
};
/**
 * Action that opens an editor sidebar.
 *
 * @param {?string} name Sidebar name to be opened.
 */

const openGeneralSidebar = name => _ref8 => {
  let {
    registry
  } = _ref8;
  registry.dispatch(store).enableComplementaryArea(constants_STORE_NAME, name);
};
/**
 * Action that closes the sidebar.
 */

const closeGeneralSidebar = () => _ref9 => {
  let {
    registry
  } = _ref9;
  registry.dispatch(store).disableComplementaryArea(constants_STORE_NAME);
};
const switchEditorMode = mode => _ref10 => {
  let {
    registry
  } = _ref10;
  registry.dispatch('core/preferences').set('core/edit-site', 'editorMode', mode); // Unselect blocks when we switch to a non visual mode.

  if (mode !== 'visual') {
    registry.dispatch(external_wp_blockEditor_namespaceObject.store).clearSelectedBlock();
  }

  if (mode === 'visual') {
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.__)('Visual editor selected'), 'assertive');
  } else if (mode === 'mosaic') {
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.__)('Mosaic view selected'), 'assertive');
  }
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/private-actions.js
/**
 * WordPress dependencies
 */


/**
 * Action that switches the canvas mode.
 *
 * @param {?string} mode Canvas mode.
 */

const setCanvasMode = mode => _ref => {
  let {
    registry,
    dispatch
  } = _ref;

  registry.dispatch(external_wp_blockEditor_namespaceObject.store).__unstableSetEditorMode('edit');

  dispatch({
    type: 'SET_CANVAS_MODE',
    mode
  }); // Check if the block list view should be open by default.

  if (mode === 'edit' && registry.select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'showListViewByDefault')) {
    dispatch.setIsListViewOpened(true);
  }
};

;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__(9756);
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/utils.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


const EMPTY_ARRAY = [];
/**
 * Get a flattened and filtered list of template parts and the matching block for that template part.
 *
 * Takes a list of blocks defined within a template, and a list of template parts, and returns a
 * flattened list of template parts and the matching block for that template part.
 *
 * @param {Array}  blocks        Blocks to flatten.
 * @param {?Array} templateParts Available template parts.
 * @return {Array} An array of template parts and their blocks.
 */

function getFilteredTemplatePartBlocks() {
  let blocks = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : EMPTY_ARRAY;
  let templateParts = arguments.length > 1 ? arguments[1] : undefined;
  const templatePartsById = templateParts ? // Key template parts by their ID.
  templateParts.reduce((newTemplateParts, part) => ({ ...newTemplateParts,
    [part.id]: part
  }), {}) : {};
  const result = []; // Iterate over all blocks, recursing into inner blocks.
  // Output will be based on a depth-first traversal.

  const stack = [...blocks];

  while (stack.length) {
    const {
      innerBlocks,
      ...block
    } = stack.shift(); // Place inner blocks at the beginning of the stack to preserve order.

    stack.unshift(...innerBlocks);

    if ((0,external_wp_blocks_namespaceObject.isTemplatePart)(block)) {
      const {
        attributes: {
          theme,
          slug
        }
      } = block;
      const templatePartId = `${theme}//${slug}`;
      const templatePart = templatePartsById[templatePartId]; // Only add to output if the found template part block is in the list of available template parts.

      if (templatePart) {
        result.push({
          templatePart,
          block
        });
      }
    }
  }

  return result;
}

const memoizedGetFilteredTemplatePartBlocks = memize_default()(getFilteredTemplatePartBlocks);


;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


/**
 * @typedef {'template'|'template_type'} TemplateType Template type.
 */

/**
 * Helper for getting a preference from the preferences store.
 *
 * This is only present so that `getSettings` doesn't need to be made a
 * registry selector.
 *
 * It's unstable because the selector needs to be exported and so part of the
 * public API to work.
 */

const __unstableGetPreference = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => (state, name) => select(external_wp_preferences_namespaceObject.store).get('core/edit-site', name));
/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state       Global application state.
 * @param {string} featureName Feature slug.
 *
 * @return {boolean} Is active.
 */

function selectors_isFeatureActive(state, featureName) {
  external_wp_deprecated_default()(`select( 'core/interface' ).isFeatureActive`, {
    since: '6.0',
    alternative: `select( 'core/preferences' ).get`
  });
  return !!__unstableGetPreference(state, featureName);
}
/**
 * Returns the current editing canvas device type.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Device type.
 */

function __experimentalGetPreviewDeviceType(state) {
  return state.deviceType;
}
/**
 * Returns whether the current user can create media or not.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Whether the current user can create media or not.
 */

const getCanUserCreateMedia = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => select(external_wp_coreData_namespaceObject.store).canUser('create', 'media'));
/**
 * Returns any available Reusable blocks.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} The available reusable blocks.
 */

const getReusableBlocks = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => () => {
  const isWeb = external_wp_element_namespaceObject.Platform.OS === 'web';
  return isWeb ? select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_block', {
    per_page: -1
  }) : [];
});
/**
 * Returns the settings, taking into account active features and permissions.
 *
 * @param {Object}   state             Global application state.
 * @param {Function} setIsInserterOpen Setter for the open state of the global inserter.
 *
 * @return {Object} Settings.
 */

const getSettings = rememo((state, setIsInserterOpen) => {
  const settings = { ...state.settings,
    outlineMode: true,
    focusMode: !!__unstableGetPreference(state, 'focusMode'),
    hasFixedToolbar: !!__unstableGetPreference(state, 'fixedToolbar'),
    keepCaretInsideBlock: !!__unstableGetPreference(state, 'keepCaretInsideBlock'),
    showIconLabels: !!__unstableGetPreference(state, 'showIconLabels'),
    __experimentalSetIsInserterOpened: setIsInserterOpen,
    __experimentalReusableBlocks: getReusableBlocks(state),
    __experimentalPreferPatternsOnRoot: 'wp_template' === getEditedPostType(state)
  };
  const canUserCreateMedia = getCanUserCreateMedia(state);

  if (!canUserCreateMedia) {
    return settings;
  }

  settings.mediaUpload = _ref => {
    let {
      onError,
      ...rest
    } = _ref;
    (0,external_wp_mediaUtils_namespaceObject.uploadMedia)({
      wpAllowedMimeTypes: state.settings.allowedMimeTypes,
      onError: _ref2 => {
        let {
          message
        } = _ref2;
        return onError(message);
      },
      ...rest
    });
  };

  return settings;
}, state => [getCanUserCreateMedia(state), state.settings, __unstableGetPreference(state, 'focusMode'), __unstableGetPreference(state, 'fixedToolbar'), __unstableGetPreference(state, 'keepCaretInsideBlock'), __unstableGetPreference(state, 'showIconLabels'), getReusableBlocks(state), getEditedPostType(state)]);
/**
 * @deprecated
 */

function getHomeTemplateId() {
  external_wp_deprecated_default()("select( 'core/edit-site' ).getHomeTemplateId", {
    since: '6.2',
    version: '6.4'
  });
}
/**
 * Returns the current edited post type (wp_template or wp_template_part).
 *
 * @param {Object} state Global application state.
 *
 * @return {TemplateType?} Template type.
 */

function getEditedPostType(state) {
  return state.editedPost.postType;
}
/**
 * Returns the ID of the currently edited template or template part.
 *
 * @param {Object} state Global application state.
 *
 * @return {string?} Post ID.
 */

function getEditedPostId(state) {
  return state.editedPost.id;
}
/**
 * Returns the edited post's context object.
 *
 * @deprecated
 * @param {Object} state Global application state.
 *
 * @return {Object} Page.
 */

function getEditedPostContext(state) {
  return state.editedPost.context;
}
/**
 * Returns the current page object.
 *
 * @deprecated
 * @param {Object} state Global application state.
 *
 * @return {Object} Page.
 */

function getPage(state) {
  return {
    context: state.editedPost.context
  };
}
/**
 * Returns the current opened/closed state of the inserter panel.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} True if the inserter panel should be open; false if closed.
 */

function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}
/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID, index to insert at and starting filter value.
 */

function __experimentalGetInsertionPoint(state) {
  const {
    rootClientId,
    insertionIndex,
    filterValue
  } = state.blockInserterPanel;
  return {
    rootClientId,
    insertionIndex,
    filterValue
  };
}
/**
 * Returns the current opened/closed state of the list view panel.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} True if the list view panel should be open; false if closed.
 */

function isListViewOpened(state) {
  return state.listViewPanel;
}
/**
 * Returns the current opened/closed state of the save panel.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} True if the save panel should be open; false if closed.
 */

function isSaveViewOpened(state) {
  return state.saveViewPanel;
}
/**
 * Returns the template parts and their blocks for the current edited template.
 *
 * @param {Object} state Global application state.
 * @return {Array} Template parts and their blocks in an array.
 */

const getCurrentTemplateTemplateParts = (0,external_wp_data_namespaceObject.createRegistrySelector)(select => state => {
  const templateType = getEditedPostType(state);
  const templateId = getEditedPostId(state);
  const template = select(external_wp_coreData_namespaceObject.store).getEditedEntityRecord('postType', templateType, templateId);
  const templateParts = select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template_part', {
    per_page: -1
  });
  return memoizedGetFilteredTemplatePartBlocks(template.blocks, templateParts);
});
/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */

function getEditorMode(state) {
  return __unstableGetPreference(state, 'editorMode');
}
/**
 * @deprecated
 */

function getCurrentTemplateNavigationPanelSubMenu() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).getCurrentTemplateNavigationPanelSubMenu", {
    since: '6.2',
    version: '6.4'
  });
}
/**
 * @deprecated
 */

function getNavigationPanelActiveMenu() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).getNavigationPanelActiveMenu", {
    since: '6.2',
    version: '6.4'
  });
}
/**
 * @deprecated
 */

function isNavigationOpened() {
  external_wp_deprecated_default()("dispatch( 'core/edit-site' ).isNavigationOpened", {
    since: '6.2',
    version: '6.4'
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/private-selectors.js
/**
 * Returns the current canvas mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Canvas mode.
 */
function getCanvasMode(state) {
  return state.canvasMode;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */








const storeConfig = {
  reducer: store_reducer,
  actions: store_actions_namespaceObject,
  selectors: store_selectors_namespaceObject
};
const store_store = (0,external_wp_data_namespaceObject.createReduxStore)(constants_STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store_store);
unlock(store_store).registerPrivateSelectors(private_selectors_namespaceObject);
unlock(store_store).registerPrivateActions(private_actions_namespaceObject);

;// CONCATENATED MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/navigation.js


/**
 * WordPress dependencies
 */

const navigation = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4c-4.4 0-8 3.6-8 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 14.5c-3.6 0-6.5-2.9-6.5-6.5S8.4 5.5 12 5.5s6.5 2.9 6.5 6.5-2.9 6.5-6.5 6.5zM9 16l4.5-3L15 8.4l-4.5 3L9 16z"
}));
/* harmony default export */ var library_navigation = (navigation);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js


/**
 * WordPress dependencies
 */

const layout = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ var library_layout = (layout);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/symbol-filled.js


/**
 * WordPress dependencies
 */

const symbolFilled = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-17.6 1L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z"
}));
/* harmony default export */ var symbol_filled = (symbolFilled);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-button/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function SidebarButton(props) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, extends_extends({}, props, {
    className: classnames_default()('edit-site-sidebar-button', props.className)
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function SidebarNavigationScreen(_ref) {
  let {
    isRoot,
    title,
    actions,
    content,
    description
  } = _ref;
  const {
    dashboardLink
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings
    } = unlock(select(store_store));
    return {
      dashboardLink: getSettings().__experimentalDashboardLink
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 2
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    spacing: 4,
    justify: "flex-start",
    className: "edit-site-sidebar-navigation-screen__title-icon"
  }, !isRoot ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorToParentButton, {
    as: SidebarButton,
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Back')
  }) : (0,external_wp_element_namespaceObject.createElement)(SidebarButton, {
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Navigate to the Dashboard'),
    href: dashboardLink || 'index.php',
    label: (0,external_wp_i18n_namespaceObject.__)('Dashboard')
  }), (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-site-sidebar-navigation-screen__title"
  }, title), actions), (0,external_wp_element_namespaceObject.createElement)("nav", {
    className: "edit-site-sidebar-navigation-screen__content"
  }, description && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "edit-site-sidebar-navigation-screen__description"
  }, description), content));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-item/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function SidebarNavigationItem(_ref) {
  let {
    className,
    icon,
    withChevron = false,
    children,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItem, extends_extends({
    className: classnames_default()('edit-site-sidebar-navigation-item', className)
  }, props), icon && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
    style: {
      fill: 'currentcolor'
    },
    icon: icon,
    size: 24
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexBlock, null, children), withChevron && (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
    style: {
      fill: 'currentcolor'
    },
    icon: chevron_right,
    size: 24
  })), !icon && children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-main/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function SidebarNavigationScreenMain() {
  const {
    navigationMenus
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEntityRecords
    } = select(external_wp_coreData_namespaceObject.store);
    return {
      navigationMenus: getEntityRecords('postType', 'wp_navigation', {
        per_page: -1,
        status: 'publish'
      })
    };
  });
  const showNavigationScreen =  false ? 0 : false;
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    isRoot: true,
    title: (0,external_wp_i18n_namespaceObject.__)('Design'),
    description: (0,external_wp_i18n_namespaceObject.__)('Customize the appearance of your website using the block editor.'),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, showNavigationScreen && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
      as: SidebarNavigationItem,
      path: "/navigation",
      withChevron: true,
      icon: library_navigation
    }, (0,external_wp_i18n_namespaceObject.__)('Navigation')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
      as: SidebarNavigationItem,
      path: "/wp_template",
      withChevron: true,
      icon: library_layout
    }, (0,external_wp_i18n_namespaceObject.__)('Templates')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, {
      as: SidebarNavigationItem,
      path: "/wp_template_part",
      withChevron: true,
      icon: symbol_filled
    }, (0,external_wp_i18n_namespaceObject.__)('Template Parts')))
  });
}

;// CONCATENATED MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/home.js


/**
 * WordPress dependencies
 */

const home = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4L4 7.9V20h16V7.9L12 4zm6.5 14.5H14V13h-4v5.5H5.5V8.8L12 5.7l6.5 3.1v9.7z"
}));
/* harmony default export */ var library_home = (home);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post.js


/**
 * WordPress dependencies
 */

const post = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m7.3 9.7 1.4 1.4c.2-.2.3-.3.4-.5 0 0 0-.1.1-.1.3-.5.4-1.1.3-1.6L12 7 9 4 7.2 6.5c-.6-.1-1.1 0-1.6.3 0 0-.1 0-.1.1-.3.1-.4.2-.6.4l1.4 1.4L4 11v1h1l2.3-2.3zM4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4z"
}));
/* harmony default export */ var library_post = (post);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/page.js


/**
 * WordPress dependencies
 */

const page = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M7 5.5h10a.5.5 0 01.5.5v12a.5.5 0 01-.5.5H7a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM17 4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2zm-1 3.75H8v1.5h8v-1.5zM8 11h8v1.5H8V11zm6 3.25H8v1.5h6v-1.5z"
}));
/* harmony default export */ var library_page = (page);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/archive.js


/**
 * WordPress dependencies
 */

const archive = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19 6.2h-5.9l-.6-1.1c-.3-.7-1-1.1-1.8-1.1H5c-1.1 0-2 .9-2 2v11.8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8.2c0-1.1-.9-2-2-2zm.5 11.6c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h5.8c.2 0 .4.1.4.3l1 2H19c.3 0 .5.2.5.5v9.5zM8 12.8h8v-1.5H8v1.5zm0 3h8v-1.5H8v1.5z"
}));
/* harmony default export */ var library_archive = (archive);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/search.js


/**
 * WordPress dependencies
 */

const search = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13.5 6C10.5 6 8 8.5 8 11.5c0 1.1.3 2.1.9 3l-3.4 3 1 1.1 3.4-2.9c1 .9 2.2 1.4 3.6 1.4 3 0 5.5-2.5 5.5-5.5C19 8.5 16.5 6 13.5 6zm0 9.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z"
}));
/* harmony default export */ var library_search = (search);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/not-found.js


/**
 * WordPress dependencies
 */

const notFound = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19 5H5c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm.5 12c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5V7c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v10zm-11-7.6h-.7l-3.1 4.3h2.8V15h1v-1.3h.7v-.8h-.7V9.4zm-.9 3.5H6.3l1.2-1.7v1.7zm5.6-3.2c-.4-.2-.8-.4-1.2-.4-.5 0-.9.1-1.2.4-.4.2-.6.6-.8 1-.2.4-.3.9-.3 1.5s.1 1.1.3 1.6c.2.4.5.8.8 1 .4.2.8.4 1.2.4.5 0 .9-.1 1.2-.4.4-.2.6-.6.8-1 .2-.4.3-1 .3-1.6 0-.6-.1-1.1-.3-1.5-.1-.5-.4-.8-.8-1zm0 3.6c-.1.3-.3.5-.5.7-.2.1-.4.2-.7.2-.3 0-.5-.1-.7-.2-.2-.1-.4-.4-.5-.7-.1-.3-.2-.7-.2-1.2 0-.7.1-1.2.4-1.5.3-.3.6-.5 1-.5s.7.2 1 .5c.3.3.4.8.4 1.5-.1.5-.1.9-.2 1.2zm5-3.9h-.7l-3.1 4.3h2.8V15h1v-1.3h.7v-.8h-.7V9.4zm-1 3.5H16l1.2-1.7v1.7z"
}));
/* harmony default export */ var not_found = (notFound);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list.js


/**
 * WordPress dependencies
 */

const list = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M4 4v1.5h16V4H4zm8 8.5h8V11h-8v1.5zM4 20h16v-1.5H4V20zm4-8c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2z"
}));
/* harmony default export */ var library_list = (list);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/category.js


/**
 * WordPress dependencies
 */

const category = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M6 5.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5H6a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM4 6a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm11-.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5h-3a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5zM13 6a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2h-3a2 2 0 01-2-2V6zm5 8.5h-3a.5.5 0 00-.5.5v3a.5.5 0 00.5.5h3a.5.5 0 00.5-.5v-3a.5.5 0 00-.5-.5zM15 13a2 2 0 00-2 2v3a2 2 0 002 2h3a2 2 0 002-2v-3a2 2 0 00-2-2h-3zm-9 1.5h3a.5.5 0 01.5.5v3a.5.5 0 01-.5.5H6a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zM4 15a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H6a2 2 0 01-2-2v-3z",
  fillRule: "evenodd",
  clipRule: "evenodd"
}));
/* harmony default export */ var library_category = (category);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post-author.js


/**
 * WordPress dependencies
 */

const postAuthor = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10 4.5a1 1 0 11-2 0 1 1 0 012 0zm1.5 0a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm2.25 7.5v-1A2.75 2.75 0 0011 8.25H7A2.75 2.75 0 004.25 11v1h1.5v-1c0-.69.56-1.25 1.25-1.25h4c.69 0 1.25.56 1.25 1.25v1h1.5zM4 20h9v-1.5H4V20zm16-4H4v-1.5h16V16z",
  fillRule: "evenodd",
  clipRule: "evenodd"
}));
/* harmony default export */ var post_author = (postAuthor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/block-meta.js


/**
 * WordPress dependencies
 */

const blockMeta = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M8.95 11.25H4v1.5h4.95v4.5H13V18c0 1.1.9 2 2 2h3c1.1 0 2-.9 2-2v-3c0-1.1-.9-2-2-2h-3c-1.1 0-2 .9-2 2v.75h-2.55v-7.5H13V9c0 1.1.9 2 2 2h3c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3c-1.1 0-2 .9-2 2v.75H8.95v4.5ZM14.5 15v3c0 .3.2.5.5.5h3c.3 0 .5-.2.5-.5v-3c0-.3-.2-.5-.5-.5h-3c-.3 0-.5.2-.5.5Zm0-6V6c0-.3.2-.5.5-.5h3c.3 0 .5.2.5.5v3c0 .3-.2.5-.5.5h-3c-.3 0-.5-.2-.5-.5Z",
  clipRule: "evenodd"
}));
/* harmony default export */ var block_meta = (blockMeta);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/post-date.js


/**
 * WordPress dependencies
 */

const postDate = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M11.696 13.972c.356-.546.599-.958.728-1.235a1.79 1.79 0 00.203-.783c0-.264-.077-.47-.23-.618-.148-.153-.354-.23-.618-.23-.295 0-.569.07-.82.212a3.413 3.413 0 00-.738.571l-.147-1.188c.289-.234.59-.41.903-.526.313-.117.66-.175 1.041-.175.375 0 .695.08.959.24.264.153.46.362.59.626.135.265.203.556.203.876 0 .362-.08.734-.24 1.115-.154.381-.427.87-.82 1.466l-.756 1.152H14v1.106h-4l1.696-2.609z"
}), (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M19.5 7h-15v12a.5.5 0 00.5.5h14a.5.5 0 00.5-.5V7zM3 7V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"
}));
/* harmony default export */ var post_date = (postDate);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/tag.js


/**
 * WordPress dependencies
 */

const tag = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.1 11.2l-6.7-6.7c-.1-.1-.3-.2-.5-.2H5c-.4-.1-.8.3-.8.7v7.8c0 .2.1.4.2.5l6.7 6.7c.2.2.5.4.7.5s.6.2.9.2c.3 0 .6-.1.9-.2.3-.1.5-.3.8-.5l5.6-5.6c.4-.4.7-1 .7-1.6.1-.6-.2-1.2-.6-1.6zM19 13.4L13.4 19c-.1.1-.2.1-.3.2-.2.1-.4.1-.6 0-.1 0-.2-.1-.3-.2l-6.5-6.5V5.8h6.8l6.5 6.5c.2.2.2.4.2.6 0 .1 0 .3-.2.5zM9 8c-.6 0-1 .4-1 1s.4 1 1 1 1-.4 1-1-.4-1-1-1z"
}));
/* harmony default export */ var library_tag = (tag);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/media.js


/**
 * WordPress dependencies
 */

const media = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.7 3H5.3C4 3 3 4 3 5.3v13.4C3 20 4 21 5.3 21h13.4c1.3 0 2.3-1 2.3-2.3V5.3C21 4 20 3 18.7 3zm.8 15.7c0 .4-.4.8-.8.8H5.3c-.4 0-.8-.4-.8-.8V5.3c0-.4.4-.8.8-.8h13.4c.4 0 .8.4.8.8v13.4zM10 15l5-3-5-3v6z"
}));
/* harmony default export */ var library_media = (media);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js


/**
 * WordPress dependencies
 */

const plus = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ var library_plus = (plus);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/template-actions-loading-screen.js


/**
 * WordPress dependencies
 */

function TemplateActionsLoadingScreen() {
  const baseCssClass = 'edit-site-template-actions-loading-screen-modal';
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    isFullScreen: true,
    isDismissible: false,
    shouldCloseOnClickOutside: false,
    shouldCloseOnEsc: false,
    onRequestClose: () => {},
    __experimentalHideHeader: true,
    className: baseCssClass
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: `${baseCssClass}__content`
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null)));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/utils.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * @typedef IHasNameAndId
 * @property {string|number} id   The entity's id.
 * @property {string}        name The entity's name.
 */

/**
 * Helper util to map records to add a `name` prop from a
 * provided path, in order to handle all entities in the same
 * fashion(implementing`IHasNameAndId` interface).
 *
 * @param {Object[]} entities The array of entities.
 * @param {string}   path     The path to map a `name` property from the entity.
 * @return {IHasNameAndId[]} An array of enitities that now implement the `IHasNameAndId` interface.
 */

const mapToIHasNameAndId = (entities, path) => {
  return (entities || []).map(entity => ({ ...entity,
    name: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)((0,external_lodash_namespaceObject.get)(entity, path))
  }));
};
/**
 * @typedef {Object} EntitiesInfo
 * @property {boolean}  hasEntities         If an entity has available records(posts, terms, etc..).
 * @property {number[]} existingEntitiesIds An array of the existing entities ids.
 */

const useExistingTemplates = () => {
  return (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template', {
    per_page: -1
  }), []);
};
const useDefaultTemplateTypes = () => {
  return (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).__experimentalGetDefaultTemplateTypes(), []);
};

const usePublicPostTypes = () => {
  const postTypes = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getPostTypes({
    per_page: -1
  }), []);
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const excludedPostTypes = ['attachment'];
    return postTypes === null || postTypes === void 0 ? void 0 : postTypes.filter(_ref => {
      let {
        viewable,
        slug
      } = _ref;
      return viewable && !excludedPostTypes.includes(slug);
    });
  }, [postTypes]);
};

const usePublicTaxonomies = () => {
  const taxonomies = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getTaxonomies({
    per_page: -1
  }), []);
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    return taxonomies === null || taxonomies === void 0 ? void 0 : taxonomies.filter(_ref2 => {
      let {
        visibility
      } = _ref2;
      return visibility === null || visibility === void 0 ? void 0 : visibility.publicly_queryable;
    });
  }, [taxonomies]);
};

function usePostTypeNeedsUniqueIdentifier(publicPostTypes) {
  const postTypeLabels = (0,external_wp_element_namespaceObject.useMemo)(() => publicPostTypes === null || publicPostTypes === void 0 ? void 0 : publicPostTypes.reduce((accumulator, _ref3) => {
    let {
      labels
    } = _ref3;
    const singularName = labels.singular_name.toLowerCase();
    accumulator[singularName] = (accumulator[singularName] || 0) + 1;
    return accumulator;
  }, {}));
  return (0,external_wp_element_namespaceObject.useCallback)(_ref4 => {
    let {
      labels,
      slug
    } = _ref4;
    const singularName = labels.singular_name.toLowerCase();
    return postTypeLabels[singularName] > 1 && singularName !== slug;
  }, [postTypeLabels]);
}

function usePostTypeArchiveMenuItems() {
  const publicPostTypes = usePublicPostTypes();
  const postTypesWithArchives = (0,external_wp_element_namespaceObject.useMemo)(() => publicPostTypes === null || publicPostTypes === void 0 ? void 0 : publicPostTypes.filter(postType => postType.has_archive), [publicPostTypes]);
  const existingTemplates = useExistingTemplates();
  const needsUniqueIdentifier = usePostTypeNeedsUniqueIdentifier(postTypesWithArchives);
  return (0,external_wp_element_namespaceObject.useMemo)(() => (postTypesWithArchives === null || postTypesWithArchives === void 0 ? void 0 : postTypesWithArchives.filter(postType => !(existingTemplates || []).some(existingTemplate => existingTemplate.slug === 'archive-' + postType.slug)).map(postType => {
    var _postType$icon;

    let title;

    if (needsUniqueIdentifier(postType)) {
      title = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Name of the post type e.g: "Post"; %2s: Slug of the post type e.g: "book".
      (0,external_wp_i18n_namespaceObject.__)('Archive: %1$s (%2$s)'), postType.labels.singular_name, postType.slug);
    } else {
      title = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the post type e.g: "Post".
      (0,external_wp_i18n_namespaceObject.__)('Archive: %s'), postType.labels.singular_name);
    }

    return {
      slug: 'archive-' + postType.slug,
      description: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the post type e.g: "Post".
      (0,external_wp_i18n_namespaceObject.__)('Displays an archive with the latests posts of type: %s.'), postType.labels.singular_name),
      title,
      // `icon` is the `menu_icon` property of a post type. We
      // only handle `dashicons` for now, even if the `menu_icon`
      // also supports urls and svg as values.
      icon: (_postType$icon = postType.icon) !== null && _postType$icon !== void 0 && _postType$icon.startsWith('dashicons-') ? postType.icon.slice(10) : library_archive,
      templatePrefix: 'archive'
    };
  })) || [], [postTypesWithArchives, existingTemplates, needsUniqueIdentifier]);
}
const usePostTypeMenuItems = onClickMenuItem => {
  const publicPostTypes = usePublicPostTypes();
  const existingTemplates = useExistingTemplates();
  const defaultTemplateTypes = useDefaultTemplateTypes();
  const needsUniqueIdentifier = usePostTypeNeedsUniqueIdentifier(publicPostTypes); // `page`is a special case in template hierarchy.

  const templatePrefixes = (0,external_wp_element_namespaceObject.useMemo)(() => publicPostTypes === null || publicPostTypes === void 0 ? void 0 : publicPostTypes.reduce((accumulator, _ref5) => {
    let {
      slug
    } = _ref5;
    let suffix = slug;

    if (slug !== 'page') {
      suffix = `single-${suffix}`;
    }

    accumulator[slug] = suffix;
    return accumulator;
  }, {}), [publicPostTypes]);
  const postTypesInfo = useEntitiesInfo('postType', templatePrefixes);
  const existingTemplateSlugs = (existingTemplates || []).map(_ref6 => {
    let {
      slug
    } = _ref6;
    return slug;
  });
  const menuItems = (publicPostTypes || []).reduce((accumulator, postType) => {
    var _postTypesInfo$slug;

    const {
      slug,
      labels,
      icon
    } = postType; // We need to check if the general template is part of the
    // defaultTemplateTypes. If it is, just use that info and
    // augment it with the specific template functionality.

    const generalTemplateSlug = templatePrefixes[slug];
    const defaultTemplateType = defaultTemplateTypes === null || defaultTemplateTypes === void 0 ? void 0 : defaultTemplateTypes.find(_ref7 => {
      let {
        slug: _slug
      } = _ref7;
      return _slug === generalTemplateSlug;
    });
    const hasGeneralTemplate = existingTemplateSlugs === null || existingTemplateSlugs === void 0 ? void 0 : existingTemplateSlugs.includes(generalTemplateSlug);

    const _needsUniqueIdentifier = needsUniqueIdentifier(postType);

    let menuItemTitle = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the post type e.g: "Post".
    (0,external_wp_i18n_namespaceObject.__)('Single item: %s'), labels.singular_name);

    if (_needsUniqueIdentifier) {
      menuItemTitle = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Name of the post type e.g: "Post"; %2s: Slug of the post type e.g: "book".
      (0,external_wp_i18n_namespaceObject.__)('Single item: %1$s (%2$s)'), labels.singular_name, slug);
    }

    const menuItem = defaultTemplateType ? { ...defaultTemplateType,
      templatePrefix: templatePrefixes[slug]
    } : {
      slug: generalTemplateSlug,
      title: menuItemTitle,
      description: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the post type e.g: "Post".
      (0,external_wp_i18n_namespaceObject.__)('Displays a single item: %s.'), labels.singular_name),
      // `icon` is the `menu_icon` property of a post type. We
      // only handle `dashicons` for now, even if the `menu_icon`
      // also supports urls and svg as values.
      icon: icon !== null && icon !== void 0 && icon.startsWith('dashicons-') ? icon.slice(10) : library_post,
      templatePrefix: templatePrefixes[slug]
    };
    const hasEntities = postTypesInfo === null || postTypesInfo === void 0 ? void 0 : (_postTypesInfo$slug = postTypesInfo[slug]) === null || _postTypesInfo$slug === void 0 ? void 0 : _postTypesInfo$slug.hasEntities; // We have a different template creation flow only if they have entities.

    if (hasEntities) {
      menuItem.onClick = template => {
        onClickMenuItem({
          type: 'postType',
          slug,
          config: {
            recordNamePath: 'title.rendered',
            queryArgs: _ref8 => {
              let {
                search
              } = _ref8;
              return {
                _fields: 'id,title,slug,link',
                orderBy: search ? 'relevance' : 'modified',
                exclude: postTypesInfo[slug].existingEntitiesIds
              };
            },
            getSpecificTemplate: suggestion => {
              const templateSlug = `${templatePrefixes[slug]}-${suggestion.slug}`;
              return {
                title: templateSlug,
                slug: templateSlug,
                templatePrefix: templatePrefixes[slug]
              };
            }
          },
          labels,
          hasGeneralTemplate,
          template
        });
      };
    } // We don't need to add the menu item if there are no
    // entities and the general template exists.


    if (!hasGeneralTemplate || hasEntities) {
      accumulator.push(menuItem);
    }

    return accumulator;
  }, []); // Split menu items into two groups: one for the default post types
  // and one for the rest.

  const postTypesMenuItems = (0,external_wp_element_namespaceObject.useMemo)(() => menuItems.reduce((accumulator, postType) => {
    const {
      slug
    } = postType;
    let key = 'postTypesMenuItems';

    if (slug === 'page') {
      key = 'defaultPostTypesMenuItems';
    }

    accumulator[key].push(postType);
    return accumulator;
  }, {
    defaultPostTypesMenuItems: [],
    postTypesMenuItems: []
  }), [menuItems]);
  return postTypesMenuItems;
};
const useTaxonomiesMenuItems = onClickMenuItem => {
  const publicTaxonomies = usePublicTaxonomies();
  const existingTemplates = useExistingTemplates();
  const defaultTemplateTypes = useDefaultTemplateTypes(); // `category` and `post_tag` are special cases in template hierarchy.

  const templatePrefixes = (0,external_wp_element_namespaceObject.useMemo)(() => publicTaxonomies === null || publicTaxonomies === void 0 ? void 0 : publicTaxonomies.reduce((accumulator, _ref9) => {
    let {
      slug
    } = _ref9;
    let suffix = slug;

    if (!['category', 'post_tag'].includes(slug)) {
      suffix = `taxonomy-${suffix}`;
    }

    if (slug === 'post_tag') {
      suffix = `tag`;
    }

    accumulator[slug] = suffix;
    return accumulator;
  }, {}), [publicTaxonomies]); // We need to keep track of naming conflicts. If a conflict
  // occurs, we need to add slug.

  const taxonomyLabels = publicTaxonomies === null || publicTaxonomies === void 0 ? void 0 : publicTaxonomies.reduce((accumulator, _ref10) => {
    let {
      labels
    } = _ref10;
    const singularName = labels.singular_name.toLowerCase();
    accumulator[singularName] = (accumulator[singularName] || 0) + 1;
    return accumulator;
  }, {});

  const needsUniqueIdentifier = (labels, slug) => {
    if (['category', 'post_tag'].includes(slug)) {
      return false;
    }

    const singularName = labels.singular_name.toLowerCase();
    return taxonomyLabels[singularName] > 1 && singularName !== slug;
  };

  const taxonomiesInfo = useEntitiesInfo('taxonomy', templatePrefixes);
  const existingTemplateSlugs = (existingTemplates || []).map(_ref11 => {
    let {
      slug
    } = _ref11;
    return slug;
  });
  const menuItems = (publicTaxonomies || []).reduce((accumulator, taxonomy) => {
    var _taxonomiesInfo$slug;

    const {
      slug,
      labels
    } = taxonomy; // We need to check if the general template is part of the
    // defaultTemplateTypes. If it is, just use that info and
    // augment it with the specific template functionality.

    const generalTemplateSlug = templatePrefixes[slug];
    const defaultTemplateType = defaultTemplateTypes === null || defaultTemplateTypes === void 0 ? void 0 : defaultTemplateTypes.find(_ref12 => {
      let {
        slug: _slug
      } = _ref12;
      return _slug === generalTemplateSlug;
    });
    const hasGeneralTemplate = existingTemplateSlugs === null || existingTemplateSlugs === void 0 ? void 0 : existingTemplateSlugs.includes(generalTemplateSlug);

    const _needsUniqueIdentifier = needsUniqueIdentifier(labels, slug);

    let menuItemTitle = labels.singular_name;

    if (_needsUniqueIdentifier) {
      menuItemTitle = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Name of the taxonomy e.g: "Category"; %2s: Slug of the taxonomy e.g: "product_cat".
      (0,external_wp_i18n_namespaceObject.__)('%1$s (%2$s)'), labels.singular_name, slug);
    }

    const menuItem = defaultTemplateType ? { ...defaultTemplateType,
      templatePrefix: templatePrefixes[slug]
    } : {
      slug: generalTemplateSlug,
      title: menuItemTitle,
      description: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the taxonomy e.g: "Product Categories".
      (0,external_wp_i18n_namespaceObject.__)('Displays taxonomy: %s.'), labels.singular_name),
      icon: block_meta,
      templatePrefix: templatePrefixes[slug]
    };
    const hasEntities = taxonomiesInfo === null || taxonomiesInfo === void 0 ? void 0 : (_taxonomiesInfo$slug = taxonomiesInfo[slug]) === null || _taxonomiesInfo$slug === void 0 ? void 0 : _taxonomiesInfo$slug.hasEntities; // We have a different template creation flow only if they have entities.

    if (hasEntities) {
      menuItem.onClick = template => {
        onClickMenuItem({
          type: 'taxonomy',
          slug,
          config: {
            queryArgs: _ref13 => {
              let {
                search
              } = _ref13;
              return {
                _fields: 'id,name,slug,link',
                orderBy: search ? 'name' : 'count',
                exclude: taxonomiesInfo[slug].existingEntitiesIds
              };
            },
            getSpecificTemplate: suggestion => {
              const templateSlug = `${templatePrefixes[slug]}-${suggestion.slug}`;
              return {
                title: templateSlug,
                slug: templateSlug,
                templatePrefix: templatePrefixes[slug]
              };
            }
          },
          labels,
          hasGeneralTemplate,
          template
        });
      };
    } // We don't need to add the menu item if there are no
    // entities and the general template exists.


    if (!hasGeneralTemplate || hasEntities) {
      accumulator.push(menuItem);
    }

    return accumulator;
  }, []); // Split menu items into two groups: one for the default taxonomies
  // and one for the rest.

  const taxonomiesMenuItems = (0,external_wp_element_namespaceObject.useMemo)(() => menuItems.reduce((accumulator, taxonomy) => {
    const {
      slug
    } = taxonomy;
    let key = 'taxonomiesMenuItems';

    if (['category', 'tag'].includes(slug)) {
      key = 'defaultTaxonomiesMenuItems';
    }

    accumulator[key].push(taxonomy);
    return accumulator;
  }, {
    defaultTaxonomiesMenuItems: [],
    taxonomiesMenuItems: []
  }), [menuItems]);
  return taxonomiesMenuItems;
};
const USE_AUTHOR_MENU_ITEM_TEMPLATE_PREFIX = {
  user: 'author'
};
const USE_AUTHOR_MENU_ITEM_QUERY_PARAMETERS = {
  user: {
    who: 'authors'
  }
};
function useAuthorMenuItem(onClickMenuItem) {
  var _authorInfo$user, _authorInfo$user2;

  const existingTemplates = useExistingTemplates();
  const defaultTemplateTypes = useDefaultTemplateTypes();
  const authorInfo = useEntitiesInfo('root', USE_AUTHOR_MENU_ITEM_TEMPLATE_PREFIX, USE_AUTHOR_MENU_ITEM_QUERY_PARAMETERS);
  let authorMenuItem = defaultTemplateTypes === null || defaultTemplateTypes === void 0 ? void 0 : defaultTemplateTypes.find(_ref14 => {
    let {
      slug
    } = _ref14;
    return slug === 'author';
  });

  if (!authorMenuItem) {
    authorMenuItem = {
      description: (0,external_wp_i18n_namespaceObject.__)('Displays latest posts written by a single author.'),
      slug: 'author',
      title: 'Author'
    };
  }

  const hasGeneralTemplate = !!(existingTemplates !== null && existingTemplates !== void 0 && existingTemplates.find(_ref15 => {
    let {
      slug
    } = _ref15;
    return slug === 'author';
  }));

  if ((_authorInfo$user = authorInfo.user) !== null && _authorInfo$user !== void 0 && _authorInfo$user.hasEntities) {
    authorMenuItem = { ...authorMenuItem,
      templatePrefix: 'author'
    };

    authorMenuItem.onClick = template => {
      onClickMenuItem({
        type: 'root',
        slug: 'user',
        config: {
          queryArgs: _ref16 => {
            let {
              search
            } = _ref16;
            return {
              _fields: 'id,name,slug,link',
              orderBy: search ? 'name' : 'registered_date',
              exclude: authorInfo.user.existingEntitiesIds,
              who: 'authors'
            };
          },
          getSpecificTemplate: suggestion => {
            const templateSlug = `author-${suggestion.slug}`;
            return {
              title: templateSlug,
              slug: templateSlug,
              templatePrefix: 'author'
            };
          }
        },
        labels: {
          singular_name: (0,external_wp_i18n_namespaceObject.__)('Author'),
          search_items: (0,external_wp_i18n_namespaceObject.__)('Search Authors'),
          not_found: (0,external_wp_i18n_namespaceObject.__)('No authors found.'),
          all_items: (0,external_wp_i18n_namespaceObject.__)('All Authors')
        },
        hasGeneralTemplate,
        template
      });
    };
  }

  if (!hasGeneralTemplate || (_authorInfo$user2 = authorInfo.user) !== null && _authorInfo$user2 !== void 0 && _authorInfo$user2.hasEntities) {
    return authorMenuItem;
  }
}
/**
 * Helper hook that filters all the existing templates by the given
 * object with the entity's slug as key and the template prefix as value.
 *
 * Example:
 * `existingTemplates` is: [ { slug: 'tag-apple' }, { slug: 'page-about' }, { slug: 'tag' } ]
 * `templatePrefixes` is: { post_tag: 'tag' }
 * It will return: { post_tag: ['apple'] }
 *
 * Note: We append the `-` to the given template prefix in this function for our checks.
 *
 * @param {Record<string,string>} templatePrefixes An object with the entity's slug as key and the template prefix as value.
 * @return {Record<string,string[]>} An object with the entity's slug as key and an array with the existing template slugs as value.
 */

const useExistingTemplateSlugs = templatePrefixes => {
  const existingTemplates = useExistingTemplates();
  const existingSlugs = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return Object.entries(templatePrefixes || {}).reduce((accumulator, _ref17) => {
      let [slug, prefix] = _ref17;
      const slugsWithTemplates = (existingTemplates || []).reduce((_accumulator, existingTemplate) => {
        const _prefix = `${prefix}-`;

        if (existingTemplate.slug.startsWith(_prefix)) {
          _accumulator.push(existingTemplate.slug.substring(_prefix.length));
        }

        return _accumulator;
      }, []);

      if (slugsWithTemplates.length) {
        accumulator[slug] = slugsWithTemplates;
      }

      return accumulator;
    }, {});
  }, [templatePrefixes, existingTemplates]);
  return existingSlugs;
};
/**
 * Helper hook that finds the existing records with an associated template,
 * as they need to be excluded from the template suggestions.
 *
 * @param {string}                entityName                The entity's name.
 * @param {Record<string,string>} templatePrefixes          An object with the entity's slug as key and the template prefix as value.
 * @param {Record<string,Object>} additionalQueryParameters An object with the entity's slug as key and additional query parameters as value.
 * @return {Record<string,EntitiesInfo>} An object with the entity's slug as key and the existing records as value.
 */


const useTemplatesToExclude = function (entityName, templatePrefixes) {
  let additionalQueryParameters = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  const slugsToExcludePerEntity = useExistingTemplateSlugs(templatePrefixes);
  const recordsToExcludePerEntity = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return Object.entries(slugsToExcludePerEntity || {}).reduce((accumulator, _ref18) => {
      let [slug, slugsWithTemplates] = _ref18;
      const entitiesWithTemplates = select(external_wp_coreData_namespaceObject.store).getEntityRecords(entityName, slug, {
        _fields: 'id',
        context: 'view',
        slug: slugsWithTemplates,
        ...additionalQueryParameters[slug]
      });

      if (entitiesWithTemplates !== null && entitiesWithTemplates !== void 0 && entitiesWithTemplates.length) {
        accumulator[slug] = entitiesWithTemplates;
      }

      return accumulator;
    }, {});
  }, [slugsToExcludePerEntity]);
  return recordsToExcludePerEntity;
};
/**
 * Helper hook that returns information about an entity having
 * records that we can create a specific template for.
 *
 * For example we can search for `terms` in `taxonomy` entity or
 * `posts` in `postType` entity.
 *
 * First we need to find the existing records with an associated template,
 * to query afterwards for any remaining record, by excluding them.
 *
 * @param {string}                entityName                The entity's name.
 * @param {Record<string,string>} templatePrefixes          An object with the entity's slug as key and the template prefix as value.
 * @param {Record<string,Object>} additionalQueryParameters An object with the entity's slug as key and additional query parameters as value.
 * @return {Record<string,EntitiesInfo>} An object with the entity's slug as key and the EntitiesInfo as value.
 */


const useEntitiesInfo = function (entityName, templatePrefixes) {
  let additionalQueryParameters = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  const recordsToExcludePerEntity = useTemplatesToExclude(entityName, templatePrefixes, additionalQueryParameters);
  const entitiesInfo = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return Object.keys(templatePrefixes || {}).reduce((accumulator, slug) => {
      var _recordsToExcludePerE, _select$getEntityReco;

      const existingEntitiesIds = (recordsToExcludePerEntity === null || recordsToExcludePerEntity === void 0 ? void 0 : (_recordsToExcludePerE = recordsToExcludePerEntity[slug]) === null || _recordsToExcludePerE === void 0 ? void 0 : _recordsToExcludePerE.map(_ref19 => {
        let {
          id
        } = _ref19;
        return id;
      })) || [];
      accumulator[slug] = {
        hasEntities: !!((_select$getEntityReco = select(external_wp_coreData_namespaceObject.store).getEntityRecords(entityName, slug, {
          per_page: 1,
          _fields: 'id',
          context: 'view',
          exclude: existingEntitiesIds,
          ...additionalQueryParameters[slug]
        })) !== null && _select$getEntityReco !== void 0 && _select$getEntityReco.length),
        existingEntitiesIds
      };
      return accumulator;
    }, {});
  }, [templatePrefixes, recordsToExcludePerEntity]);
  return entitiesInfo;
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/add-custom-template-modal.js



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



const add_custom_template_modal_EMPTY_ARRAY = [];

function SuggestionListItem(_ref) {
  let {
    suggestion,
    search,
    onSelect,
    entityForSuggestions,
    composite
  } = _ref;
  const baseCssClass = 'edit-site-custom-template-modal__suggestions_list__list-item';
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableCompositeItem, extends_extends({
    role: "option",
    as: external_wp_components_namespaceObject.Button
  }, composite, {
    className: baseCssClass,
    onClick: () => onSelect(entityForSuggestions.config.getSpecificTemplate(suggestion))
  }), (0,external_wp_element_namespaceObject.createElement)("span", {
    className: `${baseCssClass}__title`
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextHighlight, {
    text: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(suggestion.name),
    highlight: search
  })), suggestion.link && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: `${baseCssClass}__info`
  }, suggestion.link));
}

function useDebouncedInput() {
  const [input, setInput] = (0,external_wp_element_namespaceObject.useState)('');
  const [debounced, setter] = (0,external_wp_element_namespaceObject.useState)('');
  const setDebounced = (0,external_wp_compose_namespaceObject.useDebounce)(setter, 250);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (debounced !== input) {
      setDebounced(input);
    }
  }, [debounced, input]);
  return [input, setInput, debounced];
}

function useSearchSuggestions(entityForSuggestions, search) {
  const {
    config
  } = entityForSuggestions;
  const query = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    order: 'asc',
    context: 'view',
    search,
    per_page: search ? 20 : 10,
    ...config.queryArgs(search)
  }), [search, config]);
  const {
    records: searchResults,
    hasResolved: searchHasResolved
  } = (0,external_wp_coreData_namespaceObject.useEntityRecords)(entityForSuggestions.type, entityForSuggestions.slug, query);
  const [suggestions, setSuggestions] = (0,external_wp_element_namespaceObject.useState)(add_custom_template_modal_EMPTY_ARRAY);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!searchHasResolved) return;
    let newSuggestions = add_custom_template_modal_EMPTY_ARRAY;

    if (searchResults !== null && searchResults !== void 0 && searchResults.length) {
      newSuggestions = searchResults;

      if (config.recordNamePath) {
        newSuggestions = mapToIHasNameAndId(newSuggestions, config.recordNamePath);
      }
    } // Update suggestions only when the query has resolved, so as to keep
    // the previous results in the UI.


    setSuggestions(newSuggestions);
  }, [searchResults, searchHasResolved]);
  return suggestions;
}

function SuggestionList(_ref2) {
  let {
    entityForSuggestions,
    onSelect
  } = _ref2;
  const composite = (0,external_wp_components_namespaceObject.__unstableUseCompositeState)({
    orientation: 'vertical'
  });
  const [search, setSearch, debouncedSearch] = useDebouncedInput();
  const suggestions = useSearchSuggestions(entityForSuggestions, debouncedSearch);
  const {
    labels
  } = entityForSuggestions;
  const [showSearchControl, setShowSearchControl] = (0,external_wp_element_namespaceObject.useState)(false);

  if (!showSearchControl && (suggestions === null || suggestions === void 0 ? void 0 : suggestions.length) > 9) {
    setShowSearchControl(true);
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, showSearchControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SearchControl, {
    __nextHasNoMarginBottom: true,
    onChange: setSearch,
    value: search,
    label: labels.search_items,
    placeholder: labels.search_items
  }), !!(suggestions !== null && suggestions !== void 0 && suggestions.length) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableComposite, extends_extends({}, composite, {
    role: "listbox",
    className: "edit-site-custom-template-modal__suggestions_list",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Suggestions list')
  }), suggestions.map(suggestion => (0,external_wp_element_namespaceObject.createElement)(SuggestionListItem, {
    key: suggestion.slug,
    suggestion: suggestion,
    search: debouncedSearch,
    onSelect: onSelect,
    entityForSuggestions: entityForSuggestions,
    composite: composite
  }))), debouncedSearch && !(suggestions !== null && suggestions !== void 0 && suggestions.length) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "p",
    className: "edit-site-custom-template-modal__no-results"
  }, labels.not_found));
}

function AddCustomTemplateModal(_ref3) {
  let {
    onClose,
    onSelect,
    entityForSuggestions,
    isCreatingTemplate
  } = _ref3;
  const [showSearchEntities, setShowSearchEntities] = (0,external_wp_element_namespaceObject.useState)(entityForSuggestions.hasGeneralTemplate);
  const baseCssClass = 'edit-site-custom-template-modal';
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Name of the post type e.g: "Post".
    (0,external_wp_i18n_namespaceObject.__)('Add template: %s'), entityForSuggestions.labels.singular_name),
    className: baseCssClass,
    onRequestClose: onClose
  }, isCreatingTemplate && (0,external_wp_element_namespaceObject.createElement)(TemplateActionsLoadingScreen, null), !showSearchEntities && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 4
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "p"
  }, (0,external_wp_i18n_namespaceObject.__)('Select whether to create a single template for all items or a specific one.')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
    className: `${baseCssClass}__contents`,
    gap: "4",
    align: "initial"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    isBlock: true,
    as: external_wp_components_namespaceObject.Button,
    onClick: () => {
      const {
        slug,
        title,
        description,
        templatePrefix
      } = entityForSuggestions.template;
      onSelect({
        slug,
        title,
        description,
        templatePrefix
      });
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "span",
    weight: 600
  }, entityForSuggestions.labels.all_items), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "span"
  }, // translators: The user is given the choice to set up a template for all items of a post type or taxonomy, or just a specific one.
  (0,external_wp_i18n_namespaceObject.__)('For all items'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    isBlock: true,
    as: external_wp_components_namespaceObject.Button,
    onClick: () => {
      setShowSearchEntities(true);
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "span",
    weight: 600
  }, entityForSuggestions.labels.singular_name), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "span"
  }, // translators: The user is given the choice to set up a template for all items of a post type or taxonomy, or just a specific one.
  (0,external_wp_i18n_namespaceObject.__)('For a specific item'))))), showSearchEntities && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 4
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    as: "p"
  }, (0,external_wp_i18n_namespaceObject.__)('This template will be used only for the specific item chosen.')), (0,external_wp_element_namespaceObject.createElement)(SuggestionList, {
    entityForSuggestions: entityForSuggestions,
    onSelect: onSelect
  })));
}

/* harmony default export */ var add_custom_template_modal = (AddCustomTemplateModal);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/add-custom-generic-template-modal.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function AddCustomGenericTemplateModal(_ref) {
  let {
    onClose,
    createTemplate,
    isCreatingTemplate
  } = _ref;
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');

  const defaultTitle = (0,external_wp_i18n_namespaceObject.__)('Custom Template');

  const [isBusy, setIsBusy] = (0,external_wp_element_namespaceObject.useState)(false);

  async function onCreateTemplate(event) {
    event.preventDefault();

    if (isBusy) {
      return;
    }

    setIsBusy(true);

    try {
      await createTemplate({
        slug: 'wp-custom-template-' + (0,external_lodash_namespaceObject.kebabCase)(title || defaultTitle),
        title: title || defaultTitle
      }, false);
    } finally {
      setIsBusy(false);
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Create custom template'),
    onRequestClose: () => {
      onClose();
    },
    overlayClassName: "edit-site-custom-generic-template__modal"
  }, isCreatingTemplate && (0,external_wp_element_namespaceObject.createElement)(TemplateActionsLoadingScreen, null), (0,external_wp_element_namespaceObject.createElement)("form", {
    onSubmit: onCreateTemplate
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 6
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    placeholder: defaultTitle,
    disabled: isBusy,
    help: (0,external_wp_i18n_namespaceObject.__)('Describe the template, e.g. "Post with sidebar".')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: "edit-site-custom-generic-template__modal-actions",
    justify: "right"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => {
      onClose();
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, (0,external_wp_i18n_namespaceObject.__)('Create'))))));
}

/* harmony default export */ var add_custom_generic_template_modal = (AddCustomGenericTemplateModal);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/new-template.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */








const DEFAULT_TEMPLATE_SLUGS = ['front-page', 'single', 'page', 'index', 'archive', 'author', 'category', 'date', 'tag', 'taxonomy', 'search', '404'];
const TEMPLATE_ICONS = {
  'front-page': library_home,
  single: library_post,
  page: library_page,
  archive: library_archive,
  search: library_search,
  404: not_found,
  index: library_list,
  category: library_category,
  author: post_author,
  taxonomy: block_meta,
  date: post_date,
  tag: library_tag,
  attachment: library_media
};
function NewTemplate(_ref) {
  let {
    postType,
    toggleProps,
    showIcon = true
  } = _ref;
  const [showCustomTemplateModal, setShowCustomTemplateModal] = (0,external_wp_element_namespaceObject.useState)(false);
  const [showCustomGenericTemplateModal, setShowCustomGenericTemplateModal] = (0,external_wp_element_namespaceObject.useState)(false);
  const [entityForSuggestions, setEntityForSuggestions] = (0,external_wp_element_namespaceObject.useState)({});
  const [isCreatingTemplate, setIsCreatingTemplate] = (0,external_wp_element_namespaceObject.useState)(false);
  const history = useHistory();
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    createErrorNotice,
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    setTemplate,
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));

  async function createTemplate(template) {
    let isWPSuggestion = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

    if (isCreatingTemplate) {
      return;
    }

    setIsCreatingTemplate(true);

    try {
      const {
        title,
        description,
        slug,
        templatePrefix
      } = template;
      let templateContent = template.content; // Try to find fallback content from existing templates.

      if (!templateContent) {
        const fallbackTemplate = await external_wp_apiFetch_default()({
          path: (0,external_wp_url_namespaceObject.addQueryArgs)('/wp/v2/templates/lookup', {
            slug,
            is_custom: !isWPSuggestion,
            template_prefix: templatePrefix
          })
        });
        templateContent = fallbackTemplate.content.raw;
      }

      const newTemplate = await saveEntityRecord('postType', 'wp_template', {
        description,
        // Slugs need to be strings, so this is for template `404`
        slug: slug.toString(),
        status: 'publish',
        title,
        content: templateContent,
        // This adds a post meta field in template that is part of `is_custom` value calculation.
        is_wp_suggestion: isWPSuggestion
      }, {
        throwOnError: true
      }); // Set template before navigating away to avoid initial stale value.

      setTemplate(newTemplate.id, newTemplate.slug); // Switch to edit mode.

      setCanvasMode('edit'); // Navigate to the created template editor.

      history.push({
        postId: newTemplate.id,
        postType: newTemplate.type
      });
      createSuccessNotice((0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Title of the created template e.g: "Category".
      (0,external_wp_i18n_namespaceObject.__)('"%s" successfully created.'), title), {
        type: 'snackbar'
      });
    } catch (error) {
      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while creating the template.');
      createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
    } finally {
      setIsCreatingTemplate(false);
    }
  }

  const missingTemplates = useMissingTemplates(setEntityForSuggestions, setShowCustomTemplateModal);

  if (!missingTemplates.length) {
    return null;
  }

  const customTemplateDescription = (0,external_wp_i18n_namespaceObject.__)('A custom template can be manually applied to any post or page.');

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    className: "edit-site-new-template-dropdown",
    icon: showIcon ? library_plus : null,
    text: showIcon ? null : postType.labels.add_new,
    label: postType.labels.add_new_item,
    popoverProps: {
      noArrow: false
    },
    toggleProps: toggleProps
  }, () => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isCreatingTemplate && (0,external_wp_element_namespaceObject.createElement)(TemplateActionsLoadingScreen, null), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-new-template-dropdown__menu-groups"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: postType.labels.add_new_item
  }, missingTemplates.map(template => {
    const {
      title,
      description,
      slug,
      onClick,
      icon
    } = template;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Tooltip, {
      key: slug,
      position: "top right",
      text: description,
      className: "edit-site-new-template-dropdown__menu-item-tooltip"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      icon: icon || TEMPLATE_ICONS[slug] || library_post,
      iconPosition: "left",
      onClick: () => onClick ? onClick(template) : createTemplate(template)
    }, title, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, null, description)));
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Tooltip, {
    position: "top right",
    text: customTemplateDescription,
    className: "edit-site-new-template-dropdown__menu-item-tooltip"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    icon: library_layout,
    iconPosition: "left",
    onClick: () => setShowCustomGenericTemplateModal(true)
  }, (0,external_wp_i18n_namespaceObject.__)('Custom template'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, null, customTemplateDescription))))))), showCustomTemplateModal && (0,external_wp_element_namespaceObject.createElement)(add_custom_template_modal, {
    onClose: () => setShowCustomTemplateModal(false),
    onSelect: createTemplate,
    entityForSuggestions: entityForSuggestions,
    isCreatingTemplate: isCreatingTemplate
  }), showCustomGenericTemplateModal && (0,external_wp_element_namespaceObject.createElement)(add_custom_generic_template_modal, {
    onClose: () => setShowCustomGenericTemplateModal(false),
    createTemplate: createTemplate,
    isCreatingTemplate: isCreatingTemplate
  }));
}

function useMissingTemplates(setEntityForSuggestions, setShowCustomTemplateModal) {
  const existingTemplates = useExistingTemplates();
  const defaultTemplateTypes = useDefaultTemplateTypes();
  const existingTemplateSlugs = (existingTemplates || []).map(_ref2 => {
    let {
      slug
    } = _ref2;
    return slug;
  });
  const missingDefaultTemplates = (defaultTemplateTypes || []).filter(template => DEFAULT_TEMPLATE_SLUGS.includes(template.slug) && !existingTemplateSlugs.includes(template.slug));

  const onClickMenuItem = _entityForSuggestions => {
    setShowCustomTemplateModal(true);
    setEntityForSuggestions(_entityForSuggestions);
  }; // We need to replace existing default template types with
  // the create specific template functionality. The original
  // info (title, description, etc.) is preserved in the
  // used hooks.


  const enhancedMissingDefaultTemplateTypes = [...missingDefaultTemplates];
  const {
    defaultTaxonomiesMenuItems,
    taxonomiesMenuItems
  } = useTaxonomiesMenuItems(onClickMenuItem);
  const {
    defaultPostTypesMenuItems,
    postTypesMenuItems
  } = usePostTypeMenuItems(onClickMenuItem);
  const authorMenuItem = useAuthorMenuItem(onClickMenuItem);
  [...defaultTaxonomiesMenuItems, ...defaultPostTypesMenuItems, authorMenuItem].forEach(menuItem => {
    if (!menuItem) {
      return;
    }

    const matchIndex = enhancedMissingDefaultTemplateTypes.findIndex(template => template.slug === menuItem.slug); // Some default template types might have been filtered above from
    // `missingDefaultTemplates` because they only check for the general
    // template. So here we either replace or append the item, augmented
    // with the check if it has available specific item to create a
    // template for.

    if (matchIndex > -1) {
      enhancedMissingDefaultTemplateTypes[matchIndex] = menuItem;
    } else {
      enhancedMissingDefaultTemplateTypes.push(menuItem);
    }
  }); // Update the sort order to match the DEFAULT_TEMPLATE_SLUGS order.

  enhancedMissingDefaultTemplateTypes === null || enhancedMissingDefaultTemplateTypes === void 0 ? void 0 : enhancedMissingDefaultTemplateTypes.sort((template1, template2) => {
    return DEFAULT_TEMPLATE_SLUGS.indexOf(template1.slug) - DEFAULT_TEMPLATE_SLUGS.indexOf(template2.slug);
  });
  const missingTemplates = [...enhancedMissingDefaultTemplateTypes, ...usePostTypeArchiveMenuItems(), ...postTypesMenuItems, ...taxonomiesMenuItems];
  return missingTemplates;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/create-template-part-modal/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function CreateTemplatePartModal(_ref) {
  let {
    closeModal,
    onCreate
  } = _ref;
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)('');
  const [area, setArea] = (0,external_wp_element_namespaceObject.useState)(TEMPLATE_PART_AREA_GENERAL);
  const [isSubmitting, setIsSubmitting] = (0,external_wp_element_namespaceObject.useState)(false);
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(CreateTemplatePartModal);
  const templatePartAreas = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).__experimentalGetDefaultTemplatePartAreas(), []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Create a template part'),
    onRequestClose: closeModal,
    overlayClassName: "edit-site-create-template-part-modal"
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    onSubmit: async event => {
      event.preventDefault();

      if (!title) {
        return;
      }

      setIsSubmitting(true);
      await onCreate({
        title,
        area
      });
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: "4"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    required: true
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.BaseControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Area'),
    id: `edit-site-create-template-part-modal__area-selection-${instanceId}`,
    className: "edit-site-create-template-part-modal__area-base-control"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalRadioGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Area'),
    className: "edit-site-create-template-part-modal__area-radio-group",
    id: `edit-site-create-template-part-modal__area-selection-${instanceId}`,
    onChange: setArea,
    checked: area
  }, templatePartAreas.map(_ref2 => {
    let {
      icon,
      label,
      area: value,
      description
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalRadio, {
      key: label,
      value: value,
      className: "edit-site-create-template-part-modal__area-radio"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
      align: "start",
      justify: "start"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
      icon: icon
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexBlock, {
      className: "edit-site-create-template-part-modal__option-label"
    }, label, (0,external_wp_element_namespaceObject.createElement)("div", null, description)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
      className: "edit-site-create-template-part-modal__checkbox"
    }, area === value && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
      icon: library_check
    }))));
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
    className: "edit-site-create-template-part-modal__modal-actions",
    justify: "flex-end"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    onClick: () => {
      closeModal();
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    disabled: !title,
    isBusy: isSubmitting
  }, (0,external_wp_i18n_namespaceObject.__)('Create')))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/utils/template-part-create.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



const useExistingTemplateParts = () => {
  return (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getEntityRecords('postType', 'wp_template_part', {
    per_page: -1
  }), []);
};
/**
 * Return a unique template part title based on
 * the given title and existing template parts.
 *
 * @param {string} title         The original template part title.
 * @param {Object} templateParts The array of template part entities.
 * @return {string} A unique template part title.
 */

const getUniqueTemplatePartTitle = (title, templateParts) => {
  const lowercaseTitle = title.toLowerCase();
  const existingTitles = templateParts.map(templatePart => templatePart.title.rendered.toLowerCase());

  if (!existingTitles.includes(lowercaseTitle)) {
    return title;
  }

  let suffix = 2;

  while (existingTitles.includes(`${lowercaseTitle} ${suffix}`)) {
    suffix++;
  }

  return `${title} ${suffix}`;
};
/**
 * Get a valid slug for a template part.
 * Currently template parts only allow latin chars.
 * The fallback slug will receive suffix by default.
 *
 * @param {string} title The template part title.
 * @return {string} A valid template part slug.
 */

const getCleanTemplatePartSlug = title => {
  return (0,external_lodash_namespaceObject.kebabCase)(title).replace(/[^\w-]+/g, '') || 'wp-custom-part';
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/new-template-part.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */






function NewTemplatePart(_ref) {
  let {
    postType,
    showIcon = true,
    toggleProps
  } = _ref;
  const history = useHistory();
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const existingTemplateParts = useExistingTemplateParts();

  async function createTemplatePart(_ref2) {
    let {
      title,
      area
    } = _ref2;

    if (!title) {
      createErrorNotice((0,external_wp_i18n_namespaceObject.__)('Title is not defined.'), {
        type: 'snackbar'
      });
      return;
    }

    try {
      const uniqueTitle = getUniqueTemplatePartTitle(title, existingTemplateParts);
      const cleanSlug = getCleanTemplatePartSlug(uniqueTitle);
      const templatePart = await saveEntityRecord('postType', 'wp_template_part', {
        slug: cleanSlug,
        title: uniqueTitle,
        content: '',
        area
      }, {
        throwOnError: true
      });
      setIsModalOpen(false); // Switch to edit mode.

      setCanvasMode('edit'); // Navigate to the created template part editor.

      history.push({
        postId: templatePart.id,
        postType: 'wp_template_part'
      }); // TODO: Add a success notice?
    } catch (error) {
      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while creating the template part.');
      createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
      setIsModalOpen(false);
    }
  }

  const {
    as: Toggle = external_wp_components_namespaceObject.Button,
    ...restToggleProps
  } = toggleProps !== null && toggleProps !== void 0 ? toggleProps : {};
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(Toggle, extends_extends({}, restToggleProps, {
    onClick: () => {
      setIsModalOpen(true);
    },
    icon: showIcon ? library_plus : null,
    label: postType.labels.add_new
  }), showIcon ? null : postType.labels.add_new), isModalOpen && (0,external_wp_element_namespaceObject.createElement)(CreateTemplatePartModal, {
    closeModal: () => setIsModalOpen(false),
    onCreate: createTemplatePart
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/add-new-template/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function AddNewTemplate(_ref) {
  let {
    templateType = 'wp_template',
    ...props
  } = _ref;
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getPostType(templateType), [templateType]);

  if (!postType) {
    return null;
  }

  if (templateType === 'wp_template') {
    return (0,external_wp_element_namespaceObject.createElement)(NewTemplate, extends_extends({}, props, {
      postType: postType
    }));
  } else if (templateType === 'wp_template_part') {
    return (0,external_wp_element_namespaceObject.createElement)(NewTemplatePart, extends_extends({}, props, {
      postType: postType
    }));
  }

  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-templates/index.js



/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */







const config = {
  wp_template: {
    labels: {
      title: (0,external_wp_i18n_namespaceObject.__)('Templates'),
      loading: (0,external_wp_i18n_namespaceObject.__)('Loading templates'),
      notFound: (0,external_wp_i18n_namespaceObject.__)('No templates found'),
      manage: (0,external_wp_i18n_namespaceObject.__)('Manage all templates'),
      description: (0,external_wp_i18n_namespaceObject.__)('Express the layout of your site with templates.')
    }
  },
  wp_template_part: {
    labels: {
      title: (0,external_wp_i18n_namespaceObject.__)('Template parts'),
      loading: (0,external_wp_i18n_namespaceObject.__)('Loading template parts'),
      notFound: (0,external_wp_i18n_namespaceObject.__)('No template parts found'),
      manage: (0,external_wp_i18n_namespaceObject.__)('Manage all template parts'),
      description: (0,external_wp_i18n_namespaceObject.__)('Template Parts are small pieces of a layout that can be reused across multiple templates and always appear the same way. Common template parts include the site header, footer, or sidebar.')
    }
  }
};

const TemplateItem = _ref => {
  let {
    postType,
    postId,
    ...props
  } = _ref;
  const linkInfo = useLink({
    postType,
    postId
  });
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationItem, extends_extends({}, linkInfo, props));
};

function SidebarNavigationScreenTemplates() {
  const {
    params: {
      postType
    }
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const isTemplatePartsMode = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const settings = select(store_store).getSettings();
    return !!settings.supportsTemplatePartsMode;
  }, []);
  const {
    records: templates,
    isResolving: isLoading
  } = (0,external_wp_coreData_namespaceObject.useEntityRecords)('postType', postType, {
    per_page: -1
  });
  const sortedTemplates = templates ? [...templates] : [];
  sortedTemplates.sort((a, b) => a.slug.localeCompare(b.slug));
  const browseAllLink = useLink({
    path: '/' + postType + '/all'
  });
  const canCreate = !isMobileViewport && !isTemplatePartsMode;
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    isRoot: isTemplatePartsMode,
    title: config[postType].labels.title,
    description: config[postType].labels.description,
    actions: canCreate && (0,external_wp_element_namespaceObject.createElement)(AddNewTemplate, {
      templateType: postType,
      toggleProps: {
        as: SidebarButton
      }
    }),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLoading && config[postType].labels.loading, !isLoading && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, !(templates !== null && templates !== void 0 && templates.length) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItem, null, config[postType].labels.notFound), sortedTemplates.map(template => {
      var _template$title;

      return (0,external_wp_element_namespaceObject.createElement)(TemplateItem, {
        postType: postType,
        postId: template.id,
        key: template.id
      }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(((_template$title = template.title) === null || _template$title === void 0 ? void 0 : _template$title.rendered) || template.slug));
    }), !isMobileViewport && (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationItem, extends_extends({
      className: "edit-site-sidebar-navigation-screen-templates__see-all"
    }, browseAllLink, {
      children: config[postType].labels.manage
    }))))
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/pencil.js


/**
 * WordPress dependencies
 */

const pencil = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.1 5.1L16.9 2 6.2 12.7l-1.3 4.4 4.5-1.3L20.1 5.1zM4 20.8h8v-1.5H4v1.5z"
}));
/* harmony default export */ var library_pencil = (pencil);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/use-edited-entity-record/index.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function useEditedEntityRecord(postType, postId) {
  const {
    record,
    title,
    description,
    isLoaded
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostType,
      getEditedPostId
    } = select(store_store);
    const {
      getEditedEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      __experimentalGetTemplateInfo: getTemplateInfo
    } = select(external_wp_editor_namespaceObject.store);
    const usedPostType = postType !== null && postType !== void 0 ? postType : getEditedPostType();
    const usedPostId = postId !== null && postId !== void 0 ? postId : getEditedPostId();

    const _record = getEditedEntityRecord('postType', usedPostType, usedPostId);

    const _isLoaded = !!usedPostId;

    const templateInfo = getTemplateInfo(_record);
    return {
      record: _record,
      title: templateInfo.title,
      description: templateInfo.description,
      isLoaded: _isLoaded
    };
  }, [postType, postId]);
  return {
    isLoaded,
    record,
    getTitle: () => title ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title) : null,
    getDescription: () => description ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(description) : null
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-template/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */






function SidebarNavigationScreenTemplate() {
  const {
    params
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  const {
    postType,
    postId
  } = params;
  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const {
    getDescription,
    getTitle,
    record
  } = useEditedEntityRecord(postType, postId);
  let description = getDescription();

  if (!description) {
    if (record.type === 'wp_template' && record.is_custom) {
      description = (0,external_wp_i18n_namespaceObject.__)('This is a custom template that can be applied manually to any Post or Page.');
    } else if (record.type === 'wp_template_part') {
      description = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: template part title e.g: "Header".
      (0,external_wp_i18n_namespaceObject.__)('This is your %s template part.'), getTitle());
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    title: getTitle(),
    actions: (0,external_wp_element_namespaceObject.createElement)(SidebarButton, {
      onClick: () => setCanvasMode('edit'),
      label: (0,external_wp_i18n_namespaceObject.__)('Edit'),
      icon: library_pencil
    }),
    description: description
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sync-state-with-url/use-sync-path-with-url.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function getPathFromURL(urlParams) {
  var _urlParams$path;

  let path = (_urlParams$path = urlParams === null || urlParams === void 0 ? void 0 : urlParams.path) !== null && _urlParams$path !== void 0 ? _urlParams$path : '/'; // Compute the navigator path based on the URL params.

  if (urlParams !== null && urlParams !== void 0 && urlParams.postType && urlParams !== null && urlParams !== void 0 && urlParams.postId) {
    switch (urlParams.postType) {
      case 'wp_template':
      case 'wp_template_part':
        path = `/${encodeURIComponent(urlParams.postType)}/${encodeURIComponent(urlParams.postId)}`;
        break;

      default:
        path = `/navigation/${encodeURIComponent(urlParams.postType)}/${encodeURIComponent(urlParams.postId)}`;
    }
  }

  return path;
}
function useSyncPathWithURL() {
  const history = useHistory();
  const {
    params: urlParams
  } = useLocation();
  const {
    location: navigatorLocation,
    params: navigatorParams,
    goTo
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  const currentUrlParams = (0,external_wp_element_namespaceObject.useRef)(urlParams);
  const currentPath = (0,external_wp_element_namespaceObject.useRef)(navigatorLocation.path);
  const isMounting = (0,external_wp_element_namespaceObject.useRef)(true);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // The navigatorParams are only initially filled properly when the
    // navigator screens mount. so we ignore the first synchronisation.
    if (isMounting.current) {
      isMounting.current = false;
      return;
    }

    function updateUrlParams(newUrlParams) {
      if (Object.entries(newUrlParams).every(_ref => {
        let [key, value] = _ref;
        return currentUrlParams.current[key] === value;
      })) {
        return;
      }

      const updatedParams = { ...currentUrlParams.current,
        ...newUrlParams
      };
      currentUrlParams.current = updatedParams;
      history.push(updatedParams);
    }

    if (navigatorParams !== null && navigatorParams !== void 0 && navigatorParams.postType && navigatorParams !== null && navigatorParams !== void 0 && navigatorParams.postId) {
      updateUrlParams({
        postType: navigatorParams === null || navigatorParams === void 0 ? void 0 : navigatorParams.postType,
        postId: navigatorParams === null || navigatorParams === void 0 ? void 0 : navigatorParams.postId,
        path: undefined
      });
    } else {
      updateUrlParams({
        postType: undefined,
        postId: undefined,
        path: navigatorLocation.path === '/' ? undefined : navigatorLocation.path
      });
    }
  }, [navigatorLocation === null || navigatorLocation === void 0 ? void 0 : navigatorLocation.path, navigatorParams, history]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentUrlParams.current = urlParams;
    const path = getPathFromURL(urlParams);

    if (currentPath.current !== path) {
      currentPath.current = path;
      goTo(path);
    }
  }, [urlParams, goTo]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-navigation-menus/loader.js

function NavigationMenuLoader() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-sidebar-navigation-screen-navigation-menus__placeholder"
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-sidebar-navigation-screen-navigation-menus__placeholder"
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-sidebar-navigation-screen-navigation-menus__placeholder"
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-navigation-menus/navigation-menu-content.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function NavigationMenuContent(_ref) {
  let {
    rootClientId,
    onSelect
  } = _ref;
  const {
    clientIdsTree,
    isLoading
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __unstableGetClientIdsTree,
      areInnerBlocksControlled
    } = select(external_wp_blockEditor_namespaceObject.store);
    return {
      clientIdsTree: __unstableGetClientIdsTree(rootClientId),
      // This is a small hack to wait for the navigation block
      // to actually load its inner blocks.
      isLoading: !areInnerBlocksControlled(rootClientId)
    };
  }, [rootClientId]);
  const {
    replaceBlock,
    __unstableMarkNextChangeAsNotPersistent
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    OffCanvasEditor,
    LeafMoreMenu
  } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
  const offCanvasOnselect = (0,external_wp_element_namespaceObject.useCallback)(block => {
    if (block.name === 'core/navigation-link' && !block.attributes.url) {
      __unstableMarkNextChangeAsNotPersistent();

      replaceBlock(block.clientId, (0,external_wp_blocks_namespaceObject.createBlock)('core/navigation-link', block.attributes));
    } else {
      onSelect(block);
    }
  }, [onSelect, __unstableMarkNextChangeAsNotPersistent, replaceBlock]); // The hidden block is needed because it makes block edit side effects trigger.
  // For example a navigation page list load its items has an effect on edit to load its items.

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isLoading && (0,external_wp_element_namespaceObject.createElement)(NavigationMenuLoader, null), !isLoading && (0,external_wp_element_namespaceObject.createElement)(OffCanvasEditor, {
    blocks: clientIdsTree,
    onSelect: offCanvasOnselect,
    LeafMoreMenu: LeafMoreMenu,
    showAppender: false
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      visibility: 'hidden'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockTools, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, null))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-navigation-menus/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */








const sidebar_navigation_screen_navigation_menus_noop = () => {};

const NAVIGATION_MENUS_QUERY = {
  per_page: -1,
  status: 'publish'
};

function SidebarNavigationScreenWrapper(_ref) {
  let {
    children,
    actions
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    title: (0,external_wp_i18n_namespaceObject.__)('Navigation'),
    actions: actions,
    description: (0,external_wp_i18n_namespaceObject.__)('Browse your site, edit pages, and manage your primary navigation menu.'),
    content: children
  });
}

function SidebarNavigationScreenNavigationMenus() {
  var _orderedNavigationMen;

  const history = useHistory();
  const {
    navigationMenus,
    hasResolvedNavigationMenus,
    storedSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings
    } = unlock(select(store_store));
    const {
      getEntityRecords,
      hasFinishedResolution
    } = select(external_wp_coreData_namespaceObject.store);
    const navigationMenusQuery = ['postType', 'wp_navigation', NAVIGATION_MENUS_QUERY];
    return {
      storedSettings: getSettings(false),
      navigationMenus: getEntityRecords(...navigationMenusQuery),
      hasResolvedNavigationMenus: hasFinishedResolution('getEntityRecords', navigationMenusQuery)
    };
  }, []); // Sort navigation menus by date.

  const orderedNavigationMenus = (0,external_wp_element_namespaceObject.useMemo)(() => navigationMenus === null || navigationMenus === void 0 ? void 0 : navigationMenus.sort((menuA, menuB) => {
    const menuADate = new Date(menuA.date);
    const menuBDate = new Date(menuB.date);
    return menuADate.getTime() > menuBDate.getTime();
  }), [navigationMenus]);
  const firstNavigationMenu = orderedNavigationMenus === null || orderedNavigationMenus === void 0 ? void 0 : (_orderedNavigationMen = orderedNavigationMenus[0]) === null || _orderedNavigationMen === void 0 ? void 0 : _orderedNavigationMen.id;
  const blocks = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return [(0,external_wp_blocks_namespaceObject.createBlock)('core/navigation', {
      ref: firstNavigationMenu
    })];
  }, [firstNavigationMenu]);
  const isLoading = !hasResolvedNavigationMenus;
  const hasNavigationMenus = !!(navigationMenus !== null && navigationMenus !== void 0 && navigationMenus.length);
  const onSelect = (0,external_wp_element_namespaceObject.useCallback)(selectedBlock => {
    const {
      attributes,
      name
    } = selectedBlock;

    if (attributes.kind === 'post-type' && attributes.id && attributes.type && history) {
      history.push({
        postType: attributes.type,
        postId: attributes.id
      });
    }

    if (name === 'core/page-list-item' && attributes.id && history) {
      history.push({
        postType: 'page',
        postId: attributes.id
      });
    }
  }, [history]);

  if (hasResolvedNavigationMenus && !hasNavigationMenus) {
    return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenWrapper, null, (0,external_wp_i18n_namespaceObject.__)('There are no Navigation Menus.'));
  }

  if (!hasResolvedNavigationMenus || isLoading) {
    return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenWrapper, null, (0,external_wp_element_namespaceObject.createElement)(NavigationMenuLoader, null));
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockEditorProvider, {
    settings: storedSettings,
    value: blocks,
    onChange: sidebar_navigation_screen_navigation_menus_noop,
    onInput: sidebar_navigation_screen_navigation_menus_noop
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenWrapper, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-sidebar-navigation-screen-navigation-menus__content"
  }, (0,external_wp_element_namespaceObject.createElement)(NavigationMenuContent, {
    rootClientId: blocks[0].clientId,
    onSelect: onSelect
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-templates-browse/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const sidebar_navigation_screen_templates_browse_config = {
  wp_template: {
    title: (0,external_wp_i18n_namespaceObject.__)('All templates'),
    description: (0,external_wp_i18n_namespaceObject.__)('Create new templates, or reset any customizations made to the templates supplied by your theme.')
  },
  wp_template_part: {
    title: (0,external_wp_i18n_namespaceObject.__)('All template parts'),
    description: (0,external_wp_i18n_namespaceObject.__)('Create new template parts, or reset any customizations made to the template parts supplied by your theme.')
  }
};
function SidebarNavigationScreenTemplatesBrowse() {
  const {
    params: {
      postType
    }
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    title: sidebar_navigation_screen_templates_browse_config[postType].title,
    description: sidebar_navigation_screen_templates_browse_config[postType].description
  });
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
var external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/save-hub/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function SaveButton() {
  const {
    countUnsavedChanges,
    isDirty,
    isSaving,
    isSaveViewOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __experimentalGetDirtyEntityRecords,
      isSavingEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);

    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();

    const {
      isSaveViewOpened
    } = select(store_store);
    return {
      isDirty: dirtyEntityRecords.length > 0,
      isSaving: dirtyEntityRecords.some(record => isSavingEntityRecord(record.kind, record.name, record.key)),
      isSaveViewOpen: isSaveViewOpened(),
      countUnsavedChanges: dirtyEntityRecords.length
    };
  }, []);
  const {
    setIsSaveViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const disabled = !isDirty || isSaving;
  const label = disabled ? (0,external_wp_i18n_namespaceObject.__)('Saved') : (0,external_wp_i18n_namespaceObject.__)('Save');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    className: "edit-site-save-hub",
    alignment: "right",
    spacing: 4
  }, isDirty && (0,external_wp_element_namespaceObject.createElement)("span", null, (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %d: number of unsaved changes (number).
  (0,external_wp_i18n_namespaceObject._n)('%d unsaved change', '%d unsaved changes', countUnsavedChanges), countUnsavedChanges)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-site-save-hub__button",
    variant: disabled ? undefined : 'primary',
    "aria-disabled": disabled,
    "aria-expanded": isSaveViewOpen,
    isBusy: isSaving,
    onClick: disabled ? undefined : () => setIsSaveViewOpened(true),
    label: label
    /*
     * We want the tooltip to show the keyboard shortcut only when the
     * button does something, i.e. when it's not disabled.
     */
    ,
    shortcut: disabled ? undefined : external_wp_keycodes_namespaceObject.displayShortcut.primary('s'),
    icon: disabled ? library_check : undefined
  }, label));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-navigation-screen-navigation-item/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





function SidebarNavigationScreenNavigationItem() {
  var _record$title, _record$description;

  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const {
    params: {
      postType,
      postId
    }
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  const {
    record
  } = (0,external_wp_coreData_namespaceObject.useEntityRecord)('postType', postType, postId);
  return (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreen, {
    title: record ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(record === null || record === void 0 ? void 0 : (_record$title = record.title) === null || _record$title === void 0 ? void 0 : _record$title.rendered) : null,
    actions: (0,external_wp_element_namespaceObject.createElement)(SidebarButton, {
      onClick: () => setCanvasMode('edit'),
      label: (0,external_wp_i18n_namespaceObject.__)('Edit'),
      icon: library_pencil
    }),
    description: postType === 'page' ? (0,external_wp_i18n_namespaceObject.__)('Pages are static and are not listed by date. Pages do not use tags or categories.') : (0,external_wp_i18n_namespaceObject.__)('Posts are entries listed in reverse chronological order on the site homepage or on the posts page.'),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, record !== null && record !== void 0 && record.link ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
      className: "edit-site-sidebar-navigation-screen__page-link",
      href: record.link
    }, record.link) : null, record ? (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(record === null || record === void 0 ? void 0 : (_record$description = record.description) === null || _record$description === void 0 ? void 0 : _record$description.rendered) : null)
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */











function SidebarScreens() {
  useSyncPathWithURL();
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenMain, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/navigation"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenNavigationMenus, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/navigation/:postType/:postId"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenNavigationItem, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/:postType(wp_template|wp_template_part)"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenTemplates, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/:postType(wp_template|wp_template_part)/all"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenTemplatesBrowse, null)), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, {
    path: "/:postType(wp_template|wp_template_part)/:postId"
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarNavigationScreenTemplate, null)));
}

function Sidebar() {
  const {
    params: urlParams
  } = useLocation();
  const initialPath = (0,external_wp_element_namespaceObject.useRef)(getPathFromURL(urlParams));
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorProvider, {
    className: "edit-site-sidebar__content",
    initialPath: initialPath.current
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarScreens, null)), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-sidebar__footer"
  }, (0,external_wp_element_namespaceObject.createElement)(SaveButton, null)));
}

/* harmony default export */ var sidebar = ((0,external_wp_element_namespaceObject.memo)(Sidebar));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/drawer-left.js


/**
 * WordPress dependencies
 */

const drawerLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.5 18.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h2.5v13zm10-.5c0 .3-.2.5-.5.5h-8v-13h8c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ var drawer_left = (drawerLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/drawer-right.js


/**
 * WordPress dependencies
 */

const drawerRight = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  width: "24",
  height: "24",
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  clipRule: "evenodd",
  d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 14.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h8v13zm4.5-.5c0 .3-.2.5-.5.5h-2.5v-13H18c.3 0 .5.2.5.5v12z"
}));
/* harmony default export */ var drawer_right = (drawerRight);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/default-sidebar.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function DefaultSidebar(_ref) {
  let {
    className,
    identifier,
    title,
    icon,
    children,
    closeLabel,
    header,
    headerClassName,
    panelClassName
  } = _ref;
  const showIconLabels = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getSettings().showIconLabels, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(complementary_area, {
    className: className,
    scope: "core/edit-site",
    identifier: identifier,
    title: title,
    icon: icon,
    closeLabel: closeLabel,
    header: header,
    headerClassName: headerClassName,
    panelClassName: panelClassName,
    showIconLabels: showIconLabels
  }, children), (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem, {
    scope: "core/edit-site",
    identifier: identifier,
    icon: icon
  }, title));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/styles.js


/**
 * WordPress dependencies
 */

const styles = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4c-4.4 0-8 3.6-8 8v.1c0 4.1 3.2 7.5 7.2 7.9h.8c4.4 0 8-3.6 8-8s-3.6-8-8-8zm0 15V5c3.9 0 7 3.1 7 7s-3.1 7-7 7z"
}));
/* harmony default export */ var library_styles = (styles);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/seen.js


/**
 * WordPress dependencies
 */

const seen = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z"
}));
/* harmony default export */ var library_seen = (seen);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/icon-with-current-color.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


function IconWithCurrentColor(_ref) {
  let {
    className,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, extends_extends({
    className: classnames_default()(className, 'edit-site-global-styles-icon-with-current-color')
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/navigation-button.js



/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function GenericNavigationButton(_ref) {
  let {
    icon,
    children,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItem, props, icon && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
    icon: icon,
    size: 24
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, children)), !icon && children);
}

function NavigationButtonAsItem(props) {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorButton, extends_extends({
    as: GenericNavigationButton
  }, props));
}

function NavigationBackButtonAsItem(props) {
  return createElement(NavigatorToParentButton, _extends({
    as: GenericNavigationButton
  }, props));
}



;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/typography.js


/**
 * WordPress dependencies
 */

const typography = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M6.9 7L3 17.8h1.7l1-2.8h4.1l1 2.8h1.7L8.6 7H6.9zm-.7 6.6l1.5-4.3 1.5 4.3h-3zM21.6 17c-.1.1-.2.2-.3.2-.1.1-.2.1-.4.1s-.3-.1-.4-.2c-.1-.1-.1-.3-.1-.6V12c0-.5 0-1-.1-1.4-.1-.4-.3-.7-.5-1-.2-.2-.5-.4-.9-.5-.4 0-.8-.1-1.3-.1s-1 .1-1.4.2c-.4.1-.7.3-1 .4-.2.2-.4.3-.6.5-.1.2-.2.4-.2.7 0 .3.1.5.2.8.2.2.4.3.8.3.3 0 .6-.1.8-.3.2-.2.3-.4.3-.7 0-.3-.1-.5-.2-.7-.2-.2-.4-.3-.6-.4.2-.2.4-.3.7-.4.3-.1.6-.1.8-.1.3 0 .6 0 .8.1.2.1.4.3.5.5.1.2.2.5.2.9v1.1c0 .3-.1.5-.3.6-.2.2-.5.3-.9.4-.3.1-.7.3-1.1.4-.4.1-.8.3-1.1.5-.3.2-.6.4-.8.7-.2.3-.3.7-.3 1.2 0 .6.2 1.1.5 1.4.3.4.9.5 1.6.5.5 0 1-.1 1.4-.3.4-.2.8-.6 1.1-1.1 0 .4.1.7.3 1 .2.3.6.4 1.2.4.4 0 .7-.1.9-.2.2-.1.5-.3.7-.4h-.3zm-3-.9c-.2.4-.5.7-.8.8-.3.2-.6.2-.8.2-.4 0-.6-.1-.9-.3-.2-.2-.3-.6-.3-1.1 0-.5.1-.9.3-1.2s.5-.5.8-.7c.3-.2.7-.3 1-.5.3-.1.6-.3.7-.6v3.4z"
}));
/* harmony default export */ var library_typography = (typography);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/color.js


/**
 * WordPress dependencies
 */

const color = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.2 10.9c-.5-1-1.2-2.1-2.1-3.2-.6-.9-1.3-1.7-2.1-2.6L12 4l-1 1.1c-.6.9-1.3 1.7-2 2.6-.8 1.2-1.5 2.3-2 3.2-.6 1.2-1 2.2-1 3 0 3.4 2.7 6.1 6.1 6.1s6.1-2.7 6.1-6.1c0-.8-.3-1.8-1-3zm-5.1 7.6c-2.5 0-4.6-2.1-4.6-4.6 0-.3.1-1 .8-2.3.5-.9 1.1-1.9 2-3.1.7-.9 1.3-1.7 1.8-2.3.7.8 1.3 1.6 1.8 2.3.8 1.1 1.5 2.2 2 3.1.7 1.3.8 2 .8 2.3 0 2.5-2.1 4.6-4.6 4.6z"
}));
/* harmony default export */ var library_color = (color);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/border.js


/**
 * WordPress dependencies
 */

const border = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m6.6 15.6-1.2.8c.6.9 1.3 1.6 2.2 2.2l.8-1.2c-.7-.5-1.3-1.1-1.8-1.8zM5.5 12c0-.4 0-.9.1-1.3l-1.5-.3c0 .5-.1 1.1-.1 1.6s.1 1.1.2 1.6l1.5-.3c-.2-.4-.2-.9-.2-1.3zm11.9-3.6 1.2-.8c-.6-.9-1.3-1.6-2.2-2.2l-.8 1.2c.7.5 1.3 1.1 1.8 1.8zM5.3 7.6l1.2.8c.5-.7 1.1-1.3 1.8-1.8l-.7-1.3c-.9.6-1.7 1.4-2.3 2.3zm14.5 2.8-1.5.3c.1.4.1.8.1 1.3s0 .9-.1 1.3l1.5.3c.1-.5.2-1 .2-1.6s-.1-1.1-.2-1.6zM12 18.5c-.4 0-.9 0-1.3-.1l-.3 1.5c.5.1 1 .2 1.6.2s1.1-.1 1.6-.2l-.3-1.5c-.4.1-.9.1-1.3.1zm3.6-1.1.8 1.2c.9-.6 1.6-1.3 2.2-2.2l-1.2-.8c-.5.7-1.1 1.3-1.8 1.8zM10.4 4.2l.3 1.5c.4-.1.8-.1 1.3-.1s.9 0 1.3.1l.3-1.5c-.5-.1-1.1-.2-1.6-.2s-1.1.1-1.6.2z"
}));
/* harmony default export */ var library_border = (border);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/shadow.js


/**
 * WordPress dependencies
 */

const shadow = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 8c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zm0 6.5c-1.4 0-2.5-1.1-2.5-2.5s1.1-2.5 2.5-2.5 2.5 1.1 2.5 2.5-1.1 2.5-2.5 2.5zM12.8 3h-1.5v3h1.5V3zm-1.6 18h1.5v-3h-1.5v3zm6.8-9.8v1.5h3v-1.5h-3zm-12 0H3v1.5h3v-1.5zm9.7 5.6 2.1 2.1 1.1-1.1-2.1-2.1-1.1 1.1zM8.3 7.2 6.2 5.1 5.1 6.2l2.1 2.1 1.1-1.1zM5.1 17.8l1.1 1.1 2.1-2.1-1.1-1.1-2.1 2.1zM18.9 6.2l-1.1-1.1-2.1 2.1 1.1 1.1 2.1-2.1z"
}));
/* harmony default export */ var library_shadow = (shadow);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/border-panel.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



const {
  useGlobalSetting: border_panel_useGlobalSetting,
  useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function useHasBorderPanel(name) {
  const controls = [useHasBorderColorControl(name), useHasBorderRadiusControl(name), useHasBorderStyleControl(name), useHasBorderWidthControl(name)];
  return controls.some(Boolean);
}

function useHasBorderColorControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return border_panel_useGlobalSetting('border.color', name)[0] && supports.includes('borderColor');
}

function useHasBorderRadiusControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return border_panel_useGlobalSetting('border.radius', name)[0] && supports.includes('borderRadius');
}

function useHasBorderStyleControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return border_panel_useGlobalSetting('border.style', name)[0] && supports.includes('borderStyle');
}

function useHasBorderWidthControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return border_panel_useGlobalSetting('border.width', name)[0] && supports.includes('borderWidth');
}

function applyFallbackStyle(border) {
  if (!border) {
    return border;
  }

  if (!border.style && (border.color || border.width)) {
    return { ...border,
      style: 'solid'
    };
  }

  return border;
}

function applyAllFallbackStyles(border) {
  if (!border) {
    return border;
  }

  if ((0,external_wp_components_namespaceObject.__experimentalHasSplitBorders)(border)) {
    return {
      top: applyFallbackStyle(border.top),
      right: applyFallbackStyle(border.right),
      bottom: applyFallbackStyle(border.bottom),
      left: applyFallbackStyle(border.left)
    };
  }

  return applyFallbackStyle(border);
}

function BorderPanel(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : ''; // To better reflect if the user has customized a value we need to
  // ensure the style value being checked is from the `user` origin.

  const [userBorderStyles] = useGlobalStyle(`${prefix}border`, name, 'user');
  const [border, setBorder] = useGlobalStyle(`${prefix}border`, name);
  const colors = useColorsPerOrigin(name);
  const showBorderColor = useHasBorderColorControl(name);
  const showBorderStyle = useHasBorderStyleControl(name);
  const showBorderWidth = useHasBorderWidthControl(name); // Border radius.

  const showBorderRadius = useHasBorderRadiusControl(name);
  const [borderRadiusValues, setBorderRadius] = useGlobalStyle(`${prefix}border.radius`, name);

  const hasBorderRadius = () => {
    const borderValues = userBorderStyles === null || userBorderStyles === void 0 ? void 0 : userBorderStyles.radius;

    if (typeof borderValues === 'object') {
      return Object.entries(borderValues).some(Boolean);
    }

    return !!borderValues;
  };

  const resetBorder = () => {
    if (hasBorderRadius()) {
      return setBorder({
        radius: userBorderStyles.radius
      });
    }

    setBorder(undefined);
  };

  const resetAll = (0,external_wp_element_namespaceObject.useCallback)(() => setBorder(undefined), [setBorder]);
  const onBorderChange = (0,external_wp_element_namespaceObject.useCallback)(newBorder => {
    // Ensure we have a visible border style when a border width or
    // color is being selected.
    const newBorderWithStyle = applyAllFallbackStyles(newBorder); // As we can't conditionally generate styles based on if other
    // style properties have been set we need to force split border
    // definitions for user set border styles. Border radius is derived
    // from the same property i.e. `border.radius` if it is a string
    // that is used. The longhand border radii styles are only generated
    // if that property is an object.
    //
    // For borders (color, style, and width) those are all properties on
    // the `border` style property. This means if the theme.json defined
    // split borders and the user condenses them into a flat border or
    // vice-versa we'd get both sets of styles which would conflict.

    const updatedBorder = !(0,external_wp_components_namespaceObject.__experimentalHasSplitBorders)(newBorderWithStyle) ? {
      top: newBorderWithStyle,
      right: newBorderWithStyle,
      bottom: newBorderWithStyle,
      left: newBorderWithStyle
    } : {
      color: null,
      style: null,
      width: null,
      ...newBorderWithStyle
    }; // As radius is maintained separately to color, style, and width
    // maintain its value. Undefined values here will be cleaned when
    // global styles are saved.

    setBorder({
      radius: border === null || border === void 0 ? void 0 : border.radius,
      ...updatedBorder
    });
  }, [setBorder]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanel, {
    label: (0,external_wp_i18n_namespaceObject.__)('Border'),
    resetAll: resetAll
  }, (showBorderWidth || showBorderColor) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: () => (0,external_wp_components_namespaceObject.__experimentalIsDefinedBorder)(userBorderStyles),
    label: (0,external_wp_i18n_namespaceObject.__)('Border'),
    onDeselect: () => resetBorder(),
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalBorderBoxControl, {
    colors: colors,
    enableAlpha: true,
    enableStyle: showBorderStyle,
    onChange: onBorderChange,
    popoverOffset: 40,
    popoverPlacement: "left-start",
    value: border,
    __experimentalIsRenderedInSidebar: true,
    size: '__unstable-large'
  })), showBorderRadius && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: hasBorderRadius,
    label: (0,external_wp_i18n_namespaceObject.__)('Radius'),
    onDeselect: () => setBorderRadius(undefined),
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalBorderRadiusControl, {
    values: borderRadiusValues,
    onChange: value => {
      setBorderRadius(value || undefined);
    }
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/color-utils.js
/**
 * Internal dependencies
 */

function useHasColorPanel(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return supports.includes('color') || supports.includes('backgroundColor') || supports.includes('background') || supports.includes('linkColor');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/position-center.js


/**
 * WordPress dependencies
 */

const positionCenter = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M7 9v6h10V9H7zM5 19.8h14v-1.5H5v1.5zM5 4.3v1.5h14V4.3H5z"
}));
/* harmony default export */ var position_center = (positionCenter);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/stretch-wide.js


/**
 * WordPress dependencies
 */

const stretchWide = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M5 9v6h14V9H5zm11-4.8H8v1.5h8V4.2zM8 19.8h8v-1.5H8v1.5z"
}));
/* harmony default export */ var stretch_wide = (stretchWide);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/dimensions-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  useGlobalSetting: dimensions_panel_useGlobalSetting,
  useGlobalStyle: dimensions_panel_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const AXIAL_SIDES = ['horizontal', 'vertical'];
function useHasDimensionsPanel(name) {
  const hasContentSize = useHasContentSize(name);
  const hasWideSize = useHasWideSize(name);
  const hasPadding = useHasPadding(name);
  const hasMargin = useHasMargin(name);
  const hasGap = useHasGap(name);
  const hasMinHeight = useHasMinHeight(name);
  return hasContentSize || hasWideSize || hasPadding || hasMargin || hasGap || hasMinHeight;
}

function useHasContentSize(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('layout.contentSize', name);
  return settings && supports.includes('contentSize');
}

function useHasWideSize(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('layout.wideSize', name);
  return settings && supports.includes('wideSize');
}

function useHasPadding(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('spacing.padding', name);
  return settings && supports.includes('padding');
}

function useHasMargin(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('spacing.margin', name);
  return settings && supports.includes('margin');
}

function useHasGap(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('spacing.blockGap', name);
  return settings && supports.includes('blockGap');
}

function useHasMinHeight(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [settings] = dimensions_panel_useGlobalSetting('dimensions.minHeight', name);
  return settings && supports.includes('minHeight');
}

function useHasSpacingPresets() {
  var _ref, _ref2;

  const [settings] = dimensions_panel_useGlobalSetting('spacing.spacingSizes');
  const {
    custom,
    theme,
    default: defaultPresets
  } = settings || {};
  const presets = (_ref = (_ref2 = custom !== null && custom !== void 0 ? custom : theme) !== null && _ref2 !== void 0 ? _ref2 : defaultPresets) !== null && _ref !== void 0 ? _ref : [];
  return settings && presets.length > 0;
}

function filterValuesBySides(values, sides) {
  if (!sides) {
    // If no custom side configuration all sides are opted into by default.
    return values;
  } // Only include sides opted into within filtered values.


  const filteredValues = {};
  sides.forEach(side => {
    if (side === 'vertical') {
      filteredValues.top = values.top;
      filteredValues.bottom = values.bottom;
    }

    if (side === 'horizontal') {
      filteredValues.left = values.left;
      filteredValues.right = values.right;
    }

    filteredValues[side] = values[side];
  });
  return filteredValues;
}

function splitStyleValue(value) {
  // Check for shorthand value (a string value).
  if (value && typeof value === 'string') {
    // Convert to value for individual sides for BoxControl.
    return {
      top: value,
      right: value,
      bottom: value,
      left: value
    };
  }

  return value;
}

function splitGapValue(value) {
  // Check for shorthand value (a string value).
  if (value && typeof value === 'string') {
    // If the value is a string, treat it as a single side (top) for the spacing controls.
    return {
      top: value
    };
  }

  if (value) {
    return { ...value,
      right: value === null || value === void 0 ? void 0 : value.left,
      bottom: value === null || value === void 0 ? void 0 : value.top
    };
  }

  return value;
} // Props for managing `layout.contentSize`.


function useContentSizeProps(name) {
  const [contentSizeValue, setContentSizeValue] = dimensions_panel_useGlobalSetting('layout.contentSize', name);
  const [userSetContentSizeValue] = dimensions_panel_useGlobalSetting('layout.contentSize', name, 'user');

  const hasUserSetContentSizeValue = () => !!userSetContentSizeValue;

  const resetContentSizeValue = () => setContentSizeValue('');

  return {
    contentSizeValue,
    setContentSizeValue,
    hasUserSetContentSizeValue,
    resetContentSizeValue
  };
} // Props for managing `layout.wideSize`.


function useWideSizeProps(name) {
  const [wideSizeValue, setWideSizeValue] = dimensions_panel_useGlobalSetting('layout.wideSize', name);
  const [userSetWideSizeValue] = dimensions_panel_useGlobalSetting('layout.wideSize', name, 'user');

  const hasUserSetWideSizeValue = () => !!userSetWideSizeValue;

  const resetWideSizeValue = () => setWideSizeValue('');

  return {
    wideSizeValue,
    setWideSizeValue,
    hasUserSetWideSizeValue,
    resetWideSizeValue
  };
} // Props for managing `spacing.padding`.


function usePaddingProps(name) {
  let variation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  const prefix = variation ? `variations.${variation}.` : '';
  const [rawPadding, setRawPadding] = dimensions_panel_useGlobalStyle(prefix + 'spacing.padding', name);
  const paddingValues = splitStyleValue(rawPadding);
  const paddingSides = (0,external_wp_blockEditor_namespaceObject.__experimentalUseCustomSides)(name, 'padding');
  const isAxialPadding = paddingSides && paddingSides.some(side => AXIAL_SIDES.includes(side));

  const setPaddingValues = newPaddingValues => {
    const padding = filterValuesBySides(newPaddingValues, paddingSides);
    setRawPadding(padding);
  };

  const resetPaddingValue = () => setPaddingValues({});

  const [userSetPaddingValue] = dimensions_panel_useGlobalStyle(prefix + 'spacing.padding', name, 'user'); // The `hasPaddingValue` check does not need a parsed value, as `userSetPaddingValue` will be `undefined` if not set.

  const hasPaddingValue = () => !!userSetPaddingValue;

  return {
    paddingValues,
    paddingSides,
    isAxialPadding,
    setPaddingValues,
    resetPaddingValue,
    hasPaddingValue
  };
} // Props for managing `spacing.margin`.


function useMarginProps(name) {
  let variation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  const prefix = variation ? `variations.${variation}.` : '';
  const [rawMargin, setRawMargin] = dimensions_panel_useGlobalStyle(prefix + 'spacing.margin', name);
  const marginValues = splitStyleValue(rawMargin);
  const marginSides = (0,external_wp_blockEditor_namespaceObject.__experimentalUseCustomSides)(name, 'margin');
  const isAxialMargin = marginSides && marginSides.some(side => AXIAL_SIDES.includes(side));

  const setMarginValues = newMarginValues => {
    const margin = filterValuesBySides(newMarginValues, marginSides);
    setRawMargin(margin);
  };

  const resetMarginValue = () => setMarginValues({});

  const hasMarginValue = () => !!marginValues && Object.keys(marginValues).length;

  return {
    marginValues,
    marginSides,
    isAxialMargin,
    setMarginValues,
    resetMarginValue,
    hasMarginValue
  };
} // Props for managing `spacing.blockGap`.


function useBlockGapProps(name) {
  let variation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  const prefix = variation ? `variations.${variation}.` : '';
  const [gapValue, setGapValue] = dimensions_panel_useGlobalStyle(prefix + 'spacing.blockGap', name);
  const gapValues = splitGapValue(gapValue);
  const gapSides = (0,external_wp_blockEditor_namespaceObject.__experimentalUseCustomSides)(name, 'blockGap');
  const isAxialGap = gapSides && gapSides.some(side => AXIAL_SIDES.includes(side));

  const resetGapValue = () => setGapValue(undefined);

  const [userSetGapValue] = dimensions_panel_useGlobalStyle(prefix + 'spacing.blockGap', name, 'user');

  const hasGapValue = () => !!userSetGapValue;

  const setGapValues = nextBoxGapValue => {
    if (!nextBoxGapValue) {
      setGapValue(null);
    } // If axial gap is not enabled, treat the 'top' value as the shorthand gap value.


    if (!isAxialGap && nextBoxGapValue !== null && nextBoxGapValue !== void 0 && nextBoxGapValue.hasOwnProperty('top')) {
      setGapValue(nextBoxGapValue.top);
    } else {
      setGapValue({
        top: nextBoxGapValue === null || nextBoxGapValue === void 0 ? void 0 : nextBoxGapValue.top,
        left: nextBoxGapValue === null || nextBoxGapValue === void 0 ? void 0 : nextBoxGapValue.left
      });
    }
  };

  return {
    gapValue,
    gapValues,
    gapSides,
    isAxialGap,
    setGapValue,
    setGapValues,
    resetGapValue,
    hasGapValue
  };
} // Props for managing `dimensions.minHeight`.


function useMinHeightProps(name) {
  let variation = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  const prefix = variation ? `variations.${variation}.` : '';
  const [minHeightValue, setMinHeightValue] = dimensions_panel_useGlobalStyle(prefix + 'dimensions.minHeight', name);

  const resetMinHeightValue = () => setMinHeightValue(undefined);

  const hasMinHeightValue = () => !!minHeightValue;

  return {
    minHeightValue,
    setMinHeightValue,
    resetMinHeightValue,
    hasMinHeightValue
  };
}

function DimensionsPanel(_ref3) {
  let {
    name,
    variation = ''
  } = _ref3;
  const showContentSizeControl = useHasContentSize(name);
  const showWideSizeControl = useHasWideSize(name);
  const showPaddingControl = useHasPadding(name);
  const showMarginControl = useHasMargin(name);
  const showGapControl = useHasGap(name);
  const showMinHeightControl = useHasMinHeight(name);
  const showSpacingPresetsControl = useHasSpacingPresets();
  const units = (0,external_wp_components_namespaceObject.__experimentalUseCustomUnits)({
    availableUnits: dimensions_panel_useGlobalSetting('spacing.units', name)[0] || ['%', 'px', 'em', 'rem', 'vw']
  }); // Props for managing `layout.contentSize`.

  const {
    contentSizeValue,
    setContentSizeValue,
    hasUserSetContentSizeValue,
    resetContentSizeValue
  } = useContentSizeProps(name); // Props for managing `layout.wideSize`.

  const {
    wideSizeValue,
    setWideSizeValue,
    hasUserSetWideSizeValue,
    resetWideSizeValue
  } = useWideSizeProps(name); // Props for managing `spacing.padding`.

  const {
    paddingValues,
    paddingSides,
    isAxialPadding,
    setPaddingValues,
    resetPaddingValue,
    hasPaddingValue
  } = usePaddingProps(name, variation); // Props for managing `spacing.margin`.

  const {
    marginValues,
    marginSides,
    isAxialMargin,
    setMarginValues,
    resetMarginValue,
    hasMarginValue
  } = useMarginProps(name, variation); // Props for managing `spacing.blockGap`.

  const {
    gapValue,
    gapValues,
    gapSides,
    isAxialGap,
    setGapValue,
    setGapValues,
    resetGapValue,
    hasGapValue
  } = useBlockGapProps(name, variation); // Props for managing `dimensions.minHeight`.

  const {
    minHeightValue,
    setMinHeightValue,
    resetMinHeightValue,
    hasMinHeightValue
  } = useMinHeightProps(name, variation);

  const resetAll = () => {
    resetPaddingValue();
    resetMarginValue();
    resetGapValue();
    resetContentSizeValue();
    resetWideSizeValue();
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanel, {
    label: (0,external_wp_i18n_namespaceObject.__)('Dimensions'),
    resetAll: resetAll
  }, (showContentSizeControl || showWideSizeControl) && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "span-columns"
  }, (0,external_wp_i18n_namespaceObject.__)('Set the width of the main content area.')), showContentSizeControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: (0,external_wp_i18n_namespaceObject.__)('Content size'),
    hasValue: hasUserSetContentSizeValue,
    onDeselect: resetContentSizeValue,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "flex-end",
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalUnitControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Content'),
    labelPosition: "top",
    __unstableInputWidth: "80px",
    value: contentSizeValue || '',
    onChange: nextContentSize => {
      setContentSizeValue(nextContentSize);
    },
    units: units
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalView, null, (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
    icon: position_center
  })))), showWideSizeControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: (0,external_wp_i18n_namespaceObject.__)('Wide size'),
    hasValue: hasUserSetWideSizeValue,
    onDeselect: resetWideSizeValue,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "flex-end",
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalUnitControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Wide'),
    labelPosition: "top",
    __unstableInputWidth: "80px",
    value: wideSizeValue || '',
    onChange: nextWideSize => {
      setWideSizeValue(nextWideSize);
    },
    units: units
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalView, null, (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
    icon: stretch_wide
  })))), showPaddingControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: hasPaddingValue,
    label: (0,external_wp_i18n_namespaceObject.__)('Padding'),
    onDeselect: resetPaddingValue,
    isShownByDefault: true,
    className: classnames_default()({
      'tools-panel-item-spacing': showSpacingPresetsControl
    })
  }, !showSpacingPresetsControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalBoxControl, {
    values: paddingValues,
    onChange: setPaddingValues,
    label: (0,external_wp_i18n_namespaceObject.__)('Padding'),
    sides: paddingSides,
    units: units,
    allowReset: false,
    splitOnAxis: isAxialPadding
  }), showSpacingPresetsControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalSpacingSizesControl, {
    values: paddingValues,
    onChange: setPaddingValues,
    label: (0,external_wp_i18n_namespaceObject.__)('Padding'),
    sides: paddingSides,
    units: units,
    allowReset: false,
    splitOnAxis: isAxialPadding
  })), showMarginControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: hasMarginValue,
    label: (0,external_wp_i18n_namespaceObject.__)('Margin'),
    onDeselect: resetMarginValue,
    isShownByDefault: true,
    className: classnames_default()({
      'tools-panel-item-spacing': showSpacingPresetsControl
    })
  }, !showSpacingPresetsControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalBoxControl, {
    values: marginValues,
    onChange: setMarginValues,
    label: (0,external_wp_i18n_namespaceObject.__)('Margin'),
    sides: marginSides,
    units: units,
    allowReset: false,
    splitOnAxis: isAxialMargin
  }), showSpacingPresetsControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalSpacingSizesControl, {
    values: marginValues,
    onChange: setMarginValues,
    label: (0,external_wp_i18n_namespaceObject.__)('Margin'),
    sides: marginSides,
    units: units,
    allowReset: false,
    splitOnAxis: isAxialMargin
  })), showGapControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: hasGapValue,
    label: (0,external_wp_i18n_namespaceObject.__)('Block spacing'),
    onDeselect: resetGapValue,
    isShownByDefault: true,
    className: classnames_default()({
      'tools-panel-item-spacing': showSpacingPresetsControl
    })
  }, !showSpacingPresetsControl && (isAxialGap ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalBoxControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Block spacing'),
    min: 0,
    onChange: setGapValues,
    units: units,
    sides: gapSides,
    values: gapValues,
    allowReset: false,
    splitOnAxis: isAxialGap
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalUnitControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Block spacing'),
    __unstableInputWidth: "80px",
    min: 0,
    onChange: setGapValue,
    units: units,
    value: gapValue
  })), showSpacingPresetsControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalSpacingSizesControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Block spacing'),
    min: 0,
    onChange: setGapValues,
    sides: isAxialGap ? gapSides : ['top'] // Use 'top' as the shorthand property in non-axial configurations.
    ,
    values: gapValues,
    allowReset: false,
    splitOnAxis: isAxialGap
  })), showMinHeightControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    hasValue: hasMinHeightValue,
    label: (0,external_wp_i18n_namespaceObject.__)('Min. height'),
    onDeselect: resetMinHeightValue,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.HeightControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Min. height'),
    value: minHeightValue,
    onChange: setMinHeightValue
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/typography-panel.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



const {
  useGlobalSetting: typography_panel_useGlobalSetting,
  useGlobalStyle: typography_panel_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function useHasTypographyPanel(name) {
  const hasFontFamily = useHasFontFamilyControl(name);
  const hasLineHeight = useHasLineHeightControl(name);
  const hasFontAppearance = useHasAppearanceControl(name);
  const hasLetterSpacing = useHasLetterSpacingControl(name);
  const supports = getSupportedGlobalStylesPanels(name);
  return hasFontFamily || hasLineHeight || hasFontAppearance || hasLetterSpacing || supports.includes('fontSize');
}

function useHasFontFamilyControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const [fontFamiliesPerOrigin] = typography_panel_useGlobalSetting('typography.fontFamilies', name);
  const fontFamilies = (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.custom) || (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.theme) || (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.default);
  return supports.includes('fontFamily') && !!(fontFamilies !== null && fontFamilies !== void 0 && fontFamilies.length);
}

function useHasLineHeightControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return typography_panel_useGlobalSetting('typography.lineHeight', name)[0] && supports.includes('lineHeight');
}

function useHasAppearanceControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const hasFontStyles = typography_panel_useGlobalSetting('typography.fontStyle', name)[0] && supports.includes('fontStyle');
  const hasFontWeights = typography_panel_useGlobalSetting('typography.fontWeight', name)[0] && supports.includes('fontWeight');
  return hasFontStyles || hasFontWeights;
}

function useAppearanceControlLabel(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  const hasFontStyles = typography_panel_useGlobalSetting('typography.fontStyle', name)[0] && supports.includes('fontStyle');
  const hasFontWeights = typography_panel_useGlobalSetting('typography.fontWeight', name)[0] && supports.includes('fontWeight');

  if (!hasFontStyles) {
    return (0,external_wp_i18n_namespaceObject.__)('Font weight');
  }

  if (!hasFontWeights) {
    return (0,external_wp_i18n_namespaceObject.__)('Font style');
  }

  return (0,external_wp_i18n_namespaceObject.__)('Appearance');
}

function useHasLetterSpacingControl(name, element) {
  const setting = typography_panel_useGlobalSetting('typography.letterSpacing', name)[0];

  if (!setting) {
    return false;
  }

  if (!name && element === 'heading') {
    return true;
  }

  const supports = getSupportedGlobalStylesPanels(name);
  return supports.includes('letterSpacing');
}

function useHasTextTransformControl(name, element) {
  const setting = typography_panel_useGlobalSetting('typography.textTransform', name)[0];

  if (!setting) {
    return false;
  }

  if (!name && element === 'heading') {
    return true;
  }

  const supports = getSupportedGlobalStylesPanels(name);
  return supports.includes('textTransform');
}

function useHasTextDecorationControl(name, element) {
  // This is an exception for link elements.
  // We shouldn't allow other blocks or elements to set textDecoration
  // because this will be inherited by their children.
  return !name && element === 'link';
}

function useStyleWithReset(path, blockName) {
  const [style, setStyle] = typography_panel_useGlobalStyle(path, blockName);
  const [userStyle] = typography_panel_useGlobalStyle(path, blockName, 'user');

  const hasStyle = () => !!userStyle;

  const resetStyle = () => setStyle(undefined);

  return [style, setStyle, hasStyle, resetStyle];
}

function useFontSizeWithReset(path, blockName) {
  const [fontSize, setStyleCallback] = typography_panel_useGlobalStyle(path, blockName);
  const [userStyle] = typography_panel_useGlobalStyle(path, blockName, 'user');

  const hasFontSize = () => !!userStyle;

  const resetFontSize = () => setStyleCallback(undefined);

  const setFontSize = (newValue, metadata) => {
    if (!!(metadata !== null && metadata !== void 0 && metadata.slug)) {
      newValue = `var:preset|font-size|${metadata === null || metadata === void 0 ? void 0 : metadata.slug}`;
    }

    setStyleCallback(newValue);
  };

  return {
    fontSize,
    setFontSize,
    hasFontSize,
    resetFontSize
  };
}

function useFontAppearance(prefix, name) {
  const [fontStyle, setFontStyle] = typography_panel_useGlobalStyle(prefix + 'typography.fontStyle', name);
  const [userFontStyle] = typography_panel_useGlobalStyle(prefix + 'typography.fontStyle', name, 'user');
  const [fontWeight, setFontWeight] = typography_panel_useGlobalStyle(prefix + 'typography.fontWeight', name);
  const [userFontWeight] = typography_panel_useGlobalStyle(prefix + 'typography.fontWeight', name, 'user');

  const hasFontAppearance = () => !!userFontStyle || !!userFontWeight;

  const resetFontAppearance = () => {
    setFontStyle(undefined);
    setFontWeight(undefined);
  };

  return {
    fontStyle,
    setFontStyle,
    fontWeight,
    setFontWeight,
    hasFontAppearance,
    resetFontAppearance
  };
}

function TypographyPanel(_ref) {
  let {
    name,
    element,
    headingLevel,
    variation = ''
  } = _ref;
  const supports = getSupportedGlobalStylesPanels(name);
  let prefix = '';

  if (element === 'heading') {
    prefix = `elements.${headingLevel}.`;
  } else if (element && element !== 'text') {
    prefix = `elements.${element}.`;
  }

  if (variation) {
    prefix = prefix ? `variations.${variation}.${prefix}` : `variations.${variation}`;
  }

  const [fontSizesPerOrigin] = typography_panel_useGlobalSetting('typography.fontSizes', name);
  const fontSizes = (fontSizesPerOrigin === null || fontSizesPerOrigin === void 0 ? void 0 : fontSizesPerOrigin.custom) || (fontSizesPerOrigin === null || fontSizesPerOrigin === void 0 ? void 0 : fontSizesPerOrigin.theme) || (fontSizesPerOrigin === null || fontSizesPerOrigin === void 0 ? void 0 : fontSizesPerOrigin.default);
  const disableCustomFontSizes = !typography_panel_useGlobalSetting('typography.customFontSize', name)[0];
  const [fontFamiliesPerOrigin] = typography_panel_useGlobalSetting('typography.fontFamilies', name);
  const fontFamilies = (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.custom) || (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.theme) || (fontFamiliesPerOrigin === null || fontFamiliesPerOrigin === void 0 ? void 0 : fontFamiliesPerOrigin.default);
  const hasFontStyles = typography_panel_useGlobalSetting('typography.fontStyle', name)[0] && supports.includes('fontStyle');
  const hasFontWeights = typography_panel_useGlobalSetting('typography.fontWeight', name)[0] && supports.includes('fontWeight');
  const hasFontFamilyEnabled = useHasFontFamilyControl(name);
  const hasLineHeightEnabled = useHasLineHeightControl(name);
  const hasAppearanceControl = useHasAppearanceControl(name);
  const appearanceControlLabel = useAppearanceControlLabel(name);
  const hasLetterSpacingControl = useHasLetterSpacingControl(name, element);
  const hasTextTransformControl = useHasTextTransformControl(name, element);
  const hasTextDecorationControl = useHasTextDecorationControl(name, element);
  /* Disable font size controls when the option to style all headings is selected. */

  let hasFontSizeEnabled = supports.includes('fontSize');

  if (element === 'heading' && headingLevel === 'heading') {
    hasFontSizeEnabled = false;
  }

  const [fontFamily, setFontFamily, hasFontFamily, resetFontFamily] = useStyleWithReset(prefix + 'typography.fontFamily', name);
  const {
    fontSize,
    setFontSize,
    hasFontSize,
    resetFontSize
  } = useFontSizeWithReset(prefix + 'typography.fontSize', name);
  const {
    fontStyle,
    setFontStyle,
    fontWeight,
    setFontWeight,
    hasFontAppearance,
    resetFontAppearance
  } = useFontAppearance(prefix, name);
  const [lineHeight, setLineHeight, hasLineHeight, resetLineHeight] = useStyleWithReset(prefix + 'typography.lineHeight', name);
  const [letterSpacing, setLetterSpacing, hasLetterSpacing, resetLetterSpacing] = useStyleWithReset(prefix + 'typography.letterSpacing', name);
  const [textTransform, setTextTransform, hasTextTransform, resetTextTransform] = useStyleWithReset(prefix + 'typography.textTransform', name);
  const [textDecoration, setTextDecoration, hasTextDecoration, resetTextDecoration] = useStyleWithReset(prefix + 'typography.textDecoration', name);

  const resetAll = () => {
    resetFontFamily();
    resetFontSize();
    resetFontAppearance();
    resetLineHeight();
    resetLetterSpacing();
    resetTextTransform();
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanel, {
    label: (0,external_wp_i18n_namespaceObject.__)('Typography'),
    resetAll: resetAll
  }, hasFontFamilyEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    label: (0,external_wp_i18n_namespaceObject.__)('Font family'),
    hasValue: hasFontFamily,
    onDeselect: resetFontFamily,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalFontFamilyControl, {
    fontFamilies: fontFamilies,
    value: fontFamily,
    onChange: setFontFamily,
    size: "__unstable-large",
    __nextHasNoMarginBottom: true
  })), hasFontSizeEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    label: (0,external_wp_i18n_namespaceObject.__)('Font size'),
    hasValue: hasFontSize,
    onDeselect: resetFontSize,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FontSizePicker, {
    value: fontSize,
    onChange: setFontSize,
    fontSizes: fontSizes,
    disableCustomFontSizes: disableCustomFontSizes,
    withReset: false,
    withSlider: true,
    size: "__unstable-large",
    __nextHasNoMarginBottom: true
  })), hasAppearanceControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: appearanceControlLabel,
    hasValue: hasFontAppearance,
    onDeselect: resetFontAppearance,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalFontAppearanceControl, {
    value: {
      fontStyle,
      fontWeight
    },
    onChange: _ref2 => {
      let {
        fontStyle: newFontStyle,
        fontWeight: newFontWeight
      } = _ref2;
      setFontStyle(newFontStyle);
      setFontWeight(newFontWeight);
    },
    hasFontStyles: hasFontStyles,
    hasFontWeights: hasFontWeights,
    size: "__unstable-large",
    __nextHasNoMarginBottom: true
  })), hasLineHeightEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: (0,external_wp_i18n_namespaceObject.__)('Line height'),
    hasValue: hasLineHeight,
    onDeselect: resetLineHeight,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.LineHeightControl, {
    __nextHasNoMarginBottom: true,
    __unstableInputWidth: "auto",
    value: lineHeight,
    onChange: setLineHeight,
    size: "__unstable-large"
  })), hasLetterSpacingControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: (0,external_wp_i18n_namespaceObject.__)('Letter spacing'),
    hasValue: hasLetterSpacing,
    onDeselect: resetLetterSpacing,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLetterSpacingControl, {
    value: letterSpacing,
    onChange: setLetterSpacing,
    size: "__unstable-large",
    __unstableInputWidth: "auto"
  })), hasTextTransformControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    label: (0,external_wp_i18n_namespaceObject.__)('Letter case'),
    hasValue: hasTextTransform,
    onDeselect: resetTextTransform,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalTextTransformControl, {
    value: textTransform,
    onChange: setTextTransform,
    showNone: true,
    isBlock: true,
    size: "__unstable-large",
    __nextHasNoMarginBottom: true
  })), hasTextDecorationControl && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    className: "single-column",
    label: (0,external_wp_i18n_namespaceObject.__)('Text decoration'),
    hasValue: hasTextDecoration,
    onDeselect: resetTextDecoration,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalTextDecorationControl, {
    value: textDecoration,
    onChange: setTextDecoration,
    size: "__unstable-large",
    __unstableInputWidth: "auto"
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/variations-panel.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function getCoreBlockStyles(blockStyles) {
  return blockStyles === null || blockStyles === void 0 ? void 0 : blockStyles.filter(style => style.source === 'block');
}

function useHasVariationsPanel(name) {
  let parentMenu = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  const isInsideVariationsPanel = parentMenu.includes('variations');
  const blockStyles = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlockStyles
    } = select(external_wp_blocks_namespaceObject.store);
    return getBlockStyles(name);
  }, [name]);
  const coreBlockStyles = getCoreBlockStyles(blockStyles);
  return !!(coreBlockStyles !== null && coreBlockStyles !== void 0 && coreBlockStyles.length) && !isInsideVariationsPanel;
}
function VariationsPanel(_ref) {
  let {
    name
  } = _ref;
  const blockStyles = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlockStyles
    } = select(external_wp_blocks_namespaceObject.store);
    return getBlockStyles(name);
  }, [name]);
  const coreBlockStyles = getCoreBlockStyles(blockStyles);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, {
    isBordered: true,
    isSeparated: true
  }, coreBlockStyles.map((style, index) => {
    if (style !== null && style !== void 0 && style.isDefault) {
      return null;
    }

    return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
      key: index,
      path: '/blocks/' + encodeURIComponent(name) + '/variations/' + encodeURIComponent(style.name),
      "aria-label": style.label
    }, style.label);
  }));
}
function VariationPanel(_ref2) {
  let {
    blockName,
    styleName
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(context_menu, {
    parentMenu: '/blocks/' + encodeURIComponent(blockName) + '/variations/' + encodeURIComponent(styleName),
    name: blockName
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/header.js


/**
 * WordPress dependencies
 */




function ScreenHeader(_ref) {
  let {
    title,
    description
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 0
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalView, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    marginBottom: 0,
    paddingX: 4,
    paddingY: 3
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    spacing: 2
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorToParentButton, {
    style: // TODO: This style override is also used in ToolsPanelHeader.
    // It should be supported out-of-the-box by Button.
    {
      minWidth: 24,
      padding: 0
    },
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_right : chevron_left,
    isSmall: true,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous view')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    level: 5
  }, title))))), description && (0,external_wp_element_namespaceObject.createElement)("p", {
    className: "edit-site-global-styles-header__description"
  }, description));
}

/* harmony default export */ var header = (ScreenHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/block-preview-panel.js


/**
 * WordPress dependencies
 */




const BlockPreviewPanel = _ref => {
  var _getBlockType;

  let {
    name,
    variation = ''
  } = _ref;
  const blockExample = (_getBlockType = (0,external_wp_blocks_namespaceObject.getBlockType)(name)) === null || _getBlockType === void 0 ? void 0 : _getBlockType.example;
  const blockExampleWithVariation = { ...blockExample,
    attributes: { ...(blockExample === null || blockExample === void 0 ? void 0 : blockExample.attributes),
      className: variation
    }
  };
  const blocks = blockExample && (0,external_wp_blocks_namespaceObject.getBlockFromExample)(name, variation ? blockExampleWithVariation : blockExample);
  const viewportWidth = (blockExample === null || blockExample === void 0 ? void 0 : blockExample.viewportWidth) || null;
  const previewHeight = '150px';
  return !blockExample ? null : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    marginX: 4,
    marginBottom: 4
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles__block-preview-panel",
    style: {
      maxHeight: previewHeight,
      boxSizing: 'initial'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockPreview, {
    blocks: blocks,
    viewportWidth: viewportWidth,
    minHeight: previewHeight,
    additionalStyles: [{
      css: `
								body{
									min-height:${previewHeight};
									display:flex;align-items:center;justify-content:center;
								}
							`
    }]
  })));
};

/* harmony default export */ var block_preview_panel = (BlockPreviewPanel);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/subtitle.js


/**
 * WordPress dependencies
 */


function Subtitle(_ref) {
  let {
    children
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    className: "edit-site-global-styles-subtitle",
    level: 2
  }, children);
}

/* harmony default export */ var subtitle = (Subtitle);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-variations.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





function ScreenVariations(_ref) {
  let {
    name,
    path = ''
  } = _ref;
  const hasVariationsPanel = useHasVariationsPanel(name, path);

  if (!hasVariationsPanel) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-variations"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3
  }, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('Style Variations')), (0,external_wp_element_namespaceObject.createElement)(VariationsPanel, {
    name: name
  })));
}
function ScreenVariation(_ref2) {
  let {
    blockName,
    style
  } = _ref2;
  const {
    name: styleName,
    label: styleLabel
  } = style;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: styleLabel
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: blockName,
    variation: `is-style-${styleName}`
  }), (0,external_wp_element_namespaceObject.createElement)(VariationPanel, {
    blockName: blockName,
    styleName: styleName
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/shadow-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




const {
  useGlobalSetting: shadow_panel_useGlobalSetting,
  useGlobalStyle: shadow_panel_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function useHasShadowControl(name) {
  const supports = getSupportedGlobalStylesPanels(name);
  return supports.includes('shadow');
}
function ShadowPanel(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const [shadow, setShadow] = shadow_panel_useGlobalStyle(`${prefix}shadow`, name);
  const [userShadow] = shadow_panel_useGlobalStyle(`${prefix}shadow`, name, 'user');

  const hasShadow = () => !!userShadow;

  const resetShadow = () => setShadow(undefined);

  const resetAll = (0,external_wp_element_namespaceObject.useCallback)(() => resetShadow(undefined), [resetShadow]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanel, {
    label: (0,external_wp_i18n_namespaceObject.__)('Shadow'),
    resetAll: resetAll
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToolsPanelItem, {
    label: (0,external_wp_i18n_namespaceObject.__)('Shadow'),
    hasValue: hasShadow,
    onDeselect: resetShadow,
    isShownByDefault: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, {
    isBordered: true,
    isSeparated: true
  }, (0,external_wp_element_namespaceObject.createElement)(ShadowPopover, {
    shadow: shadow,
    onShadowChange: setShadow
  }))));
}

const ShadowPopover = _ref2 => {
  let {
    shadow,
    onShadowChange
  } = _ref2;
  const popoverProps = {
    placement: 'left-start',
    offset: 36,
    shift: true
  };
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    className: "edit-site-global-styles__shadow-dropdown",
    renderToggle: renderShadowToggle(),
    renderContent: () => (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalDropdownContentWrapper, {
      paddingSize: "medium"
    }, (0,external_wp_element_namespaceObject.createElement)(ShadowPopoverContainer, {
      shadow: shadow,
      onShadowChange: onShadowChange
    }))
  });
};

function renderShadowToggle() {
  return _ref3 => {
    let {
      onToggle,
      isOpen
    } = _ref3;
    const toggleProps = {
      onClick: onToggle,
      className: classnames_default()({
        'is-open': isOpen
      }),
      'aria-expanded': isOpen
    };
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, toggleProps, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
      justify: "flex-start"
    }, (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
      icon: library_shadow,
      size: 24
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
      className: "edit-site-global-styles__shadow-label"
    }, (0,external_wp_i18n_namespaceObject.__)('Shadow'))));
  };
}

function ShadowPopoverContainer(_ref4) {
  let {
    shadow,
    onShadowChange
  } = _ref4;
  const [defaultShadows] = shadow_panel_useGlobalSetting('shadow.presets.default');
  const [themeShadows] = shadow_panel_useGlobalSetting('shadow.presets.theme');
  const [defaultPresetsEnabled] = shadow_panel_useGlobalSetting('shadow.defaultPresets');
  const shadows = [...(defaultPresetsEnabled ? defaultShadows : []), ...(themeShadows || [])];
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles__shadow-panel"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 4
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    level: 5
  }, (0,external_wp_i18n_namespaceObject.__)('Shadow')), (0,external_wp_element_namespaceObject.createElement)(ShadowPresets, {
    presets: shadows,
    activeShadow: shadow,
    onSelect: onShadowChange
  })));
}

function ShadowPresets(_ref5) {
  let {
    presets,
    activeShadow,
    onSelect
  } = _ref5;
  return !presets ? null : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalGrid, {
    columns: 6,
    gap: 0,
    align: "center",
    justify: "center"
  }, presets.map(_ref6 => {
    let {
      name,
      slug,
      shadow
    } = _ref6;
    return (0,external_wp_element_namespaceObject.createElement)(ShadowIndicator, {
      key: slug,
      label: name,
      isActive: shadow === activeShadow,
      onSelect: () => onSelect(shadow === activeShadow ? undefined : shadow),
      shadow: shadow
    });
  }));
}

function ShadowIndicator(_ref7) {
  let {
    label,
    isActive,
    onSelect,
    shadow
  } = _ref7;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles__shadow-indicator-wrapper"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-site-global-styles__shadow-indicator",
    onClick: onSelect,
    label: label,
    style: {
      boxShadow: shadow
    },
    showTooltip: true
  }, isActive && (0,external_wp_element_namespaceObject.createElement)(build_module_icon, {
    icon: library_check
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/context-menu.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */











function ContextMenu(_ref) {
  let {
    name,
    parentMenu = ''
  } = _ref;
  const hasTypographyPanel = useHasTypographyPanel(name);
  const hasColorPanel = useHasColorPanel(name);
  const hasBorderPanel = useHasBorderPanel(name);
  const hasEffectsPanel = useHasShadowControl(name);
  const hasDimensionsPanel = useHasDimensionsPanel(name);
  const hasLayoutPanel = hasDimensionsPanel;
  const hasVariationsPanel = useHasVariationsPanel(name, parentMenu);
  const {
    canEditCSS
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _globalStyles$_links$, _globalStyles$_links;

    const {
      getEntityRecord,
      __experimentalGetCurrentGlobalStylesId
    } = select(external_wp_coreData_namespaceObject.store);

    const globalStylesId = __experimentalGetCurrentGlobalStylesId();

    const globalStyles = globalStylesId ? getEntityRecord('root', 'globalStyles', globalStylesId) : undefined;
    return {
      canEditCSS: (_globalStyles$_links$ = !!(globalStyles !== null && globalStyles !== void 0 && (_globalStyles$_links = globalStyles._links) !== null && _globalStyles$_links !== void 0 && _globalStyles$_links['wp:action-edit-css'])) !== null && _globalStyles$_links$ !== void 0 ? _globalStyles$_links$ : false
    };
  }, []);
  const isBlocksPanel = parentMenu.includes('blocks') && !parentMenu.includes('variations');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, hasTypographyPanel && (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    icon: library_typography,
    path: parentMenu + '/typography',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Typography styles')
  }, (0,external_wp_i18n_namespaceObject.__)('Typography')), hasColorPanel && (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    icon: library_color,
    path: parentMenu + '/colors',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Colors styles')
  }, (0,external_wp_i18n_namespaceObject.__)('Colors')), hasBorderPanel && (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    icon: library_border,
    path: parentMenu + '/border',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Border')
  }, (0,external_wp_i18n_namespaceObject.__)('Border')), hasEffectsPanel && (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    icon: library_shadow,
    path: parentMenu + '/effects',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Shadow')
  }, (0,external_wp_i18n_namespaceObject.__)('Shadow')), hasLayoutPanel && (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    icon: library_layout,
    path: parentMenu + '/layout',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Layout styles')
  }, (0,external_wp_i18n_namespaceObject.__)('Layout')), hasVariationsPanel && (0,external_wp_element_namespaceObject.createElement)(ScreenVariations, {
    name: name,
    path: parentMenu
  }), isBlocksPanel && canEditCSS && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardDivider, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    as: "p",
    paddingTop: 2,
    marginBottom: 4
  }, (0,external_wp_i18n_namespaceObject.__)('Add your own CSS to customize the block appearance.')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + '/css',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Additional block CSS')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "space-between"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_i18n_namespaceObject.__)('Additional block CSS')), (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
  }))))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardDivider, null))));
}

/* harmony default export */ var context_menu = (ContextMenu);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/preview.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


const {
  useGlobalSetting: preview_useGlobalSetting,
  useGlobalStyle: preview_useGlobalStyle,
  useGlobalStylesOutput
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const firstFrame = {
  start: {
    scale: 1,
    opacity: 1
  },
  hover: {
    scale: 0,
    opacity: 0
  }
};
const midFrame = {
  hover: {
    opacity: 1
  },
  start: {
    opacity: 0.5
  }
};
const secondFrame = {
  hover: {
    scale: 1,
    opacity: 1
  },
  start: {
    scale: 0,
    opacity: 0
  }
};
const normalizedWidth = 248;
const normalizedHeight = 152;
const normalizedColorSwatchSize = 32;

const StylesPreview = _ref => {
  let {
    label,
    isFocused,
    withHoverView
  } = _ref;
  const [fontWeight] = preview_useGlobalStyle('typography.fontWeight');
  const [fontFamily = 'serif'] = preview_useGlobalStyle('typography.fontFamily');
  const [headingFontFamily = fontFamily] = preview_useGlobalStyle('elements.h1.typography.fontFamily');
  const [headingFontWeight = fontWeight] = preview_useGlobalStyle('elements.h1.typography.fontWeight');
  const [textColor = 'black'] = preview_useGlobalStyle('color.text');
  const [headingColor = textColor] = preview_useGlobalStyle('elements.h1.color.text');
  const [backgroundColor = 'white'] = preview_useGlobalStyle('color.background');
  const [gradientValue] = preview_useGlobalStyle('color.gradient');
  const [styles] = useGlobalStylesOutput();
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const [coreColors] = preview_useGlobalSetting('color.palette.core');
  const [themeColors] = preview_useGlobalSetting('color.palette.theme');
  const [customColors] = preview_useGlobalSetting('color.palette.custom');
  const [isHovered, setIsHovered] = (0,external_wp_element_namespaceObject.useState)(false);
  const [containerResizeListener, {
    width
  }] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const ratio = width ? width / normalizedWidth : 1;
  const paletteColors = (themeColors !== null && themeColors !== void 0 ? themeColors : []).concat(customColors !== null && customColors !== void 0 ? customColors : []).concat(coreColors !== null && coreColors !== void 0 ? coreColors : []);
  const highlightedColors = paletteColors.filter( // we exclude these two colors because they are already visible in the preview.
  _ref2 => {
    let {
      color
    } = _ref2;
    return color !== backgroundColor && color !== headingColor;
  }).slice(0, 2); // Reset leaked styles from WP common.css and remove main content layout padding and border.

  const editorStyles = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (styles) {
      return [...styles, {
        css: 'html{overflow:hidden}body{min-width: 0;padding: 0;border: none;}',
        isGlobalStyles: true
      }];
    }

    return styles;
  }, [styles]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableIframe, {
    className: "edit-site-global-styles-preview__iframe",
    head: (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: editorStyles
    }),
    style: {
      height: normalizedHeight * ratio,
      visibility: !width ? 'hidden' : 'visible'
    },
    onMouseEnter: () => setIsHovered(true),
    onMouseLeave: () => setIsHovered(false),
    tabIndex: -1
  }, containerResizeListener, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    style: {
      height: normalizedHeight * ratio,
      width: '100%',
      background: gradientValue !== null && gradientValue !== void 0 ? gradientValue : backgroundColor,
      cursor: 'pointer'
    },
    initial: "start",
    animate: (isHovered || isFocused) && !disableMotion && label ? 'hover' : 'start'
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: firstFrame,
    style: {
      height: '100%',
      overflow: 'hidden'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    spacing: 10 * ratio,
    justify: "center",
    style: {
      height: '100%',
      overflow: 'hidden'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    style: {
      fontFamily: headingFontFamily,
      fontSize: 65 * ratio,
      color: headingColor,
      fontWeight: headingFontWeight
    },
    animate: {
      scale: 1,
      opacity: 1
    },
    initial: {
      scale: 0.1,
      opacity: 0
    },
    transition: {
      delay: 0.3,
      type: 'tween'
    }
  }, "Aa"), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 4 * ratio
  }, highlightedColors.map((_ref3, index) => {
    let {
      slug,
      color
    } = _ref3;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
      key: slug,
      style: {
        height: normalizedColorSwatchSize * ratio,
        width: normalizedColorSwatchSize * ratio,
        background: color,
        borderRadius: normalizedColorSwatchSize * ratio / 2
      },
      animate: {
        scale: 1,
        opacity: 1
      },
      initial: {
        scale: 0.1,
        opacity: 0
      },
      transition: {
        delay: index === 1 ? 0.2 : 0.1
      }
    });
  })))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: withHoverView && midFrame,
    style: {
      height: '100%',
      width: '100%',
      position: 'absolute',
      top: 0,
      overflow: 'hidden',
      filter: 'blur(60px)',
      opacity: 0.1
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    spacing: 0,
    justify: "flex-start",
    style: {
      height: '100%',
      overflow: 'hidden'
    }
  }, paletteColors.slice(0, 4).map((_ref4, index) => {
    let {
      color
    } = _ref4;
    return (0,external_wp_element_namespaceObject.createElement)("div", {
      key: index,
      style: {
        height: '100%',
        background: color,
        flexGrow: 1
      }
    });
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    variants: secondFrame,
    style: {
      height: '100%',
      width: '100%',
      overflow: 'hidden',
      position: 'absolute',
      top: 0
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3 * ratio,
    justify: "center",
    style: {
      height: '100%',
      overflow: 'hidden',
      padding: 10 * ratio,
      boxSizing: 'border-box'
    }
  }, label && (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      fontSize: 40 * ratio,
      fontFamily: headingFontFamily,
      color: headingColor,
      fontWeight: headingFontWeight,
      lineHeight: '1em',
      textAlign: 'center'
    }
  }, label)))));
};

/* harmony default export */ var preview = (StylesPreview);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-root.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */







function ScreenRoot() {
  const {
    useGlobalStyle
  } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
  const [customCSS] = useGlobalStyle('css');
  const {
    variations,
    canEditCSS
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _globalStyles$_links$, _globalStyles$_links;

    const {
      getEntityRecord,
      __experimentalGetCurrentGlobalStylesId,
      __experimentalGetCurrentThemeGlobalStylesVariations
    } = select(external_wp_coreData_namespaceObject.store);

    const globalStylesId = __experimentalGetCurrentGlobalStylesId();

    const globalStyles = globalStylesId ? getEntityRecord('root', 'globalStyles', globalStylesId) : undefined;
    return {
      variations: __experimentalGetCurrentThemeGlobalStylesVariations(),
      canEditCSS: (_globalStyles$_links$ = !!(globalStyles !== null && globalStyles !== void 0 && (_globalStyles$_links = globalStyles._links) !== null && _globalStyles$_links !== void 0 && _globalStyles$_links['wp:action-edit-css'])) !== null && _globalStyles$_links$ !== void 0 ? _globalStyles$_links$ : false
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
    size: "small"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 4
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardMedia, null, (0,external_wp_element_namespaceObject.createElement)(preview, null))), !!(variations !== null && variations !== void 0 && variations.length) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: "/variations",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Browse styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "space-between"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_i18n_namespaceObject.__)('Browse styles')), (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
  })))), (0,external_wp_element_namespaceObject.createElement)(context_menu, null))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardDivider, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    as: "p",
    paddingTop: 2
    /*
     * 13px matches the text inset of the NavigationButton (12px padding, plus the width of the button's border).
     * This is an ad hoc override for this instance and the Addtional CSS option below. Other options for matching the
     * the nav button inset should be looked at before reusing further.
     */
    ,
    paddingX: "13px",
    marginBottom: 4
  }, (0,external_wp_i18n_namespaceObject.__)('Customize the appearance of specific blocks for the whole site.')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: "/blocks",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Blocks styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "space-between"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_i18n_namespaceObject.__)('Blocks')), (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
  }))))), canEditCSS && !!customCSS && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardDivider, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    as: "p",
    paddingTop: 2,
    paddingX: "13px",
    marginBottom: 4
  }, (0,external_wp_i18n_namespaceObject.__)('Add your own CSS to customize the appearance and layout of your site.')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, null, (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: "/css",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Additional CSS')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "space-between"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_i18n_namespaceObject.__)('Additional CSS')), (0,external_wp_element_namespaceObject.createElement)(IconWithCurrentColor, {
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? chevron_left : chevron_right
  })))))));
}

/* harmony default export */ var screen_root = (ScreenRoot);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-block-list.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */









function useSortedBlockTypes() {
  const blockItems = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blocks_namespaceObject.store).getBlockTypes(), []); // Ensure core blocks are prioritized in the returned results,
  // because third party blocks can be registered earlier than
  // the core blocks (usually by using the `init` action),
  // thus affecting the display order.
  // We don't sort reusable blocks as they are handled differently.

  const groupByType = (blocks, block) => {
    const {
      core,
      noncore
    } = blocks;
    const type = block.name.startsWith('core/') ? core : noncore;
    type.push(block);
    return blocks;
  };

  const {
    core: coreItems,
    noncore: nonCoreItems
  } = blockItems.reduce(groupByType, {
    core: [],
    noncore: []
  });
  return [...coreItems, ...nonCoreItems];
}

function BlockMenuItem(_ref) {
  let {
    block
  } = _ref;
  const hasTypographyPanel = useHasTypographyPanel(block.name);
  const hasColorPanel = useHasColorPanel(block.name);
  const hasBorderPanel = useHasBorderPanel(block.name);
  const hasDimensionsPanel = useHasDimensionsPanel(block.name);
  const hasLayoutPanel = hasBorderPanel || hasDimensionsPanel;
  const hasVariationsPanel = useHasVariationsPanel(block.name);
  const hasBlockMenuItem = hasTypographyPanel || hasColorPanel || hasLayoutPanel || hasVariationsPanel;

  if (!hasBlockMenuItem) {
    return null;
  }

  const navigationButtonLabel = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: is the name of a block e.g., 'Image' or 'Table'.
  (0,external_wp_i18n_namespaceObject.__)('%s block styles'), block.title);
  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: '/blocks/' + encodeURIComponent(block.name),
    "aria-label": navigationButtonLabel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: block.icon
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, block.title)));
}

function ScreenBlockList() {
  const sortedBlockTypes = useSortedBlockTypes();
  const [filterValue, setFilterValue] = (0,external_wp_element_namespaceObject.useState)('');
  const debouncedSpeak = (0,external_wp_compose_namespaceObject.useDebounce)(external_wp_a11y_namespaceObject.speak, 500);
  const isMatchingSearchTerm = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blocks_namespaceObject.store).isMatchingSearchTerm, []);
  const filteredBlockTypes = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!filterValue) {
      return sortedBlockTypes;
    }

    return sortedBlockTypes.filter(blockType => isMatchingSearchTerm(blockType, filterValue));
  }, [filterValue, sortedBlockTypes, isMatchingSearchTerm]);
  const blockTypesListRef = (0,external_wp_element_namespaceObject.useRef)(); // Announce search results on change

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!filterValue) {
      return;
    } // We extract the results from the wrapper div's `ref` because
    // filtered items can contain items that will eventually not
    // render and there is no reliable way to detect when a child
    // will return `null`.
    // TODO: We should find a better way of handling this as it's
    // fragile and depends on the number of rendered elements of `BlockMenuItem`,
    // which is now one.
    // @see https://github.com/WordPress/gutenberg/pull/39117#discussion_r816022116


    const count = blockTypesListRef.current.childElementCount;
    const resultsFoundMessage = (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %d: number of results. */
    (0,external_wp_i18n_namespaceObject._n)('%d result found.', '%d results found.', count), count);
    debouncedSpeak(resultsFoundMessage, count);
  }, [filterValue, debouncedSpeak]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Blocks'),
    description: (0,external_wp_i18n_namespaceObject.__)('Customize the appearance of specific blocks and for the whole site.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SearchControl, {
    __nextHasNoMarginBottom: true,
    className: "edit-site-block-types-search",
    onChange: setFilterValue,
    value: filterValue,
    label: (0,external_wp_i18n_namespaceObject.__)('Search for blocks'),
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Search')
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    ref: blockTypesListRef,
    className: "edit-site-block-types-item-list"
  }, filteredBlockTypes.map(block => (0,external_wp_element_namespaceObject.createElement)(BlockMenuItem, {
    block: block,
    key: 'menu-itemblock-' + block.name
  }))));
}

/* harmony default export */ var screen_block_list = (ScreenBlockList);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-block.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





function ScreenBlock(_ref) {
  let {
    name
  } = _ref;
  const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(name);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: blockType.title
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name
  }), (0,external_wp_element_namespaceObject.createElement)(context_menu, {
    parentMenu: '/blocks/' + encodeURIComponent(name),
    name: name
  }));
}

/* harmony default export */ var screen_block = (ScreenBlock);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/utils.js
/**
 *
 * @param {string} variation The variation name.
 *
 * @return {string} The variation class name.
 */
function getVariationClassName(variation) {
  if (!variation) {
    return '';
  }

  return `is-style-${variation}`;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-typography.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */








const {
  useGlobalStyle: screen_typography_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function Item(_ref) {
  let {
    name,
    parentMenu,
    element,
    label
  } = _ref;
  const hasSupport = !name;
  const prefix = element === 'text' || !element ? '' : `elements.${element}.`;
  const extraStyles = element === 'link' ? {
    textDecoration: 'underline'
  } : {};
  const [fontFamily] = screen_typography_useGlobalStyle(prefix + 'typography.fontFamily', name);
  const [fontStyle] = screen_typography_useGlobalStyle(prefix + 'typography.fontStyle', name);
  const [fontWeight] = screen_typography_useGlobalStyle(prefix + 'typography.fontWeight', name);
  const [letterSpacing] = screen_typography_useGlobalStyle(prefix + 'typography.letterSpacing', name);
  const [backgroundColor] = screen_typography_useGlobalStyle(prefix + 'color.background', name);
  const [gradientValue] = screen_typography_useGlobalStyle(prefix + 'color.gradient', name);
  const [color] = screen_typography_useGlobalStyle(prefix + 'color.text', name);

  if (!hasSupport) {
    return null;
  }

  const navigationButtonLabel = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: is a subset of Typography, e.g., 'text' or 'links'.
  (0,external_wp_i18n_namespaceObject.__)('Typography %s styles'), label);
  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + '/typography/' + element,
    "aria-label": navigationButtonLabel
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    className: "edit-site-global-styles-screen-typography__indicator",
    style: {
      fontFamily: fontFamily !== null && fontFamily !== void 0 ? fontFamily : 'serif',
      background: gradientValue !== null && gradientValue !== void 0 ? gradientValue : backgroundColor,
      color,
      fontStyle,
      fontWeight,
      letterSpacing,
      ...extraStyles
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Aa')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, label)));
}

function ScreenTypography(_ref2) {
  let {
    name,
    variation = ''
  } = _ref2;
  const parentMenu = name === undefined ? '' : '/blocks/' + name;
  const variationClassName = getVariationClassName(variation);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Typography'),
    description: (0,external_wp_i18n_namespaceObject.__)('Manage the typography settings for different elements.')
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name,
    variation: variationClassName
  }), !name && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-typography"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3
  }, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('Elements')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, {
    isBordered: true,
    isSeparated: true
  }, (0,external_wp_element_namespaceObject.createElement)(Item, {
    name: name,
    parentMenu: parentMenu,
    element: "text",
    label: (0,external_wp_i18n_namespaceObject.__)('Text')
  }), (0,external_wp_element_namespaceObject.createElement)(Item, {
    name: name,
    parentMenu: parentMenu,
    element: "link",
    label: (0,external_wp_i18n_namespaceObject.__)('Links')
  }), (0,external_wp_element_namespaceObject.createElement)(Item, {
    name: name,
    parentMenu: parentMenu,
    element: "heading",
    label: (0,external_wp_i18n_namespaceObject.__)('Headings')
  }), (0,external_wp_element_namespaceObject.createElement)(Item, {
    name: name,
    parentMenu: parentMenu,
    element: "button",
    label: (0,external_wp_i18n_namespaceObject.__)('Buttons')
  })))), !!name && (0,external_wp_element_namespaceObject.createElement)(TypographyPanel, {
    name: name,
    variation: variation,
    element: "text"
  }));
}

/* harmony default export */ var screen_typography = (ScreenTypography);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/typography-preview.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const {
  useGlobalStyle: typography_preview_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function TypographyPreview(_ref) {
  let {
    name,
    element,
    headingLevel
  } = _ref;
  let prefix = '';

  if (element === 'heading') {
    prefix = `elements.${headingLevel}.`;
  } else if (element && element !== 'text') {
    prefix = `elements.${element}.`;
  }

  const [fontFamily] = typography_preview_useGlobalStyle(prefix + 'typography.fontFamily', name);
  const [gradientValue] = typography_preview_useGlobalStyle(prefix + 'color.gradient', name);
  const [backgroundColor] = typography_preview_useGlobalStyle(prefix + 'color.background', name);
  const [color] = typography_preview_useGlobalStyle(prefix + 'color.text', name);
  const [fontSize] = typography_preview_useGlobalStyle(prefix + 'typography.fontSize', name);
  const [fontStyle] = typography_preview_useGlobalStyle(prefix + 'typography.fontStyle', name);
  const [fontWeight] = typography_preview_useGlobalStyle(prefix + 'typography.fontWeight', name);
  const [letterSpacing] = typography_preview_useGlobalStyle(prefix + 'typography.letterSpacing', name);
  const extraStyles = element === 'link' ? {
    textDecoration: 'underline'
  } : {};
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-typography-preview",
    style: {
      fontFamily: fontFamily !== null && fontFamily !== void 0 ? fontFamily : 'serif',
      background: gradientValue !== null && gradientValue !== void 0 ? gradientValue : backgroundColor,
      color,
      fontSize,
      fontStyle,
      fontWeight,
      letterSpacing,
      ...extraStyles
    }
  }, "Aa");
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-typography-element.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const screen_typography_element_elements = {
  text: {
    description: (0,external_wp_i18n_namespaceObject.__)('Manage the fonts used on the site.'),
    title: (0,external_wp_i18n_namespaceObject.__)('Text')
  },
  link: {
    description: (0,external_wp_i18n_namespaceObject.__)('Manage the fonts and typography used on the links.'),
    title: (0,external_wp_i18n_namespaceObject.__)('Links')
  },
  heading: {
    description: (0,external_wp_i18n_namespaceObject.__)('Manage the fonts and typography used on headings.'),
    title: (0,external_wp_i18n_namespaceObject.__)('Headings')
  },
  button: {
    description: (0,external_wp_i18n_namespaceObject.__)('Manage the fonts and typography used on buttons.'),
    title: (0,external_wp_i18n_namespaceObject.__)('Buttons')
  }
};

function ScreenTypographyElement(_ref) {
  let {
    name,
    element
  } = _ref;
  const [headingLevel, setHeadingLevel] = (0,external_wp_element_namespaceObject.useState)('heading');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: screen_typography_element_elements[element].title,
    description: screen_typography_element_elements[element].description
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    marginX: 4
  }, (0,external_wp_element_namespaceObject.createElement)(TypographyPreview, {
    name: name,
    element: element,
    headingLevel: headingLevel
  })), element === 'heading' && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    marginX: 4,
    marginBottom: "1em"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Select heading level'),
    hideLabelFromVision: true,
    value: headingLevel,
    onChange: setHeadingLevel,
    isBlock: true,
    size: "__unstable-large",
    __nextHasNoMarginBottom: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "heading"
    /* translators: 'All' refers to selecting all heading levels 
    and applying the same style to h1-h6. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('All')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h1",
    label: (0,external_wp_i18n_namespaceObject.__)('H1')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h2",
    label: (0,external_wp_i18n_namespaceObject.__)('H2')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h3",
    label: (0,external_wp_i18n_namespaceObject.__)('H3')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h4",
    label: (0,external_wp_i18n_namespaceObject.__)('H4')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h5",
    label: (0,external_wp_i18n_namespaceObject.__)('H5')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h6",
    label: (0,external_wp_i18n_namespaceObject.__)('H6')
  }))), (0,external_wp_element_namespaceObject.createElement)(TypographyPanel, {
    name: name,
    element: element,
    headingLevel: headingLevel
  }));
}

/* harmony default export */ var screen_typography_element = (ScreenTypographyElement);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/shuffle.js


/**
 * WordPress dependencies
 */

const shuffle = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/SVG"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.192 6.75L15.47 5.03l1.06-1.06 3.537 3.53-3.537 3.53-1.06-1.06 1.723-1.72h-3.19c-.602 0-.993.202-1.28.498-.309.319-.538.792-.695 1.383-.13.488-.222 1.023-.296 1.508-.034.664-.116 1.413-.303 2.117-.193.721-.513 1.467-1.068 2.04-.575.594-1.359.954-2.357.954H4v-1.5h4.003c.601 0 .993-.202 1.28-.498.308-.319.538-.792.695-1.383.149-.557.216-1.093.288-1.662l.039-.31a9.653 9.653 0 0 1 .272-1.653c.193-.722.513-1.467 1.067-2.04.576-.594 1.36-.954 2.358-.954h3.19zM8.004 6.75c.8 0 1.46.23 1.988.628a6.24 6.24 0 0 0-.684 1.396 1.725 1.725 0 0 0-.024-.026c-.287-.296-.679-.498-1.28-.498H4v-1.5h4.003zM12.699 14.726c-.161.459-.38.94-.684 1.396.527.397 1.188.628 1.988.628h3.19l-1.722 1.72 1.06 1.06L20.067 16l-3.537-3.53-1.06 1.06 1.723 1.72h-3.19c-.602 0-.993-.202-1.28-.498a1.96 1.96 0 0 1-.024-.026z"
}));
/* harmony default export */ var library_shuffle = (shuffle);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/color-indicator-wrapper.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



function ColorIndicatorWrapper(_ref) {
  let {
    className,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, extends_extends({
    className: classnames_default()('edit-site-global-styles__color-indicator-wrapper', className)
  }, props));
}

/* harmony default export */ var color_indicator_wrapper = (ColorIndicatorWrapper);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/palette.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */






const {
  useGlobalSetting: palette_useGlobalSetting
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const EMPTY_COLORS = [];

function Palette(_ref) {
  let {
    name
  } = _ref;
  const [customColors] = palette_useGlobalSetting('color.palette.custom');
  const [themeColors] = palette_useGlobalSetting('color.palette.theme');
  const [defaultColors] = palette_useGlobalSetting('color.palette.default');
  const [defaultPaletteEnabled] = palette_useGlobalSetting('color.defaultPalette', name);
  const [randomizeThemeColors] = useColorRandomizer();
  const colors = (0,external_wp_element_namespaceObject.useMemo)(() => [...(customColors || EMPTY_COLORS), ...(themeColors || EMPTY_COLORS), ...(defaultColors && defaultPaletteEnabled ? defaultColors : EMPTY_COLORS)], [customColors, themeColors, defaultColors, defaultPaletteEnabled]);
  const screenPath = !name ? '/colors/palette' : '/blocks/' + encodeURIComponent(name) + '/colors/palette';
  const paletteButtonText = colors.length > 0 ? (0,external_wp_i18n_namespaceObject.sprintf)( // Translators: %d: Number of palette colors.
  (0,external_wp_i18n_namespaceObject._n)('%d color', '%d colors', colors.length), colors.length) : (0,external_wp_i18n_namespaceObject.__)('Add custom colors');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3
  }, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('Palette')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, {
    isBordered: true,
    isSeparated: true
  }, (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: screenPath,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Color palettes')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    direction: colors.length === 0 ? 'row-reverse' : 'row'
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalZStack, {
    isLayered: false,
    offset: -8
  }, colors.slice(0, 5).map((_ref2, index) => {
    let {
      color
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
      key: `${color}-${index}`
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
      colorValue: color
    }));
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, paletteButtonText)))), randomizeThemeColors && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    icon: library_shuffle,
    onClick: randomizeThemeColors
  }, (0,external_wp_i18n_namespaceObject.__)('Randomize colors')));
}

/* harmony default export */ var palette = (Palette);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-colors.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */










const {
  useGlobalStyle: screen_colors_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function BackgroundColorItem(_ref) {
  let {
    name,
    parentMenu,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const urlPrefix = variation ? `/variations/${variation}` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const hasSupport = supports.includes('backgroundColor') || supports.includes('background');
  const [backgroundColor] = screen_colors_useGlobalStyle(prefix + 'color.background', name);
  const [gradientValue] = screen_colors_useGlobalStyle(prefix + 'color.gradient', name);

  if (!hasSupport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + urlPrefix + '/colors/background',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Colors background styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: gradientValue !== null && gradientValue !== void 0 ? gradientValue : backgroundColor,
    "data-testid": "background-color-indicator"
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    className: "edit-site-global-styles__color-label"
  }, (0,external_wp_i18n_namespaceObject.__)('Background'))));
}

function TextColorItem(_ref2) {
  let {
    name,
    parentMenu,
    variation = ''
  } = _ref2;
  const prefix = variation ? `variations.${variation}.` : '';
  const urlPrefix = variation ? `/variations/${variation}` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const hasSupport = supports.includes('color');
  const [color] = screen_colors_useGlobalStyle(prefix + 'color.text', name);

  if (!hasSupport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + urlPrefix + '/colors/text',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Colors text styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: color,
    "data-testid": "text-color-indicator"
  })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    className: "edit-site-global-styles__color-label"
  }, (0,external_wp_i18n_namespaceObject.__)('Text'))));
}

function LinkColorItem(_ref3) {
  let {
    name,
    parentMenu,
    variation = ''
  } = _ref3;
  const prefix = variation ? `variations.${variation}.` : '';
  const urlPrefix = variation ? `/variations/${variation}` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const hasSupport = supports.includes('linkColor');
  const [color] = screen_colors_useGlobalStyle(prefix + 'elements.link.color.text', name);
  const [colorHover] = screen_colors_useGlobalStyle(prefix + 'elements.link.:hover.color.text', name);

  if (!hasSupport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + urlPrefix + '/colors/link',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Colors link styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalZStack, {
    isLayered: false,
    offset: -8
  }, (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: color
  })), (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: colorHover
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    className: "edit-site-global-styles__color-label"
  }, (0,external_wp_i18n_namespaceObject.__)('Links'))));
}

function HeadingColorItem(_ref4) {
  let {
    name,
    parentMenu,
    variation = ''
  } = _ref4;
  const prefix = variation ? `variations.${variation}.` : '';
  const urlPrefix = variation ? `/variations/${variation}` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const hasSupport = supports.includes('color');
  const [color] = screen_colors_useGlobalStyle(prefix + 'elements.heading.color.text', name);
  const [bgColor] = screen_colors_useGlobalStyle(prefix + 'elements.heading.color.background', name);

  if (!hasSupport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + urlPrefix + '/colors/heading',
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Colors heading styles')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalZStack, {
    isLayered: false,
    offset: -8
  }, (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: bgColor
  })), (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: color
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_i18n_namespaceObject.__)('Headings'))));
}

function ButtonColorItem(_ref5) {
  let {
    name,
    parentMenu,
    variation = ''
  } = _ref5;
  const prefix = variation ? `variations.${variation}.` : '';
  const urlPrefix = variation ? `/variations/${variation}` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const hasSupport = supports.includes('buttonColor');
  const [color] = screen_colors_useGlobalStyle(prefix + 'elements.button.color.text', name);
  const [bgColor] = screen_colors_useGlobalStyle(prefix + 'elements.button.color.background', name);

  if (!hasSupport) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigationButtonAsItem, {
    path: parentMenu + urlPrefix + '/colors/button'
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalZStack, {
    isLayered: false,
    offset: -8
  }, (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: bgColor
  })), (0,external_wp_element_namespaceObject.createElement)(color_indicator_wrapper, {
    expanded: false
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ColorIndicator, {
    colorValue: color
  }))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, {
    className: "edit-site-global-styles__color-label"
  }, (0,external_wp_i18n_namespaceObject.__)('Buttons'))));
}

function ScreenColors(_ref6) {
  let {
    name,
    variation = ''
  } = _ref6;
  const parentMenu = name === undefined ? '' : '/blocks/' + encodeURIComponent(name);
  const variationClassName = getVariationClassName(variation);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Colors'),
    description: (0,external_wp_i18n_namespaceObject.__)('Manage palettes and the default color of different global elements on the site.')
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name,
    variation: variationClassName
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-colors"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 10
  }, (0,external_wp_element_namespaceObject.createElement)(palette, {
    name: name
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3
  }, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('Elements')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalItemGroup, {
    isBordered: true,
    isSeparated: true
  }, (0,external_wp_element_namespaceObject.createElement)(BackgroundColorItem, {
    name: name,
    parentMenu: parentMenu,
    variation: variation
  }), (0,external_wp_element_namespaceObject.createElement)(TextColorItem, {
    name: name,
    parentMenu: parentMenu,
    variation: variation
  }), (0,external_wp_element_namespaceObject.createElement)(LinkColorItem, {
    name: name,
    parentMenu: parentMenu,
    variation: variation
  }), (0,external_wp_element_namespaceObject.createElement)(HeadingColorItem, {
    name: name,
    parentMenu: parentMenu,
    variation: variation
  }), (0,external_wp_element_namespaceObject.createElement)(ButtonColorItem, {
    name: name,
    parentMenu: parentMenu,
    variation: variation
  }))))));
}

/* harmony default export */ var screen_colors = (ScreenColors);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/color-palette-panel.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


const {
  useGlobalSetting: color_palette_panel_useGlobalSetting
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function ColorPalettePanel(_ref) {
  let {
    name
  } = _ref;
  const [themeColors, setThemeColors] = color_palette_panel_useGlobalSetting('color.palette.theme', name);
  const [baseThemeColors] = color_palette_panel_useGlobalSetting('color.palette.theme', name, 'base');
  const [defaultColors, setDefaultColors] = color_palette_panel_useGlobalSetting('color.palette.default', name);
  const [baseDefaultColors] = color_palette_panel_useGlobalSetting('color.palette.default', name, 'base');
  const [customColors, setCustomColors] = color_palette_panel_useGlobalSetting('color.palette.custom', name);
  const [defaultPaletteEnabled] = color_palette_panel_useGlobalSetting('color.defaultPalette', name);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    className: "edit-site-global-styles-color-palette-panel",
    spacing: 10
  }, !!themeColors && !!themeColors.length && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    canReset: themeColors !== baseThemeColors,
    canOnlyChangeValues: true,
    colors: themeColors,
    onChange: setThemeColors,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Theme')
  }), !!defaultColors && !!defaultColors.length && !!defaultPaletteEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    canReset: defaultColors !== baseDefaultColors,
    canOnlyChangeValues: true,
    colors: defaultColors,
    onChange: setDefaultColors,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Default')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    colors: customColors,
    onChange: setCustomColors,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Custom'),
    emptyMessage: (0,external_wp_i18n_namespaceObject.__)('Custom colors are empty! Add some colors to create your own color palette.'),
    slugPrefix: "custom-"
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/gradients-palette-panel.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



const {
  useGlobalSetting: gradients_palette_panel_useGlobalSetting
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

const gradients_palette_panel_noop = () => {};

function GradientPalettePanel(_ref) {
  let {
    name
  } = _ref;
  const [themeGradients, setThemeGradients] = gradients_palette_panel_useGlobalSetting('color.gradients.theme', name);
  const [baseThemeGradients] = gradients_palette_panel_useGlobalSetting('color.gradients.theme', name, 'base');
  const [defaultGradients, setDefaultGradients] = gradients_palette_panel_useGlobalSetting('color.gradients.default', name);
  const [baseDefaultGradients] = gradients_palette_panel_useGlobalSetting('color.gradients.default', name, 'base');
  const [customGradients, setCustomGradients] = gradients_palette_panel_useGlobalSetting('color.gradients.custom', name);
  const [defaultPaletteEnabled] = gradients_palette_panel_useGlobalSetting('color.defaultGradients', name);
  const [customDuotone] = gradients_palette_panel_useGlobalSetting('color.duotone.custom') || [];
  const [defaultDuotone] = gradients_palette_panel_useGlobalSetting('color.duotone.default') || [];
  const [themeDuotone] = gradients_palette_panel_useGlobalSetting('color.duotone.theme') || [];
  const [defaultDuotoneEnabled] = gradients_palette_panel_useGlobalSetting('color.defaultDuotone');
  const duotonePalette = [...(customDuotone || []), ...(themeDuotone || []), ...(defaultDuotone && defaultDuotoneEnabled ? defaultDuotone : [])];
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    className: "edit-site-global-styles-gradient-palette-panel",
    spacing: 10
  }, !!themeGradients && !!themeGradients.length && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    canReset: themeGradients !== baseThemeGradients,
    canOnlyChangeValues: true,
    gradients: themeGradients,
    onChange: setThemeGradients,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Theme')
  }), !!defaultGradients && !!defaultGradients.length && !!defaultPaletteEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    canReset: defaultGradients !== baseDefaultGradients,
    canOnlyChangeValues: true,
    gradients: defaultGradients,
    onChange: setDefaultGradients,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Default')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalPaletteEdit, {
    gradients: customGradients,
    onChange: setCustomGradients,
    paletteLabel: (0,external_wp_i18n_namespaceObject.__)('Custom'),
    emptyMessage: (0,external_wp_i18n_namespaceObject.__)('Custom gradients are empty! Add some gradients to create your own palette.'),
    slugPrefix: "custom-"
  }), !!duotonePalette && !!duotonePalette.length && (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('Duotone')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalSpacer, {
    margin: 3
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.DuotonePicker, {
    duotonePalette: duotonePalette,
    disableCustomDuotone: true,
    disableCustomColors: true,
    clearable: false,
    onChange: gradients_palette_panel_noop
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-color-palette.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





function ScreenColorPalette(_ref) {
  let {
    name
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Palette'),
    description: (0,external_wp_i18n_namespaceObject.__)('Palettes are used to provide default color options for blocks and various design tools. Here you can edit the colors with their labels.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
    tabs: [{
      name: 'solid',
      title: 'Solid',
      value: 'solid'
    }, {
      name: 'gradient',
      title: 'Gradient',
      value: 'gradient'
    }]
  }, tab => (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, tab.value === 'solid' && (0,external_wp_element_namespaceObject.createElement)(ColorPalettePanel, {
    name: name
  }), tab.value === 'gradient' && (0,external_wp_element_namespaceObject.createElement)(GradientPalettePanel, {
    name: name
  }))));
}

/* harmony default export */ var screen_color_palette = (ScreenColorPalette);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-background-color.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const {
  useGlobalSetting: screen_background_color_useGlobalSetting,
  useGlobalStyle: screen_background_color_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function ScreenBackgroundColor(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const [areCustomSolidsEnabled] = screen_background_color_useGlobalSetting('color.custom', name);
  const [areCustomGradientsEnabled] = screen_background_color_useGlobalSetting('color.customGradient', name);
  const colorsPerOrigin = useColorsPerOrigin(name);
  const gradientsPerOrigin = useGradientsPerOrigin(name);
  const [isBackgroundEnabled] = screen_background_color_useGlobalSetting('color.background', name);
  const hasBackgroundColor = supports.includes('backgroundColor') && isBackgroundEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const hasGradientColor = supports.includes('background') && (gradientsPerOrigin.length > 0 || areCustomGradientsEnabled);
  const [backgroundColor, setBackgroundColor] = screen_background_color_useGlobalStyle(prefix + 'color.background', name);
  const [userBackgroundColor] = screen_background_color_useGlobalStyle(prefix + 'color.background', name, 'user');
  const [gradient, setGradient] = screen_background_color_useGlobalStyle(prefix + 'color.gradient', name);
  const [userGradient] = screen_background_color_useGlobalStyle(prefix + 'color.gradient', name, 'user');

  if (!hasBackgroundColor && !hasGradientColor) {
    return null;
  }

  let backgroundSettings = {};

  if (hasBackgroundColor) {
    backgroundSettings = {
      colorValue: backgroundColor,
      onColorChange: setBackgroundColor
    };

    if (backgroundColor) {
      backgroundSettings.clearable = backgroundColor === userBackgroundColor;
    }
  }

  let gradientSettings = {};

  if (hasGradientColor) {
    gradientSettings = {
      gradientValue: gradient,
      onGradientChange: setGradient
    };

    if (gradient) {
      gradientSettings.clearable = gradient === userGradient;
    }
  }

  const controlProps = { ...backgroundSettings,
    ...gradientSettings
  };
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Background'),
    description: (0,external_wp_i18n_namespaceObject.__)('Set a background color or gradient for the whole site.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, extends_extends({
    className: classnames_default()('edit-site-screen-background-color__control', {
      'has-no-tabs': !hasBackgroundColor || !hasGradientColor
    }),
    colors: colorsPerOrigin,
    gradients: gradientsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    disableCustomGradients: !areCustomGradientsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true
  }, controlProps)));
}

/* harmony default export */ var screen_background_color = (ScreenBackgroundColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-text-color.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const {
  useGlobalSetting: screen_text_color_useGlobalSetting,
  useGlobalStyle: screen_text_color_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function ScreenTextColor(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const [areCustomSolidsEnabled] = screen_text_color_useGlobalSetting('color.custom', name);
  const [isTextEnabled] = screen_text_color_useGlobalSetting('color.text', name);
  const colorsPerOrigin = useColorsPerOrigin(name);
  const hasTextColor = supports.includes('color') && isTextEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const [color, setColor] = screen_text_color_useGlobalStyle(prefix + 'color.text', name);
  const [userColor] = screen_text_color_useGlobalStyle(prefix + 'color.text', name, 'user');

  if (!hasTextColor) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Text'),
    description: (0,external_wp_i18n_namespaceObject.__)('Set the default color used for text across the site.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, {
    className: "edit-site-screen-text-color__control",
    colors: colorsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true,
    colorValue: color,
    onColorChange: setColor,
    clearable: color === userColor
  }));
}

/* harmony default export */ var screen_text_color = (ScreenTextColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-link-color.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const {
  useGlobalSetting: screen_link_color_useGlobalSetting,
  useGlobalStyle: screen_link_color_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function ScreenLinkColor(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const [areCustomSolidsEnabled] = screen_link_color_useGlobalSetting('color.custom', name);
  const colorsPerOrigin = useColorsPerOrigin(name);
  const [isLinkEnabled] = screen_link_color_useGlobalSetting('color.link', name);
  const hasLinkColor = supports.includes('linkColor') && isLinkEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const pseudoStates = {
    default: {
      label: (0,external_wp_i18n_namespaceObject.__)('Default'),
      value: screen_link_color_useGlobalStyle(prefix + 'elements.link.color.text', name)[0],
      handler: screen_link_color_useGlobalStyle(prefix + 'elements.link.color.text', name)[1],
      userValue: screen_link_color_useGlobalStyle(prefix + 'elements.link.color.text', name, 'user')[0]
    },
    hover: {
      label: (0,external_wp_i18n_namespaceObject.__)('Hover'),
      value: screen_link_color_useGlobalStyle(prefix + 'elements.link.:hover.color.text', name)[0],
      handler: screen_link_color_useGlobalStyle(prefix + 'elements.link.:hover.color.text', name)[1],
      userValue: screen_link_color_useGlobalStyle(prefix + 'elements.link.:hover.color.text', name, 'user')[0]
    }
  };

  if (!hasLinkColor) {
    return null;
  }

  const tabs = Object.entries(pseudoStates).map(_ref2 => {
    let [selector, config] = _ref2;
    return {
      name: selector,
      title: config.label,
      className: `color-text-${selector}`
    };
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Links'),
    description: (0,external_wp_i18n_namespaceObject.__)('Set the colors used for links across the site.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
    tabs: tabs
  }, tab => {
    var _pseudoStates$tab$nam;

    const pseudoSelectorConfig = (_pseudoStates$tab$nam = pseudoStates[tab.name]) !== null && _pseudoStates$tab$nam !== void 0 ? _pseudoStates$tab$nam : null;

    if (!pseudoSelectorConfig) {
      return null;
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, {
      className: "edit-site-screen-link-color__control",
      colors: colorsPerOrigin,
      disableCustomColors: !areCustomSolidsEnabled,
      showTitle: false,
      enableAlpha: true,
      __experimentalIsRenderedInSidebar: true,
      colorValue: pseudoSelectorConfig.value,
      onColorChange: pseudoSelectorConfig.handler,
      clearable: pseudoSelectorConfig.value === pseudoSelectorConfig.userValue
    }));
  }));
}

/* harmony default export */ var screen_link_color = (ScreenLinkColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-heading-color.js



/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




const {
  useGlobalSetting: screen_heading_color_useGlobalSetting,
  useGlobalStyle: screen_heading_color_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function ScreenHeadingColor(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const [selectedLevel, setCurrentTab] = (0,external_wp_element_namespaceObject.useState)('heading');
  const supports = getSupportedGlobalStylesPanels(name);
  const [areCustomSolidsEnabled] = screen_heading_color_useGlobalSetting('color.custom', name);
  const [areCustomGradientsEnabled] = screen_heading_color_useGlobalSetting('color.customGradient', name);
  const [isTextEnabled] = screen_heading_color_useGlobalSetting('color.text', name);
  const [isBackgroundEnabled] = screen_heading_color_useGlobalSetting('color.background', name);
  const colorsPerOrigin = useColorsPerOrigin(name);
  const gradientsPerOrigin = useGradientsPerOrigin(name);
  const hasTextColor = supports.includes('color') && isTextEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const hasBackgroundColor = supports.includes('backgroundColor') && isBackgroundEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const hasGradientColor = supports.includes('background') && (gradientsPerOrigin.length > 0 || areCustomGradientsEnabled);
  const [color, setColor] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.text', name);
  const [userColor] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.text', name, 'user');
  const [backgroundColor, setBackgroundColor] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.background', name);
  const [userBackgroundColor] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.background', name, 'user');
  const [gradient, setGradient] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.gradient', name);
  const [userGradient] = screen_heading_color_useGlobalStyle(prefix + 'elements.' + selectedLevel + '.color.gradient', name, 'user');

  if (!hasTextColor && !hasBackgroundColor && !hasGradientColor) {
    return null;
  }

  let backgroundSettings = {};

  if (hasBackgroundColor) {
    backgroundSettings = {
      colorValue: backgroundColor,
      onColorChange: setBackgroundColor
    };

    if (backgroundColor) {
      backgroundSettings.clearable = backgroundColor === userBackgroundColor;
    }
  }

  let gradientSettings = {};

  if (hasGradientColor) {
    gradientSettings = {
      gradientValue: gradient,
      onGradientChange: setGradient
    };

    if (gradient) {
      gradientSettings.clearable = gradient === userGradient;
    }
  }

  const controlProps = { ...backgroundSettings,
    ...gradientSettings
  };
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Headings'),
    description: (0,external_wp_i18n_namespaceObject.__)('Set the default color used for headings across the site.')
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-heading-color"
  }, (0,external_wp_element_namespaceObject.createElement)("h4", null, (0,external_wp_i18n_namespaceObject.__)('Select heading level')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Select heading level'),
    hideLabelFromVision: true,
    value: selectedLevel,
    onChange: setCurrentTab,
    isBlock: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "heading"
    /* translators: 'All' refers to selecting all heading levels
    and applying the same style to h1-h6. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('All')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h1",
    label: (0,external_wp_i18n_namespaceObject.__)('H1')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h2",
    label: (0,external_wp_i18n_namespaceObject.__)('H2')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h3",
    label: (0,external_wp_i18n_namespaceObject.__)('H3')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h4",
    label: (0,external_wp_i18n_namespaceObject.__)('H4')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h5",
    label: (0,external_wp_i18n_namespaceObject.__)('H5')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalToggleGroupControlOption, {
    value: "h6",
    label: (0,external_wp_i18n_namespaceObject.__)('H6')
  }))), hasTextColor && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-heading-color"
  }, (0,external_wp_element_namespaceObject.createElement)("h4", null, selectedLevel === 'heading' ? (0,external_wp_i18n_namespaceObject.__)('Text color for all heading levels') : (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: heading level (h1-h6) */
  (0,external_wp_i18n_namespaceObject.__)('Text color for %s'), selectedLevel.toUpperCase())), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, {
    className: "edit-site-screen-heading-text-color__control",
    colors: colorsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true,
    colorValue: color,
    onColorChange: setColor,
    clearable: color === userColor
  })), hasBackgroundColor && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-heading-color"
  }, (0,external_wp_element_namespaceObject.createElement)("h4", null, selectedLevel === 'heading' ? (0,external_wp_i18n_namespaceObject.__)('Background color for all heading levels') : (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: heading level (h1-h6) */
  (0,external_wp_i18n_namespaceObject.__)('Background color for %s'), selectedLevel.toUpperCase())), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, extends_extends({
    className: "edit-site-screen-heading-background-color__control",
    colors: colorsPerOrigin,
    gradients: gradientsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    disableCustomGradients: !areCustomGradientsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true
  }, controlProps))));
}

/* harmony default export */ var screen_heading_color = (ScreenHeadingColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-button-color.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const {
  useGlobalSetting: screen_button_color_useGlobalSetting,
  useGlobalStyle: screen_button_color_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function ScreenButtonColor(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const prefix = variation ? `variations.${variation}.` : '';
  const supports = getSupportedGlobalStylesPanels(name);
  const colorsPerOrigin = useColorsPerOrigin(name);
  const [areCustomSolidsEnabled] = screen_button_color_useGlobalSetting('color.custom', name);
  const [isBackgroundEnabled] = screen_button_color_useGlobalSetting('color.background', name);
  const hasButtonColor = supports.includes('buttonColor') && isBackgroundEnabled && (colorsPerOrigin.length > 0 || areCustomSolidsEnabled);
  const [buttonTextColor, setButtonTextColor] = screen_button_color_useGlobalStyle(prefix + 'elements.button.color.text', name);
  const [userButtonTextColor] = screen_button_color_useGlobalStyle('elements.button.color.text', name, 'user');
  const [buttonBgColor, setButtonBgColor] = screen_button_color_useGlobalStyle('elements.button.color.background', name);
  const [userButtonBgColor] = screen_button_color_useGlobalStyle('elements.button.color.background', name, 'user');

  if (!hasButtonColor) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Buttons'),
    description: (0,external_wp_i18n_namespaceObject.__)('Set the default colors used for buttons across the site.')
  }), (0,external_wp_element_namespaceObject.createElement)("h4", {
    className: "edit-site-global-styles-section-title"
  }, (0,external_wp_i18n_namespaceObject.__)('Text color')), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, {
    className: "edit-site-screen-button-color__control",
    colors: colorsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true,
    colorValue: buttonTextColor,
    onColorChange: setButtonTextColor,
    clearable: buttonTextColor === userButtonTextColor
  }), (0,external_wp_element_namespaceObject.createElement)("h4", {
    className: "edit-site-global-styles-section-title"
  }, (0,external_wp_i18n_namespaceObject.__)('Background color')), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalColorGradientControl, {
    className: "edit-site-screen-button-color__control",
    colors: colorsPerOrigin,
    disableCustomColors: !areCustomSolidsEnabled,
    showTitle: false,
    enableAlpha: true,
    __experimentalIsRenderedInSidebar: true,
    colorValue: buttonBgColor,
    onColorChange: setButtonBgColor,
    clearable: buttonBgColor === userButtonBgColor
  }));
}

/* harmony default export */ var screen_button_color = (ScreenButtonColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-layout.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






function ScreenLayout(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const hasDimensionsPanel = useHasDimensionsPanel(name);
  const variationClassName = getVariationClassName(variation);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Layout')
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name,
    variation: variationClassName
  }), hasDimensionsPanel && (0,external_wp_element_namespaceObject.createElement)(DimensionsPanel, {
    name: name,
    variation: variation
  }));
}

/* harmony default export */ var screen_layout = (ScreenLayout);

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(5619);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/canvas-spinner/index.js


/**
 * WordPress dependencies
 */

function CanvasSpinner() {
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-canvas-spinner"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/global-styles-provider.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  GlobalStylesContext: global_styles_provider_GlobalStylesContext
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function mergeTreesCustomizer(_, srcValue) {
  // We only pass as arrays the presets,
  // in which case we want the new array of values
  // to override the old array (no merging).
  if (Array.isArray(srcValue)) {
    return srcValue;
  }
}

function mergeBaseAndUserConfigs(base, user) {
  return (0,external_lodash_namespaceObject.mergeWith)({}, base, user, mergeTreesCustomizer);
}

const cleanEmptyObject = object => {
  if (object === null || typeof object !== 'object' || Array.isArray(object)) {
    return object;
  }

  const cleanedNestedObjects = Object.fromEntries(Object.entries((0,external_lodash_namespaceObject.mapValues)(object, cleanEmptyObject)).filter(_ref => {
    let [, value] = _ref;
    return Boolean(value);
  }));
  return (0,external_lodash_namespaceObject.isEmpty)(cleanedNestedObjects) ? undefined : cleanedNestedObjects;
};

function useGlobalStylesUserConfig() {
  const {
    globalStylesId,
    isReady,
    settings,
    styles
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedEntityRecord,
      hasFinishedResolution
    } = select(external_wp_coreData_namespaceObject.store);

    const _globalStylesId = select(external_wp_coreData_namespaceObject.store).__experimentalGetCurrentGlobalStylesId();

    const record = _globalStylesId ? getEditedEntityRecord('root', 'globalStyles', _globalStylesId) : undefined;
    let hasResolved = false;

    if (hasFinishedResolution('__experimentalGetCurrentGlobalStylesId')) {
      hasResolved = _globalStylesId ? hasFinishedResolution('getEditedEntityRecord', ['root', 'globalStyles', _globalStylesId]) : true;
    }

    return {
      globalStylesId: _globalStylesId,
      isReady: hasResolved,
      settings: record === null || record === void 0 ? void 0 : record.settings,
      styles: record === null || record === void 0 ? void 0 : record.styles
    };
  }, []);
  const {
    getEditedEntityRecord
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_coreData_namespaceObject.store);
  const {
    editEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const config = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return {
      settings: settings !== null && settings !== void 0 ? settings : {},
      styles: styles !== null && styles !== void 0 ? styles : {}
    };
  }, [settings, styles]);
  const setConfig = (0,external_wp_element_namespaceObject.useCallback)(function (callback) {
    var _record$styles, _record$settings;

    let options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    const record = getEditedEntityRecord('root', 'globalStyles', globalStylesId);
    const currentConfig = {
      styles: (_record$styles = record === null || record === void 0 ? void 0 : record.styles) !== null && _record$styles !== void 0 ? _record$styles : {},
      settings: (_record$settings = record === null || record === void 0 ? void 0 : record.settings) !== null && _record$settings !== void 0 ? _record$settings : {}
    };
    const updatedConfig = callback(currentConfig);
    editEntityRecord('root', 'globalStyles', globalStylesId, {
      styles: cleanEmptyObject(updatedConfig.styles) || {},
      settings: cleanEmptyObject(updatedConfig.settings) || {}
    }, options);
  }, [globalStylesId]);
  return [isReady, config, setConfig];
}

function useGlobalStylesBaseConfig() {
  const baseConfig = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_coreData_namespaceObject.store).__experimentalGetCurrentThemeBaseGlobalStyles();
  }, []);
  return [!!baseConfig, baseConfig];
}

function useGlobalStylesContext() {
  const [isUserConfigReady, userConfig, setUserConfig] = useGlobalStylesUserConfig();
  const [isBaseConfigReady, baseConfig] = useGlobalStylesBaseConfig();
  const mergedConfig = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!baseConfig || !userConfig) {
      return {};
    }

    return mergeBaseAndUserConfigs(baseConfig, userConfig);
  }, [userConfig, baseConfig]);
  const context = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return {
      isReady: isUserConfigReady && isBaseConfigReady,
      user: userConfig,
      base: baseConfig,
      merged: mergedConfig,
      setUserConfig
    };
  }, [mergedConfig, userConfig, baseConfig, setUserConfig, isUserConfigReady, isBaseConfigReady]);
  return context;
}

function GlobalStylesProvider(_ref2) {
  let {
    children
  } = _ref2;
  const context = useGlobalStylesContext();

  if (!context.isReady) {
    return (0,external_wp_element_namespaceObject.createElement)(CanvasSpinner, null);
  }

  return (0,external_wp_element_namespaceObject.createElement)(global_styles_provider_GlobalStylesContext.Provider, {
    value: context
  }, children);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-style-variations.js


/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





const {
  GlobalStylesContext: screen_style_variations_GlobalStylesContext
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function compareVariations(a, b) {
  return es6_default()(a.styles, b.styles) && es6_default()(a.settings, b.settings);
}

function Variation(_ref) {
  let {
    variation
  } = _ref;
  const [isFocused, setIsFocused] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    base,
    user,
    setUserConfig
  } = (0,external_wp_element_namespaceObject.useContext)(screen_style_variations_GlobalStylesContext);
  const context = (0,external_wp_element_namespaceObject.useMemo)(() => {
    var _variation$settings, _variation$styles;

    return {
      user: {
        settings: (_variation$settings = variation.settings) !== null && _variation$settings !== void 0 ? _variation$settings : {},
        styles: (_variation$styles = variation.styles) !== null && _variation$styles !== void 0 ? _variation$styles : {}
      },
      base,
      merged: mergeBaseAndUserConfigs(base, variation),
      setUserConfig: () => {}
    };
  }, [variation, base]);

  const selectVariation = () => {
    setUserConfig(() => {
      return {
        settings: variation.settings,
        styles: variation.styles
      };
    });
  };

  const selectOnEnter = event => {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ENTER) {
      event.preventDefault();
      selectVariation();
    }
  };

  const isActive = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return compareVariations(user, variation);
  }, [user, variation]);
  return (0,external_wp_element_namespaceObject.createElement)(screen_style_variations_GlobalStylesContext.Provider, {
    value: context
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-global-styles-variations_item', {
      'is-active': isActive
    }),
    role: "button",
    onClick: selectVariation,
    onKeyDown: selectOnEnter,
    tabIndex: "0",
    "aria-label": variation === null || variation === void 0 ? void 0 : variation.title,
    "aria-current": isActive,
    onFocus: () => setIsFocused(true),
    onBlur: () => setIsFocused(false)
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-variations_item-preview"
  }, (0,external_wp_element_namespaceObject.createElement)(preview, {
    label: variation === null || variation === void 0 ? void 0 : variation.title,
    isFocused: isFocused,
    withHoverView: true
  }))));
}

function ScreenStyleVariations() {
  const {
    variations,
    mode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      variations: select(external_wp_coreData_namespaceObject.store).__experimentalGetCurrentThemeGlobalStylesVariations(),
      mode: select(external_wp_blockEditor_namespaceObject.store).__unstableGetEditorMode()
    };
  }, []);
  const withEmptyVariation = (0,external_wp_element_namespaceObject.useMemo)(() => {
    return [{
      title: (0,external_wp_i18n_namespaceObject.__)('Default'),
      settings: {},
      styles: {}
    }, ...variations.map(variation => {
      var _variation$settings2, _variation$styles2;

      return { ...variation,
        settings: (_variation$settings2 = variation.settings) !== null && _variation$settings2 !== void 0 ? _variation$settings2 : {},
        styles: (_variation$styles2 = variation.styles) !== null && _variation$styles2 !== void 0 ? _variation$styles2 : {}
      };
    })];
  }, [variations]);
  const {
    __unstableSetEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const shouldRevertInitialMode = (0,external_wp_element_namespaceObject.useRef)(null);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // ignore changes to zoom-out mode as we explictily change to it on mount.
    if (mode !== 'zoom-out') {
      shouldRevertInitialMode.current = false;
    }
  }, [mode]); // Intentionality left without any dependency.
  // This effect should only run the first time the component is rendered.
  // The effect opens the zoom-out view if it is not open before when applying a style variation.

  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (mode !== 'zoom-out') {
      __unstableSetEditorMode('zoom-out');

      shouldRevertInitialMode.current = true;
      return () => {
        // if there were not mode changes revert to the initial mode when unmounting.
        if (shouldRevertInitialMode.current) {
          __unstableSetEditorMode(mode);
        }
      };
    }
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    back: "/",
    title: (0,external_wp_i18n_namespaceObject.__)('Browse styles'),
    description: (0,external_wp_i18n_namespaceObject.__)('Choose a variation to change the look of the site.')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Card, {
    size: "small",
    isBorderless: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.CardBody, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalGrid, {
    columns: 2
  }, withEmptyVariation === null || withEmptyVariation === void 0 ? void 0 : withEmptyVariation.map((variation, index) => (0,external_wp_element_namespaceObject.createElement)(Variation, {
    key: index,
    variation: variation
  }))))));
}

/* harmony default export */ var screen_style_variations = (ScreenStyleVariations);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-border.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






function ScreenBorder(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const hasBorderPanel = useHasBorderPanel(name);
  const variationClassName = getVariationClassName(variation);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Border')
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name,
    variation: variationClassName
  }), hasBorderPanel && (0,external_wp_element_namespaceObject.createElement)(BorderPanel, {
    name: name,
    variation: variation
  }));
}

/* harmony default export */ var screen_border = (ScreenBorder);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/style-book/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */


const {
  ExperimentalBlockEditorProvider,
  useGlobalStyle: style_book_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const SLOT_FILL_NAME = 'EditSiteStyleBook';
const {
  Slot: StyleBookSlot,
  Fill: StyleBookFill
} = (0,external_wp_components_namespaceObject.createSlotFill)(SLOT_FILL_NAME); // The content area of the Style Book is rendered within an iframe so that global styles
// are applied to elements within the entire content area. To support elements that are
// not part of the block previews, such as headings and layout for the block previews,
// additional CSS rules need to be passed into the iframe. These are hard-coded below.
// Note that button styles are unset, and then focus rules from the `Button` component are
// applied to the `button` element, targeted via `.edit-site-style-book__example`.
// This is to ensure that browser default styles for buttons are not applied to the previews.

const STYLE_BOOK_IFRAME_STYLES = `
	.edit-site-style-book__examples {
		max-width: 900px;
		margin: 0 auto;
	}

	.edit-site-style-book__example {
		border-radius: 2px;
		cursor: pointer;
		display: flex;
		flex-direction: column;
		gap: 40px;
		margin-bottom: 40px;
		padding: 16px;
		width: 100%;
		box-sizing: border-box;
	}

	.edit-site-style-book__example.is-selected {
		box-shadow: 0 0 0 1px var(--wp-components-color-accent, var(--wp-admin-theme-color, #007cba));
	}

	.edit-site-style-book__example:focus:not(:disabled) {
		box-shadow: 0 0 0 var(--wp-admin-border-width-focus) var(--wp-components-color-accent, var(--wp-admin-theme-color, #007cba));
		outline: 3px solid transparent;
	}

	.edit-site-style-book__examples.is-wide .edit-site-style-book__example {
		flex-direction: row;
	}

	.edit-site-style-book__example-title {
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
		font-size: 11px;
		font-weight: 500;
		line-height: normal;
		margin: 0;
		text-align: left;
		text-transform: uppercase;
	}

	.edit-site-style-book__examples.is-wide .edit-site-style-book__example-title {
		text-align: right;
		width: 120px;
	}

	.edit-site-style-book__example-preview {
		width: 100%;
	}

	.edit-site-style-book__example-preview .block-editor-block-list__insertion-point,
	.edit-site-style-book__example-preview .block-list-appender {
		display: none;
	}

	.edit-site-style-book__example-preview .is-root-container > .wp-block:first-child {
		margin-top: 0;
	}
	.edit-site-style-book__example-preview .is-root-container > .wp-block:last-child {
		margin-bottom: 0;
	}
`;

function getExamples() {
  // Use our own example for the Heading block so that we can show multiple
  // heading levels.
  const headingsExample = {
    name: 'core/heading',
    title: (0,external_wp_i18n_namespaceObject.__)('Headings'),
    category: 'text',
    blocks: [(0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
      content: (0,external_wp_i18n_namespaceObject.__)('Code Is Poetry'),
      level: 1
    }), (0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
      content: (0,external_wp_i18n_namespaceObject.__)('Code Is Poetry'),
      level: 2
    }), (0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
      content: (0,external_wp_i18n_namespaceObject.__)('Code Is Poetry'),
      level: 3
    }), (0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
      content: (0,external_wp_i18n_namespaceObject.__)('Code Is Poetry'),
      level: 4
    }), (0,external_wp_blocks_namespaceObject.createBlock)('core/heading', {
      content: (0,external_wp_i18n_namespaceObject.__)('Code Is Poetry'),
      level: 5
    })]
  };
  const otherExamples = (0,external_wp_blocks_namespaceObject.getBlockTypes)().filter(blockType => {
    const {
      name,
      example,
      supports
    } = blockType;
    return name !== 'core/heading' && !!example && supports.inserter !== false;
  }).map(blockType => ({
    name: blockType.name,
    title: blockType.title,
    category: blockType.category,
    blocks: (0,external_wp_blocks_namespaceObject.getBlockFromExample)(blockType.name, blockType.example)
  }));
  return [headingsExample, ...otherExamples];
}

function StyleBook(_ref) {
  let {
    isSelected,
    onSelect,
    onClose
  } = _ref;
  const [resizeObserver, sizes] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement');
  const sectionFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();
  const [textColor] = style_book_useGlobalStyle('color.text');
  const [backgroundColor] = style_book_useGlobalStyle('color.background');
  const examples = (0,external_wp_element_namespaceObject.useMemo)(getExamples, []);
  const tabs = (0,external_wp_element_namespaceObject.useMemo)(() => (0,external_wp_blocks_namespaceObject.getCategories)().filter(category => examples.some(example => example.category === category.slug)).map(category => ({
    name: category.slug,
    title: category.title,
    icon: category.icon
  })), [examples]);
  const originalSettings = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getSettings(), []);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => ({ ...originalSettings,
    __unstableIsPreviewMode: true
  }), [originalSettings]);

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      event.preventDefault();
      onClose();
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(StyleBookFill, null, (0,external_wp_element_namespaceObject.createElement)("section", {
    className: classnames_default()('edit-site-style-book', {
      'is-wide': sizes.width > 600
    }),
    style: {
      color: textColor,
      background: backgroundColor
    },
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Style Book'),
    onKeyDown: closeOnEscape,
    ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([sectionFocusReturnRef, focusOnMountRef])
  }, resizeObserver, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-site-style-book__close-button",
    icon: close_small,
    label: (0,external_wp_i18n_namespaceObject.__)('Close Style Book'),
    onClick: onClose,
    showTooltip: false
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
    className: "edit-site-style-book__tab-panel",
    tabs: tabs
  }, tab => (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableIframe, {
    className: "edit-site-style-book__iframe",
    head: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: settings.styles
    }), (0,external_wp_element_namespaceObject.createElement)("style", null, // Forming a "block formatting context" to prevent margin collapsing.
    // @see https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Block_formatting_context
    `.is-root-container { display: flow-root; }
											body { position: relative; padding: 32px !important; }` + STYLE_BOOK_IFRAME_STYLES)),
    name: "style-book-canvas",
    tabIndex: 0
  }, settings.svgFilters, (0,external_wp_element_namespaceObject.createElement)(Examples, {
    className: classnames_default()('edit-site-style-book__examples', {
      'is-wide': sizes.width > 600
    }),
    examples: examples,
    category: tab.name,
    label: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Category of blocks, e.g. Text.
    (0,external_wp_i18n_namespaceObject.__)('Examples of blocks in the %s category'), tab.title),
    isSelected: isSelected,
    onSelect: onSelect
  })))));
}

const Examples = (0,external_wp_element_namespaceObject.memo)(_ref2 => {
  let {
    className,
    examples,
    category,
    label,
    isSelected,
    onSelect
  } = _ref2;
  const composite = (0,external_wp_components_namespaceObject.__unstableUseCompositeState)({
    orientation: 'vertical'
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableComposite, extends_extends({}, composite, {
    className: className,
    "aria-label": label
  }), examples.filter(example => example.category === category).map(example => (0,external_wp_element_namespaceObject.createElement)(Example, {
    key: example.name,
    id: `example-${example.name}`,
    composite: composite,
    title: example.title,
    blocks: example.blocks,
    isSelected: isSelected(example.name),
    onClick: () => {
      onSelect(example.name);
    }
  })));
});

const Example = _ref3 => {
  let {
    composite,
    id,
    title,
    blocks,
    isSelected,
    onClick
  } = _ref3;
  const originalSettings = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getSettings(), []);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => ({ ...originalSettings,
    __unstableIsPreviewMode: true
  }), [originalSettings]); // Cache the list of blocks to avoid additional processing when the component is re-rendered.

  const renderedBlocks = (0,external_wp_element_namespaceObject.useMemo)(() => Array.isArray(blocks) ? blocks : [blocks], [blocks]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableCompositeItem, extends_extends({}, composite, {
    className: classnames_default()('edit-site-style-book__example', {
      'is-selected': isSelected
    }),
    id: id,
    "aria-label": (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: Title of a block, e.g. Heading.
    (0,external_wp_i18n_namespaceObject.__)('Open %s styles in Styles panel'), title),
    onClick: onClick,
    role: "button",
    as: "div"
  }), (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "edit-site-style-book__example-title"
  }, title), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-style-book__example-preview",
    "aria-hidden": true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Disabled, {
    className: "edit-site-style-book__example-preview__content"
  }, (0,external_wp_element_namespaceObject.createElement)(ExperimentalBlockEditorProvider, {
    value: renderedBlocks,
    settings: settings
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
    renderAppender: false
  })))));
};

function useHasStyleBook() {
  const fills = (0,external_wp_components_namespaceObject.__experimentalUseSlotFills)(SLOT_FILL_NAME);
  return !!(fills !== null && fills !== void 0 && fills.length);
}

StyleBook.Slot = StyleBookSlot;
/* harmony default export */ var style_book = (StyleBook);


;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/info.js


/**
 * WordPress dependencies
 */

const info = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"
}));
/* harmony default export */ var library_info = (info);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/custom-css.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  useGlobalStyle: custom_css_useGlobalStyle
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function CustomCSSControl(_ref) {
  let {
    blockName
  } = _ref;
  // If blockName is defined, we are customizing CSS at the block level:
  // styles.blocks.blockName.css
  const block = !!blockName ? blockName : null;
  const [customCSS, setCustomCSS] = custom_css_useGlobalStyle('css', block);
  const [themeCSS] = custom_css_useGlobalStyle('css', block, 'base');
  const [cssError, setCSSError] = (0,external_wp_element_namespaceObject.useState)(null);
  const ignoreThemeCustomCSS = '/* IgnoreThemeCustomCSS */'; // If there is custom css from theme.json show it in the edit box
  // so the user can selectively overwrite it, rather than have the user CSS
  // completely overwrite the theme CSS by default.

  const themeCustomCSS = !customCSS && themeCSS ? `/* ${(0,external_wp_i18n_namespaceObject.__)('Theme Custom CSS start')} */\n${themeCSS}\n/* ${(0,external_wp_i18n_namespaceObject.__)('Theme Custom CSS end')} */` : undefined;

  function handleOnChange(value) {
    // If there is theme custom CSS, but the user clears the input box then save the
    // ignoreThemeCustomCSS string so that the theme custom CSS is not re-applied.
    if (themeCSS && value === '') {
      setCustomCSS(ignoreThemeCustomCSS);
      return;
    }

    setCustomCSS(value);

    if (cssError) {
      const [transformed] = (0,external_wp_blockEditor_namespaceObject.transformStyles)([{
        css: value
      }], '.editor-styles-wrapper');

      if (transformed) {
        setCSSError(null);
      }
    }
  }

  function handleOnBlur(event) {
    var _event$target;

    if (!(event !== null && event !== void 0 && (_event$target = event.target) !== null && _event$target !== void 0 && _event$target.value)) {
      setCSSError(null);
      return;
    }

    const [transformed] = (0,external_wp_blockEditor_namespaceObject.transformStyles)([{
      css: event.target.value
    }], '.editor-styles-wrapper');
    setCSSError(transformed === null ? (0,external_wp_i18n_namespaceObject.__)('There is an error with your CSS structure.') : null);
  }

  const originalThemeCustomCSS = themeCSS && customCSS && themeCustomCSS !== customCSS ? themeCSS : undefined;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, originalThemeCustomCSS && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Panel, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, {
    title: (0,external_wp_i18n_namespaceObject.__)('Original Theme Custom CSS'),
    initialOpen: false
  }, (0,external_wp_element_namespaceObject.createElement)("pre", {
    className: "edit-site-global-styles__custom-css-theme-css"
  }, originalThemeCustomCSS))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: 3
  }, (0,external_wp_element_namespaceObject.createElement)(subtitle, null, (0,external_wp_i18n_namespaceObject.__)('ADDITIONAL CSS')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextareaControl, {
    __nextHasNoMarginBottom: true,
    value: (customCSS === null || customCSS === void 0 ? void 0 : customCSS.replace(ignoreThemeCustomCSS, '')) || themeCustomCSS,
    onChange: value => handleOnChange(value),
    onBlur: handleOnBlur,
    className: "edit-site-global-styles__custom-css-input",
    spellCheck: false
  }), cssError && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Tooltip, {
    text: cssError
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles__custom-css-validation-wrapper"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
    icon: library_info,
    className: "edit-site-global-styles__custom-css-validation-icon"
  })))));
}

/* harmony default export */ var custom_css = (CustomCSSControl);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-css.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function ScreenCSS(_ref) {
  let {
    name
  } = _ref;
  // If name is defined, we are customizing CSS at the block level.
  // Display the block title in the description.
  const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(name);
  const title = blockType === null || blockType === void 0 ? void 0 : blockType.title;
  const description = title !== undefined ? (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: is the name of a block e.g., 'Image' or 'Table'.
  (0,external_wp_i18n_namespaceObject.__)('Add your own CSS to customize the appearance of the %s block.'), title) : (0,external_wp_i18n_namespaceObject.__)('Add your own CSS to customize the appearance and layout of your site.');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('CSS'),
    description: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, description, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
      href: "https://wordpress.org/support/article/css/",
      className: "edit-site-global-styles-screen-css-help-link"
    }, (0,external_wp_i18n_namespaceObject.__)('Learn more about CSS')))
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-global-styles-screen-css"
  }, (0,external_wp_element_namespaceObject.createElement)(custom_css, {
    blockName: name
  })));
}

/* harmony default export */ var screen_css = (ScreenCSS);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/screen-effects.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */






function ScreenEffects(_ref) {
  let {
    name,
    variation = ''
  } = _ref;
  const variationClassName = getVariationClassName(variation);
  const hasShadowPanel = useHasShadowControl(name);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(header, {
    title: (0,external_wp_i18n_namespaceObject.__)('Shadow')
  }), (0,external_wp_element_namespaceObject.createElement)(block_preview_panel, {
    name: name,
    variation: variationClassName
  }), hasShadowPanel && (0,external_wp_element_namespaceObject.createElement)(ShadowPanel, {
    name: name,
    variation: variation
  }));
}

/* harmony default export */ var screen_effects = (ScreenEffects);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/ui.js



/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */





















const ui_SLOT_FILL_NAME = 'GlobalStylesMenu';
const {
  Slot: GlobalStylesMenuSlot,
  Fill: GlobalStylesMenuFill
} = (0,external_wp_components_namespaceObject.createSlotFill)(ui_SLOT_FILL_NAME);

function GlobalStylesActionMenu() {
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const {
    canEditCSS
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _globalStyles$_links$, _globalStyles$_links;

    const {
      getEntityRecord,
      __experimentalGetCurrentGlobalStylesId
    } = select(external_wp_coreData_namespaceObject.store);

    const globalStylesId = __experimentalGetCurrentGlobalStylesId();

    const globalStyles = globalStylesId ? getEntityRecord('root', 'globalStyles', globalStylesId) : undefined;
    return {
      canEditCSS: (_globalStyles$_links$ = !!(globalStyles !== null && globalStyles !== void 0 && (_globalStyles$_links = globalStyles._links) !== null && _globalStyles$_links !== void 0 && _globalStyles$_links['wp:action-edit-css'])) !== null && _globalStyles$_links$ !== void 0 ? _globalStyles$_links$ : false
    };
  }, []);
  const {
    useGlobalStylesReset
  } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
  const [canReset, onReset] = useGlobalStylesReset();
  const {
    goTo
  } = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();

  const loadCustomCSS = () => goTo('/css');

  return (0,external_wp_element_namespaceObject.createElement)(GlobalStylesMenuFill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    icon: more_vertical,
    label: (0,external_wp_i18n_namespaceObject.__)('Styles actions'),
    controls: [{
      title: (0,external_wp_i18n_namespaceObject.__)('Reset to defaults'),
      onClick: onReset,
      isDisabled: !canReset
    }, {
      title: (0,external_wp_i18n_namespaceObject.__)('Welcome Guide'),
      onClick: () => toggle('core/edit-site', 'welcomeGuideStyles')
    }, ...(canEditCSS ? [{
      title: (0,external_wp_i18n_namespaceObject.__)('Additional CSS'),
      onClick: loadCustomCSS
    }] : [])]
  }));
}

function GlobalStylesNavigationScreen(_ref) {
  let {
    className,
    ...props
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorScreen, extends_extends({
    className: ['edit-site-global-styles-sidebar__navigator-screen', className].filter(Boolean).join(' ')
  }, props));
}

function BlockStyleVariationsScreens(_ref2) {
  let {
    name
  } = _ref2;
  const blockStyleVariations = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlockStyles
    } = select(external_wp_blocks_namespaceObject.store);
    return getBlockStyles(name);
  }, [name]);

  if (!(blockStyleVariations !== null && blockStyleVariations !== void 0 && blockStyleVariations.length)) {
    return null;
  }

  return blockStyleVariations.map(variation => (0,external_wp_element_namespaceObject.createElement)(ContextScreens, {
    key: variation.name + name,
    name: name,
    parentMenu: '/blocks/' + encodeURIComponent(name) + '/variations/' + encodeURIComponent(variation.name),
    variation: variation.name
  }));
}

function BlockStylesNavigationScreens(_ref3) {
  let {
    parentMenu,
    blockStyles,
    blockName
  } = _ref3;
  return blockStyles.map((style, index) => (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    key: index,
    path: parentMenu + '/variations/' + style.name
  }, (0,external_wp_element_namespaceObject.createElement)(ScreenVariation, {
    blockName: blockName,
    style: style
  })));
}

function ContextScreens(_ref4) {
  let {
    name,
    parentMenu = '',
    variation = ''
  } = _ref4;
  const blockStyleVariations = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getBlockStyles
    } = select(external_wp_blocks_namespaceObject.store);
    return getBlockStyles(name);
  }, [name]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/typography'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_typography, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/typography/text'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_typography_element, {
    name: name,
    element: "text"
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/typography/link'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_typography_element, {
    name: name,
    element: "link"
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/typography/heading'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_typography_element, {
    name: name,
    element: "heading"
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/typography/button'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_typography_element, {
    name: name,
    element: "button"
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_colors, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/palette'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_color_palette, {
    name: name
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/background'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_background_color, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/text'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_text_color, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/link'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_link_color, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/heading'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_heading_color, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/colors/button'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_button_color, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/border'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_border, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/effects'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_effects, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/layout'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_layout, {
    name: name,
    variation: variation
  })), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: parentMenu + '/css'
  }, (0,external_wp_element_namespaceObject.createElement)(screen_css, {
    name: name
  })), !!(blockStyleVariations !== null && blockStyleVariations !== void 0 && blockStyleVariations.length) && (0,external_wp_element_namespaceObject.createElement)(BlockStylesNavigationScreens, {
    parentMenu: parentMenu,
    blockStyles: blockStyleVariations,
    blockName: name
  }));
}

function GlobalStylesStyleBook(_ref5) {
  let {
    onClose
  } = _ref5;
  const navigator = (0,external_wp_components_namespaceObject.__experimentalUseNavigator)();
  const {
    path
  } = navigator.location;
  return (0,external_wp_element_namespaceObject.createElement)(style_book, {
    isSelected: blockName => // Match '/blocks/core%2Fbutton' and
    // '/blocks/core%2Fbutton/typography', but not
    // '/blocks/core%2Fbuttons'.
    path === `/blocks/${encodeURIComponent(blockName)}` || path.startsWith(`/blocks/${encodeURIComponent(blockName)}/`),
    onSelect: blockName => {
      // Now go to the selected block.
      navigator.goTo('/blocks/' + encodeURIComponent(blockName));
    },
    onClose: onClose
  });
}

function GlobalStylesUI(_ref6) {
  let {
    isStyleBookOpened,
    onCloseStyleBook
  } = _ref6;
  const blocks = (0,external_wp_blocks_namespaceObject.getBlockTypes)();
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNavigatorProvider, {
    className: "edit-site-global-styles-sidebar__navigator-provider",
    initialPath: "/"
  }, (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: "/"
  }, (0,external_wp_element_namespaceObject.createElement)(screen_root, null)), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: "/variations"
  }, (0,external_wp_element_namespaceObject.createElement)(screen_style_variations, null)), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    path: "/blocks"
  }, (0,external_wp_element_namespaceObject.createElement)(screen_block_list, null)), blocks.map(block => (0,external_wp_element_namespaceObject.createElement)(GlobalStylesNavigationScreen, {
    key: 'menu-block-' + block.name,
    path: '/blocks/' + encodeURIComponent(block.name)
  }, (0,external_wp_element_namespaceObject.createElement)(screen_block, {
    name: block.name
  }))), (0,external_wp_element_namespaceObject.createElement)(ContextScreens, null), blocks.map(block => (0,external_wp_element_namespaceObject.createElement)(ContextScreens, {
    key: 'screens-block-' + block.name,
    name: block.name,
    parentMenu: '/blocks/' + encodeURIComponent(block.name)
  })), blocks.map((block, index) => {
    return (0,external_wp_element_namespaceObject.createElement)(BlockStyleVariationsScreens, {
      key: 'screens-block-styles-' + block.name + index,
      name: block.name
    });
  }), isStyleBookOpened && (0,external_wp_element_namespaceObject.createElement)(GlobalStylesStyleBook, {
    onClose: onCloseStyleBook
  }), (0,external_wp_element_namespaceObject.createElement)(GlobalStylesActionMenu, null));
}


/* harmony default export */ var ui = (GlobalStylesUI);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles/index.js


;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/global-styles-sidebar.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





function GlobalStylesSidebar() {
  const [isStyleBookOpened, setIsStyleBookOpened] = (0,external_wp_element_namespaceObject.useState)(false);
  const editorMode = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getEditorMode(), []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (editorMode !== 'visual') {
      setIsStyleBookOpened(false);
    }
  }, [editorMode]);
  return (0,external_wp_element_namespaceObject.createElement)(DefaultSidebar, {
    className: "edit-site-global-styles-sidebar",
    identifier: "edit-site/global-styles",
    title: (0,external_wp_i18n_namespaceObject.__)('Styles'),
    icon: library_styles,
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close Styles sidebar'),
    panelClassName: "edit-site-global-styles-sidebar__panel",
    header: (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Flex, {
      className: "edit-site-global-styles-sidebar__header"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexBlock, {
      style: {
        minWidth: 'min-content'
      }
    }, (0,external_wp_element_namespaceObject.createElement)("strong", null, (0,external_wp_i18n_namespaceObject.__)('Styles'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      icon: library_seen,
      label: (0,external_wp_i18n_namespaceObject.__)('Style Book'),
      isPressed: isStyleBookOpened,
      disabled: editorMode !== 'visual',
      onClick: () => {
        setIsStyleBookOpened(!isStyleBookOpened);
      }
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.FlexItem, null, (0,external_wp_element_namespaceObject.createElement)(GlobalStylesMenuSlot, null)))
  }, (0,external_wp_element_namespaceObject.createElement)(ui, {
    isStyleBookOpened: isStyleBookOpened,
    onCloseStyleBook: () => setIsStyleBookOpened(false)
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/constants.js
const SIDEBAR_TEMPLATE = 'edit-site/template';
const SIDEBAR_BLOCK = 'edit-site/block-inspector';

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/settings-header/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




const SettingsHeader = _ref => {
  let {
    sidebarName
  } = _ref;
  const {
    enableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);

  const openTemplateSettings = () => enableComplementaryArea(constants_STORE_NAME, SIDEBAR_TEMPLATE);

  const openBlockSettings = () => enableComplementaryArea(constants_STORE_NAME, SIDEBAR_BLOCK);

  const [templateAriaLabel, templateActiveClass] = sidebarName === SIDEBAR_TEMPLATE ? // translators: ARIA label for the Template sidebar tab, selected.
  [(0,external_wp_i18n_namespaceObject.__)('Template (selected)'), 'is-active'] : // translators: ARIA label for the Template Settings Sidebar tab, not selected.
  [(0,external_wp_i18n_namespaceObject.__)('Template'), ''];
  const [blockAriaLabel, blockActiveClass] = sidebarName === SIDEBAR_BLOCK ? // translators: ARIA label for the Block Settings Sidebar tab, selected.
  [(0,external_wp_i18n_namespaceObject.__)('Block (selected)'), 'is-active'] : // translators: ARIA label for the Block Settings Sidebar tab, not selected.
  [(0,external_wp_i18n_namespaceObject.__)('Block'), ''];
  /* Use a list so screen readers will announce how many tabs there are. */

  return (0,external_wp_element_namespaceObject.createElement)("ul", null, (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: openTemplateSettings,
    className: `edit-site-sidebar-edit-mode__panel-tab ${templateActiveClass}`,
    "aria-label": templateAriaLabel // translators: Data label for the Template Settings Sidebar tab.
    ,
    "data-label": (0,external_wp_i18n_namespaceObject.__)('Template')
  }, // translators: Text label for the Template Settings Sidebar tab.
  (0,external_wp_i18n_namespaceObject.__)('Template'))), (0,external_wp_element_namespaceObject.createElement)("li", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    onClick: openBlockSettings,
    className: `edit-site-sidebar-edit-mode__panel-tab ${blockActiveClass}`,
    "aria-label": blockAriaLabel // translators: Data label for the Block Settings Sidebar tab.
    ,
    "data-label": (0,external_wp_i18n_namespaceObject.__)('Block')
  }, // translators: Text label for the Block Settings Sidebar tab.
  (0,external_wp_i18n_namespaceObject.__)('Block'))));
};

/* harmony default export */ var settings_header = (SettingsHeader);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/template-card/template-actions.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function Actions(_ref) {
  let {
    template
  } = _ref;
  const {
    revertTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isRevertable = isTemplateRevertable(template);

  if (!isRevertable) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    icon: more_vertical,
    label: (0,external_wp_i18n_namespaceObject.__)('Actions'),
    className: "edit-site-template-card__actions",
    toggleProps: {
      isSmall: true
    }
  }, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      info: (0,external_wp_i18n_namespaceObject.__)('Use the template as supplied by the theme.'),
      onClick: () => {
        revertTemplate(template);
        onClose();
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Clear customizations')));
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/template-card/template-areas.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function TemplateAreaItem(_ref) {
  let {
    area,
    clientId
  } = _ref;
  const {
    selectBlock,
    toggleBlockHighlight
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const templatePartArea = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const defaultAreas = select(external_wp_editor_namespaceObject.store).__experimentalGetDefaultTemplatePartAreas();

    return defaultAreas.find(defaultArea => defaultArea.area === area);
  }, [area]);

  const highlightBlock = () => toggleBlockHighlight(clientId, true);

  const cancelHighlightBlock = () => toggleBlockHighlight(clientId, false);

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-site-template-card__template-areas-item",
    icon: templatePartArea === null || templatePartArea === void 0 ? void 0 : templatePartArea.icon,
    onMouseOver: highlightBlock,
    onMouseLeave: cancelHighlightBlock,
    onFocus: highlightBlock,
    onBlur: cancelHighlightBlock,
    onClick: () => {
      selectBlock(clientId);
    }
  }, templatePartArea === null || templatePartArea === void 0 ? void 0 : templatePartArea.label);
}

function TemplateAreas() {
  const templateParts = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getCurrentTemplateTemplateParts(), []);

  if (!templateParts.length) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("section", {
    className: "edit-site-template-card__template-areas"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    level: 3,
    className: "edit-site-template-card__template-areas-title"
  }, (0,external_wp_i18n_namespaceObject.__)('Areas')), (0,external_wp_element_namespaceObject.createElement)("ul", {
    className: "edit-site-template-card__template-areas-list"
  }, templateParts.map(_ref2 => {
    let {
      templatePart,
      block
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)("li", {
      key: templatePart.slug
    }, (0,external_wp_element_namespaceObject.createElement)(TemplateAreaItem, {
      area: templatePart.area,
      clientId: block.clientId
    }));
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/template-card/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




function TemplateCard() {
  const {
    info: {
      title,
      description,
      icon
    },
    template
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostType,
      getEditedPostId
    } = select(store_store);
    const {
      getEditedEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);
    const {
      __experimentalGetTemplateInfo: getTemplateInfo
    } = select(external_wp_editor_namespaceObject.store);
    const postType = getEditedPostType();
    const postId = getEditedPostId();
    const record = getEditedEntityRecord('postType', postType, postId);
    const info = record ? getTemplateInfo(record) : {};
    return {
      info,
      template: record
    };
  }, []);

  if (!title && !description) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-card"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
    className: "edit-site-template-card__icon",
    icon: icon
  }), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-card__content"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-card__header"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-site-template-card__title"
  }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), (0,external_wp_element_namespaceObject.createElement)(Actions, {
    template: template
  })), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-card__description"
  }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(description)), (0,external_wp_element_namespaceObject.createElement)(TemplateAreas, null)));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */








const {
  Slot: InspectorSlot,
  Fill: InspectorFill
} = (0,external_wp_components_namespaceObject.createSlotFill)('EditSiteSidebarInspector');
const SidebarInspectorFill = InspectorFill;
function SidebarComplementaryAreaFills() {
  const {
    sidebar,
    isEditorSidebarOpened,
    hasBlockSelection,
    supportsGlobalStyles
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const _sidebar = select(store).getActiveComplementaryArea(constants_STORE_NAME);

    const _isEditorSidebarOpened = [SIDEBAR_BLOCK, SIDEBAR_TEMPLATE].includes(_sidebar);

    const settings = select(store_store).getSettings();
    return {
      sidebar: _sidebar,
      isEditorSidebarOpened: _isEditorSidebarOpened,
      hasBlockSelection: !!select(external_wp_blockEditor_namespaceObject.store).getBlockSelectionStart(),
      supportsGlobalStyles: !(settings !== null && settings !== void 0 && settings.supportsTemplatePartsMode)
    };
  }, []);
  const {
    enableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isEditorSidebarOpened) return;

    if (hasBlockSelection) {
      enableComplementaryArea(constants_STORE_NAME, SIDEBAR_BLOCK);
    } else {
      enableComplementaryArea(constants_STORE_NAME, SIDEBAR_TEMPLATE);
    }
  }, [hasBlockSelection, isEditorSidebarOpened]);
  let sidebarName = sidebar;

  if (!isEditorSidebarOpened) {
    sidebarName = hasBlockSelection ? SIDEBAR_BLOCK : SIDEBAR_TEMPLATE;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(DefaultSidebar, {
    identifier: sidebarName,
    title: (0,external_wp_i18n_namespaceObject.__)('Settings'),
    icon: (0,external_wp_i18n_namespaceObject.isRTL)() ? drawer_left : drawer_right,
    closeLabel: (0,external_wp_i18n_namespaceObject.__)('Close settings sidebar'),
    header: (0,external_wp_element_namespaceObject.createElement)(settings_header, {
      sidebarName: sidebarName
    }),
    headerClassName: "edit-site-sidebar-edit-mode__panel-tabs"
  }, sidebarName === SIDEBAR_TEMPLATE && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.PanelBody, null, (0,external_wp_element_namespaceObject.createElement)(TemplateCard, null)), sidebarName === SIDEBAR_BLOCK && (0,external_wp_element_namespaceObject.createElement)(InspectorSlot, {
    bubblesVirtually: true
  })), supportsGlobalStyles && (0,external_wp_element_namespaceObject.createElement)(GlobalStylesSidebar, null));
}

;// CONCATENATED MODULE: external ["wp","reusableBlocks"]
var external_wp_reusableBlocks_namespaceObject = window["wp"]["reusableBlocks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/inserter-media-categories.js
/**
 * The `edit-site` settings here need to be in sync with the corresponding ones in `site-editor` package.
 * See `packages/edit-site/src/components/block-editor/inserter-media-categories.js`.
 *
 * In the future we could consider creating an Openvese package that can be used in both `editor` and `site-editor`.
 * The rest of the settings would still need to be in sync though.
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/block-editor').InserterMediaRequest} InserterMediaRequest */

/** @typedef {import('@wordpress/block-editor').InserterMediaItem} InserterMediaItem */

/**
 * Interface for inserter media category labels.
 *
 * @typedef {Object} InserterMediaCategoryLabels
 * @property {string} name                    General name of the media category. It's used in the inserter media items list.
 * @property {string} [search_items='Search'] Label for searching items. Default is ‘Search Posts’ / ‘Search Pages’.
 */

/**
 * Interface for inserter media category.
 *
 * @typedef {Object} InserterMediaCategory
 * @property {string}                                                 name                 The name of the media category, that should be unique among all media categories.
 * @property {InserterMediaCategoryLabels}                            labels               Labels for the media category.
 * @property {('image'|'audio'|'video')}                              mediaType            The media type of the media category.
 * @property {(InserterMediaRequest) => Promise<InserterMediaItem[]>} fetch                The function to fetch media items for the category.
 * @property {(InserterMediaItem) => string}                          [getReportUrl]       If the media category supports reporting media items, this function should return
 *                                                                                         the report url for the media item. It accepts the `InserterMediaItem` as an argument.
 * @property {boolean}                                                [isExternalResource] If the media category is an external resource, this should be set to true.
 *                                                                                         This is used to avoid making a request to the external resource when the user
 *                                                                                         opens the inserter for the first time.
 */

const getExternalLink = (url, text) => `<a ${getExternalLinkAttributes(url)}>${text}</a>`;

const getExternalLinkAttributes = url => `href="${url}" target="_blank" rel="noreferrer noopener"`;

const getOpenverseLicense = (license, licenseVersion) => {
  let licenseName = license.trim(); // PDM has no abbreviation

  if (license !== 'pdm') {
    licenseName = license.toUpperCase().replace('SAMPLING', 'Sampling');
  } // If version is known, append version to the name.
  // The license has to have a version to be valid. Only
  // PDM (public domain mark) doesn't have a version.


  if (licenseVersion) {
    licenseName += ` ${licenseVersion}`;
  } // For licenses other than public-domain marks, prepend 'CC' to the name.


  if (!['pdm', 'cc0'].includes(license)) {
    licenseName = `CC ${licenseName}`;
  }

  return licenseName;
};

const getOpenverseCaption = item => {
  const {
    title,
    foreign_landing_url: foreignLandingUrl,
    creator,
    creator_url: creatorUrl,
    license,
    license_version: licenseVersion,
    license_url: licenseUrl
  } = item;
  const fullLicense = getOpenverseLicense(license, licenseVersion);

  const _creator = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(creator);

  let _caption;

  if (_creator) {
    _caption = title ? (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Title of a media work from Openverse; %2s: Name of the work's creator; %3s: Work's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('"%1$s" by %2$s/ %3$s', 'caption'), getExternalLink(foreignLandingUrl, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), creatorUrl ? getExternalLink(creatorUrl, _creator) : _creator, licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense) : (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Link attributes for a given Openverse media work; %2s: Name of the work's creator; %3s: Works's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('<a %1$s>Work</a> by %2$s/ %3$s', 'caption'), getExternalLinkAttributes(foreignLandingUrl), creatorUrl ? getExternalLink(creatorUrl, _creator) : _creator, licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense);
  } else {
    _caption = title ? (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Title of a media work from Openverse; %2s: Work's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('"%1$s"/ %2$s', 'caption'), getExternalLink(foreignLandingUrl, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense) : (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %1s: Link attributes for a given Openverse media work; %2s: Works's licence e.g: "CC0 1.0".
    (0,external_wp_i18n_namespaceObject._x)('<a %1$s>Work</a>/ %3$s', 'caption'), getExternalLinkAttributes(foreignLandingUrl), licenseUrl ? getExternalLink(`${licenseUrl}?ref=openverse`, fullLicense) : fullLicense);
  }

  return _caption.replace(/\s{2}/g, ' ');
};

const coreMediaFetch = async function () {
  let query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  const mediaItems = await (0,external_wp_data_namespaceObject.resolveSelect)(external_wp_coreData_namespaceObject.store).getMediaItems({ ...query,
    orderBy: !!(query !== null && query !== void 0 && query.search) ? 'relevance' : 'date'
  });
  return mediaItems.map(mediaItem => {
    var _mediaItem$media_deta, _mediaItem$media_deta2, _mediaItem$media_deta3, _mediaItem$caption;

    return { ...mediaItem,
      alt: mediaItem.alt_text,
      url: mediaItem.source_url,
      previewUrl: (_mediaItem$media_deta = mediaItem.media_details) === null || _mediaItem$media_deta === void 0 ? void 0 : (_mediaItem$media_deta2 = _mediaItem$media_deta.sizes) === null || _mediaItem$media_deta2 === void 0 ? void 0 : (_mediaItem$media_deta3 = _mediaItem$media_deta2.medium) === null || _mediaItem$media_deta3 === void 0 ? void 0 : _mediaItem$media_deta3.source_url,
      caption: (_mediaItem$caption = mediaItem.caption) === null || _mediaItem$caption === void 0 ? void 0 : _mediaItem$caption.raw
    };
  });
};
/** @type {InserterMediaCategory[]} */


const inserterMediaCategories = [{
  name: 'images',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Images'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search images')
  },
  mediaType: 'image',

  async fetch() {
    let query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    return coreMediaFetch({ ...query,
      media_type: 'image'
    });
  }

}, {
  name: 'videos',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Videos'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search videos')
  },
  mediaType: 'video',

  async fetch() {
    let query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    return coreMediaFetch({ ...query,
      media_type: 'video'
    });
  }

}, {
  name: 'audio',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Audio'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search audio')
  },
  mediaType: 'audio',

  async fetch() {
    let query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    return coreMediaFetch({ ...query,
      media_type: 'audio'
    });
  }

}, {
  name: 'openverse',
  labels: {
    name: (0,external_wp_i18n_namespaceObject.__)('Openverse'),
    search_items: (0,external_wp_i18n_namespaceObject.__)('Search Openverse')
  },
  mediaType: 'image',

  async fetch() {
    let query = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    const defaultArgs = {
      mature: false,
      excluded_source: 'flickr,inaturalist,wikimedia',
      license: 'pdm,cc0'
    };
    const finalQuery = { ...query,
      ...defaultArgs
    };
    const mapFromInserterMediaRequest = {
      per_page: 'page_size',
      search: 'q'
    };
    const url = new URL('https://api.openverse.engineering/v1/images/');
    Object.entries(finalQuery).forEach(_ref => {
      let [key, value] = _ref;
      const queryKey = mapFromInserterMediaRequest[key] || key;
      url.searchParams.set(queryKey, value);
    });
    const response = await window.fetch(url, {
      headers: {
        'User-Agent': 'WordPress/inserter-media-fetch'
      }
    });
    const jsonResponse = await response.json();
    const results = jsonResponse.results;
    return results.map(result => {
      var _result$title;

      return { ...result,
        // This is a temp solution for better titles, until Openverse API
        // completes the cleaning up of some titles of their upstream data.
        title: (_result$title = result.title) !== null && _result$title !== void 0 && _result$title.toLowerCase().startsWith('file:') ? result.title.slice(5) : result.title,
        sourceId: result.id,
        id: undefined,
        caption: getOpenverseCaption(result),
        previewUrl: result.thumbnail
      };
    });
  },

  getReportUrl: _ref2 => {
    let {
      sourceId
    } = _ref2;
    return `https://wordpress.org/openverse/image/${sourceId}/report/`;
  },
  isExternalResource: true
}];
/* harmony default export */ var inserter_media_categories = (inserterMediaCategories);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-part-converter/convert-to-regular.js


/**
 * WordPress dependencies
 */




function ConvertToRegularBlocks(_ref) {
  let {
    clientId
  } = _ref;
  const {
    getBlocks
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const {
    replaceBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const canRemove = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).canRemoveBlock(clientId), [clientId]);

  if (!canRemove) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      onClick: () => {
        replaceBlocks(clientId, getBlocks(clientId));
        onClose();
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Detach blocks from template part'));
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-part-converter/convert-to-template-part.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




function ConvertToTemplatePart(_ref) {
  let {
    clientIds,
    blocks
  } = _ref;
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    replaceBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    saveEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    createSuccessNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const existingTemplateParts = useExistingTemplateParts();
  const {
    canCreate
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      supportsTemplatePartsMode
    } = select(store_store).getSettings();
    return {
      canCreate: !supportsTemplatePartsMode
    };
  }, []);

  if (!canCreate) {
    return null;
  }

  const onConvert = async _ref2 => {
    let {
      title,
      area
    } = _ref2;
    const uniqueTitle = getUniqueTemplatePartTitle(title, existingTemplateParts);
    const cleanSlug = getCleanTemplatePartSlug(uniqueTitle);
    const templatePart = await saveEntityRecord('postType', 'wp_template_part', {
      slug: cleanSlug,
      title: uniqueTitle,
      content: (0,external_wp_blocks_namespaceObject.serialize)(blocks),
      area
    });
    replaceBlocks(clientIds, (0,external_wp_blocks_namespaceObject.createBlock)('core/template-part', {
      slug: templatePart.slug,
      theme: templatePart.theme
    }));
    createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Template part created.'), {
      type: 'snackbar'
    }); // The modal and this component will be unmounted because of `replaceBlocks` above,
    // so no need to call `closeModal` or `onClose`.
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, null, () => (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    icon: symbol_filled,
    onClick: () => {
      setIsModalOpen(true);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Create Template part'))), isModalOpen && (0,external_wp_element_namespaceObject.createElement)(CreateTemplatePartModal, {
    closeModal: () => {
      setIsModalOpen(false);
    },
    onCreate: onConvert
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-part-converter/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



function TemplatePartConverter() {
  var _blocks$;

  const {
    clientIds,
    blocks
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSelectedBlockClientIds,
      getBlocksByClientId
    } = select(external_wp_blockEditor_namespaceObject.store);
    const selectedBlockClientIds = getSelectedBlockClientIds();
    return {
      clientIds: selectedBlockClientIds,
      blocks: getBlocksByClientId(selectedBlockClientIds)
    };
  }, []); // Allow converting a single template part to standard blocks.

  if (blocks.length === 1 && ((_blocks$ = blocks[0]) === null || _blocks$ === void 0 ? void 0 : _blocks$.name) === 'core/template-part') {
    return (0,external_wp_element_namespaceObject.createElement)(ConvertToRegularBlocks, {
      clientId: clientIds[0]
    });
  }

  return (0,external_wp_element_namespaceObject.createElement)(ConvertToTemplatePart, {
    clientIds: clientIds,
    blocks: blocks
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-left.js


/**
 * WordPress dependencies
 */

const arrowLeft = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 11.2H6.8l3.7-3.7-1-1L3.9 12l5.6 5.5 1-1-3.7-3.7H20z"
}));
/* harmony default export */ var arrow_left = (arrowLeft);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/back-button.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function BackButton() {
  var _location$state;

  const location = useLocation();
  const history = useHistory();
  const isTemplatePart = location.params.postType === 'wp_template_part';
  const previousTemplateId = (_location$state = location.state) === null || _location$state === void 0 ? void 0 : _location$state.fromTemplateId;

  if (!isTemplatePart || !previousTemplateId) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "edit-site-visual-editor__back-button",
    icon: arrow_left,
    onClick: () => {
      history.back();
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Back'));
}

/* harmony default export */ var back_button = (BackButton);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/resize-handle.js


/**
 * WordPress dependencies
 */



const DELTA_DISTANCE = 20; // The distance to resize per keydown in pixels.

function ResizeHandle(_ref) {
  let {
    variation = 'default',
    direction,
    resizeWidthBy
  } = _ref;

  function handleKeyDown(event) {
    const {
      keyCode
    } = event;

    if (direction === 'left' && keyCode === external_wp_keycodes_namespaceObject.LEFT || direction === 'right' && keyCode === external_wp_keycodes_namespaceObject.RIGHT) {
      resizeWidthBy(DELTA_DISTANCE);
    } else if (direction === 'left' && keyCode === external_wp_keycodes_namespaceObject.RIGHT || direction === 'right' && keyCode === external_wp_keycodes_namespaceObject.LEFT) {
      resizeWidthBy(-DELTA_DISTANCE);
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("button", {
    className: `resizable-editor__drag-handle is-${direction} is-variation-${variation}`,
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Drag to resize'),
    "aria-describedby": `resizable-editor__resize-help-${direction}`,
    onKeyDown: handleKeyDown
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    id: `resizable-editor__resize-help-${direction}`
  }, (0,external_wp_i18n_namespaceObject.__)('Use left and right arrow keys to resize the canvas.')));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/resizable-editor.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

 // Removes the inline styles in the drag handles.

const HANDLE_STYLES_OVERRIDE = {
  position: undefined,
  userSelect: undefined,
  cursor: undefined,
  width: undefined,
  height: undefined,
  top: undefined,
  right: undefined,
  bottom: undefined,
  left: undefined
};

function ResizableEditor(_ref) {
  let {
    enableResizing,
    height,
    children
  } = _ref;
  const [width, setWidth] = (0,external_wp_element_namespaceObject.useState)('100%');
  const resizableRef = (0,external_wp_element_namespaceObject.useRef)();
  const resizeWidthBy = (0,external_wp_element_namespaceObject.useCallback)(deltaPixels => {
    if (resizableRef.current) {
      setWidth(resizableRef.current.offsetWidth + deltaPixels);
    }
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ResizableBox, {
    ref: api => {
      resizableRef.current = api === null || api === void 0 ? void 0 : api.resizable;
    },
    size: {
      width: enableResizing ? width : '100%',
      height: enableResizing && height ? height : '100%'
    },
    onResizeStop: (event, direction, element) => {
      setWidth(element.style.width);
    },
    minWidth: 300,
    maxWidth: "100%",
    maxHeight: "100%",
    enable: {
      right: enableResizing,
      left: enableResizing
    },
    showHandle: enableResizing // The editor is centered horizontally, resizing it only
    // moves half the distance. Hence double the ratio to correctly
    // align the cursor to the resizer handle.
    ,
    resizeRatio: 2,
    handleComponent: {
      left: (0,external_wp_element_namespaceObject.createElement)(ResizeHandle, {
        direction: "left",
        resizeWidthBy: resizeWidthBy
      }),
      right: (0,external_wp_element_namespaceObject.createElement)(ResizeHandle, {
        direction: "right",
        resizeWidthBy: resizeWidthBy
      })
    },
    handleClasses: undefined,
    handleStyles: {
      left: HANDLE_STYLES_OVERRIDE,
      right: HANDLE_STYLES_OVERRIDE
    }
  }, children);
}

/* harmony default export */ var resizable_editor = (ResizableEditor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/editor-canvas.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function EditorCanvas(_ref) {
  let {
    enableResizing,
    settings,
    children,
    ...props
  } = _ref;
  const {
    canvasMode,
    deviceType,
    isZoomOutMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    deviceType: select(store_store).__experimentalGetPreviewDeviceType(),
    isZoomOutMode: select(external_wp_blockEditor_namespaceObject.store).__unstableGetEditorMode() === 'zoom-out',
    canvasMode: unlock(select(store_store)).getCanvasMode()
  }), []);
  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const deviceStyles = (0,external_wp_blockEditor_namespaceObject.__experimentalUseResizeCanvas)(deviceType);
  const mouseMoveTypingRef = (0,external_wp_blockEditor_namespaceObject.__unstableUseMouseMoveTypingReset)();
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableIframe, extends_extends({
    scale: isZoomOutMode && 0.45 || undefined,
    frameSize: isZoomOutMode ? 100 : undefined,
    style: enableResizing ? {} : deviceStyles,
    head: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableEditorStyles, {
      styles: settings.styles
    }), (0,external_wp_element_namespaceObject.createElement)("style", null, // Forming a "block formatting context" to prevent margin collapsing.
    // @see https://developer.mozilla.org/en-US/docs/Web/Guide/CSS/Block_formatting_context
    `.is-root-container { display: flow-root; }
							body { position: relative;
							${canvasMode === 'view' ? 'cursor: pointer;' : ''}}}`), enableResizing && (0,external_wp_element_namespaceObject.createElement)("style", null, // Some themes will have `min-height: 100vh` for the root container,
    // which isn't a requirement in auto resize mode.
    `.is-root-container { min-height: 0 !important; }`)),
    ref: mouseMoveTypingRef,
    name: "editor-canvas",
    className: "edit-site-visual-editor__editor-canvas"
  }, props, {
    role: canvasMode === 'view' ? 'button' : undefined,
    onClick: canvasMode === 'view' ? () => setCanvasMode('edit') : undefined,
    readonly: canvasMode === 'view'
  }), settings.svgFilters, children);
}

/* harmony default export */ var editor_canvas = (EditorCanvas);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/block-editor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */










const {
  ExperimentalBlockEditorProvider: block_editor_ExperimentalBlockEditorProvider
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const LAYOUT = {
  type: 'default',
  // At the root level of the site editor, no alignments should be allowed.
  alignments: []
};
function BlockEditor() {
  var _storedSettings$__exp, _storedSettings$__exp2;

  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    storedSettings,
    templateType,
    canvasMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings,
      getEditedPostType,
      getCanvasMode
    } = unlock(select(store_store));
    return {
      storedSettings: getSettings(setIsInserterOpened),
      templateType: getEditedPostType(),
      canvasMode: getCanvasMode()
    };
  }, [setIsInserterOpened]);
  const settingsBlockPatterns = (_storedSettings$__exp = storedSettings.__experimentalAdditionalBlockPatterns) !== null && _storedSettings$__exp !== void 0 ? _storedSettings$__exp : // WP 6.0
  storedSettings.__experimentalBlockPatterns; // WP 5.9

  const settingsBlockPatternCategories = (_storedSettings$__exp2 = storedSettings.__experimentalAdditionalBlockPatternCategories) !== null && _storedSettings$__exp2 !== void 0 ? _storedSettings$__exp2 : // WP 6.0
  storedSettings.__experimentalBlockPatternCategories; // WP 5.9

  const {
    restBlockPatterns,
    restBlockPatternCategories
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    restBlockPatterns: select(external_wp_coreData_namespaceObject.store).getBlockPatterns(),
    restBlockPatternCategories: select(external_wp_coreData_namespaceObject.store).getBlockPatternCategories()
  }), []);
  const blockPatterns = (0,external_wp_element_namespaceObject.useMemo)(() => [...(settingsBlockPatterns || []), ...(restBlockPatterns || [])].filter((x, index, arr) => index === arr.findIndex(y => x.name === y.name)).filter(_ref => {
    let {
      postTypes
    } = _ref;
    return !postTypes || Array.isArray(postTypes) && postTypes.includes(templateType);
  }), [settingsBlockPatterns, restBlockPatterns, templateType]);
  const blockPatternCategories = (0,external_wp_element_namespaceObject.useMemo)(() => [...(settingsBlockPatternCategories || []), ...(restBlockPatternCategories || [])].filter((x, index, arr) => index === arr.findIndex(y => x.name === y.name)), [settingsBlockPatternCategories, restBlockPatternCategories]);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => {
    const {
      __experimentalAdditionalBlockPatterns,
      __experimentalAdditionalBlockPatternCategories,
      ...restStoredSettings
    } = storedSettings;
    return { ...restStoredSettings,
      inserterMediaCategories: inserter_media_categories,
      __experimentalBlockPatterns: blockPatterns,
      __experimentalBlockPatternCategories: blockPatternCategories
    };
  }, [storedSettings, blockPatterns, blockPatternCategories]);
  const [blocks, onInput, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)('postType', templateType);
  const contentRef = (0,external_wp_element_namespaceObject.useRef)();
  const mergedRefs = (0,external_wp_compose_namespaceObject.useMergeRefs)([contentRef, (0,external_wp_blockEditor_namespaceObject.__unstableUseClipboardHandler)(), (0,external_wp_blockEditor_namespaceObject.__unstableUseTypingObserver)()]);
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('small', '<');
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const [resizeObserver, sizes] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const isTemplatePart = templateType === 'wp_template_part';
  const hasBlocks = blocks.length !== 0;
  const enableResizing = isTemplatePart && canvasMode !== 'view' && // Disable resizing in mobile viewport.
  !isMobileViewport;
  const isViewMode = canvasMode === 'view';
  const showBlockAppender = isTemplatePart && hasBlocks || isViewMode ? false : undefined;
  return (0,external_wp_element_namespaceObject.createElement)(block_editor_ExperimentalBlockEditorProvider, {
    settings: settings,
    value: blocks,
    onInput: onInput,
    onChange: onChange,
    useSubRegistry: false
  }, (0,external_wp_element_namespaceObject.createElement)(TemplatePartConverter, null), (0,external_wp_element_namespaceObject.createElement)(SidebarInspectorFill, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockInspector, null)), (0,external_wp_element_namespaceObject.createElement)(style_book.Slot, null, _ref2 => {
    var _sizes$height;

    let [styleBook] = _ref2;
    return styleBook ? (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-site-visual-editor is-focus-mode"
    }, (0,external_wp_element_namespaceObject.createElement)(resizable_editor, {
      enableResizing: true
    }, styleBook)) : (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockTools, {
      className: classnames_default()('edit-site-visual-editor', {
        'is-focus-mode': isTemplatePart || !!styleBook,
        'is-view-mode': isViewMode
      }),
      __unstableContentRef: contentRef,
      onClick: event => {
        // Clear selected block when clicking on the gray background.
        if (event.target === event.currentTarget) {
          clearSelectedBlock();
        }
      }
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockEditorKeyboardShortcuts.Register, null), (0,external_wp_element_namespaceObject.createElement)(back_button, null), (0,external_wp_element_namespaceObject.createElement)(resizable_editor, {
      enableResizing: enableResizing,
      height: (_sizes$height = sizes.height) !== null && _sizes$height !== void 0 ? _sizes$height : '100%'
    }, (0,external_wp_element_namespaceObject.createElement)(editor_canvas, {
      enableResizing: enableResizing,
      settings: settings,
      contentRef: mergedRefs,
      readonly: canvasMode === 'view'
    }, resizeObserver, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockList, {
      className: "edit-site-block-editor__block-list wp-site-blocks",
      __experimentalLayout: LAYOUT,
      renderAppender: showBlockAppender
    }))));
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_reusableBlocks_namespaceObject.ReusableBlocksMenuItems, null));
}

// EXTERNAL MODULE: ./node_modules/react-autosize-textarea/lib/index.js
var lib = __webpack_require__(773);
;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/code-editor/code-editor-text-area.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





function CodeEditorTextArea(_ref) {
  let {
    value,
    onChange,
    onInput
  } = _ref;
  const [stateValue, setStateValue] = (0,external_wp_element_namespaceObject.useState)(value);
  const [isDirty, setIsDirty] = (0,external_wp_element_namespaceObject.useState)(false);
  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(CodeEditorTextArea);
  const valueRef = (0,external_wp_element_namespaceObject.useRef)();

  if (!isDirty && stateValue !== value) {
    setStateValue(value);
  }
  /**
   * Handles a textarea change event to notify the onChange prop callback and
   * reflect the new value in the component's own state. This marks the start
   * of the user's edits, if not already changed, preventing future props
   * changes to value from replacing the rendered value. This is expected to
   * be followed by a reset to dirty state via `stopEditing`.
   *
   * @see stopEditing
   *
   * @param {Event} event Change event.
   */


  const onChangeHandler = event => {
    const newValue = event.target.value;
    onInput(newValue);
    setStateValue(newValue);
    setIsDirty(true);
    valueRef.current = newValue;
  };
  /**
   * Function called when the user has completed their edits, responsible for
   * ensuring that changes, if made, are surfaced to the onPersist prop
   * callback and resetting dirty state.
   */


  const stopEditing = () => {
    if (isDirty) {
      onChange(stateValue);
      setIsDirty(false);
    }
  }; // Ensure changes aren't lost when component unmounts.


  (0,external_wp_element_namespaceObject.useEffect)(() => {
    return () => {
      if (valueRef.current) {
        onChange(valueRef.current);
      }
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "label",
    htmlFor: `code-editor-text-area-${instanceId}`
  }, (0,external_wp_i18n_namespaceObject.__)('Type text or HTML')), (0,external_wp_element_namespaceObject.createElement)(lib/* default */.Z, {
    autoComplete: "off",
    dir: "auto",
    value: stateValue,
    onChange: onChangeHandler,
    onBlur: stopEditing,
    className: "edit-site-code-editor-text-area",
    id: `code-editor-text-area-${instanceId}`,
    placeholder: (0,external_wp_i18n_namespaceObject.__)('Start writing with text or HTML')
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/code-editor/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function CodeEditor() {
  const {
    templateType,
    shortcut
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostType
    } = select(store_store);
    const {
      getShortcutRepresentation
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      templateType: getEditedPostType(),
      shortcut: getShortcutRepresentation('core/edit-site/toggle-mode')
    };
  }, []);
  const [contentStructure, setContent] = (0,external_wp_coreData_namespaceObject.useEntityProp)('postType', templateType, 'content');
  const [blocks,, onChange] = (0,external_wp_coreData_namespaceObject.useEntityBlockEditor)('postType', templateType); // Replicates the logic found in getEditedPostContent().

  let content;

  if (contentStructure instanceof Function) {
    content = contentStructure({
      blocks
    });
  } else if (blocks) {
    // If we have parsed blocks already, they should be our source of truth.
    // Parsing applies block deprecations and legacy block conversions that
    // unparsed content will not have.
    content = (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(blocks);
  } else {
    content = contentStructure;
  }

  const {
    switchEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-code-editor"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-code-editor__toolbar"
  }, (0,external_wp_element_namespaceObject.createElement)("h2", null, (0,external_wp_i18n_namespaceObject.__)('Editing code')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => switchEditorMode('visual'),
    shortcut: shortcut
  }, (0,external_wp_i18n_namespaceObject.__)('Exit code editor'))), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-code-editor__body"
  }, (0,external_wp_element_namespaceObject.createElement)(CodeEditorTextArea, {
    value: content,
    onChange: newContent => {
      onChange((0,external_wp_blocks_namespaceObject.parse)(newContent), {
        selection: undefined
      });
    },
    onInput: setContent
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcuts/edit-mode.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





function KeyboardShortcutsEditMode() {
  const {
    getEditorMode
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const isListViewOpen = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).isListViewOpened(), []);
  const isBlockInspectorOpen = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).getActiveComplementaryArea(store_store.name) === SIDEBAR_BLOCK, []);
  const {
    redo,
    undo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    setIsListViewOpened,
    switchEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const {
    replaceBlocks
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const {
    getBlockName,
    getSelectedBlockClientId,
    getBlockAttributes
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);

  const handleTextLevelShortcut = (event, level) => {
    event.preventDefault();
    const destinationBlockName = level === 0 ? 'core/paragraph' : 'core/heading';
    const currentClientId = getSelectedBlockClientId();

    if (currentClientId === null) {
      return;
    }

    const blockName = getBlockName(currentClientId);

    if (blockName !== 'core/paragraph' && blockName !== 'core/heading') {
      return;
    }

    const attributes = getBlockAttributes(currentClientId);
    const textAlign = blockName === 'core/paragraph' ? 'align' : 'textAlign';
    const destinationTextAlign = destinationBlockName === 'core/paragraph' ? 'align' : 'textAlign';
    replaceBlocks(currentClientId, (0,external_wp_blocks_namespaceObject.createBlock)(destinationBlockName, {
      level,
      content: attributes.content,
      ...{
        [destinationTextAlign]: attributes[textAlign]
      }
    }));
  };

  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/undo', event => {
    undo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/redo', event => {
    redo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/toggle-list-view', () => {
    setIsListViewOpened(!isListViewOpen);
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/toggle-block-settings-sidebar', event => {
    // This shortcut has no known clashes, but use preventDefault to prevent any
    // obscure shortcuts from triggering.
    event.preventDefault();

    if (isBlockInspectorOpen) {
      disableComplementaryArea(constants_STORE_NAME);
    } else {
      enableComplementaryArea(constants_STORE_NAME, SIDEBAR_BLOCK);
    }
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/toggle-mode', () => {
    switchEditorMode(getEditorMode() === 'visual' ? 'text' : 'visual');
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/transform-heading-to-paragraph', event => handleTextLevelShortcut(event, 0));
  [1, 2, 3, 4, 5, 6].forEach(level => {
    //the loop is based off on a constant therefore
    //the hook will execute the same way every time
    //eslint-disable-next-line react-hooks/rules-of-hooks
    (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)(`core/edit-site/transform-paragraph-to-heading-${level}`, event => handleTextLevelShortcut(event, level));
  });
  return null;
}

/* harmony default export */ var edit_mode = (KeyboardShortcutsEditMode);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js


/**
 * WordPress dependencies
 */

const close_close = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ var library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/secondary-sidebar/inserter-sidebar.js



/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function InserterSidebar() {
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const insertionPoint = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).__experimentalGetInsertionPoint(), []);
  const isMobile = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const TagName = !isMobile ? external_wp_components_namespaceObject.VisuallyHidden : 'div';
  const [inserterDialogRef, inserterDialogProps] = (0,external_wp_compose_namespaceObject.__experimentalUseDialog)({
    onClose: () => setIsInserterOpened(false),
    focusOnMount: null
  });
  const libraryRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    libraryRef.current.focusSearch();
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)("div", extends_extends({
    ref: inserterDialogRef
  }, inserterDialogProps, {
    className: "edit-site-editor__inserter-panel"
  }), (0,external_wp_element_namespaceObject.createElement)(TagName, {
    className: "edit-site-editor__inserter-panel-header"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    icon: library_close,
    label: (0,external_wp_i18n_namespaceObject.__)('Close block inserter'),
    onClick: () => setIsInserterOpened(false)
  })), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-editor__inserter-panel-content"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLibrary, {
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobile,
    rootClientId: insertionPoint.rootClientId,
    __experimentalInsertionIndex: insertionPoint.insertionIndex,
    __experimentalFilterValue: insertionPoint.filterValue,
    ref: libraryRef
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const focusOnMountRef = (0,external_wp_compose_namespaceObject.useFocusOnMount)('firstElement');
  const headerFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();
  const contentFocusReturnRef = (0,external_wp_compose_namespaceObject.useFocusReturn)();

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE && !event.defaultPrevented) {
      setIsListViewOpened(false);
    }
  }

  const instanceId = (0,external_wp_compose_namespaceObject.useInstanceId)(ListViewSidebar);
  const labelId = `edit-site-editor__list-view-panel-label-${instanceId}`;
  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    (0,external_wp_element_namespaceObject.createElement)("div", {
      "aria-labelledby": labelId,
      className: "edit-site-editor__list-view-panel",
      onKeyDown: closeOnEscape
    }, (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-site-editor__list-view-panel-header",
      ref: headerFocusReturnRef
    }, (0,external_wp_element_namespaceObject.createElement)("strong", {
      id: labelId
    }, (0,external_wp_i18n_namespaceObject.__)('List View')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
      icon: close_small,
      label: (0,external_wp_i18n_namespaceObject.__)('Close List View Sidebar'),
      onClick: () => setIsListViewOpened(false)
    })), (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-site-editor__list-view-panel-content",
      ref: (0,external_wp_compose_namespaceObject.useMergeRefs)([contentFocusReturnRef, focusOnMountRef])
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalListView, null)))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/welcome-guide/image.js

function WelcomeGuideImage(_ref) {
  let {
    nonAnimatedSrc,
    animatedSrc
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)("picture", {
    className: "edit-site-welcome-guide__image"
  }, (0,external_wp_element_namespaceObject.createElement)("source", {
    srcSet: nonAnimatedSrc,
    media: "(prefers-reduced-motion: reduce)"
  }), (0,external_wp_element_namespaceObject.createElement)("img", {
    src: animatedSrc,
    width: "312",
    height: "240",
    alt: ""
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/welcome-guide/editor.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function WelcomeGuideEditor() {
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const isActive = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'welcomeGuide'), []);

  if (!isActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Guide, {
    className: "edit-site-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to the site editor'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get Started'),
    onFinish: () => toggle('core/edit-site', 'welcomeGuide'),
    pages: [{
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/edit-your-site.svg?1",
        animatedSrc: "https://s.w.org/images/block-editor/edit-your-site.gif?1"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-site-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Edit your site')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('Design everything on your site — from the header right down to the footer — using blocks.')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.__)('Click <StylesIconImage /> to start designing your blocks, and choose your typography, layout, and colors.'), {
        StylesIconImage: (0,external_wp_element_namespaceObject.createElement)("img", {
          alt: (0,external_wp_i18n_namespaceObject.__)('styles'),
          src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12 4c-4.4 0-8 3.6-8 8v.1c0 4.1 3.2 7.5 7.2 7.9h.8c4.4 0 8-3.6 8-8s-3.6-8-8-8zm0 15V5c3.9 0 7 3.1 7 7s-3.1 7-7 7z' fill='%231E1E1E'/%3E%3C/svg%3E%0A"
        })
      })))
    }]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/welcome-guide/styles.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



function WelcomeGuideStyles() {
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const {
    isActive,
    isStylesOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const sidebar = select(store).getActiveComplementaryArea(store_store.name);
    return {
      isActive: !!select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'welcomeGuideStyles'),
      isStylesOpen: sidebar === 'edit-site/global-styles'
    };
  }, []);

  if (!isActive || !isStylesOpen) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Guide, {
    className: "edit-site-welcome-guide",
    contentLabel: (0,external_wp_i18n_namespaceObject.__)('Welcome to styles'),
    finishButtonText: (0,external_wp_i18n_namespaceObject.__)('Get Started'),
    onFinish: () => toggle('core/edit-site', 'welcomeGuideStyles'),
    pages: [{
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-to-styles.svg?1",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-to-styles.gif?1"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-site-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Welcome to Styles')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('Tweak your site, or give it a whole new look! Get creative — how about a new color palette for your buttons, or choosing a new font? Take a look at what you can do here.')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/set-the-design.svg?1",
        animatedSrc: "https://s.w.org/images/block-editor/set-the-design.gif?1"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-site-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Set the design')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('You can customize your site as much as you like with different colors, typography, and layouts. Or if you prefer, just leave it up to your theme to handle! ')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/personalize-blocks.svg?1",
        animatedSrc: "https://s.w.org/images/block-editor/personalize-blocks.gif?1"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-site-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Personalize blocks')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('You can adjust your blocks to ensure a cohesive experience across your site — add your unique colors to a branded Button block, or adjust the Heading block to your preferred size.')))
    }, {
      image: (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("h1", {
        className: "edit-site-welcome-guide__heading"
      }, (0,external_wp_i18n_namespaceObject.__)('Learn more')), (0,external_wp_element_namespaceObject.createElement)("p", {
        className: "edit-site-welcome-guide__text"
      }, (0,external_wp_i18n_namespaceObject.__)('New to block themes and styling your site? '), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/styles-overview/')
      }, (0,external_wp_i18n_namespaceObject.__)('Here’s a detailed guide to learn how to make the most of it.'))))
    }]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/welcome-guide/index.js


/**
 * Internal dependencies
 */


function WelcomeGuide() {
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideEditor, null), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideStyles, null));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/global-styles-renderer/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



const {
  useGlobalStylesOutput: global_styles_renderer_useGlobalStylesOutput
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);

function useGlobalStylesRenderer() {
  const [styles, settings, svgFilters] = global_styles_renderer_useGlobalStylesOutput();
  const {
    getSettings
  } = (0,external_wp_data_namespaceObject.useSelect)(store_store);
  const {
    updateSettings
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    var _currentStoreSettings;

    if (!styles || !settings) {
      return;
    }

    const currentStoreSettings = getSettings();
    const nonGlobalStyles = Object.values((_currentStoreSettings = currentStoreSettings.styles) !== null && _currentStoreSettings !== void 0 ? _currentStoreSettings : []).filter(style => !style.isGlobalStyles);
    updateSettings({ ...currentStoreSettings,
      styles: [...nonGlobalStyles, ...styles],
      svgFilters,
      __experimentalFeatures: settings
    });
  }, [styles, settings]);
}

function GlobalStylesRenderer() {
  useGlobalStylesRenderer();
  return null;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/routes/use-title.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function useTitle(title) {
  const location = useLocation();
  const siteTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEntityReco;

    return (_select$getEntityReco = select(external_wp_coreData_namespaceObject.store).getEntityRecord('root', 'site')) === null || _select$getEntityReco === void 0 ? void 0 : _select$getEntityReco.title;
  }, []);
  const isInitialLocationRef = (0,external_wp_element_namespaceObject.useRef)(true);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    isInitialLocationRef.current = false;
  }, [location]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // Don't update or announce the title for initial page load.
    if (isInitialLocationRef.current) {
      return;
    }

    if (title && siteTitle) {
      // @see https://github.com/WordPress/wordpress-develop/blob/94849898192d271d533e09756007e176feb80697/src/wp-admin/admin-header.php#L67-L68
      const formattedTitle = (0,external_wp_i18n_namespaceObject.sprintf)(
      /* translators: Admin screen title. 1: Admin screen name, 2: Network or site name. */
      (0,external_wp_i18n_namespaceObject.__)('%1$s ‹ %2$s — WordPress'), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(siteTitle));
      document.title = formattedTitle; // Announce title on route change for screen readers.

      (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)(
      /* translators: The page title that is currently displaying. */
      (0,external_wp_i18n_namespaceObject.__)('Now displaying: %s'), document.title), 'assertive');
    }
  }, [title, siteTitle, location]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/editor/index.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */















const interfaceLabels = {
  /* translators: accessibility text for the editor content landmark region. */
  body: (0,external_wp_i18n_namespaceObject.__)('Editor content'),

  /* translators: accessibility text for the editor settings landmark region. */
  sidebar: (0,external_wp_i18n_namespaceObject.__)('Editor settings'),

  /* translators: accessibility text for the editor publish landmark region. */
  actions: (0,external_wp_i18n_namespaceObject.__)('Editor publish'),

  /* translators: accessibility text for the editor footer landmark region. */
  footer: (0,external_wp_i18n_namespaceObject.__)('Editor footer')
};
function Editor() {
  const {
    record: editedPost,
    getTitle,
    isLoaded: hasLoadedPost
  } = useEditedEntityRecord();
  const {
    id: editedPostId,
    type: editedPostType
  } = editedPost;
  const {
    context,
    editorMode,
    canvasMode,
    blockEditorMode,
    isRightSidebarOpen,
    isInserterOpen,
    isListViewOpen,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEditedPostContext,
      getEditorMode,
      getCanvasMode,
      isInserterOpened,
      isListViewOpened
    } = unlock(select(store_store));
    const {
      __unstableGetEditorMode
    } = select(external_wp_blockEditor_namespaceObject.store);
    const {
      getActiveComplementaryArea
    } = select(store); // The currently selected entity to display.
    // Typically template or template part in the site editor.

    return {
      context: getEditedPostContext(),
      editorMode: getEditorMode(),
      canvasMode: getCanvasMode(),
      blockEditorMode: __unstableGetEditorMode(),
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened(),
      isRightSidebarOpen: getActiveComplementaryArea(store_store.name),
      showIconLabels: select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'showIconLabels')
    };
  }, []);
  const {
    setEditedPostContext
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const isViewMode = canvasMode === 'view';
  const isEditMode = canvasMode === 'edit';
  const showVisualEditor = isViewMode || editorMode === 'visual';
  const showBlockBreakcrumb = isEditMode && showVisualEditor && blockEditorMode !== 'zoom-out';
  const shouldShowInserter = isEditMode && showVisualEditor && isInserterOpen;
  const shouldShowListView = isEditMode && showVisualEditor && isListViewOpen;
  const secondarySidebarLabel = isListViewOpen ? (0,external_wp_i18n_namespaceObject.__)('List View') : (0,external_wp_i18n_namespaceObject.__)('Block Library');
  const blockContext = (0,external_wp_element_namespaceObject.useMemo)(() => ({ ...context,
    queryContext: [(context === null || context === void 0 ? void 0 : context.queryContext) || {
      page: 1
    }, newQueryContext => setEditedPostContext({ ...context,
      queryContext: { ...(context === null || context === void 0 ? void 0 : context.queryContext),
        ...newQueryContext
      }
    })]
  }), [context, setEditedPostContext]);
  let title;

  if (hasLoadedPost) {
    const type = editedPostType === 'wp_template' ? (0,external_wp_i18n_namespaceObject.__)('Template') : (0,external_wp_i18n_namespaceObject.__)('Template Part');
    title = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: A breadcrumb trail in browser tab. %1$s: title of template being edited, %2$s: type of template (Template or Template Part).
    (0,external_wp_i18n_namespaceObject.__)('%1$s ‹ %2$s ‹ Editor'), getTitle(), type);
  } // Only announce the title once the editor is ready to prevent "Replace"
  // action in <URlQueryController> from double-announcing.


  useTitle(hasLoadedPost && title);

  if (!hasLoadedPost) {
    return (0,external_wp_element_namespaceObject.createElement)(CanvasSpinner, null);
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, isEditMode && (0,external_wp_element_namespaceObject.createElement)(WelcomeGuide, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_coreData_namespaceObject.EntityProvider, {
    kind: "root",
    type: "site"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_coreData_namespaceObject.EntityProvider, {
    kind: "postType",
    type: editedPostType,
    id: editedPostId
  }, (0,external_wp_element_namespaceObject.createElement)(GlobalStylesProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockContextProvider, {
    value: blockContext
  }, (0,external_wp_element_namespaceObject.createElement)(SidebarComplementaryAreaFills, null), (0,external_wp_element_namespaceObject.createElement)(interface_skeleton, {
    enableRegionNavigation: false,
    className: showIconLabels && 'show-icon-labels',
    notices: isEditMode && (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorSnackbars, null),
    content: (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(GlobalStylesRenderer, null), isEditMode && (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorNotices, null), showVisualEditor && editedPost && (0,external_wp_element_namespaceObject.createElement)(BlockEditor, null), editorMode === 'text' && editedPost && isEditMode && (0,external_wp_element_namespaceObject.createElement)(CodeEditor, null), hasLoadedPost && !editedPost && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Notice, {
      status: "warning",
      isDismissible: false
    }, (0,external_wp_i18n_namespaceObject.__)("You attempted to edit an item that doesn't exist. Perhaps it was deleted?")), isEditMode && (0,external_wp_element_namespaceObject.createElement)(edit_mode, null)),
    secondarySidebar: isEditMode && (shouldShowInserter && (0,external_wp_element_namespaceObject.createElement)(InserterSidebar, null) || shouldShowListView && (0,external_wp_element_namespaceObject.createElement)(ListViewSidebar, null)),
    sidebar: isEditMode && isRightSidebarOpen && (0,external_wp_element_namespaceObject.createElement)(complementary_area.Slot, {
      scope: "core/edit-site"
    }),
    footer: showBlockBreakcrumb && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockBreadcrumb, {
      rootLabelText: (0,external_wp_i18n_namespaceObject.__)('Template')
    }),
    labels: { ...interfaceLabels,
      secondarySidebar: secondarySidebarLabel
    }
  }))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/use-register-shortcuts.js
/**
 * WordPress dependencies
 */




function useRegisterShortcuts() {
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-site/next-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the next part of the editor.'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-site/previous-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }, {
        modifier: 'ctrlShift',
        character: '~'
      }]
    });
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/header.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function Header(_ref) {
  var _postType$labels;

  let {
    templateType
  } = _ref;
  const {
    canCreate,
    postType
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      supportsTemplatePartsMode
    } = select(store_store).getSettings();
    return {
      postType: select(external_wp_coreData_namespaceObject.store).getPostType(templateType),
      canCreate: !supportsTemplatePartsMode
    };
  }, [templateType]);

  if (!postType) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)("header", {
    className: "edit-site-list-header"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
    level: 1,
    className: "edit-site-list-header__title"
  }, (_postType$labels = postType.labels) === null || _postType$labels === void 0 ? void 0 : _postType$labels.name), canCreate && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-list-header__right"
  }, (0,external_wp_element_namespaceObject.createElement)(AddNewTemplate, {
    templateType: templateType,
    showIcon: false,
    toggleProps: {
      variant: 'primary'
    }
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/utils/is-template-removable.js
/**
 * Check if a template is removable.
 *
 * @param {Object} template The template entity to check.
 * @return {boolean} Whether the template is revertable.
 */
function isTemplateRemovable(template) {
  if (!template) {
    return false;
  }

  return template.source === 'custom' && !template.has_theme_file;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/actions/rename-menu-item.js


/**
 * WordPress dependencies
 */






function RenameMenuItem(_ref) {
  let {
    template,
    onClose
  } = _ref;
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)(() => template.title.rendered);
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    editEntityRecord,
    saveEditedEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  if (template.type === 'wp_template' && !template.is_custom) {
    return null;
  }

  async function onTemplateRename(event) {
    event.preventDefault();

    try {
      await editEntityRecord('postType', template.type, template.id, {
        title
      }); // Update state before saving rerenders the list.

      setTitle('');
      setIsModalOpen(false);
      onClose(); // Persist edited entity.

      await saveEditedEntityRecord('postType', template.type, template.id, {
        throwOnError: true
      });
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Entity renamed.'), {
        type: 'snackbar'
      });
    } catch (error) {
      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while renaming the entity.');
      createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      setIsModalOpen(true);
      setTitle(template.title.rendered);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Rename')), isModalOpen && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    title: (0,external_wp_i18n_namespaceObject.__)('Rename'),
    onRequestClose: () => {
      setIsModalOpen(false);
    },
    overlayClassName: "edit-site-list__rename-modal"
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    onSubmit: onTemplateRename
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    spacing: "5"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Name'),
    value: title,
    onChange: setTitle,
    required: true
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "right"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "tertiary",
    onClick: () => {
      setIsModalOpen(false);
    }
  }, (0,external_wp_i18n_namespaceObject.__)('Cancel')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit"
  }, (0,external_wp_i18n_namespaceObject.__)('Save')))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/actions/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





function actions_Actions(_ref) {
  let {
    template
  } = _ref;
  const {
    removeTemplate,
    revertTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    saveEditedEntityRecord
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const {
    createSuccessNotice,
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const isRemovable = isTemplateRemovable(template);
  const isRevertable = isTemplateRevertable(template);

  if (!isRemovable && !isRevertable) {
    return null;
  }

  async function revertAndSaveTemplate() {
    try {
      await revertTemplate(template, {
        allowUndo: false
      });
      await saveEditedEntityRecord('postType', template.type, template.id);
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)('Entity reverted.'), {
        type: 'snackbar'
      });
    } catch (error) {
      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while reverting the entity.');
      createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.DropdownMenu, {
    icon: more_vertical,
    label: (0,external_wp_i18n_namespaceObject.__)('Actions'),
    className: "edit-site-list-table__actions"
  }, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, isRemovable && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(RenameMenuItem, {
      template: template,
      onClose: onClose
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      isDestructive: true,
      isTertiary: true,
      onClick: () => {
        removeTemplate(template);
        onClose();
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Delete'))), isRevertable && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      info: (0,external_wp_i18n_namespaceObject.__)('Use the template as supplied by the theme.'),
      onClick: () => {
        revertAndSaveTemplate();
        onClose();
      }
    }, (0,external_wp_i18n_namespaceObject.__)('Clear customizations')));
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/plugins.js


/**
 * WordPress dependencies
 */

const plugins = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10.5 4v4h3V4H15v4h1.5a1 1 0 011 1v4l-3 4v2a1 1 0 01-1 1h-3a1 1 0 01-1-1v-2l-3-4V9a1 1 0 011-1H9V4h1.5zm.5 12.5v2h2v-2l3-4v-3H8v3l3 4z"
}));
/* harmony default export */ var library_plugins = (plugins);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/comment-author-avatar.js


/**
 * WordPress dependencies
 */

const commentAuthorAvatar = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  fillRule: "evenodd",
  d: "M7.25 16.437a6.5 6.5 0 1 1 9.5 0V16A2.75 2.75 0 0 0 14 13.25h-4A2.75 2.75 0 0 0 7.25 16v.437Zm1.5 1.193a6.47 6.47 0 0 0 3.25.87 6.47 6.47 0 0 0 3.25-.87V16c0-.69-.56-1.25-1.25-1.25h-4c-.69 0-1.25.56-1.25 1.25v1.63ZM4 12a8 8 0 1 1 16 0 8 8 0 0 1-16 0Zm10-2a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z",
  clipRule: "evenodd"
}));
/* harmony default export */ var comment_author_avatar = (commentAuthorAvatar);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/globe.js


/**
 * WordPress dependencies
 */

const globe = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 3.3c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8s-4-8.8-8.8-8.8zm6.5 5.5h-2.6C15.4 7.3 14.8 6 14 5c2 .6 3.6 2 4.5 3.8zm.7 3.2c0 .6-.1 1.2-.2 1.8h-2.9c.1-.6.1-1.2.1-1.8s-.1-1.2-.1-1.8H19c.2.6.2 1.2.2 1.8zM12 18.7c-1-.7-1.8-1.9-2.3-3.5h4.6c-.5 1.6-1.3 2.9-2.3 3.5zm-2.6-4.9c-.1-.6-.1-1.1-.1-1.8 0-.6.1-1.2.1-1.8h5.2c.1.6.1 1.1.1 1.8s-.1 1.2-.1 1.8H9.4zM4.8 12c0-.6.1-1.2.2-1.8h2.9c-.1.6-.1 1.2-.1 1.8 0 .6.1 1.2.1 1.8H5c-.2-.6-.2-1.2-.2-1.8zM12 5.3c1 .7 1.8 1.9 2.3 3.5H9.7c.5-1.6 1.3-2.9 2.3-3.5zM10 5c-.8 1-1.4 2.3-1.8 3.8H5.5C6.4 7 8 5.6 10 5zM5.5 15.3h2.6c.4 1.5 1 2.8 1.8 3.7-1.8-.6-3.5-2-4.4-3.7zM14 19c.8-1 1.4-2.2 1.8-3.7h2.6C17.6 17 16 18.4 14 19z"
}));
/* harmony default export */ var library_globe = (globe);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/added-by.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







const TEMPLATE_POST_TYPE_NAMES = ['wp_template', 'wp_template_part'];

function BaseAddedBy(_ref) {
  let {
    text,
    icon,
    imageUrl,
    isCustomized,
    templateType
  } = _ref;
  const [isImageLoaded, setIsImageLoaded] = (0,external_wp_element_namespaceObject.useState)(false);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "left"
  }, imageUrl ? (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-list-added-by__avatar', {
      'is-loaded': isImageLoaded
    })
  }, (0,external_wp_element_namespaceObject.createElement)("img", {
    onLoad: () => setIsImageLoaded(true),
    alt: "",
    src: imageUrl
  })) : (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-list-added-by__icon"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
    icon: icon
  })), (0,external_wp_element_namespaceObject.createElement)("span", null, text, isCustomized && (0,external_wp_element_namespaceObject.createElement)("span", {
    className: "edit-site-list-added-by__customized-info"
  }, templateType === 'wp_template' ? (0,external_wp_i18n_namespaceObject._x)('Customized', 'template') : (0,external_wp_i18n_namespaceObject._x)('Customized', 'template part'))));
}

function AddedByTheme(_ref2) {
  var _theme$name;

  let {
    slug,
    isCustomized,
    templateType
  } = _ref2;
  const theme = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getTheme(slug), [slug]);
  return (0,external_wp_element_namespaceObject.createElement)(BaseAddedBy, {
    icon: library_layout,
    text: (theme === null || theme === void 0 ? void 0 : (_theme$name = theme.name) === null || _theme$name === void 0 ? void 0 : _theme$name.rendered) || slug,
    isCustomized: isCustomized,
    templateType: templateType
  });
}

function AddedByPlugin(_ref3) {
  let {
    slug,
    isCustomized,
    templateType
  } = _ref3;
  const plugin = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getPlugin(slug), [slug]);
  return (0,external_wp_element_namespaceObject.createElement)(BaseAddedBy, {
    icon: library_plugins,
    text: (plugin === null || plugin === void 0 ? void 0 : plugin.name) || slug,
    isCustomized: isCustomized,
    templateType: templateType
  });
}

function AddedByAuthor(_ref4) {
  var _user$avatar_urls;

  let {
    id,
    templateType
  } = _ref4;
  const user = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getUser(id), [id]);
  return (0,external_wp_element_namespaceObject.createElement)(BaseAddedBy, {
    icon: comment_author_avatar,
    imageUrl: user === null || user === void 0 ? void 0 : (_user$avatar_urls = user.avatar_urls) === null || _user$avatar_urls === void 0 ? void 0 : _user$avatar_urls[48],
    text: user === null || user === void 0 ? void 0 : user.nickname,
    templateType: templateType
  });
}

function AddedBySite(_ref5) {
  let {
    templateType
  } = _ref5;
  const {
    name,
    logoURL
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getMedia;

    const {
      getEntityRecord,
      getMedia
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase');
    return {
      name: siteData === null || siteData === void 0 ? void 0 : siteData.name,
      logoURL: siteData !== null && siteData !== void 0 && siteData.site_logo ? (_getMedia = getMedia(siteData.site_logo)) === null || _getMedia === void 0 ? void 0 : _getMedia.source_url : undefined
    };
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(BaseAddedBy, {
    icon: library_globe,
    imageUrl: logoURL,
    text: name,
    templateType: templateType
  });
}

function AddedBy(_ref6) {
  let {
    templateType,
    template
  } = _ref6;

  if (!template) {
    return;
  }

  if (TEMPLATE_POST_TYPE_NAMES.includes(templateType)) {
    // Template originally provided by a theme, but customized by a user.
    // Templates originally didn't have the 'origin' field so identify
    // older customized templates by checking for no origin and a 'theme'
    // or 'custom' source.
    if (template.has_theme_file && (template.origin === 'theme' || !template.origin && ['theme', 'custom'].includes(template.source))) {
      return (0,external_wp_element_namespaceObject.createElement)(AddedByTheme, {
        slug: template.theme,
        isCustomized: template.source === 'custom',
        templateType: templateType
      });
    } // Template originally provided by a plugin, but customized by a user.


    if (template.has_theme_file && template.origin === 'plugin') {
      return (0,external_wp_element_namespaceObject.createElement)(AddedByPlugin, {
        slug: template.theme,
        isCustomized: template.source === 'custom',
        templateType: templateType
      });
    } // Template was created from scratch, but has no author. Author support
    // was only added to templates in WordPress 5.9. Fallback to showing the
    // site logo and title.


    if (!template.has_theme_file && template.source === 'custom' && !template.author) {
      return (0,external_wp_element_namespaceObject.createElement)(AddedBySite, {
        templateType: templateType
      });
    }
  } // Simply show the author for templates created from scratch that have an
  // author or for any other post type.


  return (0,external_wp_element_namespaceObject.createElement)(AddedByAuthor, {
    id: template.author,
    templateType: templateType
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/table.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




function Table(_ref) {
  let {
    templateType
  } = _ref;
  const {
    records: templates,
    isResolving: isLoading
  } = (0,external_wp_coreData_namespaceObject.useEntityRecords)('postType', templateType, {
    per_page: -1
  });
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getPostType(templateType), [templateType]);

  if (!templates || isLoading) {
    return null;
  }

  if (!templates.length) {
    var _postType$labels, _postType$labels$name;

    return (0,external_wp_element_namespaceObject.createElement)("div", null, (0,external_wp_i18n_namespaceObject.sprintf)( // translators: The template type name, should be either "templates" or "template parts".
    (0,external_wp_i18n_namespaceObject.__)('No %s found.'), postType === null || postType === void 0 ? void 0 : (_postType$labels = postType.labels) === null || _postType$labels === void 0 ? void 0 : (_postType$labels$name = _postType$labels.name) === null || _postType$labels$name === void 0 ? void 0 : _postType$labels$name.toLowerCase()));
  }

  const sortedTemplates = [...templates];
  sortedTemplates.sort((a, b) => a.slug.localeCompare(b.slug));
  return (// These explicit aria roles are needed for Safari.
    // See https://developer.mozilla.org/en-US/docs/Web/CSS/display#tables
    (0,external_wp_element_namespaceObject.createElement)("table", {
      className: "edit-site-list-table",
      role: "table"
    }, (0,external_wp_element_namespaceObject.createElement)("thead", null, (0,external_wp_element_namespaceObject.createElement)("tr", {
      className: "edit-site-list-table-head",
      role: "row"
    }, (0,external_wp_element_namespaceObject.createElement)("th", {
      className: "edit-site-list-table-column",
      role: "columnheader"
    }, (0,external_wp_i18n_namespaceObject.__)('Template')), (0,external_wp_element_namespaceObject.createElement)("th", {
      className: "edit-site-list-table-column",
      role: "columnheader"
    }, (0,external_wp_i18n_namespaceObject.__)('Added by')), (0,external_wp_element_namespaceObject.createElement)("th", {
      className: "edit-site-list-table-column",
      role: "columnheader"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, null, (0,external_wp_i18n_namespaceObject.__)('Actions'))))), (0,external_wp_element_namespaceObject.createElement)("tbody", null, sortedTemplates.map(template => {
      var _template$title;

      return (0,external_wp_element_namespaceObject.createElement)("tr", {
        key: template.id,
        className: "edit-site-list-table-row",
        role: "row"
      }, (0,external_wp_element_namespaceObject.createElement)("td", {
        className: "edit-site-list-table-column",
        role: "cell"
      }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHeading, {
        level: 4
      }, (0,external_wp_element_namespaceObject.createElement)(Link, {
        params: {
          postId: template.id,
          postType: template.type
        }
      }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(((_template$title = template.title) === null || _template$title === void 0 ? void 0 : _template$title.rendered) || template.slug))), (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(template.description)), (0,external_wp_element_namespaceObject.createElement)("td", {
        className: "edit-site-list-table-column",
        role: "cell"
      }, (0,external_wp_element_namespaceObject.createElement)(AddedBy, {
        templateType: templateType,
        template: template
      })), (0,external_wp_element_namespaceObject.createElement)("td", {
        className: "edit-site-list-table-column",
        role: "cell"
      }, (0,external_wp_element_namespaceObject.createElement)(actions_Actions, {
        template: template
      })));
    })))
  );
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/list/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */






function List() {
  var _postType$labels, _postType$labels2;

  const {
    params: {
      path
    }
  } = useLocation();
  const templateType = path === '/wp_template/all' ? 'wp_template' : 'wp_template_part';
  useRegisterShortcuts();
  const {
    previousShortcut,
    nextShortcut
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return {
      previousShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-site/previous-region'),
      nextShortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getAllShortcutKeyCombinations('core/edit-site/next-region')
    };
  }, []);
  const postType = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).getPostType(templateType), [templateType]);
  useTitle(postType === null || postType === void 0 ? void 0 : (_postType$labels = postType.labels) === null || _postType$labels === void 0 ? void 0 : _postType$labels.name); // `postType` could load in asynchronously. Only provide the detailed region labels if
  // the postType has loaded, otherwise `InterfaceSkeleton` will fallback to the defaults.

  const itemsListLabel = postType === null || postType === void 0 ? void 0 : (_postType$labels2 = postType.labels) === null || _postType$labels2 === void 0 ? void 0 : _postType$labels2.items_list;
  const detailedRegionLabels = postType ? {
    header: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s - the name of the page, 'Header' as in the header area of that page.
    (0,external_wp_i18n_namespaceObject.__)('%s - Header'), itemsListLabel),
    body: (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s - the name of the page, 'Content' as in the content area of that page.
    (0,external_wp_i18n_namespaceObject.__)('%s - Content'), itemsListLabel)
  } : undefined;
  return (0,external_wp_element_namespaceObject.createElement)(interface_skeleton, {
    className: "edit-site-list",
    labels: detailedRegionLabels,
    header: (0,external_wp_element_namespaceObject.createElement)(Header, {
      templateType: templateType
    }),
    notices: (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EditorSnackbars, null),
    content: (0,external_wp_element_namespaceObject.createElement)(Table, {
      templateType: templateType
    }),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/error-boundary/warning.js


/**
 * WordPress dependencies
 */





function CopyButton(_ref) {
  let {
    text,
    children
  } = _ref;
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    ref: ref
  }, children);
}

function ErrorBoundaryWarning(_ref2) {
  let {
    message,
    error
  } = _ref2;
  const actions = [(0,external_wp_element_namespaceObject.createElement)(CopyButton, {
    key: "copy-error",
    text: error.stack
  }, (0,external_wp_i18n_namespaceObject.__)('Copy Error'))];
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.Warning, {
    className: "editor-error-boundary",
    actions: actions
  }, message);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/error-boundary/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


class ErrorBoundary extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      error: null
    };
  }

  componentDidCatch(error) {
    (0,external_wp_hooks_namespaceObject.doAction)('editor.ErrorBoundary.errorLogged', error);
  }

  static getDerivedStateFromError(error) {
    return {
      error
    };
  }

  render() {
    if (!this.state.error) {
      return this.props.children;
    }

    return (0,external_wp_element_namespaceObject.createElement)(ErrorBoundaryWarning, {
      message: (0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error.'),
      error: this.state.error
    });
  }

}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/utils/get-is-list-page.js
/**
 * Returns if the params match the list page route.
 *
 * @param {Object} params      The url params.
 * @param {string} params.path The current path.
 *
 * @return {boolean} Is list page or not.
 */
function getIsListPage(_ref) {
  let {
    path
  } = _ref;
  return path === '/wp_template/all' || path === '/wp_template_part/all';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js


/**
 * WordPress dependencies
 */

const listView = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ var list_view = (listView);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-up-down.js


/**
 * WordPress dependencies
 */

const chevronUpDown = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "m12 20-4.5-3.6-.9 1.2L12 22l5.5-4.4-.9-1.2L12 20zm0-16 4.5 3.6.9-1.2L12 2 6.5 6.4l.9 1.2L12 4z"
}));
/* harmony default export */ var chevron_up_down = (chevronUpDown);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js


/**
 * WordPress dependencies
 */

const external = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ var library_external = (external);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Remove a link.')
}, {
  keyCombination: {
    character: '[['
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Insert a link to a post or page.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Underline the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'd'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Strikethrough the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'x'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text inline code.')
}];

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * WordPress dependencies
 */



function KeyCombination(_ref) {
  let {
    keyCombination,
    forceAriaLabel
  } = _ref;
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return (0,external_wp_element_namespaceObject.createElement)("kbd", {
    className: "edit-site-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, (Array.isArray(shortcut) ? shortcut : [shortcut]).map((character, index) => {
    if (character === '+') {
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, {
        key: index
      }, character);
    }

    return (0,external_wp_element_namespaceObject.createElement)("kbd", {
      key: index,
      className: "edit-site-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut(_ref2) {
  let {
    description,
    keyCombination,
    aliases = [],
    ariaLabel
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-keyboard-shortcut-help-modal__shortcut-description"
  }, description), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-keyboard-shortcut-help-modal__shortcut-term"
  }, (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => (0,external_wp_element_namespaceObject.createElement)(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function DynamicShortcut(_ref) {
  let {
    name
  } = _ref;
  const {
    keyCombination,
    description,
    aliases
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(Shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





const ShortcutList = _ref => {
  let {
    shortcuts
  } = _ref;
  return (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */

    /* eslint-disable jsx-a11y/no-redundant-roles */
    (0,external_wp_element_namespaceObject.createElement)("ul", {
      className: "edit-site-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map((shortcut, index) => (0,external_wp_element_namespaceObject.createElement)("li", {
      className: "edit-site-keyboard-shortcut-help-modal__shortcut",
      key: index
    }, typeof shortcut === 'string' ? (0,external_wp_element_namespaceObject.createElement)(DynamicShortcut, {
      name: shortcut
    }) : (0,external_wp_element_namespaceObject.createElement)(Shortcut, shortcut))))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

const ShortcutSection = _ref2 => {
  let {
    title,
    shortcuts,
    className
  } = _ref2;
  return (0,external_wp_element_namespaceObject.createElement)("section", {
    className: classnames_default()('edit-site-keyboard-shortcut-help-modal__section', className)
  }, !!title && (0,external_wp_element_namespaceObject.createElement)("h2", {
    className: "edit-site-keyboard-shortcut-help-modal__section-title"
  }, title), (0,external_wp_element_namespaceObject.createElement)(ShortcutList, {
    shortcuts: shortcuts
  }));
};

const ShortcutCategorySection = _ref3 => {
  let {
    title,
    categoryName,
    additionalShortcuts = []
  } = _ref3;
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  let {
    isModalActive,
    toggleModal
  } = _ref4;

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
    className: "edit-site-keyboard-shortcut-help-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'),
    onRequestClose: toggleModal
  }, (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    className: "edit-site-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-site/keyboard-shortcuts']
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Global shortcuts'),
    categoryName: "global"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Selection shortcuts'),
    categoryName: "selection"
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutCategorySection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: (0,external_wp_i18n_namespaceObject.__)('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Forward-slash')
    }]
  }), (0,external_wp_element_namespaceObject.createElement)(ShortcutSection, {
    title: (0,external_wp_i18n_namespaceObject.__)('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/preferences-modal/enable-feature.js



/**
 * WordPress dependencies
 */



function EnableFeature(props) {
  const {
    featureName,
    ...remainingProps
  } = props;
  const isChecked = (0,external_wp_data_namespaceObject.useSelect)(select => !!select(external_wp_preferences_namespaceObject.store).get('core/edit-site', featureName), [featureName]);
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);

  const onChange = () => toggle('core/edit-site', featureName);

  return (0,external_wp_element_namespaceObject.createElement)(preferences_modal_base_option, extends_extends({
    onChange: onChange,
    isChecked: isChecked
  }, remainingProps));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/preferences-modal/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function EditSitePreferencesModal(_ref) {
  let {
    isModalActive,
    toggleModal
  } = _ref;
  const sections = (0,external_wp_element_namespaceObject.useMemo)(() => [{
    name: 'general',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('General'),
    content: (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Appearance'),
      description: (0,external_wp_i18n_namespaceObject.__)('Customize options related to the block editor interface and editing flow.')
    }, (0,external_wp_element_namespaceObject.createElement)(EnableFeature, {
      featureName: "focusMode",
      help: (0,external_wp_i18n_namespaceObject.__)('Highlights the current block and fades other content.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode')
    }), (0,external_wp_element_namespaceObject.createElement)(EnableFeature, {
      featureName: "showIconLabels",
      label: (0,external_wp_i18n_namespaceObject.__)('Show button text labels'),
      help: (0,external_wp_i18n_namespaceObject.__)('Show text instead of icons on buttons.')
    }), (0,external_wp_element_namespaceObject.createElement)(EnableFeature, {
      featureName: "showListViewByDefault",
      help: (0,external_wp_i18n_namespaceObject.__)('Opens the block list view sidebar by default.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Always open list view')
    }))
  }, {
    name: 'blocks',
    tabLabel: (0,external_wp_i18n_namespaceObject.__)('Blocks'),
    content: (0,external_wp_element_namespaceObject.createElement)(preferences_modal_section, {
      title: (0,external_wp_i18n_namespaceObject.__)('Block interactions'),
      description: (0,external_wp_i18n_namespaceObject.__)('Customize how you interact with blocks in the block library and editing canvas.')
    }, (0,external_wp_element_namespaceObject.createElement)(EnableFeature, {
      featureName: "keepCaretInsideBlock",
      help: (0,external_wp_i18n_namespaceObject.__)('Aids screen readers by stopping text caret from leaving blocks.'),
      label: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block')
    }))
  }]);

  if (!isModalActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(PreferencesModal, {
    closeModal: toggleModal
  }, (0,external_wp_element_namespaceObject.createElement)(PreferencesModalTabs, {
    sections: sections
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/tools-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = (0,external_wp_components_namespaceObject.createSlotFill)('EditSiteToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = _ref => {
  let {
    fillProps
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(Slot, {
    fillProps: fillProps
  }, fills => !(0,external_lodash_namespaceObject.isEmpty)(fills) && fills);
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

// EXTERNAL MODULE: ./node_modules/downloadjs/download.js
var download = __webpack_require__(8981);
var download_default = /*#__PURE__*/__webpack_require__.n(download);
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/download.js


/**
 * WordPress dependencies
 */

const download_download = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18 11.3l-1-1.1-4 4V3h-1.5v11.3L7 10.2l-1 1.1 6.2 5.8 5.8-5.8zm.5 3.7v3.5h-13V15H4v5h16v-5h-1.5z"
}));
/* harmony default export */ var library_download = (download_download);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/more-menu/site-export.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







function SiteExport() {
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  async function handleExport() {
    try {
      const response = await external_wp_apiFetch_default()({
        path: '/wp-block-editor/v1/export',
        parse: false,
        headers: {
          Accept: 'application/zip'
        }
      });
      const blob = await response.blob();
      const contentDisposition = response.headers.get('content-disposition');
      const contentDispositionMatches = contentDisposition.match(/=(.+)\.zip/);
      const fileName = contentDispositionMatches[1] ? contentDispositionMatches[1] : 'edit-site-export';
      download_default()(blob, fileName + '.zip', 'application/zip');
    } catch (errorResponse) {
      let error = {};

      try {
        error = await errorResponse.json();
      } catch (e) {}

      const errorMessage = error.message && error.code !== 'unknown_error' ? error.message : (0,external_wp_i18n_namespaceObject.__)('An error occurred while creating the site export.');
      createErrorNotice(errorMessage, {
        type: 'snackbar'
      });
    }
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    role: "menuitem",
    icon: library_download,
    onClick: handleExport,
    info: (0,external_wp_i18n_namespaceObject.__)('Download your theme with updated templates and styles.')
  }, (0,external_wp_i18n_namespaceObject._x)('Export', 'site exporter menu item'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/more-menu/welcome-guide-menu-item.js


/**
 * WordPress dependencies
 */




function WelcomeGuideMenuItem() {
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => toggle('core/edit-site', 'welcomeGuide')
  }, (0,external_wp_i18n_namespaceObject.__)('Welcome Guide'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/more-menu/copy-content-menu-item.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function CopyContentMenuItem() {
  const {
    createNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const getText = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return () => {
      const {
        getEditedPostId,
        getEditedPostType
      } = select(store_store);
      const {
        getEditedEntityRecord
      } = select(external_wp_coreData_namespaceObject.store);
      const record = getEditedEntityRecord('postType', getEditedPostType(), getEditedPostId());

      if (record) {
        if (typeof record.content === 'function') {
          return record.content(record);
        } else if (record.blocks) {
          return (0,external_wp_blocks_namespaceObject.__unstableSerializeAndClean)(record.blocks);
        } else if (record.content) {
          return record.content;
        }
      }

      return '';
    };
  }, []);

  function onSuccess() {
    createNotice('info', (0,external_wp_i18n_namespaceObject.__)('All content copied.'), {
      isDismissible: true,
      type: 'snackbar'
    });
  }

  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(getText, onSuccess);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    ref: ref
  }, (0,external_wp_i18n_namespaceObject.__)('Copy all blocks'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/mode-switcher/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * Set of available mode options.
 *
 * @type {Array}
 */

const MODES = [{
  value: 'visual',
  label: (0,external_wp_i18n_namespaceObject.__)('Visual editor')
}, {
  value: 'text',
  label: (0,external_wp_i18n_namespaceObject.__)('Code editor')
}];

function ModeSwitcher() {
  const {
    shortcut,
    mode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => ({
    shortcut: select(external_wp_keyboardShortcuts_namespaceObject.store).getShortcutRepresentation('core/edit-site/toggle-mode'),
    isRichEditingEnabled: select(store_store).getSettings().richEditingEnabled,
    isCodeEditingEnabled: select(store_store).getSettings().codeEditingEnabled,
    mode: select(store_store).getEditorMode()
  }), []);
  const {
    switchEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const choices = MODES.map(choice => {
    if (choice.value !== mode) {
      return { ...choice,
        shortcut
      };
    }

    return choice;
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    label: (0,external_wp_i18n_namespaceObject.__)('Editor')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItemsChoice, {
    choices: choices,
    value: mode,
    onSelect: switchEditorMode
  }));
}

/* harmony default export */ var mode_switcher = (ModeSwitcher);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/more-menu/index.js


/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */








function MoreMenu(_ref) {
  let {
    showIconLabels
  } = _ref;
  const [isModalActive, toggleModal] = (0,external_wp_element_namespaceObject.useReducer)(isActive => !isActive, false);
  const [isPreferencesModalActive, togglePreferencesModal] = (0,external_wp_element_namespaceObject.useReducer)(isActive => !isActive, false);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/keyboard-shortcuts', toggleModal);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(MoreMenuDropdown, {
    toggleProps: {
      showTooltip: !showIconLabels,
      ...(showIconLabels && {
        variant: 'tertiary'
      })
    }
  }, _ref2 => {
    let {
      onClose
    } = _ref2;
    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
      label: (0,external_wp_i18n_namespaceObject._x)('View', 'noun')
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
      scope: "core/edit-site",
      name: "fixedToolbar",
      label: (0,external_wp_i18n_namespaceObject.__)('Top toolbar'),
      info: (0,external_wp_i18n_namespaceObject.__)('Access all block and document tools in a single place'),
      messageActivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar activated'),
      messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar deactivated')
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
      scope: "core/edit-site",
      name: "focusMode",
      label: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode'),
      info: (0,external_wp_i18n_namespaceObject.__)('Focus on one block at a time'),
      messageActivated: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode activated'),
      messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Spotlight mode deactivated')
    }), (0,external_wp_element_namespaceObject.createElement)(mode_switcher, null), (0,external_wp_element_namespaceObject.createElement)(action_item.Slot, {
      name: "core/edit-site/plugin-more-menu",
      label: (0,external_wp_i18n_namespaceObject.__)('Plugins'),
      as: external_wp_components_namespaceObject.MenuGroup,
      fillProps: {
        onClick: onClose
      }
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
      label: (0,external_wp_i18n_namespaceObject.__)('Tools')
    }, (0,external_wp_element_namespaceObject.createElement)(SiteExport, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      onClick: toggleModal,
      shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access('h')
    }, (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts')), (0,external_wp_element_namespaceObject.createElement)(WelcomeGuideMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(CopyContentMenuItem, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      icon: library_external,
      role: "menuitem",
      href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/support/article/site-editor/'),
      target: "_blank",
      rel: "noopener noreferrer"
    }, (0,external_wp_i18n_namespaceObject.__)('Help'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
      as: "span"
    },
    /* translators: accessibility text */
    (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)'))), (0,external_wp_element_namespaceObject.createElement)(tools_more_menu_group.Slot, {
      fillProps: {
        onClose
      }
    })), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
      onClick: togglePreferencesModal
    }, (0,external_wp_i18n_namespaceObject.__)('Preferences'))));
  }), (0,external_wp_element_namespaceObject.createElement)(KeyboardShortcutHelpModal, {
    isModalActive: isModalActive,
    toggleModal: toggleModal
  }), (0,external_wp_element_namespaceObject.createElement)(EditSitePreferencesModal, {
    isModalActive: isPreferencesModalActive,
    toggleModal: togglePreferencesModal
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/save-button/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function save_button_SaveButton() {
  const {
    isDirty,
    isSaving,
    isSaveViewOpen
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      __experimentalGetDirtyEntityRecords,
      isSavingEntityRecord
    } = select(external_wp_coreData_namespaceObject.store);

    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();

    const {
      isSaveViewOpened
    } = select(store_store);
    return {
      isDirty: dirtyEntityRecords.length > 0,
      isSaving: dirtyEntityRecords.some(record => isSavingEntityRecord(record.kind, record.name, record.key)),
      isSaveViewOpen: isSaveViewOpened()
    };
  }, []);
  const {
    setIsSaveViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const disabled = !isDirty || isSaving;

  const label = (0,external_wp_i18n_namespaceObject.__)('Save');

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    className: "edit-site-save-button__button",
    "aria-disabled": disabled,
    "aria-expanded": isSaveViewOpen,
    isBusy: isSaving,
    onClick: disabled ? undefined : () => setIsSaveViewOpened(true),
    label: label
    /*
     * We want the tooltip to show the keyboard shortcut only when the
     * button does something, i.e. when it's not disabled.
     */
    ,
    shortcut: disabled ? undefined : external_wp_keycodes_namespaceObject.displayShortcut.primary('s')
    /*
     * Displaying the keyboard shortcut conditionally makes the tooltip
     * itself show conditionally. This would trigger a full-rerendering
     * of the button that we want to avoid. By setting `showTooltip`,
     & the tooltip is always rendered even when there's no keyboard shortcut.
     */
    ,
    showTooltip: true
  }, label);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js


/**
 * WordPress dependencies
 */

const undo = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ var library_undo = (undo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js


/**
 * WordPress dependencies
 */

const redo = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ var library_redo = (redo);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/undo-redo/undo.js



/**
 * WordPress dependencies
 */








function UndoButton(props, ref) {
  const hasUndo = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).hasUndo(), []);
  const {
    undo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, extends_extends({}, props, {
    ref: ref,
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_undo : library_redo,
    label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
    shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: hasUndo ? undo : undefined
  }));
}

/* harmony default export */ var undo_redo_undo = ((0,external_wp_element_namespaceObject.forwardRef)(UndoButton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/undo-redo/redo.js



/**
 * WordPress dependencies
 */








function RedoButton(props, ref) {
  const shortcut = (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? external_wp_keycodes_namespaceObject.displayShortcut.primaryShift('z') : external_wp_keycodes_namespaceObject.displayShortcut.primary('y');
  const hasRedo = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_coreData_namespaceObject.store).hasRedo(), []);
  const {
    redo
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, extends_extends({}, props, {
    ref: ref,
    icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_redo : library_undo,
    label: (0,external_wp_i18n_namespaceObject.__)('Redo'),
    shortcut: shortcut // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: hasRedo ? redo : undefined
  }));
}

/* harmony default export */ var undo_redo_redo = ((0,external_wp_element_namespaceObject.forwardRef)(RedoButton));

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-down.js


/**
 * WordPress dependencies
 */

const chevronDown = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"
}));
/* harmony default export */ var chevron_down = (chevronDown);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-details/edit-template-title.js


/**
 * WordPress dependencies
 */




function EditTemplateTitle(_ref) {
  let {
    template
  } = _ref;
  const [forceEmpty, setForceEmpty] = (0,external_wp_element_namespaceObject.useState)(false);
  const [title, setTitle] = (0,external_wp_coreData_namespaceObject.useEntityProp)('postType', template.type, 'title', template.id);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Title'),
    value: forceEmpty ? '' : title,
    help: template.type !== 'wp_template_part' ? (0,external_wp_i18n_namespaceObject.__)('Give the template a title that indicates its purpose, e.g. "Full Width".') : null,
    onChange: newTitle => {
      if (!newTitle && !forceEmpty) {
        setForceEmpty(true);
        return;
      }

      setForceEmpty(false);
      setTitle(newTitle);
    },
    onBlur: () => setForceEmpty(false)
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-details/template-part-area-selector.js


/**
 * WordPress dependencies
 */





function TemplatePartAreaSelector(_ref) {
  let {
    id
  } = _ref;
  const [area, setArea] = (0,external_wp_coreData_namespaceObject.useEntityProp)('postType', 'wp_template_part', 'area', id);
  const definedAreas = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).__experimentalGetDefaultTemplatePartAreas(), []);
  const areaOptions = definedAreas.map(_ref2 => {
    let {
      label,
      area: _area
    } = _ref2;
    return {
      label,
      value: _area
    };
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SelectControl, {
    __nextHasNoMarginBottom: true,
    label: (0,external_wp_i18n_namespaceObject.__)('Area'),
    labelPosition: "top",
    options: areaOptions,
    value: area,
    onChange: setArea
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/template-details/index.js



/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */






function TemplateDetails(_ref) {
  let {
    template,
    onClose
  } = _ref;
  const {
    title,
    description
  } = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_editor_namespaceObject.store).__experimentalGetTemplateInfo(template), []);
  const {
    revertTemplate
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store); // TODO: We should update this to filter by template part's areas as well.

  const browseAllLinkProps = useLink({
    path: '/' + template.type + '/all'
  });
  const isTemplatePart = template.type === 'wp_template_part'; // Only user-created and non-default templates can change the name.
  // But any user-created template part can be renamed.

  const canEditTitle = isTemplatePart ? !template.has_theme_file : template.is_custom && !template.has_theme_file;

  if (!template) {
    return null;
  }

  const revert = () => {
    revertTemplate(template);
    onClose();
  };

  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-details"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalVStack, {
    className: "edit-site-template-details__group",
    spacing: 3
  }, canEditTitle ? (0,external_wp_element_namespaceObject.createElement)(EditTemplateTitle, {
    template: template
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: 16,
    weight: 600,
    className: "edit-site-template-details__title",
    as: "p"
  }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(title)), description && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: "body",
    className: "edit-site-template-details__description",
    as: "p"
  }, (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(description))), isTemplatePart && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-template-details__group"
  }, (0,external_wp_element_namespaceObject.createElement)(TemplatePartAreaSelector, {
    id: template.id
  })), isTemplateRevertable(template) && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, {
    className: "edit-site-template-details__group edit-site-template-details__revert"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    className: "edit-site-template-details__revert-button",
    info: (0,external_wp_i18n_namespaceObject.__)('Use the template as supplied by the theme.'),
    onClick: revert
  }, (0,external_wp_i18n_namespaceObject.__)('Clear customizations'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, extends_extends({
    className: "edit-site-template-details__show-all-button"
  }, browseAllLinkProps), (template === null || template === void 0 ? void 0 : template.type) === 'wp_template' ? (0,external_wp_i18n_namespaceObject.__)('Manage all templates') : (0,external_wp_i18n_namespaceObject.__)('Manage all template parts')));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/document-actions/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




function getBlockDisplayText(block) {
  if (block) {
    const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(block.name);
    return blockType ? (0,external_wp_blocks_namespaceObject.__experimentalGetBlockLabel)(blockType, block.attributes) : null;
  }

  return null;
}

function useSecondaryText() {
  const {
    getBlock
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const activeEntityBlockId = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).__experimentalGetActiveBlockIdByBlockNames(['core/template-part']), []);
  const blockInformation = (0,external_wp_blockEditor_namespaceObject.useBlockDisplayInformation)(activeEntityBlockId);

  if (activeEntityBlockId) {
    return {
      label: getBlockDisplayText(getBlock(activeEntityBlockId)),
      isActive: true,
      icon: blockInformation === null || blockInformation === void 0 ? void 0 : blockInformation.icon
    };
  }

  return {};
}

function DocumentActions() {
  const showIconLabels = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'showIconLabels'), []);
  const {
    isLoaded,
    record,
    getTitle
  } = useEditedEntityRecord();
  const {
    label,
    icon
  } = useSecondaryText(); // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.

  const [popoverAnchor, setPopoverAnchor] = (0,external_wp_element_namespaceObject.useState)(null); // Memoize popoverProps to avoid returning a new object every time.

  const popoverProps = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    // Use the title wrapper as the popover anchor so that the dropdown is
    // centered over the whole title area rather than just one part of it.
    anchor: popoverAnchor,
    placement: 'bottom'
  }), [popoverAnchor]); // Return a simple loading indicator until we have information to show.

  if (!isLoaded) {
    return (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-site-document-actions"
    }, (0,external_wp_i18n_namespaceObject.__)('Loading…'));
  } // Return feedback that the template does not seem to exist.


  if (!record) {
    return (0,external_wp_element_namespaceObject.createElement)("div", {
      className: "edit-site-document-actions"
    }, (0,external_wp_i18n_namespaceObject.__)('Template not found'));
  }

  const entityLabel = record.type === 'wp_template_part' ? (0,external_wp_i18n_namespaceObject.__)('template part') : (0,external_wp_i18n_namespaceObject.__)('template');
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-document-actions', {
      'has-secondary-label': !!label
    })
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    ref: setPopoverAnchor,
    className: "edit-site-document-actions__title-wrapper"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: "body",
    className: "edit-site-document-actions__title",
    as: "h1"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  }, (0,external_wp_i18n_namespaceObject.sprintf)(
  /* translators: %s: the entity being edited, like "template"*/
  (0,external_wp_i18n_namespaceObject.__)('Editing %s: '), entityLabel)), getTitle()), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-document-actions__secondary-item"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.BlockIcon, {
    icon: icon,
    showColors: true
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalText, {
    size: "body"
  }, label !== null && label !== void 0 ? label : '')), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Dropdown, {
    popoverProps: popoverProps,
    renderToggle: _ref => {
      let {
        isOpen,
        onToggle
      } = _ref;
      return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
        className: "edit-site-document-actions__get-info",
        icon: chevron_down,
        "aria-expanded": isOpen,
        "aria-haspopup": "true",
        onClick: onToggle,
        variant: showIconLabels ? 'tertiary' : undefined,
        label: (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: the entity to see details about, like "template"*/
        (0,external_wp_i18n_namespaceObject.__)('Show %s details'), entityLabel)
      }, showIconLabels && (0,external_wp_i18n_namespaceObject.__)('Details'));
    },
    contentClassName: "edit-site-document-actions__info-dropdown",
    renderContent: _ref2 => {
      let {
        onClose
      } = _ref2;
      return (0,external_wp_element_namespaceObject.createElement)(TemplateDetails, {
        template: record,
        onClose: onClose
      });
    }
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */












/**
 * Internal dependencies
 */









const preventDefault = event => {
  event.preventDefault();
};

function HeaderEditMode() {
  var _window;

  const inserterButton = (0,external_wp_element_namespaceObject.useRef)();
  const {
    deviceType,
    templateType,
    isInserterOpen,
    isListViewOpen,
    listViewShortcut,
    isVisualMode,
    blockEditorMode,
    homeUrl,
    showIconLabels
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getUnstableBase;

    const {
      __experimentalGetPreviewDeviceType,
      getEditedPostType,
      isInserterOpened,
      isListViewOpened,
      getEditorMode
    } = select(store_store);
    const {
      getShortcutRepresentation
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    const {
      __unstableGetEditorMode
    } = select(external_wp_blockEditor_namespaceObject.store);
    const postType = getEditedPostType();
    const {
      getUnstableBase // Site index.

    } = select(external_wp_coreData_namespaceObject.store);
    return {
      deviceType: __experimentalGetPreviewDeviceType(),
      templateType: postType,
      isInserterOpen: isInserterOpened(),
      isListViewOpen: isListViewOpened(),
      listViewShortcut: getShortcutRepresentation('core/edit-site/toggle-list-view'),
      isVisualMode: getEditorMode() === 'visual',
      blockEditorMode: __unstableGetEditorMode(),
      homeUrl: (_getUnstableBase = getUnstableBase()) === null || _getUnstableBase === void 0 ? void 0 : _getUnstableBase.home,
      showIconLabels: select(external_wp_preferences_namespaceObject.store).get('core/edit-site', 'showIconLabels')
    };
  }, []);
  const {
    __experimentalSetPreviewDeviceType: setPreviewDeviceType,
    setIsInserterOpened,
    setIsListViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  const {
    __unstableSetEditorMode
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const isLargeViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium');
  const toggleInserter = (0,external_wp_element_namespaceObject.useCallback)(() => {
    if (isInserterOpen) {
      // Focusing the inserter button should close the inserter popover.
      // However, there are some cases it won't close when the focus is lost.
      // See https://github.com/WordPress/gutenberg/issues/43090 for more details.
      inserterButton.current.focus();
      setIsInserterOpened(false);
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpen, setIsInserterOpened]);
  const toggleListView = (0,external_wp_element_namespaceObject.useCallback)(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const hasStyleBook = useHasStyleBook();
  const isFocusMode = templateType === 'wp_template_part';
  /* translators: button label text should, if possible, be under 16 characters. */

  const longLabel = (0,external_wp_i18n_namespaceObject._x)('Toggle block inserter', 'Generic label for block inserter button');

  const shortLabel = !isInserterOpen ? (0,external_wp_i18n_namespaceObject.__)('Add') : (0,external_wp_i18n_namespaceObject.__)('Close');
  const isZoomedOutViewExperimentEnabled = ((_window = window) === null || _window === void 0 ? void 0 : _window.__experimentalEnableZoomedOutView) && isVisualMode;
  const isZoomedOutView = blockEditorMode === 'zoom-out';
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-header-edit-mode', {
      'show-icon-labels': showIconLabels
    })
  }, !hasStyleBook && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
    className: "edit-site-header-edit-mode__start",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Document tools')
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-header-edit-mode__toolbar"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    ref: inserterButton,
    as: external_wp_components_namespaceObject.Button,
    className: "edit-site-header-edit-mode__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpen,
    onMouseDown: preventDefault,
    onClick: toggleInserter,
    disabled: !isVisualMode,
    icon: library_plus,
    label: showIconLabels ? shortLabel : longLabel,
    showTooltip: !showIconLabels
  }), isLargeViewport && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_blockEditor_namespaceObject.ToolSelector,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined,
    disabled: !isVisualMode
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: undo_redo_undo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: undo_redo_redo,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_components_namespaceObject.Button,
    className: "edit-site-header-edit-mode__list-view-toggle",
    disabled: !isVisualMode || isZoomedOutView,
    icon: list_view,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('List View'),
    onClick: toggleListView,
    shortcut: listViewShortcut,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), isZoomedOutViewExperimentEnabled && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ToolbarItem, {
    as: external_wp_components_namespaceObject.Button,
    className: "edit-site-header-edit-mode__zoom-out-view-toggle",
    icon: chevron_up_down,
    isPressed: isZoomedOutView
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: (0,external_wp_i18n_namespaceObject.__)('Zoom-out View'),
    onClick: () => {
      setPreviewDeviceType('desktop');

      __unstableSetEditorMode(isZoomedOutView ? 'edit' : 'zoom-out');
    }
  })))), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-header-edit-mode__center"
  }, hasStyleBook ? (0,external_wp_i18n_namespaceObject.__)('Style Book') : (0,external_wp_element_namespaceObject.createElement)(DocumentActions, null)), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-header-edit-mode__end"
  }, (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-header-edit-mode__actions"
  }, !isFocusMode && !hasStyleBook && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-header-edit-mode__preview-options', {
      'is-zoomed-out': isZoomedOutView
    })
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalPreviewOptions, {
    deviceType: deviceType,
    setDeviceType: setPreviewDeviceType
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    viewLabel: (0,external_wp_i18n_namespaceObject.__)('View')
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuGroup, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.MenuItem, {
    href: homeUrl,
    target: "_blank",
    icon: library_external
  }, (0,external_wp_i18n_namespaceObject.__)('View site'), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.VisuallyHidden, {
    as: "span"
  },
  /* translators: accessibility text */
  (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)')))))), (0,external_wp_element_namespaceObject.createElement)(save_button_SaveButton, null), (0,external_wp_element_namespaceObject.createElement)(pinned_items.Slot, {
    scope: "core/edit-site"
  }), (0,external_wp_element_namespaceObject.createElement)(MoreMenu, {
    showIconLabels: showIconLabels
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sync-state-with-url/use-init-edited-entity-from-url.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function useInitEditedEntityFromURL() {
  const {
    params: {
      postId,
      postType
    } = {}
  } = useLocation();
  const {
    isRequestingSite,
    homepageId,
    url
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSite
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getSite();
    return {
      isRequestingSite: !siteData,
      homepageId: (siteData === null || siteData === void 0 ? void 0 : siteData.show_on_front) === 'page' ? siteData.page_on_front : null,
      url: siteData === null || siteData === void 0 ? void 0 : siteData.url
    };
  }, []);
  const {
    setTemplate,
    setTemplatePart,
    setPage
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (postType && postId) {
      switch (postType) {
        case 'wp_template':
          setTemplate(postId);
          break;

        case 'wp_template_part':
          setTemplatePart(postId);
          break;

        default:
          setPage({
            context: {
              postType,
              postId
            }
          });
      }

      return;
    } // In all other cases, we need to set the home page in the site editor view.


    if (homepageId) {
      setPage({
        context: {
          postType: 'page',
          postId: homepageId
        }
      });
    } else if (!isRequestingSite) {
      setPage({
        path: url
      });
    }
  }, [url, postId, postType, homepageId, isRequestingSite, setPage, setTemplate, setTemplatePart]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js


/**
 * WordPress dependencies
 */

const wordpress = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ var library_wordpress = (wordpress);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/site-icon/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







function SiteIcon(_ref) {
  let {
    className
  } = _ref;
  const {
    isRequestingSite,
    siteIconUrl
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getEntityRecord,
      isResolving
    } = select(external_wp_coreData_namespaceObject.store);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      isRequestingSite: isResolving('core', 'getEntityRecord', ['root', '__unstableBase', undefined]),
      siteIconUrl: siteData.site_icon_url
    };
  }, []);

  if (isRequestingSite && !siteIconUrl) {
    return null;
  }

  const icon = siteIconUrl ? (0,external_wp_element_namespaceObject.createElement)("img", {
    className: "edit-site-site-icon__image",
    alt: (0,external_wp_i18n_namespaceObject.__)('Site Icon'),
    src: siteIconUrl
  }) : (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Icon, {
    className: "edit-site-site-icon__icon",
    size: "32px",
    icon: library_wordpress
  });
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()(className, 'edit-site-site-icon')
  }, icon);
}

/* harmony default export */ var site_icon = (SiteIcon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/site-hub/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */




const HUB_ANIMATION_DURATION = 0.3;
const SiteHub = (0,external_wp_element_namespaceObject.forwardRef)((props, ref) => {
  const {
    canvasMode,
    dashboardLink
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    select(store_store).getEditedPostType();
    const {
      getCanvasMode,
      getSettings
    } = unlock(select(store_store));
    return {
      canvasMode: getCanvasMode(),
      dashboardLink: getSettings().__experimentalDashboardLink
    };
  }, []);
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const isBackToDashboardButton = canvasMode === 'view';
  const showLabels = canvasMode !== 'edit';
  const siteIconButtonProps = isBackToDashboardButton ? {
    href: dashboardLink || 'index.php',
    'aria-label': (0,external_wp_i18n_namespaceObject.__)('Go back to the dashboard')
  } : {
    label: (0,external_wp_i18n_namespaceObject.__)('Open Navigation Sidebar'),
    onClick: () => {
      clearSelectedBlock();
      setCanvasMode('view');
    }
  };
  const siteTitle = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$getEntityReco;

    return (_select$getEntityReco = select(external_wp_coreData_namespaceObject.store).getEntityRecord('root', 'site')) === null || _select$getEntityReco === void 0 ? void 0 : _select$getEntityReco.title;
  }, []);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, extends_extends({
    ref: ref
  }, props, {
    className: classnames_default()('edit-site-site-hub', props.className),
    layout: true,
    transition: {
      type: 'tween',
      duration: disableMotion ? 0 : HUB_ANIMATION_DURATION,
      ease: 'easeOut'
    }
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    justify: "flex-start",
    className: "edit-site-site-hub__text-content",
    spacing: "0"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    className: "edit-site-site-hub__view-mode-toggle-container",
    layout: true,
    transition: {
      type: 'tween',
      duration: disableMotion ? 0 : HUB_ANIMATION_DURATION,
      ease: 'easeOut'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, extends_extends({}, siteIconButtonProps, {
    className: "edit-site-layout__view-mode-toggle"
  }), (0,external_wp_element_namespaceObject.createElement)(site_icon, {
    className: "edit-site-layout__view-mode-toggle-icon"
  }))), showLabels && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-site-hub__site-title"
  }, siteTitle)));
});
/* harmony default export */ var site_hub = (SiteHub);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sync-state-with-url/use-sync-canvas-mode-with-url.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




function useSyncCanvasModeWithURL() {
  const history = useHistory();
  const {
    params
  } = useLocation();
  const canvasMode = (0,external_wp_data_namespaceObject.useSelect)(select => unlock(select(store_store)).getCanvasMode(), []);
  const {
    setCanvasMode
  } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store_store));
  const currentCanvasMode = (0,external_wp_element_namespaceObject.useRef)(canvasMode);
  const {
    canvas: canvasInUrl
  } = params;
  const currentCanvasInUrl = (0,external_wp_element_namespaceObject.useRef)(canvasInUrl);
  const currentUrlParams = (0,external_wp_element_namespaceObject.useRef)(params);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentUrlParams.current = params;
  }, [params]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentCanvasMode.current = canvasMode;

    if (canvasMode === 'init') {
      return;
    }

    if (canvasMode === 'edit' && currentCanvasInUrl.current !== canvasMode) {
      history.push({ ...currentUrlParams.current,
        canvas: 'edit'
      });
    }

    if (canvasMode === 'view' && currentCanvasInUrl.current !== undefined) {
      history.push({ ...currentUrlParams.current,
        canvas: undefined
      });
    }
  }, [canvasMode, history]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    currentCanvasInUrl.current = canvasInUrl;

    if (canvasInUrl === undefined && currentCanvasMode.current !== 'view') {
      setCanvasMode('view');
    } else if (canvasInUrl === 'edit' && currentCanvasMode.current !== 'edit') {
      setCanvasMode('edit');
    }
  }, [canvasInUrl, setCanvasMode]);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/save-panel/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function SavePanel() {
  const {
    isSaveViewOpen,
    canvasMode
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      isSaveViewOpened,
      getCanvasMode
    } = unlock(select(store_store)); // The currently selected entity to display.
    // Typically template or template part in the site editor.

    return {
      isSaveViewOpen: isSaveViewOpened(),
      canvasMode: getCanvasMode()
    };
  }, []);
  const {
    setIsSaveViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);

  const onClose = () => setIsSaveViewOpened(false);

  if (canvasMode === 'view') {
    return isSaveViewOpen ? (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Modal, {
      className: "edit-site-save-panel__modal",
      onRequestClose: onClose,
      __experimentalHideHeader: true,
      contentLabel: (0,external_wp_i18n_namespaceObject.__)('Save site, content, and template changes')
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EntitiesSavedStates, {
      close: onClose
    })) : null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: classnames_default()('edit-site-layout__actions', {
      'is-entity-save-view-open': isSaveViewOpen
    }),
    ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Save sidebar')
  }, isSaveViewOpen ? (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.EntitiesSavedStates, {
    close: onClose
  }) : (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-editor__toggle-save-panel"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "secondary",
    className: "edit-site-editor__toggle-save-panel-button",
    onClick: () => setIsSaveViewOpened(true),
    "aria-expanded": false
  }, (0,external_wp_i18n_namespaceObject.__)('Open save panel'))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcuts/register.js
/**
 * WordPress dependencies
 */






function KeyboardShortcutsRegister() {
  // Registering the shortcuts.
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/edit-site/save',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Save your changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    registerShortcut({
      name: 'core/edit-site/undo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/edit-site/redo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      },
      // Disable on Apple OS because it conflicts with the browser's
      // history shortcut. It's a fine alias for both Windows and Linux.
      // Since there's no conflict for Ctrl+Shift+Z on both Windows and
      // Linux, we keep it as the default for consistency.
      aliases: (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? [] : [{
        modifier: 'primary',
        character: 'y'
      }]
    });
    registerShortcut({
      name: 'core/edit-site/toggle-list-view',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Open the block list view.'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
    registerShortcut({
      name: 'core/edit-site/toggle-block-settings-sidebar',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Show or hide the block settings sidebar.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: ','
      }
    });
    registerShortcut({
      name: 'core/edit-site/keyboard-shortcuts',
      category: 'main',
      description: (0,external_wp_i18n_namespaceObject.__)('Display these keyboard shortcuts.'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
    registerShortcut({
      name: 'core/edit-site/next-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the next part of the editor.'),
      keyCombination: {
        modifier: 'ctrl',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'n'
      }]
    });
    registerShortcut({
      name: 'core/edit-site/previous-region',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }, {
        modifier: 'ctrlShift',
        character: '~'
      }]
    });
    registerShortcut({
      name: 'core/edit-site/toggle-mode',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Switch between visual editor and code editor.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'm'
      }
    });
    registerShortcut({
      name: `core/edit-site/transform-heading-to-paragraph`,
      category: 'block-library',
      description: (0,external_wp_i18n_namespaceObject.__)('Transform heading to paragraph.'),
      keyCombination: {
        modifier: 'access',
        character: `0`
      }
    });
    [1, 2, 3, 4, 5, 6].forEach(level => {
      registerShortcut({
        name: `core/edit-site/transform-paragraph-to-heading-${level}`,
        category: 'block-library',
        description: (0,external_wp_i18n_namespaceObject.__)('Transform paragraph to heading.'),
        keyCombination: {
          modifier: 'access',
          character: `${level}`
        }
      });
    });
  }, [registerShortcut]);
  return null;
}

/* harmony default export */ var register = (KeyboardShortcutsRegister);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/keyboard-shortcuts/global.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function KeyboardShortcutsGlobal() {
  const {
    __experimentalGetDirtyEntityRecords,
    isSavingEntityRecord
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_coreData_namespaceObject.store);
  const {
    setIsSaveViewOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store_store);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/edit-site/save', event => {
    event.preventDefault();

    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();

    const isDirty = !!dirtyEntityRecords.length;
    const isSaving = dirtyEntityRecords.some(record => isSavingEntityRecord(record.kind, record.name, record.key));

    if (!isSaving && isDirty) {
      setIsSaveViewOpened(true);
    }
  });
  return null;
}

/* harmony default export */ var global = (KeyboardShortcutsGlobal);

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/layout/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */

















const ANIMATION_DURATION = 0.5;
const emptyResizeHandleStyles = {
  position: undefined,
  userSelect: undefined,
  cursor: undefined,
  width: undefined,
  height: undefined,
  top: undefined,
  right: undefined,
  bottom: undefined,
  left: undefined
};
function Layout() {
  // This ensures the edited entity id and type are initialized properly.
  useInitEditedEntityFromURL();
  useSyncCanvasModeWithURL();
  const hubRef = (0,external_wp_element_namespaceObject.useRef)();
  const {
    params
  } = useLocation();
  const isListPage = getIsListPage(params);
  const isEditorPage = !isListPage;
  const {
    canvasMode,
    previousShortcut,
    nextShortcut
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getAllShortcutKeyCombinations
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    const {
      getCanvasMode
    } = unlock(select(store_store));
    return {
      canvasMode: getCanvasMode(),
      previousShortcut: getAllShortcutKeyCombinations('core/edit-site/previous-region'),
      nextShortcut: getAllShortcutKeyCombinations('core/edit-site/next-region')
    };
  }, []);
  const navigateRegionsProps = (0,external_wp_components_namespaceObject.__unstableUseNavigateRegions)({
    previous: previousShortcut,
    next: nextShortcut
  });
  const disableMotion = (0,external_wp_compose_namespaceObject.useReducedMotion)();
  const isMobileViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('medium', '<');
  const canvasPadding = isMobileViewport ? 0 : 24;
  const showSidebar = isMobileViewport && !isListPage || !isMobileViewport && (canvasMode === 'view' || !isEditorPage);
  const showCanvas = isMobileViewport && isEditorPage && canvasMode === 'edit' || !isMobileViewport || !isEditorPage;
  const showFrame = !isEditorPage && !isMobileViewport || !isMobileViewport && isEditorPage && canvasMode === 'view';
  const isFullCanvas = isMobileViewport && isListPage || isEditorPage && canvasMode === 'edit';
  const [canvasResizer, canvasSize] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const [fullResizer, fullSize] = (0,external_wp_compose_namespaceObject.useResizeObserver)();
  const [forcedWidth, setForcedWidth] = (0,external_wp_element_namespaceObject.useState)(null);
  const [isResizing, setIsResizing] = (0,external_wp_element_namespaceObject.useState)(false);
  const isResizingEnabled = !isMobileViewport && canvasMode === 'view';
  const defaultSidebarWidth = isMobileViewport ? '100vw' : 360;
  let canvasWidth = isResizing ? '100%' : fullSize.width;

  if (showFrame && !isResizing) {
    canvasWidth = canvasSize.width - canvasPadding;
  } // Synchronizing the URL with the store value of canvasMode happens in an effect
  // This condition ensures the component is only rendered after the synchronization happens
  // which prevents any animations due to potential canvasMode value change.


  if (canvasMode === 'init') {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(register, null), (0,external_wp_element_namespaceObject.createElement)(global, null), fullResizer, (0,external_wp_element_namespaceObject.createElement)("div", extends_extends({}, navigateRegionsProps, {
    ref: navigateRegionsProps.ref,
    className: classnames_default()('edit-site-layout', navigateRegionsProps.className, {
      'is-full-canvas': isFullCanvas,
      'is-edit-mode': canvasMode === 'edit'
    })
  }), (0,external_wp_element_namespaceObject.createElement)(site_hub, {
    ref: hubRef,
    className: "edit-site-layout__hub",
    style: {
      width: isResizingEnabled && forcedWidth ? forcedWidth - 48 : undefined
    }
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableAnimatePresence, {
    initial: false
  }, isEditorPage && canvasMode === 'edit' && (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    className: "edit-site-layout__header",
    ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Editor top bar'),
    as: external_wp_components_namespaceObject.__unstableMotion.div,
    animate: {
      y: 0
    },
    initial: {
      y: '-100%'
    },
    exit: {
      y: '-100%'
    },
    transition: {
      type: 'tween',
      duration: disableMotion ? 0 : ANIMATION_DURATION,
      ease: 'easeOut'
    }
  }, canvasMode === 'edit' && (0,external_wp_element_namespaceObject.createElement)(HeaderEditMode, null))), (0,external_wp_element_namespaceObject.createElement)("div", {
    className: "edit-site-layout__content"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableAnimatePresence, {
    initial: false
  }, showSidebar && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.ResizableBox, {
    as: external_wp_components_namespaceObject.__unstableMotion.div,
    initial: {
      opacity: 0
    },
    animate: {
      opacity: 1
    },
    exit: {
      opacity: 0
    },
    transition: {
      type: 'tween',
      duration: disableMotion || isResizing ? 0 : ANIMATION_DURATION,
      ease: 'easeOut'
    },
    size: {
      height: '100%',
      width: isResizingEnabled && forcedWidth ? forcedWidth : defaultSidebarWidth
    },
    className: "edit-site-layout__sidebar",
    enable: {
      right: isResizingEnabled
    },
    onResizeStop: (event, direction, elt) => {
      setForcedWidth(elt.clientWidth);
      setIsResizing(false);
    },
    onResizeStart: () => {
      setIsResizing(true);
    },
    onResize: (event, direction, elt) => {
      // This is a performance optimization
      // We set the width imperatively to avoid re-rendering
      // the whole component while resizing.
      hubRef.current.style.width = elt.clientWidth - 48 + 'px';
    },
    handleComponent: {
      right: (0,external_wp_element_namespaceObject.createElement)(ResizeHandle, {
        direction: "right",
        variation: "separator",
        resizeWidthBy: delta => {
          setForcedWidth((forcedWidth !== null && forcedWidth !== void 0 ? forcedWidth : defaultSidebarWidth) + delta);
        }
      })
    },
    handleClasses: undefined,
    handleStyles: {
      right: emptyResizeHandleStyles
    },
    minWidth: isResizingEnabled ? 320 : undefined,
    maxWidth: isResizingEnabled && fullSize ? fullSize.width - 360 : undefined
  }, (0,external_wp_element_namespaceObject.createElement)(NavigableRegion, {
    ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Navigation sidebar')
  }, (0,external_wp_element_namespaceObject.createElement)(sidebar, null)))), (0,external_wp_element_namespaceObject.createElement)(SavePanel, null), showCanvas && (0,external_wp_element_namespaceObject.createElement)("div", {
    className: classnames_default()('edit-site-layout__canvas-container', {
      'is-resizing': isResizing
    }),
    style: {
      paddingTop: showFrame ? canvasPadding : 0,
      paddingBottom: showFrame ? canvasPadding : 0
    }
  }, canvasResizer, !!canvasSize.width && (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    whileHover: isEditorPage && canvasMode === 'view' ? {
      scale: 1.01,
      transition: {
        duration: disableMotion || isResizing ? 0 : 0.2
      }
    } : {},
    initial: false,
    layout: "position",
    className: "edit-site-layout__canvas",
    transition: {
      type: 'tween',
      duration: disableMotion || isResizing ? 0 : ANIMATION_DURATION,
      ease: 'easeOut'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__unstableMotion.div, {
    style: {
      position: 'absolute',
      top: 0,
      left: 0,
      bottom: 0
    },
    initial: false,
    animate: {
      width: canvasWidth
    },
    transition: {
      type: 'tween',
      duration: disableMotion || isResizing ? 0 : ANIMATION_DURATION,
      ease: 'easeOut'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(ErrorBoundary, null, isEditorPage && (0,external_wp_element_namespaceObject.createElement)(Editor, null), isListPage && (0,external_wp_element_namespaceObject.createElement)(List, null))))))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/app/index.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function App() {
  const {
    createErrorNotice
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);

  function onPluginAreaError(name) {
    createErrorNotice((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: plugin name */
    (0,external_wp_i18n_namespaceObject.__)('The "%s" plugin has encountered an error and cannot be rendered.'), name));
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_keyboardShortcuts_namespaceObject.ShortcutProvider, {
    style: {
      height: '100%'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SlotFillProvider, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover.Slot, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_editor_namespaceObject.UnsavedChangesWarning, null), (0,external_wp_element_namespaceObject.createElement)(Routes, null, (0,external_wp_element_namespaceObject.createElement)(Layout, null), (0,external_wp_element_namespaceObject.createElement)(external_wp_plugins_namespaceObject.PluginArea, {
    onError: onPluginAreaError
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/sidebar-edit-mode/plugin-sidebar/index.js



/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Renders a sidebar when activated. The contents within the `PluginSidebar` will appear as content within the sidebar.
 * It also automatically renders a corresponding `PluginSidebarMenuItem` component when `isPinnable` flag is set to `true`.
 * If you wish to display the sidebar, you can with use the `PluginSidebarMoreMenuItem` component or the `wp.data.dispatch` API:
 *
 * ```js
 * wp.data.dispatch( 'core/edit-site' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
 * ```
 *
 * @see PluginSidebarMoreMenuItem
 *
 * @param {Object}                props                                 Element props.
 * @param {string}                props.name                            A string identifying the sidebar. Must be unique for every sidebar registered within the scope of your plugin.
 * @param {string}                [props.className]                     An optional class name added to the sidebar body.
 * @param {string}                props.title                           Title displayed at the top of the sidebar.
 * @param {boolean}               [props.isPinnable=true]               Whether to allow to pin sidebar to the toolbar. When set to `true` it also automatically renders a corresponding menu item.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var el = wp.element.createElement;
 * var PanelBody = wp.components.PanelBody;
 * var PluginSidebar = wp.editSite.PluginSidebar;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MyPluginSidebar() {
 * 	return el(
 * 			PluginSidebar,
 * 			{
 * 				name: 'my-sidebar',
 * 				title: 'My sidebar title',
 * 				icon: moreIcon,
 * 			},
 * 			el(
 * 				PanelBody,
 * 				{},
 * 				__( 'My sidebar content' )
 * 			)
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PanelBody } from '@wordpress/components';
 * import { PluginSidebar } from '@wordpress/edit-site';
 * import { more } from '@wordpress/icons';
 *
 * const MyPluginSidebar = () => (
 * 	<PluginSidebar
 * 		name="my-sidebar"
 * 		title="My sidebar title"
 * 		icon={ more }
 * 	>
 * 		<PanelBody>
 * 			{ __( 'My sidebar content' ) }
 * 		</PanelBody>
 * 	</PluginSidebar>
 * );
 * ```
 */

function PluginSidebarEditSite(_ref) {
  let {
    className,
    ...props
  } = _ref;
  const showIconLabels = (0,external_wp_data_namespaceObject.useSelect)(select => select(store_store).getSettings().showIconLabels, []);
  return (0,external_wp_element_namespaceObject.createElement)(complementary_area, extends_extends({
    panelClassName: className,
    className: "edit-site-sidebar-edit-mode",
    scope: "core/edit-site",
    showIconLabels: showIconLabels
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/plugin-sidebar-more-menu-item/index.js



/**
 * WordPress dependencies
 */

/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down,
 * and can be used to activate the corresponding `PluginSidebar` component.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                props.target                          A string identifying the target sidebar you wish to be activated by this menu item. Must be the same as the `name` prop you have given to that sidebar.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginSidebarMoreMenuItem = wp.editSite.PluginSidebarMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function MySidebarMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginSidebarMoreMenuItem,
 * 		{
 * 			target: 'my-sidebar',
 * 			icon: moreIcon,
 * 		},
 * 		__( 'My sidebar title' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginSidebarMoreMenuItem } from '@wordpress/edit-site';
 * import { more } from '@wordpress/icons';
 *
 * const MySidebarMoreMenuItem = () => (
 * 	<PluginSidebarMoreMenuItem
 * 		target="my-sidebar"
 * 		icon={ more }
 * 	>
 * 		{ __( 'My sidebar title' ) }
 * 	</PluginSidebarMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

function PluginSidebarMoreMenuItem(props) {
  return (0,external_wp_element_namespaceObject.createElement)(ComplementaryAreaMoreMenuItem // Menu item is marked with unstable prop for backward compatibility.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  , extends_extends({
    __unstableExplicitMenuItem: true,
    scope: "core/edit-site"
  }, props));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/components/header-edit-mode/plugin-more-menu-item/index.js
/**
 * WordPress dependencies
 */




/**
 * Renders a menu item in `Plugins` group in `More Menu` drop down, and can be used to as a button or link depending on the props provided.
 * The text within the component appears as the menu item label.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.href]                          When `href` is provided then the menu item is represented as an anchor rather than button. It corresponds to the `href` attribute of the anchor.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered to the left of the menu item label.
 * @param {Function}              [props.onClick=noop]                  The callback function to be executed when the user clicks the menu item.
 * @param {...*}                  [props.other]                         Any additional props are passed through to the underlying [Button](/packages/components/src/button/README.md) component.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginMoreMenuItem = wp.editSite.PluginMoreMenuItem;
 * var moreIcon = wp.element.createElement( 'svg' ); //... svg element.
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * function MyButtonMoreMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginMoreMenuItem,
 * 		{
 * 			icon: moreIcon,
 * 			onClick: onButtonClick,
 * 		},
 * 		__( 'My button title' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginMoreMenuItem } from '@wordpress/edit-site';
 * import { more } from '@wordpress/icons';
 *
 * function onButtonClick() {
 * 	alert( 'Button clicked.' );
 * }
 *
 * const MyButtonMoreMenuItem = () => (
 * 	<PluginMoreMenuItem
 * 		icon={ more }
 * 		onClick={ onButtonClick }
 * 	>
 * 		{ __( 'My button title' ) }
 * 	</PluginMoreMenuItem>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

/* harmony default export */ var plugin_more_menu_item = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_plugins_namespaceObject.withPluginContext)((context, ownProps) => {
  var _ownProps$as;

  return {
    as: (_ownProps$as = ownProps.as) !== null && _ownProps$as !== void 0 ? _ownProps$as : external_wp_components_namespaceObject.MenuItem,
    icon: ownProps.icon || context.icon,
    name: 'core/edit-site/plugin-more-menu'
  };
}))(action_item));

;// CONCATENATED MODULE: ./node_modules/@wordpress/edit-site/build-module/index.js


/**
 * WordPress dependencies
 */










/**
 * Internal dependencies
 */




/**
 * Initializes the site editor screen.
 *
 * @param {string} id       ID of the root element to render the screen in.
 * @param {Object} settings Editor settings.
 */

function initializeEditor(id, settings) {
  const target = document.getElementById(id);
  const root = (0,external_wp_element_namespaceObject.createRoot)(target);

  settings.__experimentalFetchLinkSuggestions = (search, searchOptions) => (0,external_wp_coreData_namespaceObject.__experimentalFetchLinkSuggestions)(search, searchOptions, settings);

  settings.__experimentalFetchRichUrlData = external_wp_coreData_namespaceObject.__experimentalFetchUrlData;

  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).__experimentalReapplyBlockTypeFilters();

  const coreBlocks = (0,external_wp_blockLibrary_namespaceObject.__experimentalGetCoreBlocks)().filter(_ref => {
    let {
      name
    } = _ref;
    return name !== 'core/freeform';
  });

  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)(coreBlocks);
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).setFreeformFallbackBlockName('core/html');
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)({
    inserter: false
  });
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)({
    inserter: false
  });

  if (false) {} // We dispatch actions and update the store synchronously before rendering
  // so that we won't trigger unnecessary re-renders with useEffect.


  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core/edit-site', {
    editorMode: 'visual',
    fixedToolbar: false,
    focusMode: false,
    keepCaretInsideBlock: false,
    welcomeGuide: true,
    welcomeGuideStyles: true,
    showListViewByDefault: false
  });
  (0,external_wp_data_namespaceObject.dispatch)(store).setDefaultComplementaryArea('core/edit-site', 'edit-site/template');
  (0,external_wp_data_namespaceObject.dispatch)(store_store).updateSettings(settings); // Keep the defaultTemplateTypes in the core/editor settings too,
  // so that they can be selected with core/editor selectors in any editor.
  // This is needed because edit-site doesn't initialize with EditorProvider,
  // which internally uses updateEditorSettings as well.

  (0,external_wp_data_namespaceObject.dispatch)(external_wp_editor_namespaceObject.store).updateEditorSettings({
    defaultTemplateTypes: settings.defaultTemplateTypes,
    defaultTemplatePartAreas: settings.defaultTemplatePartAreas
  }); // Prevent the default browser action for files dropped outside of dropzones.

  window.addEventListener('dragover', e => e.preventDefault(), false);
  window.addEventListener('drop', e => e.preventDefault(), false);
  root.render((0,external_wp_element_namespaceObject.createElement)(App, null));
  return root;
}
function reinitializeEditor() {
  external_wp_deprecated_default()('wp.editSite.reinitializeEditor', {
    since: '6.2',
    version: '6.3'
  });
}




}();
(window.wp = window.wp || {}).editSite = __webpack_exports__;
/******/ })()
;