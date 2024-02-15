/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 7734:
/***/ ((module) => {



// do not edit .js files directly - edit src/index.jst


  var envHasBigInt64Array = typeof BigInt64Array !== 'undefined';


module.exports = function equal(a, b) {
  if (a === b) return true;

  if (a && b && typeof a == 'object' && typeof b == 'object') {
    if (a.constructor !== b.constructor) return false;

    var length, i, keys;
    if (Array.isArray(a)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (!equal(a[i], b[i])) return false;
      return true;
    }


    if ((a instanceof Map) && (b instanceof Map)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      for (i of a.entries())
        if (!equal(i[1], b.get(i[0]))) return false;
      return true;
    }

    if ((a instanceof Set) && (b instanceof Set)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      return true;
    }

    if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (a[i] !== b[i]) return false;
      return true;
    }


    if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
    if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
    if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) return false;

    for (i = length; i-- !== 0;)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

    for (i = length; i-- !== 0;) {
      var key = keys[i];

      if (!equal(a[key], b[key])) return false;
    }

    return true;
  }

  // true if both NaN, false otherwise
  return a!==a && b!==b;
};


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
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ build_module)
});

;// CONCATENATED MODULE: external "React"
const external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/server-side-render/build-module/server-side-render.js

/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







const EMPTY_OBJECT = {};
function rendererPath(block, attributes = null, urlQueryArgs = {}) {
  return (0,external_wp_url_namespaceObject.addQueryArgs)(`/wp/v2/block-renderer/${block}`, {
    context: 'edit',
    ...(null !== attributes ? {
      attributes
    } : {}),
    ...urlQueryArgs
  });
}
function removeBlockSupportAttributes(attributes) {
  const {
    backgroundColor,
    borderColor,
    fontFamily,
    fontSize,
    gradient,
    textColor,
    className,
    ...restAttributes
  } = attributes;
  const {
    border,
    color,
    elements,
    spacing,
    typography,
    ...restStyles
  } = attributes?.style || EMPTY_OBJECT;
  return {
    ...restAttributes,
    style: restStyles
  };
}
function DefaultEmptyResponsePlaceholder({
  className
}) {
  return (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Placeholder, {
    className: className
  }, (0,external_wp_i18n_namespaceObject.__)('Block rendered as empty.'));
}
function DefaultErrorResponsePlaceholder({
  response,
  className
}) {
  const errorMessage = (0,external_wp_i18n_namespaceObject.sprintf)(
  // translators: %s: error message describing the problem
  (0,external_wp_i18n_namespaceObject.__)('Error loading block: %s'), response.errorMsg);
  return (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Placeholder, {
    className: className
  }, errorMessage);
}
function DefaultLoadingResponsePlaceholder({
  children,
  showLoader
}) {
  return (0,external_React_namespaceObject.createElement)("div", {
    style: {
      position: 'relative'
    }
  }, showLoader && (0,external_React_namespaceObject.createElement)("div", {
    style: {
      position: 'absolute',
      top: '50%',
      left: '50%',
      marginTop: '-9px',
      marginLeft: '-9px'
    }
  }, (0,external_React_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null)), (0,external_React_namespaceObject.createElement)("div", {
    style: {
      opacity: showLoader ? '0.3' : 1
    }
  }, children));
}
function ServerSideRender(props) {
  const {
    attributes,
    block,
    className,
    httpMethod = 'GET',
    urlQueryArgs,
    skipBlockSupportAttributes = false,
    EmptyResponsePlaceholder = DefaultEmptyResponsePlaceholder,
    ErrorResponsePlaceholder = DefaultErrorResponsePlaceholder,
    LoadingResponsePlaceholder = DefaultLoadingResponsePlaceholder
  } = props;
  const isMountedRef = (0,external_wp_element_namespaceObject.useRef)(true);
  const [showLoader, setShowLoader] = (0,external_wp_element_namespaceObject.useState)(false);
  const fetchRequestRef = (0,external_wp_element_namespaceObject.useRef)();
  const [response, setResponse] = (0,external_wp_element_namespaceObject.useState)(null);
  const prevProps = (0,external_wp_compose_namespaceObject.usePrevious)(props);
  const [isLoading, setIsLoading] = (0,external_wp_element_namespaceObject.useState)(false);
  function fetchData() {
    var _sanitizedAttributes, _sanitizedAttributes2;
    if (!isMountedRef.current) {
      return;
    }
    setIsLoading(true);
    let sanitizedAttributes = attributes && (0,external_wp_blocks_namespaceObject.__experimentalSanitizeBlockAttributes)(block, attributes);
    if (skipBlockSupportAttributes) {
      sanitizedAttributes = removeBlockSupportAttributes(sanitizedAttributes);
    }

    // If httpMethod is 'POST', send the attributes in the request body instead of the URL.
    // This allows sending a larger attributes object than in a GET request, where the attributes are in the URL.
    const isPostRequest = 'POST' === httpMethod;
    const urlAttributes = isPostRequest ? null : (_sanitizedAttributes = sanitizedAttributes) !== null && _sanitizedAttributes !== void 0 ? _sanitizedAttributes : null;
    const path = rendererPath(block, urlAttributes, urlQueryArgs);
    const data = isPostRequest ? {
      attributes: (_sanitizedAttributes2 = sanitizedAttributes) !== null && _sanitizedAttributes2 !== void 0 ? _sanitizedAttributes2 : null
    } : null;

    // Store the latest fetch request so that when we process it, we can
    // check if it is the current request, to avoid race conditions on slow networks.
    const fetchRequest = fetchRequestRef.current = external_wp_apiFetch_default()({
      path,
      data,
      method: isPostRequest ? 'POST' : 'GET'
    }).then(fetchResponse => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current && fetchResponse) {
        setResponse(fetchResponse.rendered);
      }
    }).catch(error => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current) {
        setResponse({
          error: true,
          errorMsg: error.message
        });
      }
    }).finally(() => {
      if (isMountedRef.current && fetchRequest === fetchRequestRef.current) {
        setIsLoading(false);
      }
    });
    return fetchRequest;
  }
  const debouncedFetchData = (0,external_wp_compose_namespaceObject.useDebounce)(fetchData, 500);

  // When the component unmounts, set isMountedRef to false. This will
  // let the async fetch callbacks know when to stop.
  (0,external_wp_element_namespaceObject.useEffect)(() => () => {
    isMountedRef.current = false;
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // Don't debounce the first fetch. This ensures that the first render
    // shows data as soon as possible.
    if (prevProps === undefined) {
      fetchData();
    } else if (!es6_default()(prevProps, props)) {
      debouncedFetchData();
    }
  });

  /**
   * Effect to handle showing the loading placeholder.
   * Show it only if there is no previous response or
   * the request takes more than one second.
   */
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (!isLoading) {
      return;
    }
    const timeout = setTimeout(() => {
      setShowLoader(true);
    }, 1000);
    return () => clearTimeout(timeout);
  }, [isLoading]);
  const hasResponse = !!response;
  const hasEmptyResponse = response === '';
  const hasError = response?.error;
  if (isLoading) {
    return (0,external_React_namespaceObject.createElement)(LoadingResponsePlaceholder, {
      ...props,
      showLoader: showLoader
    }, hasResponse && (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.RawHTML, {
      className: className
    }, response));
  }
  if (hasEmptyResponse || !hasResponse) {
    return (0,external_React_namespaceObject.createElement)(EmptyResponsePlaceholder, {
      ...props
    });
  }
  if (hasError) {
    return (0,external_React_namespaceObject.createElement)(ErrorResponsePlaceholder, {
      response: response,
      ...props
    });
  }
  return (0,external_React_namespaceObject.createElement)(external_wp_element_namespaceObject.RawHTML, {
    className: className
  }, response);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/server-side-render/build-module/index.js

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Constants
 */
