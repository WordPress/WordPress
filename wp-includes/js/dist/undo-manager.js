/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 923:
/***/ ((module) => {

module.exports = window["wp"]["isShallowEqual"];

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
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createUndoManager: () => (/* binding */ createUndoManager)
/* harmony export */ });
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(923);
/* harmony import */ var _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */


/** @typedef {import('./types').HistoryRecord}  HistoryRecord */
/** @typedef {import('./types').HistoryChange}  HistoryChange */
/** @typedef {import('./types').HistoryChanges} HistoryChanges */
/** @typedef {import('./types').UndoManager} UndoManager */

/**
 * Merge changes for a single item into a record of changes.
 *
 * @param {Record< string, HistoryChange >} changes1 Previous changes
 * @param {Record< string, HistoryChange >} changes2 NextChanges
 *
 * @return {Record< string, HistoryChange >} Merged changes
 */
function mergeHistoryChanges(changes1, changes2) {
  /**
   * @type {Record< string, HistoryChange >}
   */
  const newChanges = {
    ...changes1
  };
  Object.entries(changes2).forEach(([key, value]) => {
    if (newChanges[key]) {
      newChanges[key] = {
        ...newChanges[key],
        to: value.to
      };
    } else {
      newChanges[key] = value;
    }
  });
  return newChanges;
}

/**
 * Adds history changes for a single item into a record of changes.
 *
 * @param {HistoryRecord}  record  The record to merge into.
 * @param {HistoryChanges} changes The changes to merge.
 */
const addHistoryChangesIntoRecord = (record, changes) => {
  const existingChangesIndex = record?.findIndex(({
    id: recordIdentifier
  }) => {
    return typeof recordIdentifier === 'string' ? recordIdentifier === changes.id : _wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_0___default()(recordIdentifier, changes.id);
  });
  const nextRecord = [...record];
  if (existingChangesIndex !== -1) {
    // If the edit is already in the stack leave the initial "from" value.
    nextRecord[existingChangesIndex] = {
      id: changes.id,
      changes: mergeHistoryChanges(nextRecord[existingChangesIndex].changes, changes.changes)
    };
  } else {
    nextRecord.push(changes);
  }
  return nextRecord;
};

/**
 * Creates an undo manager.
 *
 * @return {UndoManager} Undo manager.
 */
function createUndoManager() {
  /**
   * @type {HistoryRecord[]}
   */
  let history = [];
  /**
   * @type {HistoryRecord}
   */
  let stagedRecord = [];
  /**
   * @type {number}
   */
  let offset = 0;
  const dropPendingRedos = () => {
    history = history.slice(0, offset || undefined);
    offset = 0;
  };
  const appendStagedRecordToLatestHistoryRecord = () => {
    var _history$index;
    const index = history.length === 0 ? 0 : history.length - 1;
    let latestRecord = (_history$index = history[index]) !== null && _history$index !== void 0 ? _history$index : [];
    stagedRecord.forEach(changes => {
      latestRecord = addHistoryChangesIntoRecord(latestRecord, changes);
    });
    stagedRecord = [];
    history[index] = latestRecord;
  };

  /**
   * Checks whether a record is empty.
   * A record is considered empty if it the changes keep the same values.
   * Also updates to function values are ignored.
   *
   * @param {HistoryRecord} record
   * @return {boolean} Whether the record is empty.
   */
  const isRecordEmpty = record => {
    const filteredRecord = record.filter(({
      changes
    }) => {
      return Object.values(changes).some(({
        from,
        to
      }) => typeof from !== 'function' && typeof to !== 'function' && !_wordpress_is_shallow_equal__WEBPACK_IMPORTED_MODULE_0___default()(from, to));
    });
    return !filteredRecord.length;
  };
  return {
    /**
     * Record changes into the history.
     *
     * @param {HistoryRecord=} record   A record of changes to record.
     * @param {boolean}        isStaged Whether to immediately create an undo point or not.
     */
    addRecord(record, isStaged = false) {
      const isEmpty = !record || isRecordEmpty(record);
      if (isStaged) {
        if (isEmpty) {
          return;
        }
        record.forEach(changes => {
          stagedRecord = addHistoryChangesIntoRecord(stagedRecord, changes);
        });
      } else {
        dropPendingRedos();
        if (stagedRecord.length) {
          appendStagedRecordToLatestHistoryRecord();
        }
        if (isEmpty) {
          return;
        }
        history.push(record);
      }
    },
    undo() {
      if (stagedRecord.length) {
        dropPendingRedos();
        appendStagedRecordToLatestHistoryRecord();
      }
      const undoRecord = history[history.length - 1 + offset];
      if (!undoRecord) {
        return;
      }
      offset -= 1;
      return undoRecord;
    },
    redo() {
      const redoRecord = history[history.length + offset];
      if (!redoRecord) {
        return;
      }
      offset += 1;
      return redoRecord;
    },
    hasUndo() {
      return !!history[history.length - 1 + offset];
    },
    hasRedo() {
      return !!history[history.length + offset];
    }
  };
}

(window.wp = window.wp || {}).undoManager = __webpack_exports__;
/******/ })()
;