<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

;// CONCATENATED MODULE: external ["wp","richText"]
const external_wp_richText_namespaceObject = window["wp"]["richText"];
;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-bold.js


/**
 * WordPress dependencies
 */

const formatBold = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M14.7 11.3c1-.6 1.5-1.6 1.5-3 0-2.3-1.3-3.4-4-3.4H7v14h5.8c1.4 0 2.5-.3 3.3-1 .8-.7 1.2-1.7 1.2-2.9.1-1.9-.8-3.1-2.6-3.7zm-5.1-4h2.3c.6 0 1.1.1 1.4.4.3.3.5.7.5 1.2s-.2 1-.5 1.2c-.3.3-.8.4-1.4.4H9.6V7.3zm4.6 9c-.4.3-1 .4-1.7.4H9.6v-3.9h2.9c.7 0 1.3.2 1.7.5.4.3.6.8.6 1.5s-.2 1.2-.6 1.5z"
}));
/* harmony default export */ const format_bold = (formatBold);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/bold/index.js
=======
this["wp"] = this["wp"] || {}; this["wp"]["formatLibrary"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "t1DA");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1CF3":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["dom"]; }());

/***/ }),

/***/ "1OyB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "Ff2n":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _objectWithoutProperties; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "JX7q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "Ji7U":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _inherits; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

/***/ }),

/***/ "Mmq9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["url"]; }());

/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["keycodes"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "U8pU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "foSv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ "jSdM":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["editor"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "md7G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("U8pU");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("JX7q");


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ "qRz9":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),

/***/ "t1DA":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__("qRz9");

// EXTERNAL MODULE: external {"this":["wp","editor"]}
var external_this_wp_editor_ = __webpack_require__("jSdM");

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/bold/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD
const bold_name = 'core/bold';

const title = (0,external_wp_i18n_namespaceObject.__)('Bold');

const bold = {
  name: bold_name,
  title,
  tagName: 'strong',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onToggle() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: bold_name,
        title
      }));
    }

    function onClick() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: bold_name
      }));
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
      type: "primary",
      character: "b",
      onUse: onToggle
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      name: "bold",
      icon: format_bold,
      title: title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "b"
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableRichTextInputEvent, {
      inputType: "formatBold",
      onInput: onToggle
    }));
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/code.js


/**
 * WordPress dependencies
 */

const code = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
}));
/* harmony default export */ const library_code = (code);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/code/index.js
=======
var bold_name = 'core/bold';
var bold = {
  name: bold_name,
  title: Object(external_this_wp_i18n_["__"])('Bold'),
  tagName: 'strong',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange;

    var onToggle = function onToggle() {
      return onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: bold_name
      }));
    };

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
      type: "primary",
      character: "b",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextToolbarButton"], {
      name: "bold",
      icon: "editor-bold",
      title: Object(external_this_wp_i18n_["__"])('Bold'),
      onClick: onToggle,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "b"
    }));
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/code/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */



<<<<<<< HEAD

const code_name = 'core/code';

const code_title = (0,external_wp_i18n_namespaceObject.__)('Inline code');

const code_code = {
  name: code_name,
  title: code_title,
  tagName: 'code',
  className: null,

  __unstableInputRule(value) {
    const BACKTICK = '`';
    const {
      start,
      text
    } = value;
    const characterBefore = text[start - 1]; // Quick check the text for the necessary character.

    if (characterBefore !== BACKTICK) {
      return value;
    }

    if (start - 2 < 0) {
      return value;
    }

    const indexBefore = text.lastIndexOf(BACKTICK, start - 2);

    if (indexBefore === -1) {
      return value;
    }

    const startIndex = indexBefore;
    const endIndex = start - 2;

    if (startIndex === endIndex) {
      return value;
    }

    value = (0,external_wp_richText_namespaceObject.remove)(value, startIndex, startIndex + 1);
    value = (0,external_wp_richText_namespaceObject.remove)(value, endIndex, endIndex + 1);
    value = (0,external_wp_richText_namespaceObject.applyFormat)(value, {
      type: code_name
    }, startIndex, endIndex);
    return value;
  },

  edit({
    value,
    onChange,
    onFocus,
    isActive
  }) {
    function onClick() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: code_name,
        title: code_title
      }));
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
      type: "access",
      character: "x",
      onUse: onClick
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      icon: library_code,
      title: code_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    }));
  }

};

;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/keyboard-return.js


/**
 * WordPress dependencies
 */

const keyboardReturn = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "-2 -2 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M6.734 16.106l2.176-2.38-1.093-1.028-3.846 4.158 3.846 4.157 1.093-1.027-2.176-2.38h2.811c1.125 0 2.25.03 3.374 0 1.428-.001 3.362-.25 4.963-1.277 1.66-1.065 2.868-2.906 2.868-5.859 0-2.479-1.327-4.896-3.65-5.93-1.82-.813-3.044-.8-4.806-.788l-.567.002v1.5c.184 0 .368 0 .553-.002 1.82-.007 2.704-.014 4.21.657 1.854.827 2.76 2.657 2.76 4.561 0 2.472-.973 3.824-2.178 4.596-1.258.807-2.864 1.04-4.163 1.04h-.02c-1.115.03-2.229 0-3.344 0H6.734z"
}));
/* harmony default export */ const keyboard_return = (keyboardReturn);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/image/index.js
=======
var code_name = 'core/code';
var code = {
  name: code_name,
  title: Object(external_this_wp_i18n_["__"])('Code'),
  tagName: 'code',
  className: null,
  edit: function edit(_ref) {
    var value = _ref.value,
        onChange = _ref.onChange;

    var onToggle = function onToggle() {
      return onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: code_name
      }));
    };

    return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
      type: "access",
      character: "x",
      onUse: onToggle
    });
  }
};

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__("1OyB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__("vuIU");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__("md7G");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__("foSv");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__("Ji7U");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__("JX7q");

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__("tI+e");

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/image/index.js






>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */





<<<<<<< HEAD

const ALLOWED_MEDIA_TYPES = ['image'];
const image_name = 'core/image';

const image_title = (0,external_wp_i18n_namespaceObject.__)('Inline image');

const image_image = {
  name: image_name,
  title: image_title,
  keywords: [(0,external_wp_i18n_namespaceObject.__)('photo'), (0,external_wp_i18n_namespaceObject.__)('media')],
=======
var ALLOWED_MEDIA_TYPES = ['image'];
var image_name = 'core/image';
var image_image = {
  name: image_name,
  title: Object(external_this_wp_i18n_["__"])('Image'),
  keywords: [Object(external_this_wp_i18n_["__"])('photo'), Object(external_this_wp_i18n_["__"])('media')],
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  object: true,
  tagName: 'img',
  className: null,
  attributes: {
    className: 'class',
    style: 'style',
    url: 'src',
    alt: 'alt'
  },
<<<<<<< HEAD
  edit: Edit
};

function InlineUI({
  value,
  onChange,
  activeObjectAttributes,
  contentRef
}) {
  const {
    style
  } = activeObjectAttributes;
  const [width, setWidth] = (0,external_wp_element_namespaceObject.useState)(style?.replace(/\D/g, ''));
  const popoverAnchor = (0,external_wp_richText_namespaceObject.useAnchor)({
    editableContentElement: contentRef.current,
    settings: image_image
  });
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover, {
    placement: "bottom",
    focusOnMount: false,
    anchor: popoverAnchor,
    className: "block-editor-format-toolbar__image-popover"
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    className: "block-editor-format-toolbar__image-container-content",
    onSubmit: event => {
      const newReplacements = value.replacements.slice();
      newReplacements[value.start] = {
        type: image_name,
        attributes: { ...activeObjectAttributes,
          style: width ? `width: ${width}px;` : ''
        }
      };
      onChange({ ...value,
        replacements: newReplacements
      });
      event.preventDefault();
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "bottom",
    spacing: "0"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalNumberControl, {
    className: "block-editor-format-toolbar__image-container-value",
    label: (0,external_wp_i18n_namespaceObject.__)('Width'),
    value: width,
    min: 1,
    onChange: newWidth => setWidth(newWidth)
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "block-editor-format-toolbar__image-container-button",
    icon: keyboard_return,
    label: (0,external_wp_i18n_namespaceObject.__)('Apply'),
    type: "submit"
  }))));
}

