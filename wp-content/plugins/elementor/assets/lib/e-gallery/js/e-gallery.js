/*! E-Gallery v1.2.0 by Elementor */
var EGallery =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/e-gallery.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

module.exports = _assertThisInitialized;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

module.exports = _classCallCheck;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/createClass.js":
/*!************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/createClass.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

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

module.exports = _createClass;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _getPrototypeOf(o) {
  module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

module.exports = _getPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/inherits.js":
/*!*********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/inherits.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf */ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js");

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
  if (superClass) setPrototypeOf(subClass, superClass);
}

module.exports = _inherits;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var _typeof = __webpack_require__(/*! ../helpers/typeof */ "./node_modules/@babel/runtime/helpers/typeof.js");

var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized */ "./node_modules/@babel/runtime/helpers/assertThisInitialized.js");

function _possibleConstructorReturn(self, call) {
  if (call && (_typeof(call) === "object" || typeof call === "function")) {
    return call;
  }

  return assertThisInitialized(self);
}

module.exports = _possibleConstructorReturn;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!***************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _setPrototypeOf(o, p) {
  module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}

module.exports = _setPrototypeOf;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _typeof3(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof3 = function _typeof3(obj) { return typeof obj; }; } else { _typeof3 = function _typeof3(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof3(obj); }

function _typeof2(obj) {
  if (typeof Symbol === "function" && _typeof3(Symbol.iterator) === "symbol") {
    _typeof2 = function _typeof2(obj) {
      return _typeof3(obj);
    };
  } else {
    _typeof2 = function _typeof2(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof3(obj);
    };
  }

  return _typeof2(obj);
}

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    module.exports = _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    module.exports = _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

module.exports = _typeof;

/***/ }),

/***/ "./src/js/e-gallery.js":
/*!*****************************!*\
  !*** ./src/js/e-gallery.js ***!
  \*****************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return EGallery; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _types_grid__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./types/grid */ "./src/js/types/grid.js");
/* harmony import */ var _types_justified__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./types/justified */ "./src/js/types/justified.js");
/* harmony import */ var _types_masonry__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./types/masonry */ "./src/js/types/masonry.js");
/* harmony import */ var _scss_e_gallery_scss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../scss/e-gallery.scss */ "./src/scss/e-gallery.scss");
/* harmony import */ var _scss_e_gallery_scss__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_scss_e_gallery_scss__WEBPACK_IMPORTED_MODULE_5__);







var EGallery =
/*#__PURE__*/
function () {
  function EGallery(userSettings) {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, EGallery);

    this.userSettings = userSettings;
    this.initGalleriesTypes();
    this.createGallery();
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(EGallery, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        container: null,
        items: null,
        type: 'grid',
        tags: [],
        overlay: false,
        overlayTemplate: '<div class="{{ classesPrefix }}{{ classes.overlayTitle }}">{{ title }}</div><div class="{{ classesPrefix }}{{ classes.overlayDescription }}">{{ description }}</div>',
        columns: 5,
        horizontalGap: 10,
        verticalGap: 10,
        rtl: false,
        animationDuration: 350,
        lazyLoad: false,
        classesPrefix: 'e-gallery-',
        classes: {
          container: 'container',
          item: 'item',
          image: 'image',
          overlay: 'overlay',
          overlayTitle: 'overlay__title',
          overlayDescription: 'overlay__description',
          link: 'link',
          firstRowItem: 'first-row-item',
          animated: '-animated',
          hidden: 'item--hidden',
          lazyLoad: '-lazyload',
          imageLoaded: 'image-loaded'
        },
        selectors: {
          items: '.e-gallery-item',
          image: '.e-gallery-image'
        },
        breakpoints: {
          1024: {
            horizontalGap: 5,
            verticalGap: 5,
            columns: 4
          },
          768: {
            horizontalGap: 1,
            verticalGap: 1,
            columns: 2
          }
        }
      };
    }
  }, {
    key: "initGalleriesTypes",
    value: function initGalleriesTypes() {
      this.galleriesTypes = {
        grid: _types_grid__WEBPACK_IMPORTED_MODULE_2__["default"],
        justified: _types_justified__WEBPACK_IMPORTED_MODULE_3__["default"],
        masonry: _types_masonry__WEBPACK_IMPORTED_MODULE_4__["default"]
      };
    }
  }, {
    key: "createGallery",
    value: function createGallery() {
      var settings = jQuery.extend(this.getDefaultSettings(), this.userSettings);
      var GalleryHandlerType = this.galleriesTypes[settings.type];
      this.galleryHandler = new GalleryHandlerType(settings);
    }
  }, {
    key: "setSettings",
    value: function setSettings(key, value) {
      this.galleryHandler.setSettings(key, value);
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.galleryHandler.destroy();
    }
  }]);

  return EGallery;
}();



