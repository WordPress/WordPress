var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
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

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
  }
});

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/style-engine
var require_style_engine = __commonJS({
  "package-external:@wordpress/style-engine"(exports, module) {
    module.exports = window.wp.styleEngine;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// packages/lazy-editor/build-module/component.js
var import_editor = __toESM(require_editor());
var import_core_data5 = __toESM(require_core_data());
var import_data6 = __toESM(require_data());
var import_components = __toESM(require_components());
var import_element4 = __toESM(require_element());

// packages/lazy-editor/build-module/hooks/use-styles-id.js
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());
function useStylesId({ templateId }) {
  const { globalStylesId, stylesId } = (0, import_data.useSelect)(
    (select2) => {
      const coreDataSelect = select2(import_core_data.store);
      const template = templateId ? coreDataSelect.getEntityRecord(
        "postType",
        "wp_template",
        templateId
      ) : null;
      return {
        globalStylesId: coreDataSelect.__experimentalGetCurrentGlobalStylesId(),
        stylesId: template?.styles_id
      };
    },
    [templateId]
  );
  return stylesId || globalStylesId;
}

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
  "dimensions.minHeight",
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
var ROOT_BLOCK_SELECTOR = "body";
var ROOT_CSS_PROPERTIES_SELECTOR = ":root";
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
  }
];
function scopeSelector(scope, selector) {
  if (!scope || !selector) {
    return selector;
  }
  const scopes = scope.split(",");
  const selectors = selector.split(",");
  const selectorsScoped = [];
  scopes.forEach((outer) => {
    selectors.forEach((inner) => {
      selectorsScoped.push(`${outer.trim()} ${inner.trim()}`);
    });
  });
  return selectorsScoped.join(", ");
}
function scopeFeatureSelectors(scope, selectors) {
  if (!scope || !selectors) {
    return;
  }
  const featureSelectors = {};
  Object.entries(selectors).forEach(([feature, selector]) => {
    if (typeof selector === "string") {
      featureSelectors[feature] = scopeSelector(scope, selector);
    }
    if (typeof selector === "object") {
      featureSelectors[feature] = {};
      Object.entries(selector).forEach(
        ([subfeature, subfeatureSelector]) => {
          featureSelectors[feature][subfeature] = scopeSelector(
            scope,
            subfeatureSelector
          );
        }
      );
    }
  });
  return featureSelectors;
}
function appendToSelector(selector, toAppend) {
  if (!selector.includes(",")) {
    return selector + toAppend;
  }
  const selectors = selector.split(",");
  const newSelectors = selectors.map((sel) => sel + toAppend);
  return newSelectors.join(",");
}
function getBlockStyleVariationSelector(variation, blockSelector) {
  const variationClass = `.is-style-${variation}`;
  if (!blockSelector) {
    return variationClass;
  }
  const ancestorRegex = /((?::\([^)]+\))?\s*)([^\s:]+)/;
  const addVariationClass = (_match, group1, group2) => {
    return group1 + group2 + variationClass;
  };
  const result = blockSelector.split(",").map((part) => part.replace(ancestorRegex, addVariationClass));
  return result.join(",");
}
function getResolvedRefValue(ruleValue, tree) {
  if (!ruleValue || !tree) {
    return ruleValue;
  }
  if (typeof ruleValue === "object" && "ref" in ruleValue && ruleValue?.ref) {
    const resolvedRuleValue = (0, import_style_engine.getCSSValueFromRawStyle)(
      getValueFromObjectPath(tree, ruleValue.ref)
    );
    if (typeof resolvedRuleValue === "object" && resolvedRuleValue !== null && "ref" in resolvedRuleValue && resolvedRuleValue?.ref) {
      return void 0;
    }
    if (resolvedRuleValue === void 0) {
      return ruleValue;
    }
    return resolvedRuleValue;
  }
  return ruleValue;
}
function getResolvedThemeFilePath(file, themeFileURIs) {
  if (!file || !themeFileURIs || !Array.isArray(themeFileURIs)) {
    return file;
  }
  const uri = themeFileURIs.find(
    (themeFileUri) => themeFileUri?.name === file
  );
  if (!uri?.href) {
    return file;
  }
  return uri?.href;
}
function getResolvedValue(ruleValue, tree) {
  if (!ruleValue || !tree) {
    return ruleValue;
  }
  const resolvedValue = getResolvedRefValue(ruleValue, tree);
  if (typeof resolvedValue === "object" && resolvedValue !== null && "url" in resolvedValue && resolvedValue?.url) {
    resolvedValue.url = getResolvedThemeFilePath(
      resolvedValue.url,
      tree?._links?.["wp:theme-file"]
    );
  }
  return resolvedValue;
}

// packages/global-styles-engine/build-module/core/render.js
var import_blocks = __toESM(require_blocks());
var import_style_engine2 = __toESM(require_style_engine());
var import_data2 = __toESM(require_data());

// packages/global-styles-engine/build-module/core/selectors.js
function getBlockSelector(blockType, target = "root", options = {}) {
  if (!target) {
    return null;
  }
  const { fallback = false } = options;
  const { name, selectors, supports } = blockType;
  const hasSelectors = selectors && Object.keys(selectors).length > 0;
  const path = Array.isArray(target) ? target.join(".") : target;
  let rootSelector = null;
  if (hasSelectors && selectors.root) {
    rootSelector = selectors?.root;
  } else if (supports?.__experimentalSelector) {
    rootSelector = supports.__experimentalSelector;
  } else {
    rootSelector = ".wp-block-" + name.replace("core/", "").replace("/", "-");
  }
  if (path === "root") {
    return rootSelector;
  }
  const pathArray = Array.isArray(target) ? target : target.split(".");
  if (pathArray.length === 1) {
    const fallbackSelector = fallback ? rootSelector : null;
    if (hasSelectors) {
      const featureSelector2 = getValueFromObjectPath(
        selectors,
        `${path}.root`,
        null
      ) || getValueFromObjectPath(selectors, path, null);
      return featureSelector2 || fallbackSelector;
    }
    const featureSelector = supports ? getValueFromObjectPath(
      supports,
      `${path}.__experimentalSelector`,
      null
    ) : void 0;
    if (!featureSelector) {
      return fallbackSelector;
    }
    return scopeSelector(rootSelector, featureSelector);
  }
  let subfeatureSelector;
  if (hasSelectors) {
    subfeatureSelector = getValueFromObjectPath(selectors, path, null);
  }
  if (subfeatureSelector) {
    return subfeatureSelector;
  }
  if (fallback) {
    return getBlockSelector(blockType, pathArray[0], options);
  }
  return null;
}

