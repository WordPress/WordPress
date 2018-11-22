this["wp"] = this["wp"] || {}; this["wp"]["reduxRoutine"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./node_modules/@wordpress/redux-routine/build-module/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return _typeof; });\nfunction _typeof2(obj) { if (typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : typeof obj; }; } return _typeof2(obj); }\n\nfunction _typeof(obj) {\n  if (typeof Symbol === \"function\" && _typeof2(Symbol.iterator) === \"symbol\") {\n    _typeof = function _typeof(obj) {\n      return _typeof2(obj);\n    };\n  } else {\n    _typeof = function _typeof(obj) {\n      return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : _typeof2(obj);\n    };\n  }\n\n  return _typeof(obj);\n}\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@babel/runtime/helpers/esm/typeof.js?");

/***/ }),

/***/ "./node_modules/@wordpress/redux-routine/build-module/cast-error.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@wordpress/redux-routine/build-module/cast-error.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return castError; });\n/**\n * Casts value as an error if it's not one.\n *\n * @param {*} error The value to cast.\n *\n * @return {Error} The cast error.\n */\nfunction castError(error) {\n  if (!(error instanceof Error)) {\n    error = new Error(error);\n  }\n\n  return error;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/redux-routine/build-module/cast-error.js?");

/***/ }),

/***/ "./node_modules/@wordpress/redux-routine/build-module/index.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@wordpress/redux-routine/build-module/index.js ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return createMiddleware; });\n/* harmony import */ var _is_generator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-generator */ \"./node_modules/@wordpress/redux-routine/build-module/is-generator.js\");\n/* harmony import */ var _runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./runtime */ \"./node_modules/@wordpress/redux-routine/build-module/runtime.js\");\n/**\n * Internal dependencies\n */\n\n\n/**\n * Creates a Redux middleware, given an object of controls where each key is an\n * action type for which to act upon, the value a function which returns either\n * a promise which is to resolve when evaluation of the action should continue,\n * or a value. The value or resolved promise value is assigned on the return\n * value of the yield assignment. If the control handler returns undefined, the\n * execution is not continued.\n *\n * @param {Object} controls Object of control handlers.\n *\n * @return {Function} Co-routine runtime\n */\n\nfunction createMiddleware() {\n  var controls = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  return function (store) {\n    var runtime = Object(_runtime__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(controls, store.dispatch);\n    return function (next) {\n      return function (action) {\n        if (!Object(_is_generator__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(action)) {\n          return next(action);\n        }\n\n        return runtime(action);\n      };\n    };\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/redux-routine/build-module/index.js?");

/***/ }),

/***/ "./node_modules/@wordpress/redux-routine/build-module/is-action.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@wordpress/redux-routine/build-module/is-action.js ***!
  \*************************************************************************/
/*! exports provided: isAction, isActionOfType */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isAction\", function() { return isAction; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"isActionOfType\", function() { return isActionOfType; });\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * External imports\n */\n\n/**\n * Returns true if the given object quacks like an action.\n *\n * @param {*} object Object to test\n *\n * @return {boolean}  Whether object is an action.\n */\n\nfunction isAction(object) {\n  return Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isPlainObject\"])(object) && Object(lodash__WEBPACK_IMPORTED_MODULE_0__[\"isString\"])(object.type);\n}\n/**\n * Returns true if the given object quacks like an action and has a specific\n * action type\n *\n * @param {*}      object       Object to test\n * @param {string} expectedType The expected type for the action.\n *\n * @return {boolean} Whether object is an action and is of specific type.\n */\n\nfunction isActionOfType(object, expectedType) {\n  return isAction(object) && object.type === expectedType;\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/redux-routine/build-module/is-action.js?");

/***/ }),

/***/ "./node_modules/@wordpress/redux-routine/build-module/is-generator.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@wordpress/redux-routine/build-module/is-generator.js ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return isGenerator; });\n/**\n * Returns true if the given object is a generator, or false otherwise.\n *\n * @link https://www.ecma-international.org/ecma-262/6.0/#sec-generator-objects\n *\n * @param {*} object Object to test.\n *\n * @return {boolean} Whether object is a generator.\n */\nfunction isGenerator(object) {\n  return !!object && object[Symbol.toStringTag] === 'Generator';\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/redux-routine/build-module/is-generator.js?");

