/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ index_default)
});

// UNUSED EXPORTS: ServerSideRender, useServerSideRender

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","apiFetch"]
const external_wp_apiFetch_namespaceObject = window["wp"]["apiFetch"];
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_namespaceObject);
;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// ./node_modules/@wordpress/server-side-render/build-module/hook.js





function rendererPath(block, attributes = null, urlQueryArgs = {}) {
  return (0,external_wp_url_namespaceObject.addQueryArgs)(`/wp/v2/block-renderer/${block}`, {
    context: "edit",
    ...null !== attributes ? { attributes } : {},
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
    shadow,
    spacing,
    typography,
    ...restStyles
  } = attributes?.style || {};
  return {
    ...restAttributes,
    style: restStyles
  };
}
function useServerSideRender(args) {
  const [response, setResponse] = (0,external_wp_element_namespaceObject.useState)({ status: "idle" });
  const shouldDebounceRef = (0,external_wp_element_namespaceObject.useRef)(false);
  const {
    attributes,
    block,
    skipBlockSupportAttributes = false,
    httpMethod = "GET",
    urlQueryArgs
  } = args;
  let sanitizedAttributes = attributes && (0,external_wp_blocks_namespaceObject.__experimentalSanitizeBlockAttributes)(block, attributes);
  if (skipBlockSupportAttributes) {
    sanitizedAttributes = removeBlockSupportAttributes(sanitizedAttributes);
  }
  const isPostRequest = "POST" === httpMethod;
  const urlAttributes = isPostRequest ? null : sanitizedAttributes;
  const path = rendererPath(block, urlAttributes, urlQueryArgs);
  const body = isPostRequest ? JSON.stringify({ attributes: sanitizedAttributes ?? null }) : void 0;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const controller = new AbortController();
    const debouncedFetch = (0,external_wp_compose_namespaceObject.debounce)(
      function() {
        {
          setResponse({ status: "loading" });
          external_wp_apiFetch_default()({
            path,
            method: isPostRequest ? "POST" : "GET",
            body,
            headers: isPostRequest ? {
              "Content-Type": "application/json"
            } : {},
            signal: controller.signal
          }).then((res) => {
            setResponse({
              status: "success",
              content: res ? res.rendered : ""
            });
          }).catch((error) => {
            if (error.name === "AbortError") {
              return;
            }
            setResponse({
              status: "error",
              error: error.message
            });
          }).finally(() => {
            shouldDebounceRef.current = true;
          });
        }
      },
      shouldDebounceRef.current ? 500 : 0
    );
    debouncedFetch();
    return () => {
      controller.abort();
      debouncedFetch.cancel();
    };
  }, [path, isPostRequest, body]);
  return response;
}


;// ./node_modules/@wordpress/server-side-render/build-module/server-side-render.js






const EMPTY_OBJECT = {};
function DefaultEmptyResponsePlaceholder({ className }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, { className, children: (0,external_wp_i18n_namespaceObject.__)("Block rendered as empty.") });
}
function DefaultErrorResponsePlaceholder({ message, className }) {
  const errorMessage = (0,external_wp_i18n_namespaceObject.sprintf)(
    // translators: %s: error message describing the problem
    (0,external_wp_i18n_namespaceObject.__)("Error loading block: %s"),
    message
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Placeholder, { className, children: errorMessage });
}
function DefaultLoadingResponsePlaceholder({ children }) {
  const [showLoader, setShowLoader] = (0,external_wp_element_namespaceObject.useState)(false);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const timeout = setTimeout(() => {
      setShowLoader(true);
    }, 1e3);
    return () => clearTimeout(timeout);
  }, []);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", { style: { position: "relative" }, children: [
    showLoader && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      "div",
      {
        style: {
          position: "absolute",
          top: "50%",
          left: "50%",
          marginTop: "-9px",
          marginLeft: "-9px"
        },
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Spinner, {})
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { style: { opacity: showLoader ? "0.3" : 1 }, children })
  ] });
}
function ServerSideRender(props) {
  const prevContentRef = (0,external_wp_element_namespaceObject.useRef)("");
  const {
    className,
    EmptyResponsePlaceholder = DefaultEmptyResponsePlaceholder,
    ErrorResponsePlaceholder = DefaultErrorResponsePlaceholder,
    LoadingResponsePlaceholder = DefaultLoadingResponsePlaceholder,
    ...restProps
  } = props;
  const { content, status, error } = useServerSideRender(restProps);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (content) {
      prevContentRef.current = content;
    }
  }, [content]);
  if (status === "loading") {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(LoadingResponsePlaceholder, { ...props, children: !!prevContentRef.current && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.RawHTML, { className, children: prevContentRef.current }) });
  }
  if (status === "success" && !content) {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(EmptyResponsePlaceholder, { ...props });
  }
  if (status === "error") {
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ErrorResponsePlaceholder, { message: error, ...props });
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.RawHTML, { className, children: content });
}
function ServerSideRenderWithPostId({
  urlQueryArgs = EMPTY_OBJECT,
  ...props
}) {
  const currentPostId = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const postId = select("core/editor")?.getCurrentPostId();
    return postId && typeof postId === "number" ? postId : null;
  }, []);
  const newUrlQueryArgs = (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!currentPostId) {
      return urlQueryArgs;
    }
    return {
      post_id: currentPostId,
      ...urlQueryArgs
    };
  }, [currentPostId, urlQueryArgs]);
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(ServerSideRender, { urlQueryArgs: newUrlQueryArgs, ...props });
}


;// ./node_modules/@wordpress/server-side-render/build-module/index.js


const ServerSideRenderCompat = ServerSideRenderWithPostId;
ServerSideRenderCompat.ServerSideRender = ServerSideRenderWithPostId;
ServerSideRenderCompat.useServerSideRender = useServerSideRender;
var index_default = ServerSideRenderCompat;


(window.wp = window.wp || {}).serverSideRender = __webpack_exports__["default"];
/******/ })()
;