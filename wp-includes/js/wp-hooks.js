this["wp"] = this["wp"] || {}; this["wp"]["hooks"] =
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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 13);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Validate a hookName string.
 *
 * @param  {string} hookName The hook name to validate. Should be a non empty string containing
 *                           only numbers, letters, dashes, periods and underscores. Also,
 *                           the hook name cannot begin with `__`.
 *
 * @return {bool}            Whether the hook name is valid.
 */
function validateHookName(hookName) {

	if ('string' !== typeof hookName || '' === hookName) {
		console.error('The hook name must be a non-empty string.');
		return false;
	}

	if (/^__/.test(hookName)) {
		console.error('The hook name cannot begin with `__`.');
		return false;
	}

	if (!/^[a-zA-Z][a-zA-Z0-9_.-]*$/.test(hookName)) {
		console.error('The hook name can only contain numbers, letters, dashes, periods and underscores.');
		return false;
	}

	return true;
}

exports.default = validateHookName;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Validate a namespace string.
 *
 * @param  {string} namespace The namespace to validate - should take the form
 *                            `vendorName/pluginName/functionName`.
 *
 * @return {bool}             Whether the namespace is valid.
 */
function validateNamespace(namespace) {

	if ('string' !== typeof namespace || '' === namespace) {
		console.error('The namespace must be a non-empty string.');
		return false;
	}

	if (!/^[a-zA-Z][a-zA-Z0-9_.-/]*$/.test(namespace)) {
		console.error('The namespace can only contain numbers, letters, dashes, periods and underscores, plus the forward slash dividing slug and description in the namespace.');
		return false;
	}

	if (!/^[a-zA-Z][a-zA-Z0-9_.-]*\/[a-zA-Z][a-zA-Z0-9_.-]*\/[a-zA-Z][a-zA-Z0-9_.-]*$/.test(namespace)) {
		console.error('The namespace must take the form `vendor/plugin/function`.');
		return false;
	}

	return true;
}

exports.default = validateNamespace;

/***/ }),
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.didFilter = exports.didAction = exports.doingFilter = exports.doingAction = exports.currentFilter = exports.currentAction = exports.applyFilters = exports.doAction = exports.removeAllFilters = exports.removeAllActions = exports.hasFilter = exports.hasAction = exports.removeFilter = exports.removeAction = exports.addFilter = exports.addAction = undefined;

var _hooks = __webpack_require__(14);

var _hooks2 = _interopRequireDefault(_hooks);

var _createAddHook = __webpack_require__(15);

var _createAddHook2 = _interopRequireDefault(_createAddHook);

var _createRemoveHook = __webpack_require__(16);

var _createRemoveHook2 = _interopRequireDefault(_createRemoveHook);

var _createHasHook = __webpack_require__(17);

var _createHasHook2 = _interopRequireDefault(_createHasHook);

var _createRunHook = __webpack_require__(18);

var _createRunHook2 = _interopRequireDefault(_createRunHook);

var _createCurrentHook = __webpack_require__(19);

var _createCurrentHook2 = _interopRequireDefault(_createCurrentHook);

var _createDoingHook = __webpack_require__(20);

var _createDoingHook2 = _interopRequireDefault(_createDoingHook);

var _createDidHook = __webpack_require__(21);

var _createDidHook2 = _interopRequireDefault(_createDidHook);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// Add action/filter functions.
var addAction = exports.addAction = (0, _createAddHook2.default)(_hooks2.default.actions);
var addFilter = exports.addFilter = (0, _createAddHook2.default)(_hooks2.default.filters);

// Remove action/filter functions.
var removeAction = exports.removeAction = (0, _createRemoveHook2.default)(_hooks2.default.actions);
var removeFilter = exports.removeFilter = (0, _createRemoveHook2.default)(_hooks2.default.filters);

// Has action/filter functions.
var hasAction = exports.hasAction = (0, _createHasHook2.default)(_hooks2.default.actions);
var hasFilter = exports.hasFilter = (0, _createHasHook2.default)(_hooks2.default.filters);

// Remove all actions/filters functions.
var removeAllActions = exports.removeAllActions = (0, _createRemoveHook2.default)(_hooks2.default.actions, true);
var removeAllFilters = exports.removeAllFilters = (0, _createRemoveHook2.default)(_hooks2.default.filters, true);

// Do action/apply filters functions.
var doAction = exports.doAction = (0, _createRunHook2.default)(_hooks2.default.actions);
var applyFilters = exports.applyFilters = (0, _createRunHook2.default)(_hooks2.default.filters, true);

// Current action/filter functions.
var currentAction = exports.currentAction = (0, _createCurrentHook2.default)(_hooks2.default.actions);
var currentFilter = exports.currentFilter = (0, _createCurrentHook2.default)(_hooks2.default.filters);

// Doing action/filter: true while a hook is being run.
var doingAction = exports.doingAction = (0, _createDoingHook2.default)(_hooks2.default.actions);
var doingFilter = exports.doingFilter = (0, _createDoingHook2.default)(_hooks2.default.filters);