/***/ }),

/***/ "./node_modules/@wordpress/redux-routine/build-module/runtime.js":
/*!***********************************************************************!*\
  !*** ./node_modules/@wordpress/redux-routine/build-module/runtime.js ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return createRuntime; });\n/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/esm/typeof */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var rungen__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! rungen */ \"./node_modules/rungen/dist/index.js\");\n/* harmony import */ var rungen__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(rungen__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ \"lodash\");\n/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var is_promise__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! is-promise */ \"./node_modules/is-promise/index.js\");\n/* harmony import */ var is_promise__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(is_promise__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var _cast_error__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./cast-error */ \"./node_modules/@wordpress/redux-routine/build-module/cast-error.js\");\n/* harmony import */ var _is_action__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./is-action */ \"./node_modules/@wordpress/redux-routine/build-module/is-action.js\");\n\n\n/**\n * External dependencies\n */\n\n\n\n/**\n * Internal dependencies\n */\n\n\n\n/**\n * Create a co-routine runtime.\n *\n * @param {Object}    controls Object of control handlers.\n * @param {function}  dispatch Unhandled action dispatch.\n *\n * @return {function} co-routine runtime\n */\n\nfunction createRuntime() {\n  var controls = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};\n  var dispatch = arguments.length > 1 ? arguments[1] : undefined;\n  var rungenControls = Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"map\"])(controls, function (control, actionType) {\n    return function (value, next, iterate, yieldNext, yieldError) {\n      if (!Object(_is_action__WEBPACK_IMPORTED_MODULE_5__[\"isActionOfType\"])(value, actionType)) {\n        return false;\n      }\n\n      var routine = control(value);\n\n      if (is_promise__WEBPACK_IMPORTED_MODULE_3___default()(routine)) {\n        // Async control routine awaits resolution.\n        routine.then(yieldNext, function (error) {\n          return yieldError(Object(_cast_error__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(error));\n        });\n      } else {\n        next(routine);\n      }\n\n      return true;\n    };\n  });\n\n  var unhandledActionControl = function unhandledActionControl(value, next) {\n    if (!Object(_is_action__WEBPACK_IMPORTED_MODULE_5__[\"isAction\"])(value)) {\n      return false;\n    }\n\n    dispatch(value);\n    next();\n    return true;\n  };\n\n  rungenControls.push(unhandledActionControl);\n  var rungenRuntime = Object(rungen__WEBPACK_IMPORTED_MODULE_1__[\"create\"])(rungenControls);\n  return function (action) {\n    return new Promise(function (resolve, reject) {\n      return rungenRuntime(action, function (result) {\n        if (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(result) === 'object' && Object(lodash__WEBPACK_IMPORTED_MODULE_2__[\"isString\"])(result.type)) {\n          dispatch(result);\n        }\n\n        resolve(result);\n      }, reject);\n    });\n  };\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/@wordpress/redux-routine/build-module/runtime.js?");

/***/ }),