function Edit({
  value,
  onChange,
  onFocus,
  isObjectActive,
  activeObjectAttributes,
  contentRef
}) {
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);

  function openModal() {
    setIsModalOpen(true);
  }

  function closeModal() {
    setIsModalOpen(false);
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.MediaUploadCheck, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
    icon: (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SVG, {
      xmlns: "http://www.w3.org/2000/svg",
      viewBox: "0 0 24 24"
    }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Path, {
      d: "M4 18.5h16V17H4v1.5zM16 13v1.5h4V13h-4zM5.1 15h7.8c.6 0 1.1-.5 1.1-1.1V6.1c0-.6-.5-1.1-1.1-1.1H5.1C4.5 5 4 5.5 4 6.1v7.8c0 .6.5 1.1 1.1 1.1zm.4-8.5h7V10l-1-1c-.3-.3-.8-.3-1 0l-1.6 1.5-1.2-.7c-.3-.2-.6-.2-.9 0l-1.3 1V6.5zm0 6.1l1.8-1.3 1.3.8c.3.2.7.2.9-.1l1.5-1.4 1.5 1.4v1.5h-7v-.9z"
    })),
    title: image_title,
    onClick: openModal,
    isActive: isObjectActive
  }), isModalOpen && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.MediaUpload, {
    allowedTypes: ALLOWED_MEDIA_TYPES,
    onSelect: ({
      id,
      url,
      alt,
      width: imgWidth
    }) => {
      closeModal();
      onChange((0,external_wp_richText_namespaceObject.insertObject)(value, {
        type: image_name,
        attributes: {
          className: `wp-image-${id}`,
          style: `width: ${Math.min(imgWidth, 150)}px;`,
          url,
          alt
        }
      }));
      onFocus();
    },
    onClose: closeModal,
    render: ({
      open
    }) => {
      open();
      return null;
    }
  }), isObjectActive && (0,external_wp_element_namespaceObject.createElement)(InlineUI, {
    value: value,
    onChange: onChange,
    activeObjectAttributes: activeObjectAttributes,
    contentRef: contentRef
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-italic.js


/**
 * WordPress dependencies
 */

const formatItalic = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12.5 5L10 19h1.9l2.5-14z"
}));
/* harmony default export */ const format_italic = (formatItalic);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/italic/index.js
=======
  edit:
  /*#__PURE__*/
  function (_Component) {
    Object(inherits["a" /* default */])(ImageEdit, _Component);

    function ImageEdit() {
      var _this;

      Object(classCallCheck["a" /* default */])(this, ImageEdit);

      _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ImageEdit).apply(this, arguments));
      _this.openModal = _this.openModal.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
      _this.closeModal = _this.closeModal.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
      _this.state = {
        modal: false
      };
      return _this;
    }

    Object(createClass["a" /* default */])(ImageEdit, [{
      key: "openModal",
      value: function openModal() {
        this.setState({
          modal: true
        });
      }
    }, {
      key: "closeModal",
      value: function closeModal() {
        this.setState({
          modal: false
        });
      }
    }, {
      key: "render",
      value: function render() {
        var _this2 = this;

        var _this$props = this.props,
            value = _this$props.value,
            onChange = _this$props.onChange;
        return Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MediaUploadCheck"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextInserterItem"], {
          name: image_name,
          icon: Object(external_this_wp_element_["createElement"])(external_this_wp_components_["SVG"], {
            xmlns: "http://www.w3.org/2000/svg",
            viewBox: "0 0 24 24"
          }, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Path"], {
            d: "M4 16h10c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v9c0 1.1.9 2 2 2zM4 5h10v9H4V5zm14 9v2h4v-2h-4zM2 20h20v-2H2v2zm6.4-8.8L7 9.4 5 12h8l-2.6-3.4-2 2.6z"
          })),
          title: Object(external_this_wp_i18n_["__"])('Inline Image'),
          onClick: this.openModal
        }), this.state.modal && Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["MediaUpload"], {
          allowedTypes: ALLOWED_MEDIA_TYPES,
          onSelect: function onSelect(_ref) {
            var id = _ref.id,
                url = _ref.url,
                alt = _ref.alt,
                width = _ref.width;

            _this2.closeModal();

            onChange(Object(external_this_wp_richText_["insertObject"])(value, {
              type: image_name,
              attributes: {
                className: "wp-image-".concat(id),
                style: "width: ".concat(Math.min(width, 150), "px;"),
                url: url,
                alt: alt
              }
            }));
          },
          onClose: this.closeModal,
          render: function render(_ref2) {
            var open = _ref2.open;
            open();
            return null;
          }
        }));
      }
    }]);

    return ImageEdit;
  }(external_this_wp_element_["Component"])
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/italic/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */




<<<<<<< HEAD
const italic_name = 'core/italic';

const italic_title = (0,external_wp_i18n_namespaceObject.__)('Italic');

const italic = {
  name: italic_name,
  title: italic_title,
  tagName: 'em',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onToggle() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: italic_name,
        title: italic_title
      }));
    }

    function onClick() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: italic_name
      }));
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
      type: "primary",
      character: "i",
      onUse: onToggle
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      name: "italic",
      icon: format_italic,
      title: italic_title,
      onClick: onClick,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "i"
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableRichTextInputEvent, {
      inputType: "formatItalic",
      onInput: onToggle
    }));
  }

};

;// CONCATENATED MODULE: external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// CONCATENATED MODULE: external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/link-off.js
=======
var italic_name = 'core/italic';
var italic = {
  name: italic_name,
  title: Object(external_this_wp_i18n_["__"])('Italic'),
  tagName: 'em',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange;

    var onToggle = function onToggle() {
      return onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: italic_name
      }));
    };

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
      type: "primary",
      character: "i",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextToolbarButton"], {
      name: "italic",
      icon: "editor-italic",
      title: Object(external_this_wp_i18n_["__"])('Italic'),
      onClick: onToggle,
      isActive: isActive,
      shortcutType: "primary",
      shortcutCharacter: "i"
    }));
  }
};

// EXTERNAL MODULE: external {"this":["wp","url"]}
var external_this_wp_url_ = __webpack_require__("Mmq9");

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external {"this":["wp","keycodes"]}
var external_this_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: external {"this":["wp","dom"]}
var external_this_wp_dom_ = __webpack_require__("1CF3");

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/positioned-at-selection.js





>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const linkOff = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.031 4.703 15.576 4l-1.56 3H14v.03l-2.324 4.47H9.5V13h1.396l-1.502 2.889h-.95a3.694 3.694 0 0 1 0-7.389H10V7H8.444a5.194 5.194 0 1 0 0 10.389h.17L7.5 19.53l1.416.719L15.049 8.5h.507a3.694 3.694 0 0 1 0 7.39H14v1.5h1.556a5.194 5.194 0 0 0 .273-10.383l1.202-2.304Z"
}));
/* harmony default export */ const link_off = (linkOff);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/link.js


/**
 * WordPress dependencies
 */

const link_link = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M10 17.389H8.444A5.194 5.194 0 1 1 8.444 7H10v1.5H8.444a3.694 3.694 0 0 0 0 7.389H10v1.5ZM14 7h1.556a5.194 5.194 0 0 1 0 10.39H14v-1.5h1.556a3.694 3.694 0 0 0 0-7.39H14V7Zm-4.5 6h5v-1.5h-5V13Z"
}));
/* harmony default export */ const library_link = (link_link);

;// CONCATENATED MODULE: external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/utils.js
=======

/**
 * Returns a style object for applying as `position: absolute` for an element
 * relative to the bottom-center of the current selection. Includes `top` and
 * `left` style properties.
 *
 * @return {Object} Style object.
 */

function getCurrentCaretPositionStyle() {
  var selection = window.getSelection(); // Unlikely, but in the case there is no selection, return empty styles so
  // as to avoid a thrown error by `Selection#getRangeAt` on invalid index.

  if (selection.rangeCount === 0) {
    return {};
  } // Get position relative viewport.


  var rect = Object(external_this_wp_dom_["getRectangleFromRange"])(selection.getRangeAt(0));
  var top = rect.top + rect.height;
  var left = rect.left + rect.width / 2; // Offset by positioned parent, if one exists.

  var offsetParent = Object(external_this_wp_dom_["getOffsetParent"])(selection.anchorNode);

  if (offsetParent) {
    var parentRect = offsetParent.getBoundingClientRect();
    top -= parentRect.top;
    left -= parentRect.left;
  }

  return {
    top: top,
    left: left
  };
}
/**
 * Component which renders itself positioned under the current caret selection.
 * The position is calculated at the time of the component being mounted, so it
 * should only be mounted after the desired selection has been made.
 *
 * @type {WPComponent}
 */


var positioned_at_selection_PositionedAtSelection =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(PositionedAtSelection, _Component);

  function PositionedAtSelection() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, PositionedAtSelection);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(PositionedAtSelection).apply(this, arguments));
    _this.state = {
      style: getCurrentCaretPositionStyle()
    };
    return _this;
  }

  Object(createClass["a" /* default */])(PositionedAtSelection, [{
    key: "render",
    value: function render() {
      var children = this.props.children;
      var style = this.state.style;
      return Object(external_this_wp_element_["createElement"])("div", {
        className: "editor-format-toolbar__selection-position",
        style: style
      }, children);
    }
  }]);

  return PositionedAtSelection;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var positioned_at_selection = (positioned_at_selection_PositionedAtSelection);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/utils.js
/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Check for issues with the provided href.
 *
 * @param {string} href The href.
 *
 * @return {boolean} Is the href invalid?
 */

