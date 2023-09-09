<<<<<<< HEAD
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
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ build_module)
});

;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js
/**
 * @param {string} nonce
 * @return {import('../types').APIFetchMiddleware & { nonce: string }} A middleware to enhance a request with a nonce.
 */
function createNonceMiddleware(nonce) {
  /**
   * @type {import('../types').APIFetchMiddleware & { nonce: string }}
   */
  const middleware = (options, next) => {
    const {
      headers = {}
    } = options; // If an 'X-WP-Nonce' header (or any case-insensitive variation
    // thereof) was specified, no need to add a nonce header.

    for (const headerName in headers) {
      if (headerName.toLowerCase() === 'x-wp-nonce' && headers[headerName] === middleware.nonce) {
        return next(options);
      }
    }

    return next({ ...options,
      headers: { ...headers,
        'X-WP-Nonce': middleware.nonce
      }
    });
  };

  middleware.nonce = nonce;
  return middleware;
}

/* harmony default export */ const nonce = (createNonceMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js
/**
 * @type {import('../types').APIFetchMiddleware}
 */
const namespaceAndEndpointMiddleware = (options, next) => {
  let path = options.path;
  let namespaceTrimmed, endpointTrimmed;
=======
this["wp"] = this["wp"] || {}; this["wp"]["apiFetch"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "jqrR");
/******/ })
/************************************************************************/
/******/ ({

/***/ "Ff2n":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _objectWithoutProperties; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ "HaE+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _asyncToGenerator; });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ "jqrR":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__("g56x");

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js


/**
 * External dependencies
 */


var nonce_createNonceMiddleware = function createNonceMiddleware(nonce) {
  var usedNonce = nonce;
  /**
   * This is not ideal but it's fine for now.
   *
   * Configure heartbeat to refresh the wp-api nonce, keeping the editor
   * authorization intact.
   */

  Object(external_this_wp_hooks_["addAction"])('heartbeat.tick', 'core/api-fetch/create-nonce-middleware', function (response) {
    if (response['rest-nonce']) {
      usedNonce = response['rest-nonce'];
    }
  });
  return function (options, next) {
    var headers = options.headers || {}; // If an 'X-WP-Nonce' header (or any case-insensitive variation
    // thereof) was specified, no need to add a nonce header.

    var addNonceHeader = true;

    for (var headerName in headers) {
      if (headers.hasOwnProperty(headerName)) {
        if (headerName.toLowerCase() === 'x-wp-nonce') {
          addNonceHeader = false;
          break;
        }
      }
    }

    if (addNonceHeader) {
      // Do not mutate the original headers object, if any.
      headers = Object(objectSpread["a" /* default */])({}, headers, {
        'X-WP-Nonce': usedNonce
      });
    }

    return next(Object(objectSpread["a" /* default */])({}, options, {
      headers: headers
    }));
  };
};

/* harmony default export */ var middlewares_nonce = (nonce_createNonceMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js


var namespace_endpoint_namespaceAndEndpointMiddleware = function namespaceAndEndpointMiddleware(options, next) {
  var path = options.path;
  var namespaceTrimmed, endpointTrimmed;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (typeof options.namespace === 'string' && typeof options.endpoint === 'string') {
    namespaceTrimmed = options.namespace.replace(/^\/|\/$/g, '');
    endpointTrimmed = options.endpoint.replace(/^\//, '');

    if (endpointTrimmed) {
      path = namespaceTrimmed + '/' + endpointTrimmed;
    } else {
      path = namespaceTrimmed;
    }
  }

  delete options.namespace;
  delete options.endpoint;
<<<<<<< HEAD
  return next({ ...options,
    path
  });
};

/* harmony default export */ const namespace_endpoint = (namespaceAndEndpointMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js
=======
  return next(Object(objectSpread["a" /* default */])({}, options, {
    path: path
  }));
};

/* harmony default export */ var namespace_endpoint = (namespace_endpoint_namespaceAndEndpointMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/**
 * @param {string} rootURL
 * @return {import('../types').APIFetchMiddleware} Root URL middleware.
 */

const createRootURLMiddleware = rootURL => (options, next) => {
  return namespace_endpoint(options, optionsWithPath => {
    let url = optionsWithPath.url;
    let path = optionsWithPath.path;
    let apiRoot;

    if (typeof path === 'string') {
      apiRoot = rootURL;

      if (-1 !== rootURL.indexOf('?')) {
        path = path.replace('?', '&');
      }

      path = path.replace(/^\//, ''); // API root may already include query parameter prefix if site is
      // configured to use plain permalinks.

      if ('string' === typeof apiRoot && -1 !== apiRoot.indexOf('?')) {
        path = path.replace('?', '&');
      }

      url = apiRoot + path;
    }

    return next({ ...optionsWithPath,
      url
    });
  });
};

/* harmony default export */ const root_url = (createRootURLMiddleware);

;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js
/**
 * WordPress dependencies
 */

/**
 * @param {Record<string, any>} preloadedData
 * @return {import('../types').APIFetchMiddleware} Preloading middleware.
 */

function createPreloadingMiddleware(preloadedData) {
  const cache = Object.fromEntries(Object.entries(preloadedData).map(([path, data]) => [(0,external_wp_url_namespaceObject.normalizePath)(path), data]));
  return (options, next) => {
    const {
      parse = true
    } = options;
    /** @type {string | void} */

    let rawPath = options.path;

    if (!rawPath && options.url) {
      const {
        rest_route: pathFromQuery,
        ...queryArgs
      } = (0,external_wp_url_namespaceObject.getQueryArgs)(options.url);

      if (typeof pathFromQuery === 'string') {
        rawPath = (0,external_wp_url_namespaceObject.addQueryArgs)(pathFromQuery, queryArgs);
      }
    }

    if (typeof rawPath !== 'string') {
      return next(options);
    }

    const method = options.method || 'GET';
    const path = (0,external_wp_url_namespaceObject.normalizePath)(rawPath);

    if ('GET' === method && cache[path]) {
      const cacheData = cache[path]; // Unsetting the cache key ensures that the data is only used a single time.

      delete cache[path];
      return prepareResponse(cacheData, !!parse);
    } else if ('OPTIONS' === method && cache[method] && cache[method][path]) {
      const cacheData = cache[method][path]; // Unsetting the cache key ensures that the data is only used a single time.

      delete cache[method][path];
      return prepareResponse(cacheData, !!parse);
=======

var root_url_createRootURLMiddleware = function createRootURLMiddleware(rootURL) {
  return function (options, next) {
    return namespace_endpoint(options, function (optionsWithPath) {
      var url = optionsWithPath.url;
      var path = optionsWithPath.path;
      var apiRoot;

      if (typeof path === 'string') {
        apiRoot = rootURL;

        if (-1 !== rootURL.indexOf('?')) {
          path = path.replace('?', '&');
        }

        path = path.replace(/^\//, ''); // API root may already include query parameter prefix if site is
        // configured to use plain permalinks.

        if ('string' === typeof apiRoot && -1 !== apiRoot.indexOf('?')) {
          path = path.replace('?', '&');
        }

        url = apiRoot + path;
      }

      return next(Object(objectSpread["a" /* default */])({}, optionsWithPath, {
        url: url
      }));
    });
  };
};

/* harmony default export */ var root_url = (root_url_createRootURLMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js
var createPreloadingMiddleware = function createPreloadingMiddleware(preloadedData) {
  return function (options, next) {
    function getStablePath(path) {
      var splitted = path.split('?');
      var query = splitted[1];
      var base = splitted[0];

      if (!query) {
        return base;
      } // 'b=1&c=2&a=5'


      return base + '?' + query // [ 'b=1', 'c=2', 'a=5' ]
      .split('&') // [ [ 'b, '1' ], [ 'c', '2' ], [ 'a', '5' ] ]
      .map(function (entry) {
        return entry.split('=');
      }) // [ [ 'a', '5' ], [ 'b, '1' ], [ 'c', '2' ] ]
      .sort(function (a, b) {
        return a[0].localeCompare(b[0]);
      }) // [ 'a=5', 'b=1', 'c=2' ]
      .map(function (pair) {
        return pair.join('=');
      }) // 'a=5&b=1&c=2'
      .join('&');
    }

    var _options$parse = options.parse,
        parse = _options$parse === void 0 ? true : _options$parse;

    if (typeof options.path === 'string') {
      var method = options.method || 'GET';
      var path = getStablePath(options.path);

      if (parse && 'GET' === method && preloadedData[path]) {
        return Promise.resolve(preloadedData[path].body);
      } else if ('OPTIONS' === method && preloadedData[method][path]) {
        return Promise.resolve(preloadedData[method][path]);
      }
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }

    return next(options);
  };
<<<<<<< HEAD
}
/**
 * This is a helper function that sends a success response.
 *
 * @param {Record<string, any>} responseData
 * @param {boolean}             parse
 * @return {Promise<any>} Promise with the response.
 */


function prepareResponse(responseData, parse) {
  return Promise.resolve(parse ? responseData.body : new window.Response(JSON.stringify(responseData.body), {
    status: 200,
    statusText: 'OK',
    headers: responseData.headers
  }));
}

/* harmony default export */ const preloading = (createPreloadingMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Apply query arguments to both URL and Path, whichever is present.
 *
 * @param {import('../types').APIFetchOptions} props
 * @param {Record<string, string | number>}    queryArgs
 * @return {import('../types').APIFetchOptions} The request with the modified query args
 */

const modifyQuery = ({
  path,
  url,
  ...options
}, queryArgs) => ({ ...options,
  url: url && (0,external_wp_url_namespaceObject.addQueryArgs)(url, queryArgs),
  path: path && (0,external_wp_url_namespaceObject.addQueryArgs)(path, queryArgs)
});
/**
 * Duplicates parsing functionality from apiFetch.
 *
 * @param {Response} response
 * @return {Promise<any>} Parsed response json.
 */


const parseResponse = response => response.json ? response.json() : Promise.reject(response);
/**
 * @param {string | null} linkHeader
 * @return {{ next?: string }} The parsed link header.
 */


const parseLinkHeader = linkHeader => {
=======
};

/* harmony default export */ var preloading = (createPreloadingMiddleware);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__("HaE+");

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__("Mmq9");

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js




/**
 * WordPress dependencies
 */
 // Apply query arguments to both URL and Path, whichever is present.

var fetch_all_middleware_modifyQuery = function modifyQuery(_ref, queryArgs) {
  var path = _ref.path,
      url = _ref.url,
      options = Object(objectWithoutProperties["a" /* default */])(_ref, ["path", "url"]);

  return Object(objectSpread["a" /* default */])({}, options, {
    url: url && Object(external_this_wp_url_["addQueryArgs"])(url, queryArgs),
    path: path && Object(external_this_wp_url_["addQueryArgs"])(path, queryArgs)
  });
}; // Duplicates parsing functionality from apiFetch.


var fetch_all_middleware_parseResponse = function parseResponse(response) {
  return response.json ? response.json() : Promise.reject(response);
};

var parseLinkHeader = function parseLinkHeader(linkHeader) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  if (!linkHeader) {
    return {};
  }

<<<<<<< HEAD
  const match = linkHeader.match(/<([^>]+)>; rel="next"/);
=======
  var match = linkHeader.match(/<([^>]+)>; rel="next"/);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  return match ? {
    next: match[1]
  } : {};
};
<<<<<<< HEAD
/**
 * @param {Response} response
 * @return {string | undefined} The next page URL.
 */


const getNextPageUrl = response => {
  const {
    next
  } = parseLinkHeader(response.headers.get('link'));
  return next;
};
/**
 * @param {import('../types').APIFetchOptions} options
 * @return {boolean} True if the request contains an unbounded query.
 */


const requestContainsUnboundedQuery = options => {
  const pathIsUnbounded = !!options.path && options.path.indexOf('per_page=-1') !== -1;
  const urlIsUnbounded = !!options.url && options.url.indexOf('per_page=-1') !== -1;
  return pathIsUnbounded || urlIsUnbounded;
};
/**
 * The REST API enforces an upper limit on the per_page option. To handle large
 * collections, apiFetch consumers can pass `per_page=-1`; this middleware will
 * then recursively assemble a full response array from all available pages.
 *
 * @type {import('../types').APIFetchMiddleware}
 */


const fetchAllMiddleware = async (options, next) => {
  if (options.parse === false) {
    // If a consumer has opted out of parsing, do not apply middleware.
    return next(options);
  }

  if (!requestContainsUnboundedQuery(options)) {
    // If neither url nor path is requesting all items, do not apply middleware.
    return next(options);
  } // Retrieve requested page of results.


  const response = await build_module({ ...modifyQuery(options, {
      per_page: 100
    }),
    // Ensure headers are returned for page 1.
    parse: false
  });
  const results = await parseResponse(response);

  if (!Array.isArray(results)) {
    // We have no reliable way of merging non-array results.
    return results;
  }

  let nextPage = getNextPageUrl(response);

  if (!nextPage) {
    // There are no further pages to request.
    return results;
  } // Iteratively fetch all remaining pages until no "next" header is found.


  let mergedResults =
  /** @type {any[]} */
  [].concat(results);

  while (nextPage) {
    const nextResponse = await build_module({ ...options,
      // Ensure the URL for the next page is used instead of any provided path.
      path: undefined,
      url: nextPage,
      // Ensure we still get headers so we can identify the next page.
      parse: false
    });
    const nextResults = await parseResponse(nextResponse);
    mergedResults = mergedResults.concat(nextResults);
    nextPage = getNextPageUrl(nextResponse);
  }

  return mergedResults;
};

/* harmony default export */ const fetch_all_middleware = (fetchAllMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js
/**
 * Set of HTTP methods which are eligible to be overridden.
 *
 * @type {Set<string>}
 */
const OVERRIDE_METHODS = new Set(['PATCH', 'PUT', 'DELETE']);
=======

var getNextPageUrl = function getNextPageUrl(response) {
  var _parseLinkHeader = parseLinkHeader(response.headers.get('link')),
      next = _parseLinkHeader.next;

  return next;
};

var requestContainsUnboundedQuery = function requestContainsUnboundedQuery(options) {
  var pathIsUnbounded = options.path && options.path.indexOf('per_page=-1') !== -1;
  var urlIsUnbounded = options.url && options.url.indexOf('per_page=-1') !== -1;
  return pathIsUnbounded || urlIsUnbounded;
}; // The REST API enforces an upper limit on the per_page option. To handle large
// collections, apiFetch consumers can pass `per_page=-1`; this middleware will
// then recursively assemble a full response array from all available pages.


var fetchAllMiddleware =
/*#__PURE__*/
function () {
  var _ref2 = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regeneratorRuntime.mark(function _callee(options, next) {
    var response, results, nextPage, mergedResults, nextResponse, nextResults;
    return regeneratorRuntime.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            if (!(options.parse === false)) {
              _context.next = 2;
              break;
            }

            return _context.abrupt("return", next(options));

          case 2:
            if (requestContainsUnboundedQuery(options)) {
              _context.next = 4;
              break;
            }

            return _context.abrupt("return", next(options));

          case 4:
            _context.next = 6;
            return next(Object(objectSpread["a" /* default */])({}, fetch_all_middleware_modifyQuery(options, {
              per_page: 100
            }), {
              // Ensure headers are returned for page 1.
              parse: false
            }));

          case 6:
            response = _context.sent;
            _context.next = 9;
            return fetch_all_middleware_parseResponse(response);

          case 9:
            results = _context.sent;

            if (Array.isArray(results)) {
              _context.next = 12;
              break;
            }

            return _context.abrupt("return", results);

          case 12:
            nextPage = getNextPageUrl(response);

            if (nextPage) {
              _context.next = 15;
              break;
            }

            return _context.abrupt("return", results);

          case 15:
            // Iteratively fetch all remaining pages until no "next" header is found.
            mergedResults = [].concat(results);

          case 16:
            if (!nextPage) {
              _context.next = 27;
              break;
            }

            _context.next = 19;
            return next(Object(objectSpread["a" /* default */])({}, options, {
              // Ensure the URL for the next page is used instead of any provided path.
              path: undefined,
              url: nextPage,
              // Ensure we still get headers so we can identify the next page.
              parse: false
            }));

          case 19:
            nextResponse = _context.sent;
            _context.next = 22;
            return fetch_all_middleware_parseResponse(nextResponse);

          case 22:
            nextResults = _context.sent;
            mergedResults = mergedResults.concat(nextResults);
            nextPage = getNextPageUrl(nextResponse);
            _context.next = 16;
            break;

          case 27:
            return _context.abrupt("return", mergedResults);

          case 28:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, this);
  }));

  return function fetchAllMiddleware(_x, _x2) {
    return _ref2.apply(this, arguments);
  };
}();

/* harmony default export */ var fetch_all_middleware = (fetchAllMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js


/**
 * Set of HTTP methods which are eligible to be overridden.
 *
 * @type {Set}
 */
var OVERRIDE_METHODS = new Set(['PATCH', 'PUT', 'DELETE']);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Default request method.
 *
 * "A request has an associated method (a method). Unless stated otherwise it
 * is `GET`."
 *
 * @see  https://fetch.spec.whatwg.org/#requests
 *
 * @type {string}
 */

<<<<<<< HEAD
const DEFAULT_METHOD = 'GET';
=======
var DEFAULT_METHOD = 'GET';
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * API Fetch middleware which overrides the request method for HTTP v1
 * compatibility leveraging the REST API X-HTTP-Method-Override header.
 *
<<<<<<< HEAD
 * @type {import('../types').APIFetchMiddleware}
 */

const httpV1Middleware = (options, next) => {
  const {
    method = DEFAULT_METHOD
  } = options;

  if (OVERRIDE_METHODS.has(method.toUpperCase())) {
    options = { ...options,
      headers: { ...options.headers,
        'X-HTTP-Method-Override': method,
        'Content-Type': 'application/json'
      },
      method: 'POST'
    };
  }

  return next(options);
};

/* harmony default export */ const http_v1 = (httpV1Middleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js
=======
 * @param {Object}   options Fetch options.
 * @param {Function} next    [description]
 *
 * @return {*} The evaluated result of the remaining middleware chain.
 */

function httpV1Middleware(options, next) {
  var _options = options,
      _options$method = _options.method,
      method = _options$method === void 0 ? DEFAULT_METHOD : _options$method;

  if (OVERRIDE_METHODS.has(method.toUpperCase())) {
    options = Object(objectSpread["a" /* default */])({}, options, {
      headers: Object(objectSpread["a" /* default */])({}, options.headers, {
        'X-HTTP-Method-Override': method,
        'Content-Type': 'application/json'
      }),
      method: 'POST'
    });
  }

  return next(options, next);
}

/* harmony default export */ var http_v1 = (httpV1Middleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
/**
 * @type {import('../types').APIFetchMiddleware}
 */

const userLocaleMiddleware = (options, next) => {
  if (typeof options.url === 'string' && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.url, '_locale')) {
    options.url = (0,external_wp_url_namespaceObject.addQueryArgs)(options.url, {
=======

function userLocaleMiddleware(options, next) {
  if (typeof options.url === 'string' && !Object(external_this_wp_url_["hasQueryArg"])(options.url, '_locale')) {
    options.url = Object(external_this_wp_url_["addQueryArgs"])(options.url, {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      _locale: 'user'
    });
  }

<<<<<<< HEAD
  if (typeof options.path === 'string' && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.path, '_locale')) {
    options.path = (0,external_wp_url_namespaceObject.addQueryArgs)(options.path, {
=======
  if (typeof options.path === 'string' && !Object(external_this_wp_url_["hasQueryArg"])(options.path, '_locale')) {
    options.path = Object(external_this_wp_url_["addQueryArgs"])(options.path, {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      _locale: 'user'
    });
  }

<<<<<<< HEAD
  return next(options);
};

/* harmony default export */ const user_locale = (userLocaleMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/utils/response.js
/**
 * WordPress dependencies
 */

/**
 * Parses the apiFetch response.
 *
 * @param {Response} response
 * @param {boolean}  shouldParseResponse
 *
 * @return {Promise<any> | null | Response} Parsed response.
 */

const response_parseResponse = (response, shouldParseResponse = true) => {
  if (shouldParseResponse) {
    if (response.status === 204) {
      return null;
    }

    return response.json ? response.json() : Promise.reject(response);
  }

  return response;
};
/**
 * Calls the `json` function on the Response, throwing an error if the response
 * doesn't have a json function or if parsing the json itself fails.
 *
 * @param {Response} response
 * @return {Promise<any>} Parsed response.
 */


const parseJsonAndNormalizeError = response => {
  const invalidJsonError = {
    code: 'invalid_json',
    message: (0,external_wp_i18n_namespaceObject.__)('The response is not a valid JSON response.')
  };

  if (!response || !response.json) {
    throw invalidJsonError;
  }

  return response.json().catch(() => {
    throw invalidJsonError;
  });
};
/**
 * Parses the apiFetch response properly and normalize response errors.
 *
 * @param {Response} response
 * @param {boolean}  shouldParseResponse
 *
 * @return {Promise<any>} Parsed response.
 */


const parseResponseAndNormalizeError = (response, shouldParseResponse = true) => {
  return Promise.resolve(response_parseResponse(response, shouldParseResponse)).catch(res => parseAndThrowError(res, shouldParseResponse));
};
/**
 * Parses a response, throwing an error if parsing the response fails.
 *
 * @param {Response} response
 * @param {boolean}  shouldParseResponse
 * @return {Promise<any>} Parsed response.
 */

function parseAndThrowError(response, shouldParseResponse = true) {
  if (!shouldParseResponse) {
    throw response;
  }

  return parseJsonAndNormalizeError(response).then(error => {
    const unknownError = {
      code: 'unknown_error',
      message: (0,external_wp_i18n_namespaceObject.__)('An unknown error occurred.')
    };
    throw error || unknownError;
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/media-upload.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * @param {import('../types').APIFetchOptions} options
 * @return {boolean} True if the request is for media upload.
 */

function isMediaUploadRequest(options) {
  const isCreateMethod = !!options.method && options.method === 'POST';
  const isMediaEndpoint = !!options.path && options.path.indexOf('/wp/v2/media') !== -1 || !!options.url && options.url.indexOf('/wp/v2/media') !== -1;
  return isMediaEndpoint && isCreateMethod;
}
/**
 * Middleware handling media upload failures and retries.
 *
 * @type {import('../types').APIFetchMiddleware}
 */


const mediaUploadMiddleware = (options, next) => {
  if (!isMediaUploadRequest(options)) {
    return next(options);
  }

  let retries = 0;
  const maxRetries = 5;
  /**
   * @param {string} attachmentId
   * @return {Promise<any>} Processed post response.
   */

  const postProcess = attachmentId => {
    retries++;
    return next({
      path: `/wp/v2/media/${attachmentId}/post-process`,
      method: 'POST',
      data: {
        action: 'create-image-subsizes'
      },
      parse: false
    }).catch(() => {
      if (retries < maxRetries) {
        return postProcess(attachmentId);
      }

      next({
        path: `/wp/v2/media/${attachmentId}?force=true`,
        method: 'DELETE'
      });
      return Promise.reject();
    });
  };

  return next({ ...options,
    parse: false
  }).catch(response => {
    const attachmentId = response.headers.get('x-wp-upload-attachment-id');

    if (response.status >= 500 && response.status < 600 && attachmentId) {
      return postProcess(attachmentId).catch(() => {
        if (options.parse !== false) {
          return Promise.reject({
            code: 'post_process',
            message: (0,external_wp_i18n_namespaceObject.__)('Media upload failed. If this is a photo or a large image, please scale it down and try again.')
          });
        }

        return Promise.reject(response);
      });
    }

    return parseAndThrowError(response, options.parse);
  }).then(response => parseResponseAndNormalizeError(response, options.parse));
};

/* harmony default export */ const media_upload = (mediaUploadMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/middlewares/theme-preview.js
/**
 * WordPress dependencies
 */

/**
 * This appends a `wp_theme_preview` parameter to the REST API request URL if
 * the admin URL contains a `theme` GET parameter.
 *
 * @param {Record<string, any>} themePath
 * @return {import('../types').APIFetchMiddleware} Preloading middleware.
 */

const createThemePreviewMiddleware = themePath => (options, next) => {
  if (typeof options.url === 'string' && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.url, 'wp_theme_preview')) {
    options.url = (0,external_wp_url_namespaceObject.addQueryArgs)(options.url, {
      wp_theme_preview: themePath
    });
  }

  if (typeof options.path === 'string' && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.path, 'wp_theme_preview')) {
    options.path = (0,external_wp_url_namespaceObject.addQueryArgs)(options.path, {
      wp_theme_preview: themePath
    });
  }

  return next(options);
};

/* harmony default export */ const theme_preview = (createThemePreviewMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/index.js
=======
  return next(options, next);
}

/* harmony default export */ var user_locale = (userLocaleMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/api-fetch/build-module/index.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */








<<<<<<< HEAD



=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Default set of header values which should be sent with every request unless
 * explicitly provided through apiFetch options.
 *
<<<<<<< HEAD
 * @type {Record<string, string>}
 */

const DEFAULT_HEADERS = {
=======
 * @type {Object}
 */

var DEFAULT_HEADERS = {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  // The backend uses the Accept header as a condition for considering an
  // incoming request as a REST request.
  //
  // See: https://core.trac.wordpress.org/ticket/44534
  Accept: 'application/json, */*;q=0.1'
};
/**
 * Default set of fetch option values which should be sent with every request
 * unless explicitly provided through apiFetch options.
 *
 * @type {Object}
 */

<<<<<<< HEAD
const DEFAULT_OPTIONS = {
  credentials: 'include'
};
/** @typedef {import('./types').APIFetchMiddleware} APIFetchMiddleware */

/** @typedef {import('./types').APIFetchOptions} APIFetchOptions */

/**
 * @type {import('./types').APIFetchMiddleware[]}
 */

const middlewares = [user_locale, namespace_endpoint, http_v1, fetch_all_middleware];
/**
 * Register a middleware
 *
 * @param {import('./types').APIFetchMiddleware} middleware
 */

function registerMiddleware(middleware) {
  middlewares.unshift(middleware);
}
/**
 * Checks the status of a response, throwing the Response as an error if
 * it is outside the 200 range.
 *
 * @param {Response} response
 * @return {Response} The response if the status is in the 200 range.
 */


const checkStatus = response => {
  if (response.status >= 200 && response.status < 300) {
    return response;
  }

  throw response;
};
/** @typedef {(options: import('./types').APIFetchOptions) => Promise<any>} FetchHandler*/

/**
 * @type {FetchHandler}
 */


const defaultFetchHandler = nextOptions => {
  const {
    url,
    path,
    data,
    parse = true,
    ...remainingOptions
  } = nextOptions;
  let {
    body,
    headers
  } = nextOptions; // Merge explicitly-provided headers with default values.

  headers = { ...DEFAULT_HEADERS,
    ...headers
  }; // The `data` property is a shorthand for sending a JSON body.

  if (data) {
    body = JSON.stringify(data);
    headers['Content-Type'] = 'application/json';
  }

  const responsePromise = window.fetch( // Fall back to explicitly passing `window.location` which is the behavior if `undefined` is passed.
  url || path || window.location.href, { ...DEFAULT_OPTIONS,
    ...remainingOptions,
    body,
    headers
  });
  return responsePromise.then(value => Promise.resolve(value).then(checkStatus).catch(response => parseAndThrowError(response, parse)).then(response => parseResponseAndNormalizeError(response, parse)), err => {
    // Re-throw AbortError for the users to handle it themselves.
    if (err && err.name === 'AbortError') {
      throw err;
    } // Otherwise, there is most likely no network connection.
    // Unfortunately the message might depend on the browser.


    throw {
      code: 'fetch_error',
      message: (0,external_wp_i18n_namespaceObject.__)('You are probably offline.')
    };
  });
};
/** @type {FetchHandler} */


let fetchHandler = defaultFetchHandler;
/**
 * Defines a custom fetch handler for making the requests that will override
 * the default one using window.fetch
 *
 * @param {FetchHandler} newFetchHandler The new fetch handler
 */

function setFetchHandler(newFetchHandler) {
  fetchHandler = newFetchHandler;
}
/**
 * @template T
 * @param {import('./types').APIFetchOptions} options
 * @return {Promise<T>} A promise representing the request processed via the registered middlewares.
 */


function apiFetch(options) {
  // creates a nested function chain that calls all middlewares and finally the `fetchHandler`,
  // converting `middlewares = [ m1, m2, m3 ]` into:
  // ```
  // opts1 => m1( opts1, opts2 => m2( opts2, opts3 => m3( opts3, fetchHandler ) ) );
  // ```
  const enhancedHandler = middlewares.reduceRight((
  /** @type {FetchHandler} */
  next, middleware) => {
    return workingOptions => middleware(workingOptions, next);
  }, fetchHandler);
  return enhancedHandler(options).catch(error => {
    if (error.code !== 'rest_cookie_invalid_nonce') {
      return Promise.reject(error);
    } // If the nonce is invalid, refresh it and try again.


    return window // @ts-ignore
    .fetch(apiFetch.nonceEndpoint).then(checkStatus).then(data => data.text()).then(text => {
      // @ts-ignore
      apiFetch.nonceMiddleware.nonce = text;
      return apiFetch(options);
    });
  });
}

apiFetch.use = registerMiddleware;
apiFetch.setFetchHandler = setFetchHandler;
apiFetch.createNonceMiddleware = nonce;
apiFetch.createPreloadingMiddleware = preloading;
apiFetch.createRootURLMiddleware = root_url;
apiFetch.fetchAllMiddleware = fetch_all_middleware;
apiFetch.mediaUploadMiddleware = media_upload;
apiFetch.createThemePreviewMiddleware = theme_preview;
/* harmony default export */ const build_module = (apiFetch);

(window.wp = window.wp || {}).apiFetch = __webpack_exports__["default"];
/******/ })()
;
=======
var DEFAULT_OPTIONS = {
  credentials: 'include'
};
var middlewares = [];

function registerMiddleware(middleware) {
  middlewares.push(middleware);
}

function apiFetch(options) {
  var raw = function raw(nextOptions) {
    var url = nextOptions.url,
        path = nextOptions.path,
        data = nextOptions.data,
        _nextOptions$parse = nextOptions.parse,
        parse = _nextOptions$parse === void 0 ? true : _nextOptions$parse,
        remainingOptions = Object(objectWithoutProperties["a" /* default */])(nextOptions, ["url", "path", "data", "parse"]);

    var body = nextOptions.body,
        headers = nextOptions.headers; // Merge explicitly-provided headers with default values.

    headers = Object(objectSpread["a" /* default */])({}, DEFAULT_HEADERS, headers); // The `data` property is a shorthand for sending a JSON body.

    if (data) {
      body = JSON.stringify(data);
      headers['Content-Type'] = 'application/json';
    }

    var responsePromise = window.fetch(url || path, Object(objectSpread["a" /* default */])({}, DEFAULT_OPTIONS, remainingOptions, {
      body: body,
      headers: headers
    }));

    var checkStatus = function checkStatus(response) {
      if (response.status >= 200 && response.status < 300) {
        return response;
      }

      throw response;
    };

    var parseResponse = function parseResponse(response) {
      if (parse) {
        if (response.status === 204) {
          return null;
        }

        return response.json ? response.json() : Promise.reject(response);
      }

      return response;
    };

    return responsePromise.then(checkStatus).then(parseResponse).catch(function (response) {
      if (!parse) {
        throw response;
      }

      var invalidJsonError = {
        code: 'invalid_json',
        message: Object(external_this_wp_i18n_["__"])('The response is not a valid JSON response.')
      };

      if (!response || !response.json) {
        throw invalidJsonError;
      }

      return response.json().catch(function () {
        throw invalidJsonError;
      }).then(function (error) {
        var unknownError = {
          code: 'unknown_error',
          message: Object(external_this_wp_i18n_["__"])('An unknown error occurred.')
        };
        throw error || unknownError;
      });
    });
  };

  var steps = [raw, fetch_all_middleware, http_v1, namespace_endpoint, user_locale].concat(middlewares).reverse();

  var runMiddleware = function runMiddleware(index) {
    return function (nextOptions) {
      var nextMiddleware = steps[index];
      var next = runMiddleware(index + 1);
      return nextMiddleware(nextOptions, next);
    };
  };

  return runMiddleware(0)(options);
}

apiFetch.use = registerMiddleware;
apiFetch.createNonceMiddleware = middlewares_nonce;
apiFetch.createPreloadingMiddleware = preloading;
apiFetch.createRootURLMiddleware = root_url;
apiFetch.fetchAllMiddleware = fetch_all_middleware;
/* harmony default export */ var build_module = __webpack_exports__["default"] = (apiFetch);


/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "rePB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ })

/******/ })["default"];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