/***/ "./node_modules/is-promise/index.js":
/*!******************************************!*\
  !*** ./node_modules/is-promise/index.js ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = isPromise;\n\nfunction isPromise(obj) {\n  return !!obj && (typeof obj === 'object' || typeof obj === 'function') && typeof obj.then === 'function';\n}\n\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/is-promise/index.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/controls/async.js":
/*!****************************************************!*\
  !*** ./node_modules/rungen/dist/controls/async.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.race = exports.join = exports.fork = exports.promise = undefined;\n\nvar _is = __webpack_require__(/*! ../utils/is */ \"./node_modules/rungen/dist/utils/is.js\");\n\nvar _is2 = _interopRequireDefault(_is);\n\nvar _helpers = __webpack_require__(/*! ../utils/helpers */ \"./node_modules/rungen/dist/utils/helpers.js\");\n\nvar _dispatcher = __webpack_require__(/*! ../utils/dispatcher */ \"./node_modules/rungen/dist/utils/dispatcher.js\");\n\nvar _dispatcher2 = _interopRequireDefault(_dispatcher);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nvar promise = exports.promise = function promise(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.promise(value)) return false;\n  value.then(next, raiseNext);\n  return true;\n};\n\nvar forkedTasks = new Map();\nvar fork = exports.fork = function fork(value, next, rungen) {\n  if (!_is2.default.fork(value)) return false;\n  var task = Symbol('fork');\n  var dispatcher = (0, _dispatcher2.default)();\n  forkedTasks.set(task, dispatcher);\n  rungen(value.iterator.apply(null, value.args), function (result) {\n    return dispatcher.dispatch(result);\n  }, function (err) {\n    return dispatcher.dispatch((0, _helpers.error)(err));\n  });\n  var unsubscribe = dispatcher.subscribe(function () {\n    unsubscribe();\n    forkedTasks.delete(task);\n  });\n  next(task);\n  return true;\n};\n\nvar join = exports.join = function join(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.join(value)) return false;\n  var dispatcher = forkedTasks.get(value.task);\n  if (!dispatcher) {\n    raiseNext('join error : task not found');\n  } else {\n    (function () {\n      var unsubscribe = dispatcher.subscribe(function (result) {\n        unsubscribe();\n        next(result);\n      });\n    })();\n  }\n  return true;\n};\n\nvar race = exports.race = function race(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.race(value)) return false;\n  var finished = false;\n  var success = function success(result, k, v) {\n    if (finished) return;\n    finished = true;\n    result[k] = v;\n    next(result);\n  };\n\n  var fail = function fail(err) {\n    if (finished) return;\n    raiseNext(err);\n  };\n  if (_is2.default.array(value.competitors)) {\n    (function () {\n      var result = value.competitors.map(function () {\n        return false;\n      });\n      value.competitors.forEach(function (competitor, index) {\n        rungen(competitor, function (output) {\n          return success(result, index, output);\n        }, fail);\n      });\n    })();\n  } else {\n    (function () {\n      var result = Object.keys(value.competitors).reduce(function (p, c) {\n        p[c] = false;\n        return p;\n      }, {});\n      Object.keys(value.competitors).forEach(function (index) {\n        rungen(value.competitors[index], function (output) {\n          return success(result, index, output);\n        }, fail);\n      });\n    })();\n  }\n  return true;\n};\n\nvar subscribe = function subscribe(value, next) {\n  if (!_is2.default.subscribe(value)) return false;\n  if (!_is2.default.channel(value.channel)) {\n    throw new Error('the first argument of \"subscribe\" must be a valid channel');\n  }\n  var unsubscribe = value.channel.subscribe(function (ret) {\n    unsubscribe && unsubscribe();\n    next(ret);\n  });\n\n  return true;\n};\n\nexports.default = [promise, fork, join, race, subscribe];\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/controls/async.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/controls/builtin.js":
/*!******************************************************!*\
  !*** ./node_modules/rungen/dist/controls/builtin.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.iterator = exports.array = exports.object = exports.error = exports.any = undefined;\n\nvar _is = __webpack_require__(/*! ../utils/is */ \"./node_modules/rungen/dist/utils/is.js\");\n\nvar _is2 = _interopRequireDefault(_is);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nvar any = exports.any = function any(value, next, rungen, yieldNext) {\n  yieldNext(value);\n  return true;\n};\n\nvar error = exports.error = function error(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.error(value)) return false;\n  raiseNext(value.error);\n  return true;\n};\n\nvar object = exports.object = function object(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.all(value) || !_is2.default.obj(value.value)) return false;\n  var result = {};\n  var keys = Object.keys(value.value);\n  var count = 0;\n  var hasError = false;\n  var gotResultSuccess = function gotResultSuccess(key, ret) {\n    if (hasError) return;\n    result[key] = ret;\n    count++;\n    if (count === keys.length) {\n      yieldNext(result);\n    }\n  };\n\n  var gotResultError = function gotResultError(key, error) {\n    if (hasError) return;\n    hasError = true;\n    raiseNext(error);\n  };\n\n  keys.map(function (key) {\n    rungen(value.value[key], function (ret) {\n      return gotResultSuccess(key, ret);\n    }, function (err) {\n      return gotResultError(key, err);\n    });\n  });\n\n  return true;\n};\n\nvar array = exports.array = function array(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.all(value) || !_is2.default.array(value.value)) return false;\n  var result = [];\n  var count = 0;\n  var hasError = false;\n  var gotResultSuccess = function gotResultSuccess(key, ret) {\n    if (hasError) return;\n    result[key] = ret;\n    count++;\n    if (count === value.value.length) {\n      yieldNext(result);\n    }\n  };\n\n  var gotResultError = function gotResultError(key, error) {\n    if (hasError) return;\n    hasError = true;\n    raiseNext(error);\n  };\n\n  value.value.map(function (v, key) {\n    rungen(v, function (ret) {\n      return gotResultSuccess(key, ret);\n    }, function (err) {\n      return gotResultError(key, err);\n    });\n  });\n\n  return true;\n};\n\nvar iterator = exports.iterator = function iterator(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.iterator(value)) return false;\n  rungen(value, next, raiseNext);\n  return true;\n};\n\nexports.default = [error, iterator, array, object, any];\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/controls/builtin.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/controls/wrap.js":
