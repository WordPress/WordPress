this["wp"] = this["wp"] || {}; this["wp"]["listReusableBlocks"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 489);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 1:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ 10:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ 16:
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

/***/ 19:
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

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 20:
/***/ (function(module, exports) {

(function() { module.exports = this["regeneratorRuntime"]; }());

/***/ }),

/***/ 21:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 22:
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

/***/ }),

/***/ 23:
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

/***/ 24:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(40);
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(16);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 3:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ 40:
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

/***/ 45:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _asyncToGenerator; });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ 47:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["apiFetch"]; }());

/***/ }),

/***/ 489:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__(1);

// EXTERNAL MODULE: external {"this":"regeneratorRuntime"}
var external_this_regeneratorRuntime_ = __webpack_require__(20);
var external_this_regeneratorRuntime_default = /*#__PURE__*/__webpack_require__.n(external_this_regeneratorRuntime_);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__(45);

// EXTERNAL MODULE: external {"this":"lodash"}
var external_this_lodash_ = __webpack_require__(2);

// EXTERNAL MODULE: external {"this":["wp","apiFetch"]}
var external_this_wp_apiFetch_ = __webpack_require__(47);
var external_this_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_apiFetch_);

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/file.js
/**
 * Downloads a file.
 *
 * @param {string} fileName    File Name.
 * @param {string} content     File Content.
 * @param {string} contentType File mime type.
 */
function download(fileName, content, contentType) {
  var file = new window.Blob([content], {
    type: contentType
  }); // IE11 can't use the click to download technique
  // we use a specific IE11 technique instead.

  if (window.navigator.msSaveOrOpenBlob) {
    window.navigator.msSaveOrOpenBlob(file, fileName);
  } else {
    var a = document.createElement('a');
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }
}
/**
 * Reads the textual content of the given file.
 *
 * @param  {File} file        File.
 * @return {Promise<string>}  Content of the file.
 */

function readTextFile(file) {
  var reader = new window.FileReader();
  return new Promise(function (resolve) {
    reader.onload = function () {
      resolve(reader.result);
    };

    reader.readAsText(file);
  });
}

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/export.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Export a reusable block as a JSON file.
 *
 * @param {number} id
 */

function exportReusableBlock(_x) {
  return _exportReusableBlock.apply(this, arguments);
}

function _exportReusableBlock() {
  _exportReusableBlock = Object(asyncToGenerator["a" /* default */])( /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(function _callee(id) {
    var postType, post, title, content, fileContent, fileName;
    return external_this_regeneratorRuntime_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/types/wp_block"
            });

          case 2:
            postType = _context.sent;
            _context.next = 5;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/".concat(postType.rest_base, "/").concat(id, "?context=edit")
            });

          case 5:
            post = _context.sent;
            title = post.title.raw;
            content = post.content.raw;
            fileContent = JSON.stringify({
              __file: 'wp_block',
              title: title,
              content: content
            }, null, 2);
            fileName = Object(external_this_lodash_["kebabCase"])(title) + '.json';
            download(fileName, fileContent, 'application/json');

          case 11:
          case "end":
            return _context.stop();
        }
      }
    }, _callee);
  }));
  return _exportReusableBlock.apply(this, arguments);
}

/* harmony default export */ var utils_export = (exportReusableBlock);

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__(3);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(21);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(22);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(16);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(23);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(24);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(19);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__(10);

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/utils/import.js



/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Import a reusable block from a JSON file.
 *
 * @param {File}     file File.
 * @return {Promise} Promise returning the imported reusable block.
 */

function importReusableBlock(_x) {
  return _importReusableBlock.apply(this, arguments);
}

function _importReusableBlock() {
  _importReusableBlock = Object(asyncToGenerator["a" /* default */])( /*#__PURE__*/external_this_regeneratorRuntime_default.a.mark(function _callee(file) {
    var fileContent, parsedContent, postType, reusableBlock;
    return external_this_regeneratorRuntime_default.a.wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            _context.next = 2;
            return readTextFile(file);

          case 2:
            fileContent = _context.sent;
            _context.prev = 3;
            parsedContent = JSON.parse(fileContent);
            _context.next = 10;
            break;

          case 7:
            _context.prev = 7;
            _context.t0 = _context["catch"](3);
            throw new Error('Invalid JSON file');

          case 10:
            if (!(parsedContent.__file !== 'wp_block' || !parsedContent.title || !parsedContent.content || !Object(external_this_lodash_["isString"])(parsedContent.title) || !Object(external_this_lodash_["isString"])(parsedContent.content))) {
              _context.next = 12;
              break;
            }

            throw new Error('Invalid Reusable Block JSON file');

          case 12:
            _context.next = 14;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/types/wp_block"
            });

          case 14:
            postType = _context.sent;
            _context.next = 17;
            return external_this_wp_apiFetch_default()({
              path: "/wp/v2/".concat(postType.rest_base),
              data: {
                title: parsedContent.title,
                content: parsedContent.content,
                status: 'publish'
              },
              method: 'POST'
            });

          case 17:
            reusableBlock = _context.sent;
            return _context.abrupt("return", reusableBlock);

          case 19:
          case "end":
            return _context.stop();
        }
      }
    }, _callee, null, [[3, 7]]);
  }));
  return _importReusableBlock.apply(this, arguments);
}

/* harmony default export */ var utils_import = (importReusableBlock);

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-form/index.js