function isValidHref(href) {
  if (!href) {
    return false;
  }

<<<<<<< HEAD
  const trimmedHref = href.trim();
=======
  var trimmedHref = href.trim();
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (!trimmedHref) {
    return false;
  } // Does the href start with something that looks like a URL protocol?


  if (/^\S+:/.test(trimmedHref)) {
<<<<<<< HEAD
    const protocol = (0,external_wp_url_namespaceObject.getProtocol)(trimmedHref);

    if (!(0,external_wp_url_namespaceObject.isValidProtocol)(protocol)) {
=======
    var protocol = Object(external_this_wp_url_["getProtocol"])(trimmedHref);

    if (!Object(external_this_wp_url_["isValidProtocol"])(protocol)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      return false;
    } // Add some extra checks for http(s) URIs, since these are the most common use-case.
    // This ensures URIs with an http protocol have exactly two forward slashes following the protocol.


<<<<<<< HEAD
    if (protocol.startsWith('http') && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
      return false;
    }

    const authority = (0,external_wp_url_namespaceObject.getAuthority)(trimmedHref);

    if (!(0,external_wp_url_namespaceObject.isValidAuthority)(authority)) {
      return false;
    }

    const path = (0,external_wp_url_namespaceObject.getPath)(trimmedHref);

    if (path && !(0,external_wp_url_namespaceObject.isValidPath)(path)) {
      return false;
    }

    const queryString = (0,external_wp_url_namespaceObject.getQueryString)(trimmedHref);

    if (queryString && !(0,external_wp_url_namespaceObject.isValidQueryString)(queryString)) {
      return false;
    }

    const fragment = (0,external_wp_url_namespaceObject.getFragment)(trimmedHref);

    if (fragment && !(0,external_wp_url_namespaceObject.isValidFragment)(fragment)) {
=======
    if (Object(external_lodash_["startsWith"])(protocol, 'http') && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
      return false;
    }

    var authority = Object(external_this_wp_url_["getAuthority"])(trimmedHref);

    if (!Object(external_this_wp_url_["isValidAuthority"])(authority)) {
      return false;
    }

    var path = Object(external_this_wp_url_["getPath"])(trimmedHref);

    if (path && !Object(external_this_wp_url_["isValidPath"])(path)) {
      return false;
    }

    var queryString = Object(external_this_wp_url_["getQueryString"])(trimmedHref);

    if (queryString && !Object(external_this_wp_url_["isValidQueryString"])(queryString)) {
      return false;
    }

    var fragment = Object(external_this_wp_url_["getFragment"])(trimmedHref);

    if (fragment && !Object(external_this_wp_url_["isValidFragment"])(fragment)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      return false;
    }
  } // Validate anchor links.


<<<<<<< HEAD
  if (trimmedHref.startsWith('#') && !(0,external_wp_url_namespaceObject.isValidFragment)(trimmedHref)) {
=======
  if (Object(external_lodash_["startsWith"])(trimmedHref, '#') && !Object(external_this_wp_url_["isValidFragment"])(trimmedHref)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    return false;
  }

  return true;
}
<<<<<<< HEAD
/**
 * Generates the format object that will be applied to the link text.
 *
 * @param {Object}  options
 * @param {string}  options.url              The href of the link.
 * @param {string}  options.type             The type of the link.
 * @param {string}  options.id               The ID of the link.
 * @param {boolean} options.opensInNewWindow Whether this link will open in a new window.
 *
 * @return {Object} The final format object.
 */

function createLinkFormat({
  url,
  type,
  id,
  opensInNewWindow
}) {
  const format = {
    type: 'core/link',
    attributes: {
      url
    }
  };
  if (type) format.attributes.type = type;
  if (id) format.attributes.id = id;

  if (opensInNewWindow) {
    format.attributes.target = '_blank';
    format.attributes.rel = 'noreferrer noopener';
  }

  return format;
}
/* eslint-disable jsdoc/no-undefined-types */

/**
 * Get the start and end boundaries of a given format from a rich text value.
 *
 *
 * @param {RichTextValue} value      the rich text value to interrogate.
 * @param {string}        format     the identifier for the target format (e.g. `core/link`, `core/bold`).
 * @param {number?}       startIndex optional startIndex to seek from.
 * @param {number?}       endIndex   optional endIndex to seek from.
 * @return {Object}	object containing start and end values for the given format.
 */

/* eslint-enable jsdoc/no-undefined-types */

function getFormatBoundary(value, format, startIndex = value.start, endIndex = value.end) {
  const EMPTY_BOUNDARIES = {
    start: null,
    end: null
  };
  const {
    formats
  } = value;
  let targetFormat;
  let initialIndex;

  if (!formats?.length) {
    return EMPTY_BOUNDARIES;
  } // Clone formats to avoid modifying source formats.


  const newFormats = formats.slice();
  const formatAtStart = newFormats[startIndex]?.find(({
    type
  }) => type === format.type);
  const formatAtEnd = newFormats[endIndex]?.find(({
    type
  }) => type === format.type);
  const formatAtEndMinusOne = newFormats[endIndex - 1]?.find(({
    type
  }) => type === format.type);

  if (!!formatAtStart) {
    // Set values to conform to "start"
    targetFormat = formatAtStart;
    initialIndex = startIndex;
  } else if (!!formatAtEnd) {
    // Set values to conform to "end"
    targetFormat = formatAtEnd;
    initialIndex = endIndex;
  } else if (!!formatAtEndMinusOne) {
    // This is an edge case which will occur if you create a format, then place
    // the caret just before the format and hit the back ARROW key. The resulting
    // value object will have start and end +1 beyond the edge of the format boundary.
    targetFormat = formatAtEndMinusOne;
    initialIndex = endIndex - 1;
  } else {
    return EMPTY_BOUNDARIES;
  }

  const index = newFormats[initialIndex].indexOf(targetFormat);
  const walkingArgs = [newFormats, initialIndex, targetFormat, index]; // Walk the startIndex "backwards" to the leading "edge" of the matching format.

  startIndex = walkToStart(...walkingArgs); // Walk the endIndex "forwards" until the trailing "edge" of the matching format.

  endIndex = walkToEnd(...walkingArgs); // Safe guard: start index cannot be less than 0.

  startIndex = startIndex < 0 ? 0 : startIndex; // // Return the indicies of the "edges" as the boundaries.

  return {
    start: startIndex,
    end: endIndex
  };
}
/**
 * Walks forwards/backwards towards the boundary of a given format within an
 * array of format objects. Returns the index of the boundary.
 *
 * @param {Array}  formats         the formats to search for the given format type.
 * @param {number} initialIndex    the starting index from which to walk.
 * @param {Object} targetFormatRef a reference to the format type object being sought.
 * @param {number} formatIndex     the index at which we expect the target format object to be.
 * @param {string} direction       either 'forwards' or 'backwards' to indicate the direction.
 * @return {number} the index of the boundary of the given format.
 */

function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
  let index = initialIndex;
  const directions = {
    forwards: 1,
    backwards: -1
  };
  const directionIncrement = directions[direction] || 1; // invalid direction arg default to forwards

  const inverseDirectionIncrement = directionIncrement * -1;

  while (formats[index] && formats[index][formatIndex] === targetFormatRef) {
    // Increment/decrement in the direction of operation.
    index = index + directionIncrement;
  } // Restore by one in inverse direction of operation
  // to avoid out of bounds.


  index = index + inverseDirectionIncrement;
  return index;
}

const partialRight = (fn, ...partialArgs) => (...args) => fn(...args, ...partialArgs);

const walkToStart = partialRight(walkToBoundary, 'backwards');
const walkToEnd = partialRight(walkToBoundary, 'forwards');

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/use-link-instance-key.js
// Weakly referenced map allows unused ids to be garbage collected.
const weakMap = new WeakMap(); // Incrementing zero-based ID value.

let id = -1;
const prefix = 'link-control-instance';

function getKey(_id) {
  return `${prefix}-${_id}`;
}
/**
 * Builds a unique link control key for the given object reference.
 *
 * @param {Object} instance an unique object reference specific to this link control instance.
 * @return {string | undefined} the unique key to use for this link control.
 */


function useLinkInstanceKey(instance) {
  if (!instance) {
    return;
  }

  if (weakMap.has(instance)) {
    return getKey(weakMap.get(instance));
  }

  id += 1;
  weakMap.set(instance, id);
  return getKey(id);
}

/* harmony default export */ const use_link_instance_key = (useLinkInstanceKey);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/inline.js


/**
 * WordPress dependencies
=======

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/inline.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */




<<<<<<< HEAD



/**
 * Internal dependencies
 */





function InlineLinkUI({
  isActive,
  activeAttributes,
  addingLink,
  value,
  onChange,
  speak,
  stopAddingLink,
  contentRef
}) {
  const richLinkTextValue = getRichTextValueFromSelection(value, isActive); // Get the text content minus any HTML tags.

  const richTextText = richLinkTextValue.text;
  /**
   * Pending settings to be applied to the next link. When inserting a new
   * link, toggle values cannot be applied immediately, because there is not
   * yet a link for them to apply to. Thus, they are maintained in a state
   * value until the time that the link can be inserted or edited.
   *
   * @type {[Object|undefined,Function]}
   */

  const [nextLinkValue, setNextLinkValue] = (0,external_wp_element_namespaceObject.useState)();
  const {
    createPageEntity,
    userCanCreatePages
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getSettings
    } = select(external_wp_blockEditor_namespaceObject.store);

    const _settings = getSettings();

    return {
      createPageEntity: _settings.__experimentalCreatePageEntity,
      userCanCreatePages: _settings.__experimentalUserCanCreatePages
    };
  }, []);
  const linkValue = {
    url: activeAttributes.url,
    type: activeAttributes.type,
    id: activeAttributes.id,
    opensInNewTab: activeAttributes.target === '_blank',
    title: richTextText,
    ...nextLinkValue
  };

  function removeLink() {
    const newValue = (0,external_wp_richText_namespaceObject.removeFormat)(value, 'core/link');
    onChange(newValue);
    stopAddingLink();
    speak((0,external_wp_i18n_namespaceObject.__)('Link removed.'), 'assertive');
  }

  function onChangeLink(nextValue) {
    // Merge with values from state, both for the purpose of assigning the
    // next state value, and for use in constructing the new link format if
    // the link is ready to be applied.
    nextValue = { ...nextLinkValue,
      ...nextValue
    }; // LinkControl calls `onChange` immediately upon the toggling a setting.

    const didToggleSetting = linkValue.opensInNewTab !== nextValue.opensInNewTab && linkValue.url === nextValue.url; // If change handler was called as a result of a settings change during
    // link insertion, it must be held in state until the link is ready to
    // be applied.

    const didToggleSettingForNewLink = didToggleSetting && nextValue.url === undefined; // If link will be assigned, the state value can be considered flushed.
    // Otherwise, persist the pending changes.

    setNextLinkValue(didToggleSettingForNewLink ? nextValue : undefined);

    if (didToggleSettingForNewLink) {
      return;
    }

    const newUrl = (0,external_wp_url_namespaceObject.prependHTTP)(nextValue.url);
    const linkFormat = createLinkFormat({
      url: newUrl,
      type: nextValue.type,
      id: nextValue.id !== undefined && nextValue.id !== null ? String(nextValue.id) : undefined,
      opensInNewWindow: nextValue.opensInNewTab
    });
    const newText = nextValue.title || newUrl;

    if ((0,external_wp_richText_namespaceObject.isCollapsed)(value) && !isActive) {
      // Scenario: we don't have any actively selected text or formats.
      const toInsert = (0,external_wp_richText_namespaceObject.applyFormat)((0,external_wp_richText_namespaceObject.create)({
        text: newText
      }), linkFormat, 0, newText.length);
      onChange((0,external_wp_richText_namespaceObject.insert)(value, toInsert));
    } else {
      // Scenario: we have any active text selection or an active format.
      let newValue;

      if (newText === richTextText) {
        // If we're not updating the text then ignore.
        newValue = (0,external_wp_richText_namespaceObject.applyFormat)(value, linkFormat);
      } else {
        // Create new RichText value for the new text in order that we
        // can apply formats to it.
        newValue = (0,external_wp_richText_namespaceObject.create)({
          text: newText
        }); // Apply the new Link format to this new text value.

        newValue = (0,external_wp_richText_namespaceObject.applyFormat)(newValue, linkFormat, 0, newText.length); // Get the boundaries of the active link format.

        const boundary = getFormatBoundary(value, {
          type: 'core/link'
        }); // Split the value at the start of the active link format.
        // Passing "start" as the 3rd parameter is required to ensure
        // the second half of the split value is split at the format's
        // start boundary and avoids relying on the value's "end" property
        // which may not correspond correctly.

        const [valBefore, valAfter] = (0,external_wp_richText_namespaceObject.split)(value, boundary.start, boundary.start); // Update the original (full) RichTextValue replacing the
        // target text with the *new* RichTextValue containing:
        // 1. The new text content.
        // 2. The new link format.
        // As "replace" will operate on the first match only, it is
        // run only against the second half of the value which was
        // split at the active format's boundary. This avoids a bug
        // with incorrectly targetted replacements.
        // See: https://github.com/WordPress/gutenberg/issues/41771.
        // Note original formats will be lost when applying this change.
        // That is expected behaviour.
        // See: https://github.com/WordPress/gutenberg/pull/33849#issuecomment-936134179.

        const newValAfter = (0,external_wp_richText_namespaceObject.replace)(valAfter, richTextText, newValue);
        newValue = (0,external_wp_richText_namespaceObject.concat)(valBefore, newValAfter);
      }

      newValue.start = newValue.end;
      newValue.activeFormats = [];
      onChange(newValue);
    } // Focus should only be shifted back to the formatted segment when the
    // URL is submitted.


    if (!didToggleSetting) {
      stopAddingLink();
    }

    if (!isValidHref(newUrl)) {
      speak((0,external_wp_i18n_namespaceObject.__)('Warning: the link has been inserted but may have errors. Please test it.'), 'assertive');
    } else if (isActive) {
      speak((0,external_wp_i18n_namespaceObject.__)('Link edited.'), 'assertive');
    } else {
      speak((0,external_wp_i18n_namespaceObject.__)('Link inserted.'), 'assertive');
    }
  }

  const popoverAnchor = (0,external_wp_richText_namespaceObject.useAnchor)({
    editableContentElement: contentRef.current,
    settings: build_module_link_link
  }); // Generate a string based key that is unique to this anchor reference.
  // This is used to force re-mount the LinkControl component to avoid
  // potential stale state bugs caused by the component not being remounted
  // See https://github.com/WordPress/gutenberg/pull/34742.

  const forceRemountKey = use_link_instance_key(popoverAnchor); // The focusOnMount prop shouldn't evolve during render of a Popover
  // otherwise it causes a render of the content.

  const focusOnMount = (0,external_wp_element_namespaceObject.useRef)(addingLink ? 'firstElement' : false);

  async function handleCreate(pageTitle) {
    const page = await createPageEntity({
      title: pageTitle,
      status: 'draft'
    });
    return {
      id: page.id,
      type: page.type,
      title: page.title.rendered,
      url: page.link,
      kind: 'post-type'
    };
  }

  function createButtonText(searchTerm) {
    return (0,external_wp_element_namespaceObject.createInterpolateElement)((0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: %s: search term. */
    (0,external_wp_i18n_namespaceObject.__)('Create page: <mark>%s</mark>'), searchTerm), {
      mark: (0,external_wp_element_namespaceObject.createElement)("mark", null)
    });
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover, {
    anchor: popoverAnchor,
    focusOnMount: focusOnMount.current,
    onClose: stopAddingLink,
    onFocusOutside: () => stopAddingLink(false),
    placement: "bottom",
    shift: true
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__experimentalLinkControl, {
    key: forceRemountKey,
    value: linkValue,
    onChange: onChangeLink,
    onRemove: removeLink,
    forceIsEditingLink: addingLink,
    hasRichPreviews: true,
    createSuggestion: createPageEntity && handleCreate,
    withCreateSuggestion: userCanCreatePages,
    createSuggestionButtonText: createButtonText,
    hasTextControl: true
  }));
}

