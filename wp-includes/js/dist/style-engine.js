/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "compileCSS": function() { return /* binding */ compileCSS; },
  "getCSSRules": function() { return /* binding */ getCSSRules; }
});

;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/constants.js
const VARIABLE_REFERENCE_PREFIX = 'var:';
const VARIABLE_PATH_SEPARATOR_TOKEN_ATTRIBUTE = '|';
const VARIABLE_PATH_SEPARATOR_TOKEN_STYLE = '--';

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/utils.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Returns a JSON representation of the generated CSS rules.
 *
 * @param  style   Style object.
 * @param  options Options object with settings to adjust how the styles are generated.
 * @param  path    An array of strings representing the path to the style value in the style object.
 * @param  ruleKey A CSS property key.
 *
 * @return GeneratedCSSRule[] CSS rules.
 */

function generateRule(style, options, path, ruleKey) {
  const styleValue = (0,external_lodash_namespaceObject.get)(style, path);
  return styleValue ? [{
    selector: options === null || options === void 0 ? void 0 : options.selector,
    key: ruleKey,
    value: getCSSVarFromStyleValue(styleValue)
  }] : [];
}
/**
 * Returns a JSON representation of the generated CSS rules taking into account box model properties, top, right, bottom, left.
 *
 * @param  style                Style object.
 * @param  options              Options object with settings to adjust how the styles are generated.
 * @param  path                 An array of strings representing the path to the style value in the style object.
 * @param  ruleKeys             An array of CSS property keys and patterns.
 * @param  individualProperties The "sides" or individual properties for which to generate rules.
 *
 * @return GeneratedCSSRule[]  CSS rules.
 */

function generateBoxRules(style, options, path, ruleKeys) {
  let individualProperties = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : ['top', 'right', 'bottom', 'left'];
  const boxStyle = (0,external_lodash_namespaceObject.get)(style, path);

  if (!boxStyle) {
    return [];
  }

  const rules = [];

  if (typeof boxStyle === 'string') {
    rules.push({
      selector: options === null || options === void 0 ? void 0 : options.selector,
      key: ruleKeys.default,
      value: boxStyle
    });
  } else {
    const sideRules = individualProperties.reduce((acc, side) => {
      const value = getCSSVarFromStyleValue((0,external_lodash_namespaceObject.get)(boxStyle, [side]));

      if (value) {
        acc.push({
          selector: options === null || options === void 0 ? void 0 : options.selector,
          key: ruleKeys === null || ruleKeys === void 0 ? void 0 : ruleKeys.individual.replace('%s', upperFirst(side)),
          value
        });
      }

      return acc;
    }, []);
    rules.push(...sideRules);
  }

  return rules;
}
/**
 * Returns a CSS var value from incoming style value following the pattern `var:description|context|slug`.
 *
 * @param  styleValue A raw style value.
 *
 * @return string A CSS var value.
 */

function getCSSVarFromStyleValue(styleValue) {
  if (typeof styleValue === 'string' && styleValue.startsWith(VARIABLE_REFERENCE_PREFIX)) {
    const variable = styleValue.slice(VARIABLE_REFERENCE_PREFIX.length).split(VARIABLE_PATH_SEPARATOR_TOKEN_ATTRIBUTE).map(presetVariable => (0,external_lodash_namespaceObject.kebabCase)(presetVariable)).join(VARIABLE_PATH_SEPARATOR_TOKEN_STYLE);
    return `var(--wp--${variable})`;
  }

  return styleValue;
}
/**
 * Capitalizes the first letter in a string.
 *
 * @param {string} str The string whose first letter the function will capitalize.
 *
 * @return string A CSS var value.
 */