// node_modules/colord/index.mjs
var r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
var t = function(r2) {
  return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
};
var n = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r2) / n2 + 0;
};
var e = function(r2, t2, n2) {
  return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t2 ? r2 : t2;
};
var u = function(r2) {
  return (r2 = isFinite(r2) ? r2 % 360 : 0) > 0 ? r2 : r2 + 360;
};
var a = function(r2) {
  return { r: e(r2.r, 0, 255), g: e(r2.g, 0, 255), b: e(r2.b, 0, 255), a: e(r2.a) };
};
var o = function(r2) {
  return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
};
var i = /^#([0-9a-f]{3,8})$/i;
var s = function(r2) {
  var t2 = r2.toString(16);
  return t2.length < 2 ? "0" + t2 : t2;
};
var h = function(r2) {
  var t2 = r2.r, n2 = r2.g, e2 = r2.b, u2 = r2.a, a2 = Math.max(t2, n2, e2), o2 = a2 - Math.min(t2, n2, e2), i2 = o2 ? a2 === t2 ? (n2 - e2) / o2 : a2 === n2 ? 2 + (e2 - t2) / o2 : 4 + (t2 - n2) / o2 : 0;
  return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o2 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
};
var b = function(r2) {
  var t2 = r2.h, n2 = r2.s, e2 = r2.v, u2 = r2.a;
  t2 = t2 / 360 * 6, n2 /= 100, e2 /= 100;
  var a2 = Math.floor(t2), o2 = e2 * (1 - n2), i2 = e2 * (1 - (t2 - a2) * n2), s2 = e2 * (1 - (1 - t2 + a2) * n2), h2 = a2 % 6;
  return { r: 255 * [e2, i2, o2, o2, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o2, o2][h2], b: 255 * [o2, o2, s2, e2, e2, i2][h2], a: u2 };
};
var g = function(r2) {
  return { h: u(r2.h), s: e(r2.s, 0, 100), l: e(r2.l, 0, 100), a: e(r2.a) };
};
var d = function(r2) {
  return { h: n(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
};
var f = function(r2) {
  return b((n2 = (t2 = r2).s, { h: t2.h, s: (n2 *= ((e2 = t2.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t2.a }));
  var t2, n2, e2;
};
var c = function(r2) {
  return { h: (t2 = h(r2)).h, s: (u2 = (200 - (n2 = t2.s)) * (e2 = t2.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t2.a };
  var t2, n2, e2, u2;
};
var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
var y = { string: [[function(r2) {
  var t2 = i.exec(r2);
  return t2 ? (r2 = t2[1]).length <= 4 ? { r: parseInt(r2[0] + r2[0], 16), g: parseInt(r2[1] + r2[1], 16), b: parseInt(r2[2] + r2[2], 16), a: 4 === r2.length ? n(parseInt(r2[3] + r2[3], 16) / 255, 2) : 1 } : 6 === r2.length || 8 === r2.length ? { r: parseInt(r2.substr(0, 2), 16), g: parseInt(r2.substr(2, 2), 16), b: parseInt(r2.substr(4, 2), 16), a: 8 === r2.length ? n(parseInt(r2.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
}, "hex"], [function(r2) {
  var t2 = v.exec(r2) || m.exec(r2);
  return t2 ? t2[2] !== t2[4] || t2[4] !== t2[6] ? null : a({ r: Number(t2[1]) / (t2[2] ? 100 / 255 : 1), g: Number(t2[3]) / (t2[4] ? 100 / 255 : 1), b: Number(t2[5]) / (t2[6] ? 100 / 255 : 1), a: void 0 === t2[7] ? 1 : Number(t2[7]) / (t2[8] ? 100 : 1) }) : null;
}, "rgb"], [function(t2) {
  var n2 = l.exec(t2) || p.exec(t2);
  if (!n2) return null;
  var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
  return f(a2);
}, "hsl"]], object: [[function(r2) {
  var n2 = r2.r, e2 = r2.g, u2 = r2.b, o2 = r2.a, i2 = void 0 === o2 ? 1 : o2;
  return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
}, "rgb"], [function(r2) {
  var n2 = r2.h, e2 = r2.s, u2 = r2.l, a2 = r2.a, o2 = void 0 === a2 ? 1 : a2;
  if (!t(n2) || !t(e2) || !t(u2)) return null;
  var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o2) });
  return f(i2);
}, "hsl"], [function(r2) {
  var n2 = r2.h, a2 = r2.s, o2 = r2.v, i2 = r2.a, s2 = void 0 === i2 ? 1 : i2;
  if (!t(n2) || !t(a2) || !t(o2)) return null;
  var h2 = (function(r3) {
    return { h: u(r3.h), s: e(r3.s, 0, 100), v: e(r3.v, 0, 100), a: e(r3.a) };
  })({ h: Number(n2), s: Number(a2), v: Number(o2), a: Number(s2) });
  return b(h2);
}, "hsv"]] };
var N = function(r2, t2) {
  for (var n2 = 0; n2 < t2.length; n2++) {
    var e2 = t2[n2][0](r2);
    if (e2) return [e2, t2[n2][1]];
  }
  return [null, void 0];
};
var x = function(r2) {
  return "string" == typeof r2 ? N(r2.trim(), y.string) : "object" == typeof r2 && null !== r2 ? N(r2, y.object) : [null, void 0];
};
var M = function(r2, t2) {
  var n2 = c(r2);
  return { h: n2.h, s: e(n2.s + 100 * t2, 0, 100), l: n2.l, a: n2.a };
};
var H = function(r2) {
  return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
};
var $ = function(r2, t2) {
  var n2 = c(r2);
  return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t2, 0, 100), a: n2.a };
};
var j = (function() {
  function r2(r3) {
    this.parsed = x(r3)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
  }
  return r2.prototype.isValid = function() {
    return null !== this.parsed;
  }, r2.prototype.brightness = function() {
    return n(H(this.rgba), 2);
  }, r2.prototype.isDark = function() {
    return H(this.rgba) < 0.5;
  }, r2.prototype.isLight = function() {
    return H(this.rgba) >= 0.5;
  }, r2.prototype.toHex = function() {
    return r3 = o(this.rgba), t2 = r3.r, e2 = r3.g, u2 = r3.b, i2 = (a2 = r3.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t2) + s(e2) + s(u2) + i2;
    var r3, t2, e2, u2, a2, i2;
  }, r2.prototype.toRgb = function() {
    return o(this.rgba);
  }, r2.prototype.toRgbString = function() {
    return r3 = o(this.rgba), t2 = r3.r, n2 = r3.g, e2 = r3.b, (u2 = r3.a) < 1 ? "rgba(" + t2 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t2 + ", " + n2 + ", " + e2 + ")";
    var r3, t2, n2, e2, u2;
  }, r2.prototype.toHsl = function() {
    return d(c(this.rgba));
  }, r2.prototype.toHslString = function() {
    return r3 = d(c(this.rgba)), t2 = r3.h, n2 = r3.s, e2 = r3.l, (u2 = r3.a) < 1 ? "hsla(" + t2 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t2 + ", " + n2 + "%, " + e2 + "%)";
    var r3, t2, n2, e2, u2;
  }, r2.prototype.toHsv = function() {
    return r3 = h(this.rgba), { h: n(r3.h), s: n(r3.s), v: n(r3.v), a: n(r3.a, 3) };
    var r3;
  }, r2.prototype.invert = function() {
    return w({ r: 255 - (r3 = this.rgba).r, g: 255 - r3.g, b: 255 - r3.b, a: r3.a });
    var r3;
  }, r2.prototype.saturate = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, r3));
  }, r2.prototype.desaturate = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, -r3));
  }, r2.prototype.grayscale = function() {
    return w(M(this.rgba, -1));
  }, r2.prototype.lighten = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w($(this.rgba, r3));
  }, r2.prototype.darken = function(r3) {
    return void 0 === r3 && (r3 = 0.1), w($(this.rgba, -r3));
  }, r2.prototype.rotate = function(r3) {
    return void 0 === r3 && (r3 = 15), this.hue(this.hue() + r3);
  }, r2.prototype.alpha = function(r3) {
    return "number" == typeof r3 ? w({ r: (t2 = this.rgba).r, g: t2.g, b: t2.b, a: r3 }) : n(this.rgba.a, 3);
    var t2;
  }, r2.prototype.hue = function(r3) {
    var t2 = c(this.rgba);
    return "number" == typeof r3 ? w({ h: r3, s: t2.s, l: t2.l, a: t2.a }) : n(t2.h);
  }, r2.prototype.isEqual = function(r3) {
    return this.toHex() === w(r3).toHex();
  }, r2;
})();
var w = function(r2) {
  return r2 instanceof j ? r2 : new j(r2);
};

// packages/global-styles-engine/build-module/utils/duotone.js
function getValuesFromColors(colors = []) {
  const values = {
    r: [],
    g: [],
    b: [],
    a: []
  };
  colors.forEach((color) => {
    const rgbColor = w(color).toRgb();
    values.r.push(rgbColor.r / 255);
    values.g.push(rgbColor.g / 255);
    values.b.push(rgbColor.b / 255);
    values.a.push(rgbColor.a);
  });
  return values;
}
function getDuotoneFilter(id, colors) {
  const values = getValuesFromColors(colors);
  return `
<svg
	xmlns:xlink="http://www.w3.org/1999/xlink"
	viewBox="0 0 0 0"
	width="0"
	height="0"
	focusable="false"
	role="none"
	aria-hidden="true"
	style="visibility: hidden; position: absolute; left: -9999px; overflow: hidden;"
>
	<defs>
		<filter id="${id}">
			<!--
				Use sRGB instead of linearRGB so transparency looks correct.
				Use perceptual brightness to convert to grayscale.
			-->
			<feColorMatrix color-interpolation-filters="sRGB" type="matrix" values=" .299 .587 .114 0 0 .299 .587 .114 0 0 .299 .587 .114 0 0 .299 .587 .114 0 0 "></feColorMatrix>
			<!-- Use sRGB instead of linearRGB to be consistent with how CSS gradients work. -->
			<feComponentTransfer color-interpolation-filters="sRGB">
				<feFuncR type="table" tableValues="${values.r.join(" ")}"></feFuncR>
				<feFuncG type="table" tableValues="${values.g.join(" ")}"></feFuncG>
				<feFuncB type="table" tableValues="${values.b.join(" ")}"></feFuncB>
				<feFuncA type="table" tableValues="${values.a.join(" ")}"></feFuncA>
			</feComponentTransfer>
			<!-- Re-mask the image with the original transparency since the feColorMatrix above loses that information. -->
			<feComposite in2="SourceGraphic" operator="in"></feComposite>
		</filter>
	</defs>
</svg>`;
}