function getRichTextValueFromSelection(value, isActive) {
  // Default to the selection ranges on the RichTextValue object.
  let textStart = value.start;
  let textEnd = value.end; // If the format is currently active then the rich text value
  // should always be taken from the bounds of the active format
  // and not the selected text.

  if (isActive) {
    const boundary = getFormatBoundary(value, {
      type: 'core/link'
    });
    textStart = boundary.start; // Text *selection* always extends +1 beyond the edge of the format.
    // We account for that here.

    textEnd = boundary.end + 1;
  } // Get a RichTextValue containing the selected text content.


  return (0,external_wp_richText_namespaceObject.slice)(value, textStart, textEnd);
}

/* harmony default export */ const inline = ((0,external_wp_components_namespaceObject.withSpokenMessages)(InlineLinkUI));

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/index.js
=======
var stopKeyPropagation = function stopKeyPropagation(event) {
  return event.stopPropagation();
};
/**
 * Generates the format object that will be applied to the link text.
 *
 * @param {string}  url              The href of the link.
 * @param {boolean} opensInNewWindow Whether this link will open in a new window.
 * @param {Object}  text             The text that is being hyperlinked.
 *
 * @return {Object} The final format object.
 */


function createLinkFormat(_ref) {
  var url = _ref.url,
      opensInNewWindow = _ref.opensInNewWindow,
      text = _ref.text;
  var format = {
    type: 'core/link',
    attributes: {
      url: url
    }
  };

  if (opensInNewWindow) {
    // translators: accessibility label for external links, where the argument is the link text
    var label = Object(external_this_wp_i18n_["sprintf"])(Object(external_this_wp_i18n_["__"])('%s (opens in a new tab)'), text);
    format.attributes.target = '_blank';
    format.attributes.rel = 'noreferrer noopener';
    format.attributes['aria-label'] = label;
  }

  return format;
}

function isShowingInput(props, state) {
  return props.addingLink || state.editLink;
}

var inline_LinkEditor = function LinkEditor(_ref2) {
  var value = _ref2.value,
      onChangeInputValue = _ref2.onChangeInputValue,
      onKeyDown = _ref2.onKeyDown,
      submitLink = _ref2.submitLink,
      autocompleteRef = _ref2.autocompleteRef;
  return (// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar

    /* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
    Object(external_this_wp_element_["createElement"])("form", {
      className: "editor-format-toolbar__link-container-content",
      onKeyPress: stopKeyPropagation,
      onKeyDown: onKeyDown,
      onSubmit: submitLink
    }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["URLInput"], {
      value: value,
      onChange: onChangeInputValue,
      autocompleteRef: autocompleteRef
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
      icon: "editor-break",
      label: Object(external_this_wp_i18n_["__"])('Apply'),
      type: "submit"
    }))
    /* eslint-enable jsx-a11y/no-noninteractive-element-interactions */

  );
};

var inline_LinkViewerUrl = function LinkViewerUrl(_ref3) {
  var url = _ref3.url;
  var prependedURL = Object(external_this_wp_url_["prependHTTP"])(url);
  var linkClassName = classnames_default()('editor-format-toolbar__link-container-value', {
    'has-invalid-link': !isValidHref(prependedURL)
  });

  if (!url) {
    return Object(external_this_wp_element_["createElement"])("span", {
      className: linkClassName
    });
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ExternalLink"], {
    className: linkClassName,
    href: url
  }, Object(external_this_wp_url_["filterURLForDisplay"])(Object(external_this_wp_url_["safeDecodeURI"])(url)));
};