const build_module_EMPTY_OBJECT = {};
const ExportedServerSideRender = (0,external_wp_data_namespaceObject.withSelect)(select => {
  // FIXME: @wordpress/server-side-render should not depend on @wordpress/editor.
  // It is used by blocks that can be loaded into a *non-post* block editor.
  // eslint-disable-next-line @wordpress/data-no-store-string-literals
  const coreEditorSelect = select('core/editor');
  if (coreEditorSelect) {
    const currentPostId = coreEditorSelect.getCurrentPostId();
    // For templates and template parts we use a custom ID format.
    // Since they aren't real posts, we don't want to use their ID
    // for server-side rendering. Since they use a string based ID,
    // we can assume real post IDs are numbers.
    if (currentPostId && typeof currentPostId === 'number') {
      return {
        currentPostId
      };
    }
  }
  return build_module_EMPTY_OBJECT;
})(({
  urlQueryArgs = build_module_EMPTY_OBJECT,
  currentPostId,
  ...props
}) => {
  const newUrlQueryArgs = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!currentPostId) {
      return urlQueryArgs;
    }
    return {
      post_id: currentPostId,
      ...urlQueryArgs
    };
  }, [currentPostId, urlQueryArgs]);
  return (0,external_React_namespaceObject.createElement)(ServerSideRender, {
    urlQueryArgs: newUrlQueryArgs,
    ...props
  });
});
/* harmony default export */ const build_module = (ExportedServerSideRender);

})();

(window.wp = window.wp || {}).serverSideRender = __webpack_exports__["default"];
/******/ })()
;