// packages/global-styles-engine/build-module/utils/string.js
function kebabCase(str) {
  return str.replace(/([a-z])([A-Z])/g, "$1-$2").replace(/([0-9])([a-zA-Z])/g, "$1-$2").replace(/([a-zA-Z])([0-9])/g, "$1-$2").replace(/[\s_]+/g, "-").toLowerCase();
}

// packages/global-styles-engine/build-module/utils/spacing.js
function getSpacingPresetCssVar(value) {
  if (!value) {
    return;
  }
  const slug = value.match(/var:preset\|spacing\|(.+)/);
  if (!slug) {
    return value;
  }
  return `var(--wp--preset--spacing--${slug[1]})`;
}

// packages/global-styles-engine/build-module/utils/gap.js
function getGapBoxControlValueFromStyle(blockGapValue) {
  if (!blockGapValue) {
    return null;
  }
  const isValueString = typeof blockGapValue === "string";
  return {
    top: isValueString ? blockGapValue : blockGapValue?.top,
    left: isValueString ? blockGapValue : blockGapValue?.left
  };
}
function getGapCSSValue(blockGapValue, defaultValue = "0") {
  const blockGapBoxControlValue = getGapBoxControlValueFromStyle(blockGapValue);
  if (!blockGapBoxControlValue) {
    return null;
  }
  const row = getSpacingPresetCssVar(blockGapBoxControlValue?.top) || defaultValue;
  const column = getSpacingPresetCssVar(blockGapBoxControlValue?.left) || defaultValue;
  return row === column ? row : `${row} ${column}`;
}

// packages/global-styles-engine/build-module/utils/background.js
var BACKGROUND_BLOCK_DEFAULT_VALUES = {
  backgroundSize: "cover",
  backgroundPosition: "50% 50%"
  // used only when backgroundSize is 'contain'.
};
function setBackgroundStyleDefaults(backgroundStyle) {
  if (!backgroundStyle || // @ts-expect-error
  !backgroundStyle?.backgroundImage?.url) {
    return;
  }
  let backgroundStylesWithDefaults;
  if (!backgroundStyle?.backgroundSize) {
    backgroundStylesWithDefaults = {
      backgroundSize: BACKGROUND_BLOCK_DEFAULT_VALUES.backgroundSize
    };
  }
  if ("contain" === backgroundStyle?.backgroundSize && !backgroundStyle?.backgroundPosition) {
    backgroundStylesWithDefaults = {
      backgroundPosition: BACKGROUND_BLOCK_DEFAULT_VALUES.backgroundPosition
    };
  }
  return backgroundStylesWithDefaults;
}

// packages/global-styles-engine/build-module/utils/layout.js
var LAYOUT_DEFINITIONS = {
  default: {
    name: "default",
    slug: "flow",
    className: "is-layout-flow",
    baseStyles: [
      {
        selector: " > .alignleft",
        rules: {
          float: "left",
          "margin-inline-start": "0",
          "margin-inline-end": "2em"
        }
      },
      {
        selector: " > .alignright",
        rules: {
          float: "right",
          "margin-inline-start": "2em",
          "margin-inline-end": "0"
        }
      },
      {
        selector: " > .aligncenter",
        rules: {
          "margin-left": "auto !important",
          "margin-right": "auto !important"
        }
      }
    ],
    spacingStyles: [
      {
        selector: " > :first-child",
        rules: {
          "margin-block-start": "0"
        }
      },
      {
        selector: " > :last-child",
        rules: {
          "margin-block-end": "0"
        }
      },
      {
        selector: " > *",
        rules: {
          "margin-block-start": null,
          "margin-block-end": "0"
        }
      }
    ]
  },
  constrained: {
    name: "constrained",
    slug: "constrained",
    className: "is-layout-constrained",
    baseStyles: [
      {
        selector: " > .alignleft",
        rules: {
          float: "left",
          "margin-inline-start": "0",
          "margin-inline-end": "2em"
        }
      },
      {
        selector: " > .alignright",
        rules: {
          float: "right",
          "margin-inline-start": "2em",
          "margin-inline-end": "0"
        }
      },
      {
        selector: " > .aligncenter",
        rules: {
          "margin-left": "auto !important",
          "margin-right": "auto !important"
        }
      },
      {
        selector: " > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
        rules: {
          "max-width": "var(--wp--style--global--content-size)",
          "margin-left": "auto !important",
          "margin-right": "auto !important"
        }
      },
      {
        selector: " > .alignwide",
        rules: {
          "max-width": "var(--wp--style--global--wide-size)"
        }
      }
    ],
    spacingStyles: [
      {
        selector: " > :first-child",
        rules: {
          "margin-block-start": "0"
        }
      },
      {
        selector: " > :last-child",
        rules: {
          "margin-block-end": "0"
        }
      },
      {
        selector: " > *",
        rules: {
          "margin-block-start": null,
          "margin-block-end": "0"
        }
      }
    ]
  },
  flex: {
    name: "flex",
    slug: "flex",
    className: "is-layout-flex",
    displayMode: "flex",
    baseStyles: [
      {
        selector: "",
        rules: {
          "flex-wrap": "wrap",
          "align-items": "center"
        }
      },
      {
        selector: " > :is(*, div)",
        // :is(*, div) instead of just * increases the specificity by 001.
        rules: {
          margin: "0"
        }
      }
    ],
    spacingStyles: [
      {
        selector: "",
        rules: {
          gap: null
        }
      }
    ]
  },
  grid: {
    name: "grid",
    slug: "grid",
    className: "is-layout-grid",
    displayMode: "grid",
    baseStyles: [
      {
        selector: " > :is(*, div)",
        // :is(*, div) instead of just * increases the specificity by 001.
        rules: {
          margin: "0"
        }
      }
    ],
    spacingStyles: [
      {
        selector: "",
        rules: {
          gap: null
        }
      }
    ]
  }
};