/***/ }),

/***/ "./src/js/types/base.js":
/*!******************************!*\
  !*** ./src/js/types/base.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return BaseGalleryType; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./src/js/utils/index.js");




var BaseGalleryType =
/*#__PURE__*/
function () {
  function BaseGalleryType(settings) {
    var _this = this;

    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, BaseGalleryType);

    this.settings = jQuery.extend(true, this.getDefaultSettings(), settings);
    this.$container = jQuery(this.settings.container);
    this.timeouts = [];
    this.initElements();
    this.prepareGallery();
    var oldRunGallery = this.runGallery.bind(this);
    this.runGallery = this.debounce(function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      if (_this.settings.lazyLoad) {
        oldRunGallery.apply(void 0, args);
      } else {
        _this.allImagesPromise.then(function () {
          return oldRunGallery.apply(void 0, args);
        });
      }
    }, 300);

    if (this.settings.lazyLoad) {
      this.handleScroll = this.debounce(function () {
        return _this.lazyLoadImages();
      }, 16);
    }

    this.bindEvents();
    this.runGallery();
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(BaseGalleryType, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {};
    }
  }, {
    key: "getItemClass",
    value: function getItemClass(className) {
      return this.settings.classesPrefix + className;
    }
  }, {
    key: "initElements",
    value: function initElements() {
      this.elements = {
        $window: jQuery(window)
      };
      var directionClass = '-' + (this.settings.rtl ? 'rtl' : 'ltr');
      var containerClasses = this.getItemClass(this.settings.classes.container) + ' ' + this.getItemClass(this.settings.type) + ' ' + this.getItemClass(directionClass);

      if (this.settings.lazyLoad) {
        containerClasses += ' ' + this.getItemClass(this.settings.classes.lazyLoad);
      }

      this.$container.addClass(containerClasses);
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      this.elements.$window.on('resize', this.runGallery);

      if (this.settings.lazyLoad) {
        this.elements.$window.on('scroll', this.handleScroll);
      }
    }
  }, {
    key: "getNestedObjectData",
    value: function getNestedObjectData(object, key) {
      var keyStack = key.split('.'),
          currentKey = keyStack.splice(0, 1);

      if (!keyStack.length) {
        return {
          object: object,
          key: key
        };
      }

      return this.getNestedObjectData(object[currentKey], keyStack.join('.'));
    }
  }, {
    key: "getTemplateArgs",
    value: function getTemplateArgs(args, key) {
      var nestedObjectData = this.getNestedObjectData(args, key);
      return nestedObjectData.object[nestedObjectData.key] || '';
    }
  }, {
    key: "getCurrentBreakpoint",
    value: function getCurrentBreakpoint() {
      var breakpoints = Object.keys(this.settings.breakpoints).map(Number).sort(function (a, b) {
        return a - b;
      });
      var currentBreakpoint = 0;
      breakpoints.some(function (breakpoint) {
        if (innerWidth < breakpoint) {
          currentBreakpoint = breakpoint;
          return true;
        }

        return false;
      });
      return currentBreakpoint;
    }
  }, {
    key: "getCurrentDeviceSetting",
    value: function getCurrentDeviceSetting(settingKey) {
      var currentBreakpoint = this.getCurrentBreakpoint();

      if (currentBreakpoint) {
        return this.settings.breakpoints[currentBreakpoint][settingKey];
      }

      return this.settings[settingKey];
    }
  }, {
    key: "getActiveItems",
    value: function getActiveItems() {
      var returnIndexes = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      var activeTags = this.settings.tags,
          activeIndexes = [];

      if (!activeTags.length) {
        if (returnIndexes) {
          this.$items.each(function (index) {
            activeIndexes.push(index);
          });
          return activeIndexes;
        }

        return this.$items;
      }

      var filteredItems = this.$items.filter(function (index, item) {
        var itemTags = item.dataset.eGalleryTags;

        if (!itemTags) {
          return false;
        }

        itemTags = itemTags.split(/[ ,]+/);

        if (activeTags.some(function (tag) {
          return itemTags.includes(tag);
        })) {
          if (returnIndexes) {
            activeIndexes.push(index);
          }

          return true;
        }

        return false;
      });

      if (returnIndexes) {
        return activeIndexes;
      }

      return filteredItems;
    }
  }, {
    key: "getImageData",
    value: function getImageData(index) {
      if (this.settings.tags.length) {
        index = this.getActiveItems(true)[index];
      }

      return this.imagesData[index];
    }
  }, {
    key: "compileTemplate",
    value: function compileTemplate(template, args) {
      var _this2 = this;

      return template.replace(/{{([^}]+)}}/g, function (match, placeholder) {
        return _this2.getTemplateArgs(args, placeholder.trim());
      });
    }
  }, {
    key: "createOverlay",
    value: function createOverlay(overlayData) {
      var _this$settings = this.settings,
          classes = _this$settings.classes,
          overlayTemplate = _this$settings.overlayTemplate,
          $overlay = jQuery('<div>', {
        "class": this.getItemClass(classes.overlay)
      }),
          overlayContent = this.compileTemplate(overlayTemplate, jQuery.extend(true, this.settings, overlayData));
      $overlay.html(overlayContent);
      return $overlay;
    }
  }, {
    key: "createItem",
    value: function createItem(itemData) {
      var classes = this.settings.classes,
          $item = jQuery('<div>', {
        "class": this.getItemClass(classes.item),
        'data-e-gallery-tags': itemData.tags
      }),
          $image = jQuery('<div>', {
        "class": this.getItemClass(classes.image)
      });
      var $overlay;

      if (!this.settings.lazyLoad) {
        $image.css('background-image', 'url(' + itemData.thumbnail + ')');
      }

      if (this.settings.overlay) {
        $overlay = this.createOverlay(itemData);
      }

      var $contentWrapper = $item;

      if (itemData.url) {
        $contentWrapper = jQuery('<a>', {
          "class": this.getItemClass(classes.link),
          href: itemData.url
        });
        $item.html($contentWrapper);
      }

      $contentWrapper.html($image);

      if ($overlay) {
        $contentWrapper.append($overlay);
      }

      return $item;
    }
  }, {
    key: "debounce",
    value: function debounce(func, wait) {
      var _this3 = this;

      var timeout;
      return function () {
        for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
          args[_key2] = arguments[_key2];
        }

        clearTimeout(timeout);
        timeout = setTimeout(function () {
          return func.apply(void 0, args);
        }, wait);

        _this3.timeouts.push(timeout);
      };
    }
  }, {
    key: "buildGallery",
    value: function buildGallery() {
      var _this4 = this;

      var items = this.settings.items;
      this.$items = jQuery();
      items.forEach(function (item) {
        var $item = _this4.createItem(item);

        _this4.$items = _this4.$items.add($item);

        _this4.$container.append($item);
      });
    }
  }, {
    key: "loadImages",
    value: function loadImages() {
      var _this5 = this;

      var allPromises = [];
      this.settings.items.forEach(function (item, index) {
        var image = new Image(),
            promise = new Promise(function (resolve) {
          image.onload = resolve;
        });
        allPromises.push(promise);
        promise.then(function () {
          return _this5.calculateImageSize(image, index);
        });
        image.src = item.thumbnail;
      });
      this.allImagesPromise = Promise.all(allPromises);
    }
  }, {
    key: "lazyLoadImages",
    value: function lazyLoadImages() {
      var _this6 = this;

      if (this.lazyLoadComplete) {
        return;
      }

      var $items = this.getActiveItems(),
          itemsIndexes = this.getActiveItems(true);
      $items.each(function (index, item) {
        var itemData = _this6.settings.items[itemsIndexes[index]];

        if (itemData.loading || !Object(_utils__WEBPACK_IMPORTED_MODULE_2__["elementInView"])(item)) {
          return true;
        }

        itemData.loading = true;
        var $item = jQuery(item),
            image = new Image(),
            promise = new Promise(function (resolve) {
          image.onload = resolve;
        });
        promise.then(function () {
          $item.find(_this6.settings.selectors.image).css('background-image', 'url("' + itemData.thumbnail + '")').addClass(_this6.getItemClass(_this6.settings.classes.imageLoaded));
          _this6.loadedItemsCount++;

          if (_this6.loadedItemsCount === _this6.settings.items.length) {
            _this6.lazyLoadComplete = true;
          }
        });
        image.src = itemData.thumbnail;
        return true;
      });
    }
  }, {
    key: "calculateImageSize",
    value: function calculateImageSize(image, index) {
      this.imagesData[index] = {
        width: image.width,
        height: image.height,
        ratio: image.width / image.height
      };
    }
  }, {
    key: "createImagesData",
    value: function createImagesData() {
      var _this7 = this;

      this.settings.items.forEach(function (item, index) {
        return _this7.calculateImageSize(item, index);
      });
    }
  }, {
    key: "makeGalleryFromContent",
    value: function makeGalleryFromContent() {
      var selectors = this.settings.selectors,
          isLazyLoad = this.settings.lazyLoad,
          items = [];
      this.$items = this.$container.find(selectors.items);
      this.$items.each(function (index, item) {
        var $image = jQuery(item).find(selectors.image);
        items[index] = {
          thumbnail: $image.data('thumbnail')
        };

        if (isLazyLoad) {
          items[index].width = $image.data('width');
          items[index].height = $image.data('height');
        } else {
          $image.css('background-image', "url(\"".concat($image.data('thumbnail'), "\")"));
        }
      });
      this.settings.items = items;
    }
  }, {
    key: "prepareGallery",
    value: function prepareGallery() {
      if (this.settings.items) {
        this.buildGallery();
      } else {
        this.makeGalleryFromContent();
      }

      this.imagesData = [];

      if (this.settings.lazyLoad) {
        this.loadedItemsCount = 0;
        this.lazyLoadComplete = false;
        this.createImagesData();
      } else {
        this.loadImages();
      }
    }
  }, {
    key: "runGallery",
    value: function runGallery(refresh) {
      var _this8 = this;

      var containerStyle = this.$container[0].style;
      containerStyle.setProperty('--hgap', this.getCurrentDeviceSetting('horizontalGap') + 'px');
      containerStyle.setProperty('--vgap', this.getCurrentDeviceSetting('verticalGap') + 'px');
      containerStyle.setProperty('--animation-duration', this.settings.animationDuration + 'ms');
      this.$items.addClass(this.getItemClass(this.settings.classes.hidden));
      this.getActiveItems().removeClass(this.getItemClass(this.settings.classes.hidden));

      if (this.settings.lazyLoad) {
        setTimeout(function () {
          return _this8.lazyLoadImages();
        }, 300);
      }

      this.run(refresh);
    }
  }, {
    key: "setSettings",
    value: function setSettings(key, value) {
      var nestedObjectData = this.getNestedObjectData(this.settings, key);

      if (nestedObjectData.object) {
        nestedObjectData.object[nestedObjectData.key] = value;
        this.runGallery(true);
      }
    }
  }, {
    key: "unbindEvents",
    value: function unbindEvents() {
      this.elements.$window.off('resize', this.runGallery);
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.unbindEvents();
      this.$container.empty();
      this.timeouts.forEach(function (timeout) {
        return clearTimeout(timeout);
      });
    }
  }]);

  return BaseGalleryType;
}();