/*!***************************************************!*\
  !*** ./node_modules/rungen/dist/controls/wrap.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.cps = exports.call = undefined;\n\nvar _is = __webpack_require__(/*! ../utils/is */ \"./node_modules/rungen/dist/utils/is.js\");\n\nvar _is2 = _interopRequireDefault(_is);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nfunction _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }\n\nvar call = exports.call = function call(value, next, rungen, yieldNext, raiseNext) {\n  if (!_is2.default.call(value)) return false;\n  try {\n    next(value.func.apply(value.context, value.args));\n  } catch (err) {\n    raiseNext(err);\n  }\n  return true;\n};\n\nvar cps = exports.cps = function cps(value, next, rungen, yieldNext, raiseNext) {\n  var _value$func;\n\n  if (!_is2.default.cps(value)) return false;\n  (_value$func = value.func).call.apply(_value$func, [null].concat(_toConsumableArray(value.args), [function (err, result) {\n    if (err) raiseNext(err);else next(result);\n  }]));\n  return true;\n};\n\nexports.default = [call, cps];\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/controls/wrap.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/create.js":
/*!********************************************!*\
  !*** ./node_modules/rungen/dist/create.js ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\n\nvar _builtin = __webpack_require__(/*! ./controls/builtin */ \"./node_modules/rungen/dist/controls/builtin.js\");\n\nvar _builtin2 = _interopRequireDefault(_builtin);\n\nvar _is = __webpack_require__(/*! ./utils/is */ \"./node_modules/rungen/dist/utils/is.js\");\n\nvar _is2 = _interopRequireDefault(_is);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nfunction _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }\n\nvar create = function create() {\n  var userControls = arguments.length <= 0 || arguments[0] === undefined ? [] : arguments[0];\n\n  var controls = [].concat(_toConsumableArray(userControls), _toConsumableArray(_builtin2.default));\n\n  var runtime = function runtime(input) {\n    var success = arguments.length <= 1 || arguments[1] === undefined ? function () {} : arguments[1];\n    var error = arguments.length <= 2 || arguments[2] === undefined ? function () {} : arguments[2];\n\n    var iterate = function iterate(gen) {\n      var yieldValue = function yieldValue(isError) {\n        return function (ret) {\n          try {\n            var _ref = isError ? gen.throw(ret) : gen.next(ret);\n\n            var value = _ref.value;\n            var done = _ref.done;\n\n            if (done) return success(value);\n            next(value);\n          } catch (e) {\n            return error(e);\n          }\n        };\n      };\n\n      var next = function next(ret) {\n        controls.some(function (control) {\n          return control(ret, next, runtime, yieldValue(false), yieldValue(true));\n        });\n      };\n\n      yieldValue(false)();\n    };\n\n    var iterator = _is2.default.iterator(input) ? input : regeneratorRuntime.mark(function _callee() {\n      return regeneratorRuntime.wrap(function _callee$(_context) {\n        while (1) {\n          switch (_context.prev = _context.next) {\n            case 0:\n              _context.next = 2;\n              return input;\n\n            case 2:\n              return _context.abrupt('return', _context.sent);\n\n            case 3:\n            case 'end':\n              return _context.stop();\n          }\n        }\n      }, _callee, this);\n    })();\n\n    iterate(iterator, success, error);\n  };\n\n  return runtime;\n};\n\nexports.default = create;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/create.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/index.js":