// packages/global-styles-engine/build-module/core/render.js
var ELEMENT_CLASS_NAMES = {
  button: "wp-element-button",
  caption: "wp-element-caption"
};
var BLOCK_SUPPORT_FEATURE_LEVEL_SELECTORS = {
  __experimentalBorder: "border",
  color: "color",
  spacing: "spacing",
  typography: "typography"
};
function getPresetsDeclarations(blockPresets = {}, mergedSettings) {
  return PRESET_METADATA.reduce(
    (declarations, { path, valueKey, valueFunc, cssVarInfix }) => {
      const presetByOrigin = getValueFromObjectPath(
        blockPresets,
        path,
        []
      );
      ["default", "theme", "custom"].forEach((origin) => {
        if (presetByOrigin[origin]) {
          presetByOrigin[origin].forEach((value) => {
            if (valueKey && !valueFunc) {
              declarations.push(
                `--wp--preset--${cssVarInfix}--${kebabCase(
                  value.slug
                )}: ${value[valueKey]}`
              );
            } else if (valueFunc && typeof valueFunc === "function") {
              declarations.push(
                `--wp--preset--${cssVarInfix}--${kebabCase(
                  value.slug
                )}: ${valueFunc(value, mergedSettings)}`
              );
            }
          });
        }
      });
      return declarations;
    },
    []
  );
}
function getPresetsClasses(blockSelector = "*", blockPresets = {}) {
  return PRESET_METADATA.reduce(
    (declarations, { path, cssVarInfix, classes }) => {
      if (!classes) {
        return declarations;
      }
      const presetByOrigin = getValueFromObjectPath(
        blockPresets,
        path,
        []
      );
      ["default", "theme", "custom"].forEach((origin) => {
        if (presetByOrigin[origin]) {
          presetByOrigin[origin].forEach(
            ({ slug }) => {
              classes.forEach(
                ({
                  classSuffix,
                  propertyName
                }) => {
                  const classSelectorToUse = `.has-${kebabCase(
                    slug
                  )}-${classSuffix}`;
                  const selectorToUse = blockSelector.split(",").map(
                    (selector) => `${selector}${classSelectorToUse}`
                  ).join(",");
                  const value = `var(--wp--preset--${cssVarInfix}--${kebabCase(
                    slug
                  )})`;
                  declarations += `${selectorToUse}{${propertyName}: ${value} !important;}`;
                }
              );
            }
          );
        }
      });
      return declarations;
    },
    ""
  );
}
function getPresetsSvgFilters(blockPresets = {}) {
  return PRESET_METADATA.filter(
    // Duotone are the only type of filters for now.
    (metadata) => metadata.path.at(-1) === "duotone"
  ).flatMap((metadata) => {
    const presetByOrigin = getValueFromObjectPath(
      blockPresets,
      metadata.path,
      {}
    );
    return ["default", "theme"].filter((origin) => presetByOrigin[origin]).flatMap(
      (origin) => presetByOrigin[origin].map(
        (preset) => getDuotoneFilter(
          `wp-duotone-${preset.slug}`,
          preset.colors
        )
      )
    ).join("");
  });
}
function flattenTree(input = {}, prefix, token) {
  let result = [];
  Object.keys(input).forEach((key) => {
    const newKey = prefix + kebabCase(key.replace("/", "-"));
    const newLeaf = input[key];
    if (newLeaf instanceof Object) {
      const newPrefix = newKey + token;
      result = [...result, ...flattenTree(newLeaf, newPrefix, token)];
    } else {
      result.push(`${newKey}: ${newLeaf}`);
    }
  });
  return result;
}
function concatFeatureVariationSelectorString(featureSelector, styleVariationSelector) {
  const featureSelectors = featureSelector.split(",");
  const combinedSelectors = [];
  featureSelectors.forEach((selector) => {
    combinedSelectors.push(
      `${styleVariationSelector.trim()}${selector.trim()}`
    );
  });
  return combinedSelectors.join(", ");
}
var getFeatureDeclarations = (selectors, styles) => {
  const declarations = {};
  Object.entries(selectors).forEach(([feature, selector]) => {
    if (feature === "root" || !styles?.[feature]) {
      return;
    }
    const isShorthand = typeof selector === "string";
    if (!isShorthand && typeof selector === "object" && selector !== null) {
      Object.entries(selector).forEach(
        ([subfeature, subfeatureSelector]) => {
          if (subfeature === "root" || !styles?.[feature][subfeature]) {
            return;
          }
          const subfeatureStyles = {
            [feature]: {
              [subfeature]: styles[feature][subfeature]
            }
          };
          const newDeclarations = getStylesDeclarations(subfeatureStyles);
          declarations[subfeatureSelector] = [
            ...declarations[subfeatureSelector] || [],
            ...newDeclarations
          ];
          delete styles[feature][subfeature];
        }
      );
    }
    if (isShorthand || typeof selector === "object" && selector !== null && "root" in selector) {
      const featureSelector = isShorthand ? selector : selector.root;
      const featureStyles = { [feature]: styles[feature] };
      const newDeclarations = getStylesDeclarations(featureStyles);
      declarations[featureSelector] = [
        ...declarations[featureSelector] || [],
        ...newDeclarations
      ];
      delete styles[feature];
    }
  });
  return declarations;
};
function getStylesDeclarations(blockStyles = {}, selector = "", useRootPaddingAlign, tree = {}, disableRootPadding = false) {
  const isRoot = ROOT_BLOCK_SELECTOR === selector;
  const output = Object.entries(
    import_blocks.__EXPERIMENTAL_STYLE_PROPERTY
  ).reduce(
    (declarations, [key, { value, properties, useEngine, rootOnly }]) => {
      if (rootOnly && !isRoot) {
        return declarations;
      }
      const pathToValue = value;
      if (pathToValue[0] === "elements" || useEngine) {
        return declarations;
      }
      const styleValue = getValueFromObjectPath(
        blockStyles,
        pathToValue
      );
      if (key === "--wp--style--root--padding" && (typeof styleValue === "string" || !useRootPaddingAlign)) {
        return declarations;
      }
      if (properties && typeof styleValue !== "string") {
        Object.entries(properties).forEach((entry) => {
          const [name, prop] = entry;
          if (!getValueFromObjectPath(styleValue, [prop], false)) {
            return;
          }
          const cssProperty = name.startsWith("--") ? name : kebabCase(name);
          declarations.push(
            `${cssProperty}: ${(0, import_style_engine2.getCSSValueFromRawStyle)(
              getValueFromObjectPath(styleValue, [prop])
            )}`
          );
        });
      } else if (getValueFromObjectPath(blockStyles, pathToValue, false)) {
        const cssProperty = key.startsWith("--") ? key : kebabCase(key);
        declarations.push(
          `${cssProperty}: ${(0, import_style_engine2.getCSSValueFromRawStyle)(
            getValueFromObjectPath(blockStyles, pathToValue)
          )}`
        );
      }
      return declarations;
    },
    []
  );
  if (!!blockStyles.background) {
    if (blockStyles.background?.backgroundImage) {
      blockStyles.background.backgroundImage = getResolvedValue(
        blockStyles.background.backgroundImage,
        tree
      );
    }
    if (!isRoot && !!blockStyles.background?.backgroundImage?.id) {
      blockStyles = {
        ...blockStyles,
        background: {
          ...blockStyles.background,
          ...setBackgroundStyleDefaults(blockStyles.background)
        }
      };
    }
  }
  const extraRules = (0, import_style_engine2.getCSSRules)(blockStyles);
  extraRules.forEach((rule) => {
    if (isRoot && (useRootPaddingAlign || disableRootPadding) && rule.key.startsWith("padding")) {
      return;
    }
    const cssProperty = rule.key.startsWith("--") ? rule.key : kebabCase(rule.key);
    let ruleValue = getResolvedValue(rule.value, tree);
    if (cssProperty === "font-size") {
      ruleValue = getTypographyFontSizeValue(
        { name: "", slug: "", size: ruleValue },
        tree?.settings
      );
    }
    if (cssProperty === "aspect-ratio") {
      output.push("min-height: unset");
    }
    output.push(`${cssProperty}: ${ruleValue}`);
  });
  return output;
}
function getLayoutStyles({
  layoutDefinitions = LAYOUT_DEFINITIONS,
  style,
  selector,
  hasBlockGapSupport,
  hasFallbackGapSupport,
  fallbackGapValue
}) {
  let ruleset = "";
  let gapValue = hasBlockGapSupport ? getGapCSSValue(style?.spacing?.blockGap) : "";
  if (hasFallbackGapSupport) {
    if (selector === ROOT_BLOCK_SELECTOR) {
      gapValue = !gapValue ? "0.5em" : gapValue;
    } else if (!hasBlockGapSupport && fallbackGapValue) {
      gapValue = fallbackGapValue;
    }
  }
  if (gapValue && layoutDefinitions) {
    Object.values(layoutDefinitions).forEach(
      ({ className, name, spacingStyles }) => {
        if (!hasBlockGapSupport && "flex" !== name && "grid" !== name) {
          return;
        }
        if (spacingStyles?.length) {
          spacingStyles.forEach((spacingStyle) => {
            const declarations = [];
            if (spacingStyle.rules) {
              Object.entries(spacingStyle.rules).forEach(
                ([cssProperty, cssValue]) => {
                  declarations.push(
                    `${cssProperty}: ${cssValue ? cssValue : gapValue}`
                  );
                }
              );
            }
            if (declarations.length) {
              let combinedSelector = "";
              if (!hasBlockGapSupport) {
                combinedSelector = selector === ROOT_BLOCK_SELECTOR ? `:where(.${className}${spacingStyle?.selector || ""})` : `:where(${selector}.${className}${spacingStyle?.selector || ""})`;
              } else {
                combinedSelector = selector === ROOT_BLOCK_SELECTOR ? `:root :where(.${className})${spacingStyle?.selector || ""}` : `:root :where(${selector}-${className})${spacingStyle?.selector || ""}`;
              }
              ruleset += `${combinedSelector} { ${declarations.join(
                "; "
              )}; }`;
            }
          });
        }
      }
    );
    if (selector === ROOT_BLOCK_SELECTOR && hasBlockGapSupport) {
      ruleset += `${ROOT_CSS_PROPERTIES_SELECTOR} { --wp--style--block-gap: ${gapValue}; }`;
    }
  }
  if (selector === ROOT_BLOCK_SELECTOR && layoutDefinitions) {
    const validDisplayModes = ["block", "flex", "grid"];
    Object.values(layoutDefinitions).forEach(
      ({ className, displayMode, baseStyles }) => {
        if (displayMode && validDisplayModes.includes(displayMode)) {
          ruleset += `${selector} .${className} { display:${displayMode}; }`;
        }
        if (baseStyles?.length) {
          baseStyles.forEach((baseStyle) => {
            const declarations = [];
            if (baseStyle.rules) {
              Object.entries(baseStyle.rules).forEach(
                ([cssProperty, cssValue]) => {
                  declarations.push(
                    `${cssProperty}: ${cssValue}`
                  );
                }
              );
            }
            if (declarations.length) {
              const combinedSelector = `.${className}${baseStyle?.selector || ""}`;
              ruleset += `${combinedSelector} { ${declarations.join(
                "; "
              )}; }`;
            }
          });
        }
      }
    );
  }
  return ruleset;
}
var STYLE_KEYS = [
  "border",
  "color",
  "dimensions",
  "spacing",
  "typography",
  "filter",
  "outline",
  "shadow",
  "background"
];
function pickStyleKeys(treeToPickFrom) {
  if (!treeToPickFrom) {
    return {};
  }
  const entries = Object.entries(treeToPickFrom);
  const pickedEntries = entries.filter(
    ([key]) => STYLE_KEYS.includes(key)
  );
  const clonedEntries = pickedEntries.map(([key, style]) => [
    key,
    JSON.parse(JSON.stringify(style))
  ]);
  return Object.fromEntries(clonedEntries);
}
var getNodesWithStyles = (tree, blockSelectors) => {
  const nodes = [];
  if (!tree?.styles) {
    return nodes;
  }
  const styles = pickStyleKeys(tree.styles);
  if (styles) {
    nodes.push({
      styles,
      selector: ROOT_BLOCK_SELECTOR,
      // Root selector (body) styles should not be wrapped in `:root where()` to keep
      // specificity at (0,0,1) and maintain backwards compatibility.
      skipSelectorWrapper: true
    });
  }
  Object.entries(import_blocks.__EXPERIMENTAL_ELEMENTS).forEach(([name, selector]) => {
    if (tree.styles?.elements?.[name]) {
      nodes.push({
        styles: tree.styles?.elements?.[name] ?? {},
        selector,
        // Top level elements that don't use a class name should not receive the
        // `:root :where()` wrapper to maintain backwards compatibility.
        skipSelectorWrapper: !ELEMENT_CLASS_NAMES[name]
      });
    }
  });
  Object.entries(tree.styles?.blocks ?? {}).forEach(
    ([blockName, node]) => {
      const blockStyles = pickStyleKeys(node);
      const typedNode = node;
      if (typedNode?.variations) {
        const variations = {};
        Object.entries(typedNode.variations).forEach(
          ([variationName, variation]) => {
            const typedVariation = variation;
            variations[variationName] = pickStyleKeys(typedVariation);
            if (typedVariation?.css) {
              variations[variationName].css = typedVariation.css;
            }
            const variationSelector = typeof blockSelectors !== "string" ? blockSelectors[blockName]?.styleVariationSelectors?.[variationName] : void 0;
            Object.entries(
              typedVariation?.elements ?? {}
            ).forEach(([element, elementStyles]) => {
              if (elementStyles && import_blocks.__EXPERIMENTAL_ELEMENTS[element]) {
                nodes.push({
                  styles: elementStyles,
                  selector: scopeSelector(
                    variationSelector,
                    import_blocks.__EXPERIMENTAL_ELEMENTS[element]
                  )
                });
              }
            });
            Object.entries(typedVariation?.blocks ?? {}).forEach(
              ([
                variationBlockName,
                variationBlockStyles
              ]) => {
                const variationBlockSelector = typeof blockSelectors !== "string" ? scopeSelector(
                  variationSelector,
                  blockSelectors[variationBlockName]?.selector
                ) : void 0;
                const variationDuotoneSelector = typeof blockSelectors !== "string" ? scopeSelector(
                  variationSelector,
                  blockSelectors[variationBlockName]?.duotoneSelector
                ) : void 0;
                const variationFeatureSelectors = typeof blockSelectors !== "string" ? scopeFeatureSelectors(
                  variationSelector,
                  blockSelectors[variationBlockName]?.featureSelectors ?? {}
                ) : void 0;
                const variationBlockStyleNodes = pickStyleKeys(variationBlockStyles);
                if (variationBlockStyles?.css) {
                  variationBlockStyleNodes.css = variationBlockStyles.css;
                }
                if (!variationBlockSelector || typeof blockSelectors === "string") {
                  return;
                }
                nodes.push({
                  selector: variationBlockSelector,
                  duotoneSelector: variationDuotoneSelector,
                  featureSelectors: variationFeatureSelectors,
                  fallbackGapValue: blockSelectors[variationBlockName]?.fallbackGapValue,
                  hasLayoutSupport: blockSelectors[variationBlockName]?.hasLayoutSupport,
                  styles: variationBlockStyleNodes
                });
                Object.entries(
                  variationBlockStyles.elements ?? {}
                ).forEach(
                  ([
                    variationBlockElement,
                    variationBlockElementStyles
                  ]) => {
                    if (variationBlockElementStyles && import_blocks.__EXPERIMENTAL_ELEMENTS[variationBlockElement]) {
                      nodes.push({
                        styles: variationBlockElementStyles,
                        selector: scopeSelector(
                          variationBlockSelector,
                          import_blocks.__EXPERIMENTAL_ELEMENTS[variationBlockElement]
                        )
                      });
                    }
                  }
                );
              }
            );
          }
        );
        blockStyles.variations = variations;
      }
      if (typeof blockSelectors !== "string" && blockSelectors?.[blockName]?.selector) {
        nodes.push({
          duotoneSelector: blockSelectors[blockName].duotoneSelector,
          fallbackGapValue: blockSelectors[blockName].fallbackGapValue,
          hasLayoutSupport: blockSelectors[blockName].hasLayoutSupport,
          selector: blockSelectors[blockName].selector,
          styles: blockStyles,
          featureSelectors: blockSelectors[blockName].featureSelectors,
          styleVariationSelectors: blockSelectors[blockName].styleVariationSelectors
        });
      }
      Object.entries(typedNode?.elements ?? {}).forEach(
        ([elementName, value]) => {
          if (typeof blockSelectors !== "string" && value && blockSelectors?.[blockName] && import_blocks.__EXPERIMENTAL_ELEMENTS[elementName]) {
            nodes.push({
              styles: value,
              selector: blockSelectors[blockName]?.selector.split(",").map((sel) => {
                const elementSelectors = import_blocks.__EXPERIMENTAL_ELEMENTS[elementName].split(",");
                return elementSelectors.map(
                  (elementSelector) => sel + " " + elementSelector
                );
              }).join(",")
            });
          }
        }
      );
    }
  );
  return nodes;
};
var getNodesWithSettings = (tree, blockSelectors) => {
  const nodes = [];
  if (!tree?.settings) {
    return nodes;
  }
  const pickPresets = (treeToPickFrom) => {
    let presets2 = {};
    PRESET_METADATA.forEach(({ path }) => {
      const value = getValueFromObjectPath(treeToPickFrom, path, false);
      if (value !== false) {
        presets2 = setImmutably(presets2, path, value);
      }
    });
    return presets2;
  };
  const presets = pickPresets(tree.settings);
  const custom = tree.settings?.custom;
  if (Object.keys(presets).length > 0 || custom) {
    nodes.push({
      presets,
      custom,
      selector: ROOT_CSS_PROPERTIES_SELECTOR
    });
  }
  Object.entries(tree.settings?.blocks ?? {}).forEach(
    ([blockName, node]) => {
      const blockCustom = node.custom;
      if (typeof blockSelectors === "string" || !blockSelectors[blockName]) {
        return;
      }
      const blockPresets = pickPresets(node);
      if (Object.keys(blockPresets).length > 0 || blockCustom) {
        nodes.push({
          presets: blockPresets,
          custom: blockCustom,
          selector: blockSelectors[blockName]?.selector
        });
      }
    }
  );
  return nodes;
};
var generateCustomProperties = (tree, blockSelectors) => {
  const settings = getNodesWithSettings(tree, blockSelectors);
  let ruleset = "";
  settings.forEach(({ presets, custom, selector }) => {
    const declarations = tree?.settings ? getPresetsDeclarations(presets, tree?.settings) : [];
    const customProps = flattenTree(custom, "--wp--custom--", "--");
    if (customProps.length > 0) {
      declarations.push(...customProps);
    }
    if (declarations.length > 0) {
      ruleset += `${selector}{${declarations.join(";")};}`;
    }
  });
  return ruleset;
};
var transformToStyles = (tree, blockSelectors, hasBlockGapSupport, hasFallbackGapSupport, disableLayoutStyles = false, disableRootPadding = false, styleOptions = {}) => {
  const options = {
    blockGap: true,
    blockStyles: true,
    layoutStyles: true,
    marginReset: true,
    presets: true,
    rootPadding: true,
    variationStyles: false,
    ...styleOptions
  };
  const nodesWithStyles = getNodesWithStyles(tree, blockSelectors);
  const nodesWithSettings = getNodesWithSettings(tree, blockSelectors);
  const useRootPaddingAlign = tree?.settings?.useRootPaddingAwareAlignments;
  const { contentSize, wideSize } = tree?.settings?.layout || {};
  const hasBodyStyles = options.marginReset || options.rootPadding || options.layoutStyles;
  let ruleset = "";
  if (options.presets && (contentSize || wideSize)) {
    ruleset += `${ROOT_CSS_PROPERTIES_SELECTOR} {`;
    ruleset = contentSize ? ruleset + ` --wp--style--global--content-size: ${contentSize};` : ruleset;
    ruleset = wideSize ? ruleset + ` --wp--style--global--wide-size: ${wideSize};` : ruleset;
    ruleset += "}";
  }
  if (hasBodyStyles) {
    ruleset += ":where(body) {margin: 0;";
    if (options.rootPadding && useRootPaddingAlign) {
      ruleset += `padding-right: 0; padding-left: 0; padding-top: var(--wp--style--root--padding-top); padding-bottom: var(--wp--style--root--padding-bottom) }
				.has-global-padding { padding-right: var(--wp--style--root--padding-right); padding-left: var(--wp--style--root--padding-left); }
				.has-global-padding > .alignfull { margin-right: calc(var(--wp--style--root--padding-right) * -1); margin-left: calc(var(--wp--style--root--padding-left) * -1); }
				.has-global-padding :where(:not(.alignfull.is-layout-flow) > .has-global-padding:not(.wp-block-block, .alignfull)) { padding-right: 0; padding-left: 0; }
				.has-global-padding :where(:not(.alignfull.is-layout-flow) > .has-global-padding:not(.wp-block-block, .alignfull)) > .alignfull { margin-left: 0; margin-right: 0;
				`;
    }
    ruleset += "}";
  }
  if (options.blockStyles) {
    nodesWithStyles.forEach(
      ({
        selector,
        duotoneSelector,
        styles,
        fallbackGapValue,
        hasLayoutSupport,
        featureSelectors,
        styleVariationSelectors,
        skipSelectorWrapper
      }) => {
        if (featureSelectors) {
          const featureDeclarations = getFeatureDeclarations(
            featureSelectors,
            styles
          );
          Object.entries(featureDeclarations).forEach(
            ([cssSelector, declarations]) => {
              if (declarations.length) {
                const rules = declarations.join(";");
                ruleset += `:root :where(${cssSelector}){${rules};}`;
              }
            }
          );
        }
        if (duotoneSelector) {
          const duotoneStyles = {};
          if (styles?.filter) {
            duotoneStyles.filter = styles.filter;
            delete styles.filter;
          }
          const duotoneDeclarations = getStylesDeclarations(duotoneStyles);
          if (duotoneDeclarations.length) {
            ruleset += `${duotoneSelector}{${duotoneDeclarations.join(
              ";"
            )};}`;
          }
        }
        if (!disableLayoutStyles && (ROOT_BLOCK_SELECTOR === selector || hasLayoutSupport)) {
          ruleset += getLayoutStyles({
            style: styles,
            selector,
            hasBlockGapSupport,
            hasFallbackGapSupport,
            fallbackGapValue
          });
        }
        const styleDeclarations = getStylesDeclarations(
          styles,
          selector,
          useRootPaddingAlign,
          tree,
          disableRootPadding
        );
        if (styleDeclarations?.length) {
          const generalSelector = skipSelectorWrapper ? selector : `:root :where(${selector})`;
          ruleset += `${generalSelector}{${styleDeclarations.join(
            ";"
          )};}`;
        }
        if (styles?.css) {
          ruleset += processCSSNesting(
            styles.css,
            `:root :where(${selector})`
          );
        }
        if (options.variationStyles && styleVariationSelectors) {
          Object.entries(styleVariationSelectors).forEach(
            ([styleVariationName, styleVariationSelector]) => {
              const styleVariations = styles?.variations?.[styleVariationName];
              if (styleVariations) {
                if (featureSelectors) {
                  const featureDeclarations = getFeatureDeclarations(
                    featureSelectors,
                    styleVariations
                  );
                  Object.entries(
                    featureDeclarations
                  ).forEach(
                    ([baseSelector, declarations]) => {
                      if (declarations.length) {
                        const cssSelector = concatFeatureVariationSelectorString(
                          baseSelector,
                          styleVariationSelector
                        );
                        const rules = declarations.join(";");
                        ruleset += `:root :where(${cssSelector}){${rules};}`;
                      }
                    }
                  );
                }
                const styleVariationDeclarations = getStylesDeclarations(
                  styleVariations,
                  styleVariationSelector,
                  useRootPaddingAlign,
                  tree
                );
                if (styleVariationDeclarations.length) {
                  ruleset += `:root :where(${styleVariationSelector}){${styleVariationDeclarations.join(
                    ";"
                  )};}`;
                }
                if (styleVariations?.css) {
                  ruleset += processCSSNesting(
                    styleVariations.css,
                    `:root :where(${styleVariationSelector})`
                  );
                }
              }
            }
          );
        }
        const pseudoSelectorStyles = Object.entries(styles).filter(
          ([key]) => key.startsWith(":")
        );
        if (pseudoSelectorStyles?.length) {
          pseudoSelectorStyles.forEach(
            ([pseudoKey, pseudoStyle]) => {
              const pseudoDeclarations = getStylesDeclarations(pseudoStyle);
              if (!pseudoDeclarations?.length) {
                return;
              }
              const _selector = selector.split(",").map((sel) => sel + pseudoKey).join(",");
              const pseudoRule = `:root :where(${_selector}){${pseudoDeclarations.join(
                ";"
              )};}`;
              ruleset += pseudoRule;
            }
          );
        }
      }
    );
  }
  if (options.layoutStyles) {
    ruleset = ruleset + ".wp-site-blocks > .alignleft { float: left; margin-right: 2em; }";
    ruleset = ruleset + ".wp-site-blocks > .alignright { float: right; margin-left: 2em; }";
    ruleset = ruleset + ".wp-site-blocks > .aligncenter { justify-content: center; margin-left: auto; margin-right: auto; }";
  }
  if (options.blockGap && hasBlockGapSupport) {
    const gapValue = getGapCSSValue(tree?.styles?.spacing?.blockGap) || "0.5em";
    ruleset = ruleset + `:root :where(.wp-site-blocks) > * { margin-block-start: ${gapValue}; margin-block-end: 0; }`;
    ruleset = ruleset + ":root :where(.wp-site-blocks) > :first-child { margin-block-start: 0; }";
    ruleset = ruleset + ":root :where(.wp-site-blocks) > :last-child { margin-block-end: 0; }";
  }
  if (options.presets) {
    nodesWithSettings.forEach(({ selector, presets }) => {
      if (ROOT_BLOCK_SELECTOR === selector || ROOT_CSS_PROPERTIES_SELECTOR === selector) {
        selector = "";
      }
      const classes = getPresetsClasses(selector, presets);
      if (classes.length > 0) {
        ruleset += classes;
      }
    });
  }
  return ruleset;
};
function generateSvgFilters(tree, blockSelectors) {
  const nodesWithSettings = getNodesWithSettings(tree, blockSelectors);
  return nodesWithSettings.flatMap(({ presets }) => {
    return getPresetsSvgFilters(presets);
  });
}
var getSelectorsConfig = (blockType, rootSelector) => {
  if (blockType?.selectors && Object.keys(blockType.selectors).length > 0) {
    return blockType.selectors;
  }
  const config = {
    root: rootSelector
  };
  Object.entries(BLOCK_SUPPORT_FEATURE_LEVEL_SELECTORS).forEach(
    ([featureKey, featureName]) => {
      const featureSelector = getBlockSelector(blockType, featureKey);
      if (featureSelector) {
        config[featureName] = featureSelector;
      }
    }
  );
  return config;
};
var getBlockSelectors = (blockTypes, variationInstanceId) => {
  const { getBlockStyles } = (0, import_data2.select)(import_blocks.store);
  const result = {};
  blockTypes.forEach((blockType) => {
    const name = blockType.name;
    const selector = getBlockSelector(blockType);
    if (!selector) {
      return;
    }
    let duotoneSelector = getBlockSelector(blockType, "filter.duotone");
    if (!duotoneSelector) {
      const rootSelector = getBlockSelector(blockType);
      const duotoneSupport = (0, import_blocks.getBlockSupport)(
        blockType,
        "color.__experimentalDuotone",
        false
      );
      duotoneSelector = duotoneSupport && rootSelector && scopeSelector(rootSelector, duotoneSupport);
    }
    const hasLayoutSupport = !!blockType?.supports?.layout || !!blockType?.supports?.__experimentalLayout;
    const fallbackGapValue = (
      // @ts-expect-error
      blockType?.supports?.spacing?.blockGap?.__experimentalDefault
    );
    const blockStyleVariations = getBlockStyles(name);
    const styleVariationSelectors = {};
    blockStyleVariations?.forEach((variation) => {
      const variationSuffix = variationInstanceId ? `-${variationInstanceId}` : "";
      const variationName = `${variation.name}${variationSuffix}`;
      const styleVariationSelector = getBlockStyleVariationSelector(
        variationName,
        selector
      );
      styleVariationSelectors[variationName] = styleVariationSelector;
    });
    const featureSelectors = getSelectorsConfig(blockType, selector);
    result[name] = {
      duotoneSelector: duotoneSelector ?? void 0,
      fallbackGapValue,
      featureSelectors: Object.keys(featureSelectors).length ? featureSelectors : void 0,
      hasLayoutSupport,
      name,
      selector,
      styleVariationSelectors: blockStyleVariations?.length ? styleVariationSelectors : void 0
    };
  });
  return result;
};
function updateConfigWithSeparator(config) {
  const blocks = config.styles?.blocks;
  const separatorBlock = blocks?.["core/separator"];
  const needsSeparatorStyleUpdate = separatorBlock && separatorBlock.color?.background && !separatorBlock.color?.text && !separatorBlock.border?.color;
  if (needsSeparatorStyleUpdate) {
    return {
      ...config,
      styles: {
        ...config.styles,
        blocks: {
          ...blocks,
          "core/separator": {
            ...separatorBlock,
            color: {
              ...separatorBlock.color,
              text: separatorBlock.color?.background
            }
          }
        }
      }
    };
  }
  return config;
}
function processCSSNesting(css, blockSelector) {
  let processedCSS = "";
  if (!css || css.trim() === "") {
    return processedCSS;
  }
  const parts = css.split("&");
  parts.forEach((part) => {
    if (!part || part.trim() === "") {
      return;
    }
    const isRootCss = !part.includes("{");
    if (isRootCss) {
      processedCSS += `:root :where(${blockSelector}){${part.trim()}}`;
    } else {
      const splitPart = part.replace("}", "").split("{");
      if (splitPart.length !== 2) {
        return;
      }
      const [nestedSelector, cssValue] = splitPart;
      const matches = nestedSelector.match(/([>+~\s]*::[a-zA-Z-]+)/);
      const pseudoPart = matches ? matches[1] : "";
      const withoutPseudoElement = matches ? nestedSelector.replace(pseudoPart, "").trim() : nestedSelector.trim();
      let combinedSelector;
      if (withoutPseudoElement === "") {
        combinedSelector = blockSelector;
      } else {
        combinedSelector = nestedSelector.startsWith(" ") ? scopeSelector(blockSelector, withoutPseudoElement) : appendToSelector(blockSelector, withoutPseudoElement);
      }
      processedCSS += `:root :where(${combinedSelector})${pseudoPart}{${cssValue.trim()}}`;
    }
  });
  return processedCSS;
}
function generateGlobalStyles(config = {}, blockTypes = [], options = {}) {
  const {
    hasBlockGapSupport: hasBlockGapSupportOption,
    hasFallbackGapSupport: hasFallbackGapSupportOption,
    disableLayoutStyles = false,
    disableRootPadding = false
  } = options;
  const blocks = blockTypes.length > 0 ? blockTypes : (0, import_blocks.getBlockTypes)();
  const blockGap = getSetting(config, "spacing.blockGap");
  const hasBlockGapSupport = hasBlockGapSupportOption ?? blockGap !== null;
  const hasFallbackGapSupport = hasFallbackGapSupportOption ?? !hasBlockGapSupport;
  if (!config?.styles || !config?.settings) {
    return [[], {}];
  }
  const updatedConfig = updateConfigWithSeparator(config);
  const blockSelectors = getBlockSelectors(blocks);
  const customProperties = generateCustomProperties(
    updatedConfig,
    blockSelectors
  );
  const globalStyles = transformToStyles(
    updatedConfig,
    blockSelectors,
    hasBlockGapSupport,
    hasFallbackGapSupport,
    disableLayoutStyles,
    disableRootPadding
  );
  const svgs = generateSvgFilters(updatedConfig, blockSelectors);
  const styles = [
    {
      css: customProperties,
      isGlobalStyles: true
    },
    {
      css: globalStyles,
      isGlobalStyles: true
    },
    // Load custom CSS in own stylesheet so that any invalid CSS entered in the input won't break all the global styles in the editor.
    {
      css: updatedConfig?.styles?.css ?? "",
      isGlobalStyles: true
    },
    {
      assets: svgs,
      __unstableType: "svg",
      isGlobalStyles: true
    }
  ];
  blocks.forEach((blockType) => {
    const blockStyles = updatedConfig?.styles?.blocks?.[blockType.name];
    if (blockStyles?.css) {
      const selector = blockSelectors[blockType.name].selector;
      styles.push({
        css: processCSSNesting(blockStyles.css, selector),
        isGlobalStyles: true
      });
    }
  });
  return [styles, updatedConfig.settings];
}