var inline_LinkViewer = function LinkViewer(_ref4) {
  var url = _ref4.url,
      editLink = _ref4.editLink;
  return (// Disable reason: KeyPress must be suppressed so the block doesn't hide the toolbar

    /* eslint-disable jsx-a11y/no-static-element-interactions */
    Object(external_this_wp_element_["createElement"])("div", {
      className: "editor-format-toolbar__link-container-content",
      onKeyPress: stopKeyPropagation
    }, Object(external_this_wp_element_["createElement"])(inline_LinkViewerUrl, {
      url: url
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
      icon: "edit",
      label: Object(external_this_wp_i18n_["__"])('Edit'),
      onClick: editLink
    }))
    /* eslint-enable jsx-a11y/no-static-element-interactions */

  );
};

var inline_InlineLinkUI =
/*#__PURE__*/
function (_Component) {
  Object(inherits["a" /* default */])(InlineLinkUI, _Component);

  function InlineLinkUI() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, InlineLinkUI);

    _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(InlineLinkUI).apply(this, arguments));
    _this.editLink = _this.editLink.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.submitLink = _this.submitLink.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onKeyDown = _this.onKeyDown.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onChangeInputValue = _this.onChangeInputValue.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.setLinkTarget = _this.setLinkTarget.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.onClickOutside = _this.onClickOutside.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.resetState = _this.resetState.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
    _this.autocompleteRef = Object(external_this_wp_element_["createRef"])();
    _this.state = {
      opensInNewWindow: false,
      inputValue: ''
    };
    return _this;
  }

  Object(createClass["a" /* default */])(InlineLinkUI, [{
    key: "onKeyDown",
    value: function onKeyDown(event) {
      if ([external_this_wp_keycodes_["LEFT"], external_this_wp_keycodes_["DOWN"], external_this_wp_keycodes_["RIGHT"], external_this_wp_keycodes_["UP"], external_this_wp_keycodes_["BACKSPACE"], external_this_wp_keycodes_["ENTER"]].indexOf(event.keyCode) > -1) {
        // Stop the key event from propagating up to ObserveTyping.startTypingInTextField.
        event.stopPropagation();
      }
    }
  }, {
    key: "onChangeInputValue",
    value: function onChangeInputValue(inputValue) {
      this.setState({
        inputValue: inputValue
      });
    }
  }, {
    key: "setLinkTarget",
    value: function setLinkTarget(opensInNewWindow) {
      var _this$props = this.props,
          _this$props$activeAtt = _this$props.activeAttributes.url,
          url = _this$props$activeAtt === void 0 ? '' : _this$props$activeAtt,
          value = _this$props.value,
          onChange = _this$props.onChange;
      this.setState({
        opensInNewWindow: opensInNewWindow
      }); // Apply now if URL is not being edited.

      if (!isShowingInput(this.props, this.state)) {
        var selectedText = Object(external_this_wp_richText_["getTextContent"])(Object(external_this_wp_richText_["slice"])(value));
        onChange(Object(external_this_wp_richText_["applyFormat"])(value, createLinkFormat({
          url: url,
          opensInNewWindow: opensInNewWindow,
          text: selectedText
        })));
      }
    }
  }, {
    key: "editLink",
    value: function editLink(event) {
      this.setState({
        editLink: true
      });
      event.preventDefault();
    }
  }, {
    key: "submitLink",
    value: function submitLink(event) {
      var _this$props2 = this.props,
          isActive = _this$props2.isActive,
          value = _this$props2.value,
          onChange = _this$props2.onChange,
          speak = _this$props2.speak;
      var _this$state = this.state,
          inputValue = _this$state.inputValue,
          opensInNewWindow = _this$state.opensInNewWindow;
      var url = Object(external_this_wp_url_["prependHTTP"])(inputValue);
      var selectedText = Object(external_this_wp_richText_["getTextContent"])(Object(external_this_wp_richText_["slice"])(value));
      var format = createLinkFormat({
        url: url,
        opensInNewWindow: opensInNewWindow,
        text: selectedText
      });
      event.preventDefault();

      if (Object(external_this_wp_richText_["isCollapsed"])(value) && !isActive) {
        var toInsert = Object(external_this_wp_richText_["applyFormat"])(Object(external_this_wp_richText_["create"])({
          text: url
        }), format, 0, url.length);
        onChange(Object(external_this_wp_richText_["insert"])(value, toInsert));
      } else {
        onChange(Object(external_this_wp_richText_["applyFormat"])(value, format));
      }

      this.resetState();

      if (!isValidHref(url)) {
        speak(Object(external_this_wp_i18n_["__"])('Warning: the link has been inserted but may have errors. Please test it.'), 'assertive');
      } else if (isActive) {
        speak(Object(external_this_wp_i18n_["__"])('Link edited.'), 'assertive');
      } else {
        speak(Object(external_this_wp_i18n_["__"])('Link inserted.'), 'assertive');
      }
    }
  }, {
    key: "onClickOutside",
    value: function onClickOutside(event) {
      // The autocomplete suggestions list renders in a separate popover (in a portal),
      // so onClickOutside fails to detect that a click on a suggestion occured in the
      // LinkContainer. Detect clicks on autocomplete suggestions using a ref here, and
      // return to avoid the popover being closed.
      var autocompleteElement = this.autocompleteRef.current;

      if (autocompleteElement && autocompleteElement.contains(event.target)) {
        return;
      }

      this.resetState();
    }
  }, {
    key: "resetState",
    value: function resetState() {
      this.props.stopAddingLink();
      this.setState({
        editLink: false
      });
    }
  }, {
    key: "render",
    value: function render() {
      var _this2 = this;

      var _this$props3 = this.props,
          isActive = _this$props3.isActive,
          url = _this$props3.activeAttributes.url,
          addingLink = _this$props3.addingLink,
          value = _this$props3.value;

      if (!isActive && !addingLink) {
        return null;
      }

      var _this$state2 = this.state,
          inputValue = _this$state2.inputValue,
          opensInNewWindow = _this$state2.opensInNewWindow;
      var showInput = isShowingInput(this.props, this.state);
      return Object(external_this_wp_element_["createElement"])(positioned_at_selection, {
        key: "".concat(value.start).concat(value.end)
        /* Used to force rerender on selection change */

      }, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["URLPopover"], {
        onClickOutside: this.onClickOutside,
        onClose: this.resetState,
        focusOnMount: showInput ? 'firstElement' : false,
        renderSettings: function renderSettings() {
          return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["ToggleControl"], {
            label: Object(external_this_wp_i18n_["__"])('Open in New Tab'),
            checked: opensInNewWindow,
            onChange: _this2.setLinkTarget
          });
        }
      }, showInput ? Object(external_this_wp_element_["createElement"])(inline_LinkEditor, {
        value: inputValue,
        onChangeInputValue: this.onChangeInputValue,
        onKeyDown: this.onKeyDown,
        submitLink: this.submitLink,
        autocompleteRef: this.autocompleteRef
      }) : Object(external_this_wp_element_["createElement"])(inline_LinkViewer, {
        url: url,
        editLink: this.editLink
      })));
    }
  }], [{
    key: "getDerivedStateFromProps",
    value: function getDerivedStateFromProps(props, state) {
      var _props$activeAttribut = props.activeAttributes,
          url = _props$activeAttribut.url,
          target = _props$activeAttribut.target;
      var opensInNewWindow = target === '_blank';

      if (!isShowingInput(props, state)) {
        if (url !== state.inputValue) {
          return {
            inputValue: url
          };
        }

        if (opensInNewWindow !== state.opensInNewWindow) {
          return {
            opensInNewWindow: opensInNewWindow
          };
        }
      }

      return null;
    }
  }]);

  return InlineLinkUI;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var inline = (Object(external_this_wp_components_["withSpokenMessages"])(inline_InlineLinkUI));

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/link/index.js






>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */






<<<<<<< HEAD


=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD

const link_name = 'core/link';

const link_title = (0,external_wp_i18n_namespaceObject.__)('Link');

function link_Edit({
  isActive,
  activeAttributes,
  value,
  onChange,
  onFocus,
  contentRef
}) {
  const [addingLink, setAddingLink] = (0,external_wp_element_namespaceObject.useState)(false);

  function addLink() {
    const text = (0,external_wp_richText_namespaceObject.getTextContent)((0,external_wp_richText_namespaceObject.slice)(value));

    if (text && (0,external_wp_url_namespaceObject.isURL)(text) && isValidHref(text)) {
      onChange((0,external_wp_richText_namespaceObject.applyFormat)(value, {
        type: link_name,
        attributes: {
          url: text
        }
      }));
    } else if (text && (0,external_wp_url_namespaceObject.isEmail)(text)) {
      onChange((0,external_wp_richText_namespaceObject.applyFormat)(value, {
        type: link_name,
        attributes: {
          url: `mailto:${text}`
        }
      }));
    } else {
      setAddingLink(true);
    }
  }

  function stopAddingLink(returnFocus = true) {
    setAddingLink(false);

    if (returnFocus) {
      onFocus();
    }
  }

  function onRemoveFormat() {
    onChange((0,external_wp_richText_namespaceObject.removeFormat)(value, link_name));
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.__)('Link removed.'), 'assertive');
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
    type: "primary",
    character: "k",
    onUse: addLink
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
    type: "primaryShift",
    character: "k",
    onUse: onRemoveFormat
  }), isActive && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
    name: "link",
    icon: link_off,
    title: (0,external_wp_i18n_namespaceObject.__)('Unlink'),
    onClick: onRemoveFormat,
    isActive: isActive,
    shortcutType: "primaryShift",
    shortcutCharacter: "k"
  }), !isActive && (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
    name: "link",
    icon: library_link,
    title: link_title,
    onClick: addLink,
    isActive: isActive,
    shortcutType: "primary",
    shortcutCharacter: "k"
  }), (addingLink || isActive) && (0,external_wp_element_namespaceObject.createElement)(inline, {
    addingLink: addingLink,
    stopAddingLink: stopAddingLink,
    isActive: isActive,
    activeAttributes: activeAttributes,
    value: value,
    onChange: onChange,
    contentRef: contentRef
  }));
}