/*!*******************************************!*\
  !*** ./node_modules/rungen/dist/index.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.wrapControls = exports.asyncControls = exports.create = undefined;\n\nvar _helpers = __webpack_require__(/*! ./utils/helpers */ \"./node_modules/rungen/dist/utils/helpers.js\");\n\nObject.keys(_helpers).forEach(function (key) {\n  if (key === \"default\") return;\n  Object.defineProperty(exports, key, {\n    enumerable: true,\n    get: function get() {\n      return _helpers[key];\n    }\n  });\n});\n\nvar _create = __webpack_require__(/*! ./create */ \"./node_modules/rungen/dist/create.js\");\n\nvar _create2 = _interopRequireDefault(_create);\n\nvar _async = __webpack_require__(/*! ./controls/async */ \"./node_modules/rungen/dist/controls/async.js\");\n\nvar _async2 = _interopRequireDefault(_async);\n\nvar _wrap = __webpack_require__(/*! ./controls/wrap */ \"./node_modules/rungen/dist/controls/wrap.js\");\n\nvar _wrap2 = _interopRequireDefault(_wrap);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nexports.create = _create2.default;\nexports.asyncControls = _async2.default;\nexports.wrapControls = _wrap2.default;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/index.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/utils/dispatcher.js":
/*!******************************************************!*\
  !*** ./node_modules/rungen/dist/utils/dispatcher.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nvar createDispatcher = function createDispatcher() {\n  var listeners = [];\n\n  return {\n    subscribe: function subscribe(listener) {\n      listeners.push(listener);\n      return function () {\n        listeners = listeners.filter(function (l) {\n          return l !== listener;\n        });\n      };\n    },\n    dispatch: function dispatch(action) {\n      listeners.slice().forEach(function (listener) {\n        return listener(action);\n      });\n    }\n  };\n};\n\nexports.default = createDispatcher;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/utils/dispatcher.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/utils/helpers.js":
/*!***************************************************!*\
  !*** ./node_modules/rungen/dist/utils/helpers.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nexports.createChannel = exports.subscribe = exports.cps = exports.apply = exports.call = exports.invoke = exports.delay = exports.race = exports.join = exports.fork = exports.error = exports.all = undefined;\n\nvar _keys = __webpack_require__(/*! ./keys */ \"./node_modules/rungen/dist/utils/keys.js\");\n\nvar _keys2 = _interopRequireDefault(_keys);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nvar all = exports.all = function all(value) {\n  return {\n    type: _keys2.default.all,\n    value: value\n  };\n};\n\nvar error = exports.error = function error(err) {\n  return {\n    type: _keys2.default.error,\n    error: err\n  };\n};\n\nvar fork = exports.fork = function fork(iterator) {\n  for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {\n    args[_key - 1] = arguments[_key];\n  }\n\n  return {\n    type: _keys2.default.fork,\n    iterator: iterator,\n    args: args\n  };\n};\n\nvar join = exports.join = function join(task) {\n  return {\n    type: _keys2.default.join,\n    task: task\n  };\n};\n\nvar race = exports.race = function race(competitors) {\n  return {\n    type: _keys2.default.race,\n    competitors: competitors\n  };\n};\n\nvar delay = exports.delay = function delay(timeout) {\n  return new Promise(function (resolve) {\n    setTimeout(function () {\n      return resolve(true);\n    }, timeout);\n  });\n};\n\nvar invoke = exports.invoke = function invoke(func) {\n  for (var _len2 = arguments.length, args = Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {\n    args[_key2 - 1] = arguments[_key2];\n  }\n\n  return {\n    type: _keys2.default.call,\n    func: func,\n    context: null,\n    args: args\n  };\n};\n\nvar call = exports.call = function call(func, context) {\n  for (var _len3 = arguments.length, args = Array(_len3 > 2 ? _len3 - 2 : 0), _key3 = 2; _key3 < _len3; _key3++) {\n    args[_key3 - 2] = arguments[_key3];\n  }\n\n  return {\n    type: _keys2.default.call,\n    func: func,\n    context: context,\n    args: args\n  };\n};\n\nvar apply = exports.apply = function apply(func, context, args) {\n  return {\n    type: _keys2.default.call,\n    func: func,\n    context: context,\n    args: args\n  };\n};\n\nvar cps = exports.cps = function cps(func) {\n  for (var _len4 = arguments.length, args = Array(_len4 > 1 ? _len4 - 1 : 0), _key4 = 1; _key4 < _len4; _key4++) {\n    args[_key4 - 1] = arguments[_key4];\n  }\n\n  return {\n    type: _keys2.default.cps,\n    func: func,\n    args: args\n  };\n};\n\nvar subscribe = exports.subscribe = function subscribe(channel) {\n  return {\n    type: _keys2.default.subscribe,\n    channel: channel\n  };\n};\n\nvar createChannel = exports.createChannel = function createChannel(callback) {\n  var listeners = [];\n  var subscribe = function subscribe(l) {\n    listeners.push(l);\n    return function () {\n      return listeners.splice(listeners.indexOf(l), 1);\n    };\n  };\n  var next = function next(val) {\n    return listeners.forEach(function (l) {\n      return l(val);\n    });\n  };\n  callback(next);\n\n  return {\n    subscribe: subscribe\n  };\n};\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/utils/helpers.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/utils/is.js":