// packages/lazy-editor/build-module/hooks/use-editor-settings.js
var import_core_data3 = __toESM(require_core_data());
var import_element2 = __toESM(require_element());
var import_data4 = __toESM(require_data());

// packages/lazy-editor/build-module/hooks/use-global-styles.js
var import_core_data2 = __toESM(require_core_data());
var import_data3 = __toESM(require_data());
var import_element = __toESM(require_element());
function useUserGlobalStyles(id) {
  const { userGlobalStyles } = (0, import_data3.useSelect)(
    (select2) => {
      const { getEntityRecord, getEditedEntityRecord, canUser } = select2(import_core_data2.store);
      const userCanEditGlobalStyles = canUser("update", {
        kind: "root",
        name: "globalStyles",
        id
      });
      let record;
      if (
        /*
         * Test that the OPTIONS request for user capabilities is complete
         * before fetching the global styles entity record.
         * This is to avoid fetching the global styles entity unnecessarily.
         */
        typeof userCanEditGlobalStyles === "boolean"
      ) {
        if (userCanEditGlobalStyles) {
          record = getEditedEntityRecord(
            "root",
            "globalStyles",
            id
          );
        } else {
          record = getEntityRecord("root", "globalStyles", id, {
            context: "view"
          });
        }
      }
      return {
        userGlobalStyles: record
      };
    },
    [id]
  );
  return (0, import_element.useMemo)(() => {
    if (!userGlobalStyles) {
      return {
        user: void 0
      };
    }
    const user = userGlobalStyles;
    return {
      user
    };
  }, [userGlobalStyles]);
}

