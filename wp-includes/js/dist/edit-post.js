this["wp"] = this["wp"] || {}; this["wp"]["editPost"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "dSQ2");
/******/ })
/************************************************************************/
/******/ ({

/***/ "0dt7":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return STORE_NAME; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return VIEW_AS_LINK_SELECTOR; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return VIEW_AS_PREVIEW_LINK_SELECTOR; });
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/edit-post';
/**
 * CSS selector string for the admin bar view post link anchor tag.
 *
 * @type {string}
 */

const VIEW_AS_LINK_SELECTOR = '#wp-admin-bar-view a';
/**
 * CSS selector string for the admin bar preview post link anchor tag.
 *
 * @type {string}
 */

const VIEW_AS_PREVIEW_LINK_SELECTOR = '#wp-admin-bar-preview a';


/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "1iEr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronRight = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronRight);


/***/ }),

/***/ "2gm7":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronLeft);


/***/ }),

/***/ "51Zz":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dataControls"]; }());

/***/ }),

/***/ "6aBm":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["mediaUtils"]; }());

/***/ }),

/***/ "8oxB":
/***/ (function(module, exports) {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ }),

/***/ "Cg8A":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const cog = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M10.289 4.836A1 1 0 0111.275 4h1.306a1 1 0 01.987.836l.244 1.466c.787.26 1.503.679 2.108 1.218l1.393-.522a1 1 0 011.216.437l.653 1.13a1 1 0 01-.23 1.273l-1.148.944a6.025 6.025 0 010 2.435l1.149.946a1 1 0 01.23 1.272l-.653 1.13a1 1 0 01-1.216.437l-1.394-.522c-.605.54-1.32.958-2.108 1.218l-.244 1.466a1 1 0 01-.987.836h-1.306a1 1 0 01-.986-.836l-.244-1.466a5.995 5.995 0 01-2.108-1.218l-1.394.522a1 1 0 01-1.217-.436l-.653-1.131a1 1 0 01.23-1.272l1.149-.946a6.026 6.026 0 010-2.435l-1.148-.944a1 1 0 01-.23-1.272l.653-1.131a1 1 0 011.217-.437l1.393.522a5.994 5.994 0 012.108-1.218l.244-1.466zM14.929 12a3 3 0 11-6 0 3 3 0 016 0z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (cog);


/***/ }),

/***/ "Civd":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const layout = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 5.5H6a.5.5 0 00-.5.5v3h13V6a.5.5 0 00-.5-.5zm.5 5H10v8h8a.5.5 0 00.5-.5v-7.5zm-10 0h-3V18a.5.5 0 00.5.5h2.5v-8zM6 4h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (layout);


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "K+tz":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const external = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (external);


/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "KEfo":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["viewport"]; }());

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ }),

/***/ "NWDH":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const chevronDown = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (chevronDown);


/***/ }),

/***/ "OzlF":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const listView = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (listView);


/***/ }),

/***/ "Q4Sy":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const plus = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ __webpack_exports__["a"] = (plus);


/***/ }),

/***/ "QyPg":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockLibrary"]; }());

/***/ }),

/***/ "RMJe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const check = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M16.7 7.1l-6.3 8.5-3.3-2.5-.9 1.2 4.5 3.4L17.9 8z"
}));
/* harmony default export */ __webpack_exports__["a"] = (check);


/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keycodes"]; }());

/***/ }),

/***/ "T40v":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ store; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "openGeneralSidebar", function() { return openGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "closeGeneralSidebar", function() { return closeGeneralSidebar; });
__webpack_require__.d(actions_namespaceObject, "openModal", function() { return openModal; });
__webpack_require__.d(actions_namespaceObject, "closeModal", function() { return closeModal; });
__webpack_require__.d(actions_namespaceObject, "openPublishSidebar", function() { return openPublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "closePublishSidebar", function() { return closePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "togglePublishSidebar", function() { return togglePublishSidebar; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelEnabled", function() { return toggleEditorPanelEnabled; });
__webpack_require__.d(actions_namespaceObject, "toggleEditorPanelOpened", function() { return toggleEditorPanelOpened; });
__webpack_require__.d(actions_namespaceObject, "removeEditorPanel", function() { return removeEditorPanel; });
__webpack_require__.d(actions_namespaceObject, "toggleFeature", function() { return toggleFeature; });
__webpack_require__.d(actions_namespaceObject, "switchEditorMode", function() { return switchEditorMode; });
__webpack_require__.d(actions_namespaceObject, "togglePinnedPluginItem", function() { return togglePinnedPluginItem; });
__webpack_require__.d(actions_namespaceObject, "hideBlockTypes", function() { return hideBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "updatePreferredStyleVariations", function() { return updatePreferredStyleVariations; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateLocalAutosaveInterval", function() { return __experimentalUpdateLocalAutosaveInterval; });
__webpack_require__.d(actions_namespaceObject, "showBlockTypes", function() { return showBlockTypes; });
__webpack_require__.d(actions_namespaceObject, "setAvailableMetaBoxesPerLocation", function() { return setAvailableMetaBoxesPerLocation; });
__webpack_require__.d(actions_namespaceObject, "requestMetaBoxUpdates", function() { return requestMetaBoxUpdates; });
__webpack_require__.d(actions_namespaceObject, "metaBoxUpdatesSuccess", function() { return metaBoxUpdatesSuccess; });
__webpack_require__.d(actions_namespaceObject, "metaBoxUpdatesFailure", function() { return metaBoxUpdatesFailure; });
__webpack_require__.d(actions_namespaceObject, "__experimentalSetPreviewDeviceType", function() { return __experimentalSetPreviewDeviceType; });
__webpack_require__.d(actions_namespaceObject, "setIsInserterOpened", function() { return setIsInserterOpened; });
__webpack_require__.d(actions_namespaceObject, "setIsListViewOpened", function() { return setIsListViewOpened; });
__webpack_require__.d(actions_namespaceObject, "setIsEditingTemplate", function() { return setIsEditingTemplate; });
__webpack_require__.d(actions_namespaceObject, "__unstableSwitchToTemplateMode", function() { return __unstableSwitchToTemplateMode; });
__webpack_require__.d(actions_namespaceObject, "__unstableCreateTemplate", function() { return __unstableCreateTemplate; });
__webpack_require__.d(actions_namespaceObject, "initializeMetaBoxes", function() { return initializeMetaBoxes; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getEditorMode", function() { return getEditorMode; });
__webpack_require__.d(selectors_namespaceObject, "isEditorSidebarOpened", function() { return isEditorSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isPluginSidebarOpened", function() { return isPluginSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "getActiveGeneralSidebarName", function() { return getActiveGeneralSidebarName; });
__webpack_require__.d(selectors_namespaceObject, "getPreferences", function() { return getPreferences; });
__webpack_require__.d(selectors_namespaceObject, "getPreference", function() { return getPreference; });
__webpack_require__.d(selectors_namespaceObject, "isPublishSidebarOpened", function() { return isPublishSidebarOpened; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelRemoved", function() { return isEditorPanelRemoved; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelEnabled", function() { return isEditorPanelEnabled; });
__webpack_require__.d(selectors_namespaceObject, "isEditorPanelOpened", function() { return isEditorPanelOpened; });
__webpack_require__.d(selectors_namespaceObject, "isModalActive", function() { return isModalActive; });
__webpack_require__.d(selectors_namespaceObject, "isFeatureActive", function() { return isFeatureActive; });
__webpack_require__.d(selectors_namespaceObject, "isPluginItemPinned", function() { return isPluginItemPinned; });
__webpack_require__.d(selectors_namespaceObject, "getActiveMetaBoxLocations", function() { return getActiveMetaBoxLocations; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationVisible", function() { return isMetaBoxLocationVisible; });
__webpack_require__.d(selectors_namespaceObject, "isMetaBoxLocationActive", function() { return isMetaBoxLocationActive; });
__webpack_require__.d(selectors_namespaceObject, "getMetaBoxesPerLocation", function() { return getMetaBoxesPerLocation; });
__webpack_require__.d(selectors_namespaceObject, "getAllMetaBoxes", function() { return getAllMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "hasMetaBoxes", function() { return selectors_hasMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "isSavingMetaBoxes", function() { return selectors_isSavingMetaBoxes; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetPreviewDeviceType", function() { return __experimentalGetPreviewDeviceType; });
__webpack_require__.d(selectors_namespaceObject, "isInserterOpened", function() { return isInserterOpened; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetInsertionPoint", function() { return __experimentalGetInsertionPoint; });
__webpack_require__.d(selectors_namespaceObject, "isListViewOpened", function() { return isListViewOpened; });
__webpack_require__.d(selectors_namespaceObject, "isEditingTemplate", function() { return selectors_isEditingTemplate; });
__webpack_require__.d(selectors_namespaceObject, "areMetaBoxesInitialized", function() { return areMetaBoxesInitialized; });
__webpack_require__.d(selectors_namespaceObject, "getEditedPostTemplate", function() { return getEditedPostTemplate; });

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","dataControls"]
var external_wp_dataControls_ = __webpack_require__("51Zz");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/defaults.js
const PREFERENCES_DEFAULTS = {
  editorMode: 'visual',
  panels: {
    'post-status': {
      opened: true
    }
  },
  hiddenBlockTypes: [],
  preferredStyleVariations: {},
  localAutosaveInterval: 15
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/reducer.js
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
 * Higher-order reducer creator which provides the given initial state for the
 * original reducer.
 *
 * @param {*} initialState Initial state to provide to reducer.
 *
 * @return {Function} Higher-order reducer.
 */

const createWithInitialState = initialState => reducer => {
  return function () {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
    let action = arguments.length > 1 ? arguments[1] : undefined;
    return reducer(state, action);
  };
};
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                           Current state.
 * @param {string}  state.mode                      Current editor mode, either
 *                                                  "visual" or "text".
 * @param {boolean} state.isGeneralSidebarDismissed Whether general sidebar is
 *                                                  dismissed. False by default
 *                                                  or when closing general
 *                                                  sidebar, true when opening
 *                                                  sidebar.
 * @param {boolean} state.isSidebarOpened           Whether the sidebar is
 *                                                  opened or closed.
 * @param {Object}  state.panels                    The state of the different
 *                                                  sidebar panels.
 * @param {Object}  action                          Dispatched action.
 *
 * @return {Object} Updated state.
 */


const preferences = Object(external_lodash_["flow"])([external_wp_data_["combineReducers"], createWithInitialState(PREFERENCES_DEFAULTS)])({
  panels(state, action) {
    switch (action.type) {
      case 'TOGGLE_PANEL_ENABLED':
        {
          const {
            panelName
          } = action;
          return { ...state,
            [panelName]: { ...state[panelName],
              enabled: !Object(external_lodash_["get"])(state, [panelName, 'enabled'], true)
            }
          };
        }

      case 'TOGGLE_PANEL_OPENED':
        {
          const {
            panelName
          } = action;
          const isOpen = state[panelName] === true || Object(external_lodash_["get"])(state, [panelName, 'opened'], false);
          return { ...state,
            [panelName]: { ...state[panelName],
              opened: !isOpen
            }
          };
        }
    }

    return state;
  },

  editorMode(state, action) {
    if (action.type === 'SWITCH_MODE') {
      return action.mode;
    }

    return state;
  },

  hiddenBlockTypes(state, action) {
    switch (action.type) {
      case 'SHOW_BLOCK_TYPES':
        return Object(external_lodash_["without"])(state, ...action.blockNames);

      case 'HIDE_BLOCK_TYPES':
        return Object(external_lodash_["union"])(state, action.blockNames);
    }

    return state;
  },

  preferredStyleVariations(state, action) {
    switch (action.type) {
      case 'UPDATE_PREFERRED_STYLE_VARIATIONS':
        {
          if (!action.blockName) {
            return state;
          }

          if (!action.blockStyle) {
            return Object(external_lodash_["omit"])(state, [action.blockName]);
          }

          return { ...state,
            [action.blockName]: action.blockStyle
          };
        }
    }

    return state;
  },

  localAutosaveInterval(state, action) {
    switch (action.type) {
      case 'UPDATE_LOCAL_AUTOSAVE_INTERVAL':
        return action.interval;
    }

    return state;
  }

});
/**
 * Reducer storing the list of all programmatically removed panels.
 *
 * @param {Array}  state  Current state.
 * @param {Object} action Action object.
 *
 * @return {Array} Updated state.
 */

function removedPanels() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REMOVE_PANEL':
      if (!Object(external_lodash_["includes"])(state, action.panelName)) {
        return [...state, action.panelName];
      }

  }

  return state;
}
/**
 * Reducer for storing the name of the open modal, or null if no modal is open.
 *
 * @param {Object} state  Previous state.
 * @param {Object} action Action object containing the `name` of the modal
 *
 * @return {Object} Updated state
 */

function activeModal() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_MODAL':
      return action.name;

    case 'CLOSE_MODAL':
      return null;
  }

  return state;
}
function publishSidebarActive() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'OPEN_PUBLISH_SIDEBAR':
      return true;

    case 'CLOSE_PUBLISH_SIDEBAR':
      return false;

    case 'TOGGLE_PUBLISH_SIDEBAR':
      return !state;
  }

  return state;
}
/**
 * Reducer keeping track of the meta boxes isSaving state.
 * A "true" value means the meta boxes saving request is in-flight.
 *
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */

function isSavingMetaBoxes() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'REQUEST_META_BOX_UPDATES':
      return true;

    case 'META_BOX_UPDATES_SUCCESS':
    case 'META_BOX_UPDATES_FAILURE':
      return false;

    default:
      return state;
  }
}
/**
 * Reducer keeping track of the meta boxes per location.
 *
 * @param {boolean} state  Previous state.
 * @param {Object}  action Action Object.
 *
 * @return {Object} Updated state.
 */

function metaBoxLocations() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_META_BOXES_PER_LOCATIONS':
      return action.metaBoxesPerLocation;
  }

  return state;
}
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
 * Reducer to set the block inserter panel open or closed.
 *
 * Note: this reducer interacts with the list view panel reducer
 * to make sure that only one of the two panels is open at the same time.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 */

function blockInserterPanel() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_LIST_VIEW_OPENED':
      return action.isOpen ? false : state;

    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }

  return state;
}
/**
 * Reducer to set the list view panel open or closed.
 *
 * Note: this reducer interacts with the inserter panel reducer
 * to make sure that only one of the two panels is open at the same time.
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
  }

  return state;
}
/**
 * Reducer tracking whether the inserter is open.
 *
 * @param {boolean} state
 * @param {Object}  action
 */

function isEditingTemplate() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'SET_IS_EDITING_TEMPLATE':
      return action.value;
  }

  return state;
}
/**
 * Reducer tracking whether meta boxes are initialized.
 *
 * @param {boolean} state
 * @param {Object}  action
 *
 * @return {boolean} Updated state.
 */


function metaBoxesInitialized() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'META_BOXES_INITIALIZED':
      return true;
  }

  return state;
}

const metaBoxes = Object(external_wp_data_["combineReducers"])({
  isSaving: isSavingMetaBoxes,
  locations: metaBoxLocations,
  initialized: metaBoxesInitialized
});
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  activeModal,
  metaBoxes,
  preferences,
  publishSidebarActive,
  removedPanels,
  deviceType,
  blockInserterPanel,
  listViewPanel,
  isEditingTemplate
}));

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./node_modules/@wordpress/interface/build-module/index.js + 17 modules
var build_module = __webpack_require__("U60i");

// EXTERNAL MODULE: external ["wp","a11y"]
var external_wp_a11y_ = __webpack_require__("gdqT");

// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external ["wp","editor"]
var external_wp_editor_ = __webpack_require__("jSdM");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/utils/meta-boxes.js
/**
 * Function returning the current Meta Boxes DOM Node in the editor
 * whether the meta box area is opened or not.
 * If the MetaBox Area is visible returns it, and returns the original container instead.
 *
 * @param {string} location Meta Box location.
 *
 * @return {string} HTML content.
 */
const getMetaBoxContainer = location => {
  const area = document.querySelector(`.edit-post-meta-boxes-area.is-${location} .metabox-location-${location}`);

  if (area) {
    return area;
  }

  return document.querySelector('#metaboxes .metabox-location-' + location);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/actions.js
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
 * Returns an action object used in signalling that the user opened an editor sidebar.
 *
 * @param {?string} name Sidebar name to be opened.
 *
 * @yield {Object} Action object.
 */

function* openGeneralSidebar(name) {
  yield external_wp_data_["controls"].dispatch(build_module["i" /* store */], 'enableComplementaryArea', store.name, name);
}
/**
 * Returns an action object signalling that the user closed the sidebar.
 *
 * @yield {Object} Action object.
 */

function* closeGeneralSidebar() {
  yield external_wp_data_["controls"].dispatch(build_module["i" /* store */], 'disableComplementaryArea', store.name);
}
/**
 * Returns an action object used in signalling that the user opened a modal.
 *
 * @param {string} name A string that uniquely identifies the modal.
 *
 * @return {Object} Action object.
 */

function openModal(name) {
  return {
    type: 'OPEN_MODAL',
    name
  };
}
/**
 * Returns an action object signalling that the user closed a modal.
 *
 * @return {Object} Action object.
 */

function closeModal() {
  return {
    type: 'CLOSE_MODAL'
  };
}
/**
 * Returns an action object used in signalling that the user opened the publish
 * sidebar.
 *
 * @return {Object} Action object
 */

function openPublishSidebar() {
  return {
    type: 'OPEN_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user closed the
 * publish sidebar.
 *
 * @return {Object} Action object.
 */

function closePublishSidebar() {
  return {
    type: 'CLOSE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used in signalling that the user toggles the publish sidebar.
 *
 * @return {Object} Action object
 */

function togglePublishSidebar() {
  return {
    type: 'TOGGLE_PUBLISH_SIDEBAR'
  };
}
/**
 * Returns an action object used to enable or disable a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to enable or disable.
 *
 * @return {Object} Action object.
 */

function toggleEditorPanelEnabled(panelName) {
  return {
    type: 'TOGGLE_PANEL_ENABLED',
    panelName
  };
}
/**
 * Returns an action object used to open or close a panel in the editor.
 *
 * @param {string} panelName A string that identifies the panel to open or close.
 *
 * @return {Object} Action object.
 */

function toggleEditorPanelOpened(panelName) {
  return {
    type: 'TOGGLE_PANEL_OPENED',
    panelName
  };
}
/**
 * Returns an action object used to remove a panel from the editor.
 *
 * @param {string} panelName A string that identifies the panel to remove.
 *
 * @return {Object} Action object.
 */

function removeEditorPanel(panelName) {
  return {
    type: 'REMOVE_PANEL',
    panelName
  };
}
/**
 * Triggers an action used to toggle a feature flag.
 *
 * @param {string} feature Feature name.
 */

function* toggleFeature(feature) {
  yield external_wp_data_["controls"].dispatch(build_module["i" /* store */].name, 'toggleFeature', 'core/edit-post', feature);
}
function* switchEditorMode(mode) {
  yield {
    type: 'SWITCH_MODE',
    mode
  }; // Unselect blocks when we switch to the code editor.

  if (mode !== 'visual') {
    yield external_wp_data_["controls"].dispatch(external_wp_blockEditor_["store"], 'clearSelectedBlock');
  }

  const message = mode === 'visual' ? Object(external_wp_i18n_["__"])('Visual editor selected') : Object(external_wp_i18n_["__"])('Code editor selected');
  Object(external_wp_a11y_["speak"])(message, 'assertive');
}
/**
 * Triggers an action object used to toggle a plugin name flag.
 *
 * @param {string} pluginName Plugin name.
 */

function* togglePinnedPluginItem(pluginName) {
  const isPinned = yield external_wp_data_["controls"].select(build_module["i" /* store */], 'isItemPinned', 'core/edit-post', pluginName);
  yield external_wp_data_["controls"].dispatch(build_module["i" /* store */], isPinned ? 'unpinItem' : 'pinItem', 'core/edit-post', pluginName);
}
/**
 * Returns an action object used in signalling that block types by the given
 * name(s) should be hidden.
 *
 * @param {string[]} blockNames Names of block types to hide.
 *
 * @return {Object} Action object.
 */

function hideBlockTypes(blockNames) {
  return {
    type: 'HIDE_BLOCK_TYPES',
    blockNames: Object(external_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling that a style should be auto-applied when a block is created.
 *
 * @param {string}  blockName  Name of the block.
 * @param {?string} blockStyle Name of the style that should be auto applied. If undefined, the "auto apply" setting of the block is removed.
 *
 * @return {Object} Action object.
 */

function updatePreferredStyleVariations(blockName, blockStyle) {
  return {
    type: 'UPDATE_PREFERRED_STYLE_VARIATIONS',
    blockName,
    blockStyle
  };
}
/**
 * Returns an action object used in signalling that the editor should attempt
 * to locally autosave the current post every `interval` seconds.
 *
 * @param {number} interval The new interval, in seconds.
 * @return {Object} Action object.
 */

function __experimentalUpdateLocalAutosaveInterval(interval) {
  return {
    type: 'UPDATE_LOCAL_AUTOSAVE_INTERVAL',
    interval
  };
}
/**
 * Returns an action object used in signalling that block types by the given
 * name(s) should be shown.
 *
 * @param {string[]} blockNames Names of block types to show.
 *
 * @return {Object} Action object.
 */

function showBlockTypes(blockNames) {
  return {
    type: 'SHOW_BLOCK_TYPES',
    blockNames: Object(external_lodash_["castArray"])(blockNames)
  };
}
/**
 * Returns an action object used in signaling
 * what Meta boxes are available in which location.
 *
 * @param {Object} metaBoxesPerLocation Meta boxes per location.
 *
 * @yield {Object} Action object.
 */

function* setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
  yield {
    type: 'SET_META_BOXES_PER_LOCATIONS',
    metaBoxesPerLocation
  };
}
/**
 * Returns an action object used to request meta box update.
 *
 * @yield {Object} Action object.
 */

function* requestMetaBoxUpdates() {
  yield {
    type: 'REQUEST_META_BOX_UPDATES'
  }; // Saves the wp_editor fields

  if (window.tinyMCE) {
    window.tinyMCE.triggerSave();
  } // Additional data needed for backward compatibility.
  // If we do not provide this data, the post will be overridden with the default values.


  const post = yield external_wp_data_["controls"].select(external_wp_editor_["store"], 'getCurrentPost');
  const additionalData = [post.comment_status ? ['comment_status', post.comment_status] : false, post.ping_status ? ['ping_status', post.ping_status] : false, post.sticky ? ['sticky', post.sticky] : false, post.author ? ['post_author', post.author] : false].filter(Boolean); // We gather all the metaboxes locations data and the base form data

  const baseFormData = new window.FormData(document.querySelector('.metabox-base-form'));
  const activeMetaBoxLocations = yield external_wp_data_["controls"].select(store, 'getActiveMetaBoxLocations');
  const formDataToMerge = [baseFormData, ...activeMetaBoxLocations.map(location => new window.FormData(getMetaBoxContainer(location)))]; // Merge all form data objects into a single one.

  const formData = Object(external_lodash_["reduce"])(formDataToMerge, (memo, currentFormData) => {
    for (const [key, value] of currentFormData) {
      memo.append(key, value);
    }

    return memo;
  }, new window.FormData());
  additionalData.forEach(_ref => {
    let [key, value] = _ref;
    return formData.append(key, value);
  });

  try {
    // Save the metaboxes
    yield Object(external_wp_dataControls_["apiFetch"])({
      url: window._wpMetaBoxUrl,
      method: 'POST',
      body: formData,
      parse: false
    });
    yield external_wp_data_["controls"].dispatch(store, 'metaBoxUpdatesSuccess');
  } catch {
    yield external_wp_data_["controls"].dispatch(store, 'metaBoxUpdatesFailure');
  }
}
/**
 * Returns an action object used to signal a successful meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesSuccess() {
  return {
    type: 'META_BOX_UPDATES_SUCCESS'
  };
}
/**
 * Returns an action object used to signal a failed meta box update.
 *
 * @return {Object} Action object.
 */

function metaBoxUpdatesFailure() {
  return {
    type: 'META_BOX_UPDATES_FAILURE'
  };
}
/**
 * Returns an action object used to toggle the width of the editing canvas.
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
 * Returns an action object used to open/close the inserter.
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
 * Returns an action object used to open/close the list view.
 *
 * @param {boolean} isOpen A boolean representing whether the list view should be opened or closed.
 * @return {Object} Action object.
 */

function setIsListViewOpened(isOpen) {
  return {
    type: 'SET_IS_LIST_VIEW_OPENED',
    isOpen
  };
}
/**
 * Returns an action object used to switch to template editing.
 *
 * @param {boolean} value Is editing template.
 * @return {Object} Action object.
 */

function setIsEditingTemplate(value) {
  return {
    type: 'SET_IS_EDITING_TEMPLATE',
    value
  };
}
/**
 * Switches to the template mode.
 *
 * @param {boolean} newTemplate Is new template.
 */

function* __unstableSwitchToTemplateMode() {
  let newTemplate = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
  yield setIsEditingTemplate(true);
  const isWelcomeGuideActive = yield external_wp_data_["controls"].select(store, 'isFeatureActive', 'welcomeGuideTemplate');

  if (!isWelcomeGuideActive) {
    const message = newTemplate ? Object(external_wp_i18n_["__"])("Custom template created. You're in template mode now.") : Object(external_wp_i18n_["__"])('Editing template. Changes made here affect all posts and pages that use the template.');
    yield external_wp_data_["controls"].dispatch(external_wp_notices_["store"], 'createSuccessNotice', message, {
      type: 'snackbar'
    });
  }
}
/**
 * Create a block based template.
 *
 * @param {Object?} template Template to create and assign.
 */

function* __unstableCreateTemplate(template) {
  const savedTemplate = yield external_wp_data_["controls"].dispatch(external_wp_coreData_["store"], 'saveEntityRecord', 'postType', 'wp_template', template);
  const post = yield external_wp_data_["controls"].select(external_wp_editor_["store"], 'getCurrentPost');
  yield external_wp_data_["controls"].dispatch(external_wp_coreData_["store"], 'editEntityRecord', 'postType', post.type, post.id, {
    template: savedTemplate.slug
  });
}
let actions_metaBoxesInitialized = false;
/**
 * Initializes WordPress `postboxes` script and the logic for saving meta boxes.
 */

function* initializeMetaBoxes() {
  const isEditorReady = yield external_wp_data_["controls"].select(external_wp_editor_["store"], '__unstableIsEditorReady');

  if (!isEditorReady) {
    return;
  }

  const postType = yield external_wp_data_["controls"].select(external_wp_editor_["store"], 'getCurrentPostType'); // Only initialize once.

  if (actions_metaBoxesInitialized) {
    return;
  }

  if (window.postboxes.page !== postType) {
    window.postboxes.add_postbox_toggles(postType);
  }

  actions_metaBoxesInitialized = true;
  let wasSavingPost = yield external_wp_data_["controls"].select(external_wp_editor_["store"], 'isSavingPost');
  let wasAutosavingPost = yield external_wp_data_["controls"].select(external_wp_editor_["store"], 'isAutosavingPost');
  const hasMetaBoxes = yield external_wp_data_["controls"].select(store, 'hasMetaBoxes'); // Save metaboxes when performing a full save on the post.

  Object(external_wp_data_["subscribe"])(() => {
    const isSavingPost = Object(external_wp_data_["select"])(external_wp_editor_["store"]).isSavingPost();
    const isAutosavingPost = Object(external_wp_data_["select"])(external_wp_editor_["store"]).isAutosavingPost(); // Save metaboxes on save completion, except for autosaves that are not a post preview.
    //
    // Meta boxes are initialized once at page load. It is not necessary to
    // account for updates on each state change.
    //
    // See: https://github.com/WordPress/WordPress/blob/5.1.1/wp-admin/includes/post.php#L2307-L2309

    const shouldTriggerMetaboxesSave = hasMetaBoxes && wasSavingPost && !isSavingPost && !wasAutosavingPost; // Save current state for next inspection.

    wasSavingPost = isSavingPost;
    wasAutosavingPost = isAutosavingPost;

    if (shouldTriggerMetaboxesSave) {
      Object(external_wp_data_["dispatch"])(store).requestMetaBoxUpdates();
    }
  });
  return {
    type: 'META_BOXES_INITIALIZED'
  };
}

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Returns the current editing mode.
 *
 * @param {Object} state Global application state.
 *
 * @return {string} Editing mode.
 */

function getEditorMode(state) {
  return getPreference(state, 'editorMode', 'visual');
}
/**
 * Returns true if the editor sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the editor sidebar is opened.
 */

const isEditorSidebarOpened = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  const activeGeneralSidebar = select(build_module["i" /* store */]).getActiveComplementaryArea('core/edit-post');
  return Object(external_lodash_["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
});
/**
 * Returns true if the plugin sidebar is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the plugin sidebar is opened.
 */

const isPluginSidebarOpened = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  const activeGeneralSidebar = select(build_module["i" /* store */]).getActiveComplementaryArea('core/edit-post');
  return !!activeGeneralSidebar && !Object(external_lodash_["includes"])(['edit-post/document', 'edit-post/block'], activeGeneralSidebar);
});
/**
 * Returns the current active general sidebar name, or null if there is no
 * general sidebar active. The active general sidebar is a unique name to
 * identify either an editor or plugin sidebar.
 *
 * Examples:
 *
 *  - `edit-post/document`
 *  - `my-plugin/insert-image-sidebar`
 *
 * @param {Object} state Global application state.
 *
 * @return {?string} Active general sidebar name.
 */

const getActiveGeneralSidebarName = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  return select(build_module["i" /* store */]).getActiveComplementaryArea('core/edit-post');
});
/**
 * Returns the preferences (these preferences are persisted locally).
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} Preferences Object.
 */

function getPreferences(state) {
  return state.preferences;
}
/**
 *
 * @param {Object} state         Global application state.
 * @param {string} preferenceKey Preference Key.
 * @param {*}      defaultValue  Default Value.
 *
 * @return {*} Preference Value.
 */

function getPreference(state, preferenceKey, defaultValue) {
  const preferences = getPreferences(state);
  const value = preferences[preferenceKey];
  return value === undefined ? defaultValue : value;
}
/**
 * Returns true if the publish sidebar is opened.
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether the publish sidebar is open.
 */

function isPublishSidebarOpened(state) {
  return state.publishSidebarActive;
}
/**
 * Returns true if the given panel was programmatically removed, or false otherwise.
 * All panels are not removed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is removed.
 */

function isEditorPanelRemoved(state, panelName) {
  return Object(external_lodash_["includes"])(state.removedPanels, panelName);
}
/**
 * Returns true if the given panel is enabled, or false otherwise. Panels are
 * enabled by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is enabled.
 */

function isEditorPanelEnabled(state, panelName) {
  const panels = getPreference(state, 'panels');
  return !isEditorPanelRemoved(state, panelName) && Object(external_lodash_["get"])(panels, [panelName, 'enabled'], true);
}
/**
 * Returns true if the given panel is open, or false otherwise. Panels are
 * closed by default.
 *
 * @param {Object} state     Global application state.
 * @param {string} panelName A string that identifies the panel.
 *
 * @return {boolean} Whether or not the panel is open.
 */

function isEditorPanelOpened(state, panelName) {
  const panels = getPreference(state, 'panels');
  return Object(external_lodash_["get"])(panels, [panelName]) === true || Object(external_lodash_["get"])(panels, [panelName, 'opened']) === true;
}
/**
 * Returns true if a modal is active, or false otherwise.
 *
 * @param {Object} state     Global application state.
 * @param {string} modalName A string that uniquely identifies the modal.
 *
 * @return {boolean} Whether the modal is active.
 */

function isModalActive(state, modalName) {
  return state.activeModal === modalName;
}
/**
 * Returns whether the given feature is enabled or not.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */

const isFeatureActive = Object(external_wp_data_["createRegistrySelector"])(select => (state, feature) => {
  return select(build_module["i" /* store */]).isFeatureActive('core/edit-post', feature);
});
/**
 * Returns true if the plugin item is pinned to the header.
 * When the value is not set it defaults to true.
 *
 * @param {Object} state      Global application state.
 * @param {string} pluginName Plugin item name.
 *
 * @return {boolean} Whether the plugin item is pinned.
 */

const isPluginItemPinned = Object(external_wp_data_["createRegistrySelector"])(select => (state, pluginName) => {
  return select(build_module["i" /* store */]).isItemPinned('core/edit-post', pluginName);
});
/**
 * Returns an array of active meta box locations.
 *
 * @param {Object} state Post editor state.
 *
 * @return {string[]} Active meta box locations.
 */

const getActiveMetaBoxLocations = Object(rememo["a" /* default */])(state => {
  return Object.keys(state.metaBoxes.locations).filter(location => isMetaBoxLocationActive(state, location));
}, state => [state.metaBoxes.locations]);
/**
 * Returns true if a metabox location is active and visible
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active and visible.
 */

function isMetaBoxLocationVisible(state, location) {
  return isMetaBoxLocationActive(state, location) && Object(external_lodash_["some"])(getMetaBoxesPerLocation(state, location), _ref => {
    let {
      id
    } = _ref;
    return isEditorPanelEnabled(state, `meta-box-${id}`);
  });
}
/**
 * Returns true if there is an active meta box in the given location, or false
 * otherwise.
 *
 * @param {Object} state    Post editor state.
 * @param {string} location Meta box location to test.
 *
 * @return {boolean} Whether the meta box location is active.
 */

function isMetaBoxLocationActive(state, location) {
  const metaBoxes = getMetaBoxesPerLocation(state, location);
  return !!metaBoxes && metaBoxes.length !== 0;
}
/**
 * Returns the list of all the available meta boxes for a given location.
 *
 * @param {Object} state    Global application state.
 * @param {string} location Meta box location to test.
 *
 * @return {?Array} List of meta boxes.
 */

function getMetaBoxesPerLocation(state, location) {
  return state.metaBoxes.locations[location];
}
/**
 * Returns the list of all the available meta boxes.
 *
 * @param {Object} state Global application state.
 *
 * @return {Array} List of meta boxes.
 */

const getAllMetaBoxes = Object(rememo["a" /* default */])(state => {
  return Object(external_lodash_["flatten"])(Object(external_lodash_["values"])(state.metaBoxes.locations));
}, state => [state.metaBoxes.locations]);
/**
 * Returns true if the post is using Meta Boxes
 *
 * @param {Object} state Global application state
 *
 * @return {boolean} Whether there are metaboxes or not.
 */

function selectors_hasMetaBoxes(state) {
  return getActiveMetaBoxLocations(state).length > 0;
}
/**
 * Returns true if the Meta Boxes are being saved.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the metaboxes are being saved.
 */

function selectors_isSavingMetaBoxes(state) {
  return state.metaBoxes.isSaving;
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
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the inserter is opened.
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
 * Returns true if the list view is opened.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether the list view is opened.
 */

function isListViewOpened(state) {
  return state.listViewPanel;
}
/**
 * Returns true if the template editing mode is enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether we're editing the template.
 */

function selectors_isEditingTemplate(state) {
  return state.isEditingTemplate;
}
/**
 * Returns true if meta boxes are initialized.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether meta boxes are initialized.
 */

function areMetaBoxesInitialized(state) {
  return state.metaBoxes.initialized;
}
/**
 * Retrieves the template of the currently edited post.
 *
 * @return {Object?} Post Template.
 */

const getEditedPostTemplate = Object(external_wp_data_["createRegistrySelector"])(select => () => {
  const currentTemplate = select(external_wp_editor_["store"]).getEditedPostAttribute('template');

  if (currentTemplate) {
    var _select$getEntityReco;

    const templateWithSameSlug = (_select$getEntityReco = select(external_wp_coreData_["store"]).getEntityRecords('postType', 'wp_template')) === null || _select$getEntityReco === void 0 ? void 0 : _select$getEntityReco.find(template => template.slug === currentTemplate);

    if (!templateWithSameSlug) {
      return templateWithSameSlug;
    }

    return select(external_wp_coreData_["store"]).getEditedEntityRecord('postType', 'wp_template', templateWithSameSlug.id);
  }

  const post = select(external_wp_editor_["store"]).getCurrentPost();

  if (post.link) {
    return select(external_wp_coreData_["store"]).__experimentalGetTemplateForLink(post.link);
  }

  return null;
});

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
var constants = __webpack_require__("0dt7");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





const storeConfig = {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  controls: external_wp_dataControls_["controls"],
  persist: ['preferences']
};
/**
 * Store definition for the edit post namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(constants["a" /* STORE_NAME */], storeConfig); // Ideally we use register instead of register store.

Object(external_wp_data_["registerStore"])(constants["a" /* STORE_NAME */], storeConfig);


/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

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
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
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

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "TvNi":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["plugins"]; }());

/***/ }),