/*!**********************************************!*\
  !*** ./node_modules/rungen/dist/utils/is.js ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\n\nvar _typeof = typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol ? \"symbol\" : typeof obj; };\n\nvar _keys = __webpack_require__(/*! ./keys */ \"./node_modules/rungen/dist/utils/keys.js\");\n\nvar _keys2 = _interopRequireDefault(_keys);\n\nfunction _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }\n\nvar is = {\n  obj: function obj(value) {\n    return (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && !!value;\n  },\n  all: function all(value) {\n    return is.obj(value) && value.type === _keys2.default.all;\n  },\n  error: function error(value) {\n    return is.obj(value) && value.type === _keys2.default.error;\n  },\n  array: Array.isArray,\n  func: function func(value) {\n    return typeof value === 'function';\n  },\n  promise: function promise(value) {\n    return value && is.func(value.then);\n  },\n  iterator: function iterator(value) {\n    return value && is.func(value.next) && is.func(value.throw);\n  },\n  fork: function fork(value) {\n    return is.obj(value) && value.type === _keys2.default.fork;\n  },\n  join: function join(value) {\n    return is.obj(value) && value.type === _keys2.default.join;\n  },\n  race: function race(value) {\n    return is.obj(value) && value.type === _keys2.default.race;\n  },\n  call: function call(value) {\n    return is.obj(value) && value.type === _keys2.default.call;\n  },\n  cps: function cps(value) {\n    return is.obj(value) && value.type === _keys2.default.cps;\n  },\n  subscribe: function subscribe(value) {\n    return is.obj(value) && value.type === _keys2.default.subscribe;\n  },\n  channel: function channel(value) {\n    return is.obj(value) && is.func(value.subscribe);\n  }\n};\n\nexports.default = is;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/utils/is.js?");

/***/ }),

/***/ "./node_modules/rungen/dist/utils/keys.js":
/*!************************************************!*\
  !*** ./node_modules/rungen/dist/utils/keys.js ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nvar keys = {\n  all: Symbol('all'),\n  error: Symbol('error'),\n  fork: Symbol('fork'),\n  join: Symbol('join'),\n  race: Symbol('race'),\n  call: Symbol('call'),\n  cps: Symbol('cps'),\n  subscribe: Symbol('subscribe')\n};\n\nexports.default = keys;\n\n//# sourceURL=webpack://wp.%5Bname%5D/./node_modules/rungen/dist/utils/keys.js?");

/***/ }),

/***/ "lodash":
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("(function() { module.exports = this[\"lodash\"]; }());\n\n//# sourceURL=webpack://wp.%5Bname%5D/external_%22lodash%22?");

/***/ })

/******/ })["default"];