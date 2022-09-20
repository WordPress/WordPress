/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
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
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": function() { return /* binding */ build_module; }
});

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
function _extends() {
  _extends = Object.assign ? Object.assign.bind() : function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };
  return _extends.apply(this, arguments);
}
;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external ["wp","deprecated"]
var external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// CONCATENATED MODULE: external ["wp","url"]
var external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","components"]
var external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","blocks"]
var external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/server-side-render/build-module/server-side-render.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








function rendererPath(block) {
  let attributes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  let urlQueryArgs = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  return (0,external_wp_url_namespaceObject.addQueryArgs)(`/wp/v2/block-renderer/${block}`, {
    context: 'edit',
    ...(null !== attributes ? {
      attributes
    } : {}),
    ...urlQueryArgs
  });
}

function DefaultEmptyResponsePlaceholder(_ref) {
  let {
    className
  } = _ref;
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Placeholder, {
    className: className
  }, (0,external_wp_i18n_namespaceObject.__)('Block rendered as empty.'));
}

function DefaultErrorResponsePlaceholder(_ref2) {
  let {
    response,
    className
  } = _ref2;
  const errorMessage = (0,external_wp_i18n_namespaceObject.sprintf)( // translators: %s: error message describing the problem
  (0,external_wp_i18n_namespaceObject.__)('Error loading block: %s'), response.errorMsg);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Placeholder, {
    className: className
  }, errorMessage);
}

function DefaultLoadingResponsePlaceholder(_ref3) {
  let {
    children,
    showLoader
  } = _ref3;
  return (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      position: 'relative'
    }
  }, showLoader && (0,external_wp_element_namespaceObject.createElement)("div", {
    style: {
      position: 'absolute',
      top: '50%',
      left: '50%',
      marginTop: '-9px',
      marginLeft: '-9px'
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Spinner, null)), (0,external_wp_element_namespaceObject.createElement)("div", {
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
    if (!isMountedRef.current) {
      return;
    }

    setIsLoading(true);

    const sanitizedAttributes = attributes && (0,external_wp_blocks_namespaceObject.__experimentalSanitizeBlockAttributes)(block, attributes); // If httpMethod is 'POST', send the attributes in the request body instead of the URL.
    // This allows sending a larger attributes object than in a GET request, where the attributes are in the URL.


    const isPostRequest = 'POST' === httpMethod;
    const urlAttributes = isPostRequest ? null : sanitizedAttributes !== null && sanitizedAttributes !== void 0 ? sanitizedAttributes : null;
    const path = rendererPath(block, urlAttributes, urlQueryArgs);
    const data = isPostRequest ? {
      attributes: sanitizedAttributes !== null && sanitizedAttributes !== void 0 ? sanitizedAttributes : null
    } : null; // Store the latest fetch request so that when we process it, we can
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

  const debouncedFetchData = (0,external_wp_compose_namespaceObject.useDebounce)(fetchData, 500); // When the component unmounts, set isMountedRef to false. This will
  // let the async fetch callbacks know when to stop.

  (0,external_wp_element_namespaceObject.useEffect)(() => () => {
    isMountedRef.current = false;
  }, []);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // Don't debounce the first fetch. This ensures that the first render
    // shows data as soon as possible.
    if (prevProps === undefined) {
      fetchData();
    } else if (!(0,external_lodash_namespaceObject.isEqual)(prevProps, props)) {
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
  const hasError = response === null || response === void 0 ? void 0 : response.error;

  if (isLoading) {
    return (0,external_wp_element_namespaceObject.createElement)(LoadingResponsePlaceholder, _extends({}, props, {
      showLoader: showLoader
    }), hasResponse && (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.RawHTML, {
      className: className
    }, response));
  }

  if (hasEmptyResponse || !hasResponse) {
    return (0,external_wp_element_namespaceObject.createElement)(EmptyResponsePlaceholder, props);
  }

  if (hasError) {
    return (0,external_wp_element_namespaceObject.createElement)(ErrorResponsePlaceholder, _extends({
      response: response
    }, props));
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.RawHTML, {
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

const EMPTY_OBJECT = {};
const ExportedServerSideRender = (0,external_wp_data_namespaceObject.withSelect)(select => {
  // FIXME: @wordpress/server-side-render should not depend on @wordpress/editor.
  // It is used by blocks that can be loaded into a *non-post* block editor.
  // eslint-disable-next-line @wordpress/data-no-store-string-literals
  const coreEditorSelect = select('core/editor');

  if (coreEditorSelect) {
    const currentPostId = coreEditorSelect.getCurrentPostId(); // For templates and template parts we use a custom ID format.
    // Since they aren't real posts, we don't want to use their ID
    // for server-side rendering. Since they use a string based ID,
    // we can assume real post IDs are numbers.

    if (currentPostId && typeof currentPostId === 'number') {
      return {
        currentPostId
      };
    }
  }

  return EMPTY_OBJECT;
})(_ref => {
  let {
    urlQueryArgs = EMPTY_OBJECT,
    currentPostId,
    ...props
  } = _ref;
  const newUrlQueryArgs = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!currentPostId) {
      return urlQueryArgs;
    }

    return {
      post_id: currentPostId,
      ...urlQueryArgs
    };
  }, [currentPostId, urlQueryArgs]);
  return (0,external_wp_element_namespaceObject.createElement)(ServerSideRender, _extends({
    urlQueryArgs: newUrlQueryArgs
  }, props));
});

if (window && window.wp && window.wp.components) {
  window.wp.components.ServerSideRender = (0,external_wp_element_namespaceObject.forwardRef)((props, ref) => {
    external_wp_deprecated_default()('wp.components.ServerSideRender', {
      version: '6.2',
      since: '5.3',
      alternative: 'wp.serverSideRender'
    });
    return (0,external_wp_element_namespaceObject.createElement)(ExportedServerSideRender, _extends({}, props, {
      ref: ref
    }));
  });
}

/* harmony default export */ var build_module = (ExportedServerSideRender);

(window.wp = window.wp || {}).serverSideRender = __webpack_exports__["default"];
/******/ })()
;