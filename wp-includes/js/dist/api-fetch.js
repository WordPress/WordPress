this["wp"] = this["wp"] || {}; this["wp"]["apiFetch"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/api-fetch/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _asyncToGenerator; });\nfunction asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {\n  try {\n    var info = gen[key](arg);\n    var value = info.value;\n  } catch (error) {\n    reject(error);\n    return;\n  }\n\n  if (info.done) {\n    resolve(value);\n  } else {\n    Promise.resolve(value).then(_next, _throw);\n  }\n}\n\nfunction _asyncToGenerator(fn) {\n  return function () {\n    var self = this,\n        args = arguments;\n    return new Promise(function (resolve, reject) {\n      var gen = fn.apply(self, args);\n\n      function _next(value) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"next\", value);\n      }\n\n      function _throw(err) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"throw\", err);\n      }\n\n      _next(undefined);\n    });\n  };\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _defineProperty; });\nfunction _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectSpread.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectSpread.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectSpread; });\n/* harmony import */ var _defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n\nfunction _objectSpread(target) {\n  for (var i = 1; i < arguments.length; i++) {\n    var source = arguments[i] != null ? arguments[i] : {};\n    var ownKeys = Object.keys(source);\n\n    if (typeof Object.getOwnPropertySymbols === 'function') {\n      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {\n        return Object.getOwnPropertyDescriptor(source, sym).enumerable;\n      }));\n    }\n\n    ownKeys.forEach(function (key) {\n      Object(_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(target, key, source[key]);\n    });\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectWithoutProperties; });\n/* harmony import */ var _objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./objectWithoutPropertiesLoose */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js\");\n\nfunction _objectWithoutProperties(source, excluded) {\n  if (source == null) return {};\n  var target = Object(_objectWithoutPropertiesLoose__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(source, excluded);\n  var key, i;\n\n  if (Object.getOwnPropertySymbols) {\n    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);\n\n    for (i = 0; i < sourceSymbolKeys.length; i++) {\n      key = sourceSymbolKeys[i];\n      if (excluded.indexOf(key) >= 0) continue;\n      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;\n      target[key] = source[key];\n    }\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _objectWithoutPropertiesLoose; });\nfunction _objectWithoutPropertiesLoose(source, excluded) {\n  if (source == null) return {};\n  var target = {};\n  var sourceKeys = Object.keys(source);\n  var key, i;\n\n  for (i = 0; i < sourceKeys.length; i++) {\n    key = sourceKeys[i];\n    if (excluded.indexOf(key) >= 0) continue;\n    target[key] = source[key];\n  }\n\n  return target;\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/index.js ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ \"@wordpress/i18n\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _middlewares_nonce__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./middlewares/nonce */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js\");\n/* harmony import */ var _middlewares_root_url__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./middlewares/root-url */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js\");\n/* harmony import */ var _middlewares_preloading__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./middlewares/preloading */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js\");\n/* harmony import */ var _middlewares_fetch_all_middleware__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./middlewares/fetch-all-middleware */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js\");\n/* harmony import */ var _middlewares_namespace_endpoint__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./middlewares/namespace-endpoint */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js\");\n/* harmony import */ var _middlewares_http_v1__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./middlewares/http-v1 */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js\");\n/* harmony import */ var _middlewares_user_locale__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./middlewares/user-locale */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js\");\n\n\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n\n\n\n\n\n\n\n/**\n * Default set of header values which should be sent with every request unless\n * explicitly provided through apiFetch options.\n *\n * @type {Object}\n */\n\nvar DEFAULT_HEADERS = {\n  // The backend uses the Accept header as a condition for considering an\n  // incoming request as a REST request.\n  //\n  // See: https://core.trac.wordpress.org/ticket/44534\n  Accept: 'application/json, */*;q=0.1'\n};\n/**\n * Default set of fetch option values which should be sent with every request\n * unless explicitly provided through apiFetch options.\n *\n * @type {Object}\n */\n\nvar DEFAULT_OPTIONS = {\n  credentials: 'include'\n};\nvar middlewares = [];\n\nfunction registerMiddleware(middleware) {\n  middlewares.push(middleware);\n}\n\nfunction apiFetch(options) {\n  var raw = function raw(nextOptions) {\n    var url = nextOptions.url,\n        path = nextOptions.path,\n        data = nextOptions.data,\n        _nextOptions$parse = nextOptions.parse,\n        parse = _nextOptions$parse === void 0 ? true : _nextOptions$parse,\n        remainingOptions = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(nextOptions, [\"url\", \"path\", \"data\", \"parse\"]);\n\n    var body = nextOptions.body,\n        headers = nextOptions.headers; // Merge explicitly-provided headers with default values.\n\n    headers = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, DEFAULT_HEADERS, headers); // The `data` property is a shorthand for sending a JSON body.\n\n    if (data) {\n      body = JSON.stringify(data);\n      headers['Content-Type'] = 'application/json';\n    }\n\n    var responsePromise = window.fetch(url || path, Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, DEFAULT_OPTIONS, remainingOptions, {\n      body: body,\n      headers: headers\n    }));\n\n    var checkStatus = function checkStatus(response) {\n      if (response.status >= 200 && response.status < 300) {\n        return response;\n      }\n\n      throw response;\n    };\n\n    var parseResponse = function parseResponse(response) {\n      if (parse) {\n        if (response.status === 204) {\n          return null;\n        }\n\n        return response.json ? response.json() : Promise.reject(response);\n      }\n\n      return response;\n    };\n\n    return responsePromise.then(checkStatus).then(parseResponse).catch(function (response) {\n      if (!parse) {\n        throw response;\n      }\n\n      var invalidJsonError = {\n        code: 'invalid_json',\n        message: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__[\"__\"])('The response is not a valid JSON response.')\n      };\n\n      if (!response || !response.json) {\n        throw invalidJsonError;\n      }\n\n      return response.json().catch(function () {\n        throw invalidJsonError;\n      }).then(function (error) {\n        var unknownError = {\n          code: 'unknown_error',\n          message: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__[\"__\"])('An unknown error occurred.')\n        };\n        throw error || unknownError;\n      });\n    });\n  };\n\n  var steps = [raw, _middlewares_fetch_all_middleware__WEBPACK_IMPORTED_MODULE_6__[\"default\"], _middlewares_http_v1__WEBPACK_IMPORTED_MODULE_8__[\"default\"], _middlewares_namespace_endpoint__WEBPACK_IMPORTED_MODULE_7__[\"default\"], _middlewares_user_locale__WEBPACK_IMPORTED_MODULE_9__[\"default\"]].concat(middlewares).reverse();\n\n  var runMiddleware = function runMiddleware(index) {\n    return function (nextOptions) {\n      var nextMiddleware = steps[index];\n      var next = runMiddleware(index + 1);\n      return nextMiddleware(nextOptions, next);\n    };\n  };\n\n  return runMiddleware(0)(options);\n}\n\napiFetch.use = registerMiddleware;\napiFetch.createNonceMiddleware = _middlewares_nonce__WEBPACK_IMPORTED_MODULE_3__[\"default\"];\napiFetch.createPreloadingMiddleware = _middlewares_preloading__WEBPACK_IMPORTED_MODULE_5__[\"default\"];\napiFetch.createRootURLMiddleware = _middlewares_root_url__WEBPACK_IMPORTED_MODULE_4__[\"default\"];\napiFetch.fetchAllMiddleware = _middlewares_fetch_all_middleware__WEBPACK_IMPORTED_MODULE_6__[\"default\"];\n/* harmony default export */ __webpack_exports__[\"default\"] = (apiFetch);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js":
/*!********************************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectWithoutProperties */ \"./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_3__);\n\n\n\n\n/**\n * WordPress dependencies\n */\n // Apply query arguments to both URL and Path, whichever is present.\n\nvar modifyQuery = function modifyQuery(_ref, queryArgs) {\n  var path = _ref.path,\n      url = _ref.url,\n      options = Object(_babel_runtime_helpers_esm_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_ref, [\"path\", \"url\"]);\n\n  return Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__[\"default\"])({}, options, {\n    url: url && Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_3__[\"addQueryArgs\"])(url, queryArgs),\n    path: path && Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_3__[\"addQueryArgs\"])(path, queryArgs)\n  });\n}; // Duplicates parsing functionality from apiFetch.\n\n\nvar parseResponse = function parseResponse(response) {\n  return response.json ? response.json() : Promise.reject(response);\n};\n\nvar parseLinkHeader = function parseLinkHeader(linkHeader) {\n  if (!linkHeader) {\n    return {};\n  }\n\n  var match = linkHeader.match(/<([^>]+)>; rel=\"next\"/);\n  return match ? {\n    next: match[1]\n  } : {};\n};\n\nvar getNextPageUrl = function getNextPageUrl(response) {\n  var _parseLinkHeader = parseLinkHeader(response.headers.get('link')),\n      next = _parseLinkHeader.next;\n\n  return next;\n};\n\nvar requestContainsUnboundedQuery = function requestContainsUnboundedQuery(options) {\n  var pathIsUnbounded = options.path && options.path.indexOf('per_page=-1') !== -1;\n  var urlIsUnbounded = options.url && options.url.indexOf('per_page=-1') !== -1;\n  return pathIsUnbounded || urlIsUnbounded;\n}; // The REST API enforces an upper limit on the per_page option. To handle large\n// collections, apiFetch consumers can pass `per_page=-1`; this middleware will\n// then recursively assemble a full response array from all available pages.\n\n\nvar fetchAllMiddleware =\n/*#__PURE__*/\nfunction () {\n  var _ref2 = Object(_babel_runtime_helpers_esm_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(\n  /*#__PURE__*/\n  regeneratorRuntime.mark(function _callee(options, next) {\n    var response, results, nextPage, mergedResults, nextResponse, nextResults;\n    return regeneratorRuntime.wrap(function _callee$(_context) {\n      while (1) {\n        switch (_context.prev = _context.next) {\n          case 0:\n            if (!(options.parse === false)) {\n              _context.next = 2;\n              break;\n            }\n\n            return _context.abrupt(\"return\", next(options));\n\n          case 2:\n            if (requestContainsUnboundedQuery(options)) {\n              _context.next = 4;\n              break;\n            }\n\n            return _context.abrupt(\"return\", next(options));\n\n          case 4:\n            _context.next = 6;\n            return next(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__[\"default\"])({}, modifyQuery(options, {\n              per_page: 100\n            }), {\n              // Ensure headers are returned for page 1.\n              parse: false\n            }));\n\n          case 6:\n            response = _context.sent;\n            _context.next = 9;\n            return parseResponse(response);\n\n          case 9:\n            results = _context.sent;\n\n            if (Array.isArray(results)) {\n              _context.next = 12;\n              break;\n            }\n\n            return _context.abrupt(\"return\", results);\n\n          case 12:\n            nextPage = getNextPageUrl(response);\n\n            if (nextPage) {\n              _context.next = 15;\n              break;\n            }\n\n            return _context.abrupt(\"return\", results);\n\n          case 15:\n            // Iteratively fetch all remaining pages until no \"next\" header is found.\n            mergedResults = [].concat(results);\n\n          case 16:\n            if (!nextPage) {\n              _context.next = 27;\n              break;\n            }\n\n            _context.next = 19;\n            return next(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_1__[\"default\"])({}, options, {\n              // Ensure the URL for the next page is used instead of any provided path.\n              path: undefined,\n              url: nextPage,\n              // Ensure we still get headers so we can identify the next page.\n              parse: false\n            }));\n\n          case 19:\n            nextResponse = _context.sent;\n            _context.next = 22;\n            return parseResponse(nextResponse);\n\n          case 22:\n            nextResults = _context.sent;\n            mergedResults = mergedResults.concat(nextResults);\n            nextPage = getNextPageUrl(nextResponse);\n            _context.next = 16;\n            break;\n\n          case 27:\n            return _context.abrupt(\"return\", mergedResults);\n\n          case 28:\n          case \"end\":\n            return _context.stop();\n        }\n      }\n    }, _callee, this);\n  }));\n\n  return function fetchAllMiddleware(_x, _x2) {\n    return _ref2.apply(this, arguments);\n  };\n}();\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (fetchAllMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n\n\n/**\n * Set of HTTP methods which are eligible to be overridden.\n *\n * @type {Set}\n */\nvar OVERRIDE_METHODS = new Set(['PATCH', 'PUT', 'DELETE']);\n/**\n * Default request method.\n *\n * \"A request has an associated method (a method). Unless stated otherwise it\n * is `GET`.\"\n *\n * @see  https://fetch.spec.whatwg.org/#requests\n *\n * @type {string}\n */\n\nvar DEFAULT_METHOD = 'GET';\n/**\n * API Fetch middleware which overrides the request method for HTTP v1\n * compatibility leveraging the REST API X-HTTP-Method-Override header.\n *\n * @param {Object}   options Fetch options.\n * @param {Function} next    [description]\n *\n * @return {*} The evaluated result of the remaining middleware chain.\n */\n\nfunction httpV1Middleware(options, next) {\n  var _options = options,\n      _options$method = _options.method,\n      method = _options$method === void 0 ? DEFAULT_METHOD : _options$method;\n\n  if (OVERRIDE_METHODS.has(method.toUpperCase())) {\n    options = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, options, {\n      headers: Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, options.headers, {\n        'X-HTTP-Method-Override': method,\n        'Content-Type': 'application/json'\n      }),\n      method: 'POST'\n    });\n  }\n\n  return next(options, next);\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (httpV1Middleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js":
/*!******************************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n\n\nvar namespaceAndEndpointMiddleware = function namespaceAndEndpointMiddleware(options, next) {\n  var path = options.path;\n  var namespaceTrimmed, endpointTrimmed;\n\n  if (typeof options.namespace === 'string' && typeof options.endpoint === 'string') {\n    namespaceTrimmed = options.namespace.replace(/^\\/|\\/$/g, '');\n    endpointTrimmed = options.endpoint.replace(/^\\//, '');\n\n    if (endpointTrimmed) {\n      path = namespaceTrimmed + '/' + endpointTrimmed;\n    } else {\n      path = namespaceTrimmed;\n    }\n  }\n\n  delete options.namespace;\n  delete options.endpoint;\n  return next(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, options, {\n    path: path\n  }));\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (namespaceAndEndpointMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/hooks */ \"@wordpress/hooks\");\n/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__);\n\n\n/**\n * External dependencies\n */\n\n\nvar createNonceMiddleware = function createNonceMiddleware(nonce) {\n  var usedNonce = nonce;\n  /**\n   * This is not ideal but it's fine for now.\n   *\n   * Configure heartbeat to refresh the wp-api nonce, keeping the editor\n   * authorization intact.\n   */\n\n  Object(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_1__[\"addAction\"])('heartbeat.tick', 'core/api-fetch/create-nonce-middleware', function (response) {\n    if (response['rest-nonce']) {\n      usedNonce = response['rest-nonce'];\n    }\n  });\n  return function (options, next) {\n    var headers = options.headers || {}; // If an 'X-WP-Nonce' header (or any case-insensitive variation\n    // thereof) was specified, no need to add a nonce header.\n\n    var addNonceHeader = true;\n\n    for (var headerName in headers) {\n      if (headers.hasOwnProperty(headerName)) {\n        if (headerName.toLowerCase() === 'x-wp-nonce') {\n          addNonceHeader = false;\n          break;\n        }\n      }\n    }\n\n    if (addNonceHeader) {\n      // Do not mutate the original headers object, if any.\n      headers = Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, headers, {\n        'X-WP-Nonce': usedNonce\n      });\n    }\n\n    return next(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, options, {\n      headers: headers\n    }));\n  };\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (createNonceMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js":
/*!**********************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\nvar createPreloadingMiddleware = function createPreloadingMiddleware(preloadedData) {\n  return function (options, next) {\n    function getStablePath(path) {\n      var splitted = path.split('?');\n      var query = splitted[1];\n      var base = splitted[0];\n\n      if (!query) {\n        return base;\n      } // 'b=1&c=2&a=5'\n\n\n      return base + '?' + query // [ 'b=1', 'c=2', 'a=5' ]\n      .split('&') // [ [ 'b, '1' ], [ 'c', '2' ], [ 'a', '5' ] ]\n      .map(function (entry) {\n        return entry.split('=');\n      }) // [ [ 'a', '5' ], [ 'b, '1' ], [ 'c', '2' ] ]\n      .sort(function (a, b) {\n        return a[0].localeCompare(b[0]);\n      }) // [ 'a=5', 'b=1', 'c=2' ]\n      .map(function (pair) {\n        return pair.join('=');\n      }) // 'a=5&b=1&c=2'\n      .join('&');\n    }\n\n    var _options$parse = options.parse,\n        parse = _options$parse === void 0 ? true : _options$parse;\n\n    if (typeof options.path === 'string') {\n      var method = options.method || 'GET';\n      var path = getStablePath(options.path);\n\n      if (parse && 'GET' === method && preloadedData[path]) {\n        return Promise.resolve(preloadedData[path].body);\n      } else if ('OPTIONS' === method && preloadedData[method][path]) {\n        return Promise.resolve(preloadedData[method][path]);\n      }\n    }\n\n    return next(options);\n  };\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (createPreloadingMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js":
/*!********************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/objectSpread */ \"./node_modules/@babel/runtime/helpers/esm/objectSpread.js\");\n/* harmony import */ var _namespace_endpoint__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./namespace-endpoint */ \"./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js\");\n\n\n/**\n * Internal dependencies\n */\n\n\nvar createRootURLMiddleware = function createRootURLMiddleware(rootURL) {\n  return function (options, next) {\n    return Object(_namespace_endpoint__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(options, function (optionsWithPath) {\n      var url = optionsWithPath.url;\n      var path = optionsWithPath.path;\n      var apiRoot;\n\n      if (typeof path === 'string') {\n        apiRoot = rootURL;\n\n        if (-1 !== rootURL.indexOf('?')) {\n          path = path.replace('?', '&');\n        }\n\n        path = path.replace(/^\\//, ''); // API root may already include query parameter prefix if site is\n        // configured to use plain permalinks.\n\n        if ('string' === typeof apiRoot && -1 !== apiRoot.indexOf('?')) {\n          path = path.replace('?', '&');\n        }\n\n        url = apiRoot + path;\n      }\n\n      return next(Object(_babel_runtime_helpers_esm_objectSpread__WEBPACK_IMPORTED_MODULE_0__[\"default\"])({}, optionsWithPath, {\n        url: url\n      }));\n    });\n  };\n};\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (createRootURLMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js?");

/***/ }),

/***/ "./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js":
/*!***********************************************************************************!*\
  !*** ./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/url */ \"@wordpress/url\");\n/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * WordPress dependencies\n */\n\n\nfunction userLocaleMiddleware(options, next) {\n  if (typeof options.url === 'string' && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__[\"hasQueryArg\"])(options.url, '_locale')) {\n    options.url = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__[\"addQueryArgs\"])(options.url, {\n      _locale: 'user'\n    });\n  }\n\n  if (typeof options.path === 'string' && !Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__[\"hasQueryArg\"])(options.path, '_locale')) {\n    options.path = Object(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__[\"addQueryArgs\"])(options.path, {\n      _locale: 'user'\n    });\n  }\n\n  return next(options, next);\n}\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (userLocaleMiddleware);\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js?");

/***/ }),

/***/ "@wordpress/hooks":
/*!****************************************!*\
  !*** external {"this":["wp","hooks"]} ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"hooks\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22hooks%22%5D%7D?");

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"i18n\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22i18n%22%5D%7D?");

/***/ }),

/***/ "@wordpress/url":
/*!**************************************!*\
  !*** external {"this":["wp","url"]} ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"url\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22url%22%5D%7D?");

/***/ })

/******/ })["default"];