/***/ "U60i":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "b", function() { return /* reexport */ complementary_area; });
__webpack_require__.d(__webpack_exports__, "c", function() { return /* reexport */ ComplementaryAreaMoreMenuItem; });
__webpack_require__.d(__webpack_exports__, "d", function() { return /* reexport */ fullscreen_mode; });
__webpack_require__.d(__webpack_exports__, "e", function() { return /* reexport */ interface_skeleton; });
__webpack_require__.d(__webpack_exports__, "h", function() { return /* reexport */ pinned_items; });
__webpack_require__.d(__webpack_exports__, "f", function() { return /* reexport */ MoreMenuDropdown; });
__webpack_require__.d(__webpack_exports__, "g", function() { return /* reexport */ MoreMenuFeatureToggle; });
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ action_item; });
__webpack_require__.d(__webpack_exports__, "i", function() { return /* reexport */ store; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "enableComplementaryArea", function() { return actions_enableComplementaryArea; });
__webpack_require__.d(actions_namespaceObject, "disableComplementaryArea", function() { return actions_disableComplementaryArea; });
__webpack_require__.d(actions_namespaceObject, "pinItem", function() { return actions_pinItem; });
__webpack_require__.d(actions_namespaceObject, "unpinItem", function() { return actions_unpinItem; });
__webpack_require__.d(actions_namespaceObject, "toggleFeature", function() { return actions_toggleFeature; });
__webpack_require__.d(actions_namespaceObject, "setFeatureValue", function() { return setFeatureValue; });
__webpack_require__.d(actions_namespaceObject, "setFeatureDefaults", function() { return setFeatureDefaults; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/interface/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getActiveComplementaryArea", function() { return selectors_getActiveComplementaryArea; });
__webpack_require__.d(selectors_namespaceObject, "isItemPinned", function() { return selectors_isItemPinned; });
__webpack_require__.d(selectors_namespaceObject, "isFeatureActive", function() { return isFeatureActive; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
var check = __webpack_require__("RMJe");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-filled.js
var star_filled = __webpack_require__("URob");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/star-empty.js
var star_empty = __webpack_require__("Xxwi");

// EXTERNAL MODULE: external ["wp","viewport"]
var external_wp_viewport_ = __webpack_require__("KEfo");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js
var close_small = __webpack_require__("bWcr");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer to keep tract of the active area per scope.
 *
 * @param {boolean} state           Previous state.
 * @param {Object}  action          Action object.
 * @param {string}  action.type     Action type.
 * @param {string}  action.itemType Type of item.
 * @param {string}  action.scope    Item scope.
 * @param {string}  action.item     Item name.
 *
 * @return {Object} Updated state.
 */

function singleEnableItems() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let {
    type,
    itemType,
    scope,
    item
  } = arguments.length > 1 ? arguments[1] : undefined;

  if (type !== 'SET_SINGLE_ENABLE_ITEM' || !itemType || !scope) {
    return state;
  }

  return { ...state,
    [itemType]: { ...state[itemType],
      [scope]: item || null
    }
  };
}
/**
 * Reducer keeping track of the "pinned" items per scope.
 *
 * @param {boolean} state           Previous state.
 * @param {Object}  action          Action object.
 * @param {string}  action.type     Action type.
 * @param {string}  action.itemType Type of item.
 * @param {string}  action.scope    Item scope.
 * @param {string}  action.item     Item name.
 * @param {boolean} action.isEnable Whether the item is pinned.
 *
 * @return {Object} Updated state.
 */

function multipleEnableItems() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let {
    type,
    itemType,
    scope,
    item,
    isEnable
  } = arguments.length > 1 ? arguments[1] : undefined;

  if (type !== 'SET_MULTIPLE_ENABLE_ITEM' || !itemType || !scope || !item || Object(external_lodash_["get"])(state, [itemType, scope, item]) === isEnable) {
    return state;
  }

  const currentTypeState = state[itemType] || {};
  const currentScopeState = currentTypeState[scope] || {};
  return { ...state,
    [itemType]: { ...currentTypeState,
      [scope]: { ...currentScopeState,
        [item]: isEnable || false
      }
    }
  };
}
/**
 * Reducer returning the defaults for user preferences.
 *
 * This is kept intentionally separate from the preferences
 * themselves so that defaults are not persisted.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const preferenceDefaults = Object(external_wp_data_["combineReducers"])({
  features() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    let action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SET_FEATURE_DEFAULTS') {
      const {
        scope,
        defaults
      } = action;
      return { ...state,
        [scope]: { ...state[scope],
          ...defaults
        }
      };
    }

    return state;
  }

});
/**
 * Reducer returning the user preferences.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const preferences = Object(external_wp_data_["combineReducers"])({
  features() {
    let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
    let action = arguments.length > 1 ? arguments[1] : undefined;

    if (action.type === 'SET_FEATURE_VALUE') {
      const {
        scope,
        featureName,
        value
      } = action;
      return { ...state,
        [scope]: { ...state[scope],
          [featureName]: value
        }
      };
    }

    return state;
  }

});
const enableItems = Object(external_wp_data_["combineReducers"])({
  singleEnableItems,
  multipleEnableItems
});
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  enableItems,
  preferenceDefaults,
  preferences
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/actions.js
/**
 * Returns an action object used in signalling that an active area should be changed.
 *
 * @param {string} itemType Type of item.
 * @param {string} scope    Item scope.
 * @param {string} item     Item identifier.
 *
 * @return {Object} Action object.
 */
function setSingleEnableItem(itemType, scope, item) {
  return {
    type: 'SET_SINGLE_ENABLE_ITEM',
    itemType,
    scope,
    item
  };
}
/**
 * Returns an action object used in signalling that a complementary item should be enabled.
 *
 * @param {string} scope Complementary area scope.
 * @param {string} area  Area identifier.
 *
 * @return {Object} Action object.
 */


function actions_enableComplementaryArea(scope, area) {
  return setSingleEnableItem('complementaryArea', scope, area);
}
/**
 * Returns an action object used in signalling that the complementary area of a given scope should be disabled.
 *
 * @param {string} scope Complementary area scope.
 *
 * @return {Object} Action object.
 */

function actions_disableComplementaryArea(scope) {
  return setSingleEnableItem('complementaryArea', scope, undefined);
}
/**
 * Returns an action object to make an area enabled/disabled.
 *
 * @param {string}  itemType Type of item.
 * @param {string}  scope    Item scope.
 * @param {string}  item     Item identifier.
 * @param {boolean} isEnable Boolean indicating if an area should be pinned or not.
 *
 * @return {Object} Action object.
 */

function setMultipleEnableItem(itemType, scope, item, isEnable) {
  return {
    type: 'SET_MULTIPLE_ENABLE_ITEM',
    itemType,
    scope,
    item,
    isEnable
  };
}
/**
 * Returns an action object used in signalling that an item should be pinned.
 *
 * @param {string} scope  Item scope.
 * @param {string} itemId Item identifier.
 *
 * @return {Object} Action object.
 */


function actions_pinItem(scope, itemId) {
  return setMultipleEnableItem('pinnedItems', scope, itemId, true);
}
/**
 * Returns an action object used in signalling that an item should be unpinned.
 *
 * @param {string} scope  Item scope.
 * @param {string} itemId Item identifier.
 *
 * @return {Object} Action object.
 */

function actions_unpinItem(scope, itemId) {
  return setMultipleEnableItem('pinnedItems', scope, itemId, false);
}
/**
 * Returns an action object used in signalling that a feature should be toggled.
 *
 * @param {string} scope       The feature scope (e.g. core/edit-post).
 * @param {string} featureName The feature name.
 */

function actions_toggleFeature(scope, featureName) {
  return function (_ref) {
    let {
      select,
      dispatch
    } = _ref;
    const currentValue = select.isFeatureActive(scope, featureName);
    dispatch.setFeatureValue(scope, featureName, !currentValue);
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
  return {
    type: 'SET_FEATURE_VALUE',
    scope,
    featureName,
    value: !!value
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
  return {
    type: 'SET_FEATURE_DEFAULTS',
    scope,
    defaults
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * Returns the item that is enabled in a given scope.
 *
 * @param {Object} state    Global application state.
 * @param {string} itemType Type of item.
 * @param {string} scope    Item scope.
 *
 * @return {?string|null} The item that is enabled in the passed scope and type.
 */

function getSingleEnableItem(state, itemType, scope) {
  return Object(external_lodash_["get"])(state.enableItems.singleEnableItems, [itemType, scope]);
}
/**
 * Returns the complementary area that is active in a given scope.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Item scope.
 *
 * @return {string} The complementary area that is active in the given scope.
 */


function selectors_getActiveComplementaryArea(state, scope) {
  return getSingleEnableItem(state, 'complementaryArea', scope);
}
/**
 * Returns a boolean indicating if an item is enabled or not in a given scope.
 *
 * @param {Object} state    Global application state.
 * @param {string} itemType Type of item.
 * @param {string} scope    Scope.
 * @param {string} item     Item to check.
 *
 * @return {boolean|undefined} True if the item is enabled, false otherwise if the item is explicitly disabled, and undefined if there is no information for that item.
 */

function isMultipleEnabledItemEnabled(state, itemType, scope, item) {
  return Object(external_lodash_["get"])(state.enableItems.multipleEnableItems, [itemType, scope, item]);
}
/**
 * Returns a boolean indicating if an item is pinned or not.
 *
 * @param {Object} state Global application state.
 * @param {string} scope Scope.
 * @param {string} item  Item to check.
 *
 * @return {boolean} True if the item is pinned and false otherwise.
 */


function selectors_isItemPinned(state, scope, item) {
  return isMultipleEnabledItemEnabled(state, 'pinnedItems', scope, item) !== false;
}
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

function isFeatureActive(state, scope, featureName) {
  var _state$preferences$fe, _state$preferenceDefa;

  const featureValue = (_state$preferences$fe = state.preferences.features[scope]) === null || _state$preferences$fe === void 0 ? void 0 : _state$preferences$fe[featureName];
  const defaultedFeatureValue = featureValue !== undefined ? featureValue : (_state$preferenceDefa = state.preferenceDefaults.features[scope]) === null || _state$preferenceDefa === void 0 ? void 0 : _state$preferenceDefa[featureName];
  return !!defaultedFeatureValue;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/interface';

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/store/index.js
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

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['enableItems', 'preferences'],
  __experimentalUseThunks: true
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_wp_data_["registerStore"])(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['enableItems', 'preferences'],
  __experimentalUseThunks: true
});

// EXTERNAL MODULE: external ["wp","plugins"]
var external_wp_plugins_ = __webpack_require__("TvNi");

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-context/index.js
/**
 * WordPress dependencies
 */

/* harmony default export */ var complementary_area_context = (Object(external_wp_plugins_["withPluginContext"])((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    identifier: ownProps.identifier || `${context.name}/${ownProps.name}`
  };
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-toggle/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function ComplementaryAreaToggle(_ref) {
  let {
    as = external_wp_components_["Button"],
    scope,
    identifier,
    icon,
    selectedIcon,
    ...props
  } = _ref;
  const ComponentToUse = as;
  const isSelected = Object(external_wp_data_["useSelect"])(select => select(store).getActiveComplementaryArea(scope) === identifier, [identifier]);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = Object(external_wp_data_["useDispatch"])(store);
  return Object(external_wp_element_["createElement"])(ComponentToUse, Object(esm_extends["a" /* default */])({
    icon: selectedIcon && isSelected ? selectedIcon : icon,
    onClick: () => {
      if (isSelected) {
        disableComplementaryArea(scope);
      } else {
        enableComplementaryArea(scope, identifier);
      }
    }
  }, Object(external_lodash_["omit"])(props, ['name'])));
}

/* harmony default export */ var complementary_area_toggle = (complementary_area_context(ComplementaryAreaToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-header/index.js



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
  const toggleButton = Object(external_wp_element_["createElement"])(complementary_area_toggle, Object(esm_extends["a" /* default */])({
    icon: close_small["a" /* default */]
  }, toggleButtonProps));
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
    className: "components-panel__header interface-complementary-area-header__small"
  }, smallScreenTitle && Object(external_wp_element_["createElement"])("span", {
    className: "interface-complementary-area-header__small-title"
  }, smallScreenTitle), toggleButton), Object(external_wp_element_["createElement"])("div", {
    className: classnames_default()('components-panel__header', 'interface-complementary-area-header', className),
    tabIndex: -1
  }, children, toggleButton));
};

/* harmony default export */ var complementary_area_header = (ComplementaryAreaHeader);

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/action-item/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function ActionItemSlot(_ref) {
  let {
    name,
    as: Component = external_wp_components_["ButtonGroup"],
    fillProps = {},
    bubblesVirtually,
    ...props
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_components_["Slot"], {
    name: name,
    bubblesVirtually: bubblesVirtually,
    fillProps: fillProps
  }, fills => {
    if (Object(external_lodash_["isEmpty"])(external_wp_element_["Children"].toArray(fills))) {
      return null;
    } // Special handling exists for backward compatibility.
    // It ensures that menu items created by plugin authors aren't
    // duplicated with automatically injected menu items coming
    // from pinnable plugin sidebars.
    // @see https://github.com/WordPress/gutenberg/issues/14457


    const initializedByPlugins = [];
    external_wp_element_["Children"].forEach(fills, _ref2 => {
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
    const children = external_wp_element_["Children"].map(fills, child => {
      if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(child.props.__unstableTarget)) {
        return null;
      }

      return child;
    });
    return Object(external_wp_element_["createElement"])(Component, props, children);
  });
}

function ActionItem(_ref3) {
  let {
    name,
    as: Component = external_wp_components_["Button"],
    onClick,
    ...props
  } = _ref3;
  return Object(external_wp_element_["createElement"])(external_wp_components_["Fill"], {
    name: name
  }, _ref4 => {
    let {
      onClick: fpOnClick
    } = _ref4;
    return Object(external_wp_element_["createElement"])(Component, Object(esm_extends["a" /* default */])({
      onClick: onClick || fpOnClick ? function () {
        (onClick || external_lodash_["noop"])(...arguments);
        (fpOnClick || external_lodash_["noop"])(...arguments);
      } : undefined
    }, props));
  });
}

ActionItem.Slot = ActionItemSlot;
/* harmony default export */ var action_item = (ActionItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area-more-menu-item/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




const PluginsMenuItem = props => // Menu item is marked with unstable prop for backward compatibility.
// They are removed so they don't leak to DOM elements.
// @see https://github.com/WordPress/gutenberg/issues/14457
Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], Object(external_lodash_["omit"])(props, ['__unstableExplicitMenuItem', '__unstableTarget']));

function ComplementaryAreaMoreMenuItem(_ref) {
  let {
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  } = _ref;
  return Object(external_wp_element_["createElement"])(complementary_area_toggle, Object(esm_extends["a" /* default */])({
    as: toggleProps => {
      return Object(external_wp_element_["createElement"])(action_item, Object(esm_extends["a" /* default */])({
        __unstableExplicitMenuItem: __unstableExplicitMenuItem,
        __unstableTarget: `${scope}/${target}`,
        as: PluginsMenuItem,
        name: `${scope}/plugin-more-menu`
      }, toggleProps));
    },
    role: "menuitemcheckbox",
    selectedIcon: check["a" /* default */],
    name: target,
    scope: scope
  }, props));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/pinned-items/index.js



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
  return Object(external_wp_element_["createElement"])(external_wp_components_["Fill"], Object(esm_extends["a" /* default */])({
    name: `PinnedItems/${scope}`
  }, props));
}

function PinnedItemsSlot(_ref2) {
  let {
    scope,
    className,
    ...props
  } = _ref2;
  return Object(external_wp_element_["createElement"])(external_wp_components_["Slot"], Object(esm_extends["a" /* default */])({
    name: `PinnedItems/${scope}`
  }, props), fills => !Object(external_lodash_["isEmpty"])(fills) && Object(external_wp_element_["createElement"])("div", {
    className: classnames_default()(className, 'interface-pinned-items')
  }, fills));
}

PinnedItems.Slot = PinnedItemsSlot;
/* harmony default export */ var pinned_items = (PinnedItems);

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/complementary-area/index.js



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
  return Object(external_wp_element_["createElement"])(external_wp_components_["Slot"], Object(esm_extends["a" /* default */])({
    name: `ComplementaryArea/${scope}`
  }, props));
}

function ComplementaryAreaFill(_ref2) {
  let {
    scope,
    children,
    className
  } = _ref2;
  return Object(external_wp_element_["createElement"])(external_wp_components_["Fill"], {
    name: `ComplementaryArea/${scope}`
  }, Object(external_wp_element_["createElement"])("div", {
    className: className
  }, children));
}

function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
  const previousIsSmall = Object(external_wp_element_["useRef"])(false);
  const shouldOpenWhenNotSmall = Object(external_wp_element_["useRef"])(false);
  const {
    enableComplementaryArea,
    disableComplementaryArea
  } = Object(external_wp_data_["useDispatch"])(store);
  Object(external_wp_element_["useEffect"])(() => {
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
    closeLabel = Object(external_wp_i18n_["__"])('Close plugin'),
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
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getActiveComplementaryArea,
      isItemPinned
    } = select(store);

    const _activeArea = getActiveComplementaryArea(scope);

    return {
      isActive: _activeArea === identifier,
      isPinned: isItemPinned(scope, identifier),
      activeArea: _activeArea,
      isSmall: select(external_wp_viewport_["store"]).isViewportMatch('< medium'),
      isLarge: select(external_wp_viewport_["store"]).isViewportMatch('large')
    };
  }, [identifier, scope]);
  useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall);
  const {
    enableComplementaryArea,
    disableComplementaryArea,
    pinItem,
    unpinItem
  } = Object(external_wp_data_["useDispatch"])(store);
  Object(external_wp_element_["useEffect"])(() => {
    if (isActiveByDefault && activeArea === undefined && !isSmall) {
      enableComplementaryArea(scope, identifier);
    }
  }, [activeArea, isActiveByDefault, scope, identifier, isSmall]);
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, isPinnable && Object(external_wp_element_["createElement"])(pinned_items, {
    scope: scope
  }, isPinned && Object(external_wp_element_["createElement"])(complementary_area_toggle, {
    scope: scope,
    identifier: identifier,
    isPressed: isActive && (!showIconLabels || isLarge),
    "aria-expanded": isActive,
    label: title,
    icon: showIconLabels ? check["a" /* default */] : icon,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  })), name && isPinnable && Object(external_wp_element_["createElement"])(ComplementaryAreaMoreMenuItem, {
    target: name,
    scope: scope,
    icon: icon
  }, title), isActive && Object(external_wp_element_["createElement"])(ComplementaryAreaFill, {
    className: classnames_default()('interface-complementary-area', className),
    scope: scope
  }, Object(external_wp_element_["createElement"])(complementary_area_header, {
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
  }, header || Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("strong", null, title), isPinnable && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "interface-complementary-area__pin-unpin-item",
    icon: isPinned ? star_filled["a" /* default */] : star_empty["a" /* default */],
    label: isPinned ? Object(external_wp_i18n_["__"])('Unpin from toolbar') : Object(external_wp_i18n_["__"])('Pin to toolbar'),
    onClick: () => (isPinned ? unpinItem : pinItem)(scope, identifier),
    isPressed: isPinned,
    "aria-expanded": isPinned
  }))), Object(external_wp_element_["createElement"])(external_wp_components_["Panel"], {
    className: panelClassName
  }, children)));
}

