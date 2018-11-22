this["wp"] = this["wp"] || {}; this["wp"]["notices"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/notices/build/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _arrayWithoutHoles(arr) {\n  if (Array.isArray(arr)) {\n    for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {\n      arr2[i] = arr[i];\n    }\n\n    return arr2;\n  }\n}\n\nmodule.exports = _arrayWithoutHoles;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/defineProperty.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _defineProperty(obj, key, value) {\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n\n  return obj;\n}\n\nmodule.exports = _defineProperty;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _interopRequireDefault(obj) {\n  return obj && obj.__esModule ? obj : {\n    default: obj\n  };\n}\n\nmodule.exports = _interopRequireDefault;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/interopRequireDefault.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/interopRequireWildcard.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/interopRequireWildcard.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _interopRequireWildcard(obj) {\n  if (obj && obj.__esModule) {\n    return obj;\n  } else {\n    var newObj = {};\n\n    if (obj != null) {\n      for (var key in obj) {\n        if (Object.prototype.hasOwnProperty.call(obj, key)) {\n          var desc = Object.defineProperty && Object.getOwnPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : {};\n\n          if (desc.get || desc.set) {\n            Object.defineProperty(newObj, key, desc);\n          } else {\n            newObj[key] = obj[key];\n          }\n        }\n      }\n    }\n\n    newObj.default = obj;\n    return newObj;\n  }\n}\n\nmodule.exports = _interopRequireWildcard;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/interopRequireWildcard.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _iterableToArray(iter) {\n  if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === \"[object Arguments]\") return Array.from(iter);\n}\n\nmodule.exports = _iterableToArray;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/iterableToArray.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function _nonIterableSpread() {\n  throw new TypeError(\"Invalid attempt to spread non-iterable instance\");\n}\n\nmodule.exports = _nonIterableSpread;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/nonIterableSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/objectSpread.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/objectSpread.js ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var defineProperty = __webpack_require__(/*! ./defineProperty */ \"./node_modules/@babel/runtime/helpers/defineProperty.js\");\n\nfunction _objectSpread(target) {\n  for (var i = 1; i < arguments.length; i++) {\n    var source = arguments[i] != null ? arguments[i] : {};\n    var ownKeys = Object.keys(source);\n\n    if (typeof Object.getOwnPropertySymbols === 'function') {\n      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {\n        return Object.getOwnPropertyDescriptor(source, sym).enumerable;\n      }));\n    }\n\n    ownKeys.forEach(function (key) {\n      defineProperty(target, key, source[key]);\n    });\n  }\n\n  return target;\n}\n\nmodule.exports = _objectSpread;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/objectSpread.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles */ \"./node_modules/@babel/runtime/helpers/arrayWithoutHoles.js\");\n\nvar iterableToArray = __webpack_require__(/*! ./iterableToArray */ \"./node_modules/@babel/runtime/helpers/iterableToArray.js\");\n\nvar nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread */ \"./node_modules/@babel/runtime/helpers/nonIterableSpread.js\");\n\nfunction _toConsumableArray(arr) {\n  return arrayWithoutHoles(arr) || iterableToArray(arr) || nonIterableSpread();\n}\n\nmodule.exports = _toConsumableArray;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/toConsumableArray.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/index.js":
/*!********************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/index.js ***!
  \********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n__webpack_require__(/*! ./store */ \"./node_modules/@wordpress/notices/build/store/index.js\");\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/actions.js":
/*!****************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/actions.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.createNotice = createNotice;\nexports.createSuccessNotice = createSuccessNotice;\nexports.createInfoNotice = createInfoNotice;\nexports.createErrorNotice = createErrorNotice;\nexports.createWarningNotice = createWarningNotice;\nexports.removeNotice = removeNotice;\n\nvar _lodash = __webpack_require__(/*! lodash */ \"lodash\");\n\nvar _constants = __webpack_require__(/*! ./constants */ \"./node_modules/@wordpress/notices/build/store/constants.js\");\n\nvar _marked =\n/*#__PURE__*/\nregeneratorRuntime.mark(createNotice);\n\n/**\n * Yields action objects used in signalling that a notice is to be created.\n *\n * @param {?string}                status                Notice status.\n *                                                       Defaults to `info`.\n * @param {string}                 content               Notice message.\n * @param {?Object}                options               Notice options.\n * @param {?string}                options.context       Context under which to\n *                                                       group notice.\n * @param {?string}                options.id            Identifier for notice.\n *                                                       Automatically assigned\n *                                                       if not specified.\n * @param {?boolean}               options.isDismissible Whether the notice can\n *                                                       be dismissed by user.\n *                                                       Defaults to `true`.\n * @param {?boolean}               options.speak         Whether the notice\n *                                                       content should be\n *                                                       announced to screen\n *                                                       readers. Defaults to\n *                                                       `true`.\n * @param {?Array<WPNoticeAction>} options.actions       User actions to be\n *                                                       presented with notice.\n */\nfunction createNotice() {\n  var status,\n      content,\n      options,\n      _options$speak,\n      speak,\n      _options$isDismissibl,\n      isDismissible,\n      _options$context,\n      context,\n      _options$id,\n      id,\n      _options$actions,\n      actions,\n      __unstableHTML,\n      _args = arguments;\n\n  return regeneratorRuntime.wrap(function createNotice$(_context) {\n    while (1) {\n      switch (_context.prev = _context.next) {\n        case 0:\n          status = _args.length > 0 && _args[0] !== undefined ? _args[0] : _constants.DEFAULT_STATUS;\n          content = _args.length > 1 ? _args[1] : undefined;\n          options = _args.length > 2 && _args[2] !== undefined ? _args[2] : {};\n          _options$speak = options.speak, speak = _options$speak === void 0 ? true : _options$speak, _options$isDismissibl = options.isDismissible, isDismissible = _options$isDismissibl === void 0 ? true : _options$isDismissibl, _options$context = options.context, context = _options$context === void 0 ? _constants.DEFAULT_CONTEXT : _options$context, _options$id = options.id, id = _options$id === void 0 ? (0, _lodash.uniqueId)(context) : _options$id, _options$actions = options.actions, actions = _options$actions === void 0 ? [] : _options$actions, __unstableHTML = options.__unstableHTML; // The supported value shape of content is currently limited to plain text\n          // strings. To avoid setting expectation that e.g. a WPElement could be\n          // supported, cast to a string.\n\n          content = String(content);\n\n          if (!speak) {\n            _context.next = 8;\n            break;\n          }\n\n          _context.next = 8;\n          return {\n            type: 'SPEAK',\n            message: content\n          };\n\n        case 8:\n          _context.next = 10;\n          return {\n            type: 'CREATE_NOTICE',\n            context: context,\n            notice: {\n              id: id,\n              status: status,\n              content: content,\n              __unstableHTML: __unstableHTML,\n              isDismissible: isDismissible,\n              actions: actions\n            }\n          };\n\n        case 10:\n        case \"end\":\n          return _context.stop();\n      }\n    }\n  }, _marked, this);\n}\n/**\n * Returns an action object used in signalling that a success notice is to be\n * created. Refer to `createNotice` for options documentation.\n *\n * @see createNotice\n *\n * @param {string}  content Notice message.\n * @param {?Object} options Optional notice options.\n *\n * @return {Object} Action object.\n */\n\n\nfunction createSuccessNotice(content, options) {\n  return createNotice('success', content, options);\n}\n/**\n * Returns an action object used in signalling that an info notice is to be\n * created. Refer to `createNotice` for options documentation.\n *\n * @see createNotice\n *\n * @param {string}  content Notice message.\n * @param {?Object} options Optional notice options.\n *\n * @return {Object} Action object.\n */\n\n\nfunction createInfoNotice(content, options) {\n  return createNotice('info', content, options);\n}\n/**\n * Returns an action object used in signalling that an error notice is to be\n * created. Refer to `createNotice` for options documentation.\n *\n * @see createNotice\n *\n * @param {string}  content Notice message.\n * @param {?Object} options Optional notice options.\n *\n * @return {Object} Action object.\n */\n\n\nfunction createErrorNotice(content, options) {\n  return createNotice('error', content, options);\n}\n/**\n * Returns an action object used in signalling that a warning notice is to be\n * created. Refer to `createNotice` for options documentation.\n *\n * @see createNotice\n *\n * @param {string}  content Notice message.\n * @param {?Object} options Optional notice options.\n *\n * @return {Object} Action object.\n */\n\n\nfunction createWarningNotice(content, options) {\n  return createNotice('warning', content, options);\n}\n/**\n * Returns an action object used in signalling that a notice is to be removed.\n *\n * @param {string}  id      Notice unique identifier.\n * @param {?string} context Optional context (grouping) in which the notice is\n *                          intended to appear. Defaults to default context.\n *\n * @return {Object} Action object.\n */\n\n\nfunction removeNotice(id) {\n  var context = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _constants.DEFAULT_CONTEXT;\n  return {\n    type: 'REMOVE_NOTICE',\n    id: id,\n    context: context\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/actions.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/constants.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/constants.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.DEFAULT_STATUS = exports.DEFAULT_CONTEXT = void 0;\n\n/**\n * Default context to use for notice grouping when not otherwise specified. Its\n * specific value doesn't hold much meaning, but it must be reasonably unique\n * and, more importantly, referenced consistently in the store implementation.\n *\n * @type {string}\n */\nvar DEFAULT_CONTEXT = 'global';\n/**\n * Default notice status.\n *\n * @type {string}\n */\n\nexports.DEFAULT_CONTEXT = DEFAULT_CONTEXT;\nvar DEFAULT_STATUS = 'info';\nexports.DEFAULT_STATUS = DEFAULT_STATUS;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/constants.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/controls.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/controls.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.default = void 0;\n\nvar _a11y = __webpack_require__(/*! @wordpress/a11y */ \"@wordpress/a11y\");\n\n/**\n * WordPress dependencies\n */\nvar _default = {\n  SPEAK: function SPEAK(action) {\n    (0, _a11y.speak)(action.message, 'assertive');\n  }\n};\nexports.default = _default;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/controls.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/index.js":
/*!**************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/index.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _interopRequireWildcard = __webpack_require__(/*! @babel/runtime/helpers/interopRequireWildcard */ \"./node_modules/@babel/runtime/helpers/interopRequireWildcard.js\");\n\nvar _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ \"./node_modules/@babel/runtime/helpers/interopRequireDefault.js\");\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.default = void 0;\n\nvar _data = __webpack_require__(/*! @wordpress/data */ \"@wordpress/data\");\n\nvar _reducer = _interopRequireDefault(__webpack_require__(/*! ./reducer */ \"./node_modules/@wordpress/notices/build/store/reducer.js\"));\n\nvar actions = _interopRequireWildcard(__webpack_require__(/*! ./actions */ \"./node_modules/@wordpress/notices/build/store/actions.js\"));\n\nvar selectors = _interopRequireWildcard(__webpack_require__(/*! ./selectors */ \"./node_modules/@wordpress/notices/build/store/selectors.js\"));\n\nvar _controls = _interopRequireDefault(__webpack_require__(/*! ./controls */ \"./node_modules/@wordpress/notices/build/store/controls.js\"));\n\n/**\n * WordPress dependencies\n */\n\n/**\n * Internal dependencies\n */\nvar _default = (0, _data.registerStore)('core/notices', {\n  reducer: _reducer.default,\n  actions: actions,\n  selectors: selectors,\n  controls: _controls.default\n});\n\nexports.default = _default;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/reducer.js":
/*!****************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/reducer.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ \"./node_modules/@babel/runtime/helpers/interopRequireDefault.js\");\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.default = void 0;\n\nvar _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ \"./node_modules/@babel/runtime/helpers/toConsumableArray.js\"));\n\nvar _lodash = __webpack_require__(/*! lodash */ \"lodash\");\n\nvar _onSubKey = _interopRequireDefault(__webpack_require__(/*! ./utils/on-sub-key */ \"./node_modules/@wordpress/notices/build/store/utils/on-sub-key.js\"));\n\n/**\n * External dependencies\n */\n\n/**\n * Internal dependencies\n */\n\n/**\n * Reducer returning the next notices state. The notices state is an object\n * where each key is a context, its value an array of notice objects.\n *\n * @param {Object} state  Current state.\n * @param {Object} action Dispatched action.\n *\n * @return {Object} Updated state.\n */\nvar notices = (0, _onSubKey.default)('context')(function () {\n  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];\n  var action = arguments.length > 1 ? arguments[1] : undefined;\n\n  switch (action.type) {\n    case 'CREATE_NOTICE':\n      // Avoid duplicates on ID.\n      return (0, _toConsumableArray2.default)((0, _lodash.reject)(state, {\n        id: action.notice.id\n      })).concat([action.notice]);\n\n    case 'REMOVE_NOTICE':\n      return (0, _lodash.reject)(state, {\n        id: action.id\n      });\n  }\n\n  return state;\n});\nvar _default = notices;\nexports.default = _default;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/reducer.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/selectors.js":
/*!******************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/selectors.js ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.getNotices = getNotices;\n\nvar _constants = __webpack_require__(/*! ./constants */ \"./node_modules/@wordpress/notices/build/store/constants.js\");\n\n/**\n * Internal dependencies\n */\n\n/**\n * The default empty set of notices to return when there are no notices\n * assigned for a given notices context. This can occur if the getNotices\n * selector is called without a notice ever having been created for the\n * context. A shared value is used to ensure referential equality between\n * sequential selector calls, since otherwise `[] !== []`.\n *\n * @type {Array}\n */\nvar DEFAULT_NOTICES = [];\n/**\n * Notice object.\n *\n * @property {string}  id               Unique identifier of notice.\n * @property {string}  status           Status of notice, one of `success`,\n *                                      `info`, `error`, or `warning`. Defaults\n *                                      to `info`.\n * @property {string}  content          Notice message.\n * @property {string}  __unstableHTML   Notice message as raw HTML. Intended to\n *                                      serve primarily for compatibility of\n *                                      server-rendered notices, and SHOULD NOT\n *                                      be used for notices. It is subject to\n *                                      removal without notice.\n * @property {boolean} isDismissible    Whether the notice can be dismissed by\n *                                      user. Defaults to `true`.\n * @property {WPNoticeAction[]} actions User actions to present with notice.\n *\n * @typedef {WPNotice}\n */\n\n/**\n * Object describing a user action option associated with a notice.\n *\n * @property {string}    label    Message to use as action label.\n * @property {?string}   url      Optional URL of resource if action incurs\n *                                browser navigation.\n * @property {?Function} callback Optional function to invoke when action is\n *                                triggered by user.\n *\n * @typedef {WPNoticeAction}\n */\n\n/**\n * Returns all notices as an array, optionally for a given context. Defaults to\n * the global context.\n *\n * @param {Object}  state   Notices state.\n * @param {?string} context Optional grouping context.\n *\n * @return {WPNotice[]} Array of notices.\n */\n\nfunction getNotices(state) {\n  var context = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : _constants.DEFAULT_CONTEXT;\n  return state[context] || DEFAULT_NOTICES;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/selectors.js?");

/***/ }),

