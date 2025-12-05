/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../node_modules/@babel/runtime/helpers/defineProperty.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/defineProperty.js ***!
  \****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperty(e, r, t) {
  return (r = toPropertyKey(r)) in e ? Object.defineProperty(e, r, {
    value: t,
    enumerable: !0,
    configurable: !0,
    writable: !0
  }) : e[r] = t, e;
}
module.exports = _defineProperty, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/interopRequireDefault.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _interopRequireDefault(e) {
  return e && e.__esModule ? e : {
    "default": e
  };
}
module.exports = _interopRequireDefault, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPrimitive.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPrimitive.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function toPrimitive(t, r) {
  if ("object" != _typeof(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != _typeof(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}
module.exports = toPrimitive, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toPropertyKey.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toPropertyKey.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var toPrimitive = __webpack_require__(/*! ./toPrimitive.js */ "../node_modules/@babel/runtime/helpers/toPrimitive.js");
function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == _typeof(i) ? i : i + "";
}
module.exports = toPropertyKey, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/typeof.js":
/*!********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/typeof.js ***!
  \********************************************************/
/***/ ((module) => {

function _typeof(o) {
  "@babel/helpers - typeof";

  return module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _typeof(o);
}
module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*********************************************!*\
  !*** ../assets/dev/js/admin/hints/media.js ***!
  \*********************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
(function (_elementorAdminHints, _wp) {
  if (!((_elementorAdminHints = elementorAdminHints) !== null && _elementorAdminHints !== void 0 && _elementorAdminHints.mediaHint)) {
    return;
  }
  if (!((_wp = wp) !== null && _wp !== void 0 && (_wp = _wp.media) !== null && _wp !== void 0 && (_wp = _wp.view) !== null && _wp !== void 0 && (_wp = _wp.Attachment) !== null && _wp !== void 0 && _wp.Details)) {
    return;
  }
  wp.media.view.Attachment.Details = wp.media.view.Attachment.Details.extend({
    _tmpl: "<div class=\"e-hint__container\" style=\"clear:both\" data-event=\"<%= dismissible %>\">\n\t\t<div class=\"elementor-control-notice elementor-control-notice-type-<%= type %>\" data-display=\"<%= display %>\">\n\t\t\t<div class=\"elementor-control-notice-icon\">\n\t\t\t\t<svg width=\"18\" height=\"18\" viewBox=\"0 0 18 18\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n\t\t\t\t\t<path d=\"M2.25 9H3M9 2.25V3M15 9H15.75M4.2 4.2L4.725 4.725M13.8 4.2L13.275 4.725M7.27496 12.75H10.725M6.75 12C6.12035 11.5278 5.65525 10.8694 5.42057 10.1181C5.1859 9.36687 5.19355 8.56082 5.44244 7.81415C5.69133 7.06748 6.16884 6.41804 6.80734 5.95784C7.44583 5.49764 8.21294 5.25 9 5.25C9.78706 5.25 10.5542 5.49764 11.1927 5.95784C11.8312 6.41804 12.3087 7.06748 12.5576 7.81415C12.8065 8.56082 12.8141 9.36687 12.5794 10.1181C12.3448 10.8694 11.8796 11.5278 11.25 12C10.9572 12.2899 10.7367 12.6446 10.6064 13.0355C10.4761 13.4264 10.4397 13.8424 10.5 14.25C10.5 14.6478 10.342 15.0294 10.0607 15.3107C9.77936 15.592 9.39782 15.75 9 15.75C8.60218 15.75 8.22064 15.592 7.93934 15.3107C7.65804 15.0294 7.5 14.6478 7.5 14.25C7.56034 13.8424 7.52389 13.4264 7.3936 13.0355C7.2633 12.6446 7.04282 12.2899 6.75 12Z\" stroke=\"currentColor\" stroke-width=\"1.2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"></path>\n\t\t\t\t</svg>\n\t\t\t</div>\n\t\t\t<div class=\"elementor-control-notice-main action-handler\" data-event=\"<%= button_event %>\" data-settings=\"<%= button_data %>\">\n\t\t\t\t<div class=\"elementor-control-notice-main-content\"><%= content %></div>\n\t\t\t\t<div class=\"elementor-control-notice-main-actions\">\n\t\t\t\t<% if ( typeof(button_text) !== \"undefined\" ) { %>\n\t\t\t\t\t<button class=\"e-btn e-<%= type %> e-btn-1\">\n\t\t\t\t\t\t<%= button_text %>\n\t\t\t\t\t</button>\n\t\t\t\t<% } %>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t\t<button class=\"elementor-control-notice-dismiss\" data-event=\"<%= dismissible %>\" aria-label=\"<%= dismiss %>\">\n\t\t\t\t<i class=\"eicon eicon-close\" aria-hidden=\"true\"></i>\n\t\t\t</button>\n\t\t</div>\n\t</div>",
    template: function template(view) {
      // Get the template and parse it
      var html = wp.media.template('attachment-details')(view); // The template to extend
      var dom = document.createElement('div');
      dom.innerHTML = html;
      if (!this.shouldDisplayHint(view)) {
        return dom.innerHTML;
      }
      var hint = document.createElement('div'); // Create a new element
      hint.classList.add('e-hint'); // Add a class to the element for styling
      hint.innerHTML = _.template(this._tmpl)(elementorAdminHints.mediaHint); // Add the content to the new element

      // Insert the new element at the correct spot
      var details = dom.querySelector('.attachment-info');
      details.appendChild(hint); // Add new element at the correct spot

      return dom.innerHTML;
    },
    events: _objectSpread(_objectSpread({}, wp.media.view.Attachment.Details.prototype.events), {}, {
      'click .elementor-control-notice-dismiss': 'dismiss',
      'click .e-hint__container a': 'onHintAnchorClick',
      'click .e-hint__container button.e-btn-1': 'onHintAction'
    }),
    shouldDisplayHint: function shouldDisplayHint(view) {
      var _elementorAdminHints2;
      if (!elementorAdminHints || !((_elementorAdminHints2 = elementorAdminHints) !== null && _elementorAdminHints2 !== void 0 && _elementorAdminHints2.mediaHint)) {
        return false;
      }
      if (window.elementorHints !== undefined) {
        return false;
      }
      if (view.type !== 'image') {
        return false;
      }
      if (elementorAdminHints.mediaHint.display) {
        return true;
      }
      return this.imageNotOptimized(view);
    },
    imageNotOptimized: function imageNotOptimized(attachment) {
      var checks = {
        height: 1080,
        width: 1920,
        filesizeInBytes: 100000
      };
      return Object.keys(checks).some(function (key) {
        var value = attachment[key] || false;
        return value && value > checks[key];
      });
    },
    onHintAction: function onHintAction(event) {
      event.preventDefault();
      var b64Settings = event.target.closest('.action-handler').dataset.settings;
      var settings = atob(b64Settings);
      var _JSON$parse = JSON.parse(settings),
        _JSON$parse$action_ur = _JSON$parse.action_url,
        actionURL = _JSON$parse$action_ur === void 0 ? null : _JSON$parse$action_ur;
      if (actionURL) {
        window.open(actionURL, '_blank');
      }
      this.dismiss(event);
    },
    onHintAnchorClick: function onHintAnchorClick(event) {
      this.dismiss(event);
    },
    dismiss: function dismiss(event) {
      elementorCommon.ajax.addRequest('dismissed_editor_notices', {
        data: {
          dismissId: event.target.closest('.e-hint__container').dataset.event
        }
      });
      this.hideHint(event);
    },
    hideHint: function hideHint(event) {
      event.target.closest('.e-hint__container').remove();
      window.elementorHints = {};
    }
  });
})();
})();

/******/ })()
;
//# sourceMappingURL=media-hints.js.map