// packages/lazy-editor/build-module/lock-unlock.js
var import_private_apis = __toESM(require_private_apis());
var { unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/lazy-editor"
);

// packages/lazy-editor/build-module/hooks/use-editor-settings.js
function useEditorSettings({ stylesId }) {
  const { editorSettings } = (0, import_data4.useSelect)(
    (select2) => ({
      editorSettings: unlock(
        select2(import_core_data3.store)
      ).getEditorSettings()
    }),
    []
  );
  const { user: globalStyles } = useUserGlobalStyles(stylesId);
  const [globalStylesCSS] = generateGlobalStyles(globalStyles);
  const hasEditorSettings = !!editorSettings;
  const styles = (0, import_element2.useMemo)(() => {
    if (!hasEditorSettings) {
      return [];
    }
    return [
      ...editorSettings?.styles ?? [],
      ...globalStylesCSS
    ];
  }, [hasEditorSettings, editorSettings?.styles, globalStylesCSS]);
  return {
    isReady: hasEditorSettings,
    editorSettings: (0, import_element2.useMemo)(
      () => ({
        ...editorSettings ?? {},
        styles
      }),
      [editorSettings, styles]
    )
  };
}

// packages/asset-loader/build-module/index.js
function loadStylesheet(handle, styleData) {
  return new Promise((resolve) => {
    if (!styleData.src) {
      resolve();
      return;
    }
    const existingLink = document.getElementById(handle + "-css");
    if (existingLink) {
      resolve();
      return;
    }
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = styleData.src + (styleData.version ? "?ver=" + styleData.version : "");
    link.id = handle + "-css";
    link.media = styleData.media || "all";
    link.onload = () => resolve();
    link.onerror = () => {
      console.error(`Failed to load stylesheet: ${handle}`);
      resolve();
    };
    document.head.appendChild(link);
  });
}
function loadScript(handle, scriptData) {
  if (!scriptData.src) {
    const marker = document.createElement("script");
    marker.id = handle + "-js";
    marker.textContent = "// Processed: " + handle;
    return marker;
  }
  const script = document.createElement("script");
  script.src = scriptData.src + (scriptData.version ? "?ver=" + scriptData.version : "");
  script.id = handle + "-js";
  script.async = false;
  return script;
}
function injectInlineStyle(handle, inlineStyle, position) {
  let styleContent = "";
  if (Array.isArray(inlineStyle)) {
    styleContent = inlineStyle.join("\n");
  } else if (typeof inlineStyle === "string") {
    styleContent = inlineStyle;
  }
  if (styleContent && styleContent.trim()) {
    const styleId = handle + "-" + position + "-inline-css";
    if (!document.getElementById(styleId)) {
      const style = document.createElement("style");
      style.id = styleId;
      style.textContent = styleContent.trim();
      document.head.appendChild(style);
    }
  }
}
function injectInlineScript(handle, inlineScript, position) {
  let scriptContent = inlineScript;
  if (Array.isArray(scriptContent)) {
    scriptContent = scriptContent.join("\n");
  }
  const script = document.createElement("script");
  script.id = handle + "-" + position + "-js";
  script.textContent = scriptContent.trim();
  return script;
}
function buildDependencyOrderedList(assetsData) {
  const visited = /* @__PURE__ */ new Set();
  const visiting = /* @__PURE__ */ new Set();
  const orderedList = [];
  function visit(handle) {
    if (visited.has(handle)) {
      return;
    }
    if (visiting.has(handle)) {
      console.warn(
        `Circular dependency detected for handle: ${handle}`
      );
      return;
    }
    visiting.add(handle);
    if (assetsData[handle]) {
      const deps = assetsData[handle].deps || [];
      deps.forEach((dep) => {
        if (assetsData[dep]) {
          visit(dep);
        }
      });
    }
    visiting.delete(handle);
    visited.add(handle);
    if (assetsData[handle]) {
      orderedList.push(handle);
    }
  }
  Object.keys(assetsData).forEach((handle) => {
    visit(handle);
  });
  return orderedList;
}
async function performScriptLoad(scriptElements, destination) {
  let parallel = [];
  for (const scriptElement of scriptElements) {
    if (scriptElement.src) {
      const loader = Promise.withResolvers();
      scriptElement.onload = () => loader.resolve();
      scriptElement.onerror = () => {
        console.error(`Failed to load script: ${scriptElement.id}`);
        loader.resolve();
      };
      parallel.push(loader.promise);
    } else {
      await Promise.all(parallel);
      parallel = [];
    }
    destination.appendChild(scriptElement);
  }
  await Promise.all(parallel);
  parallel = [];
}
async function loadAssets(scriptsData, inlineScripts, stylesData, inlineStyles) {
  const orderedStyles = buildDependencyOrderedList(stylesData);
  const orderedScripts = buildDependencyOrderedList(scriptsData);
  const stylePromises = [];
  for (const handle of orderedStyles) {
    const beforeInline = inlineStyles.before?.[handle];
    if (beforeInline) {
      injectInlineStyle(handle, beforeInline, "before");
    }
    stylePromises.push(loadStylesheet(handle, stylesData[handle]));
    const afterInline = inlineStyles.after?.[handle];
    if (afterInline) {
      injectInlineStyle(handle, afterInline, "after");
    }
  }
  const scriptElementsHead = [];
  const scriptElementsBody = [];
  for (const handle of orderedScripts) {
    const inFooter = scriptsData[handle].in_footer || false;
    const scriptElements = inFooter ? scriptElementsBody : scriptElementsHead;
    const beforeInline = inlineScripts.before?.[handle];
    if (beforeInline) {
      scriptElements.push(
        injectInlineScript(handle, beforeInline, "before")
      );
    }
    scriptElements.push(loadScript(handle, scriptsData[handle]));
    const afterInline = inlineScripts.after?.[handle];
    if (afterInline) {
      scriptElements.push(
        injectInlineScript(handle, afterInline, "after")
      );
    }
  }
  const scriptsPromise = (async () => {
    await performScriptLoad(scriptElementsHead, document.head);
    await performScriptLoad(scriptElementsBody, document.body);
  })();
  await Promise.all([Promise.all(stylePromises), scriptsPromise]);
}
var index_default = loadAssets;