const ComplementaryAreaWrapped = complementary_area_context(ComplementaryArea);
ComplementaryAreaWrapped.Slot = ComplementaryAreaSlot;
/* harmony default export */ var complementary_area = (ComplementaryAreaWrapped);

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/fullscreen-mode/index.js
/**
 * WordPress dependencies
 */


const FullscreenMode = _ref => {
  let {
    isActive
  } = _ref;
  Object(external_wp_element_["useEffect"])(() => {
    let isSticky = false; // `is-fullscreen-mode` is set in PHP as a body class by Gutenberg, and this causes
    // `sticky-menu` to be applied by WordPress and prevents the admin menu being scrolled
    // even if `is-fullscreen-mode` is then removed. Let's remove `sticky-menu` here as
    // a consequence of the FullscreenMode setup

    if (document.body.classList.contains('sticky-menu')) {
      isSticky = true;
      document.body.classList.remove('sticky-menu');
    }

    return () => {
      if (isSticky) {
        document.body.classList.add('sticky-menu');
      }
    };
  }, []);
  Object(external_wp_element_["useEffect"])(() => {
    if (isActive) {
      document.body.classList.add('is-fullscreen-mode');
    } else {
      document.body.classList.remove('is-fullscreen-mode');
    }

    return () => {
      if (isActive) {
        document.body.classList.remove('is-fullscreen-mode');
      }
    };
  }, [isActive]);
  return null;
};

/* harmony default export */ var fullscreen_mode = (FullscreenMode);

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/interface-skeleton/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */

/**
 * WordPress dependencies
 */






function useHTMLClass(className) {
  Object(external_wp_element_["useEffect"])(() => {
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
    footer,
    header,
    sidebar,
    secondarySidebar,
    notices,
    content,
    drawer,
    actions,
    labels,
    className,
    shortcuts
  } = _ref;
  const navigateRegionsProps = Object(external_wp_components_["__unstableUseNavigateRegions"])(shortcuts);
  useHTMLClass('interface-interface-skeleton__html-container');
  const defaultLabels = {
    /* translators: accessibility text for the nav bar landmark region. */
    drawer: Object(external_wp_i18n_["__"])('Drawer'),

    /* translators: accessibility text for the top bar landmark region. */
    header: Object(external_wp_i18n_["__"])('Header'),

    /* translators: accessibility text for the content landmark region. */
    body: Object(external_wp_i18n_["__"])('Content'),

    /* translators: accessibility text for the secondary sidebar landmark region. */
    secondarySidebar: Object(external_wp_i18n_["__"])('Block Library'),

    /* translators: accessibility text for the settings landmark region. */
    sidebar: Object(external_wp_i18n_["__"])('Settings'),

    /* translators: accessibility text for the publish landmark region. */
    actions: Object(external_wp_i18n_["__"])('Publish'),

    /* translators: accessibility text for the footer landmark region. */
    footer: Object(external_wp_i18n_["__"])('Footer')
  };
  const mergedLabels = { ...defaultLabels,
    ...labels
  };
  return Object(external_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({}, navigateRegionsProps, {
    ref: Object(external_wp_compose_["useMergeRefs"])([ref, navigateRegionsProps.ref]),
    className: classnames_default()(className, 'interface-interface-skeleton', navigateRegionsProps.className, !!footer && 'has-footer')
  }), !!drawer && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__drawer",
    role: "region",
    "aria-label": mergedLabels.drawer,
    tabIndex: "-1"
  }, drawer), Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__editor"
  }, !!header && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__header",
    role: "region",
    "aria-label": mergedLabels.header,
    tabIndex: "-1"
  }, header), Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__body"
  }, !!secondarySidebar && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__secondary-sidebar",
    role: "region",
    "aria-label": mergedLabels.secondarySidebar,
    tabIndex: "-1"
  }, secondarySidebar), !!notices && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__notices"
  }, notices), Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__content",
    role: "region",
    "aria-label": mergedLabels.body,
    tabIndex: "-1"
  }, content), !!sidebar && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__sidebar",
    role: "region",
    "aria-label": mergedLabels.sidebar,
    tabIndex: "-1"
  }, sidebar), !!actions && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__actions",
    role: "region",
    "aria-label": mergedLabels.actions,
    tabIndex: "-1"
  }, actions))), !!footer && Object(external_wp_element_["createElement"])("div", {
    className: "interface-interface-skeleton__footer",
    role: "region",
    "aria-label": mergedLabels.footer,
    tabIndex: "-1"
  }, footer));
}

/* harmony default export */ var interface_skeleton = (Object(external_wp_element_["forwardRef"])(InterfaceSkeleton));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js
var more_vertical = __webpack_require__("VKE3");

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-dropdown/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function MoreMenuDropdown(_ref) {
  let {
    as: DropdownComponent = external_wp_components_["DropdownMenu"],
    className,

    /* translators: button label text should, if possible, be under 16 characters. */
    label = Object(external_wp_i18n_["__"])('Options'),
    popoverProps,
    toggleProps,
    children
  } = _ref;
  return Object(external_wp_element_["createElement"])(DropdownComponent, {
    className: classnames_default()('interface-more-menu-dropdown', className),
    icon: more_vertical["a" /* default */],
    label: label,
    popoverProps: {
      position: 'bottom left',
      ...popoverProps,
      className: classnames_default()('interface-more-menu-dropdown__content', popoverProps === null || popoverProps === void 0 ? void 0 : popoverProps.className)
    },
    toggleProps: {
      tooltipPosition: 'bottom',
      ...toggleProps
    }
  }, onClose => children(onClose));
}

// EXTERNAL MODULE: external ["wp","a11y"]
var external_wp_a11y_ = __webpack_require__("gdqT");

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/more-menu-feature-toggle/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function MoreMenuFeatureToggle(_ref) {
  let {
    scope,
    label,
    info,
    messageActivated,
    messageDeactivated,
    shortcut,
    feature
  } = _ref;
  const isActive = Object(external_wp_data_["useSelect"])(select => select(store).isFeatureActive(scope, feature), [feature]);
  const {
    toggleFeature
  } = Object(external_wp_data_["useDispatch"])(store);

  const speakMessage = () => {
    if (isActive) {
      Object(external_wp_a11y_["speak"])(messageDeactivated || Object(external_wp_i18n_["__"])('Feature deactivated'));
    } else {
      Object(external_wp_a11y_["speak"])(messageActivated || Object(external_wp_i18n_["__"])('Feature activated'));
    }
  };

  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    icon: isActive && check["a" /* default */],
    isSelected: isActive,
    onClick: () => {
      toggleFeature(scope, feature);
      speakMessage();
    },
    role: "menuitemcheckbox",
    info: info,
    shortcut: shortcut
  }, label);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/components/index.js









// CONCATENATED MODULE: ./node_modules/@wordpress/interface/build-module/index.js




/***/ }),

/***/ "URob":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starFilled = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z"
}));
/* harmony default export */ __webpack_exports__["a"] = (starFilled);


/***/ }),

/***/ "VKE3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const moreVertical = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreVertical);


/***/ }),

/***/ "Xxwi":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const starEmpty = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  fillRule: "evenodd",
  d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
  clipRule: "evenodd"
}));
/* harmony default export */ __webpack_exports__["a"] = (starEmpty);


/***/ }),

/***/ "Y5n4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* reexport */ enable_custom_fields; });
__webpack_require__.d(__webpack_exports__, "c", function() { return /* reexport */ enable_panel; });
__webpack_require__.d(__webpack_exports__, "d", function() { return /* reexport */ enable_plugin_document_setting_panel; });
__webpack_require__.d(__webpack_exports__, "e", function() { return /* reexport */ enable_publish_sidebar; });
__webpack_require__.d(__webpack_exports__, "b", function() { return /* reexport */ enable_feature; });

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","editor"]
var external_wp_editor_ = __webpack_require__("jSdM");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/base.js


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
  return Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-preferences-modal__option"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["ToggleControl"], {
    help: help,
    label: label,
    checked: isChecked,
    onChange: onChange
  }), children);
}

/* harmony default export */ var base = (BaseOption);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-custom-fields.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function CustomFieldsConfirmation(_ref) {
  let {
    willEnable
  } = _ref;
  const [isReloading, setIsReloading] = Object(external_wp_element_["useState"])(false);
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("p", {
    className: "edit-post-preferences-modal__custom-fields-confirmation-message"
  }, Object(external_wp_i18n_["__"])('A page reload is required for this change. Make sure your content is saved before reloading.')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "edit-post-preferences-modal__custom-fields-confirmation-button",
    variant: "secondary",
    isBusy: isReloading,
    disabled: isReloading,
    onClick: () => {
      setIsReloading(true);
      document.getElementById('toggle-custom-fields-form').submit();
    }
  }, willEnable ? Object(external_wp_i18n_["__"])('Enable & Reload') : Object(external_wp_i18n_["__"])('Disable & Reload')));
}
function EnableCustomFieldsOption(_ref2) {
  let {
    label,
    areCustomFieldsEnabled
  } = _ref2;
  const [isChecked, setIsChecked] = Object(external_wp_element_["useState"])(areCustomFieldsEnabled);
  return Object(external_wp_element_["createElement"])(base, {
    label: label,
    isChecked: isChecked,
    onChange: setIsChecked
  }, isChecked !== areCustomFieldsEnabled && Object(external_wp_element_["createElement"])(CustomFieldsConfirmation, {
    willEnable: isChecked
  }));
}
/* harmony default export */ var enable_custom_fields = (Object(external_wp_data_["withSelect"])(select => ({
  areCustomFieldsEnabled: !!select(external_wp_editor_["store"]).getEditorSettings().enableCustomFields
}))(EnableCustomFieldsOption));

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js + 5 modules
var store = __webpack_require__("T40v");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-panel.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/* harmony default export */ var enable_panel = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])((select, _ref) => {
  let {
    panelName
  } = _ref;
  const {
    isEditorPanelEnabled,
    isEditorPanelRemoved
  } = select(store["a" /* store */]);
  return {
    isRemoved: isEditorPanelRemoved(panelName),
    isChecked: isEditorPanelEnabled(panelName)
  };
}), Object(external_wp_compose_["ifCondition"])(_ref2 => {
  let {
    isRemoved
  } = _ref2;
  return !isRemoved;
}), Object(external_wp_data_["withDispatch"])((dispatch, _ref3) => {
  let {
    panelName
  } = _ref3;
  return {
    onChange: () => dispatch(store["a" /* store */]).toggleEditorPanelEnabled(panelName)
  };
}))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-plugin-document-setting-panel.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const {
  Fill,
  Slot
} = Object(external_wp_components_["createSlotFill"])('EnablePluginDocumentSettingPanelOption');

const EnablePluginDocumentSettingPanelOption = _ref => {
  let {
    label,
    panelName
  } = _ref;
  return Object(external_wp_element_["createElement"])(Fill, null, Object(external_wp_element_["createElement"])(enable_panel, {
    label: label,
    panelName: panelName
  }));
};

EnablePluginDocumentSettingPanelOption.Slot = Slot;
/* harmony default export */ var enable_plugin_document_setting_panel = (EnablePluginDocumentSettingPanelOption);

// EXTERNAL MODULE: external ["wp","viewport"]
var external_wp_viewport_ = __webpack_require__("KEfo");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-publish-sidebar.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/* harmony default export */ var enable_publish_sidebar = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => ({
  isChecked: select(external_wp_editor_["store"]).isPublishSidebarEnabled()
})), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    enablePublishSidebar,
    disablePublishSidebar
  } = dispatch(external_wp_editor_["store"]);
  return {
    onChange: isEnabled => isEnabled ? enablePublishSidebar() : disablePublishSidebar()
  };
}), // In < medium viewports we override this option and always show the publish sidebar.
// See the edit-post's header component for the specific logic.
Object(external_wp_viewport_["ifViewportMatches"])('medium'))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/enable-feature.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/* harmony default export */ var enable_feature = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])((select, _ref) => {
  let {
    featureName
  } = _ref;
  const {
    isFeatureActive
  } = select(store["a" /* store */]);
  return {
    isChecked: isFeatureActive(featureName)
  };
}), Object(external_wp_data_["withDispatch"])((dispatch, _ref2) => {
  let {
    featureName
  } = _ref2;
  return {
    onChange: () => dispatch(store["a" /* store */]).toggleFeature(featureName)
  };
}))(base));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/index.js







/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "Z23Y":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["warning"]; }());

/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "bWcr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const closeSmall = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ __webpack_exports__["a"] = (closeSmall);


/***/ }),

/***/ "cjQ8":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const arrowLeft = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 10.8H6.7l4.1-4.5-1.1-1.1-5.8 6.3 5.8 5.8 1.1-1.1-4-3.9H20z"
}));
/* harmony default export */ __webpack_exports__["a"] = (arrowLeft);


/***/ }),

/***/ "dSQ2":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "reinitializeEditor", function() { return /* binding */ reinitializeEditor; });
__webpack_require__.d(__webpack_exports__, "initializeEditor", function() { return /* binding */ initializeEditor; });
__webpack_require__.d(__webpack_exports__, "PluginBlockSettingsMenuItem", function() { return /* reexport */ plugin_block_settings_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginDocumentSettingPanel", function() { return /* reexport */ plugin_document_setting_panel["a" /* default */]; });
__webpack_require__.d(__webpack_exports__, "PluginMoreMenuItem", function() { return /* reexport */ plugin_more_menu_item; });
__webpack_require__.d(__webpack_exports__, "PluginPostPublishPanel", function() { return /* reexport */ plugin_post_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginPostStatusInfo", function() { return /* reexport */ plugin_post_status_info; });
__webpack_require__.d(__webpack_exports__, "PluginPrePublishPanel", function() { return /* reexport */ plugin_pre_publish_panel; });
__webpack_require__.d(__webpack_exports__, "PluginSidebar", function() { return /* reexport */ PluginSidebarEditPost; });
__webpack_require__.d(__webpack_exports__, "PluginSidebarMoreMenuItem", function() { return /* reexport */ PluginSidebarMoreMenuItem; });
__webpack_require__.d(__webpack_exports__, "__experimentalFullscreenModeClose", function() { return /* reexport */ fullscreen_mode_close; });
__webpack_require__.d(__webpack_exports__, "__experimentalMainDashboardButton", function() { return /* reexport */ main_dashboard_button; });
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store["a" /* store */]; });

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","blockLibrary"]
var external_wp_blockLibrary_ = __webpack_require__("QyPg");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: ./node_modules/@wordpress/interface/build-module/index.js + 17 modules
var build_module = __webpack_require__("U60i");

// EXTERNAL MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_ = __webpack_require__("6aBm");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/components/index.js
/**
 * WordPress dependencies
 */



const replaceMediaUpload = () => external_wp_mediaUtils_["MediaUpload"];

Object(external_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-post/replace-media-upload', replaceMediaUpload);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/validate-multiple-use/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








const enhance = Object(external_wp_compose_["compose"])(
/**
 * For blocks whose block type doesn't support `multiple`, provides the
 * wrapped component with `originalBlockClientId` -- a reference to the
 * first block of the same type in the content -- if and only if that
 * "original" block is not the current one. Thus, an inexisting
 * `originalBlockClientId` prop signals that the block is valid.
 *
 * @param {WPComponent} WrappedBlockEdit A filtered BlockEdit instance.
 *
 * @return {WPComponent} Enhanced component with merged state data props.
 */
Object(external_wp_data_["withSelect"])((select, block) => {
  const multiple = Object(external_wp_blocks_["hasBlockSupport"])(block.name, 'multiple', true); // For block types with `multiple` support, there is no "original
  // block" to be found in the content, as the block itself is valid.

  if (multiple) {
    return {};
  } // Otherwise, only pass `originalBlockClientId` if it refers to a different
  // block from the current one.


  const blocks = select(external_wp_blockEditor_["store"]).getBlocks();
  const firstOfSameType = Object(external_lodash_["find"])(blocks, _ref => {
    let {
      name
    } = _ref;
    return block.name === name;
  });
  const isInvalid = firstOfSameType && firstOfSameType.clientId !== block.clientId;
  return {
    originalBlockClientId: isInvalid && firstOfSameType.clientId
  };
}), Object(external_wp_data_["withDispatch"])((dispatch, _ref2) => {
  let {
    originalBlockClientId
  } = _ref2;
  return {
    selectFirst: () => dispatch(external_wp_blockEditor_["store"]).selectBlock(originalBlockClientId)
  };
}));
const withMultipleValidation = Object(external_wp_compose_["createHigherOrderComponent"])(BlockEdit => {
  return enhance(_ref3 => {
    let {
      originalBlockClientId,
      selectFirst,
      ...props
    } = _ref3;

    if (!originalBlockClientId) {
      return Object(external_wp_element_["createElement"])(BlockEdit, props);
    }

    const blockType = Object(external_wp_blocks_["getBlockType"])(props.name);
    const outboundType = getOutboundType(props.name);
    return [Object(external_wp_element_["createElement"])("div", {
      key: "invalid-preview",
      style: {
        minHeight: '60px'
      }
    }, Object(external_wp_element_["createElement"])(BlockEdit, Object(esm_extends["a" /* default */])({
      key: "block-edit"
    }, props))), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["Warning"], {
      key: "multiple-use-warning",
      actions: [Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        key: "find-original",
        variant: "secondary",
        onClick: selectFirst
      }, Object(external_wp_i18n_["__"])('Find original')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        key: "remove",
        variant: "secondary",
        onClick: () => props.onReplace([])
      }, Object(external_wp_i18n_["__"])('Remove')), outboundType && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        key: "transform",
        variant: "secondary",
        onClick: () => props.onReplace(Object(external_wp_blocks_["createBlock"])(outboundType.name, props.attributes))
      }, Object(external_wp_i18n_["__"])('Transform into:'), " ", outboundType.title)]
    }, Object(external_wp_element_["createElement"])("strong", null, blockType === null || blockType === void 0 ? void 0 : blockType.title, ": "), Object(external_wp_i18n_["__"])('This block can only be used once.'))];
  });
}, 'withMultipleValidation');
/**
 * Given a base block name, returns the default block type to which to offer
 * transforms.
 *
 * @param {string} blockName Base block name.
 *
 * @return {?Object} The chosen default block type.
 */

function getOutboundType(blockName) {
  // Grab the first outbound transform
  const transform = Object(external_wp_blocks_["findTransform"])(Object(external_wp_blocks_["getBlockTransforms"])('to', blockName), _ref4 => {
    let {
      type,
      blocks
    } = _ref4;
    return type === 'block' && blocks.length === 1;
  } // What about when .length > 1?
  );

  if (!transform) {
    return null;
  }

  return Object(external_wp_blocks_["getBlockType"])(transform.blocks[0]);
}

Object(external_wp_hooks_["addFilter"])('editor.BlockEdit', 'core/edit-post/validate-multiple-use/with-multiple-validation', withMultipleValidation);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/hooks/index.js
/**
 * Internal dependencies
 */



// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js
var external = __webpack_require__("K+tz");

// EXTERNAL MODULE: external ["wp","plugins"]
var external_wp_plugins_ = __webpack_require__("TvNi");

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__("Mmq9");

// EXTERNAL MODULE: external ["wp","notices"]
var external_wp_notices_ = __webpack_require__("onLe");

// EXTERNAL MODULE: external ["wp","editor"]
var external_wp_editor_ = __webpack_require__("jSdM");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/copy-content-menu-item/index.js


/**
 * WordPress dependencies
 */






function CopyContentMenuItem() {
  const {
    createNotice
  } = Object(external_wp_data_["useDispatch"])(external_wp_notices_["store"]);
  const getText = Object(external_wp_data_["useSelect"])(select => () => select(external_wp_editor_["store"]).getEditedPostAttribute('content'), []);

  function onSuccess() {
    createNotice('info', Object(external_wp_i18n_["__"])('All content copied.'), {
      isDismissible: true,
      type: 'snackbar'
    });
  }

  const ref = Object(external_wp_compose_["useCopyToClipboard"])(getText, onSuccess);
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    ref: ref
  }, Object(external_wp_i18n_["__"])('Copy all content'));
}

// EXTERNAL MODULE: external ["wp","keycodes"]
var external_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/store/index.js + 5 modules
var store = __webpack_require__("T40v");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/keyboard-shortcuts-help-menu-item/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function KeyboardShortcutsHelpMenuItem(_ref) {
  let {
    openModal
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    onClick: () => {
      openModal('edit-post/keyboard-shortcut-help');
    },
    shortcut: external_wp_keycodes_["displayShortcut"].access('h')
  }, Object(external_wp_i18n_["__"])('Keyboard shortcuts'));
}
/* harmony default export */ var keyboard_shortcuts_help_menu_item = (Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    openModal
  } = dispatch(store["a" /* store */]);
  return {
    openModal
  };
})(KeyboardShortcutsHelpMenuItem));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/tools-more-menu-group/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



const {
  Fill: ToolsMoreMenuGroup,
  Slot
} = Object(external_wp_components_["createSlotFill"])('ToolsMoreMenuGroup');

ToolsMoreMenuGroup.Slot = _ref => {
  let {
    fillProps
  } = _ref;
  return Object(external_wp_element_["createElement"])(Slot, {
    fillProps: fillProps
  }, fills => !Object(external_lodash_["isEmpty"])(fills) && Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["__"])('Tools')
  }, fills));
};

/* harmony default export */ var tools_more_menu_group = (ToolsMoreMenuGroup);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/welcome-guide-menu-item/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function WelcomeGuideMenuItem() {
  const isTemplateMode = Object(external_wp_data_["useSelect"])(select => select(store["a" /* store */]).isEditingTemplate(), []);
  return Object(external_wp_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-post",
    feature: isTemplateMode ? 'welcomeGuideTemplate' : 'welcomeGuide',
    label: Object(external_wp_i18n_["__"])('Welcome Guide')
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/plugins/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





Object(external_wp_plugins_["registerPlugin"])('edit-post', {
  render() {
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(tools_more_menu_group, null, _ref => {
      let {
        onClose
      } = _ref;
      return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
        role: "menuitem",
        href: Object(external_wp_url_["addQueryArgs"])('edit.php', {
          post_type: 'wp_block'
        })
      }, Object(external_wp_i18n_["__"])('Manage Reusable blocks')), Object(external_wp_element_["createElement"])(keyboard_shortcuts_help_menu_item, {
        onSelect: onClose
      }), Object(external_wp_element_["createElement"])(WelcomeGuideMenuItem, null), Object(external_wp_element_["createElement"])(CopyContentMenuItem, null), Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
        role: "menuitem",
        icon: external["a" /* default */],
        href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/'),
        target: "_blank",
        rel: "noopener noreferrer"
      }, Object(external_wp_i18n_["__"])('Help'), Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
        as: "span"
      },
      /* translators: accessibility text */
      Object(external_wp_i18n_["__"])('(opens in a new tab)'))));
    }));
  }

});

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_ = __webpack_require__("hF7m");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/prevent-event-discovery.js
/* harmony default export */ var prevent_event_discovery = ({
  't a l e s o f g u t e n b e r g': event => {
    const {
      ownerDocument
    } = event.target;

    if (!ownerDocument.activeElement.classList.contains('edit-post-visual-editor') && ownerDocument.activeElement !== ownerDocument.body) {
      return;
    }

    event.preventDefault();
    window.wp.data.dispatch('core/block-editor').insertBlock(window.wp.blocks.createBlock('core/paragraph', {
      content: '🐡🐢🦀🐤🦋🐘🐧🐹🦁🦄🦍🐼🐿🎃🐴🐝🐆🦕🦔🌱🍇π🍌🐉💧🥨🌌🍂🍠🥦🥚🥝🎟🥥🥒🛵🥖🍒🍯🎾🎲🐺🐚🐮⌛️'
    }));
  }
});

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/text-editor/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function TextEditor(_ref) {
  let {
    onExit,
    isRichEditingEnabled
  } = _ref;
  return Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor"
  }, isRichEditingEnabled && Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__toolbar"
  }, Object(external_wp_element_["createElement"])("h2", null, Object(external_wp_i18n_["__"])('Editing code')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "tertiary",
    onClick: onExit,
    shortcut: external_wp_keycodes_["displayShortcut"].secondary('m')
  }, Object(external_wp_i18n_["__"])('Exit code editor')), Object(external_wp_element_["createElement"])(external_wp_editor_["TextEditorGlobalKeyboardShortcuts"], null)), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-text-editor__body"
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostTitle"], null), Object(external_wp_element_["createElement"])(external_wp_editor_["PostTextEditor"], null)));
}

/* harmony default export */ var text_editor = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => ({
  isRichEditingEnabled: select(external_wp_editor_["store"]).getEditorSettings().richEditingEnabled
})), Object(external_wp_data_["withDispatch"])(dispatch => {
  return {
    onExit() {
      dispatch(store["a" /* store */]).switchEditorMode('visual');
    }

  };
}))(TextEditor));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/arrow-left.js
var arrow_left = __webpack_require__("cjQ8");

// EXTERNAL MODULE: external ["wp","a11y"]
var external_wp_a11y_ = __webpack_require__("gdqT");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/block-inspector-button.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function BlockInspectorButton(_ref) {
  let {
    onClick = external_lodash_["noop"],
    small = false
  } = _ref;
  const {
    shortcut,
    areAdvancedSettingsOpened
  } = Object(external_wp_data_["useSelect"])(select => ({
    shortcut: select(external_wp_keyboardShortcuts_["store"]).getShortcutRepresentation('core/edit-post/toggle-sidebar'),
    areAdvancedSettingsOpened: select(store["a" /* store */]).getActiveGeneralSidebarName() === 'edit-post/block'
  }), []);
  const {
    openGeneralSidebar,
    closeGeneralSidebar
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const label = areAdvancedSettingsOpened ? Object(external_wp_i18n_["__"])('Hide more settings') : Object(external_wp_i18n_["__"])('Show more settings');
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    onClick: () => {
      if (areAdvancedSettingsOpened) {
        closeGeneralSidebar();
        Object(external_wp_a11y_["speak"])(Object(external_wp_i18n_["__"])('Block settings closed'));
      } else {
        openGeneralSidebar('edit-post/block');
        Object(external_wp_a11y_["speak"])(Object(external_wp_i18n_["__"])('Additional settings are now available in the Editor block settings sidebar'));
      }

      onClick();
    },
    shortcut: shortcut
  }, !small && label);
}
/* harmony default export */ var block_inspector_button = (BlockInspectorButton);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/visual-editor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */




function MaybeIframe(_ref) {
  let {
    children,
    contentRef,
    shouldIframe,
    styles,
    style
  } = _ref;
  const ref = Object(external_wp_blockEditor_["__unstableUseMouseMoveTypingReset"])();

  if (!shouldIframe) {
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableEditorStyles"], {
      styles: styles
    }), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["WritingFlow"], {
      ref: contentRef,
      className: "editor-styles-wrapper",
      style: {
        flex: '1',
        ...style
      },
      tabIndex: -1
    }, children));
  }

  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableIframe"], {
    head: Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableEditorStyles"], {
      styles: styles
    }),
    ref: ref,
    contentRef: contentRef,
    style: {
      width: '100%',
      height: '100%',
      display: 'block'
    },
    name: "editor-canvas"
  }, children);
}

