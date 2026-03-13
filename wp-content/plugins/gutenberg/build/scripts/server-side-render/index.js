var wp;
(wp ||= {}).serverSideRender = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/server-side-render/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    ServerSideRender: () => ServerSideRenderWithPostId,
    default: () => index_default,
    useServerSideRender: () => useServerSideRender
  });

  // packages/server-side-render/build-module/server-side-render.js
  var import_element2 = __toESM(require_element());
  var import_i18n = __toESM(require_i18n());
  var import_components = __toESM(require_components());
  var import_data = __toESM(require_data());

  // packages/server-side-render/build-module/hook.js
  var import_compose = __toESM(require_compose());
  var import_element = __toESM(require_element());
  var import_api_fetch = __toESM(require_api_fetch());
  var import_url = __toESM(require_url());
  var import_blocks = __toESM(require_blocks());
  function rendererPath(block, attributes = null, urlQueryArgs = {}) {
    return (0, import_url.addQueryArgs)(`/wp/v2/block-renderer/${block}`, {
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
    const [response, setResponse] = (0, import_element.useState)({ status: "idle" });
    const shouldDebounceRef = (0, import_element.useRef)(false);
    const {
      attributes,
      block,
      skipBlockSupportAttributes = false,
      httpMethod = "GET",
      urlQueryArgs
    } = args;
    let sanitizedAttributes = attributes && (0, import_blocks.__experimentalSanitizeBlockAttributes)(block, attributes);
    if (skipBlockSupportAttributes) {
      sanitizedAttributes = removeBlockSupportAttributes(sanitizedAttributes);
    }
    const isPostRequest = "POST" === httpMethod;
    const urlAttributes = isPostRequest ? null : sanitizedAttributes;
    const path = rendererPath(block, urlAttributes, urlQueryArgs);
    const body = isPostRequest ? JSON.stringify({ attributes: sanitizedAttributes ?? null }) : void 0;
    (0, import_element.useEffect)(() => {
      const controller = new AbortController();
      const debouncedFetch = (0, import_compose.debounce)(
        function() {
          {
            setResponse({ status: "loading" });
            (0, import_api_fetch.default)({
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

  // packages/server-side-render/build-module/server-side-render.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var EMPTY_OBJECT = {};
  function DefaultEmptyResponsePlaceholder({ className }) {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Placeholder, { className, children: (0, import_i18n.__)("Block rendered as empty.") });
  }
  function DefaultErrorResponsePlaceholder({ message, className }) {
    const errorMessage = (0, import_i18n.sprintf)(
      // translators: %s: error message describing the problem
      (0, import_i18n.__)("Error loading block: %s"),
      message
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Placeholder, { className, children: errorMessage });
  }
  function DefaultLoadingResponsePlaceholder({ children }) {
    const [showLoader, setShowLoader] = (0, import_element2.useState)(false);
    (0, import_element2.useEffect)(() => {
      const timeout = setTimeout(() => {
        setShowLoader(true);
      }, 1e3);
      return () => clearTimeout(timeout);
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)("div", { style: { position: "relative" }, children: [
      showLoader && /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        "div",
        {
          style: {
            position: "absolute",
            top: "50%",
            left: "50%",
            marginTop: "-9px",
            marginLeft: "-9px"
          },
          children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Spinner, {})
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)("div", { style: { opacity: showLoader ? "0.3" : 1 }, children })
    ] });
  }
  function ServerSideRender(props) {
    const prevContentRef = (0, import_element2.useRef)("");
    const {
      className,
      EmptyResponsePlaceholder = DefaultEmptyResponsePlaceholder,
      ErrorResponsePlaceholder = DefaultErrorResponsePlaceholder,
      LoadingResponsePlaceholder = DefaultLoadingResponsePlaceholder,
      ...restProps
    } = props;
    const { content, status, error } = useServerSideRender(restProps);
    (0, import_element2.useEffect)(() => {
      if (content) {
        prevContentRef.current = content;
      }
    }, [content]);
    if (status === "loading") {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(LoadingResponsePlaceholder, { ...props, children: !!prevContentRef.current && /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_element2.RawHTML, { className, children: prevContentRef.current }) });
    }
    if (status === "success" && !content) {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(EmptyResponsePlaceholder, { ...props });
    }
    if (status === "error") {
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(ErrorResponsePlaceholder, { message: error, ...props });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_element2.RawHTML, { className, children: content });
  }
  function ServerSideRenderWithPostId({
    urlQueryArgs = EMPTY_OBJECT,
    ...props
  }) {
    const currentPostId = (0, import_data.useSelect)((select) => {
      const postId = select("core/editor")?.getCurrentPostId();
      return postId && typeof postId === "number" ? postId : null;
    }, []);
    const newUrlQueryArgs = (0, import_element2.useMemo)(() => {
      if (!currentPostId) {
        return urlQueryArgs;
      }
      return {
        post_id: currentPostId,
        ...urlQueryArgs
      };
    }, [currentPostId, urlQueryArgs]);
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(ServerSideRender, { urlQueryArgs: newUrlQueryArgs, ...props });
  }

  // packages/server-side-render/build-module/index.js
  var ServerSideRenderCompat = ServerSideRenderWithPostId;
  ServerSideRenderCompat.ServerSideRender = ServerSideRenderWithPostId;
  ServerSideRenderCompat.useServerSideRender = useServerSideRender;
  var index_default = ServerSideRenderCompat;
  return __toCommonJS(index_exports);
})();
if (typeof wp.serverSideRender === 'object' && wp.serverSideRender.default) { wp.serverSideRender = wp.serverSideRender.default; }
//# sourceMappingURL=index.js.map