/***/ }),

/***/ "./src/js/types/grid.js":
/*!******************************!*\
  !*** ./src/js/types/grid.js ***!
  \******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Grid; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./base */ "./src/js/types/base.js");







var Grid =
/*#__PURE__*/
function (_BaseGalleryType) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(Grid, _BaseGalleryType);

  function Grid() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Grid);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(Grid).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Grid, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        aspectRatio: '16:9'
      };
    }
  }, {
    key: "setItemsPosition",
    value: function setItemsPosition() {
      var columns = this.getCurrentDeviceSetting('columns');
      this.getActiveItems().each(function (index, item) {
        item.style.setProperty('--column', index % columns);
        item.style.setProperty('--row', Math.floor(index / columns));
      });
    }
  }, {
    key: "setContainerSize",
    value: function setContainerSize() {
      var columns = this.getCurrentDeviceSetting('columns'),
          rows = Math.ceil(this.getActiveItems().length / columns),
          containerStyle = this.$container[0].style;
      containerStyle.setProperty('--columns', columns);
      containerStyle.setProperty('--rows', rows);
      var itemWidth = this.getActiveItems().width(),
          aspectRatio = this.settings.aspectRatio.split(':'),
          aspectRatioPercents = aspectRatio[1] / aspectRatio[0],
          itemHeight = aspectRatioPercents * itemWidth,
          totalHeight = itemHeight * rows + this.getCurrentDeviceSetting('horizontalGap') * (rows - 1),
          calculatedAspectRatio = totalHeight / this.$container.width() * 100;
      containerStyle.setProperty('--aspect-ratio', aspectRatioPercents * 100 + '%');
      containerStyle.setProperty('--container-aspect-ratio', calculatedAspectRatio + '%');
    }
  }, {
    key: "run",
    value: function run() {
      var _this = this;

      var animatedClass = this.getItemClass(this.settings.classes.animated);
      this.$container.addClass(animatedClass);
      setTimeout(function () {
        _this.setItemsPosition();

        _this.setContainerSize();

        setTimeout(function () {
          return _this.$container.removeClass(animatedClass);
        }, _this.settings.animationDuration);
      }, 50);
    }
  }]);

  return Grid;
}(_base__WEBPACK_IMPORTED_MODULE_5__["default"]);