function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = Object(getPrototypeOf["a" /* default */])(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = Object(getPrototypeOf["a" /* default */])(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return Object(possibleConstructorReturn["a" /* default */])(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */



var import_form_ImportForm = /*#__PURE__*/function (_Component) {
  Object(inherits["a" /* default */])(ImportForm, _Component);

  var _super = _createSuper(ImportForm);

  function ImportForm() {
    var _this;

    Object(classCallCheck["a" /* default */])(this, ImportForm);

    _this = _super.apply(this, arguments);
    _this.state = {
      isLoading: false,
      error: null,
      file: null
    };
    _this.isStillMounted = true;
    _this.onChangeFile = _this.onChangeFile.bind(Object(assertThisInitialized["a" /* default */])(_this));
    _this.onSubmit = _this.onSubmit.bind(Object(assertThisInitialized["a" /* default */])(_this));
    return _this;
  }

  Object(createClass["a" /* default */])(ImportForm, [{
    key: "componentWillUnmount",
    value: function componentWillUnmount() {
      this.isStillMounted = false;
    }
  }, {
    key: "onChangeFile",
    value: function onChangeFile(event) {
      this.setState({
        file: event.target.files[0]
      });
    }
  }, {
    key: "onSubmit",
    value: function onSubmit(event) {
      var _this2 = this;

      event.preventDefault();
      var file = this.state.file;
      var onUpload = this.props.onUpload;

      if (!file) {
        return;
      }

      this.setState({
        isLoading: true
      });
      utils_import(file).then(function (reusableBlock) {
        if (!_this2.isStillMounted) {
          return;
        }

        _this2.setState({
          isLoading: false
        });

        onUpload(reusableBlock);
      }).catch(function (error) {
        if (!_this2.isStillMounted) {
          return;
        }

        var uiMessage;

        switch (error.message) {
          case 'Invalid JSON file':
            uiMessage = Object(external_this_wp_i18n_["__"])('Invalid JSON file');
            break;

          case 'Invalid Reusable Block JSON file':
            uiMessage = Object(external_this_wp_i18n_["__"])('Invalid Reusable Block JSON file');
            break;

          default:
            uiMessage = Object(external_this_wp_i18n_["__"])('Unknown error');
        }

        _this2.setState({
          isLoading: false,
          error: uiMessage
        });
      });
    }
  }, {
    key: "render",
    value: function render() {
      var instanceId = this.props.instanceId;
      var _this$state = this.state,
          file = _this$state.file,
          isLoading = _this$state.isLoading,
          error = _this$state.error;
      var inputId = 'list-reusable-blocks-import-form-' + instanceId;
      return Object(external_this_wp_element_["createElement"])("form", {
        className: "list-reusable-blocks-import-form",
        onSubmit: this.onSubmit
      }, error && Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Notice"], {
        status: "error"
      }, error), Object(external_this_wp_element_["createElement"])("label", {
        htmlFor: inputId,
        className: "list-reusable-blocks-import-form__label"
      }, Object(external_this_wp_i18n_["__"])('File')), Object(external_this_wp_element_["createElement"])("input", {
        id: inputId,
        type: "file",
        onChange: this.onChangeFile
      }), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        type: "submit",
        isBusy: isLoading,
        disabled: !file || isLoading,
        isSecondary: true,
        className: "list-reusable-blocks-import-form__button"
      }, Object(external_this_wp_i18n_["_x"])('Import', 'button label')));
    }
  }]);

  return ImportForm;
}(external_this_wp_element_["Component"]);

/* harmony default export */ var import_form = (Object(external_this_wp_compose_["withInstanceId"])(import_form_ImportForm));

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/components/import-dropdown/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



function ImportDropdown(_ref) {
  var onUpload = _ref.onUpload;
  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Dropdown"], {
    position: "bottom right",
    contentClassName: "list-reusable-blocks-import-dropdown__content",
    renderToggle: function renderToggle(_ref2) {
      var isOpen = _ref2.isOpen,
          onToggle = _ref2.onToggle;
      return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
        "aria-expanded": isOpen,
        onClick: onToggle,
        isPrimary: true
      }, Object(external_this_wp_i18n_["__"])('Import from JSON'));
    },
    renderContent: function renderContent(_ref3) {
      var onClose = _ref3.onClose;
      return Object(external_this_wp_element_["createElement"])(import_form, {
        onUpload: Object(external_this_lodash_["flow"])(onClose, onUpload)
      });
    }
  });
}

/* harmony default export */ var import_dropdown = (ImportDropdown);

// CONCATENATED MODULE: ./node_modules/@wordpress/list-reusable-blocks/build-module/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


 // Setup Export Links

document.body.addEventListener('click', function (event) {
  if (!event.target.classList.contains('wp-list-reusable-blocks__export')) {
    return;
  }

  event.preventDefault();
  utils_export(event.target.dataset.id);
}); // Setup Import Form

document.addEventListener('DOMContentLoaded', function () {
  var button = document.querySelector('.page-title-action');

  if (!button) {
    return;
  }

  var showNotice = function showNotice() {
    var notice = document.createElement('div');
    notice.className = 'notice notice-success is-dismissible';
    notice.innerHTML = "<p>".concat(Object(external_this_wp_i18n_["__"])('Reusable block imported successfully!'), "</p>");
    var headerEnd = document.querySelector('.wp-header-end');

    if (!headerEnd) {
      return;
    }

    headerEnd.parentNode.insertBefore(notice, headerEnd);
  };

  var container = document.createElement('div');
  container.className = 'list-reusable-blocks__container';
  button.parentNode.insertBefore(container, button);
  Object(external_this_wp_element_["render"])(Object(external_this_wp_element_["createElement"])(import_dropdown, {
    onUpload: showNotice
  }), container);
});


/***/ })

/******/ });