function upperFirst(_ref) {
  let [firstLetter, ...rest] = _ref;
  return firstLetter.toUpperCase() + rest.join('');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/border/index.js
/**
 * Internal dependencies
 */

const color = {
  name: 'color',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['border', 'color'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'borderColor';
    return generateRule(style, options, path, ruleKey);
  }
};
const radius = {
  name: 'radius',
  generate: (style, options) => {
    return generateBoxRules(style, options, ['border', 'radius'], {
      default: 'borderRadius',
      individual: 'border%sRadius'
    }, ['topLeft', 'topRight', 'bottomLeft', 'bottomRight']);
  }
};
const borderStyle = {
  name: 'style',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['border', 'style'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'borderStyle';
    return generateRule(style, options, path, ruleKey);
  }
};
const width = {
  name: 'width',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['border', 'width'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'borderWidth';
    return generateRule(style, options, path, ruleKey);
  }
};
const borderDefinitionsWithIndividualStyles = [color, borderStyle, width];
/**
 * Returns a curried generator function with the individual border property ('top' | 'right' | 'bottom' | 'left') baked in.
 *
 * @param  individualProperty Individual border property ('top' | 'right' | 'bottom' | 'left').
 *
 * @return StyleDefinition[ 'generate' ]
 */

const createBorderGenerateFunction = individualProperty => (style, options) => {
  var _style$border;

  const styleValue = style === null || style === void 0 ? void 0 : (_style$border = style.border) === null || _style$border === void 0 ? void 0 : _style$border[individualProperty];

  if (!styleValue) {
    return [];
  }

  return borderDefinitionsWithIndividualStyles.reduce((acc, borderDefinition) => {
    const key = borderDefinition.name;

    if (styleValue.hasOwnProperty(key) && typeof borderDefinition.generate === 'function') {
      const ruleKey = `border${upperFirst(individualProperty)}${upperFirst(key)}`;
      acc.push(...borderDefinition.generate(style, options, ['border', individualProperty, key], ruleKey));
    }

    return acc;
  }, []);
};

const borderTop = {
  name: 'borderTop',
  generate: createBorderGenerateFunction('top')
};
const borderRight = {
  name: 'borderRight',
  generate: createBorderGenerateFunction('right')
};
const borderBottom = {
  name: 'borderBottom',
  generate: createBorderGenerateFunction('bottom')
};
const borderLeft = {
  name: 'borderLeft',
  generate: createBorderGenerateFunction('left')
};
/* harmony default export */ var border = ([...borderDefinitionsWithIndividualStyles, radius, borderTop, borderRight, borderBottom, borderLeft]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/color/background.js
/**
 * Internal dependencies
 */

const background = {
  name: 'background',
  generate: (style, options) => {
    return generateRule(style, options, ['color', 'background'], 'backgroundColor');
  }
};
/* harmony default export */ var color_background = (background);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/color/gradient.js
/**
 * Internal dependencies
 */

const gradient = {
  name: 'gradient',
  generate: (style, options) => {
    return generateRule(style, options, ['color', 'gradient'], 'background');
  }
};
/* harmony default export */ var color_gradient = (gradient);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/color/text.js
/**
 * Internal dependencies
 */

const text_text = {
  name: 'text',
  generate: (style, options) => {
    return generateRule(style, options, ['color', 'text'], 'color');
  }
};
/* harmony default export */ var color_text = (text_text);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/color/index.js
/**
 * Internal dependencies
 */



/* harmony default export */ var styles_color = ([color_text, color_gradient, color_background]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/shadow/index.js
/**
 * Internal dependencies
 */

const shadow = {
  name: 'shadow',
  generate: (style, options) => {
    return generateRule(style, options, ['shadow'], 'boxShadow');
  }
};
/* harmony default export */ var styles_shadow = ([shadow]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/outline/index.js
/**
 * Internal dependencies
 */

const outline_color = {
  name: 'color',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['outline', 'color'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'outlineColor';
    return generateRule(style, options, path, ruleKey);
  }
};
const offset = {
  name: 'offset',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['outline', 'offset'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'outlineOffset';
    return generateRule(style, options, path, ruleKey);
  }
};
const outlineStyle = {
  name: 'style',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['outline', 'style'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'outlineStyle';
    return generateRule(style, options, path, ruleKey);
  }
};
const outline_width = {
  name: 'width',
  generate: function (style, options) {
    let path = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : ['outline', 'width'];
    let ruleKey = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'outlineWidth';
    return generateRule(style, options, path, ruleKey);
  }
};
/* harmony default export */ var outline = ([outline_color, outlineStyle, offset, outline_width]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/spacing/padding.js
/**
 * Internal dependencies
 */

const padding = {
  name: 'padding',
  generate: (style, options) => {
    return generateBoxRules(style, options, ['spacing', 'padding'], {
      default: 'padding',
      individual: 'padding%s'
    });
  }
};
/* harmony default export */ var spacing_padding = (padding);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/spacing/margin.js
/**
 * Internal dependencies
 */

const margin = {
  name: 'margin',
  generate: (style, options) => {
    return generateBoxRules(style, options, ['spacing', 'margin'], {
      default: 'margin',
      individual: 'margin%s'
    });
  }
};
/* harmony default export */ var spacing_margin = (margin);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/spacing/index.js
/**
 * Internal dependencies
 */


/* harmony default export */ var spacing = ([spacing_margin, spacing_padding]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/typography/index.js
/**
 * Internal dependencies
 */

const fontSize = {
  name: 'fontSize',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'fontSize'], 'fontSize');
  }
};
const fontStyle = {
  name: 'fontStyle',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'fontStyle'], 'fontStyle');
  }
};
const fontWeight = {
  name: 'fontWeight',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'fontWeight'], 'fontWeight');
  }
};
const fontFamily = {
  name: 'fontFamily',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'fontFamily'], 'fontFamily');
  }
};
const letterSpacing = {
  name: 'letterSpacing',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'letterSpacing'], 'letterSpacing');
  }
};
const lineHeight = {
  name: 'letterSpacing',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'lineHeight'], 'lineHeight');
  }
};
const textDecoration = {
  name: 'textDecoration',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'textDecoration'], 'textDecoration');
  }
};
const textTransform = {
  name: 'textTransform',
  generate: (style, options) => {
    return generateRule(style, options, ['typography', 'textTransform'], 'textTransform');
  }
};
/* harmony default export */ var typography = ([fontFamily, fontSize, fontStyle, fontWeight, letterSpacing, lineHeight, textDecoration, textTransform]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/styles/index.js
/**
 * Internal dependencies
 */






const styleDefinitions = [...border, ...styles_color, ...outline, ...spacing, ...typography, ...styles_shadow];

;// CONCATENATED MODULE: ./node_modules/@wordpress/style-engine/build-module/index.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Generates a stylesheet for a given style object and selector.
 *
 * @since 6.1.0 Introduced in WordPress core.
 *
 * @param  style   Style object, for example, the value of a block's attributes.style object or the top level styles in theme.json
 * @param  options Options object with settings to adjust how the styles are generated.
 *
 * @return A generated stylesheet or inline style declarations.
 */

function compileCSS(style) {
  let options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  const rules = getCSSRules(style, options); // If no selector is provided, treat generated rules as inline styles to be returned as a single string.

  if (!(options !== null && options !== void 0 && options.selector)) {
    const inlineRules = [];
    rules.forEach(rule => {
      inlineRules.push(`${(0,external_lodash_namespaceObject.kebabCase)(rule.key)}: ${rule.value};`);
    });
    return inlineRules.join(' ');
  }

  const groupedRules = (0,external_lodash_namespaceObject.groupBy)(rules, 'selector');
  const selectorRules = Object.keys(groupedRules).reduce((acc, subSelector) => {
    acc.push(`${subSelector} { ${groupedRules[subSelector].map(rule => `${(0,external_lodash_namespaceObject.kebabCase)(rule.key)}: ${rule.value};`).join(' ')} }`);
    return acc;
  }, []);
  return selectorRules.join('\n');
}
/**
 * Returns a JSON representation of the generated CSS rules.
 *
 * @since 6.1.0 Introduced in WordPress core.
 *
 * @param  style   Style object, for example, the value of a block's attributes.style object or the top level styles in theme.json
 * @param  options Options object with settings to adjust how the styles are generated.
 *
 * @return A collection of objects containing the selector, if any, the CSS property key (camelcase) and parsed CSS value.
 */

function getCSSRules(style) {
  let options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
  const rules = [];
  styleDefinitions.forEach(definition => {
    if (typeof definition.generate === 'function') {
      rules.push(...definition.generate(style, options));
    }
  });
  return rules;
}

(window.wp = window.wp || {}).styleEngine = __webpack_exports__;
/******/ })()
;