/***/ }),

/***/ "./src/js/types/justified.js":
/*!***********************************!*\
  !*** ./src/js/types/justified.js ***!
  \***********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Justified; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./base */ "./src/js/types/base.js");







var Justified =
/*#__PURE__*/
function (_BaseGalleryType) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(Justified, _BaseGalleryType);

  function Justified() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Justified);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(Justified).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Justified, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        idealRowHeight: 200,
        lastRow: 'auto',
        breakpoints: {
          1024: {
            idealRowHeight: 150
          },
          768: {
            idealRowHeight: 100
          }
        }
      };
    }
  }, {
    key: "run",
    value: function run() {
      this.rowsHeights = [];
      this.rowsCount = 0;
      this.containerWidth = this.$container.width();
      this.makeJustifiedRow(0);
    }
  }, {
    key: "makeJustifiedRow",
    value: function makeJustifiedRow(startIndex) {
      var oldRowWidth = 0;

      for (var index = startIndex;; index++) {
        var imageData = this.getImageData(index);
        var itemComputedWidth = Math.round(this.getCurrentDeviceSetting('idealRowHeight') * imageData.ratio);

        if (itemComputedWidth > this.containerWidth) {
          itemComputedWidth = this.containerWidth;
        }

        var newRowWidth = oldRowWidth + itemComputedWidth;

        if (newRowWidth > this.containerWidth) {
          var oldDiff = this.containerWidth - oldRowWidth,
              newDiff = newRowWidth - this.containerWidth;

          if (oldDiff < newDiff) {
            this.fitImagesInContainer(startIndex, index, oldRowWidth);
            this.rowsCount++;
            this.makeJustifiedRow(index);
            break;
          }
        }

        var isLastItem = index === this.getActiveItems().length - 1;
        imageData.computedWidth = itemComputedWidth;

        if (isLastItem) {
          var lastRowMode = this.getCurrentDeviceSetting('lastRow');

          if ('hide' !== lastRowMode) {
            var totalRowWidth = 'fit' === lastRowMode || 0.7 <= newRowWidth / this.containerWidth ? newRowWidth : this.containerWidth;
            this.fitImagesInContainer(startIndex, index + 1, totalRowWidth);
          }

          this.inflateGalleryHeight();
          break;
        }

        oldRowWidth = newRowWidth;
      }
    }
  }, {
    key: "fitImagesInContainer",
    value: function fitImagesInContainer(startIndex, endIndex, rowWidth) {
      var gapCount = endIndex - startIndex - 1,
          $items = this.getActiveItems();
      var aggregatedWidth = 0;

      for (var index = startIndex; index < endIndex; index++) {
        var imageData = this.getImageData(index),
            percentWidth = imageData.computedWidth / rowWidth,
            item = $items.get(index),
            firstRowItemClass = this.getItemClass(this.settings.classes.firstRowItem);
        item.style.setProperty('--item-width', percentWidth);
        item.style.setProperty('--gap-count', gapCount);
        item.style.setProperty('--item-height', imageData.height / imageData.width * 100 + '%');
        item.style.setProperty('--item-start', aggregatedWidth);
        item.style.setProperty('--item-row-index', index - startIndex);
        aggregatedWidth += percentWidth;

        if (index === startIndex) {
          item.classList.add(firstRowItemClass);
          var imagePxWidth = percentWidth * (this.containerWidth - gapCount * this.getCurrentDeviceSetting('horizontalGap'));
          this.rowsHeights.push(imagePxWidth / imageData.ratio);
        } else {
          item.classList.remove(firstRowItemClass);
        }
      }
    }
  }, {
    key: "inflateGalleryHeight",
    value: function inflateGalleryHeight() {
      var totalRowsHeight = this.rowsHeights.reduce(function (total, item) {
        return total + item;
      }),
          finalContainerHeight = totalRowsHeight + this.rowsCount * this.getCurrentDeviceSetting('verticalGap'),
          containerAspectRatio = finalContainerHeight / this.containerWidth,
          percentRowsHeights = this.rowsHeights.map(function (rowHeight) {
        return rowHeight / finalContainerHeight * 100;
      });
      var currentRow = -1,
          accumulatedTop = 0;
      this.getActiveItems().each(function (index, item) {
        var itemRowIndex = item.style.getPropertyValue('--item-row-index'),
            isFirstItem = '0' === itemRowIndex;

        if (isFirstItem) {
          currentRow++;

          if (currentRow) {
            accumulatedTop += percentRowsHeights[currentRow - 1];
          }
        }

        item.style.setProperty('--item-top', accumulatedTop + '%');
        item.style.setProperty('--item-height', percentRowsHeights[currentRow] + '%');
        item.style.setProperty('--row', currentRow);
      });
      this.$container[0].style.setProperty('--container-aspect-ratio', containerAspectRatio);
    }
  }]);

  return Justified;
}(_base__WEBPACK_IMPORTED_MODULE_5__["default"]);



