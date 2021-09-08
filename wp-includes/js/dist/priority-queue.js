this["wp"] = this["wp"] || {}; this["wp"]["priorityQueue"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "XPKI");
/******/ })
/************************************************************************/
/******/ ({

/***/ "XPKI":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "createQueue", function() { return /* binding */ build_module_createQueue; });

// CONCATENATED MODULE: ./node_modules/@wordpress/priority-queue/build-module/request-idle-callback.js
/**
 * @typedef {( timeOrDeadline: IdleDeadline | number ) => void} Callback
 */

/**
 * @return {(callback: Callback) => void} RequestIdleCallback
 */
function createRequestIdleCallback() {
  if (typeof window === 'undefined') {
    return function (callback) {
      setTimeout(function () {
        return callback(Date.now());
      }, 0);
    };
  }

  return window.requestIdleCallback || window.requestAnimationFrame;
}
/* harmony default export */ var request_idle_callback = (createRequestIdleCallback());

// CONCATENATED MODULE: ./node_modules/@wordpress/priority-queue/build-module/index.js
/**
 * Internal dependencies
 */

/**
 * Enqueued callback to invoke once idle time permits.
 *
 * @typedef {()=>void} WPPriorityQueueCallback
 */

/**
 * An object used to associate callbacks in a particular context grouping.
 *
 * @typedef {{}} WPPriorityQueueContext
 */

/**
 * Function to add callback to priority queue.
 *
 * @typedef {(element:WPPriorityQueueContext,item:WPPriorityQueueCallback)=>void} WPPriorityQueueAdd
 */

/**
 * Function to flush callbacks from priority queue.
 *
 * @typedef {(element:WPPriorityQueueContext)=>boolean} WPPriorityQueueFlush
 */

/**
 * Reset the queue.
 *
 * @typedef {()=>void} WPPriorityQueueReset
 */

/**
 * Priority queue instance.
 *
 * @typedef {Object} WPPriorityQueue
 *
 * @property {WPPriorityQueueAdd}   add   Add callback to queue for context.
 * @property {WPPriorityQueueFlush} flush Flush queue for context.
 * @property {WPPriorityQueueReset} reset Reset queue.
 */

/**
 * Creates a context-aware queue that only executes
 * the last task of a given context.
 *
 * @example
 *```js
 * import { createQueue } from '@wordpress/priority-queue';
 *
 * const queue = createQueue();
 *
 * // Context objects.
 * const ctx1 = {};
 * const ctx2 = {};
 *
 * // For a given context in the queue, only the last callback is executed.
 * queue.add( ctx1, () => console.log( 'This will be printed first' ) );
 * queue.add( ctx2, () => console.log( 'This won\'t be printed' ) );
 * queue.add( ctx2, () => console.log( 'This will be printed second' ) );
 *```
 *
 * @return {WPPriorityQueue} Queue object with `add`, `flush` and `reset` methods.
 */

var build_module_createQueue = function createQueue() {
  /** @type {WPPriorityQueueContext[]} */
  var waitingList = [];
  /** @type {WeakMap<WPPriorityQueueContext,WPPriorityQueueCallback>} */

  var elementsMap = new WeakMap();
  var isRunning = false;
  /* eslint-disable jsdoc/valid-types */

  /**
   * Callback to process as much queue as time permits.
   *
   * @param {IdleDeadline|number} deadline Idle callback deadline object, or
   *                                       animation frame timestamp.
   */

  /* eslint-enable */

  var runWaitingList = function runWaitingList(deadline) {
    var hasTimeRemaining = typeof deadline === 'number' ? function () {
      return false;
    } : function () {
      return deadline.timeRemaining() > 0;
    };

    do {
      if (waitingList.length === 0) {
        isRunning = false;
        return;
      }

      var nextElement =
      /** @type {WPPriorityQueueContext} */
      waitingList.shift();
      var callback =
      /** @type {WPPriorityQueueCallback} */
      elementsMap.get(nextElement);
      callback();
      elementsMap.delete(nextElement);
    } while (hasTimeRemaining());

    request_idle_callback(runWaitingList);
  };
  /**
   * Add a callback to the queue for a given context.
   *
   * @type {WPPriorityQueueAdd}
   *
   * @param {WPPriorityQueueContext}  element Context object.
   * @param {WPPriorityQueueCallback} item    Callback function.
   */


  var add = function add(element, item) {
    if (!elementsMap.has(element)) {
      waitingList.push(element);
    }

    elementsMap.set(element, item);

    if (!isRunning) {
      isRunning = true;
      request_idle_callback(runWaitingList);
    }
  };
  /**
   * Flushes queue for a given context, returning true if the flush was
   * performed, or false if there is no queue for the given context.
   *
   * @type {WPPriorityQueueFlush}
   *
   * @param {WPPriorityQueueContext} element Context object.
   *
   * @return {boolean} Whether flush was performed.
   */


  var flush = function flush(element) {
    if (!elementsMap.has(element)) {
      return false;
    }

    var index = waitingList.indexOf(element);
    waitingList.splice(index, 1);
    var callback =
    /** @type {WPPriorityQueueCallback} */
    elementsMap.get(element);
    elementsMap.delete(element);
    callback();
    return true;
  };
  /**
   * Reset the queue without running the pending callbacks.
   *
   * @type {WPPriorityQueueReset}
   */


  var reset = function reset() {
    waitingList = [];
    elementsMap = new WeakMap();
    isRunning = false;
  };

  return {
    add: add,
    flush: flush,
    reset: reset
  };
};


/***/ })

/******/ });