function VisualEditor(_ref2) {
  let {
    styles
  } = _ref2;
  const {
    deviceType,
    isTemplateMode,
    wrapperBlockName,
    wrapperUniqueId
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isEditingTemplate,
      __experimentalGetPreviewDeviceType
    } = select(store["a" /* store */]);
    const {
      getCurrentPostId,
      getCurrentPostType
    } = select(external_wp_editor_["store"]);

    const _isTemplateMode = isEditingTemplate();

    let _wrapperBlockName;

    if (getCurrentPostType() === 'wp_block') {
      _wrapperBlockName = 'core/block';
    } else if (!_isTemplateMode) {
      _wrapperBlockName = 'core/post-content';
    }

    return {
      deviceType: __experimentalGetPreviewDeviceType(),
      isTemplateMode: _isTemplateMode,
      wrapperBlockName: _wrapperBlockName,
      wrapperUniqueId: getCurrentPostId()
    };
  }, []);
  const hasMetaBoxes = Object(external_wp_data_["useSelect"])(select => select(store["a" /* store */]).hasMetaBoxes(), []);
  const themeSupportsLayout = Object(external_wp_data_["useSelect"])(select => {
    const {
      getSettings
    } = select(external_wp_blockEditor_["store"]);
    return getSettings().supportsLayout;
  }, []);
  const {
    clearSelectedBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const {
    setIsEditingTemplate
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const desktopCanvasStyles = {
    // We intentionally omit a 100% height here. The container is a flex item, so the 100% height is granted by default.
    // If a percentage height is present, older browsers such as Safari 13 apply that, but do so incorrectly as the inheritance is buggy.
    width: '100%',
    margin: 0,
    display: 'flex',
    flexFlow: 'column',
    // Default background color so that grey
    // .edit-post-editor-regions__content color doesn't show through.
    background: 'white'
  };
  const templateModeStyles = { ...desktopCanvasStyles,
    borderRadius: '2px 2px 0 0',
    border: '1px solid #ddd',
    borderBottom: 0
  };
  const resizedCanvasStyles = Object(external_wp_blockEditor_["__experimentalUseResizeCanvas"])(deviceType, isTemplateMode);
  const defaultLayout = Object(external_wp_blockEditor_["useSetting"])('layout');
  const previewMode = 'is-' + deviceType.toLowerCase() + '-preview';
  let animatedStyles = isTemplateMode ? templateModeStyles : desktopCanvasStyles;

  if (resizedCanvasStyles) {
    animatedStyles = resizedCanvasStyles;
  }

  let paddingBottom; // Add a constant padding for the typewritter effect. When typing at the
  // bottom, there needs to be room to scroll up.

  if (!hasMetaBoxes && !resizedCanvasStyles && !isTemplateMode) {
    paddingBottom = '40vh';
  }

  const ref = Object(external_wp_element_["useRef"])();
  const contentRef = Object(external_wp_compose_["useMergeRefs"])([ref, Object(external_wp_blockEditor_["__unstableUseClipboardHandler"])(), Object(external_wp_blockEditor_["__unstableUseCanvasClickRedirect"])(), Object(external_wp_blockEditor_["__unstableUseTypewriter"])(), Object(external_wp_blockEditor_["__unstableUseTypingObserver"])(), Object(external_wp_blockEditor_["__unstableUseBlockSelectionClearer"])()]);
  const blockSelectionClearerRef = Object(external_wp_blockEditor_["__unstableUseBlockSelectionClearer"])();
  const [, RecursionProvider] = Object(external_wp_blockEditor_["__experimentalUseNoRecursiveRenders"])(wrapperUniqueId, wrapperBlockName);
  const layout = Object(external_wp_element_["useMemo"])(() => {
    if (isTemplateMode) {
      return {
        type: 'default'
      };
    }

    if (themeSupportsLayout) {
      return defaultLayout;
    }

    return undefined;
  }, [isTemplateMode, themeSupportsLayout, defaultLayout]);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockTools"], {
    __unstableContentRef: ref,
    className: classnames_default()('edit-post-visual-editor', {
      'is-template-mode': isTemplateMode
    })
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["VisualEditorGlobalKeyboardShortcuts"], null), Object(external_wp_element_["createElement"])(external_wp_components_["__unstableMotion"].div, {
    className: "edit-post-visual-editor__content-area",
    animate: {
      padding: isTemplateMode ? '48px 48px 0' : '0'
    },
    ref: blockSelectionClearerRef
  }, isTemplateMode && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "edit-post-visual-editor__exit-template-mode",
    icon: arrow_left["a" /* default */],
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    }
  }, Object(external_wp_i18n_["__"])('Back')), Object(external_wp_element_["createElement"])(external_wp_components_["__unstableMotion"].div, {
    animate: animatedStyles,
    initial: desktopCanvasStyles,
    className: previewMode
  }, Object(external_wp_element_["createElement"])(MaybeIframe, {
    shouldIframe: isTemplateMode || deviceType === 'Tablet' || deviceType === 'Mobile',
    contentRef: contentRef,
    styles: styles,
    style: {
      paddingBottom
    }
  }, themeSupportsLayout && !isTemplateMode && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__experimentalLayoutStyle"], {
    selector: ".edit-post-visual-editor__post-title-wrapper, .block-editor-block-list__layout.is-root-container",
    layout: defaultLayout
  }), !isTemplateMode && Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-visual-editor__post-title-wrapper"
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostTitle"], null)), Object(external_wp_element_["createElement"])(RecursionProvider, null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockList"], {
    className: isTemplateMode ? 'wp-site-blocks' : undefined,
    __experimentalLayout: layout
  }))))), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableBlockSettingsMenuFirstItem"], null, _ref3 => {
    let {
      onClose
    } = _ref3;
    return Object(external_wp_element_["createElement"])(block_inspector_button, {
      onClick: onClose
    });
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function KeyboardShortcuts() {
  const {
    getBlockSelectionStart
  } = Object(external_wp_data_["useSelect"])(external_wp_blockEditor_["store"]);
  const {
    getEditorMode,
    isEditorSidebarOpened,
    isListViewOpened
  } = Object(external_wp_data_["useSelect"])(store["a" /* store */]);
  const isModeToggleDisabled = Object(external_wp_data_["useSelect"])(select => {
    const {
      richEditingEnabled,
      codeEditingEnabled
    } = select(external_wp_editor_["store"]).getEditorSettings();
    return !richEditingEnabled || !codeEditingEnabled;
  }, []);
  const {
    switchEditorMode,
    openGeneralSidebar,
    closeGeneralSidebar,
    toggleFeature,
    setIsListViewOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    registerShortcut
  } = Object(external_wp_data_["useDispatch"])(external_wp_keyboardShortcuts_["store"]);
  Object(external_wp_element_["useEffect"])(() => {
    registerShortcut({
      name: 'core/edit-post/toggle-mode',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Switch between visual editor and code editor.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'm'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-fullscreen',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Toggle fullscreen mode.'),
      keyCombination: {
        modifier: 'secondary',
        character: 'f'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-list-view',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Open the block list view.'),
      keyCombination: {
        modifier: 'access',
        character: 'o'
      }
    });
    registerShortcut({
      name: 'core/edit-post/toggle-sidebar',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Show or hide the settings sidebar.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: ','
      }
    });
    registerShortcut({
      name: 'core/edit-post/next-region',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Navigate to the next part of the editor.'),
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
      name: 'core/edit-post/previous-region',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Navigate to the previous part of the editor.'),
      keyCombination: {
        modifier: 'ctrlShift',
        character: '`'
      },
      aliases: [{
        modifier: 'access',
        character: 'p'
      }]
    });
    registerShortcut({
      name: 'core/edit-post/keyboard-shortcuts',
      category: 'main',
      description: Object(external_wp_i18n_["__"])('Display these keyboard shortcuts.'),
      keyCombination: {
        modifier: 'access',
        character: 'h'
      }
    });
  }, []);
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-mode', () => {
    switchEditorMode(getEditorMode() === 'visual' ? 'text' : 'visual');
  }, {
    isDisabled: isModeToggleDisabled
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-fullscreen', () => {
    toggleFeature('fullscreenMode');
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-sidebar', event => {
    // This shortcut has no known clashes, but use preventDefault to prevent any
    // obscure shortcuts from triggering.
    event.preventDefault();

    if (isEditorSidebarOpened()) {
      closeGeneralSidebar();
    } else {
      const sidebarToOpen = getBlockSelectionStart() ? 'edit-post/block' : 'edit-post/document';
      openGeneralSidebar(sidebarToOpen);
    }
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/toggle-list-view', () => setIsListViewOpened(!isListViewOpened()));
  return null;
}

/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: Object(external_wp_i18n_["__"])('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: Object(external_wp_i18n_["__"])('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: Object(external_wp_i18n_["__"])('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: Object(external_wp_i18n_["__"])('Remove a link.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: Object(external_wp_i18n_["__"])('Underline the selected text.')
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function KeyCombination(_ref) {
  let {
    keyCombination,
    forceAriaLabel
  } = _ref;
  const shortcut = keyCombination.modifier ? external_wp_keycodes_["displayShortcutList"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_["shortcutAriaLabel"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return Object(external_wp_element_["createElement"])("kbd", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, Object(external_lodash_["castArray"])(shortcut).map((character, index) => {
    if (character === '+') {
      return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], {
        key: index
      }, character);
    }

    return Object(external_wp_element_["createElement"])("kbd", {
      key: index,
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-key"
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
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-description"
  }, description), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-keyboard-shortcut-help-modal__shortcut-term"
  }, Object(external_wp_element_["createElement"])(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => Object(external_wp_element_["createElement"])(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


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
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_wp_keyboardShortcuts_["store"]);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);

  if (!keyCombination) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/keyboard-shortcut-help-modal/index.js


/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





const MODAL_NAME = 'edit-post/keyboard-shortcut-help';

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
    Object(external_wp_element_["createElement"])("ul", {
      className: "edit-post-keyboard-shortcut-help-modal__shortcut-list",
      role: "list"
    }, shortcuts.map((shortcut, index) => Object(external_wp_element_["createElement"])("li", {
      className: "edit-post-keyboard-shortcut-help-modal__shortcut",
      key: index
    }, Object(external_lodash_["isString"])(shortcut) ? Object(external_wp_element_["createElement"])(dynamic_shortcut, {
      name: shortcut
    }) : Object(external_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, shortcut))))
    /* eslint-enable jsx-a11y/no-redundant-roles */

  );
};

const ShortcutSection = _ref2 => {
  let {
    title,
    shortcuts,
    className
  } = _ref2;
  return Object(external_wp_element_["createElement"])("section", {
    className: classnames_default()('edit-post-keyboard-shortcut-help-modal__section', className)
  }, !!title && Object(external_wp_element_["createElement"])("h2", {
    className: "edit-post-keyboard-shortcut-help-modal__section-title"
  }, title), Object(external_wp_element_["createElement"])(ShortcutList, {
    shortcuts: shortcuts
  }));
};

const ShortcutCategorySection = _ref3 => {
  let {
    title,
    categoryName,
    additionalShortcuts = []
  } = _ref3;
  const categoryShortcuts = Object(external_wp_data_["useSelect"])(select => {
    return select(external_wp_keyboardShortcuts_["store"]).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return Object(external_wp_element_["createElement"])(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};

function KeyboardShortcutHelpModal(_ref4) {
  let {
    isModalActive,
    toggleModal
  } = _ref4;
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/edit-post/keyboard-shortcuts', toggleModal);

  if (!isModalActive) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    className: "edit-post-keyboard-shortcut-help-modal",
    title: Object(external_wp_i18n_["__"])('Keyboard shortcuts'),
    closeLabel: Object(external_wp_i18n_["__"])('Close'),
    onRequestClose: toggleModal
  }, Object(external_wp_element_["createElement"])(ShortcutSection, {
    className: "edit-post-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/edit-post/keyboard-shortcuts']
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Global shortcuts'),
    categoryName: "global"
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Selection shortcuts'),
    categoryName: "selection"
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: Object(external_wp_i18n_["__"])('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: Object(external_wp_i18n_["__"])('Forward-slash')
    }]
  }), Object(external_wp_element_["createElement"])(ShortcutSection, {
    title: Object(external_wp_i18n_["__"])('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}
/* harmony default export */ var keyboard_shortcut_help_modal = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => ({
  isModalActive: select(store["a" /* store */]).isModalActive(MODAL_NAME)
})), Object(external_wp_data_["withDispatch"])((dispatch, _ref5) => {
  let {
    isModalActive
  } = _ref5;
  const {
    openModal,
    closeModal
  } = dispatch(store["a" /* store */]);
  return {
    toggleModal: () => isModalActive ? closeModal() : openModal(MODAL_NAME)
  };
})])(KeyboardShortcutHelpModal));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
var build_module_icon = __webpack_require__("iClF");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-left.js
var chevron_left = __webpack_require__("2gm7");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-right.js
var chevron_right = __webpack_require__("1iEr");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/section.js


const Section = _ref => {
  let {
    description,
    title,
    children
  } = _ref;
  return Object(external_wp_element_["createElement"])("section", {
    className: "edit-post-preferences-modal__section"
  }, Object(external_wp_element_["createElement"])("h2", {
    className: "edit-post-preferences-modal__section-title"
  }, title), description && Object(external_wp_element_["createElement"])("p", {
    className: "edit-post-preferences-modal__section-description"
  }, description), children);
};

/* harmony default export */ var preferences_modal_section = (Section);

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/options/index.js + 6 modules
var options = __webpack_require__("Y5n4");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/meta-boxes-section.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function MetaBoxesSection(_ref) {
  let {
    areCustomFieldsRegistered,
    metaBoxes,
    ...sectionProps
  } = _ref;
  // The 'Custom Fields' meta box is a special case that we handle separately.
  const thirdPartyMetaBoxes = Object(external_lodash_["filter"])(metaBoxes, _ref2 => {
    let {
      id
    } = _ref2;
    return id !== 'postcustom';
  });

  if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(preferences_modal_section, sectionProps, areCustomFieldsRegistered && Object(external_wp_element_["createElement"])(options["a" /* EnableCustomFieldsOption */], {
    label: Object(external_wp_i18n_["__"])('Custom fields')
  }), Object(external_lodash_["map"])(thirdPartyMetaBoxes, _ref3 => {
    let {
      id,
      title
    } = _ref3;
    return Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      key: id,
      label: title,
      panelName: `meta-box-${id}`
    });
  }));
}
/* harmony default export */ var meta_boxes_section = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getEditorSettings
  } = select(external_wp_editor_["store"]);
  const {
    getAllMetaBoxes
  } = select(store["a" /* store */]);
  return {
    // This setting should not live in the block editor's store.
    areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== undefined,
    metaBoxes: getAllMetaBoxes()
  };
})(MetaBoxesSection));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/checklist.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function BlockTypesChecklist(_ref) {
  let {
    blockTypes,
    value,
    onItemChange
  } = _ref;
  return Object(external_wp_element_["createElement"])("ul", {
    className: "edit-post-block-manager__checklist"
  }, blockTypes.map(blockType => Object(external_wp_element_["createElement"])("li", {
    key: blockType.name,
    className: "edit-post-block-manager__checklist-item"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    label: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, blockType.title, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockIcon"], {
      icon: blockType.icon
    })),
    checked: value.includes(blockType.name),
    onChange: Object(external_lodash_["partial"])(onItemChange, blockType.name)
  }))));
}

/* harmony default export */ var checklist = (BlockTypesChecklist);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/edit-post-settings/index.js
/**
 * WordPress dependencies
 */

const EditPostSettings = Object(external_wp_element_["createContext"])({});
/* harmony default export */ var edit_post_settings = (EditPostSettings);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/category.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */





function BlockManagerCategory(_ref) {
  let {
    instanceId,
    title,
    blockTypes,
    hiddenBlockTypes,
    toggleVisible,
    toggleAllVisible
  } = _ref;
  const settings = Object(external_wp_element_["useContext"])(edit_post_settings);
  const {
    allowedBlockTypes
  } = settings;
  const filteredBlockTypes = Object(external_wp_element_["useMemo"])(() => {
    if (allowedBlockTypes === true) {
      return blockTypes;
    }

    return blockTypes.filter(_ref2 => {
      let {
        name
      } = _ref2;
      return Object(external_lodash_["includes"])(allowedBlockTypes || [], name);
    });
  }, [allowedBlockTypes, blockTypes]);

  if (!filteredBlockTypes.length) {
    return null;
  }

  const checkedBlockNames = Object(external_lodash_["without"])(Object(external_lodash_["map"])(filteredBlockTypes, 'name'), ...hiddenBlockTypes);
  const titleId = 'edit-post-block-manager__category-title-' + instanceId;
  const isAllChecked = checkedBlockNames.length === filteredBlockTypes.length;
  let ariaChecked;

  if (isAllChecked) {
    ariaChecked = 'true';
  } else if (checkedBlockNames.length > 0) {
    ariaChecked = 'mixed';
  } else {
    ariaChecked = 'false';
  }

  return Object(external_wp_element_["createElement"])("div", {
    role: "group",
    "aria-labelledby": titleId,
    className: "edit-post-block-manager__category"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["CheckboxControl"], {
    checked: isAllChecked,
    onChange: toggleAllVisible,
    className: "edit-post-block-manager__category-title",
    "aria-checked": ariaChecked,
    label: Object(external_wp_element_["createElement"])("span", {
      id: titleId
    }, title)
  }), Object(external_wp_element_["createElement"])(checklist, {
    blockTypes: filteredBlockTypes,
    value: checkedBlockNames,
    onItemChange: toggleVisible
  }));
}

/* harmony default export */ var block_manager_category = (Object(external_wp_compose_["compose"])([external_wp_compose_["withInstanceId"], Object(external_wp_data_["withSelect"])(select => {
  const {
    getPreference
  } = select(store["a" /* store */]);
  return {
    hiddenBlockTypes: getPreference('hiddenBlockTypes')
  };
}), Object(external_wp_data_["withDispatch"])((dispatch, ownProps) => {
  const {
    showBlockTypes,
    hideBlockTypes
  } = dispatch(store["a" /* store */]);
  return {
    toggleVisible(blockName, nextIsChecked) {
      if (nextIsChecked) {
        showBlockTypes(blockName);
      } else {
        hideBlockTypes(blockName);
      }
    },

    toggleAllVisible(nextIsChecked) {
      const blockNames = Object(external_lodash_["map"])(ownProps.blockTypes, 'name');

      if (nextIsChecked) {
        showBlockTypes(blockNames);
      } else {
        hideBlockTypes(blockNames);
      }
    }

  };
})])(BlockManagerCategory));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-manager/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




function BlockManager(_ref) {
  let {
    blockTypes,
    categories,
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  } = _ref;
  const [search, setSearch] = Object(external_wp_element_["useState"])(''); // Filtering occurs here (as opposed to `withSelect`) to avoid
  // wasted renders by consequence of `Array#filter` producing
  // a new value reference on each call.

  blockTypes = blockTypes.filter(blockType => hasBlockSupport(blockType, 'inserter', true) && (!search || isMatchingSearchTerm(blockType, search)) && (!blockType.parent || Object(external_lodash_["includes"])(blockType.parent, 'core/post-content')));
  return Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-block-manager__content"
  }, !!numberOfHiddenBlocks && Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-block-manager__disabled-blocks-count"
  }, Object(external_wp_i18n_["sprintf"])(
  /* translators: %d: number of blocks. */
  Object(external_wp_i18n_["_n"])('%d block is hidden.', '%d blocks are hidden.', numberOfHiddenBlocks), numberOfHiddenBlocks)), Object(external_wp_element_["createElement"])(external_wp_components_["SearchControl"], {
    label: Object(external_wp_i18n_["__"])('Search for a block'),
    placeholder: Object(external_wp_i18n_["__"])('Search for a block'),
    value: search,
    onChange: nextSearch => setSearch(nextSearch),
    className: "edit-post-block-manager__search"
  }), Object(external_wp_element_["createElement"])("div", {
    tabIndex: "0",
    role: "region",
    "aria-label": Object(external_wp_i18n_["__"])('Available block types'),
    className: "edit-post-block-manager__results"
  }, blockTypes.length === 0 && Object(external_wp_element_["createElement"])("p", {
    className: "edit-post-block-manager__no-results"
  }, Object(external_wp_i18n_["__"])('No blocks found.')), categories.map(category => Object(external_wp_element_["createElement"])(block_manager_category, {
    key: category.slug,
    title: category.title,
    blockTypes: Object(external_lodash_["filter"])(blockTypes, {
      category: category.slug
    })
  })), Object(external_wp_element_["createElement"])(block_manager_category, {
    title: Object(external_wp_i18n_["__"])('Uncategorized'),
    blockTypes: Object(external_lodash_["filter"])(blockTypes, _ref2 => {
      let {
        category
      } = _ref2;
      return !category;
    })
  })));
}

/* harmony default export */ var block_manager = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getBlockTypes,
    getCategories,
    hasBlockSupport,
    isMatchingSearchTerm
  } = select(external_wp_blocks_["store"]);
  const {
    getPreference
  } = select(store["a" /* store */]);
  const hiddenBlockTypes = getPreference('hiddenBlockTypes');
  const numberOfHiddenBlocks = Object(external_lodash_["isArray"])(hiddenBlockTypes) && hiddenBlockTypes.length;
  return {
    blockTypes: getBlockTypes(),
    categories: getCategories(),
    hasBlockSupport,
    isMatchingSearchTerm,
    numberOfHiddenBlocks
  };
})(BlockManager));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/preferences-modal/index.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */






const preferences_modal_MODAL_NAME = 'edit-post/preferences';
const PREFERENCES_MENU = 'preferences-menu';

function NavigationButton(_ref) {
  let {
    as: Tag = external_wp_components_["Button"],
    path,
    isBack = false,
    ...props
  } = _ref;
  const navigator = Object(external_wp_components_["__experimentalUseNavigator"])();
  return Object(external_wp_element_["createElement"])(Tag, Object(esm_extends["a" /* default */])({
    onClick: () => navigator.push(path, {
      isBack
    })
  }, props));
}

function PreferencesModal() {
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('medium');
  const {
    closeModal
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    isModalActive,
    isViewable
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_["store"]);
    const {
      getPostType
    } = select(external_wp_coreData_["store"]);
    const postType = getPostType(getEditedPostAttribute('type'));
    return {
      isModalActive: select(store["a" /* store */]).isModalActive(preferences_modal_MODAL_NAME),
      isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false)
    };
  }, []);
  const showBlockBreadcrumbsOption = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditorSettings
    } = select(external_wp_editor_["store"]);
    const {
      getEditorMode,
      isFeatureActive
    } = select(store["a" /* store */]);
    const mode = getEditorMode();
    const isRichEditingEnabled = getEditorSettings().richEditingEnabled;
    const hasReducedUI = isFeatureActive('reducedUI');
    return !hasReducedUI && isLargeViewport && isRichEditingEnabled && mode === 'visual';
  }, [isLargeViewport]);
  const sections = Object(external_wp_element_["useMemo"])(() => [{
    name: 'general',
    tabLabel: Object(external_wp_i18n_["__"])('General'),
    content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, isLargeViewport && Object(external_wp_element_["createElement"])(preferences_modal_section, {
      title: Object(external_wp_i18n_["__"])('Publishing'),
      description: Object(external_wp_i18n_["__"])('Change options related to publishing.')
    }, Object(external_wp_element_["createElement"])(options["e" /* EnablePublishSidebarOption */], {
      help: Object(external_wp_i18n_["__"])('Review settings, such as visibility and tags.'),
      label: Object(external_wp_i18n_["__"])('Include pre-publish checklist')
    })), Object(external_wp_element_["createElement"])(preferences_modal_section, {
      title: Object(external_wp_i18n_["__"])('Appearance'),
      description: Object(external_wp_i18n_["__"])('Customize options related to the block editor interface and editing flow.')
    }, Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "reducedUI",
      help: Object(external_wp_i18n_["__"])('Compacts options and outlines in the toolbar.'),
      label: Object(external_wp_i18n_["__"])('Reduce the interface')
    }), Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "focusMode",
      help: Object(external_wp_i18n_["__"])('Highlights the current block and fades other content.'),
      label: Object(external_wp_i18n_["__"])('Spotlight mode')
    }), Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "showIconLabels",
      help: Object(external_wp_i18n_["__"])('Shows text instead of icons.'),
      label: Object(external_wp_i18n_["__"])('Display button labels')
    }), Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "themeStyles",
      help: Object(external_wp_i18n_["__"])('Make the editor look like your theme.'),
      label: Object(external_wp_i18n_["__"])('Use theme styles')
    }), showBlockBreadcrumbsOption && Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "showBlockBreadcrumbs",
      help: Object(external_wp_i18n_["__"])('Shows block breadcrumbs at the bottom of the editor.'),
      label: Object(external_wp_i18n_["__"])('Display block breadcrumbs')
    })))
  }, {
    name: 'blocks',
    tabLabel: Object(external_wp_i18n_["__"])('Blocks'),
    content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(preferences_modal_section, {
      title: Object(external_wp_i18n_["__"])('Block interactions'),
      description: Object(external_wp_i18n_["__"])('Customize how you interact with blocks in the block library and editing canvas.')
    }, Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "mostUsedBlocks",
      help: Object(external_wp_i18n_["__"])('Places the most frequent blocks in the block library.'),
      label: Object(external_wp_i18n_["__"])('Show most used blocks')
    }), Object(external_wp_element_["createElement"])(options["b" /* EnableFeature */], {
      featureName: "keepCaretInsideBlock",
      help: Object(external_wp_i18n_["__"])('Aids screen readers by stopping text caret from leaving blocks.'),
      label: Object(external_wp_i18n_["__"])('Contain text cursor inside block')
    })), Object(external_wp_element_["createElement"])(preferences_modal_section, {
      title: Object(external_wp_i18n_["__"])('Visible blocks'),
      description: Object(external_wp_i18n_["__"])("Disable blocks that you don't want to appear in the inserter. They can always be toggled back on later.")
    }, Object(external_wp_element_["createElement"])(block_manager, null)))
  }, {
    name: 'panels',
    tabLabel: Object(external_wp_i18n_["__"])('Panels'),
    content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(preferences_modal_section, {
      title: Object(external_wp_i18n_["__"])('Document settings'),
      description: Object(external_wp_i18n_["__"])('Choose what displays in the panel.')
    }, Object(external_wp_element_["createElement"])(options["d" /* EnablePluginDocumentSettingPanelOption */].Slot, null), isViewable && Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Permalink'),
      panelName: "post-link"
    }), isViewable && Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Template'),
      panelName: "template"
    }), Object(external_wp_element_["createElement"])(external_wp_editor_["PostTaxonomies"], {
      taxonomyWrapper: (content, taxonomy) => Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
        label: Object(external_lodash_["get"])(taxonomy, ['labels', 'menu_name']),
        panelName: `taxonomy-panel-${taxonomy.slug}`
      })
    }), Object(external_wp_element_["createElement"])(external_wp_editor_["PostFeaturedImageCheck"], null, Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Featured image'),
      panelName: "featured-image"
    })), Object(external_wp_element_["createElement"])(external_wp_editor_["PostExcerptCheck"], null, Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Excerpt'),
      panelName: "post-excerpt"
    })), Object(external_wp_element_["createElement"])(external_wp_editor_["PostTypeSupportCheck"], {
      supportKeys: ['comments', 'trackbacks']
    }, Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Discussion'),
      panelName: "discussion-panel"
    })), Object(external_wp_element_["createElement"])(external_wp_editor_["PageAttributesCheck"], null, Object(external_wp_element_["createElement"])(options["c" /* EnablePanelOption */], {
      label: Object(external_wp_i18n_["__"])('Page attributes'),
      panelName: "page-attributes"
    }))), Object(external_wp_element_["createElement"])(meta_boxes_section, {
      title: Object(external_wp_i18n_["__"])('Additional'),
      description: Object(external_wp_i18n_["__"])('Add extra areas to the editor.')
    }))
  }], [isViewable, isLargeViewport, showBlockBreadcrumbsOption]); // This is also used to sync the two different rendered components
  // between small and large viewports.

  const [activeMenu, setActiveMenu] = Object(external_wp_element_["useState"])(PREFERENCES_MENU);
  /**
   * Create helper objects from `sections` for easier data handling.
   * `tabs` is used for creating the `TabPanel` and `sectionsContentMap`
   * is used for easier access to active tab's content.
   */

  const {
    tabs,
    sectionsContentMap
  } = Object(external_wp_element_["useMemo"])(() => sections.reduce((accumulator, _ref2) => {
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
  }), [sections]);
  const getCurrentTab = Object(external_wp_element_["useCallback"])(tab => sectionsContentMap[tab.name] || null, [sectionsContentMap]);

  if (!isModalActive) {
    return null;
  }

  let modalContent; // We render different components based on the viewport size.

  if (isLargeViewport) {
    modalContent = Object(external_wp_element_["createElement"])(external_wp_components_["TabPanel"], {
      className: "edit-post-preferences__tabs",
      tabs: tabs,
      initialTabName: activeMenu !== PREFERENCES_MENU ? activeMenu : undefined,
      onSelect: setActiveMenu,
      orientation: "vertical"
    }, getCurrentTab);
  } else {
    modalContent = Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalNavigatorProvider"], {
      initialPath: "/"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalNavigatorScreen"], {
      path: "/"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Card"], {
      isBorderless: true,
      size: "small"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["CardBody"], null, Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalItemGroup"], null, tabs.map(tab => {
      return Object(external_wp_element_["createElement"])(NavigationButton, {
        key: tab.name,
        path: tab.name,
        as: external_wp_components_["__experimentalItem"],
        isAction: true
      }, Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalHStack"], {
        justify: "space-between"
      }, Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalTruncate"], null, tab.title)), Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
        icon: Object(external_wp_i18n_["isRTL"])() ? chevron_left["a" /* default */] : chevron_right["a" /* default */]
      }))));
    }))))), sections.map(section => {
      return Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalNavigatorScreen"], {
        key: `${section.name}-menu`,
        path: section.name
      }, Object(external_wp_element_["createElement"])(external_wp_components_["Card"], {
        isBorderless: true,
        size: "large"
      }, Object(external_wp_element_["createElement"])(external_wp_components_["CardHeader"], {
        isBorderless: false,
        justify: "left",
        size: "small",
        gap: "6"
      }, Object(external_wp_element_["createElement"])(NavigationButton, {
        path: "/",
        icon: Object(external_wp_i18n_["isRTL"])() ? chevron_right["a" /* default */] : chevron_left["a" /* default */],
        isBack: true,
        "aria-label": Object(external_wp_i18n_["__"])('Navigate to the previous view')
      }), Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalText"], {
        size: "16"
      }, section.tabLabel)), Object(external_wp_element_["createElement"])(external_wp_components_["CardBody"], null, section.content)));
    }));
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    className: "edit-post-preferences-modal",
    title: Object(external_wp_i18n_["__"])('Preferences'),
    closeLabel: Object(external_wp_i18n_["__"])('Close'),
    onRequestClose: closeModal
  }, modalContent);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/browser-url/index.js
