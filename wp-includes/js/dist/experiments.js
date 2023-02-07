/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "__dangerousOptInToUnstableAPIsOnlyForCoreModules": function() { return /* reexport */ __dangerousOptInToUnstableAPIsOnlyForCoreModules; }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/experiments/build-module/implementation.js
/**
 * wordpress/experimental – the utilities to enable private cross-package
 * exports of experimental APIs.
 *
 * This "implementation.js" file is needed for the sake of the unit tests. It
 * exports more than the public API of the package to aid in testing.
 */

/**
 * The list of core modules allowed to opt-in to the experimental APIs.
 */
const CORE_MODULES_USING_EXPERIMENTS = ['@wordpress/data', '@wordpress/editor', '@wordpress/blocks', '@wordpress/block-editor', '@wordpress/customize-widgets', '@wordpress/edit-site', '@wordpress/edit-post', '@wordpress/edit-widgets', '@wordpress/block-library'];
/**
 * A list of core modules that already opted-in to
 * the experiments package.
 */

const registeredExperiments = [];
/*
 * Warning for theme and plugin developers.
 *
 * The use of experimental developer APIs is intended for use by WordPress Core
 * and the Gutenberg plugin exclusively.
 *
 * Dangerously opting in to using these APIs is NOT RECOMMENDED. Furthermore,
 * the WordPress Core philosophy to strive to maintain backward compatibility
 * for third-party developers DOES NOT APPLY to experimental APIs.
 *
 * THE CONSENT STRING FOR OPTING IN TO THESE APIS MAY CHANGE AT ANY TIME AND
 * WITHOUT NOTICE. THIS CHANGE WILL BREAK EXISTING THIRD-PARTY CODE. SUCH A
 * CHANGE MAY OCCUR IN EITHER A MAJOR OR MINOR RELEASE.
 */

const requiredConsent = 'I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.';
const __dangerousOptInToUnstableAPIsOnlyForCoreModules = (consent, moduleName) => {
  if (!CORE_MODULES_USING_EXPERIMENTS.includes(moduleName)) {
    throw new Error(`You tried to opt-in to unstable APIs as module "${moduleName}". ` + 'This feature is only for JavaScript modules shipped with WordPress core. ' + 'Please do not use it in plugins and themes as the unstable APIs will be removed ' + 'without a warning. If you ignore this error and depend on unstable features, ' + 'your product will inevitably break on one of the next WordPress releases.');
  }

  if (registeredExperiments.includes(moduleName)) {
    throw new Error(`You tried to opt-in to unstable APIs as module "${moduleName}" which is already registered. ` + 'This feature is only for JavaScript modules shipped with WordPress core. ' + 'Please do not use it in plugins and themes as the unstable APIs will be removed ' + 'without a warning. If you ignore this error and depend on unstable features, ' + 'your product will inevitably break on one of the next WordPress releases.');
  }

  if (consent !== requiredConsent) {
    throw new Error(`You tried to opt-in to unstable APIs without confirming you know the consequences. ` + 'This feature is only for JavaScript modules shipped with WordPress core. ' + 'Please do not use it in plugins and themes as the unstable APIs will removed ' + 'without a warning. If you ignore this error and depend on unstable features, ' + 'your product will inevitably break on the next WordPress release.');
  }

  registeredExperiments.push(moduleName);
  return {
    lock,
    unlock
  };
};
/**
 * Binds private data to an object.
 * It does not alter the passed object in any way, only
 * registers it in an internal map of private data.
 *
 * The private data can't be accessed by any other means
 * than the `unlock` function.
 *
 * @example
 * ```js
 * const object = {};
 * const privateData = { a: 1 };
 * lock( object, privateData );
 *
 * object
 * // {}
 *
 * unlock( object );
 * // { a: 1 }
 * ```
 *
 * @param {Object|Function} object      The object to bind the private data to.
 * @param {any}             privateData The private data to bind to the object.
 */

function lock(object, privateData) {
  if (!object) {
    throw new Error('Cannot lock an undefined object.');
  }

  if (!(__experiment in object)) {
    object[__experiment] = {};
  }

  lockedData.set(object[__experiment], privateData);
}
/**
 * Unlocks the private data bound to an object.
 *
 * It does not alter the passed object in any way, only
 * returns the private data paired with it using the `lock()`
 * function.
 *
 * @example
 * ```js
 * const object = {};
 * const privateData = { a: 1 };
 * lock( object, privateData );
 *
 * object
 * // {}
 *
 * unlock( object );
 * // { a: 1 }
 * ```
 *
 * @param {any} object The object to unlock the private data from.
 * @return {any} The private data bound to the object.
 */


function unlock(object) {
  if (!object) {
    throw new Error('Cannot unlock an undefined object.');
  }

  if (!(__experiment in object)) {
    throw new Error('Cannot unlock an object that was not locked before. ');
  }

  return lockedData.get(object[__experiment]);
}

const lockedData = new WeakMap();
/**
 * Used by lock() and unlock() to uniquely identify the private data
 * related to a containing object.
 */

const __experiment = Symbol('Experiment ID'); // Unit tests utilities:

/**
 * Private function to allow the unit tests to allow
 * a mock module to access the experimental APIs.
 *
 * @param {string} name The name of the module.
 */


function allowCoreModule(name) {
  CORE_MODULES_USING_EXPERIMENTS.push(name);
}
/**
 * Private function to allow the unit tests to set
 * a custom list of allowed modules.
 */

function resetAllowedCoreModules() {
  while (CORE_MODULES_USING_EXPERIMENTS.length) {
    CORE_MODULES_USING_EXPERIMENTS.pop();
  }
}
/**
 * Private function to allow the unit tests to reset
 * the list of registered experiments.
 */

function resetRegisteredExperiments() {
  while (registeredExperiments.length) {
    registeredExperiments.pop();
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/experiments/build-module/index.js


(window.wp = window.wp || {}).experiments = __webpack_exports__;
/******/ })()
;