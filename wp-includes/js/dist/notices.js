/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/notices/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  createErrorNotice: () => (createErrorNotice),
  createInfoNotice: () => (createInfoNotice),
  createNotice: () => (createNotice),
  createSuccessNotice: () => (createSuccessNotice),
  createWarningNotice: () => (createWarningNotice),
  removeAllNotices: () => (removeAllNotices),
  removeNotice: () => (removeNotice),
  removeNotices: () => (removeNotices)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/notices/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getNotices: () => (getNotices)
});

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/notices/build-module/store/utils/on-sub-key.js
const onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
  const key = action[actionProperty];
  if (key === void 0) {
    return state;
  }
  const nextKeyState = reducer(state[key], action);
  if (nextKeyState === state[key]) {
    return state;
  }
  return {
    ...state,
    [key]: nextKeyState
  };
};
var on_sub_key_default = onSubKey;


;// ./node_modules/@wordpress/notices/build-module/store/reducer.js

const notices = on_sub_key_default("context")((state = [], action) => {
  switch (action.type) {
    case "CREATE_NOTICE":
      return [
        ...state.filter(({ id }) => id !== action.notice.id),
        action.notice
      ];
    case "REMOVE_NOTICE":
      return state.filter(({ id }) => id !== action.id);
    case "REMOVE_NOTICES":
      return state.filter(({ id }) => !action.ids.includes(id));
    case "REMOVE_ALL_NOTICES":
      return state.filter(({ type }) => type !== action.noticeType);
  }
  return state;
});
var reducer_default = notices;


;// ./node_modules/@wordpress/notices/build-module/store/constants.js
const DEFAULT_CONTEXT = "global";
const DEFAULT_STATUS = "info";


;// ./node_modules/@wordpress/notices/build-module/store/actions.js

let uniqueId = 0;
function createNotice(status = DEFAULT_STATUS, content, options = {}) {
  const {
    speak = true,
    isDismissible = true,
    context = DEFAULT_CONTEXT,
    id = `${context}${++uniqueId}`,
    actions = [],
    type = "default",
    __unstableHTML,
    icon = null,
    explicitDismiss = false,
    onDismiss
  } = options;
  content = String(content);
  return {
    type: "CREATE_NOTICE",
    context,
    notice: {
      id,
      status,
      content,
      spokenMessage: speak ? content : null,
      __unstableHTML,
      isDismissible,
      actions,
      type,
      icon,
      explicitDismiss,
      onDismiss
    }
  };
}
function createSuccessNotice(content, options) {
  return createNotice("success", content, options);
}
function createInfoNotice(content, options) {
  return createNotice("info", content, options);
}
function createErrorNotice(content, options) {
  return createNotice("error", content, options);
}
function createWarningNotice(content, options) {
  return createNotice("warning", content, options);
}
function removeNotice(id, context = DEFAULT_CONTEXT) {
  return {
    type: "REMOVE_NOTICE",
    id,
    context
  };
}
function removeAllNotices(noticeType = "default", context = DEFAULT_CONTEXT) {
  return {
    type: "REMOVE_ALL_NOTICES",
    noticeType,
    context
  };
}
function removeNotices(ids, context = DEFAULT_CONTEXT) {
  return {
    type: "REMOVE_NOTICES",
    ids,
    context
  };
}


;// ./node_modules/@wordpress/notices/build-module/store/selectors.js

const DEFAULT_NOTICES = [];
function getNotices(state, context = DEFAULT_CONTEXT) {
  return state[context] || DEFAULT_NOTICES;
}


;// ./node_modules/@wordpress/notices/build-module/store/index.js




const store = (0,external_wp_data_namespaceObject.createReduxStore)("core/notices", {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/notices/build-module/index.js



(window.wp = window.wp || {}).notices = __webpack_exports__;
/******/ })()
;