/**
 * WordPress dependencies
 */




/**
 * Returns the Post's Edit URL.
 *
 * @param {number} postId Post ID.
 *
 * @return {string} Post edit URL.
 */

function getPostEditURL(postId) {
  return Object(external_wp_url_["addQueryArgs"])('post.php', {
    post: postId,
    action: 'edit'
  });
}
/**
 * Returns the Post's Trashed URL.
 *
 * @param {number} postId   Post ID.
 * @param {string} postType Post Type.
 *
 * @return {string} Post trashed URL.
 */

function getPostTrashedURL(postId, postType) {
  return Object(external_wp_url_["addQueryArgs"])('edit.php', {
    trashed: 1,
    post_type: postType,
    ids: postId
  });
}
class browser_url_BrowserURL extends external_wp_element_["Component"] {
  constructor() {
    super(...arguments);
    this.state = {
      historyId: null
    };
  }

  componentDidUpdate(prevProps) {
    const {
      postId,
      postStatus,
      postType,
      isSavingPost
    } = this.props;
    const {
      historyId
    } = this.state; // Posts are still dirty while saving so wait for saving to finish
    // to avoid the unsaved changes warning when trashing posts.

    if (postStatus === 'trash' && !isSavingPost) {
      this.setTrashURL(postId, postType);
      return;
    }

    if ((postId !== prevProps.postId || postId !== historyId) && postStatus !== 'auto-draft' && postId) {
      this.setBrowserURL(postId);
    }
  }
  /**
   * Navigates the browser to the post trashed URL to show a notice about the trashed post.
   *
   * @param {number} postId   Post ID.
   * @param {string} postType Post Type.
   */


  setTrashURL(postId, postType) {
    window.location.href = getPostTrashedURL(postId, postType);
  }
  /**
   * Replaces the browser URL with a post editor link for the given post ID.
   *
   * Note it is important that, since this function may be called when the
   * editor first loads, the result generated `getPostEditURL` matches that
   * produced by the server. Otherwise, the URL will change unexpectedly.
   *
   * @param {number} postId Post ID for which to generate post editor URL.
   */


  setBrowserURL(postId) {
    window.history.replaceState({
      id: postId
    }, 'Post ' + postId, getPostEditURL(postId));
    this.setState(() => ({
      historyId: postId
    }));
  }

  render() {
    return null;
  }

}
/* harmony default export */ var browser_url = (Object(external_wp_data_["withSelect"])(select => {
  const {
    getCurrentPost,
    isSavingPost
  } = select(external_wp_editor_["store"]);
  const post = getCurrentPost();
  let {
    id,
    status,
    type
  } = post;
  const isTemplate = ['wp_template', 'wp_template_part'].includes(type);

  if (isTemplate) {
    id = post.wp_id;
  }

  return {
    postId: id,
    postStatus: status,
    postType: type,
    isSavingPost: isSavingPost()
  };
})(browser_url_BrowserURL));

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/wordpress.js
var wordpress = __webpack_require__("wduq");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/fullscreen-mode-close/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



function FullscreenModeClose(_ref) {
  let {
    showTooltip,
    icon,
    href
  } = _ref;
  const {
    isActive,
    isRequestingSiteIcon,
    postType,
    siteIconUrl
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getCurrentPostType
    } = select(external_wp_editor_["store"]);
    const {
      isFeatureActive
    } = select(store["a" /* store */]);
    const {
      getEntityRecord,
      getPostType,
      isResolving
    } = select(external_wp_coreData_["store"]);
    const siteData = getEntityRecord('root', '__unstableBase', undefined) || {};
    return {
      isActive: isFeatureActive('fullscreenMode'),
      isRequestingSiteIcon: isResolving('getEntityRecord', ['root', '__unstableBase', undefined]),
      postType: getPostType(getCurrentPostType()),
      siteIconUrl: siteData.site_icon_url
    };
  }, []);
  const disableMotion = Object(external_wp_compose_["useReducedMotion"])();

  if (!isActive || !postType) {
    return null;
  }

  let buttonIcon = Object(external_wp_element_["createElement"])(external_wp_components_["Icon"], {
    size: "36px",
    icon: wordpress["a" /* default */]
  });
  const effect = {
    expand: {
      scale: 1.7,
      borderRadius: 0,
      transition: {
        type: 'tween',
        duration: '0.2'
      }
    }
  };

  if (siteIconUrl) {
    buttonIcon = Object(external_wp_element_["createElement"])(external_wp_components_["__unstableMotion"].img, {
      variants: !disableMotion && effect,
      alt: Object(external_wp_i18n_["__"])('Site Icon'),
      className: "edit-post-fullscreen-mode-close_site-icon",
      src: siteIconUrl
    });
  }

  if (isRequestingSiteIcon) {
    buttonIcon = null;
  } // Override default icon if custom icon is provided via props.


  if (icon) {
    buttonIcon = Object(external_wp_element_["createElement"])(external_wp_components_["Icon"], {
      size: "36px",
      icon: icon
    });
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["__unstableMotion"].div, {
    whileHover: "expand"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "edit-post-fullscreen-mode-close has-icon",
    href: href !== null && href !== void 0 ? href : Object(external_wp_url_["addQueryArgs"])('edit.php', {
      post_type: postType.slug
    }),
    label: Object(external_lodash_["get"])(postType, ['labels', 'view_items'], Object(external_wp_i18n_["__"])('Back')),
    showTooltip: showTooltip
  }, buttonIcon));
}

/* harmony default export */ var fullscreen_mode_close = (FullscreenModeClose);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/list-view.js
var list_view = __webpack_require__("OzlF");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js
var plus = __webpack_require__("Q4Sy");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/header-toolbar/index.js


/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */



const preventDefault = event => {
  event.preventDefault();
};

function HeaderToolbar() {
  const inserterButton = Object(external_wp_element_["useRef"])();
  const {
    setIsInserterOpened,
    setIsListViewOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    isInserterEnabled,
    isInserterOpened,
    isTextModeEnabled,
    showIconLabels,
    isListViewOpen,
    listViewShortcut
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      hasInserterItems,
      getBlockRootClientId,
      getBlockSelectionEnd
    } = select(external_wp_blockEditor_["store"]);
    const {
      getEditorSettings
    } = select(external_wp_editor_["store"]);
    const {
      getEditorMode,
      isFeatureActive,
      isListViewOpened
    } = select(store["a" /* store */]);
    const {
      getShortcutRepresentation
    } = select(external_wp_keyboardShortcuts_["store"]);
    return {
      // This setting (richEditingEnabled) should not live in the block editor's setting.
      isInserterEnabled: getEditorMode() === 'visual' && getEditorSettings().richEditingEnabled && hasInserterItems(getBlockRootClientId(getBlockSelectionEnd())),
      isInserterOpened: select(store["a" /* store */]).isInserterOpened(),
      isTextModeEnabled: getEditorMode() === 'text',
      showIconLabels: isFeatureActive('showIconLabels'),
      isListViewOpen: isListViewOpened(),
      listViewShortcut: getShortcutRepresentation('core/edit-post/toggle-list-view')
    };
  }, []);
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('medium');
  const isWideViewport = Object(external_wp_compose_["useViewportMatch"])('wide');
  /* translators: accessibility text for the editor toolbar */

  const toolbarAriaLabel = Object(external_wp_i18n_["__"])('Document tools');

  const toggleListView = Object(external_wp_element_["useCallback"])(() => setIsListViewOpened(!isListViewOpen), [setIsListViewOpened, isListViewOpen]);
  const overflowItems = Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    as: external_wp_editor_["TableOfContents"],
    hasOutlineItemsDisabled: isTextModeEnabled,
    repositionDropdown: showIconLabels && !isWideViewport,
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    as: external_wp_components_["Button"],
    className: "edit-post-header-toolbar__list-view-toggle",
    icon: list_view["a" /* default */],
    disabled: isTextModeEnabled,
    isPressed: isListViewOpen
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('List View'),
    onClick: toggleListView,
    shortcut: listViewShortcut,
    showTooltip: !showIconLabels
  }));
  const openInserter = Object(external_wp_element_["useCallback"])(() => {
    if (isInserterOpened) {
      // Focusing the inserter button closes the inserter popover
      inserterButton.current.focus();
    } else {
      setIsInserterOpened(true);
    }
  }, [isInserterOpened, setIsInserterOpened]);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["NavigableToolbar"], {
    className: "edit-post-header-toolbar",
    "aria-label": toolbarAriaLabel
  }, Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-header-toolbar__left"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    ref: inserterButton,
    as: external_wp_components_["Button"],
    className: "edit-post-header-toolbar__inserter-toggle",
    variant: "primary",
    isPressed: isInserterOpened,
    onMouseDown: preventDefault,
    onClick: openInserter,
    disabled: !isInserterEnabled,
    icon: plus["a" /* default */]
    /* translators: button label text should, if possible, be under 16
    characters. */
    ,
    label: Object(external_wp_i18n_["_x"])('Toggle block inserter', 'Generic label for block inserter button'),
    showTooltip: !showIconLabels
  }, showIconLabels && (!isInserterOpened ? Object(external_wp_i18n_["__"])('Add') : Object(external_wp_i18n_["__"])('Close'))), (isWideViewport || !showIconLabels) && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, isLargeViewport && Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    as: external_wp_blockEditor_["ToolSelector"],
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined,
    disabled: isTextModeEnabled
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    as: external_wp_editor_["EditorHistoryUndo"],
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarItem"], {
    as: external_wp_editor_["EditorHistoryRedo"],
    showTooltip: !showIconLabels,
    variant: showIconLabels ? 'tertiary' : undefined
  }), overflowItems)));
}

/* harmony default export */ var header_toolbar = (HeaderToolbar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/mode-switcher/index.js


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
  label: Object(external_wp_i18n_["__"])('Visual editor')
}, {
  value: 'text',
  label: Object(external_wp_i18n_["__"])('Code editor')
}];

function ModeSwitcher() {
  const {
    shortcut,
    isRichEditingEnabled,
    isCodeEditingEnabled,
    isEditingTemplate,
    mode
  } = Object(external_wp_data_["useSelect"])(select => ({
    shortcut: select(external_wp_keyboardShortcuts_["store"]).getShortcutRepresentation('core/edit-post/toggle-mode'),
    isRichEditingEnabled: select(external_wp_editor_["store"]).getEditorSettings().richEditingEnabled,
    isCodeEditingEnabled: select(external_wp_editor_["store"]).getEditorSettings().codeEditingEnabled,
    isEditingTemplate: select(store["a" /* store */]).isEditingTemplate(),
    mode: select(store["a" /* store */]).getEditorMode()
  }), []);
  const {
    switchEditorMode
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);

  if (isEditingTemplate) {
    return null;
  }

  if (!isRichEditingEnabled || !isCodeEditingEnabled) {
    return null;
  }

  const choices = MODES.map(choice => {
    if (choice.value !== mode) {
      return { ...choice,
        shortcut
      };
    }

    return choice;
  });
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["__"])('Editor')
  }, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItemsChoice"], {
    choices: choices,
    value: mode,
    onSelect: switchEditorMode
  }));
}

/* harmony default export */ var mode_switcher = (ModeSwitcher);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/preferences-menu-item/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function PreferencesMenuItem() {
  const {
    openModal
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    onClick: () => {
      openModal('edit-post/preferences');
    }
  }, Object(external_wp_i18n_["__"])('Preferences'));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/writing-menu/index.js


/**
 * WordPress dependencies
 */






function WritingMenu() {
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('medium');

  if (!isLargeViewport) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["_x"])('View', 'noun')
  }, Object(external_wp_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-post",
    feature: "fixedToolbar",
    label: Object(external_wp_i18n_["__"])('Top toolbar'),
    info: Object(external_wp_i18n_["__"])('Access all block and document tools in a single place'),
    messageActivated: Object(external_wp_i18n_["__"])('Top toolbar activated'),
    messageDeactivated: Object(external_wp_i18n_["__"])('Top toolbar deactivated')
  }), Object(external_wp_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-post",
    feature: "focusMode",
    label: Object(external_wp_i18n_["__"])('Spotlight mode'),
    info: Object(external_wp_i18n_["__"])('Focus on one block at a time'),
    messageActivated: Object(external_wp_i18n_["__"])('Spotlight mode activated'),
    messageDeactivated: Object(external_wp_i18n_["__"])('Spotlight mode deactivated')
  }), Object(external_wp_element_["createElement"])(build_module["g" /* MoreMenuFeatureToggle */], {
    scope: "core/edit-post",
    feature: "fullscreenMode",
    label: Object(external_wp_i18n_["__"])('Fullscreen mode'),
    info: Object(external_wp_i18n_["__"])('Work without distraction'),
    messageActivated: Object(external_wp_i18n_["__"])('Fullscreen mode activated'),
    messageDeactivated: Object(external_wp_i18n_["__"])('Fullscreen mode deactivated'),
    shortcut: external_wp_keycodes_["displayShortcut"].secondary('f')
  }));
}

/* harmony default export */ var writing_menu = (WritingMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/more-menu/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





const POPOVER_PROPS = {
  className: 'edit-post-more-menu__content'
};

const MoreMenu = _ref => {
  let {
    showIconLabels
  } = _ref;
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('large');
  return Object(external_wp_element_["createElement"])(build_module["f" /* MoreMenuDropdown */], {
    className: "edit-post-more-menu",
    popoverProps: POPOVER_PROPS,
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
    return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, showIconLabels && !isLargeViewport && Object(external_wp_element_["createElement"])(build_module["h" /* PinnedItems */].Slot, {
      className: showIconLabels && 'show-icon-labels',
      scope: "core/edit-post"
    }), Object(external_wp_element_["createElement"])(writing_menu, null), Object(external_wp_element_["createElement"])(mode_switcher, null), Object(external_wp_element_["createElement"])(build_module["a" /* ActionItem */].Slot, {
      name: "core/edit-post/plugin-more-menu",
      label: Object(external_wp_i18n_["__"])('Plugins'),
      as: external_wp_components_["MenuGroup"],
      fillProps: {
        onClick: onClose
      }
    }), Object(external_wp_element_["createElement"])(tools_more_menu_group.Slot, {
      fillProps: {
        onClose
      }
    }), Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], null, Object(external_wp_element_["createElement"])(PreferencesMenuItem, null)));
  });
};

/* harmony default export */ var more_menu = (MoreMenu);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/post-publish-button-or-toggle.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function PostPublishButtonOrToggle(_ref) {
  let {
    forceIsDirty,
    forceIsSaving,
    hasPublishAction,
    isBeingScheduled,
    isPending,
    isPublished,
    isPublishSidebarEnabled,
    isPublishSidebarOpened,
    isScheduled,
    togglePublishSidebar,
    setEntitiesSavedStatesCallback
  } = _ref;
  const IS_TOGGLE = 'toggle';
  const IS_BUTTON = 'button';
  const isSmallerThanMediumViewport = Object(external_wp_compose_["useViewportMatch"])('medium', '<');
  let component;
  /**
   * Conditions to show a BUTTON (publish directly) or a TOGGLE (open publish sidebar):
   *
   * 1) We want to show a BUTTON when the post status is at the _final stage_
   * for a particular role (see https://wordpress.org/support/article/post-status/):
   *
   * - is published
   * - is scheduled to be published
   * - is pending and can't be published (but only for viewports >= medium).
   * 	 Originally, we considered showing a button for pending posts that couldn't be published
   * 	 (for example, for an author with the contributor role). Some languages can have
   * 	 long translations for "Submit for review", so given the lack of UI real estate available
   * 	 we decided to take into account the viewport in that case.
   *  	 See: https://github.com/WordPress/gutenberg/issues/10475
   *
   * 2) Then, in small viewports, we'll show a TOGGLE.
   *
   * 3) Finally, we'll use the publish sidebar status to decide:
   *
   * - if it is enabled, we show a TOGGLE
   * - if it is disabled, we show a BUTTON
   */

  if (isPublished || isScheduled && isBeingScheduled || isPending && !hasPublishAction && !isSmallerThanMediumViewport) {
    component = IS_BUTTON;
  } else if (isSmallerThanMediumViewport) {
    component = IS_TOGGLE;
  } else if (isPublishSidebarEnabled) {
    component = IS_TOGGLE;
  } else {
    component = IS_BUTTON;
  }

  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostPublishButton"], {
    forceIsDirty: forceIsDirty,
    forceIsSaving: forceIsSaving,
    isOpen: isPublishSidebarOpened,
    isToggle: component === IS_TOGGLE,
    onToggle: togglePublishSidebar,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  });
}
/* harmony default export */ var post_publish_button_or_toggle = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])(select => ({
  hasPublishAction: Object(external_lodash_["get"])(select(external_wp_editor_["store"]).getCurrentPost(), ['_links', 'wp:action-publish'], false),
  isBeingScheduled: select(external_wp_editor_["store"]).isEditedPostBeingScheduled(),
  isPending: select(external_wp_editor_["store"]).isCurrentPostPending(),
  isPublished: select(external_wp_editor_["store"]).isCurrentPostPublished(),
  isPublishSidebarEnabled: select(external_wp_editor_["store"]).isPublishSidebarEnabled(),
  isPublishSidebarOpened: select(store["a" /* store */]).isPublishSidebarOpened(),
  isScheduled: select(external_wp_editor_["store"]).isCurrentPostScheduled()
})), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    togglePublishSidebar
  } = dispatch(store["a" /* store */]);
  return {
    togglePublishSidebar
  };
}))(PostPublishButtonOrToggle));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/device-preview/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function DevicePreview() {
  const {
    hasActiveMetaboxes,
    isPostSaveable,
    isSaving,
    deviceType
  } = Object(external_wp_data_["useSelect"])(select => ({
    hasActiveMetaboxes: select(store["a" /* store */]).hasMetaBoxes(),
    isSaving: select(store["a" /* store */]).isSavingMetaBoxes(),
    isPostSaveable: select(external_wp_editor_["store"]).isEditedPostSaveable(),
    deviceType: select(store["a" /* store */]).__experimentalGetPreviewDeviceType()
  }), []);
  const {
    __experimentalSetPreviewDeviceType: setPreviewDeviceType
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__experimentalPreviewOptions"], {
    isEnabled: isPostSaveable,
    className: "edit-post-post-preview-dropdown",
    deviceType: deviceType,
    setDeviceType: setPreviewDeviceType
  }, Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], null, Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-header-preview__grouping-external"
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostPreviewButton"], {
    className: 'edit-post-header-preview__button-external',
    role: "menuitem",
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined,
    textContent: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_i18n_["__"])('Preview in new tab'), Object(external_wp_element_["createElement"])(external_wp_components_["Icon"], {
      icon: external["a" /* default */]
    }))
  }))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/main-dashboard-button/index.js


/**
 * WordPress dependencies
 */

const slotName = '__experimentalMainDashboardButton';
const {
  Fill,
  Slot: MainDashboardButtonSlot
} = Object(external_wp_components_["createSlotFill"])(slotName);
const MainDashboardButton = Fill;

const main_dashboard_button_Slot = _ref => {
  let {
    children
  } = _ref;
  const slot = Object(external_wp_components_["__experimentalUseSlot"])(slotName);
  const hasFills = Boolean(slot.fills && slot.fills.length);

  if (!hasFills) {
    return children;
  }

  return Object(external_wp_element_["createElement"])(MainDashboardButtonSlot, {
    bubblesVirtually: true
  });
};

MainDashboardButton.Slot = main_dashboard_button_Slot;
/* harmony default export */ var main_dashboard_button = (MainDashboardButton);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/chevron-down.js
var chevron_down = __webpack_require__("NWDH");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/delete-template.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function DeleteTemplate() {
  const {
    clearSelectedBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const {
    setIsEditingTemplate
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    getEditorSettings
  } = Object(external_wp_data_["useSelect"])(external_wp_editor_["store"]);
  const {
    updateEditorSettings,
    editPost
  } = Object(external_wp_data_["useDispatch"])(external_wp_editor_["store"]);
  const {
    deleteEntityRecord
  } = Object(external_wp_data_["useDispatch"])(external_wp_coreData_["store"]);
  const {
    template
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store["a" /* store */]);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null
    };
  }, []);

  if (!template || !template.wp_id) {
    return null;
  }

  let templateTitle = template.slug;

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    className: "edit-post-template-top-area__second-menu-group"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    className: "edit-post-template-top-area__delete-template-button",
    isDestructive: true,
    variant: "secondary",
    "aria-label": Object(external_wp_i18n_["__"])('Delete template'),
    onClick: () => {
      if ( // eslint-disable-next-line no-alert
      window.confirm(Object(external_wp_i18n_["sprintf"])(
      /* translators: %s: template name */
      Object(external_wp_i18n_["__"])('Are you sure you want to delete the %s template? It may be used by other pages or posts.'), templateTitle))) {
        clearSelectedBlock();
        setIsEditingTemplate(false);
        editPost({
          template: ''
        });
        const settings = getEditorSettings();
        const newAvailableTemplates = Object(external_lodash_["pickBy"])(settings.availableTemplates, (_title, id) => {
          return id !== template.slug;
        });
        updateEditorSettings({ ...settings,
          availableTemplates: newAvailableTemplates
        });
        deleteEntityRecord('postType', 'wp_template', template.id);
      }
    }
  }, Object(external_wp_i18n_["__"])('Delete template')));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/edit-template-title.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */


function EditTemplateTitle() {
  const {
    template
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    return {
      template: getEditedPostTemplate()
    };
  }, []);
  const {
    editEntityRecord
  } = Object(external_wp_data_["useDispatch"])(external_wp_coreData_["store"]);
  const {
    getEditorSettings
  } = Object(external_wp_data_["useSelect"])(external_wp_editor_["store"]);
  const {
    updateEditorSettings
  } = Object(external_wp_data_["useDispatch"])(external_wp_editor_["store"]);

  if (template.has_theme_file) {
    return null;
  }

  let templateTitle = Object(external_wp_i18n_["__"])('Default');

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    label: Object(external_wp_i18n_["__"])('Title'),
    value: templateTitle,
    help: Object(external_wp_i18n_["__"])('Give the template a title that indicates its purpose, e.g. "Full Width".'),
    onChange: newTitle => {
      const settings = getEditorSettings();
      const newAvailableTemplates = Object(external_lodash_["mapValues"])(settings.availableTemplates, (existingTitle, id) => {
        if (id !== template.slug) {
          return existingTitle;
        }

        return newTitle;
      });
      updateEditorSettings({ ...settings,
        availableTemplates: newAvailableTemplates
      });
      editEntityRecord('postType', 'wp_template', template.id, {
        title: newTitle
      });
    }
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/template-description.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function TemplateDescription() {
  const {
    description,
    title
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    return {
      title: getEditedPostTemplate().title,
      description: getEditedPostTemplate().description
    };
  }, []);

  if (!description) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalHeading"], {
    level: 4,
    weight: 600
  }, title), Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalText"], {
    className: "edit-post-template-details__description",
    size: "body",
    as: "p",
    style: {
      marginTop: '12px'
    }
  }, description));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/template-title/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */








function TemplateTitle() {
  const {
    template,
    isEditing,
    title
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_["store"]);

    const _isEditing = isEditingTemplate();

    return {
      template: _isEditing ? getEditedPostTemplate() : null,
      isEditing: _isEditing,
      title: getEditedPostAttribute('title') ? getEditedPostAttribute('title') : Object(external_wp_i18n_["__"])('Untitled')
    };
  }, []);
  const {
    clearSelectedBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const {
    setIsEditingTemplate
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);

  if (!isEditing || !template) {
    return null;
  }

  let templateTitle = Object(external_wp_i18n_["__"])('Default');

  if (template !== null && template !== void 0 && template.title) {
    templateTitle = template.title;
  } else if (!!template) {
    templateTitle = template.slug;
  }

  const hasOptions = !!(template.custom || template.wp_id || template.description);
  return Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-template-top-area"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "edit-post-template-post-title",
    isLink: true,
    showTooltip: true,
    label: Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: Title of the referring post, e.g: "Hello World!" */
    Object(external_wp_i18n_["__"])('Edit %s'), title),
    onClick: () => {
      clearSelectedBlock();
      setIsEditingTemplate(false);
    }
  }, title), hasOptions ? Object(external_wp_element_["createElement"])(external_wp_components_["Dropdown"], {
    position: "bottom center",
    contentClassName: "edit-post-template-top-area__popover",
    renderToggle: _ref => {
      let {
        onToggle
      } = _ref;
      return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        className: "edit-post-template-title",
        isLink: true,
        icon: chevron_down["a" /* default */],
        showTooltip: true,
        onClick: onToggle,
        label: Object(external_wp_i18n_["__"])('Template Options')
      }, templateTitle);
    },
    renderContent: () => Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(EditTemplateTitle, null), Object(external_wp_element_["createElement"])(TemplateDescription, null), Object(external_wp_element_["createElement"])(DeleteTemplate, null))
  }) : Object(external_wp_element_["createElement"])(external_wp_components_["__experimentalText"], {
    className: "edit-post-template-title",
    size: "body",
    style: {
      lineHeight: '24px'
    }
  }, templateTitle));
}

/* harmony default export */ var template_title = (TemplateTitle);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */










