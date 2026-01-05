var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __require = /* @__PURE__ */ ((x2) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x2, {
  get: (a2, b2) => (typeof require !== "undefined" ? require : a2)[b2]
}) : x2)(function(x2) {
  if (typeof require !== "undefined") return require.apply(this, arguments);
  throw Error('Dynamic require of "' + x2 + '" is not supported');
});
var __commonJS = (cb, mod) => function __require4() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
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

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/editor
var require_editor = __commonJS({
  "package-external:@wordpress/editor"(exports, module) {
    module.exports = window.wp.editor;
  }
});

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
  }
});

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// package-external:@wordpress/block-editor
var require_block_editor = __commonJS({
  "package-external:@wordpress/block-editor"(exports, module) {
    module.exports = window.wp.blockEditor;
  }
});

// package-external:@wordpress/compose
var require_compose = __commonJS({
  "package-external:@wordpress/compose"(exports, module) {
    module.exports = window.wp.compose;
  }
});

// package-external:@wordpress/style-engine
var require_style_engine = __commonJS({
  "package-external:@wordpress/style-engine"(exports, module) {
    module.exports = window.wp.styleEngine;
  }
});

// node_modules/fast-deep-equal/es6/index.js
var require_es6 = __commonJS({
  "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
    "use strict";
    module.exports = function equal(a2, b2) {
      if (a2 === b2) return true;
      if (a2 && b2 && typeof a2 == "object" && typeof b2 == "object") {
        if (a2.constructor !== b2.constructor) return false;
        var length, i2, keys;
        if (Array.isArray(a2)) {
          length = a2.length;
          if (length != b2.length) return false;
          for (i2 = length; i2-- !== 0; )
            if (!equal(a2[i2], b2[i2])) return false;
          return true;
        }
        if (a2 instanceof Map && b2 instanceof Map) {
          if (a2.size !== b2.size) return false;
          for (i2 of a2.entries())
            if (!b2.has(i2[0])) return false;
          for (i2 of a2.entries())
            if (!equal(i2[1], b2.get(i2[0]))) return false;
          return true;
        }
        if (a2 instanceof Set && b2 instanceof Set) {
          if (a2.size !== b2.size) return false;
          for (i2 of a2.entries())
            if (!b2.has(i2[0])) return false;
          return true;
        }
        if (ArrayBuffer.isView(a2) && ArrayBuffer.isView(b2)) {
          length = a2.length;
          if (length != b2.length) return false;
          for (i2 = length; i2-- !== 0; )
            if (a2[i2] !== b2[i2]) return false;
          return true;
        }
        if (a2.constructor === RegExp) return a2.source === b2.source && a2.flags === b2.flags;
        if (a2.valueOf !== Object.prototype.valueOf) return a2.valueOf() === b2.valueOf();
        if (a2.toString !== Object.prototype.toString) return a2.toString() === b2.toString();
        keys = Object.keys(a2);
        length = keys.length;
        if (length !== Object.keys(b2).length) return false;
        for (i2 = length; i2-- !== 0; )
          if (!Object.prototype.hasOwnProperty.call(b2, keys[i2])) return false;
        for (i2 = length; i2-- !== 0; ) {
          var key = keys[i2];
          if (!equal(a2[key], b2[key])) return false;
        }
        return true;
      }
      return a2 !== a2 && b2 !== b2;
    };
  }
});

// node_modules/deepmerge/dist/cjs.js
var require_cjs = __commonJS({
  "node_modules/deepmerge/dist/cjs.js"(exports, module) {
    "use strict";
    var isMergeableObject = function isMergeableObject2(value) {
      return isNonNullObject(value) && !isSpecial(value);
    };
    function isNonNullObject(value) {
      return !!value && typeof value === "object";
    }
    function isSpecial(value) {
      var stringValue = Object.prototype.toString.call(value);
      return stringValue === "[object RegExp]" || stringValue === "[object Date]" || isReactElement(value);
    }
    var canUseSymbol = typeof Symbol === "function" && Symbol.for;
    var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for("react.element") : 60103;
    function isReactElement(value) {
      return value.$$typeof === REACT_ELEMENT_TYPE;
    }
    function emptyTarget(val) {
      return Array.isArray(val) ? [] : {};
    }
    function cloneUnlessOtherwiseSpecified(value, options) {
      return options.clone !== false && options.isMergeableObject(value) ? deepmerge2(emptyTarget(value), value, options) : value;
    }
    function defaultArrayMerge(target, source, options) {
      return target.concat(source).map(function(element) {
        return cloneUnlessOtherwiseSpecified(element, options);
      });
    }
    function getMergeFunction(key, options) {
      if (!options.customMerge) {
        return deepmerge2;
      }
      var customMerge = options.customMerge(key);
      return typeof customMerge === "function" ? customMerge : deepmerge2;
    }
    function getEnumerableOwnPropertySymbols(target) {
      return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function(symbol) {
        return Object.propertyIsEnumerable.call(target, symbol);
      }) : [];
    }
    function getKeys(target) {
      return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
    }
    function propertyIsOnObject(object, property) {
      try {
        return property in object;
      } catch (_) {
        return false;
      }
    }
    function propertyIsUnsafe(target, key) {
      return propertyIsOnObject(target, key) && !(Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key));
    }
    function mergeObject(target, source, options) {
      var destination = {};
      if (options.isMergeableObject(target)) {
        getKeys(target).forEach(function(key) {
          destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
        });
      }
      getKeys(source).forEach(function(key) {
        if (propertyIsUnsafe(target, key)) {
          return;
        }
        if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
          destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
        } else {
          destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
        }
      });
      return destination;
    }
    function deepmerge2(target, source, options) {
      options = options || {};
      options.arrayMerge = options.arrayMerge || defaultArrayMerge;
      options.isMergeableObject = options.isMergeableObject || isMergeableObject;
      options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
      var sourceIsArray = Array.isArray(source);
      var targetIsArray = Array.isArray(target);
      var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;
      if (!sourceAndTargetTypesMatch) {
        return cloneUnlessOtherwiseSpecified(source, options);
      } else if (sourceIsArray) {
        return options.arrayMerge(target, source, options);
      } else {
        return mergeObject(target, source, options);
      }
    }
    deepmerge2.all = function deepmergeAll(array, options) {
      if (!Array.isArray(array)) {
        throw new Error("first argument should be an array");
      }
      return array.reduce(function(prev, next) {
        return deepmerge2(prev, next, options);
      }, {});
    };
    var deepmerge_1 = deepmerge2;
    module.exports = deepmerge_1;
  }
});

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// package-external:@wordpress/keycodes
var require_keycodes = __commonJS({
  "package-external:@wordpress/keycodes"(exports, module) {
    module.exports = window.wp.keycodes;
  }
});

// package-external:@wordpress/api-fetch
var require_api_fetch = __commonJS({
  "package-external:@wordpress/api-fetch"(exports, module) {
    module.exports = window.wp.apiFetch;
  }
});

// package-external:@wordpress/date
var require_date = __commonJS({
  "package-external:@wordpress/date"(exports, module) {
    module.exports = window.wp.date;
  }
});

// node_modules/clsx/dist/clsx.mjs
function r(e2) {
  var t3, f2, n2 = "";
  if ("string" == typeof e2 || "number" == typeof e2) n2 += e2;
  else if ("object" == typeof e2) if (Array.isArray(e2)) {
    var o3 = e2.length;
    for (t3 = 0; t3 < o3; t3++) e2[t3] && (f2 = r(e2[t3])) && (n2 && (n2 += " "), n2 += f2);
  } else for (f2 in e2) e2[f2] && (n2 && (n2 += " "), n2 += f2);
  return n2;
}
function clsx() {
  for (var e2, t3, f2 = 0, n2 = "", o3 = arguments.length; f2 < o3; f2++) (e2 = arguments[f2]) && (t3 = r(e2)) && (n2 && (n2 += " "), n2 += t3);
  return n2;
}
var clsx_default = clsx;

// packages/admin-ui/build-module/navigable-region/index.js
var import_element = __toESM(require_element());
var import_jsx_runtime = __toESM(require_jsx_runtime());
var NavigableRegion = (0, import_element.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      Tag,
      {
        ref,
        className: clsx_default("admin-ui-navigable-region", className),
        "aria-label": ariaLabel,
        role: "region",
        tabIndex: "-1",
        ...props,
        children
      }
    );
  }
);
NavigableRegion.displayName = "NavigableRegion";
var navigable_region_default = NavigableRegion;

// packages/admin-ui/build-module/page/header.js
var import_components2 = __toESM(require_components());

// packages/admin-ui/build-module/page/sidebar-toggle-slot.js
var import_components = __toESM(require_components());
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.js
var import_jsx_runtime2 = __toESM(require_jsx_runtime());
function Header({
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalVStack, { className: "admin-ui-page__header", as: "header", children: [
    /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { justify: "space-between", spacing: 2, children: [
      /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { spacing: 2, justify: "left", children: [
        showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
          SidebarToggleSlot,
          {
            bubblesVirtually: true,
            className: "admin-ui-page__sidebar-toggle-slot"
          }
        ),
        title && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_components2.__experimentalHeading, { as: "h2", level: 3, weight: 500, truncate: true, children: title }),
        breadcrumbs,
        badges
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        import_components2.__experimentalHStack,
        {
          style: { width: "auto", flexShrink: 0 },
          spacing: 2,
          className: "admin-ui-page__header-actions",
          children: actions
        }
      )
    ] }),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}

// packages/admin-ui/build-module/page/index.js
var import_jsx_runtime3 = __toESM(require_jsx_runtime());
function Page({
  breadcrumbs,
  badges,
  title,
  subTitle,
  children,
  className,
  actions,
  hasPadding = false,
  showSidebarToggle = true
}) {
  const classes = clsx_default("admin-ui-page", className);
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      Header,
      {
        breadcrumbs,
        badges,
        title,
        subTitle,
        actions,
        showSidebarToggle
      }
    ),
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/font-list/stage.tsx
var import_i18n46 = __toESM(require_i18n());
var import_components63 = __toESM(require_components());
var import_editor = __toESM(require_editor());
var import_core_data12 = __toESM(require_core_data());
var import_data13 = __toESM(require_data());
var import_element34 = __toESM(require_element());

// packages/global-styles-ui/build-module/global-styles-ui.js
var import_components62 = __toESM(require_components());
var import_blocks5 = __toESM(require_blocks());
var import_data12 = __toESM(require_data());
var import_block_editor13 = __toESM(require_block_editor());
var import_element33 = __toESM(require_element());
var import_compose6 = __toESM(require_compose());

// packages/global-styles-engine/build-module/utils/object.js
function setImmutably(object, path, value) {
  path = Array.isArray(path) ? [...path] : [path];
  object = Array.isArray(object) ? [...object] : { ...object };
  const leaf = path.pop();
  let prev = object;
  for (const key of path) {
    const lvl = prev[key];
    prev = prev[key] = Array.isArray(lvl) ? [...lvl] : { ...lvl };
  }
  prev[leaf] = value;
  return object;
}
var getValueFromObjectPath = (object, path, defaultValue) => {
  const arrayPath = Array.isArray(path) ? path : path.split(".");
  let value = object;
  arrayPath.forEach((fieldName) => {
    value = value?.[fieldName];
  });
  return value ?? defaultValue;
};

// packages/global-styles-engine/build-module/settings/get-setting.js
var VALID_SETTINGS = [
  "appearanceTools",
  "useRootPaddingAwareAlignments",
  "background.backgroundImage",
  "background.backgroundRepeat",
  "background.backgroundSize",
  "background.backgroundPosition",
  "border.color",
  "border.radius",
  "border.radiusSizes",
  "border.style",
  "border.width",
  "shadow.presets",
  "shadow.defaultPresets",
  "color.background",
  "color.button",
  "color.caption",
  "color.custom",
  "color.customDuotone",
  "color.customGradient",
  "color.defaultDuotone",
  "color.defaultGradients",
  "color.defaultPalette",
  "color.duotone",
  "color.gradients",
  "color.heading",
  "color.link",
  "color.palette",
  "color.text",
  "custom",
  "dimensions.aspectRatio",
  "dimensions.height",
  "dimensions.minHeight",
  "dimensions.width",
  "dimensions.dimensionSizes",
  "layout.contentSize",
  "layout.definitions",
  "layout.wideSize",
  "lightbox.enabled",
  "lightbox.allowEditing",
  "position.fixed",
  "position.sticky",
  "spacing.customSpacingSize",
  "spacing.defaultSpacingSizes",
  "spacing.spacingSizes",
  "spacing.spacingScale",
  "spacing.blockGap",
  "spacing.margin",
  "spacing.padding",
  "spacing.units",
  "typography.fluid",
  "typography.customFontSize",
  "typography.defaultFontSizes",
  "typography.dropCap",
  "typography.fontFamilies",
  "typography.fontSizes",
  "typography.fontStyle",
  "typography.fontWeight",
  "typography.letterSpacing",
  "typography.lineHeight",
  "typography.textAlign",
  "typography.textColumns",
  "typography.textDecoration",
  "typography.textTransform",
  "typography.writingMode"
];
function getSetting(globalStyles, path, blockName) {
  const appendedBlockPath = blockName ? ".blocks." + blockName : "";
  const appendedPropertyPath = path ? "." + path : "";
  const contextualPath = `settings${appendedBlockPath}${appendedPropertyPath}`;
  const globalPath = `settings${appendedPropertyPath}`;
  if (path) {
    return getValueFromObjectPath(globalStyles, contextualPath) ?? getValueFromObjectPath(globalStyles, globalPath);
  }
  let result = {};
  VALID_SETTINGS.forEach((setting) => {
    const value = getValueFromObjectPath(
      globalStyles,
      `settings${appendedBlockPath}.${setting}`
    ) ?? getValueFromObjectPath(globalStyles, `settings.${setting}`);
    if (value !== void 0) {
      result = setImmutably(result, setting.split("."), value);
    }
  });
  return result;
}

// packages/global-styles-engine/build-module/settings/set-setting.js
function setSetting(globalStyles, path, newValue, blockName) {
  const appendedBlockPath = blockName ? ".blocks." + blockName : "";
  const appendedPropertyPath = path ? "." + path : "";
  const finalPath = `settings${appendedBlockPath}${appendedPropertyPath}`;
  return setImmutably(
    globalStyles,
    finalPath.split("."),
    newValue
  );
}

// packages/global-styles-engine/build-module/utils/common.js
var import_style_engine = __toESM(require_style_engine());

// packages/global-styles-engine/build-module/utils/fluid.js
var DEFAULT_MAXIMUM_VIEWPORT_WIDTH = "1600px";
var DEFAULT_MINIMUM_VIEWPORT_WIDTH = "320px";
var DEFAULT_SCALE_FACTOR = 1;
var DEFAULT_MINIMUM_FONT_SIZE_FACTOR_MIN = 0.25;
var DEFAULT_MINIMUM_FONT_SIZE_FACTOR_MAX = 0.75;
var DEFAULT_MINIMUM_FONT_SIZE_LIMIT = "14px";
function getComputedFluidTypographyValue({
  minimumFontSize,
  maximumFontSize,
  fontSize,
  minimumViewportWidth = DEFAULT_MINIMUM_VIEWPORT_WIDTH,
  maximumViewportWidth = DEFAULT_MAXIMUM_VIEWPORT_WIDTH,
  scaleFactor = DEFAULT_SCALE_FACTOR,
  minimumFontSizeLimit
}) {
  minimumFontSizeLimit = !!getTypographyValueAndUnit(minimumFontSizeLimit) ? minimumFontSizeLimit : DEFAULT_MINIMUM_FONT_SIZE_LIMIT;
  if (fontSize) {
    const fontSizeParsed = getTypographyValueAndUnit(fontSize);
    if (!fontSizeParsed?.unit || !fontSizeParsed?.value) {
      return null;
    }
    const minimumFontSizeLimitParsed = getTypographyValueAndUnit(
      minimumFontSizeLimit,
      {
        coerceTo: fontSizeParsed.unit
      }
    );
    if (!!minimumFontSizeLimitParsed?.value && !minimumFontSize && !maximumFontSize) {
      if (fontSizeParsed?.value <= minimumFontSizeLimitParsed?.value) {
        return null;
      }
    }
    if (!maximumFontSize) {
      maximumFontSize = `${fontSizeParsed.value}${fontSizeParsed.unit}`;
    }
    if (!minimumFontSize) {
      const fontSizeValueInPx = fontSizeParsed.unit === "px" ? fontSizeParsed.value : fontSizeParsed.value * 16;
      const minimumFontSizeFactor = Math.min(
        Math.max(
          1 - 0.075 * Math.log2(fontSizeValueInPx),
          DEFAULT_MINIMUM_FONT_SIZE_FACTOR_MIN
        ),
        DEFAULT_MINIMUM_FONT_SIZE_FACTOR_MAX
      );
      const calculatedMinimumFontSize = roundToPrecision(
        fontSizeParsed.value * minimumFontSizeFactor,
        3
      );
      if (!!minimumFontSizeLimitParsed?.value && calculatedMinimumFontSize < minimumFontSizeLimitParsed?.value) {
        minimumFontSize = `${minimumFontSizeLimitParsed.value}${minimumFontSizeLimitParsed.unit}`;
      } else {
        minimumFontSize = `${calculatedMinimumFontSize}${fontSizeParsed.unit}`;
      }
    }
  }
  const minimumFontSizeParsed = getTypographyValueAndUnit(minimumFontSize);
  const fontSizeUnit = minimumFontSizeParsed?.unit || "rem";
  const maximumFontSizeParsed = getTypographyValueAndUnit(maximumFontSize, {
    coerceTo: fontSizeUnit
  });
  if (!minimumFontSizeParsed || !maximumFontSizeParsed) {
    return null;
  }
  const minimumFontSizeRem = getTypographyValueAndUnit(minimumFontSize, {
    coerceTo: "rem"
  });
  const maximumViewportWidthParsed = getTypographyValueAndUnit(
    maximumViewportWidth,
    { coerceTo: fontSizeUnit }
  );
  const minimumViewportWidthParsed = getTypographyValueAndUnit(
    minimumViewportWidth,
    { coerceTo: fontSizeUnit }
  );
  if (!maximumViewportWidthParsed || !minimumViewportWidthParsed || !minimumFontSizeRem) {
    return null;
  }
  const linearDenominator = maximumViewportWidthParsed.value - minimumViewportWidthParsed.value;
  if (!linearDenominator) {
    return null;
  }
  const minViewportWidthOffsetValue = roundToPrecision(
    minimumViewportWidthParsed.value / 100,
    3
  );
  const viewportWidthOffset = roundToPrecision(minViewportWidthOffsetValue, 3) + fontSizeUnit;
  const linearFactor = 100 * ((maximumFontSizeParsed.value - minimumFontSizeParsed.value) / linearDenominator);
  const linearFactorScaled = roundToPrecision(
    (linearFactor || 1) * scaleFactor,
    3
  );
  const fluidTargetFontSize = `${minimumFontSizeRem.value}${minimumFontSizeRem.unit} + ((1vw - ${viewportWidthOffset}) * ${linearFactorScaled})`;
  return `clamp(${minimumFontSize}, ${fluidTargetFontSize}, ${maximumFontSize})`;
}
function getTypographyValueAndUnit(rawValue, options = {}) {
  if (typeof rawValue !== "string" && typeof rawValue !== "number") {
    return null;
  }
  if (isFinite(rawValue)) {
    rawValue = `${rawValue}px`;
  }
  const { coerceTo, rootSizeValue, acceptableUnits } = {
    coerceTo: "",
    // Default browser font size. Later we could inject some JS to compute this `getComputedStyle( document.querySelector( "html" ) ).fontSize`.
    rootSizeValue: 16,
    acceptableUnits: ["rem", "px", "em"],
    ...options
  };
  const acceptableUnitsGroup = acceptableUnits?.join("|");
  const regexUnits = new RegExp(
    `^(\\d*\\.?\\d+)(${acceptableUnitsGroup}){1,1}$`
  );
  const matches = rawValue.toString().match(regexUnits);
  if (!matches || matches.length < 3) {
    return null;
  }
  let [, value, unit] = matches;
  let returnValue = parseFloat(value);
  if ("px" === coerceTo && ("em" === unit || "rem" === unit)) {
    returnValue = returnValue * rootSizeValue;
    unit = coerceTo;
  }
  if ("px" === unit && ("em" === coerceTo || "rem" === coerceTo)) {
    returnValue = returnValue / rootSizeValue;
    unit = coerceTo;
  }
  if (("em" === coerceTo || "rem" === coerceTo) && ("em" === unit || "rem" === unit)) {
    unit = coerceTo;
  }
  if (!unit) {
    return null;
  }
  return {
    value: roundToPrecision(returnValue, 3),
    unit
  };
}
function roundToPrecision(value, digits = 3) {
  const base = Math.pow(10, digits);
  return Math.round(value * base) / base;
}

// packages/global-styles-engine/build-module/utils/typography.js
function isFluidTypographyEnabled(typographySettings) {
  const fluidSettings = typographySettings?.fluid;
  return true === fluidSettings || fluidSettings && typeof fluidSettings === "object" && Object.keys(fluidSettings).length > 0;
}
function getFluidTypographyOptionsFromSettings(settings) {
  const typographySettings = settings?.typography ?? {};
  const layoutSettings = settings?.layout;
  const defaultMaxViewportWidth = getTypographyValueAndUnit(
    layoutSettings?.wideSize
  ) ? layoutSettings?.wideSize : null;
  return isFluidTypographyEnabled(typographySettings) && defaultMaxViewportWidth ? {
    fluid: {
      maxViewportWidth: defaultMaxViewportWidth,
      ...typeof typographySettings.fluid === "object" ? typographySettings.fluid : {}
    }
  } : {
    fluid: typographySettings?.fluid
  };
}
function getTypographyFontSizeValue(preset, settings) {
  const { size: defaultSize } = preset;
  if (!defaultSize || "0" === defaultSize || false === preset?.fluid) {
    return defaultSize;
  }
  if (!isFluidTypographyEnabled(settings?.typography) && !isFluidTypographyEnabled(preset)) {
    return defaultSize;
  }
  const fluidTypographySettings = getFluidTypographyOptionsFromSettings(settings)?.fluid ?? {};
  const fluidFontSizeValue = getComputedFluidTypographyValue({
    minimumFontSize: typeof preset?.fluid === "boolean" ? void 0 : preset?.fluid?.min,
    maximumFontSize: typeof preset?.fluid === "boolean" ? void 0 : preset?.fluid?.max,
    fontSize: defaultSize,
    minimumFontSizeLimit: typeof fluidTypographySettings === "object" ? fluidTypographySettings?.minFontSize : void 0,
    maximumViewportWidth: typeof fluidTypographySettings === "object" ? fluidTypographySettings?.maxViewportWidth : void 0,
    minimumViewportWidth: typeof fluidTypographySettings === "object" ? fluidTypographySettings?.minViewportWidth : void 0
  });
  if (!!fluidFontSizeValue) {
    return fluidFontSizeValue;
  }
  return defaultSize;
}

// packages/global-styles-engine/build-module/utils/common.js
var PRESET_METADATA = [
  {
    path: ["color", "palette"],
    valueKey: "color",
    cssVarInfix: "color",
    classes: [
      { classSuffix: "color", propertyName: "color" },
      {
        classSuffix: "background-color",
        propertyName: "background-color"
      },
      {
        classSuffix: "border-color",
        propertyName: "border-color"
      }
    ]
  },
  {
    path: ["color", "gradients"],
    valueKey: "gradient",
    cssVarInfix: "gradient",
    classes: [
      {
        classSuffix: "gradient-background",
        propertyName: "background"
      }
    ]
  },
  {
    path: ["color", "duotone"],
    valueKey: "colors",
    cssVarInfix: "duotone",
    valueFunc: ({ slug }) => `url( '#wp-duotone-${slug}' )`,
    classes: []
  },
  {
    path: ["shadow", "presets"],
    valueKey: "shadow",
    cssVarInfix: "shadow",
    classes: []
  },
  {
    path: ["typography", "fontSizes"],
    valueFunc: (preset, settings) => getTypographyFontSizeValue(preset, settings),
    valueKey: "size",
    cssVarInfix: "font-size",
    classes: [{ classSuffix: "font-size", propertyName: "font-size" }]
  },
  {
    path: ["typography", "fontFamilies"],
    valueKey: "fontFamily",
    cssVarInfix: "font-family",
    classes: [
      { classSuffix: "font-family", propertyName: "font-family" }
    ]
  },
  {
    path: ["spacing", "spacingSizes"],
    valueKey: "size",
    cssVarInfix: "spacing",
    valueFunc: ({ size }) => size,
    classes: []
  },
  {
    path: ["border", "radiusSizes"],
    valueKey: "size",
    cssVarInfix: "border-radius",
    classes: []
  },
  {
    path: ["dimensions", "dimensionSizes"],
    valueKey: "size",
    cssVarInfix: "dimension",
    classes: []
  }
];
function findInPresetsBy(settings, blockName, presetPath = [], presetProperty = "slug", presetValueValue) {
  const orderedPresetsByOrigin = [
    blockName ? getValueFromObjectPath(settings, [
      "blocks",
      blockName,
      ...presetPath
    ]) : void 0,
    getValueFromObjectPath(settings, presetPath)
  ].filter(Boolean);
  for (const presetByOrigin of orderedPresetsByOrigin) {
    if (presetByOrigin) {
      const origins = ["custom", "theme", "default"];
      for (const origin of origins) {
        const presets = presetByOrigin[origin];
        if (presets) {
          const presetObject = presets.find(
            (preset) => preset[presetProperty] === presetValueValue
          );
          if (presetObject) {
            if (presetProperty === "slug") {
              return presetObject;
            }
            const highestPresetObjectWithSameSlug = findInPresetsBy(
              settings,
              blockName,
              presetPath,
              "slug",
              presetObject.slug
            );
            if (highestPresetObjectWithSameSlug[presetProperty] === presetObject[presetProperty]) {
              return presetObject;
            }
            return void 0;
          }
        }
      }
    }
  }
}
function getValueFromPresetVariable(features, blockName, variable, [presetType, slug] = []) {
  const metadata = PRESET_METADATA.find(
    (data) => data.cssVarInfix === presetType
  );
  if (!metadata || !features.settings) {
    return variable;
  }
  const presetObject = findInPresetsBy(
    features.settings,
    blockName,
    metadata.path,
    "slug",
    slug
  );
  if (presetObject) {
    const { valueKey } = metadata;
    const result = presetObject[valueKey];
    return getValueFromVariable(features, blockName, result);
  }
  return variable;
}
function getValueFromCustomVariable(features, blockName, variable, path = []) {
  const result = (blockName ? getValueFromObjectPath(features?.settings ?? {}, [
    "blocks",
    blockName,
    "custom",
    ...path
  ]) : void 0) ?? getValueFromObjectPath(features?.settings ?? {}, [
    "custom",
    ...path
  ]);
  if (!result) {
    return variable;
  }
  return getValueFromVariable(features, blockName, result);
}
function getValueFromVariable(features, blockName, variable) {
  if (!variable || typeof variable !== "string") {
    if (typeof variable === "object" && variable !== null && "ref" in variable && typeof variable.ref === "string") {
      const resolvedVariable = getValueFromObjectPath(
        features,
        variable.ref
      );
      if (!resolvedVariable || typeof resolvedVariable === "object" && "ref" in resolvedVariable) {
        return resolvedVariable;
      }
      variable = resolvedVariable;
    } else {
      return variable;
    }
  }
  const USER_VALUE_PREFIX = "var:";
  const THEME_VALUE_PREFIX = "var(--wp--";
  const THEME_VALUE_SUFFIX = ")";
  let parsedVar;
  if (variable.startsWith(USER_VALUE_PREFIX)) {
    parsedVar = variable.slice(USER_VALUE_PREFIX.length).split("|");
  } else if (variable.startsWith(THEME_VALUE_PREFIX) && variable.endsWith(THEME_VALUE_SUFFIX)) {
    parsedVar = variable.slice(THEME_VALUE_PREFIX.length, -THEME_VALUE_SUFFIX.length).split("--");
  } else {
    return variable;
  }
  const [type, ...path] = parsedVar;
  if (type === "preset") {
    return getValueFromPresetVariable(
      features,
      blockName,
      variable,
      path
    );
  }
  if (type === "custom") {
    return getValueFromCustomVariable(
      features,
      blockName,
      variable,
      path
    );
  }
  return variable;
}

// packages/global-styles-engine/build-module/settings/get-style.js
function getStyle(globalStyles, path, blockName, shouldDecodeEncode = true) {
  const appendedPath = path ? "." + path : "";
  const finalPath = !blockName ? `styles${appendedPath}` : `styles.blocks.${blockName}${appendedPath}`;
  if (!globalStyles) {
    return void 0;
  }
  const rawResult = getValueFromObjectPath(globalStyles, finalPath);
  const result = shouldDecodeEncode ? getValueFromVariable(globalStyles, blockName, rawResult) : rawResult;
  return result;
}

// packages/global-styles-engine/build-module/settings/set-style.js
function setStyle(globalStyles, path, newValue, blockName) {
  const appendedPath = path ? "." + path : "";
  const finalPath = !blockName ? `styles${appendedPath}` : `styles.blocks.${blockName}${appendedPath}`;
  return setImmutably(
    globalStyles,
    finalPath.split("."),
    newValue
  );
}

// packages/global-styles-engine/build-module/core/equal.js
var import_es6 = __toESM(require_es6());
function areGlobalStylesEqual(original, variation) {
  if (typeof original !== "object" || typeof variation !== "object") {
    return original === variation;
  }
  return (0, import_es6.default)(original?.styles, variation?.styles) && (0, import_es6.default)(original?.settings, variation?.settings);
}

// packages/global-styles-engine/build-module/core/merge.js
var import_deepmerge = __toESM(require_cjs());

// node_modules/is-plain-object/dist/is-plain-object.mjs
function isObject(o3) {
  return Object.prototype.toString.call(o3) === "[object Object]";
}
function isPlainObject(o3) {
  var ctor, prot;
  if (isObject(o3) === false) return false;
  ctor = o3.constructor;
  if (ctor === void 0) return true;
  prot = ctor.prototype;
  if (isObject(prot) === false) return false;
  if (prot.hasOwnProperty("isPrototypeOf") === false) {
    return false;
  }
  return true;
}

// packages/global-styles-engine/build-module/core/merge.js
function mergeGlobalStyles(base, user) {
  return (0, import_deepmerge.default)(base, user, {
    /*
     * We only pass as arrays the presets,
     * in which case we want the new array of values
     * to override the old array (no merging).
     */
    isMergeableObject: isPlainObject,
    /*
     * Exceptions to the above rule.
     * Background images should be replaced, not merged,
     * as they themselves are specific object definitions for the style.
     */
    customMerge: (key) => {
      if (key === "backgroundImage") {
        return (baseConfig, userConfig) => userConfig ?? baseConfig;
      }
      return void 0;
    }
  });
}

// node_modules/colord/index.mjs
var r2 = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
var t = function(r3) {
  return "string" == typeof r3 ? r3.length > 0 : "number" == typeof r3;
};
var n = function(r3, t3, n2) {
  return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = Math.pow(10, t3)), Math.round(n2 * r3) / n2 + 0;
};
var e = function(r3, t3, n2) {
  return void 0 === t3 && (t3 = 0), void 0 === n2 && (n2 = 1), r3 > n2 ? n2 : r3 > t3 ? r3 : t3;
};
var u = function(r3) {
  return (r3 = isFinite(r3) ? r3 % 360 : 0) > 0 ? r3 : r3 + 360;
};
var a = function(r3) {
  return { r: e(r3.r, 0, 255), g: e(r3.g, 0, 255), b: e(r3.b, 0, 255), a: e(r3.a) };
};
var o = function(r3) {
  return { r: n(r3.r), g: n(r3.g), b: n(r3.b), a: n(r3.a, 3) };
};
var i = /^#([0-9a-f]{3,8})$/i;
var s = function(r3) {
  var t3 = r3.toString(16);
  return t3.length < 2 ? "0" + t3 : t3;
};
var h = function(r3) {
  var t3 = r3.r, n2 = r3.g, e2 = r3.b, u2 = r3.a, a2 = Math.max(t3, n2, e2), o3 = a2 - Math.min(t3, n2, e2), i2 = o3 ? a2 === t3 ? (n2 - e2) / o3 : a2 === n2 ? 2 + (e2 - t3) / o3 : 4 + (t3 - n2) / o3 : 0;
  return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o3 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
};
var b = function(r3) {
  var t3 = r3.h, n2 = r3.s, e2 = r3.v, u2 = r3.a;
  t3 = t3 / 360 * 6, n2 /= 100, e2 /= 100;
  var a2 = Math.floor(t3), o3 = e2 * (1 - n2), i2 = e2 * (1 - (t3 - a2) * n2), s2 = e2 * (1 - (1 - t3 + a2) * n2), h2 = a2 % 6;
  return { r: 255 * [e2, i2, o3, o3, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o3, o3][h2], b: 255 * [o3, o3, s2, e2, e2, i2][h2], a: u2 };
};
var g = function(r3) {
  return { h: u(r3.h), s: e(r3.s, 0, 100), l: e(r3.l, 0, 100), a: e(r3.a) };
};
var d = function(r3) {
  return { h: n(r3.h), s: n(r3.s), l: n(r3.l), a: n(r3.a, 3) };
};
var f = function(r3) {
  return b((n2 = (t3 = r3).s, { h: t3.h, s: (n2 *= ((e2 = t3.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t3.a }));
  var t3, n2, e2;
};
var c = function(r3) {
  return { h: (t3 = h(r3)).h, s: (u2 = (200 - (n2 = t3.s)) * (e2 = t3.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t3.a };
  var t3, n2, e2, u2;
};
var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var p2 = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var y = { string: [[function(r3) {
  var t3 = i.exec(r3);
  return t3 ? (r3 = t3[1]).length <= 4 ? { r: parseInt(r3[0] + r3[0], 16), g: parseInt(r3[1] + r3[1], 16), b: parseInt(r3[2] + r3[2], 16), a: 4 === r3.length ? n(parseInt(r3[3] + r3[3], 16) / 255, 2) : 1 } : 6 === r3.length || 8 === r3.length ? { r: parseInt(r3.substr(0, 2), 16), g: parseInt(r3.substr(2, 2), 16), b: parseInt(r3.substr(4, 2), 16), a: 8 === r3.length ? n(parseInt(r3.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
}, "hex"], [function(r3) {
  var t3 = v.exec(r3) || m.exec(r3);
  return t3 ? t3[2] !== t3[4] || t3[4] !== t3[6] ? null : a({ r: Number(t3[1]) / (t3[2] ? 100 / 255 : 1), g: Number(t3[3]) / (t3[4] ? 100 / 255 : 1), b: Number(t3[5]) / (t3[6] ? 100 / 255 : 1), a: void 0 === t3[7] ? 1 : Number(t3[7]) / (t3[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t3) {
  var n2 = l.exec(t3) || p2.exec(t3);
  if (!n2) return null;
  var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r2[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
  return f(a2);
}, "hsl"]], object: [[function(r3) {
  var n2 = r3.r, e2 = r3.g, u2 = r3.b, o3 = r3.a, i2 = void 0 === o3 ? 1 : o3;
  return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
}, "rgb"], [function(r3) {
  var n2 = r3.h, e2 = r3.s, u2 = r3.l, a2 = r3.a, o3 = void 0 === a2 ? 1 : a2;
  if (!t(n2) || !t(e2) || !t(u2)) return null;
  var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o3) });
  return f(i2);
}, "hsl"], [function(r3) {
  var n2 = r3.h, a2 = r3.s, o3 = r3.v, i2 = r3.a, s2 = void 0 === i2 ? 1 : i2;
  if (!t(n2) || !t(a2) || !t(o3)) return null;
  var h2 = (function(r4) {
    return { h: u(r4.h), s: e(r4.s, 0, 100), v: e(r4.v, 0, 100), a: e(r4.a) };
  })({ h: Number(n2), s: Number(a2), v: Number(o3), a: Number(s2) });
  return b(h2);
}, "hsv"]] };
var N = function(r3, t3) {
  for (var n2 = 0; n2 < t3.length; n2++) {
    var e2 = t3[n2][0](r3);
    if (e2) return [e2, t3[n2][1]];
  }
  return [null, void 0];
};
var x = function(r3) {
  return "string" == typeof r3 ? N(r3.trim(), y.string) : "object" == typeof r3 && null !== r3 ? N(r3, y.object) : [null, void 0];
};
var M = function(r3, t3) {
  var n2 = c(r3);
  return { h: n2.h, s: e(n2.s + 100 * t3, 0, 100), l: n2.l, a: n2.a };
};
var H = function(r3) {
  return (299 * r3.r + 587 * r3.g + 114 * r3.b) / 1e3 / 255;
};
var $ = function(r3, t3) {
  var n2 = c(r3);
  return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t3, 0, 100), a: n2.a };
};
var j = (function() {
  function r3(r4) {
    this.parsed = x(r4)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
  }
  return r3.prototype.isValid = function() {
    return null !== this.parsed;
  }, r3.prototype.brightness = function() {
    return n(H(this.rgba), 2);
  }, r3.prototype.isDark = function() {
    return H(this.rgba) < 0.5;
  }, r3.prototype.isLight = function() {
    return H(this.rgba) >= 0.5;
  }, r3.prototype.toHex = function() {
    return r4 = o(this.rgba), t3 = r4.r, e2 = r4.g, u2 = r4.b, i2 = (a2 = r4.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t3) + s(e2) + s(u2) + i2;
    var r4, t3, e2, u2, a2, i2;
  }, r3.prototype.toRgb = function() {
    return o(this.rgba);
  }, r3.prototype.toRgbString = function() {
    return r4 = o(this.rgba), t3 = r4.r, n2 = r4.g, e2 = r4.b, (u2 = r4.a) < 1 ? "rgba(" + t3 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t3 + ", " + n2 + ", " + e2 + ")";
    var r4, t3, n2, e2, u2;
  }, r3.prototype.toHsl = function() {
    return d(c(this.rgba));
  }, r3.prototype.toHslString = function() {
    return r4 = d(c(this.rgba)), t3 = r4.h, n2 = r4.s, e2 = r4.l, (u2 = r4.a) < 1 ? "hsla(" + t3 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t3 + ", " + n2 + "%, " + e2 + "%)";
    var r4, t3, n2, e2, u2;
  }, r3.prototype.toHsv = function() {
    return r4 = h(this.rgba), { h: n(r4.h), s: n(r4.s), v: n(r4.v), a: n(r4.a, 3) };
    var r4;
  }, r3.prototype.invert = function() {
    return w({ r: 255 - (r4 = this.rgba).r, g: 255 - r4.g, b: 255 - r4.b, a: r4.a });
    var r4;
  }, r3.prototype.saturate = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w(M(this.rgba, r4));
  }, r3.prototype.desaturate = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w(M(this.rgba, -r4));
  }, r3.prototype.grayscale = function() {
    return w(M(this.rgba, -1));
  }, r3.prototype.lighten = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w($(this.rgba, r4));
  }, r3.prototype.darken = function(r4) {
    return void 0 === r4 && (r4 = 0.1), w($(this.rgba, -r4));
  }, r3.prototype.rotate = function(r4) {
    return void 0 === r4 && (r4 = 15), this.hue(this.hue() + r4);
  }, r3.prototype.alpha = function(r4) {
    return "number" == typeof r4 ? w({ r: (t3 = this.rgba).r, g: t3.g, b: t3.b, a: r4 }) : n(this.rgba.a, 3);
    var t3;
  }, r3.prototype.hue = function(r4) {
    var t3 = c(this.rgba);
    return "number" == typeof r4 ? w({ h: r4, s: t3.s, l: t3.l, a: t3.a }) : n(t3.h);
  }, r3.prototype.isEqual = function(r4) {
    return this.toHex() === w(r4).toHex();
  }, r3;
})();
var w = function(r3) {
  return r3 instanceof j ? r3 : new j(r3);
};
var S = [];
var k = function(r3) {
  r3.forEach(function(r4) {
    S.indexOf(r4) < 0 && (r4(j, y), S.push(r4));
  });
};

// packages/global-styles-ui/build-module/provider.js
var import_element3 = __toESM(require_element());

// packages/global-styles-ui/build-module/context.js
var import_element2 = __toESM(require_element());
var GlobalStylesContext = (0, import_element2.createContext)({
  user: { styles: {}, settings: {} },
  base: { styles: {}, settings: {} },
  merged: { styles: {}, settings: {} },
  onChange: () => {
  },
  fontLibraryEnabled: false
});

// packages/global-styles-ui/build-module/provider.js
var import_jsx_runtime4 = __toESM(require_jsx_runtime());
function GlobalStylesProvider({
  children,
  value,
  baseValue,
  onChange,
  fontLibraryEnabled
}) {
  const merged = (0, import_element3.useMemo)(() => {
    return mergeGlobalStyles(baseValue, value);
  }, [baseValue, value]);
  const contextValue = (0, import_element3.useMemo)(
    () => ({
      user: value,
      base: baseValue,
      merged,
      onChange,
      fontLibraryEnabled
    }),
    [value, baseValue, merged, onChange, fontLibraryEnabled]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(GlobalStylesContext.Provider, { value: contextValue, children });
}

// packages/global-styles-ui/build-module/screen-root.js
var import_components9 = __toESM(require_components());
var import_i18n4 = __toESM(require_i18n());

// packages/icons/build-module/icon/index.js
var import_element4 = __toESM(require_element());
var icon_default = (0, import_element4.forwardRef)(
  ({ icon, size = 24, ...props }, ref) => {
    return (0, import_element4.cloneElement)(icon, {
      width: size,
      height: size,
      ...props,
      ref
    });
  }
);

// packages/icons/build-module/library/chevron-left.js
var import_primitives = __toESM(require_primitives());
var import_jsx_runtime5 = __toESM(require_jsx_runtime());
var chevron_left_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });

// packages/icons/build-module/library/chevron-right.js
var import_primitives2 = __toESM(require_primitives());
var import_jsx_runtime6 = __toESM(require_jsx_runtime());
var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives2.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

// packages/icons/build-module/library/more-vertical.js
var import_primitives3 = __toESM(require_primitives());
var import_jsx_runtime7 = __toESM(require_jsx_runtime());
var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives3.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });

// packages/icons/build-module/library/next.js
var import_primitives4 = __toESM(require_primitives());
var import_jsx_runtime8 = __toESM(require_jsx_runtime());
var next_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives4.Path, { d: "M6.6 6L5.4 7l4.5 5-4.5 5 1.1 1 5.5-6-5.4-6zm6 0l-1.1 1 4.5 5-4.5 5 1.1 1 5.5-6-5.5-6z" }) });

// packages/icons/build-module/library/previous.js
var import_primitives5 = __toESM(require_primitives());
var import_jsx_runtime9 = __toESM(require_jsx_runtime());
var previous_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives5.Path, { d: "M11.6 7l-1.1-1L5 12l5.5 6 1.1-1L7 12l4.6-5zm6 0l-1.1-1-5.5 6 5.5 6 1.1-1-4.6-5 4.6-5z" }) });

// packages/global-styles-ui/build-module/screen-root.js
var import_data2 = __toESM(require_data());
var import_core_data2 = __toESM(require_core_data());

// packages/global-styles-ui/build-module/icon-with-current-color.js
var import_jsx_runtime10 = __toESM(require_jsx_runtime());
function IconWithCurrentColor({
  className,
  ...props
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
    icon_default,
    {
      className: clsx_default(
        className,
        "global-styles-ui-icon-with-current-color"
      ),
      ...props
    }
  );
}

// packages/global-styles-ui/build-module/navigation-button.js
var import_components3 = __toESM(require_components());
var import_jsx_runtime11 = __toESM(require_jsx_runtime());
function GenericNavigationButton({
  icon,
  children,
  ...props
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components3.__experimentalItem, { ...props, children: [
    icon && /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components3.__experimentalHStack, { justify: "flex-start", children: [
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(IconWithCurrentColor, { icon, size: 24 }),
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components3.FlexItem, { children })
    ] }),
    !icon && children
  ] });
}
function NavigationButtonAsItem(props) {
  return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components3.Navigator.Button, { as: GenericNavigationButton, ...props });
}

// packages/global-styles-ui/build-module/root-menu.js
var import_components4 = __toESM(require_components());
var import_i18n2 = __toESM(require_i18n());
var import_block_editor = __toESM(require_block_editor());

// node_modules/colord/plugins/a11y.mjs
var o2 = function(o3) {
  var t3 = o3 / 255;
  return t3 < 0.04045 ? t3 / 12.92 : Math.pow((t3 + 0.055) / 1.055, 2.4);
};
var t2 = function(t3) {
  return 0.2126 * o2(t3.r) + 0.7152 * o2(t3.g) + 0.0722 * o2(t3.b);
};
function a11y_default(o3) {
  o3.prototype.luminance = function() {
    return o4 = t2(this.rgba), void 0 === (r3 = 2) && (r3 = 0), void 0 === n2 && (n2 = Math.pow(10, r3)), Math.round(n2 * o4) / n2 + 0;
    var o4, r3, n2;
  }, o3.prototype.contrast = function(r3) {
    void 0 === r3 && (r3 = "#FFF");
    var n2, a2, i2, e2, v2, u2, d2, c2 = r3 instanceof o3 ? r3 : new o3(r3);
    return e2 = this.rgba, v2 = c2.toRgb(), u2 = t2(e2), d2 = t2(v2), n2 = u2 > d2 ? (u2 + 0.05) / (d2 + 0.05) : (d2 + 0.05) / (u2 + 0.05), void 0 === (a2 = 2) && (a2 = 0), void 0 === i2 && (i2 = Math.pow(10, a2)), Math.floor(i2 * n2) / i2 + 0;
  }, o3.prototype.isReadable = function(o4, t3) {
    return void 0 === o4 && (o4 = "#FFF"), void 0 === t3 && (t3 = {}), this.contrast(o4) >= (e2 = void 0 === (i2 = (r3 = t3).size) ? "normal" : i2, "AAA" === (a2 = void 0 === (n2 = r3.level) ? "AA" : n2) && "normal" === e2 ? 7 : "AA" === a2 && "large" === e2 ? 3 : 4.5);
    var r3, n2, a2, i2, e2;
  };
}

// packages/global-styles-ui/build-module/hooks.js
var import_element5 = __toESM(require_element());
var import_data = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var import_i18n = __toESM(require_i18n());

// packages/global-styles-ui/build-module/utils.js
function removePropertiesFromObject(object, properties) {
  if (!properties?.length) {
    return object;
  }
  if (typeof object !== "object" || !object || !Object.keys(object).length) {
    return object;
  }
  for (const key in object) {
    if (properties.includes(key)) {
      delete object[key];
    } else if (typeof object[key] === "object") {
      removePropertiesFromObject(object[key], properties);
    }
  }
  return object;
}
var filterObjectByProperties = (object, properties) => {
  if (!object || !properties?.length) {
    return {};
  }
  const newObject = {};
  Object.keys(object).forEach((key) => {
    if (properties.includes(key)) {
      newObject[key] = object[key];
    } else if (typeof object[key] === "object") {
      const newFilter = filterObjectByProperties(
        object[key],
        properties
      );
      if (Object.keys(newFilter).length) {
        newObject[key] = newFilter;
      }
    }
  });
  return newObject;
};
function isVariationWithProperties(variation, properties) {
  const variationWithProperties = filterObjectByProperties(
    structuredClone(variation),
    properties
  );
  return areGlobalStylesEqual(variationWithProperties, variation);
}
function getFontFamilyFromSetting(fontFamilies, setting) {
  if (!Array.isArray(fontFamilies) || !setting) {
    return null;
  }
  const fontFamilyVariable = setting.replace("var(", "").replace(")", "");
  const fontFamilySlug = fontFamilyVariable?.split("--").slice(-1)[0];
  return fontFamilies.find(
    (fontFamily) => fontFamily.slug === fontFamilySlug
  );
}
function getFontFamilies(themeJson) {
  const themeFontFamilies = themeJson?.settings?.typography?.fontFamilies?.theme;
  const customFontFamilies = themeJson?.settings?.typography?.fontFamilies?.custom;
  let fontFamilies = [];
  if (themeFontFamilies && customFontFamilies) {
    fontFamilies = [...themeFontFamilies, ...customFontFamilies];
  } else if (themeFontFamilies) {
    fontFamilies = themeFontFamilies;
  } else if (customFontFamilies) {
    fontFamilies = customFontFamilies;
  }
  const bodyFontFamilySetting = themeJson?.styles?.typography?.fontFamily;
  const bodyFontFamily = getFontFamilyFromSetting(
    fontFamilies,
    bodyFontFamilySetting
  );
  const headingFontFamilySetting = themeJson?.styles?.elements?.heading?.typography?.fontFamily;
  let headingFontFamily;
  if (!headingFontFamilySetting) {
    headingFontFamily = bodyFontFamily;
  } else {
    headingFontFamily = getFontFamilyFromSetting(
      fontFamilies,
      themeJson?.styles?.elements?.heading?.typography?.fontFamily
    );
  }
  return [bodyFontFamily, headingFontFamily];
}

// packages/global-styles-ui/build-module/hooks.js
k([a11y_default]);
function useStyle(path, blockName, readFrom = "merged", shouldDecodeEncode = true) {
  const { user, base, merged, onChange } = (0, import_element5.useContext)(GlobalStylesContext);
  let sourceValue = merged;
  if (readFrom === "base") {
    sourceValue = base;
  } else if (readFrom === "user") {
    sourceValue = user;
  }
  const styleValue = (0, import_element5.useMemo)(
    () => getStyle(sourceValue, path, blockName, shouldDecodeEncode),
    [sourceValue, path, blockName, shouldDecodeEncode]
  );
  const setStyleValue = (0, import_element5.useCallback)(
    (newValue) => {
      const newGlobalStyles = setStyle(
        user,
        path,
        newValue,
        blockName
      );
      onChange(newGlobalStyles);
    },
    [user, onChange, path, blockName]
  );
  return [styleValue, setStyleValue];
}
function useSetting(path, blockName, readFrom = "merged") {
  const { user, base, merged, onChange } = (0, import_element5.useContext)(GlobalStylesContext);
  let sourceValue = merged;
  if (readFrom === "base") {
    sourceValue = base;
  } else if (readFrom === "user") {
    sourceValue = user;
  }
  const settingValue = (0, import_element5.useMemo)(
    () => getSetting(sourceValue, path, blockName),
    [sourceValue, path, blockName]
  );
  const setSettingValue = (0, import_element5.useCallback)(
    (newValue) => {
      const newGlobalStyles = setSetting(
        user,
        path,
        newValue,
        blockName
      );
      onChange(newGlobalStyles);
    },
    [user, onChange, path, blockName]
  );
  return [settingValue, setSettingValue];
}
var EMPTY_ARRAY = [];
function hasThemeVariation({
  title,
  settings,
  styles
}) {
  return title === (0, import_i18n.__)("Default") || Object.keys(settings || {}).length > 0 || Object.keys(styles || {}).length > 0;
}
function useCurrentMergeThemeStyleVariationsWithUserConfig(properties = []) {
  const { variationsFromTheme } = (0, import_data.useSelect)((select) => {
    const _variationsFromTheme = select(
      import_core_data.store
    ).__experimentalGetCurrentThemeGlobalStylesVariations?.();
    return {
      variationsFromTheme: _variationsFromTheme || EMPTY_ARRAY
    };
  }, []);
  const { user: userVariation } = (0, import_element5.useContext)(GlobalStylesContext);
  return (0, import_element5.useMemo)(() => {
    const clonedUserVariation = structuredClone(userVariation);
    const userVariationWithoutProperties = removePropertiesFromObject(
      clonedUserVariation,
      properties
    );
    userVariationWithoutProperties.title = (0, import_i18n.__)("Default");
    const variationsWithPropertiesAndBase = variationsFromTheme.filter((variation) => {
      return isVariationWithProperties(variation, properties);
    }).map((variation) => {
      return mergeGlobalStyles(
        userVariationWithoutProperties,
        variation
      );
    });
    const variationsByProperties = [
      userVariationWithoutProperties,
      ...variationsWithPropertiesAndBase
    ];
    return variationsByProperties?.length ? variationsByProperties.filter(hasThemeVariation) : [];
  }, [properties, userVariation, variationsFromTheme]);
}

// packages/global-styles-ui/build-module/lock-unlock.js
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/global-styles-ui"
);

// packages/global-styles-ui/build-module/root-menu.js
var import_jsx_runtime12 = __toESM(require_jsx_runtime());
var {
  useHasDimensionsPanel,
  useHasTypographyPanel,
  useHasColorPanel,
  useSettingsForBlockElement,
  useHasBackgroundPanel
} = unlock(import_block_editor.privateApis);

// packages/global-styles-ui/build-module/preview-styles.js
var import_components8 = __toESM(require_components());

// packages/global-styles-ui/build-module/preview-hooks.js
function useStylesPreviewColors() {
  const [textColor = "black"] = useStyle("color.text");
  const [backgroundColor = "white"] = useStyle("color.background");
  const [headingColor = textColor] = useStyle(
    "elements.h1.color.text"
  );
  const [linkColor = headingColor] = useStyle(
    "elements.link.color.text"
  );
  const [buttonBackgroundColor = linkColor] = useStyle(
    "elements.button.color.background"
  );
  const [coreColors] = useSetting("color.palette.core") || [];
  const [themeColors] = useSetting("color.palette.theme") || [];
  const [customColors] = useSetting("color.palette.custom") || [];
  const paletteColors = (themeColors ?? []).concat(customColors ?? []).concat(coreColors ?? []);
  const textColorObject = paletteColors.filter(
    ({ color }) => color === textColor
  );
  const buttonBackgroundColorObject = paletteColors.filter(
    ({ color }) => color === buttonBackgroundColor
  );
  const highlightedColors = textColorObject.concat(buttonBackgroundColorObject).concat(paletteColors).filter(
    // we exclude these background color because it is already visible in the preview.
    ({ color }) => color !== backgroundColor
  ).slice(0, 2);
  return {
    paletteColors,
    highlightedColors
  };
}

// packages/global-styles-ui/build-module/typography-example.js
var import_element6 = __toESM(require_element());
var import_components5 = __toESM(require_components());
var import_i18n3 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/font-library/utils/preview-styles.js
function findNearest(input, numbers) {
  if (numbers.length === 0) {
    return null;
  }
  numbers.sort((a2, b2) => Math.abs(input - a2) - Math.abs(input - b2));
  return numbers[0];
}
function extractFontWeights(fontFaces) {
  const result = [];
  fontFaces.forEach((face) => {
    const weights = String(face.fontWeight).split(" ");
    if (weights.length === 2) {
      const start = parseInt(weights[0]);
      const end = parseInt(weights[1]);
      for (let i2 = start; i2 <= end; i2 += 100) {
        result.push(i2);
      }
    } else if (weights.length === 1) {
      result.push(parseInt(weights[0]));
    }
  });
  return result;
}
function formatFontFamily(input) {
  const regex = /^(?!generic\([ a-zA-Z\-]+\)$)(?!^[a-zA-Z\-]+$).+/;
  const output = input.trim();
  const formatItem = (item) => {
    item = item.trim();
    if (item.match(regex)) {
      item = item.replace(/^["']|["']$/g, "");
      return `"${item}"`;
    }
    return item;
  };
  if (output.includes(",")) {
    return output.split(",").map(formatItem).filter((item) => item !== "").join(", ");
  }
  return formatItem(output);
}
function formatFontFaceName(input) {
  if (!input) {
    return "";
  }
  let output = input.trim();
  if (output.includes(",")) {
    output = (output.split(",").find((item) => item.trim() !== "") ?? "").trim();
  }
  output = output.replace(/^["']|["']$/g, "");
  if (window.navigator.userAgent.toLowerCase().includes("firefox")) {
    output = `"${output}"`;
  }
  return output;
}
function getFamilyPreviewStyle(family) {
  const style = {
    fontFamily: formatFontFamily(family.fontFamily)
  };
  if (!("fontFace" in family) || !Array.isArray(family.fontFace)) {
    style.fontWeight = "400";
    style.fontStyle = "normal";
    return style;
  }
  if (family.fontFace) {
    const normalFaces = family.fontFace.filter(
      (face) => face?.fontStyle && face.fontStyle.toLowerCase() === "normal"
    );
    if (normalFaces.length > 0) {
      style.fontStyle = "normal";
      const normalWeights = extractFontWeights(normalFaces);
      const nearestWeight = findNearest(400, normalWeights);
      style.fontWeight = String(nearestWeight) || "400";
    } else {
      style.fontStyle = family.fontFace.length && family.fontFace[0].fontStyle || "normal";
      style.fontWeight = family.fontFace.length && String(family.fontFace[0].fontWeight) || "400";
    }
  }
  return style;
}
function getFacePreviewStyle(face) {
  return {
    fontFamily: formatFontFamily(face.fontFamily),
    fontStyle: face.fontStyle || "normal",
    fontWeight: face.fontWeight || "400"
  };
}

// packages/global-styles-ui/build-module/typography-example.js
var import_jsx_runtime13 = __toESM(require_jsx_runtime());
function PreviewTypography({
  fontSize,
  variation
}) {
  const { base } = (0, import_element6.useContext)(GlobalStylesContext);
  let config = base;
  if (variation) {
    config = { ...base, ...variation };
  }
  const [textColor] = useStyle("color.text");
  const [bodyFontFamilies, headingFontFamilies] = getFontFamilies(config);
  const bodyPreviewStyle = bodyFontFamilies ? getFamilyPreviewStyle(bodyFontFamilies) : {};
  const headingPreviewStyle = headingFontFamilies ? getFamilyPreviewStyle(headingFontFamilies) : {};
  if (textColor) {
    bodyPreviewStyle.color = textColor;
    headingPreviewStyle.color = textColor;
  }
  if (fontSize) {
    bodyPreviewStyle.fontSize = fontSize;
    headingPreviewStyle.fontSize = fontSize;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(
    import_components5.__unstableMotion.div,
    {
      animate: {
        scale: 1,
        opacity: 1
      },
      initial: {
        scale: 0.1,
        opacity: 0
      },
      transition: {
        delay: 0.3,
        type: "tween"
      },
      style: {
        textAlign: "center",
        lineHeight: 1
      },
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime13.jsx)("span", { style: headingPreviewStyle, children: (0, import_i18n3._x)("A", "Uppercase letter A") }),
        /* @__PURE__ */ (0, import_jsx_runtime13.jsx)("span", { style: bodyPreviewStyle, children: (0, import_i18n3._x)("a", "Lowercase letter A") })
      ]
    }
  );
}

// packages/global-styles-ui/build-module/highlighted-colors.js
var import_components6 = __toESM(require_components());
var import_jsx_runtime14 = __toESM(require_jsx_runtime());
function HighlightedColors({
  normalizedColorSwatchSize,
  ratio
}) {
  const { highlightedColors } = useStylesPreviewColors();
  const scaledSwatchSize = normalizedColorSwatchSize * ratio;
  return highlightedColors.map(({ slug, color }, index) => /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
    import_components6.__unstableMotion.div,
    {
      style: {
        height: scaledSwatchSize,
        width: scaledSwatchSize,
        background: color,
        borderRadius: scaledSwatchSize / 2
      },
      animate: {
        scale: 1,
        opacity: 1
      },
      initial: {
        scale: 0.1,
        opacity: 0
      },
      transition: {
        delay: index === 1 ? 0.2 : 0.1
      }
    },
    `${slug}-${index}`
  ));
}

// packages/global-styles-ui/build-module/preview-wrapper.js
var import_components7 = __toESM(require_components());
var import_compose = __toESM(require_compose());
var import_element7 = __toESM(require_element());
var import_jsx_runtime15 = __toESM(require_jsx_runtime());
var normalizedWidth = 248;
var normalizedHeight = 152;
var THROTTLE_OPTIONS = {
  leading: true,
  trailing: true
};
function PreviewWrapper({
  children,
  label,
  isFocused,
  withHoverView
}) {
  const [backgroundColor = "white"] = useStyle("color.background");
  const [gradientValue] = useStyle("color.gradient");
  const disableMotion = (0, import_compose.useReducedMotion)();
  const [isHovered, setIsHovered] = (0, import_element7.useState)(false);
  const [containerResizeListener, { width }] = (0, import_compose.useResizeObserver)();
  const [throttledWidth, setThrottledWidthState] = (0, import_element7.useState)(width);
  const [ratioState, setRatioState] = (0, import_element7.useState)();
  const setThrottledWidth = (0, import_compose.useThrottle)(
    setThrottledWidthState,
    250,
    THROTTLE_OPTIONS
  );
  (0, import_element7.useLayoutEffect)(() => {
    if (width) {
      setThrottledWidth(width);
    }
  }, [width, setThrottledWidth]);
  (0, import_element7.useLayoutEffect)(() => {
    const newRatio = throttledWidth ? throttledWidth / normalizedWidth : 1;
    const ratioDiff = newRatio - (ratioState || 0);
    const isRatioDiffBigEnough = Math.abs(ratioDiff) > 0.1;
    if (isRatioDiffBigEnough || !ratioState) {
      setRatioState(newRatio);
    }
  }, [throttledWidth, ratioState]);
  const fallbackRatio = width ? width / normalizedWidth : 1;
  const ratio = ratioState ? ratioState : fallbackRatio;
  const isReady = !!width;
  return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(import_jsx_runtime15.Fragment, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime15.jsx)("div", { style: { position: "relative" }, children: containerResizeListener }),
    isReady && /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
      "div",
      {
        className: "global-styles-ui-preview__wrapper",
        style: {
          height: normalizedHeight * ratio
        },
        onMouseEnter: () => setIsHovered(true),
        onMouseLeave: () => setIsHovered(false),
        tabIndex: -1,
        children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
          import_components7.__unstableMotion.div,
          {
            style: {
              height: normalizedHeight * ratio,
              width: "100%",
              background: gradientValue ?? backgroundColor,
              cursor: withHoverView ? "pointer" : void 0
            },
            initial: "start",
            animate: (isHovered || isFocused) && !disableMotion && label ? "hover" : "start",
            children: [].concat(children).map(
              (child, key) => child({ ratio, key })
            )
          }
        )
      }
    )
  ] });
}
var preview_wrapper_default = PreviewWrapper;

// packages/global-styles-ui/build-module/preview-styles.js
var import_jsx_runtime16 = __toESM(require_jsx_runtime());
var firstFrameVariants = {
  start: {
    scale: 1,
    opacity: 1
  },
  hover: {
    scale: 0,
    opacity: 0
  }
};
var midFrameVariants = {
  hover: {
    opacity: 1
  },
  start: {
    opacity: 0.5
  }
};
var secondFrameVariants = {
  hover: {
    scale: 1,
    opacity: 1
  },
  start: {
    scale: 0,
    opacity: 0
  }
};
function PreviewStyles({
  label,
  isFocused,
  withHoverView,
  variation
}) {
  const [fontWeight] = useStyle("typography.fontWeight");
  const [fontFamily = "serif"] = useStyle(
    "typography.fontFamily"
  );
  const [headingFontFamily = fontFamily] = useStyle(
    "elements.h1.typography.fontFamily"
  );
  const [headingFontWeight = fontWeight] = useStyle(
    "elements.h1.typography.fontWeight"
  );
  const [textColor = "black"] = useStyle("color.text");
  const [headingColor = textColor] = useStyle(
    "elements.h1.color.text"
  );
  const { paletteColors } = useStylesPreviewColors();
  return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(
    preview_wrapper_default,
    {
      label,
      isFocused,
      withHoverView,
      children: [
        ({ ratio, key }) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
          import_components8.__unstableMotion.div,
          {
            variants: firstFrameVariants,
            style: {
              height: "100%",
              overflow: "hidden"
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(
              import_components8.__experimentalHStack,
              {
                spacing: 10 * ratio,
                justify: "center",
                style: {
                  height: "100%",
                  overflow: "hidden"
                },
                children: [
                  /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                    PreviewTypography,
                    {
                      fontSize: 65 * ratio,
                      variation
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components8.__experimentalVStack, { spacing: 4 * ratio, children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                    HighlightedColors,
                    {
                      normalizedColorSwatchSize: 32,
                      ratio
                    }
                  ) })
                ]
              }
            )
          },
          key
        ),
        ({ key }) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
          import_components8.__unstableMotion.div,
          {
            variants: withHoverView ? midFrameVariants : void 0,
            style: {
              height: "100%",
              width: "100%",
              position: "absolute",
              top: 0,
              overflow: "hidden",
              filter: "blur(60px)",
              opacity: 0.1
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
              import_components8.__experimentalHStack,
              {
                spacing: 0,
                justify: "flex-start",
                style: {
                  height: "100%",
                  overflow: "hidden"
                },
                children: paletteColors.slice(0, 4).map(({ color }, index) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                  "div",
                  {
                    style: {
                      height: "100%",
                      background: color,
                      flexGrow: 1
                    }
                  },
                  index
                ))
              }
            )
          },
          key
        ),
        ({ ratio, key }) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
          import_components8.__unstableMotion.div,
          {
            variants: secondFrameVariants,
            style: {
              height: "100%",
              width: "100%",
              overflow: "hidden",
              position: "absolute",
              top: 0
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
              import_components8.__experimentalVStack,
              {
                spacing: 3 * ratio,
                justify: "center",
                style: {
                  height: "100%",
                  overflow: "hidden",
                  padding: 10 * ratio,
                  boxSizing: "border-box"
                },
                children: label && /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                  "div",
                  {
                    style: {
                      fontSize: 40 * ratio,
                      fontFamily: headingFontFamily,
                      color: headingColor,
                      fontWeight: headingFontWeight,
                      lineHeight: "1em",
                      textAlign: "center"
                    },
                    children: label
                  }
                )
              }
            )
          },
          key
        )
      ]
    }
  );
}
var preview_styles_default = PreviewStyles;

// packages/global-styles-ui/build-module/screen-root.js
var import_jsx_runtime17 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-block-list.js
var import_blocks2 = __toESM(require_blocks());
var import_i18n6 = __toESM(require_i18n());
var import_components12 = __toESM(require_components());
var import_data4 = __toESM(require_data());
var import_element8 = __toESM(require_element());
var import_block_editor2 = __toESM(require_block_editor());
var import_compose2 = __toESM(require_compose());
import { speak } from "@wordpress/a11y";

// packages/global-styles-ui/build-module/variations/variations-panel.js
var import_blocks = __toESM(require_blocks());
var import_data3 = __toESM(require_data());
var import_components10 = __toESM(require_components());
var import_jsx_runtime18 = __toESM(require_jsx_runtime());
function getFilteredBlockStyles(blockStyles, variations) {
  return blockStyles?.filter(
    (style) => style.source === "block" || variations.includes(style.name)
  ) || [];
}
function useBlockVariations(name2) {
  const blockStyles = (0, import_data3.useSelect)(
    (select) => {
      const { getBlockStyles } = select(import_blocks.store);
      return getBlockStyles(name2);
    },
    [name2]
  );
  const [variations] = useStyle("variations", name2);
  const variationNames = Object.keys(variations ?? {});
  return getFilteredBlockStyles(blockStyles, variationNames);
}

// packages/global-styles-ui/build-module/screen-header.js
var import_components11 = __toESM(require_components());
var import_i18n5 = __toESM(require_i18n());
var import_jsx_runtime19 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-block-list.js
var import_jsx_runtime20 = __toESM(require_jsx_runtime());
var {
  useHasDimensionsPanel: useHasDimensionsPanel2,
  useHasTypographyPanel: useHasTypographyPanel2,
  useHasBorderPanel,
  useSettingsForBlockElement: useSettingsForBlockElement2,
  useHasColorPanel: useHasColorPanel2
} = unlock(import_block_editor2.privateApis);
function useSortedBlockTypes() {
  const blockItems = (0, import_data4.useSelect)(
    (select) => select(import_blocks2.store).getBlockTypes(),
    []
  );
  const groupByType = (blocks, block) => {
    const { core, noncore } = blocks;
    const type = block.name.startsWith("core/") ? core : noncore;
    type.push(block);
    return blocks;
  };
  const { core: coreItems, noncore: nonCoreItems } = blockItems.reduce(
    groupByType,
    { core: [], noncore: [] }
  );
  return [...coreItems, ...nonCoreItems];
}
function useBlockHasGlobalStyles(blockName) {
  const [rawSettings] = useSetting("", blockName);
  const settings = useSettingsForBlockElement2(rawSettings, blockName);
  const hasTypographyPanel = useHasTypographyPanel2(settings);
  const hasColorPanel = useHasColorPanel2(settings);
  const hasBorderPanel = useHasBorderPanel(settings);
  const hasDimensionsPanel = useHasDimensionsPanel2(settings);
  const hasLayoutPanel = hasBorderPanel || hasDimensionsPanel;
  const hasVariationsPanel = !!useBlockVariations(blockName)?.length;
  const hasGlobalStyles = hasTypographyPanel || hasColorPanel || hasLayoutPanel || hasVariationsPanel;
  return hasGlobalStyles;
}
function BlockMenuItem({ block }) {
  const hasBlockMenuItem = useBlockHasGlobalStyles(block.name);
  if (!hasBlockMenuItem) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
    NavigationButtonAsItem,
    {
      path: "/blocks/" + encodeURIComponent(block.name),
      children: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_components12.__experimentalHStack, { justify: "flex-start", children: [
        /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_block_editor2.BlockIcon, { icon: block.icon }),
        /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components12.FlexItem, { children: block.title })
      ] })
    }
  );
}
function BlockList({ filterValue }) {
  const sortedBlockTypes = useSortedBlockTypes();
  const debouncedSpeak = (0, import_compose2.useDebounce)(speak, 500);
  const { isMatchingSearchTerm } = (0, import_data4.useSelect)(import_blocks2.store);
  const filteredBlockTypes = !filterValue ? sortedBlockTypes : sortedBlockTypes.filter(
    (blockType) => isMatchingSearchTerm(blockType, filterValue)
  );
  const blockTypesListRef = (0, import_element8.useRef)(null);
  (0, import_element8.useEffect)(() => {
    if (!filterValue) {
      return;
    }
    const count = blockTypesListRef.current?.childElementCount || 0;
    const resultsFoundMessage = (0, import_i18n6.sprintf)(
      /* translators: %d: number of results. */
      (0, import_i18n6._n)("%d result found.", "%d results found.", count),
      count
    );
    debouncedSpeak(resultsFoundMessage, "polite");
  }, [filterValue, debouncedSpeak]);
  return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
    "div",
    {
      ref: blockTypesListRef,
      className: "global-styles-ui-block-types-item-list",
      role: "list",
      children: filteredBlockTypes.length === 0 ? /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components12.__experimentalText, { align: "center", as: "p", children: (0, import_i18n6.__)("No blocks found.") }) : filteredBlockTypes.map((block) => /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        BlockMenuItem,
        {
          block
        },
        "menu-itemblock-" + block.name
      ))
    }
  );
}
var MemoizedBlockList = (0, import_element8.memo)(BlockList);

// packages/global-styles-ui/build-module/screen-block.js
var import_blocks4 = __toESM(require_blocks());
var import_block_editor4 = __toESM(require_block_editor());
var import_element10 = __toESM(require_element());
var import_data5 = __toESM(require_data());
var import_core_data3 = __toESM(require_core_data());
var import_components15 = __toESM(require_components());
var import_i18n7 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/block-preview-panel.js
var import_block_editor3 = __toESM(require_block_editor());
var import_blocks3 = __toESM(require_blocks());
var import_components13 = __toESM(require_components());
var import_element9 = __toESM(require_element());
var import_jsx_runtime21 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/subtitle.js
var import_components14 = __toESM(require_components());
var import_jsx_runtime22 = __toESM(require_jsx_runtime());
function Subtitle({ children, level = 2 }) {
  return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_components14.__experimentalHeading, { className: "global-styles-ui-subtitle", level, children });
}

// packages/global-styles-ui/build-module/screen-block.js
var import_jsx_runtime23 = __toESM(require_jsx_runtime());
var {
  useHasDimensionsPanel: useHasDimensionsPanel3,
  useHasTypographyPanel: useHasTypographyPanel3,
  useHasBorderPanel: useHasBorderPanel2,
  useSettingsForBlockElement: useSettingsForBlockElement3,
  useHasColorPanel: useHasColorPanel3,
  useHasFiltersPanel,
  useHasImageSettingsPanel,
  useHasBackgroundPanel: useHasBackgroundPanel2,
  BackgroundPanel: StylesBackgroundPanel,
  BorderPanel: StylesBorderPanel,
  ColorPanel: StylesColorPanel,
  TypographyPanel: StylesTypographyPanel,
  DimensionsPanel: StylesDimensionsPanel,
  FiltersPanel: StylesFiltersPanel,
  ImageSettingsPanel,
  AdvancedPanel: StylesAdvancedPanel
} = unlock(import_block_editor4.privateApis);

// packages/global-styles-ui/build-module/screen-typography.js
var import_i18n21 = __toESM(require_i18n());
var import_components35 = __toESM(require_components());
var import_element21 = __toESM(require_element());

// packages/global-styles-ui/build-module/screen-body.js
var import_components16 = __toESM(require_components());
var import_jsx_runtime24 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/typography-elements.js
var import_i18n8 = __toESM(require_i18n());
var import_components17 = __toESM(require_components());
var import_jsx_runtime25 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/variations/variations-typography.js
var import_components20 = __toESM(require_components());

// packages/global-styles-ui/build-module/preview-typography.js
var import_components18 = __toESM(require_components());
var import_jsx_runtime26 = __toESM(require_jsx_runtime());
var StylesPreviewTypography = ({
  variation,
  isFocused,
  withHoverView
}) => {
  return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
    preview_wrapper_default,
    {
      label: variation.title,
      isFocused,
      withHoverView,
      children: ({ ratio, key }) => /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
        import_components18.__experimentalHStack,
        {
          spacing: 10 * ratio,
          justify: "center",
          style: {
            height: "100%",
            overflow: "hidden"
          },
          children: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
            PreviewTypography,
            {
              variation,
              fontSize: 85 * ratio
            }
          )
        },
        key
      )
    }
  );
};
var preview_typography_default = StylesPreviewTypography;

// packages/global-styles-ui/build-module/variations/variation.js
var import_components19 = __toESM(require_components());
var import_element11 = __toESM(require_element());
var import_keycodes = __toESM(require_keycodes());
var import_i18n9 = __toESM(require_i18n());
var import_jsx_runtime27 = __toESM(require_jsx_runtime());
function Variation({
  variation,
  children,
  isPill = false,
  properties,
  showTooltip = false
}) {
  const [isFocused, setIsFocused] = (0, import_element11.useState)(false);
  const {
    base,
    user,
    onChange: setUserConfig
  } = (0, import_element11.useContext)(GlobalStylesContext);
  const context = (0, import_element11.useMemo)(() => {
    let merged = mergeGlobalStyles(base, variation);
    if (properties) {
      merged = filterObjectByProperties(merged, properties);
    }
    return {
      user: variation,
      base,
      merged,
      onChange: () => {
      }
    };
  }, [variation, base, properties]);
  const selectVariation = () => setUserConfig(variation);
  const selectOnEnter = (event) => {
    if (event.keyCode === import_keycodes.ENTER) {
      event.preventDefault();
      selectVariation();
    }
  };
  const isActive = (0, import_element11.useMemo)(
    () => areGlobalStylesEqual(user, variation),
    [user, variation]
  );
  let label = variation?.title;
  if (variation?.description) {
    label = (0, import_i18n9.sprintf)(
      /* translators: 1: variation title. 2: variation description. */
      (0, import_i18n9._x)("%1$s (%2$s)", "variation label"),
      variation?.title,
      variation?.description
    );
  }
  const content = /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
    "div",
    {
      className: clsx_default("global-styles-ui-variations_item", {
        "is-active": isActive
      }),
      role: "button",
      onClick: selectVariation,
      onKeyDown: selectOnEnter,
      tabIndex: 0,
      "aria-label": label,
      "aria-current": isActive,
      onFocus: () => setIsFocused(true),
      onBlur: () => setIsFocused(false),
      children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
        "div",
        {
          className: clsx_default("global-styles-ui-variations_item-preview", {
            "is-pill": isPill
          }),
          children: children(isFocused)
        }
      )
    }
  );
  return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(GlobalStylesContext.Provider, { value: context, children: showTooltip ? /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_components19.Tooltip, { text: variation?.title, children: content }) : content });
}

// packages/global-styles-ui/build-module/variations/variations-typography.js
var import_jsx_runtime28 = __toESM(require_jsx_runtime());
var propertiesToFilter = ["typography"];
function TypographyVariations({
  title,
  gap = 2
}) {
  const typographyVariations = useCurrentMergeThemeStyleVariationsWithUserConfig(propertiesToFilter);
  if (typographyVariations?.length <= 1) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(import_components20.__experimentalVStack, { spacing: 3, children: [
    title && /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(Subtitle, { level: 3, children: title }),
    /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      import_components20.__experimentalGrid,
      {
        columns: 3,
        gap,
        className: "global-styles-ui-style-variations-container",
        children: typographyVariations.map(
          (variation, index) => {
            return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
              Variation,
              {
                variation,
                properties: propertiesToFilter,
                showTooltip: true,
                children: () => /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
                  preview_typography_default,
                  {
                    variation
                  }
                )
              },
              index
            );
          }
        )
      }
    )
  ] });
}

// packages/global-styles-ui/build-module/font-families.js
var import_i18n19 = __toESM(require_i18n());
var import_components33 = __toESM(require_components());
var import_element20 = __toESM(require_element());

// packages/global-styles-ui/build-module/font-library/context.js
var import_element12 = __toESM(require_element());
var import_data6 = __toESM(require_data());
var import_core_data5 = __toESM(require_core_data());
var import_i18n11 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/font-library/api.js
var import_api_fetch = __toESM(require_api_fetch());
var import_core_data4 = __toESM(require_core_data());
var FONT_FAMILIES_URL = "/wp/v2/font-families";
function invalidateFontFamilyCache(registry) {
  const { receiveEntityRecords } = registry.dispatch(import_core_data4.store);
  receiveEntityRecords(
    "postType",
    "wp_font_family",
    [],
    void 0,
    true
    // invalidateCache
  );
}
async function fetchInstallFontFamily(data, registry) {
  const config = {
    path: FONT_FAMILIES_URL,
    method: "POST",
    body: data
  };
  const response = await (0, import_api_fetch.default)(config);
  invalidateFontFamilyCache(registry);
  return {
    id: response.id,
    ...response.font_family_settings,
    fontFace: []
  };
}
async function fetchInstallFontFace(fontFamilyId, data, registry) {
  const config = {
    path: `${FONT_FAMILIES_URL}/${fontFamilyId}/font-faces`,
    method: "POST",
    body: data
  };
  const response = await (0, import_api_fetch.default)(config);
  invalidateFontFamilyCache(registry);
  return {
    id: response.id,
    ...response.font_face_settings
  };
}

// packages/global-styles-ui/build-module/font-library/utils/index.js
var import_components21 = __toESM(require_components());

// packages/global-styles-ui/build-module/font-library/utils/constants.js
var import_i18n10 = __toESM(require_i18n());
var ALLOWED_FILE_EXTENSIONS = ["otf", "ttf", "woff", "woff2"];
var FONT_WEIGHTS = {
  100: (0, import_i18n10._x)("Thin", "font weight"),
  200: (0, import_i18n10._x)("Extra-light", "font weight"),
  300: (0, import_i18n10._x)("Light", "font weight"),
  400: (0, import_i18n10._x)("Normal", "font weight"),
  500: (0, import_i18n10._x)("Medium", "font weight"),
  600: (0, import_i18n10._x)("Semi-bold", "font weight"),
  700: (0, import_i18n10._x)("Bold", "font weight"),
  800: (0, import_i18n10._x)("Extra-bold", "font weight"),
  900: (0, import_i18n10._x)("Black", "font weight")
};
var FONT_STYLES = {
  normal: (0, import_i18n10._x)("Normal", "font style"),
  italic: (0, import_i18n10._x)("Italic", "font style")
};

// packages/global-styles-ui/build-module/font-library/utils/index.js
var { File } = window;
var { kebabCase } = unlock(import_components21.privateApis);
function setUIValuesNeeded(font2, extraValues = {}) {
  if (!font2.name && (font2.fontFamily || font2.slug)) {
    font2.name = font2.fontFamily || font2.slug;
  }
  return {
    ...font2,
    ...extraValues
  };
}
function isUrlEncoded(url) {
  if (typeof url !== "string") {
    return false;
  }
  return url !== decodeURIComponent(url);
}
function getFontFaceVariantName(face) {
  const weightName = FONT_WEIGHTS[face.fontWeight ?? ""] || face.fontWeight;
  const styleName = face.fontStyle === "normal" ? "" : FONT_STYLES[face.fontStyle ?? ""] || face.fontStyle;
  return `${weightName} ${styleName}`;
}
function mergeFontFaces(existing = [], incoming = []) {
  const map = /* @__PURE__ */ new Map();
  for (const face of existing) {
    map.set(`${face.fontWeight}${face.fontStyle}`, face);
  }
  for (const face of incoming) {
    map.set(`${face.fontWeight}${face.fontStyle}`, face);
  }
  return Array.from(map.values());
}
function mergeFontFamilies(existing = [], incoming = []) {
  const map = /* @__PURE__ */ new Map();
  for (const font2 of existing) {
    map.set(font2.slug, { ...font2 });
  }
  for (const font2 of incoming) {
    if (map.has(font2.slug)) {
      const { fontFace: incomingFontFaces, ...restIncoming } = font2;
      const existingFont = map.get(font2.slug);
      const mergedFontFaces = mergeFontFaces(
        existingFont.fontFace,
        incomingFontFaces
      );
      map.set(font2.slug, {
        ...restIncoming,
        fontFace: mergedFontFaces
      });
    } else {
      map.set(font2.slug, { ...font2 });
    }
  }
  return Array.from(map.values());
}
async function loadFontFaceInBrowser(fontFace, source, addTo = "all") {
  let dataSource;
  if (typeof source === "string") {
    dataSource = `url(${source})`;
  } else if (source instanceof File) {
    dataSource = await source.arrayBuffer();
  } else {
    return;
  }
  const newFont = new window.FontFace(
    formatFontFaceName(fontFace.fontFamily),
    dataSource,
    {
      style: fontFace.fontStyle,
      weight: String(fontFace.fontWeight)
    }
  );
  const loadedFace = await newFont.load();
  if (addTo === "document" || addTo === "all") {
    document.fonts.add(loadedFace);
  }
  if (addTo === "iframe" || addTo === "all") {
    const iframe = document.querySelector(
      'iframe[name="editor-canvas"]'
    );
    if (iframe?.contentDocument) {
      iframe.contentDocument.fonts.add(loadedFace);
    }
  }
}
function unloadFontFaceInBrowser(fontFace, removeFrom = "all") {
  const unloadFontFace = (fonts) => {
    fonts.forEach((f2) => {
      if (f2.family === formatFontFaceName(fontFace?.fontFamily) && f2.weight === fontFace?.fontWeight && f2.style === fontFace?.fontStyle) {
        fonts.delete(f2);
      }
    });
  };
  if (removeFrom === "document" || removeFrom === "all") {
    unloadFontFace(document.fonts);
  }
  if (removeFrom === "iframe" || removeFrom === "all") {
    const iframe = document.querySelector(
      'iframe[name="editor-canvas"]'
    );
    if (iframe?.contentDocument) {
      unloadFontFace(iframe.contentDocument.fonts);
    }
  }
}
function getDisplaySrcFromFontFace(input) {
  if (!input) {
    return;
  }
  let src;
  if (Array.isArray(input)) {
    src = input[0];
  } else {
    src = input;
  }
  if (src.startsWith("file:.")) {
    return;
  }
  if (!isUrlEncoded(src)) {
    src = encodeURI(src);
  }
  return src;
}
function makeFontFamilyFormData(fontFamily) {
  const formData = new FormData();
  const { fontFace, category, ...familyWithValidParameters } = fontFamily;
  const fontFamilySettings = {
    ...familyWithValidParameters,
    slug: kebabCase(fontFamily.slug)
  };
  formData.append(
    "font_family_settings",
    JSON.stringify(fontFamilySettings)
  );
  return formData;
}
function makeFontFacesFormData(font2) {
  const fontFacesFormData = (font2?.fontFace ?? []).map(
    (item, faceIndex) => {
      const face = { ...item };
      const formData = new FormData();
      if (face.file) {
        const files = Array.isArray(face.file) ? face.file : [face.file];
        const src = [];
        files.forEach((file, key) => {
          const fileId = `file-${faceIndex}-${key}`;
          formData.append(fileId, file, file.name);
          src.push(fileId);
        });
        face.src = src.length === 1 ? src[0] : src;
        delete face.file;
        formData.append("font_face_settings", JSON.stringify(face));
      } else {
        formData.append("font_face_settings", JSON.stringify(face));
      }
      return formData;
    }
  );
  return fontFacesFormData;
}
async function batchInstallFontFaces(fontFamilyId, fontFacesData, registry) {
  const responses = [];
  for (const faceData of fontFacesData) {
    try {
      const response = await fetchInstallFontFace(
        fontFamilyId,
        faceData,
        registry
      );
      responses.push({ status: "fulfilled", value: response });
    } catch (error) {
      responses.push({ status: "rejected", reason: error });
    }
  }
  const results = {
    errors: [],
    successes: []
  };
  responses.forEach((result, index) => {
    if (result.status === "fulfilled" && result.value) {
      const response = result.value;
      results.successes.push(response);
    } else if (result.reason) {
      results.errors.push({
        data: fontFacesData[index],
        message: result.reason.message
      });
    }
  });
  return results;
}
async function downloadFontFaceAssets(src) {
  src = Array.isArray(src) ? src : [src];
  const files = await Promise.all(
    src.map(async (url) => {
      return fetch(new Request(url)).then((response) => {
        if (!response.ok) {
          throw new Error(
            `Error downloading font face asset from ${url}. Server responded with status: ${response.status}`
          );
        }
        return response.blob();
      }).then((blob) => {
        const filename = url.split("/").pop();
        const file = new File([blob], filename, {
          type: blob.type
        });
        return file;
      });
    })
  );
  return files.length === 1 ? files[0] : files;
}
function checkFontFaceInstalled(fontFace, collection) {
  return -1 !== collection.findIndex((collectionFontFace) => {
    return collectionFontFace.fontWeight === fontFace.fontWeight && collectionFontFace.fontStyle === fontFace.fontStyle;
  });
}

// packages/global-styles-ui/build-module/font-library/utils/set-immutably.js
function setImmutably2(object, path, value) {
  path = Array.isArray(path) ? [...path] : [path];
  object = Array.isArray(object) ? [...object] : { ...object };
  const leaf = path.pop();
  let prev = object;
  for (const key of path) {
    const lvl = prev[key];
    prev = prev[key] = Array.isArray(lvl) ? [...lvl] : { ...lvl };
  }
  prev[leaf] = value;
  return object;
}

// packages/global-styles-ui/build-module/font-library/utils/toggleFont.js
function toggleFont(font2, face, initialfonts = []) {
  const isFontActivated = (f2) => f2.slug === font2.slug;
  const getActivatedFont = (fonts) => fonts.find(isFontActivated);
  const toggleEntireFontFamily = (activatedFont2) => {
    if (!activatedFont2) {
      return [...initialfonts, font2];
    }
    return initialfonts.filter(
      (f2) => !isFontActivated(f2)
    );
  };
  const toggleFontVariant = (activatedFont2) => {
    const isFaceActivated = (f2) => f2.fontWeight === face.fontWeight && f2.fontStyle === face.fontStyle;
    if (!activatedFont2) {
      return [...initialfonts, { ...font2, fontFace: [face] }];
    }
    let newFontFaces = activatedFont2.fontFace || [];
    if (newFontFaces.find(isFaceActivated)) {
      newFontFaces = newFontFaces.filter(
        (f2) => !isFaceActivated(f2)
      );
    } else {
      newFontFaces = [...newFontFaces, face];
    }
    if (newFontFaces.length === 0) {
      return initialfonts.filter(
        (f2) => !isFontActivated(f2)
      );
    }
    return initialfonts.map(
      (f2) => isFontActivated(f2) ? { ...f2, fontFace: newFontFaces } : f2
    );
  };
  const activatedFont = getActivatedFont(initialfonts);
  if (!face) {
    return toggleEntireFontFamily(activatedFont);
  }
  return toggleFontVariant(activatedFont);
}

// packages/global-styles-ui/build-module/font-library/context.js
var import_jsx_runtime29 = __toESM(require_jsx_runtime());
var FontLibraryContext = (0, import_element12.createContext)(
  {}
);
FontLibraryContext.displayName = "FontLibraryContext";
function FontLibraryProvider({ children }) {
  const registry = (0, import_data6.useRegistry)();
  const { saveEntityRecord, deleteEntityRecord } = (0, import_data6.useDispatch)(import_core_data5.store);
  const { globalStylesId } = (0, import_data6.useSelect)((select) => {
    const { __experimentalGetCurrentGlobalStylesId } = select(import_core_data5.store);
    return { globalStylesId: __experimentalGetCurrentGlobalStylesId() };
  }, []);
  const globalStyles = (0, import_core_data5.useEntityRecord)(
    "root",
    "globalStyles",
    globalStylesId
  );
  const [isInstalling, setIsInstalling] = (0, import_element12.useState)(false);
  const { records: libraryPosts = [], isResolving: isResolvingLibrary } = (0, import_core_data5.useEntityRecords)(
    "postType",
    "wp_font_family",
    {
      _embed: true
    }
  );
  const libraryFonts = (libraryPosts || []).map((fontFamilyPost) => {
    return {
      id: fontFamilyPost.id,
      ...fontFamilyPost.font_family_settings || {},
      fontFace: fontFamilyPost?._embedded?.font_faces?.map(
        (face) => face.font_face_settings
      ) || []
    };
  }) || [];
  const [fontFamilies, setFontFamilies] = useSetting("typography.fontFamilies");
  const saveFontFamilies = async (fonts) => {
    if (!globalStyles.record) {
      return;
    }
    const updatedGlobalStyles = globalStyles.record;
    const finalGlobalStyles = setImmutably2(
      updatedGlobalStyles ?? {},
      ["settings", "typography", "fontFamilies"],
      fonts
    );
    await saveEntityRecord("root", "globalStyles", finalGlobalStyles);
  };
  const [modalTabOpen, setModalTabOpen] = (0, import_element12.useState)("");
  const [libraryFontSelected, setLibraryFontSelected] = (0, import_element12.useState)(void 0);
  const themeFonts = fontFamilies?.theme ? fontFamilies.theme.map((f2) => setUIValuesNeeded(f2, { source: "theme" })).sort((a2, b2) => a2.name.localeCompare(b2.name)) : [];
  const customFonts = fontFamilies?.custom ? fontFamilies.custom.map((f2) => setUIValuesNeeded(f2, { source: "custom" })).sort((a2, b2) => a2.name.localeCompare(b2.name)) : [];
  const baseCustomFonts = libraryFonts ? libraryFonts.map((f2) => setUIValuesNeeded(f2, { source: "custom" })).sort((a2, b2) => a2.name.localeCompare(b2.name)) : [];
  (0, import_element12.useEffect)(() => {
    if (!modalTabOpen) {
      setLibraryFontSelected(void 0);
    }
  }, [modalTabOpen]);
  const handleSetLibraryFontSelected = (font2) => {
    if (!font2) {
      setLibraryFontSelected(void 0);
      return;
    }
    const fonts = font2.source === "theme" ? themeFonts : baseCustomFonts;
    const fontSelected = fonts.find((f2) => f2.slug === font2.slug);
    setLibraryFontSelected({
      ...fontSelected || font2,
      source: font2.source
    });
  };
  const [loadedFontUrls] = (0, import_element12.useState)(/* @__PURE__ */ new Set());
  const getAvailableFontsOutline = (availableFontFamilies) => {
    const outline = availableFontFamilies.reduce(
      (acc, font2) => {
        const availableFontFaces = font2?.fontFace && font2.fontFace?.length > 0 ? font2?.fontFace.map(
          (face) => `${face.fontStyle ?? ""}${face.fontWeight ?? ""}`
        ) : ["normal400"];
        acc[font2.slug] = availableFontFaces;
        return acc;
      },
      {}
    );
    return outline;
  };
  const getActivatedFontsOutline = (source) => {
    switch (source) {
      case "theme":
        return getAvailableFontsOutline(themeFonts);
      case "custom":
      default:
        return getAvailableFontsOutline(customFonts);
    }
  };
  const isFontActivated = (slug, style, weight, source) => {
    if (!style && !weight) {
      return !!getActivatedFontsOutline(source)[slug];
    }
    return !!getActivatedFontsOutline(source)[slug]?.includes(
      (style ?? "") + (weight ?? "")
    );
  };
  const getFontFacesActivated = (slug, source) => {
    return getActivatedFontsOutline(source)[slug] || [];
  };
  async function installFonts(fontFamiliesToInstall) {
    setIsInstalling(true);
    try {
      const fontFamiliesToActivate = [];
      let installationErrors = [];
      for (const fontFamilyToInstall of fontFamiliesToInstall) {
        let isANewFontFamily = false;
        const fontFamilyRecords = await (0, import_data6.resolveSelect)(
          import_core_data5.store
        ).getEntityRecords("postType", "wp_font_family", {
          slug: fontFamilyToInstall.slug,
          per_page: 1,
          _embed: true
        });
        const fontFamilyPost = fontFamilyRecords && fontFamilyRecords.length > 0 ? fontFamilyRecords[0] : null;
        let installedFontFamily = fontFamilyPost ? {
          id: fontFamilyPost.id,
          ...fontFamilyPost.font_family_settings,
          fontFace: (fontFamilyPost?._embedded?.font_faces ?? []).map(
            (face) => face.font_face_settings
          ) || []
        } : null;
        if (!installedFontFamily) {
          isANewFontFamily = true;
          installedFontFamily = await fetchInstallFontFamily(
            makeFontFamilyFormData(fontFamilyToInstall),
            registry
          );
        }
        const alreadyInstalledFontFaces = installedFontFamily.fontFace && fontFamilyToInstall.fontFace ? installedFontFamily.fontFace.filter(
          (fontFaceToInstall) => fontFaceToInstall && fontFamilyToInstall.fontFace && checkFontFaceInstalled(
            fontFaceToInstall,
            fontFamilyToInstall.fontFace
          )
        ) : [];
        if (installedFontFamily.fontFace && fontFamilyToInstall.fontFace) {
          fontFamilyToInstall.fontFace = fontFamilyToInstall.fontFace.filter(
            (fontFaceToInstall) => !checkFontFaceInstalled(
              fontFaceToInstall,
              installedFontFamily.fontFace
            )
          );
        }
        let successfullyInstalledFontFaces = [];
        let unsuccessfullyInstalledFontFaces = [];
        if (fontFamilyToInstall?.fontFace?.length ?? 0 > 0) {
          const response = await batchInstallFontFaces(
            installedFontFamily.id,
            makeFontFacesFormData(
              fontFamilyToInstall
            ),
            registry
          );
          successfullyInstalledFontFaces = response?.successes;
          unsuccessfullyInstalledFontFaces = response?.errors;
        }
        if (successfullyInstalledFontFaces?.length > 0 || alreadyInstalledFontFaces?.length > 0) {
          installedFontFamily.fontFace = [
            ...successfullyInstalledFontFaces
          ];
          fontFamiliesToActivate.push(installedFontFamily);
        }
        if (installedFontFamily && !fontFamilyToInstall?.fontFace?.length) {
          fontFamiliesToActivate.push(installedFontFamily);
        }
        if (isANewFontFamily && (fontFamilyToInstall?.fontFace?.length ?? 0) > 0 && successfullyInstalledFontFaces?.length === 0) {
          await deleteEntityRecord(
            "postType",
            "wp_font_family",
            installedFontFamily.id,
            { force: true }
          );
        }
        installationErrors = installationErrors.concat(
          unsuccessfullyInstalledFontFaces
        );
      }
      const installationErrorMessages = installationErrors.reduce(
        (unique, item) => unique.includes(item.message) ? unique : [...unique, item.message],
        []
      );
      if (fontFamiliesToActivate.length > 0) {
        const activeFonts = activateCustomFontFamilies(
          fontFamiliesToActivate
        );
        await saveFontFamilies(activeFonts);
      }
      if (installationErrorMessages.length > 0) {
        const installError = new Error((0, import_i18n11.__)("There was an error installing fonts."));
        installError.installationErrors = installationErrorMessages;
        throw installError;
      }
    } finally {
      setIsInstalling(false);
    }
  }
  async function uninstallFontFamily(fontFamilyToUninstall) {
    if (!fontFamilyToUninstall?.id) {
      throw new Error((0, import_i18n11.__)("Font family to uninstall is not defined."));
    }
    try {
      await deleteEntityRecord(
        "postType",
        "wp_font_family",
        fontFamilyToUninstall.id,
        { force: true }
      );
      const activeFonts = deactivateFontFamily(fontFamilyToUninstall);
      await saveFontFamilies(activeFonts);
      return { deleted: true };
    } catch (error) {
      console.error(
        `There was an error uninstalling the font family:`,
        error
      );
      throw error;
    }
  }
  const deactivateFontFamily = (font2) => {
    const initialCustomFonts = fontFamilies?.[font2.source ?? ""] ?? [];
    const newCustomFonts = initialCustomFonts.filter(
      (f2) => f2.slug !== font2.slug
    );
    const activeFonts = {
      ...fontFamilies,
      [font2.source ?? ""]: newCustomFonts
    };
    setFontFamilies(activeFonts);
    if (font2.fontFace) {
      font2.fontFace.forEach((face) => {
        unloadFontFaceInBrowser(face, "all");
      });
    }
    return activeFonts;
  };
  const activateCustomFontFamilies = (fontsToAdd) => {
    const fontsToActivate = cleanFontsForSave(fontsToAdd);
    const activeFonts = {
      ...fontFamilies,
      // Merge the existing custom fonts with the new fonts.
      custom: mergeFontFamilies(fontFamilies?.custom, fontsToActivate)
    };
    setFontFamilies(activeFonts);
    loadFontsInBrowser(fontsToActivate);
    return activeFonts;
  };
  const cleanFontsForSave = (fonts) => {
    return fonts.map(({ id: _familyDbId, fontFace, ...font2 }) => ({
      ...font2,
      ...fontFace && fontFace.length > 0 ? {
        fontFace: fontFace.map(
          ({ id: _faceDbId, ...face }) => face
        )
      } : {}
    }));
  };
  const loadFontsInBrowser = (fonts) => {
    fonts.forEach((font2) => {
      if (font2.fontFace) {
        font2.fontFace.forEach((face) => {
          const displaySrc = getDisplaySrcFromFontFace(
            face?.src ?? ""
          );
          if (displaySrc) {
            loadFontFaceInBrowser(face, displaySrc, "all");
          }
        });
      }
    });
  };
  const toggleActivateFont = (font2, face) => {
    const initialFonts = fontFamilies?.[font2.source ?? ""] ?? [];
    const newFonts = toggleFont(font2, face, initialFonts);
    setFontFamilies({
      ...fontFamilies,
      [font2.source ?? ""]: newFonts
    });
    const isFaceActivated = isFontActivated(
      font2.slug,
      face?.fontStyle ?? "",
      face?.fontWeight ?? "",
      font2.source ?? "custom"
    );
    if (face && isFaceActivated) {
      unloadFontFaceInBrowser(face, "all");
    } else {
      const displaySrc = getDisplaySrcFromFontFace(face?.src ?? "");
      if (face && displaySrc) {
        loadFontFaceInBrowser(face, displaySrc, "all");
      }
    }
  };
  const loadFontFaceAsset = async (fontFace) => {
    if (!fontFace.src) {
      return;
    }
    const src = getDisplaySrcFromFontFace(fontFace.src);
    if (!src || loadedFontUrls.has(src)) {
      return;
    }
    loadFontFaceInBrowser(fontFace, src, "document");
    loadedFontUrls.add(src);
  };
  return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
    FontLibraryContext.Provider,
    {
      value: {
        libraryFontSelected,
        handleSetLibraryFontSelected,
        fontFamilies: fontFamilies ?? {},
        baseCustomFonts,
        isFontActivated,
        getFontFacesActivated,
        loadFontFaceAsset,
        installFonts,
        uninstallFontFamily,
        toggleActivateFont,
        getAvailableFontsOutline,
        modalTabOpen,
        setModalTabOpen,
        saveFontFamilies,
        isResolvingLibrary,
        isInstalling
      },
      children
    }
  );
}
var context_default = FontLibraryProvider;

// packages/global-styles-ui/build-module/font-library/modal.js
var import_i18n17 = __toESM(require_i18n());
var import_components31 = __toESM(require_components());
var import_core_data8 = __toESM(require_core_data());
var import_data8 = __toESM(require_data());

// packages/global-styles-ui/build-module/font-library/installed-fonts.js
var import_components25 = __toESM(require_components());
var import_core_data6 = __toESM(require_core_data());
var import_data7 = __toESM(require_data());
var import_element15 = __toESM(require_element());
var import_i18n13 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/font-library/font-card.js
var import_i18n12 = __toESM(require_i18n());
var import_components23 = __toESM(require_components());

// packages/global-styles-ui/build-module/font-library/font-demo.js
var import_components22 = __toESM(require_components());
var import_element13 = __toESM(require_element());
var import_jsx_runtime30 = __toESM(require_jsx_runtime());
function getPreviewUrl(fontFace) {
  if (fontFace.preview) {
    return fontFace.preview;
  }
  if (fontFace.src) {
    return Array.isArray(fontFace.src) ? fontFace.src[0] : fontFace.src;
  }
  return void 0;
}
function getDisplayFontFace(font2) {
  if ("fontStyle" in font2 && font2.fontStyle || "fontWeight" in font2 && font2.fontWeight) {
    return font2;
  }
  if ("fontFace" in font2 && font2.fontFace && font2.fontFace.length) {
    return font2.fontFace.find(
      (face) => face.fontStyle === "normal" && face.fontWeight === "400"
    ) || font2.fontFace[0];
  }
  return {
    fontStyle: "normal",
    fontWeight: "400",
    fontFamily: font2.fontFamily
  };
}
function FontDemo({ font: font2, text }) {
  const ref = (0, import_element13.useRef)(null);
  const fontFace = getDisplayFontFace(font2);
  const style = getFamilyPreviewStyle(font2);
  text = text || ("name" in font2 ? font2.name : "");
  const customPreviewUrl = font2.preview;
  const [isIntersecting, setIsIntersecting] = (0, import_element13.useState)(false);
  const [isAssetLoaded, setIsAssetLoaded] = (0, import_element13.useState)(false);
  const { loadFontFaceAsset } = (0, import_element13.useContext)(FontLibraryContext);
  const previewUrl = customPreviewUrl ?? getPreviewUrl(fontFace);
  const isPreviewImage = previewUrl && previewUrl.match(/\.(png|jpg|jpeg|gif|svg)$/i);
  const faceStyles = getFacePreviewStyle(fontFace);
  const textDemoStyle = {
    fontSize: "18px",
    lineHeight: 1,
    opacity: isAssetLoaded ? "1" : "0",
    ...style,
    ...faceStyles
  };
  (0, import_element13.useEffect)(() => {
    const observer = new window.IntersectionObserver(([entry]) => {
      setIsIntersecting(entry.isIntersecting);
    }, {});
    if (ref.current) {
      observer.observe(ref.current);
    }
    return () => observer.disconnect();
  }, [ref]);
  (0, import_element13.useEffect)(() => {
    const loadAsset = async () => {
      if (isIntersecting) {
        if (!isPreviewImage && fontFace.src) {
          await loadFontFaceAsset(fontFace);
        }
        setIsAssetLoaded(true);
      }
    };
    loadAsset();
  }, [fontFace, isIntersecting, loadFontFaceAsset, isPreviewImage]);
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)("div", { ref, children: isPreviewImage ? /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
    "img",
    {
      src: previewUrl,
      loading: "lazy",
      alt: text,
      className: "font-library__font-variant_demo-image"
    }
  ) : /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
    import_components22.__experimentalText,
    {
      style: textDemoStyle,
      className: "font-library__font-variant_demo-text",
      children: text
    }
  ) });
}
var font_demo_default = FontDemo;

// packages/global-styles-ui/build-module/font-library/font-card.js
var import_jsx_runtime31 = __toESM(require_jsx_runtime());
function FontCard({
  font: font2,
  onClick,
  variantsText,
  navigatorPath
}) {
  const variantsCount = font2.fontFace?.length || 1;
  const style = {
    cursor: !!onClick ? "pointer" : "default"
  };
  const navigator = (0, import_components23.useNavigator)();
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
    import_components23.Button,
    {
      __next40pxDefaultSize: true,
      onClick: () => {
        onClick();
        if (navigatorPath) {
          navigator.goTo(navigatorPath);
        }
      },
      style,
      className: "font-library__font-card",
      children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(import_components23.Flex, { justify: "space-between", wrap: false, children: [
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(font_demo_default, { font: font2 }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(import_components23.Flex, { justify: "flex-end", children: [
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components23.FlexItem, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components23.__experimentalText, { className: "font-library__font-card__count", children: variantsText || (0, import_i18n12.sprintf)(
            /* translators: %d: Number of font variants. */
            (0, import_i18n12._n)(
              "%d variant",
              "%d variants",
              variantsCount
            ),
            variantsCount
          ) }) }),
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components23.FlexItem, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(icon_default, { icon: (0, import_i18n12.isRTL)() ? chevron_left_default : chevron_right_default }) })
        ] })
      ] })
    }
  );
}
var font_card_default = FontCard;

// packages/global-styles-ui/build-module/font-library/library-font-variant.js
var import_element14 = __toESM(require_element());
var import_components24 = __toESM(require_components());
var import_jsx_runtime32 = __toESM(require_jsx_runtime());
function LibraryFontVariant({
  face,
  font: font2
}) {
  const { isFontActivated, toggleActivateFont } = (0, import_element14.useContext)(FontLibraryContext);
  const isInstalled = (font2?.fontFace?.length ?? 0) > 0 ? isFontActivated(
    font2.slug,
    face.fontStyle,
    face.fontWeight,
    font2.source
  ) : isFontActivated(font2.slug, void 0, void 0, font2.source);
  const handleToggleActivation = () => {
    if ((font2?.fontFace?.length ?? 0) > 0) {
      toggleActivateFont(font2, face);
      return;
    }
    toggleActivateFont(font2);
  };
  const displayName = font2.name + " " + getFontFaceVariantName(face);
  const checkboxId = (0, import_element14.useId)();
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "font-library__font-card", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsxs)(import_components24.Flex, { justify: "flex-start", align: "center", gap: "1rem", children: [
    /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
      import_components24.CheckboxControl,
      {
        checked: isInstalled,
        onChange: handleToggleActivation,
        id: checkboxId
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("label", { htmlFor: checkboxId, children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
      font_demo_default,
      {
        font: face,
        text: displayName,
        onClick: handleToggleActivation
      }
    ) })
  ] }) });
}
var library_font_variant_default = LibraryFontVariant;

// packages/global-styles-ui/build-module/font-library/utils/sort-font-faces.js
function getNumericFontWeight(value) {
  switch (value) {
    case "normal":
      return 400;
    case "bold":
      return 700;
    case "bolder":
      return 500;
    case "lighter":
      return 300;
    default:
      return parseInt(value, 10);
  }
}
function sortFontFaces(faces) {
  return faces.sort((a2, b2) => {
    if (a2.fontStyle === "normal" && b2.fontStyle !== "normal") {
      return -1;
    }
    if (b2.fontStyle === "normal" && a2.fontStyle !== "normal") {
      return 1;
    }
    if (a2.fontStyle === b2.fontStyle) {
      return getNumericFontWeight(a2.fontWeight?.toString() ?? "normal") - getNumericFontWeight(b2.fontWeight?.toString() ?? "normal");
    }
    if (!a2.fontStyle || !b2.fontStyle) {
      return !a2.fontStyle ? 1 : -1;
    }
    return a2.fontStyle.localeCompare(b2.fontStyle);
  });
}

// packages/global-styles-ui/build-module/font-library/installed-fonts.js
var import_jsx_runtime33 = __toESM(require_jsx_runtime());
function InstalledFonts() {
  const {
    baseCustomFonts,
    libraryFontSelected,
    handleSetLibraryFontSelected,
    uninstallFontFamily,
    isResolvingLibrary,
    isInstalling,
    saveFontFamilies,
    getFontFacesActivated
  } = (0, import_element15.useContext)(FontLibraryContext);
  const [fontFamilies, setFontFamilies] = useSetting("typography.fontFamilies");
  const [isConfirmDeleteOpen, setIsConfirmDeleteOpen] = (0, import_element15.useState)(false);
  const [notice, setNotice] = (0, import_element15.useState)(null);
  const [baseFontFamilies] = useSetting("typography.fontFamilies", void 0, "base");
  const globalStylesId = (0, import_data7.useSelect)((select) => {
    const { __experimentalGetCurrentGlobalStylesId } = select(import_core_data6.store);
    return __experimentalGetCurrentGlobalStylesId();
  }, []);
  const globalStyles = (0, import_core_data6.useEntityRecord)(
    "root",
    "globalStyles",
    globalStylesId
  );
  const fontFamiliesHasChanges = !!globalStyles?.edits?.settings?.typography?.fontFamilies;
  const themeFonts = fontFamilies?.theme ? fontFamilies.theme.map((f2) => setUIValuesNeeded(f2, { source: "theme" })).sort((a2, b2) => a2.name.localeCompare(b2.name)) : [];
  const themeFontsSlugs = new Set(themeFonts.map((f2) => f2.slug));
  const baseThemeFonts = baseFontFamilies?.theme ? themeFonts.concat(
    baseFontFamilies.theme.filter((f2) => !themeFontsSlugs.has(f2.slug)).map((f2) => setUIValuesNeeded(f2, { source: "theme" })).sort((a2, b2) => a2.name.localeCompare(b2.name))
  ) : [];
  const customFontFamilyId = libraryFontSelected?.source === "custom" && libraryFontSelected?.id;
  const canUserDelete = (0, import_data7.useSelect)(
    (select) => {
      const { canUser } = select(import_core_data6.store);
      return customFontFamilyId && canUser("delete", {
        kind: "postType",
        name: "wp_font_family",
        id: customFontFamilyId
      });
    },
    [customFontFamilyId]
  );
  const shouldDisplayDeleteButton = !!libraryFontSelected && libraryFontSelected?.source !== "theme" && canUserDelete;
  const handleUninstallClick = () => {
    setIsConfirmDeleteOpen(true);
  };
  const handleUpdate = async () => {
    setNotice(null);
    try {
      await saveFontFamilies(fontFamilies);
      setNotice({
        type: "success",
        message: (0, import_i18n13.__)("Font family updated successfully.")
      });
    } catch (error) {
      setNotice({
        type: "error",
        message: (0, import_i18n13.sprintf)(
          /* translators: %s: error message */
          (0, import_i18n13.__)("There was an error updating the font family. %s"),
          error.message
        )
      });
    }
  };
  const getFontFacesToDisplay = (font2) => {
    if (!font2) {
      return [];
    }
    if (!font2.fontFace || !font2.fontFace.length) {
      return [
        {
          fontFamily: font2.fontFamily,
          fontStyle: "normal",
          fontWeight: "400"
        }
      ];
    }
    return sortFontFaces(font2.fontFace);
  };
  const getFontCardVariantsText = (font2) => {
    const variantsInstalled = font2?.fontFace && (font2?.fontFace?.length ?? 0) > 0 ? font2.fontFace.length : 1;
    const variantsActive = getFontFacesActivated(
      font2.slug,
      font2.source
    ).length;
    return (0, import_i18n13.sprintf)(
      /* translators: 1: Active font variants, 2: Total font variants. */
      (0, import_i18n13.__)("%1$d/%2$d variants active"),
      variantsActive,
      variantsInstalled
    );
  };
  (0, import_element15.useEffect)(() => {
    handleSetLibraryFontSelected(libraryFontSelected);
  }, []);
  const activeFontsCount = libraryFontSelected ? getFontFacesActivated(
    libraryFontSelected.slug,
    libraryFontSelected.source
  ).length : 0;
  const selectedFontsCount = libraryFontSelected?.fontFace?.length ?? (libraryFontSelected?.fontFamily ? 1 : 0);
  const isIndeterminate = activeFontsCount > 0 && activeFontsCount !== selectedFontsCount;
  const isSelectAllChecked = activeFontsCount === selectedFontsCount;
  const toggleSelectAll = () => {
    if (!libraryFontSelected || !libraryFontSelected?.source) {
      return;
    }
    const initialFonts = fontFamilies?.[libraryFontSelected.source]?.filter(
      (f2) => f2.slug !== libraryFontSelected.slug
    ) ?? [];
    const newFonts = isSelectAllChecked ? initialFonts : [...initialFonts, libraryFontSelected];
    setFontFamilies({
      ...fontFamilies,
      [libraryFontSelected.source]: newFonts
    });
    if (libraryFontSelected.fontFace) {
      libraryFontSelected.fontFace.forEach((face) => {
        if (isSelectAllChecked) {
          unloadFontFaceInBrowser(face, "all");
        } else {
          const displaySrc = getDisplaySrcFromFontFace(
            face?.src ?? ""
          );
          if (displaySrc) {
            loadFontFaceInBrowser(face, displaySrc, "all");
          }
        }
      });
    }
  };
  const hasFonts = baseThemeFonts.length > 0 || baseCustomFonts.length > 0;
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)("div", { className: "font-library__tabpanel-layout", children: [
    isResolvingLibrary && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { className: "font-library__loading", children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.ProgressBar, {}) }),
    !isResolvingLibrary && /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_jsx_runtime33.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(
        import_components25.Navigator,
        {
          initialPath: libraryFontSelected ? "/fontFamily" : "/",
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.Navigator.Screen, { path: "/", children: /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.__experimentalVStack, { spacing: "8", children: [
              notice && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                import_components25.Notice,
                {
                  status: notice.type,
                  onRemove: () => setNotice(null),
                  children: notice.message
                }
              ),
              !hasFonts && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalText, { as: "p", children: (0, import_i18n13.__)("No fonts installed.") }),
              baseThemeFonts.length > 0 && /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.__experimentalVStack, { children: [
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("h2", {
                  className: "font-library__fonts-title",
                  /* translators: Heading for a list of fonts provided by the theme. */
                  children: (0, import_i18n13._x)("Theme", "font source")
                }),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  "ul",
                  {
                    role: "list",
                    className: "font-library__fonts-list",
                    children: baseThemeFonts.map((font2) => /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                      "li",
                      {
                        className: "font-library__fonts-list-item",
                        children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                          font_card_default,
                          {
                            font: font2,
                            navigatorPath: "/fontFamily",
                            variantsText: getFontCardVariantsText(
                              font2
                            ),
                            onClick: () => {
                              setNotice(null);
                              handleSetLibraryFontSelected(
                                font2
                              );
                            }
                          }
                        )
                      },
                      font2.slug
                    ))
                  }
                )
              ] }),
              baseCustomFonts.length > 0 && /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.__experimentalVStack, { children: [
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("h2", {
                  className: "font-library__fonts-title",
                  /* translators: Heading for a list of fonts installed by the user. */
                  children: (0, import_i18n13._x)("Custom", "font source")
                }),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  "ul",
                  {
                    role: "list",
                    className: "font-library__fonts-list",
                    children: baseCustomFonts.map((font2) => /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                      "li",
                      {
                        className: "font-library__fonts-list-item",
                        children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                          font_card_default,
                          {
                            font: font2,
                            navigatorPath: "/fontFamily",
                            variantsText: getFontCardVariantsText(
                              font2
                            ),
                            onClick: () => {
                              setNotice(null);
                              handleSetLibraryFontSelected(
                                font2
                              );
                            }
                          }
                        )
                      },
                      font2.slug
                    ))
                  }
                )
              ] })
            ] }) }),
            /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.Navigator.Screen, { path: "/fontFamily", children: [
              libraryFontSelected && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                ConfirmDeleteDialog,
                {
                  font: libraryFontSelected,
                  isOpen: isConfirmDeleteOpen,
                  setIsOpen: setIsConfirmDeleteOpen,
                  setNotice,
                  uninstallFontFamily,
                  handleSetLibraryFontSelected
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.Flex, { justify: "flex-start", children: [
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  import_components25.Navigator.BackButton,
                  {
                    icon: (0, import_i18n13.isRTL)() ? chevron_right_default : chevron_left_default,
                    size: "small",
                    onClick: () => {
                      handleSetLibraryFontSelected(
                        void 0
                      );
                      setNotice(null);
                    },
                    label: (0, import_i18n13.__)("Back")
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  import_components25.__experimentalHeading,
                  {
                    level: 2,
                    size: 13,
                    className: "global-styles-ui-header",
                    children: libraryFontSelected?.name
                  }
                )
              ] }),
              notice && /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_jsx_runtime33.Fragment, { children: [
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalSpacer, { margin: 1 }),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  import_components25.Notice,
                  {
                    status: notice.type,
                    onRemove: () => setNotice(null),
                    children: notice.message
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalSpacer, { margin: 1 })
              ] }),
              /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalSpacer, { margin: 4 }),
              /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalText, { children: (0, import_i18n13.__)(
                "Choose font variants. Keep in mind that too many variants could make your site slower."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalSpacer, { margin: 4 }),
              /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.__experimentalVStack, { spacing: 0, children: [
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  import_components25.CheckboxControl,
                  {
                    className: "font-library__select-all",
                    label: (0, import_i18n13.__)("Select all"),
                    checked: isSelectAllChecked,
                    onChange: toggleSelectAll,
                    indeterminate: isIndeterminate
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.__experimentalSpacer, { margin: 8 }),
                /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                  "ul",
                  {
                    role: "list",
                    className: "font-library__fonts-list",
                    children: libraryFontSelected && getFontFacesToDisplay(
                      libraryFontSelected
                    ).map((face, i2) => /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                      "li",
                      {
                        className: "font-library__fonts-list-item",
                        children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
                          library_font_variant_default,
                          {
                            font: libraryFontSelected,
                            face
                          },
                          `face${i2}`
                        )
                      },
                      `face${i2}`
                    ))
                  }
                )
              ] })
            ] })
          ]
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_components25.__experimentalHStack, { justify: "flex-end", className: "font-library__footer", children: [
        isInstalling && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_components25.ProgressBar, {}),
        shouldDisplayDeleteButton && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
          import_components25.Button,
          {
            __next40pxDefaultSize: true,
            isDestructive: true,
            variant: "tertiary",
            onClick: handleUninstallClick,
            children: (0, import_i18n13.__)("Delete")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
          import_components25.Button,
          {
            __next40pxDefaultSize: true,
            variant: "primary",
            onClick: handleUpdate,
            disabled: !fontFamiliesHasChanges,
            accessibleWhenDisabled: true,
            children: (0, import_i18n13.__)("Update")
          }
        )
      ] })
    ] })
  ] });
}
function ConfirmDeleteDialog({
  font: font2,
  isOpen,
  setIsOpen,
  setNotice,
  uninstallFontFamily,
  handleSetLibraryFontSelected
}) {
  const navigator = (0, import_components25.useNavigator)();
  const handleConfirmUninstall = async () => {
    setNotice(null);
    setIsOpen(false);
    try {
      await uninstallFontFamily(font2);
      navigator.goBack();
      handleSetLibraryFontSelected(void 0);
      setNotice({
        type: "success",
        message: (0, import_i18n13.__)("Font family uninstalled successfully.")
      });
    } catch (error) {
      setNotice({
        type: "error",
        message: (0, import_i18n13.__)("There was an error uninstalling the font family.") + error.message
      });
    }
  };
  const handleCancelUninstall = () => {
    setIsOpen(false);
  };
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
    import_components25.__experimentalConfirmDialog,
    {
      isOpen,
      cancelButtonText: (0, import_i18n13.__)("Cancel"),
      confirmButtonText: (0, import_i18n13.__)("Delete"),
      onCancel: handleCancelUninstall,
      onConfirm: handleConfirmUninstall,
      size: "medium",
      children: font2 && (0, import_i18n13.sprintf)(
        /* translators: %s: Name of the font. */
        (0, import_i18n13.__)(
          'Are you sure you want to delete "%s" font and all its variants and assets?'
        ),
        font2.name
      )
    }
  );
}
var installed_fonts_default = InstalledFonts;

// packages/global-styles-ui/build-module/font-library/font-collection.js
var import_element17 = __toESM(require_element());
var import_components28 = __toESM(require_components());
var import_compose3 = __toESM(require_compose());
var import_i18n15 = __toESM(require_i18n());
var import_core_data7 = __toESM(require_core_data());

// packages/global-styles-ui/build-module/font-library/utils/filter-fonts.js
function filterFonts(fonts, filters) {
  const { category, search } = filters;
  let filteredFonts = fonts || [];
  if (category && category !== "all") {
    filteredFonts = filteredFonts.filter(
      (font2) => font2.categories && font2.categories.indexOf(category) !== -1
    );
  }
  if (search) {
    filteredFonts = filteredFonts.filter(
      (font2) => font2.font_family_settings && font2.font_family_settings.name.toLowerCase().includes(search.toLowerCase())
    );
  }
  return filteredFonts;
}

// packages/global-styles-ui/build-module/font-library/utils/fonts-outline.js
function getFontsOutline(fonts) {
  return fonts.reduce(
    (acc, font2) => ({
      ...acc,
      [font2.slug]: (font2?.fontFace || []).reduce(
        (faces, face) => ({
          ...faces,
          [`${face.fontStyle}-${face.fontWeight}`]: true
        }),
        {}
      )
    }),
    {}
  );
}
function isFontFontFaceInOutline(slug, face, outline) {
  if (!face) {
    return !!outline[slug];
  }
  return !!outline[slug]?.[`${face.fontStyle}-${face.fontWeight}`];
}

// packages/global-styles-ui/build-module/font-library/google-fonts-confirm-dialog.js
var import_i18n14 = __toESM(require_i18n());
var import_components26 = __toESM(require_components());
var import_jsx_runtime34 = __toESM(require_jsx_runtime());
function GoogleFontsConfirmDialog() {
  const handleConfirm = () => {
    window.localStorage.setItem(
      "wp-font-library-google-fonts-permission",
      "true"
    );
    window.dispatchEvent(new Event("storage"));
  };
  return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)("div", { className: "font-library__google-fonts-confirm", children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.Card, { children: /* @__PURE__ */ (0, import_jsx_runtime34.jsxs)(import_components26.CardBody, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalHeading, { level: 2, children: (0, import_i18n14.__)("Connect to Google Fonts") }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalSpacer, { margin: 6 }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalText, { as: "p", children: (0, import_i18n14.__)(
      "To install fonts from Google you must give permission to connect directly to Google servers. The fonts you install will be downloaded from Google and stored on your site. Your site will then use these locally-hosted fonts."
    ) }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalSpacer, { margin: 3 }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalText, { as: "p", children: (0, import_i18n14.__)(
      "You can alternatively upload files directly on the Upload tab."
    ) }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_components26.__experimentalSpacer, { margin: 6 }),
    /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
      import_components26.Button,
      {
        __next40pxDefaultSize: true,
        variant: "primary",
        onClick: handleConfirm,
        children: (0, import_i18n14.__)("Allow access to Google Fonts")
      }
    )
  ] }) }) });
}
var google_fonts_confirm_dialog_default = GoogleFontsConfirmDialog;

// packages/global-styles-ui/build-module/font-library/collection-font-variant.js
var import_element16 = __toESM(require_element());
var import_components27 = __toESM(require_components());
var import_jsx_runtime35 = __toESM(require_jsx_runtime());
function CollectionFontVariant({
  face,
  font: font2,
  handleToggleVariant,
  selected
}) {
  const handleToggleActivation = () => {
    if (font2?.fontFace) {
      handleToggleVariant(font2, face);
      return;
    }
    handleToggleVariant(font2);
  };
  const displayName = font2.name + " " + getFontFaceVariantName(face);
  const checkboxId = (0, import_element16.useId)();
  return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("div", { className: "font-library__font-card", children: /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(import_components27.Flex, { justify: "flex-start", align: "center", gap: "1rem", children: [
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      import_components27.CheckboxControl,
      {
        checked: selected,
        onChange: handleToggleActivation,
        id: checkboxId
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("label", { htmlFor: checkboxId, children: /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      font_demo_default,
      {
        font: face,
        text: displayName,
        onClick: handleToggleActivation
      }
    ) })
  ] }) });
}
var collection_font_variant_default = CollectionFontVariant;

// packages/global-styles-ui/build-module/font-library/font-collection.js
var import_jsx_runtime36 = __toESM(require_jsx_runtime());
var DEFAULT_CATEGORY = {
  slug: "all",
  name: (0, import_i18n15._x)("All", "font categories")
};
var LOCAL_STORAGE_ITEM = "wp-font-library-google-fonts-permission";
var MIN_WINDOW_HEIGHT = 500;
function FontCollection({ slug }) {
  const requiresPermission = slug === "google-fonts";
  const getGoogleFontsPermissionFromStorage = () => {
    return window.localStorage.getItem(LOCAL_STORAGE_ITEM) === "true";
  };
  const [selectedFont, setSelectedFont] = (0, import_element17.useState)(
    null
  );
  const [notice, setNotice] = (0, import_element17.useState)(null);
  const [fontsToInstall, setFontsToInstall] = (0, import_element17.useState)(
    []
  );
  const [page, setPage] = (0, import_element17.useState)(1);
  const [filters, setFilters] = (0, import_element17.useState)({});
  const [renderConfirmDialog, setRenderConfirmDialog] = (0, import_element17.useState)(
    requiresPermission && !getGoogleFontsPermissionFromStorage()
  );
  const { installFonts, isInstalling } = (0, import_element17.useContext)(FontLibraryContext);
  const { record: selectedCollection, isResolving: isLoading } = (0, import_core_data7.useEntityRecord)("root", "fontCollection", slug);
  (0, import_element17.useEffect)(() => {
    const handleStorage = () => {
      setRenderConfirmDialog(
        requiresPermission && !getGoogleFontsPermissionFromStorage()
      );
    };
    handleStorage();
    window.addEventListener("storage", handleStorage);
    return () => window.removeEventListener("storage", handleStorage);
  }, [slug, requiresPermission]);
  const revokeAccess = () => {
    window.localStorage.setItem(LOCAL_STORAGE_ITEM, "false");
    window.dispatchEvent(new Event("storage"));
  };
  (0, import_element17.useEffect)(() => {
    setSelectedFont(null);
  }, [slug]);
  (0, import_element17.useEffect)(() => {
    setFontsToInstall([]);
  }, [selectedFont]);
  const collectionFonts = (0, import_element17.useMemo)(
    () => selectedCollection?.font_families ?? [],
    [selectedCollection]
  );
  const collectionCategories = selectedCollection?.categories ?? [];
  const categories = [DEFAULT_CATEGORY, ...collectionCategories];
  const fonts = (0, import_element17.useMemo)(
    () => filterFonts(collectionFonts, filters),
    [collectionFonts, filters]
  );
  const windowHeight = Math.max(window.innerHeight, MIN_WINDOW_HEIGHT);
  const pageSize = Math.floor((windowHeight - 417) / 61);
  const totalPages = Math.ceil(fonts.length / pageSize);
  const itemsStart = (page - 1) * pageSize;
  const itemsLimit = page * pageSize;
  const items = fonts.slice(itemsStart, itemsLimit);
  const handleCategoryFilter = (category) => {
    setFilters({ ...filters, category });
    setPage(1);
  };
  const handleUpdateSearchInput = (value) => {
    setFilters({ ...filters, search: value });
    setPage(1);
  };
  const debouncedUpdateSearchInput = (0, import_compose3.debounce)(handleUpdateSearchInput, 300);
  const handleToggleVariant = (font2, face) => {
    const newFontsToInstall = toggleFont(font2, face, fontsToInstall);
    setFontsToInstall(newFontsToInstall);
  };
  const fontToInstallOutline = getFontsOutline(fontsToInstall);
  const resetFontsToInstall = () => {
    setFontsToInstall([]);
  };
  const selectFontCount = fontsToInstall.length > 0 ? fontsToInstall[0]?.fontFace?.length ?? 0 : 0;
  const isIndeterminate = selectFontCount > 0 && selectFontCount !== selectedFont?.fontFace?.length;
  const isSelectAllChecked = selectFontCount === selectedFont?.fontFace?.length;
  const toggleSelectAll = () => {
    const newFonts = [];
    if (!isSelectAllChecked && selectedFont) {
      newFonts.push(selectedFont);
    }
    setFontsToInstall(newFonts);
  };
  const handleInstall = async () => {
    setNotice(null);
    const fontFamily = fontsToInstall[0];
    try {
      if (fontFamily?.fontFace) {
        await Promise.all(
          fontFamily.fontFace.map(async (fontFace) => {
            if (fontFace.src) {
              fontFace.file = await downloadFontFaceAssets(
                fontFace.src
              );
            }
          })
        );
      }
    } catch (error) {
      setNotice({
        type: "error",
        message: (0, import_i18n15.__)(
          "Error installing the fonts, could not be downloaded."
        )
      });
      return;
    }
    try {
      await installFonts([fontFamily]);
      setNotice({
        type: "success",
        message: (0, import_i18n15.__)("Fonts were installed successfully.")
      });
    } catch (error) {
      setNotice({
        type: "error",
        message: error.message
      });
    }
    resetFontsToInstall();
  };
  const getSortedFontFaces = (fontFamily) => {
    if (!fontFamily) {
      return [];
    }
    if (!fontFamily.fontFace || !fontFamily.fontFace.length) {
      return [
        {
          fontFamily: fontFamily.fontFamily,
          fontStyle: "normal",
          fontWeight: "400"
        }
      ];
    }
    return sortFontFaces(fontFamily.fontFace);
  };
  if (renderConfirmDialog) {
    return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(google_fonts_confirm_dialog_default, {});
  }
  const ActionsComponent = () => {
    if (slug !== "google-fonts" || renderConfirmDialog || selectedFont) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
      import_components28.DropdownMenu,
      {
        icon: more_vertical_default,
        label: (0, import_i18n15.__)("Actions"),
        popoverProps: {
          position: "bottom left"
        },
        controls: [
          {
            title: (0, import_i18n15.__)("Revoke access to Google Fonts"),
            onClick: revokeAccess
          }
        ]
      }
    );
  };
  return /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)("div", { className: "font-library__tabpanel-layout", children: [
    isLoading && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)("div", { className: "font-library__loading", children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.ProgressBar, {}) }),
    !isLoading && selectedCollection && /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_jsx_runtime36.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(
        import_components28.Navigator,
        {
          initialPath: "/",
          className: "font-library__tabpanel-layout",
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.Navigator.Screen, { path: "/", children: [
              /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.__experimentalHStack, { justify: "space-between", children: [
                /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.__experimentalVStack, { children: [
                  /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalHeading, { level: 2, size: 13, children: selectedCollection.name }),
                  /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalText, { children: selectedCollection.description })
                ] }),
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(ActionsComponent, {})
              ] }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 4 }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.__experimentalHStack, { spacing: 4, justify: "space-between", children: [
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                  import_components28.SearchControl,
                  {
                    value: filters.search,
                    placeholder: (0, import_i18n15.__)("Font name\u2026"),
                    label: (0, import_i18n15.__)("Search"),
                    onChange: debouncedUpdateSearchInput,
                    hideLabelFromVision: false
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                  import_components28.SelectControl,
                  {
                    __next40pxDefaultSize: true,
                    label: (0, import_i18n15.__)("Category"),
                    value: filters.category,
                    onChange: handleCategoryFilter,
                    children: categories && categories.map((category) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                      "option",
                      {
                        value: category.slug,
                        children: category.name
                      },
                      category.slug
                    ))
                  }
                )
              ] }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 4 }),
              !!selectedCollection?.font_families?.length && !fonts.length && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalText, { children: (0, import_i18n15.__)(
                "No fonts found. Try with a different search term."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)("div", { className: "font-library__fonts-grid__main", children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                "ul",
                {
                  role: "list",
                  className: "font-library__fonts-list",
                  children: items.map((font2) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                    "li",
                    {
                      className: "font-library__fonts-list-item",
                      children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                        font_card_default,
                        {
                          font: font2.font_family_settings,
                          navigatorPath: "/fontFamily",
                          onClick: () => {
                            setSelectedFont(
                              font2.font_family_settings
                            );
                          }
                        }
                      )
                    },
                    font2.font_family_settings.slug
                  ))
                }
              ) })
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.Navigator.Screen, { path: "/fontFamily", children: [
              /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.Flex, { justify: "flex-start", children: [
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                  import_components28.Navigator.BackButton,
                  {
                    icon: (0, import_i18n15.isRTL)() ? chevron_right_default : chevron_left_default,
                    size: "small",
                    onClick: () => {
                      setSelectedFont(null);
                      setNotice(null);
                    },
                    label: (0, import_i18n15.__)("Back")
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                  import_components28.__experimentalHeading,
                  {
                    level: 2,
                    size: 13,
                    className: "global-styles-ui-header",
                    children: selectedFont?.name
                  }
                )
              ] }),
              notice && /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_jsx_runtime36.Fragment, { children: [
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 1 }),
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                  import_components28.Notice,
                  {
                    status: notice.type,
                    onRemove: () => setNotice(null),
                    children: notice.message
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 1 })
              ] }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 4 }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalText, { children: (0, import_i18n15.__)("Select font variants to install.") }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 4 }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                import_components28.CheckboxControl,
                {
                  className: "font-library__select-all",
                  label: (0, import_i18n15.__)("Select all"),
                  checked: isSelectAllChecked,
                  onChange: toggleSelectAll,
                  indeterminate: isIndeterminate
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalVStack, { spacing: 0, children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                "ul",
                {
                  role: "list",
                  className: "font-library__fonts-list",
                  children: selectedFont && getSortedFontFaces(selectedFont).map(
                    (face, i2) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                      "li",
                      {
                        className: "font-library__fonts-list-item",
                        children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                          collection_font_variant_default,
                          {
                            font: selectedFont,
                            face,
                            handleToggleVariant,
                            selected: isFontFontFaceInOutline(
                              selectedFont.slug,
                              selectedFont.fontFace ? face : null,
                              // If the font has no fontFace, we want to check if the font is in the outline
                              fontToInstallOutline
                            )
                          }
                        )
                      },
                      `face${i2}`
                    )
                  )
                }
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(import_components28.__experimentalSpacer, { margin: 16 })
            ] })
          ]
        }
      ),
      selectedFont && /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
        import_components28.Flex,
        {
          justify: "flex-end",
          className: "font-library__footer",
          children: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
            import_components28.Button,
            {
              __next40pxDefaultSize: true,
              variant: "primary",
              onClick: handleInstall,
              isBusy: isInstalling,
              disabled: fontsToInstall.length === 0 || isInstalling,
              accessibleWhenDisabled: true,
              children: (0, import_i18n15.__)("Install")
            }
          )
        }
      ),
      !selectedFont && /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(
        import_components28.__experimentalHStack,
        {
          expanded: false,
          className: "font-library__footer",
          justify: "end",
          spacing: 6,
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
              import_components28.__experimentalHStack,
              {
                justify: "flex-start",
                expanded: false,
                spacing: 1,
                className: "font-library__page-selection",
                children: (0, import_element17.createInterpolateElement)(
                  (0, import_i18n15.sprintf)(
                    // translators: 1: Current page number, 2: Total number of pages.
                    (0, import_i18n15._x)(
                      "<div>Page</div>%1$s<div>of %2$d</div>",
                      "paging"
                    ),
                    "<CurrentPage />",
                    totalPages
                  ),
                  {
                    div: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)("div", { "aria-hidden": true }),
                    CurrentPage: /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                      import_components28.SelectControl,
                      {
                        "aria-label": (0, import_i18n15.__)(
                          "Current page"
                        ),
                        value: page.toString(),
                        options: [
                          ...Array(totalPages)
                        ].map((e2, i2) => {
                          return {
                            label: (i2 + 1).toString(),
                            value: (i2 + 1).toString()
                          };
                        }),
                        onChange: (newPage) => setPage(
                          parseInt(newPage)
                        ),
                        size: "small",
                        variant: "minimal"
                      }
                    )
                  }
                )
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime36.jsxs)(import_components28.__experimentalHStack, { expanded: false, spacing: 1, children: [
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                import_components28.Button,
                {
                  onClick: () => setPage(page - 1),
                  disabled: page === 1,
                  accessibleWhenDisabled: true,
                  label: (0, import_i18n15.__)("Previous page"),
                  icon: (0, import_i18n15.isRTL)() ? next_default : previous_default,
                  showTooltip: true,
                  size: "compact",
                  tooltipPosition: "top"
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(
                import_components28.Button,
                {
                  onClick: () => setPage(page + 1),
                  disabled: page === totalPages,
                  accessibleWhenDisabled: true,
                  label: (0, import_i18n15.__)("Next page"),
                  icon: (0, import_i18n15.isRTL)() ? previous_default : next_default,
                  showTooltip: true,
                  size: "compact",
                  tooltipPosition: "top"
                }
              )
            ] })
          ]
        }
      )
    ] })
  ] });
}
var font_collection_default = FontCollection;

// packages/global-styles-ui/build-module/font-library/upload-fonts.js
var import_i18n16 = __toESM(require_i18n());
var import_components30 = __toESM(require_components());
var import_element18 = __toESM(require_element());

// packages/global-styles-ui/build-module/font-library/lib/unbrotli.js
var __getOwnPropNames2 = Object.getOwnPropertyNames;
var __require2 = /* @__PURE__ */ ((x2) => typeof __require !== "undefined" ? __require : typeof Proxy !== "undefined" ? new Proxy(x2, {
  get: (a2, b2) => (typeof __require !== "undefined" ? __require : a2)[b2]
}) : x2)(function(x2) {
  if (typeof __require !== "undefined") return __require.apply(this, arguments);
  throw Error('Dynamic require of "' + x2 + '" is not supported');
});
var __commonJS2 = (cb, mod) => function __require22() {
  return mod || (0, cb[__getOwnPropNames2(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var require_unbrotli = __commonJS2({
  "packages/global-styles-ui/src/font-library/lib/unbrotli.js"(exports, module) {
    (function(f2) {
      if (typeof exports === "object" && typeof module !== "undefined") {
        module.exports = f2();
      } else if (typeof define === "function" && define.amd) {
        define([], f2);
      } else {
        var g2;
        if (typeof window !== "undefined") {
          g2 = window;
        } else if (typeof global !== "undefined") {
          g2 = global;
        } else if (typeof self !== "undefined") {
          g2 = self;
        } else {
          g2 = this;
        }
        g2.unbrotli = f2();
      }
    })(function() {
      var define2, module2, exports2;
      return (/* @__PURE__ */ (function() {
        function r3(e2, n2, t3) {
          function o3(i22, f2) {
            if (!n2[i22]) {
              if (!e2[i22]) {
                var c2 = "function" == typeof __require2 && __require2;
                if (!f2 && c2) return c2(i22, true);
                if (u2) return u2(i22, true);
                var a2 = new Error("Cannot find module '" + i22 + "'");
                throw a2.code = "MODULE_NOT_FOUND", a2;
              }
              var p3 = n2[i22] = { exports: {} };
              e2[i22][0].call(
                p3.exports,
                function(r22) {
                  var n22 = e2[i22][1][r22];
                  return o3(n22 || r22);
                },
                p3,
                p3.exports,
                r3,
                e2,
                n2,
                t3
              );
            }
            return n2[i22].exports;
          }
          for (var u2 = "function" == typeof __require2 && __require2, i2 = 0; i2 < t3.length; i2++)
            o3(t3[i2]);
          return o3;
        }
        return r3;
      })())(
        {
          1: [
            function(require2, module3, exports3) {
              var BROTLI_READ_SIZE = 4096;
              var BROTLI_IBUF_SIZE = 2 * BROTLI_READ_SIZE + 32;
              var BROTLI_IBUF_MASK = 2 * BROTLI_READ_SIZE - 1;
              var kBitMask = new Uint32Array([
                0,
                1,
                3,
                7,
                15,
                31,
                63,
                127,
                255,
                511,
                1023,
                2047,
                4095,
                8191,
                16383,
                32767,
                65535,
                131071,
                262143,
                524287,
                1048575,
                2097151,
                4194303,
                8388607,
                16777215
              ]);
              function BrotliBitReader(input) {
                this.buf_ = new Uint8Array(BROTLI_IBUF_SIZE);
                this.input_ = input;
                this.reset();
              }
              BrotliBitReader.READ_SIZE = BROTLI_READ_SIZE;
              BrotliBitReader.IBUF_MASK = BROTLI_IBUF_MASK;
              BrotliBitReader.prototype.reset = function() {
                this.buf_ptr_ = 0;
                this.val_ = 0;
                this.pos_ = 0;
                this.bit_pos_ = 0;
                this.bit_end_pos_ = 0;
                this.eos_ = 0;
                this.readMoreInput();
                for (var i2 = 0; i2 < 4; i2++) {
                  this.val_ |= this.buf_[this.pos_] << 8 * i2;
                  ++this.pos_;
                }
                return this.bit_end_pos_ > 0;
              };
              BrotliBitReader.prototype.readMoreInput = function() {
                if (this.bit_end_pos_ > 256) {
                  return;
                } else if (this.eos_) {
                  if (this.bit_pos_ > this.bit_end_pos_)
                    throw new Error(
                      "Unexpected end of input " + this.bit_pos_ + " " + this.bit_end_pos_
                    );
                } else {
                  var dst = this.buf_ptr_;
                  var bytes_read = this.input_.read(
                    this.buf_,
                    dst,
                    BROTLI_READ_SIZE
                  );
                  if (bytes_read < 0) {
                    throw new Error("Unexpected end of input");
                  }
                  if (bytes_read < BROTLI_READ_SIZE) {
                    this.eos_ = 1;
                    for (var p3 = 0; p3 < 32; p3++)
                      this.buf_[dst + bytes_read + p3] = 0;
                  }
                  if (dst === 0) {
                    for (var p3 = 0; p3 < 32; p3++)
                      this.buf_[(BROTLI_READ_SIZE << 1) + p3] = this.buf_[p3];
                    this.buf_ptr_ = BROTLI_READ_SIZE;
                  } else {
                    this.buf_ptr_ = 0;
                  }
                  this.bit_end_pos_ += bytes_read << 3;
                }
              };
              BrotliBitReader.prototype.fillBitWindow = function() {
                while (this.bit_pos_ >= 8) {
                  this.val_ >>>= 8;
                  this.val_ |= this.buf_[this.pos_ & BROTLI_IBUF_MASK] << 24;
                  ++this.pos_;
                  this.bit_pos_ = this.bit_pos_ - 8 >>> 0;
                  this.bit_end_pos_ = this.bit_end_pos_ - 8 >>> 0;
                }
              };
              BrotliBitReader.prototype.readBits = function(n_bits) {
                if (32 - this.bit_pos_ < n_bits) {
                  this.fillBitWindow();
                }
                var val = this.val_ >>> this.bit_pos_ & kBitMask[n_bits];
                this.bit_pos_ += n_bits;
                return val;
              };
              module3.exports = BrotliBitReader;
            },
            {}
          ],
          2: [
            function(require2, module3, exports3) {
              var CONTEXT_LSB6 = 0;
              var CONTEXT_MSB6 = 1;
              var CONTEXT_UTF8 = 2;
              var CONTEXT_SIGNED = 3;
              exports3.lookup = new Uint8Array([
                /* CONTEXT_UTF8, last byte. */
                /* ASCII range. */
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                4,
                4,
                0,
                0,
                4,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                8,
                12,
                16,
                12,
                12,
                20,
                12,
                16,
                24,
                28,
                12,
                12,
                32,
                12,
                36,
                12,
                44,
                44,
                44,
                44,
                44,
                44,
                44,
                44,
                44,
                44,
                32,
                32,
                24,
                40,
                28,
                12,
                12,
                48,
                52,
                52,
                52,
                48,
                52,
                52,
                52,
                48,
                52,
                52,
                52,
                52,
                52,
                48,
                52,
                52,
                52,
                52,
                52,
                48,
                52,
                52,
                52,
                52,
                52,
                24,
                12,
                28,
                12,
                12,
                12,
                56,
                60,
                60,
                60,
                56,
                60,
                60,
                60,
                56,
                60,
                60,
                60,
                60,
                60,
                56,
                60,
                60,
                60,
                60,
                60,
                56,
                60,
                60,
                60,
                60,
                60,
                24,
                12,
                28,
                12,
                0,
                /* UTF8 continuation byte range. */
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                0,
                1,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                2,
                3,
                /* ASCII range. */
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                1,
                1,
                1,
                1,
                1,
                1,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                1,
                1,
                1,
                1,
                0,
                /* UTF8 continuation byte range. */
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                0,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                1,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                2,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                3,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                4,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                5,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                6,
                7,
                /* CONTEXT_SIGNED, last byte, same as the above values shifted by 3 bits. */
                0,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                8,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                24,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                32,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                40,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                48,
                56,
                /* CONTEXT_LSB6, last byte. */
                0,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18,
                19,
                20,
                21,
                22,
                23,
                24,
                25,
                26,
                27,
                28,
                29,
                30,
                31,
                32,
                33,
                34,
                35,
                36,
                37,
                38,
                39,
                40,
                41,
                42,
                43,
                44,
                45,
                46,
                47,
                48,
                49,
                50,
                51,
                52,
                53,
                54,
                55,
                56,
                57,
                58,
                59,
                60,
                61,
                62,
                63,
                0,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18,
                19,
                20,
                21,
                22,
                23,
                24,
                25,
                26,
                27,
                28,
                29,
                30,
                31,
                32,
                33,
                34,
                35,
                36,
                37,
                38,
                39,
                40,
                41,
                42,
                43,
                44,
                45,
                46,
                47,
                48,
                49,
                50,
                51,
                52,
                53,
                54,
                55,
                56,
                57,
                58,
                59,
                60,
                61,
                62,
                63,
                0,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18,
                19,
                20,
                21,
                22,
                23,
                24,
                25,
                26,
                27,
                28,
                29,
                30,
                31,
                32,
                33,
                34,
                35,
                36,
                37,
                38,
                39,
                40,
                41,
                42,
                43,
                44,
                45,
                46,
                47,
                48,
                49,
                50,
                51,
                52,
                53,
                54,
                55,
                56,
                57,
                58,
                59,
                60,
                61,
                62,
                63,
                0,
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18,
                19,
                20,
                21,
                22,
                23,
                24,
                25,
                26,
                27,
                28,
                29,
                30,
                31,
                32,
                33,
                34,
                35,
                36,
                37,
                38,
                39,
                40,
                41,
                42,
                43,
                44,
                45,
                46,
                47,
                48,
                49,
                50,
                51,
                52,
                53,
                54,
                55,
                56,
                57,
                58,
                59,
                60,
                61,
                62,
                63,
                /* CONTEXT_MSB6, last byte. */
                0,
                0,
                0,
                0,
                1,
                1,
                1,
                1,
                2,
                2,
                2,
                2,
                3,
                3,
                3,
                3,
                4,
                4,
                4,
                4,
                5,
                5,
                5,
                5,
                6,
                6,
                6,
                6,
                7,
                7,
                7,
                7,
                8,
                8,
                8,
                8,
                9,
                9,
                9,
                9,
                10,
                10,
                10,
                10,
                11,
                11,
                11,
                11,
                12,
                12,
                12,
                12,
                13,
                13,
                13,
                13,
                14,
                14,
                14,
                14,
                15,
                15,
                15,
                15,
                16,
                16,
                16,
                16,
                17,
                17,
                17,
                17,
                18,
                18,
                18,
                18,
                19,
                19,
                19,
                19,
                20,
                20,
                20,
                20,
                21,
                21,
                21,
                21,
                22,
                22,
                22,
                22,
                23,
                23,
                23,
                23,
                24,
                24,
                24,
                24,
                25,
                25,
                25,
                25,
                26,
                26,
                26,
                26,
                27,
                27,
                27,
                27,
                28,
                28,
                28,
                28,
                29,
                29,
                29,
                29,
                30,
                30,
                30,
                30,
                31,
                31,
                31,
                31,
                32,
                32,
                32,
                32,
                33,
                33,
                33,
                33,
                34,
                34,
                34,
                34,
                35,
                35,
                35,
                35,
                36,
                36,
                36,
                36,
                37,
                37,
                37,
                37,
                38,
                38,
                38,
                38,
                39,
                39,
                39,
                39,
                40,
                40,
                40,
                40,
                41,
                41,
                41,
                41,
                42,
                42,
                42,
                42,
                43,
                43,
                43,
                43,
                44,
                44,
                44,
                44,
                45,
                45,
                45,
                45,
                46,
                46,
                46,
                46,
                47,
                47,
                47,
                47,
                48,
                48,
                48,
                48,
                49,
                49,
                49,
                49,
                50,
                50,
                50,
                50,
                51,
                51,
                51,
                51,
                52,
                52,
                52,
                52,
                53,
                53,
                53,
                53,
                54,
                54,
                54,
                54,
                55,
                55,
                55,
                55,
                56,
                56,
                56,
                56,
                57,
                57,
                57,
                57,
                58,
                58,
                58,
                58,
                59,
                59,
                59,
                59,
                60,
                60,
                60,
                60,
                61,
                61,
                61,
                61,
                62,
                62,
                62,
                62,
                63,
                63,
                63,
                63,
                /* CONTEXT_{M,L}SB6, second last byte, */
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0
              ]);
              exports3.lookupOffsets = new Uint16Array([
                /* CONTEXT_LSB6 */
                1024,
                1536,
                1280,
                1536,
                0,
                256,
                768,
                512
              ]);
            },
            {}
          ],
          3: [
            function(require2, module3, exports3) {
              var BrotliInput = require2("./streams").BrotliInput;
              var BrotliOutput = require2("./streams").BrotliOutput;
              var BrotliBitReader = require2("./bit_reader");
              var BrotliDictionary = require2("./dictionary");
              var HuffmanCode = require2("./huffman").HuffmanCode;
              var BrotliBuildHuffmanTable = require2("./huffman").BrotliBuildHuffmanTable;
              var Context = require2("./context");
              var Prefix = require2("./prefix");
              var Transform = require2("./transform");
              var kDefaultCodeLength = 8;
              var kCodeLengthRepeatCode = 16;
              var kNumLiteralCodes = 256;
              var kNumInsertAndCopyCodes = 704;
              var kNumBlockLengthCodes = 26;
              var kLiteralContextBits = 6;
              var kDistanceContextBits = 2;
              var HUFFMAN_TABLE_BITS = 8;
              var HUFFMAN_TABLE_MASK = 255;
              var HUFFMAN_MAX_TABLE_SIZE = 1080;
              var CODE_LENGTH_CODES = 18;
              var kCodeLengthCodeOrder = new Uint8Array([
                1,
                2,
                3,
                4,
                0,
                5,
                17,
                6,
                16,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15
              ]);
              var NUM_DISTANCE_SHORT_CODES = 16;
              var kDistanceShortCodeIndexOffset = new Uint8Array([
                3,
                2,
                1,
                0,
                3,
                3,
                3,
                3,
                3,
                3,
                2,
                2,
                2,
                2,
                2,
                2
              ]);
              var kDistanceShortCodeValueOffset = new Int8Array([
                0,
                0,
                0,
                0,
                -1,
                1,
                -2,
                2,
                -3,
                3,
                -1,
                1,
                -2,
                2,
                -3,
                3
              ]);
              var kMaxHuffmanTableSize = new Uint16Array([
                256,
                402,
                436,
                468,
                500,
                534,
                566,
                598,
                630,
                662,
                694,
                726,
                758,
                790,
                822,
                854,
                886,
                920,
                952,
                984,
                1016,
                1048,
                1080
              ]);
              function DecodeWindowBits(br) {
                var n2;
                if (br.readBits(1) === 0) {
                  return 16;
                }
                n2 = br.readBits(3);
                if (n2 > 0) {
                  return 17 + n2;
                }
                n2 = br.readBits(3);
                if (n2 > 0) {
                  return 8 + n2;
                }
                return 17;
              }
              function DecodeVarLenUint8(br) {
                if (br.readBits(1)) {
                  var nbits = br.readBits(3);
                  if (nbits === 0) {
                    return 1;
                  } else {
                    return br.readBits(nbits) + (1 << nbits);
                  }
                }
                return 0;
              }
              function MetaBlockLength() {
                this.meta_block_length = 0;
                this.input_end = 0;
                this.is_uncompressed = 0;
                this.is_metadata = false;
              }
              function DecodeMetaBlockLength(br) {
                var out = new MetaBlockLength();
                var size_nibbles;
                var size_bytes;
                var i2;
                out.input_end = br.readBits(1);
                if (out.input_end && br.readBits(1)) {
                  return out;
                }
                size_nibbles = br.readBits(2) + 4;
                if (size_nibbles === 7) {
                  out.is_metadata = true;
                  if (br.readBits(1) !== 0)
                    throw new Error("Invalid reserved bit");
                  size_bytes = br.readBits(2);
                  if (size_bytes === 0) return out;
                  for (i2 = 0; i2 < size_bytes; i2++) {
                    var next_byte = br.readBits(8);
                    if (i2 + 1 === size_bytes && size_bytes > 1 && next_byte === 0)
                      throw new Error("Invalid size byte");
                    out.meta_block_length |= next_byte << i2 * 8;
                  }
                } else {
                  for (i2 = 0; i2 < size_nibbles; ++i2) {
                    var next_nibble = br.readBits(4);
                    if (i2 + 1 === size_nibbles && size_nibbles > 4 && next_nibble === 0)
                      throw new Error("Invalid size nibble");
                    out.meta_block_length |= next_nibble << i2 * 4;
                  }
                }
                ++out.meta_block_length;
                if (!out.input_end && !out.is_metadata) {
                  out.is_uncompressed = br.readBits(1);
                }
                return out;
              }
              function ReadSymbol(table, index, br) {
                var start_index = index;
                var nbits;
                br.fillBitWindow();
                index += br.val_ >>> br.bit_pos_ & HUFFMAN_TABLE_MASK;
                nbits = table[index].bits - HUFFMAN_TABLE_BITS;
                if (nbits > 0) {
                  br.bit_pos_ += HUFFMAN_TABLE_BITS;
                  index += table[index].value;
                  index += br.val_ >>> br.bit_pos_ & (1 << nbits) - 1;
                }
                br.bit_pos_ += table[index].bits;
                return table[index].value;
              }
              function ReadHuffmanCodeLengths(code_length_code_lengths, num_symbols, code_lengths, br) {
                var symbol = 0;
                var prev_code_len = kDefaultCodeLength;
                var repeat = 0;
                var repeat_code_len = 0;
                var space = 32768;
                var table = [];
                for (var i2 = 0; i2 < 32; i2++)
                  table.push(new HuffmanCode(0, 0));
                BrotliBuildHuffmanTable(
                  table,
                  0,
                  5,
                  code_length_code_lengths,
                  CODE_LENGTH_CODES
                );
                while (symbol < num_symbols && space > 0) {
                  var p3 = 0;
                  var code_len;
                  br.readMoreInput();
                  br.fillBitWindow();
                  p3 += br.val_ >>> br.bit_pos_ & 31;
                  br.bit_pos_ += table[p3].bits;
                  code_len = table[p3].value & 255;
                  if (code_len < kCodeLengthRepeatCode) {
                    repeat = 0;
                    code_lengths[symbol++] = code_len;
                    if (code_len !== 0) {
                      prev_code_len = code_len;
                      space -= 32768 >> code_len;
                    }
                  } else {
                    var extra_bits = code_len - 14;
                    var old_repeat;
                    var repeat_delta;
                    var new_len = 0;
                    if (code_len === kCodeLengthRepeatCode) {
                      new_len = prev_code_len;
                    }
                    if (repeat_code_len !== new_len) {
                      repeat = 0;
                      repeat_code_len = new_len;
                    }
                    old_repeat = repeat;
                    if (repeat > 0) {
                      repeat -= 2;
                      repeat <<= extra_bits;
                    }
                    repeat += br.readBits(extra_bits) + 3;
                    repeat_delta = repeat - old_repeat;
                    if (symbol + repeat_delta > num_symbols) {
                      throw new Error(
                        "[ReadHuffmanCodeLengths] symbol + repeat_delta > num_symbols"
                      );
                    }
                    for (var x2 = 0; x2 < repeat_delta; x2++)
                      code_lengths[symbol + x2] = repeat_code_len;
                    symbol += repeat_delta;
                    if (repeat_code_len !== 0) {
                      space -= repeat_delta << 15 - repeat_code_len;
                    }
                  }
                }
                if (space !== 0) {
                  throw new Error(
                    "[ReadHuffmanCodeLengths] space = " + space
                  );
                }
                for (; symbol < num_symbols; symbol++)
                  code_lengths[symbol] = 0;
              }
              function ReadHuffmanCode(alphabet_size, tables, table, br) {
                var table_size = 0;
                var simple_code_or_skip;
                var code_lengths = new Uint8Array(alphabet_size);
                br.readMoreInput();
                simple_code_or_skip = br.readBits(2);
                if (simple_code_or_skip === 1) {
                  var i2;
                  var max_bits_counter = alphabet_size - 1;
                  var max_bits = 0;
                  var symbols = new Int32Array(4);
                  var num_symbols = br.readBits(2) + 1;
                  while (max_bits_counter) {
                    max_bits_counter >>= 1;
                    ++max_bits;
                  }
                  for (i2 = 0; i2 < num_symbols; ++i2) {
                    symbols[i2] = br.readBits(max_bits) % alphabet_size;
                    code_lengths[symbols[i2]] = 2;
                  }
                  code_lengths[symbols[0]] = 1;
                  switch (num_symbols) {
                    case 1:
                      break;
                    case 3:
                      if (symbols[0] === symbols[1] || symbols[0] === symbols[2] || symbols[1] === symbols[2]) {
                        throw new Error(
                          "[ReadHuffmanCode] invalid symbols"
                        );
                      }
                      break;
                    case 2:
                      if (symbols[0] === symbols[1]) {
                        throw new Error(
                          "[ReadHuffmanCode] invalid symbols"
                        );
                      }
                      code_lengths[symbols[1]] = 1;
                      break;
                    case 4:
                      if (symbols[0] === symbols[1] || symbols[0] === symbols[2] || symbols[0] === symbols[3] || symbols[1] === symbols[2] || symbols[1] === symbols[3] || symbols[2] === symbols[3]) {
                        throw new Error(
                          "[ReadHuffmanCode] invalid symbols"
                        );
                      }
                      if (br.readBits(1)) {
                        code_lengths[symbols[2]] = 3;
                        code_lengths[symbols[3]] = 3;
                      } else {
                        code_lengths[symbols[0]] = 2;
                      }
                      break;
                  }
                } else {
                  var i2;
                  var code_length_code_lengths = new Uint8Array(
                    CODE_LENGTH_CODES
                  );
                  var space = 32;
                  var num_codes = 0;
                  var huff = [
                    new HuffmanCode(2, 0),
                    new HuffmanCode(2, 4),
                    new HuffmanCode(2, 3),
                    new HuffmanCode(3, 2),
                    new HuffmanCode(2, 0),
                    new HuffmanCode(2, 4),
                    new HuffmanCode(2, 3),
                    new HuffmanCode(4, 1),
                    new HuffmanCode(2, 0),
                    new HuffmanCode(2, 4),
                    new HuffmanCode(2, 3),
                    new HuffmanCode(3, 2),
                    new HuffmanCode(2, 0),
                    new HuffmanCode(2, 4),
                    new HuffmanCode(2, 3),
                    new HuffmanCode(4, 5)
                  ];
                  for (i2 = simple_code_or_skip; i2 < CODE_LENGTH_CODES && space > 0; ++i2) {
                    var code_len_idx = kCodeLengthCodeOrder[i2];
                    var p3 = 0;
                    var v2;
                    br.fillBitWindow();
                    p3 += br.val_ >>> br.bit_pos_ & 15;
                    br.bit_pos_ += huff[p3].bits;
                    v2 = huff[p3].value;
                    code_length_code_lengths[code_len_idx] = v2;
                    if (v2 !== 0) {
                      space -= 32 >> v2;
                      ++num_codes;
                    }
                  }
                  if (!(num_codes === 1 || space === 0))
                    throw new Error(
                      "[ReadHuffmanCode] invalid num_codes or space"
                    );
                  ReadHuffmanCodeLengths(
                    code_length_code_lengths,
                    alphabet_size,
                    code_lengths,
                    br
                  );
                }
                table_size = BrotliBuildHuffmanTable(
                  tables,
                  table,
                  HUFFMAN_TABLE_BITS,
                  code_lengths,
                  alphabet_size
                );
                if (table_size === 0) {
                  throw new Error(
                    "[ReadHuffmanCode] BuildHuffmanTable failed: "
                  );
                }
                return table_size;
              }
              function ReadBlockLength(table, index, br) {
                var code;
                var nbits;
                code = ReadSymbol(table, index, br);
                nbits = Prefix.kBlockLengthPrefixCode[code].nbits;
                return Prefix.kBlockLengthPrefixCode[code].offset + br.readBits(nbits);
              }
              function TranslateShortCodes(code, ringbuffer, index) {
                var val;
                if (code < NUM_DISTANCE_SHORT_CODES) {
                  index += kDistanceShortCodeIndexOffset[code];
                  index &= 3;
                  val = ringbuffer[index] + kDistanceShortCodeValueOffset[code];
                } else {
                  val = code - NUM_DISTANCE_SHORT_CODES + 1;
                }
                return val;
              }
              function MoveToFront(v2, index) {
                var value = v2[index];
                var i2 = index;
                for (; i2; --i2) v2[i2] = v2[i2 - 1];
                v2[0] = value;
              }
              function InverseMoveToFrontTransform(v2, v_len) {
                var mtf = new Uint8Array(256);
                var i2;
                for (i2 = 0; i2 < 256; ++i2) {
                  mtf[i2] = i2;
                }
                for (i2 = 0; i2 < v_len; ++i2) {
                  var index = v2[i2];
                  v2[i2] = mtf[index];
                  if (index) MoveToFront(mtf, index);
                }
              }
              function HuffmanTreeGroup(alphabet_size, num_htrees) {
                this.alphabet_size = alphabet_size;
                this.num_htrees = num_htrees;
                this.codes = new Array(
                  num_htrees + num_htrees * kMaxHuffmanTableSize[alphabet_size + 31 >>> 5]
                );
                this.htrees = new Uint32Array(num_htrees);
              }
              HuffmanTreeGroup.prototype.decode = function(br) {
                var i2;
                var table_size;
                var next = 0;
                for (i2 = 0; i2 < this.num_htrees; ++i2) {
                  this.htrees[i2] = next;
                  table_size = ReadHuffmanCode(
                    this.alphabet_size,
                    this.codes,
                    next,
                    br
                  );
                  next += table_size;
                }
              };
              function DecodeContextMap(context_map_size, br) {
                var out = { num_htrees: null, context_map: null };
                var use_rle_for_zeros;
                var max_run_length_prefix = 0;
                var table;
                var i2;
                br.readMoreInput();
                var num_htrees = out.num_htrees = DecodeVarLenUint8(br) + 1;
                var context_map = out.context_map = new Uint8Array(
                  context_map_size
                );
                if (num_htrees <= 1) {
                  return out;
                }
                use_rle_for_zeros = br.readBits(1);
                if (use_rle_for_zeros) {
                  max_run_length_prefix = br.readBits(4) + 1;
                }
                table = [];
                for (i2 = 0; i2 < HUFFMAN_MAX_TABLE_SIZE; i2++) {
                  table[i2] = new HuffmanCode(0, 0);
                }
                ReadHuffmanCode(
                  num_htrees + max_run_length_prefix,
                  table,
                  0,
                  br
                );
                for (i2 = 0; i2 < context_map_size; ) {
                  var code;
                  br.readMoreInput();
                  code = ReadSymbol(table, 0, br);
                  if (code === 0) {
                    context_map[i2] = 0;
                    ++i2;
                  } else if (code <= max_run_length_prefix) {
                    var reps = 1 + (1 << code) + br.readBits(code);
                    while (--reps) {
                      if (i2 >= context_map_size) {
                        throw new Error(
                          "[DecodeContextMap] i >= context_map_size"
                        );
                      }
                      context_map[i2] = 0;
                      ++i2;
                    }
                  } else {
                    context_map[i2] = code - max_run_length_prefix;
                    ++i2;
                  }
                }
                if (br.readBits(1)) {
                  InverseMoveToFrontTransform(
                    context_map,
                    context_map_size
                  );
                }
                return out;
              }
              function DecodeBlockType(max_block_type, trees, tree_type, block_types, ringbuffers, indexes, br) {
                var ringbuffer = tree_type * 2;
                var index = tree_type;
                var type_code = ReadSymbol(
                  trees,
                  tree_type * HUFFMAN_MAX_TABLE_SIZE,
                  br
                );
                var block_type;
                if (type_code === 0) {
                  block_type = ringbuffers[ringbuffer + (indexes[index] & 1)];
                } else if (type_code === 1) {
                  block_type = ringbuffers[ringbuffer + (indexes[index] - 1 & 1)] + 1;
                } else {
                  block_type = type_code - 2;
                }
                if (block_type >= max_block_type) {
                  block_type -= max_block_type;
                }
                block_types[tree_type] = block_type;
                ringbuffers[ringbuffer + (indexes[index] & 1)] = block_type;
                ++indexes[index];
              }
              function CopyUncompressedBlockToOutput(output, len, pos, ringbuffer, ringbuffer_mask, br) {
                var rb_size = ringbuffer_mask + 1;
                var rb_pos = pos & ringbuffer_mask;
                var br_pos = br.pos_ & BrotliBitReader.IBUF_MASK;
                var nbytes;
                if (len < 8 || br.bit_pos_ + (len << 3) < br.bit_end_pos_) {
                  while (len-- > 0) {
                    br.readMoreInput();
                    ringbuffer[rb_pos++] = br.readBits(8);
                    if (rb_pos === rb_size) {
                      output.write(ringbuffer, rb_size);
                      rb_pos = 0;
                    }
                  }
                  return;
                }
                if (br.bit_end_pos_ < 32) {
                  throw new Error(
                    "[CopyUncompressedBlockToOutput] br.bit_end_pos_ < 32"
                  );
                }
                while (br.bit_pos_ < 32) {
                  ringbuffer[rb_pos] = br.val_ >>> br.bit_pos_;
                  br.bit_pos_ += 8;
                  ++rb_pos;
                  --len;
                }
                nbytes = br.bit_end_pos_ - br.bit_pos_ >> 3;
                if (br_pos + nbytes > BrotliBitReader.IBUF_MASK) {
                  var tail = BrotliBitReader.IBUF_MASK + 1 - br_pos;
                  for (var x2 = 0; x2 < tail; x2++)
                    ringbuffer[rb_pos + x2] = br.buf_[br_pos + x2];
                  nbytes -= tail;
                  rb_pos += tail;
                  len -= tail;
                  br_pos = 0;
                }
                for (var x2 = 0; x2 < nbytes; x2++)
                  ringbuffer[rb_pos + x2] = br.buf_[br_pos + x2];
                rb_pos += nbytes;
                len -= nbytes;
                if (rb_pos >= rb_size) {
                  output.write(ringbuffer, rb_size);
                  rb_pos -= rb_size;
                  for (var x2 = 0; x2 < rb_pos; x2++)
                    ringbuffer[x2] = ringbuffer[rb_size + x2];
                }
                while (rb_pos + len >= rb_size) {
                  nbytes = rb_size - rb_pos;
                  if (br.input_.read(ringbuffer, rb_pos, nbytes) < nbytes) {
                    throw new Error(
                      "[CopyUncompressedBlockToOutput] not enough bytes"
                    );
                  }
                  output.write(ringbuffer, rb_size);
                  len -= nbytes;
                  rb_pos = 0;
                }
                if (br.input_.read(ringbuffer, rb_pos, len) < len) {
                  throw new Error(
                    "[CopyUncompressedBlockToOutput] not enough bytes"
                  );
                }
                br.reset();
              }
              function JumpToByteBoundary(br) {
                var new_bit_pos = br.bit_pos_ + 7 & ~7;
                var pad_bits = br.readBits(new_bit_pos - br.bit_pos_);
                return pad_bits == 0;
              }
              function BrotliDecompressedSize(buffer) {
                var input = new BrotliInput(buffer);
                var br = new BrotliBitReader(input);
                DecodeWindowBits(br);
                var out = DecodeMetaBlockLength(br);
                return out.meta_block_length;
              }
              exports3.BrotliDecompressedSize = BrotliDecompressedSize;
              function BrotliDecompressBuffer(buffer, output_size) {
                var input = new BrotliInput(buffer);
                if (output_size == null) {
                  output_size = BrotliDecompressedSize(buffer);
                }
                var output_buffer = new Uint8Array(output_size);
                var output = new BrotliOutput(output_buffer);
                BrotliDecompress(input, output);
                if (output.pos < output.buffer.length) {
                  output.buffer = output.buffer.subarray(
                    0,
                    output.pos
                  );
                }
                return output.buffer;
              }
              exports3.BrotliDecompressBuffer = BrotliDecompressBuffer;
              function BrotliDecompress(input, output) {
                var i2;
                var pos = 0;
                var input_end = 0;
                var window_bits = 0;
                var max_backward_distance;
                var max_distance = 0;
                var ringbuffer_size;
                var ringbuffer_mask;
                var ringbuffer;
                var ringbuffer_end;
                var dist_rb = [16, 15, 11, 4];
                var dist_rb_idx = 0;
                var prev_byte1 = 0;
                var prev_byte2 = 0;
                var hgroup = [
                  new HuffmanTreeGroup(0, 0),
                  new HuffmanTreeGroup(0, 0),
                  new HuffmanTreeGroup(0, 0)
                ];
                var block_type_trees;
                var block_len_trees;
                var br;
                var kRingBufferWriteAheadSlack = 128 + BrotliBitReader.READ_SIZE;
                br = new BrotliBitReader(input);
                window_bits = DecodeWindowBits(br);
                max_backward_distance = (1 << window_bits) - 16;
                ringbuffer_size = 1 << window_bits;
                ringbuffer_mask = ringbuffer_size - 1;
                ringbuffer = new Uint8Array(
                  ringbuffer_size + kRingBufferWriteAheadSlack + BrotliDictionary.maxDictionaryWordLength
                );
                ringbuffer_end = ringbuffer_size;
                block_type_trees = [];
                block_len_trees = [];
                for (var x2 = 0; x2 < 3 * HUFFMAN_MAX_TABLE_SIZE; x2++) {
                  block_type_trees[x2] = new HuffmanCode(0, 0);
                  block_len_trees[x2] = new HuffmanCode(0, 0);
                }
                while (!input_end) {
                  var meta_block_remaining_len = 0;
                  var is_uncompressed;
                  var block_length = [1 << 28, 1 << 28, 1 << 28];
                  var block_type = [0];
                  var num_block_types = [1, 1, 1];
                  var block_type_rb = [0, 1, 0, 1, 0, 1];
                  var block_type_rb_index = [0];
                  var distance_postfix_bits;
                  var num_direct_distance_codes;
                  var distance_postfix_mask;
                  var num_distance_codes;
                  var context_map = null;
                  var context_modes = null;
                  var num_literal_htrees;
                  var dist_context_map = null;
                  var num_dist_htrees;
                  var context_offset = 0;
                  var context_map_slice = null;
                  var literal_htree_index = 0;
                  var dist_context_offset = 0;
                  var dist_context_map_slice = null;
                  var dist_htree_index = 0;
                  var context_lookup_offset1 = 0;
                  var context_lookup_offset2 = 0;
                  var context_mode;
                  var htree_command;
                  for (i2 = 0; i2 < 3; ++i2) {
                    hgroup[i2].codes = null;
                    hgroup[i2].htrees = null;
                  }
                  br.readMoreInput();
                  var _out = DecodeMetaBlockLength(br);
                  meta_block_remaining_len = _out.meta_block_length;
                  if (pos + meta_block_remaining_len > output.buffer.length) {
                    var tmp = new Uint8Array(
                      pos + meta_block_remaining_len
                    );
                    tmp.set(output.buffer);
                    output.buffer = tmp;
                  }
                  input_end = _out.input_end;
                  is_uncompressed = _out.is_uncompressed;
                  if (_out.is_metadata) {
                    JumpToByteBoundary(br);
                    for (; meta_block_remaining_len > 0; --meta_block_remaining_len) {
                      br.readMoreInput();
                      br.readBits(8);
                    }
                    continue;
                  }
                  if (meta_block_remaining_len === 0) {
                    continue;
                  }
                  if (is_uncompressed) {
                    br.bit_pos_ = br.bit_pos_ + 7 & ~7;
                    CopyUncompressedBlockToOutput(
                      output,
                      meta_block_remaining_len,
                      pos,
                      ringbuffer,
                      ringbuffer_mask,
                      br
                    );
                    pos += meta_block_remaining_len;
                    continue;
                  }
                  for (i2 = 0; i2 < 3; ++i2) {
                    num_block_types[i2] = DecodeVarLenUint8(br) + 1;
                    if (num_block_types[i2] >= 2) {
                      ReadHuffmanCode(
                        num_block_types[i2] + 2,
                        block_type_trees,
                        i2 * HUFFMAN_MAX_TABLE_SIZE,
                        br
                      );
                      ReadHuffmanCode(
                        kNumBlockLengthCodes,
                        block_len_trees,
                        i2 * HUFFMAN_MAX_TABLE_SIZE,
                        br
                      );
                      block_length[i2] = ReadBlockLength(
                        block_len_trees,
                        i2 * HUFFMAN_MAX_TABLE_SIZE,
                        br
                      );
                      block_type_rb_index[i2] = 1;
                    }
                  }
                  br.readMoreInput();
                  distance_postfix_bits = br.readBits(2);
                  num_direct_distance_codes = NUM_DISTANCE_SHORT_CODES + (br.readBits(4) << distance_postfix_bits);
                  distance_postfix_mask = (1 << distance_postfix_bits) - 1;
                  num_distance_codes = num_direct_distance_codes + (48 << distance_postfix_bits);
                  context_modes = new Uint8Array(
                    num_block_types[0]
                  );
                  for (i2 = 0; i2 < num_block_types[0]; ++i2) {
                    br.readMoreInput();
                    context_modes[i2] = br.readBits(2) << 1;
                  }
                  var _o1 = DecodeContextMap(
                    num_block_types[0] << kLiteralContextBits,
                    br
                  );
                  num_literal_htrees = _o1.num_htrees;
                  context_map = _o1.context_map;
                  var _o2 = DecodeContextMap(
                    num_block_types[2] << kDistanceContextBits,
                    br
                  );
                  num_dist_htrees = _o2.num_htrees;
                  dist_context_map = _o2.context_map;
                  hgroup[0] = new HuffmanTreeGroup(
                    kNumLiteralCodes,
                    num_literal_htrees
                  );
                  hgroup[1] = new HuffmanTreeGroup(
                    kNumInsertAndCopyCodes,
                    num_block_types[1]
                  );
                  hgroup[2] = new HuffmanTreeGroup(
                    num_distance_codes,
                    num_dist_htrees
                  );
                  for (i2 = 0; i2 < 3; ++i2) {
                    hgroup[i2].decode(br);
                  }
                  context_map_slice = 0;
                  dist_context_map_slice = 0;
                  context_mode = context_modes[block_type[0]];
                  context_lookup_offset1 = Context.lookupOffsets[context_mode];
                  context_lookup_offset2 = Context.lookupOffsets[context_mode + 1];
                  htree_command = hgroup[1].htrees[0];
                  while (meta_block_remaining_len > 0) {
                    var cmd_code;
                    var range_idx;
                    var insert_code;
                    var copy_code;
                    var insert_length;
                    var copy_length;
                    var distance_code;
                    var distance;
                    var context;
                    var j2;
                    var copy_dst;
                    br.readMoreInput();
                    if (block_length[1] === 0) {
                      DecodeBlockType(
                        num_block_types[1],
                        block_type_trees,
                        1,
                        block_type,
                        block_type_rb,
                        block_type_rb_index,
                        br
                      );
                      block_length[1] = ReadBlockLength(
                        block_len_trees,
                        HUFFMAN_MAX_TABLE_SIZE,
                        br
                      );
                      htree_command = hgroup[1].htrees[block_type[1]];
                    }
                    --block_length[1];
                    cmd_code = ReadSymbol(
                      hgroup[1].codes,
                      htree_command,
                      br
                    );
                    range_idx = cmd_code >> 6;
                    if (range_idx >= 2) {
                      range_idx -= 2;
                      distance_code = -1;
                    } else {
                      distance_code = 0;
                    }
                    insert_code = Prefix.kInsertRangeLut[range_idx] + (cmd_code >> 3 & 7);
                    copy_code = Prefix.kCopyRangeLut[range_idx] + (cmd_code & 7);
                    insert_length = Prefix.kInsertLengthPrefixCode[insert_code].offset + br.readBits(
                      Prefix.kInsertLengthPrefixCode[insert_code].nbits
                    );
                    copy_length = Prefix.kCopyLengthPrefixCode[copy_code].offset + br.readBits(
                      Prefix.kCopyLengthPrefixCode[copy_code].nbits
                    );
                    prev_byte1 = ringbuffer[pos - 1 & ringbuffer_mask];
                    prev_byte2 = ringbuffer[pos - 2 & ringbuffer_mask];
                    for (j2 = 0; j2 < insert_length; ++j2) {
                      br.readMoreInput();
                      if (block_length[0] === 0) {
                        DecodeBlockType(
                          num_block_types[0],
                          block_type_trees,
                          0,
                          block_type,
                          block_type_rb,
                          block_type_rb_index,
                          br
                        );
                        block_length[0] = ReadBlockLength(
                          block_len_trees,
                          0,
                          br
                        );
                        context_offset = block_type[0] << kLiteralContextBits;
                        context_map_slice = context_offset;
                        context_mode = context_modes[block_type[0]];
                        context_lookup_offset1 = Context.lookupOffsets[context_mode];
                        context_lookup_offset2 = Context.lookupOffsets[context_mode + 1];
                      }
                      context = Context.lookup[context_lookup_offset1 + prev_byte1] | Context.lookup[context_lookup_offset2 + prev_byte2];
                      literal_htree_index = context_map[context_map_slice + context];
                      --block_length[0];
                      prev_byte2 = prev_byte1;
                      prev_byte1 = ReadSymbol(
                        hgroup[0].codes,
                        hgroup[0].htrees[literal_htree_index],
                        br
                      );
                      ringbuffer[pos & ringbuffer_mask] = prev_byte1;
                      if ((pos & ringbuffer_mask) === ringbuffer_mask) {
                        output.write(
                          ringbuffer,
                          ringbuffer_size
                        );
                      }
                      ++pos;
                    }
                    meta_block_remaining_len -= insert_length;
                    if (meta_block_remaining_len <= 0) break;
                    if (distance_code < 0) {
                      var context;
                      br.readMoreInput();
                      if (block_length[2] === 0) {
                        DecodeBlockType(
                          num_block_types[2],
                          block_type_trees,
                          2,
                          block_type,
                          block_type_rb,
                          block_type_rb_index,
                          br
                        );
                        block_length[2] = ReadBlockLength(
                          block_len_trees,
                          2 * HUFFMAN_MAX_TABLE_SIZE,
                          br
                        );
                        dist_context_offset = block_type[2] << kDistanceContextBits;
                        dist_context_map_slice = dist_context_offset;
                      }
                      --block_length[2];
                      context = (copy_length > 4 ? 3 : copy_length - 2) & 255;
                      dist_htree_index = dist_context_map[dist_context_map_slice + context];
                      distance_code = ReadSymbol(
                        hgroup[2].codes,
                        hgroup[2].htrees[dist_htree_index],
                        br
                      );
                      if (distance_code >= num_direct_distance_codes) {
                        var nbits;
                        var postfix;
                        var offset;
                        distance_code -= num_direct_distance_codes;
                        postfix = distance_code & distance_postfix_mask;
                        distance_code >>= distance_postfix_bits;
                        nbits = (distance_code >> 1) + 1;
                        offset = (2 + (distance_code & 1) << nbits) - 4;
                        distance_code = num_direct_distance_codes + (offset + br.readBits(nbits) << distance_postfix_bits) + postfix;
                      }
                    }
                    distance = TranslateShortCodes(
                      distance_code,
                      dist_rb,
                      dist_rb_idx
                    );
                    if (distance < 0) {
                      throw new Error(
                        "[BrotliDecompress] invalid distance"
                      );
                    }
                    if (pos < max_backward_distance && max_distance !== max_backward_distance) {
                      max_distance = pos;
                    } else {
                      max_distance = max_backward_distance;
                    }
                    copy_dst = pos & ringbuffer_mask;
                    if (distance > max_distance) {
                      if (copy_length >= BrotliDictionary.minDictionaryWordLength && copy_length <= BrotliDictionary.maxDictionaryWordLength) {
                        var offset = BrotliDictionary.offsetsByLength[copy_length];
                        var word_id = distance - max_distance - 1;
                        var shift = BrotliDictionary.sizeBitsByLength[copy_length];
                        var mask = (1 << shift) - 1;
                        var word_idx = word_id & mask;
                        var transform_idx = word_id >> shift;
                        offset += word_idx * copy_length;
                        if (transform_idx < Transform.kNumTransforms) {
                          var len = Transform.transformDictionaryWord(
                            ringbuffer,
                            copy_dst,
                            offset,
                            copy_length,
                            transform_idx
                          );
                          copy_dst += len;
                          pos += len;
                          meta_block_remaining_len -= len;
                          if (copy_dst >= ringbuffer_end) {
                            output.write(
                              ringbuffer,
                              ringbuffer_size
                            );
                            for (var _x9 = 0; _x9 < copy_dst - ringbuffer_end; _x9++)
                              ringbuffer[_x9] = ringbuffer[ringbuffer_end + _x9];
                          }
                        } else {
                          throw new Error(
                            "Invalid backward reference. pos: " + pos + " distance: " + distance + " len: " + copy_length + " bytes left: " + meta_block_remaining_len
                          );
                        }
                      } else {
                        throw new Error(
                          "Invalid backward reference. pos: " + pos + " distance: " + distance + " len: " + copy_length + " bytes left: " + meta_block_remaining_len
                        );
                      }
                    } else {
                      if (distance_code > 0) {
                        dist_rb[dist_rb_idx & 3] = distance;
                        ++dist_rb_idx;
                      }
                      if (copy_length > meta_block_remaining_len) {
                        throw new Error(
                          "Invalid backward reference. pos: " + pos + " distance: " + distance + " len: " + copy_length + " bytes left: " + meta_block_remaining_len
                        );
                      }
                      for (j2 = 0; j2 < copy_length; ++j2) {
                        ringbuffer[pos & ringbuffer_mask] = ringbuffer[pos - distance & ringbuffer_mask];
                        if ((pos & ringbuffer_mask) === ringbuffer_mask) {
                          output.write(
                            ringbuffer,
                            ringbuffer_size
                          );
                        }
                        ++pos;
                        --meta_block_remaining_len;
                      }
                    }
                    prev_byte1 = ringbuffer[pos - 1 & ringbuffer_mask];
                    prev_byte2 = ringbuffer[pos - 2 & ringbuffer_mask];
                  }
                  pos &= 1073741823;
                }
                output.write(ringbuffer, pos & ringbuffer_mask);
              }
              exports3.BrotliDecompress = BrotliDecompress;
              BrotliDictionary.init();
            },
            {
              "./bit_reader": 1,
              "./context": 2,
              "./dictionary": 6,
              "./huffman": 7,
              "./prefix": 9,
              "./streams": 10,
              "./transform": 11
            }
          ],
          4: [
            function(require2, module3, exports3) {
              var base64 = require2("base64-js");
              exports3.init = function() {
                var BrotliDecompressBuffer = require2("./decode").BrotliDecompressBuffer;
                var compressed = base64.toByteArray(
                  require2("./dictionary.bin.js")
                );
                return BrotliDecompressBuffer(compressed);
              };
            },
            { "./decode": 3, "./dictionary.bin.js": 5, "base64-js": 8 }
          ],
          5: [
            function(require2, module3, exports3) {
              module3.exports = "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";
            },
            {}
          ],
          6: [
            function(require2, module3, exports3) {
              var data = require2("./dictionary-browser");
              exports3.init = function() {
                exports3.dictionary = data.init();
              };
              exports3.offsetsByLength = new Uint32Array([
                0,
                0,
                0,
                0,
                0,
                4096,
                9216,
                21504,
                35840,
                44032,
                53248,
                63488,
                74752,
                87040,
                93696,
                100864,
                104704,
                106752,
                108928,
                113536,
                115968,
                118528,
                119872,
                121280,
                122016
              ]);
              exports3.sizeBitsByLength = new Uint8Array([
                0,
                0,
                0,
                0,
                10,
                10,
                11,
                11,
                10,
                10,
                10,
                10,
                10,
                9,
                9,
                8,
                7,
                7,
                8,
                7,
                7,
                6,
                6,
                5,
                5
              ]);
              exports3.minDictionaryWordLength = 4;
              exports3.maxDictionaryWordLength = 24;
            },
            { "./dictionary-browser": 4 }
          ],
          7: [
            function(require2, module3, exports3) {
              function HuffmanCode(bits, value) {
                this.bits = bits;
                this.value = value;
              }
              exports3.HuffmanCode = HuffmanCode;
              var MAX_LENGTH = 15;
              function GetNextKey(key, len) {
                var step = 1 << len - 1;
                while (key & step) {
                  step >>= 1;
                }
                return (key & step - 1) + step;
              }
              function ReplicateValue(table, i2, step, end, code) {
                do {
                  end -= step;
                  table[i2 + end] = new HuffmanCode(
                    code.bits,
                    code.value
                  );
                } while (end > 0);
              }
              function NextTableBitSize(count, len, root_bits) {
                var left = 1 << len - root_bits;
                while (len < MAX_LENGTH) {
                  left -= count[len];
                  if (left <= 0) break;
                  ++len;
                  left <<= 1;
                }
                return len - root_bits;
              }
              exports3.BrotliBuildHuffmanTable = function(root_table, table, root_bits, code_lengths, code_lengths_size) {
                var start_table = table;
                var code;
                var len;
                var symbol;
                var key;
                var step;
                var low;
                var mask;
                var table_bits;
                var table_size;
                var total_size;
                var sorted;
                var count = new Int32Array(
                  MAX_LENGTH + 1
                );
                var offset = new Int32Array(
                  MAX_LENGTH + 1
                );
                sorted = new Int32Array(code_lengths_size);
                for (symbol = 0; symbol < code_lengths_size; symbol++) {
                  count[code_lengths[symbol]]++;
                }
                offset[1] = 0;
                for (len = 1; len < MAX_LENGTH; len++) {
                  offset[len + 1] = offset[len] + count[len];
                }
                for (symbol = 0; symbol < code_lengths_size; symbol++) {
                  if (code_lengths[symbol] !== 0) {
                    sorted[offset[code_lengths[symbol]]++] = symbol;
                  }
                }
                table_bits = root_bits;
                table_size = 1 << table_bits;
                total_size = table_size;
                if (offset[MAX_LENGTH] === 1) {
                  for (key = 0; key < total_size; ++key) {
                    root_table[table + key] = new HuffmanCode(
                      0,
                      sorted[0] & 65535
                    );
                  }
                  return total_size;
                }
                key = 0;
                symbol = 0;
                for (len = 1, step = 2; len <= root_bits; ++len, step <<= 1) {
                  for (; count[len] > 0; --count[len]) {
                    code = new HuffmanCode(
                      len & 255,
                      sorted[symbol++] & 65535
                    );
                    ReplicateValue(
                      root_table,
                      table + key,
                      step,
                      table_size,
                      code
                    );
                    key = GetNextKey(key, len);
                  }
                }
                mask = total_size - 1;
                low = -1;
                for (len = root_bits + 1, step = 2; len <= MAX_LENGTH; ++len, step <<= 1) {
                  for (; count[len] > 0; --count[len]) {
                    if ((key & mask) !== low) {
                      table += table_size;
                      table_bits = NextTableBitSize(
                        count,
                        len,
                        root_bits
                      );
                      table_size = 1 << table_bits;
                      total_size += table_size;
                      low = key & mask;
                      root_table[start_table + low] = new HuffmanCode(
                        table_bits + root_bits & 255,
                        table - start_table - low & 65535
                      );
                    }
                    code = new HuffmanCode(
                      len - root_bits & 255,
                      sorted[symbol++] & 65535
                    );
                    ReplicateValue(
                      root_table,
                      table + (key >> root_bits),
                      step,
                      table_size,
                      code
                    );
                    key = GetNextKey(key, len);
                  }
                }
                return total_size;
              };
            },
            {}
          ],
          8: [
            function(require2, module3, exports3) {
              "use strict";
              exports3.byteLength = byteLength;
              exports3.toByteArray = toByteArray;
              exports3.fromByteArray = fromByteArray;
              var lookup = [];
              var revLookup = [];
              var Arr = typeof Uint8Array !== "undefined" ? Uint8Array : Array;
              var code = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
              for (var i2 = 0, len = code.length; i2 < len; ++i2) {
                lookup[i2] = code[i2];
                revLookup[code.charCodeAt(i2)] = i2;
              }
              revLookup["-".charCodeAt(0)] = 62;
              revLookup["_".charCodeAt(0)] = 63;
              function getLens(b64) {
                var len2 = b64.length;
                if (len2 % 4 > 0) {
                  throw new Error(
                    "Invalid string. Length must be a multiple of 4"
                  );
                }
                var validLen = b64.indexOf("=");
                if (validLen === -1) validLen = len2;
                var placeHoldersLen = validLen === len2 ? 0 : 4 - validLen % 4;
                return [validLen, placeHoldersLen];
              }
              function byteLength(b64) {
                var lens = getLens(b64);
                var validLen = lens[0];
                var placeHoldersLen = lens[1];
                return (validLen + placeHoldersLen) * 3 / 4 - placeHoldersLen;
              }
              function _byteLength(b64, validLen, placeHoldersLen) {
                return (validLen + placeHoldersLen) * 3 / 4 - placeHoldersLen;
              }
              function toByteArray(b64) {
                var tmp;
                var lens = getLens(b64);
                var validLen = lens[0];
                var placeHoldersLen = lens[1];
                var arr = new Arr(
                  _byteLength(b64, validLen, placeHoldersLen)
                );
                var curByte = 0;
                var len2 = placeHoldersLen > 0 ? validLen - 4 : validLen;
                for (var i22 = 0; i22 < len2; i22 += 4) {
                  tmp = revLookup[b64.charCodeAt(i22)] << 18 | revLookup[b64.charCodeAt(i22 + 1)] << 12 | revLookup[b64.charCodeAt(i22 + 2)] << 6 | revLookup[b64.charCodeAt(i22 + 3)];
                  arr[curByte++] = tmp >> 16 & 255;
                  arr[curByte++] = tmp >> 8 & 255;
                  arr[curByte++] = tmp & 255;
                }
                if (placeHoldersLen === 2) {
                  tmp = revLookup[b64.charCodeAt(i22)] << 2 | revLookup[b64.charCodeAt(i22 + 1)] >> 4;
                  arr[curByte++] = tmp & 255;
                }
                if (placeHoldersLen === 1) {
                  tmp = revLookup[b64.charCodeAt(i22)] << 10 | revLookup[b64.charCodeAt(i22 + 1)] << 4 | revLookup[b64.charCodeAt(i22 + 2)] >> 2;
                  arr[curByte++] = tmp >> 8 & 255;
                  arr[curByte++] = tmp & 255;
                }
                return arr;
              }
              function tripletToBase64(num) {
                return lookup[num >> 18 & 63] + lookup[num >> 12 & 63] + lookup[num >> 6 & 63] + lookup[num & 63];
              }
              function encodeChunk(uint8, start, end) {
                var tmp;
                var output = [];
                for (var i22 = start; i22 < end; i22 += 3) {
                  tmp = (uint8[i22] << 16 & 16711680) + (uint8[i22 + 1] << 8 & 65280) + (uint8[i22 + 2] & 255);
                  output.push(tripletToBase64(tmp));
                }
                return output.join("");
              }
              function fromByteArray(uint8) {
                var tmp;
                var len2 = uint8.length;
                var extraBytes = len2 % 3;
                var parts = [];
                var maxChunkLength = 16383;
                for (var i22 = 0, len22 = len2 - extraBytes; i22 < len22; i22 += maxChunkLength) {
                  parts.push(
                    encodeChunk(
                      uint8,
                      i22,
                      i22 + maxChunkLength > len22 ? len22 : i22 + maxChunkLength
                    )
                  );
                }
                if (extraBytes === 1) {
                  tmp = uint8[len2 - 1];
                  parts.push(
                    lookup[tmp >> 2] + lookup[tmp << 4 & 63] + "=="
                  );
                } else if (extraBytes === 2) {
                  tmp = (uint8[len2 - 2] << 8) + uint8[len2 - 1];
                  parts.push(
                    lookup[tmp >> 10] + lookup[tmp >> 4 & 63] + lookup[tmp << 2 & 63] + "="
                  );
                }
                return parts.join("");
              }
            },
            {}
          ],
          9: [
            function(require2, module3, exports3) {
              function PrefixCodeRange(offset, nbits) {
                this.offset = offset;
                this.nbits = nbits;
              }
              exports3.kBlockLengthPrefixCode = [
                new PrefixCodeRange(1, 2),
                new PrefixCodeRange(5, 2),
                new PrefixCodeRange(9, 2),
                new PrefixCodeRange(13, 2),
                new PrefixCodeRange(17, 3),
                new PrefixCodeRange(25, 3),
                new PrefixCodeRange(33, 3),
                new PrefixCodeRange(41, 3),
                new PrefixCodeRange(49, 4),
                new PrefixCodeRange(65, 4),
                new PrefixCodeRange(81, 4),
                new PrefixCodeRange(97, 4),
                new PrefixCodeRange(113, 5),
                new PrefixCodeRange(145, 5),
                new PrefixCodeRange(177, 5),
                new PrefixCodeRange(209, 5),
                new PrefixCodeRange(241, 6),
                new PrefixCodeRange(305, 6),
                new PrefixCodeRange(369, 7),
                new PrefixCodeRange(497, 8),
                new PrefixCodeRange(753, 9),
                new PrefixCodeRange(1265, 10),
                new PrefixCodeRange(2289, 11),
                new PrefixCodeRange(4337, 12),
                new PrefixCodeRange(8433, 13),
                new PrefixCodeRange(16625, 24)
              ];
              exports3.kInsertLengthPrefixCode = [
                new PrefixCodeRange(0, 0),
                new PrefixCodeRange(1, 0),
                new PrefixCodeRange(2, 0),
                new PrefixCodeRange(3, 0),
                new PrefixCodeRange(4, 0),
                new PrefixCodeRange(5, 0),
                new PrefixCodeRange(6, 1),
                new PrefixCodeRange(8, 1),
                new PrefixCodeRange(10, 2),
                new PrefixCodeRange(14, 2),
                new PrefixCodeRange(18, 3),
                new PrefixCodeRange(26, 3),
                new PrefixCodeRange(34, 4),
                new PrefixCodeRange(50, 4),
                new PrefixCodeRange(66, 5),
                new PrefixCodeRange(98, 5),
                new PrefixCodeRange(130, 6),
                new PrefixCodeRange(194, 7),
                new PrefixCodeRange(322, 8),
                new PrefixCodeRange(578, 9),
                new PrefixCodeRange(1090, 10),
                new PrefixCodeRange(2114, 12),
                new PrefixCodeRange(6210, 14),
                new PrefixCodeRange(22594, 24)
              ];
              exports3.kCopyLengthPrefixCode = [
                new PrefixCodeRange(2, 0),
                new PrefixCodeRange(3, 0),
                new PrefixCodeRange(4, 0),
                new PrefixCodeRange(5, 0),
                new PrefixCodeRange(6, 0),
                new PrefixCodeRange(7, 0),
                new PrefixCodeRange(8, 0),
                new PrefixCodeRange(9, 0),
                new PrefixCodeRange(10, 1),
                new PrefixCodeRange(12, 1),
                new PrefixCodeRange(14, 2),
                new PrefixCodeRange(18, 2),
                new PrefixCodeRange(22, 3),
                new PrefixCodeRange(30, 3),
                new PrefixCodeRange(38, 4),
                new PrefixCodeRange(54, 4),
                new PrefixCodeRange(70, 5),
                new PrefixCodeRange(102, 5),
                new PrefixCodeRange(134, 6),
                new PrefixCodeRange(198, 7),
                new PrefixCodeRange(326, 8),
                new PrefixCodeRange(582, 9),
                new PrefixCodeRange(1094, 10),
                new PrefixCodeRange(2118, 24)
              ];
              exports3.kInsertRangeLut = [0, 0, 8, 8, 0, 16, 8, 16, 16];
              exports3.kCopyRangeLut = [0, 8, 0, 8, 16, 0, 16, 8, 16];
            },
            {}
          ],
          10: [
            function(require2, module3, exports3) {
              function BrotliInput(buffer) {
                this.buffer = buffer;
                this.pos = 0;
              }
              BrotliInput.prototype.read = function(buf, i2, count) {
                if (this.pos + count > this.buffer.length) {
                  count = this.buffer.length - this.pos;
                }
                for (var p3 = 0; p3 < count; p3++)
                  buf[i2 + p3] = this.buffer[this.pos + p3];
                this.pos += count;
                return count;
              };
              exports3.BrotliInput = BrotliInput;
              function BrotliOutput(buf) {
                this.buffer = buf;
                this.pos = 0;
              }
              BrotliOutput.prototype.write = function(buf, count) {
                if (this.pos + count > this.buffer.length)
                  throw new Error(
                    "Output buffer is not large enough"
                  );
                this.buffer.set(buf.subarray(0, count), this.pos);
                this.pos += count;
                return count;
              };
              exports3.BrotliOutput = BrotliOutput;
            },
            {}
          ],
          11: [
            function(require2, module3, exports3) {
              var BrotliDictionary = require2("./dictionary");
              var kIdentity = 0;
              var kOmitLast1 = 1;
              var kOmitLast2 = 2;
              var kOmitLast3 = 3;
              var kOmitLast4 = 4;
              var kOmitLast5 = 5;
              var kOmitLast6 = 6;
              var kOmitLast7 = 7;
              var kOmitLast8 = 8;
              var kOmitLast9 = 9;
              var kUppercaseFirst = 10;
              var kUppercaseAll = 11;
              var kOmitFirst1 = 12;
              var kOmitFirst2 = 13;
              var kOmitFirst3 = 14;
              var kOmitFirst4 = 15;
              var kOmitFirst5 = 16;
              var kOmitFirst6 = 17;
              var kOmitFirst7 = 18;
              var kOmitFirst8 = 19;
              var kOmitFirst9 = 20;
              function Transform(prefix, transform, suffix) {
                this.prefix = new Uint8Array(prefix.length);
                this.transform = transform;
                this.suffix = new Uint8Array(suffix.length);
                for (var i2 = 0; i2 < prefix.length; i2++)
                  this.prefix[i2] = prefix.charCodeAt(i2);
                for (var i2 = 0; i2 < suffix.length; i2++)
                  this.suffix[i2] = suffix.charCodeAt(i2);
              }
              var kTransforms = [
                new Transform("", kIdentity, ""),
                new Transform("", kIdentity, " "),
                new Transform(" ", kIdentity, " "),
                new Transform("", kOmitFirst1, ""),
                new Transform("", kUppercaseFirst, " "),
                new Transform("", kIdentity, " the "),
                new Transform(" ", kIdentity, ""),
                new Transform("s ", kIdentity, " "),
                new Transform("", kIdentity, " of "),
                new Transform("", kUppercaseFirst, ""),
                new Transform("", kIdentity, " and "),
                new Transform("", kOmitFirst2, ""),
                new Transform("", kOmitLast1, ""),
                new Transform(", ", kIdentity, " "),
                new Transform("", kIdentity, ", "),
                new Transform(" ", kUppercaseFirst, " "),
                new Transform("", kIdentity, " in "),
                new Transform("", kIdentity, " to "),
                new Transform("e ", kIdentity, " "),
                new Transform("", kIdentity, '"'),
                new Transform("", kIdentity, "."),
                new Transform("", kIdentity, '">'),
                new Transform("", kIdentity, "\n"),
                new Transform("", kOmitLast3, ""),
                new Transform("", kIdentity, "]"),
                new Transform("", kIdentity, " for "),
                new Transform("", kOmitFirst3, ""),
                new Transform("", kOmitLast2, ""),
                new Transform("", kIdentity, " a "),
                new Transform("", kIdentity, " that "),
                new Transform(" ", kUppercaseFirst, ""),
                new Transform("", kIdentity, ". "),
                new Transform(".", kIdentity, ""),
                new Transform(" ", kIdentity, ", "),
                new Transform("", kOmitFirst4, ""),
                new Transform("", kIdentity, " with "),
                new Transform("", kIdentity, "'"),
                new Transform("", kIdentity, " from "),
                new Transform("", kIdentity, " by "),
                new Transform("", kOmitFirst5, ""),
                new Transform("", kOmitFirst6, ""),
                new Transform(" the ", kIdentity, ""),
                new Transform("", kOmitLast4, ""),
                new Transform("", kIdentity, ". The "),
                new Transform("", kUppercaseAll, ""),
                new Transform("", kIdentity, " on "),
                new Transform("", kIdentity, " as "),
                new Transform("", kIdentity, " is "),
                new Transform("", kOmitLast7, ""),
                new Transform("", kOmitLast1, "ing "),
                new Transform("", kIdentity, "\n	"),
                new Transform("", kIdentity, ":"),
                new Transform(" ", kIdentity, ". "),
                new Transform("", kIdentity, "ed "),
                new Transform("", kOmitFirst9, ""),
                new Transform("", kOmitFirst7, ""),
                new Transform("", kOmitLast6, ""),
                new Transform("", kIdentity, "("),
                new Transform("", kUppercaseFirst, ", "),
                new Transform("", kOmitLast8, ""),
                new Transform("", kIdentity, " at "),
                new Transform("", kIdentity, "ly "),
                new Transform(" the ", kIdentity, " of "),
                new Transform("", kOmitLast5, ""),
                new Transform("", kOmitLast9, ""),
                new Transform(" ", kUppercaseFirst, ", "),
                new Transform("", kUppercaseFirst, '"'),
                new Transform(".", kIdentity, "("),
                new Transform("", kUppercaseAll, " "),
                new Transform("", kUppercaseFirst, '">'),
                new Transform("", kIdentity, '="'),
                new Transform(" ", kIdentity, "."),
                new Transform(".com/", kIdentity, ""),
                new Transform(" the ", kIdentity, " of the "),
                new Transform("", kUppercaseFirst, "'"),
                new Transform("", kIdentity, ". This "),
                new Transform("", kIdentity, ","),
                new Transform(".", kIdentity, " "),
                new Transform("", kUppercaseFirst, "("),
                new Transform("", kUppercaseFirst, "."),
                new Transform("", kIdentity, " not "),
                new Transform(" ", kIdentity, '="'),
                new Transform("", kIdentity, "er "),
                new Transform(" ", kUppercaseAll, " "),
                new Transform("", kIdentity, "al "),
                new Transform(" ", kUppercaseAll, ""),
                new Transform("", kIdentity, "='"),
                new Transform("", kUppercaseAll, '"'),
                new Transform("", kUppercaseFirst, ". "),
                new Transform(" ", kIdentity, "("),
                new Transform("", kIdentity, "ful "),
                new Transform(" ", kUppercaseFirst, ". "),
                new Transform("", kIdentity, "ive "),
                new Transform("", kIdentity, "less "),
                new Transform("", kUppercaseAll, "'"),
                new Transform("", kIdentity, "est "),
                new Transform(" ", kUppercaseFirst, "."),
                new Transform("", kUppercaseAll, '">'),
                new Transform(" ", kIdentity, "='"),
                new Transform("", kUppercaseFirst, ","),
                new Transform("", kIdentity, "ize "),
                new Transform("", kUppercaseAll, "."),
                new Transform("\xC2\xA0", kIdentity, ""),
                new Transform(" ", kIdentity, ","),
                new Transform("", kUppercaseFirst, '="'),
                new Transform("", kUppercaseAll, '="'),
                new Transform("", kIdentity, "ous "),
                new Transform("", kUppercaseAll, ", "),
                new Transform("", kUppercaseFirst, "='"),
                new Transform(" ", kUppercaseFirst, ","),
                new Transform(" ", kUppercaseAll, '="'),
                new Transform(" ", kUppercaseAll, ", "),
                new Transform("", kUppercaseAll, ","),
                new Transform("", kUppercaseAll, "("),
                new Transform("", kUppercaseAll, ". "),
                new Transform(" ", kUppercaseAll, "."),
                new Transform("", kUppercaseAll, "='"),
                new Transform(" ", kUppercaseAll, ". "),
                new Transform(" ", kUppercaseFirst, '="'),
                new Transform(" ", kUppercaseAll, "='"),
                new Transform(" ", kUppercaseFirst, "='")
              ];
              exports3.kTransforms = kTransforms;
              exports3.kNumTransforms = kTransforms.length;
              function ToUpperCase(p3, i2) {
                if (p3[i2] < 192) {
                  if (p3[i2] >= 97 && p3[i2] <= 122) {
                    p3[i2] ^= 32;
                  }
                  return 1;
                }
                if (p3[i2] < 224) {
                  p3[i2 + 1] ^= 32;
                  return 2;
                }
                p3[i2 + 2] ^= 5;
                return 3;
              }
              exports3.transformDictionaryWord = function(dst, idx, word, len, transform) {
                var prefix = kTransforms[transform].prefix;
                var suffix = kTransforms[transform].suffix;
                var t3 = kTransforms[transform].transform;
                var skip = t3 < kOmitFirst1 ? 0 : t3 - (kOmitFirst1 - 1);
                var i2 = 0;
                var start_idx = idx;
                var uppercase;
                if (skip > len) {
                  skip = len;
                }
                var prefix_pos = 0;
                while (prefix_pos < prefix.length) {
                  dst[idx++] = prefix[prefix_pos++];
                }
                word += skip;
                len -= skip;
                if (t3 <= kOmitLast9) {
                  len -= t3;
                }
                for (i2 = 0; i2 < len; i2++) {
                  dst[idx++] = BrotliDictionary.dictionary[word + i2];
                }
                uppercase = idx - len;
                if (t3 === kUppercaseFirst) {
                  ToUpperCase(dst, uppercase);
                } else if (t3 === kUppercaseAll) {
                  while (len > 0) {
                    var step = ToUpperCase(dst, uppercase);
                    uppercase += step;
                    len -= step;
                  }
                }
                var suffix_pos = 0;
                while (suffix_pos < suffix.length) {
                  dst[idx++] = suffix[suffix_pos++];
                }
                return idx - start_idx;
              };
            },
            { "./dictionary": 6 }
          ],
          12: [
            function(require2, module3, exports3) {
              module3.exports = require2("./dec/decode").BrotliDecompressBuffer;
            },
            { "./dec/decode": 3 }
          ]
        },
        {},
        [12]
      )(12);
    });
  }
});
var unbrotli_default = require_unbrotli();

// packages/global-styles-ui/build-module/font-library/lib/inflate.js
var __getOwnPropNames3 = Object.getOwnPropertyNames;
var __require3 = /* @__PURE__ */ ((x2) => typeof __require !== "undefined" ? __require : typeof Proxy !== "undefined" ? new Proxy(x2, {
  get: (a2, b2) => (typeof __require !== "undefined" ? __require : a2)[b2]
}) : x2)(function(x2) {
  if (typeof __require !== "undefined") return __require.apply(this, arguments);
  throw Error('Dynamic require of "' + x2 + '" is not supported');
});
var __commonJS3 = (cb, mod) => function __require22() {
  return mod || (0, cb[__getOwnPropNames3(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var require_inflate = __commonJS3({
  "packages/global-styles-ui/src/font-library/lib/inflate.js"(exports, module) {
    (function(f2) {
      if (typeof exports === "object" && typeof module !== "undefined") {
        module.exports = f2();
      } else if (typeof define === "function" && define.amd) {
        define([], f2);
      } else {
        var g2;
        if (typeof window !== "undefined") {
          g2 = window;
        } else if (typeof global !== "undefined") {
          g2 = global;
        } else if (typeof self !== "undefined") {
          g2 = self;
        } else {
          g2 = this;
        }
        g2.pako = f2();
      }
    })(function() {
      var define2, module2, exports2;
      return (/* @__PURE__ */ (function() {
        function r3(e2, n2, t3) {
          function o3(i22, f2) {
            if (!n2[i22]) {
              if (!e2[i22]) {
                var c2 = "function" == typeof __require3 && __require3;
                if (!f2 && c2) return c2(i22, true);
                if (u2) return u2(i22, true);
                var a2 = new Error("Cannot find module '" + i22 + "'");
                throw a2.code = "MODULE_NOT_FOUND", a2;
              }
              var p3 = n2[i22] = { exports: {} };
              e2[i22][0].call(
                p3.exports,
                function(r22) {
                  var n22 = e2[i22][1][r22];
                  return o3(n22 || r22);
                },
                p3,
                p3.exports,
                r3,
                e2,
                n2,
                t3
              );
            }
            return n2[i22].exports;
          }
          for (var u2 = "function" == typeof __require3 && __require3, i2 = 0; i2 < t3.length; i2++)
            o3(t3[i2]);
          return o3;
        }
        return r3;
      })())(
        {
          1: [
            function(require2, module3, exports3) {
              "use strict";
              var TYPED_OK = typeof Uint8Array !== "undefined" && typeof Uint16Array !== "undefined" && typeof Int32Array !== "undefined";
              function _has(obj, key) {
                return Object.prototype.hasOwnProperty.call(obj, key);
              }
              exports3.assign = function(obj) {
                var sources = Array.prototype.slice.call(
                  arguments,
                  1
                );
                while (sources.length) {
                  var source = sources.shift();
                  if (!source) {
                    continue;
                  }
                  if (typeof source !== "object") {
                    throw new TypeError(
                      source + "must be non-object"
                    );
                  }
                  for (var p3 in source) {
                    if (_has(source, p3)) {
                      obj[p3] = source[p3];
                    }
                  }
                }
                return obj;
              };
              exports3.shrinkBuf = function(buf, size) {
                if (buf.length === size) {
                  return buf;
                }
                if (buf.subarray) {
                  return buf.subarray(0, size);
                }
                buf.length = size;
                return buf;
              };
              var fnTyped = {
                arraySet: function(dest, src, src_offs, len, dest_offs) {
                  if (src.subarray && dest.subarray) {
                    dest.set(
                      src.subarray(src_offs, src_offs + len),
                      dest_offs
                    );
                    return;
                  }
                  for (var i2 = 0; i2 < len; i2++) {
                    dest[dest_offs + i2] = src[src_offs + i2];
                  }
                },
                // Join array of chunks to single array.
                flattenChunks: function(chunks) {
                  var i2, l2, len, pos, chunk, result;
                  len = 0;
                  for (i2 = 0, l2 = chunks.length; i2 < l2; i2++) {
                    len += chunks[i2].length;
                  }
                  result = new Uint8Array(len);
                  pos = 0;
                  for (i2 = 0, l2 = chunks.length; i2 < l2; i2++) {
                    chunk = chunks[i2];
                    result.set(chunk, pos);
                    pos += chunk.length;
                  }
                  return result;
                }
              };
              var fnUntyped = {
                arraySet: function(dest, src, src_offs, len, dest_offs) {
                  for (var i2 = 0; i2 < len; i2++) {
                    dest[dest_offs + i2] = src[src_offs + i2];
                  }
                },
                // Join array of chunks to single array.
                flattenChunks: function(chunks) {
                  return [].concat.apply([], chunks);
                }
              };
              exports3.setTyped = function(on) {
                if (on) {
                  exports3.Buf8 = Uint8Array;
                  exports3.Buf16 = Uint16Array;
                  exports3.Buf32 = Int32Array;
                  exports3.assign(exports3, fnTyped);
                } else {
                  exports3.Buf8 = Array;
                  exports3.Buf16 = Array;
                  exports3.Buf32 = Array;
                  exports3.assign(exports3, fnUntyped);
                }
              };
              exports3.setTyped(TYPED_OK);
            },
            {}
          ],
          2: [
            function(require2, module3, exports3) {
              "use strict";
              var utils = require2("./common");
              var STR_APPLY_OK = true;
              var STR_APPLY_UIA_OK = true;
              try {
                String.fromCharCode.apply(null, [0]);
              } catch (__42) {
                STR_APPLY_OK = false;
              }
              try {
                String.fromCharCode.apply(null, new Uint8Array(1));
              } catch (__42) {
                STR_APPLY_UIA_OK = false;
              }
              var _utf8len = new utils.Buf8(256);
              for (var q = 0; q < 256; q++) {
                _utf8len[q] = q >= 252 ? 6 : q >= 248 ? 5 : q >= 240 ? 4 : q >= 224 ? 3 : q >= 192 ? 2 : 1;
              }
              _utf8len[254] = _utf8len[254] = 1;
              exports3.string2buf = function(str) {
                var buf, c2, c22, m_pos, i2, str_len = str.length, buf_len = 0;
                for (m_pos = 0; m_pos < str_len; m_pos++) {
                  c2 = str.charCodeAt(m_pos);
                  if ((c2 & 64512) === 55296 && m_pos + 1 < str_len) {
                    c22 = str.charCodeAt(m_pos + 1);
                    if ((c22 & 64512) === 56320) {
                      c2 = 65536 + (c2 - 55296 << 10) + (c22 - 56320);
                      m_pos++;
                    }
                  }
                  buf_len += c2 < 128 ? 1 : c2 < 2048 ? 2 : c2 < 65536 ? 3 : 4;
                }
                buf = new utils.Buf8(buf_len);
                for (i2 = 0, m_pos = 0; i2 < buf_len; m_pos++) {
                  c2 = str.charCodeAt(m_pos);
                  if ((c2 & 64512) === 55296 && m_pos + 1 < str_len) {
                    c22 = str.charCodeAt(m_pos + 1);
                    if ((c22 & 64512) === 56320) {
                      c2 = 65536 + (c2 - 55296 << 10) + (c22 - 56320);
                      m_pos++;
                    }
                  }
                  if (c2 < 128) {
                    buf[i2++] = c2;
                  } else if (c2 < 2048) {
                    buf[i2++] = 192 | c2 >>> 6;
                    buf[i2++] = 128 | c2 & 63;
                  } else if (c2 < 65536) {
                    buf[i2++] = 224 | c2 >>> 12;
                    buf[i2++] = 128 | c2 >>> 6 & 63;
                    buf[i2++] = 128 | c2 & 63;
                  } else {
                    buf[i2++] = 240 | c2 >>> 18;
                    buf[i2++] = 128 | c2 >>> 12 & 63;
                    buf[i2++] = 128 | c2 >>> 6 & 63;
                    buf[i2++] = 128 | c2 & 63;
                  }
                }
                return buf;
              };
              function buf2binstring(buf, len) {
                if (len < 65534) {
                  if (buf.subarray && STR_APPLY_UIA_OK || !buf.subarray && STR_APPLY_OK) {
                    return String.fromCharCode.apply(
                      null,
                      utils.shrinkBuf(buf, len)
                    );
                  }
                }
                var result = "";
                for (var i2 = 0; i2 < len; i2++) {
                  result += String.fromCharCode(buf[i2]);
                }
                return result;
              }
              exports3.buf2binstring = function(buf) {
                return buf2binstring(buf, buf.length);
              };
              exports3.binstring2buf = function(str) {
                var buf = new utils.Buf8(str.length);
                for (var i2 = 0, len = buf.length; i2 < len; i2++) {
                  buf[i2] = str.charCodeAt(i2);
                }
                return buf;
              };
              exports3.buf2string = function(buf, max) {
                var i2, out, c2, c_len;
                var len = max || buf.length;
                var utf16buf = new Array(len * 2);
                for (out = 0, i2 = 0; i2 < len; ) {
                  c2 = buf[i2++];
                  if (c2 < 128) {
                    utf16buf[out++] = c2;
                    continue;
                  }
                  c_len = _utf8len[c2];
                  if (c_len > 4) {
                    utf16buf[out++] = 65533;
                    i2 += c_len - 1;
                    continue;
                  }
                  c2 &= c_len === 2 ? 31 : c_len === 3 ? 15 : 7;
                  while (c_len > 1 && i2 < len) {
                    c2 = c2 << 6 | buf[i2++] & 63;
                    c_len--;
                  }
                  if (c_len > 1) {
                    utf16buf[out++] = 65533;
                    continue;
                  }
                  if (c2 < 65536) {
                    utf16buf[out++] = c2;
                  } else {
                    c2 -= 65536;
                    utf16buf[out++] = 55296 | c2 >> 10 & 1023;
                    utf16buf[out++] = 56320 | c2 & 1023;
                  }
                }
                return buf2binstring(utf16buf, out);
              };
              exports3.utf8border = function(buf, max) {
                var pos;
                max = max || buf.length;
                if (max > buf.length) {
                  max = buf.length;
                }
                pos = max - 1;
                while (pos >= 0 && (buf[pos] & 192) === 128) {
                  pos--;
                }
                if (pos < 0) {
                  return max;
                }
                if (pos === 0) {
                  return max;
                }
                return pos + _utf8len[buf[pos]] > max ? pos : max;
              };
            },
            { "./common": 1 }
          ],
          3: [
            function(require2, module3, exports3) {
              "use strict";
              function adler32(adler, buf, len, pos) {
                var s1 = adler & 65535 | 0, s2 = adler >>> 16 & 65535 | 0, n2 = 0;
                while (len !== 0) {
                  n2 = len > 2e3 ? 2e3 : len;
                  len -= n2;
                  do {
                    s1 = s1 + buf[pos++] | 0;
                    s2 = s2 + s1 | 0;
                  } while (--n2);
                  s1 %= 65521;
                  s2 %= 65521;
                }
                return s1 | s2 << 16 | 0;
              }
              module3.exports = adler32;
            },
            {}
          ],
          4: [
            function(require2, module3, exports3) {
              "use strict";
              module3.exports = {
                /* Allowed flush values; see deflate() and inflate() below for details */
                Z_NO_FLUSH: 0,
                Z_PARTIAL_FLUSH: 1,
                Z_SYNC_FLUSH: 2,
                Z_FULL_FLUSH: 3,
                Z_FINISH: 4,
                Z_BLOCK: 5,
                Z_TREES: 6,
                /* Return codes for the compression/decompression functions. Negative values
                 * are errors, positive values are used for special but normal events.
                 */
                Z_OK: 0,
                Z_STREAM_END: 1,
                Z_NEED_DICT: 2,
                Z_ERRNO: -1,
                Z_STREAM_ERROR: -2,
                Z_DATA_ERROR: -3,
                //Z_MEM_ERROR:     -4,
                Z_BUF_ERROR: -5,
                //Z_VERSION_ERROR: -6,
                /* compression levels */
                Z_NO_COMPRESSION: 0,
                Z_BEST_SPEED: 1,
                Z_BEST_COMPRESSION: 9,
                Z_DEFAULT_COMPRESSION: -1,
                Z_FILTERED: 1,
                Z_HUFFMAN_ONLY: 2,
                Z_RLE: 3,
                Z_FIXED: 4,
                Z_DEFAULT_STRATEGY: 0,
                /* Possible values of the data_type field (though see inflate()) */
                Z_BINARY: 0,
                Z_TEXT: 1,
                //Z_ASCII:                1, // = Z_TEXT (deprecated)
                Z_UNKNOWN: 2,
                /* The deflate compression method */
                Z_DEFLATED: 8
                //Z_NULL:                 null // Use -1 or null inline, depending on var type
              };
            },
            {}
          ],
          5: [
            function(require2, module3, exports3) {
              "use strict";
              function makeTable() {
                var c2, table = [];
                for (var n2 = 0; n2 < 256; n2++) {
                  c2 = n2;
                  for (var k2 = 0; k2 < 8; k2++) {
                    c2 = c2 & 1 ? 3988292384 ^ c2 >>> 1 : c2 >>> 1;
                  }
                  table[n2] = c2;
                }
                return table;
              }
              var crcTable = makeTable();
              function crc32(crc, buf, len, pos) {
                var t3 = crcTable, end = pos + len;
                crc ^= -1;
                for (var i2 = pos; i2 < end; i2++) {
                  crc = crc >>> 8 ^ t3[(crc ^ buf[i2]) & 255];
                }
                return crc ^ -1;
              }
              module3.exports = crc32;
            },
            {}
          ],
          6: [
            function(require2, module3, exports3) {
              "use strict";
              function GZheader() {
                this.text = 0;
                this.time = 0;
                this.xflags = 0;
                this.os = 0;
                this.extra = null;
                this.extra_len = 0;
                this.name = "";
                this.comment = "";
                this.hcrc = 0;
                this.done = false;
              }
              module3.exports = GZheader;
            },
            {}
          ],
          7: [
            function(require2, module3, exports3) {
              "use strict";
              var BAD = 30;
              var TYPE = 12;
              module3.exports = function inflate_fast(strm, start) {
                var state;
                var _in;
                var last;
                var _out;
                var beg;
                var end;
                var dmax;
                var wsize;
                var whave;
                var wnext;
                var s_window;
                var hold;
                var bits;
                var lcode;
                var dcode;
                var lmask;
                var dmask;
                var here;
                var op;
                var len;
                var dist;
                var from;
                var from_source;
                var input, output;
                state = strm.state;
                _in = strm.next_in;
                input = strm.input;
                last = _in + (strm.avail_in - 5);
                _out = strm.next_out;
                output = strm.output;
                beg = _out - (start - strm.avail_out);
                end = _out + (strm.avail_out - 257);
                dmax = state.dmax;
                wsize = state.wsize;
                whave = state.whave;
                wnext = state.wnext;
                s_window = state.window;
                hold = state.hold;
                bits = state.bits;
                lcode = state.lencode;
                dcode = state.distcode;
                lmask = (1 << state.lenbits) - 1;
                dmask = (1 << state.distbits) - 1;
                top: do {
                  if (bits < 15) {
                    hold += input[_in++] << bits;
                    bits += 8;
                    hold += input[_in++] << bits;
                    bits += 8;
                  }
                  here = lcode[hold & lmask];
                  dolen: for (; ; ) {
                    op = here >>> 24;
                    hold >>>= op;
                    bits -= op;
                    op = here >>> 16 & 255;
                    if (op === 0) {
                      output[_out++] = here & 65535;
                    } else if (op & 16) {
                      len = here & 65535;
                      op &= 15;
                      if (op) {
                        if (bits < op) {
                          hold += input[_in++] << bits;
                          bits += 8;
                        }
                        len += hold & (1 << op) - 1;
                        hold >>>= op;
                        bits -= op;
                      }
                      if (bits < 15) {
                        hold += input[_in++] << bits;
                        bits += 8;
                        hold += input[_in++] << bits;
                        bits += 8;
                      }
                      here = dcode[hold & dmask];
                      dodist: for (; ; ) {
                        op = here >>> 24;
                        hold >>>= op;
                        bits -= op;
                        op = here >>> 16 & 255;
                        if (op & 16) {
                          dist = here & 65535;
                          op &= 15;
                          if (bits < op) {
                            hold += input[_in++] << bits;
                            bits += 8;
                            if (bits < op) {
                              hold += input[_in++] << bits;
                              bits += 8;
                            }
                          }
                          dist += hold & (1 << op) - 1;
                          if (dist > dmax) {
                            strm.msg = "invalid distance too far back";
                            state.mode = BAD;
                            break top;
                          }
                          hold >>>= op;
                          bits -= op;
                          op = _out - beg;
                          if (dist > op) {
                            op = dist - op;
                            if (op > whave) {
                              if (state.sane) {
                                strm.msg = "invalid distance too far back";
                                state.mode = BAD;
                                break top;
                              }
                            }
                            from = 0;
                            from_source = s_window;
                            if (wnext === 0) {
                              from += wsize - op;
                              if (op < len) {
                                len -= op;
                                do {
                                  output[_out++] = s_window[from++];
                                } while (--op);
                                from = _out - dist;
                                from_source = output;
                              }
                            } else if (wnext < op) {
                              from += wsize + wnext - op;
                              op -= wnext;
                              if (op < len) {
                                len -= op;
                                do {
                                  output[_out++] = s_window[from++];
                                } while (--op);
                                from = 0;
                                if (wnext < len) {
                                  op = wnext;
                                  len -= op;
                                  do {
                                    output[_out++] = s_window[from++];
                                  } while (--op);
                                  from = _out - dist;
                                  from_source = output;
                                }
                              }
                            } else {
                              from += wnext - op;
                              if (op < len) {
                                len -= op;
                                do {
                                  output[_out++] = s_window[from++];
                                } while (--op);
                                from = _out - dist;
                                from_source = output;
                              }
                            }
                            while (len > 2) {
                              output[_out++] = from_source[from++];
                              output[_out++] = from_source[from++];
                              output[_out++] = from_source[from++];
                              len -= 3;
                            }
                            if (len) {
                              output[_out++] = from_source[from++];
                              if (len > 1) {
                                output[_out++] = from_source[from++];
                              }
                            }
                          } else {
                            from = _out - dist;
                            do {
                              output[_out++] = output[from++];
                              output[_out++] = output[from++];
                              output[_out++] = output[from++];
                              len -= 3;
                            } while (len > 2);
                            if (len) {
                              output[_out++] = output[from++];
                              if (len > 1) {
                                output[_out++] = output[from++];
                              }
                            }
                          }
                        } else if ((op & 64) === 0) {
                          here = dcode[(here & 65535) + (hold & (1 << op) - 1)];
                          continue dodist;
                        } else {
                          strm.msg = "invalid distance code";
                          state.mode = BAD;
                          break top;
                        }
                        break;
                      }
                    } else if ((op & 64) === 0) {
                      here = lcode[(here & 65535) + (hold & (1 << op) - 1)];
                      continue dolen;
                    } else if (op & 32) {
                      state.mode = TYPE;
                      break top;
                    } else {
                      strm.msg = "invalid literal/length code";
                      state.mode = BAD;
                      break top;
                    }
                    break;
                  }
                } while (_in < last && _out < end);
                len = bits >> 3;
                _in -= len;
                bits -= len << 3;
                hold &= (1 << bits) - 1;
                strm.next_in = _in;
                strm.next_out = _out;
                strm.avail_in = _in < last ? 5 + (last - _in) : 5 - (_in - last);
                strm.avail_out = _out < end ? 257 + (end - _out) : 257 - (_out - end);
                state.hold = hold;
                state.bits = bits;
                return;
              };
            },
            {}
          ],
          8: [
            function(require2, module3, exports3) {
              "use strict";
              var utils = require2("../utils/common");
              var adler32 = require2("./adler32");
              var crc32 = require2("./crc32");
              var inflate_fast = require2("./inffast");
              var inflate_table = require2("./inftrees");
              var CODES = 0;
              var LENS = 1;
              var DISTS = 2;
              var Z_FINISH = 4;
              var Z_BLOCK = 5;
              var Z_TREES = 6;
              var Z_OK = 0;
              var Z_STREAM_END = 1;
              var Z_NEED_DICT = 2;
              var Z_STREAM_ERROR = -2;
              var Z_DATA_ERROR = -3;
              var Z_MEM_ERROR = -4;
              var Z_BUF_ERROR = -5;
              var Z_DEFLATED = 8;
              var HEAD = 1;
              var FLAGS = 2;
              var TIME = 3;
              var OS = 4;
              var EXLEN = 5;
              var EXTRA = 6;
              var NAME = 7;
              var COMMENT = 8;
              var HCRC = 9;
              var DICTID = 10;
              var DICT = 11;
              var TYPE = 12;
              var TYPEDO = 13;
              var STORED = 14;
              var COPY_ = 15;
              var COPY = 16;
              var TABLE = 17;
              var LENLENS = 18;
              var CODELENS = 19;
              var LEN_ = 20;
              var LEN = 21;
              var LENEXT = 22;
              var DIST = 23;
              var DISTEXT = 24;
              var MATCH = 25;
              var LIT = 26;
              var CHECK = 27;
              var LENGTH = 28;
              var DONE = 29;
              var BAD = 30;
              var MEM = 31;
              var SYNC = 32;
              var ENOUGH_LENS = 852;
              var ENOUGH_DISTS = 592;
              var MAX_WBITS = 15;
              var DEF_WBITS = MAX_WBITS;
              function zswap32(q) {
                return (q >>> 24 & 255) + (q >>> 8 & 65280) + ((q & 65280) << 8) + ((q & 255) << 24);
              }
              function InflateState() {
                this.mode = 0;
                this.last = false;
                this.wrap = 0;
                this.havedict = false;
                this.flags = 0;
                this.dmax = 0;
                this.check = 0;
                this.total = 0;
                this.head = null;
                this.wbits = 0;
                this.wsize = 0;
                this.whave = 0;
                this.wnext = 0;
                this.window = null;
                this.hold = 0;
                this.bits = 0;
                this.length = 0;
                this.offset = 0;
                this.extra = 0;
                this.lencode = null;
                this.distcode = null;
                this.lenbits = 0;
                this.distbits = 0;
                this.ncode = 0;
                this.nlen = 0;
                this.ndist = 0;
                this.have = 0;
                this.next = null;
                this.lens = new utils.Buf16(
                  320
                );
                this.work = new utils.Buf16(
                  288
                );
                this.lendyn = null;
                this.distdyn = null;
                this.sane = 0;
                this.back = 0;
                this.was = 0;
              }
              function inflateResetKeep(strm) {
                var state;
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                strm.total_in = strm.total_out = state.total = 0;
                strm.msg = "";
                if (state.wrap) {
                  strm.adler = state.wrap & 1;
                }
                state.mode = HEAD;
                state.last = 0;
                state.havedict = 0;
                state.dmax = 32768;
                state.head = null;
                state.hold = 0;
                state.bits = 0;
                state.lencode = state.lendyn = new utils.Buf32(
                  ENOUGH_LENS
                );
                state.distcode = state.distdyn = new utils.Buf32(
                  ENOUGH_DISTS
                );
                state.sane = 1;
                state.back = -1;
                return Z_OK;
              }
              function inflateReset(strm) {
                var state;
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                state.wsize = 0;
                state.whave = 0;
                state.wnext = 0;
                return inflateResetKeep(strm);
              }
              function inflateReset2(strm, windowBits) {
                var wrap;
                var state;
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                if (windowBits < 0) {
                  wrap = 0;
                  windowBits = -windowBits;
                } else {
                  wrap = (windowBits >> 4) + 1;
                  if (windowBits < 48) {
                    windowBits &= 15;
                  }
                }
                if (windowBits && (windowBits < 8 || windowBits > 15)) {
                  return Z_STREAM_ERROR;
                }
                if (state.window !== null && state.wbits !== windowBits) {
                  state.window = null;
                }
                state.wrap = wrap;
                state.wbits = windowBits;
                return inflateReset(strm);
              }
              function inflateInit2(strm, windowBits) {
                var ret;
                var state;
                if (!strm) {
                  return Z_STREAM_ERROR;
                }
                state = new InflateState();
                strm.state = state;
                state.window = null;
                ret = inflateReset2(strm, windowBits);
                if (ret !== Z_OK) {
                  strm.state = null;
                }
                return ret;
              }
              function inflateInit(strm) {
                return inflateInit2(strm, DEF_WBITS);
              }
              var virgin = true;
              var lenfix, distfix;
              function fixedtables(state) {
                if (virgin) {
                  var sym;
                  lenfix = new utils.Buf32(512);
                  distfix = new utils.Buf32(32);
                  sym = 0;
                  while (sym < 144) {
                    state.lens[sym++] = 8;
                  }
                  while (sym < 256) {
                    state.lens[sym++] = 9;
                  }
                  while (sym < 280) {
                    state.lens[sym++] = 7;
                  }
                  while (sym < 288) {
                    state.lens[sym++] = 8;
                  }
                  inflate_table(
                    LENS,
                    state.lens,
                    0,
                    288,
                    lenfix,
                    0,
                    state.work,
                    { bits: 9 }
                  );
                  sym = 0;
                  while (sym < 32) {
                    state.lens[sym++] = 5;
                  }
                  inflate_table(
                    DISTS,
                    state.lens,
                    0,
                    32,
                    distfix,
                    0,
                    state.work,
                    { bits: 5 }
                  );
                  virgin = false;
                }
                state.lencode = lenfix;
                state.lenbits = 9;
                state.distcode = distfix;
                state.distbits = 5;
              }
              function updatewindow(strm, src, end, copy) {
                var dist;
                var state = strm.state;
                if (state.window === null) {
                  state.wsize = 1 << state.wbits;
                  state.wnext = 0;
                  state.whave = 0;
                  state.window = new utils.Buf8(state.wsize);
                }
                if (copy >= state.wsize) {
                  utils.arraySet(
                    state.window,
                    src,
                    end - state.wsize,
                    state.wsize,
                    0
                  );
                  state.wnext = 0;
                  state.whave = state.wsize;
                } else {
                  dist = state.wsize - state.wnext;
                  if (dist > copy) {
                    dist = copy;
                  }
                  utils.arraySet(
                    state.window,
                    src,
                    end - copy,
                    dist,
                    state.wnext
                  );
                  copy -= dist;
                  if (copy) {
                    utils.arraySet(
                      state.window,
                      src,
                      end - copy,
                      copy,
                      0
                    );
                    state.wnext = copy;
                    state.whave = state.wsize;
                  } else {
                    state.wnext += dist;
                    if (state.wnext === state.wsize) {
                      state.wnext = 0;
                    }
                    if (state.whave < state.wsize) {
                      state.whave += dist;
                    }
                  }
                }
                return 0;
              }
              function inflate(strm, flush) {
                var state;
                var input, output;
                var next;
                var put;
                var have, left;
                var hold;
                var bits;
                var _in, _out;
                var copy;
                var from;
                var from_source;
                var here = 0;
                var here_bits, here_op, here_val;
                var last_bits, last_op, last_val;
                var len;
                var ret;
                var hbuf = new utils.Buf8(
                  4
                );
                var opts;
                var n2;
                var order = (
                  /* permutation of code lengths */
                  [
                    16,
                    17,
                    18,
                    0,
                    8,
                    7,
                    9,
                    6,
                    10,
                    5,
                    11,
                    4,
                    12,
                    3,
                    13,
                    2,
                    14,
                    1,
                    15
                  ]
                );
                if (!strm || !strm.state || !strm.output || !strm.input && strm.avail_in !== 0) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                if (state.mode === TYPE) {
                  state.mode = TYPEDO;
                }
                put = strm.next_out;
                output = strm.output;
                left = strm.avail_out;
                next = strm.next_in;
                input = strm.input;
                have = strm.avail_in;
                hold = state.hold;
                bits = state.bits;
                _in = have;
                _out = left;
                ret = Z_OK;
                inf_leave: for (; ; ) {
                  switch (state.mode) {
                    case HEAD:
                      if (state.wrap === 0) {
                        state.mode = TYPEDO;
                        break;
                      }
                      while (bits < 16) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if (state.wrap & 2 && hold === 35615) {
                        state.check = 0;
                        hbuf[0] = hold & 255;
                        hbuf[1] = hold >>> 8 & 255;
                        state.check = crc32(
                          state.check,
                          hbuf,
                          2,
                          0
                        );
                        hold = 0;
                        bits = 0;
                        state.mode = FLAGS;
                        break;
                      }
                      state.flags = 0;
                      if (state.head) {
                        state.head.done = false;
                      }
                      if (!(state.wrap & 1) || (((hold & 255) << 8) + (hold >> 8)) % 31) {
                        strm.msg = "incorrect header check";
                        state.mode = BAD;
                        break;
                      }
                      if ((hold & 15) !== Z_DEFLATED) {
                        strm.msg = "unknown compression method";
                        state.mode = BAD;
                        break;
                      }
                      hold >>>= 4;
                      bits -= 4;
                      len = (hold & 15) + 8;
                      if (state.wbits === 0) {
                        state.wbits = len;
                      } else if (len > state.wbits) {
                        strm.msg = "invalid window size";
                        state.mode = BAD;
                        break;
                      }
                      state.dmax = 1 << len;
                      strm.adler = state.check = 1;
                      state.mode = hold & 512 ? DICTID : TYPE;
                      hold = 0;
                      bits = 0;
                      break;
                    case FLAGS:
                      while (bits < 16) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      state.flags = hold;
                      if ((state.flags & 255) !== Z_DEFLATED) {
                        strm.msg = "unknown compression method";
                        state.mode = BAD;
                        break;
                      }
                      if (state.flags & 57344) {
                        strm.msg = "unknown header flags set";
                        state.mode = BAD;
                        break;
                      }
                      if (state.head) {
                        state.head.text = hold >> 8 & 1;
                      }
                      if (state.flags & 512) {
                        hbuf[0] = hold & 255;
                        hbuf[1] = hold >>> 8 & 255;
                        state.check = crc32(
                          state.check,
                          hbuf,
                          2,
                          0
                        );
                      }
                      hold = 0;
                      bits = 0;
                      state.mode = TIME;
                    /* falls through */
                    case TIME:
                      while (bits < 32) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if (state.head) {
                        state.head.time = hold;
                      }
                      if (state.flags & 512) {
                        hbuf[0] = hold & 255;
                        hbuf[1] = hold >>> 8 & 255;
                        hbuf[2] = hold >>> 16 & 255;
                        hbuf[3] = hold >>> 24 & 255;
                        state.check = crc32(
                          state.check,
                          hbuf,
                          4,
                          0
                        );
                      }
                      hold = 0;
                      bits = 0;
                      state.mode = OS;
                    /* falls through */
                    case OS:
                      while (bits < 16) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if (state.head) {
                        state.head.xflags = hold & 255;
                        state.head.os = hold >> 8;
                      }
                      if (state.flags & 512) {
                        hbuf[0] = hold & 255;
                        hbuf[1] = hold >>> 8 & 255;
                        state.check = crc32(
                          state.check,
                          hbuf,
                          2,
                          0
                        );
                      }
                      hold = 0;
                      bits = 0;
                      state.mode = EXLEN;
                    /* falls through */
                    case EXLEN:
                      if (state.flags & 1024) {
                        while (bits < 16) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        state.length = hold;
                        if (state.head) {
                          state.head.extra_len = hold;
                        }
                        if (state.flags & 512) {
                          hbuf[0] = hold & 255;
                          hbuf[1] = hold >>> 8 & 255;
                          state.check = crc32(
                            state.check,
                            hbuf,
                            2,
                            0
                          );
                        }
                        hold = 0;
                        bits = 0;
                      } else if (state.head) {
                        state.head.extra = null;
                      }
                      state.mode = EXTRA;
                    /* falls through */
                    case EXTRA:
                      if (state.flags & 1024) {
                        copy = state.length;
                        if (copy > have) {
                          copy = have;
                        }
                        if (copy) {
                          if (state.head) {
                            len = state.head.extra_len - state.length;
                            if (!state.head.extra) {
                              state.head.extra = new Array(
                                state.head.extra_len
                              );
                            }
                            utils.arraySet(
                              state.head.extra,
                              input,
                              next,
                              // extra field is limited to 65536 bytes
                              // - no need for additional size check
                              copy,
                              /*len + copy > state.head.extra_max - len ? state.head.extra_max : copy,*/
                              len
                            );
                          }
                          if (state.flags & 512) {
                            state.check = crc32(
                              state.check,
                              input,
                              copy,
                              next
                            );
                          }
                          have -= copy;
                          next += copy;
                          state.length -= copy;
                        }
                        if (state.length) {
                          break inf_leave;
                        }
                      }
                      state.length = 0;
                      state.mode = NAME;
                    /* falls through */
                    case NAME:
                      if (state.flags & 2048) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        copy = 0;
                        do {
                          len = input[next + copy++];
                          if (state.head && len && state.length < 65536) {
                            state.head.name += String.fromCharCode(len);
                          }
                        } while (len && copy < have);
                        if (state.flags & 512) {
                          state.check = crc32(
                            state.check,
                            input,
                            copy,
                            next
                          );
                        }
                        have -= copy;
                        next += copy;
                        if (len) {
                          break inf_leave;
                        }
                      } else if (state.head) {
                        state.head.name = null;
                      }
                      state.length = 0;
                      state.mode = COMMENT;
                    /* falls through */
                    case COMMENT:
                      if (state.flags & 4096) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        copy = 0;
                        do {
                          len = input[next + copy++];
                          if (state.head && len && state.length < 65536) {
                            state.head.comment += String.fromCharCode(len);
                          }
                        } while (len && copy < have);
                        if (state.flags & 512) {
                          state.check = crc32(
                            state.check,
                            input,
                            copy,
                            next
                          );
                        }
                        have -= copy;
                        next += copy;
                        if (len) {
                          break inf_leave;
                        }
                      } else if (state.head) {
                        state.head.comment = null;
                      }
                      state.mode = HCRC;
                    /* falls through */
                    case HCRC:
                      if (state.flags & 512) {
                        while (bits < 16) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        if (hold !== (state.check & 65535)) {
                          strm.msg = "header crc mismatch";
                          state.mode = BAD;
                          break;
                        }
                        hold = 0;
                        bits = 0;
                      }
                      if (state.head) {
                        state.head.hcrc = state.flags >> 9 & 1;
                        state.head.done = true;
                      }
                      strm.adler = state.check = 0;
                      state.mode = TYPE;
                      break;
                    case DICTID:
                      while (bits < 32) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      strm.adler = state.check = zswap32(hold);
                      hold = 0;
                      bits = 0;
                      state.mode = DICT;
                    /* falls through */
                    case DICT:
                      if (state.havedict === 0) {
                        strm.next_out = put;
                        strm.avail_out = left;
                        strm.next_in = next;
                        strm.avail_in = have;
                        state.hold = hold;
                        state.bits = bits;
                        return Z_NEED_DICT;
                      }
                      strm.adler = state.check = 1;
                      state.mode = TYPE;
                    /* falls through */
                    case TYPE:
                      if (flush === Z_BLOCK || flush === Z_TREES) {
                        break inf_leave;
                      }
                    /* falls through */
                    case TYPEDO:
                      if (state.last) {
                        hold >>>= bits & 7;
                        bits -= bits & 7;
                        state.mode = CHECK;
                        break;
                      }
                      while (bits < 3) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      state.last = hold & 1;
                      hold >>>= 1;
                      bits -= 1;
                      switch (hold & 3) {
                        case 0:
                          state.mode = STORED;
                          break;
                        case 1:
                          fixedtables(state);
                          state.mode = LEN_;
                          if (flush === Z_TREES) {
                            hold >>>= 2;
                            bits -= 2;
                            break inf_leave;
                          }
                          break;
                        case 2:
                          state.mode = TABLE;
                          break;
                        case 3:
                          strm.msg = "invalid block type";
                          state.mode = BAD;
                      }
                      hold >>>= 2;
                      bits -= 2;
                      break;
                    case STORED:
                      hold >>>= bits & 7;
                      bits -= bits & 7;
                      while (bits < 32) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if ((hold & 65535) !== (hold >>> 16 ^ 65535)) {
                        strm.msg = "invalid stored block lengths";
                        state.mode = BAD;
                        break;
                      }
                      state.length = hold & 65535;
                      hold = 0;
                      bits = 0;
                      state.mode = COPY_;
                      if (flush === Z_TREES) {
                        break inf_leave;
                      }
                    /* falls through */
                    case COPY_:
                      state.mode = COPY;
                    /* falls through */
                    case COPY:
                      copy = state.length;
                      if (copy) {
                        if (copy > have) {
                          copy = have;
                        }
                        if (copy > left) {
                          copy = left;
                        }
                        if (copy === 0) {
                          break inf_leave;
                        }
                        utils.arraySet(
                          output,
                          input,
                          next,
                          copy,
                          put
                        );
                        have -= copy;
                        next += copy;
                        left -= copy;
                        put += copy;
                        state.length -= copy;
                        break;
                      }
                      state.mode = TYPE;
                      break;
                    case TABLE:
                      while (bits < 14) {
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      state.nlen = (hold & 31) + 257;
                      hold >>>= 5;
                      bits -= 5;
                      state.ndist = (hold & 31) + 1;
                      hold >>>= 5;
                      bits -= 5;
                      state.ncode = (hold & 15) + 4;
                      hold >>>= 4;
                      bits -= 4;
                      if (state.nlen > 286 || state.ndist > 30) {
                        strm.msg = "too many length or distance symbols";
                        state.mode = BAD;
                        break;
                      }
                      state.have = 0;
                      state.mode = LENLENS;
                    /* falls through */
                    case LENLENS:
                      while (state.have < state.ncode) {
                        while (bits < 3) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        state.lens[order[state.have++]] = hold & 7;
                        hold >>>= 3;
                        bits -= 3;
                      }
                      while (state.have < 19) {
                        state.lens[order[state.have++]] = 0;
                      }
                      state.lencode = state.lendyn;
                      state.lenbits = 7;
                      opts = { bits: state.lenbits };
                      ret = inflate_table(
                        CODES,
                        state.lens,
                        0,
                        19,
                        state.lencode,
                        0,
                        state.work,
                        opts
                      );
                      state.lenbits = opts.bits;
                      if (ret) {
                        strm.msg = "invalid code lengths set";
                        state.mode = BAD;
                        break;
                      }
                      state.have = 0;
                      state.mode = CODELENS;
                    /* falls through */
                    case CODELENS:
                      while (state.have < state.nlen + state.ndist) {
                        for (; ; ) {
                          here = state.lencode[hold & (1 << state.lenbits) - 1];
                          here_bits = here >>> 24;
                          here_op = here >>> 16 & 255;
                          here_val = here & 65535;
                          if (here_bits <= bits) {
                            break;
                          }
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        if (here_val < 16) {
                          hold >>>= here_bits;
                          bits -= here_bits;
                          state.lens[state.have++] = here_val;
                        } else {
                          if (here_val === 16) {
                            n2 = here_bits + 2;
                            while (bits < n2) {
                              if (have === 0) {
                                break inf_leave;
                              }
                              have--;
                              hold += input[next++] << bits;
                              bits += 8;
                            }
                            hold >>>= here_bits;
                            bits -= here_bits;
                            if (state.have === 0) {
                              strm.msg = "invalid bit length repeat";
                              state.mode = BAD;
                              break;
                            }
                            len = state.lens[state.have - 1];
                            copy = 3 + (hold & 3);
                            hold >>>= 2;
                            bits -= 2;
                          } else if (here_val === 17) {
                            n2 = here_bits + 3;
                            while (bits < n2) {
                              if (have === 0) {
                                break inf_leave;
                              }
                              have--;
                              hold += input[next++] << bits;
                              bits += 8;
                            }
                            hold >>>= here_bits;
                            bits -= here_bits;
                            len = 0;
                            copy = 3 + (hold & 7);
                            hold >>>= 3;
                            bits -= 3;
                          } else {
                            n2 = here_bits + 7;
                            while (bits < n2) {
                              if (have === 0) {
                                break inf_leave;
                              }
                              have--;
                              hold += input[next++] << bits;
                              bits += 8;
                            }
                            hold >>>= here_bits;
                            bits -= here_bits;
                            len = 0;
                            copy = 11 + (hold & 127);
                            hold >>>= 7;
                            bits -= 7;
                          }
                          if (state.have + copy > state.nlen + state.ndist) {
                            strm.msg = "invalid bit length repeat";
                            state.mode = BAD;
                            break;
                          }
                          while (copy--) {
                            state.lens[state.have++] = len;
                          }
                        }
                      }
                      if (state.mode === BAD) {
                        break;
                      }
                      if (state.lens[256] === 0) {
                        strm.msg = "invalid code -- missing end-of-block";
                        state.mode = BAD;
                        break;
                      }
                      state.lenbits = 9;
                      opts = { bits: state.lenbits };
                      ret = inflate_table(
                        LENS,
                        state.lens,
                        0,
                        state.nlen,
                        state.lencode,
                        0,
                        state.work,
                        opts
                      );
                      state.lenbits = opts.bits;
                      if (ret) {
                        strm.msg = "invalid literal/lengths set";
                        state.mode = BAD;
                        break;
                      }
                      state.distbits = 6;
                      state.distcode = state.distdyn;
                      opts = { bits: state.distbits };
                      ret = inflate_table(
                        DISTS,
                        state.lens,
                        state.nlen,
                        state.ndist,
                        state.distcode,
                        0,
                        state.work,
                        opts
                      );
                      state.distbits = opts.bits;
                      if (ret) {
                        strm.msg = "invalid distances set";
                        state.mode = BAD;
                        break;
                      }
                      state.mode = LEN_;
                      if (flush === Z_TREES) {
                        break inf_leave;
                      }
                    /* falls through */
                    case LEN_:
                      state.mode = LEN;
                    /* falls through */
                    case LEN:
                      if (have >= 6 && left >= 258) {
                        strm.next_out = put;
                        strm.avail_out = left;
                        strm.next_in = next;
                        strm.avail_in = have;
                        state.hold = hold;
                        state.bits = bits;
                        inflate_fast(strm, _out);
                        put = strm.next_out;
                        output = strm.output;
                        left = strm.avail_out;
                        next = strm.next_in;
                        input = strm.input;
                        have = strm.avail_in;
                        hold = state.hold;
                        bits = state.bits;
                        if (state.mode === TYPE) {
                          state.back = -1;
                        }
                        break;
                      }
                      state.back = 0;
                      for (; ; ) {
                        here = state.lencode[hold & (1 << state.lenbits) - 1];
                        here_bits = here >>> 24;
                        here_op = here >>> 16 & 255;
                        here_val = here & 65535;
                        if (here_bits <= bits) {
                          break;
                        }
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if (here_op && (here_op & 240) === 0) {
                        last_bits = here_bits;
                        last_op = here_op;
                        last_val = here_val;
                        for (; ; ) {
                          here = state.lencode[last_val + ((hold & (1 << last_bits + last_op) - 1) >> last_bits)];
                          here_bits = here >>> 24;
                          here_op = here >>> 16 & 255;
                          here_val = here & 65535;
                          if (last_bits + here_bits <= bits) {
                            break;
                          }
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        hold >>>= last_bits;
                        bits -= last_bits;
                        state.back += last_bits;
                      }
                      hold >>>= here_bits;
                      bits -= here_bits;
                      state.back += here_bits;
                      state.length = here_val;
                      if (here_op === 0) {
                        state.mode = LIT;
                        break;
                      }
                      if (here_op & 32) {
                        state.back = -1;
                        state.mode = TYPE;
                        break;
                      }
                      if (here_op & 64) {
                        strm.msg = "invalid literal/length code";
                        state.mode = BAD;
                        break;
                      }
                      state.extra = here_op & 15;
                      state.mode = LENEXT;
                    /* falls through */
                    case LENEXT:
                      if (state.extra) {
                        n2 = state.extra;
                        while (bits < n2) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        state.length += hold & (1 << state.extra) - 1;
                        hold >>>= state.extra;
                        bits -= state.extra;
                        state.back += state.extra;
                      }
                      state.was = state.length;
                      state.mode = DIST;
                    /* falls through */
                    case DIST:
                      for (; ; ) {
                        here = state.distcode[hold & (1 << state.distbits) - 1];
                        here_bits = here >>> 24;
                        here_op = here >>> 16 & 255;
                        here_val = here & 65535;
                        if (here_bits <= bits) {
                          break;
                        }
                        if (have === 0) {
                          break inf_leave;
                        }
                        have--;
                        hold += input[next++] << bits;
                        bits += 8;
                      }
                      if ((here_op & 240) === 0) {
                        last_bits = here_bits;
                        last_op = here_op;
                        last_val = here_val;
                        for (; ; ) {
                          here = state.distcode[last_val + ((hold & (1 << last_bits + last_op) - 1) >> last_bits)];
                          here_bits = here >>> 24;
                          here_op = here >>> 16 & 255;
                          here_val = here & 65535;
                          if (last_bits + here_bits <= bits) {
                            break;
                          }
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        hold >>>= last_bits;
                        bits -= last_bits;
                        state.back += last_bits;
                      }
                      hold >>>= here_bits;
                      bits -= here_bits;
                      state.back += here_bits;
                      if (here_op & 64) {
                        strm.msg = "invalid distance code";
                        state.mode = BAD;
                        break;
                      }
                      state.offset = here_val;
                      state.extra = here_op & 15;
                      state.mode = DISTEXT;
                    /* falls through */
                    case DISTEXT:
                      if (state.extra) {
                        n2 = state.extra;
                        while (bits < n2) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        state.offset += hold & (1 << state.extra) - 1;
                        hold >>>= state.extra;
                        bits -= state.extra;
                        state.back += state.extra;
                      }
                      if (state.offset > state.dmax) {
                        strm.msg = "invalid distance too far back";
                        state.mode = BAD;
                        break;
                      }
                      state.mode = MATCH;
                    /* falls through */
                    case MATCH:
                      if (left === 0) {
                        break inf_leave;
                      }
                      copy = _out - left;
                      if (state.offset > copy) {
                        copy = state.offset - copy;
                        if (copy > state.whave) {
                          if (state.sane) {
                            strm.msg = "invalid distance too far back";
                            state.mode = BAD;
                            break;
                          }
                        }
                        if (copy > state.wnext) {
                          copy -= state.wnext;
                          from = state.wsize - copy;
                        } else {
                          from = state.wnext - copy;
                        }
                        if (copy > state.length) {
                          copy = state.length;
                        }
                        from_source = state.window;
                      } else {
                        from_source = output;
                        from = put - state.offset;
                        copy = state.length;
                      }
                      if (copy > left) {
                        copy = left;
                      }
                      left -= copy;
                      state.length -= copy;
                      do {
                        output[put++] = from_source[from++];
                      } while (--copy);
                      if (state.length === 0) {
                        state.mode = LEN;
                      }
                      break;
                    case LIT:
                      if (left === 0) {
                        break inf_leave;
                      }
                      output[put++] = state.length;
                      left--;
                      state.mode = LEN;
                      break;
                    case CHECK:
                      if (state.wrap) {
                        while (bits < 32) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold |= input[next++] << bits;
                          bits += 8;
                        }
                        _out -= left;
                        strm.total_out += _out;
                        state.total += _out;
                        if (_out) {
                          strm.adler = state.check = /*UPDATE(state.check, put - _out, _out);*/
                          state.flags ? crc32(
                            state.check,
                            output,
                            _out,
                            put - _out
                          ) : adler32(
                            state.check,
                            output,
                            _out,
                            put - _out
                          );
                        }
                        _out = left;
                        if ((state.flags ? hold : zswap32(hold)) !== state.check) {
                          strm.msg = "incorrect data check";
                          state.mode = BAD;
                          break;
                        }
                        hold = 0;
                        bits = 0;
                      }
                      state.mode = LENGTH;
                    /* falls through */
                    case LENGTH:
                      if (state.wrap && state.flags) {
                        while (bits < 32) {
                          if (have === 0) {
                            break inf_leave;
                          }
                          have--;
                          hold += input[next++] << bits;
                          bits += 8;
                        }
                        if (hold !== (state.total & 4294967295)) {
                          strm.msg = "incorrect length check";
                          state.mode = BAD;
                          break;
                        }
                        hold = 0;
                        bits = 0;
                      }
                      state.mode = DONE;
                    /* falls through */
                    case DONE:
                      ret = Z_STREAM_END;
                      break inf_leave;
                    case BAD:
                      ret = Z_DATA_ERROR;
                      break inf_leave;
                    case MEM:
                      return Z_MEM_ERROR;
                    case SYNC:
                    /* falls through */
                    default:
                      return Z_STREAM_ERROR;
                  }
                }
                strm.next_out = put;
                strm.avail_out = left;
                strm.next_in = next;
                strm.avail_in = have;
                state.hold = hold;
                state.bits = bits;
                if (state.wsize || _out !== strm.avail_out && state.mode < BAD && (state.mode < CHECK || flush !== Z_FINISH)) {
                  if (updatewindow(
                    strm,
                    strm.output,
                    strm.next_out,
                    _out - strm.avail_out
                  )) {
                    state.mode = MEM;
                    return Z_MEM_ERROR;
                  }
                }
                _in -= strm.avail_in;
                _out -= strm.avail_out;
                strm.total_in += _in;
                strm.total_out += _out;
                state.total += _out;
                if (state.wrap && _out) {
                  strm.adler = state.check = /*UPDATE(state.check, strm.next_out - _out, _out);*/
                  state.flags ? crc32(
                    state.check,
                    output,
                    _out,
                    strm.next_out - _out
                  ) : adler32(
                    state.check,
                    output,
                    _out,
                    strm.next_out - _out
                  );
                }
                strm.data_type = state.bits + (state.last ? 64 : 0) + (state.mode === TYPE ? 128 : 0) + (state.mode === LEN_ || state.mode === COPY_ ? 256 : 0);
                if ((_in === 0 && _out === 0 || flush === Z_FINISH) && ret === Z_OK) {
                  ret = Z_BUF_ERROR;
                }
                return ret;
              }
              function inflateEnd(strm) {
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                var state = strm.state;
                if (state.window) {
                  state.window = null;
                }
                strm.state = null;
                return Z_OK;
              }
              function inflateGetHeader(strm, head2) {
                var state;
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                if ((state.wrap & 2) === 0) {
                  return Z_STREAM_ERROR;
                }
                state.head = head2;
                head2.done = false;
                return Z_OK;
              }
              function inflateSetDictionary(strm, dictionary) {
                var dictLength = dictionary.length;
                var state;
                var dictid;
                var ret;
                if (!strm || !strm.state) {
                  return Z_STREAM_ERROR;
                }
                state = strm.state;
                if (state.wrap !== 0 && state.mode !== DICT) {
                  return Z_STREAM_ERROR;
                }
                if (state.mode === DICT) {
                  dictid = 1;
                  dictid = adler32(
                    dictid,
                    dictionary,
                    dictLength,
                    0
                  );
                  if (dictid !== state.check) {
                    return Z_DATA_ERROR;
                  }
                }
                ret = updatewindow(
                  strm,
                  dictionary,
                  dictLength,
                  dictLength
                );
                if (ret) {
                  state.mode = MEM;
                  return Z_MEM_ERROR;
                }
                state.havedict = 1;
                return Z_OK;
              }
              exports3.inflateReset = inflateReset;
              exports3.inflateReset2 = inflateReset2;
              exports3.inflateResetKeep = inflateResetKeep;
              exports3.inflateInit = inflateInit;
              exports3.inflateInit2 = inflateInit2;
              exports3.inflate = inflate;
              exports3.inflateEnd = inflateEnd;
              exports3.inflateGetHeader = inflateGetHeader;
              exports3.inflateSetDictionary = inflateSetDictionary;
              exports3.inflateInfo = "pako inflate (from Nodeca project)";
            },
            {
              "../utils/common": 1,
              "./adler32": 3,
              "./crc32": 5,
              "./inffast": 7,
              "./inftrees": 9
            }
          ],
          9: [
            function(require2, module3, exports3) {
              "use strict";
              var utils = require2("../utils/common");
              var MAXBITS = 15;
              var ENOUGH_LENS = 852;
              var ENOUGH_DISTS = 592;
              var CODES = 0;
              var LENS = 1;
              var DISTS = 2;
              var lbase = [
                /* Length codes 257..285 base */
                3,
                4,
                5,
                6,
                7,
                8,
                9,
                10,
                11,
                13,
                15,
                17,
                19,
                23,
                27,
                31,
                35,
                43,
                51,
                59,
                67,
                83,
                99,
                115,
                131,
                163,
                195,
                227,
                258,
                0,
                0
              ];
              var lext = [
                /* Length codes 257..285 extra */
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                16,
                17,
                17,
                17,
                17,
                18,
                18,
                18,
                18,
                19,
                19,
                19,
                19,
                20,
                20,
                20,
                20,
                21,
                21,
                21,
                21,
                16,
                72,
                78
              ];
              var dbase = [
                /* Distance codes 0..29 base */
                1,
                2,
                3,
                4,
                5,
                7,
                9,
                13,
                17,
                25,
                33,
                49,
                65,
                97,
                129,
                193,
                257,
                385,
                513,
                769,
                1025,
                1537,
                2049,
                3073,
                4097,
                6145,
                8193,
                12289,
                16385,
                24577,
                0,
                0
              ];
              var dext = [
                /* Distance codes 0..29 extra */
                16,
                16,
                16,
                16,
                17,
                17,
                18,
                18,
                19,
                19,
                20,
                20,
                21,
                21,
                22,
                22,
                23,
                23,
                24,
                24,
                25,
                25,
                26,
                26,
                27,
                27,
                28,
                28,
                29,
                29,
                64,
                64
              ];
              module3.exports = function inflate_table(type, lens, lens_index, codes, table, table_index, work, opts) {
                var bits = opts.bits;
                var len = 0;
                var sym = 0;
                var min = 0, max = 0;
                var root = 0;
                var curr = 0;
                var drop = 0;
                var left = 0;
                var used = 0;
                var huff = 0;
                var incr;
                var fill;
                var low;
                var mask;
                var next;
                var base = null;
                var base_index = 0;
                var end;
                var count = new utils.Buf16(MAXBITS + 1);
                var offs = new utils.Buf16(MAXBITS + 1);
                var extra = null;
                var extra_index = 0;
                var here_bits, here_op, here_val;
                for (len = 0; len <= MAXBITS; len++) {
                  count[len] = 0;
                }
                for (sym = 0; sym < codes; sym++) {
                  count[lens[lens_index + sym]]++;
                }
                root = bits;
                for (max = MAXBITS; max >= 1; max--) {
                  if (count[max] !== 0) {
                    break;
                  }
                }
                if (root > max) {
                  root = max;
                }
                if (max === 0) {
                  table[table_index++] = 1 << 24 | 64 << 16 | 0;
                  table[table_index++] = 1 << 24 | 64 << 16 | 0;
                  opts.bits = 1;
                  return 0;
                }
                for (min = 1; min < max; min++) {
                  if (count[min] !== 0) {
                    break;
                  }
                }
                if (root < min) {
                  root = min;
                }
                left = 1;
                for (len = 1; len <= MAXBITS; len++) {
                  left <<= 1;
                  left -= count[len];
                  if (left < 0) {
                    return -1;
                  }
                }
                if (left > 0 && (type === CODES || max !== 1)) {
                  return -1;
                }
                offs[1] = 0;
                for (len = 1; len < MAXBITS; len++) {
                  offs[len + 1] = offs[len] + count[len];
                }
                for (sym = 0; sym < codes; sym++) {
                  if (lens[lens_index + sym] !== 0) {
                    work[offs[lens[lens_index + sym]]++] = sym;
                  }
                }
                if (type === CODES) {
                  base = extra = work;
                  end = 19;
                } else if (type === LENS) {
                  base = lbase;
                  base_index -= 257;
                  extra = lext;
                  extra_index -= 257;
                  end = 256;
                } else {
                  base = dbase;
                  extra = dext;
                  end = -1;
                }
                huff = 0;
                sym = 0;
                len = min;
                next = table_index;
                curr = root;
                drop = 0;
                low = -1;
                used = 1 << root;
                mask = used - 1;
                if (type === LENS && used > ENOUGH_LENS || type === DISTS && used > ENOUGH_DISTS) {
                  return 1;
                }
                for (; ; ) {
                  here_bits = len - drop;
                  if (work[sym] < end) {
                    here_op = 0;
                    here_val = work[sym];
                  } else if (work[sym] > end) {
                    here_op = extra[extra_index + work[sym]];
                    here_val = base[base_index + work[sym]];
                  } else {
                    here_op = 32 + 64;
                    here_val = 0;
                  }
                  incr = 1 << len - drop;
                  fill = 1 << curr;
                  min = fill;
                  do {
                    fill -= incr;
                    table[next + (huff >> drop) + fill] = here_bits << 24 | here_op << 16 | here_val | 0;
                  } while (fill !== 0);
                  incr = 1 << len - 1;
                  while (huff & incr) {
                    incr >>= 1;
                  }
                  if (incr !== 0) {
                    huff &= incr - 1;
                    huff += incr;
                  } else {
                    huff = 0;
                  }
                  sym++;
                  if (--count[len] === 0) {
                    if (len === max) {
                      break;
                    }
                    len = lens[lens_index + work[sym]];
                  }
                  if (len > root && (huff & mask) !== low) {
                    if (drop === 0) {
                      drop = root;
                    }
                    next += min;
                    curr = len - drop;
                    left = 1 << curr;
                    while (curr + drop < max) {
                      left -= count[curr + drop];
                      if (left <= 0) {
                        break;
                      }
                      curr++;
                      left <<= 1;
                    }
                    used += 1 << curr;
                    if (type === LENS && used > ENOUGH_LENS || type === DISTS && used > ENOUGH_DISTS) {
                      return 1;
                    }
                    low = huff & mask;
                    table[low] = root << 24 | curr << 16 | next - table_index | 0;
                  }
                }
                if (huff !== 0) {
                  table[next + huff] = len - drop << 24 | 64 << 16 | 0;
                }
                opts.bits = root;
                return 0;
              };
            },
            { "../utils/common": 1 }
          ],
          10: [
            function(require2, module3, exports3) {
              "use strict";
              module3.exports = {
                2: "need dictionary",
                1: "stream end",
                0: "",
                "-1": "file error",
                "-2": "stream error",
                "-3": "data error",
                "-4": "insufficient memory",
                "-5": "buffer error",
                "-6": "incompatible version"
              };
            },
            {}
          ],
          11: [
            function(require2, module3, exports3) {
              "use strict";
              function ZStream() {
                this.input = null;
                this.next_in = 0;
                this.avail_in = 0;
                this.total_in = 0;
                this.output = null;
                this.next_out = 0;
                this.avail_out = 0;
                this.total_out = 0;
                this.msg = "";
                this.state = null;
                this.data_type = 2;
                this.adler = 0;
              }
              module3.exports = ZStream;
            },
            {}
          ],
          "/lib/inflate.js": [
            function(require2, module3, exports3) {
              "use strict";
              var zlib_inflate = require2("./zlib/inflate");
              var utils = require2("./utils/common");
              var strings = require2("./utils/strings");
              var c2 = require2("./zlib/constants");
              var msg = require2("./zlib/messages");
              var ZStream = require2("./zlib/zstream");
              var GZheader = require2("./zlib/gzheader");
              var toString = Object.prototype.toString;
              function Inflate(options) {
                if (!(this instanceof Inflate))
                  return new Inflate(options);
                this.options = utils.assign(
                  {
                    chunkSize: 16384,
                    windowBits: 0,
                    to: ""
                  },
                  options || {}
                );
                var opt = this.options;
                if (opt.raw && opt.windowBits >= 0 && opt.windowBits < 16) {
                  opt.windowBits = -opt.windowBits;
                  if (opt.windowBits === 0) {
                    opt.windowBits = -15;
                  }
                }
                if (opt.windowBits >= 0 && opt.windowBits < 16 && !(options && options.windowBits)) {
                  opt.windowBits += 32;
                }
                if (opt.windowBits > 15 && opt.windowBits < 48) {
                  if ((opt.windowBits & 15) === 0) {
                    opt.windowBits |= 15;
                  }
                }
                this.err = 0;
                this.msg = "";
                this.ended = false;
                this.chunks = [];
                this.strm = new ZStream();
                this.strm.avail_out = 0;
                var status = zlib_inflate.inflateInit2(
                  this.strm,
                  opt.windowBits
                );
                if (status !== c2.Z_OK) {
                  throw new Error(msg[status]);
                }
                this.header = new GZheader();
                zlib_inflate.inflateGetHeader(this.strm, this.header);
                if (opt.dictionary) {
                  if (typeof opt.dictionary === "string") {
                    opt.dictionary = strings.string2buf(
                      opt.dictionary
                    );
                  } else if (toString.call(opt.dictionary) === "[object ArrayBuffer]") {
                    opt.dictionary = new Uint8Array(
                      opt.dictionary
                    );
                  }
                  if (opt.raw) {
                    status = zlib_inflate.inflateSetDictionary(
                      this.strm,
                      opt.dictionary
                    );
                    if (status !== c2.Z_OK) {
                      throw new Error(msg[status]);
                    }
                  }
                }
              }
              Inflate.prototype.push = function(data, mode) {
                var strm = this.strm;
                var chunkSize = this.options.chunkSize;
                var dictionary = this.options.dictionary;
                var status, _mode;
                var next_out_utf8, tail, utf8str;
                var allowBufError = false;
                if (this.ended) {
                  return false;
                }
                _mode = mode === ~~mode ? mode : mode === true ? c2.Z_FINISH : c2.Z_NO_FLUSH;
                if (typeof data === "string") {
                  strm.input = strings.binstring2buf(data);
                } else if (toString.call(data) === "[object ArrayBuffer]") {
                  strm.input = new Uint8Array(data);
                } else {
                  strm.input = data;
                }
                strm.next_in = 0;
                strm.avail_in = strm.input.length;
                do {
                  if (strm.avail_out === 0) {
                    strm.output = new utils.Buf8(chunkSize);
                    strm.next_out = 0;
                    strm.avail_out = chunkSize;
                  }
                  status = zlib_inflate.inflate(
                    strm,
                    c2.Z_NO_FLUSH
                  );
                  if (status === c2.Z_NEED_DICT && dictionary) {
                    status = zlib_inflate.inflateSetDictionary(
                      this.strm,
                      dictionary
                    );
                  }
                  if (status === c2.Z_BUF_ERROR && allowBufError === true) {
                    status = c2.Z_OK;
                    allowBufError = false;
                  }
                  if (status !== c2.Z_STREAM_END && status !== c2.Z_OK) {
                    this.onEnd(status);
                    this.ended = true;
                    return false;
                  }
                  if (strm.next_out) {
                    if (strm.avail_out === 0 || status === c2.Z_STREAM_END || strm.avail_in === 0 && (_mode === c2.Z_FINISH || _mode === c2.Z_SYNC_FLUSH)) {
                      if (this.options.to === "string") {
                        next_out_utf8 = strings.utf8border(
                          strm.output,
                          strm.next_out
                        );
                        tail = strm.next_out - next_out_utf8;
                        utf8str = strings.buf2string(
                          strm.output,
                          next_out_utf8
                        );
                        strm.next_out = tail;
                        strm.avail_out = chunkSize - tail;
                        if (tail) {
                          utils.arraySet(
                            strm.output,
                            strm.output,
                            next_out_utf8,
                            tail,
                            0
                          );
                        }
                        this.onData(utf8str);
                      } else {
                        this.onData(
                          utils.shrinkBuf(
                            strm.output,
                            strm.next_out
                          )
                        );
                      }
                    }
                  }
                  if (strm.avail_in === 0 && strm.avail_out === 0) {
                    allowBufError = true;
                  }
                } while ((strm.avail_in > 0 || strm.avail_out === 0) && status !== c2.Z_STREAM_END);
                if (status === c2.Z_STREAM_END) {
                  _mode = c2.Z_FINISH;
                }
                if (_mode === c2.Z_FINISH) {
                  status = zlib_inflate.inflateEnd(this.strm);
                  this.onEnd(status);
                  this.ended = true;
                  return status === c2.Z_OK;
                }
                if (_mode === c2.Z_SYNC_FLUSH) {
                  this.onEnd(c2.Z_OK);
                  strm.avail_out = 0;
                  return true;
                }
                return true;
              };
              Inflate.prototype.onData = function(chunk) {
                this.chunks.push(chunk);
              };
              Inflate.prototype.onEnd = function(status) {
                if (status === c2.Z_OK) {
                  if (this.options.to === "string") {
                    this.result = this.chunks.join("");
                  } else {
                    this.result = utils.flattenChunks(
                      this.chunks
                    );
                  }
                }
                this.chunks = [];
                this.err = status;
                this.msg = this.strm.msg;
              };
              function inflate(input, options) {
                var inflator = new Inflate(options);
                inflator.push(input, true);
                if (inflator.err) {
                  throw inflator.msg || msg[inflator.err];
                }
                return inflator.result;
              }
              function inflateRaw(input, options) {
                options = options || {};
                options.raw = true;
                return inflate(input, options);
              }
              exports3.Inflate = Inflate;
              exports3.inflate = inflate;
              exports3.inflateRaw = inflateRaw;
              exports3.ungzip = inflate;
            },
            {
              "./utils/common": 1,
              "./utils/strings": 2,
              "./zlib/constants": 4,
              "./zlib/gzheader": 6,
              "./zlib/inflate": 8,
              "./zlib/messages": 10,
              "./zlib/zstream": 11
            }
          ]
        },
        {},
        []
      )("/lib/inflate.js");
    });
  }
});
var inflate_default = require_inflate();

// packages/global-styles-ui/build-module/font-library/lib/lib-font.browser.js
var fetchFunction = globalThis.fetch;
var Event2 = class {
  constructor(type, detail = {}, msg) {
    this.type = type;
    this.detail = detail;
    this.msg = msg;
    Object.defineProperty(this, `__mayPropagate`, {
      enumerable: false,
      writable: true
    });
    this.__mayPropagate = true;
  }
  preventDefault() {
  }
  stopPropagation() {
    this.__mayPropagate = false;
  }
  valueOf() {
    return this;
  }
  toString() {
    return this.msg ? `[${this.type} event]: ${this.msg}` : `[${this.type} event]`;
  }
};
var EventManager = class {
  constructor() {
    this.listeners = {};
  }
  addEventListener(type, listener, useCapture) {
    let bin = this.listeners[type] || [];
    if (useCapture) bin.unshift(listener);
    else bin.push(listener);
    this.listeners[type] = bin;
  }
  removeEventListener(type, listener) {
    let bin = this.listeners[type] || [];
    let pos = bin.findIndex((e2) => e2 === listener);
    if (pos > -1) {
      bin.splice(pos, 1);
      this.listeners[type] = bin;
    }
  }
  dispatch(event) {
    let bin = this.listeners[event.type];
    if (bin) {
      for (let l2 = 0, e2 = bin.length; l2 < e2; l2++) {
        if (!event.__mayPropagate) break;
        bin[l2](event);
      }
    }
  }
};
var startDate = (/* @__PURE__ */ new Date(`1904-01-01T00:00:00+0000`)).getTime();
function asText(data) {
  return Array.from(data).map((v2) => String.fromCharCode(v2)).join(``);
}
var Parser = class {
  constructor(dict, dataview, name2) {
    this.name = (name2 || dict.tag || ``).trim();
    this.length = dict.length;
    this.start = dict.offset;
    this.offset = 0;
    this.data = dataview;
    [
      `getInt8`,
      `getUint8`,
      `getInt16`,
      `getUint16`,
      `getInt32`,
      `getUint32`,
      `getBigInt64`,
      `getBigUint64`
    ].forEach((name3) => {
      let fn = name3.replace(/get(Big)?/, "").toLowerCase();
      let increment = parseInt(name3.replace(/[^\d]/g, "")) / 8;
      Object.defineProperty(this, fn, {
        get: () => this.getValue(name3, increment)
      });
    });
  }
  get currentPosition() {
    return this.start + this.offset;
  }
  set currentPosition(position) {
    this.start = position;
    this.offset = 0;
  }
  skip(n2 = 0, bits = 8) {
    this.offset += n2 * bits / 8;
  }
  getValue(type, increment) {
    let pos = this.start + this.offset;
    this.offset += increment;
    try {
      return this.data[type](pos);
    } catch (e2) {
      console.error(`parser`, type, increment, this);
      console.error(`parser`, this.start, this.offset);
      throw e2;
    }
  }
  flags(n2) {
    if (n2 === 8 || n2 === 16 || n2 === 32 || n2 === 64) {
      return this[`uint${n2}`].toString(2).padStart(n2, 0).split(``).map((v2) => v2 === "1");
    }
    console.error(
      `Error parsing flags: flag types can only be 1, 2, 4, or 8 bytes long`
    );
    console.trace();
  }
  get tag() {
    const t3 = this.uint32;
    return asText([
      t3 >> 24 & 255,
      t3 >> 16 & 255,
      t3 >> 8 & 255,
      t3 & 255
    ]);
  }
  get fixed() {
    let major = this.int16;
    let minor = Math.round(1e3 * this.uint16 / 65356);
    return major + minor / 1e3;
  }
  get legacyFixed() {
    let major = this.uint16;
    let minor = this.uint16.toString(16).padStart(4, 0);
    return parseFloat(`${major}.${minor}`);
  }
  get uint24() {
    return (this.uint8 << 16) + (this.uint8 << 8) + this.uint8;
  }
  get uint128() {
    let value = 0;
    for (let i2 = 0; i2 < 5; i2++) {
      let byte = this.uint8;
      value = value * 128 + (byte & 127);
      if (byte < 128) break;
    }
    return value;
  }
  get longdatetime() {
    return new Date(startDate + 1e3 * parseInt(this.int64.toString()));
  }
  get fword() {
    return this.int16;
  }
  get ufword() {
    return this.uint16;
  }
  get Offset16() {
    return this.uint16;
  }
  get Offset32() {
    return this.uint32;
  }
  get F2DOT14() {
    const bits = p.uint16;
    const integer = [0, 1, -2, -1][bits >> 14];
    const fraction = bits & 16383;
    return integer + fraction / 16384;
  }
  verifyLength() {
    if (this.offset != this.length) {
      console.error(
        `unexpected parsed table size (${this.offset}) for "${this.name}" (expected ${this.length})`
      );
    }
  }
  readBytes(n2 = 0, position = 0, bits = 8, signed = false) {
    n2 = n2 || this.length;
    if (n2 === 0) return [];
    if (position) this.currentPosition = position;
    const fn = `${signed ? `` : `u`}int${bits}`, slice = [];
    while (n2--) slice.push(this[fn]);
    return slice;
  }
};
var ParsedData = class {
  constructor(parser) {
    const pGetter = { enumerable: false, get: () => parser };
    Object.defineProperty(this, `parser`, pGetter);
    const start = parser.currentPosition;
    const startGetter = { enumerable: false, get: () => start };
    Object.defineProperty(this, `start`, startGetter);
  }
  load(struct) {
    Object.keys(struct).forEach((p22) => {
      let props = Object.getOwnPropertyDescriptor(struct, p22);
      if (props.get) {
        this[p22] = props.get.bind(this);
      } else if (props.value !== void 0) {
        this[p22] = props.value;
      }
    });
    if (this.parser.length) {
      this.parser.verifyLength();
    }
  }
};
var SimpleTable = class extends ParsedData {
  constructor(dict, dataview, name2) {
    const { parser, start } = super(
      new Parser(dict, dataview, name2)
    );
    const pGetter = { enumerable: false, get: () => parser };
    Object.defineProperty(this, `p`, pGetter);
    const startGetter = { enumerable: false, get: () => start };
    Object.defineProperty(this, `tableStart`, startGetter);
  }
};
function lazy$1(object, property, getter) {
  let val;
  Object.defineProperty(object, property, {
    get: () => {
      if (val) return val;
      val = getter();
      return val;
    },
    enumerable: true
  });
}
var SFNT = class extends SimpleTable {
  constructor(font2, dataview, createTable2) {
    const { p: p22 } = super({ offset: 0, length: 12 }, dataview, `sfnt`);
    this.version = p22.uint32;
    this.numTables = p22.uint16;
    this.searchRange = p22.uint16;
    this.entrySelector = p22.uint16;
    this.rangeShift = p22.uint16;
    p22.verifyLength();
    this.directory = [...new Array(this.numTables)].map(
      (_) => new TableRecord(p22)
    );
    this.tables = {};
    this.directory.forEach((entry) => {
      const getter = () => createTable2(
        this.tables,
        {
          tag: entry.tag,
          offset: entry.offset,
          length: entry.length
        },
        dataview
      );
      lazy$1(this.tables, entry.tag.trim(), getter);
    });
  }
};
var TableRecord = class {
  constructor(p22) {
    this.tag = p22.tag;
    this.checksum = p22.uint32;
    this.offset = p22.uint32;
    this.length = p22.uint32;
  }
};
var gzipDecode = inflate_default.inflate || void 0;
var nativeGzipDecode = void 0;
var WOFF$1 = class extends SimpleTable {
  constructor(font2, dataview, createTable2) {
    const { p: p22 } = super({ offset: 0, length: 44 }, dataview, `woff`);
    this.signature = p22.tag;
    this.flavor = p22.uint32;
    this.length = p22.uint32;
    this.numTables = p22.uint16;
    p22.uint16;
    this.totalSfntSize = p22.uint32;
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.metaOffset = p22.uint32;
    this.metaLength = p22.uint32;
    this.metaOrigLength = p22.uint32;
    this.privOffset = p22.uint32;
    this.privLength = p22.uint32;
    p22.verifyLength();
    this.directory = [...new Array(this.numTables)].map(
      (_) => new WoffTableDirectoryEntry(p22)
    );
    buildWoffLazyLookups(this, dataview, createTable2);
  }
};
var WoffTableDirectoryEntry = class {
  constructor(p22) {
    this.tag = p22.tag;
    this.offset = p22.uint32;
    this.compLength = p22.uint32;
    this.origLength = p22.uint32;
    this.origChecksum = p22.uint32;
  }
};
function buildWoffLazyLookups(woff, dataview, createTable2) {
  woff.tables = {};
  woff.directory.forEach((entry) => {
    lazy$1(woff.tables, entry.tag.trim(), () => {
      let offset = 0;
      let view = dataview;
      if (entry.compLength !== entry.origLength) {
        const data = dataview.buffer.slice(
          entry.offset,
          entry.offset + entry.compLength
        );
        let unpacked;
        if (gzipDecode) {
          unpacked = gzipDecode(new Uint8Array(data));
        } else if (nativeGzipDecode) {
          unpacked = nativeGzipDecode(new Uint8Array(data));
        } else {
          const msg = `no brotli decoder available to decode WOFF2 font`;
          if (font.onerror) font.onerror(msg);
          throw new Error(msg);
        }
        view = new DataView(unpacked.buffer);
      } else {
        offset = entry.offset;
      }
      return createTable2(
        woff.tables,
        { tag: entry.tag, offset, length: entry.origLength },
        view
      );
    });
  });
}
var brotliDecode = unbrotli_default;
var nativeBrotliDecode = void 0;
var WOFF2$1 = class extends SimpleTable {
  constructor(font2, dataview, createTable2) {
    const { p: p22 } = super({ offset: 0, length: 48 }, dataview, `woff2`);
    this.signature = p22.tag;
    this.flavor = p22.uint32;
    this.length = p22.uint32;
    this.numTables = p22.uint16;
    p22.uint16;
    this.totalSfntSize = p22.uint32;
    this.totalCompressedSize = p22.uint32;
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.metaOffset = p22.uint32;
    this.metaLength = p22.uint32;
    this.metaOrigLength = p22.uint32;
    this.privOffset = p22.uint32;
    this.privLength = p22.uint32;
    p22.verifyLength();
    this.directory = [...new Array(this.numTables)].map(
      (_) => new Woff2TableDirectoryEntry(p22)
    );
    let dictOffset = p22.currentPosition;
    this.directory[0].offset = 0;
    this.directory.forEach((e2, i2) => {
      let next = this.directory[i2 + 1];
      if (next) {
        next.offset = e2.offset + (e2.transformLength !== void 0 ? e2.transformLength : e2.origLength);
      }
    });
    let decoded;
    let buffer = dataview.buffer.slice(dictOffset);
    if (brotliDecode) {
      decoded = brotliDecode(new Uint8Array(buffer));
    } else if (nativeBrotliDecode) {
      decoded = new Uint8Array(nativeBrotliDecode(buffer));
    } else {
      const msg = `no brotli decoder available to decode WOFF2 font`;
      if (font2.onerror) font2.onerror(msg);
      throw new Error(msg);
    }
    buildWoff2LazyLookups(this, decoded, createTable2);
  }
};
var Woff2TableDirectoryEntry = class {
  constructor(p22) {
    this.flags = p22.uint8;
    const tagNumber = this.tagNumber = this.flags & 63;
    if (tagNumber === 63) {
      this.tag = p22.tag;
    } else {
      this.tag = getWOFF2Tag(tagNumber);
    }
    const transformVersion = this.transformVersion = (this.flags & 192) >> 6;
    let hasTransforms = transformVersion !== 0;
    if (this.tag === `glyf` || this.tag === `loca`) {
      hasTransforms = this.transformVersion !== 3;
    }
    this.origLength = p22.uint128;
    if (hasTransforms) {
      this.transformLength = p22.uint128;
    }
  }
};
function buildWoff2LazyLookups(woff2, decoded, createTable2) {
  woff2.tables = {};
  woff2.directory.forEach((entry) => {
    lazy$1(woff2.tables, entry.tag.trim(), () => {
      const start = entry.offset;
      const end = start + (entry.transformLength ? entry.transformLength : entry.origLength);
      const data = new DataView(decoded.slice(start, end).buffer);
      try {
        return createTable2(
          woff2.tables,
          { tag: entry.tag, offset: 0, length: entry.origLength },
          data
        );
      } catch (e2) {
        console.error(e2);
      }
    });
  });
}
function getWOFF2Tag(flag) {
  return [
    `cmap`,
    `head`,
    `hhea`,
    `hmtx`,
    `maxp`,
    `name`,
    `OS/2`,
    `post`,
    `cvt `,
    `fpgm`,
    `glyf`,
    `loca`,
    `prep`,
    `CFF `,
    `VORG`,
    `EBDT`,
    `EBLC`,
    `gasp`,
    `hdmx`,
    `kern`,
    `LTSH`,
    `PCLT`,
    `VDMX`,
    `vhea`,
    `vmtx`,
    `BASE`,
    `GDEF`,
    `GPOS`,
    `GSUB`,
    `EBSC`,
    `JSTF`,
    `MATH`,
    `CBDT`,
    `CBLC`,
    `COLR`,
    `CPAL`,
    `SVG `,
    `sbix`,
    `acnt`,
    `avar`,
    `bdat`,
    `bloc`,
    `bsln`,
    `cvar`,
    `fdsc`,
    `feat`,
    `fmtx`,
    `fvar`,
    `gvar`,
    `hsty`,
    `just`,
    `lcar`,
    `mort`,
    `morx`,
    `opbd`,
    `prop`,
    `trak`,
    `Zapf`,
    `Silf`,
    `Glat`,
    `Gloc`,
    `Feat`,
    `Sill`
  ][flag & 63];
}
var tableClasses = {};
var tableClassesLoaded = false;
Promise.all([
  Promise.resolve().then(function() {
    return cmap$1;
  }),
  Promise.resolve().then(function() {
    return head$1;
  }),
  Promise.resolve().then(function() {
    return hhea$1;
  }),
  Promise.resolve().then(function() {
    return hmtx$1;
  }),
  Promise.resolve().then(function() {
    return maxp$1;
  }),
  Promise.resolve().then(function() {
    return name$1;
  }),
  Promise.resolve().then(function() {
    return OS2$1;
  }),
  Promise.resolve().then(function() {
    return post$1;
  }),
  Promise.resolve().then(function() {
    return BASE$1;
  }),
  Promise.resolve().then(function() {
    return GDEF$1;
  }),
  Promise.resolve().then(function() {
    return GSUB$1;
  }),
  Promise.resolve().then(function() {
    return GPOS$1;
  }),
  Promise.resolve().then(function() {
    return SVG$1;
  }),
  Promise.resolve().then(function() {
    return fvar$1;
  }),
  Promise.resolve().then(function() {
    return cvt$1;
  }),
  Promise.resolve().then(function() {
    return fpgm$1;
  }),
  Promise.resolve().then(function() {
    return gasp$1;
  }),
  Promise.resolve().then(function() {
    return glyf$1;
  }),
  Promise.resolve().then(function() {
    return loca$1;
  }),
  Promise.resolve().then(function() {
    return prep$1;
  }),
  Promise.resolve().then(function() {
    return CFF$1;
  }),
  Promise.resolve().then(function() {
    return CFF2$1;
  }),
  Promise.resolve().then(function() {
    return VORG$1;
  }),
  Promise.resolve().then(function() {
    return EBLC$1;
  }),
  Promise.resolve().then(function() {
    return EBDT$1;
  }),
  Promise.resolve().then(function() {
    return EBSC$1;
  }),
  Promise.resolve().then(function() {
    return CBLC$1;
  }),
  Promise.resolve().then(function() {
    return CBDT$1;
  }),
  Promise.resolve().then(function() {
    return sbix$1;
  }),
  Promise.resolve().then(function() {
    return COLR$1;
  }),
  Promise.resolve().then(function() {
    return CPAL$1;
  }),
  Promise.resolve().then(function() {
    return DSIG$1;
  }),
  Promise.resolve().then(function() {
    return hdmx$1;
  }),
  Promise.resolve().then(function() {
    return kern$1;
  }),
  Promise.resolve().then(function() {
    return LTSH$1;
  }),
  Promise.resolve().then(function() {
    return MERG$1;
  }),
  Promise.resolve().then(function() {
    return meta$1;
  }),
  Promise.resolve().then(function() {
    return PCLT$1;
  }),
  Promise.resolve().then(function() {
    return VDMX$1;
  }),
  Promise.resolve().then(function() {
    return vhea$1;
  }),
  Promise.resolve().then(function() {
    return vmtx$1;
  })
]).then((data) => {
  data.forEach((e2) => {
    let name2 = Object.keys(e2)[0];
    tableClasses[name2] = e2[name2];
  });
  tableClassesLoaded = true;
});
function createTable(tables, dict, dataview) {
  let name2 = dict.tag.replace(/[^\w\d]/g, ``);
  let Type = tableClasses[name2];
  if (Type) return new Type(dict, dataview, tables);
  console.warn(
    `lib-font has no definition for ${name2}. The table was skipped.`
  );
  return {};
}
function loadTableClasses() {
  let count = 0;
  function checkLoaded(resolve, reject) {
    if (!tableClassesLoaded) {
      if (count > 10) {
        return reject(new Error(`loading took too long`));
      }
      count++;
      return setTimeout(() => checkLoaded(resolve), 250);
    }
    resolve(createTable);
  }
  return new Promise((resolve, reject) => checkLoaded(resolve));
}
function getFontCSSFormat(path, errorOnStyle) {
  let pos = path.lastIndexOf(`.`);
  let ext = (path.substring(pos + 1) || ``).toLowerCase();
  let format = {
    ttf: `truetype`,
    otf: `opentype`,
    woff: `woff`,
    woff2: `woff2`
  }[ext];
  if (format) return format;
  let msg = {
    eot: `The .eot format is not supported: it died in January 12, 2016, when Microsoft retired all versions of IE that didn't already support WOFF.`,
    svg: `The .svg format is not supported: SVG fonts (not to be confused with OpenType with embedded SVG) were so bad we took the entire fonts chapter out of the SVG specification again.`,
    fon: `The .fon format is not supported: this is an ancient Windows bitmap font format.`,
    ttc: `Based on the current CSS specification, font collections are not (yet?) supported.`
  }[ext];
  if (!msg) msg = `${path} is not a known webfont format.`;
  if (errorOnStyle) {
    throw new Error(msg);
  } else {
    console.warn(`Could not load font: ${msg}`);
  }
}
async function setupFontFace(name2, url, options = {}) {
  if (!globalThis.document) return;
  let format = getFontCSSFormat(url, options.errorOnStyle);
  if (!format) return;
  let style = document.createElement(`style`);
  style.className = `injected-by-Font-js`;
  let rules = [];
  if (options.styleRules) {
    rules = Object.entries(options.styleRules).map(
      ([key, value]) => `${key}: ${value};`
    );
  }
  style.textContent = `
@font-face {
    font-family: "${name2}";
    ${rules.join(
    `
	`
  )}
    src: url("${url}") format("${format}");
}`;
  globalThis.document.head.appendChild(style);
  return style;
}
var TTF = [0, 1, 0, 0];
var OTF = [79, 84, 84, 79];
var WOFF = [119, 79, 70, 70];
var WOFF2 = [119, 79, 70, 50];
function match(ar1, ar2) {
  if (ar1.length !== ar2.length) return;
  for (let i2 = 0; i2 < ar1.length; i2++) {
    if (ar1[i2] !== ar2[i2]) return;
  }
  return true;
}
function validFontFormat(dataview) {
  const LEAD_BYTES = [
    dataview.getUint8(0),
    dataview.getUint8(1),
    dataview.getUint8(2),
    dataview.getUint8(3)
  ];
  if (match(LEAD_BYTES, TTF) || match(LEAD_BYTES, OTF)) return `SFNT`;
  if (match(LEAD_BYTES, WOFF)) return `WOFF`;
  if (match(LEAD_BYTES, WOFF2)) return `WOFF2`;
}
function checkFetchResponseStatus(response) {
  if (!response.ok) {
    throw new Error(
      `HTTP ${response.status} - ${response.statusText}`
    );
  }
  return response;
}
var Font = class extends EventManager {
  constructor(name2, options = {}) {
    super();
    this.name = name2;
    this.options = options;
    this.metrics = false;
  }
  get src() {
    return this.__src;
  }
  set src(src) {
    this.__src = src;
    (async () => {
      if (globalThis.document && !this.options.skipStyleSheet) {
        await setupFontFace(this.name, src, this.options);
      }
      this.loadFont(src);
    })();
  }
  async loadFont(url, filename) {
    fetch(url).then(
      (response) => checkFetchResponseStatus(response) && response.arrayBuffer()
    ).then(
      (buffer) => this.fromDataBuffer(buffer, filename || url)
    ).catch((err) => {
      const evt = new Event2(
        `error`,
        err,
        `Failed to load font at ${filename || url}`
      );
      this.dispatch(evt);
      if (this.onerror) this.onerror(evt);
    });
  }
  async fromDataBuffer(buffer, filenameOrUrL) {
    this.fontData = new DataView(buffer);
    let type = validFontFormat(this.fontData);
    if (!type) {
      throw new Error(
        `${filenameOrUrL} is either an unsupported font format, or not a font at all.`
      );
    }
    await this.parseBasicData(type);
    const evt = new Event2("load", { font: this });
    this.dispatch(evt);
    if (this.onload) this.onload(evt);
  }
  async parseBasicData(type) {
    return loadTableClasses().then((createTable2) => {
      if (type === `SFNT`) {
        this.opentype = new SFNT(this, this.fontData, createTable2);
      }
      if (type === `WOFF`) {
        this.opentype = new WOFF$1(this, this.fontData, createTable2);
      }
      if (type === `WOFF2`) {
        this.opentype = new WOFF2$1(this, this.fontData, createTable2);
      }
      return this.opentype;
    });
  }
  getGlyphId(char) {
    return this.opentype.tables.cmap.getGlyphId(char);
  }
  reverse(glyphid) {
    return this.opentype.tables.cmap.reverse(glyphid);
  }
  supports(char) {
    return this.getGlyphId(char) !== 0;
  }
  supportsVariation(variation) {
    return this.opentype.tables.cmap.supportsVariation(variation) !== false;
  }
  measureText(text, size = 16) {
    if (this.__unloaded)
      throw new Error(
        "Cannot measure text: font was unloaded. Please reload before calling measureText()"
      );
    let d2 = document.createElement("div");
    d2.textContent = text;
    d2.style.fontFamily = this.name;
    d2.style.fontSize = `${size}px`;
    d2.style.color = `transparent`;
    d2.style.background = `transparent`;
    d2.style.top = `0`;
    d2.style.left = `0`;
    d2.style.position = `absolute`;
    document.body.appendChild(d2);
    let bbox = d2.getBoundingClientRect();
    document.body.removeChild(d2);
    const OS22 = this.opentype.tables["OS/2"];
    bbox.fontSize = size;
    bbox.ascender = OS22.sTypoAscender;
    bbox.descender = OS22.sTypoDescender;
    return bbox;
  }
  unload() {
    if (this.styleElement.parentNode) {
      this.styleElement.parentNode.removeElement(this.styleElement);
      const evt = new Event2("unload", { font: this });
      this.dispatch(evt);
      if (this.onunload) this.onunload(evt);
    }
    this._unloaded = true;
  }
  load() {
    if (this.__unloaded) {
      delete this.__unloaded;
      document.head.appendChild(this.styleElement);
      const evt = new Event2("load", { font: this });
      this.dispatch(evt);
      if (this.onload) this.onload(evt);
    }
  }
};
globalThis.Font = Font;
var Subtable = class extends ParsedData {
  constructor(p22, plaformID, encodingID) {
    super(p22);
    this.plaformID = plaformID;
    this.encodingID = encodingID;
  }
};
var Format0 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 0;
    this.length = p22.uint16;
    this.language = p22.uint16;
    this.glyphIdArray = [...new Array(256)].map((_) => p22.uint8);
  }
  supports(charCode) {
    if (charCode.charCodeAt) {
      charCode = -1;
      console.warn(
        `supports(character) not implemented for cmap subtable format 0. only supports(id) is implemented.`
      );
    }
    return 0 <= charCode && charCode <= 255;
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 0`);
    return {};
  }
  getSupportedCharCodes() {
    return [{ start: 1, end: 256 }];
  }
};
var Format2 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 2;
    this.length = p22.uint16;
    this.language = p22.uint16;
    this.subHeaderKeys = [...new Array(256)].map((_) => p22.uint16);
    const subHeaderCount = Math.max(...this.subHeaderKeys);
    const subHeaderOffset = p22.currentPosition;
    lazy$1(this, `subHeaders`, () => {
      p22.currentPosition = subHeaderOffset;
      return [...new Array(subHeaderCount)].map(
        (_) => new SubHeader(p22)
      );
    });
    const glyphIndexOffset = subHeaderOffset + subHeaderCount * 8;
    lazy$1(this, `glyphIndexArray`, () => {
      p22.currentPosition = glyphIndexOffset;
      return [...new Array(subHeaderCount)].map((_) => p22.uint16);
    });
  }
  supports(charCode) {
    if (charCode.charCodeAt) {
      charCode = -1;
      console.warn(
        `supports(character) not implemented for cmap subtable format 2. only supports(id) is implemented.`
      );
    }
    const low = charCode && 255;
    const high = charCode && 65280;
    const subHeaderKey = this.subHeaders[high];
    const subheader = this.subHeaders[subHeaderKey];
    const first = subheader.firstCode;
    const last = first + subheader.entryCount;
    return first <= low && low <= last;
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 2`);
    return {};
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) {
      return this.subHeaders.map((h2) => ({
        firstCode: h2.firstCode,
        lastCode: h2.lastCode
      }));
    }
    return this.subHeaders.map((h2) => ({
      start: h2.firstCode,
      end: h2.lastCode
    }));
  }
};
var SubHeader = class {
  constructor(p22) {
    this.firstCode = p22.uint16;
    this.entryCount = p22.uint16;
    this.lastCode = this.first + this.entryCount;
    this.idDelta = p22.int16;
    this.idRangeOffset = p22.uint16;
  }
};
var Format4 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 4;
    this.length = p22.uint16;
    this.language = p22.uint16;
    this.segCountX2 = p22.uint16;
    this.segCount = this.segCountX2 / 2;
    this.searchRange = p22.uint16;
    this.entrySelector = p22.uint16;
    this.rangeShift = p22.uint16;
    const endCodePosition = p22.currentPosition;
    lazy$1(
      this,
      `endCode`,
      () => p22.readBytes(this.segCount, endCodePosition, 16)
    );
    const startCodePosition = endCodePosition + 2 + this.segCountX2;
    lazy$1(
      this,
      `startCode`,
      () => p22.readBytes(this.segCount, startCodePosition, 16)
    );
    const idDeltaPosition = startCodePosition + this.segCountX2;
    lazy$1(
      this,
      `idDelta`,
      () => p22.readBytes(this.segCount, idDeltaPosition, 16, true)
    );
    const idRangePosition = idDeltaPosition + this.segCountX2;
    lazy$1(
      this,
      `idRangeOffset`,
      () => p22.readBytes(this.segCount, idRangePosition, 16)
    );
    const glyphIdArrayPosition = idRangePosition + this.segCountX2;
    const glyphIdArrayLength = this.length - (glyphIdArrayPosition - this.tableStart);
    lazy$1(
      this,
      `glyphIdArray`,
      () => p22.readBytes(glyphIdArrayLength, glyphIdArrayPosition, 16)
    );
    lazy$1(
      this,
      `segments`,
      () => this.buildSegments(idRangePosition, glyphIdArrayPosition, p22)
    );
  }
  buildSegments(idRangePosition, glyphIdArrayPosition, p22) {
    const build = (_, i2) => {
      let startCode = this.startCode[i2], endCode = this.endCode[i2], idDelta = this.idDelta[i2], idRangeOffset = this.idRangeOffset[i2], idRangeOffsetPointer = idRangePosition + 2 * i2, glyphIDs = [];
      if (idRangeOffset === 0) {
        for (let i22 = startCode + idDelta, e2 = endCode + idDelta; i22 <= e2; i22++) {
          glyphIDs.push(i22);
        }
      } else {
        for (let i22 = 0, e2 = endCode - startCode; i22 <= e2; i22++) {
          p22.currentPosition = idRangeOffsetPointer + idRangeOffset + i22 * 2;
          glyphIDs.push(p22.uint16);
        }
      }
      return {
        startCode,
        endCode,
        idDelta,
        idRangeOffset,
        glyphIDs
      };
    };
    return [...new Array(this.segCount)].map(build);
  }
  reverse(glyphID) {
    let s2 = this.segments.find((v2) => v2.glyphIDs.includes(glyphID));
    if (!s2) return {};
    const code = s2.startCode + s2.glyphIDs.indexOf(glyphID);
    return { code, unicode: String.fromCodePoint(code) };
  }
  getGlyphId(charCode) {
    if (charCode.charCodeAt) charCode = charCode.charCodeAt(0);
    if (55296 <= charCode && charCode <= 57343) return 0;
    if ((charCode & 65534) === 65534 || (charCode & 65535) === 65535)
      return 0;
    let segment = this.segments.find(
      (s2) => s2.startCode <= charCode && charCode <= s2.endCode
    );
    if (!segment) return 0;
    return segment.glyphIDs[charCode - segment.startCode];
  }
  supports(charCode) {
    return this.getGlyphId(charCode) !== 0;
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) return this.segments;
    return this.segments.map((v2) => ({
      start: v2.startCode,
      end: v2.endCode
    }));
  }
};
var Format6 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 6;
    this.length = p22.uint16;
    this.language = p22.uint16;
    this.firstCode = p22.uint16;
    this.entryCount = p22.uint16;
    this.lastCode = this.firstCode + this.entryCount - 1;
    const getter = () => [...new Array(this.entryCount)].map((_) => p22.uint16);
    lazy$1(this, `glyphIdArray`, getter);
  }
  supports(charCode) {
    if (charCode.charCodeAt) {
      charCode = -1;
      console.warn(
        `supports(character) not implemented for cmap subtable format 6. only supports(id) is implemented.`
      );
    }
    if (charCode < this.firstCode) return {};
    if (charCode > this.firstCode + this.entryCount) return {};
    const code = charCode - this.firstCode;
    return { code, unicode: String.fromCodePoint(code) };
  }
  reverse(glyphID) {
    let pos = this.glyphIdArray.indexOf(glyphID);
    if (pos > -1) return this.firstCode + pos;
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) {
      return [{ firstCode: this.firstCode, lastCode: this.lastCode }];
    }
    return [{ start: this.firstCode, end: this.lastCode }];
  }
};
var Format8 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 8;
    p22.uint16;
    this.length = p22.uint32;
    this.language = p22.uint32;
    this.is32 = [...new Array(8192)].map((_) => p22.uint8);
    this.numGroups = p22.uint32;
    const getter = () => [...new Array(this.numGroups)].map(
      (_) => new SequentialMapGroup$1(p22)
    );
    lazy$1(this, `groups`, getter);
  }
  supports(charCode) {
    if (charCode.charCodeAt) {
      charCode = -1;
      console.warn(
        `supports(character) not implemented for cmap subtable format 8. only supports(id) is implemented.`
      );
    }
    return this.groups.findIndex(
      (s2) => s2.startcharCode <= charCode && charCode <= s2.endcharCode
    ) !== -1;
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 8`);
    return {};
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) return this.groups;
    return this.groups.map((v2) => ({
      start: v2.startcharCode,
      end: v2.endcharCode
    }));
  }
};
var SequentialMapGroup$1 = class {
  constructor(p22) {
    this.startcharCode = p22.uint32;
    this.endcharCode = p22.uint32;
    this.startGlyphID = p22.uint32;
  }
};
var Format10 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 10;
    p22.uint16;
    this.length = p22.uint32;
    this.language = p22.uint32;
    this.startCharCode = p22.uint32;
    this.numChars = p22.uint32;
    this.endCharCode = this.startCharCode + this.numChars;
    const getter = () => [...new Array(this.numChars)].map((_) => p22.uint16);
    lazy$1(this, `glyphs`, getter);
  }
  supports(charCode) {
    if (charCode.charCodeAt) {
      charCode = -1;
      console.warn(
        `supports(character) not implemented for cmap subtable format 10. only supports(id) is implemented.`
      );
    }
    if (charCode < this.startCharCode) return false;
    if (charCode > this.startCharCode + this.numChars) return false;
    return charCode - this.startCharCode;
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 10`);
    return {};
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) {
      return [
        {
          startCharCode: this.startCharCode,
          endCharCode: this.endCharCode
        }
      ];
    }
    return [{ start: this.startCharCode, end: this.endCharCode }];
  }
};
var Format12 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 12;
    p22.uint16;
    this.length = p22.uint32;
    this.language = p22.uint32;
    this.numGroups = p22.uint32;
    const getter = () => [...new Array(this.numGroups)].map(
      (_) => new SequentialMapGroup(p22)
    );
    lazy$1(this, `groups`, getter);
  }
  supports(charCode) {
    if (charCode.charCodeAt) charCode = charCode.charCodeAt(0);
    if (55296 <= charCode && charCode <= 57343) return 0;
    if ((charCode & 65534) === 65534 || (charCode & 65535) === 65535)
      return 0;
    return this.groups.findIndex(
      (s2) => s2.startCharCode <= charCode && charCode <= s2.endCharCode
    ) !== -1;
  }
  reverse(glyphID) {
    for (let group of this.groups) {
      let start = group.startGlyphID;
      if (start > glyphID) continue;
      if (start === glyphID) return group.startCharCode;
      let end = start + (group.endCharCode - group.startCharCode);
      if (end < glyphID) continue;
      const code = group.startCharCode + (glyphID - start);
      return { code, unicode: String.fromCodePoint(code) };
    }
    return {};
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) return this.groups;
    return this.groups.map((v2) => ({
      start: v2.startCharCode,
      end: v2.endCharCode
    }));
  }
};
var SequentialMapGroup = class {
  constructor(p22) {
    this.startCharCode = p22.uint32;
    this.endCharCode = p22.uint32;
    this.startGlyphID = p22.uint32;
  }
};
var Format13 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.format = 13;
    p22.uint16;
    this.length = p22.uint32;
    this.language = p22.uint32;
    this.numGroups = p22.uint32;
    const getter = [...new Array(this.numGroups)].map(
      (_) => new ConstantMapGroup(p22)
    );
    lazy$1(this, `groups`, getter);
  }
  supports(charCode) {
    if (charCode.charCodeAt) charCode = charCode.charCodeAt(0);
    return this.groups.findIndex(
      (s2) => s2.startCharCode <= charCode && charCode <= s2.endCharCode
    ) !== -1;
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 13`);
    return {};
  }
  getSupportedCharCodes(preservePropNames = false) {
    if (preservePropNames) return this.groups;
    return this.groups.map((v2) => ({
      start: v2.startCharCode,
      end: v2.endCharCode
    }));
  }
};
var ConstantMapGroup = class {
  constructor(p22) {
    this.startCharCode = p22.uint32;
    this.endCharCode = p22.uint32;
    this.glyphID = p22.uint32;
  }
};
var Format14 = class extends Subtable {
  constructor(p22, platformID, encodingID) {
    super(p22, platformID, encodingID);
    this.subTableStart = p22.currentPosition;
    this.format = 14;
    this.length = p22.uint32;
    this.numVarSelectorRecords = p22.uint32;
    lazy$1(
      this,
      `varSelectors`,
      () => [...new Array(this.numVarSelectorRecords)].map(
        (_) => new VariationSelector(p22)
      )
    );
  }
  supports() {
    console.warn(`supports not implemented for cmap subtable format 14`);
    return 0;
  }
  getSupportedCharCodes() {
    console.warn(
      `getSupportedCharCodes not implemented for cmap subtable format 14`
    );
    return [];
  }
  reverse(glyphID) {
    console.warn(`reverse not implemented for cmap subtable format 14`);
    return {};
  }
  supportsVariation(variation) {
    let v2 = this.varSelector.find(
      (uvs) => uvs.varSelector === variation
    );
    return v2 ? v2 : false;
  }
  getSupportedVariations() {
    return this.varSelectors.map((v2) => v2.varSelector);
  }
};
var VariationSelector = class {
  constructor(p22) {
    this.varSelector = p22.uint24;
    this.defaultUVSOffset = p22.Offset32;
    this.nonDefaultUVSOffset = p22.Offset32;
  }
};
function createSubTable(parser, platformID, encodingID) {
  const format = parser.uint16;
  if (format === 0) return new Format0(parser, platformID, encodingID);
  if (format === 2) return new Format2(parser, platformID, encodingID);
  if (format === 4) return new Format4(parser, platformID, encodingID);
  if (format === 6) return new Format6(parser, platformID, encodingID);
  if (format === 8) return new Format8(parser, platformID, encodingID);
  if (format === 10) return new Format10(parser, platformID, encodingID);
  if (format === 12) return new Format12(parser, platformID, encodingID);
  if (format === 13) return new Format13(parser, platformID, encodingID);
  if (format === 14) return new Format14(parser, platformID, encodingID);
  return {};
}
var cmap = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numTables = p22.uint16;
    this.encodingRecords = [...new Array(this.numTables)].map(
      (_) => new EncodingRecord(p22, this.tableStart)
    );
  }
  getSubTable(tableID) {
    return this.encodingRecords[tableID].table;
  }
  getSupportedEncodings() {
    return this.encodingRecords.map((r3) => ({
      platformID: r3.platformID,
      encodingId: r3.encodingID
    }));
  }
  getSupportedCharCodes(platformID, encodingID) {
    const recordID = this.encodingRecords.findIndex(
      (r3) => r3.platformID === platformID && r3.encodingID === encodingID
    );
    if (recordID === -1) return false;
    const subtable = this.getSubTable(recordID);
    return subtable.getSupportedCharCodes();
  }
  reverse(glyphid) {
    for (let i2 = 0; i2 < this.numTables; i2++) {
      let code = this.getSubTable(i2).reverse(glyphid);
      if (code) return code;
    }
  }
  getGlyphId(char) {
    let last = 0;
    this.encodingRecords.some((_, tableID) => {
      let t3 = this.getSubTable(tableID);
      if (!t3.getGlyphId) return false;
      last = t3.getGlyphId(char);
      return last !== 0;
    });
    return last;
  }
  supports(char) {
    return this.encodingRecords.some((_, tableID) => {
      const t3 = this.getSubTable(tableID);
      return t3.supports && t3.supports(char) !== false;
    });
  }
  supportsVariation(variation) {
    return this.encodingRecords.some((_, tableID) => {
      const t3 = this.getSubTable(tableID);
      return t3.supportsVariation && t3.supportsVariation(variation) !== false;
    });
  }
};
var EncodingRecord = class {
  constructor(p22, tableStart) {
    const platformID = this.platformID = p22.uint16;
    const encodingID = this.encodingID = p22.uint16;
    const offset = this.offset = p22.Offset32;
    lazy$1(this, `table`, () => {
      p22.currentPosition = tableStart + offset;
      return createSubTable(p22, platformID, encodingID);
    });
  }
};
var cmap$1 = Object.freeze({ __proto__: null, cmap });
var head = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.load({
      majorVersion: p22.uint16,
      minorVersion: p22.uint16,
      fontRevision: p22.fixed,
      checkSumAdjustment: p22.uint32,
      magicNumber: p22.uint32,
      flags: p22.flags(16),
      unitsPerEm: p22.uint16,
      created: p22.longdatetime,
      modified: p22.longdatetime,
      xMin: p22.int16,
      yMin: p22.int16,
      xMax: p22.int16,
      yMax: p22.int16,
      macStyle: p22.flags(16),
      lowestRecPPEM: p22.uint16,
      fontDirectionHint: p22.uint16,
      indexToLocFormat: p22.uint16,
      glyphDataFormat: p22.uint16
    });
  }
};
var head$1 = Object.freeze({ __proto__: null, head });
var hhea = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.ascender = p22.fword;
    this.descender = p22.fword;
    this.lineGap = p22.fword;
    this.advanceWidthMax = p22.ufword;
    this.minLeftSideBearing = p22.fword;
    this.minRightSideBearing = p22.fword;
    this.xMaxExtent = p22.fword;
    this.caretSlopeRise = p22.int16;
    this.caretSlopeRun = p22.int16;
    this.caretOffset = p22.int16;
    p22.int16;
    p22.int16;
    p22.int16;
    p22.int16;
    this.metricDataFormat = p22.int16;
    this.numberOfHMetrics = p22.uint16;
    p22.verifyLength();
  }
};
var hhea$1 = Object.freeze({ __proto__: null, hhea });
var hmtx = class extends SimpleTable {
  constructor(dict, dataview, tables) {
    const { p: p22 } = super(dict, dataview);
    const numberOfHMetrics = tables.hhea.numberOfHMetrics;
    const numGlyphs = tables.maxp.numGlyphs;
    const metricsStart = p22.currentPosition;
    lazy$1(this, `hMetrics`, () => {
      p22.currentPosition = metricsStart;
      return [...new Array(numberOfHMetrics)].map(
        (_) => new LongHorMetric(p22.uint16, p22.int16)
      );
    });
    if (numberOfHMetrics < numGlyphs) {
      const lsbStart = metricsStart + numberOfHMetrics * 4;
      lazy$1(this, `leftSideBearings`, () => {
        p22.currentPosition = lsbStart;
        return [...new Array(numGlyphs - numberOfHMetrics)].map(
          (_) => p22.int16
        );
      });
    }
  }
};
var LongHorMetric = class {
  constructor(w2, b2) {
    this.advanceWidth = w2;
    this.lsb = b2;
  }
};
var hmtx$1 = Object.freeze({ __proto__: null, hmtx });
var maxp = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.legacyFixed;
    this.numGlyphs = p22.uint16;
    if (this.version === 1) {
      this.maxPoints = p22.uint16;
      this.maxContours = p22.uint16;
      this.maxCompositePoints = p22.uint16;
      this.maxCompositeContours = p22.uint16;
      this.maxZones = p22.uint16;
      this.maxTwilightPoints = p22.uint16;
      this.maxStorage = p22.uint16;
      this.maxFunctionDefs = p22.uint16;
      this.maxInstructionDefs = p22.uint16;
      this.maxStackElements = p22.uint16;
      this.maxSizeOfInstructions = p22.uint16;
      this.maxComponentElements = p22.uint16;
      this.maxComponentDepth = p22.uint16;
    }
    p22.verifyLength();
  }
};
var maxp$1 = Object.freeze({ __proto__: null, maxp });
var name = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.format = p22.uint16;
    this.count = p22.uint16;
    this.stringOffset = p22.Offset16;
    this.nameRecords = [...new Array(this.count)].map(
      (_) => new NameRecord(p22, this)
    );
    if (this.format === 1) {
      this.langTagCount = p22.uint16;
      this.langTagRecords = [...new Array(this.langTagCount)].map(
        (_) => new LangTagRecord(p22.uint16, p22.Offset16)
      );
    }
    this.stringStart = this.tableStart + this.stringOffset;
  }
  get(nameID) {
    let record = this.nameRecords.find(
      (record2) => record2.nameID === nameID
    );
    if (record) return record.string;
  }
};
var LangTagRecord = class {
  constructor(length, offset) {
    this.length = length;
    this.offset = offset;
  }
};
var NameRecord = class {
  constructor(p22, nameTable) {
    this.platformID = p22.uint16;
    this.encodingID = p22.uint16;
    this.languageID = p22.uint16;
    this.nameID = p22.uint16;
    this.length = p22.uint16;
    this.offset = p22.Offset16;
    lazy$1(this, `string`, () => {
      p22.currentPosition = nameTable.stringStart + this.offset;
      return decodeString(p22, this);
    });
  }
};
function decodeString(p22, record) {
  const { platformID, length } = record;
  if (length === 0) return ``;
  if (platformID === 0 || platformID === 3) {
    const str2 = [];
    for (let i2 = 0, e2 = length / 2; i2 < e2; i2++)
      str2[i2] = String.fromCharCode(p22.uint16);
    return str2.join(``);
  }
  const bytes = p22.readBytes(length);
  const str = [];
  bytes.forEach(function(b2, i2) {
    str[i2] = String.fromCharCode(b2);
  });
  return str.join(``);
}
var name$1 = Object.freeze({ __proto__: null, name });
var OS2 = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.xAvgCharWidth = p22.int16;
    this.usWeightClass = p22.uint16;
    this.usWidthClass = p22.uint16;
    this.fsType = p22.uint16;
    this.ySubscriptXSize = p22.int16;
    this.ySubscriptYSize = p22.int16;
    this.ySubscriptXOffset = p22.int16;
    this.ySubscriptYOffset = p22.int16;
    this.ySuperscriptXSize = p22.int16;
    this.ySuperscriptYSize = p22.int16;
    this.ySuperscriptXOffset = p22.int16;
    this.ySuperscriptYOffset = p22.int16;
    this.yStrikeoutSize = p22.int16;
    this.yStrikeoutPosition = p22.int16;
    this.sFamilyClass = p22.int16;
    this.panose = [...new Array(10)].map((_) => p22.uint8);
    this.ulUnicodeRange1 = p22.flags(32);
    this.ulUnicodeRange2 = p22.flags(32);
    this.ulUnicodeRange3 = p22.flags(32);
    this.ulUnicodeRange4 = p22.flags(32);
    this.achVendID = p22.tag;
    this.fsSelection = p22.uint16;
    this.usFirstCharIndex = p22.uint16;
    this.usLastCharIndex = p22.uint16;
    this.sTypoAscender = p22.int16;
    this.sTypoDescender = p22.int16;
    this.sTypoLineGap = p22.int16;
    this.usWinAscent = p22.uint16;
    this.usWinDescent = p22.uint16;
    if (this.version === 0) return p22.verifyLength();
    this.ulCodePageRange1 = p22.flags(32);
    this.ulCodePageRange2 = p22.flags(32);
    if (this.version === 1) return p22.verifyLength();
    this.sxHeight = p22.int16;
    this.sCapHeight = p22.int16;
    this.usDefaultChar = p22.uint16;
    this.usBreakChar = p22.uint16;
    this.usMaxContext = p22.uint16;
    if (this.version <= 4) return p22.verifyLength();
    this.usLowerOpticalPointSize = p22.uint16;
    this.usUpperOpticalPointSize = p22.uint16;
    if (this.version === 5) return p22.verifyLength();
  }
};
var OS2$1 = Object.freeze({ __proto__: null, OS2 });
var post = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.legacyFixed;
    this.italicAngle = p22.fixed;
    this.underlinePosition = p22.fword;
    this.underlineThickness = p22.fword;
    this.isFixedPitch = p22.uint32;
    this.minMemType42 = p22.uint32;
    this.maxMemType42 = p22.uint32;
    this.minMemType1 = p22.uint32;
    this.maxMemType1 = p22.uint32;
    if (this.version === 1 || this.version === 3) return p22.verifyLength();
    this.numGlyphs = p22.uint16;
    if (this.version === 2) {
      this.glyphNameIndex = [...new Array(this.numGlyphs)].map(
        (_) => p22.uint16
      );
      this.namesOffset = p22.currentPosition;
      this.glyphNameOffsets = [1];
      for (let i2 = 0; i2 < this.numGlyphs; i2++) {
        let index = this.glyphNameIndex[i2];
        if (index < macStrings.length) {
          this.glyphNameOffsets.push(this.glyphNameOffsets[i2]);
          continue;
        }
        let bytelength = p22.int8;
        p22.skip(bytelength);
        this.glyphNameOffsets.push(
          this.glyphNameOffsets[i2] + bytelength + 1
        );
      }
    }
    if (this.version === 2.5) {
      this.offset = [...new Array(this.numGlyphs)].map(
        (_) => p22.int8
      );
    }
  }
  getGlyphName(glyphid) {
    if (this.version !== 2) {
      console.warn(
        `post table version ${this.version} does not support glyph name lookups`
      );
      return ``;
    }
    let index = this.glyphNameIndex[glyphid];
    if (index < 258) return macStrings[index];
    let offset = this.glyphNameOffsets[glyphid];
    let next = this.glyphNameOffsets[glyphid + 1];
    let len = next - offset - 1;
    if (len === 0) return `.notdef.`;
    this.parser.currentPosition = this.namesOffset + offset;
    const data = this.parser.readBytes(
      len,
      this.namesOffset + offset,
      8,
      true
    );
    return data.map((b2) => String.fromCharCode(b2)).join(``);
  }
};
var macStrings = [
  `.notdef`,
  `.null`,
  `nonmarkingreturn`,
  `space`,
  `exclam`,
  `quotedbl`,
  `numbersign`,
  `dollar`,
  `percent`,
  `ampersand`,
  `quotesingle`,
  `parenleft`,
  `parenright`,
  `asterisk`,
  `plus`,
  `comma`,
  `hyphen`,
  `period`,
  `slash`,
  `zero`,
  `one`,
  `two`,
  `three`,
  `four`,
  `five`,
  `six`,
  `seven`,
  `eight`,
  `nine`,
  `colon`,
  `semicolon`,
  `less`,
  `equal`,
  `greater`,
  `question`,
  `at`,
  `A`,
  `B`,
  `C`,
  `D`,
  `E`,
  `F`,
  `G`,
  `H`,
  `I`,
  `J`,
  `K`,
  `L`,
  `M`,
  `N`,
  `O`,
  `P`,
  `Q`,
  `R`,
  `S`,
  `T`,
  `U`,
  `V`,
  `W`,
  `X`,
  `Y`,
  `Z`,
  `bracketleft`,
  `backslash`,
  `bracketright`,
  `asciicircum`,
  `underscore`,
  `grave`,
  `a`,
  `b`,
  `c`,
  `d`,
  `e`,
  `f`,
  `g`,
  `h`,
  `i`,
  `j`,
  `k`,
  `l`,
  `m`,
  `n`,
  `o`,
  `p`,
  `q`,
  `r`,
  `s`,
  `t`,
  `u`,
  `v`,
  `w`,
  `x`,
  `y`,
  `z`,
  `braceleft`,
  `bar`,
  `braceright`,
  `asciitilde`,
  `Adieresis`,
  `Aring`,
  `Ccedilla`,
  `Eacute`,
  `Ntilde`,
  `Odieresis`,
  `Udieresis`,
  `aacute`,
  `agrave`,
  `acircumflex`,
  `adieresis`,
  `atilde`,
  `aring`,
  `ccedilla`,
  `eacute`,
  `egrave`,
  `ecircumflex`,
  `edieresis`,
  `iacute`,
  `igrave`,
  `icircumflex`,
  `idieresis`,
  `ntilde`,
  `oacute`,
  `ograve`,
  `ocircumflex`,
  `odieresis`,
  `otilde`,
  `uacute`,
  `ugrave`,
  `ucircumflex`,
  `udieresis`,
  `dagger`,
  `degree`,
  `cent`,
  `sterling`,
  `section`,
  `bullet`,
  `paragraph`,
  `germandbls`,
  `registered`,
  `copyright`,
  `trademark`,
  `acute`,
  `dieresis`,
  `notequal`,
  `AE`,
  `Oslash`,
  `infinity`,
  `plusminus`,
  `lessequal`,
  `greaterequal`,
  `yen`,
  `mu`,
  `partialdiff`,
  `summation`,
  `product`,
  `pi`,
  `integral`,
  `ordfeminine`,
  `ordmasculine`,
  `Omega`,
  `ae`,
  `oslash`,
  `questiondown`,
  `exclamdown`,
  `logicalnot`,
  `radical`,
  `florin`,
  `approxequal`,
  `Delta`,
  `guillemotleft`,
  `guillemotright`,
  `ellipsis`,
  `nonbreakingspace`,
  `Agrave`,
  `Atilde`,
  `Otilde`,
  `OE`,
  `oe`,
  `endash`,
  `emdash`,
  `quotedblleft`,
  `quotedblright`,
  `quoteleft`,
  `quoteright`,
  `divide`,
  `lozenge`,
  `ydieresis`,
  `Ydieresis`,
  `fraction`,
  `currency`,
  `guilsinglleft`,
  `guilsinglright`,
  `fi`,
  `fl`,
  `daggerdbl`,
  `periodcentered`,
  `quotesinglbase`,
  `quotedblbase`,
  `perthousand`,
  `Acircumflex`,
  `Ecircumflex`,
  `Aacute`,
  `Edieresis`,
  `Egrave`,
  `Iacute`,
  `Icircumflex`,
  `Idieresis`,
  `Igrave`,
  `Oacute`,
  `Ocircumflex`,
  `apple`,
  `Ograve`,
  `Uacute`,
  `Ucircumflex`,
  `Ugrave`,
  `dotlessi`,
  `circumflex`,
  `tilde`,
  `macron`,
  `breve`,
  `dotaccent`,
  `ring`,
  `cedilla`,
  `hungarumlaut`,
  `ogonek`,
  `caron`,
  `Lslash`,
  `lslash`,
  `Scaron`,
  `scaron`,
  `Zcaron`,
  `zcaron`,
  `brokenbar`,
  `Eth`,
  `eth`,
  `Yacute`,
  `yacute`,
  `Thorn`,
  `thorn`,
  `minus`,
  `multiply`,
  `onesuperior`,
  `twosuperior`,
  `threesuperior`,
  `onehalf`,
  `onequarter`,
  `threequarters`,
  `franc`,
  `Gbreve`,
  `gbreve`,
  `Idotaccent`,
  `Scedilla`,
  `scedilla`,
  `Cacute`,
  `cacute`,
  `Ccaron`,
  `ccaron`,
  `dcroat`
];
var post$1 = Object.freeze({ __proto__: null, post });
var BASE = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.horizAxisOffset = p22.Offset16;
    this.vertAxisOffset = p22.Offset16;
    lazy$1(
      this,
      `horizAxis`,
      () => new AxisTable(
        { offset: dict.offset + this.horizAxisOffset },
        dataview
      )
    );
    lazy$1(
      this,
      `vertAxis`,
      () => new AxisTable(
        { offset: dict.offset + this.vertAxisOffset },
        dataview
      )
    );
    if (this.majorVersion === 1 && this.minorVersion === 1) {
      this.itemVarStoreOffset = p22.Offset32;
      lazy$1(
        this,
        `itemVarStore`,
        () => new AxisTable(
          { offset: dict.offset + this.itemVarStoreOffset },
          dataview
        )
      );
    }
  }
};
var AxisTable = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview, `AxisTable`);
    this.baseTagListOffset = p22.Offset16;
    this.baseScriptListOffset = p22.Offset16;
    lazy$1(
      this,
      `baseTagList`,
      () => new BaseTagListTable(
        { offset: dict.offset + this.baseTagListOffset },
        dataview
      )
    );
    lazy$1(
      this,
      `baseScriptList`,
      () => new BaseScriptListTable(
        { offset: dict.offset + this.baseScriptListOffset },
        dataview
      )
    );
  }
};
var BaseTagListTable = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview, `BaseTagListTable`);
    this.baseTagCount = p22.uint16;
    this.baselineTags = [...new Array(this.baseTagCount)].map(
      (_) => p22.tag
    );
  }
};
var BaseScriptListTable = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview, `BaseScriptListTable`);
    this.baseScriptCount = p22.uint16;
    const recordStart = p22.currentPosition;
    lazy$1(this, `baseScriptRecords`, () => {
      p22.currentPosition = recordStart;
      return [...new Array(this.baseScriptCount)].map(
        (_) => new BaseScriptRecord(this.start, p22)
      );
    });
  }
};
var BaseScriptRecord = class {
  constructor(baseScriptListTableStart, p22) {
    this.baseScriptTag = p22.tag;
    this.baseScriptOffset = p22.Offset16;
    lazy$1(this, `baseScriptTable`, () => {
      p22.currentPosition = baseScriptListTableStart + this.baseScriptOffset;
      return new BaseScriptTable(p22);
    });
  }
};
var BaseScriptTable = class {
  constructor(p22) {
    this.start = p22.currentPosition;
    this.baseValuesOffset = p22.Offset16;
    this.defaultMinMaxOffset = p22.Offset16;
    this.baseLangSysCount = p22.uint16;
    this.baseLangSysRecords = [...new Array(this.baseLangSysCount)].map(
      (_) => new BaseLangSysRecord(this.start, p22)
    );
    lazy$1(this, `baseValues`, () => {
      p22.currentPosition = this.start + this.baseValuesOffset;
      return new BaseValuesTable(p22);
    });
    lazy$1(this, `defaultMinMax`, () => {
      p22.currentPosition = this.start + this.defaultMinMaxOffset;
      return new MinMaxTable(p22);
    });
  }
};
var BaseLangSysRecord = class {
  constructor(baseScriptTableStart, p22) {
    this.baseLangSysTag = p22.tag;
    this.minMaxOffset = p22.Offset16;
    lazy$1(this, `minMax`, () => {
      p22.currentPosition = baseScriptTableStart + this.minMaxOffset;
      return new MinMaxTable(p22);
    });
  }
};
var BaseValuesTable = class {
  constructor(p22) {
    this.parser = p22;
    this.start = p22.currentPosition;
    this.defaultBaselineIndex = p22.uint16;
    this.baseCoordCount = p22.uint16;
    this.baseCoords = [...new Array(this.baseCoordCount)].map(
      (_) => p22.Offset16
    );
  }
  getTable(id) {
    this.parser.currentPosition = this.start + this.baseCoords[id];
    return new BaseCoordTable(this.parser);
  }
};
var MinMaxTable = class {
  constructor(p22) {
    this.minCoord = p22.Offset16;
    this.maxCoord = p22.Offset16;
    this.featMinMaxCount = p22.uint16;
    const recordStart = p22.currentPosition;
    lazy$1(this, `featMinMaxRecords`, () => {
      p22.currentPosition = recordStart;
      return [...new Array(this.featMinMaxCount)].map(
        (_) => new FeatMinMaxRecord(p22)
      );
    });
  }
};
var FeatMinMaxRecord = class {
  constructor(p22) {
    this.featureTableTag = p22.tag;
    this.minCoord = p22.Offset16;
    this.maxCoord = p22.Offset16;
  }
};
var BaseCoordTable = class {
  constructor(p22) {
    this.baseCoordFormat = p22.uint16;
    this.coordinate = p22.int16;
    if (this.baseCoordFormat === 2) {
      this.referenceGlyph = p22.uint16;
      this.baseCoordPoint = p22.uint16;
    }
    if (this.baseCoordFormat === 3) {
      this.deviceTable = p22.Offset16;
    }
  }
};
var BASE$1 = Object.freeze({ __proto__: null, BASE });
var ClassDefinition = class {
  constructor(p22) {
    this.classFormat = p22.uint16;
    if (this.classFormat === 1) {
      this.startGlyphID = p22.uint16;
      this.glyphCount = p22.uint16;
      this.classValueArray = [...new Array(this.glyphCount)].map(
        (_) => p22.uint16
      );
    }
    if (this.classFormat === 2) {
      this.classRangeCount = p22.uint16;
      this.classRangeRecords = [
        ...new Array(this.classRangeCount)
      ].map((_) => new ClassRangeRecord(p22));
    }
  }
};
var ClassRangeRecord = class {
  constructor(p22) {
    this.startGlyphID = p22.uint16;
    this.endGlyphID = p22.uint16;
    this.class = p22.uint16;
  }
};
var CoverageTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.coverageFormat = p22.uint16;
    if (this.coverageFormat === 1) {
      this.glyphCount = p22.uint16;
      this.glyphArray = [...new Array(this.glyphCount)].map(
        (_) => p22.uint16
      );
    }
    if (this.coverageFormat === 2) {
      this.rangeCount = p22.uint16;
      this.rangeRecords = [...new Array(this.rangeCount)].map(
        (_) => new CoverageRangeRecord(p22)
      );
    }
  }
};
var CoverageRangeRecord = class {
  constructor(p22) {
    this.startGlyphID = p22.uint16;
    this.endGlyphID = p22.uint16;
    this.startCoverageIndex = p22.uint16;
  }
};
var ItemVariationStoreTable = class {
  constructor(table, p22) {
    this.table = table;
    this.parser = p22;
    this.start = p22.currentPosition;
    this.format = p22.uint16;
    this.variationRegionListOffset = p22.Offset32;
    this.itemVariationDataCount = p22.uint16;
    this.itemVariationDataOffsets = [
      ...new Array(this.itemVariationDataCount)
    ].map((_) => p22.Offset32);
  }
};
var GDEF = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.glyphClassDefOffset = p22.Offset16;
    lazy$1(this, `glyphClassDefs`, () => {
      if (this.glyphClassDefOffset === 0) return void 0;
      p22.currentPosition = this.tableStart + this.glyphClassDefOffset;
      return new ClassDefinition(p22);
    });
    this.attachListOffset = p22.Offset16;
    lazy$1(this, `attachList`, () => {
      if (this.attachListOffset === 0) return void 0;
      p22.currentPosition = this.tableStart + this.attachListOffset;
      return new AttachList(p22);
    });
    this.ligCaretListOffset = p22.Offset16;
    lazy$1(this, `ligCaretList`, () => {
      if (this.ligCaretListOffset === 0) return void 0;
      p22.currentPosition = this.tableStart + this.ligCaretListOffset;
      return new LigCaretList(p22);
    });
    this.markAttachClassDefOffset = p22.Offset16;
    lazy$1(this, `markAttachClassDef`, () => {
      if (this.markAttachClassDefOffset === 0) return void 0;
      p22.currentPosition = this.tableStart + this.markAttachClassDefOffset;
      return new ClassDefinition(p22);
    });
    if (this.minorVersion >= 2) {
      this.markGlyphSetsDefOffset = p22.Offset16;
      lazy$1(this, `markGlyphSetsDef`, () => {
        if (this.markGlyphSetsDefOffset === 0) return void 0;
        p22.currentPosition = this.tableStart + this.markGlyphSetsDefOffset;
        return new MarkGlyphSetsTable(p22);
      });
    }
    if (this.minorVersion === 3) {
      this.itemVarStoreOffset = p22.Offset32;
      lazy$1(this, `itemVarStore`, () => {
        if (this.itemVarStoreOffset === 0) return void 0;
        p22.currentPosition = this.tableStart + this.itemVarStoreOffset;
        return new ItemVariationStoreTable(p22);
      });
    }
  }
};
var AttachList = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.coverageOffset = p22.Offset16;
    this.glyphCount = p22.uint16;
    this.attachPointOffsets = [...new Array(this.glyphCount)].map(
      (_) => p22.Offset16
    );
  }
  getPoint(pointID) {
    this.parser.currentPosition = this.start + this.attachPointOffsets[pointID];
    return new AttachPoint(this.parser);
  }
};
var AttachPoint = class {
  constructor(p22) {
    this.pointCount = p22.uint16;
    this.pointIndices = [...new Array(this.pointCount)].map(
      (_) => p22.uint16
    );
  }
};
var LigCaretList = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.coverageOffset = p22.Offset16;
    lazy$1(this, `coverage`, () => {
      p22.currentPosition = this.start + this.coverageOffset;
      return new CoverageTable(p22);
    });
    this.ligGlyphCount = p22.uint16;
    this.ligGlyphOffsets = [...new Array(this.ligGlyphCount)].map(
      (_) => p22.Offset16
    );
  }
  getLigGlyph(ligGlyphID) {
    this.parser.currentPosition = this.start + this.ligGlyphOffsets[ligGlyphID];
    return new LigGlyph(this.parser);
  }
};
var LigGlyph = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.caretCount = p22.uint16;
    this.caretValueOffsets = [...new Array(this.caretCount)].map(
      (_) => p22.Offset16
    );
  }
  getCaretValue(caretID) {
    this.parser.currentPosition = this.start + this.caretValueOffsets[caretID];
    return new CaretValue(this.parser);
  }
};
var CaretValue = class {
  constructor(p22) {
    this.caretValueFormat = p22.uint16;
    if (this.caretValueFormat === 1) {
      this.coordinate = p22.int16;
    }
    if (this.caretValueFormat === 2) {
      this.caretValuePointIndex = p22.uint16;
    }
    if (this.caretValueFormat === 3) {
      this.coordinate = p22.int16;
      this.deviceOffset = p22.Offset16;
    }
  }
};
var MarkGlyphSetsTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.markGlyphSetTableFormat = p22.uint16;
    this.markGlyphSetCount = p22.uint16;
    this.coverageOffsets = [...new Array(this.markGlyphSetCount)].map(
      (_) => p22.Offset32
    );
  }
  getMarkGlyphSet(markGlyphSetID) {
    this.parser.currentPosition = this.start + this.coverageOffsets[markGlyphSetID];
    return new CoverageTable(this.parser);
  }
};
var GDEF$1 = Object.freeze({ __proto__: null, GDEF });
var ScriptList = class extends ParsedData {
  static EMPTY = { scriptCount: 0, scriptRecords: [] };
  constructor(p22) {
    super(p22);
    this.scriptCount = p22.uint16;
    this.scriptRecords = [...new Array(this.scriptCount)].map(
      (_) => new ScriptRecord(p22)
    );
  }
};
var ScriptRecord = class {
  constructor(p22) {
    this.scriptTag = p22.tag;
    this.scriptOffset = p22.Offset16;
  }
};
var ScriptTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.defaultLangSys = p22.Offset16;
    this.langSysCount = p22.uint16;
    this.langSysRecords = [...new Array(this.langSysCount)].map(
      (_) => new LangSysRecord(p22)
    );
  }
};
var LangSysRecord = class {
  constructor(p22) {
    this.langSysTag = p22.tag;
    this.langSysOffset = p22.Offset16;
  }
};
var LangSysTable = class {
  constructor(p22) {
    this.lookupOrder = p22.Offset16;
    this.requiredFeatureIndex = p22.uint16;
    this.featureIndexCount = p22.uint16;
    this.featureIndices = [...new Array(this.featureIndexCount)].map(
      (_) => p22.uint16
    );
  }
};
var FeatureList = class extends ParsedData {
  static EMPTY = { featureCount: 0, featureRecords: [] };
  constructor(p22) {
    super(p22);
    this.featureCount = p22.uint16;
    this.featureRecords = [...new Array(this.featureCount)].map(
      (_) => new FeatureRecord(p22)
    );
  }
};
var FeatureRecord = class {
  constructor(p22) {
    this.featureTag = p22.tag;
    this.featureOffset = p22.Offset16;
  }
};
var FeatureTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.featureParams = p22.Offset16;
    this.lookupIndexCount = p22.uint16;
    this.lookupListIndices = [...new Array(this.lookupIndexCount)].map(
      (_) => p22.uint16
    );
  }
  getFeatureParams() {
    if (this.featureParams > 0) {
      const p22 = this.parser;
      p22.currentPosition = this.start + this.featureParams;
      const tag = this.featureTag;
      if (tag === `size`) return new Size(p22);
      if (tag.startsWith(`cc`)) return new CharacterVariant(p22);
      if (tag.startsWith(`ss`)) return new StylisticSet(p22);
    }
  }
};
var CharacterVariant = class {
  constructor(p22) {
    this.format = p22.uint16;
    this.featUiLabelNameId = p22.uint16;
    this.featUiTooltipTextNameId = p22.uint16;
    this.sampleTextNameId = p22.uint16;
    this.numNamedParameters = p22.uint16;
    this.firstParamUiLabelNameId = p22.uint16;
    this.charCount = p22.uint16;
    this.character = [...new Array(this.charCount)].map(
      (_) => p22.uint24
    );
  }
};
var Size = class {
  constructor(p22) {
    this.designSize = p22.uint16;
    this.subfamilyIdentifier = p22.uint16;
    this.subfamilyNameID = p22.uint16;
    this.smallEnd = p22.uint16;
    this.largeEnd = p22.uint16;
  }
};
var StylisticSet = class {
  constructor(p22) {
    this.version = p22.uint16;
    this.UINameID = p22.uint16;
  }
};
function undoCoverageOffsetParsing(instance) {
  instance.parser.currentPosition -= 2;
  delete instance.coverageOffset;
  delete instance.getCoverageTable;
}
var LookupType$1 = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.substFormat = p22.uint16;
    this.coverageOffset = p22.Offset16;
  }
  getCoverageTable() {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.coverageOffset;
    return new CoverageTable(p22);
  }
};
var SubstLookupRecord = class {
  constructor(p22) {
    this.glyphSequenceIndex = p22.uint16;
    this.lookupListIndex = p22.uint16;
  }
};
var LookupType1$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    this.deltaGlyphID = p22.int16;
  }
};
var LookupType2$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    this.sequenceCount = p22.uint16;
    this.sequenceOffsets = [...new Array(this.sequenceCount)].map(
      (_) => p22.Offset16
    );
  }
  getSequence(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.sequenceOffsets[index];
    return new SequenceTable(p22);
  }
};
var SequenceTable = class {
  constructor(p22) {
    this.glyphCount = p22.uint16;
    this.substituteGlyphIDs = [...new Array(this.glyphCount)].map(
      (_) => p22.uint16
    );
  }
};
var LookupType3$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    this.alternateSetCount = p22.uint16;
    this.alternateSetOffsets = [
      ...new Array(this.alternateSetCount)
    ].map((_) => p22.Offset16);
  }
  getAlternateSet(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.alternateSetOffsets[index];
    return new AlternateSetTable(p22);
  }
};
var AlternateSetTable = class {
  constructor(p22) {
    this.glyphCount = p22.uint16;
    this.alternateGlyphIDs = [...new Array(this.glyphCount)].map(
      (_) => p22.uint16
    );
  }
};
var LookupType4$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    this.ligatureSetCount = p22.uint16;
    this.ligatureSetOffsets = [...new Array(this.ligatureSetCount)].map(
      (_) => p22.Offset16
    );
  }
  getLigatureSet(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.ligatureSetOffsets[index];
    return new LigatureSetTable(p22);
  }
};
var LigatureSetTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.ligatureCount = p22.uint16;
    this.ligatureOffsets = [...new Array(this.ligatureCount)].map(
      (_) => p22.Offset16
    );
  }
  getLigature(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.ligatureOffsets[index];
    return new LigatureTable(p22);
  }
};
var LigatureTable = class {
  constructor(p22) {
    this.ligatureGlyph = p22.uint16;
    this.componentCount = p22.uint16;
    this.componentGlyphIDs = [
      ...new Array(this.componentCount - 1)
    ].map((_) => p22.uint16);
  }
};
var LookupType5$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    if (this.substFormat === 1) {
      this.subRuleSetCount = p22.uint16;
      this.subRuleSetOffsets = [
        ...new Array(this.subRuleSetCount)
      ].map((_) => p22.Offset16);
    }
    if (this.substFormat === 2) {
      this.classDefOffset = p22.Offset16;
      this.subClassSetCount = p22.uint16;
      this.subClassSetOffsets = [
        ...new Array(this.subClassSetCount)
      ].map((_) => p22.Offset16);
    }
    if (this.substFormat === 3) {
      undoCoverageOffsetParsing(this);
      this.glyphCount = p22.uint16;
      this.substitutionCount = p22.uint16;
      this.coverageOffsets = [...new Array(this.glyphCount)].map(
        (_) => p22.Offset16
      );
      this.substLookupRecords = [
        ...new Array(this.substitutionCount)
      ].map((_) => new SubstLookupRecord(p22));
    }
  }
  getSubRuleSet(index) {
    if (this.substFormat !== 1)
      throw new Error(
        `lookup type 5.${this.substFormat} has no subrule sets.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + this.subRuleSetOffsets[index];
    return new SubRuleSetTable(p22);
  }
  getSubClassSet(index) {
    if (this.substFormat !== 2)
      throw new Error(
        `lookup type 5.${this.substFormat} has no subclass sets.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + this.subClassSetOffsets[index];
    return new SubClassSetTable(p22);
  }
  getCoverageTable(index) {
    if (this.substFormat !== 3 && !index)
      return super.getCoverageTable();
    if (!index)
      throw new Error(
        `lookup type 5.${this.substFormat} requires an coverage table index.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + this.coverageOffsets[index];
    return new CoverageTable(p22);
  }
};
var SubRuleSetTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.subRuleCount = p22.uint16;
    this.subRuleOffsets = [...new Array(this.subRuleCount)].map(
      (_) => p22.Offset16
    );
  }
  getSubRule(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.subRuleOffsets[index];
    return new SubRuleTable(p22);
  }
};
var SubRuleTable = class {
  constructor(p22) {
    this.glyphCount = p22.uint16;
    this.substitutionCount = p22.uint16;
    this.inputSequence = [...new Array(this.glyphCount - 1)].map(
      (_) => p22.uint16
    );
    this.substLookupRecords = [
      ...new Array(this.substitutionCount)
    ].map((_) => new SubstLookupRecord(p22));
  }
};
var SubClassSetTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.subClassRuleCount = p22.uint16;
    this.subClassRuleOffsets = [
      ...new Array(this.subClassRuleCount)
    ].map((_) => p22.Offset16);
  }
  getSubClass(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.subClassRuleOffsets[index];
    return new SubClassRuleTable(p22);
  }
};
var SubClassRuleTable = class extends SubRuleTable {
  constructor(p22) {
    super(p22);
  }
};
var LookupType6$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    if (this.substFormat === 1) {
      this.chainSubRuleSetCount = p22.uint16;
      this.chainSubRuleSetOffsets = [
        ...new Array(this.chainSubRuleSetCount)
      ].map((_) => p22.Offset16);
    }
    if (this.substFormat === 2) {
      this.backtrackClassDefOffset = p22.Offset16;
      this.inputClassDefOffset = p22.Offset16;
      this.lookaheadClassDefOffset = p22.Offset16;
      this.chainSubClassSetCount = p22.uint16;
      this.chainSubClassSetOffsets = [
        ...new Array(this.chainSubClassSetCount)
      ].map((_) => p22.Offset16);
    }
    if (this.substFormat === 3) {
      undoCoverageOffsetParsing(this);
      this.backtrackGlyphCount = p22.uint16;
      this.backtrackCoverageOffsets = [
        ...new Array(this.backtrackGlyphCount)
      ].map((_) => p22.Offset16);
      this.inputGlyphCount = p22.uint16;
      this.inputCoverageOffsets = [
        ...new Array(this.inputGlyphCount)
      ].map((_) => p22.Offset16);
      this.lookaheadGlyphCount = p22.uint16;
      this.lookaheadCoverageOffsets = [
        ...new Array(this.lookaheadGlyphCount)
      ].map((_) => p22.Offset16);
      this.seqLookupCount = p22.uint16;
      this.seqLookupRecords = [
        ...new Array(this.substitutionCount)
      ].map((_) => new SequenceLookupRecord(p22));
    }
  }
  getChainSubRuleSet(index) {
    if (this.substFormat !== 1)
      throw new Error(
        `lookup type 6.${this.substFormat} has no chainsubrule sets.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + this.chainSubRuleSetOffsets[index];
    return new ChainSubRuleSetTable(p22);
  }
  getChainSubClassSet(index) {
    if (this.substFormat !== 2)
      throw new Error(
        `lookup type 6.${this.substFormat} has no chainsubclass sets.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + this.chainSubClassSetOffsets[index];
    return new ChainSubClassSetTable(p22);
  }
  getCoverageFromOffset(offset) {
    if (this.substFormat !== 3)
      throw new Error(
        `lookup type 6.${this.substFormat} does not use contextual coverage offsets.`
      );
    let p22 = this.parser;
    p22.currentPosition = this.start + offset;
    return new CoverageTable(p22);
  }
};
var ChainSubRuleSetTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.chainSubRuleCount = p22.uint16;
    this.chainSubRuleOffsets = [
      ...new Array(this.chainSubRuleCount)
    ].map((_) => p22.Offset16);
  }
  getSubRule(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.chainSubRuleOffsets[index];
    return new ChainSubRuleTable(p22);
  }
};
var ChainSubRuleTable = class {
  constructor(p22) {
    this.backtrackGlyphCount = p22.uint16;
    this.backtrackSequence = [
      ...new Array(this.backtrackGlyphCount)
    ].map((_) => p22.uint16);
    this.inputGlyphCount = p22.uint16;
    this.inputSequence = [...new Array(this.inputGlyphCount - 1)].map(
      (_) => p22.uint16
    );
    this.lookaheadGlyphCount = p22.uint16;
    this.lookAheadSequence = [
      ...new Array(this.lookAheadGlyphCount)
    ].map((_) => p22.uint16);
    this.substitutionCount = p22.uint16;
    this.substLookupRecords = [...new Array(this.SubstCount)].map(
      (_) => new SubstLookupRecord(p22)
    );
  }
};
var ChainSubClassSetTable = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.chainSubClassRuleCount = p22.uint16;
    this.chainSubClassRuleOffsets = [
      ...new Array(this.chainSubClassRuleCount)
    ].map((_) => p22.Offset16);
  }
  getSubClass(index) {
    let p22 = this.parser;
    p22.currentPosition = this.start + this.chainSubRuleOffsets[index];
    return new ChainSubClassRuleTable(p22);
  }
};
var ChainSubClassRuleTable = class {
  constructor(p22) {
    this.backtrackGlyphCount = p22.uint16;
    this.backtrackSequence = [
      ...new Array(this.backtrackGlyphCount)
    ].map((_) => p22.uint16);
    this.inputGlyphCount = p22.uint16;
    this.inputSequence = [...new Array(this.inputGlyphCount - 1)].map(
      (_) => p22.uint16
    );
    this.lookaheadGlyphCount = p22.uint16;
    this.lookAheadSequence = [
      ...new Array(this.lookAheadGlyphCount)
    ].map((_) => p22.uint16);
    this.substitutionCount = p22.uint16;
    this.substLookupRecords = [
      ...new Array(this.substitutionCount)
    ].map((_) => new SequenceLookupRecord(p22));
  }
};
var SequenceLookupRecord = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.sequenceIndex = p22.uint16;
    this.lookupListIndex = p22.uint16;
  }
};
var LookupType7$1 = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.substFormat = p22.uint16;
    this.extensionLookupType = p22.uint16;
    this.extensionOffset = p22.Offset32;
  }
};
var LookupType8$1 = class extends LookupType$1 {
  constructor(p22) {
    super(p22);
    this.backtrackGlyphCount = p22.uint16;
    this.backtrackCoverageOffsets = [
      ...new Array(this.backtrackGlyphCount)
    ].map((_) => p22.Offset16);
    this.lookaheadGlyphCount = p22.uint16;
    this.lookaheadCoverageOffsets = [
      new Array(this.lookaheadGlyphCount)
    ].map((_) => p22.Offset16);
    this.glyphCount = p22.uint16;
    this.substituteGlyphIDs = [...new Array(this.glyphCount)].map(
      (_) => p22.uint16
    );
  }
};
var GSUBtables = {
  buildSubtable: function(type, p22) {
    const subtable = new [
      void 0,
      LookupType1$1,
      LookupType2$1,
      LookupType3$1,
      LookupType4$1,
      LookupType5$1,
      LookupType6$1,
      LookupType7$1,
      LookupType8$1
    ][type](p22);
    subtable.type = type;
    return subtable;
  }
};
var LookupType = class extends ParsedData {
  constructor(p22) {
    super(p22);
  }
};
var LookupType1 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 1`);
  }
};
var LookupType2 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 2`);
  }
};
var LookupType3 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 3`);
  }
};
var LookupType4 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 4`);
  }
};
var LookupType5 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 5`);
  }
};
var LookupType6 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 6`);
  }
};
var LookupType7 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 7`);
  }
};
var LookupType8 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 8`);
  }
};
var LookupType9 = class extends LookupType {
  constructor(p22) {
    super(p22);
    console.log(`lookup type 9`);
  }
};
var GPOStables = {
  buildSubtable: function(type, p22) {
    const subtable = new [
      void 0,
      LookupType1,
      LookupType2,
      LookupType3,
      LookupType4,
      LookupType5,
      LookupType6,
      LookupType7,
      LookupType8,
      LookupType9
    ][type](p22);
    subtable.type = type;
    return subtable;
  }
};
var LookupList = class extends ParsedData {
  static EMPTY = { lookupCount: 0, lookups: [] };
  constructor(p22) {
    super(p22);
    this.lookupCount = p22.uint16;
    this.lookups = [...new Array(this.lookupCount)].map(
      (_) => p22.Offset16
    );
  }
};
var LookupTable = class extends ParsedData {
  constructor(p22, type) {
    super(p22);
    this.ctType = type;
    this.lookupType = p22.uint16;
    this.lookupFlag = p22.uint16;
    this.subTableCount = p22.uint16;
    this.subtableOffsets = [...new Array(this.subTableCount)].map(
      (_) => p22.Offset16
    );
    this.markFilteringSet = p22.uint16;
  }
  get rightToLeft() {
    return this.lookupFlag & true;
  }
  get ignoreBaseGlyphs() {
    return this.lookupFlag & true;
  }
  get ignoreLigatures() {
    return this.lookupFlag & true;
  }
  get ignoreMarks() {
    return this.lookupFlag & true;
  }
  get useMarkFilteringSet() {
    return this.lookupFlag & true;
  }
  get markAttachmentType() {
    return this.lookupFlag & true;
  }
  getSubTable(index) {
    const builder = this.ctType === `GSUB` ? GSUBtables : GPOStables;
    this.parser.currentPosition = this.start + this.subtableOffsets[index];
    return builder.buildSubtable(this.lookupType, this.parser);
  }
};
var CommonLayoutTable = class extends SimpleTable {
  constructor(dict, dataview, name2) {
    const { p: p22, tableStart } = super(dict, dataview, name2);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.scriptListOffset = p22.Offset16;
    this.featureListOffset = p22.Offset16;
    this.lookupListOffset = p22.Offset16;
    if (this.majorVersion === 1 && this.minorVersion === 1) {
      this.featureVariationsOffset = p22.Offset32;
    }
    const no_content = !(this.scriptListOffset || this.featureListOffset || this.lookupListOffset);
    lazy$1(this, `scriptList`, () => {
      if (no_content) return ScriptList.EMPTY;
      p22.currentPosition = tableStart + this.scriptListOffset;
      return new ScriptList(p22);
    });
    lazy$1(this, `featureList`, () => {
      if (no_content) return FeatureList.EMPTY;
      p22.currentPosition = tableStart + this.featureListOffset;
      return new FeatureList(p22);
    });
    lazy$1(this, `lookupList`, () => {
      if (no_content) return LookupList.EMPTY;
      p22.currentPosition = tableStart + this.lookupListOffset;
      return new LookupList(p22);
    });
    if (this.featureVariationsOffset) {
      lazy$1(this, `featureVariations`, () => {
        if (no_content) return FeatureVariations.EMPTY;
        p22.currentPosition = tableStart + this.featureVariationsOffset;
        return new FeatureVariations(p22);
      });
    }
  }
  getSupportedScripts() {
    return this.scriptList.scriptRecords.map((r3) => r3.scriptTag);
  }
  getScriptTable(scriptTag) {
    let record = this.scriptList.scriptRecords.find(
      (r3) => r3.scriptTag === scriptTag
    );
    this.parser.currentPosition = this.scriptList.start + record.scriptOffset;
    let table = new ScriptTable(this.parser);
    table.scriptTag = scriptTag;
    return table;
  }
  ensureScriptTable(arg) {
    if (typeof arg === "string") {
      return this.getScriptTable(arg);
    }
    return arg;
  }
  getSupportedLangSys(scriptTable) {
    scriptTable = this.ensureScriptTable(scriptTable);
    const hasDefault = scriptTable.defaultLangSys !== 0;
    const supported = scriptTable.langSysRecords.map(
      (l2) => l2.langSysTag
    );
    if (hasDefault) supported.unshift(`dflt`);
    return supported;
  }
  getDefaultLangSysTable(scriptTable) {
    scriptTable = this.ensureScriptTable(scriptTable);
    let offset = scriptTable.defaultLangSys;
    if (offset !== 0) {
      this.parser.currentPosition = scriptTable.start + offset;
      let table = new LangSysTable(this.parser);
      table.langSysTag = ``;
      table.defaultForScript = scriptTable.scriptTag;
      return table;
    }
  }
  getLangSysTable(scriptTable, langSysTag = `dflt`) {
    if (langSysTag === `dflt`)
      return this.getDefaultLangSysTable(scriptTable);
    scriptTable = this.ensureScriptTable(scriptTable);
    let record = scriptTable.langSysRecords.find(
      (l2) => l2.langSysTag === langSysTag
    );
    this.parser.currentPosition = scriptTable.start + record.langSysOffset;
    let table = new LangSysTable(this.parser);
    table.langSysTag = langSysTag;
    return table;
  }
  getFeatures(langSysTable) {
    return langSysTable.featureIndices.map(
      (index) => this.getFeature(index)
    );
  }
  getFeature(indexOrTag) {
    let record;
    if (parseInt(indexOrTag) == indexOrTag) {
      record = this.featureList.featureRecords[indexOrTag];
    } else {
      record = this.featureList.featureRecords.find(
        (f2) => f2.featureTag === indexOrTag
      );
    }
    if (!record) return;
    this.parser.currentPosition = this.featureList.start + record.featureOffset;
    let table = new FeatureTable(this.parser);
    table.featureTag = record.featureTag;
    return table;
  }
  getLookups(featureTable) {
    return featureTable.lookupListIndices.map(
      (index) => this.getLookup(index)
    );
  }
  getLookup(lookupIndex, type) {
    let lookupOffset = this.lookupList.lookups[lookupIndex];
    this.parser.currentPosition = this.lookupList.start + lookupOffset;
    return new LookupTable(this.parser, type);
  }
};
var GSUB = class extends CommonLayoutTable {
  constructor(dict, dataview) {
    super(dict, dataview, `GSUB`);
  }
  getLookup(lookupIndex) {
    return super.getLookup(lookupIndex, `GSUB`);
  }
};
var GSUB$1 = Object.freeze({ __proto__: null, GSUB });
var GPOS = class extends CommonLayoutTable {
  constructor(dict, dataview) {
    super(dict, dataview, `GPOS`);
  }
  getLookup(lookupIndex) {
    return super.getLookup(lookupIndex, `GPOS`);
  }
};
var GPOS$1 = Object.freeze({ __proto__: null, GPOS });
var SVG6 = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.offsetToSVGDocumentList = p22.Offset32;
    p22.currentPosition = this.tableStart + this.offsetToSVGDocumentList;
    this.documentList = new SVGDocumentList(p22);
  }
};
var SVGDocumentList = class extends ParsedData {
  constructor(p22) {
    super(p22);
    this.numEntries = p22.uint16;
    this.documentRecords = [...new Array(this.numEntries)].map(
      (_) => new SVGDocumentRecord(p22)
    );
  }
  getDocument(documentID) {
    let record = this.documentRecords[documentID];
    if (!record) return "";
    let offset = this.start + record.svgDocOffset;
    this.parser.currentPosition = offset;
    return this.parser.readBytes(record.svgDocLength);
  }
  getDocumentForGlyph(glyphID) {
    let id = this.documentRecords.findIndex(
      (d2) => d2.startGlyphID <= glyphID && glyphID <= d2.endGlyphID
    );
    if (id === -1) return "";
    return this.getDocument(id);
  }
};
var SVGDocumentRecord = class {
  constructor(p22) {
    this.startGlyphID = p22.uint16;
    this.endGlyphID = p22.uint16;
    this.svgDocOffset = p22.Offset32;
    this.svgDocLength = p22.uint32;
  }
};
var SVG$1 = Object.freeze({ __proto__: null, SVG: SVG6 });
var fvar = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.axesArrayOffset = p22.Offset16;
    p22.uint16;
    this.axisCount = p22.uint16;
    this.axisSize = p22.uint16;
    this.instanceCount = p22.uint16;
    this.instanceSize = p22.uint16;
    const axisStart = this.tableStart + this.axesArrayOffset;
    lazy$1(this, `axes`, () => {
      p22.currentPosition = axisStart;
      return [...new Array(this.axisCount)].map(
        (_) => new VariationAxisRecord(p22)
      );
    });
    const instanceStart = axisStart + this.axisCount * this.axisSize;
    lazy$1(this, `instances`, () => {
      let instances = [];
      for (let i2 = 0; i2 < this.instanceCount; i2++) {
        p22.currentPosition = instanceStart + i2 * this.instanceSize;
        instances.push(
          new InstanceRecord(p22, this.axisCount, this.instanceSize)
        );
      }
      return instances;
    });
  }
  getSupportedAxes() {
    return this.axes.map((a2) => a2.tag);
  }
  getAxis(name2) {
    return this.axes.find((a2) => a2.tag === name2);
  }
};
var VariationAxisRecord = class {
  constructor(p22) {
    this.tag = p22.tag;
    this.minValue = p22.fixed;
    this.defaultValue = p22.fixed;
    this.maxValue = p22.fixed;
    this.flags = p22.flags(16);
    this.axisNameID = p22.uint16;
  }
};
var InstanceRecord = class {
  constructor(p22, axisCount, size) {
    let start = p22.currentPosition;
    this.subfamilyNameID = p22.uint16;
    p22.uint16;
    this.coordinates = [...new Array(axisCount)].map(
      (_) => p22.fixed
    );
    if (p22.currentPosition - start < size) {
      this.postScriptNameID = p22.uint16;
    }
  }
};
var fvar$1 = Object.freeze({ __proto__: null, fvar });
var cvt = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    const n2 = dict.length / 2;
    lazy$1(
      this,
      `items`,
      () => [...new Array(n2)].map((_) => p22.fword)
    );
  }
};
var cvt$1 = Object.freeze({ __proto__: null, cvt });
var fpgm = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    lazy$1(
      this,
      `instructions`,
      () => [...new Array(dict.length)].map((_) => p22.uint8)
    );
  }
};
var fpgm$1 = Object.freeze({ __proto__: null, fpgm });
var gasp = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numRanges = p22.uint16;
    const getter = () => [...new Array(this.numRanges)].map(
      (_) => new GASPRange(p22)
    );
    lazy$1(this, `gaspRanges`, getter);
  }
};
var GASPRange = class {
  constructor(p22) {
    this.rangeMaxPPEM = p22.uint16;
    this.rangeGaspBehavior = p22.uint16;
  }
};
var gasp$1 = Object.freeze({ __proto__: null, gasp });
var glyf = class extends SimpleTable {
  constructor(dict, dataview) {
    super(dict, dataview);
  }
  getGlyphData(offset, length) {
    this.parser.currentPosition = this.tableStart + offset;
    return this.parser.readBytes(length);
  }
};
var glyf$1 = Object.freeze({ __proto__: null, glyf });
var loca = class extends SimpleTable {
  constructor(dict, dataview, tables) {
    const { p: p22 } = super(dict, dataview);
    const n2 = tables.maxp.numGlyphs + 1;
    if (tables.head.indexToLocFormat === 0) {
      this.x2 = true;
      lazy$1(
        this,
        `offsets`,
        () => [...new Array(n2)].map((_) => p22.Offset16)
      );
    } else {
      lazy$1(
        this,
        `offsets`,
        () => [...new Array(n2)].map((_) => p22.Offset32)
      );
    }
  }
  getGlyphDataOffsetAndLength(glyphID) {
    let offset = this.offsets[glyphID] * this.x2 ? 2 : 1;
    let nextOffset = this.offsets[glyphID + 1] * this.x2 ? 2 : 1;
    return { offset, length: nextOffset - offset };
  }
};
var loca$1 = Object.freeze({ __proto__: null, loca });
var prep = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    lazy$1(
      this,
      `instructions`,
      () => [...new Array(dict.length)].map((_) => p22.uint8)
    );
  }
};
var prep$1 = Object.freeze({ __proto__: null, prep });
var CFF = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    lazy$1(this, `data`, () => p22.readBytes());
  }
};
var CFF$1 = Object.freeze({ __proto__: null, CFF });
var CFF2 = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    lazy$1(this, `data`, () => p22.readBytes());
  }
};
var CFF2$1 = Object.freeze({ __proto__: null, CFF2 });
var VORG = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.defaultVertOriginY = p22.int16;
    this.numVertOriginYMetrics = p22.uint16;
    lazy$1(
      this,
      `vertORiginYMetrics`,
      () => [...new Array(this.numVertOriginYMetrics)].map(
        (_) => new VertOriginYMetric(p22)
      )
    );
  }
};
var VertOriginYMetric = class {
  constructor(p22) {
    this.glyphIndex = p22.uint16;
    this.vertOriginY = p22.int16;
  }
};
var VORG$1 = Object.freeze({ __proto__: null, VORG });
var BitmapSize = class {
  constructor(p22) {
    this.indexSubTableArrayOffset = p22.Offset32;
    this.indexTablesSize = p22.uint32;
    this.numberofIndexSubTables = p22.uint32;
    this.colorRef = p22.uint32;
    this.hori = new SbitLineMetrics(p22);
    this.vert = new SbitLineMetrics(p22);
    this.startGlyphIndex = p22.uint16;
    this.endGlyphIndex = p22.uint16;
    this.ppemX = p22.uint8;
    this.ppemY = p22.uint8;
    this.bitDepth = p22.uint8;
    this.flags = p22.int8;
  }
};
var BitmapScale = class {
  constructor(p22) {
    this.hori = new SbitLineMetrics(p22);
    this.vert = new SbitLineMetrics(p22);
    this.ppemX = p22.uint8;
    this.ppemY = p22.uint8;
    this.substitutePpemX = p22.uint8;
    this.substitutePpemY = p22.uint8;
  }
};
var SbitLineMetrics = class {
  constructor(p22) {
    this.ascender = p22.int8;
    this.descender = p22.int8;
    this.widthMax = p22.uint8;
    this.caretSlopeNumerator = p22.int8;
    this.caretSlopeDenominator = p22.int8;
    this.caretOffset = p22.int8;
    this.minOriginSB = p22.int8;
    this.minAdvanceSB = p22.int8;
    this.maxBeforeBL = p22.int8;
    this.minAfterBL = p22.int8;
    this.pad1 = p22.int8;
    this.pad2 = p22.int8;
  }
};
var EBLC = class extends SimpleTable {
  constructor(dict, dataview, name2) {
    const { p: p22 } = super(dict, dataview, name2);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.numSizes = p22.uint32;
    lazy$1(
      this,
      `bitMapSizes`,
      () => [...new Array(this.numSizes)].map(
        (_) => new BitmapSize(p22)
      )
    );
  }
};
var EBLC$1 = Object.freeze({ __proto__: null, EBLC });
var EBDT = class extends SimpleTable {
  constructor(dict, dataview, name2) {
    const { p: p22 } = super(dict, dataview, name2);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
  }
};
var EBDT$1 = Object.freeze({ __proto__: null, EBDT });
var EBSC = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.majorVersion = p22.uint16;
    this.minorVersion = p22.uint16;
    this.numSizes = p22.uint32;
    lazy$1(
      this,
      `bitmapScales`,
      () => [...new Array(this.numSizes)].map(
        (_) => new BitmapScale(p22)
      )
    );
  }
};
var EBSC$1 = Object.freeze({ __proto__: null, EBSC });
var CBLC = class extends EBLC {
  constructor(dict, dataview) {
    super(dict, dataview, `CBLC`);
  }
};
var CBLC$1 = Object.freeze({ __proto__: null, CBLC });
var CBDT = class extends EBDT {
  constructor(dict, dataview) {
    super(dict, dataview, `CBDT`);
  }
};
var CBDT$1 = Object.freeze({ __proto__: null, CBDT });
var sbix = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.flags = p22.flags(16);
    this.numStrikes = p22.uint32;
    lazy$1(
      this,
      `strikeOffsets`,
      () => [...new Array(this.numStrikes)].map((_) => p22.Offset32)
    );
  }
};
var sbix$1 = Object.freeze({ __proto__: null, sbix });
var COLR = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numBaseGlyphRecords = p22.uint16;
    this.baseGlyphRecordsOffset = p22.Offset32;
    this.layerRecordsOffset = p22.Offset32;
    this.numLayerRecords = p22.uint16;
  }
  getBaseGlyphRecord(glyphID) {
    let start = this.tableStart + this.baseGlyphRecordsOffset;
    this.parser.currentPosition = start;
    let first = new BaseGlyphRecord(this.parser);
    let firstID = first.gID;
    let end = this.tableStart + this.layerRecordsOffset - 6;
    this.parser.currentPosition = end;
    let last = new BaseGlyphRecord(this.parser);
    let lastID = last.gID;
    if (firstID === glyphID) return first;
    if (lastID === glyphID) return last;
    while (true) {
      if (start === end) break;
      let mid = start + (end - start) / 12;
      this.parser.currentPosition = mid;
      let middle = new BaseGlyphRecord(this.parser);
      let midID = middle.gID;
      if (midID === glyphID) return middle;
      else if (midID > glyphID) {
        end = mid;
      } else if (midID < glyphID) {
        start = mid;
      }
    }
    return false;
  }
  getLayers(glyphID) {
    let record = this.getBaseGlyphRecord(glyphID);
    this.parser.currentPosition = this.tableStart + this.layerRecordsOffset + 4 * record.firstLayerIndex;
    return [...new Array(record.numLayers)].map(
      (_) => new LayerRecord(p)
    );
  }
};
var BaseGlyphRecord = class {
  constructor(p22) {
    this.gID = p22.uint16;
    this.firstLayerIndex = p22.uint16;
    this.numLayers = p22.uint16;
  }
};
var LayerRecord = class {
  constructor(p22) {
    this.gID = p22.uint16;
    this.paletteIndex = p22.uint16;
  }
};
var COLR$1 = Object.freeze({ __proto__: null, COLR });
var CPAL = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numPaletteEntries = p22.uint16;
    const numPalettes = this.numPalettes = p22.uint16;
    this.numColorRecords = p22.uint16;
    this.offsetFirstColorRecord = p22.Offset32;
    this.colorRecordIndices = [...new Array(this.numPalettes)].map(
      (_) => p22.uint16
    );
    lazy$1(this, `colorRecords`, () => {
      p22.currentPosition = this.tableStart + this.offsetFirstColorRecord;
      return [...new Array(this.numColorRecords)].map(
        (_) => new ColorRecord(p22)
      );
    });
    if (this.version === 1) {
      this.offsetPaletteTypeArray = p22.Offset32;
      this.offsetPaletteLabelArray = p22.Offset32;
      this.offsetPaletteEntryLabelArray = p22.Offset32;
      lazy$1(this, `paletteTypeArray`, () => {
        p22.currentPosition = this.tableStart + this.offsetPaletteTypeArray;
        return new PaletteTypeArray(p22, numPalettes);
      });
      lazy$1(this, `paletteLabelArray`, () => {
        p22.currentPosition = this.tableStart + this.offsetPaletteLabelArray;
        return new PaletteLabelsArray(p22, numPalettes);
      });
      lazy$1(this, `paletteEntryLabelArray`, () => {
        p22.currentPosition = this.tableStart + this.offsetPaletteEntryLabelArray;
        return new PaletteEntryLabelArray(p22, numPalettes);
      });
    }
  }
};
var ColorRecord = class {
  constructor(p22) {
    this.blue = p22.uint8;
    this.green = p22.uint8;
    this.red = p22.uint8;
    this.alpha = p22.uint8;
  }
};
var PaletteTypeArray = class {
  constructor(p22, numPalettes) {
    this.paletteTypes = [...new Array(numPalettes)].map(
      (_) => p22.uint32
    );
  }
};
var PaletteLabelsArray = class {
  constructor(p22, numPalettes) {
    this.paletteLabels = [...new Array(numPalettes)].map(
      (_) => p22.uint16
    );
  }
};
var PaletteEntryLabelArray = class {
  constructor(p22, numPalettes) {
    this.paletteEntryLabels = [...new Array(numPalettes)].map(
      (_) => p22.uint16
    );
  }
};
var CPAL$1 = Object.freeze({ __proto__: null, CPAL });
var DSIG = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint32;
    this.numSignatures = p22.uint16;
    this.flags = p22.uint16;
    this.signatureRecords = [...new Array(this.numSignatures)].map(
      (_) => new SignatureRecord(p22)
    );
  }
  getData(signatureID) {
    const record = this.signatureRecords[signatureID];
    this.parser.currentPosition = this.tableStart + record.offset;
    return new SignatureBlockFormat1(this.parser);
  }
};
var SignatureRecord = class {
  constructor(p22) {
    this.format = p22.uint32;
    this.length = p22.uint32;
    this.offset = p22.Offset32;
  }
};
var SignatureBlockFormat1 = class {
  constructor(p22) {
    p22.uint16;
    p22.uint16;
    this.signatureLength = p22.uint32;
    this.signature = p22.readBytes(this.signatureLength);
  }
};
var DSIG$1 = Object.freeze({ __proto__: null, DSIG });
var hdmx = class extends SimpleTable {
  constructor(dict, dataview, tables) {
    const { p: p22 } = super(dict, dataview);
    const numGlyphs = tables.hmtx.numGlyphs;
    this.version = p22.uint16;
    this.numRecords = p22.int16;
    this.sizeDeviceRecord = p22.int32;
    this.records = [...new Array(numRecords)].map(
      (_) => new DeviceRecord(p22, numGlyphs)
    );
  }
};
var DeviceRecord = class {
  constructor(p22, numGlyphs) {
    this.pixelSize = p22.uint8;
    this.maxWidth = p22.uint8;
    this.widths = p22.readBytes(numGlyphs);
  }
};
var hdmx$1 = Object.freeze({ __proto__: null, hdmx });
var kern = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.nTables = p22.uint16;
    lazy$1(this, `tables`, () => {
      let offset = this.tableStart + 4;
      const tables = [];
      for (let i2 = 0; i2 < this.nTables; i2++) {
        p22.currentPosition = offset;
        let subtable = new KernSubTable(p22);
        tables.push(subtable);
        offset += subtable;
      }
      return tables;
    });
  }
};
var KernSubTable = class {
  constructor(p22) {
    this.version = p22.uint16;
    this.length = p22.uint16;
    this.coverage = p22.flags(8);
    this.format = p22.uint8;
    if (this.format === 0) {
      this.nPairs = p22.uint16;
      this.searchRange = p22.uint16;
      this.entrySelector = p22.uint16;
      this.rangeShift = p22.uint16;
      lazy$1(
        this,
        `pairs`,
        () => [...new Array(this.nPairs)].map((_) => new Pair(p22))
      );
    }
    if (this.format === 2) {
      console.warn(
        `Kern subtable format 2 is not supported: this parser currently only parses universal table data.`
      );
    }
  }
  get horizontal() {
    return this.coverage[0];
  }
  get minimum() {
    return this.coverage[1];
  }
  get crossstream() {
    return this.coverage[2];
  }
  get override() {
    return this.coverage[3];
  }
};
var Pair = class {
  constructor(p22) {
    this.left = p22.uint16;
    this.right = p22.uint16;
    this.value = p22.fword;
  }
};
var kern$1 = Object.freeze({ __proto__: null, kern });
var LTSH = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numGlyphs = p22.uint16;
    this.yPels = p22.readBytes(this.numGlyphs);
  }
};
var LTSH$1 = Object.freeze({ __proto__: null, LTSH });
var MERG = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.mergeClassCount = p22.uint16;
    this.mergeDataOffset = p22.Offset16;
    this.classDefCount = p22.uint16;
    this.offsetToClassDefOffsets = p22.Offset16;
    lazy$1(
      this,
      `mergeEntryMatrix`,
      () => [...new Array(this.mergeClassCount)].map(
        (_) => p22.readBytes(this.mergeClassCount)
      )
    );
    console.warn(`Full MERG parsing is currently not supported.`);
    console.warn(
      `If you need this table parsed, please file an issue, or better yet, a PR.`
    );
  }
};
var MERG$1 = Object.freeze({ __proto__: null, MERG });
var meta = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint32;
    this.flags = p22.uint32;
    p22.uint32;
    this.dataMapsCount = p22.uint32;
    this.dataMaps = [...new Array(this.dataMapsCount)].map(
      (_) => new DataMap(this.tableStart, p22)
    );
  }
};
var DataMap = class {
  constructor(tableStart, p22) {
    this.tableStart = tableStart;
    this.parser = p22;
    this.tag = p22.tag;
    this.dataOffset = p22.Offset32;
    this.dataLength = p22.uint32;
  }
  getData() {
    this.parser.currentField = this.tableStart + this.dataOffset;
    return this.parser.readBytes(this.dataLength);
  }
};
var meta$1 = Object.freeze({ __proto__: null, meta });
var PCLT = class extends SimpleTable {
  constructor(dict, dataview) {
    super(dict, dataview);
    console.warn(
      `This font uses a PCLT table, which is currently not supported by this parser.`
    );
    console.warn(
      `If you need this table parsed, please file an issue, or better yet, a PR.`
    );
  }
};
var PCLT$1 = Object.freeze({ __proto__: null, PCLT });
var VDMX = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.uint16;
    this.numRecs = p22.uint16;
    this.numRatios = p22.uint16;
    this.ratRanges = [...new Array(this.numRatios)].map(
      (_) => new RatioRange(p22)
    );
    this.offsets = [...new Array(this.numRatios)].map(
      (_) => p22.Offset16
    );
    this.VDMXGroups = [...new Array(this.numRecs)].map(
      (_) => new VDMXGroup(p22)
    );
  }
};
var RatioRange = class {
  constructor(p22) {
    this.bCharSet = p22.uint8;
    this.xRatio = p22.uint8;
    this.yStartRatio = p22.uint8;
    this.yEndRatio = p22.uint8;
  }
};
var VDMXGroup = class {
  constructor(p22) {
    this.recs = p22.uint16;
    this.startsz = p22.uint8;
    this.endsz = p22.uint8;
    this.records = [...new Array(this.recs)].map(
      (_) => new vTable(p22)
    );
  }
};
var vTable = class {
  constructor(p22) {
    this.yPelHeight = p22.uint16;
    this.yMax = p22.int16;
    this.yMin = p22.int16;
  }
};
var VDMX$1 = Object.freeze({ __proto__: null, VDMX });
var vhea = class extends SimpleTable {
  constructor(dict, dataview) {
    const { p: p22 } = super(dict, dataview);
    this.version = p22.fixed;
    this.ascent = this.vertTypoAscender = p22.int16;
    this.descent = this.vertTypoDescender = p22.int16;
    this.lineGap = this.vertTypoLineGap = p22.int16;
    this.advanceHeightMax = p22.int16;
    this.minTopSideBearing = p22.int16;
    this.minBottomSideBearing = p22.int16;
    this.yMaxExtent = p22.int16;
    this.caretSlopeRise = p22.int16;
    this.caretSlopeRun = p22.int16;
    this.caretOffset = p22.int16;
    this.reserved = p22.int16;
    this.reserved = p22.int16;
    this.reserved = p22.int16;
    this.reserved = p22.int16;
    this.metricDataFormat = p22.int16;
    this.numOfLongVerMetrics = p22.uint16;
    p22.verifyLength();
  }
};
var vhea$1 = Object.freeze({ __proto__: null, vhea });
var vmtx = class extends SimpleTable {
  constructor(dict, dataview, tables) {
    super(dict, dataview);
    const numOfLongVerMetrics = tables.vhea.numOfLongVerMetrics;
    const numGlyphs = tables.maxp.numGlyphs;
    const metricsStart = p.currentPosition;
    lazy(this, `vMetrics`, () => {
      p.currentPosition = metricsStart;
      return [...new Array(numOfLongVerMetrics)].map(
        (_) => new LongVertMetric(p.uint16, p.int16)
      );
    });
    if (numOfLongVerMetrics < numGlyphs) {
      const tsbStart = metricsStart + numOfLongVerMetrics * 4;
      lazy(this, `topSideBearings`, () => {
        p.currentPosition = tsbStart;
        return [...new Array(numGlyphs - numOfLongVerMetrics)].map(
          (_) => p.int16
        );
      });
    }
  }
};
var LongVertMetric = class {
  constructor(h2, b2) {
    this.advanceHeight = h2;
    this.topSideBearing = b2;
  }
};
var vmtx$1 = Object.freeze({ __proto__: null, vmtx });

// packages/global-styles-ui/build-module/font-library/utils/make-families-from-faces.js
var import_components29 = __toESM(require_components());
var { kebabCase: kebabCase2 } = unlock(import_components29.privateApis);
function makeFamiliesFromFaces(fontFaces) {
  const fontFamiliesObject = fontFaces.reduce(
    (acc, item) => {
      if (!acc[item.fontFamily]) {
        acc[item.fontFamily] = {
          name: item.fontFamily,
          fontFamily: item.fontFamily,
          slug: kebabCase2(item.fontFamily.toLowerCase()),
          fontFace: []
        };
      }
      acc[item.fontFamily].fontFace.push(item);
      return acc;
    },
    {}
  );
  return Object.values(fontFamiliesObject);
}

// packages/global-styles-ui/build-module/font-library/upload-fonts.js
var import_jsx_runtime37 = __toESM(require_jsx_runtime());
function UploadFonts() {
  const { installFonts } = (0, import_element18.useContext)(FontLibraryContext);
  const [isUploading, setIsUploading] = (0, import_element18.useState)(false);
  const [notice, setNotice] = (0, import_element18.useState)(null);
  const handleDropZone = (files) => {
    handleFilesUpload(files);
  };
  const onFilesUpload = (event) => {
    handleFilesUpload(event.target.files);
  };
  const handleFilesUpload = async (files) => {
    if (!files) {
      return;
    }
    setNotice(null);
    setIsUploading(true);
    const uniqueFilenames = /* @__PURE__ */ new Set();
    const selectedFiles = [...files];
    let hasInvalidFiles = false;
    const checkFilesPromises = selectedFiles.map(async (file) => {
      const isFont = await isFontFile(file);
      if (!isFont) {
        hasInvalidFiles = true;
        return null;
      }
      if (uniqueFilenames.has(file.name)) {
        return null;
      }
      const fileExtension = (((file.name ?? "").split(".") ?? []).pop() ?? "").toLowerCase();
      if (ALLOWED_FILE_EXTENSIONS.includes(fileExtension)) {
        uniqueFilenames.add(file.name);
        return file;
      }
      return null;
    });
    const allowedFiles = (await Promise.all(checkFilesPromises)).filter((file) => null !== file);
    if (allowedFiles.length > 0) {
      loadFiles(allowedFiles);
    } else {
      const message = hasInvalidFiles ? (0, import_i18n16.__)("Sorry, you are not allowed to upload this file type.") : (0, import_i18n16.__)("No fonts found to install.");
      setNotice({
        type: "error",
        message
      });
      setIsUploading(false);
    }
  };
  const loadFiles = async (files) => {
    const fontFacesLoaded = await Promise.all(
      files.map(async (fontFile) => {
        const fontFaceData = await getFontFaceMetadata(fontFile);
        await loadFontFaceInBrowser(
          fontFaceData,
          fontFaceData.file,
          "all"
        );
        return fontFaceData;
      })
    );
    handleInstall(fontFacesLoaded);
  };
  async function isFontFile(file) {
    const font2 = new Font("Uploaded Font");
    try {
      const buffer = await readFileAsArrayBuffer(file);
      await font2.fromDataBuffer(buffer, "font");
      return true;
    } catch (error) {
      return false;
    }
  }
  async function readFileAsArrayBuffer(file) {
    return new Promise((resolve, reject) => {
      const reader = new window.FileReader();
      reader.readAsArrayBuffer(file);
      reader.onload = () => resolve(reader.result);
      reader.onerror = reject;
    });
  }
  const getFontFaceMetadata = async (fontFile) => {
    const buffer = await readFileAsArrayBuffer(fontFile);
    const fontObj = new Font("Uploaded Font");
    fontObj.fromDataBuffer(buffer, fontFile.name);
    const onloadEvent = await new Promise(
      (resolve) => fontObj.onload = resolve
    );
    const font2 = onloadEvent.detail.font;
    const { name: name2 } = font2.opentype.tables;
    const fontName = name2.get(16) || name2.get(1);
    const isItalic = name2.get(2).toLowerCase().includes("italic");
    const fontWeight = font2.opentype.tables["OS/2"].usWeightClass || "normal";
    const isVariable = !!font2.opentype.tables.fvar;
    const weightAxis = isVariable && font2.opentype.tables.fvar.axes.find(
      ({ tag }) => tag === "wght"
    );
    const weightRange = weightAxis ? `${weightAxis.minValue} ${weightAxis.maxValue}` : null;
    return {
      file: fontFile,
      fontFamily: fontName,
      fontStyle: isItalic ? "italic" : "normal",
      fontWeight: weightRange || fontWeight
    };
  };
  const handleInstall = async (fontFaces) => {
    const fontFamilies = makeFamiliesFromFaces(fontFaces);
    try {
      await installFonts(fontFamilies);
      setNotice({
        type: "success",
        message: (0, import_i18n16.__)("Fonts were installed successfully.")
      });
    } catch (error) {
      const typedError = error;
      setNotice({
        type: "error",
        message: typedError.message,
        errors: typedError?.installationErrors
      });
    }
    setIsUploading(false);
  };
  return /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)("div", { className: "font-library__tabpanel-layout", children: [
    /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components30.DropZone, { onFilesDrop: handleDropZone }),
    /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_components30.__experimentalVStack, { className: "font-library__local-fonts", children: [
      notice && /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(
        import_components30.Notice,
        {
          status: notice.type,
          __unstableHTML: true,
          onRemove: () => setNotice(null),
          children: [
            notice.message,
            notice.errors && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)("ul", { children: notice.errors.map((error, index) => /* @__PURE__ */ (0, import_jsx_runtime37.jsx)("li", { children: error }, index)) })
          ]
        }
      ),
      isUploading && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components30.FlexItem, { children: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)("div", { className: "font-library__upload-area", children: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components30.ProgressBar, {}) }) }),
      !isUploading && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
        import_components30.FormFileUpload,
        {
          accept: ALLOWED_FILE_EXTENSIONS.map(
            (ext) => `.${ext}`
          ).join(","),
          multiple: true,
          onChange: onFilesUpload,
          render: ({ openFileDialog }) => /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
            import_components30.Button,
            {
              __next40pxDefaultSize: true,
              className: "font-library__upload-area",
              onClick: openFileDialog,
              children: (0, import_i18n16.__)("Upload font")
            }
          )
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components30.__experimentalSpacer, { margin: 2 }),
      /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components30.__experimentalText, { className: "font-library__upload-area__text", children: (0, import_i18n16.__)(
        "Uploaded fonts appear in your library and can be used in your theme. Supported formats: .ttf, .otf, .woff, and .woff2."
      ) })
    ] })
  ] });
}
var upload_fonts_default = UploadFonts;

// packages/global-styles-ui/build-module/font-library/modal.js
var import_jsx_runtime38 = __toESM(require_jsx_runtime());
var { Tabs } = unlock(import_components31.privateApis);
var DEFAULT_TAB = {
  id: "installed-fonts",
  title: (0, import_i18n17._x)("Library", "Font library")
};
var UPLOAD_TAB = {
  id: "upload-fonts",
  title: (0, import_i18n17._x)("Upload", "noun")
};

// packages/global-styles-ui/build-module/font-family-item.js
var import_i18n18 = __toESM(require_i18n());
var import_components32 = __toESM(require_components());
var import_element19 = __toESM(require_element());
var import_jsx_runtime39 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-families.js
var import_jsx_runtime40 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/font-sizes-count.js
var import_i18n20 = __toESM(require_i18n());
var import_components34 = __toESM(require_components());
var import_jsx_runtime41 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-typography.js
var import_jsx_runtime42 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-typography-element.js
var import_i18n22 = __toESM(require_i18n());
var import_components36 = __toESM(require_components());
var import_element22 = __toESM(require_element());

// packages/global-styles-ui/build-module/typography-panel.js
var import_block_editor5 = __toESM(require_block_editor());
var import_jsx_runtime43 = __toESM(require_jsx_runtime());
var { useSettingsForBlockElement: useSettingsForBlockElement4, TypographyPanel: StylesTypographyPanel2 } = unlock(import_block_editor5.privateApis);

// packages/global-styles-ui/build-module/typography-preview.js
var import_jsx_runtime44 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-typography-element.js
var import_jsx_runtime45 = __toESM(require_jsx_runtime());
var elements = {
  text: {
    description: (0, import_i18n22.__)("Manage the fonts used on the site."),
    title: (0, import_i18n22.__)("Text")
  },
  link: {
    description: (0, import_i18n22.__)("Manage the fonts and typography used on the links."),
    title: (0, import_i18n22.__)("Links")
  },
  heading: {
    description: (0, import_i18n22.__)("Manage the fonts and typography used on headings."),
    title: (0, import_i18n22.__)("Headings")
  },
  caption: {
    description: (0, import_i18n22.__)("Manage the fonts and typography used on captions."),
    title: (0, import_i18n22.__)("Captions")
  },
  button: {
    description: (0, import_i18n22.__)("Manage the fonts and typography used on buttons."),
    title: (0, import_i18n22.__)("Buttons")
  }
};

// packages/global-styles-ui/build-module/screen-colors.js
var import_i18n24 = __toESM(require_i18n());
var import_components39 = __toESM(require_components());
var import_block_editor6 = __toESM(require_block_editor());

// packages/global-styles-ui/build-module/palette.js
var import_components38 = __toESM(require_components());
var import_i18n23 = __toESM(require_i18n());
var import_element23 = __toESM(require_element());

// packages/global-styles-ui/build-module/color-indicator-wrapper.js
var import_components37 = __toESM(require_components());
var import_jsx_runtime46 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/palette.js
var import_jsx_runtime47 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-colors.js
var import_jsx_runtime48 = __toESM(require_jsx_runtime());
var { useSettingsForBlockElement: useSettingsForBlockElement5, ColorPanel: StylesColorPanel2 } = unlock(
  import_block_editor6.privateApis
);

// packages/global-styles-ui/build-module/screen-color-palette.js
var import_i18n27 = __toESM(require_i18n());
var import_components44 = __toESM(require_components());

// packages/global-styles-ui/build-module/color-palette-panel.js
var import_compose4 = __toESM(require_compose());
var import_components42 = __toESM(require_components());
var import_i18n25 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/variations/variations-color.js
var import_components41 = __toESM(require_components());

// packages/global-styles-ui/build-module/preview-colors.js
var import_components40 = __toESM(require_components());

// packages/global-styles-ui/build-module/preset-colors.js
var import_jsx_runtime49 = __toESM(require_jsx_runtime());
function PresetColors() {
  const { paletteColors } = useStylesPreviewColors();
  return paletteColors.slice(0, 4).map(({ slug, color }, index) => /* @__PURE__ */ (0, import_jsx_runtime49.jsx)(
    "div",
    {
      style: {
        flexGrow: 1,
        height: "100%",
        background: color
      }
    },
    `${slug}-${index}`
  ));
}

// packages/global-styles-ui/build-module/preview-colors.js
var import_jsx_runtime50 = __toESM(require_jsx_runtime());
var firstFrameVariants2 = {
  start: {
    scale: 1,
    opacity: 1
  },
  hover: {
    scale: 0,
    opacity: 0
  }
};
var StylesPreviewColors = ({
  label,
  isFocused,
  withHoverView
}) => {
  return /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
    preview_wrapper_default,
    {
      label,
      isFocused,
      withHoverView,
      children: ({ key }) => /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
        import_components40.__unstableMotion.div,
        {
          variants: firstFrameVariants2,
          style: {
            height: "100%",
            overflow: "hidden"
          },
          children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(
            import_components40.__experimentalHStack,
            {
              spacing: 0,
              justify: "center",
              style: {
                height: "100%",
                overflow: "hidden"
              },
              children: /* @__PURE__ */ (0, import_jsx_runtime50.jsx)(PresetColors, {})
            }
          )
        },
        key
      )
    }
  );
};
var preview_colors_default = StylesPreviewColors;

// packages/global-styles-ui/build-module/variations/variations-color.js
var import_jsx_runtime51 = __toESM(require_jsx_runtime());
var propertiesToFilter2 = ["color"];
function ColorVariations({
  title,
  gap = 2
}) {
  const colorVariations = useCurrentMergeThemeStyleVariationsWithUserConfig(propertiesToFilter2);
  if (colorVariations?.length <= 1) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime51.jsxs)(import_components41.__experimentalVStack, { spacing: 3, children: [
    title && /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(Subtitle, { level: 3, children: title }),
    /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(import_components41.__experimentalGrid, { gap, children: colorVariations.map((variation, index) => /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(
      Variation,
      {
        variation,
        isPill: true,
        properties: propertiesToFilter2,
        showTooltip: true,
        children: () => /* @__PURE__ */ (0, import_jsx_runtime51.jsx)(preview_colors_default, {})
      },
      index
    )) })
  ] });
}

// packages/global-styles-ui/build-module/color-palette-panel.js
var import_jsx_runtime52 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/gradients-palette-panel.js
var import_compose5 = __toESM(require_compose());
var import_components43 = __toESM(require_components());
var import_i18n26 = __toESM(require_i18n());
var import_jsx_runtime53 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-color-palette.js
var import_jsx_runtime54 = __toESM(require_jsx_runtime());
var { Tabs: Tabs2 } = unlock(import_components44.privateApis);

// packages/global-styles-ui/build-module/screen-background.js
var import_i18n28 = __toESM(require_i18n());
var import_block_editor8 = __toESM(require_block_editor());
var import_components45 = __toESM(require_components());

// packages/global-styles-ui/build-module/background-panel.js
var import_block_editor7 = __toESM(require_block_editor());
var import_jsx_runtime55 = __toESM(require_jsx_runtime());
var { BackgroundPanel: StylesBackgroundPanel2 } = unlock(
  import_block_editor7.privateApis
);

// packages/global-styles-ui/build-module/screen-background.js
var import_jsx_runtime56 = __toESM(require_jsx_runtime());
var { useHasBackgroundPanel: useHasBackgroundPanel3 } = unlock(import_block_editor8.privateApis);

// packages/global-styles-ui/build-module/shadows-panel.js
var import_components47 = __toESM(require_components());
var import_i18n30 = __toESM(require_i18n());
var import_element24 = __toESM(require_element());

// packages/global-styles-ui/build-module/confirm-reset-shadow-dialog.js
var import_components46 = __toESM(require_components());
var import_i18n29 = __toESM(require_i18n());
var import_jsx_runtime57 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/shadows-panel.js
var import_jsx_runtime58 = __toESM(require_jsx_runtime());
var { Menu } = unlock(import_components47.privateApis);

// packages/global-styles-ui/build-module/shadows-edit-panel.js
var import_components48 = __toESM(require_components());
var import_i18n31 = __toESM(require_i18n());
var import_element25 = __toESM(require_element());
var import_jsx_runtime59 = __toESM(require_jsx_runtime());
var { Menu: Menu2 } = unlock(import_components48.privateApis);
var customShadowMenuItems = [
  {
    label: (0, import_i18n31.__)("Rename"),
    action: "rename"
  },
  {
    label: (0, import_i18n31.__)("Delete"),
    action: "delete"
  }
];
var presetShadowMenuItems = [
  {
    label: (0, import_i18n31.__)("Reset"),
    action: "reset"
  }
];

// packages/global-styles-ui/build-module/screen-shadows.js
var import_jsx_runtime60 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-layout.js
var import_i18n32 = __toESM(require_i18n());
var import_block_editor10 = __toESM(require_block_editor());

// packages/global-styles-ui/build-module/dimensions-panel.js
var import_block_editor9 = __toESM(require_block_editor());
var import_element26 = __toESM(require_element());
var import_jsx_runtime61 = __toESM(require_jsx_runtime());
var { useSettingsForBlockElement: useSettingsForBlockElement6, DimensionsPanel: StylesDimensionsPanel2 } = unlock(import_block_editor9.privateApis);

// packages/global-styles-ui/build-module/screen-layout.js
var import_jsx_runtime62 = __toESM(require_jsx_runtime());
var { useHasDimensionsPanel: useHasDimensionsPanel4, useSettingsForBlockElement: useSettingsForBlockElement7 } = unlock(
  import_block_editor10.privateApis
);

// packages/global-styles-ui/build-module/screen-style-variations.js
var import_components51 = __toESM(require_components());
var import_i18n35 = __toESM(require_i18n());

// packages/global-styles-ui/build-module/style-variations-content.js
var import_i18n34 = __toESM(require_i18n());
var import_components50 = __toESM(require_components());

// packages/global-styles-ui/build-module/style-variations-container.js
var import_core_data9 = __toESM(require_core_data());
var import_data9 = __toESM(require_data());
var import_element27 = __toESM(require_element());
var import_components49 = __toESM(require_components());
var import_i18n33 = __toESM(require_i18n());
var import_jsx_runtime63 = __toESM(require_jsx_runtime());
function StyleVariationsContainer({
  gap = 2
}) {
  const { user } = (0, import_element27.useContext)(GlobalStylesContext);
  const userStyles = user?.styles;
  const variations = (0, import_data9.useSelect)((select) => {
    const result = select(
      import_core_data9.store
    ).__experimentalGetCurrentThemeGlobalStylesVariations();
    return Array.isArray(result) ? result : void 0;
  }, []);
  const fullStyleVariations = variations?.filter(
    (variation) => {
      return !isVariationWithProperties(variation, ["color"]) && !isVariationWithProperties(variation, [
        "typography",
        "spacing"
      ]);
    }
  );
  const themeVariations = (0, import_element27.useMemo)(() => {
    const withEmptyVariation = [
      {
        title: (0, import_i18n33.__)("Default"),
        settings: {},
        styles: {}
      },
      ...fullStyleVariations ?? []
    ];
    return [
      ...withEmptyVariation.map((variation) => {
        const blockStyles = variation?.styles?.blocks ? { ...variation.styles.blocks } : {};
        if (userStyles?.blocks) {
          Object.keys(userStyles.blocks).forEach((blockName) => {
            if (userStyles.blocks?.[blockName]?.css) {
              const variationBlockStyles = blockStyles[blockName] || {};
              const customCSS = {
                css: `${blockStyles[blockName]?.css || ""} ${userStyles.blocks?.[blockName]?.css?.trim() || ""}`
              };
              blockStyles[blockName] = {
                ...variationBlockStyles,
                ...customCSS
              };
            }
          });
        }
        const css2 = userStyles?.css || variation.styles?.css ? {
          css: `${variation.styles?.css || ""} ${userStyles?.css || ""}`
        } : {};
        const blocks = Object.keys(blockStyles).length > 0 ? { blocks: blockStyles } : {};
        const styles = {
          ...variation.styles,
          ...css2,
          ...blocks
        };
        return {
          ...variation,
          settings: variation.settings ?? {},
          styles
        };
      })
    ];
  }, [fullStyleVariations, userStyles?.blocks, userStyles?.css]);
  if (!fullStyleVariations || fullStyleVariations.length < 1) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
    import_components49.__experimentalGrid,
    {
      columns: 2,
      className: "global-styles-ui-style-variations-container",
      gap,
      children: themeVariations.map(
        (variation, index) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(Variation, { variation, children: (isFocused) => /* @__PURE__ */ (0, import_jsx_runtime63.jsx)(
          preview_styles_default,
          {
            label: variation?.title,
            withHoverView: true,
            isFocused,
            variation
          }
        ) }, index)
      )
    }
  );
}
var style_variations_container_default = StyleVariationsContainer;

// packages/global-styles-ui/build-module/style-variations-content.js
var import_jsx_runtime64 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-style-variations.js
var import_jsx_runtime65 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-css.js
var import_i18n36 = __toESM(require_i18n());
var import_components52 = __toESM(require_components());
var import_block_editor11 = __toESM(require_block_editor());
var import_jsx_runtime66 = __toESM(require_jsx_runtime());
var { AdvancedPanel: StylesAdvancedPanel2 } = unlock(import_block_editor11.privateApis);

// packages/global-styles-ui/build-module/screen-revisions/index.js
var import_i18n39 = __toESM(require_i18n());
var import_components55 = __toESM(require_components());
var import_element29 = __toESM(require_element());

// packages/global-styles-ui/build-module/screen-revisions/use-global-styles-revisions.js
var import_data10 = __toESM(require_data());
var import_core_data10 = __toESM(require_core_data());
var import_element28 = __toESM(require_element());

// packages/global-styles-ui/build-module/screen-revisions/revisions-buttons.js
var import_i18n37 = __toESM(require_i18n());
var import_components53 = __toESM(require_components());
var import_date = __toESM(require_date());
var import_core_data11 = __toESM(require_core_data());
var import_data11 = __toESM(require_data());
var import_keycodes2 = __toESM(require_keycodes());
var import_jsx_runtime67 = __toESM(require_jsx_runtime());
var DAY_IN_MILLISECONDS = 60 * 60 * 1e3 * 24;

// packages/global-styles-ui/build-module/pagination/index.js
var import_components54 = __toESM(require_components());
var import_i18n38 = __toESM(require_i18n());
var import_jsx_runtime68 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/screen-revisions/index.js
var import_jsx_runtime69 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/font-sizes.js
var import_i18n41 = __toESM(require_i18n());
var import_components57 = __toESM(require_components());
var import_element30 = __toESM(require_element());

// packages/global-styles-ui/build-module/font-sizes/confirm-reset-font-sizes-dialog.js
var import_components56 = __toESM(require_components());
var import_i18n40 = __toESM(require_i18n());
var import_jsx_runtime70 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/font-sizes.js
var import_jsx_runtime71 = __toESM(require_jsx_runtime());
var { Menu: Menu3 } = unlock(import_components57.privateApis);

// packages/global-styles-ui/build-module/font-sizes/font-size.js
var import_i18n45 = __toESM(require_i18n());
var import_components61 = __toESM(require_components());
var import_element32 = __toESM(require_element());

// packages/global-styles-ui/build-module/font-sizes/font-size-preview.js
var import_block_editor12 = __toESM(require_block_editor());
var import_i18n42 = __toESM(require_i18n());
var import_jsx_runtime72 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/confirm-delete-font-size-dialog.js
var import_components58 = __toESM(require_components());
var import_i18n43 = __toESM(require_i18n());
var import_jsx_runtime73 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/rename-font-size-dialog.js
var import_components59 = __toESM(require_components());
var import_i18n44 = __toESM(require_i18n());
var import_element31 = __toESM(require_element());
var import_jsx_runtime74 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/size-control/index.js
var import_components60 = __toESM(require_components());
var import_jsx_runtime75 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/font-sizes/font-size.js
var import_jsx_runtime76 = __toESM(require_jsx_runtime());
var { Menu: Menu4 } = unlock(import_components61.privateApis);

// packages/global-styles-ui/build-module/global-styles-ui.js
var import_jsx_runtime77 = __toESM(require_jsx_runtime());

// packages/global-styles-ui/build-module/with-global-styles-provider.js
var import_jsx_runtime78 = __toESM(require_jsx_runtime());
function withGlobalStylesProvider(Component) {
  return function WrappedComponent({
    value,
    baseValue,
    onChange,
    ...props
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime78.jsx)(
      GlobalStylesProvider,
      {
        value,
        baseValue,
        onChange,
        children: /* @__PURE__ */ (0, import_jsx_runtime78.jsx)(Component, { ...props })
      }
    );
  };
}

// packages/global-styles-ui/build-module/style-variations.js
var StyleVariations = withGlobalStylesProvider(style_variations_container_default);

// packages/global-styles-ui/build-module/color-variations.js
var ColorVariations2 = withGlobalStylesProvider(ColorVariations);

// packages/global-styles-ui/build-module/typography-variations.js
var TypographyVariations2 = withGlobalStylesProvider(TypographyVariations);

// packages/global-styles-ui/build-module/font-library/font-library.js
var import_jsx_runtime79 = __toESM(require_jsx_runtime());
function FontLibrary({
  value,
  baseValue,
  onChange,
  activeTab = "installed-fonts"
}) {
  let content;
  switch (activeTab) {
    case "upload-fonts":
      content = /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(upload_fonts_default, {});
      break;
    case "installed-fonts":
      content = /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(installed_fonts_default, {});
      break;
    default:
      content = /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(font_collection_default, { slug: activeTab });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(
    GlobalStylesProvider,
    {
      value,
      baseValue,
      onChange,
      children: /* @__PURE__ */ (0, import_jsx_runtime79.jsx)(context_default, { children: content })
    }
  );
}

// routes/font-list/lock-unlock.ts
var import_private_apis2 = __toESM(require_private_apis());
var { unlock: unlock2 } = (0, import_private_apis2.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/font-list-route"
);

// routes/font-list/style.scss
var css = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Typography
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
@media (min-width: 782px) {
  .font-library-modal.font-library-modal {
    width: 65vw;
  }
}
.font-library-modal .components-modal__header {
  border-bottom: none;
}

.font-library-modal .components-modal__content {
  padding: 0;
  margin-bottom: 90px;
}

.font-library-modal .font-library__subtitle {
  text-transform: uppercase;
  font-weight: 499;
  font-size: 11px;
}

.font-library-modal__tab-panel {
  height: calc(100% - 50px);
}

.font-library__tabpanel-layout {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.font-library__tabpanel-layout > div {
  flex-grow: 1;
}

.font-library__tabpanel-layout .font-library__loading {
  width: 100%;
  height: 100%;
  display: flex;
  position: absolute;
  left: 0;
  top: 0;
  align-items: center;
  justify-content: center;
  padding-top: 124px;
  box-sizing: border-box;
}

.font-library__tabpanel-layout .components-navigator-screen {
  padding: 24px;
  width: 100%;
}

.font-library__footer {
  position: absolute;
  width: 100%;
  bottom: 0;
  border-top: 1px solid #ddd;
  padding: 24px;
  background-color: #fff;
  box-sizing: border-box;
  flex-grow: 0 !important;
  flex-shrink: 0;
  height: 90px;
}

.font-library__page-selection {
  font-size: 11px;
  font-weight: 499;
  text-transform: uppercase;
}

@media (min-width: 600px) {
  .font-library__page-selection .font-library__page-selection-trigger {
    font-size: 11px !important;
    font-weight: 499;
  }
}
.font-library__fonts-title {
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 600;
  margin-top: 0;
  margin-bottom: 0;
}

.font-library__fonts-list {
  list-style: none;
  padding: 0;
  margin-top: 0;
  margin-bottom: 0;
}

.font-library__fonts-list-item {
  margin-bottom: 0;
}

.font-library__font-card {
  box-sizing: border-box;
  border: 1px solid #ddd;
  width: 100%;
  height: auto !important;
  padding: 16px;
  margin-top: -1px; /* To collapse the margin with the previous element */
}

.font-library__font-card:hover {
  background-color: #f0f0f0;
}

.font-library__font-card:focus {
  position: relative;
}

.font-library__font-card .font-library__font-card__name {
  font-weight: bold;
}

.font-library__font-card .font-library__font-card__count {
  color: #757575;
}

.font-library__font-card .font-library__font-variant_demo-image {
  display: block;
  height: 24px;
  width: auto;
}

.font-library__font-card .font-library__font-variant_demo-text {
  white-space: nowrap;
  flex-shrink: 0;
}

@media not (prefers-reduced-motion) {
  .font-library__font-card .font-library__font-variant_demo-text {
    transition: opacity 0.3s ease-in-out;
  }
}
.font-library-modal__tablist-container {
  position: sticky;
  top: 0;
  border-bottom: 1px solid #ddd;
  background: #fff;
  z-index: 1;
}

.font-library__upload-area {
  align-items: center;
  display: flex;
  justify-content: center;
  height: 256px !important;
  width: 100%;
}

button.font-library__upload-area {
  background-color: #f0f0f0;
}

.font-library__local-fonts {
  margin: 24px auto;
  width: 80%;
}

.font-library__local-fonts .font-library__upload-area__text {
  color: #757575;
}

.font-library__google-fonts-confirm {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 64px;
}

.font-library__google-fonts-confirm p {
  line-height: 1.4;
}

.font-library__google-fonts-confirm h2 {
  font-size: 1.2rem;
  font-weight: 400;
}

.font-library__google-fonts-confirm .components-card {
  padding: 16px;
  width: 400px;
}

.font-library__google-fonts-confirm .components-button {
  width: 100%;
  justify-content: center;
}

.font-library__select-all {
  padding: 16px 16px 16px 17px;
}

.font-library__select-all .components-checkbox-control__label {
  padding-left: 16px;
}

.global-styles-ui-pagination .components-button.is-tertiary {
  width: 32px;
  height: 32px;
  justify-content: center;
}

.global-styles-ui-screen-revisions__revisions-list {
  list-style: none;
  margin: 0 16px 16px 16px;
  flex-grow: 1;
}

.global-styles-ui-screen-revisions__revisions-list li {
  margin-bottom: 0;
}

.global-styles-ui-screen-revisions__revision-item {
  position: relative;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.global-styles-ui-screen-revisions__revision-item[role=option]:active, .global-styles-ui-screen-revisions__revision-item[role=option]:focus {
  box-shadow: 0 0 0 var(--wp-admin-border-width-focus) var(--wp-admin-theme-color);
  outline: 2px solid transparent;
}

.global-styles-ui-screen-revisions__revision-item:hover {
  background: rgba(var(--wp-admin-theme-color--rgb), 0.04);
}

.global-styles-ui-screen-revisions__revision-item:hover .global-styles-ui-screen-revisions__date {
  color: var(--wp-admin-theme-color);
}

.global-styles-ui-screen-revisions__revision-item::before, .global-styles-ui-screen-revisions__revision-item::after {
  position: absolute;
  content: "\\a";
  display: block;
}

.global-styles-ui-screen-revisions__revision-item::before {
  background: #ddd;
  border-radius: 50%;
  height: 8px;
  width: 8px;
  top: 18px;
  left: 17px;
  transform: translate(-50%, -50%);
  z-index: 1;
  border: 4px solid transparent;
}

.global-styles-ui-screen-revisions__revision-item[aria-selected=true] {
  border-radius: 2px;
  outline: 3px solid transparent;
  outline-offset: -2px;
  color: var(--wp-admin-theme-color);
  background: rgba(var(--wp-admin-theme-color--rgb), 0.04);
}

.global-styles-ui-screen-revisions__revision-item[aria-selected=true] .global-styles-ui-screen-revisions__date {
  color: var(--wp-admin-theme-color);
}

.global-styles-ui-screen-revisions__revision-item[aria-selected=true]::before {
  background: var(--wp-admin-theme-color);
}

.global-styles-ui-screen-revisions__revision-item[aria-selected=true] .global-styles-ui-screen-revisions__changes > li,
.global-styles-ui-screen-revisions__revision-item[aria-selected=true] .global-styles-ui-screen-revisions__meta,
.global-styles-ui-screen-revisions__revision-item[aria-selected=true] .global-styles-ui-screen-revisions__applied-text {
  color: #1e1e1e;
}

.global-styles-ui-screen-revisions__revision-item::after {
  height: 100%;
  left: 16px;
  top: 0;
  width: 0;
  border: 0.5px solid #ddd;
}

.global-styles-ui-screen-revisions__revision-item:first-child::after {
  top: 18px;
}

.global-styles-ui-screen-revisions__revision-item:last-child::after {
  height: 18px;
}

.global-styles-ui-screen-revisions__revision-item-wrapper {
  display: block;
  padding: 12px 12px 4px 40px;
}

.global-styles-ui-screen-revisions__apply-button.is-primary,
.global-styles-ui-screen-revisions__applied-text {
  align-self: flex-start;
  margin: 4px 12px 12px 40px;
}

.global-styles-ui-screen-revisions__changes,
.global-styles-ui-screen-revisions__meta,
.global-styles-ui-screen-revisions__applied-text {
  color: #757575;
  font-size: 12px;
}

.global-styles-ui-screen-revisions__description {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.global-styles-ui-screen-revisions__description .global-styles-ui-screen-revisions__date {
  text-transform: uppercase;
  font-weight: 600;
  font-size: 12px;
}

.global-styles-ui-screen-revisions__meta {
  display: flex;
  justify-content: start;
  width: 100%;
  align-items: flex-start;
  text-align: left;
  margin-bottom: 4px;
}

.global-styles-ui-screen-revisions__meta img {
  width: 16px;
  height: 16px;
  border-radius: 100%;
  margin-right: 8px;
}

.global-styles-ui-screen-revisions__loading {
  margin: 24px auto !important;
}

.global-styles-ui-screen-revisions__changes {
  text-align: left;
  line-height: 1.4;
  margin-left: 12px;
  list-style: disc;
}

.global-styles-ui-screen-revisions__changes li {
  margin-bottom: 4px;
}

.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination {
  justify-content: space-between;
  gap: 2px;
}

.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination .edit-site-pagination__total {
  position: absolute;
  left: -1000px;
  height: 1px;
  margin: -1px;
  overflow: hidden;
}

.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination .components-text {
  font-size: 12px;
  will-change: opacity;
}

.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination .components-button.is-tertiary {
  color: #1e1e1e;
}

.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination .components-button.is-tertiary:disabled,
.global-styles-ui-screen-revisions__pagination.global-styles-ui-screen-revisions__pagination .components-button.is-tertiary[aria-disabled=true] {
  color: #949494;
}

.global-styles-ui-screen-revisions__footer {
  height: 56px;
  z-index: 1;
  position: sticky;
  min-width: 100%;
  bottom: 0;
  background: #fff;
  padding: 12px;
  border-top: 1px solid #ddd;
}

.global-styles-ui-variations_item {
  box-sizing: border-box;
  cursor: pointer;
}

.global-styles-ui-variations_item .global-styles-ui-variations_item-preview {
  border-radius: 2px;
  outline: 1px solid rgba(0, 0, 0, 0.1);
  outline-offset: -1px;
  overflow: hidden;
  position: relative;
}

@media not (prefers-reduced-motion) {
  .global-styles-ui-variations_item .global-styles-ui-variations_item-preview {
    transition: outline 0.1s linear;
  }
}
.global-styles-ui-variations_item .global-styles-ui-variations_item-preview.is-pill {
  height: 32px;
}

.global-styles-ui-variations_item .global-styles-ui-variations_item-preview.is-pill .block-editor-iframe__scale-container {
  overflow: hidden;
}

.global-styles-ui-variations_item:not(.is-active):hover .global-styles-ui-variations_item-preview {
  outline-color: rgba(0, 0, 0, 0.3);
}

.global-styles-ui-variations_item.is-active .global-styles-ui-variations_item-preview, .global-styles-ui-variations_item:focus-visible .global-styles-ui-variations_item-preview {
  outline-color: #1e1e1e;
  outline-offset: 1px;
  outline-width: var(--wp-admin-border-width-focus);
}

.global-styles-ui-variations_item:focus-visible .global-styles-ui-variations_item-preview {
  outline-color: var(--wp-admin-theme-color);
}

.global-styles-ui-preview {
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  cursor: pointer;
}

.global-styles-ui-preview__wrapper {
  max-width: 100%;
  display: block;
  width: 100%;
}

.global-styles-ui-typography-preview {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100px;
  margin-bottom: 20px;
  background: #f0f0f0;
  border-radius: 2px;
  overflow: hidden;
}

.global-styles-ui-font-size__item {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  line-break: anywhere;
}

.global-styles-ui-font-size__item-value {
  color: #757575;
}

.global-styles-ui-screen-typography__indicator {
  height: 24px;
  width: 24px;
  font-size: 14px;
  display: flex !important;
  align-items: center;
  justify-content: center;
  border-radius: 2px;
}

.global-styles-ui-block-types-search {
  margin-bottom: 10px;
  padding: 0 16px;
}

.global-styles-ui-screen-typography__font-variants-count {
  color: #757575;
}

.global-styles-ui-font-families__manage-fonts {
  justify-content: center;
}

.global-styles-ui-screen .color-block-support-panel {
  padding-left: 0;
  padding-right: 0;
  padding-top: 0;
  border-top: none;
  row-gap: 12px;
}

.global-styles-ui-header {
  margin-bottom: 0 !important;
}

.global-styles-ui-subtitle {
  margin-bottom: 0 !important;
  text-transform: uppercase;
  font-weight: 499 !important;
  font-size: 11px !important;
}

.global-styles-ui-section-title {
  color: #2f2f2f;
  font-weight: 600;
  line-height: 1.2;
  padding: 16px 16px 0;
  margin: 0;
}

.global-styles-ui-icon-with-current-color {
  fill: currentColor;
}

.global-styles-ui__color-indicator-wrapper {
  height: 24px;
  flex-shrink: 0;
}

.global-styles-ui__shadows-panel__options-container,
.global-styles-ui__typography-panel__options-container {
  height: 24px;
}

.global-styles-ui__block-preview-panel {
  position: relative;
  width: 100%;
  border: #ddd 1px solid;
  border-radius: 2px;
  overflow: hidden;
}

.global-styles-ui__shadow-preview-panel {
  height: 144px;
  border: #ddd 1px solid;
  border-radius: 2px;
  overflow: auto;
  background-image: repeating-linear-gradient(45deg, #f5f5f5 25%, rgba(0, 0, 0, 0) 0, rgba(0, 0, 0, 0) 75%, #f5f5f5 0, #f5f5f5), repeating-linear-gradient(45deg, #f5f5f5 25%, rgba(0, 0, 0, 0) 0, rgba(0, 0, 0, 0) 75%, #f5f5f5 0, #f5f5f5);
  background-position: 0 0, 8px 8px;
  background-size: 16px 16px;
}

.global-styles-ui__shadow-preview-panel .global-styles-ui__shadow-preview-block {
  border: #ddd 1px solid;
  border-radius: 2px;
  background-color: #fff;
  width: 60%;
  height: 60px;
}

.global-styles-ui__shadow-editor__dropdown-content {
  width: 280px;
}

.global-styles-ui__shadow-editor-panel {
  margin-bottom: 4px;
}

.global-styles-ui__shadow-editor__dropdown {
  width: 100%;
  position: relative;
}

.global-styles-ui__shadow-editor__dropdown-toggle {
  width: 100%;
  height: auto;
  padding-top: 8px;
  padding-bottom: 8px;
  text-align: left;
  border-radius: inherit;
}

.global-styles-ui__shadow-editor__dropdown-toggle.is-open {
  background: #f0f0f0;
  color: var(--wp-admin-theme-color);
}

.global-styles-ui__shadow-editor__remove-button {
  position: absolute;
  right: 8px;
  top: 8px;
  opacity: 0;
}

.global-styles-ui__shadow-editor__remove-button.global-styles-ui__shadow-editor__remove-button {
  border: none;
}

.global-styles-ui__shadow-editor__dropdown-toggle:hover + .global-styles-ui__shadow-editor__remove-button, .global-styles-ui__shadow-editor__remove-button:focus, .global-styles-ui__shadow-editor__remove-button:hover {
  opacity: 1;
}

@media (hover: none) {
  .global-styles-ui__shadow-editor__remove-button {
    opacity: 1;
  }
}
.global-styles-ui-screen-css {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  margin: 16px;
}

.global-styles-ui-screen-css .components-v-stack {
  flex: 1 1 auto;
}

.global-styles-ui-screen-css .components-v-stack .block-editor-global-styles-advanced-panel__custom-css-input {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}

.global-styles-ui-screen-css .components-v-stack .block-editor-global-styles-advanced-panel__custom-css-input .components-base-control__field {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
}

.global-styles-ui-screen-css .components-v-stack .block-editor-global-styles-advanced-panel__custom-css-input .components-base-control__field .components-textarea-control__input {
  flex: 1 1 auto;
  /*rtl:ignore*/
  direction: ltr;
}

.global-styles-ui-screen-css-help-link {
  display: inline-block;
  margin-top: 8px;
}

.global-styles-ui-screen-variations {
  margin-top: 16px;
  border-top: 1px solid #ddd;
}

.global-styles-ui-screen-variations > * {
  margin: 24px 16px;
}

.global-styles-ui-sidebar__navigator-provider {
  height: 100%;
}

.global-styles-ui-sidebar__navigator-screen {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.global-styles-ui-sidebar__navigator-screen .single-column {
  grid-column: span 1;
}

.global-styles-ui-screen-root.global-styles-ui-screen-root,
.global-styles-ui-screen-style-variations.global-styles-ui-screen-style-variations {
  background: unset;
  color: inherit;
}

.global-styles-ui-sidebar__panel .block-editor-block-icon svg {
  fill: currentColor;
}

.global-styles-ui-screen-root__active-style-tile.global-styles-ui-screen-root__active-style-tile, .global-styles-ui-screen-root__active-style-tile.global-styles-ui-screen-root__active-style-tile .global-styles-ui-screen-root__active-style-tile-preview {
  border-radius: 2px;
}

.global-styles-ui-screen-root__active-style-tile-preview {
  clip-path: border-box;
}

.global-styles-ui-color-palette-panel,
.global-styles-ui-gradient-palette-panel {
  padding: 16px;
}

.font-library-page__tablist {
  border-bottom: 1px solid #f0f0f0;
  padding: 0 24px;
}

.font-library-page__tab-panel {
  flex-grow: 1;
  max-height: calc(100% - 110px);
  overflow: auto;
}
.admin-ui-page:has(.font-library__footer) .font-library-page__tab-panel {
  max-height: calc(100% - 198px);
}`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css));

// routes/font-list/stage.tsx
var { Tabs: Tabs3 } = unlock2(import_components63.privateApis);
var { useGlobalStyles } = unlock2(import_editor.privateApis);
function FontLibraryPage() {
  const { records: collections = [] } = (0, import_core_data12.useEntityRecords)("root", "fontCollection", {
    _fields: "slug,name,description"
  });
  const [activeTab, setActiveTab] = (0, import_element34.useState)("installed-fonts");
  const { base, user, setUser, isReady } = useGlobalStyles();
  const canUserCreate = (0, import_data13.useSelect)((select) => {
    return select(import_core_data12.store).canUser("create", {
      kind: "postType",
      name: "wp_font_family"
    });
  }, []);
  if (!isReady) {
    return null;
  }
  const tabs = [
    {
      id: "installed-fonts",
      title: (0, import_i18n46.__)("Library")
    }
  ];
  if (canUserCreate) {
    tabs.push({
      id: "upload-fonts",
      title: (0, import_i18n46.__)("Upload")
    });
    tabs.push(
      ...(collections || []).map(({ slug, name: name2 }) => ({
        id: slug,
        title: collections && collections.length === 1 && slug === "google-fonts" ? (0, import_i18n46.__)("Install Fonts") : name2
      }))
    );
  }
  return /* @__PURE__ */ React.createElement(page_default, { title: (0, import_i18n46.__)("Fonts") }, /* @__PURE__ */ React.createElement(
    Tabs3,
    {
      selectedTabId: activeTab,
      onSelect: (tabId) => setActiveTab(tabId)
    },
    /* @__PURE__ */ React.createElement("div", { className: "font-library-page__tablist" }, /* @__PURE__ */ React.createElement(Tabs3.TabList, null, tabs.map(({ id, title }) => /* @__PURE__ */ React.createElement(Tabs3.Tab, { key: id, tabId: id }, title)))),
    tabs.map(({ id }) => /* @__PURE__ */ React.createElement(
      Tabs3.TabPanel,
      {
        key: id,
        tabId: id,
        focusable: false,
        className: "font-library-page__tab-panel"
      },
      /* @__PURE__ */ React.createElement(
        FontLibrary,
        {
          value: user,
          baseValue: base,
          onChange: setUser,
          activeTab: id
        }
      )
    ))
  ));
}
function Stage() {
  return /* @__PURE__ */ React.createElement(FontLibraryPage, null);
}
var stage = Stage;
export {
  stage
};
/*! Bundled license information:

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/