// Did action/filter functions.
var didAction = exports.didAction = (0, _createDidHook2.default)(_hooks2.default.actions);
var didFilter = exports.didFilter = (0, _createDidHook2.default)(_hooks2.default.filters);

/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
/**
 * Contains the registered hooks, keyed by hook type. Each hook type is an
 * array of objects with priority and callback of each registered hook.
 */
var HOOKS = {
  actions: {},
  filters: {}
};

exports.default = HOOKS;

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _validateNamespace = __webpack_require__(1);

var _validateNamespace2 = _interopRequireDefault(_validateNamespace);

var _validateHookName = __webpack_require__(0);

var _validateHookName2 = _interopRequireDefault(_validateHookName);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Returns a function which, when invoked, will add a hook.
 *
 * @param  {Object}   hooks Stored hooks, keyed by hook name.
 *
 * @return {Function}       Function that adds a new hook.
 */
function createAddHook(hooks) {
	/**
  * Adds the hook to the appropriate hooks container.
  *
  * @param {string}   hookName  Name of hook to add
  * @param {string}   namespace The unique namespace identifying the callback in the form `vendorName/pluginName/functionName`.
  * @param {Function} callback  Function to call when the hook is run
  * @param {?number}  priority  Priority of this hook (default=10)
  */
	return function addHook(hookName, namespace, callback) {
		var priority = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 10;


		if (!(0, _validateHookName2.default)(hookName)) {
			return;
		}

		if (!(0, _validateNamespace2.default)(namespace)) {
			return;
		}

		if ('function' !== typeof callback) {
			console.error('The hook callback must be a function.');
			return;
		}

		// Validate numeric priority
		if ('number' !== typeof priority) {
			console.error('If specified, the hook priority must be a number.');
			return;
		}

		var handler = { callback: callback, priority: priority, namespace: namespace };

		if (hooks.hasOwnProperty(hookName)) {
			// Find the correct insert index of the new hook.
			var handlers = hooks[hookName].handlers;
			var i = 0;
			while (i < handlers.length) {
				if (handlers[i].priority > priority) {
					break;
				}
				i++;
			}
			// Insert (or append) the new hook.
			handlers.splice(i, 0, handler);
			// We may also be currently executing this hook.  If the callback
			// we're adding would come after the current callback, there's no
			// problem; otherwise we need to increase the execution index of
			// any other runs by 1 to account for the added element.
			(hooks.__current || []).forEach(function (hookInfo) {
				if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
					hookInfo.currentIndex++;
				}
			});
		} else {
			// This is the first hook of its type.
			hooks[hookName] = {
				handlers: [handler],
				runs: 0
			};
		}
	};
}

exports.default = createAddHook;

/***/ }),
/* 16 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _validateNamespace = __webpack_require__(1);

var _validateNamespace2 = _interopRequireDefault(_validateNamespace);

var _validateHookName = __webpack_require__(0);

var _validateHookName2 = _interopRequireDefault(_validateHookName);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Returns a function which, when invoked, will remove a specified hook or all
 * hooks by the given name.
 *
 * @param  {Object}   hooks      Stored hooks, keyed by hook name.
 * @param  {bool}     removeAll  Whether to remove all callbacks for a hookName, without regard to namespace. Used to create `removeAll*` functions.
 *
 * @return {Function}            Function that removes hooks.
 */
function createRemoveHook(hooks, removeAll) {
	/**
  * Removes the specified callback (or all callbacks) from the hook with a
  * given hookName and namespace.
  *
  * @param {string}    hookName  The name of the hook to modify.
  * @param {string}    namespace The unique namespace identifying the callback in the form `vendorName/pluginName/functionName`.
  *
  * @return {number}             The number of callbacks removed.
  */
	return function removeHook(hookName, namespace) {

		if (!(0, _validateHookName2.default)(hookName)) {
			return;
		}

		if (!removeAll && !(0, _validateNamespace2.default)(namespace)) {
			return;
		}

		// Bail if no hooks exist by this name
		if (!hooks.hasOwnProperty(hookName)) {
			return 0;
		}

		var handlersRemoved = 0;

		if (removeAll) {
			handlersRemoved = hooks[hookName].handlers.length;
			hooks[hookName] = {
				runs: hooks[hookName].runs,
				handlers: []
			};
		} else {
			// Try to find the specified callback to remove.
			var handlers = hooks[hookName].handlers;

			var _loop = function _loop(i) {
				if (handlers[i].namespace === namespace) {
					handlers.splice(i, 1);
					handlersRemoved++;
					// This callback may also be part of a hook that is
					// currently executing.  If the callback we're removing
					// comes after the current callback, there's no problem;
					// otherwise we need to decrease the execution index of any
					// other runs by 1 to account for the removed element.
					(hooks.__current || []).forEach(function (hookInfo) {
						if (hookInfo.name === hookName && hookInfo.currentIndex >= i) {
							hookInfo.currentIndex--;
						}
					});
				}
			};

			for (var i = handlers.length - 1; i >= 0; i--) {
				_loop(i);
			}
		}

		return handlersRemoved;
	};
}