const build_module_link_link = {
  name: link_name,
  title: link_title,
=======
var link_name = 'core/link';
var link_link = {
  name: link_name,
  title: Object(external_this_wp_i18n_["__"])('Link'),
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  tagName: 'a',
  className: null,
  attributes: {
    url: 'href',
<<<<<<< HEAD
    type: 'data-type',
    id: 'data-id',
    target: 'target'
  },

  __unstablePasteRule(value, {
    html,
    plainText
  }) {
    if ((0,external_wp_richText_namespaceObject.isCollapsed)(value)) {
      return value;
    }

    const pastedText = (html || plainText).replace(/<[^>]+>/g, '').trim(); // A URL was pasted, turn the selection into a link.

    if (!(0,external_wp_url_namespaceObject.isURL)(pastedText)) {
      return value;
    } // Allows us to ask for this information when we get a report.


    window.console.log('Created link:\n\n', pastedText);
    return (0,external_wp_richText_namespaceObject.applyFormat)(value, {
      type: link_name,
      attributes: {
        url: (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(pastedText)
      }
    });
  },

  edit: link_Edit
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/format-strikethrough.js
=======
    target: 'target'
  },
  edit: Object(external_this_wp_components_["withSpokenMessages"])(
  /*#__PURE__*/
  function (_Component) {
    Object(inherits["a" /* default */])(LinkEdit, _Component);

    function LinkEdit() {
      var _this;

      Object(classCallCheck["a" /* default */])(this, LinkEdit);

      _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(LinkEdit).apply(this, arguments));
      _this.addLink = _this.addLink.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
      _this.stopAddingLink = _this.stopAddingLink.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
      _this.onRemoveFormat = _this.onRemoveFormat.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
      _this.state = {
        addingLink: false
      };
      return _this;
    }

    Object(createClass["a" /* default */])(LinkEdit, [{
      key: "addLink",
      value: function addLink() {
        var _this$props = this.props,
            value = _this$props.value,
            onChange = _this$props.onChange;
        var text = Object(external_this_wp_richText_["getTextContent"])(Object(external_this_wp_richText_["slice"])(value));

        if (text && Object(external_this_wp_url_["isURL"])(text)) {
          onChange(Object(external_this_wp_richText_["applyFormat"])(value, {
            type: link_name,
            attributes: {
              url: text
            }
          }));
        } else {
          this.setState({
            addingLink: true
          });
        }
      }
    }, {
      key: "stopAddingLink",
      value: function stopAddingLink() {
        this.setState({
          addingLink: false
        });
      }
    }, {
      key: "onRemoveFormat",
      value: function onRemoveFormat() {
        var _this$props2 = this.props,
            value = _this$props2.value,
            onChange = _this$props2.onChange,
            speak = _this$props2.speak;
        onChange(Object(external_this_wp_richText_["removeFormat"])(value, link_name));
        speak(Object(external_this_wp_i18n_["__"])('Link removed.'), 'assertive');
      }
    }, {
      key: "render",
      value: function render() {
        var _this$props3 = this.props,
            isActive = _this$props3.isActive,
            activeAttributes = _this$props3.activeAttributes,
            value = _this$props3.value,
            onChange = _this$props3.onChange;
        return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
          type: "access",
          character: "a",
          onUse: this.addLink
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
          type: "access",
          character: "s",
          onUse: this.onRemoveFormat
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
          type: "primary",
          character: "k",
          onUse: this.addLink
        }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
          type: "primaryShift",
          character: "k",
          onUse: this.onRemoveFormat
        }), isActive && Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextToolbarButton"], {
          name: "link",
          icon: "editor-unlink",
          title: Object(external_this_wp_i18n_["__"])('Unlink'),
          onClick: this.onRemoveFormat,
          isActive: isActive,
          shortcutType: "primaryShift",
          shortcutCharacter: "k"
        }), !isActive && Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextToolbarButton"], {
          name: "link",
          icon: "admin-links",
          title: Object(external_this_wp_i18n_["__"])('Link'),
          onClick: this.addLink,
          isActive: isActive,
          shortcutType: "primary",
          shortcutCharacter: "k"
        }), Object(external_this_wp_element_["createElement"])(inline, {
          addingLink: this.state.addingLink,
          stopAddingLink: this.stopAddingLink,
          isActive: isActive,
          activeAttributes: activeAttributes,
          value: value,
          onChange: onChange
        }));
      }
    }]);

    return LinkEdit;
  }(external_this_wp_element_["Component"]))
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/strikethrough/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
const formatStrikethrough = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M9.1 9v-.5c0-.6.2-1.1.7-1.4.5-.3 1.2-.5 2-.5.7 0 1.4.1 2.1.3.7.2 1.4.5 2.1.9l.2-1.9c-.6-.3-1.2-.5-1.9-.7-.8-.1-1.6-.2-2.4-.2-1.5 0-2.7.3-3.6 1-.8.7-1.2 1.5-1.2 2.6V9h2zM20 12H4v1h8.3c.3.1.6.2.8.3.5.2.9.5 1.1.8.3.3.4.7.4 1.2 0 .7-.2 1.1-.8 1.5-.5.3-1.2.5-2.1.5-.8 0-1.6-.1-2.4-.3-.8-.2-1.5-.5-2.2-.8L7 18.1c.5.2 1.2.4 2 .6.8.2 1.6.3 2.4.3 1.7 0 3-.3 3.9-1 .9-.7 1.3-1.6 1.3-2.8 0-.9-.2-1.7-.7-2.2H20v-1z"
}));
/* harmony default export */ const format_strikethrough = (formatStrikethrough);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/strikethrough/index.js


/**
 * WordPress dependencies
 */




const strikethrough_name = 'core/strikethrough';

const strikethrough_title = (0,external_wp_i18n_namespaceObject.__)('Strikethrough');

const strikethrough = {
  name: strikethrough_name,
  title: strikethrough_title,
  tagName: 's',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onClick() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: strikethrough_name,
        title: strikethrough_title
      }));
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
      type: "access",
      character: "d",
      onUse: onClick
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      icon: format_strikethrough,
      title: strikethrough_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    }));
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/underline/index.js


/**
 * WordPress dependencies
 */



const underline_name = 'core/underline';

const underline_title = (0,external_wp_i18n_namespaceObject.__)('Underline');

const underline = {
  name: underline_name,
  title: underline_title,
  tagName: 'span',
  className: null,
  attributes: {
    style: 'style'
  },

  edit({
    value,
    onChange
  }) {
    const onToggle = () => {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: underline_name,
        attributes: {
          style: 'text-decoration: underline;'
        },
        title: underline_title
      }));
    };

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextShortcut, {
      type: "primary",
      character: "u",
      onUse: onToggle
    }), (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.__unstableRichTextInputEvent, {
      inputType: "formatUnderline",
      onInput: onToggle
    }));
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/icon/index.js
/**
 * WordPress dependencies
 */

/** @typedef {{icon: JSX.Element, size?: number} & import('@wordpress/primitives').SVGProps} IconProps */

/**
 * Return an SVG icon.
 *
 * @param {IconProps} props icon is the SVG component to render
 *                          size is a number specifiying the icon size in pixels
 *                          Other props will be passed to wrapped SVG component
 *
 * @return {JSX.Element}  Icon component
 */

function Icon({
  icon,
  size = 24,
  ...props
}) {
  return (0,external_wp_element_namespaceObject.cloneElement)(icon, {
    width: size,
    height: size,
    ...props
  });
}

/* harmony default export */ const icon = (Icon);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/text-color.js


/**
 * WordPress dependencies
 */

const textColor = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12.9 6h-2l-4 11h1.9l1.1-3h4.2l1.1 3h1.9L12.9 6zm-2.5 6.5l1.5-4.9 1.7 4.9h-3.2z"
}));
/* harmony default export */ const text_color = (textColor);

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/color.js


/**
 * WordPress dependencies
 */

const color = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.2 10.9c-.5-1-1.2-2.1-2.1-3.2-.6-.9-1.3-1.7-2.1-2.6L12 4l-1 1.1c-.6.9-1.3 1.7-2 2.6-.8 1.2-1.5 2.3-2 3.2-.6 1.2-1 2.2-1 3 0 3.4 2.7 6.1 6.1 6.1s6.1-2.7 6.1-6.1c0-.8-.3-1.8-1-3zm-5.1 7.6c-2.5 0-4.6-2.1-4.6-4.6 0-.3.1-1 .8-2.3.5-.9 1.1-1.9 2-3.1.7-.9 1.3-1.7 1.8-2.3.7.8 1.3 1.6 1.8 2.3.8 1.1 1.5 2.2 2 3.1.7 1.3.8 2 .8 2.3 0 2.5-2.1 4.6-4.6 4.6z"
}));
/* harmony default export */ const library_color = (color);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/text-color/inline.js