// packages/lazy-editor/build-module/hooks/use-editor-assets.js
var import_core_data4 = __toESM(require_core_data());
var import_element3 = __toESM(require_element());
var import_data5 = __toESM(require_data());
var loadAssetsPromise;
async function loadEditorAssets() {
  const load = async () => {
    const editorAssets = await unlock(
      (0, import_data5.resolveSelect)(import_core_data4.store)
    ).getEditorAssets();
    await index_default(
      editorAssets.scripts || {},
      editorAssets.inline_scripts || { before: {}, after: {} },
      editorAssets.styles || {},
      editorAssets.inline_styles || { before: {}, after: {} }
    );
  };
  if (!loadAssetsPromise) {
    loadAssetsPromise = load();
  }
  return loadAssetsPromise;
}
function useEditorAssets() {
  const editorAssets = (0, import_data5.useSelect)((select2) => {
    return unlock(select2(import_core_data4.store)).getEditorAssets();
  }, []);
  const [assetsLoaded, setAssetsLoaded] = (0, import_element3.useState)(false);
  (0, import_element3.useEffect)(() => {
    if (editorAssets && !assetsLoaded) {
      loadEditorAssets().then(() => {
        setAssetsLoaded(true);
      }).catch((error) => {
        console.error("Failed to load editor assets:", error);
      });
    }
  }, [editorAssets, assetsLoaded]);
  return {
    isReady: !!editorAssets && assetsLoaded,
    assetsLoaded
  };
}