/***/ "./node_modules/@wordpress/notices/build/store/utils/on-sub-key.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/notices/build/store/utils/on-sub-key.js ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ \"./node_modules/@babel/runtime/helpers/interopRequireDefault.js\");\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.default = exports.onSubKey = void 0;\n\nvar _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ \"./node_modules/@babel/runtime/helpers/defineProperty.js\"));\n\nvar _objectSpread3 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectSpread */ \"./node_modules/@babel/runtime/helpers/objectSpread.js\"));\n\n/**\n * Higher-order reducer creator which creates a combined reducer object, keyed\n * by a property on the action object.\n *\n * @param {string} actionProperty Action property by which to key object.\n *\n * @return {Function} Higher-order reducer.\n */\nvar onSubKey = function onSubKey(actionProperty) {\n  return function (reducer) {\n    return function () {\n      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n      var action = arguments.length > 1 ? arguments[1] : undefined;\n      // Retrieve subkey from action. Do not track if undefined; useful for cases\n      // where reducer is scoped by action shape.\n      var key = action[actionProperty];\n\n      if (key === undefined) {\n        return state;\n      } // Avoid updating state if unchanged. Note that this also accounts for a\n      // reducer which returns undefined on a key which is not yet tracked.\n\n\n      var nextKeyState = reducer(state[key], action);\n\n      if (nextKeyState === state[key]) {\n        return state;\n      }\n\n      return (0, _objectSpread3.default)({}, state, (0, _defineProperty2.default)({}, key, nextKeyState));\n    };\n  };\n};\n\nexports.onSubKey = onSubKey;\nvar _default = onSubKey;\nexports.default = _default;\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/notices/build/store/utils/on-sub-key.js?");

/***/ }),

/***/ "@wordpress/a11y":
/*!***************************************!*\
  !*** external {"this":["wp","a11y"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"a11y\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22a11y%22%5D%7D?");

/***/ }),

/***/ "@wordpress/data":
/*!***************************************!*\
  !*** external {"this":["wp","data"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"wp\"][\"data\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%7B%22this%22:%5B%22wp%22,%22data%22%5D%7D?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ });