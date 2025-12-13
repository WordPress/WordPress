"use strict";
var wp;
(wp ||= {}).element = (() => {
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

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // vendor-external:react-dom
  var require_react_dom = __commonJS({
    "vendor-external:react-dom"(exports, module) {
      module.exports = window.ReactDOM;
    }
  });

  // node_modules/react-dom/client.js
  var require_client = __commonJS({
    "node_modules/react-dom/client.js"(exports) {
      "use strict";
      var m = require_react_dom();
      if (false) {
        exports.createRoot = m.createRoot;
        exports.hydrateRoot = m.hydrateRoot;
      } else {
        i = m.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
        exports.createRoot = function(c, o) {
          i.usingClientEntryPoint = true;
          try {
            return m.createRoot(c, o);
          } finally {
            i.usingClientEntryPoint = false;
          }
        };
        exports.hydrateRoot = function(c, h, o) {
          i.usingClientEntryPoint = true;
          try {
            return m.hydrateRoot(c, h, o);
          } finally {
            i.usingClientEntryPoint = false;
          }
        };
      }
      var i;
    }
  });

  // package-external:@wordpress/escape-html
  var require_escape_html = __commonJS({
    "package-external:@wordpress/escape-html"(exports, module) {
      module.exports = window.wp.escapeHtml;
    }
  });

  // packages/element/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    Children: () => import_react.Children,
    Component: () => import_react.Component,
    Fragment: () => import_react.Fragment,
    Platform: () => platform_default,
    PureComponent: () => import_react.PureComponent,
    RawHTML: () => RawHTML,
    StrictMode: () => import_react.StrictMode,
    Suspense: () => import_react.Suspense,
    cloneElement: () => import_react.cloneElement,
    concatChildren: () => concatChildren,
    createContext: () => import_react.createContext,
    createElement: () => import_react.createElement,
    createInterpolateElement: () => create_interpolate_element_default,
    createPortal: () => import_react_dom.createPortal,
    createRef: () => import_react.createRef,
    createRoot: () => import_client.createRoot,
    findDOMNode: () => import_react_dom.findDOMNode,
    flushSync: () => import_react_dom.flushSync,
    forwardRef: () => import_react.forwardRef,
    hydrate: () => import_react_dom.hydrate,
    hydrateRoot: () => import_client.hydrateRoot,
    isEmptyElement: () => isEmptyElement,
    isValidElement: () => import_react.isValidElement,
    lazy: () => import_react.lazy,
    memo: () => import_react.memo,
    render: () => import_react_dom.render,
    renderToString: () => serialize_default,
    startTransition: () => import_react.startTransition,
    switchChildrenNodeName: () => switchChildrenNodeName,
    unmountComponentAtNode: () => import_react_dom.unmountComponentAtNode,
    useCallback: () => import_react.useCallback,
    useContext: () => import_react.useContext,
    useDebugValue: () => import_react.useDebugValue,
    useDeferredValue: () => import_react.useDeferredValue,
    useEffect: () => import_react.useEffect,
    useId: () => import_react.useId,
    useImperativeHandle: () => import_react.useImperativeHandle,
    useInsertionEffect: () => import_react.useInsertionEffect,
    useLayoutEffect: () => import_react.useLayoutEffect,
    useMemo: () => import_react.useMemo,
    useReducer: () => import_react.useReducer,
    useRef: () => import_react.useRef,
    useState: () => import_react.useState,
    useSyncExternalStore: () => import_react.useSyncExternalStore,
    useTransition: () => import_react.useTransition
  });

  // packages/element/build-module/react.js
  var import_react = __toESM(require_react());
  function concatChildren(...childrenArguments) {
    return childrenArguments.reduce(
      (accumulator, children, i) => {
        import_react.Children.forEach(children, (child, j) => {
          if ((0, import_react.isValidElement)(child) && typeof child !== "string") {
            child = (0, import_react.cloneElement)(child, {
              key: [i, j].join()
            });
          }
          accumulator.push(child);
        });
        return accumulator;
      },
      []
    );
  }
  function switchChildrenNodeName(children, nodeName) {
    return children && import_react.Children.map(children, (elt, index) => {
      if (typeof elt?.valueOf() === "string") {
        return (0, import_react.createElement)(nodeName, { key: index }, elt);
      }
      if (!(0, import_react.isValidElement)(elt)) {
        return elt;
      }
      const { children: childrenProp, ...props } = elt.props;
      return (0, import_react.createElement)(
        nodeName,
        { key: index, ...props },
        childrenProp
      );
    });
  }

  // packages/element/build-module/create-interpolate-element.js
  var indoc;
  var offset;
  var output;
  var stack;
  var tokenizer = /<(\/)?(\w+)\s*(\/)?>/g;
  function createFrame(element, tokenStart, tokenLength, prevOffset, leadingTextStart) {
    return {
      element,
      tokenStart,
      tokenLength,
      prevOffset,
      leadingTextStart,
      children: []
    };
  }
  var createInterpolateElement = (interpolatedString, conversionMap) => {
    indoc = interpolatedString;
    offset = 0;
    output = [];
    stack = [];
    tokenizer.lastIndex = 0;
    if (!isValidConversionMap(conversionMap)) {
      throw new TypeError(
        "The conversionMap provided is not valid. It must be an object with values that are React Elements"
      );
    }
    do {
    } while (proceed(conversionMap));
    return (0, import_react.createElement)(import_react.Fragment, null, ...output);
  };
  var isValidConversionMap = (conversionMap) => {
    const isObject2 = typeof conversionMap === "object" && conversionMap !== null;
    const values = isObject2 && Object.values(conversionMap);
    return isObject2 && values.length > 0 && values.every((element) => (0, import_react.isValidElement)(element));
  };
  function proceed(conversionMap) {
    const next = nextToken();
    const [tokenType, name, startOffset, tokenLength] = next;
    const stackDepth = stack.length;
    const leadingTextStart = startOffset > offset ? offset : null;
    if (name && !conversionMap[name]) {
      addText();
      return false;
    }
    switch (tokenType) {
      case "no-more-tokens":
        if (stackDepth !== 0) {
          const { leadingTextStart: stackLeadingText, tokenStart } = stack.pop();
          output.push(indoc.substr(stackLeadingText, tokenStart));
        }
        addText();
        return false;
      case "self-closed":
        if (0 === stackDepth) {
          if (null !== leadingTextStart) {
            output.push(
              indoc.substr(
                leadingTextStart,
                startOffset - leadingTextStart
              )
            );
          }
          output.push(conversionMap[name]);
          offset = startOffset + tokenLength;
          return true;
        }
        addChild(
          createFrame(conversionMap[name], startOffset, tokenLength)
        );
        offset = startOffset + tokenLength;
        return true;
      case "opener":
        stack.push(
          createFrame(
            conversionMap[name],
            startOffset,
            tokenLength,
            startOffset + tokenLength,
            leadingTextStart
          )
        );
        offset = startOffset + tokenLength;
        return true;
      case "closer":
        if (1 === stackDepth) {
          closeOuterElement(startOffset);
          offset = startOffset + tokenLength;
          return true;
        }
        const stackTop = stack.pop();
        const text = indoc.substr(
          stackTop.prevOffset,
          startOffset - stackTop.prevOffset
        );
        stackTop.children.push(text);
        stackTop.prevOffset = startOffset + tokenLength;
        const frame = createFrame(
          stackTop.element,
          stackTop.tokenStart,
          stackTop.tokenLength,
          startOffset + tokenLength
        );
        frame.children = stackTop.children;
        addChild(frame);
        offset = startOffset + tokenLength;
        return true;
      default:
        addText();
        return false;
    }
  }
  function nextToken() {
    const matches = tokenizer.exec(indoc);
    if (null === matches) {
      return ["no-more-tokens"];
    }
    const startedAt = matches.index;
    const [match, isClosing, name, isSelfClosed] = matches;
    const length = match.length;
    if (isSelfClosed) {
      return ["self-closed", name, startedAt, length];
    }
    if (isClosing) {
      return ["closer", name, startedAt, length];
    }
    return ["opener", name, startedAt, length];
  }
  function addText() {
    const length = indoc.length - offset;
    if (0 === length) {
      return;
    }
    output.push(indoc.substr(offset, length));
  }
  function addChild(frame) {
    const { element, tokenStart, tokenLength, prevOffset, children } = frame;
    const parent = stack[stack.length - 1];
    const text = indoc.substr(
      parent.prevOffset,
      tokenStart - parent.prevOffset
    );
    if (text) {
      parent.children.push(text);
    }
    parent.children.push((0, import_react.cloneElement)(element, null, ...children));
    parent.prevOffset = prevOffset ? prevOffset : tokenStart + tokenLength;
  }
  function closeOuterElement(endOffset) {
    const { element, leadingTextStart, prevOffset, tokenStart, children } = stack.pop();
    const text = endOffset ? indoc.substr(prevOffset, endOffset - prevOffset) : indoc.substr(prevOffset);
    if (text) {
      children.push(text);
    }
    if (null !== leadingTextStart) {
      output.push(
        indoc.substr(leadingTextStart, tokenStart - leadingTextStart)
      );
    }
    output.push((0, import_react.cloneElement)(element, null, ...children));
  }
  var create_interpolate_element_default = createInterpolateElement;

  // packages/element/build-module/react-platform.js
  var import_react_dom = __toESM(require_react_dom());
  var import_client = __toESM(require_client());

  // packages/element/build-module/utils.js
  var isEmptyElement = (element) => {
    if (typeof element === "number") {
      return false;
    }
    if (typeof element?.valueOf() === "string" || Array.isArray(element)) {
      return !element.length;
    }
    return !element;
  };

  // packages/element/build-module/platform.js
  var Platform = {
    /** Platform identifier. Will always be `'web'` in this module. */
    OS: "web",
    /**
     * Select a value based on the platform.
     *
     * @template T
     * @param    spec - Object with optional platform-specific values.
     * @return The selected value.
     */
    select(spec) {
      return "web" in spec ? spec.web : spec.default;
    },
    /** Whether the platform is web */
    isWeb: true
  };
  var platform_default = Platform;

  // node_modules/is-plain-object/dist/is-plain-object.mjs
  function isObject(o) {
    return Object.prototype.toString.call(o) === "[object Object]";
  }
  function isPlainObject(o) {
    var ctor, prot;
    if (isObject(o) === false) return false;
    ctor = o.constructor;
    if (ctor === void 0) return true;
    prot = ctor.prototype;
    if (isObject(prot) === false) return false;
    if (prot.hasOwnProperty("isPrototypeOf") === false) {
      return false;
    }
    return true;
  }

  // node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
    };
    return __assign.apply(this, arguments);
  };

  // node_modules/lower-case/dist.es2015/index.js
  function lowerCase(str) {
    return str.toLowerCase();
  }

  // node_modules/no-case/dist.es2015/index.js
  var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
  var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
  function noCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    while (result.charAt(start) === "\0")
      start++;
    while (result.charAt(end - 1) === "\0")
      end--;
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
  }
  function replace(input, re, value) {
    if (re instanceof RegExp)
      return input.replace(re, value);
    return re.reduce(function(input2, re2) {
      return input2.replace(re2, value);
    }, input);
  }

  // node_modules/dot-case/dist.es2015/index.js
  function dotCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: "." }, options));
  }

  // node_modules/param-case/dist.es2015/index.js
  function paramCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return dotCase(input, __assign({ delimiter: "-" }, options));
  }

  // packages/element/build-module/serialize.js
  var import_escape_html = __toESM(require_escape_html());

  // packages/element/build-module/raw-html.js
  function RawHTML({
    children,
    ...props
  }) {
    let rawHtml = "";
    import_react.Children.toArray(children).forEach((child) => {
      if (typeof child === "string" && child.trim() !== "") {
        rawHtml += child;
      }
    });
    return (0, import_react.createElement)("div", {
      dangerouslySetInnerHTML: { __html: rawHtml },
      ...props
    });
  }

  // packages/element/build-module/serialize.js
  var Context = (0, import_react.createContext)(void 0);
  Context.displayName = "ElementContext";
  var { Provider, Consumer } = Context;
  var ForwardRef = (0, import_react.forwardRef)(() => {
    return null;
  });
  var ATTRIBUTES_TYPES = /* @__PURE__ */ new Set(["string", "boolean", "number"]);
  var SELF_CLOSING_TAGS = /* @__PURE__ */ new Set([
    "area",
    "base",
    "br",
    "col",
    "command",
    "embed",
    "hr",
    "img",
    "input",
    "keygen",
    "link",
    "meta",
    "param",
    "source",
    "track",
    "wbr"
  ]);
  var BOOLEAN_ATTRIBUTES = /* @__PURE__ */ new Set([
    "allowfullscreen",
    "allowpaymentrequest",
    "allowusermedia",
    "async",
    "autofocus",
    "autoplay",
    "checked",
    "controls",
    "default",
    "defer",
    "disabled",
    "download",
    "formnovalidate",
    "hidden",
    "ismap",
    "itemscope",
    "loop",
    "multiple",
    "muted",
    "nomodule",
    "novalidate",
    "open",
    "playsinline",
    "readonly",
    "required",
    "reversed",
    "selected",
    "typemustmatch"
  ]);
  var ENUMERATED_ATTRIBUTES = /* @__PURE__ */ new Set([
    "autocapitalize",
    "autocomplete",
    "charset",
    "contenteditable",
    "crossorigin",
    "decoding",
    "dir",
    "draggable",
    "enctype",
    "formenctype",
    "formmethod",
    "http-equiv",
    "inputmode",
    "kind",
    "method",
    "preload",
    "scope",
    "shape",
    "spellcheck",
    "translate",
    "type",
    "wrap"
  ]);
  var CSS_PROPERTIES_SUPPORTS_UNITLESS = /* @__PURE__ */ new Set([
    "animation",
    "animationIterationCount",
    "baselineShift",
    "borderImageOutset",
    "borderImageSlice",
    "borderImageWidth",
    "columnCount",
    "cx",
    "cy",
    "fillOpacity",
    "flexGrow",
    "flexShrink",
    "floodOpacity",
    "fontWeight",
    "gridColumnEnd",
    "gridColumnStart",
    "gridRowEnd",
    "gridRowStart",
    "lineHeight",
    "opacity",
    "order",
    "orphans",
    "r",
    "rx",
    "ry",
    "shapeImageThreshold",
    "stopOpacity",
    "strokeDasharray",
    "strokeDashoffset",
    "strokeMiterlimit",
    "strokeOpacity",
    "strokeWidth",
    "tabSize",
    "widows",
    "x",
    "y",
    "zIndex",
    "zoom"
  ]);
  function hasPrefix(string, prefixes) {
    return prefixes.some((prefix) => string.indexOf(prefix) === 0);
  }
  function isInternalAttribute(attribute) {
    return "key" === attribute || "children" === attribute;
  }
  function getNormalAttributeValue(attribute, value) {
    switch (attribute) {
      case "style":
        return renderStyle(value);
    }
    return value;
  }
  var SVG_ATTRIBUTE_WITH_DASHES_LIST = [
    "accentHeight",
    "alignmentBaseline",
    "arabicForm",
    "baselineShift",
    "capHeight",
    "clipPath",
    "clipRule",
    "colorInterpolation",
    "colorInterpolationFilters",
    "colorProfile",
    "colorRendering",
    "dominantBaseline",
    "enableBackground",
    "fillOpacity",
    "fillRule",
    "floodColor",
    "floodOpacity",
    "fontFamily",
    "fontSize",
    "fontSizeAdjust",
    "fontStretch",
    "fontStyle",
    "fontVariant",
    "fontWeight",
    "glyphName",
    "glyphOrientationHorizontal",
    "glyphOrientationVertical",
    "horizAdvX",
    "horizOriginX",
    "imageRendering",
    "letterSpacing",
    "lightingColor",
    "markerEnd",
    "markerMid",
    "markerStart",
    "overlinePosition",
    "overlineThickness",
    "paintOrder",
    "panose1",
    "pointerEvents",
    "renderingIntent",
    "shapeRendering",
    "stopColor",
    "stopOpacity",
    "strikethroughPosition",
    "strikethroughThickness",
    "strokeDasharray",
    "strokeDashoffset",
    "strokeLinecap",
    "strokeLinejoin",
    "strokeMiterlimit",
    "strokeOpacity",
    "strokeWidth",
    "textAnchor",
    "textDecoration",
    "textRendering",
    "underlinePosition",
    "underlineThickness",
    "unicodeBidi",
    "unicodeRange",
    "unitsPerEm",
    "vAlphabetic",
    "vHanging",
    "vIdeographic",
    "vMathematical",
    "vectorEffect",
    "vertAdvY",
    "vertOriginX",
    "vertOriginY",
    "wordSpacing",
    "writingMode",
    "xmlnsXlink",
    "xHeight"
  ].reduce(
    (map, attribute) => {
      map[attribute.toLowerCase()] = attribute;
      return map;
    },
    {}
  );
  var CASE_SENSITIVE_SVG_ATTRIBUTES = [
    "allowReorder",
    "attributeName",
    "attributeType",
    "autoReverse",
    "baseFrequency",
    "baseProfile",
    "calcMode",
    "clipPathUnits",
    "contentScriptType",
    "contentStyleType",
    "diffuseConstant",
    "edgeMode",
    "externalResourcesRequired",
    "filterRes",
    "filterUnits",
    "glyphRef",
    "gradientTransform",
    "gradientUnits",
    "kernelMatrix",
    "kernelUnitLength",
    "keyPoints",
    "keySplines",
    "keyTimes",
    "lengthAdjust",
    "limitingConeAngle",
    "markerHeight",
    "markerUnits",
    "markerWidth",
    "maskContentUnits",
    "maskUnits",
    "numOctaves",
    "pathLength",
    "patternContentUnits",
    "patternTransform",
    "patternUnits",
    "pointsAtX",
    "pointsAtY",
    "pointsAtZ",
    "preserveAlpha",
    "preserveAspectRatio",
    "primitiveUnits",
    "refX",
    "refY",
    "repeatCount",
    "repeatDur",
    "requiredExtensions",
    "requiredFeatures",
    "specularConstant",
    "specularExponent",
    "spreadMethod",
    "startOffset",
    "stdDeviation",
    "stitchTiles",
    "suppressContentEditableWarning",
    "suppressHydrationWarning",
    "surfaceScale",
    "systemLanguage",
    "tableValues",
    "targetX",
    "targetY",
    "textLength",
    "viewBox",
    "viewTarget",
    "xChannelSelector",
    "yChannelSelector"
  ].reduce(
    (map, attribute) => {
      map[attribute.toLowerCase()] = attribute;
      return map;
    },
    {}
  );
  var SVG_ATTRIBUTES_WITH_COLONS = [
    "xlink:actuate",
    "xlink:arcrole",
    "xlink:href",
    "xlink:role",
    "xlink:show",
    "xlink:title",
    "xlink:type",
    "xml:base",
    "xml:lang",
    "xml:space",
    "xmlns:xlink"
  ].reduce(
    (map, attribute) => {
      map[attribute.replace(":", "").toLowerCase()] = attribute;
      return map;
    },
    {}
  );
  function getNormalAttributeName(attribute) {
    switch (attribute) {
      case "htmlFor":
        return "for";
      case "className":
        return "class";
    }
    const attributeLowerCase = attribute.toLowerCase();
    if (CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase]) {
      return CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase];
    } else if (SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]) {
      return paramCase(
        SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]
      );
    } else if (SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase]) {
      return SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase];
    }
    return attributeLowerCase;
  }
  function getNormalStylePropertyName(property) {
    if (property.startsWith("--")) {
      return property;
    }
    if (hasPrefix(property, ["ms", "O", "Moz", "Webkit"])) {
      return "-" + paramCase(property);
    }
    return paramCase(property);
  }
  function getNormalStylePropertyValue(property, value) {
    if (typeof value === "number" && 0 !== value && !hasPrefix(property, ["--"]) && !CSS_PROPERTIES_SUPPORTS_UNITLESS.has(property)) {
      return value + "px";
    }
    return value;
  }
  function renderElement(element, context, legacyContext = {}) {
    if (null === element || void 0 === element || false === element) {
      return "";
    }
    if (Array.isArray(element)) {
      return renderChildren(element, context, legacyContext);
    }
    switch (typeof element) {
      case "string":
        return (0, import_escape_html.escapeHTML)(element);
      case "number":
        return element.toString();
    }
    const { type, props } = element;
    switch (type) {
      case import_react.StrictMode:
      case import_react.Fragment:
        return renderChildren(props.children, context, legacyContext);
      case RawHTML:
        const { children, ...wrapperProps } = props;
        return renderNativeComponent(
          !Object.keys(wrapperProps).length ? null : "div",
          {
            ...wrapperProps,
            dangerouslySetInnerHTML: { __html: children }
          },
          context,
          legacyContext
        );
    }
    switch (typeof type) {
      case "string":
        return renderNativeComponent(type, props, context, legacyContext);
      case "function":
        if (type.prototype && typeof type.prototype.render === "function") {
          return renderComponent(type, props, context, legacyContext);
        }
        return renderElement(
          type(props, legacyContext),
          context,
          legacyContext
        );
    }
    switch (type && type.$$typeof) {
      case Provider.$$typeof:
        return renderChildren(props.children, props.value, legacyContext);
      case Consumer.$$typeof:
        return renderElement(
          props.children(context || type._currentValue),
          context,
          legacyContext
        );
      case ForwardRef.$$typeof:
        return renderElement(
          type.render(props),
          context,
          legacyContext
        );
    }
    return "";
  }
  function renderNativeComponent(type, props, context, legacyContext = {}) {
    let content = "";
    if (type === "textarea" && props.hasOwnProperty("value")) {
      content = renderChildren(props.value, context, legacyContext);
      const { value, ...restProps } = props;
      props = restProps;
    } else if (props.dangerouslySetInnerHTML && typeof props.dangerouslySetInnerHTML.__html === "string") {
      content = props.dangerouslySetInnerHTML.__html;
    } else if (typeof props.children !== "undefined") {
      content = renderChildren(props.children, context, legacyContext);
    }
    if (!type) {
      return content;
    }
    const attributes = renderAttributes(props);
    if (SELF_CLOSING_TAGS.has(type)) {
      return "<" + type + attributes + "/>";
    }
    return "<" + type + attributes + ">" + content + "</" + type + ">";
  }
  function renderComponent(Component2, props, context, legacyContext = {}) {
    const instance = new Component2(props, legacyContext);
    if (typeof instance.getChildContext === "function") {
      Object.assign(legacyContext, instance.getChildContext());
    }
    const html = renderElement(instance.render(), context, legacyContext);
    return html;
  }
  function renderChildren(children, context, legacyContext = {}) {
    let result = "";
    const childrenArray = Array.isArray(children) ? children : [children];
    for (let i = 0; i < childrenArray.length; i++) {
      const child = childrenArray[i];
      result += renderElement(child, context, legacyContext);
    }
    return result;
  }
  function renderAttributes(props) {
    let result = "";
    for (const key in props) {
      const attribute = getNormalAttributeName(key);
      if (!(0, import_escape_html.isValidAttributeName)(attribute)) {
        continue;
      }
      let value = getNormalAttributeValue(key, props[key]);
      if (!ATTRIBUTES_TYPES.has(typeof value)) {
        continue;
      }
      if (isInternalAttribute(key)) {
        continue;
      }
      const isBooleanAttribute = BOOLEAN_ATTRIBUTES.has(attribute);
      if (isBooleanAttribute && value === false) {
        continue;
      }
      const isMeaningfulAttribute = isBooleanAttribute || hasPrefix(key, ["data-", "aria-"]) || ENUMERATED_ATTRIBUTES.has(attribute);
      if (typeof value === "boolean" && !isMeaningfulAttribute) {
        continue;
      }
      result += " " + attribute;
      if (isBooleanAttribute) {
        continue;
      }
      if (typeof value === "string") {
        value = (0, import_escape_html.escapeAttribute)(value);
      }
      result += '="' + value + '"';
    }
    return result;
  }
  function renderStyle(style) {
    if (!isPlainObject(style)) {
      return style;
    }
    let result;
    const styleObj = style;
    for (const property in styleObj) {
      const value = styleObj[property];
      if (null === value || void 0 === value) {
        continue;
      }
      if (result) {
        result += ";";
      } else {
        result = "";
      }
      const normalName = getNormalStylePropertyName(property);
      const normalValue = getNormalStylePropertyValue(property, value);
      result += normalName + ":" + normalValue;
    }
    return result;
  }
  var serialize_default = renderElement;
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/
//# sourceMappingURL=index.js.map
