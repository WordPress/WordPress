var wp;
(wp ||= {}).viewport = (() => {
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

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/viewport/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    ifViewportMatches: () => if_viewport_matches_default,
    store: () => store,
    withViewportMatch: () => with_viewport_match_default
  });

  // packages/viewport/build-module/listener.js
  var import_compose = __toESM(require_compose());
  var import_data2 = __toESM(require_data());

  // packages/viewport/build-module/store/index.js
  var import_data = __toESM(require_data());

  // packages/viewport/build-module/store/reducer.js
  function reducer(state = {}, action) {
    switch (action.type) {
      case "SET_IS_MATCHING":
        return action.values;
    }
    return state;
  }
  var reducer_default = reducer;

  // packages/viewport/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    setIsMatching: () => setIsMatching
  });
  function setIsMatching(values) {
    return {
      type: "SET_IS_MATCHING",
      values
    };
  }

  // packages/viewport/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    isViewportMatch: () => isViewportMatch
  });
  function isViewportMatch(state, query) {
    if (query.indexOf(" ") === -1) {
      query = ">= " + query;
    }
    return !!state[query];
  }

  // packages/viewport/build-module/store/index.js
  var STORE_NAME = "core/viewport";
  var store = (0, import_data.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data.register)(store);

  // packages/viewport/build-module/listener.js
  var addDimensionsEventListener = (breakpoints, operators) => {
    const setIsMatching2 = (0, import_compose.debounce)(
      () => {
        const values = Object.fromEntries(
          queries.map(([key, query]) => [key, query.matches])
        );
        (0, import_data2.dispatch)(store).setIsMatching(values);
      },
      0,
      { leading: true }
    );
    const operatorEntries = Object.entries(operators);
    const queries = Object.entries(breakpoints).flatMap(
      ([name, width]) => {
        return operatorEntries.map(([operator, condition]) => {
          const list = window.matchMedia(
            `(${condition}: ${width}px)`
          );
          list.addEventListener("change", setIsMatching2);
          return [`${operator} ${name}`, list];
        });
      }
    );
    window.addEventListener("orientationchange", setIsMatching2);
    setIsMatching2();
    setIsMatching2.flush();
  };
  var listener_default = addDimensionsEventListener;

  // packages/viewport/build-module/if-viewport-matches.js
  var import_compose3 = __toESM(require_compose());

  // packages/viewport/build-module/with-viewport-match.js
  var import_compose2 = __toESM(require_compose());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var withViewportMatch = (queries) => {
    const queryEntries = Object.entries(queries);
    const useViewPortQueriesResult = () => Object.fromEntries(
      queryEntries.map(([key, query]) => {
        let [operator, breakpointName] = query.split(" ");
        if (breakpointName === void 0) {
          breakpointName = operator;
          operator = ">=";
        }
        return [key, (0, import_compose2.useViewportMatch)(breakpointName, operator)];
      })
    );
    return (0, import_compose2.createHigherOrderComponent)((WrappedComponent) => {
      return (0, import_compose2.pure)((props) => {
        const queriesResult = useViewPortQueriesResult();
        return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WrappedComponent, { ...props, ...queriesResult });
      });
    }, "withViewportMatch");
  };
  var with_viewport_match_default = withViewportMatch;

  // packages/viewport/build-module/if-viewport-matches.js
  var ifViewportMatches = (query) => (0, import_compose3.createHigherOrderComponent)(
    (0, import_compose3.compose)([
      with_viewport_match_default({
        isViewportMatch: query
      }),
      (0, import_compose3.ifCondition)((props) => props.isViewportMatch)
    ]),
    "ifViewportMatches"
  );
  var if_viewport_matches_default = ifViewportMatches;

  // packages/viewport/build-module/index.js
  var BREAKPOINTS = {
    huge: 1440,
    wide: 1280,
    large: 960,
    medium: 782,
    small: 600,
    mobile: 480
  };
  var OPERATORS = {
    "<": "max-width",
    ">=": "min-width"
  };
  listener_default(BREAKPOINTS, OPERATORS);
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
