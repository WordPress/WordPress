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
  "store": function() { return /* reexport */ store; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/notices/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "createErrorNotice": function() { return createErrorNotice; },
  "createInfoNotice": function() { return createInfoNotice; },
  "createNotice": function() { return createNotice; },
  "createSuccessNotice": function() { return createSuccessNotice; },
  "createWarningNotice": function() { return createWarningNotice; },
  "removeNotice": function() { return removeNotice; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/notices/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getNotices": function() { return getNotices; }
});

;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/utils/on-sub-key.js
/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
const onSubKey = actionProperty => reducer => function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;
  // Retrieve subkey from action. Do not track if undefined; useful for cases
  // where reducer is scoped by action shape.
  const key = action[actionProperty];

  if (key === undefined) {
    return state;
  } // Avoid updating state if unchanged. Note that this also accounts for a
  // reducer which returns undefined on a key which is not yet tracked.


  const nextKeyState = reducer(state[key], action);

  if (nextKeyState === state[key]) {
    return state;
  }

  return { ...state,
    [key]: nextKeyState
  };
};
/* harmony default export */ var on_sub_key = (onSubKey);

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/reducer.js
/**
 * Internal dependencies
 */

/**
 * Reducer returning the next notices state. The notices state is an object
 * where each key is a context, its value an array of notice objects.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

const notices = on_sub_key('context')(function () {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'CREATE_NOTICE':
      // Avoid duplicates on ID.
      return [...state.filter(_ref => {
        let {
          id
        } = _ref;
        return id !== action.notice.id;
      }), action.notice];

    case 'REMOVE_NOTICE':
      return state.filter(_ref2 => {
        let {
          id
        } = _ref2;
        return id !== action.id;
      });
  }

  return state;
});
/* harmony default export */ var reducer = (notices);

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/constants.js
/**
 * Default context to use for notice grouping when not otherwise specified. Its
 * specific value doesn't hold much meaning, but it must be reasonably unique
 * and, more importantly, referenced consistently in the store implementation.
 *
 * @type {string}
 */
const DEFAULT_CONTEXT = 'global';
/**
 * Default notice status.
 *
 * @type {string}
 */

const DEFAULT_STATUS = 'info';

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/actions.js
/**
 * Internal dependencies
 */

/**
 * @typedef {Object} WPNoticeAction Object describing a user action option associated with a notice.
 *
 * @property {string}    label   Message to use as action label.
 * @property {?string}   url     Optional URL of resource if action incurs
 *                               browser navigation.
 * @property {?Function} onClick Optional function to invoke when action is
 *                               triggered by user.
 *
 */

let uniqueId = 0;
/**
 * Returns an action object used in signalling that a notice is to be created.
 *
 * @param {string}                [status='info']              Notice status.
 * @param {string}                content                      Notice message.
 * @param {Object}                [options]                    Notice options.
 * @param {string}                [options.context='global']   Context under which to
 *                                                             group notice.
 * @param {string}                [options.id]                 Identifier for notice.
 *                                                             Automatically assigned
 *                                                             if not specified.
 * @param {boolean}               [options.isDismissible=true] Whether the notice can
 *                                                             be dismissed by user.
 * @param {string}                [options.type='default']     Type of notice, one of
 *                                                             `default`, or `snackbar`.
 * @param {boolean}               [options.speak=true]         Whether the notice
 *                                                             content should be
 *                                                             announced to screen
 *                                                             readers.
 * @param {Array<WPNoticeAction>} [options.actions]            User actions to be
 *                                                             presented with notice.
 * @param {string}                [options.icon]               An icon displayed with the notice.
 *                                                             Only used when type is set to `snackbar`.
 * @param {boolean}               [options.explicitDismiss]    Whether the notice includes
 *                                                             an explicit dismiss button and
 *                                                             can't be dismissed by clicking
 *                                                             the body of the notice. Only applies
 *                                                             when type is set to `snackbar`.
 * @param {Function}              [options.onDismiss]          Called when the notice is dismissed.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *     const { createNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () => createNotice( 'success', __( 'Notice message' ) ) }
 *         >
 *             { __( 'Generate a success notice!' ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createNotice() {
  let status = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATUS;
  let content = arguments.length > 1 ? arguments[1] : undefined;
  let options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  const {
    speak = true,
    isDismissible = true,
    context = DEFAULT_CONTEXT,
    id = `${context}${++uniqueId}`,
    actions = [],
    type = 'default',
    __unstableHTML,
    icon = null,
    explicitDismiss = false,
    onDismiss
  } = options; // The supported value shape of content is currently limited to plain text
  // strings. To avoid setting expectation that e.g. a WPElement could be
  // supported, cast to a string.

  content = String(content);
  return {
    type: 'CREATE_NOTICE',
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
/**
 * Returns an action object used in signalling that a success notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *     const { createSuccessNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createSuccessNotice( __( 'Success!' ), {
 *                     type: 'snackbar',
 *                     icon: '🔥',
 *                 } )
 *             }
 *         >
 *             { __( 'Generate a snackbar success notice!' ) }
 *        </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createSuccessNotice(content, options) {
  return createNotice('success', content, options);
}
/**
 * Returns an action object used in signalling that an info notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *     const { createInfoNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                createInfoNotice( __( 'Something happened!' ), {
 *                   isDismissible: false,
 *                } )
 *             }
 *         >
 *         { __( 'Generate a notice that cannot be dismissed.' ) }
 *       </Button>
 *       );
 * };
 *```
 *
 * @return {Object} Action object.
 */