/**
 * WordPress dependencies
 */




=======



var strikethrough_name = 'core/strikethrough';
var strikethrough = {
  name: strikethrough_name,
  title: Object(external_this_wp_i18n_["__"])('Strikethrough'),
  tagName: 'del',
  className: null,
  edit: function edit(_ref) {
    var isActive = _ref.isActive,
        value = _ref.value,
        onChange = _ref.onChange;

    var onToggle = function onToggle() {
      return onChange(Object(external_this_wp_richText_["toggleFormat"])(value, {
        type: strikethrough_name
      }));
    };

    return Object(external_this_wp_element_["createElement"])(external_this_wp_element_["Fragment"], null, Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextShortcut"], {
      type: "access",
      character: "d",
      onUse: onToggle
    }), Object(external_this_wp_element_["createElement"])(external_this_wp_editor_["RichTextToolbarButton"], {
      name: "strikethrough",
      icon: "editor-strikethrough",
      title: Object(external_this_wp_i18n_["__"])('Strikethrough'),
      onClick: onToggle,
      isActive: isActive,
      shortcutType: "access",
      shortcutCharacter: "d"
    }));
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * Internal dependencies
 */



<<<<<<< HEAD
function parseCSS(css = '') {
  return css.split(';').reduce((accumulator, rule) => {
    if (rule) {
      const [property, value] = rule.split(':');
      if (property === 'color') accumulator.color = value;
      if (property === 'background-color' && value !== transparentValue) accumulator.backgroundColor = value;
    }

    return accumulator;
  }, {});
}

function parseClassName(className = '', colorSettings) {
  return className.split(' ').reduce((accumulator, name) => {
    // `colorSlug` could contain dashes, so simply match the start and end.
    if (name.startsWith('has-') && name.endsWith('-color')) {
      const colorSlug = name.replace(/^has-/, '').replace(/-color$/, '');
      const colorObject = (0,external_wp_blockEditor_namespaceObject.getColorObjectByAttributeValues)(colorSettings, colorSlug);
      accumulator.color = colorObject.color;
    }

    return accumulator;
  }, {});
}
function getActiveColors(value, name, colorSettings) {
  const activeColorFormat = (0,external_wp_richText_namespaceObject.getActiveFormat)(value, name);

  if (!activeColorFormat) {
    return {};
  }

  return { ...parseCSS(activeColorFormat.attributes.style),
    ...parseClassName(activeColorFormat.attributes.class, colorSettings)
  };
}

function setColors(value, name, colorSettings, colors) {
  const {
    color,
    backgroundColor
  } = { ...getActiveColors(value, name, colorSettings),
    ...colors
  };

  if (!color && !backgroundColor) {
    return (0,external_wp_richText_namespaceObject.removeFormat)(value, name);
  }

  const styles = [];
  const classNames = [];
  const attributes = {};

  if (backgroundColor) {
    styles.push(['background-color', backgroundColor].join(':'));
  } else {
    // Override default browser color for mark element.
    styles.push(['background-color', transparentValue].join(':'));
  }

  if (color) {
    const colorObject = (0,external_wp_blockEditor_namespaceObject.getColorObjectByColorValue)(colorSettings, color);

    if (colorObject) {
      classNames.push((0,external_wp_blockEditor_namespaceObject.getColorClassName)('color', colorObject.slug));
    } else {
      styles.push(['color', color].join(':'));
    }
  }

  if (styles.length) attributes.style = styles.join(';');
  if (classNames.length) attributes.class = classNames.join(' ');
  return (0,external_wp_richText_namespaceObject.applyFormat)(value, {
    type: name,
    attributes
  });
}

function ColorPicker({
  name,
  property,
  value,
  onChange
}) {
  const colors = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _getSettings$colors;

    const {
      getSettings
    } = select(external_wp_blockEditor_namespaceObject.store);
    return (_getSettings$colors = getSettings().colors) !== null && _getSettings$colors !== void 0 ? _getSettings$colors : [];
  }, []);
  const onColorChange = (0,external_wp_element_namespaceObject.useCallback)(color => {
    onChange(setColors(value, name, colors, {
      [property]: color
    }));
  }, [colors, onChange, property]);
  const activeColors = (0,external_wp_element_namespaceObject.useMemo)(() => getActiveColors(value, name, colors), [name, value, colors]);
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.ColorPalette, {
    value: activeColors[property],
    onChange: onColorChange
  });
}

function InlineColorUI({
  name,
  value,
  onChange,
  onClose,
  contentRef
}) {
  /*
   As you change the text color by typing a HEX value into a field,
   the return value of document.getSelection jumps to the field you're editing,
   not the highlighted text. Given that useAnchor uses document.getSelection,
   it will return null, since it can't find the <mark> element within the HEX input.
   This caches the last truthy value of the selection anchor reference.
   */
  const popoverAnchor = (0,external_wp_blockEditor_namespaceObject.useCachedTruthy)((0,external_wp_richText_namespaceObject.useAnchor)({
    editableContentElement: contentRef.current,
    settings: text_color_textColor
  }));
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover, {
    onClose: onClose,
    className: "components-inline-color-popover",
    anchor: popoverAnchor
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TabPanel, {
    tabs: [{
      name: 'color',
      title: (0,external_wp_i18n_namespaceObject.__)('Text')
    }, {
      name: 'backgroundColor',
      title: (0,external_wp_i18n_namespaceObject.__)('Background')
    }]
  }, tab => (0,external_wp_element_namespaceObject.createElement)(ColorPicker, {
    name: name,
    property: tab.name,
    value: value,
    onChange: onChange
  })));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/text-color/index.js
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */


<<<<<<< HEAD



/**
 * Internal dependencies
 */


const transparentValue = 'rgba(0, 0, 0, 0)';
const text_color_name = 'core/text-color';

const text_color_title = (0,external_wp_i18n_namespaceObject.__)('Highlight');

const EMPTY_ARRAY = [];

function getComputedStyleProperty(element, property) {
  const {
    ownerDocument
  } = element;
  const {
    defaultView
  } = ownerDocument;
  const style = defaultView.getComputedStyle(element);
  const value = style.getPropertyValue(property);

  if (property === 'background-color' && value === transparentValue && element.parentElement) {
    return getComputedStyleProperty(element.parentElement, property);
  }

  return value;
}

function fillComputedColors(element, {
  color,
  backgroundColor
}) {
  if (!color && !backgroundColor) {
    return;
  }

  return {
    color: color || getComputedStyleProperty(element, 'color'),
    backgroundColor: backgroundColor === transparentValue ? getComputedStyleProperty(element, 'background-color') : backgroundColor
  };
}

function TextColorEdit({
  value,
  onChange,
  isActive,
  activeAttributes,
  contentRef
}) {
  const allowCustomControl = (0,external_wp_blockEditor_namespaceObject.useSetting)('color.custom');
  const colors = (0,external_wp_blockEditor_namespaceObject.useSetting)('color.palette') || EMPTY_ARRAY;
  const [isAddingColor, setIsAddingColor] = (0,external_wp_element_namespaceObject.useState)(false);
  const enableIsAddingColor = (0,external_wp_element_namespaceObject.useCallback)(() => setIsAddingColor(true), [setIsAddingColor]);
  const disableIsAddingColor = (0,external_wp_element_namespaceObject.useCallback)(() => setIsAddingColor(false), [setIsAddingColor]);
  const colorIndicatorStyle = (0,external_wp_element_namespaceObject.useMemo)(() => fillComputedColors(contentRef.current, getActiveColors(value, text_color_name, colors)), [value, colors]);
  const hasColorsToChoose = colors.length || !allowCustomControl;

  if (!hasColorsToChoose && !isActive) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
    className: "format-library-text-color-button",
    isActive: isActive,
    icon: (0,external_wp_element_namespaceObject.createElement)(icon, {
      icon: Object.keys(activeAttributes).length ? text_color : library_color,
      style: colorIndicatorStyle
    }),
    title: text_color_title // If has no colors to choose but a color is active remove the color onClick.
    ,
    onClick: hasColorsToChoose ? enableIsAddingColor : () => onChange((0,external_wp_richText_namespaceObject.removeFormat)(value, text_color_name)),
    role: "menuitemcheckbox"
  }), isAddingColor && (0,external_wp_element_namespaceObject.createElement)(InlineColorUI, {
    name: text_color_name,
    onClose: disableIsAddingColor,
    activeAttributes: activeAttributes,
    value: value,
    onChange: onChange,
    contentRef: contentRef
  }));
}