function Header(_ref) {
  let {
    setEntitiesSavedStatesCallback
  } = _ref;
  const {
    hasActiveMetaboxes,
    isPublishSidebarOpened,
    isSaving,
    showIconLabels,
    hasReducedUI
  } = Object(external_wp_data_["useSelect"])(select => ({
    hasActiveMetaboxes: select(store["a" /* store */]).hasMetaBoxes(),
    isPublishSidebarOpened: select(store["a" /* store */]).isPublishSidebarOpened(),
    isSaving: select(store["a" /* store */]).isSavingMetaBoxes(),
    showIconLabels: select(store["a" /* store */]).isFeatureActive('showIconLabels'),
    hasReducedUI: select(store["a" /* store */]).isFeatureActive('reducedUI')
  }), []);
  const isLargeViewport = Object(external_wp_compose_["useViewportMatch"])('large');
  const classes = classnames_default()('edit-post-header', {
    'has-reduced-ui': hasReducedUI
  });
  return Object(external_wp_element_["createElement"])("div", {
    className: classes
  }, Object(external_wp_element_["createElement"])(main_dashboard_button.Slot, null, Object(external_wp_element_["createElement"])(fullscreen_mode_close, null)), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-header__toolbar"
  }, Object(external_wp_element_["createElement"])(header_toolbar, null), Object(external_wp_element_["createElement"])(template_title, null)), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-header__settings"
  }, !isPublishSidebarOpened && // This button isn't completely hidden by the publish sidebar.
  // We can't hide the whole toolbar when the publish sidebar is open because
  // we want to prevent mounting/unmounting the PostPublishButtonOrToggle DOM node.
  // We track that DOM node to return focus to the PostPublishButtonOrToggle
  // when the publish sidebar has been closed.
  Object(external_wp_element_["createElement"])(external_wp_editor_["PostSavedState"], {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    showIconLabels: showIconLabels
  }), Object(external_wp_element_["createElement"])(DevicePreview, null), Object(external_wp_element_["createElement"])(external_wp_editor_["PostPreviewButton"], {
    forceIsAutosaveable: hasActiveMetaboxes,
    forcePreviewLink: isSaving ? null : undefined
  }), Object(external_wp_element_["createElement"])(post_publish_button_or_toggle, {
    forceIsDirty: hasActiveMetaboxes,
    forceIsSaving: isSaving,
    setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
  }), (isLargeViewport || !showIconLabels) && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(build_module["h" /* PinnedItems */].Slot, {
    scope: "core/edit-post"
  }), Object(external_wp_element_["createElement"])(more_menu, {
    showIconLabels: showIconLabels
  })), showIconLabels && !isLargeViewport && Object(external_wp_element_["createElement"])(more_menu, {
    showIconLabels: showIconLabels
  })));
}

/* harmony default export */ var header = (Header);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js
var library_close = __webpack_require__("w95h");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/inserter-sidebar.js



