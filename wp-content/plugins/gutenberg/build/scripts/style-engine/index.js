"use strict";
var wp;
(wp ||= {}).styleEngine = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // packages/style-engine/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    compileCSS: () => compileCSS,
    getCSSRules: () => getCSSRules,
    getCSSValueFromRawStyle: () => getCSSValueFromRawStyle
  });

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

  // packages/style-engine/build-module/styles/constants.js
  var VARIABLE_REFERENCE_PREFIX = "var:";
  var VARIABLE_PATH_SEPARATOR_TOKEN_ATTRIBUTE = "|";
  var VARIABLE_PATH_SEPARATOR_TOKEN_STYLE = "--";

  // packages/style-engine/build-module/styles/utils.js
  var getStyleValueByPath = (object, path) => {
    let value = object;
    path.forEach((fieldName) => {
      value = value?.[fieldName];
    });
    return value;
  };
  function generateRule(style, options, path, ruleKey) {
    const styleValue = getStyleValueByPath(style, path);
    return styleValue ? [
      {
        selector: options?.selector,
        key: ruleKey,
        value: getCSSValueFromRawStyle(styleValue)
      }
    ] : [];
  }
  function generateBoxRules(style, options, path, ruleKeys, individualProperties = ["top", "right", "bottom", "left"]) {
    const boxStyle = getStyleValueByPath(
      style,
      path
    );
    if (!boxStyle) {
      return [];
    }
    const rules = [];
    if (typeof boxStyle === "string") {
      rules.push({
        selector: options?.selector,
        key: ruleKeys.default,
        value: getCSSValueFromRawStyle(boxStyle)
      });
    } else {
      const sideRules = individualProperties.reduce(
        (acc, side) => {
          const value = getCSSValueFromRawStyle(
            getStyleValueByPath(boxStyle, [side])
          );
          if (value) {
            acc.push({
              selector: options?.selector,
              key: ruleKeys?.individual.replace(
                "%s",
                upperFirst(side)
              ),
              value
            });
          }
          return acc;
        },
        []
      );
      rules.push(...sideRules);
    }
    return rules;
  }
  function getCSSValueFromRawStyle(styleValue) {
    if (typeof styleValue === "string" && styleValue.startsWith(VARIABLE_REFERENCE_PREFIX)) {
      const variable = styleValue.slice(VARIABLE_REFERENCE_PREFIX.length).split(VARIABLE_PATH_SEPARATOR_TOKEN_ATTRIBUTE).map(
        (presetVariable) => paramCase(presetVariable, {
          splitRegexp: [
            /([a-z0-9])([A-Z])/g,
            // fooBar => foo-bar, 3Bar => 3-bar
            /([0-9])([a-z])/g,
            // 3bar => 3-bar
            /([A-Za-z])([0-9])/g,
            // Foo3 => foo-3, foo3 => foo-3
            /([A-Z])([A-Z][a-z])/g
            // FOOBar => foo-bar
          ]
        })
      ).join(VARIABLE_PATH_SEPARATOR_TOKEN_STYLE);
      return `var(--wp--${variable})`;
    }
    return styleValue;
  }
  function upperFirst(string) {
    const [firstLetter, ...rest] = string;
    return firstLetter.toUpperCase() + rest.join("");
  }
  function camelCaseJoin(strings) {
    const [firstItem, ...rest] = strings;
    return firstItem.toLowerCase() + rest.map(upperFirst).join("");
  }
  function safeDecodeURI(uri) {
    try {
      return decodeURI(uri);
    } catch (uriError) {
      return uri;
    }
  }

  // packages/style-engine/build-module/styles/border/index.js
  function createBorderGenerateFunction(path) {
    return (style, options) => generateRule(style, options, path, camelCaseJoin(path));
  }
  function createBorderEdgeGenerateFunction(edge) {
    return (style, options) => {
      return ["color", "style", "width"].flatMap((key) => {
        const path = ["border", edge, key];
        return createBorderGenerateFunction(path)(style, options);
      });
    };
  }
  var color = {
    name: "color",
    generate: createBorderGenerateFunction(["border", "color"])
  };
  var radius = {
    name: "radius",
    generate: (style, options) => {
      return generateBoxRules(
        style,
        options,
        ["border", "radius"],
        {
          default: "borderRadius",
          individual: "border%sRadius"
        },
        ["topLeft", "topRight", "bottomLeft", "bottomRight"]
      );
    }
  };
  var borderStyle = {
    name: "style",
    generate: createBorderGenerateFunction(["border", "style"])
  };
  var width = {
    name: "width",
    generate: createBorderGenerateFunction(["border", "width"])
  };
  var borderTop = {
    name: "borderTop",
    generate: createBorderEdgeGenerateFunction("top")
  };
  var borderRight = {
    name: "borderRight",
    generate: createBorderEdgeGenerateFunction("right")
  };
  var borderBottom = {
    name: "borderBottom",
    generate: createBorderEdgeGenerateFunction("bottom")
  };
  var borderLeft = {
    name: "borderLeft",
    generate: createBorderEdgeGenerateFunction("left")
  };
  var border_default = [
    color,
    borderStyle,
    width,
    radius,
    borderTop,
    borderRight,
    borderBottom,
    borderLeft
  ];

  // packages/style-engine/build-module/styles/color/background.js
  var background = {
    name: "background",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["color", "background"],
        "backgroundColor"
      );
    }
  };
  var background_default = background;

  // packages/style-engine/build-module/styles/color/gradient.js
  var gradient = {
    name: "gradient",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["color", "gradient"],
        "background"
      );
    }
  };
  var gradient_default = gradient;

  // packages/style-engine/build-module/styles/color/text.js
  var text = {
    name: "text",
    generate: (style, options) => {
      return generateRule(style, options, ["color", "text"], "color");
    }
  };
  var text_default = text;

  // packages/style-engine/build-module/styles/color/index.js
  var color_default = [text_default, gradient_default, background_default];

  // packages/style-engine/build-module/styles/dimensions/index.js
  var minHeight = {
    name: "minHeight",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["dimensions", "minHeight"],
        "minHeight"
      );
    }
  };
  var aspectRatio = {
    name: "aspectRatio",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["dimensions", "aspectRatio"],
        "aspectRatio"
      );
    }
  };
  var dimensions_default = [minHeight, aspectRatio];

  // packages/style-engine/build-module/styles/background/index.js
  var backgroundImage = {
    name: "backgroundImage",
    generate: (style, options) => {
      const _backgroundImage = style?.background?.backgroundImage;
      if (typeof _backgroundImage === "object" && _backgroundImage?.url) {
        return [
          {
            selector: options.selector,
            key: "backgroundImage",
            // Passed `url` may already be encoded. To prevent double encoding, decodeURI is executed to revert to the original string.
            value: `url( '${encodeURI(
              safeDecodeURI(_backgroundImage.url)
            )}' )`
          }
        ];
      }
      return generateRule(
        style,
        options,
        ["background", "backgroundImage"],
        "backgroundImage"
      );
    }
  };
  var backgroundPosition = {
    name: "backgroundPosition",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["background", "backgroundPosition"],
        "backgroundPosition"
      );
    }
  };
  var backgroundRepeat = {
    name: "backgroundRepeat",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["background", "backgroundRepeat"],
        "backgroundRepeat"
      );
    }
  };
  var backgroundSize = {
    name: "backgroundSize",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["background", "backgroundSize"],
        "backgroundSize"
      );
    }
  };
  var backgroundAttachment = {
    name: "backgroundAttachment",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["background", "backgroundAttachment"],
        "backgroundAttachment"
      );
    }
  };
  var background_default2 = [
    backgroundImage,
    backgroundPosition,
    backgroundRepeat,
    backgroundSize,
    backgroundAttachment
  ];

  // packages/style-engine/build-module/styles/shadow/index.js
  var shadow = {
    name: "shadow",
    generate: (style, options) => {
      return generateRule(style, options, ["shadow"], "boxShadow");
    }
  };
  var shadow_default = [shadow];

  // packages/style-engine/build-module/styles/outline/index.js
  var color2 = {
    name: "color",
    generate: (style, options, path = ["outline", "color"], ruleKey = "outlineColor") => {
      return generateRule(style, options, path, ruleKey);
    }
  };
  var offset = {
    name: "offset",
    generate: (style, options, path = ["outline", "offset"], ruleKey = "outlineOffset") => {
      return generateRule(style, options, path, ruleKey);
    }
  };
  var outlineStyle = {
    name: "style",
    generate: (style, options, path = ["outline", "style"], ruleKey = "outlineStyle") => {
      return generateRule(style, options, path, ruleKey);
    }
  };
  var width2 = {
    name: "width",
    generate: (style, options, path = ["outline", "width"], ruleKey = "outlineWidth") => {
      return generateRule(style, options, path, ruleKey);
    }
  };
  var outline_default = [color2, outlineStyle, offset, width2];

  // packages/style-engine/build-module/styles/spacing/padding.js
  var padding = {
    name: "padding",
    generate: (style, options) => {
      return generateBoxRules(style, options, ["spacing", "padding"], {
        default: "padding",
        individual: "padding%s"
      });
    }
  };
  var padding_default = padding;

  // packages/style-engine/build-module/styles/spacing/margin.js
  var margin = {
    name: "margin",
    generate: (style, options) => {
      return generateBoxRules(style, options, ["spacing", "margin"], {
        default: "margin",
        individual: "margin%s"
      });
    }
  };
  var margin_default = margin;

  // packages/style-engine/build-module/styles/spacing/index.js
  var spacing_default = [margin_default, padding_default];

  // packages/style-engine/build-module/styles/typography/index.js
  var fontSize = {
    name: "fontSize",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "fontSize"],
        "fontSize"
      );
    }
  };
  var fontStyle = {
    name: "fontStyle",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "fontStyle"],
        "fontStyle"
      );
    }
  };
  var fontWeight = {
    name: "fontWeight",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "fontWeight"],
        "fontWeight"
      );
    }
  };
  var fontFamily = {
    name: "fontFamily",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "fontFamily"],
        "fontFamily"
      );
    }
  };
  var letterSpacing = {
    name: "letterSpacing",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "letterSpacing"],
        "letterSpacing"
      );
    }
  };
  var lineHeight = {
    name: "lineHeight",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "lineHeight"],
        "lineHeight"
      );
    }
  };
  var textColumns = {
    name: "textColumns",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "textColumns"],
        "columnCount"
      );
    }
  };
  var textDecoration = {
    name: "textDecoration",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "textDecoration"],
        "textDecoration"
      );
    }
  };
  var textTransform = {
    name: "textTransform",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "textTransform"],
        "textTransform"
      );
    }
  };
  var writingMode = {
    name: "writingMode",
    generate: (style, options) => {
      return generateRule(
        style,
        options,
        ["typography", "writingMode"],
        "writingMode"
      );
    }
  };
  var typography_default = [
    fontFamily,
    fontSize,
    fontStyle,
    fontWeight,
    letterSpacing,
    lineHeight,
    textColumns,
    textDecoration,
    textTransform,
    writingMode
  ];

  // packages/style-engine/build-module/styles/index.js
  var styleDefinitions = [
    ...border_default,
    ...color_default,
    ...dimensions_default,
    ...outline_default,
    ...spacing_default,
    ...typography_default,
    ...shadow_default,
    ...background_default2
  ];

  // packages/style-engine/build-module/index.js
  function compileCSS(style, options = {}) {
    const rules = getCSSRules(style, options);
    if (!options?.selector) {
      const inlineRules = [];
      rules.forEach((rule) => {
        inlineRules.push(`${paramCase(rule.key)}: ${rule.value};`);
      });
      return inlineRules.join(" ");
    }
    const groupedRules = rules.reduce(
      (acc, rule) => {
        const { selector } = rule;
        if (!selector) {
          return acc;
        }
        if (!acc[selector]) {
          acc[selector] = [];
        }
        acc[selector].push(rule);
        return acc;
      },
      {}
    );
    const selectorRules = Object.keys(groupedRules).reduce(
      (acc, subSelector) => {
        acc.push(
          `${subSelector} { ${groupedRules[subSelector].map(
            (rule) => `${paramCase(rule.key)}: ${rule.value};`
          ).join(" ")} }`
        );
        return acc;
      },
      []
    );
    return selectorRules.join("\n");
  }
  function getCSSRules(style, options = {}) {
    const rules = [];
    styleDefinitions.forEach((definition) => {
      if (typeof definition.generate === "function") {
        rules.push(...definition.generate(style, options));
      }
    });
    return rules;
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