const text_color_textColor = {
  name: text_color_name,
  title: text_color_title,
  tagName: 'mark',
  className: 'has-inline-color',
  attributes: {
    style: 'style',
    class: 'class'
  },

  /*
   * Since this format relies on the <mark> tag, it's important to
   * prevent the default yellow background color applied by most
   * browsers. The solution is to detect when this format is used with a
   * text color but no background color, and in such cases to override
   * the default styling with a transparent background.
   *
   * @see https://github.com/WordPress/gutenberg/pull/35516
   */
  __unstableFilterAttributeValue(key, value) {
    if (key !== 'style') return value; // We should not add a background-color if it's already set.

    if (value && value.includes('background-color')) return value;
    const addedCSS = ['background-color', transparentValue].join(':'); // Prepend `addedCSS` to avoid a double `;;` as any the existing CSS
    // rules will already include a `;`.

    return value ? [addedCSS, value].join(';') : addedCSS;
  },

  edit: TextColorEdit
};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/subscript.js


/**
 * WordPress dependencies
 */

const subscript = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.9 18.3l.8-1.2c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.1-.3-.4-.5-.6-.7-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.2 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3L15 19.4h4.3v-1.2h-2.4zM14.1 7.2h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ const library_subscript = (subscript);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/subscript/index.js


/**
 * WordPress dependencies
 */




const subscript_name = 'core/subscript';

const subscript_title = (0,external_wp_i18n_namespaceObject.__)('Subscript');

const subscript_subscript = {
  name: subscript_name,
  title: subscript_title,
  tagName: 'sub',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onToggle() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: subscript_name,
        title: subscript_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      icon: library_subscript,
      title: subscript_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/superscript.js


/**
 * WordPress dependencies
 */

const superscript = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M16.9 10.3l.8-1.3c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.2-.2-.4-.4-.7-.6-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.1 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3l-1.8 2.8h4.3v-1.2h-2.2zm-2.8-3.1h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z"
}));
/* harmony default export */ const library_superscript = (superscript);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/superscript/index.js


/**
 * WordPress dependencies
 */




const superscript_name = 'core/superscript';

const superscript_title = (0,external_wp_i18n_namespaceObject.__)('Superscript');

const superscript_superscript = {
  name: superscript_name,
  title: superscript_title,
  tagName: 'sup',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onToggle() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: superscript_name,
        title: superscript_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      icon: library_superscript,
      title: superscript_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/button.js


/**
 * WordPress dependencies
 */

const button_button = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M8 12.5h8V11H8v1.5Z M19 6.5H5a2 2 0 0 0-2 2V15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a2 2 0 0 0-2-2ZM5 8h14a.5.5 0 0 1 .5.5V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V8.5A.5.5 0 0 1 5 8Z"
}));
/* harmony default export */ const library_button = (button_button);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/keyboard/index.js


/**
 * WordPress dependencies
 */




const keyboard_name = 'core/keyboard';

const keyboard_title = (0,external_wp_i18n_namespaceObject.__)('Keyboard input');

const keyboard = {
  name: keyboard_name,
  title: keyboard_title,
  tagName: 'kbd',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onToggle() {
      onChange((0,external_wp_richText_namespaceObject.toggleFormat)(value, {
        type: keyboard_name,
        title: keyboard_title
      }));
    }

    function onClick() {
      onToggle();
      onFocus();
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      icon: library_button,
      title: keyboard_title,
      onClick: onClick,
      isActive: isActive,
      role: "menuitemcheckbox"
    });
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/help.js


/**
 * WordPress dependencies
 */

const help = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M12 4.75a7.25 7.25 0 100 14.5 7.25 7.25 0 000-14.5zM3.25 12a8.75 8.75 0 1117.5 0 8.75 8.75 0 01-17.5 0zM12 8.75a1.5 1.5 0 01.167 2.99c-.465.052-.917.44-.917 1.01V14h1.5v-.845A3 3 0 109 10.25h1.5a1.5 1.5 0 011.5-1.5zM11.25 15v1.5h1.5V15h-1.5z"
}));
/* harmony default export */ const library_help = (help);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/unknown/index.js


/**
 * WordPress dependencies
 */




const unknown_name = 'core/unknown';

const unknown_title = (0,external_wp_i18n_namespaceObject.__)('Clear Unknown Formatting');

const unknown = {
  name: unknown_name,
  title: unknown_title,
  tagName: '*',
  className: null,

  edit({
    isActive,
    value,
    onChange,
    onFocus
  }) {
    function onClick() {
      onChange((0,external_wp_richText_namespaceObject.removeFormat)(value, unknown_name));
      onFocus();
    }

    const selectedValue = (0,external_wp_richText_namespaceObject.slice)(value);
    const hasUnknownFormats = selectedValue.formats.some(formats => {
      return formats.some(format => format.type === unknown_name);
    });

    if (!isActive && !hasUnknownFormats) {
      return null;
    }

    return (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
      name: "unknown",
      icon: library_help,
      title: unknown_title,
      onClick: onClick,
      isActive: true
    });
  }

};

;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/language.js


/**
 * WordPress dependencies
 */

const language = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M17.5 10h-1.7l-3.7 10.5h1.7l.9-2.6h3.9l.9 2.6h1.7L17.5 10zm-2.2 6.3 1.4-4 1.4 4h-2.8zm-4.8-3.8c1.6-1.8 2.9-3.6 3.7-5.7H16V5.2h-5.8V3H8.8v2.2H3v1.5h9.6c-.7 1.6-1.8 3.1-3.1 4.6C8.6 10.2 7.8 9 7.2 8H5.6c.6 1.4 1.7 2.9 2.9 4.4l-2.4 2.4c-.3.4-.7.8-1.1 1.2l1 1 1.2-1.2c.8-.8 1.6-1.5 2.3-2.3.8.9 1.7 1.7 2.5 2.5l.6-1.5c-.7-.6-1.4-1.3-2.1-2z"
}));
/* harmony default export */ const library_language = (language);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/language/index.js


/**
 * WordPress dependencies
 */

/**
 * WordPress dependencies
 */






const language_name = 'core/language';

const language_title = (0,external_wp_i18n_namespaceObject.__)('Language');

const language_language = {
  name: language_name,
  tagName: 'bdo',
  className: null,
  edit: language_Edit,
  title: language_title
};

function language_Edit({
  isActive,
  value,
  onChange,
  contentRef
}) {
  const [isPopoverVisible, setIsPopoverVisible] = (0,external_wp_element_namespaceObject.useState)(false);

  const togglePopover = () => {
    setIsPopoverVisible(state => !state);
  };

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_element_namespaceObject.Fragment, null, (0,external_wp_element_namespaceObject.createElement)(external_wp_blockEditor_namespaceObject.RichTextToolbarButton, {
    icon: library_language,
    label: language_title,
    title: language_title,
    onClick: () => {
      if (isActive) {
        onChange((0,external_wp_richText_namespaceObject.removeFormat)(value, language_name));
      } else {
        togglePopover();
      }
    },
    isActive: isActive,
    role: "menuitemcheckbox"
  }), isPopoverVisible && (0,external_wp_element_namespaceObject.createElement)(InlineLanguageUI, {
    value: value,
    onChange: onChange,
    onClose: togglePopover,
    contentRef: contentRef
  }));
}

function InlineLanguageUI({
  value,
  contentRef,
  onChange,
  onClose
}) {
  const popoverAnchor = (0,external_wp_richText_namespaceObject.useAnchor)({
    editableContentElement: contentRef.current,
    settings: language_language
  });
  const [lang, setLang] = (0,external_wp_element_namespaceObject.useState)('');
  const [dir, setDir] = (0,external_wp_element_namespaceObject.useState)('ltr');
  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover, {
    className: "block-editor-format-toolbar__language-popover",
    anchor: popoverAnchor,
    placement: "bottom",
    onClose: onClose
  }, (0,external_wp_element_namespaceObject.createElement)("form", {
    className: "block-editor-format-toolbar__language-container-content",
    onSubmit: event => {
      event.preventDefault();
      onChange((0,external_wp_richText_namespaceObject.applyFormat)(value, {
        type: language_name,
        attributes: {
          lang,
          dir
        }
      }));
      onClose();
    }
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.TextControl, {
    label: language_title,
    value: lang,
    onChange: val => setLang(val),
    help: (0,external_wp_i18n_namespaceObject.__)('A valid language attribute, like "en" or "fr".')
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.SelectControl, {
    label: (0,external_wp_i18n_namespaceObject.__)('Text direction'),
    value: dir,
    options: [{
      label: (0,external_wp_i18n_namespaceObject.__)('Left to right'),
      value: 'ltr'
    }, {
      label: (0,external_wp_i18n_namespaceObject.__)('Right to left'),
      value: 'rtl'
    }],
    onChange: val => setDir(val)
  }), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.__experimentalHStack, {
    alignment: "right"
  }, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "primary",
    type: "submit",
    text: (0,external_wp_i18n_namespaceObject.__)('Apply')
  }))));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/default-formats.js
/**
 * Internal dependencies
 */













/* harmony default export */ const default_formats = ([bold, code_code, image_image, italic, build_module_link_link, strikethrough, underline, text_color_textColor, subscript_subscript, superscript_superscript, keyboard, unknown, language_language]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/format-library/build-module/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


default_formats.forEach(({
  name,
  ...settings
}) => (0,external_wp_richText_namespaceObject.registerFormatType)(name, settings));

(window.wp = window.wp || {}).formatLibrary = __webpack_exports__;
/******/ })()
;
=======
[bold, code, image_image, italic, link_link, strikethrough].forEach(function (_ref) {
  var name = _ref.name,
      settings = Object(objectWithoutProperties["a" /* default */])(_ref, ["name"]);

  return Object(external_this_wp_richText_["registerFormatType"])(name, settings);
});


/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "vuIU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _createClass; });
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
