"use strict";
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["styleguide-app"],{

/***/ "../modules/styleguide/assets/js/frontend/app.js":
/*!*******************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/app.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = App;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _settings = __webpack_require__(/*! ./contexts/settings */ "../modules/styleguide/assets/js/frontend/contexts/settings.js");
var _activeContext = _interopRequireDefault(__webpack_require__(/*! ./contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./components/header */ "../modules/styleguide/assets/js/frontend/components/header.js"));
var _colorsArea = _interopRequireDefault(__webpack_require__(/*! ./components/areas/colors-area */ "../modules/styleguide/assets/js/frontend/components/areas/colors-area.js"));
var _fontsArea = _interopRequireDefault(__webpack_require__(/*! ./components/areas/fonts-area */ "../modules/styleguide/assets/js/frontend/components/areas/fonts-area.js"));
var _appWrapper = _interopRequireDefault(__webpack_require__(/*! ./components/app-wrapper */ "../modules/styleguide/assets/js/frontend/components/app-wrapper.js"));
var _templateObject;
var Content = _styledComponents.default.div(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tpadding: 48px 0;\n"])));
function App() {
  return /*#__PURE__*/_react.default.createElement(_settings.SettingsProvider, null, /*#__PURE__*/_react.default.createElement(_appWrapper.default, null, /*#__PURE__*/_react.default.createElement(_activeContext.default, null, /*#__PURE__*/_react.default.createElement(_header.default, null), /*#__PURE__*/_react.default.createElement(Content, null, /*#__PURE__*/_react.default.createElement(_colorsArea.default, null), /*#__PURE__*/_react.default.createElement(_fontsArea.default, null)))));
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/app-wrapper.js":
/*!**************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/app-wrapper.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = AppWrapper;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _settings = __webpack_require__(/*! ../contexts/settings */ "../modules/styleguide/assets/js/frontend/contexts/settings.js");
var _loader = _interopRequireDefault(__webpack_require__(/*! ./global/loader */ "../modules/styleguide/assets/js/frontend/components/global/loader.js"));
function AppWrapper(props) {
  var _useSettings = (0, _settings.useSettings)(),
    settings = _useSettings.settings,
    isReady = _useSettings.isReady;
  if (!isReady) {
    return /*#__PURE__*/_react.default.createElement(_loader.default, null);
  }
  var isDebug = settings.get('config').get('is_debug'),
    Wrapper = isDebug ? _react.default.StrictMode : _react.default.Fragment;
  return /*#__PURE__*/_react.default.createElement(Wrapper, null, props.children);
}
AppWrapper.propTypes = {
  children: PropTypes.oneOfType([PropTypes.node, PropTypes.arrayOf(PropTypes.node)]).isRequired
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/areas/area-title.js":
/*!*******************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/areas/area-title.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _templateObject;
var AreaTitle = _styledComponents.default.h2(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tcolor: var(--e-a-color-txt);\n\tfont-family: Roboto, sans-serif;\n\tfont-size: 30px;\n\tfont-weight: 400;\n\ttext-transform: capitalize;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tletter-spacing: 0;\n\tword-spacing: 0;\n\ttext-align: center;\n\tpadding: 0;\n\tmargin: 0 0 48px 0;\n"])));
var _default = exports["default"] = AreaTitle;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/areas/area.js":
/*!*************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/areas/area.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _settings = __webpack_require__(/*! ../../contexts/settings */ "../modules/styleguide/assets/js/frontend/contexts/settings.js");
var _loader = _interopRequireDefault(__webpack_require__(/*! ../global/loader */ "../modules/styleguide/assets/js/frontend/components/global/loader.js"));
var _divBase = _interopRequireDefault(__webpack_require__(/*! ../global/div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _areaTitle = _interopRequireDefault(__webpack_require__(/*! ./area-title */ "../modules/styleguide/assets/js/frontend/components/areas/area-title.js"));
var _section = _interopRequireDefault(__webpack_require__(/*! ../section */ "../modules/styleguide/assets/js/frontend/components/section.js"));
var _templateObject;
var Wrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\twidth: 100%;\n  \tpadding-top: 96px;\n\tmin-height: 100px;\n\n\t@media (max-width: 1024px) {\n      \tpadding-top: 50px;\n\t}\n"])));
var Area = _react.default.forwardRef(function (props, ref) {
  var config = props.config;
  var _useSettings = (0, _settings.useSettings)(),
    settings = _useSettings.settings,
    isReady = _useSettings.isReady;
  return /*#__PURE__*/_react.default.createElement(Wrapper, {
    ref: ref
  }, /*#__PURE__*/_react.default.createElement(_areaTitle.default, {
    name: config.type
  }, config.title), !isReady ? /*#__PURE__*/_react.default.createElement(_loader.default, null) : /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, config.sections.map(function (section) {
    var items = settings.get(config.type).get(section.type);
    return items.length ? /*#__PURE__*/_react.default.createElement(_section.default, {
      key: section.type,
      title: section.title,
      items: items,
      columns: section.columns,
      component: config.component,
      type: section.type
    }) : null;
  })));
});
Area.propTypes = {
  config: PropTypes.shape({
    type: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    sections: PropTypes.arrayOf(PropTypes.shape({
      type: PropTypes.string.isRequired,
      title: PropTypes.string.isRequired,
      columns: PropTypes.object
    })).isRequired,
    component: PropTypes.func.isRequired
  }).isRequired
};
var _default = exports["default"] = Area;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/areas/colors-area.js":
/*!********************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/areas/colors-area.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ColorsArea;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _activeContext = __webpack_require__(/*! ../../contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js");
var _area = _interopRequireDefault(__webpack_require__(/*! ./area */ "../modules/styleguide/assets/js/frontend/components/areas/area.js"));
var _color = _interopRequireDefault(__webpack_require__(/*! ../item/color */ "../modules/styleguide/assets/js/frontend/components/item/color.js"));
function ColorsArea() {
  var _useActiveContext = (0, _activeContext.useActiveContext)(),
    colorsAreaRef = _useActiveContext.colorsAreaRef;
  var areaConfig = {
    title: __('Global Colors', 'elementor'),
    type: 'colors',
    component: _color.default,
    sections: [{
      type: 'system_colors',
      title: __('System Colors', 'elementor'),
      columns: {
        desktop: 4,
        mobile: 2
      }
    }, {
      type: 'custom_colors',
      title: __('Custom Colors', 'elementor'),
      columns: {
        desktop: 6,
        mobile: 2
      }
    }]
  };
  return /*#__PURE__*/_react.default.createElement(_area.default, {
    ref: colorsAreaRef,
    config: areaConfig
  });
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/areas/fonts-area.js":
/*!*******************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/areas/fonts-area.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FontsArea;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _activeContext = __webpack_require__(/*! ../../contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js");
var _area = _interopRequireDefault(__webpack_require__(/*! ./area */ "../modules/styleguide/assets/js/frontend/components/areas/area.js"));
var _font = _interopRequireDefault(__webpack_require__(/*! ../item/font */ "../modules/styleguide/assets/js/frontend/components/item/font.js"));
function FontsArea() {
  var _useActiveContext = (0, _activeContext.useActiveContext)(),
    fontsAreaRef = _useActiveContext.fontsAreaRef;
  var areaConfig = {
    title: __('Global Fonts', 'elementor'),
    type: 'fonts',
    component: _font.default,
    sections: [{
      type: 'system_typography',
      title: __('System Fonts', 'elementor'),
      flex: 'column',
      columns: {
        desktop: 1,
        mobile: 1
      }
    }, {
      type: 'custom_typography',
      title: __('Custom Fonts', 'elementor'),
      flex: 'column',
      columns: {
        desktop: 1,
        mobile: 1
      }
    }]
  };
  return /*#__PURE__*/_react.default.createElement(_area.default, {
    ref: fontsAreaRef,
    config: areaConfig
  });
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/global/div-base.js":
/*!******************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/global/div-base.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _templateObject;
var DivBase = _styledComponents.default.div(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tbox-sizing: border-box;\n\tposition: relative;\n"])));
var _default = exports["default"] = DivBase;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/global/element-title.js":
/*!***********************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/global/element-title.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _templateObject;
var ElementTitle = _styledComponents.default.p(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tcolor: var(--e-a-color-txt);\n\tfont-family: Roboto, sans-serif;\n\tfont-size: 12px;\n\tfont-weight: 500;\n\ttext-transform: capitalize;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tline-height: 1.1em;\n\tletter-spacing: 0;\n\tword-spacing: 0;\n\tpadding: 0;\n\tmargin: 0;\n"])));
var _default = exports["default"] = ElementTitle;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/global/element-wrapper.js":
/*!*************************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/global/element-wrapper.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireWildcard(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _divBase = _interopRequireDefault(__webpack_require__(/*! ./div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _templateObject, _templateObject2, _templateObject3;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Wrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tdisplay: flex;\n\tflex-direction: column;\n\tgap: 12px;\n\talign-items: flex-start;\n\tborder: 1px solid transparent;\n\tborder-radius: 3px;\n\tpadding: 12px;\n\tcursor: pointer;\n\t", "\n\n\t&:hover:not(.active) {\n\t\tbackground-color: var(--e-a-bg-hover);\n\t\tborder-color: var(--e-a-border-color-bold);\n\t}\n\n\t&.active {\n\t\tbackground-color: var(--e-a-bg-active);\n\t\tborder-color: var(--e-a-border-color-accent);\n\t}\n\n\t@media (max-width: 767px) {\n\t\t", "\n\t}\n"])), function (_ref) {
  var _columns$desktop;
  var columns = _ref.columns;
  var columnWidth = 100 / ((_columns$desktop = columns.desktop) !== null && _columns$desktop !== void 0 ? _columns$desktop : 1);
  return (0, _styledComponents.css)(_templateObject2 || (_templateObject2 = (0, _taggedTemplateLiteral2.default)(["\n\t\t\tflex: 0 0 ", "%;\n\t\t"])), columnWidth);
}, function (_ref2) {
  var _columns$mobile;
  var columns = _ref2.columns;
  var columnWidth = 100 / ((_columns$mobile = columns.mobile) !== null && _columns$mobile !== void 0 ? _columns$mobile : 1);
  return (0, _styledComponents.css)(_templateObject3 || (_templateObject3 = (0, _taggedTemplateLiteral2.default)(["\n\t\t\t\tflex: 0 0 ", "%;\n\t\t\t"])), columnWidth);
});
var ElementWrapper = _react.default.forwardRef(function (props, ref) {
  var isActive = props.isActive,
    children = props.children;
  return /*#__PURE__*/_react.default.createElement(Wrapper, (0, _extends2.default)({}, props, {
    ref: ref,
    className: isActive ? 'active' : ''
  }), children);
});
var _default = exports["default"] = ElementWrapper;
ElementWrapper.propTypes = {
  isActive: PropTypes.bool,
  children: PropTypes.oneOfType([PropTypes.node, PropTypes.arrayOf(PropTypes.node)])
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/global/inner-wrapper.js":
/*!***********************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/global/inner-wrapper.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _divBase = _interopRequireDefault(__webpack_require__(/*! ./div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _templateObject;
var innerWrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tdisplay: flex;\n\talign-items: center;\n\twidth: 100%;\n\tmax-width: 1140px;\n\tmargin: auto;\n\tflex-wrap: wrap;\n\tflex-direction: ", ";\n\n\t@media (max-width: 1140px) {\n\t\tpadding: 0 15px;\n\t}\n\n\t@media (max-width: 767px) {\n\t\tpadding: 0 13px;\n\t}\n"])), function (props) {
  var _props$flexDirection;
  return (_props$flexDirection = props.flexDirection) !== null && _props$flexDirection !== void 0 ? _props$flexDirection : 'row';
});
var _default = exports["default"] = innerWrapper;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/global/loader.js":
/*!****************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/global/loader.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Loader;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Loader() {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-styleguide-loader"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-loading eicon-animation-spin"
  }));
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/header.js":
/*!*********************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/header.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Header;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _activeContext = __webpack_require__(/*! ../contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js");
var _divBase = _interopRequireDefault(__webpack_require__(/*! ./global/div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _innerWrapper = _interopRequireDefault(__webpack_require__(/*! ./global/inner-wrapper */ "../modules/styleguide/assets/js/frontend/components/global/inner-wrapper.js"));
var _templateObject, _templateObject2, _templateObject3, _templateObject4;
var Button = _styledComponents.default.button.attrs(function (props) {
  return {
    'data-e-active': props.isActive ? true : null
  };
})(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tfont-size: 16px;\n\theight: 100%;\n\tfont-weight: 500;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tline-height: 1.5em;\n\tletter-spacing: 0;\n\tcolor: var(--e-a-color-txt);\n\tborder: none;\n\tbackground: none;\n\ttext-transform: capitalize;\n\tfont-family: Roboto, sans-serif;\n\tpadding: 0;\n\n\t&:hover, &[data-e-active='true'], &:focus {\n\t\toutline: none;\n\t\tbackground: none;\n\t\tcolor: var(--e-a-color-txt-accent);\n\t}\n"])));
var AreaButton = function AreaButton(props) {
  var _useActiveContext = (0, _activeContext.useActiveContext)(),
    activeArea = _useActiveContext.activeArea,
    activateArea = _useActiveContext.activateArea;
  var area = props.area,
    children = props.children;
  var onClick = function onClick() {
    activateArea(area);
  };

  // TODO: Add hover/active states

  return /*#__PURE__*/_react.default.createElement(Button, {
    variant: "transparent",
    size: "s",
    onClick: onClick,
    isActive: area === activeArea
  }, children);
};
var Wrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject2 || (_templateObject2 = (0, _taggedTemplateLiteral2.default)(["\n\tposition: fixed;\n\ttop: 0;\n\tleft: 0;\n\twidth: 100%;\n\theight: 48px;\n\tdisplay: flex;\n\tbackground: var(--e-a-bg-default);\n\tborder-bottom: 1px solid var(--e-a-border-color-bold);\n\tz-index: 1;\n"])));
var ButtonsWrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject3 || (_templateObject3 = (0, _taggedTemplateLiteral2.default)(["\n\tdisplay: flex;\n\tjustify-content: flex-end;\n\tflex-grow: 1;\n\tgap: 20px;\n"])));
var Title = _styledComponents.default.h2(_templateObject4 || (_templateObject4 = (0, _taggedTemplateLiteral2.default)(["\n\tcolor: var(--e-a-color-txt-accent);\n\tfont-family: Roboto, sans-serif;\n\tfont-size: 16px;\n\tfont-weight: 600;\n\ttext-transform: capitalize;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tline-height: 1.2em;\n\tletter-spacing: 0;\n\tword-spacing: 0;\n\tmargin: 0;\n"])));
function Header() {
  return /*#__PURE__*/_react.default.createElement(Wrapper, null, /*#__PURE__*/_react.default.createElement(_innerWrapper.default, null, /*#__PURE__*/_react.default.createElement(Title, null, __('Show global settings', 'elementor')), /*#__PURE__*/_react.default.createElement(ButtonsWrapper, null, /*#__PURE__*/_react.default.createElement(AreaButton, {
    area: 'colors'
  }, __('Colors', 'elementor')), /*#__PURE__*/_react.default.createElement(AreaButton, {
    area: 'fonts'
  }, __('Fonts', 'elementor')))));
}
AreaButton.propTypes = {
  area: PropTypes.string.isRequired,
  children: PropTypes.node.isRequired
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/item/color.js":
/*!*************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/item/color.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Color;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _divBase = _interopRequireDefault(__webpack_require__(/*! ../global/div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _elementTitle = _interopRequireDefault(__webpack_require__(/*! ../global/element-title */ "../modules/styleguide/assets/js/frontend/components/global/element-title.js"));
var _elementWrapper = _interopRequireDefault(__webpack_require__(/*! ../global/element-wrapper */ "../modules/styleguide/assets/js/frontend/components/global/element-wrapper.js"));
var _activeContext = __webpack_require__(/*! ../../contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js");
var _templateObject, _templateObject2;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Content = (0, _styledComponents.default)(_divBase.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tdisplay: flex;\n\twidth: 100%;\n\theight: 100px;\n\tbackground-color: ", ";\n\tborder: 1px solid var(--e-a-border-color-focus);\n\tborder-radius: 3px;\n\talign-items: end;\n"])), function (props) {
  return props.hex;
});
var HexString = _styledComponents.default.p(_templateObject2 || (_templateObject2 = (0, _taggedTemplateLiteral2.default)(["\n\tcolor: var(--e-a-color-txt-invert);\n\tfont-family: Roboto, sans-serif;\n\theight: 12px;\n\tfont-size: 12px;\n\tfont-weight: 500;\n\ttext-transform: uppercase;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tline-height: 1.1em;\n\tletter-spacing: 0;\n\tword-spacing: 0;\n\tmargin: 12px;\n"])));
function Color(props) {
  var _useActiveContext = (0, _activeContext.useActiveContext)(),
    activeElement = _useActiveContext.activeElement,
    activateElement = _useActiveContext.activateElement,
    getElementControl = _useActiveContext.getElementControl;
  var item = props.item,
    type = props.type;
  var source = 'color';
  var _id = item._id,
    title = item.title,
    hex = item.color;
  var elementControl = getElementControl(type, source, _id);
  var ref = (0, _react.useRef)(null);
  (0, _react.useEffect)(function () {
    if (elementControl === activeElement) {
      ref.current.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'center'
      });
    }
  }, [activeElement]);
  var onClick = function onClick() {
    activateElement(type, source, _id);
  };
  return /*#__PURE__*/_react.default.createElement(_elementWrapper.default, {
    columns: props.columns,
    ref: ref,
    isActive: elementControl === activeElement,
    onClick: onClick
  }, /*#__PURE__*/_react.default.createElement(_elementTitle.default, null, title), /*#__PURE__*/_react.default.createElement(Content, {
    hex: hex
  }, /*#__PURE__*/_react.default.createElement(HexString, null, hex)));
}
Color.propTypes = {
  item: PropTypes.shape({
    _id: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    color: PropTypes.string
  }).isRequired,
  type: PropTypes.string.isRequired,
  columns: PropTypes.shape({
    desktop: PropTypes.number,
    mobile: PropTypes.number
  })
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/item/font.js":
/*!************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/item/font.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Font;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _activeContext = __webpack_require__(/*! ../../contexts/active-context */ "../modules/styleguide/assets/js/frontend/contexts/active-context.js");
var _settings = __webpack_require__(/*! ../../contexts/settings */ "../modules/styleguide/assets/js/frontend/contexts/settings.js");
var _elementWrapper = _interopRequireDefault(__webpack_require__(/*! ../global/element-wrapper */ "../modules/styleguide/assets/js/frontend/components/global/element-wrapper.js"));
var _elementTitle = _interopRequireDefault(__webpack_require__(/*! ../global/element-title */ "../modules/styleguide/assets/js/frontend/components/global/element-title.js"));
var _templateObject, _templateObject2;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Title = (0, _styledComponents.default)(_elementTitle.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tfont-size: 18px;\n"])));
var Content = _styledComponents.default.p.withConfig({
  shouldForwardProp: function shouldForwardProp(prop) {
    return 'style' !== prop;
  }
})(_templateObject2 || (_templateObject2 = (0, _taggedTemplateLiteral2.default)(["\n\t", ";\n"])), function (_ref) {
  var style = _ref.style;
  var styleObjectToString = function styleObjectToString(obj) {
    return Object.keys(obj).reduce(function (acc, key) {
      return acc + "".concat(key, ": ").concat(obj[key], ";");
    }, '');
  };
  return "\n\t\t\t".concat(styleObjectToString(style.style), "\n\n\t\t\t@media (max-width: 1024px) {\n\t\t\t\t").concat(styleObjectToString(style.tablet), "\n\t\t\t}\n\n\t\t\t@media (max-width: 767px) {\n\t\t\t\t").concat(styleObjectToString(style.mobile), "\n\t\t\t}\n\t\t");
});
var parseFontToStyle = function parseFontToStyle(font, fallbackFamily) {
  var defaultKeyParser = function defaultKeyParser(key) {
    return key.replace('typography_', '').replace('_', '-');
  };
  var fallbackLowered = fallbackFamily.toLowerCase();
  var familyParser = function familyParser(value) {
    return value ? value + ", ".concat(fallbackLowered) : fallbackLowered;
  };
  var sizeParser = function sizeParser(value) {
    if (!value || !value.size) {
      return '';
    }
    return "".concat(value.size).concat(value.unit);
  };
  var defaultParser = function defaultParser(value) {
    return value;
  };
  var allowedProperties = {
    typography_font_family: {
      valueParser: familyParser,
      keyParser: defaultKeyParser
    },
    typography_font_size: {
      valueParser: sizeParser,
      keyParser: defaultKeyParser
    },
    typography_letter_spacing: {
      valueParser: sizeParser,
      keyParser: defaultKeyParser
    },
    typography_line_height: {
      valueParser: sizeParser,
      keyParser: defaultKeyParser
    },
    typography_word_spacing: {
      valueParser: sizeParser,
      keyParser: defaultKeyParser
    },
    typography_font_style: {
      valueParser: defaultParser,
      keyParser: defaultKeyParser
    },
    typography_font_weight: {
      valueParser: defaultParser,
      keyParser: defaultKeyParser
    },
    typography_text_transform: {
      valueParser: defaultParser,
      keyParser: defaultKeyParser
    },
    typography_text_decoration: {
      valueParser: defaultParser,
      keyParser: defaultKeyParser
    }
  };
  var responsiveProperties = ['typography_font_size', 'typography_letter_spacing', 'typography_line_height', 'typography_word_spacing'];
  var reducer = function reducer(acc, property, screen) {
    var parsers = allowedProperties[property];
    var key = parsers.keyParser(property);
    var keyInFontObject = property + (screen ? '_' + screen : '');
    var value = parsers.valueParser(font[keyInFontObject]);
    if (value) {
      acc[key] = value;
    }
    return acc;
  };
  var style = Object.keys(allowedProperties).reduce(function (acc, property) {
    return reducer(acc, property, '');
  }, {});
  var tablet = responsiveProperties.reduce(function (acc, property) {
    return reducer(acc, property, 'tablet');
  }, {});
  var mobile = responsiveProperties.reduce(function (acc, property) {
    return reducer(acc, property, 'mobile');
  }, {});
  return {
    style: style,
    tablet: tablet,
    mobile: mobile
  };
};
function Font(props) {
  var _useActiveContext = (0, _activeContext.useActiveContext)(),
    activeElement = _useActiveContext.activeElement,
    activateElement = _useActiveContext.activateElement,
    getElementControl = _useActiveContext.getElementControl;
  var item = props.item,
    type = props.type;
  var source = 'typography';
  var _id = item._id,
    title = item.title;
  var elementControl = getElementControl(type, source, _id);
  var ref = (0, _react.useRef)(null);
  var _useSettings = (0, _settings.useSettings)(),
    settings = _useSettings.settings,
    isReady = _useSettings.isReady;
  var generateStyle = (0, _react.useMemo)(function () {
    if (!isReady) {
      return '';
    }
    return parseFontToStyle(item, settings.get('fonts').get('fallback_font'));
  }, [item, settings]);
  var onClick = function onClick() {
    activateElement(type, source, _id);
  };
  (0, _react.useEffect)(function () {
    if (elementControl === activeElement) {
      ref.current.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'center'
      });
    }
  }, [activeElement]);
  return /*#__PURE__*/_react.default.createElement(_elementWrapper.default, {
    columns: props.columns,
    ref: ref,
    isActive: elementControl === activeElement,
    onClick: onClick
  }, /*#__PURE__*/_react.default.createElement(Title, null, title), /*#__PURE__*/_react.default.createElement(Content, {
    style: generateStyle
  }, __('The five boxing wizards jump quickly.', 'elementor')));
}
Font.propTypes = {
  item: PropTypes.shape({
    _id: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    color: PropTypes.string
  }).isRequired,
  type: PropTypes.string.isRequired,
  columns: PropTypes.shape({
    desktop: PropTypes.number,
    mobile: PropTypes.number
  })
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/section-title.js":
/*!****************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/section-title.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _styledComponents = _interopRequireDefault(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _templateObject;
var SectionTitle = _styledComponents.default.h3(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tpadding: 16px 12px;\n\tborder-style: solid;\n\tborder-width: 0 0 1px 0;\n\tborder-color: var(--e-a-border-color-bold);\n\tcolor: var(--e-a-color-txt);\n\tfont-family: Roboto, sans-serif;\n\tfont-size: 16px;\n\tfont-weight: 500;\n\ttext-transform: capitalize;\n\tfont-style: normal;\n\ttext-decoration: none;\n\tline-height: 1.5em;\n\tletter-spacing: 0;\n\tword-spacing: 0;\n\tmargin: 0 auto 25px;\n\twidth: 100%;\n\tmax-width: 1140px;\n"])));
var _default = exports["default"] = SectionTitle;

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/components/section.js":
/*!**********************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/components/section.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Section;
var _taggedTemplateLiteral2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/taggedTemplateLiteral */ "../node_modules/@babel/runtime/helpers/taggedTemplateLiteral.js"));
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _styledComponents = _interopRequireWildcard(__webpack_require__(/*! styled-components */ "../node_modules/styled-components/dist/styled-components.browser.esm.js"));
var _sectionTitle = _interopRequireDefault(__webpack_require__(/*! ./section-title */ "../modules/styleguide/assets/js/frontend/components/section-title.js"));
var _divBase = _interopRequireDefault(__webpack_require__(/*! ./global/div-base */ "../modules/styleguide/assets/js/frontend/components/global/div-base.js"));
var _innerWrapper = _interopRequireDefault(__webpack_require__(/*! ./global/inner-wrapper */ "../modules/styleguide/assets/js/frontend/components/global/inner-wrapper.js"));
var _templateObject, _templateObject2, _templateObject3;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Wrapper = (0, _styledComponents.default)(_divBase.default)(_templateObject || (_templateObject = (0, _taggedTemplateLiteral2.default)(["\n\tmargin-top: 55px;\n"])));
var Content = (0, _styledComponents.default)(_divBase.default)(_templateObject2 || (_templateObject2 = (0, _taggedTemplateLiteral2.default)(["\n\tdisplay: flex;\n\twidth: 100%;\n\n\t", ";\n"])), function (_ref) {
  var flex = _ref.flex;
  return flex && (0, _styledComponents.css)(_templateObject3 || (_templateObject3 = (0, _taggedTemplateLiteral2.default)(["\n\t\tflex-direction: ", ";\n\t\tflex-wrap: ", ";\n\t"])), 'column' === flex ? 'column' : 'row', 'column' === flex ? 'nowrap' : 'wrap');
});
function Section(props) {
  var title = props.title,
    items = props.items,
    columns = props.columns,
    Item = props.component,
    type = props.type,
    _props$flex = props.flex,
    flex = _props$flex === void 0 ? 'row' : _props$flex;
  return /*#__PURE__*/_react.default.createElement(Wrapper, null, /*#__PURE__*/_react.default.createElement(_sectionTitle.default, null, title), /*#__PURE__*/_react.default.createElement(_innerWrapper.default, null, /*#__PURE__*/_react.default.createElement(Content, {
    flex: flex
  }, items.map(function (item) {
    return /*#__PURE__*/_react.default.createElement(Item, {
      key: item._id,
      item: item,
      type: type ? type : null,
      columns: columns
    });
  }))));
}
Section.propTypes = {
  title: PropTypes.string.isRequired,
  items: PropTypes.array.isRequired,
  columns: PropTypes.shape({
    desktop: PropTypes.number,
    mobile: PropTypes.number
  }),
  component: PropTypes.func.isRequired,
  type: PropTypes.string,
  flex: PropTypes.oneOf(['row', 'column'])
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/contexts/active-context.js":
/*!***************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/contexts/active-context.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.ActiveContext = void 0;
exports.useActiveContext = useActiveContext;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _settings = __webpack_require__(/*! ./settings */ "../modules/styleguide/assets/js/frontend/contexts/settings.js");
var _useIntersectionObserver = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-intersection-observer */ "../modules/styleguide/assets/js/frontend/hooks/use-intersection-observer.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var ActiveContext = exports.ActiveContext = (0, _react.createContext)(null);
var ActiveProvider = function ActiveProvider(props) {
  var _useState = (0, _react.useState)({
      element: '',
      area: ''
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    active = _useState2[0],
    setActive = _useState2[1];
  var colorsAreaRef = (0, _react.useRef)(null);
  var fontsAreaRef = (0, _react.useRef)(null);
  var _useSettings = (0, _settings.useSettings)(),
    isReady = _useSettings.isReady;
  var _useIntersectionObser = (0, _useIntersectionObserver.default)(function (intersectingArea) {
      if (colorsAreaRef.current === intersectingArea.target) {
        activateArea('colors', {
          scroll: false
        });
        return;
      }
      if (fontsAreaRef.current === intersectingArea.target) {
        activateArea('fonts', {
          scroll: false
        });
      }
    }),
    setObservedElements = _useIntersectionObser.setObservedElements;
  var activateElement = function activateElement(type, source, id) {
    if ('color' === source) {
      window.top.$e.route('panel/global/global-colors', {
        activeControl: "".concat(type, "/").concat(id, "/color")
      }, {
        history: false
      });
    }
    if ('typography' === source) {
      window.top.$e.route('panel/global/global-typography', {
        activeControl: "".concat(type, "/").concat(id, "/typography_typography")
      }, {
        history: false
      });
    }
  };
  var getElementControl = function getElementControl(type, source, id) {
    if ('color' === source) {
      return "".concat(type, "/").concat(id, "/color");
    }
    if ('typography' === source) {
      return "".concat(type, "/").concat(id, "/typography_typography");
    }
  };
  var activateArea = function activateArea(area) {
    var _ref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
      _ref$scroll = _ref.scroll,
      scroll = _ref$scroll === void 0 ? true : _ref$scroll;
    if (scroll) {
      scrollToArea(area);
    }
    setActive(function (prevState) {
      return _objectSpread(_objectSpread({}, prevState), {}, {
        area: area
      });
    });
  };
  var scrollToArea = function scrollToArea(area) {
    var ref = 'colors' === area ? colorsAreaRef : fontsAreaRef;
    ref.current.scrollIntoView({
      behavior: 'smooth',
      block: 'start',
      inline: 'start'
    });
  };
  (0, _react.useEffect)(function () {
    if (window.top.$e.routes.is('panel/global/global-colors')) {
      scrollToArea('colors');
    }
    if (window.top.$e.routes.is('panel/global/global-typography')) {
      scrollToArea('fonts');
    }
  }, []);
  (0, _react.useEffect)(function () {
    if (!isReady) {
      return;
    }
    setObservedElements([colorsAreaRef.current, fontsAreaRef.current]);
    window.top.$e.routes.on('run:after', function (component, route, args) {
      if ('panel/global/global-typography' === route) {
        setActive(function () {
          return {
            area: 'fonts',
            element: args.activeControl
          };
        });
      }
      if ('panel/global/global-colors' === route) {
        setActive(function () {
          return {
            area: 'colors',
            element: args.activeControl
          };
        });
      }
    });
  }, [isReady]);
  var value = {
    activeElement: active.element,
    activeArea: active.area,
    activateElement: activateElement,
    activateArea: activateArea,
    colorsAreaRef: colorsAreaRef,
    fontsAreaRef: fontsAreaRef,
    getElementControl: getElementControl
  };
  return /*#__PURE__*/_react.default.createElement(ActiveContext.Provider, (0, _extends2.default)({
    value: value
  }, props));
};
var _default = exports["default"] = ActiveProvider;
function useActiveContext() {
  return (0, _react.useContext)(ActiveContext);
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/contexts/settings.js":
/*!*********************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/contexts/settings.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useSettings = exports.SettingsProvider = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useDebouncedCallback = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-debounced-callback */ "../modules/styleguide/assets/js/frontend/hooks/use-debounced-callback.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var SettingsContext = (0, _react.createContext)(null);

/**
 * @return {{settings: Map, isReady: boolean}|null} context
 */
var useSettings = exports.useSettings = function useSettings() {
  return (0, _react.useContext)(SettingsContext);
};
var SettingsProvider = exports.SettingsProvider = function SettingsProvider(props) {
  var _useState = (0, _react.useState)('idle'),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    status = _useState2[0],
    setStatus = _useState2[1];
  var _useState3 = (0, _react.useState)(new Map()),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    settings = _useState4[0],
    _setSettings = _useState4[1];
  var settingsRef = (0, _react.useRef)(settings);

  // TODO: Use `useDebouncedCallback` instead of `useCallback`.
  var setSettings = function setSettings(newSettings) {
    settingsRef.current = newSettings;
    _setSettings(newSettings);
  };
  (0, _react.useEffect)(function () {
    setStatus('loaded');
  }, [settings]);
  var getInitialSettings = function getInitialSettings() {
    setStatus('loading');
    var kitSettings = elementor.documents.getCurrent().config.settings.settings;
    var map = new Map([['colors', new Map([['system_colors', kitSettings.system_colors], ['custom_colors', kitSettings.custom_colors]])], ['fonts', new Map([['system_typography', kitSettings.system_typography], ['custom_typography', kitSettings.custom_typography], ['fallback_font', kitSettings.default_generic_fonts]])], ['config', new Map([['is_debug', elementorCommon.config.isElementorDebug]])]]);
    setSettings(map);
  };
  var onCommandEvent = (0, _react.useCallback)(function (event) {
    switch (event.detail.command) {
      case 'document/elements/settings':
        onSettingsChange(event.detail.args);
        break;
      case 'document/repeater/insert':
        onInsert(event.detail.args);
        break;
      case 'document/repeater/remove':
        onRemove(event.detail.args);
        break;
      default:
        break;
    }
  }, []);

  /**
   * Triggered when a color or font is changed.
   * Has a 100ms debounce.
   *
   * @param {{container: {model: {attributes: {name: string}}, id: number}, settings: {}}} args
   */
  var onSettingsChange = (0, _useDebouncedCallback.default)(function (args) {
    var name = args.container.model.attributes.name;
    var newSettings = new Map(settingsRef.current);
    var _iterator = _createForOfIteratorHelper(newSettings.entries()),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var _step$value = (0, _slicedToArray2.default)(_step.value, 2),
          group = _step$value[0],
          groupSettings = _step$value[1];
        if (!groupSettings.has(name)) {
          continue;
        }
        if (Array.isArray(groupSettings.get(name))) {
          var index = groupSettings.get(name).findIndex(function (item) {
            return item._id === args.container.id;
          });
          if (-1 === index) {
            return;
          }
          newSettings.get(group).get(name)[index] = _objectSpread(_objectSpread({}, groupSettings.get(name)[index]), args.settings);
        } else {
          newSettings.get(group).set(name, args.settings);
        }
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
    setSettings(newSettings);
  }, 100);

  /**
   * Triggered when a new custom color or font is created.
   *
   * @param {{name: string, model: string, options: {at: number}}} args
   */
  var onInsert = function onInsert(args) {
    var name = args.name;
    var newSettings = new Map(settingsRef.current);
    var _iterator2 = _createForOfIteratorHelper(newSettings.entries()),
      _step2;
    try {
      for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
        var _args$options;
        var _step2$value = (0, _slicedToArray2.default)(_step2.value, 2),
          group = _step2$value[0],
          groupSettings = _step2$value[1];
        if (!groupSettings.has(name)) {
          continue;
        }
        var newArray = (0, _toConsumableArray2.default)(groupSettings.get(name));
        var at = undefined === ((_args$options = args.options) === null || _args$options === void 0 ? void 0 : _args$options.at) ? newArray.length : args.options.at;
        newSettings.get(group).set(name, [].concat((0, _toConsumableArray2.default)(newArray.slice(0, at)), [args.model], (0, _toConsumableArray2.default)(newArray.slice(at))));
      }
    } catch (err) {
      _iterator2.e(err);
    } finally {
      _iterator2.f();
    }
    setSettings(newSettings);
  };

  /**
   * Triggered when a custom color or font is removed.
   *
   * @param {{name: string, index: number}} args
   */
  var onRemove = function onRemove(args) {
    var name = args.name;
    var newSettings = new Map(settingsRef.current);
    var _iterator3 = _createForOfIteratorHelper(newSettings.entries()),
      _step3;
    try {
      for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
        var _step3$value = (0, _slicedToArray2.default)(_step3.value, 2),
          group = _step3$value[0],
          groupSettings = _step3$value[1];
        if (!groupSettings.has(name)) {
          continue;
        }
        var newArray = (0, _toConsumableArray2.default)(groupSettings.get(name));
        newSettings.get(group).set(name, newArray.filter(function (item, index) {
          return index !== args.index;
        }));
      }
    } catch (err) {
      _iterator3.e(err);
    } finally {
      _iterator3.f();
    }
    setSettings(newSettings);
  };
  (0, _react.useEffect)(function () {
    getInitialSettings();
    window.top.addEventListener('elementor/commands/run/after', onCommandEvent, {
      passive: true
    });
    return function () {
      window.top.removeEventListener('elementor/commands/run/after', onCommandEvent);
    };
  }, []);
  var value = {
    settings: settings,
    isReady: 'loaded' === status
  };
  return /*#__PURE__*/_react.default.createElement(SettingsContext.Provider, (0, _extends2.default)({
    value: value
  }, props));
};

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/hooks/use-debounced-callback.js":
/*!********************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/hooks/use-debounced-callback.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useDebouncedCallback;
var _react = __webpack_require__(/*! react */ "react");
function useDebouncedCallback(callback, wait) {
  var timeout = (0, _react.useRef)();
  return (0, _react.useCallback)(function () {
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    var later = function later() {
      clearTimeout(timeout.current);
      callback.apply(void 0, args);
    };
    clearTimeout(timeout.current);
    timeout.current = setTimeout(later, wait);
  }, [callback, wait]);
}

