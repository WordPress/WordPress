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
  "default": () => (/* binding */ index_default)
});

;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/nonce.js
function createNonceMiddleware(nonce) {
  const middleware = (options, next) => {
    const { headers = {} } = options;
    for (const headerName in headers) {
      if (headerName.toLowerCase() === "x-wp-nonce" && headers[headerName] === middleware.nonce) {
        return next(options);
      }
    }
    return next({
      ...options,
      headers: {
        ...headers,
        "X-WP-Nonce": middleware.nonce
      }
    });
  };
  middleware.nonce = nonce;
  return middleware;
}
var nonce_default = createNonceMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/namespace-endpoint.js
const namespaceAndEndpointMiddleware = (options, next) => {
  let path = options.path;
  let namespaceTrimmed, endpointTrimmed;
  if (typeof options.namespace === "string" && typeof options.endpoint === "string") {
    namespaceTrimmed = options.namespace.replace(/^\/|\/$/g, "");
    endpointTrimmed = options.endpoint.replace(/^\//, "");
    if (endpointTrimmed) {
      path = namespaceTrimmed + "/" + endpointTrimmed;
    } else {
      path = namespaceTrimmed;
    }
  }
  delete options.namespace;
  delete options.endpoint;
  return next({
    ...options,
    path
  });
};
var namespace_endpoint_default = namespaceAndEndpointMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/root-url.js

const createRootURLMiddleware = (rootURL) => (options, next) => {
  return namespace_endpoint_default(options, (optionsWithPath) => {
    let url = optionsWithPath.url;
    let path = optionsWithPath.path;
    let apiRoot;
    if (typeof path === "string") {
      apiRoot = rootURL;
      if (-1 !== rootURL.indexOf("?")) {
        path = path.replace("?", "&");
      }
      path = path.replace(/^\//, "");
      if ("string" === typeof apiRoot && -1 !== apiRoot.indexOf("?")) {
        path = path.replace("?", "&");
      }
      url = apiRoot + path;
    }
    return next({
      ...optionsWithPath,
      url
    });
  });
};
var root_url_default = createRootURLMiddleware;


;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/preloading.js

function createPreloadingMiddleware(preloadedData) {
  const cache = Object.fromEntries(
    Object.entries(preloadedData).map(([path, data]) => [
      (0,external_wp_url_namespaceObject.normalizePath)(path),
      data
    ])
  );
  return (options, next) => {
    const { parse = true } = options;
    let rawPath = options.path;
    if (!rawPath && options.url) {
      const { rest_route: pathFromQuery, ...queryArgs } = (0,external_wp_url_namespaceObject.getQueryArgs)(
        options.url
      );
      if (typeof pathFromQuery === "string") {
        rawPath = (0,external_wp_url_namespaceObject.addQueryArgs)(pathFromQuery, queryArgs);
      }
    }
    if (typeof rawPath !== "string") {
      return next(options);
    }
    const method = options.method || "GET";
    const path = (0,external_wp_url_namespaceObject.normalizePath)(rawPath);
    if ("GET" === method && cache[path]) {
      const cacheData = cache[path];
      delete cache[path];
      return prepareResponse(cacheData, !!parse);
    } else if ("OPTIONS" === method && cache[method] && cache[method][path]) {
      const cacheData = cache[method][path];
      delete cache[method][path];
      return prepareResponse(cacheData, !!parse);
    }
    return next(options);
  };
}
function prepareResponse(responseData, parse) {
  if (parse) {
    return Promise.resolve(responseData.body);
  }
  try {
    return Promise.resolve(
      new window.Response(JSON.stringify(responseData.body), {
        status: 200,
        statusText: "OK",
        headers: responseData.headers
      })
    );
  } catch {
    Object.entries(
      responseData.headers
    ).forEach(([key, value]) => {
      if (key.toLowerCase() === "link") {
        responseData.headers[key] = value.replace(
          /<([^>]+)>/,
          (_, url) => `<${encodeURI(url)}>`
        );
      }
    });
    return Promise.resolve(
      parse ? responseData.body : new window.Response(JSON.stringify(responseData.body), {
        status: 200,
        statusText: "OK",
        headers: responseData.headers
      })
    );
  }
}
var preloading_default = createPreloadingMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/fetch-all-middleware.js


const modifyQuery = ({ path, url, ...options }, queryArgs) => ({
  ...options,
  url: url && (0,external_wp_url_namespaceObject.addQueryArgs)(url, queryArgs),
  path: path && (0,external_wp_url_namespaceObject.addQueryArgs)(path, queryArgs)
});
const parseResponse = (response) => response.json ? response.json() : Promise.reject(response);
const parseLinkHeader = (linkHeader) => {
  if (!linkHeader) {
    return {};
  }
  const match = linkHeader.match(/<([^>]+)>; rel="next"/);
  return match ? {
    next: match[1]
  } : {};
};
const getNextPageUrl = (response) => {
  const { next } = parseLinkHeader(response.headers.get("link"));
  return next;
};
const requestContainsUnboundedQuery = (options) => {
  const pathIsUnbounded = !!options.path && options.path.indexOf("per_page=-1") !== -1;
  const urlIsUnbounded = !!options.url && options.url.indexOf("per_page=-1") !== -1;
  return pathIsUnbounded || urlIsUnbounded;
};
const fetchAllMiddleware = async (options, next) => {
  if (options.parse === false) {
    return next(options);
  }
  if (!requestContainsUnboundedQuery(options)) {
    return next(options);
  }
  const response = await index_default({
    ...modifyQuery(options, {
      per_page: 100
    }),
    // Ensure headers are returned for page 1.
    parse: false
  });
  const results = await parseResponse(response);
  if (!Array.isArray(results)) {
    return results;
  }
  let nextPage = getNextPageUrl(response);
  if (!nextPage) {
    return results;
  }
  let mergedResults = [].concat(results);
  while (nextPage) {
    const nextResponse = await index_default({
      ...options,
      // Ensure the URL for the next page is used instead of any provided path.
      path: void 0,
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
var fetch_all_middleware_default = fetchAllMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/http-v1.js
const OVERRIDE_METHODS = /* @__PURE__ */ new Set(["PATCH", "PUT", "DELETE"]);
const DEFAULT_METHOD = "GET";
const httpV1Middleware = (options, next) => {
  const { method = DEFAULT_METHOD } = options;
  if (OVERRIDE_METHODS.has(method.toUpperCase())) {
    options = {
      ...options,
      headers: {
        ...options.headers,
        "X-HTTP-Method-Override": method,
        "Content-Type": "application/json"
      },
      method: "POST"
    };
  }
  return next(options);
};
var http_v1_default = httpV1Middleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/user-locale.js

const userLocaleMiddleware = (options, next) => {
  if (typeof options.url === "string" && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.url, "_locale")) {
    options.url = (0,external_wp_url_namespaceObject.addQueryArgs)(options.url, { _locale: "user" });
  }
  if (typeof options.path === "string" && !(0,external_wp_url_namespaceObject.hasQueryArg)(options.path, "_locale")) {
    options.path = (0,external_wp_url_namespaceObject.addQueryArgs)(options.path, { _locale: "user" });
  }
  return next(options);
};
var user_locale_default = userLocaleMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/utils/response.js

async function parseJsonAndNormalizeError(response) {
  try {
    return await response.json();
  } catch {
    throw {
      code: "invalid_json",
      message: (0,external_wp_i18n_namespaceObject.__)("The response is not a valid JSON response.")
    };
  }
}
async function parseResponseAndNormalizeError(response, shouldParseResponse = true) {
  if (!shouldParseResponse) {
    return response;
  }
  if (response.status === 204) {
    return null;
  }
  return await parseJsonAndNormalizeError(response);
}
async function parseAndThrowError(response, shouldParseResponse = true) {
  if (!shouldParseResponse) {
    throw response;
  }
  throw await parseJsonAndNormalizeError(response);
}


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/media-upload.js


function isMediaUploadRequest(options) {
  const isCreateMethod = !!options.method && options.method === "POST";
  const isMediaEndpoint = !!options.path && options.path.indexOf("/wp/v2/media") !== -1 || !!options.url && options.url.indexOf("/wp/v2/media") !== -1;
  return isMediaEndpoint && isCreateMethod;
}
const mediaUploadMiddleware = (options, next) => {
  if (!isMediaUploadRequest(options)) {
    return next(options);
  }
  let retries = 0;
  const maxRetries = 5;
  const postProcess = (attachmentId) => {
    retries++;
    return next({
      path: `/wp/v2/media/${attachmentId}/post-process`,
      method: "POST",
      data: { action: "create-image-subsizes" },
      parse: false
    }).catch(() => {
      if (retries < maxRetries) {
        return postProcess(attachmentId);
      }
      next({
        path: `/wp/v2/media/${attachmentId}?force=true`,
        method: "DELETE"
      });
      return Promise.reject();
    });
  };
  return next({ ...options, parse: false }).catch((response) => {
    if (!(response instanceof globalThis.Response)) {
      return Promise.reject(response);
    }
    const attachmentId = response.headers.get(
      "x-wp-upload-attachment-id"
    );
    if (response.status >= 500 && response.status < 600 && attachmentId) {
      return postProcess(attachmentId).catch(() => {
        if (options.parse !== false) {
          return Promise.reject({
            code: "post_process",
            message: (0,external_wp_i18n_namespaceObject.__)(
              "Media upload failed. If this is a photo or a large image, please scale it down and try again."
            )
          });
        }
        return Promise.reject(response);
      });
    }
    return parseAndThrowError(response, options.parse);
  }).then(
    (response) => parseResponseAndNormalizeError(response, options.parse)
  );
};
var media_upload_default = mediaUploadMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/middlewares/theme-preview.js

const createThemePreviewMiddleware = (themePath) => (options, next) => {
  if (typeof options.url === "string") {
    const wpThemePreview = (0,external_wp_url_namespaceObject.getQueryArg)(
      options.url,
      "wp_theme_preview"
    );
    if (wpThemePreview === void 0) {
      options.url = (0,external_wp_url_namespaceObject.addQueryArgs)(options.url, {
        wp_theme_preview: themePath
      });
    } else if (wpThemePreview === "") {
      options.url = (0,external_wp_url_namespaceObject.removeQueryArgs)(
        options.url,
        "wp_theme_preview"
      );
    }
  }
  if (typeof options.path === "string") {
    const wpThemePreview = (0,external_wp_url_namespaceObject.getQueryArg)(
      options.path,
      "wp_theme_preview"
    );
    if (wpThemePreview === void 0) {
      options.path = (0,external_wp_url_namespaceObject.addQueryArgs)(options.path, {
        wp_theme_preview: themePath
      });
    } else if (wpThemePreview === "") {
      options.path = (0,external_wp_url_namespaceObject.removeQueryArgs)(
        options.path,
        "wp_theme_preview"
      );
    }
  }
  return next(options);
};
var theme_preview_default = createThemePreviewMiddleware;


;// ./node_modules/@wordpress/api-fetch/build-module/index.js











const DEFAULT_HEADERS = {
  // The backend uses the Accept header as a condition for considering an
  // incoming request as a REST request.
  //
  // See: https://core.trac.wordpress.org/ticket/44534
  Accept: "application/json, */*;q=0.1"
};
const DEFAULT_OPTIONS = {
  credentials: "include"
};
const middlewares = [
  user_locale_default,
  namespace_endpoint_default,
  http_v1_default,
  fetch_all_middleware_default
];
function registerMiddleware(middleware) {
  middlewares.unshift(middleware);
}
const defaultFetchHandler = (nextOptions) => {
  const { url, path, data, parse = true, ...remainingOptions } = nextOptions;
  let { body, headers } = nextOptions;
  headers = { ...DEFAULT_HEADERS, ...headers };
  if (data) {
    body = JSON.stringify(data);
    headers["Content-Type"] = "application/json";
  }
  const responsePromise = globalThis.fetch(
    // Fall back to explicitly passing `window.location` which is the behavior if `undefined` is passed.
    url || path || window.location.href,
    {
      ...DEFAULT_OPTIONS,
      ...remainingOptions,
      body,
      headers
    }
  );
  return responsePromise.then(
    (response) => {
      if (!response.ok) {
        return parseAndThrowError(response, parse);
      }
      return parseResponseAndNormalizeError(response, parse);
    },
    (err) => {
      if (err && err.name === "AbortError") {
        throw err;
      }
      if (!globalThis.navigator.onLine) {
        throw {
          code: "offline_error",
          message: (0,external_wp_i18n_namespaceObject.__)(
            "Unable to connect. Please check your Internet connection."
          )
        };
      }
      throw {
        code: "fetch_error",
        message: (0,external_wp_i18n_namespaceObject.__)(
          "Could not get a valid response from the server."
        )
      };
    }
  );
};
let fetchHandler = defaultFetchHandler;
function setFetchHandler(newFetchHandler) {
  fetchHandler = newFetchHandler;
}
const apiFetch = (options) => {
  const enhancedHandler = middlewares.reduceRight(
    (next, middleware) => {
      return (workingOptions) => middleware(workingOptions, next);
    },
    fetchHandler
  );
  return enhancedHandler(options).catch((error) => {
    if (error.code !== "rest_cookie_invalid_nonce") {
      return Promise.reject(error);
    }
    return globalThis.fetch(apiFetch.nonceEndpoint).then((response) => {
      if (!response.ok) {
        return Promise.reject(error);
      }
      return response.text();
    }).then((text) => {
      apiFetch.nonceMiddleware.nonce = text;
      return apiFetch(options);
    });
  });
};
apiFetch.use = registerMiddleware;
apiFetch.setFetchHandler = setFetchHandler;
apiFetch.createNonceMiddleware = nonce_default;
apiFetch.createPreloadingMiddleware = preloading_default;
apiFetch.createRootURLMiddleware = root_url_default;
apiFetch.fetchAllMiddleware = fetch_all_middleware_default;
apiFetch.mediaUploadMiddleware = media_upload_default;
apiFetch.createThemePreviewMiddleware = theme_preview_default;
var index_default = apiFetch;



(window.wp = window.wp || {}).apiFetch = __webpack_exports__["default"];
/******/ })()
;