/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function InserterSidebar() {
  const {
    insertionPoint,
    showMostUsedBlocks
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isFeatureActive,
      __experimentalGetInsertionPoint
    } = select(store["a" /* store */]);
    return {
      insertionPoint: __experimentalGetInsertionPoint(),
      showMostUsedBlocks: isFeatureActive('mostUsedBlocks')
    };
  }, []);
  const {
    setIsInserterOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const isMobileViewport = Object(external_wp_compose_["useViewportMatch"])('medium', '<');
  const [inserterDialogRef, inserterDialogProps] = Object(external_wp_compose_["__experimentalUseDialog"])({
    onClose: () => setIsInserterOpened(false)
  });
  return Object(external_wp_element_["createElement"])("div", Object(esm_extends["a" /* default */])({
    ref: inserterDialogRef
  }, inserterDialogProps, {
    className: "edit-post-editor__inserter-panel"
  }), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-editor__inserter-panel-header"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    icon: library_close["a" /* default */],
    onClick: () => setIsInserterOpened(false)
  })), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-editor__inserter-panel-content"
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__experimentalLibrary"], {
    showMostUsedBlocks: showMostUsedBlocks,
    showInserterHelpPanel: true,
    shouldFocusBlock: isMobileViewport,
    rootClientId: insertionPoint.rootClientId,
    __experimentalInsertionIndex: insertionPoint.insertionIndex,
    __experimentalFilterValue: insertionPoint.filterValue
  })));
}

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js
var close_small = __webpack_require__("bWcr");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/secondary-sidebar/list-view-sidebar.js


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function ListViewSidebar() {
  const {
    setIsListViewOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    clearSelectedBlock,
    selectBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);

  async function selectEditorBlock(clientId) {
    await clearSelectedBlock();
    selectBlock(clientId, -1);
  }

  const focusOnMountRef = Object(external_wp_compose_["useFocusOnMount"])('firstElement');
  const focusReturnRef = Object(external_wp_compose_["useFocusReturn"])();

  function closeOnEscape(event) {
    if (event.keyCode === external_wp_keycodes_["ESCAPE"] && !event.defaultPrevented) {
      event.preventDefault();
      setIsListViewOpened(false);
    }
  }

  const instanceId = Object(external_wp_compose_["useInstanceId"])(ListViewSidebar);
  const labelId = `edit-post-editor__list-view-panel-label-${instanceId}`;
  return (// eslint-disable-next-line jsx-a11y/no-static-element-interactions
    Object(external_wp_element_["createElement"])("div", {
      "aria-labelledby": labelId,
      className: "edit-post-editor__list-view-panel",
      onKeyDown: closeOnEscape
    }, Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-editor__list-view-panel-header"
    }, Object(external_wp_element_["createElement"])("strong", {
      id: labelId
    }, Object(external_wp_i18n_["__"])('List view')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      icon: close_small["a" /* default */],
      label: Object(external_wp_i18n_["__"])('Close list view sidebar'),
      onClick: () => setIsListViewOpened(false)
    })), Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-editor__list-view-panel-content",
      ref: Object(external_wp_compose_["useMergeRefs"])([focusReturnRef, focusOnMountRef])
    }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__experimentalListView"], {
      onSelect: selectEditorBlock,
      showNestedBlocks: true,
      __experimentalFeatures: true,
      __experimentalPersistentListViewFeatures: true
    })))
  );
}

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/cog.js
var cog = __webpack_require__("Cg8A");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-header/index.js


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
    openGeneralSidebar
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);

  const openDocumentSettings = () => openGeneralSidebar('edit-post/document');

  const openBlockSettings = () => openGeneralSidebar('edit-post/block');

  const {
    documentLabel,
    isTemplateMode
  } = Object(external_wp_data_["useSelect"])(select => {
    const postTypeLabel = select(external_wp_editor_["store"]).getPostTypeLabel();
    return {
      // translators: Default label for the Document sidebar tab, not selected.
      documentLabel: postTypeLabel || Object(external_wp_i18n_["_x"])('Document', 'noun'),
      isTemplateMode: select(store["a" /* store */]).isEditingTemplate()
    };
  }, []);
  const [documentAriaLabel, documentActiveClass] = sidebarName === 'edit-post/document' ? // translators: ARIA label for the Document sidebar tab, selected. %s: Document label.
  [Object(external_wp_i18n_["sprintf"])(Object(external_wp_i18n_["__"])('%s (selected)'), documentLabel), 'is-active'] : [documentLabel, ''];
  const [blockAriaLabel, blockActiveClass] = sidebarName === 'edit-post/block' ? // translators: ARIA label for the Block Settings Sidebar tab, selected.
  [Object(external_wp_i18n_["__"])('Block (selected)'), 'is-active'] : // translators: ARIA label for the Block Settings Sidebar tab, not selected.
  [Object(external_wp_i18n_["__"])('Block'), ''];
  const [templateAriaLabel, templateActiveClass] = sidebarName === 'edit-post/document' ? [Object(external_wp_i18n_["__"])('Template (selected)'), 'is-active'] : [Object(external_wp_i18n_["__"])('Template'), ''];
  /* Use a list so screen readers will announce how many tabs there are. */

  return Object(external_wp_element_["createElement"])("ul", null, !isTemplateMode && Object(external_wp_element_["createElement"])("li", null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${documentActiveClass}`,
    "aria-label": documentAriaLabel,
    "data-label": documentLabel
  }, documentLabel)), isTemplateMode && Object(external_wp_element_["createElement"])("li", null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: openDocumentSettings,
    className: `edit-post-sidebar__panel-tab ${templateActiveClass}`,
    "aria-label": templateAriaLabel,
    "data-label": Object(external_wp_i18n_["__"])('Template')
  }, Object(external_wp_i18n_["__"])('Template'))), Object(external_wp_element_["createElement"])("li", null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    onClick: openBlockSettings,
    className: `edit-post-sidebar__panel-tab ${blockActiveClass}`,
    "aria-label": blockAriaLabel // translators: Data label for the Block Settings Sidebar tab.
    ,
    "data-label": Object(external_wp_i18n_["__"])('Block')
  }, // translators: Text label for the Block Settings Sidebar tab.
  Object(external_wp_i18n_["__"])('Block'))));
};

/* harmony default export */ var settings_header = (SettingsHeader);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-visibility/index.js


/**
 * WordPress dependencies
 */



function PostVisibility() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostVisibilityCheck"], {
    render: _ref => {
      let {
        canEdit
      } = _ref;
      return Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], {
        className: "edit-post-post-visibility"
      }, Object(external_wp_element_["createElement"])("span", null, Object(external_wp_i18n_["__"])('Visibility')), !canEdit && Object(external_wp_element_["createElement"])("span", null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostVisibilityLabel"], null)), canEdit && Object(external_wp_element_["createElement"])(external_wp_components_["Dropdown"], {
        position: "bottom left",
        contentClassName: "edit-post-post-visibility__dialog",
        renderToggle: _ref2 => {
          let {
            isOpen,
            onToggle
          } = _ref2;
          return Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
            "aria-expanded": isOpen,
            className: "edit-post-post-visibility__toggle",
            onClick: onToggle,
            variant: "tertiary"
          }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostVisibilityLabel"], null));
        },
        renderContent: () => Object(external_wp_element_["createElement"])(external_wp_editor_["PostVisibility"], null)
      }));
    }
  });
}
/* harmony default export */ var post_visibility = (PostVisibility);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-trash/index.js


/**
 * WordPress dependencies
 */


function PostTrash() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostTrashCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostTrash"], null)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-schedule/index.js


/**
 * WordPress dependencies
 */




function PostSchedule() {
  const anchorRef = Object(external_wp_element_["useRef"])();
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostScheduleCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], {
    className: "edit-post-post-schedule",
    ref: anchorRef
  }, Object(external_wp_element_["createElement"])("span", null, Object(external_wp_i18n_["__"])('Publish')), Object(external_wp_element_["createElement"])(external_wp_components_["Dropdown"], {
    popoverProps: {
      anchorRef: anchorRef.current
    },
    position: "bottom left",
    contentClassName: "edit-post-post-schedule__dialog",
    renderToggle: _ref => {
      let {
        onToggle,
        isOpen
      } = _ref;
      return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
        className: "edit-post-post-schedule__toggle",
        onClick: onToggle,
        "aria-expanded": isOpen,
        variant: "tertiary"
      }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostScheduleLabel"], null)));
    },
    renderContent: () => Object(external_wp_element_["createElement"])(external_wp_editor_["PostSchedule"], null)
  })));
}
/* harmony default export */ var post_schedule = (PostSchedule);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-sticky/index.js


/**
 * WordPress dependencies
 */


function PostSticky() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostStickyCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostSticky"], null)));
}
/* harmony default export */ var post_sticky = (PostSticky);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-author/index.js


/**
 * WordPress dependencies
 */


function PostAuthor() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostAuthorCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostAuthor"], null)));
}
/* harmony default export */ var post_author = (PostAuthor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-slug/index.js


/**
 * WordPress dependencies
 */


function PostSlug() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostSlugCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostSlug"], null)));
}
/* harmony default export */ var post_slug = (PostSlug);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-format/index.js


/**
 * WordPress dependencies
 */


function PostFormat() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostFormatCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostFormat"], null)));
}
/* harmony default export */ var post_format = (PostFormat);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-pending-status/index.js


/**
 * WordPress dependencies
 */


function PostPendingStatus() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostPendingStatusCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostPendingStatus"], null)));
}
/* harmony default export */ var post_pending_status = (PostPendingStatus);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-status-info/index.js


/**
 * Defines as extensibility slot for the Status & visibility panel.
 */

/**
 * WordPress dependencies
 */

const {
  Fill: plugin_post_status_info_Fill,
  Slot: plugin_post_status_info_Slot
} = Object(external_wp_components_["createSlotFill"])('PluginPostStatusInfo');
/**
 * Renders a row in the Status & visibility panel of the Document sidebar.
 * It should be noted that this is named and implemented around the function it serves
 * and not its location, which may change in future iterations.
 *
 * @param {Object}    props             Component properties.
 * @param {string}    [props.className] An optional class name added to the row.
 * @param {WPElement} props.children    Children to be rendered.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
 *
 * function MyPluginPostStatusInfo() {
 * 	return wp.element.createElement(
 * 		PluginPostStatusInfo,
 * 		{
 * 			className: 'my-plugin-post-status-info',
 * 		},
 * 		__( 'My post status info' )
 * 	)
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPostStatusInfo } from '@wordpress/edit-post';
 *
 * const MyPluginPostStatusInfo = () => (
 * 	<PluginPostStatusInfo
 * 		className="my-plugin-post-status-info"
 * 	>
 * 		{ __( 'My post status info' ) }
 * 	</PluginPostStatusInfo>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */

const PluginPostStatusInfo = _ref => {
  let {
    children,
    className
  } = _ref;
  return Object(external_wp_element_["createElement"])(plugin_post_status_info_Fill, null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], {
    className: className
  }, children));
};

PluginPostStatusInfo.Slot = plugin_post_status_info_Slot;
/* harmony default export */ var plugin_post_status_info = (PluginPostStatusInfo);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-status/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */











/**
 * Module Constants
 */

const PANEL_NAME = 'post-status';

function PostStatus(_ref) {
  let {
    isOpened,
    onTogglePanel
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    className: "edit-post-post-status",
    title: Object(external_wp_i18n_["__"])('Status & visibility'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(plugin_post_status_info.Slot, null, fills => Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(post_visibility, null), Object(external_wp_element_["createElement"])(post_schedule, null), Object(external_wp_element_["createElement"])(post_format, null), Object(external_wp_element_["createElement"])(post_sticky, null), Object(external_wp_element_["createElement"])(post_pending_status, null), Object(external_wp_element_["createElement"])(post_slug, null), Object(external_wp_element_["createElement"])(post_author, null), fills, Object(external_wp_element_["createElement"])(PostTrash, null))));
}

/* harmony default export */ var post_status = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  // We use isEditorPanelRemoved to hide the panel if it was programatically removed. We do
  // not use isEditorPanelEnabled since this panel should not be disabled through the UI.
  const {
    isEditorPanelRemoved,
    isEditorPanelOpened
  } = select(store["a" /* store */]);
  return {
    isRemoved: isEditorPanelRemoved(PANEL_NAME),
    isOpened: isEditorPanelOpened(PANEL_NAME)
  };
}), Object(external_wp_compose_["ifCondition"])(_ref2 => {
  let {
    isRemoved
  } = _ref2;
  return !isRemoved;
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onTogglePanel() {
    return dispatch(store["a" /* store */]).toggleEditorPanelOpened(PANEL_NAME);
  }

}))])(PostStatus));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/last-revision/index.js


/**
 * WordPress dependencies
 */



function LastRevision() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostLastRevisionCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    className: "edit-post-last-revision__panel"
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostLastRevision"], null)));
}

/* harmony default export */ var last_revision = (LastRevision);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/taxonomy-panel.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function TaxonomyPanel(_ref) {
  let {
    isEnabled,
    taxonomy,
    isOpened,
    onTogglePanel,
    children
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  const taxonomyMenuName = Object(external_lodash_["get"])(taxonomy, ['labels', 'menu_name']);

  if (!taxonomyMenuName) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: taxonomyMenuName,
    opened: isOpened,
    onToggle: onTogglePanel
  }, children);
}

/* harmony default export */ var taxonomy_panel = (Object(external_wp_compose_["compose"])(Object(external_wp_data_["withSelect"])((select, ownProps) => {
  const slug = Object(external_lodash_["get"])(ownProps.taxonomy, ['slug']);
  const panelName = slug ? `taxonomy-panel-${slug}` : '';
  return {
    panelName,
    isEnabled: slug ? select(store["a" /* store */]).isEditorPanelEnabled(panelName) : false,
    isOpened: slug ? select(store["a" /* store */]).isEditorPanelOpened(panelName) : false
  };
}), Object(external_wp_data_["withDispatch"])((dispatch, ownProps) => ({
  onTogglePanel: () => {
    dispatch(store["a" /* store */]).toggleEditorPanelOpened(ownProps.panelName);
  }
})))(TaxonomyPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-taxonomies/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function PostTaxonomies() {
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostTaxonomiesCheck"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostTaxonomies"], {
    taxonomyWrapper: (content, taxonomy) => {
      return Object(external_wp_element_["createElement"])(taxonomy_panel, {
        taxonomy: taxonomy
      }, content);
    }
  }));
}

/* harmony default export */ var post_taxonomies = (PostTaxonomies);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/featured-image/index.js


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
 * Module Constants
 */

const featured_image_PANEL_NAME = 'featured-image';

function FeaturedImage(_ref) {
  let {
    isEnabled,
    isOpened,
    postType,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostFeaturedImageCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_lodash_["get"])(postType, ['labels', 'featured_image'], Object(external_wp_i18n_["__"])('Featured image')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostFeaturedImage"], null)));
}

const applyWithSelect = Object(external_wp_data_["withSelect"])(select => {
  const {
    getEditedPostAttribute
  } = select(external_wp_editor_["store"]);
  const {
    getPostType
  } = select(external_wp_coreData_["store"]);
  const {
    isEditorPanelEnabled,
    isEditorPanelOpened
  } = select(store["a" /* store */]);
  return {
    postType: getPostType(getEditedPostAttribute('type')),
    isEnabled: isEditorPanelEnabled(featured_image_PANEL_NAME),
    isOpened: isEditorPanelOpened(featured_image_PANEL_NAME)
  };
});
const applyWithDispatch = Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    toggleEditorPanelOpened
  } = dispatch(store["a" /* store */]);
  return {
    onTogglePanel: Object(external_lodash_["partial"])(toggleEditorPanelOpened, featured_image_PANEL_NAME)
  };
});
/* harmony default export */ var featured_image = (Object(external_wp_compose_["compose"])(applyWithSelect, applyWithDispatch)(FeaturedImage));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-excerpt/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const post_excerpt_PANEL_NAME = 'post-excerpt';

function PostExcerpt(_ref) {
  let {
    isEnabled,
    isOpened,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostExcerptCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_wp_i18n_["__"])('Excerpt'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostExcerpt"], null)));
}

/* harmony default export */ var post_excerpt = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    isEnabled: select(store["a" /* store */]).isEditorPanelEnabled(post_excerpt_PANEL_NAME),
    isOpened: select(store["a" /* store */]).isEditorPanelOpened(post_excerpt_PANEL_NAME)
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onTogglePanel() {
    return dispatch(store["a" /* store */]).toggleEditorPanelOpened(post_excerpt_PANEL_NAME);
  }

}))])(PostExcerpt));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/post-link/index.js


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
 * Module Constants
 */

const post_link_PANEL_NAME = 'post-link';

function PostLink(_ref) {
  let {
    isOpened,
    onTogglePanel,
    isEditable,
    postLink,
    permalinkPrefix,
    permalinkSuffix,
    editPermalink,
    postSlug,
    postTypeLabel
  } = _ref;
  const [forceEmptyField, setForceEmptyField] = Object(external_wp_element_["useState"])(false);
  let prefixElement, postNameElement, suffixElement;

  if (isEditable) {
    prefixElement = permalinkPrefix && Object(external_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-prefix"
    }, permalinkPrefix);
    postNameElement = postSlug && Object(external_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-post-name"
    }, postSlug);
    suffixElement = permalinkSuffix && Object(external_wp_element_["createElement"])("span", {
      className: "edit-post-post-link__link-suffix"
    }, permalinkSuffix);
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_wp_i18n_["__"])('Permalink'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, isEditable && Object(external_wp_element_["createElement"])("div", {
    className: "editor-post-link"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    label: Object(external_wp_i18n_["__"])('URL Slug'),
    value: forceEmptyField ? '' : postSlug,
    autoComplete: "off",
    spellCheck: "false",
    onChange: newValue => {
      editPermalink(newValue); // When we delete the field the permalink gets
      // reverted to the original value.
      // The forceEmptyField logic allows the user to have
      // the field temporarily empty while typing.

      if (!newValue) {
        if (!forceEmptyField) {
          setForceEmptyField(true);
        }

        return;
      }

      if (forceEmptyField) {
        setForceEmptyField(false);
      }
    },
    onBlur: event => {
      editPermalink(Object(external_wp_editor_["cleanForSlug"])(event.target.value));

      if (forceEmptyField) {
        setForceEmptyField(false);
      }
    }
  }), Object(external_wp_element_["createElement"])("p", null, Object(external_wp_i18n_["__"])('The last part of the URL.'), ' ', Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
    href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/writing-posts/#post-field-descriptions')
  }, Object(external_wp_i18n_["__"])('Read about permalinks')))), Object(external_wp_element_["createElement"])("h3", {
    className: "edit-post-post-link__preview-label"
  }, postTypeLabel || Object(external_wp_i18n_["__"])('View post')), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-post-link__preview-link-container"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
    className: "edit-post-post-link__link",
    href: postLink,
    target: "_blank"
  }, isEditable ? Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, prefixElement, postNameElement, suffixElement) : postLink)));
}

/* harmony default export */ var post_link = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  const {
    isPermalinkEditable,
    getCurrentPost,
    isCurrentPostPublished,
    getPermalinkParts,
    getEditedPostAttribute,
    getEditedPostSlug
  } = select(external_wp_editor_["store"]);
  const {
    isEditorPanelEnabled,
    isEditorPanelOpened
  } = select(store["a" /* store */]);
  const {
    getPostType
  } = select(external_wp_coreData_["store"]);
  const {
    link
  } = getCurrentPost();
  const postTypeName = getEditedPostAttribute('type');
  const postType = getPostType(postTypeName);
  const permalinkParts = getPermalinkParts();
  return {
    postLink: link,
    isEditable: isPermalinkEditable(),
    isPublished: isCurrentPostPublished(),
    isOpened: isEditorPanelOpened(post_link_PANEL_NAME),
    isEnabled: isEditorPanelEnabled(post_link_PANEL_NAME),
    isViewable: Object(external_lodash_["get"])(postType, ['viewable'], false),
    postSlug: Object(external_wp_url_["safeDecodeURIComponent"])(getEditedPostSlug()),
    postTypeLabel: Object(external_lodash_["get"])(postType, ['labels', 'view_item']),
    hasPermalinkParts: !!permalinkParts,
    permalinkPrefix: permalinkParts === null || permalinkParts === void 0 ? void 0 : permalinkParts.prefix,
    permalinkSuffix: permalinkParts === null || permalinkParts === void 0 ? void 0 : permalinkParts.suffix
  };
}), Object(external_wp_compose_["ifCondition"])(_ref2 => {
  let {
    isEnabled,
    postLink,
    isViewable,
    hasPermalinkParts
  } = _ref2;
  return isEnabled && postLink && isViewable && hasPermalinkParts;
}), Object(external_wp_data_["withDispatch"])(dispatch => {
  const {
    toggleEditorPanelOpened
  } = dispatch(store["a" /* store */]);
  const {
    editPost
  } = dispatch(external_wp_editor_["store"]);
  return {
    onTogglePanel: () => toggleEditorPanelOpened(post_link_PANEL_NAME),
    editPermalink: newSlug => {
      editPost({
        slug: newSlug
      });
    }
  };
})])(PostLink));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/discussion-panel/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


/**
 * Module Constants
 */

const discussion_panel_PANEL_NAME = 'discussion-panel';

function DiscussionPanel(_ref) {
  let {
    isEnabled,
    isOpened,
    onTogglePanel
  } = _ref;

  if (!isEnabled) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: ['comments', 'trackbacks']
  }, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_wp_i18n_["__"])('Discussion'),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "comments"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostComments"], null))), Object(external_wp_element_["createElement"])(external_wp_editor_["PostTypeSupportCheck"], {
    supportKeys: "trackbacks"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PostPingbacks"], null)))));
}

/* harmony default export */ var discussion_panel = (Object(external_wp_compose_["compose"])([Object(external_wp_data_["withSelect"])(select => {
  return {
    isEnabled: select(store["a" /* store */]).isEditorPanelEnabled(discussion_panel_PANEL_NAME),
    isOpened: select(store["a" /* store */]).isEditorPanelOpened(discussion_panel_PANEL_NAME)
  };
}), Object(external_wp_data_["withDispatch"])(dispatch => ({
  onTogglePanel() {
    return dispatch(store["a" /* store */]).toggleEditorPanelOpened(discussion_panel_PANEL_NAME);
  }

}))])(DiscussionPanel));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/page-attributes/index.js


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
 * Module Constants
 */

const page_attributes_PANEL_NAME = 'page-attributes';
function PageAttributes() {
  const {
    isEnabled,
    isOpened,
    postType
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostAttribute
    } = select(external_wp_editor_["store"]);
    const {
      isEditorPanelEnabled,
      isEditorPanelOpened
    } = select(store["a" /* store */]);
    const {
      getPostType
    } = select(external_wp_coreData_["store"]);
    return {
      isEnabled: isEditorPanelEnabled(page_attributes_PANEL_NAME),
      isOpened: isEditorPanelOpened(page_attributes_PANEL_NAME),
      postType: getPostType(getEditedPostAttribute('type'))
    };
  }, []);
  const {
    toggleEditorPanelOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);

  if (!isEnabled || !postType) {
    return null;
  }

  const onTogglePanel = Object(external_lodash_["partial"])(toggleEditorPanelOpened, page_attributes_PANEL_NAME);
  return Object(external_wp_element_["createElement"])(external_wp_editor_["PageAttributesCheck"], null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: Object(external_lodash_["get"])(postType, ['labels', 'attributes'], Object(external_wp_i18n_["__"])('Page attributes')),
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(external_wp_editor_["PageAttributesParent"], null), Object(external_wp_element_["createElement"])(external_wp_components_["PanelRow"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["PageAttributesOrder"], null))));
}
/* harmony default export */ var page_attributes = (PageAttributes);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js


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
 * Render metabox area.
 *
 * @param {Object} props          Component props.
 * @param {string} props.location metabox location.
 * @return {WPComponent} The component to be rendered.
 */

function MetaBoxesArea(_ref) {
  let {
    location
  } = _ref;
  const container = Object(external_wp_element_["useRef"])(null);
  const formRef = Object(external_wp_element_["useRef"])(null);
  Object(external_wp_element_["useEffect"])(() => {
    formRef.current = document.querySelector('.metabox-location-' + location);

    if (formRef.current) {
      container.current.appendChild(formRef.current);
    }

    return () => {
      if (formRef.current) {
        document.querySelector('#metaboxes').appendChild(formRef.current);
      }
    };
  }, [location]);
  const isSaving = Object(external_wp_data_["useSelect"])(select => {
    return select(store["a" /* store */]).isSavingMetaBoxes();
  }, []);
  const classes = classnames_default()('edit-post-meta-boxes-area', `is-${location}`, {
    'is-loading': isSaving
  });
  return Object(external_wp_element_["createElement"])("div", {
    className: classes
  }, isSaving && Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-meta-boxes-area__container",
    ref: container
  }), Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-meta-boxes-area__clear"
  }));
}

/* harmony default export */ var meta_boxes_area = (MetaBoxesArea);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



class meta_box_visibility_MetaBoxVisibility extends external_wp_element_["Component"] {
  componentDidMount() {
    this.updateDOM();
  }

  componentDidUpdate(prevProps) {
    if (this.props.isVisible !== prevProps.isVisible) {
      this.updateDOM();
    }
  }

  updateDOM() {
    const {
      id,
      isVisible
    } = this.props;
    const element = document.getElementById(id);

    if (!element) {
      return;
    }

    if (isVisible) {
      element.classList.remove('is-hidden');
    } else {
      element.classList.add('is-hidden');
    }
  }

  render() {
    return null;
  }

}

/* harmony default export */ var meta_box_visibility = (Object(external_wp_data_["withSelect"])((select, _ref) => {
  let {
    id
  } = _ref;
  return {
    isVisible: select(store["a" /* store */]).isEditorPanelEnabled(`meta-box-${id}`)
  };
})(meta_box_visibility_MetaBoxVisibility));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/meta-boxes/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */




function MetaBoxes(_ref) {
  let {
    location
  } = _ref;
  const registry = Object(external_wp_data_["useRegistry"])();
  const {
    metaBoxes,
    areMetaBoxesInitialized,
    isEditorReady
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      __unstableIsEditorReady
    } = select(external_wp_editor_["store"]);
    const {
      getMetaBoxesPerLocation,
      areMetaBoxesInitialized: _areMetaBoxesInitialized
    } = select(store["a" /* store */]);
    return {
      metaBoxes: getMetaBoxesPerLocation(location),
      areMetaBoxesInitialized: _areMetaBoxesInitialized(),
      isEditorReady: __unstableIsEditorReady()
    };
  }, [location]); // When editor is ready, initialize postboxes (wp core script) and metabox
  // saving. This initializes all meta box locations, not just this specific
  // one.

  Object(external_wp_element_["useEffect"])(() => {
    if (isEditorReady && !areMetaBoxesInitialized) {
      registry.dispatch(store["a" /* store */]).initializeMetaBoxes();
    }
  }, [isEditorReady, areMetaBoxesInitialized]);

  if (!areMetaBoxesInitialized) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_lodash_["map"])(metaBoxes, _ref2 => {
    let {
      id
    } = _ref2;
    return Object(external_wp_element_["createElement"])(meta_box_visibility, {
      key: id,
      id: id
    });
  }), Object(external_wp_element_["createElement"])(meta_boxes_area, {
    location: location
  }));
}

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-document-setting-panel/index.js
var plugin_document_setting_panel = __webpack_require__("xrib");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-sidebar/index.js



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
 * wp.data.dispatch( 'core/edit-post' ).openGeneralSidebar( 'plugin-name/sidebar-name' );
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
 * var PluginSidebar = wp.editPost.PluginSidebar;
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
 * import { PluginSidebar } from '@wordpress/edit-post';
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

function PluginSidebarEditPost(_ref) {
  let {
    className,
    ...props
  } = _ref;
  const {
    postTitle,
    shortcut,
    showIconLabels
  } = Object(external_wp_data_["useSelect"])(select => {
    return {
      postTitle: select(external_wp_editor_["store"]).getEditedPostAttribute('title'),
      shortcut: select(external_wp_keyboardShortcuts_["store"]).getShortcutRepresentation('core/edit-post/toggle-sidebar'),
      showIconLabels: select(store["a" /* store */]).isFeatureActive('showIconLabels')
    };
  }, []);
  return Object(external_wp_element_["createElement"])(build_module["b" /* ComplementaryArea */], Object(esm_extends["a" /* default */])({
    panelClassName: className,
    className: "edit-post-sidebar",
    smallScreenTitle: postTitle || Object(external_wp_i18n_["__"])('(no title)'),
    scope: "core/edit-post",
    toggleShortcut: shortcut,
    showIconLabels: showIconLabels
  }, props));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/template/actions.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */




function PostTemplateActions() {
  const [isModalOpen, setIsModalOpen] = Object(external_wp_element_["useState"])(false);
  const [isBusy, setIsBusy] = Object(external_wp_element_["useState"])(false);
  const [title, setTitle] = Object(external_wp_element_["useState"])('');
  const {
    template,
    supportsTemplateMode,
    defaultTemplate
  } = Object(external_wp_data_["useSelect"])(select => {
    var _getPostType$viewable, _getPostType;

    const {
      getCurrentPostType,
      getEditorSettings
    } = select(external_wp_editor_["store"]);
    const {
      getPostType
    } = select(external_wp_coreData_["store"]);
    const {
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    const isViewable = (_getPostType$viewable = (_getPostType = getPostType(getCurrentPostType())) === null || _getPostType === void 0 ? void 0 : _getPostType.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;

    const _supportsTemplateMode = getEditorSettings().supportsTemplateMode && isViewable;

    return {
      template: _supportsTemplateMode && getEditedPostTemplate(),
      supportsTemplateMode: _supportsTemplateMode,
      defaultTemplate: getEditorSettings().defaultBlockTemplate
    };
  }, []);
  const {
    __unstableCreateTemplate,
    __unstableSwitchToTemplateMode
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);

  if (!supportsTemplateMode) {
    return null;
  }

  const defaultTitle = Object(external_wp_i18n_["__"])('Custom Template');

  async function onCreateTemplate(event) {
    event.preventDefault();

    if (isBusy) {
      return;
    }

    setIsBusy(true);
    const newTemplateContent = defaultTemplate !== null && defaultTemplate !== void 0 ? defaultTemplate : Object(external_wp_blocks_["serialize"])([Object(external_wp_blocks_["createBlock"])('core/group', {
      tagName: 'header',
      layout: {
        inherit: true
      }
    }, [Object(external_wp_blocks_["createBlock"])('core/site-title'), Object(external_wp_blocks_["createBlock"])('core/site-tagline')]), Object(external_wp_blocks_["createBlock"])('core/separator'), Object(external_wp_blocks_["createBlock"])('core/group', {
      tagName: 'main'
    }, [Object(external_wp_blocks_["createBlock"])('core/group', {
      layout: {
        inherit: true
      }
    }, [Object(external_wp_blocks_["createBlock"])('core/post-title')]), Object(external_wp_blocks_["createBlock"])('core/post-content', {
      layout: {
        inherit: true
      }
    })])]);
    await __unstableCreateTemplate({
      slug: 'wp-custom-template-' + Object(external_lodash_["kebabCase"])(title || defaultTitle),
      content: newTemplateContent,
      title: title || defaultTitle
    });
    setIsBusy(false);
    setIsModalOpen(false);

    __unstableSwitchToTemplateMode(true);
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
    className: "edit-post-template__actions"
  }, !!template && Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "link",
    onClick: () => __unstableSwitchToTemplateMode()
  }, Object(external_wp_i18n_["__"])('Edit')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "link",
    onClick: () => setIsModalOpen(true)
  },
  /* translators: button to create a new template */
  Object(external_wp_i18n_["_x"])('New', 'action'))), isModalOpen && Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    title: Object(external_wp_i18n_["__"])('Create custom template'),
    closeLabel: Object(external_wp_i18n_["__"])('Close'),
    onRequestClose: () => {
      setIsModalOpen(false);
      setTitle('');
    },
    overlayClassName: "edit-post-template__modal"
  }, Object(external_wp_element_["createElement"])("form", {
    onSubmit: onCreateTemplate
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Flex"], {
    align: "flex-start",
    gap: 8
  }, Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["TextControl"], {
    label: Object(external_wp_i18n_["__"])('Name'),
    value: title,
    onChange: setTitle,
    placeholder: defaultTitle,
    disabled: isBusy,
    help: Object(external_wp_i18n_["__"])('Describe the purpose of the template, e.g. "Full Width". Custom templates can be applied to any post or page.')
  }))), Object(external_wp_element_["createElement"])(external_wp_components_["Flex"], {
    className: "edit-post-template__modal-actions",
    justify: "flex-end",
    expanded: false
  }, Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "tertiary",
    onClick: () => {
      setIsModalOpen(false);
      setTitle('');
    }
  }, Object(external_wp_i18n_["__"])('Cancel'))), Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    variant: "primary",
    type: "submit",
    isBusy: isBusy,
    "aria-disabled": isBusy
  }, Object(external_wp_i18n_["__"])('Create')))))));
}

/* harmony default export */ var actions = (PostTemplateActions);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/template/index.js


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
 * Module Constants
 */

const template_PANEL_NAME = 'template';
function TemplatePanel() {
  const {
    isEnabled,
    isOpened,
    selectedTemplate,
    availableTemplates,
    fetchedTemplates,
    isViewable,
    template,
    supportsTemplateMode,
    canUserCreate
  } = Object(external_wp_data_["useSelect"])(select => {
    var _getPostType$viewable, _getPostType;

    const {
      isEditorPanelEnabled,
      isEditorPanelOpened,
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    const {
      getEditedPostAttribute,
      getEditorSettings,
      getCurrentPostType
    } = select(external_wp_editor_["store"]);
    const {
      getPostType,
      getEntityRecords,
      canUser
    } = select(external_wp_coreData_["store"]);
    const currentPostType = getCurrentPostType();

    const _isViewable = (_getPostType$viewable = (_getPostType = getPostType(currentPostType)) === null || _getPostType === void 0 ? void 0 : _getPostType.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;

    const _supportsTemplateMode = select(external_wp_editor_["store"]).getEditorSettings().supportsTemplateMode && _isViewable;

    const wpTemplates = getEntityRecords('postType', 'wp_template', {
      post_type: currentPostType
    });
    const newAvailableTemplates = Object(external_lodash_["fromPairs"])((wpTemplates || []).map(_ref => {
      let {
        slug,
        title
      } = _ref;
      return [slug, title.rendered];
    }));
    return {
      isEnabled: isEditorPanelEnabled(template_PANEL_NAME),
      isOpened: isEditorPanelOpened(template_PANEL_NAME),
      selectedTemplate: getEditedPostAttribute('template'),
      availableTemplates: getEditorSettings().availableTemplates,
      fetchedTemplates: newAvailableTemplates,
      template: _supportsTemplateMode && getEditedPostTemplate(),
      isViewable: _isViewable,
      supportsTemplateMode: _supportsTemplateMode,
      canUserCreate: canUser('create', 'templates')
    };
  }, []);
  const templates = Object(external_wp_element_["useMemo"])(() => {
    return { ...availableTemplates,
      ...fetchedTemplates
    };
  }, [availableTemplates, fetchedTemplates]);
  const {
    toggleEditorPanelOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    editPost
  } = Object(external_wp_data_["useDispatch"])(external_wp_editor_["store"]);

  if (!isEnabled || !isViewable || Object(external_lodash_["isEmpty"])(availableTemplates) && (!supportsTemplateMode || !canUserCreate)) {
    return null;
  }

  const onTogglePanel = Object(external_lodash_["partial"])(toggleEditorPanelOpened, template_PANEL_NAME);

  let panelTitle = Object(external_wp_i18n_["__"])('Template');

  if (!!template) {
    var _template$title;

    panelTitle = Object(external_wp_i18n_["sprintf"])(
    /* translators: %s: template title */
    Object(external_wp_i18n_["__"])('Template: %s'), (_template$title = template === null || template === void 0 ? void 0 : template.title) !== null && _template$title !== void 0 ? _template$title : template.slug);
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    title: panelTitle,
    opened: isOpened,
    onToggle: onTogglePanel
  }, Object(external_wp_element_["createElement"])(external_wp_components_["SelectControl"], {
    hideLabelFromVision: true,
    label: Object(external_wp_i18n_["__"])('Template:'),
    value: Object.keys(templates).includes(selectedTemplate) ? selectedTemplate : '',
    onChange: templateSlug => {
      editPost({
        template: templateSlug || ''
      });
    },
    options: Object(external_lodash_["map"])(templates, (templateName, templateSlug) => ({
      value: templateSlug,
      label: templateName
    }))
  }), canUserCreate && Object(external_wp_element_["createElement"])(actions, null));
}
/* harmony default export */ var sidebar_template = (TemplatePanel);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/layout.js
var library_layout = __webpack_require__("Civd");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/template-summary/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function TemplateSummary() {
  const template = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    return getEditedPostTemplate();
  }, []);

  if (!template) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], null, Object(external_wp_element_["createElement"])(external_wp_components_["Flex"], {
    align: "flex-start",
    gap: "3"
  }, Object(external_wp_element_["createElement"])(external_wp_components_["FlexItem"], null, Object(external_wp_element_["createElement"])(build_module_icon["a" /* default */], {
    icon: library_layout["a" /* default */]
  })), Object(external_wp_element_["createElement"])(external_wp_components_["FlexBlock"], null, Object(external_wp_element_["createElement"])("h2", {
    className: "edit-post-template-summary__title"
  }, (template === null || template === void 0 ? void 0 : template.title) || (template === null || template === void 0 ? void 0 : template.slug)), Object(external_wp_element_["createElement"])("p", null, template === null || template === void 0 ? void 0 : template.description))));
}

/* harmony default export */ var template_summary = (TemplateSummary);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/settings-sidebar/index.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


















const SIDEBAR_ACTIVE_BY_DEFAULT = external_wp_element_["Platform"].select({
  web: true,
  native: false
});

const SettingsSidebar = () => {
  const {
    sidebarName,
    keyboardShortcut,
    isTemplateMode
  } = Object(external_wp_data_["useSelect"])(select => {
    // The settings sidebar is used by the edit-post/document and edit-post/block sidebars.
    // sidebarName represents the sidebar that is active or that should be active when the SettingsSidebar toggle button is pressed.
    // If one of the two sidebars is active the component will contain the content of that sidebar.
    // When neither of the the two sidebars is active we can not simply return null, because the PluginSidebarEditPost
    // component, besides being used to render the sidebar, also renders the toggle button. In that case sidebarName
    // should contain the sidebar that will be active when the toggle button is pressed. If a block
    // is selected, that should be edit-post/block otherwise it's edit-post/document.
    let sidebar = select(build_module["i" /* store */]).getActiveComplementaryArea(store["a" /* store */].name);

    if (!['edit-post/document', 'edit-post/block'].includes(sidebar)) {
      if (select(external_wp_blockEditor_["store"]).getBlockSelectionStart()) {
        sidebar = 'edit-post/block';
      }

      sidebar = 'edit-post/document';
    }

    const shortcut = select(external_wp_keyboardShortcuts_["store"]).getShortcutRepresentation('core/edit-post/toggle-sidebar');
    return {
      sidebarName: sidebar,
      keyboardShortcut: shortcut,
      isTemplateMode: select(store["a" /* store */]).isEditingTemplate()
    };
  }, []);
  return Object(external_wp_element_["createElement"])(PluginSidebarEditPost, {
    identifier: sidebarName,
    header: Object(external_wp_element_["createElement"])(settings_header, {
      sidebarName: sidebarName
    }),
    closeLabel: Object(external_wp_i18n_["__"])('Close settings'),
    headerClassName: "edit-post-sidebar__panel-tabs"
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    title: Object(external_wp_i18n_["__"])('Settings'),
    toggleShortcut: keyboardShortcut,
    icon: cog["a" /* default */],
    isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT
  }, !isTemplateMode && sidebarName === 'edit-post/document' && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(post_status, null), Object(external_wp_element_["createElement"])(sidebar_template, null), Object(external_wp_element_["createElement"])(plugin_document_setting_panel["a" /* default */].Slot, null), Object(external_wp_element_["createElement"])(last_revision, null), Object(external_wp_element_["createElement"])(post_link, null), Object(external_wp_element_["createElement"])(post_taxonomies, null), Object(external_wp_element_["createElement"])(featured_image, null), Object(external_wp_element_["createElement"])(post_excerpt, null), Object(external_wp_element_["createElement"])(discussion_panel, null), Object(external_wp_element_["createElement"])(page_attributes, null), Object(external_wp_element_["createElement"])(MetaBoxes, {
    location: "side"
  })), isTemplateMode && sidebarName === 'edit-post/document' && Object(external_wp_element_["createElement"])(template_summary, null), sidebarName === 'edit-post/block' && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockInspector"], null));
};

/* harmony default export */ var settings_sidebar = (SettingsSidebar);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/image.js

function WelcomeGuideImage(_ref) {
  let {
    nonAnimatedSrc,
    animatedSrc
  } = _ref;
  return Object(external_wp_element_["createElement"])("picture", {
    className: "edit-post-welcome-guide__image"
  }, Object(external_wp_element_["createElement"])("source", {
    srcSet: nonAnimatedSrc,
    media: "(prefers-reduced-motion: reduce)"
  }), Object(external_wp_element_["createElement"])("img", {
    src: animatedSrc,
    width: "312",
    height: "240",
    alt: ""
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/default.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



function WelcomeGuideDefault() {
  const {
    toggleFeature
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Guide"], {
    className: "edit-post-welcome-guide",
    contentLabel: Object(external_wp_i18n_["__"])('Welcome to the block editor'),
    finishButtonText: Object(external_wp_i18n_["__"])('Get started'),
    onFinish: () => toggleFeature('welcomeGuide'),
    pages: [{
      image: Object(external_wp_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
      }),
      content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("h1", {
        className: "edit-post-welcome-guide__heading"
      }, Object(external_wp_i18n_["__"])('Welcome to the block editor')), Object(external_wp_element_["createElement"])("p", {
        className: "edit-post-welcome-guide__text"
      }, Object(external_wp_i18n_["__"])('In the WordPress editor, each paragraph, image, or video is presented as a distinct “block” of content.')))
    }, {
      image: Object(external_wp_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
      }),
      content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("h1", {
        className: "edit-post-welcome-guide__heading"
      }, Object(external_wp_i18n_["__"])('Make each block your own')), Object(external_wp_element_["createElement"])("p", {
        className: "edit-post-welcome-guide__text"
      }, Object(external_wp_i18n_["__"])('Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected.')))
    }, {
      image: Object(external_wp_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
      }),
      content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("h1", {
        className: "edit-post-welcome-guide__heading"
      }, Object(external_wp_i18n_["__"])('Get to know the block library')), Object(external_wp_element_["createElement"])("p", {
        className: "edit-post-welcome-guide__text"
      }, Object(external_wp_element_["createInterpolateElement"])(Object(external_wp_i18n_["__"])('All of the blocks available to you live in the block library. You’ll find it wherever you see the <InserterIconImage /> icon.'), {
        InserterIconImage: Object(external_wp_element_["createElement"])("img", {
          alt: Object(external_wp_i18n_["__"])('inserter'),
          src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
        })
      })))
    }, {
      image: Object(external_wp_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
      }),
      content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("h1", {
        className: "edit-post-welcome-guide__heading"
      }, Object(external_wp_i18n_["__"])('Learn how to use the block editor')), Object(external_wp_element_["createElement"])("p", {
        className: "edit-post-welcome-guide__text"
      }, Object(external_wp_i18n_["__"])('New to the block editor? Want to learn more about using it? '), Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
        href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/')
      }, Object(external_wp_i18n_["__"])("Here's a detailed guide."))))
    }]
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/template.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function WelcomeGuideTemplate() {
  const {
    toggleFeature
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Guide"], {
    className: "edit-post-welcome-guide",
    contentLabel: Object(external_wp_i18n_["__"])('Welcome to the template editor'),
    finishButtonText: Object(external_wp_i18n_["__"])('Get started'),
    onFinish: () => toggleFeature('welcomeGuideTemplate'),
    pages: [{
      image: Object(external_wp_element_["createElement"])(WelcomeGuideImage, {
        nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.svg",
        animatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.gif"
      }),
      content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("h1", {
        className: "edit-post-welcome-guide__heading"
      }, Object(external_wp_i18n_["__"])('Welcome to the template editor')), Object(external_wp_element_["createElement"])("p", {
        className: "edit-post-welcome-guide__text"
      }, Object(external_wp_i18n_["__"])('Templates help define the layout of the site. You can customize all aspects of your posts and pages using blocks and patterns in this editor.')))
    }]
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/welcome-guide/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




function WelcomeGuide() {
  const {
    isActive,
    isTemplateMode
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      isFeatureActive,
      isEditingTemplate
    } = select(store["a" /* store */]);

    const _isTemplateMode = isEditingTemplate();

    const feature = _isTemplateMode ? 'welcomeGuideTemplate' : 'welcomeGuide';
    return {
      isActive: isFeatureActive(feature),
      isTemplateMode: _isTemplateMode
    };
  }, []);

  if (!isActive) {
    return null;
  }

  return isTemplateMode ? Object(external_wp_element_["createElement"])(WelcomeGuideTemplate, null) : Object(external_wp_element_["createElement"])(WelcomeGuideDefault, null);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-post-publish-panel/index.js


/**
 * WordPress dependencies
 */



const {
  Fill: plugin_post_publish_panel_Fill,
  Slot: plugin_post_publish_panel_Slot
} = Object(external_wp_components_["createSlotFill"])('PluginPostPublishPanel');

const PluginPostPublishPanelFill = _ref => {
  let {
    children,
    className,
    title,
    initialOpen = false,
    icon
  } = _ref;
  return Object(external_wp_element_["createElement"])(plugin_post_publish_panel_Fill, null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the post-publish panel in the publish flow
 * (side panel that opens after a user publishes the post).
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened. When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPostPublishPanel = wp.editPost.PluginPostPublishPanel;
 *
 * function MyPluginPostPublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPostPublishPanel,
 * 		{
 * 			className: 'my-plugin-post-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPostPublishPanel } from '@wordpress/edit-post';
 *
 * const MyPluginPostPublishPanel = () => (
 * 	<PluginPostPublishPanel
 * 		className="my-plugin-post-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 *         { __( 'My panel content' ) }
 * 	</PluginPostPublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginPostPublishPanel = Object(external_wp_compose_["compose"])(Object(external_wp_plugins_["withPluginContext"])((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPostPublishPanelFill);
PluginPostPublishPanel.Slot = plugin_post_publish_panel_Slot;
/* harmony default export */ var plugin_post_publish_panel = (PluginPostPublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/sidebar/plugin-pre-publish-panel/index.js


/**
 * WordPress dependencies
 */



const {
  Fill: plugin_pre_publish_panel_Fill,
  Slot: plugin_pre_publish_panel_Slot
} = Object(external_wp_components_["createSlotFill"])('PluginPrePublishPanel');

const PluginPrePublishPanelFill = _ref => {
  let {
    children,
    className,
    title,
    initialOpen = false,
    icon
  } = _ref;
  return Object(external_wp_element_["createElement"])(plugin_pre_publish_panel_Fill, null, Object(external_wp_element_["createElement"])(external_wp_components_["PanelBody"], {
    className: className,
    initialOpen: initialOpen || !title,
    title: title,
    icon: icon
  }, children));
};
/**
 * Renders provided content to the pre-publish side panel in the publish flow
 * (side panel that opens when a user first pushes "Publish" from the main editor).
 *
 * @param {Object}                props                                 Component props.
 * @param {string}                [props.className]                     An optional class name added to the panel.
 * @param {string}                [props.title]                         Title displayed at the top of the panel.
 * @param {boolean}               [props.initialOpen=false]             Whether to have the panel initially opened.
 *                                                                      When no title is provided it is always opened.
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/)
 *                                                                      icon slug string, or an SVG WP element, to be rendered when
 *                                                                      the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginPrePublishPanel = wp.editPost.PluginPrePublishPanel;
 *
 * function MyPluginPrePublishPanel() {
 * 	return wp.element.createElement(
 * 		PluginPrePublishPanel,
 * 		{
 * 			className: 'my-plugin-pre-publish-panel',
 * 			title: __( 'My panel title' ),
 * 			initialOpen: true,
 * 		},
 * 		__( 'My panel content' )
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginPrePublishPanel } from '@wordpress/edit-post';
 *
 * const MyPluginPrePublishPanel = () => (
 * 	<PluginPrePublishPanel
 * 		className="my-plugin-pre-publish-panel"
 * 		title={ __( 'My panel title' ) }
 * 		initialOpen={ true }
 * 	>
 * 	    { __( 'My panel content' ) }
 * 	</PluginPrePublishPanel>
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginPrePublishPanel = Object(external_wp_compose_["compose"])(Object(external_wp_plugins_["withPluginContext"])((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon
  };
}))(PluginPrePublishPanelFill);
PluginPrePublishPanel.Slot = plugin_pre_publish_panel_Slot;
/* harmony default export */ var plugin_pre_publish_panel = (PluginPrePublishPanel);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/actions-panel.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




const {
  Fill: actions_panel_Fill,
  Slot: actions_panel_Slot
} = Object(external_wp_components_["createSlotFill"])('ActionsPanel');
const ActionsPanelFill = actions_panel_Fill;
function ActionsPanel(_ref) {
  let {
    setEntitiesSavedStatesCallback,
    closeEntitiesSavedStates,
    isEntitiesSavedStatesOpen
  } = _ref;
  const {
    closePublishSidebar,
    togglePublishSidebar
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    publishSidebarOpened,
    hasActiveMetaboxes,
    isSavingMetaBoxes,
    hasNonPostEntityChanges
  } = Object(external_wp_data_["useSelect"])(select => {
    return {
      publishSidebarOpened: select(store["a" /* store */]).isPublishSidebarOpened(),
      hasActiveMetaboxes: select(store["a" /* store */]).hasMetaBoxes(),
      isSavingMetaBoxes: select(store["a" /* store */]).isSavingMetaBoxes(),
      hasNonPostEntityChanges: select(external_wp_editor_["store"]).hasNonPostEntityChanges()
    };
  }, []);
  const openEntitiesSavedStates = Object(external_wp_element_["useCallback"])(() => setEntitiesSavedStatesCallback(true), []); // It is ok for these components to be unmounted when not in visual use.
  // We don't want more than one present at a time, decide which to render.

  let unmountableContent;

  if (publishSidebarOpened) {
    unmountableContent = Object(external_wp_element_["createElement"])(external_wp_editor_["PostPublishPanel"], {
      onClose: closePublishSidebar,
      forceIsDirty: hasActiveMetaboxes,
      forceIsSaving: isSavingMetaBoxes,
      PrePublishExtension: plugin_pre_publish_panel.Slot,
      PostPublishExtension: plugin_post_publish_panel.Slot
    });
  } else if (hasNonPostEntityChanges) {
    unmountableContent = Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-layout__toggle-entities-saved-states-panel"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      variant: "secondary",
      className: "edit-post-layout__toggle-entities-saved-states-panel-button",
      onClick: openEntitiesSavedStates,
      "aria-expanded": false
    }, Object(external_wp_i18n_["__"])('Open save panel')));
  } else {
    unmountableContent = Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-layout__toggle-publish-panel"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      variant: "secondary",
      className: "edit-post-layout__toggle-publish-panel-button",
      onClick: togglePublishSidebar,
      "aria-expanded": false
    }, Object(external_wp_i18n_["__"])('Open publish panel')));
  } // Since EntitiesSavedStates controls its own panel, we can keep it
  // always mounted to retain its own component state (such as checkboxes).


  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, isEntitiesSavedStatesOpen && Object(external_wp_element_["createElement"])(external_wp_editor_["EntitiesSavedStates"], {
    close: closeEntitiesSavedStates
  }), Object(external_wp_element_["createElement"])(actions_panel_Slot, {
    bubblesVirtually: true
  }), !isEntitiesSavedStatesOpen && unmountableContent);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/layout/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */











/**
 * Internal dependencies
 */















const interfaceLabels = {
  secondarySidebar: Object(external_wp_i18n_["__"])('Block library'),

  /* translators: accessibility text for the editor top bar landmark region. */
  header: Object(external_wp_i18n_["__"])('Editor top bar'),

  /* translators: accessibility text for the editor content landmark region. */
  body: Object(external_wp_i18n_["__"])('Editor content'),

  /* translators: accessibility text for the editor settings landmark region. */
  sidebar: Object(external_wp_i18n_["__"])('Editor settings'),

  /* translators: accessibility text for the editor publish landmark region. */
  actions: Object(external_wp_i18n_["__"])('Editor publish'),

  /* translators: accessibility text for the editor footer landmark region. */
  footer: Object(external_wp_i18n_["__"])('Editor footer')
};

function Layout(_ref) {
  let {
    styles
  } = _ref;
  const isMobileViewport = Object(external_wp_compose_["useViewportMatch"])('medium', '<');
  const isHugeViewport = Object(external_wp_compose_["useViewportMatch"])('huge', '>=');
  const {
    openGeneralSidebar,
    closeGeneralSidebar,
    setIsInserterOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const {
    mode,
    isFullscreenActive,
    isRichEditingEnabled,
    sidebarIsOpened,
    hasActiveMetaboxes,
    hasFixedToolbar,
    previousShortcut,
    nextShortcut,
    hasBlockSelected,
    isInserterOpened,
    isListViewOpened,
    showIconLabels,
    hasReducedUI,
    showBlockBreadcrumbs,
    isTemplateMode,
    documentLabel
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getEditorSettings,
      getPostTypeLabel
    } = select(external_wp_editor_["store"]);
    const editorSettings = getEditorSettings();
    const postTypeLabel = getPostTypeLabel();
    return {
      isTemplateMode: select(store["a" /* store */]).isEditingTemplate(),
      hasFixedToolbar: select(store["a" /* store */]).isFeatureActive('fixedToolbar'),
      sidebarIsOpened: !!(select(build_module["i" /* store */]).getActiveComplementaryArea(store["a" /* store */].name) || select(store["a" /* store */]).isPublishSidebarOpened()),
      isFullscreenActive: select(store["a" /* store */]).isFeatureActive('fullscreenMode'),
      isInserterOpened: select(store["a" /* store */]).isInserterOpened(),
      isListViewOpened: select(store["a" /* store */]).isListViewOpened(),
      mode: select(store["a" /* store */]).getEditorMode(),
      isRichEditingEnabled: editorSettings.richEditingEnabled,
      hasActiveMetaboxes: select(store["a" /* store */]).hasMetaBoxes(),
      previousShortcut: select(external_wp_keyboardShortcuts_["store"]).getAllShortcutKeyCombinations('core/edit-post/previous-region'),
      nextShortcut: select(external_wp_keyboardShortcuts_["store"]).getAllShortcutKeyCombinations('core/edit-post/next-region'),
      showIconLabels: select(store["a" /* store */]).isFeatureActive('showIconLabels'),
      hasReducedUI: select(store["a" /* store */]).isFeatureActive('reducedUI'),
      showBlockBreadcrumbs: select(store["a" /* store */]).isFeatureActive('showBlockBreadcrumbs'),
      // translators: Default label for the Document in the Block Breadcrumb.
      documentLabel: postTypeLabel || Object(external_wp_i18n_["_x"])('Document', 'noun')
    };
  }, []);
  const className = classnames_default()('edit-post-layout', 'is-mode-' + mode, {
    'is-sidebar-opened': sidebarIsOpened,
    'has-fixed-toolbar': hasFixedToolbar,
    'has-metaboxes': hasActiveMetaboxes,
    'show-icon-labels': showIconLabels
  });

  const openSidebarPanel = () => openGeneralSidebar(hasBlockSelected ? 'edit-post/block' : 'edit-post/document'); // Inserter and Sidebars are mutually exclusive


  Object(external_wp_element_["useEffect"])(() => {
    if (sidebarIsOpened && !isHugeViewport) {
      setIsInserterOpened(false);
    }
  }, [sidebarIsOpened, isHugeViewport]);
  Object(external_wp_element_["useEffect"])(() => {
    if (isInserterOpened && !isHugeViewport) {
      closeGeneralSidebar();
    }
  }, [isInserterOpened, isHugeViewport]); // Local state for save panel.
  // Note 'truthy' callback implies an open panel.

  const [entitiesSavedStatesCallback, setEntitiesSavedStatesCallback] = Object(external_wp_element_["useState"])(false);
  const closeEntitiesSavedStates = Object(external_wp_element_["useCallback"])(arg => {
    if (typeof entitiesSavedStatesCallback === 'function') {
      entitiesSavedStatesCallback(arg);
    }

    setEntitiesSavedStatesCallback(false);
  }, [entitiesSavedStatesCallback]);

  const secondarySidebar = () => {
    if (mode === 'visual' && isInserterOpened) {
      return Object(external_wp_element_["createElement"])(InserterSidebar, null);
    }

    if (mode === 'visual' && isListViewOpened) {
      return Object(external_wp_element_["createElement"])(ListViewSidebar, null);
    }

    return null;
  };

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(build_module["d" /* FullscreenMode */], {
    isActive: isFullscreenActive
  }), Object(external_wp_element_["createElement"])(browser_url, null), Object(external_wp_element_["createElement"])(external_wp_editor_["UnsavedChangesWarning"], null), Object(external_wp_element_["createElement"])(external_wp_editor_["AutosaveMonitor"], null), Object(external_wp_element_["createElement"])(external_wp_editor_["LocalAutosaveMonitor"], null), Object(external_wp_element_["createElement"])(keyboard_shortcuts, null), Object(external_wp_element_["createElement"])(external_wp_editor_["EditorKeyboardShortcutsRegister"], null), Object(external_wp_element_["createElement"])(settings_sidebar, null), Object(external_wp_element_["createElement"])(build_module["e" /* InterfaceSkeleton */], {
    className: className,
    labels: interfaceLabels,
    header: Object(external_wp_element_["createElement"])(header, {
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    secondarySidebar: secondarySidebar(),
    sidebar: (!isMobileViewport || sidebarIsOpened) && Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, !isMobileViewport && !sidebarIsOpened && Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-layout__toggle-sidebar-panel"
    }, Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
      variant: "secondary",
      className: "edit-post-layout__toggle-sidebar-panel-button",
      onClick: openSidebarPanel,
      "aria-expanded": false
    }, hasBlockSelected ? Object(external_wp_i18n_["__"])('Open block settings') : Object(external_wp_i18n_["__"])('Open document settings'))), Object(external_wp_element_["createElement"])(build_module["b" /* ComplementaryArea */].Slot, {
      scope: "core/edit-post"
    })),
    notices: Object(external_wp_element_["createElement"])(external_wp_editor_["EditorSnackbars"], null),
    content: Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["EditorNotices"], null), (mode === 'text' || !isRichEditingEnabled) && Object(external_wp_element_["createElement"])(text_editor, null), isRichEditingEnabled && mode === 'visual' && Object(external_wp_element_["createElement"])(VisualEditor, {
      styles: styles
    }), !isTemplateMode && Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-layout__metaboxes"
    }, Object(external_wp_element_["createElement"])(MetaBoxes, {
      location: "normal"
    }), Object(external_wp_element_["createElement"])(MetaBoxes, {
      location: "advanced"
    })), isMobileViewport && sidebarIsOpened && Object(external_wp_element_["createElement"])(external_wp_components_["ScrollLock"], null)),
    footer: !hasReducedUI && showBlockBreadcrumbs && !isMobileViewport && isRichEditingEnabled && mode === 'visual' && Object(external_wp_element_["createElement"])("div", {
      className: "edit-post-layout__footer"
    }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockBreadcrumb"], {
      rootLabelText: documentLabel
    })),
    actions: Object(external_wp_element_["createElement"])(ActionsPanel, {
      closeEntitiesSavedStates: closeEntitiesSavedStates,
      isEntitiesSavedStatesOpen: entitiesSavedStatesCallback,
      setEntitiesSavedStatesCallback: setEntitiesSavedStatesCallback
    }),
    shortcuts: {
      previous: previousShortcut,
      next: nextShortcut
    }
  }), Object(external_wp_element_["createElement"])(PreferencesModal, null), Object(external_wp_element_["createElement"])(keyboard_shortcut_help_modal, null), Object(external_wp_element_["createElement"])(WelcomeGuide, null), Object(external_wp_element_["createElement"])(external_wp_components_["Popover"].Slot, null), Object(external_wp_element_["createElement"])(external_wp_plugins_["PluginArea"], null));
}

/* harmony default export */ var components_layout = (Layout);

// EXTERNAL MODULE: ./node_modules/@wordpress/edit-post/build-module/store/constants.js
var constants = __webpack_require__("0dt7");

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/listener-hooks.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


/**
 * This listener hook monitors for block selection and triggers the appropriate
 * sidebar state.
 *
 * @param {number} postId The current post id.
 */

const useBlockSelectionListener = postId => {
  const {
    hasBlockSelection,
    isEditorSidebarOpened
  } = Object(external_wp_data_["useSelect"])(select => ({
    hasBlockSelection: !!select(external_wp_blockEditor_["store"]).getBlockSelectionStart(),
    isEditorSidebarOpened: select(constants["a" /* STORE_NAME */]).isEditorSidebarOpened()
  }), [postId]);
  const {
    openGeneralSidebar
  } = Object(external_wp_data_["useDispatch"])(constants["a" /* STORE_NAME */]);
  Object(external_wp_element_["useEffect"])(() => {
    if (!isEditorSidebarOpened) {
      return;
    }

    if (hasBlockSelection) {
      openGeneralSidebar('edit-post/block');
    } else {
      openGeneralSidebar('edit-post/document');
    }
  }, [hasBlockSelection, isEditorSidebarOpened]);
};
/**
 * This listener hook monitors any change in permalink and updates the view
 * post link in the admin bar.
 *
 * @param {number} postId
 */

const useUpdatePostLinkListener = postId => {
  const {
    newPermalink
  } = Object(external_wp_data_["useSelect"])(select => ({
    newPermalink: select(external_wp_editor_["store"]).getCurrentPost().link
  }), [postId]);
  const nodeToUpdate = Object(external_wp_element_["useRef"])();
  Object(external_wp_element_["useEffect"])(() => {
    nodeToUpdate.current = document.querySelector(constants["c" /* VIEW_AS_PREVIEW_LINK_SELECTOR */]) || document.querySelector(constants["b" /* VIEW_AS_LINK_SELECTOR */]);
  }, [postId]);
  Object(external_wp_element_["useEffect"])(() => {
    if (!newPermalink || !nodeToUpdate.current) {
      return;
    }

    nodeToUpdate.current.setAttribute('href', newPermalink);
  }, [newPermalink]);
};

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/editor-initialization/index.js
/**
 * Internal dependencies
 */

/**
 * Data component used for initializing the editor and re-initializes
 * when postId changes or on unmount.
 *
 * @param {number} postId The id of the post.
 * @return {null} This is a data component so does not render any ui.
 */

function EditorInitialization(_ref) {
  let {
    postId
  } = _ref;
  useBlockSelectionListener(postId);
  useUpdatePostLinkListener(postId);
  return null;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/editor.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */







function Editor(_ref) {
  let {
    postId,
    postType,
    settings,
    initialEdits,
    onError,
    ...props
  } = _ref;
  const {
    hasFixedToolbar,
    focusMode,
    hasReducedUI,
    hasThemeStyles,
    post,
    preferredStyleVariations,
    hiddenBlockTypes,
    blockTypes,
    __experimentalLocalAutosaveInterval,
    keepCaretInsideBlock,
    isTemplateMode,
    template
  } = Object(external_wp_data_["useSelect"])(select => {
    var _getPostType$viewable, _getPostType;

    const {
      isFeatureActive,
      getPreference,
      __experimentalGetPreviewDeviceType,
      isEditingTemplate,
      getEditedPostTemplate
    } = select(store["a" /* store */]);
    const {
      getEntityRecord,
      getPostType,
      getEntityRecords
    } = select(external_wp_coreData_["store"]);
    const {
      getEditorSettings
    } = select(external_wp_editor_["store"]);
    const {
      getBlockTypes
    } = select(external_wp_blocks_["store"]);
    const isTemplate = ['wp_template', 'wp_template_part'].includes(postType); // Ideally the initializeEditor function should be called using the ID of the REST endpoint.
    // to avoid the special case.

    let postObject;

    if (isTemplate) {
      const posts = getEntityRecords('postType', postType, {
        wp_id: postId
      });
      postObject = posts === null || posts === void 0 ? void 0 : posts[0];
    } else {
      postObject = getEntityRecord('postType', postType, postId);
    }

    const supportsTemplateMode = getEditorSettings().supportsTemplateMode;
    const isViewable = (_getPostType$viewable = (_getPostType = getPostType(postType)) === null || _getPostType === void 0 ? void 0 : _getPostType.viewable) !== null && _getPostType$viewable !== void 0 ? _getPostType$viewable : false;
    return {
      hasFixedToolbar: isFeatureActive('fixedToolbar') || __experimentalGetPreviewDeviceType() !== 'Desktop',
      focusMode: isFeatureActive('focusMode'),
      hasReducedUI: isFeatureActive('reducedUI'),
      hasThemeStyles: isFeatureActive('themeStyles'),
      preferredStyleVariations: getPreference('preferredStyleVariations'),
      hiddenBlockTypes: getPreference('hiddenBlockTypes'),
      blockTypes: getBlockTypes(),
      __experimentalLocalAutosaveInterval: getPreference('localAutosaveInterval'),
      keepCaretInsideBlock: isFeatureActive('keepCaretInsideBlock'),
      isTemplateMode: isEditingTemplate(),
      template: supportsTemplateMode && isViewable ? getEditedPostTemplate() : null,
      post: postObject
    };
  }, [postType, postId]);
  const {
    updatePreferredStyleVariations,
    setIsInserterOpened
  } = Object(external_wp_data_["useDispatch"])(store["a" /* store */]);
  const editorSettings = Object(external_wp_element_["useMemo"])(() => {
    const result = { ...settings,
      __experimentalPreferredStyleVariations: {
        value: preferredStyleVariations,
        onChange: updatePreferredStyleVariations
      },
      hasFixedToolbar,
      focusMode,
      hasReducedUI,
      __experimentalLocalAutosaveInterval,
      // This is marked as experimental to give time for the quick inserter to mature.
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      keepCaretInsideBlock
    }; // Omit hidden block types if exists and non-empty.

    if (Object(external_lodash_["size"])(hiddenBlockTypes) > 0) {
      // Defer to passed setting for `allowedBlockTypes` if provided as
      // anything other than `true` (where `true` is equivalent to allow
      // all block types).
      const defaultAllowedBlockTypes = true === settings.allowedBlockTypes ? Object(external_lodash_["map"])(blockTypes, 'name') : settings.allowedBlockTypes || [];
      result.allowedBlockTypes = Object(external_lodash_["without"])(defaultAllowedBlockTypes, ...hiddenBlockTypes);
    }

    return result;
  }, [settings, hasFixedToolbar, focusMode, hasReducedUI, hiddenBlockTypes, blockTypes, preferredStyleVariations, __experimentalLocalAutosaveInterval, setIsInserterOpened, updatePreferredStyleVariations, keepCaretInsideBlock]);
  const styles = Object(external_wp_element_["useMemo"])(() => {
    const themeStyles = [];
    const presetStyles = [];
    settings.styles.forEach(style => {
      if (!style.__unstableType || style.__unstableType === 'theme') {
        themeStyles.push(style);
      } else {
        presetStyles.push(style);
      }
    });
    const defaultEditorStyles = [...settings.defaultEditorStyles, ...presetStyles];
    return hasThemeStyles && themeStyles.length ? settings.styles : defaultEditorStyles;
  }, [settings, hasThemeStyles]);

  if (!post) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["StrictMode"], null, Object(external_wp_element_["createElement"])(external_wp_keyboardShortcuts_["ShortcutProvider"], null, Object(external_wp_element_["createElement"])(edit_post_settings.Provider, {
    value: settings
  }, Object(external_wp_element_["createElement"])(external_wp_components_["SlotFillProvider"], null, Object(external_wp_element_["createElement"])(external_wp_editor_["EditorProvider"], Object(esm_extends["a" /* default */])({
    settings: editorSettings,
    post: post,
    initialEdits: initialEdits,
    useSubRegistry: false,
    __unstableTemplate: isTemplateMode ? template : undefined
  }, props), Object(external_wp_element_["createElement"])(external_wp_editor_["ErrorBoundary"], {
    onError: onError
  }, Object(external_wp_element_["createElement"])(EditorInitialization, {
    postId: postId
  }), Object(external_wp_element_["createElement"])(components_layout, {
    styles: styles
  }), Object(external_wp_element_["createElement"])(external_wp_components_["KeyboardShortcuts"], {
    shortcuts: prevent_event_discovery
  })), Object(external_wp_element_["createElement"])(external_wp_editor_["PostLockedModal"], null))))));
}

/* harmony default export */ var editor = (Editor);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/block-settings-menu/plugin-block-settings-menu-item.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */





const isEverySelectedBlockAllowed = (selected, allowed) => Object(external_lodash_["difference"])(selected, allowed).length === 0;
/**
 * Plugins may want to add an item to the menu either for every block
 * or only for the specific ones provided in the `allowedBlocks` component property.
 *
 * If there are multiple blocks selected the item will be rendered if every block
 * is of one allowed type (not necessarily the same).
 *
 * @param {string[]} selectedBlocks Array containing the names of the blocks selected
 * @param {string[]} allowedBlocks  Array containing the names of the blocks allowed
 * @return {boolean} Whether the item will be rendered or not.
 */


const shouldRenderItem = (selectedBlocks, allowedBlocks) => !Array.isArray(allowedBlocks) || isEverySelectedBlockAllowed(selectedBlocks, allowedBlocks);
/**
 * Renders a new item in the block settings menu.
 *
 * @param {Object}                props                 Component props.
 * @param {Array}                 [props.allowedBlocks] An array containing a list of block names for which the item should be shown. If not present, it'll be rendered for any block. If multiple blocks are selected, it'll be shown if and only if all of them are in the allowed list.
 * @param {WPBlockTypeIconRender} [props.icon]          The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element.
 * @param {string}                props.label           The menu item text.
 * @param {Function}              props.onClick         Callback function to be executed when the user click the menu item.
 * @param {boolean}               [props.small]         Whether to render the label or not.
 * @param {string}                [props.role]          The ARIA role for the menu item.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginBlockSettingsMenuItem = wp.editPost.PluginBlockSettingsMenuItem;
 *
 * function doOnClick(){
 * 	// To be called when the user clicks the menu item.
 * }
 *
 * function MyPluginBlockSettingsMenuItem() {
 * 	return wp.element.createElement(
 * 		PluginBlockSettingsMenuItem,
 * 		{
 * 			allowedBlocks: [ 'core/paragraph' ],
 * 			icon: 'dashicon-name',
 * 			label: __( 'Menu item text' ),
 * 			onClick: doOnClick,
 * 		}
 * 	);
 * }
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { __ } from '@wordpress/i18n';
 * import { PluginBlockSettingsMenuItem } from '@wordpress/edit-post';
 *
 * const doOnClick = ( ) => {
 *     // To be called when the user clicks the menu item.
 * };
 *
 * const MyPluginBlockSettingsMenuItem = () => (
 *     <PluginBlockSettingsMenuItem
 * 		allowedBlocks={ [ 'core/paragraph' ] }
 * 		icon='dashicon-name'
 * 		label={ __( 'Menu item text' ) }
 * 		onClick={ doOnClick } />
 * );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginBlockSettingsMenuItem = _ref => {
  let {
    allowedBlocks,
    icon,
    label,
    onClick,
    small,
    role
  } = _ref;
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockSettingsMenuControls"], null, _ref2 => {
    let {
      selectedBlocks,
      onClose
    } = _ref2;

    if (!shouldRenderItem(selectedBlocks, allowedBlocks)) {
      return null;
    }

    return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
      onClick: Object(external_wp_compose_["compose"])(onClick, onClose),
      icon: icon,
      label: small ? label : undefined,
      role: role
    }, !small && label);
  });
};

/* harmony default export */ var plugin_block_settings_menu_item = (PluginBlockSettingsMenuItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-more-menu-item/index.js
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
 * @param {...*}                  [props.other]                         Any additional props are passed through to the underlying [MenuItem](/packages/components/src/menu-item/README.md) component.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var __ = wp.i18n.__;
 * var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
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
 * import { PluginMoreMenuItem } from '@wordpress/edit-post';
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

/* harmony default export */ var plugin_more_menu_item = (Object(external_wp_compose_["compose"])(Object(external_wp_plugins_["withPluginContext"])((context, ownProps) => {
  return {
    icon: ownProps.icon || context.icon,
    name: 'core/edit-post/plugin-more-menu'
  };
}))(build_module["a" /* ActionItem */]));

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/components/header/plugin-sidebar-more-menu-item/index.js



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
 * var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
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
 * import { PluginSidebarMoreMenuItem } from '@wordpress/edit-post';
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
  return Object(external_wp_element_["createElement"])(build_module["c" /* ComplementaryAreaMoreMenuItem */] // Menu item is marked with unstable prop for backward compatibility.
  // @see https://github.com/WordPress/gutenberg/issues/14457
  , Object(esm_extends["a" /* default */])({
    __unstableExplicitMenuItem: true,
    scope: "core/edit-post"
  }, props));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/edit-post/build-module/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */





/**
 * Reinitializes the editor after the user chooses to reboot the editor after
 * an unhandled error occurs, replacing previously mounted editor element using
 * an initial state from prior to the crash.
 *
 * @param {Object}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {Element} target       DOM node in which editor is rendered.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function reinitializeEditor(postType, postId, target, settings, initialEdits) {
  Object(external_wp_element_["unmountComponentAtNode"])(target);
  const reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_wp_element_["render"])(Object(external_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits,
    recovery: true
  }), target);
}
/**
 * Initializes and returns an instance of Editor.
 *
 * @param {string}  id           Unique identifier for editor instance.
 * @param {string}  postType     Post type of the post to edit.
 * @param {Object}  postId       ID of the post to edit.
 * @param {?Object} settings     Editor settings object.
 * @param {Object}  initialEdits Programmatic edits to apply initially, to be
 *                               considered as non-user-initiated (bypass for
 *                               unsaved changes prompt).
 */

function initializeEditor(id, postType, postId, settings, initialEdits) {
  // Prevent adding template part in the post editor.
  // Only add the filter when the post editor is initialized, not imported.
  Object(external_wp_hooks_["addFilter"])('blockEditor.__unstableCanInsertBlockType', 'removeTemplatePartsFromInserter', (can, blockType) => {
    if (!Object(external_wp_data_["select"])(store["a" /* store */]).isEditingTemplate() && blockType.name === 'core/template-part') {
      return false;
    }

    return can;
  });
  const target = document.getElementById(id);
  const reboot = reinitializeEditor.bind(null, postType, postId, target, settings, initialEdits);
  Object(external_wp_data_["dispatch"])(build_module["i" /* store */]).setFeatureDefaults('core/edit-post', {
    fixedToolbar: false,
    welcomeGuide: true,
    fullscreenMode: true,
    showIconLabels: false,
    themeStyles: true,
    showBlockBreadcrumbs: true,
    welcomeGuideTemplate: true
  });

  Object(external_wp_data_["dispatch"])(external_wp_blocks_["store"]).__experimentalReapplyBlockTypeFilters();

  Object(external_wp_blockLibrary_["registerCoreBlocks"])();

  if (false) {} // Show a console log warning if the browser is not in Standards rendering mode.


  const documentMode = document.compatMode === 'CSS1Compat' ? 'Standards' : 'Quirks';

  if (documentMode !== 'Standards') {
    // eslint-disable-next-line no-console
    console.warn("Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins.");
  } // This is a temporary fix for a couple of issues specific to Webkit on iOS.
  // Without this hack the browser scrolls the mobile toolbar off-screen.
  // Once supported in Safari we can replace this in favor of preventScroll.
  // For details see issue #18632 and PR #18686
  // Specifically, we scroll `interface-interface-skeleton__body` to enable a fixed top toolbar.
  // But Mobile Safari forces the `html` element to scroll upwards, hiding the toolbar.


  const isIphone = window.navigator.userAgent.indexOf('iPhone') !== -1;

  if (isIphone) {
    window.addEventListener('scroll', event => {
      const editorScrollContainer = document.getElementsByClassName('interface-interface-skeleton__body')[0];

      if (event.target === document) {
        // Scroll element into view by scrolling the editor container by the same amount
        // that Mobile Safari tried to scroll the html element upwards.
        if (window.scrollY > 100) {
          editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
        } // Undo unwanted scroll on html element, but only in the visual editor.


        if (document.getElementsByClassName('is-mode-visual')[0]) {
          window.scrollTo(0, 0);
        }
      }
    });
  }

  Object(external_wp_element_["render"])(Object(external_wp_element_["createElement"])(editor, {
    settings: settings,
    onError: reboot,
    postId: postId,
    postType: postType,
    initialEdits: initialEdits
  }), target);
}













/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "gdqT":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["a11y"]; }());

/***/ }),

/***/ "hF7m":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keyboardShortcuts"]; }());

/***/ }),

/***/ "iClF":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
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
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["cloneElement"])(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ __webpack_exports__["a"] = (Icon);


/***/ }),

/***/ "jSdM":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["editor"]; }());

/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "onLe":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["notices"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
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
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}

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
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				cache.head.prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( cache.head ) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "w95h":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const close = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (close);


/***/ }),

/***/ "wduq":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const wordpress = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z"
}));
/* harmony default export */ __webpack_exports__["a"] = (wordpress);


/***/ }),

/***/ "wx14":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
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

  return _extends.apply(this, arguments);
}

/***/ }),

/***/ "xrib":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(process) {/* unused harmony export Fill */
/* unused harmony export Slot */
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("tI+e");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("K9lf");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("TvNi");
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("1ZqX");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_warning__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__("Z23Y");
/* harmony import */ var _wordpress_warning__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_warning__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _preferences_modal_options__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__("Y5n4");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__("T40v");


/**
 * Defines as extensibility slot for the Settings sidebar
 */

/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */



const {
  Fill,
  Slot
} = Object(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["createSlotFill"])('PluginDocumentSettingPanel');

const PluginDocumentSettingFill = _ref => {
  let {
    isEnabled,
    panelName,
    opened,
    onToggle,
    className,
    title,
    icon,
    children
  } = _ref;
  return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["Fragment"], null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_preferences_modal_options__WEBPACK_IMPORTED_MODULE_6__[/* EnablePluginDocumentSettingPanelOption */ "d"], {
    label: title,
    panelName: panelName
  }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Fill, null, isEnabled && Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
    className: className,
    title: title,
    icon: icon,
    opened: opened,
    onToggle: onToggle
  }, children)));
};
/**
 * Renders items below the Status & Availability panel in the Document Sidebar.
 *
 * @param {Object}                props                                 Component properties.
 * @param {string}                [props.name]                          The machine-friendly name for the panel.
 * @param {string}                [props.className]                     An optional class name added to the row.
 * @param {string}                [props.title]                         The title of the panel
 * @param {WPBlockTypeIconRender} [props.icon=inherits from the plugin] The [Dashicon](https://developer.wordpress.org/resource/dashicons/) icon slug string, or an SVG WP element, to be rendered when the sidebar is pinned to toolbar.
 *
 * @example
 * ```js
 * // Using ES5 syntax
 * var el = wp.element.createElement;
 * var __ = wp.i18n.__;
 * var registerPlugin = wp.plugins.registerPlugin;
 * var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
 *
 * function MyDocumentSettingPlugin() {
 * 	return el(
 * 		PluginDocumentSettingPanel,
 * 		{
 * 			className: 'my-document-setting-plugin',
 * 			title: 'My Panel',
 * 		},
 * 		__( 'My Document Setting Panel' )
 * 	);
 * }
 *
 * registerPlugin( 'my-document-setting-plugin', {
 * 		render: MyDocumentSettingPlugin
 * } );
 * ```
 *
 * @example
 * ```jsx
 * // Using ESNext syntax
 * import { registerPlugin } from '@wordpress/plugins';
 * import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
 *
 * const MyDocumentSettingTest = () => (
 * 		<PluginDocumentSettingPanel className="my-document-setting-plugin" title="My Panel">
 *			<p>My Document Setting Panel</p>
 *		</PluginDocumentSettingPanel>
 *	);
 *
 *  registerPlugin( 'document-setting-test', { render: MyDocumentSettingTest } );
 * ```
 *
 * @return {WPComponent} The component to be rendered.
 */


const PluginDocumentSettingPanel = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__["compose"])(Object(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_3__["withPluginContext"])((context, ownProps) => {
  if (undefined === ownProps.name) {
    typeof process !== "undefined" && process.env && "production" !== "production" ? _wordpress_warning__WEBPACK_IMPORTED_MODULE_5___default()('PluginDocumentSettingPanel requires a name property.') : void 0;
  }

  return {
    icon: ownProps.icon || context.icon,
    panelName: `${context.name}/${ownProps.name}`
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withSelect"])((select, _ref2) => {
  let {
    panelName
  } = _ref2;
  return {
    opened: select(_store__WEBPACK_IMPORTED_MODULE_7__[/* store */ "a"]).isEditorPanelOpened(panelName),
    isEnabled: select(_store__WEBPACK_IMPORTED_MODULE_7__[/* store */ "a"]).isEditorPanelEnabled(panelName)
  };
}), Object(_wordpress_data__WEBPACK_IMPORTED_MODULE_4__["withDispatch"])((dispatch, _ref3) => {
  let {
    panelName
  } = _ref3;
  return {
    onToggle() {
      return dispatch(_store__WEBPACK_IMPORTED_MODULE_7__[/* store */ "a"]).toggleEditorPanelOpened(panelName);
    }

  };
}))(PluginDocumentSettingFill);
PluginDocumentSettingPanel.Slot = Slot;
/* harmony default export */ __webpack_exports__["a"] = (PluginDocumentSettingPanel);

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__("8oxB")))

/***/ })

/******/ });