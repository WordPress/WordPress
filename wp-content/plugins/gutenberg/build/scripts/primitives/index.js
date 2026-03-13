"use strict";
var wp;
(wp ||= {}).primitives = (() => {
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

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/primitives/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    BlockQuotation: () => BlockQuotation,
    Circle: () => Circle,
    Defs: () => Defs,
    G: () => G,
    HorizontalRule: () => HorizontalRule,
    Line: () => Line,
    LinearGradient: () => LinearGradient,
    Path: () => Path,
    Polygon: () => Polygon,
    RadialGradient: () => RadialGradient,
    Rect: () => Rect,
    SVG: () => SVG,
    Stop: () => Stop,
    View: () => View
  });

  // node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
    return n;
  }
  var clsx_default = clsx;

  // packages/primitives/build-module/svg/index.js
  var import_element = __toESM(require_element());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var Circle = (props) => (0, import_element.createElement)("circle", props);
  var G = (props) => (0, import_element.createElement)("g", props);
  var Line = (props) => (0, import_element.createElement)("line", props);
  var Path = (props) => (0, import_element.createElement)("path", props);
  var Polygon = (props) => (0, import_element.createElement)("polygon", props);
  var Rect = (props) => (0, import_element.createElement)("rect", props);
  var Defs = (props) => (0, import_element.createElement)("defs", props);
  var RadialGradient = (props) => (0, import_element.createElement)("radialGradient", props);
  var LinearGradient = (props) => (0, import_element.createElement)("linearGradient", props);
  var Stop = (props) => (0, import_element.createElement)("stop", props);
  var SVG = (0, import_element.forwardRef)(
    /**
     * @param {SVGProps}                                    props isPressed indicates whether the SVG should appear as pressed.
     *                                                            Other props will be passed through to svg component.
     * @param {import('react').ForwardedRef<SVGSVGElement>} ref   The forwarded ref to the SVG element.
     *
     * @return {JSX.Element} Stop component
     */
    ({ className, isPressed, ...props }, ref) => {
      const appliedProps = {
        ...props,
        className: clsx_default(className, { "is-pressed": isPressed }) || void 0,
        "aria-hidden": true,
        focusable: false
      };
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("svg", { ...appliedProps, ref });
    }
  );
  SVG.displayName = "SVG";

  // packages/primitives/build-module/horizontal-rule/index.js
  var HorizontalRule = "hr";

  // packages/primitives/build-module/block-quotation/index.js
  var BlockQuotation = "blockquote";

  // packages/primitives/build-module/view/index.js
  var View = "div";
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