// packages/lazy-editor/build-module/component.js
var import_jsx_runtime = __toESM(require_jsx_runtime());
var { Editor: PrivateEditor, BackButton } = unlock(import_editor.privateApis);
function Editor({
  postType,
  postId,
  settings,
  backButton
}) {
  const templateId = (0, import_data6.useSelect)(
    (select2) => {
      if (!postType || !postId) {
        return void 0;
      }
      if (postType === "wp_template") {
        return postId;
      }
      return unlock(select2(import_core_data5.store)).getTemplateId(
        postType,
        postId
      );
    },
    [postType, postId]
  );
  const stylesId = useStylesId({ templateId });
  const { isReady: settingsReady, editorSettings } = useEditorSettings({
    stylesId
  });
  const { isReady: assetsReady } = useEditorAssets();
  const finalSettings = (0, import_element4.useMemo)(
    () => ({
      ...editorSettings,
      ...settings
    }),
    [editorSettings, settings]
  );
  if (!settingsReady || !assetsReady) {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100vh"
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Spinner, {})
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
    PrivateEditor,
    {
      postType,
      postId,
      templateId,
      settings: finalSettings,
      styles: finalSettings.styles,
      children: backButton && /* @__PURE__ */ (0, import_jsx_runtime.jsx)(BackButton, { children: backButton })
    }
  );
}
export {
  Editor
};
//# sourceMappingURL=index.js.map
