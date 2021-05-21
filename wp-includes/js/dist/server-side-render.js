this["wp"] = this["wp"] || {}; this["wp"]["serverSideRender"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "4dqW");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "4dqW":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__("ywyh");
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__("Mmq9");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// CONCATENATED MODULE: ./node_modules/@wordpress/server-side-render/build-module/server-side-render.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








function rendererPath(block, attributes = null, urlQueryArgs = {}) {
  return Object(external_wp_url_["addQueryArgs"])(`/wp/v2/block-renderer/${block}`, {
    context: 'edit',
    ...(null !== attributes ? {
      attributes
    } : {}),
    ...urlQueryArgs
  });
}

function DefaultEmptyResponsePlaceholder({
  className
}) {
  return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
    className: className
  }, Object(external_wp_i18n_["__"])('Block rendered as empty.'));
}

function DefaultErrorResponsePlaceholder({
  response,
  className
}) {
  const errorMessage = Object(external_wp_i18n_["sprintf"])( // translators: %s: error message describing the problem
  Object(external_wp_i18n_["__"])('Error loading block: %s'), response.errorMsg);
  return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
    className: className
  }, errorMessage);
}

function DefaultLoadingResponsePlaceholder({
  className
}) {
  return Object(external_wp_element_["createElement"])(external_wp_components_["Placeholder"], {
    className: className
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Spinner"], null));
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
  const isMountedRef = Object(external_wp_element_["useRef"])(true);
  const fetchRequestRef = Object(external_wp_element_["useRef"])();
  const [response, setResponse] = Object(external_wp_element_["useState"])(null);
  const prevProps = Object(external_wp_compose_["usePrevious"])(props);

  function fetchData() {
    if (!isMountedRef.current) {
      return;
    }

    if (null !== response) {
      setResponse(null);
    }

    const sanitizedAttributes = attributes && Object(external_wp_blocks_["__experimentalSanitizeBlockAttributes"])(block, attributes); // If httpMethod is 'POST', send the attributes in the request body instead of the URL.
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
    });
    return fetchRequest;
  }

  const debouncedFetchData = Object(external_wp_compose_["useDebounce"])(fetchData, 500); // When the component unmounts, set isMountedRef to false. This will
  // let the async fetch callbacks know when to stop.

  Object(external_wp_element_["useEffect"])(() => () => {
    isMountedRef.current = false;
  }, []);
  Object(external_wp_element_["useEffect"])(() => {
    // Don't debounce the first fetch. This ensures that the first render
    // shows data as soon as possible
    if (prevProps === undefined) {
      fetchData();
    } else if (!Object(external_lodash_["isEqual"])(prevProps, props)) {
      debouncedFetchData();
    }
  });

  if (response === '') {
    return Object(external_wp_element_["createElement"])(EmptyResponsePlaceholder, props);
  } else if (!response) {
    return Object(external_wp_element_["createElement"])(LoadingResponsePlaceholder, props);
  } else if (response.error) {
    return Object(external_wp_element_["createElement"])(ErrorResponsePlaceholder, Object(esm_extends["a" /* default */])({
      response: response
    }, props));
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["RawHTML"], {
    className: className
  }, response);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/server-side-render/build-module/index.js



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
const ExportedServerSideRender = Object(external_wp_data_["withSelect"])(select => {
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
})(({
  urlQueryArgs = EMPTY_OBJECT,
  currentPostId,
  ...props
}) => {
  const newUrlQueryArgs = Object(external_wp_element_["useMemo"])(() => {
    if (!currentPostId) {
      return urlQueryArgs;
    }

    return {
      post_id: currentPostId,
      ...urlQueryArgs
    };
  }, [currentPostId, urlQueryArgs]);
  return Object(external_wp_element_["createElement"])(ServerSideRender, Object(esm_extends["a" /* default */])({
    urlQueryArgs: newUrlQueryArgs
  }, props));
});

if (window && window.wp && window.wp.components) {
  window.wp.components.ServerSideRender = Object(external_wp_element_["forwardRef"])((props, ref) => {
    external_wp_deprecated_default()('wp.components.ServerSideRender', {
      since: '5.3',
      alternative: 'wp.serverSideRender'
    });
    return Object(external_wp_element_["createElement"])(ExportedServerSideRender, Object(esm_extends["a" /* default */])({}, props, {
      ref: ref
    }));
  });
}

/* harmony default export */ var build_module = __webpack_exports__["default"] = (ExportedServerSideRender);


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["url"]; }());

/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "wx14":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
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

/***/ }),

/***/ "ywyh":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ })

/******/ })["default"];