exports.default = createRemoveHook;

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Returns a function which, when invoked, will return whether any handlers are
 * attached to a particular hook.
 *
 * @param  {Object}   hooks Stored hooks, keyed by hook name.
 *
 * @return {Function}       Function that returns whether any handlers are
 *                          attached to a particular hook.
 */
function createHasHook(hooks) {
	/**
  * Returns how many handlers are attached for the given hook.
  *
  * @param  {string}  hookName The name of the hook to check for.
  *
  * @return {number}           The number of handlers that are attached to
  *                            the given hook.
  */
	return function hasHook(hookName) {
		return hooks.hasOwnProperty(hookName) ? hooks[hookName].handlers.length : 0;
	};
}

exports.default = createHasHook;

/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _validateHookName = __webpack_require__(0);

var _validateHookName2 = _interopRequireDefault(_validateHookName);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Returns a function which, when invoked, will execute all callbacks
 * registered to a hook of the specified type, optionally returning the final
 * value of the call chain.
 *
 * @param  {Object}   hooks          Stored hooks, keyed by hook name.
 * @param  {?bool}    returnFirstArg Whether each hook callback is expected to
 *                                   return its first argument.
 *
 * @return {Function}                Function that runs hook callbacks.
 */
function createRunHook(hooks, returnFirstArg) {
	/**
  * Runs all callbacks for the specified hook.
  *
  * @param  {string} hookName The name of the hook to run.
  * @param  {...*}   args     Arguments to pass to the hook callbacks.
  *
  * @return {*}               Return value of runner, if applicable.
  */
	return function runHooks(hookName) {

		if (!(0, _validateHookName2.default)(hookName)) {
			return;
		}

		if (!hooks.hasOwnProperty(hookName)) {
			hooks[hookName] = {
				runs: 0,
				handlers: []
			};
		}

		var handlers = hooks[hookName].handlers;

		for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
			args[_key - 1] = arguments[_key];
		}

		if (!handlers.length) {
			return returnFirstArg ? args[0] : undefined;
		}

		var hookInfo = {
			name: hookName,
			currentIndex: 0
		};

		hooks.__current = hooks.__current || [];
		hooks.__current.push(hookInfo);
		hooks[hookName].runs++;

		var maybeReturnValue = args[0];

		while (hookInfo.currentIndex < handlers.length) {
			var handler = handlers[hookInfo.currentIndex];
			maybeReturnValue = handler.callback.apply(null, args);
			if (returnFirstArg) {
				args[0] = maybeReturnValue;
			}
			hookInfo.currentIndex++;
		}

		hooks.__current.pop();

		if (returnFirstArg) {
			return maybeReturnValue;
		}
	};
}

exports.default = createRunHook;

/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Returns a function which, when invoked, will return the name of the
 * currently running hook, or `null` if no hook of the given type is currently
 * running.
 *
 * @param  {Object}   hooks          Stored hooks, keyed by hook name.
 *
 * @return {Function}                Function that returns the current hook.
 */
function createCurrentHook(hooks, returnFirstArg) {
	/**
  * Returns the name of the currently running hook, or `null` if no hook of
  * the given type is currently running.
  *
  * @return {?string}             The name of the currently running hook, or
  *                               `null` if no hook is currently running.
  */
	return function currentHook() {
		if (!hooks.__current || !hooks.__current.length) {
			return null;
		}

		return hooks.__current[hooks.__current.length - 1].name;
	};
}

exports.default = createCurrentHook;

/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});
/**
 * Returns a function which, when invoked, will return whether a hook is
 * currently being executed.
 *
 * @param  {Object}   hooks Stored hooks, keyed by hook name.
 *
 * @return {Function}       Function that returns whether a hook is currently
 *                          being executed.
 */
function createDoingHook(hooks) {
	/**
  * Returns whether a hook is currently being executed.
  *
  * @param  {?string} hookName The name of the hook to check for.  If
  *                            omitted, will check for any hook being executed.
  *
  * @return {bool}             Whether the hook is being executed.
  */
	return function doingHook(hookName) {
		// If the hookName was not passed, check for any current hook.
		if ('undefined' === typeof hookName) {
			return 'undefined' !== typeof hooks.__current[0];
		}

		// Return the __current hook.
		return hooks.__current[0] ? hookName === hooks.__current[0].name : false;
	};
}

exports.default = createDoingHook;

/***/ }),
/* 21 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
	value: true
});

var _validateHookName = __webpack_require__(0);

var _validateHookName2 = _interopRequireDefault(_validateHookName);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

/**
 * Returns a function which, when invoked, will return the number of times a
 * hook has been called.
 *
 * @param  {Object}   hooks Stored hooks, keyed by hook name.
 *
 * @return {Function}       Function that returns a hook's call count.
 */
function createDidHook(hooks) {
	/**
  * Returns the number of times an action has been fired.
  *
  * @param  {string} hookName The hook name to check.
  *
  * @return {number}          The number of times the hook has run.
  */
	return function didHook(hookName) {

		if (!(0, _validateHookName2.default)(hookName)) {
			return;
		}

		return hooks.hasOwnProperty(hookName) && hooks[hookName].runs ? hooks[hookName].runs : 0;
	};
}

exports.default = createDidHook;

/***/ })
/******/ ]);