/***/ }),

/***/ "../modules/styleguide/assets/js/frontend/hooks/use-intersection-observer.js":
/*!***********************************************************************************!*\
  !*** ../modules/styleguide/assets/js/frontend/hooks/use-intersection-observer.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useIntersectionObserver;
var _react = __webpack_require__(/*! react */ "react");
function useIntersectionObserver(callback) {
  var observer;
  var elements = [];
  (0, _react.useEffect)(function () {
    observer = new IntersectionObserver(function (entries) {
      var intersectingArea = entries.find(function (entry) {
        return entry.isIntersecting;
      });
      if (intersectingArea) {
        callback(intersectingArea);
      }
    }, {});
    return function () {
      observer.disconnect();
    };
  }, []);
  var observe = function observe() {
    if (elements.length !== 0) {
      elements.forEach(function (element) {
        if (element) {
          observer.observe(element);
        }
      });
    }
  };
  var unobserve = function unobserve() {
    if (elements.length !== 0) {
      elements.forEach(function (element) {
        if (element) {
          observer.unobserve(element);
        }
      });
    }
  };
  var setObservedElements = function setObservedElements(observedElements) {
    unobserve();
    elements = observedElements;
    observe();
  };
  return {
    setObservedElements: setObservedElements
  };
}

/***/ })

}]);
//# sourceMappingURL=styleguide-app.77392704cadf8bc1ca69.bundle.js.map