function createInfoNotice(content, options) {
  return createNotice('info', content, options);
}
/**
 * Returns an action object used in signalling that an error notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *     const { createErrorNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createErrorNotice( __( 'An error occurred!' ), {
 *                     type: 'snackbar',
 *                     explicitDismiss: true,
 *                 } )
 *             }
 *         >
 *             { __(
 *                 'Generate an snackbar error notice with explicit dismiss button.'
 *             ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createErrorNotice(content, options) {
  return createNotice('error', content, options);
}
/**
 * Returns an action object used in signalling that a warning notice is to be
 * created. Refer to `createNotice` for options documentation.
 *
 * @see createNotice
 *
 * @param {string} content   Notice message.
 * @param {Object} [options] Optional notice options.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *     const { createWarningNotice, createInfoNotice } = useDispatch( noticesStore );
 *     return (
 *         <Button
 *             onClick={ () =>
 *                 createWarningNotice( __( 'Warning!' ), {
 *                     onDismiss: () => {
 *                         createInfoNotice(
 *                             __( 'The warning has been dismissed!' )
 *                         );
 *                     },
 *                 } )
 *             }
 *         >
 *             { __( 'Generates a warning notice with onDismiss callback' ) }
 *         </Button>
 *     );
 * };
 * ```
 *
 * @return {Object} Action object.
 */

function createWarningNotice(content, options) {
  return createNotice('warning', content, options);
}
/**
 * Returns an action object used in signalling that a notice is to be removed.
 *
 * @param {string} id                 Notice unique identifier.
 * @param {string} [context='global'] Optional context (grouping) in which the notice is
 *                                    intended to appear. Defaults to default context.
 *
 * @example
 * ```js
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *    const notices = useSelect( ( select ) => select( noticesStore ).getNotices() );
 *    const { createWarningNotice, removeNotice } = useDispatch( noticesStore );
 *
 *    return (
 *         <>
 *             <Button
 *                 onClick={ () =>
 *                     createWarningNotice( __( 'Warning!' ), {
 *                         isDismissible: false,
 *                     } )
 *                 }
 *             >
 *                 { __( 'Generate a notice' ) }
 *             </Button>
 *             { notices.length > 0 && (
 *                 <Button onClick={ () => removeNotice( notices[ 0 ].id ) }>
 *                     { __( 'Remove the notice' ) }
 *                 </Button>
 *             ) }
 *         </>
 *     );
 *};
 * ```
 *
 * @return {Object} Action object.
 */

function removeNotice(id) {
  let context = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : DEFAULT_CONTEXT;
  return {
    type: 'REMOVE_NOTICE',
    id,
    context
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/selectors.js
/**
 * Internal dependencies
 */

/** @typedef {import('./actions').WPNoticeAction} WPNoticeAction */

/**
 * The default empty set of notices to return when there are no notices
 * assigned for a given notices context. This can occur if the getNotices
 * selector is called without a notice ever having been created for the
 * context. A shared value is used to ensure referential equality between
 * sequential selector calls, since otherwise `[] !== []`.
 *
 * @type {Array}
 */

const DEFAULT_NOTICES = [];
/**
 * @typedef {Object} WPNotice Notice object.
 *
 * @property {string}           id             Unique identifier of notice.
 * @property {string}           status         Status of notice, one of `success`,
 *                                             `info`, `error`, or `warning`. Defaults
 *                                             to `info`.
 * @property {string}           content        Notice message.
 * @property {string}           spokenMessage  Audibly announced message text used by
 *                                             assistive technologies.
 * @property {string}           __unstableHTML Notice message as raw HTML. Intended to
 *                                             serve primarily for compatibility of
 *                                             server-rendered notices, and SHOULD NOT
 *                                             be used for notices. It is subject to
 *                                             removal without notice.
 * @property {boolean}          isDismissible  Whether the notice can be dismissed by
 *                                             user. Defaults to `true`.
 * @property {string}           type           Type of notice, one of `default`,
 *                                             or `snackbar`. Defaults to `default`.
 * @property {boolean}          speak          Whether the notice content should be
 *                                             announced to screen readers. Defaults to
 *                                             `true`.
 * @property {WPNoticeAction[]} actions        User actions to present with notice.
 *
 */

/**
 * Returns all notices as an array, optionally for a given context. Defaults to
 * the global context.
 *
 * @param {Object}  state   Notices state.
 * @param {?string} context Optional grouping context.
 *
 * @example
 *
 *```js
 * import { useSelect } from '@wordpress/data';
 * import { store as noticesStore } from '@wordpress/notices';
 *
 * const ExampleComponent = () => {
 *     const notices = useSelect( ( select ) => select( noticesStore ).getNotices() );
 *     return (
 *         <ul>
 *         { notices.map( ( notice ) => (
 *             <li key={ notice.ID }>{ notice.content }</li>
 *         ) ) }
 *        </ul>
 *    )
 * };
 *```
 *
 * @return {WPNotice[]} Array of notices.
 */

function getNotices(state) {
  let context = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : DEFAULT_CONTEXT;
  return state[context] || DEFAULT_NOTICES;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




/**
 * Store definition for the notices namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)('core/notices', {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/notices/build-module/index.js


(window.wp = window.wp || {}).notices = __webpack_exports__;
/******/ })()
;