/***/ }),

/***/ "./src/js/types/masonry.js":
/*!*********************************!*\
  !*** ./src/js/types/masonry.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Masonry; });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/createClass.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/inherits.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _base__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./base */ "./src/js/types/base.js");







var Masonry =
/*#__PURE__*/
function (_BaseGalleryType) {
  _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4___default()(Masonry, _BaseGalleryType);

  function Masonry() {
    _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0___default()(this, Masonry);

    return _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_2___default()(this, _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_3___default()(Masonry).apply(this, arguments));
  }

  _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1___default()(Masonry, [{
    key: "run",
    value: function run(refresh) {
      var _this = this;

      var currentBreakpoint = this.getCurrentBreakpoint();

      if (!refresh && currentBreakpoint === this.currentBreakpoint) {
        return;
      }

      this.currentBreakpoint = currentBreakpoint;
      var heights = [],
          itemsInColumn = [],
          aggregatedHeights = [],
          columns = this.getCurrentDeviceSetting('columns'),
          containerWidth = this.$container.width(),
          horizontalGap = this.getCurrentDeviceSetting('horizontalGap'),
          itemWidth = (containerWidth - horizontalGap * (columns - 1)) / columns,
          $items = this.getActiveItems();
      var naturalColumnHeight = 0;

      for (var i = 0; i < columns; i++) {
        itemsInColumn[i] = 0;
        heights[i] = 0;
      }

      $items.each(function (index, item) {
        var imageData = _this.getImageData(index),
            itemHeight = itemWidth / imageData.ratio;

        var indexAtRow = index % columns;
        naturalColumnHeight = heights[indexAtRow];
        jQuery.each(heights, function (colNumber, currentColHeight) {
          if (currentColHeight && naturalColumnHeight > currentColHeight + 5) {
            naturalColumnHeight = currentColHeight;
            indexAtRow = colNumber;
          }
        });
        aggregatedHeights[index] = heights[indexAtRow];
        heights[indexAtRow] += itemHeight;
        item.style.setProperty('--item-height', imageData.height / imageData.width * 100 + '%');
        item.style.setProperty('--column', indexAtRow);
        item.style.setProperty('--items-in-column', itemsInColumn[indexAtRow]);
        itemsInColumn[indexAtRow]++;
      });
      var highestColumn = Math.max.apply(Math, heights),
          highestColumnIndex = heights.indexOf(highestColumn),
          rows = itemsInColumn[highestColumnIndex],
          highestColumnsGapsCount = rows - 1,
          containerAspectRatio = highestColumn / containerWidth;
      this.$container[0].style.setProperty('--columns', columns);
      this.$container[0].style.setProperty('--highest-column-gap-count', highestColumnsGapsCount);
      this.$container.css('padding-bottom', containerAspectRatio * 100 + '%');
      $items.each(function (index, item) {
        var percentHeight = aggregatedHeights[index] ? aggregatedHeights[index] / highestColumn * 100 : 0;
        item.style.setProperty('--percent-height', percentHeight + '%');
      });
    }
  }]);

  return Masonry;
}(_base__WEBPACK_IMPORTED_MODULE_5__["default"]);



/***/ }),

/***/ "./src/js/utils/element-in-view.js":
/*!*****************************************!*\
  !*** ./src/js/utils/element-in-view.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return elementInView; });
function elementInView(element) {
  var elementPart = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'top';
  var elementTop = element.getBoundingClientRect().top,
      elementHeight = element.offsetHeight,
      elementBottom = elementTop + elementHeight;
  var elementPosition;

  if ('middle' === elementPart) {
    elementPosition = elementTop + elementHeight / 2;
  } else if ('bottom' === elementPart) {
    elementPosition = elementBottom;
  } else {
    elementPosition = elementTop;
  }

  return elementPosition <= innerHeight && elementBottom >= 0;
}

/***/ }),

/***/ "./src/js/utils/index.js":
/*!*******************************!*\
  !*** ./src/js/utils/index.js ***!
  \*******************************/
/*! exports provided: elementInView */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _element_in_view__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./element-in-view */ "./src/js/utils/element-in-view.js");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "elementInView", function() { return _element_in_view__WEBPACK_IMPORTED_MODULE_0__["default"]; });



/***/ }),

/***/ "./src/scss/e-gallery.scss":
/*!*********************************!*\
  !*** ./src/scss/e-gallery.scss ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ })

/******/ })["default"];
//# sourceMappingURL=e-gallery.js.map