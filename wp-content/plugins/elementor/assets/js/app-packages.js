/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/assets/js/hooks/use-action.js":
/*!********************************************!*\
  !*** ../app/assets/js/hooks/use-action.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useAction;
function useAction() {
  return {
    backToDashboard: function backToDashboard() {
      if (window.top === window) {
        window.top.location = elementorAppConfig.admin_url;
      } else {
        // Iframe.
        window.top.$e.run('app/close');
      }
    },
    backToReferrer: function backToReferrer() {
      if (window.top === window) {
        // Directly - in case that the return_url is the login-page, the target should be the admin-page and not the login-page again.
        window.top.location = elementorAppConfig.return_url.includes(elementorAppConfig.login_url) ? elementorAppConfig.admin_url : elementorAppConfig.return_url;
      } else {
        // Iframe.
        window.top.$e.run('app/close');
      }
    }
  };
}

/***/ }),

/***/ "../app/assets/js/hooks/use-ajax.js":
/*!******************************************!*\
  !*** ../app/assets/js/hooks/use-ajax.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useAjax;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function useAjax() {
  var _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    ajax = _useState2[0],
    setAjax = _useState2[1],
    initialStatusKey = 'initial',
    uploadInitialState = {
      status: initialStatusKey,
      isComplete: false,
      response: null
    },
    _useState3 = (0, _react.useState)(uploadInitialState),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    ajaxState = _useState4[0],
    setAjaxState = _useState4[1],
    ajaxActions = {
      reset: function reset() {
        return setAjaxState(initialStatusKey);
      }
    };
  var runRequest = /*#__PURE__*/function () {
    var _ref = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(config) {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            return _context.abrupt("return", new Promise(function (resolve, reject) {
              var formData = new FormData();
              if (config.data) {
                for (var key in config.data) {
                  formData.append(key, config.data[key]);
                }
                if (!config.data.nonce) {
                  formData.append('_nonce', elementorCommon.config.ajax.nonce);
                }
              }
              var options = _objectSpread(_objectSpread({
                type: 'post',
                url: elementorCommon.config.ajax.url,
                headers: {},
                cache: false,
                contentType: false,
                processData: false
              }, config), {}, {
                data: formData,
                success: function success(response) {
                  resolve(response);
                },
                error: function error(_error) {
                  reject(_error);
                }
              });
              jQuery.ajax(options);
            }));
          case 1:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    return function runRequest(_x) {
      return _ref.apply(this, arguments);
    };
  }();
  (0, _react.useEffect)(function () {
    if (ajax) {
      runRequest(ajax).then(function (response) {
        var status = response.success ? 'success' : 'error';
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            status: status,
            response: response === null || response === void 0 ? void 0 : response.data
          });
        });
      }).catch(function (error) {
        var _error$responseJSON;
        var response = 408 === error.status ? 'timeout' : (_error$responseJSON = error.responseJSON) === null || _error$responseJSON === void 0 ? void 0 : _error$responseJSON.data;
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            status: 'error',
            response: response
          });
        });
      }).finally(function () {
        setAjaxState(function (prevState) {
          return _objectSpread(_objectSpread({}, prevState), {}, {
            isComplete: true
          });
        });
      });
    }
  }, [ajax]);
  return {
    ajax: ajax,
    setAjax: setAjax,
    ajaxState: ajaxState,
    ajaxActions: ajaxActions,
    runRequest: runRequest
  };
}

/***/ }),

/***/ "../app/assets/js/hooks/use-confirm-action.js":
/*!****************************************************!*\
  !*** ../app/assets/js/hooks/use-confirm-action.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useConfirmAction;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
var _useIntroduction2 = _interopRequireDefault(__webpack_require__(/*! ./use-introduction */ "../app/assets/js/hooks/use-introduction.js"));
function useConfirmAction(_ref) {
  var action = _ref.action,
    _ref$doNotShowAgainKe = _ref.doNotShowAgainKey,
    doNotShowAgainKey = _ref$doNotShowAgainKe === void 0 ? null : _ref$doNotShowAgainKe;
  var _useIntroduction = (0, _useIntroduction2.default)(doNotShowAgainKey),
    shouldNotShowAgain = _useIntroduction.isViewed,
    markAsShouldNotShowAgain = _useIntroduction.markAsViewed;
  var _useState = (0, _react.useState)({
      isOpen: false,
      actionArgs: []
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    dialogState = _useState2[0],
    setDialogState = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    doNotShowAgainCheckboxState = _useState4[0],
    setDoNotShowAgainCheckboxState = _useState4[1];
  return {
    checkbox: {
      isChecked: doNotShowAgainCheckboxState,
      setIsChecked: setDoNotShowAgainCheckboxState
    },
    dialog: {
      isOpen: dialogState.isOpen,
      approve: function approve() {
        action.apply(void 0, (0, _toConsumableArray2.default)(dialogState.actionArgs));
        if (doNotShowAgainCheckboxState && doNotShowAgainKey) {
          markAsShouldNotShowAgain();
        }
        setDialogState({
          isOpen: false,
          actionArgs: []
        });
      },
      dismiss: function dismiss() {
        setDialogState({
          isOpen: false,
          actionArgs: []
        });
      }
    },
    runAction: function runAction() {
      for (var _len = arguments.length, actionArgs = new Array(_len), _key = 0; _key < _len; _key++) {
        actionArgs[_key] = arguments[_key];
      }
      if (shouldNotShowAgain) {
        action.apply(void 0, actionArgs);
        return;
      }
      setDialogState({
        isOpen: true,
        actionArgs: actionArgs
      });
    }
  };
}

/***/ }),

/***/ "../app/assets/js/hooks/use-introduction.js":
/*!**************************************************!*\
  !*** ../app/assets/js/hooks/use-introduction.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useIntroduction;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _react = __webpack_require__(/*! react */ "react");
function useIntroduction(key) {
  var _window$elementorAppC;
  var _useState = (0, _react.useState)(!!((_window$elementorAppC = window.elementorAppConfig) !== null && _window$elementorAppC !== void 0 && (_window$elementorAppC = _window$elementorAppC.user) !== null && _window$elementorAppC !== void 0 && (_window$elementorAppC = _window$elementorAppC.introduction) !== null && _window$elementorAppC !== void 0 && _window$elementorAppC[key])),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isViewed = _useState2[0],
    setIsViewed = _useState2[1];
  function markAsViewed() {
    if (!key) {
      return Promise.reject();
    }
    return new Promise(function (resolve, reject) {
      if (isViewed) {
        reject();
      }
      elementorCommon.ajax.addRequest('introduction_viewed', {
        data: {
          introductionKey: key
        },
        error: function error() {
          return reject();
        },
        success: function success() {
          var _window$elementorAppC2;
          setIsViewed(true);
          if ((_window$elementorAppC2 = window.elementorAppConfig) !== null && _window$elementorAppC2 !== void 0 && (_window$elementorAppC2 = _window$elementorAppC2.user) !== null && _window$elementorAppC2 !== void 0 && _window$elementorAppC2.introduction) {
            window.elementorAppConfig.user.introduction[key] = true;
          }
          resolve();
        }
      });
    });
  }
  return {
    isViewed: isViewed,
    markAsViewed: markAsViewed
  };
}

/***/ }),

/***/ "../app/assets/js/hooks/use-page-title.js":
/*!************************************************!*\
  !*** ../app/assets/js/hooks/use-page-title.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = usePageTitle;
var _react = __webpack_require__(/*! react */ "react");
function usePageTitle(_ref) {
  var title = _ref.title,
    prefix = _ref.prefix;
  var prefixRef = (0, _react.useRef)(prefix);
  (0, _react.useEffect)(function () {
    if (!prefix) {
      prefixRef.current = __('Elementor', 'elementor');
    }
    document.title = "".concat(prefixRef.current, " | ").concat(title);
  }, [title, prefix]);
}

/***/ }),

/***/ "../app/assets/js/hooks/use-query-params.js":
/*!**************************************************!*\
  !*** ../app/assets/js/hooks/use-query-params.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useQueryParams;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function useQueryParams() {
  var _location$hash$match;
  var urlSearchParams = new URLSearchParams(window.location.search),
    urlParams = Object.fromEntries(urlSearchParams.entries()),
    hashValue = (_location$hash$match = location.hash.match(/\?(.+)/)) === null || _location$hash$match === void 0 ? void 0 : _location$hash$match[1],
    hashParams = {};
  if (hashValue) {
    hashValue.split('&').forEach(function (pair) {
      var _pair$split = pair.split('='),
        _pair$split2 = (0, _slicedToArray2.default)(_pair$split, 2),
        key = _pair$split2[0],
        value = _pair$split2[1];
      hashParams[key] = value;
    });
  }

  // Merging the URL params with the hash params.
  var queryParams = _objectSpread(_objectSpread({}, urlParams), hashParams);
  return {
    getAll: function getAll() {
      return queryParams;
    }
  };
}

/***/ }),

/***/ "../app/assets/js/layout/content.js":
/*!******************************************!*\
  !*** ../app/assets/js/layout/content.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Content;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Content(props) {
  return /*#__PURE__*/_react.default.createElement("main", {
    className: "eps-app__content ".concat(props.className)
  }, props.children);
}
Content.propTypes = {
  children: PropTypes.any,
  className: PropTypes.string
};
Content.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/layout/footer.js":
/*!*****************************************!*\
  !*** ../app/assets/js/layout/footer.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Footer;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Footer(props) {
  return /*#__PURE__*/_react.default.createElement("footer", {
    className: "eps-app__footer"
  }, props.children);
}
Footer.propTypes = {
  children: PropTypes.object
};

/***/ }),

/***/ "../app/assets/js/layout/header-button.js":
/*!************************************************!*\
  !*** ../app/assets/js/layout/header-button.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ../ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var Button = exports["default"] = /*#__PURE__*/function (_BaseButton) {
  function Button() {
    (0, _classCallCheck2.default)(this, Button);
    return _callSuper(this, Button, arguments);
  }
  (0, _inherits2.default)(Button, _BaseButton);
  return (0, _createClass2.default)(Button, [{
    key: "getCssId",
    value: function getCssId() {
      return "eps-app-header-btn-" + _superPropGet(Button, "getCssId", this, 3)([]);
    }
  }, {
    key: "getClassName",
    value: function getClassName() {
      // Avoid using the 'eps-app__header-btn' class to make sure it is not override custom styles.
      if (!this.props.includeHeaderBtnClass) {
        return _superPropGet(Button, "getClassName", this, 3)([]);
      }
      return "eps-app__header-btn " + _superPropGet(Button, "getClassName", this, 3)([]);
    }
  }]);
}(_button.default);
(0, _defineProperty2.default)(Button, "defaultProps", Object.assign({} /* Clone */, _button.default.defaultProps, {
  hideText: true,
  includeHeaderBtnClass: true
}));

/***/ }),

/***/ "../app/assets/js/layout/header-buttons.js":
/*!*************************************************!*\
  !*** ../app/assets/js/layout/header-buttons.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = HeaderButtons;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _useAction = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-action */ "../app/assets/js/hooks/use-action.js"));
var _headerButton = _interopRequireDefault(__webpack_require__(/*! ./header-button */ "../app/assets/js/layout/header-button.js"));
function HeaderButtons(props) {
  var action = (0, _useAction.default)();
  var actionOnClose = function actionOnClose() {
    if (props.onClose) {
      props.onClose();
    } else {
      action.backToDashboard();
    }
  };
  var tools = '';
  if (props.buttons.length) {
    var buttons = props.buttons.map(function (button) {
      return /*#__PURE__*/_react.default.createElement(_headerButton.default, (0, _extends2.default)({
        key: button.id
      }, button));
    });
    tools = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, buttons);
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__header-buttons"
  }, /*#__PURE__*/_react.default.createElement(_headerButton.default, {
    text: __('Close', 'elementor'),
    icon: "eicon-close",
    className: "eps-app__close-button",
    onClick: actionOnClose
  }), tools);
}
HeaderButtons.propTypes = {
  buttons: PropTypes.arrayOf(PropTypes.object),
  onClose: PropTypes.func
};
HeaderButtons.defaultProps = {
  buttons: []
};

/***/ }),

/***/ "../app/assets/js/layout/header.js":
/*!*****************************************!*\
  !*** ../app/assets/js/layout/header.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Header;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! ../ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _headerButtons = _interopRequireDefault(__webpack_require__(/*! ./header-buttons */ "../app/assets/js/layout/header-buttons.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
function Header(props) {
  (0, _usePageTitle.default)({
    title: props.title
  });
  var TitleTag = 'span',
    titleAttrs = {};
  if (props.titleRedirectRoute) {
    TitleTag = 'a';
    titleAttrs = {
      href: "#".concat(props.titleRedirectRoute),
      target: '_self'
    };
  }
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "eps-app__header"
  }, /*#__PURE__*/_react.default.createElement(TitleTag, (0, _extends2.default)({
    className: "eps-app__logo-title-wrapper"
  }, titleAttrs), /*#__PURE__*/_react.default.createElement("i", {
    className: "eps-app__logo eicon-elementor"
  }), /*#__PURE__*/_react.default.createElement("h1", {
    className: "eps-app__title"
  }, props.title)), /*#__PURE__*/_react.default.createElement(_headerButtons.default, {
    buttons: props.buttons,
    onClose: props.onClose
  }));
}
Header.propTypes = {
  title: PropTypes.string,
  titleRedirectRoute: PropTypes.string,
  buttons: PropTypes.arrayOf(PropTypes.object),
  onClose: PropTypes.func
};
Header.defaultProps = {
  buttons: []
};

/***/ }),

/***/ "../app/assets/js/layout/page.js":
/*!***************************************!*\
  !*** ../app/assets/js/layout/page.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Page;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../app/assets/js/layout/header.js"));
var _sidebar = _interopRequireDefault(__webpack_require__(/*! ./sidebar */ "../app/assets/js/layout/sidebar.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ./content */ "../app/assets/js/layout/content.js"));
var _footer = _interopRequireDefault(__webpack_require__(/*! ./footer */ "../app/assets/js/layout/footer.js"));
function Page(props) {
  var AppSidebar = function AppSidebar() {
      if (!props.sidebar) {
        return;
      }
      return /*#__PURE__*/_react.default.createElement(_sidebar.default, null, props.sidebar);
    },
    AppFooter = function AppFooter() {
      if (!props.footer) {
        return;
      }
      return /*#__PURE__*/_react.default.createElement(_footer.default, null, props.footer);
    };
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__lightbox ".concat(props.className)
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app"
  }, /*#__PURE__*/_react.default.createElement(_header.default, {
    title: props.title,
    buttons: props.headerButtons,
    titleRedirectRoute: props.titleRedirectRoute,
    onClose: props.onClose
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__main"
  }, AppSidebar(), /*#__PURE__*/_react.default.createElement(_content.default, null, props.content)), AppFooter()));
}
Page.propTypes = {
  title: PropTypes.string,
  titleRedirectRoute: PropTypes.string,
  className: PropTypes.string,
  headerButtons: PropTypes.arrayOf(PropTypes.object),
  sidebar: PropTypes.object,
  content: PropTypes.object.isRequired,
  footer: PropTypes.object,
  onClose: PropTypes.func
};
Page.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/layout/sidebar.js":
/*!******************************************!*\
  !*** ../app/assets/js/layout/sidebar.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Sidebar;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Sidebar(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__sidebar"
  }, props.children);
}
Sidebar.propTypes = {
  children: PropTypes.object
};

/***/ }),

/***/ "../app/assets/js/molecules/collapse-content.js":
/*!******************************************************!*\
  !*** ../app/assets/js/molecules/collapse-content.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CollapseContent;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function CollapseContent(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-app-collapse-content"
  }, props.children);
}
CollapseContent.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any
};
CollapseContent.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/molecules/collapse-context.js":
/*!******************************************************!*\
  !*** ../app/assets/js/molecules/collapse-context.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.CollapseContext = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var CollapseContext = exports.CollapseContext = _react.default.createContext();

/***/ }),

/***/ "../app/assets/js/molecules/collapse-toggle.js":
/*!*****************************************************!*\
  !*** ../app/assets/js/molecules/collapse-toggle.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CollapseToggle;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _collapseContext = __webpack_require__(/*! ./collapse-context */ "../app/assets/js/molecules/collapse-context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function CollapseToggle(props) {
  var context = (0, _react.useContext)(_collapseContext.CollapseContext),
    style = {
      '--e-app-collapse-toggle-icon-spacing': (0, _utils.pxToRem)(props.iconSpacing)
    },
    classNameBase = 'e-app-collapse-toggle',
    classes = [classNameBase, (0, _defineProperty2.default)({}, classNameBase + '--active', props.active)],
    attrs = {
      style: style,
      className: (0, _utils.arrayToClassName)(classes)
    };
  if (props.active) {
    attrs.onClick = function () {
      return context.toggle();
    };
  }
  return /*#__PURE__*/_react.default.createElement("div", attrs, props.children, props.active && props.showIcon && /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-caret-down e-app-collapse-toggle__icon"
  }));
}
CollapseToggle.propTypes = {
  className: PropTypes.string,
  iconSpacing: PropTypes.number,
  showIcon: PropTypes.bool,
  active: PropTypes.bool,
  children: PropTypes.any
};
CollapseToggle.defaultProps = {
  className: '',
  iconSpacing: 20,
  showIcon: true,
  active: true
};

/***/ }),

/***/ "../app/assets/js/molecules/collapse.js":
/*!**********************************************!*\
  !*** ../app/assets/js/molecules/collapse.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Collapse;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _collapseContext = __webpack_require__(/*! ./collapse-context */ "../app/assets/js/molecules/collapse-context.js");
var _collapseToggle = _interopRequireDefault(__webpack_require__(/*! ./collapse-toggle */ "../app/assets/js/molecules/collapse-toggle.js"));
var _collapseContent = _interopRequireDefault(__webpack_require__(/*! ./collapse-content */ "../app/assets/js/molecules/collapse-content.js"));
__webpack_require__(/*! ./collapse.scss */ "../app/assets/js/molecules/collapse.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Collapse(props) {
  var _useState = (0, _react.useState)(props.isOpened),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isOpened = _useState2[0],
    setIsOpened = _useState2[1],
    classNameBase = 'e-app-collapse',
    classes = [classNameBase, props.className, (0, _defineProperty2.default)({}, classNameBase + '--opened', isOpened)],
    toggle = function toggle() {
      return setIsOpened(function (prevState) {
        return !prevState;
      });
    };
  (0, _react.useEffect)(function () {
    if (props.isOpened !== isOpened) {
      setIsOpened(props.isOpened);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [props.isOpened]);
  (0, _react.useEffect)(function () {
    if (props.onChange) {
      props.onChange(isOpened);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isOpened]);
  return /*#__PURE__*/_react.default.createElement(_collapseContext.CollapseContext.Provider, {
    value: {
      toggle: toggle
    }
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children));
}
Collapse.propTypes = {
  className: PropTypes.string,
  isOpened: PropTypes.bool,
  onChange: PropTypes.func,
  children: PropTypes.oneOfType([PropTypes.node, PropTypes.arrayOf(PropTypes.node)])
};
Collapse.defaultProps = {
  className: '',
  isOpened: false
};
Collapse.Toggle = _collapseToggle.default;
Collapse.Content = _collapseContent.default;

/***/ }),

/***/ "../app/assets/js/molecules/collapse.scss":
/*!************************************************!*\
  !*** ../app/assets/js/molecules/collapse.scss ***!
  \************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/molecules/go-pro-button.js":
/*!***************************************************!*\
  !*** ../app/assets/js/molecules/go-pro-button.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoProButton;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
function GoProButton(props) {
  var baseClassName = 'e-app-go-pro-button',
    classes = [baseClassName, props.className];
  return /*#__PURE__*/_react.default.createElement(_button.default, (0, _extends2.default)({}, props, {
    className: (0, _utils.arrayToClassName)(classes),
    text: props.text
  }));
}
GoProButton.propTypes = {
  className: PropTypes.string,
  text: PropTypes.string
};
GoProButton.defaultProps = {
  className: '',
  variant: 'outlined',
  size: 'sm',
  color: 'cta',
  target: '_blank',
  rel: 'noopener noreferrer',
  text: __('Upgrade Now', 'elementor')
};

/***/ }),

/***/ "../app/assets/js/molecules/upload-file.js":
/*!*************************************************!*\
  !*** ../app/assets/js/molecules/upload-file.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = UploadFile;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./upload-file.scss */ "../app/assets/js/molecules/upload-file.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function UploadFile(props) {
  var fileInput = (0, _react.useRef)(null),
    baseClassName = 'e-app-upload-file',
    classes = [baseClassName, props.className];

  // For 'wp-media' type.
  var frame;
  return /*#__PURE__*/_react.default.createElement("div", {
    className: (0, _utils.arrayToClassName)(classes)
  }, /*#__PURE__*/_react.default.createElement("input", {
    ref: fileInput,
    type: "file",
    accept: props.filetypes.map(function (type) {
      return '.' + type;
    }).join(', '),
    className: "e-app-upload-file__input",
    onChange: function onChange(event) {
      var file = event.target.files[0];
      if (file && (0, _utils.isOneOf)(file.type, props.filetypes)) {
        props.onFileSelect(file, event, 'browse');
      } else {
        fileInput.current.value = '';
        props.onError({
          id: 'file_not_allowed',
          message: __('This file type is not allowed', 'elementor')
        });
      }
    }
  }), /*#__PURE__*/_react.default.createElement(_button.default, {
    className: "e-app-upload-file__button",
    text: props.text,
    variant: props.variant,
    color: props.color,
    size: "lg",
    hideText: props.isLoading,
    icon: props.isLoading ? 'eicon-loading eicon-animation-spin' : '',
    onClick: function onClick() {
      if (props.onFileChoose) {
        props.onFileChoose();
      }
      if (!props.isLoading) {
        if (props.onButtonClick) {
          props.onButtonClick();
        }
        if ('file-explorer' === props.type) {
          fileInput.current.click();
        } else if ('wp-media' === props.type) {
          if (frame) {
            frame.open();
            return;
          }

          // Initialize the WP Media frame.
          frame = wp.media({
            multiple: false,
            library: {
              type: ['image', 'image/svg+xml']
            }
          });
          frame.on('select', function () {
            if (props.onWpMediaSelect) {
              props.onWpMediaSelect(frame);
            }
          });
          frame.open();
        }
      }
    }
  }));
}
UploadFile.propTypes = {
  className: PropTypes.string,
  type: PropTypes.string,
  onWpMediaSelect: PropTypes.func,
  text: PropTypes.string,
  onFileSelect: PropTypes.func,
  isLoading: PropTypes.bool,
  filetypes: PropTypes.array.isRequired,
  onError: PropTypes.func,
  variant: PropTypes.string,
  color: PropTypes.string,
  onButtonClick: PropTypes.func,
  onFileChoose: PropTypes.func
};
UploadFile.defaultProps = {
  className: '',
  type: 'file-explorer',
  text: __('Select File', 'elementor'),
  onError: function onError() {},
  variant: 'contained',
  color: 'primary'
};

/***/ }),

/***/ "../app/assets/js/molecules/upload-file.scss":
/*!***************************************************!*\
  !*** ../app/assets/js/molecules/upload-file.scss ***!
  \***************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/organisms/drop-zone.js":
/*!***********************************************!*\
  !*** ../app/assets/js/organisms/drop-zone.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DropZone;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _uploadFile = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/upload-file */ "../app/assets/js/molecules/upload-file.js"));
var _dragDrop = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/drag-drop */ "../app/assets/js/ui/atoms/drag-drop.js"));
var _icon = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/icon */ "../app/assets/js/ui/atoms/icon.js"));
var _heading = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
__webpack_require__(/*! ./drop-zone.scss */ "../app/assets/js/organisms/drop-zone.scss");
function DropZone(props) {
  var classes = ['e-app-drop-zone', props.className],
    dragDropEvents = {
      onDrop: function onDrop(event) {
        if (!props.isLoading) {
          var file = event.dataTransfer.files[0];
          if (file && (0, _utils.isOneOf)(file.type, props.filetypes)) {
            props.onFileSelect(file, event, 'drop');
          } else {
            props.onError({
              id: 'file_not_allowed',
              message: __('This file type is not allowed', 'elementor')
            });
          }
        }
      }
    };
  return /*#__PURE__*/_react.default.createElement("section", {
    className: (0, _utils.arrayToClassName)(classes)
  }, /*#__PURE__*/_react.default.createElement(_dragDrop.default, (0, _extends2.default)({}, dragDropEvents, {
    isLoading: props.isLoading
  }), props.icon && /*#__PURE__*/_react.default.createElement(_icon.default, {
    className: "e-app-drop-zone__icon ".concat(props.icon)
  }), props.heading && /*#__PURE__*/_react.default.createElement(_heading.default, {
    variant: "display-3"
  }, props.heading), props.text && /*#__PURE__*/_react.default.createElement(_text.default, {
    variant: "xl",
    className: "e-app-drop-zone__text"
  }, props.text), props.secondaryText && /*#__PURE__*/_react.default.createElement(_text.default, {
    variant: "xl",
    className: "e-app-drop-zone__secondary-text"
  }, props.secondaryText), props.showButton && /*#__PURE__*/_react.default.createElement(_uploadFile.default, {
    isLoading: props.isLoading,
    type: props.type,
    onButtonClick: props.onButtonClick,
    onFileSelect: props.onFileSelect,
    onWpMediaSelect: function onWpMediaSelect(frame) {
      return props.onWpMediaSelect(frame);
    },
    onError: function onError(error) {
      return props.onError(error);
    },
    text: props.buttonText,
    filetypes: props.filetypes,
    variant: props.buttonVariant,
    color: props.buttonColor,
    onFileChoose: props.onFileChoose
  }), props.description && /*#__PURE__*/_react.default.createElement(_text.default, {
    variant: "xl",
    className: "e-app-drop-zone__description"
  }, props.description)));
}
DropZone.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any,
  type: PropTypes.string,
  onFileSelect: PropTypes.func.isRequired,
  onWpMediaSelect: PropTypes.func,
  heading: PropTypes.string,
  text: PropTypes.string,
  secondaryText: PropTypes.string,
  buttonText: PropTypes.string,
  buttonVariant: PropTypes.string,
  buttonColor: PropTypes.string,
  icon: PropTypes.string,
  showButton: PropTypes.bool,
  showIcon: PropTypes.bool,
  isLoading: PropTypes.bool,
  filetypes: PropTypes.array.isRequired,
  onError: PropTypes.func,
  description: PropTypes.string,
  onButtonClick: PropTypes.func,
  onFileChoose: PropTypes.func
};
DropZone.defaultProps = {
  className: '',
  type: 'file-explorer',
  icon: 'eicon-library-upload',
  showButton: true,
  showIcon: true,
  onError: function onError() {}
};

/***/ }),

/***/ "../app/assets/js/organisms/drop-zone.scss":
/*!*************************************************!*\
  !*** ../app/assets/js/organisms/drop-zone.scss ***!
  \*************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/organisms/error-boundary.js":
/*!****************************************************!*\
  !*** ../app/assets/js/organisms/error-boundary.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _dialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/dialog/dialog */ "../app/assets/js/ui/dialog/dialog.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
// In the current time there is no solution to use "getDerivedStateFromError" static method with functional component
// That is why this component is a class component.
// @link https://reactjs.org/docs/hooks-faq.html#do-hooks-cover-all-use-cases-for-classes
var ErrorBoundary = exports["default"] = /*#__PURE__*/function (_React$Component) {
  function ErrorBoundary(props) {
    var _this;
    (0, _classCallCheck2.default)(this, ErrorBoundary);
    _this = _callSuper(this, ErrorBoundary, [props]);
    _this.state = {
      hasError: null
    };
    return _this;
  }
  (0, _inherits2.default)(ErrorBoundary, _React$Component);
  return (0, _createClass2.default)(ErrorBoundary, [{
    key: "goBack",
    value: function goBack() {
      // If the app was opened inside an iframe, it will close it,
      // if not, it will redirect to the last location.
      if (window.top !== window.self) {
        window.top.$e.run('app/close');
      }
      window.location = elementorAppConfig.return_url;
    }
  }, {
    key: "render",
    value: function render() {
      if (this.state.hasError) {
        return /*#__PURE__*/_react.default.createElement(_dialog.default, {
          title: this.props.title,
          text: this.props.text,
          approveButtonUrl: this.props.learnMoreUrl,
          approveButtonColor: "link",
          approveButtonTarget: "_blank",
          approveButtonText: __('Learn More', 'elementor'),
          dismissButtonText: __('Go Back', 'elementor'),
          dismissButtonOnClick: this.goBack
        });
      }
      return this.props.children;
    }
  }], [{
    key: "getDerivedStateFromError",
    value: function getDerivedStateFromError() {
      return {
        hasError: true
      };
    }
  }]);
}(_react.default.Component);
(0, _defineProperty2.default)(ErrorBoundary, "propTypes", {
  children: PropTypes.any,
  title: PropTypes.string,
  text: PropTypes.string,
  learnMoreUrl: PropTypes.string
});
(0, _defineProperty2.default)(ErrorBoundary, "defaultProps", {
  title: __('App could not be loaded', 'elementor'),
  text: __('We’re sorry, but something went wrong. Click on ‘Learn more’ and follow each of the steps to quickly solve it.', 'elementor'),
  learnMoreUrl: 'https://go.elementor.com/app-general-load-issue/'
});

/***/ }),

/***/ "../app/assets/js/organisms/unfiltered-files-dialog.js":
/*!*************************************************************!*\
  !*** ../app/assets/js/organisms/unfiltered-files-dialog.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = UnfilteredFilesDialog;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _dialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/dialog/dialog */ "../app/assets/js/ui/dialog/dialog.js"));
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function UnfilteredFilesDialog(props) {
  var show = props.show,
    setShow = props.setShow,
    onReady = props.onReady,
    onCancel = props.onCancel,
    onDismiss = props.onDismiss,
    onLoad = props.onLoad,
    onEnable = props.onEnable,
    onClose = props.onClose,
    _useAjax = (0, _useAjax2.default)(),
    ajaxState = _useAjax.ajaxState,
    setAjax = _useAjax.setAjax,
    _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    enableUnfilteredFiles = _useState2[0],
    setEnableUnfilteredFiles = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isEnableError = _useState4[0],
    setIsEnableError = _useState4[1];

  // Sending the enable unfiltered files request.
  (0, _react.useEffect)(function () {
    if (enableUnfilteredFiles) {
      setShow(false);
      setAjax({
        data: {
          action: 'elementor_ajax',
          actions: JSON.stringify({
            enable_unfiltered_files_upload: {
              action: 'enable_unfiltered_files_upload'
            }
          })
        }
      });
      if (onEnable) {
        onEnable();
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [enableUnfilteredFiles]);

  // Enabling unfiltered files ajax status.
  (0, _react.useEffect)(function () {
    switch (ajaxState.status) {
      case 'success':
        onReady();
        break;
      case 'error':
        setIsEnableError(true);
        setShow(true);
        break;
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ajaxState]);
  (0, _react.useEffect)(function () {
    if (show && onLoad) {
      onLoad();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [show]);
  if (!show) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, isEnableError ? /*#__PURE__*/_react.default.createElement(_dialog.default, {
    title: __('Something went wrong.', 'elementor'),
    text: props.errorModalText,
    approveButtonColor: "link",
    approveButtonText: __('Continue', 'elementor'),
    approveButtonOnClick: onReady,
    dismissButtonText: __('Go Back', 'elementor'),
    dismissButtonOnClick: onCancel,
    onClose: onCancel
  }) : /*#__PURE__*/_react.default.createElement(_dialog.default, {
    title: __('First, enable unfiltered file uploads.', 'elementor'),
    text: props.confirmModalText,
    approveButtonColor: "link",
    approveButtonText: __('Enable', 'elementor'),
    approveButtonOnClick: function approveButtonOnClick() {
      return setEnableUnfilteredFiles(true);
    },
    dismissButtonText: __('Skip', 'elementor'),
    dismissButtonOnClick: onDismiss || onReady,
    onClose: onClose || onDismiss || onReady
  }));
}
UnfilteredFilesDialog.propTypes = {
  show: PropTypes.bool,
  setShow: PropTypes.func.isRequired,
  onReady: PropTypes.func.isRequired,
  onCancel: PropTypes.func.isRequired,
  onDismiss: PropTypes.func,
  confirmModalText: PropTypes.string.isRequired,
  errorModalText: PropTypes.string.isRequired,
  onLoad: PropTypes.func,
  onEnable: PropTypes.func,
  onClose: PropTypes.func
};
UnfilteredFilesDialog.defaultProps = {
  show: false,
  onReady: function onReady() {},
  onCancel: function onCancel() {}
};

/***/ }),

/***/ "../app/assets/js/package.js":
/*!***********************************!*\
  !*** ../app/assets/js/package.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.hooks = exports.components = exports.appUi = void 0;
var _addNewButton = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/add-new-button */ "../app/assets/js/ui/molecules/add-new-button.js"));
var _box = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/box */ "../app/assets/js/ui/atoms/box.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _card = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card */ "../app/assets/js/ui/card/card.js"));
var _cardBody = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card-body */ "../app/assets/js/ui/card/card-body.js"));
var _cardFooter = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card-footer */ "../app/assets/js/ui/card/card-footer.js"));
var _cardImage = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card-image */ "../app/assets/js/ui/card/card-image.js"));
var _cardHeader = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card-header */ "../app/assets/js/ui/card/card-header.js"));
var _cardOverlay = _interopRequireDefault(__webpack_require__(/*! ./ui/card/card-overlay */ "../app/assets/js/ui/card/card-overlay.js"));
var _checkbox = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/checkbox */ "../app/assets/js/ui/atoms/checkbox.js"));
var _collapse = _interopRequireDefault(__webpack_require__(/*! ./molecules/collapse */ "../app/assets/js/molecules/collapse.js"));
var _cssGrid = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/css-grid */ "../app/assets/js/ui/atoms/css-grid.js"));
var _dialog = _interopRequireDefault(__webpack_require__(/*! ./ui/dialog/dialog */ "../app/assets/js/ui/dialog/dialog.js"));
var _dragDrop = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/drag-drop */ "../app/assets/js/ui/atoms/drag-drop.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! ./organisms/drop-zone */ "../app/assets/js/organisms/drop-zone.js"));
var _errorBoundary = _interopRequireDefault(__webpack_require__(/*! ./organisms/error-boundary */ "../app/assets/js/organisms/error-boundary.js"));
var _heading = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
var _goProButton = _interopRequireDefault(__webpack_require__(/*! ./molecules/go-pro-button */ "../app/assets/js/molecules/go-pro-button.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! ./ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _icon = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/icon */ "../app/assets/js/ui/atoms/icon.js"));
var _list = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/list */ "../app/assets/js/ui/molecules/list.js"));
var _menu = _interopRequireDefault(__webpack_require__(/*! ./ui/menu/menu */ "../app/assets/js/ui/menu/menu.js"));
var _menuItem = _interopRequireDefault(__webpack_require__(/*! ./ui/menu/menu-item */ "../app/assets/js/ui/menu/menu-item.js"));
var _modal = _interopRequireWildcard(__webpack_require__(/*! ./ui/modal/modal */ "../app/assets/js/ui/modal/modal.js"));
var _notFound = _interopRequireDefault(__webpack_require__(/*! ./pages/not-found */ "../app/assets/js/pages/not-found.js"));
var _notice = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/notice */ "../app/assets/js/ui/molecules/notice.js"));
var _page = _interopRequireDefault(__webpack_require__(/*! ./layout/page */ "../app/assets/js/layout/page.js"));
var _popover = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/popover */ "../app/assets/js/ui/molecules/popover.js"));
var _select = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/select */ "../app/assets/js/ui/atoms/select.js"));
var _select2 = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/select2 */ "../app/assets/js/ui/molecules/select2.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! ./ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
var _uploadFile = _interopRequireDefault(__webpack_require__(/*! ./molecules/upload-file */ "../app/assets/js/molecules/upload-file.js"));
var _inlineLink = _interopRequireDefault(__webpack_require__(/*! ./ui/molecules/inline-link */ "../app/assets/js/ui/molecules/inline-link.js"));
var _unfilteredFilesDialog = _interopRequireDefault(__webpack_require__(/*! ./organisms/unfiltered-files-dialog.js */ "../app/assets/js/organisms/unfiltered-files-dialog.js"));
var _useAjax = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _useAction = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-action */ "../app/assets/js/hooks/use-action.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _useQueryParams = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-query-params */ "../app/assets/js/hooks/use-query-params.js"));
var _useIntroduction = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-introduction */ "../app/assets/js/hooks/use-introduction.js"));
var _useConfirmAction = _interopRequireDefault(__webpack_require__(/*! ./hooks/use-confirm-action */ "../app/assets/js/hooks/use-confirm-action.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
// Alphabetical order.
// App UI

// Components

// Hooks

var appUi = exports.appUi = {
  AddNewButton: _addNewButton.default,
  Box: _box.default,
  Button: _button.default,
  Card: _card.default,
  CardBody: _cardBody.default,
  CardFooter: _cardFooter.default,
  CardHeader: _cardHeader.default,
  CardImage: _cardImage.default,
  CardOverlay: _cardOverlay.default,
  Checkbox: _checkbox.default,
  Collapse: _collapse.default,
  CssGrid: _cssGrid.default,
  Dialog: _dialog.default,
  DragDrop: _dragDrop.default,
  DropZone: _dropZone.default,
  ErrorBoundary: _errorBoundary.default,
  Heading: _heading.default,
  GoProButton: _goProButton.default,
  Grid: _grid.default,
  Icon: _icon.default,
  List: _list.default,
  Menu: _menu.default,
  MenuItem: _menuItem.default,
  Modal: _modal.Modal,
  ModalProvider: _modal.default,
  NotFound: _notFound.default,
  Notice: _notice.default,
  Page: _page.default,
  Popover: _popover.default,
  Select: _select.default,
  Select2: _select2.default,
  Text: _text.default,
  UploadFile: _uploadFile.default,
  InlineLink: _inlineLink.default
};
var components = exports.components = {
  UnfilteredFilesDialog: _unfilteredFilesDialog.default
};
var hooks = exports.hooks = {
  useAjax: _useAjax.default,
  useAction: _useAction.default,
  usePageTitle: _usePageTitle.default,
  useQueryParams: _useQueryParams.default,
  useIntroduction: _useIntroduction.default,
  useConfirmAction: _useConfirmAction.default
};

/***/ }),

/***/ "../app/assets/js/pages/not-found.js":
/*!*******************************************!*\
  !*** ../app/assets/js/pages/not-found.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = NotFound;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _page = _interopRequireDefault(__webpack_require__(/*! elementor-app/layout/page */ "../app/assets/js/layout/page.js"));
function NotFound() {
  var config = {
    title: __('Not Found', 'elementor'),
    className: 'eps-app__not-found',
    content: /*#__PURE__*/_react.default.createElement("h1", null, " ", __('Not Found', 'elementor'), " "),
    sidebar: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null)
  };
  return /*#__PURE__*/_react.default.createElement(_page.default, config);
}

/***/ }),

/***/ "../app/assets/js/router.js":
/*!**********************************!*\
  !*** ../app/assets/js/router.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var React = __webpack_require__(/*! react */ "react");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
/**
 * App Router
 *
 * TODO: Temporary solution for routing extensibility.
 */
var Router = /*#__PURE__*/function () {
  function Router() {
    (0, _classCallCheck2.default)(this, Router);
    /**
     * @type {*[]}
     */
    (0, _defineProperty2.default)(this, "routes", []);
    (0, _defineProperty2.default)(this, "history", null);
  }
  return (0, _createClass2.default)(Router, [{
    key: "addRoute",
    value:
    /**
     *
     * @param {{path: string, component: Object, props: Object}} route
     */
    function addRoute(route) {
      this.routes.push(route);
    }
  }, {
    key: "getRoutes",
    value: function getRoutes() {
      return this.routes.map(function (route) {
        var props = route.props || {};
        // Use the path as a key, and add it as a prop.
        props.path = props.key = route.path;
        return React.createElement(route.component, props);
      });
    }
  }]);
}();
var router = new Router();

// Make router available for use within packages.
window.elementorAppPackages = {
  router: router
};
var _default = exports["default"] = router;

/***/ }),

/***/ "../app/assets/js/ui/atoms/box.js":
/*!****************************************!*\
  !*** ../app/assets/js/ui/atoms/box.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Box;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./box.scss */ "../app/assets/js/ui/atoms/box.scss");
function Box(props) {
  var baseClassName = 'eps-box',
    classes = [baseClassName, props.className],
    style = {};
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style['--eps-box-padding'] = (0, _utils.pxToRem)(props.padding);
    classes.push(baseClassName + '--padding');
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    style: style,
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children);
}
Box.propTypes = {
  className: PropTypes.string,
  padding: PropTypes.string,
  children: PropTypes.oneOfType([PropTypes.string, PropTypes.object, PropTypes.arrayOf(PropTypes.object)]).isRequired
};
Box.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/box.scss":
/*!******************************************!*\
  !*** ../app/assets/js/ui/atoms/box.scss ***!
  \******************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/atoms/checkbox.js":
/*!*********************************************!*\
  !*** ../app/assets/js/ui/atoms/checkbox.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Checkbox;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./checkbox.scss */ "../app/assets/js/ui/atoms/checkbox.scss");
function Checkbox(_ref) {
  var className = _ref.className,
    checked = _ref.checked,
    rounded = _ref.rounded,
    indeterminate = _ref.indeterminate,
    error = _ref.error,
    disabled = _ref.disabled,
    onChange = _ref.onChange,
    id = _ref.id;
  var baseClassName = 'eps-checkbox',
    classes = [baseClassName, className];
  if (rounded) {
    classes.push(baseClassName + '--rounded');
  }
  if (indeterminate) {
    classes.push(baseClassName + '--indeterminate');
  }
  if (error) {
    classes.push(baseClassName + '--error');
  }
  return /*#__PURE__*/_react.default.createElement("input", {
    className: (0, _utils.arrayToClassName)(classes),
    type: "checkbox",
    checked: checked,
    disabled: disabled,
    onChange: onChange,
    id: id
  });
}
Checkbox.propTypes = {
  className: PropTypes.string,
  checked: PropTypes.bool,
  disabled: PropTypes.bool,
  indeterminate: PropTypes.bool,
  rounded: PropTypes.bool,
  error: PropTypes.bool,
  onChange: PropTypes.func,
  id: PropTypes.string
};
Checkbox.defaultProps = {
  className: '',
  checked: null,
  disabled: false,
  indeterminate: false,
  error: false,
  onChange: function onChange() {}
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/checkbox.scss":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/atoms/checkbox.scss ***!
  \***********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/atoms/css-grid.js":
/*!*********************************************!*\
  !*** ../app/assets/js/ui/atoms/css-grid.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CssGrid;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./css-grid.scss */ "../app/assets/js/ui/atoms/css-grid.scss");
function CssGrid(props) {
  var gridStyle = {
    '--eps-grid-columns': props.columns,
    '--eps-grid-spacing': (0, _utils.pxToRem)(props.spacing),
    '--eps-grid-col-min-width': (0, _utils.pxToRem)(props.colMinWidth),
    '--eps-grid-col-max-width': (0, _utils.pxToRem)(props.colMaxWidth)
  };
  return /*#__PURE__*/_react.default.createElement("div", {
    style: gridStyle,
    className: "eps-css-grid ".concat(props.className)
  }, props.children);
}
CssGrid.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any.isRequired,
  columns: PropTypes.number,
  spacing: PropTypes.number,
  colMinWidth: PropTypes.number,
  colMaxWidth: PropTypes.number
};
CssGrid.defaultProps = {
  spacing: 24,
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/css-grid.scss":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/atoms/css-grid.scss ***!
  \***********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/atoms/drag-drop.js":
/*!**********************************************!*\
  !*** ../app/assets/js/ui/atoms/drag-drop.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DragDrop;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./drag-drop.scss */ "../app/assets/js/ui/atoms/drag-drop.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function DragDrop(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isDragOver = _useState2[0],
    setIsDragOver = _useState2[1],
    getClassName = function getClassName() {
      var baseClassName = 'e-app-drag-drop',
        classes = [baseClassName, props.className];
      if (isDragOver && !props.isLoading) {
        classes.push(baseClassName + '--drag-over');
      }
      return (0, _utils.arrayToClassName)(classes);
    },
    onDragDropActions = function onDragDropActions(event) {
      event.preventDefault();
      event.stopPropagation();
    },
    dragDropEvents = {
      onDrop: function onDrop(event) {
        onDragDropActions(event);
        setIsDragOver(false);
        if (props.onDrop) {
          props.onDrop(event);
        }
      },
      onDragOver: function onDragOver(event) {
        onDragDropActions(event);
        setIsDragOver(true);
        if (props.onDragOver) {
          props.onDragOver(event);
        }
      },
      onDragLeave: function onDragLeave(event) {
        onDragDropActions(event);
        setIsDragOver(false);
        if (props.onDragLeave) {
          props.onDragLeave(event);
        }
      }
    };
  return /*#__PURE__*/_react.default.createElement("div", (0, _extends2.default)({}, dragDropEvents, {
    className: getClassName()
  }), props.children);
}
DragDrop.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any,
  onDrop: PropTypes.func,
  onDragLeave: PropTypes.func,
  onDragOver: PropTypes.func,
  isLoading: PropTypes.bool
};
DragDrop.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/drag-drop.scss":
/*!************************************************!*\
  !*** ../app/assets/js/ui/atoms/drag-drop.scss ***!
  \************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/atoms/heading.js":
/*!********************************************!*\
  !*** ../app/assets/js/ui/atoms/heading.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Heading;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
function Heading(props) {
  var baseClassName = 'eps',
    classes = [props.className];
  if (props.variant) {
    classes.push(baseClassName + '-' + props.variant);
  }
  var Element = function Element() {
    return _react.default.createElement(props.tag, {
      className: (0, _utils.arrayToClassName)(classes)
    }, props.children);
  };
  return /*#__PURE__*/_react.default.createElement(Element, null);
}
Heading.propTypes = {
  className: PropTypes.string,
  children: PropTypes.oneOfType([PropTypes.string, PropTypes.object, PropTypes.arrayOf(PropTypes.object)]).isRequired,
  tag: PropTypes.oneOf(['h1', 'h2', 'h3', 'h4', 'h5', 'h6']),
  variant: PropTypes.oneOf(['display-1', 'display-2', 'display-3', 'display-4', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']).isRequired
};
Heading.defaultProps = {
  className: '',
  tag: 'h1'
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/icon.js":
/*!*****************************************!*\
  !*** ../app/assets/js/ui/atoms/icon.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _excluded = ["className"];
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var Icon = (0, _react.forwardRef)(function (props, ref) {
  var className = props.className,
    rest = (0, _objectWithoutProperties2.default)(props, _excluded);
  return /*#__PURE__*/_react.default.createElement("i", (0, _extends2.default)({
    ref: ref,
    className: "eps-icon ".concat(className)
  }, rest));
});
Icon.propTypes = {
  className: PropTypes.string.isRequired
};
Icon.defaultProps = {
  className: ''
};
var _default = exports["default"] = Icon;

/***/ }),

/***/ "../app/assets/js/ui/atoms/select.js":
/*!*******************************************!*\
  !*** ../app/assets/js/ui/atoms/select.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Select;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Select(props) {
  return /*#__PURE__*/_react.default.createElement("select", {
    multiple: props.multiple,
    className: props.className,
    value: props.value,
    onChange: props.onChange,
    ref: props.elRef,
    onClick: function onClick() {
      var _props$onClick;
      return (_props$onClick = props.onClick) === null || _props$onClick === void 0 ? void 0 : _props$onClick.call(props);
    }
  }, props.options.map(function (option) {
    return option.children ? /*#__PURE__*/_react.default.createElement("optgroup", {
      label: option.label,
      key: option.label
    }, option.children.map(function (childOption) {
      return /*#__PURE__*/_react.default.createElement("option", {
        key: childOption.value,
        value: childOption.value
      }, childOption.label);
    })) : /*#__PURE__*/_react.default.createElement("option", {
      key: option.value,
      value: option.value
    }, option.label);
  }));
}
Select.propTypes = {
  className: PropTypes.string,
  onChange: PropTypes.func,
  options: PropTypes.array,
  elRef: PropTypes.object,
  multiple: PropTypes.bool,
  value: PropTypes.oneOfType([PropTypes.array, PropTypes.string]),
  onClick: PropTypes.func
};
Select.defaultProps = {
  className: '',
  options: []
};

/***/ }),

/***/ "../app/assets/js/ui/atoms/text.js":
/*!*****************************************!*\
  !*** ../app/assets/js/ui/atoms/text.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Text;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
function Text(props) {
  var baseClassName = 'eps',
    classes = [props.className],
    variant = props.variant && 'md' !== props.variant ? '-' + props.variant : '';
  classes.push(baseClassName + '-text' + variant);
  var Element = function Element() {
    return _react.default.createElement(props.tag, {
      className: (0, _utils.arrayToClassName)(classes)
    }, props.children);
  };
  return /*#__PURE__*/_react.default.createElement(Element, null);
}
Text.propTypes = {
  className: PropTypes.string,
  variant: PropTypes.oneOf(['xl', 'lg', 'md', 'sm', 'xs', 'xxs']),
  tag: PropTypes.string,
  children: PropTypes.any.isRequired
};
Text.defaultProps = {
  className: '',
  tag: 'p'
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-body.js":
/*!*********************************************!*\
  !*** ../app/assets/js/ui/card/card-body.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardBody;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardBody(props) {
  var classNameBase = 'eps-card__body',
    classes = [classNameBase, props.className],
    style = {};
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style['--eps-card-body-padding'] = (0, _utils.pxToRem)(props.padding);
    classes.push(classNameBase + '--padding');
  }
  return /*#__PURE__*/_react.default.createElement("main", {
    className: (0, _utils.arrayToClassName)(classes),
    style: style
  }, props.children);
}
CardBody.propTypes = {
  className: PropTypes.string,
  padding: PropTypes.string,
  passive: PropTypes.bool,
  active: PropTypes.bool,
  children: PropTypes.any.isRequired
};
CardBody.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-divider.js":
/*!************************************************!*\
  !*** ../app/assets/js/ui/card/card-divider.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardDivider;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardDivider(props) {
  var classNameBase = 'eps-card__divider',
    classes = [classNameBase, props.className];
  return /*#__PURE__*/_react.default.createElement("hr", {
    className: (0, _utils.arrayToClassName)(classes)
  });
}
CardDivider.propTypes = {
  className: PropTypes.string
};
CardDivider.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-footer.js":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/card/card-footer.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardFooter;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardFooter(props) {
  var classNameBase = 'eps-card__footer',
    classes = [classNameBase, props.className],
    style = {};
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style['--eps-card-footer-padding'] = (0, _utils.pxToRem)(props.padding);
    classes.push(classNameBase + '--padding');
  }
  return /*#__PURE__*/_react.default.createElement("footer", {
    className: (0, _utils.arrayToClassName)(classes),
    style: style
  }, props.children);
}
CardFooter.propTypes = {
  className: PropTypes.string,
  padding: PropTypes.string,
  passive: PropTypes.bool,
  active: PropTypes.bool,
  children: PropTypes.object.isRequired
};
CardFooter.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-header.js":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/card/card-header.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardHeader;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardHeader(props) {
  var classNameBase = 'eps-card__header',
    classes = [classNameBase, props.className],
    style = {};
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style['--eps-card-header-padding'] = (0, _utils.pxToRem)(props.padding);
    classes.push(classNameBase + '--padding');
  }
  return /*#__PURE__*/_react.default.createElement("header", {
    className: (0, _utils.arrayToClassName)(classes),
    style: style
  }, props.children);
}
CardHeader.propTypes = {
  className: PropTypes.string,
  padding: PropTypes.string,
  passive: PropTypes.bool,
  active: PropTypes.bool,
  children: PropTypes.any.isRequired
};
CardHeader.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-headline.js":
/*!*************************************************!*\
  !*** ../app/assets/js/ui/card/card-headline.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardHeadline;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardHeadline(props) {
  var classNameBase = 'eps-card__headline',
    classes = [classNameBase, props.className];
  return /*#__PURE__*/_react.default.createElement("h4", {
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children);
}
CardHeadline.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any.isRequired
};
CardHeadline.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-image.js":
/*!**********************************************!*\
  !*** ../app/assets/js/ui/card/card-image.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardImage;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardImage(props) {
  var image = /*#__PURE__*/_react.default.createElement("img", {
    src: props.src,
    alt: props.alt,
    className: "eps-card__image",
    loading: "lazy",
    onError: props.onError
  });
  return /*#__PURE__*/_react.default.createElement("figure", {
    className: "eps-card__figure ".concat(props.className)
  }, image, props.children);
}
CardImage.propTypes = {
  className: PropTypes.string,
  src: PropTypes.string.isRequired,
  alt: PropTypes.string.isRequired,
  children: PropTypes.any,
  onError: PropTypes.func
};
CardImage.defaultProps = {
  className: '',
  onError: function onError() {}
};

/***/ }),

/***/ "../app/assets/js/ui/card/card-overlay.js":
/*!************************************************!*\
  !*** ../app/assets/js/ui/card/card-overlay.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = CardOverlay;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
function CardOverlay(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-card__image-overlay ".concat(props.className)
  }, props.children);
}
CardOverlay.propTypes = {
  className: PropTypes.string,
  children: PropTypes.object.isRequired
};
CardOverlay.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/card/card.js":
/*!****************************************!*\
  !*** ../app/assets/js/ui/card/card.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _cardHeader = _interopRequireDefault(__webpack_require__(/*! ./card-header */ "../app/assets/js/ui/card/card-header.js"));
var _cardBody = _interopRequireDefault(__webpack_require__(/*! ./card-body */ "../app/assets/js/ui/card/card-body.js"));
var _cardImage = _interopRequireDefault(__webpack_require__(/*! ./card-image */ "../app/assets/js/ui/card/card-image.js"));
var _cardOverlay = _interopRequireDefault(__webpack_require__(/*! ./card-overlay */ "../app/assets/js/ui/card/card-overlay.js"));
var _cardFooter = _interopRequireDefault(__webpack_require__(/*! ./card-footer */ "../app/assets/js/ui/card/card-footer.js"));
var _cardHeadline = _interopRequireDefault(__webpack_require__(/*! ./card-headline */ "../app/assets/js/ui/card/card-headline.js"));
var _cardDivider = _interopRequireDefault(__webpack_require__(/*! ./card-divider */ "../app/assets/js/ui/card/card-divider.js"));
__webpack_require__(/*! ./card.scss */ "../app/assets/js/ui/card/card.scss");
var Card = _react.default.forwardRef(function (props, ref) {
  return /*#__PURE__*/_react.default.createElement("article", {
    className: "eps-card ".concat(props.className),
    ref: ref
  }, props.children);
});
Card.propTypes = {
  type: PropTypes.string,
  className: PropTypes.string,
  children: PropTypes.any
};
Card.defaultProps = {
  className: ''
};
Card.displayName = 'Card';
Card.Header = _cardHeader.default;
Card.Body = _cardBody.default;
Card.Image = _cardImage.default;
Card.Overlay = _cardOverlay.default;
Card.Footer = _cardFooter.default;
Card.Headline = _cardHeadline.default;
Card.Divider = _cardDivider.default;
var _default = exports["default"] = Card;

/***/ }),

/***/ "../app/assets/js/ui/card/card.scss":
/*!******************************************!*\
  !*** ../app/assets/js/ui/card/card.scss ***!
  \******************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-actions.js":
/*!****************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-actions.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogActions;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function DialogActions(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-dialog__buttons"
  }, props.children);
}
DialogActions.propTypes = {
  children: PropTypes.any
};

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-button.js":
/*!***************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-button.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogButton;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function DialogButton(props) {
  return /*#__PURE__*/_react.default.createElement(_button.default, (0, _extends2.default)({}, props, {
    className: "eps-dialog__button ".concat(props.className)
  }));
}
DialogButton.propTypes = _objectSpread(_objectSpread({}, _button.default.propTypes), {}, {
  tabIndex: PropTypes.string,
  type: PropTypes.string
});
DialogButton.defaultProps = _objectSpread(_objectSpread({}, _button.default.defaultProps), {}, {
  tabIndex: '0',
  type: 'button'
});

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-content.js":
/*!****************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-content.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogContent;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function DialogContent(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-dialog__content"
  }, props.children);
}
DialogContent.propTypes = {
  children: PropTypes.any
};

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-text.js":
/*!*************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-text.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogText;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function DialogText(props) {
  return /*#__PURE__*/_react.default.createElement(_text.default, (0, _extends2.default)({
    variant: "xs"
  }, props, {
    className: "eps-dialog__text ".concat(props.className)
  }));
}
DialogText.propTypes = _objectSpread({}, _text.default.propTypes);
DialogText.defaultProps = _objectSpread(_objectSpread({}, _text.default.defaultProps), {}, {
  tag: 'p',
  variant: 'sm'
});

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-title.js":
/*!**************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-title.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogTitle;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _heading = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function DialogTitle(props) {
  return /*#__PURE__*/_react.default.createElement(_heading.default, (0, _extends2.default)({}, props, {
    className: "eps-dialog__title ".concat(props.className)
  }));
}
DialogTitle.propTypes = _objectSpread(_objectSpread({}, _heading.default.propTypes), {}, {
  className: PropTypes.string
});
DialogTitle.defaultProps = _objectSpread(_objectSpread({}, _heading.default.propTypes), {}, {
  variant: 'h3',
  tag: 'h3',
  className: ''
});

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog-wrapper.js":
/*!****************************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog-wrapper.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DialogWrapper;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
function DialogWrapper(props) {
  var WrapperTag = 'div';
  if (props.onSubmit) {
    WrapperTag = 'form';
  }
  return /*#__PURE__*/_react.default.createElement("section", {
    className: "eps-modal__overlay"
  }, /*#__PURE__*/_react.default.createElement(WrapperTag, {
    className: "eps-modal eps-dialog",
    onSubmit: props.onSubmit
  }, props.onClose && /*#__PURE__*/_react.default.createElement(_button.default, {
    onClick: props.onClose,
    text: __('Close', 'elementor'),
    hideText: true,
    icon: "eicon-close",
    className: "eps-dialog__close-button"
  }), props.children));
}
DialogWrapper.propTypes = {
  onClose: PropTypes.func,
  onSubmit: PropTypes.func,
  children: PropTypes.any
};

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog.js":
/*!********************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Dialog;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _dialogWrapper = _interopRequireDefault(__webpack_require__(/*! ./dialog-wrapper */ "../app/assets/js/ui/dialog/dialog-wrapper.js"));
var _dialogContent = _interopRequireDefault(__webpack_require__(/*! ./dialog-content */ "../app/assets/js/ui/dialog/dialog-content.js"));
var _dialogTitle = _interopRequireDefault(__webpack_require__(/*! ./dialog-title */ "../app/assets/js/ui/dialog/dialog-title.js"));
var _dialogText = _interopRequireDefault(__webpack_require__(/*! ./dialog-text */ "../app/assets/js/ui/dialog/dialog-text.js"));
var _dialogActions = _interopRequireDefault(__webpack_require__(/*! ./dialog-actions */ "../app/assets/js/ui/dialog/dialog-actions.js"));
var _dialogButton = _interopRequireDefault(__webpack_require__(/*! ./dialog-button */ "../app/assets/js/ui/dialog/dialog-button.js"));
__webpack_require__(/*! ./dialog.scss */ "../app/assets/js/ui/dialog/dialog.scss");
function Dialog(props) {
  return /*#__PURE__*/_react.default.createElement(_dialogWrapper.default, {
    onSubmit: props.onSubmit,
    onClose: props.onClose
  }, /*#__PURE__*/_react.default.createElement(_dialogContent.default, null, props.title && /*#__PURE__*/_react.default.createElement(_dialogTitle.default, null, props.title), props.text && /*#__PURE__*/_react.default.createElement(_dialogText.default, null, props.text), props.children), /*#__PURE__*/_react.default.createElement(_dialogActions.default, null, /*#__PURE__*/_react.default.createElement(_dialogButton.default, {
    key: "dismiss",
    text: props.dismissButtonText,
    onClick: props.dismissButtonOnClick,
    url: props.dismissButtonUrl,
    target: props.dismissButtonTarget
    // eslint-disable-next-line jsx-a11y/tabindex-no-positive
    ,
    tabIndex: "2"
  }), /*#__PURE__*/_react.default.createElement(_dialogButton.default, {
    key: "approve",
    text: props.approveButtonText,
    onClick: props.approveButtonOnClick,
    url: props.approveButtonUrl,
    target: props.approveButtonTarget,
    color: props.approveButtonColor,
    elRef: props.approveButtonRef
    // eslint-disable-next-line jsx-a11y/tabindex-no-positive
    ,
    tabIndex: "1"
  })));
}
Dialog.propTypes = {
  title: PropTypes.any,
  text: PropTypes.any,
  children: PropTypes.any,
  onSubmit: PropTypes.func,
  onClose: PropTypes.func,
  dismissButtonText: PropTypes.string.isRequired,
  dismissButtonOnClick: PropTypes.func,
  dismissButtonUrl: PropTypes.string,
  dismissButtonTarget: PropTypes.string,
  approveButtonText: PropTypes.string.isRequired,
  approveButtonOnClick: PropTypes.func,
  approveButtonUrl: PropTypes.string,
  approveButtonColor: PropTypes.string,
  approveButtonTarget: PropTypes.string,
  approveButtonRef: PropTypes.object
};
Dialog.defaultProps = {};
Dialog.Wrapper = _dialogWrapper.default;
Dialog.Content = _dialogContent.default;
Dialog.Title = _dialogTitle.default;
Dialog.Text = _dialogText.default;
Dialog.Actions = _dialogActions.default;
Dialog.Button = _dialogButton.default;

/***/ }),

/***/ "../app/assets/js/ui/dialog/dialog.scss":
/*!**********************************************!*\
  !*** ../app/assets/js/ui/dialog/dialog.scss ***!
  \**********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/grid/grid.js":
/*!****************************************!*\
  !*** ../app/assets/js/ui/grid/grid.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Grid;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./grid.scss */ "../app/assets/js/ui/grid/grid.scss");
function Grid(props) {
  var propsMap = {
      direction: '--direction{{ -VALUE }}',
      justify: '--justify{{ -VALUE }}',
      alignContent: '--align-content{{ -VALUE }}',
      alignItems: '--align-items{{ -VALUE }}',
      container: '-container',
      item: '-item',
      noWrap: '-container--no-wrap',
      wrapReverse: '-container--wrap-reverse',
      zeroMinWidth: '-item--zero-min-width',
      spacing: '-container--spacing',
      xs: '-item-xs{{ -VALUE }}',
      sm: '-item-sm{{ -VALUE }}',
      md: '-item-md{{ -VALUE }}',
      lg: '-item-lg{{ -VALUE }}',
      xl: '-item-xl{{ -VALUE }}',
      xxl: '-item-xxl{{ -VALUE }}'
    },
    getStyle = function getStyle() {
      return isValidPropValue(props.spacing) ? {
        '--grid-spacing-gutter': (0, _utils.pxToRem)(props.spacing)
      } : {};
    },
    classes = [getBaseClassName(), props.className].concat((0, _toConsumableArray2.default)(getPropsClasses(propsMap, props)));
  return /*#__PURE__*/_react.default.createElement("div", {
    style: getStyle(),
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children);
}
function getPropsClasses(propsMap, props) {
  var classes = [];
  for (var prop in propsMap) {
    if (props[prop]) {
      var propValue = isValidPropValue(props[prop]) ? props[prop] : '';
      classes.push(getBaseClassName() + renderPropValueBrackets(propsMap[prop], propValue));
    }
  }
  return classes;
}
function renderPropValueBrackets(propClass, propValue) {
  var brackets = propClass.match(/{{.*?}}/);
  if (brackets) {
    var bracketsValue = propValue ? brackets[0].replace(/[{ }]/g, '').replace(/value/i, propValue) : '';
    propClass = propClass.replace(brackets[0], bracketsValue);
  }
  return propClass;
}
function getBaseClassName() {
  return 'eps-grid';
}
function isValidPropValue(propValue) {
  return propValue && 'boolean' !== typeof propValue;
}
Grid.propTypes = {
  className: PropTypes.string,
  direction: PropTypes.oneOf(['row', 'column', 'row-reverse', 'column-reverse']),
  justify: PropTypes.oneOf(['start', 'center', 'end', 'space-between', 'space-evenly', 'space-around', 'stretch']),
  alignContent: PropTypes.oneOf(['start', 'center', 'end', 'space-between', 'stretch']),
  alignItems: PropTypes.oneOf(['start', 'center', 'end', 'baseline', 'stretch']),
  container: PropTypes.bool,
  item: PropTypes.bool,
  noWrap: PropTypes.bool,
  wrapReverse: PropTypes.bool,
  zeroMinWidth: PropTypes.bool,
  spacing: PropTypes.number,
  xs: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  sm: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  md: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  lg: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  xl: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  xxl: PropTypes.oneOfType([PropTypes.number, PropTypes.bool]),
  children: PropTypes.any.isRequired
};
Grid.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/grid/grid.scss":
/*!******************************************!*\
  !*** ../app/assets/js/ui/grid/grid.scss ***!
  \******************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/menu/menu-item.js":
/*!*********************************************!*\
  !*** ../app/assets/js/ui/menu/menu-item.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/get */ "../node_modules/@babel/runtime/helpers/get.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
__webpack_require__(/*! ./menu-item.scss */ "../app/assets/js/ui/menu/menu-item.scss");
var _button = _interopRequireDefault(__webpack_require__(/*! ../molecules/button */ "../app/assets/js/ui/molecules/button.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var SideMenuItem = exports["default"] = /*#__PURE__*/function (_BaseButton) {
  function SideMenuItem() {
    (0, _classCallCheck2.default)(this, SideMenuItem);
    return _callSuper(this, SideMenuItem, arguments);
  }
  (0, _inherits2.default)(SideMenuItem, _BaseButton);
  return (0, _createClass2.default)(SideMenuItem, [{
    key: "getCssId",
    value: function getCssId() {
      return "eps-menu-item-" + _superPropGet(SideMenuItem, "getCssId", this, 3)([]);
    }
  }, {
    key: "getClassName",
    value: function getClassName() {
      return "eps-menu-item " + _superPropGet(SideMenuItem, "getClassName", this, 3)([]);
    }
  }]);
}(_button.default);

/***/ }),

/***/ "../app/assets/js/ui/menu/menu-item.scss":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/menu/menu-item.scss ***!
  \***********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/menu/menu.js":
/*!****************************************!*\
  !*** ../app/assets/js/ui/menu/menu.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Menu;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
__webpack_require__(/*! ./menu.scss */ "../app/assets/js/ui/menu/menu.scss");
var _button = _interopRequireDefault(__webpack_require__(/*! ../molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _router = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _router2 = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
function Menu(props) {
  var ActionButton = function ActionButton(itemProps) {
    if (!props.actionButton) {
      return '';
    }
    return props.actionButton(itemProps);
  };
  if (props.promotion) {
    return /*#__PURE__*/_react.default.createElement("nav", {
      className: "eps-menu"
    }, props.children, /*#__PURE__*/_react.default.createElement("ul", null, props.menuItems.map(function (item) {
      return /*#__PURE__*/_react.default.createElement("li", {
        key: item.type,
        className: "eps-menu-item"
      }, /*#__PURE__*/_react.default.createElement(_button.default, (0, _extends2.default)({
        text: item.title,
        className: "eps-menu-item__link"
      }, item)), /*#__PURE__*/_react.default.createElement(ActionButton, item));
    })));
  }
  return /*#__PURE__*/_react.default.createElement(_router2.LocationProvider, {
    history: _router.default.appHistory
  }, /*#__PURE__*/_react.default.createElement("nav", {
    className: "eps-menu"
  }, props.children, /*#__PURE__*/_react.default.createElement("ul", null, props.menuItems.map(function (item) {
    return /*#__PURE__*/_react.default.createElement(_router2.Match, {
      key: item.type,
      path: item.url
    }, function (_ref) {
      var match = _ref.match;
      return /*#__PURE__*/_react.default.createElement("li", {
        key: item.type,
        className: "eps-menu-item".concat(match ? ' eps-menu-item--active' : '')
      }, /*#__PURE__*/_react.default.createElement(_button.default, (0, _extends2.default)({
        text: item.title,
        className: "eps-menu-item__link"
      }, item)), /*#__PURE__*/_react.default.createElement(ActionButton, item));
    });
  }))));
}
Menu.propTypes = {
  menuItems: PropTypes.arrayOf(PropTypes.object),
  children: PropTypes.any,
  actionButton: PropTypes.func,
  promotion: PropTypes.bool
};

/***/ }),

/***/ "../app/assets/js/ui/menu/menu.scss":
/*!******************************************!*\
  !*** ../app/assets/js/ui/menu/menu.scss ***!
  \******************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/modal/modal-section.js":
/*!**************************************************!*\
  !*** ../app/assets/js/ui/modal/modal-section.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ModalSection;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
function ModalSection(props) {
  return /*#__PURE__*/_react.default.createElement("section", {
    className: (0, _utils.arrayToClassName)(['eps-modal__section', props.className])
  }, props.children);
}
ModalSection.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any
};
ModalSection.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/modal/modal-tip.js":
/*!**********************************************!*\
  !*** ../app/assets/js/ui/modal/modal-tip.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ModalTip;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! ../../utils/utils */ "../app/assets/js/utils/utils.js");
var _heading = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
function ModalTip(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: (0, _utils.arrayToClassName)(['eps-modal__tip', props.className])
  }, /*#__PURE__*/_react.default.createElement(_heading.default, {
    variant: "h3",
    tag: "h3"
  }, props.title), props.description && /*#__PURE__*/_react.default.createElement(_text.default, {
    variant: "xs"
  }, props.description));
}
ModalTip.propTypes = {
  className: PropTypes.string,
  title: PropTypes.string,
  description: PropTypes.string
};
ModalTip.defaultProps = {
  className: '',
  title: __('Tip', 'elementor')
};

/***/ }),

/***/ "../app/assets/js/ui/modal/modal.js":
/*!******************************************!*\
  !*** ../app/assets/js/ui/modal/modal.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.Modal = void 0;
exports["default"] = ModalProvider;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _icon = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/icon */ "../app/assets/js/ui/atoms/icon.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
var _modalSection = _interopRequireDefault(__webpack_require__(/*! ./modal-section */ "../app/assets/js/ui/modal/modal-section.js"));
var _modalTip = _interopRequireDefault(__webpack_require__(/*! ./modal-tip */ "../app/assets/js/ui/modal/modal-tip.js"));
__webpack_require__(/*! ./modal.scss */ "../app/assets/js/ui/modal/modal.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function ModalProvider(props) {
  var _useState = (0, _react.useState)(props.show),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    show = _useState2[0],
    setShow = _useState2[1],
    hideModal = function hideModal() {
      setShow(false);

      // The purpose of the props.setShow is to sync an external state with the component inner state.
      if (props.setShow) {
        props.setShow(false);
      }
    },
    showModal = function showModal() {
      setShow(true);

      // The purpose of the props.setShow is to sync an external state with the component inner state.
      if (props.setShow) {
        props.setShow(true);
      }
    },
    modalAttrs = _objectSpread(_objectSpread({}, props), {}, {
      show: show,
      hideModal: hideModal,
      showModal: showModal
    });
  (0, _react.useEffect)(function () {
    // Sync with external state.
    setShow(props.show);
  }, [props.show]);
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, props.toggleButtonProps && /*#__PURE__*/_react.default.createElement(_button.default, (0, _extends2.default)({}, props.toggleButtonProps, {
    onClick: showModal
  })), /*#__PURE__*/_react.default.createElement(Modal, modalAttrs, props.children));
}
ModalProvider.propTypes = {
  children: PropTypes.node.isRequired,
  toggleButtonProps: PropTypes.object,
  title: PropTypes.string,
  icon: PropTypes.string,
  show: PropTypes.bool,
  setShow: PropTypes.func,
  onOpen: PropTypes.func,
  onClose: PropTypes.func
};
ModalProvider.defaultProps = {
  show: false
};
ModalProvider.Section = _modalSection.default;
ModalProvider.Tip = _modalTip.default;
var Modal = exports.Modal = function Modal(props) {
  var modalRef = (0, _react.useRef)(null),
    closeRef = (0, _react.useRef)(null),
    closeModal = function closeModal(e) {
      var node = modalRef.current,
        closeNode = closeRef.current,
        isInCloseNode = closeNode && closeNode.contains(e.target);

      // Ignore if click is inside the modal
      if (node && node.contains(e.target) && !isInCloseNode) {
        return;
      }
      props.hideModal();
      if (props.onClose) {
        props.onClose(e);
      }
    };
  (0, _react.useEffect)(function () {
    if (props.show) {
      var _props$onOpen;
      document.addEventListener('mousedown', closeModal, false);
      (_props$onOpen = props.onOpen) === null || _props$onOpen === void 0 || _props$onOpen.call(props);
    }
    return function () {
      return document.removeEventListener('mousedown', closeModal, false);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [props.show]);
  if (!props.show) {
    return null;
  }
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/click-events-have-key-events, jsx-a11y/no-static-element-interactions
    _react.default.createElement("div", {
      className: "eps-modal__overlay",
      onClick: closeModal
    }, /*#__PURE__*/_react.default.createElement("div", {
      className: (0, _utils.arrayToClassName)(['eps-modal', props.className]),
      ref: modalRef
    }, /*#__PURE__*/_react.default.createElement(_grid.default, {
      container: true,
      className: "eps-modal__header",
      justify: "space-between",
      alignItems: "center"
    }, /*#__PURE__*/_react.default.createElement(_grid.default, {
      item: true
    }, /*#__PURE__*/_react.default.createElement(_icon.default, {
      className: "eps-modal__icon ".concat(props.icon)
    }), /*#__PURE__*/_react.default.createElement(_text.default, {
      className: "title",
      tag: "span"
    }, props.title)), /*#__PURE__*/_react.default.createElement(_grid.default, {
      item: true
    }, /*#__PURE__*/_react.default.createElement("div", {
      className: "eps-modal__close-wrapper",
      ref: closeRef
    }, /*#__PURE__*/_react.default.createElement(_button.default, {
      text: __('Close', 'elementor'),
      hideText: true,
      icon: "eicon-close",
      onClick: props.closeModal
    })))), /*#__PURE__*/_react.default.createElement("div", {
      className: "eps-modal__body"
    }, props.children)))
  );
};
Modal.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any.isRequired,
  title: PropTypes.string.isRequired,
  icon: PropTypes.string,
  show: PropTypes.bool,
  setShow: PropTypes.func,
  hideModal: PropTypes.func,
  showModal: PropTypes.func,
  closeModal: PropTypes.func,
  onOpen: PropTypes.func,
  onClose: PropTypes.func
};
Modal.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/modal/modal.scss":
/*!********************************************!*\
  !*** ../app/assets/js/ui/modal/modal.scss ***!
  \********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/add-new-button.js":
/*!*******************************************************!*\
  !*** ../app/assets/js/ui/molecules/add-new-button.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/assets/js/ui/molecules/button.js"));
__webpack_require__(/*! ./add-new-button.scss */ "../app/assets/js/ui/molecules/add-new-button.scss");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var AddNewButton = exports["default"] = /*#__PURE__*/function (_Button) {
  function AddNewButton() {
    (0, _classCallCheck2.default)(this, AddNewButton);
    return _callSuper(this, AddNewButton, arguments);
  }
  (0, _inherits2.default)(AddNewButton, _Button);
  return (0, _createClass2.default)(AddNewButton, [{
    key: "getClassName",
    value: function getClassName() {
      var className = this.props.className;
      if (this.props.size) {
        className += ' eps-add-new-button--' + this.props.size;
      }
      return className;
    }
  }]);
}(_button.default);
(0, _defineProperty2.default)(AddNewButton, "propTypes", _objectSpread(_objectSpread({}, _button.default.propTypes), {}, {
  text: PropTypes.string,
  size: PropTypes.string
}));
(0, _defineProperty2.default)(AddNewButton, "defaultProps", _objectSpread(_objectSpread({}, _button.default.defaultProps), {}, {
  className: 'eps-add-new-button',
  text: __('Add New', 'elementor'),
  icon: 'eicon-plus'
}));

/***/ }),

/***/ "../app/assets/js/ui/molecules/add-new-button.scss":
/*!*********************************************************!*\
  !*** ../app/assets/js/ui/molecules/add-new-button.scss ***!
  \*********************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/button.js":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/molecules/button.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _router2 = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _popoverDialog = _interopRequireDefault(__webpack_require__(/*! ../popover-dialog/popover-dialog */ "../app/assets/js/ui/popover-dialog/popover-dialog.js"));
var _icon = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/icon */ "../app/assets/js/ui/atoms/icon.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Button = exports["default"] = /*#__PURE__*/function (_React$Component) {
  function Button() {
    (0, _classCallCheck2.default)(this, Button);
    return _callSuper(this, Button, arguments);
  }
  (0, _inherits2.default)(Button, _React$Component);
  return (0, _createClass2.default)(Button, [{
    key: "getCssId",
    value: function getCssId() {
      return this.props.id;
    }
  }, {
    key: "getClassName",
    value: function getClassName() {
      var baseClassName = 'eps-button',
        classes = [baseClassName, this.props.className];
      return classes.concat(this.getStylePropsClasses(baseClassName)).filter(function (classItem) {
        return '' !== classItem;
      }).join(' ');
    }
  }, {
    key: "getStylePropsClasses",
    value: function getStylePropsClasses(baseClassName) {
      var _this = this;
      var styleProps = ['color', 'size', 'variant'],
        stylePropClasses = [];
      styleProps.forEach(function (styleProp) {
        var stylePropValue = _this.props[styleProp];
        if (stylePropValue) {
          stylePropClasses.push(baseClassName + '--' + stylePropValue);
        }
      });
      return stylePropClasses;
    }
  }, {
    key: "getIcon",
    value: function getIcon() {
      if (this.props.icon) {
        var iconRef = _react.default.createRef(null);
        var tooltip = this.props.tooltip || this.props.text;
        var icon = /*#__PURE__*/_react.default.createElement(_icon.default, {
          ref: iconRef,
          className: this.props.icon,
          "aria-hidden": "true"
        });
        var screenReaderText = '';
        if (this.props.hideText) {
          screenReaderText = /*#__PURE__*/_react.default.createElement("span", {
            className: "sr-only"
          }, tooltip);
        }
        return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, icon, screenReaderText, this.props.tooltip && /*#__PURE__*/_react.default.createElement(_popoverDialog.default, {
          targetRef: iconRef,
          wrapperClass: "e-kit-library__tooltip"
        }, this.props.tooltip));
      }
      return '';
    }
  }, {
    key: "getText",
    value: function getText() {
      return this.props.hideText ? '' : /*#__PURE__*/_react.default.createElement("span", null, this.props.text);
    }
  }, {
    key: "render",
    value: function render() {
      var attributes = {},
        id = this.getCssId(),
        className = this.getClassName();

      // Add attributes only if they are not empty.
      if (id) {
        attributes.id = id;
      }
      if (className) {
        attributes.className = className;
      }
      if (this.props.onClick) {
        attributes.onClick = this.props.onClick;
      }
      if (this.props.rel) {
        attributes.rel = this.props.rel;
      }
      if (this.props.elRef) {
        attributes.ref = this.props.elRef;
      }
      var buttonContent = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, this.getIcon(), this.getText());
      if (this.props.url) {
        if (0 === this.props.url.indexOf('http')) {
          return /*#__PURE__*/_react.default.createElement("a", (0, _extends2.default)({
            href: this.props.url,
            target: this.props.target
          }, attributes), buttonContent);
        }

        // @see https://reach.tech/router/example/active-links.
        attributes.getProps = function (props) {
          if (props.isCurrent) {
            attributes.className += ' active';
          }
          return {
            className: attributes.className
          };
        };
        return /*#__PURE__*/_react.default.createElement(_router.LocationProvider, {
          history: _router2.default.appHistory
        }, /*#__PURE__*/_react.default.createElement(_router.Link, (0, _extends2.default)({
          to: this.props.url
        }, attributes), buttonContent));
      }
      return /*#__PURE__*/_react.default.createElement("div", attributes, buttonContent);
    }
  }]);
}(_react.default.Component);
(0, _defineProperty2.default)(Button, "propTypes", {
  text: PropTypes.string.isRequired,
  hideText: PropTypes.bool,
  icon: PropTypes.string,
  tooltip: PropTypes.string,
  id: PropTypes.string,
  className: PropTypes.string,
  url: PropTypes.string,
  onClick: PropTypes.func,
  variant: PropTypes.oneOf(['contained', 'underlined', 'outlined', '']),
  color: PropTypes.oneOf(['primary', 'secondary', 'cta', 'link', 'disabled']),
  size: PropTypes.oneOf(['sm', 'md', 'lg']),
  target: PropTypes.string,
  rel: PropTypes.string,
  elRef: PropTypes.object
});
(0, _defineProperty2.default)(Button, "defaultProps", {
  id: '',
  className: '',
  variant: '',
  target: '_parent'
});

/***/ }),

/***/ "../app/assets/js/ui/molecules/inline-link.js":
/*!****************************************************!*\
  !*** ../app/assets/js/ui/molecules/inline-link.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = InlineLink;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _router2 = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
__webpack_require__(/*! ./inline-link.scss */ "../app/assets/js/ui/molecules/inline-link.scss");
function InlineLink(props) {
  var baseClassName = 'eps-inline-link',
    colorClassName = "".concat(baseClassName, "--color-").concat(props.color),
    underlineClassName = 'none' !== props.underline ? "".concat(baseClassName, "--underline-").concat(props.underline) : '',
    italicClassName = props.italic ? "".concat(baseClassName, "--italic") : '',
    classes = [baseClassName, colorClassName, underlineClassName, italicClassName, props.className],
    className = (0, _utils.arrayToClassName)(classes),
    getRouterLink = function getRouterLink() {
      return /*#__PURE__*/_react.default.createElement(_router.LocationProvider, {
        history: _router2.default.appHistory
      }, /*#__PURE__*/_react.default.createElement(_router.Link, {
        to: props.url,
        className: className
      }, props.children));
    },
    getExternalLink = function getExternalLink() {
      return /*#__PURE__*/_react.default.createElement("a", {
        href: props.url,
        target: props.target,
        rel: props.rel,
        className: className,
        onClick: props.onClick
      }, props.children);
    },
    getActionLink = function getActionLink() {
      return /*#__PURE__*/_react.default.createElement("button", {
        className: className,
        onClick: props.onClick
      }, props.children);
    };
  if (!props.url) {
    return getActionLink();
  }
  return props.url.includes('http') ? getExternalLink() : getRouterLink();
}
InlineLink.propTypes = {
  className: PropTypes.string,
  children: PropTypes.any,
  url: PropTypes.string,
  target: PropTypes.string,
  rel: PropTypes.string,
  text: PropTypes.string,
  color: PropTypes.oneOf(['primary', 'secondary', 'cta', 'link', 'disabled']),
  underline: PropTypes.oneOf(['none', 'hover', 'always']),
  italic: PropTypes.bool,
  onClick: PropTypes.func
};
InlineLink.defaultProps = {
  className: '',
  color: 'link',
  underline: 'always',
  target: '_blank',
  rel: 'noopener noreferrer'
};

/***/ }),

/***/ "../app/assets/js/ui/molecules/inline-link.scss":
/*!******************************************************!*\
  !*** ../app/assets/js/ui/molecules/inline-link.scss ***!
  \******************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/list-item.js":
/*!**************************************************!*\
  !*** ../app/assets/js/ui/molecules/list-item.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ListItem;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
function ListItem(props) {
  var baseClassName = 'eps-list__item',
    classes = [baseClassName, props.className];
  var style;
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style = {
      '--eps-list-item-padding': (0, _utils.pxToRem)(props.padding)
    };
    classes.push(baseClassName + '--padding');
  }
  return /*#__PURE__*/_react.default.createElement("li", {
    style: style,
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children);
}
ListItem.propTypes = {
  className: PropTypes.string,
  padding: PropTypes.string,
  children: PropTypes.any.isRequired
};
ListItem.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/assets/js/ui/molecules/list.js":
/*!*********************************************!*\
  !*** ../app/assets/js/ui/molecules/list.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = List;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _listItem = _interopRequireDefault(__webpack_require__(/*! ./list-item */ "../app/assets/js/ui/molecules/list-item.js"));
__webpack_require__(/*! ./list.scss */ "../app/assets/js/ui/molecules/list.scss");
function List(props) {
  var baseClassName = 'eps-list',
    classes = [baseClassName, props.className];
  var style;
  if (Object.prototype.hasOwnProperty.call(props, 'padding')) {
    style = {
      '--eps-list-padding': (0, _utils.pxToRem)(props.padding)
    };
    classes.push(baseClassName + '--padding');
  }
  if (props.separated) {
    classes.push(baseClassName + '--separated');
  }
  return /*#__PURE__*/_react.default.createElement("ul", {
    style: style,
    className: (0, _utils.arrayToClassName)(classes)
  }, props.children);
}
List.propTypes = {
  className: PropTypes.string,
  divided: PropTypes.any,
  separated: PropTypes.any,
  padding: PropTypes.string,
  children: PropTypes.oneOfType([PropTypes.object, PropTypes.arrayOf(PropTypes.object)]).isRequired
};
List.defaultProps = {
  className: ''
};
List.Item = _listItem.default;

/***/ }),

/***/ "../app/assets/js/ui/molecules/list.scss":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/molecules/list.scss ***!
  \***********************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/notice.js":
/*!***********************************************!*\
  !*** ../app/assets/js/ui/molecules/notice.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Notice;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _utils = __webpack_require__(/*! elementor-app/utils/utils.js */ "../app/assets/js/utils/utils.js");
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
var _icon = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/icon */ "../app/assets/js/ui/atoms/icon.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
__webpack_require__(/*! ./notice.scss */ "../app/assets/js/ui/molecules/notice.scss");
var iconsClassesMap = {
  danger: 'eicon-warning',
  info: 'eicon-info-circle-o',
  warning: 'eicon-warning'
};
function Notice(props) {
  var baseClassName = 'eps-notice',
    classes = [baseClassName, props.className];
  if (props.color) {
    classes.push(baseClassName + '-semantic', baseClassName + '--' + props.color);
  }
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    className: (0, _utils.arrayToClassName)(classes),
    container: true,
    noWrap: true,
    alignItems: "center",
    justify: "space-between"
  }, /*#__PURE__*/_react.default.createElement(_grid.default, {
    item: true,
    container: true,
    alignItems: "start",
    noWrap: true
  }, props.withIcon && props.color && /*#__PURE__*/_react.default.createElement(_icon.default, {
    className: (0, _utils.arrayToClassName)(['eps-notice__icon', iconsClassesMap[props.color]])
  }), /*#__PURE__*/_react.default.createElement(_text.default, {
    variant: "xs",
    className: "eps-notice__text"
  }, props.label && /*#__PURE__*/_react.default.createElement("strong", null, props.label + ' '), props.children)), props.button && /*#__PURE__*/_react.default.createElement(_grid.default, {
    item: true,
    container: true,
    justify: "end",
    className: baseClassName + '__button-container'
  }, props.button));
}
Notice.propTypes = {
  className: PropTypes.string,
  color: PropTypes.string,
  label: PropTypes.string,
  children: PropTypes.any.isRequired,
  icon: PropTypes.string,
  withIcon: PropTypes.bool,
  button: PropTypes.object
};
Notice.defaultProps = {
  className: '',
  withIcon: true,
  button: null
};

/***/ }),

/***/ "../app/assets/js/ui/molecules/notice.scss":
/*!*************************************************!*\
  !*** ../app/assets/js/ui/molecules/notice.scss ***!
  \*************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/popover.js":
/*!************************************************!*\
  !*** ../app/assets/js/ui/molecules/popover.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Popover;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
__webpack_require__(/*! ./popover.scss */ "../app/assets/js/ui/molecules/popover.scss");
/* eslint-disable jsx-a11y/no-noninteractive-element-interactions */
/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */

function Popover(props) {
  var getArrowPositionClass = function getArrowPositionClass() {
    switch (props.arrowPosition) {
      case 'start':
        return 'eps-popover--arrow-start';
      case 'end':
        return 'eps-popover--arrow-end';
      case 'none':
        return 'eps-popover--arrow-none';
      default:
        return 'eps-popover--arrow-center';
    }
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-popover__background",
    onClick: props.closeFunction
  }), /*#__PURE__*/_react.default.createElement("ul", {
    className: "eps-popover ".concat(getArrowPositionClass(), " ").concat(props.className),
    onClick: props.closeFunction
  }, props.children));
}
Popover.propTypes = {
  children: PropTypes.any.isRequired,
  className: PropTypes.string,
  closeFunction: PropTypes.func,
  arrowPosition: PropTypes.oneOf(['start', 'center', 'end', 'none'])
};
Popover.defaultProps = {
  className: '',
  arrowPosition: 'center'
};

/***/ }),

/***/ "../app/assets/js/ui/molecules/popover.scss":
/*!**************************************************!*\
  !*** ../app/assets/js/ui/molecules/popover.scss ***!
  \**************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/molecules/select2.js":
/*!************************************************!*\
  !*** ../app/assets/js/ui/molecules/select2.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Select2;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _select = _interopRequireDefault(__webpack_require__(/*! ../atoms/select */ "../app/assets/js/ui/atoms/select.js"));
__webpack_require__(/*! ./select2.scss */ "../app/assets/js/ui/molecules/select2.scss");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * Default settings of the select 2
 *
 * @return {{placeholder: string, allowClear: boolean, dir: string}}
 */

var getDefaultSettings = function getDefaultSettings() {
  return {
    allowClear: true,
    placeholder: '',
    dir: elementorCommon.config.isRTL ? 'rtl' : 'ltr'
  };
};
/**
 * Main component
 *
 * @param {*} props
 * @return {*} component
 * @function Object() { [native code] }
 */
function Select2(props) {
  var ref = _react.default.useRef(null);

  // Initiate the select 2 library, call to onReady after initiate, and
  // listen to select event on the select instance.
  _react.default.useEffect(function () {
    var $select2 = jQuery(ref.current).select2(_objectSpread(_objectSpread(_objectSpread({}, getDefaultSettings()), props.settings), {}, {
      placeholder: props.placeholder
    })).on('select2:select select2:unselect', props.onChange);
    if (props.onReady) {
      props.onReady($select2);
    }
    return function () {
      $select2.select2('destroy').off('select2:select select2:unselect');
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [props.settings, props.options]);

  // Listen to changes in the prop `value`, if changed update the select 2.
  _react.default.useEffect(function () {
    jQuery(ref.current).val(props.value).trigger('change');
  }, [props.value]);
  return /*#__PURE__*/_react.default.createElement(_select.default, {
    multiple: props.multiple,
    value: props.value,
    onChange: props.onChange,
    elRef: ref,
    options: props.options,
    placeholder: props.placeholder
  });
}
Select2.propTypes = {
  value: PropTypes.oneOfType([PropTypes.array, PropTypes.string]),
  onChange: PropTypes.func,
  onReady: PropTypes.func,
  options: PropTypes.array,
  settings: PropTypes.object,
  multiple: PropTypes.bool,
  placeholder: PropTypes.string
};
Select2.defaultProps = {
  settings: {},
  options: [],
  dependencies: [],
  placeholder: ''
};

/***/ }),

/***/ "../app/assets/js/ui/molecules/select2.scss":
/*!**************************************************!*\
  !*** ../app/assets/js/ui/molecules/select2.scss ***!
  \**************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/assets/js/ui/popover-dialog/popover-dialog.js":
/*!************************************************************!*\
  !*** ../app/assets/js/ui/popover-dialog/popover-dialog.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PopoverDialog;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function PopoverDialog(props) {
  var targetRef = props.targetRef,
    offsetTop = props.offsetTop,
    offsetLeft = props.offsetLeft,
    wrapperClass = props.wrapperClass,
    trigger = props.trigger,
    hideAfter = props.hideAfter,
    popoverRef = (0, _react.useCallback)(function (popoverEl) {
      var target = targetRef === null || targetRef === void 0 ? void 0 : targetRef.current;

      // If the target or the popover element does not exist on the page anymore after a re-render, do nothing.
      if (!target || !popoverEl) {
        return;
      }

      /**
       * Show Popover
       */
      var showPopover = function showPopover() {
        popoverEl.style.display = 'block';
        popoverEl.setAttribute('aria-expanded', true);
        var targetRect = target.getBoundingClientRect(),
          popoverRect = popoverEl.getBoundingClientRect(),
          widthDifference = popoverRect.width - targetRect.width;
        popoverEl.style.top = targetRect.bottom + offsetTop + 'px';
        popoverEl.style.left = targetRect.left - widthDifference / 2 - offsetLeft + 'px';

        // 16px to compensate for the arrow width.
        popoverEl.style.setProperty('--popover-arrow-offset-end', (popoverRect.width - 16) / 2 + 'px');
      };

      /**
       * Hide Popover
       */
      var hidePopover = function hidePopover() {
        popoverEl.style.display = 'none';
        popoverEl.setAttribute('aria-expanded', false);
      };

      /**
       * Handle the Popover's hover functionality
       */
      var handlePopoverHover = function handlePopoverHover() {
        var hideOnMouseOut = true,
          timeOut = null;

        // Show popover on hover of the target
        target.addEventListener('mouseover', function () {
          hideOnMouseOut = true;
          showPopover();
        });

        // Hide popover when not overing over the target or the popover itself
        target.addEventListener('mouseleave', function () {
          timeOut = setTimeout(function () {
            if (hideOnMouseOut) {
              if ('block' === popoverEl.style.display) {
                hidePopover();
              }
            }
          }, hideAfter);
        });

        // Don't hide the popover if the user is still hovering over it.
        popoverEl.addEventListener('mouseover', function () {
          hideOnMouseOut = false;
          if (timeOut) {
            clearTimeout(timeOut);
            timeOut = null;
          }
        });

        // Once the user stops hovering over the popover, hide it.
        popoverEl.addEventListener('mouseleave', function () {
          timeOut = setTimeout(function () {
            if (hideOnMouseOut) {
              if ('block' === popoverEl.style.display) {
                hidePopover();
              }
            }
          }, hideAfter);
          hideOnMouseOut = true;
        });
      };

      /**
       * Handle the Popover's click functionality
       */
      var handlePopoverClick = function handlePopoverClick() {
        var popoverIsActive = false;
        target.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          if (popoverIsActive) {
            hidePopover();
            popoverIsActive = false;
          } else {
            showPopover();
            popoverIsActive = true;
          }
        });

        // Make sure the popover doesn't close when it is clicked on.
        popoverEl.addEventListener('click', function (e) {
          e.stopPropagation();
        });

        // Hide the popover when clicking outside of it.
        document.body.addEventListener('click', function () {
          if (popoverIsActive) {
            hidePopover();
            popoverIsActive = false;
          }
        });
      };
      if ('hover' === trigger) {
        handlePopoverHover();
      } else if ('click' === trigger) {
        handlePopoverClick();
      }
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [targetRef]);
  var wrapperClasses = 'e-app__popover';
  if (wrapperClass) {
    wrapperClasses += ' ' + wrapperClass;
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: wrapperClasses,
    ref: popoverRef
  }, props.children);
}
PopoverDialog.propTypes = {
  targetRef: PropTypes.oneOfType([PropTypes.func, PropTypes.shape({
    current: PropTypes.any
  })]).isRequired,
  trigger: PropTypes.string,
  direction: PropTypes.string,
  offsetTop: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  offsetLeft: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
  wrapperClass: PropTypes.string,
  children: PropTypes.any,
  hideAfter: PropTypes.number
};
PopoverDialog.defaultProps = {
  direction: 'bottom',
  trigger: 'hover',
  offsetTop: 10,
  offsetLeft: 0,
  hideAfter: 300
};

/***/ }),

/***/ "../app/assets/js/utils/utils.js":
/*!***************************************!*\
  !*** ../app/assets/js/utils/utils.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.stringToRemValues = exports.rgbToHex = exports.pxToRem = exports.isOneOf = exports.htmlDecodeTextContent = exports.arrayToObjectByKey = exports.arrayToClassName = void 0;
var _typeof2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js"));
var pxToRem = exports.pxToRem = function pxToRem(pixels) {
  if (!pixels) {
    return;
  } else if ('string' !== typeof pixels) {
    pixels = pixels.toString();
  }
  return pixels.split(' ').map(function (value) {
    return "".concat(value * 0.0625, "rem");
  }).join(' ');
};
var arrayToClassName = exports.arrayToClassName = function arrayToClassName(array, action) {
  return array.filter(function (item) {
    return 'object' === (0, _typeof2.default)(item) ? Object.entries(item)[0][1] : item;
  }).map(function (item) {
    var value = 'object' === (0, _typeof2.default)(item) ? Object.entries(item)[0][0] : item;
    return action ? action(value) : value;
  }).join(' ');
};
var stringToRemValues = exports.stringToRemValues = function stringToRemValues(string) {
  return string.split(' ').map(function (value) {
    return pxToRem(value);
  }).join(' ');
};
var rgbToHex = exports.rgbToHex = function rgbToHex(r, g, b) {
  return '#' + [r, g, b].map(function (x) {
    var hex = x.toString(16);
    return 1 === hex.length ? '0' + hex : hex;
  }).join('');
};
var isOneOf = exports.isOneOf = function isOneOf(filetype, filetypeOptions) {
  return filetypeOptions.some(function (type) {
    return filetype.includes(type);
  });
};
var arrayToObjectByKey = exports.arrayToObjectByKey = function arrayToObjectByKey(array, key) {
  var finalObject = {};
  array.forEach(function (item) {
    return finalObject[item[key]] = item;
  });
  return finalObject;
};
var htmlDecodeTextContent = exports.htmlDecodeTextContent = function htmlDecodeTextContent(input) {
  var doc = new DOMParser().parseFromString(input, 'text/html');
  return doc.documentElement.textContent;
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/context/template-types.js":
/*!**********************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/context/template-types.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.TemplateTypesConsumer = exports.Context = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
__webpack_require__(/*! ../../scss/loading.scss */ "../app/modules/site-editor/assets/scss/loading.scss");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Context = exports.Context = _react.default.createContext();
var TemplateTypesContext = /*#__PURE__*/function (_React$Component) {
  function TemplateTypesContext(props) {
    var _this;
    (0, _classCallCheck2.default)(this, TemplateTypesContext);
    _this = _callSuper(this, TemplateTypesContext, [props]);
    _this.state = {
      templateTypes: [],
      loading: true,
      error: false
    };
    return _this;
  }
  (0, _inherits2.default)(TemplateTypesContext, _React$Component);
  return (0, _createClass2.default)(TemplateTypesContext, [{
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;
      this.getTemplateTypes().then(function (response) {
        _this2.setState({
          templateTypes: response,
          loading: false
        });
      }).fail(function (error) {
        _this2.setState({
          error: error.statusText ? error.statusText : error,
          loading: false
        });
      });
    }
  }, {
    key: "getTemplateTypes",
    value: function getTemplateTypes() {
      return elementorCommon.ajax.load({
        action: 'app_site_editor_template_types'
      });
    }
  }, {
    key: "render",
    value: function render() {
      if (this.state.error) {
        return /*#__PURE__*/_react.default.createElement("div", {
          className: "e-loading-wrapper"
        }, /*#__PURE__*/_react.default.createElement("h3", null, __('Error:', 'elementor'), " ", this.state.error));
      }
      if (this.state.loading) {
        return /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loading"
        }, /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-wrapper"
        }, /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader"
        }, /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-boxes"
        }, /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-box"
        }), /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-box"
        }), /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-box"
        }), /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loader-box"
        }))), /*#__PURE__*/_react.default.createElement("div", {
          className: "elementor-loading-title"
        }, __('Loading', 'elementor'))));
      }
      return /*#__PURE__*/_react.default.createElement(Context.Provider, {
        value: this.state
      }, this.props.children);
    }
  }]);
}(_react.default.Component);
(0, _defineProperty2.default)(TemplateTypesContext, "propTypes", {
  children: PropTypes.object.isRequired
});
var TemplateTypesConsumer = exports.TemplateTypesConsumer = Context.Consumer;
var _default = exports["default"] = TemplateTypesContext;

/***/ }),

/***/ "../app/modules/site-editor/assets/js/module.js":
/*!******************************************************!*\
  !*** ../app/modules/site-editor/assets/js/module.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _router = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _promotion = _interopRequireDefault(__webpack_require__(/*! ./pages/promotion */ "../app/modules/site-editor/assets/js/pages/promotion.js"));
var _notFound = _interopRequireDefault(__webpack_require__(/*! ./pages/not-found */ "../app/modules/site-editor/assets/js/pages/not-found.js"));
var SiteEditor = exports["default"] = /*#__PURE__*/function () {
  function SiteEditor() {
    (0, _classCallCheck2.default)(this, SiteEditor);
    this.saveTemplateTypesToCache();
    _router.default.addRoute({
      path: '/site-editor/promotion',
      component: _promotion.default
    });
    _router.default.addRoute({
      path: '/site-editor/*',
      component: _notFound.default
    });
  }
  return (0, _createClass2.default)(SiteEditor, [{
    key: "saveTemplateTypesToCache",
    value: function saveTemplateTypesToCache() {
      var types = this.getTypes();
      elementorCommon.ajax.addRequestCache({
        unique_id: 'app_site_editor_template_types'
      }, types);
    }
  }, {
    key: "getTypes",
    value: function getTypes() {
      return [{
        type: 'header',
        icon: 'eicon-header',
        title: __('Header', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/header.svg'
        },
        tooltip_data: {
          title: __('What is a Header Template?', 'elementor'),
          content: __('The header template allows you to easily design and edit custom WordPress headers so you are no longer constrained by your theme’s header design limitations.', 'elementor'),
          tip: __('You can create multiple headers, and assign each to different areas of your site.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-header/',
          video_url: 'https://www.youtube.com/embed/HHy5RK6W-6I'
        }
      }, {
        type: 'footer',
        icon: 'eicon-footer',
        title: __('Footer', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/footer.svg'
        },
        tooltip_data: {
          title: __('What is a Footer Template?', 'elementor'),
          content: __('The footer template allows you to easily design and edit custom WordPress footers without the limits of your theme’s footer design constraints', 'elementor'),
          tip: __('You can create multiple footers, and assign each to different areas of your site.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-footer/',
          video_url: 'https://www.youtube.com/embed/xa8DoR4tQrY'
        }
      }, {
        type: 'single-page',
        icon: 'eicon-single-page',
        title: __('Single Page', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/single-page.svg'
        },
        tooltip_data: {
          title: __('What is a Single Page Template?', 'elementor'),
          content: __('A single page template allows you to easily create the layout and style of pages, ensuring design consistency across all the pages of your site.', 'elementor'),
          tip: __('You can create multiple single page templates, and assign each to different areas of your site.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-page/',
          video_url: 'https://www.youtube.com/embed/_y5eZ60lVoY'
        }
      }, {
        type: 'single-post',
        icon: 'eicon-single-post',
        title: __('Single Post', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/single-post.svg'
        },
        tooltip_data: {
          title: __('What is a Single Post Template?', 'elementor'),
          content: __('A single post template allows you to easily design the layout and style of posts, ensuring a design consistency throughout all your blog posts, for example.', 'elementor'),
          tip: __('You can create multiple single post templates, and assign each to a different category.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-post/',
          video_url: 'https://www.youtube.com/embed/8Fk-Edu7DL0'
        }
      }, {
        type: 'archive',
        icon: 'eicon-archive',
        title: __('Archive', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/archive.svg'
        },
        tooltip_data: {
          title: __('What is an Archive Template?', 'elementor'),
          content: __('An archive template allows you to easily design the layout and style of archive pages - those pages that show a list of posts (e.g. a blog’s list of recent posts), which may be filtered by terms such as authors, categories, tags, search results, etc.', 'elementor'),
          tip: __('If you’d like a different style for a specific category, it’s easy to create a separate archive template whose condition is to only display when users are viewing that category’s list of posts.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-archive/',
          video_url: 'https://www.youtube.com/embed/wxElpEh9bfA'
        }
      }, {
        type: 'search-results',
        icon: 'eicon-search-results',
        title: __('search results page', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/search-results.svg'
        },
        tooltip_data: {
          title: __('What is a Search Results Template?', 'elementor'),
          content: __('You can easily control the layout and design of the Search Results page with the Search Results template, which is simply a special archive template just for displaying search results.', 'elementor'),
          tip: __('You can customize the message if there are no results for the search term.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-search-results/',
          video_url: 'https://www.youtube.com/embed/KKkIU_L5sDo'
        }
      }, {
        type: 'product',
        icon: 'eicon-single-product',
        title: __('Product', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/product.svg'
        },
        tooltip_data: {
          title: __('What is a Single Product Template?', 'elementor'),
          content: __('A single product template allows you to easily design the layout and style of WooCommerce single product pages, and apply that template to various conditions that you assign.', 'elementor'),
          tip: __('You can create multiple single product templates, and assign each to different types of products, enabling a custom design for each group of similar products.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-product/',
          video_url: 'https://www.youtube.com/embed/PjhoB1RWkBM'
        }
      }, {
        type: 'products',
        icon: 'eicon-products',
        title: __('Products Archive', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/products.svg'
        },
        tooltip_data: {
          title: __('What is a Products Archive Template?', 'elementor'),
          content: __('A products archive template allows you to easily design the layout and style of your WooCommerce shop page or other product archive pages - those pages that show a list of products, which may be filtered by terms such as categories, tags, etc.', 'elementor'),
          tip: __('You can create multiple archive product templates, and assign each to different categories of products. This gives you the freedom to customize the appearance for each type of product being shown.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-products-archive/',
          video_url: 'https://www.youtube.com/embed/cQLeirgkguA'
        }
      }, {
        type: 'error-404',
        icon: 'eicon-error-404',
        title: __('404 page', 'elementor'),
        urls: {
          thumbnail: elementorAppConfig.assets_url + '/images/app/site-editor/error-404.svg'
        },
        tooltip_data: {
          title: __('What is a 404 Page Template?', 'elementor'),
          content: __('A 404 page template allows you to easily design the layout and style of the page that is displayed when a visitor arrives at a page that does not exist.', 'elementor'),
          tip: __('Keep your site\'s visitors happy when they get lost by displaying your recent posts, a search bar, or any information that might help the user find what they were looking for.', 'elementor'),
          docs: 'https://go.elementor.com/app-theme-builder-404/',
          video_url: 'https://www.youtube.com/embed/ACCNp9tBMQg'
        }
      }];
    }
  }]);
}();

/***/ }),

/***/ "../app/modules/site-editor/assets/js/molecules/site-part.js":
/*!*******************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/molecules/site-part.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SitePart;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _card = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/card/card */ "../app/assets/js/ui/card/card.js"));
var _cardHeader = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/card/card-header */ "../app/assets/js/ui/card/card-header.js"));
var _cardBody = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/card/card-body */ "../app/assets/js/ui/card/card-body.js"));
var _cardImage = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/card/card-image */ "../app/assets/js/ui/card/card-image.js"));
var _heading = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
__webpack_require__(/*! ./site-part.scss */ "../app/modules/site-editor/assets/js/molecules/site-part.scss");
function SitePart(props) {
  return /*#__PURE__*/_react.default.createElement(_card.default, {
    className: "e-site-part"
  }, /*#__PURE__*/_react.default.createElement(_cardHeader.default, null, /*#__PURE__*/_react.default.createElement(_heading.default, {
    tag: "h1",
    variant: "text-sm",
    className: "eps-card__headline"
  }, props.title), props.actionButton), /*#__PURE__*/_react.default.createElement(_cardBody.default, null, /*#__PURE__*/_react.default.createElement(_cardImage.default, {
    alt: props.title,
    src: props.thumbnail
  }, props.children)));
}
SitePart.propTypes = {
  thumbnail: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  children: PropTypes.object,
  showIndicator: PropTypes.bool,
  actionButton: PropTypes.object
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/molecules/site-part.scss":
/*!*********************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/molecules/site-part.scss ***!
  \*********************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/site-editor/assets/js/organisms/all-parts-button.js":
/*!**************************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/organisms/all-parts-button.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = AllPartsButton;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _menuItem = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/menu/menu-item */ "../app/assets/js/ui/menu/menu-item.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
function AllPartsButton(props) {
  var activePathname = '/site-editor/templates';
  return /*#__PURE__*/_react.default.createElement(_router.Match, {
    path: activePathname
  }, function (_ref) {
    var match = _ref.match;
    var className = "eps-menu-item__link".concat(match || props.promotion ? ' eps-menu-item--active' : '');
    return /*#__PURE__*/_react.default.createElement(_menuItem.default, {
      text: __('All Parts', 'elementor'),
      className: className,
      icon: "eicon-filter",
      url: props.url
    });
  });
}
AllPartsButton.propTypes = {
  url: PropTypes.string,
  promotion: PropTypes.bool
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/organisms/menu.js":
/*!**************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/organisms/menu.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Menu;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _menu = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/menu/menu */ "../app/assets/js/ui/menu/menu.js"));
var _templateTypes = __webpack_require__(/*! ../context/template-types */ "../app/modules/site-editor/assets/js/context/template-types.js");
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _addNewButton = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/add-new-button */ "../app/assets/js/ui/molecules/add-new-button.js"));
__webpack_require__(/*! ./menu.scss */ "../app/modules/site-editor/assets/js/organisms/menu.scss");
function Menu(props) {
  var _React$useContext = _react.default.useContext(_templateTypes.Context),
    templateTypes = _React$useContext.templateTypes,
    actionButton = function actionButton(itemProps) {
      var className = 'eps-menu-item__action-button';
      if (props.promotion) {
        return /*#__PURE__*/_react.default.createElement(_button.default, {
          text: __('Upgrade Now', 'elementor'),
          hideText: true,
          icon: "eicon-lock",
          className: className
        });
      }
      var goToCreate = function goToCreate() {
        location.href = itemProps.urls.create;
      };
      return /*#__PURE__*/_react.default.createElement("span", {
        className: className
      }, /*#__PURE__*/_react.default.createElement(_addNewButton.default, {
        hideText: true,
        size: "sm",
        onClick: function onClick() {
          return goToCreate();
        }
      }));
    };
  return /*#__PURE__*/_react.default.createElement(_menu.default, {
    menuItems: templateTypes,
    actionButton: actionButton,
    promotion: props.promotion
  }, props.allPartsButton, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-menu__title"
  }, __('Site Parts', 'elementor')));
}
Menu.propTypes = {
  allPartsButton: PropTypes.element.isRequired,
  promotion: PropTypes.bool
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/organisms/menu.scss":
/*!****************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/organisms/menu.scss ***!
  \****************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/site-editor/assets/js/organisms/site-parts.js":
/*!********************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/organisms/site-parts.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SiteParts;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _cssGrid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/css-grid */ "../app/assets/js/ui/atoms/css-grid.js"));
var _modal = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/modal/modal */ "../app/assets/js/ui/modal/modal.js"));
var _sitePart = _interopRequireDefault(__webpack_require__(/*! ../molecules/site-part */ "../app/modules/site-editor/assets/js/molecules/site-part.js"));
var _templateTypes = __webpack_require__(/*! ../context/template-types */ "../app/modules/site-editor/assets/js/context/template-types.js");
/* eslint-disable jsx-a11y/iframe-has-title */

var InfoButton = function InfoButton(props) {
  var toggleButtonProps = {
    text: __('Info', 'elementor'),
    hideText: true,
    icon: 'eicon-info-circle e-site-part__info-toggle'
  };
  return /*#__PURE__*/_react.default.createElement(_modal.default, {
    toggleButtonProps: toggleButtonProps,
    title: props.title
  }, /*#__PURE__*/_react.default.createElement(_cssGrid.default, {
    columns: 2,
    spacing: 60
  }, /*#__PURE__*/_react.default.createElement("section", null, /*#__PURE__*/_react.default.createElement("h3", null, props.type), /*#__PURE__*/_react.default.createElement("p", null, props.content, /*#__PURE__*/_react.default.createElement("br", null), /*#__PURE__*/_react.default.createElement(_button.default, {
    text: __('Learn More', 'elementor'),
    color: "link",
    target: "_blank",
    url: props.docs
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-modal__tip"
  }, /*#__PURE__*/_react.default.createElement("h3", null, __('Tip', 'elementor')), /*#__PURE__*/_react.default.createElement("p", null, props.tip))), /*#__PURE__*/_react.default.createElement("section", null, /*#__PURE__*/_react.default.createElement("h3", null, __('Watch Video', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "video-wrapper"
  }, /*#__PURE__*/_react.default.createElement("iframe", {
    id: "ytplayer",
    src: props.video_url,
    frameBorder: "0"
  })))));
};
InfoButton.propTypes = {
  content: PropTypes.string.isRequired,
  docs: PropTypes.string.isRequired,
  tip: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  type: PropTypes.string.isRequired,
  video_url: PropTypes.string.isRequired
};
function SiteParts(props) {
  var _React$useContext = _react.default.useContext(_templateTypes.Context),
    templateTypes = _React$useContext.templateTypes;
  return /*#__PURE__*/_react.default.createElement(_cssGrid.default, {
    className: "e-site-editor__site-parts",
    colMinWidth: 200,
    spacing: 25
  }, templateTypes.map(function (item) {
    return /*#__PURE__*/_react.default.createElement(_sitePart.default, (0, _extends2.default)({
      className: "e-site-editor__site-part",
      actionButton: /*#__PURE__*/_react.default.createElement(InfoButton, (0, _extends2.default)({
        type: item.title
      }, item.tooltip_data)),
      thumbnail: item.urls.thumbnail,
      key: item.type
    }, item), _react.default.createElement(props.hoverElement, item));
  }));
}
SiteParts.propTypes = {
  hoverElement: PropTypes.func.isRequired
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/package.js":
/*!*******************************************************!*\
  !*** ../app/modules/site-editor/assets/js/package.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _allPartsButton = _interopRequireDefault(__webpack_require__(/*! ./organisms/all-parts-button */ "../app/modules/site-editor/assets/js/organisms/all-parts-button.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ./templates/layout */ "../app/modules/site-editor/assets/js/templates/layout.js"));
var _module = _interopRequireDefault(__webpack_require__(/*! ./module */ "../app/modules/site-editor/assets/js/module.js"));
var _notFound = _interopRequireDefault(__webpack_require__(/*! ./pages/not-found */ "../app/modules/site-editor/assets/js/pages/not-found.js"));
var _siteParts = _interopRequireDefault(__webpack_require__(/*! ./organisms/site-parts */ "../app/modules/site-editor/assets/js/organisms/site-parts.js"));
var _sitePart = _interopRequireDefault(__webpack_require__(/*! ./molecules/site-part */ "../app/modules/site-editor/assets/js/molecules/site-part.js"));
var _templateTypes = __webpack_require__(/*! ./context/template-types */ "../app/modules/site-editor/assets/js/context/template-types.js");
// Alphabetical order.
var _default = exports["default"] = {
  AllPartsButton: _allPartsButton.default,
  Layout: _layout.default,
  Module: _module.default,
  NotFound: _notFound.default,
  SitePart: _sitePart.default,
  SiteParts: _siteParts.default,
  TemplateTypesContext: _templateTypes.Context
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/pages/not-found.js":
/*!***************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/pages/not-found.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = NotFound;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _dialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/dialog/dialog */ "../app/assets/js/ui/dialog/dialog.js"));
function NotFound() {
  var url = _react.default.useMemo(function () {
    var _elementorAppConfig$m;
    return ((_elementorAppConfig$m = elementorAppConfig.menu_url.split('#')) === null || _elementorAppConfig$m === void 0 ? void 0 : _elementorAppConfig$m[1]) || '/site-editor';
  }, []);
  return /*#__PURE__*/_react.default.createElement(_dialog.default, {
    title: __('Theme Builder could not be loaded', 'elementor'),
    text: __('We’re sorry, but something went wrong. Click on ‘Learn more’ and follow each of the steps to quickly solve it.', 'elementor'),
    approveButtonUrl: "https://go.elementor.com/app-theme-builder-load-issue/",
    approveButtonColor: "link",
    approveButtonTarget: "_blank",
    approveButtonText: __('Learn More', 'elementor'),
    dismissButtonText: __('Go Back', 'elementor'),
    dismissButtonUrl: url
  });
}

/***/ }),

/***/ "../app/modules/site-editor/assets/js/pages/promotion.js":
/*!***************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/pages/promotion.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Promotion;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _allPartsButton = _interopRequireDefault(__webpack_require__(/*! ../organisms/all-parts-button */ "../app/modules/site-editor/assets/js/organisms/all-parts-button.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/molecules/button */ "../app/assets/js/ui/molecules/button.js"));
var _cardOverlay = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/card/card-overlay */ "../app/assets/js/ui/card/card-overlay.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _heading = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/heading */ "../app/assets/js/ui/atoms/heading.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../templates/layout */ "../app/modules/site-editor/assets/js/templates/layout.js"));
var _siteParts = _interopRequireDefault(__webpack_require__(/*! ../organisms/site-parts */ "../app/modules/site-editor/assets/js/organisms/site-parts.js"));
var _text = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/atoms/text */ "../app/assets/js/ui/atoms/text.js"));
__webpack_require__(/*! ./promotion.scss */ "../app/modules/site-editor/assets/js/pages/promotion.scss");
function Promotion() {
  var promotionUrl = elementorAppConfig.promotion.upgrade_url || 'https://go.elementor.com/go-pro-theme-builder/',
    PromotionHoverElement = function PromotionHoverElement(props) {
      var promotionUrlWithType = "".concat(promotionUrl, "?type=").concat(props.type);
      return /*#__PURE__*/_react.default.createElement(_cardOverlay.default, {
        className: "e-site-editor__promotion-overlay"
      }, /*#__PURE__*/_react.default.createElement("a", {
        className: "e-site-editor__promotion-overlay__link",
        target: "_blank",
        rel: "noopener noreferrer",
        href: promotionUrlWithType
      }, /*#__PURE__*/_react.default.createElement("i", {
        className: "e-site-editor__promotion-overlay__icon eicon-lock"
      }), /*#__PURE__*/_react.default.createElement(_button.default, {
        size: "sm",
        color: "brand",
        variant: "contained",
        text: __('Upgrade', 'elementor')
      })));
    };
  PromotionHoverElement.propTypes = {
    className: PropTypes.string,
    type: PropTypes.string.isRequired
  };
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    allPartsButton: /*#__PURE__*/_react.default.createElement(_allPartsButton.default, {
      promotion: true
    }),
    promotion: true
  }, /*#__PURE__*/_react.default.createElement("section", {
    className: "e-site-editor__promotion"
  }, /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    className: "page-header"
  }, /*#__PURE__*/_react.default.createElement(_grid.default, {
    item: true,
    sm: 7,
    justify: "end"
  }, /*#__PURE__*/_react.default.createElement(_heading.default, {
    variant: "h1"
  }, __('Customize every part of your site', 'elementor')), /*#__PURE__*/_react.default.createElement(_text.default, null, __('Get total control, consistency and a faster workflow by designing the recurring parts that make up a complete website like the Header & Footer, Archive, 404, WooCommerce pages and more.', 'elementor'))), /*#__PURE__*/_react.default.createElement(_grid.default, {
    item: true,
    container: true,
    justify: "end",
    alignItems: "start",
    sm: 5
  }, /*#__PURE__*/_react.default.createElement(_button.default, {
    size: "sm",
    color: "cta",
    variant: "contained",
    url: promotionUrl,
    target: "_blank",
    text: __('Upgrade Now', 'elementor')
  }))), /*#__PURE__*/_react.default.createElement("hr", {
    className: "eps-separator"
  }), /*#__PURE__*/_react.default.createElement(_siteParts.default, {
    hoverElement: PromotionHoverElement
  })));
}

/***/ }),

/***/ "../app/modules/site-editor/assets/js/pages/promotion.scss":
/*!*****************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/pages/promotion.scss ***!
  \*****************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/site-editor/assets/js/templates/layout.js":
/*!****************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/templates/layout.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Layout;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _page = _interopRequireDefault(__webpack_require__(/*! elementor-app/layout/page */ "../app/assets/js/layout/page.js"));
var _menu = _interopRequireDefault(__webpack_require__(/*! ../organisms/menu */ "../app/modules/site-editor/assets/js/organisms/menu.js"));
var _templateTypes = _interopRequireDefault(__webpack_require__(/*! ../context/template-types */ "../app/modules/site-editor/assets/js/context/template-types.js"));
__webpack_require__(/*! ./site-editor.scss */ "../app/modules/site-editor/assets/js/templates/site-editor.scss");
function Layout(props) {
  var _props$titleRedirectR;
  var config = {
    title: __('Theme Builder', 'elementor'),
    titleRedirectRoute: (_props$titleRedirectR = props.titleRedirectRoute) !== null && _props$titleRedirectR !== void 0 ? _props$titleRedirectR : null,
    headerButtons: props.headerButtons,
    sidebar: /*#__PURE__*/_react.default.createElement(_menu.default, {
      allPartsButton: props.allPartsButton,
      promotion: props.promotion
    }),
    content: props.children
  };
  return /*#__PURE__*/_react.default.createElement(_templateTypes.default, null, /*#__PURE__*/_react.default.createElement(_page.default, config));
}
Layout.propTypes = {
  headerButtons: PropTypes.arrayOf(PropTypes.object),
  allPartsButton: PropTypes.element.isRequired,
  children: PropTypes.object.isRequired,
  promotion: PropTypes.bool,
  titleRedirectRoute: PropTypes.string
};
Layout.defaultProps = {
  headerButtons: []
};

/***/ }),

/***/ "../app/modules/site-editor/assets/js/templates/site-editor.scss":
/*!***********************************************************************!*\
  !*** ../app/modules/site-editor/assets/js/templates/site-editor.scss ***!
  \***********************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/site-editor/assets/scss/loading.scss":
/*!***********************************************************!*\
  !*** ../app/modules/site-editor/assets/scss/loading.scss ***!
  \***********************************************************/
/***/ (() => {



/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/OverloadYield.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/OverloadYield.js ***!
  \***************************************************************/
/***/ ((module) => {

function _OverloadYield(e, d) {
  this.v = e, this.k = d;
}
module.exports = _OverloadYield, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayLikeToArray.js ***!
  \******************************************************************/
/***/ ((module) => {

function _arrayLikeToArray(r, a) {
  (null == a || a > r.length) && (a = r.length);
  for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
  return n;
}
module.exports = _arrayLikeToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithHoles.js ***!
  \****************************************************************/
/***/ ((module) => {

function _arrayWithHoles(r) {
  if (Array.isArray(r)) return r;
}
module.exports = _arrayWithHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _arrayWithoutHoles(r) {
  if (Array.isArray(r)) return arrayLikeToArray(r);
}
module.exports = _arrayWithoutHoles, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js":
/*!***********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/assertThisInitialized.js ***!
  \***********************************************************************/
/***/ ((module) => {

function _assertThisInitialized(e) {
  if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  return e;
}
module.exports = _assertThisInitialized, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \******************************************************************/
/***/ ((module) => {

function asyncGeneratorStep(n, t, e, r, o, a, c) {
  try {
    var i = n[a](c),
      u = i.value;
  } catch (n) {
    return void e(n);
  }
  i.done ? t(u) : Promise.resolve(u).then(r, o);
}
function _asyncToGenerator(n) {
  return function () {
    var t = this,
      e = arguments;
    return new Promise(function (r, o) {
      var a = n.apply(t, e);
      function _next(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "next", n);
      }
      function _throw(n) {
        asyncGeneratorStep(a, r, o, _next, _throw, "throw", n);
      }
      _next(void 0);
    });
  };
}
module.exports = _asyncToGenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/classCallCheck.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/classCallCheck.js ***!
  \****************************************************************/
/***/ ((module) => {

function _classCallCheck(a, n) {
  if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function");
}
module.exports = _classCallCheck, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/createClass.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/createClass.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var toPropertyKey = __webpack_require__(/*! ./toPropertyKey.js */ "../node_modules/@babel/runtime/helpers/toPropertyKey.js");
function _defineProperties(e, r) {
  for (var t = 0; t < r.length; t++) {
    var o = r[t];
    o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, toPropertyKey(o.key), o);
  }
}
function _createClass(e, r, t) {
  return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", {
    writable: !1
  }), e;
}
module.exports = _createClass, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

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

/***/ "../node_modules/@babel/runtime/helpers/extends.js":
/*!*********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/extends.js ***!
  \*********************************************************/
/***/ ((module) => {

function _extends() {
  return module.exports = _extends = Object.assign ? Object.assign.bind() : function (n) {
    for (var e = 1; e < arguments.length; e++) {
      var t = arguments[e];
      for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]);
    }
    return n;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _extends.apply(null, arguments);
}
module.exports = _extends, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/get.js":
/*!*****************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/get.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var superPropBase = __webpack_require__(/*! ./superPropBase.js */ "../node_modules/@babel/runtime/helpers/superPropBase.js");
function _get() {
  return module.exports = _get = "undefined" != typeof Reflect && Reflect.get ? Reflect.get.bind() : function (e, t, r) {
    var p = superPropBase(e, t);
    if (p) {
      var n = Object.getOwnPropertyDescriptor(p, t);
      return n.get ? n.get.call(arguments.length < 3 ? e : r) : n.value;
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _get.apply(null, arguments);
}
module.exports = _get, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/getPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _getPrototypeOf(t) {
  return module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) {
    return t.__proto__ || Object.getPrototypeOf(t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _getPrototypeOf(t);
}
module.exports = _getPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/inherits.js":
/*!**********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/inherits.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _inherits(t, e) {
  if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function");
  t.prototype = Object.create(e && e.prototype, {
    constructor: {
      value: t,
      writable: !0,
      configurable: !0
    }
  }), Object.defineProperty(t, "prototype", {
    writable: !1
  }), e && setPrototypeOf(t, e);
}
module.exports = _inherits, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/iterableToArray.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArray.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _iterableToArray(r) {
  if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r);
}
module.exports = _iterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js":
/*!**********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js ***!
  \**********************************************************************/
/***/ ((module) => {

function _iterableToArrayLimit(r, l) {
  var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
  if (null != t) {
    var e,
      n,
      i,
      u,
      a = [],
      f = !0,
      o = !1;
    try {
      if (i = (t = t.call(r)).next, 0 === l) {
        if (Object(t) !== t) return;
        f = !1;
      } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
    } catch (r) {
      o = !0, n = r;
    } finally {
      try {
        if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return;
      } finally {
        if (o) throw n;
      }
    }
    return a;
  }
}
module.exports = _iterableToArrayLimit, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableRest.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableRest.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableRest, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/nonIterableSpread.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
module.exports = _nonIterableSpread, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js":
/*!*************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutProperties.js ***!
  \*************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var objectWithoutPropertiesLoose = __webpack_require__(/*! ./objectWithoutPropertiesLoose.js */ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js");
function _objectWithoutProperties(e, t) {
  if (null == e) return {};
  var o,
    r,
    i = objectWithoutPropertiesLoose(e, t);
  if (Object.getOwnPropertySymbols) {
    var n = Object.getOwnPropertySymbols(e);
    for (r = 0; r < n.length; r++) o = n[r], -1 === t.indexOf(o) && {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
  }
  return i;
}
module.exports = _objectWithoutProperties, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js":
/*!******************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/objectWithoutPropertiesLoose.js ***!
  \******************************************************************************/
/***/ ((module) => {

function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (-1 !== e.indexOf(n)) continue;
    t[n] = r[n];
  }
  return t;
}
module.exports = _objectWithoutPropertiesLoose, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js":
/*!***************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
var assertThisInitialized = __webpack_require__(/*! ./assertThisInitialized.js */ "../node_modules/@babel/runtime/helpers/assertThisInitialized.js");
function _possibleConstructorReturn(t, e) {
  if (e && ("object" == _typeof(e) || "function" == typeof e)) return e;
  if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined");
  return assertThisInitialized(t);
}
module.exports = _possibleConstructorReturn, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regenerator.js":
/*!*************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regenerator.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function _regenerator() {
  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */
  var e,
    t,
    r = "function" == typeof Symbol ? Symbol : {},
    n = r.iterator || "@@iterator",
    o = r.toStringTag || "@@toStringTag";
  function i(r, n, o, i) {
    var c = n && n.prototype instanceof Generator ? n : Generator,
      u = Object.create(c.prototype);
    return regeneratorDefine(u, "_invoke", function (r, n, o) {
      var i,
        c,
        u,
        f = 0,
        p = o || [],
        y = !1,
        G = {
          p: 0,
          n: 0,
          v: e,
          a: d,
          f: d.bind(e, 4),
          d: function d(t, r) {
            return i = t, c = 0, u = e, G.n = r, a;
          }
        };
      function d(r, n) {
        for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) {
          var o,
            i = p[t],
            d = G.p,
            l = i[2];
          r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0));
        }
        if (o || r > 1) return a;
        throw y = !0, n;
      }
      return function (o, p, l) {
        if (f > 1) throw TypeError("Generator is already running");
        for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) {
          i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u);
          try {
            if (f = 2, i) {
              if (c || (o = "next"), t = i[o]) {
                if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object");
                if (!t.done) return t;
                u = t.value, c < 2 && (c = 0);
              } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1);
              i = e;
            } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break;
          } catch (t) {
            i = e, c = 1, u = t;
          } finally {
            f = 1;
          }
        }
        return {
          value: t,
          done: y
        };
      };
    }(r, o, i), !0), u;
  }
  var a = {};
  function Generator() {}
  function GeneratorFunction() {}
  function GeneratorFunctionPrototype() {}
  t = Object.getPrototypeOf;
  var c = [][n] ? t(t([][n]())) : (regeneratorDefine(t = {}, n, function () {
      return this;
    }), t),
    u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c);
  function f(e) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, regeneratorDefine(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e;
  }
  return GeneratorFunction.prototype = GeneratorFunctionPrototype, regeneratorDefine(u, "constructor", GeneratorFunctionPrototype), regeneratorDefine(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", regeneratorDefine(GeneratorFunctionPrototype, o, "GeneratorFunction"), regeneratorDefine(u), regeneratorDefine(u, o, "Generator"), regeneratorDefine(u, n, function () {
    return this;
  }), regeneratorDefine(u, "toString", function () {
    return "[object Generator]";
  }), (module.exports = _regenerator = function _regenerator() {
    return {
      w: i,
      m: f
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regenerator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsync.js ***!
  \******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
function _regeneratorAsync(n, e, r, t, o) {
  var a = regeneratorAsyncGen(n, e, r, t, o);
  return a.next().then(function (n) {
    return n.done ? n.value : a.next();
  });
}
module.exports = _regeneratorAsync, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js":
/*!*********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js ***!
  \*********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
function _regeneratorAsyncGen(r, e, t, o, n) {
  return new regeneratorAsyncIterator(regenerator().w(r, e, t, o), n || Promise);
}
module.exports = _regeneratorAsyncGen, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js ***!
  \**************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regeneratorDefine = __webpack_require__(/*! ./regeneratorDefine.js */ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js");
function AsyncIterator(t, e) {
  function n(r, o, i, f) {
    try {
      var c = t[r](o),
        u = c.value;
      return u instanceof OverloadYield ? e.resolve(u.v).then(function (t) {
        n("next", t, i, f);
      }, function (t) {
        n("throw", t, i, f);
      }) : e.resolve(u).then(function (t) {
        c.value = t, i(c);
      }, function (t) {
        return n("throw", t, i, f);
      });
    } catch (t) {
      f(t);
    }
  }
  var r;
  this.next || (regeneratorDefine(AsyncIterator.prototype), regeneratorDefine(AsyncIterator.prototype, "function" == typeof Symbol && Symbol.asyncIterator || "@asyncIterator", function () {
    return this;
  })), regeneratorDefine(this, "_invoke", function (t, o, i) {
    function f() {
      return new e(function (e, r) {
        n(t, i, e, r);
      });
    }
    return r = r ? r.then(f, f) : f();
  }, !0);
}
module.exports = AsyncIterator, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorDefine.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorDefine.js ***!
  \*******************************************************************/
/***/ ((module) => {

function _regeneratorDefine(e, r, n, t) {
  var i = Object.defineProperty;
  try {
    i({}, "", {});
  } catch (e) {
    i = 0;
  }
  module.exports = _regeneratorDefine = function regeneratorDefine(e, r, n, t) {
    if (r) i ? i(e, r, {
      value: n,
      enumerable: !t,
      configurable: !t,
      writable: !t
    }) : e[r] = n;else {
      var o = function o(r, n) {
        _regeneratorDefine(e, r, function (e) {
          return this._invoke(r, n, e);
        });
      };
      o("next", 0), o("throw", 1), o("return", 2);
    }
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _regeneratorDefine(e, r, n, t);
}
module.exports = _regeneratorDefine, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorKeys.js ***!
  \*****************************************************************/
/***/ ((module) => {

function _regeneratorKeys(e) {
  var n = Object(e),
    r = [];
  for (var t in n) r.unshift(t);
  return function e() {
    for (; r.length;) if ((t = r.pop()) in n) return e.value = t, e.done = !1, e;
    return e.done = !0, e;
  };
}
module.exports = _regeneratorKeys, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js":
/*!********************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorRuntime.js ***!
  \********************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var OverloadYield = __webpack_require__(/*! ./OverloadYield.js */ "../node_modules/@babel/runtime/helpers/OverloadYield.js");
var regenerator = __webpack_require__(/*! ./regenerator.js */ "../node_modules/@babel/runtime/helpers/regenerator.js");
var regeneratorAsync = __webpack_require__(/*! ./regeneratorAsync.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsync.js");
var regeneratorAsyncGen = __webpack_require__(/*! ./regeneratorAsyncGen.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncGen.js");
var regeneratorAsyncIterator = __webpack_require__(/*! ./regeneratorAsyncIterator.js */ "../node_modules/@babel/runtime/helpers/regeneratorAsyncIterator.js");
var regeneratorKeys = __webpack_require__(/*! ./regeneratorKeys.js */ "../node_modules/@babel/runtime/helpers/regeneratorKeys.js");
var regeneratorValues = __webpack_require__(/*! ./regeneratorValues.js */ "../node_modules/@babel/runtime/helpers/regeneratorValues.js");
function _regeneratorRuntime() {
  "use strict";

  var r = regenerator(),
    e = r.m(_regeneratorRuntime),
    t = (Object.getPrototypeOf ? Object.getPrototypeOf(e) : e.__proto__).constructor;
  function n(r) {
    var e = "function" == typeof r && r.constructor;
    return !!e && (e === t || "GeneratorFunction" === (e.displayName || e.name));
  }
  var o = {
    "throw": 1,
    "return": 2,
    "break": 3,
    "continue": 3
  };
  function a(r) {
    var e, t;
    return function (n) {
      e || (e = {
        stop: function stop() {
          return t(n.a, 2);
        },
        "catch": function _catch() {
          return n.v;
        },
        abrupt: function abrupt(r, e) {
          return t(n.a, o[r], e);
        },
        delegateYield: function delegateYield(r, o, a) {
          return e.resultName = o, t(n.d, regeneratorValues(r), a);
        },
        finish: function finish(r) {
          return t(n.f, r);
        }
      }, t = function t(r, _t, o) {
        n.p = e.prev, n.n = e.next;
        try {
          return r(_t, o);
        } finally {
          e.next = n.n;
        }
      }), e.resultName && (e[e.resultName] = n.v, e.resultName = void 0), e.sent = n.v, e.next = n.n;
      try {
        return r.call(this, e);
      } finally {
        n.p = e.prev, n.n = e.next;
      }
    };
  }
  return (module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return {
      wrap: function wrap(e, t, n, o) {
        return r.w(a(e), t, n, o && o.reverse());
      },
      isGeneratorFunction: n,
      mark: r.m,
      awrap: function awrap(r, e) {
        return new OverloadYield(r, e);
      },
      AsyncIterator: regeneratorAsyncIterator,
      async: function async(r, e, t, o, u) {
        return (n(e) ? regeneratorAsyncGen : regeneratorAsync)(a(r), e, t, o, u);
      },
      keys: regeneratorKeys,
      values: regeneratorValues
    };
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/regeneratorValues.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/regeneratorValues.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = (__webpack_require__(/*! ./typeof.js */ "../node_modules/@babel/runtime/helpers/typeof.js")["default"]);
function _regeneratorValues(e) {
  if (null != e) {
    var t = e["function" == typeof Symbol && Symbol.iterator || "@@iterator"],
      r = 0;
    if (t) return t.call(e);
    if ("function" == typeof e.next) return e;
    if (!isNaN(e.length)) return {
      next: function next() {
        return e && r >= e.length && (e = void 0), {
          value: e && e[r++],
          done: !e
        };
      }
    };
  }
  throw new TypeError(_typeof(e) + " is not iterable");
}
module.exports = _regeneratorValues, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js":
/*!****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/setPrototypeOf.js ***!
  \****************************************************************/
/***/ ((module) => {

function _setPrototypeOf(t, e) {
  return module.exports = _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) {
    return t.__proto__ = e, t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _setPrototypeOf(t, e);
}
module.exports = _setPrototypeOf, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/slicedToArray.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/slicedToArray.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithHoles = __webpack_require__(/*! ./arrayWithHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithHoles.js");
var iterableToArrayLimit = __webpack_require__(/*! ./iterableToArrayLimit.js */ "../node_modules/@babel/runtime/helpers/iterableToArrayLimit.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableRest = __webpack_require__(/*! ./nonIterableRest.js */ "../node_modules/@babel/runtime/helpers/nonIterableRest.js");
function _slicedToArray(r, e) {
  return arrayWithHoles(r) || iterableToArrayLimit(r, e) || unsupportedIterableToArray(r, e) || nonIterableRest();
}
module.exports = _slicedToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/superPropBase.js":
/*!***************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/superPropBase.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
function _superPropBase(t, o) {
  for (; !{}.hasOwnProperty.call(t, o) && null !== (t = getPrototypeOf(t)););
  return t;
}
module.exports = _superPropBase, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/toConsumableArray.js":
/*!*******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/toConsumableArray.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayWithoutHoles = __webpack_require__(/*! ./arrayWithoutHoles.js */ "../node_modules/@babel/runtime/helpers/arrayWithoutHoles.js");
var iterableToArray = __webpack_require__(/*! ./iterableToArray.js */ "../node_modules/@babel/runtime/helpers/iterableToArray.js");
var unsupportedIterableToArray = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js");
var nonIterableSpread = __webpack_require__(/*! ./nonIterableSpread.js */ "../node_modules/@babel/runtime/helpers/nonIterableSpread.js");
function _toConsumableArray(r) {
  return arrayWithoutHoles(r) || iterableToArray(r) || unsupportedIterableToArray(r) || nonIterableSpread();
}
module.exports = _toConsumableArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js":
/*!****************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/unsupportedIterableToArray.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var arrayLikeToArray = __webpack_require__(/*! ./arrayLikeToArray.js */ "../node_modules/@babel/runtime/helpers/arrayLikeToArray.js");
function _unsupportedIterableToArray(r, a) {
  if (r) {
    if ("string" == typeof r) return arrayLikeToArray(r, a);
    var t = {}.toString.call(r).slice(8, -1);
    return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? arrayLikeToArray(r, a) : void 0;
  }
}
module.exports = _unsupportedIterableToArray, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/regenerator/index.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/regenerator/index.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// TODO(Babel 8): Remove this file.

var runtime = __webpack_require__(/*! ../helpers/regeneratorRuntime */ "../node_modules/@babel/runtime/helpers/regeneratorRuntime.js")();
module.exports = runtime;

// Copied from https://github.com/facebook/regenerator/blob/main/packages/runtime/runtime.js#L736=
try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ }),

/***/ "../node_modules/@reach/router/es/index.js":
/*!*************************************************!*\
  !*** ../node_modules/@reach/router/es/index.js ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Link: () => (/* binding */ Link),
/* harmony export */   Location: () => (/* binding */ Location),
/* harmony export */   LocationProvider: () => (/* binding */ LocationProvider),
/* harmony export */   Match: () => (/* binding */ Match),
/* harmony export */   Redirect: () => (/* binding */ Redirect),
/* harmony export */   Router: () => (/* binding */ Router),
/* harmony export */   ServerLocation: () => (/* binding */ ServerLocation),
/* harmony export */   createHistory: () => (/* reexport safe */ _lib_history__WEBPACK_IMPORTED_MODULE_5__.createHistory),
/* harmony export */   createMemorySource: () => (/* reexport safe */ _lib_history__WEBPACK_IMPORTED_MODULE_5__.createMemorySource),
/* harmony export */   globalHistory: () => (/* reexport safe */ _lib_history__WEBPACK_IMPORTED_MODULE_5__.globalHistory),
/* harmony export */   isRedirect: () => (/* binding */ isRedirect),
/* harmony export */   matchPath: () => (/* reexport safe */ _lib_utils__WEBPACK_IMPORTED_MODULE_4__.match),
/* harmony export */   navigate: () => (/* reexport safe */ _lib_history__WEBPACK_IMPORTED_MODULE_5__.navigate),
/* harmony export */   redirectTo: () => (/* binding */ redirectTo),
/* harmony export */   useLocation: () => (/* binding */ useLocation),
/* harmony export */   useMatch: () => (/* binding */ useMatch),
/* harmony export */   useNavigate: () => (/* binding */ useNavigate),
/* harmony export */   useParams: () => (/* binding */ useParams)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");
/* harmony import */ var prop_types__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(prop_types__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! invariant */ "../node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var create_react_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! create-react-context */ "../node_modules/create-react-context/lib/index.js");
/* harmony import */ var create_react_context__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(create_react_context__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var react_lifecycles_compat__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react-lifecycles-compat */ "../node_modules/react-lifecycles-compat/react-lifecycles-compat.es.js");
/* harmony import */ var _lib_utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./lib/utils */ "../node_modules/@reach/router/es/lib/utils.js");
/* harmony import */ var _lib_history__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./lib/history */ "../node_modules/@reach/router/es/lib/history.js");
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

function _objectWithoutProperties(obj, keys) { var target = {}; for (var i in obj) { if (keys.indexOf(i) >= 0) continue; if (!Object.prototype.hasOwnProperty.call(obj, i)) continue; target[i] = obj[i]; } return target; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

/* eslint-disable jsx-a11y/anchor-has-content */








////////////////////////////////////////////////////////////////////////////////

var createNamedContext = function createNamedContext(name, defaultValue) {
  var Ctx = create_react_context__WEBPACK_IMPORTED_MODULE_2___default()(defaultValue);
  Ctx.displayName = name;
  return Ctx;
};

////////////////////////////////////////////////////////////////////////////////
// Location Context/Provider
var LocationContext = createNamedContext("Location");

// sets up a listener if there isn't one already so apps don't need to be
// wrapped in some top level provider
var Location = function Location(_ref) {
  var children = _ref.children;
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    LocationContext.Consumer,
    null,
    function (context) {
      return context ? children(context) : react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        LocationProvider,
        null,
        children
      );
    }
  );
};

var LocationProvider = function (_React$Component) {
  _inherits(LocationProvider, _React$Component);

  function LocationProvider() {
    var _temp, _this, _ret;

    _classCallCheck(this, LocationProvider);

    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }

    return _ret = (_temp = (_this = _possibleConstructorReturn(this, _React$Component.call.apply(_React$Component, [this].concat(args))), _this), _this.state = {
      context: _this.getContext(),
      refs: { unlisten: null }
    }, _temp), _possibleConstructorReturn(_this, _ret);
  }

  LocationProvider.prototype.getContext = function getContext() {
    var _props$history = this.props.history,
        navigate = _props$history.navigate,
        location = _props$history.location;

    return { navigate: navigate, location: location };
  };

  LocationProvider.prototype.componentDidCatch = function componentDidCatch(error, info) {
    if (isRedirect(error)) {
      var _navigate = this.props.history.navigate;

      _navigate(error.uri, { replace: true });
    } else {
      throw error;
    }
  };

  LocationProvider.prototype.componentDidUpdate = function componentDidUpdate(prevProps, prevState) {
    if (prevState.context.location !== this.state.context.location) {
      this.props.history._onTransitionComplete();
    }
  };

  LocationProvider.prototype.componentDidMount = function componentDidMount() {
    var _this2 = this;

    var refs = this.state.refs,
        history = this.props.history;

    history._onTransitionComplete();
    refs.unlisten = history.listen(function () {
      Promise.resolve().then(function () {
        // TODO: replace rAF with react deferred update API when it's ready https://github.com/facebook/react/issues/13306
        requestAnimationFrame(function () {
          if (!_this2.unmounted) {
            _this2.setState(function () {
              return { context: _this2.getContext() };
            });
          }
        });
      });
    });
  };

  LocationProvider.prototype.componentWillUnmount = function componentWillUnmount() {
    var refs = this.state.refs;

    this.unmounted = true;
    refs.unlisten();
  };

  LocationProvider.prototype.render = function render() {
    var context = this.state.context,
        children = this.props.children;

    return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
      LocationContext.Provider,
      { value: context },
      typeof children === "function" ? children(context) : children || null
    );
  };

  return LocationProvider;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component));

////////////////////////////////////////////////////////////////////////////////


LocationProvider.defaultProps = {
  history: _lib_history__WEBPACK_IMPORTED_MODULE_5__.globalHistory
};
 true ? LocationProvider.propTypes = {
  history: (prop_types__WEBPACK_IMPORTED_MODULE_6___default().object).isRequired
} : 0;
var ServerLocation = function ServerLocation(_ref2) {
  var url = _ref2.url,
      children = _ref2.children;

  var searchIndex = url.indexOf("?");
  var searchExists = searchIndex > -1;
  var pathname = void 0;
  var search = "";
  var hash = "";

  if (searchExists) {
    pathname = url.substring(0, searchIndex);
    search = url.substring(searchIndex);
  } else {
    pathname = url;
  }

  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    LocationContext.Provider,
    {
      value: {
        location: {
          pathname: pathname,
          search: search,
          hash: hash
        },
        navigate: function navigate() {
          throw new Error("You can't call navigate on the server.");
        }
      }
    },
    children
  );
};
////////////////////////////////////////////////////////////////////////////////
// Sets baseuri and basepath for nested routers and links
var BaseContext = createNamedContext("Base", { baseuri: "/", basepath: "/" });

////////////////////////////////////////////////////////////////////////////////
// The main event, welcome to the show everybody.
var Router = function Router(props) {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    BaseContext.Consumer,
    null,
    function (baseContext) {
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        Location,
        null,
        function (locationContext) {
          return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(RouterImpl, _extends({}, baseContext, locationContext, props));
        }
      );
    }
  );
};

var RouterImpl = function (_React$PureComponent) {
  _inherits(RouterImpl, _React$PureComponent);

  function RouterImpl() {
    _classCallCheck(this, RouterImpl);

    return _possibleConstructorReturn(this, _React$PureComponent.apply(this, arguments));
  }

  RouterImpl.prototype.render = function render() {
    var _props = this.props,
        location = _props.location,
        _navigate2 = _props.navigate,
        basepath = _props.basepath,
        primary = _props.primary,
        children = _props.children,
        baseuri = _props.baseuri,
        _props$component = _props.component,
        component = _props$component === undefined ? "div" : _props$component,
        domProps = _objectWithoutProperties(_props, ["location", "navigate", "basepath", "primary", "children", "baseuri", "component"]);

    var routes = react__WEBPACK_IMPORTED_MODULE_0___default().Children.toArray(children).reduce(function (array, child) {
      var routes = createRoute(basepath)(child);
      return array.concat(routes);
    }, []);
    var pathname = location.pathname;


    var match = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.pick)(routes, pathname);

    if (match) {
      var params = match.params,
          uri = match.uri,
          route = match.route,
          element = match.route.value;

      // remove the /* from the end for child routes relative paths

      basepath = route.default ? basepath : route.path.replace(/\*$/, "");

      var props = _extends({}, params, {
        uri: uri,
        location: location,
        navigate: function navigate(to, options) {
          return _navigate2((0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(to, uri), options);
        }
      });

      var clone = react__WEBPACK_IMPORTED_MODULE_0___default().cloneElement(element, props, element.props.children ? react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        Router,
        { location: location, primary: primary },
        element.props.children
      ) : undefined);

      // using 'div' for < 16.3 support
      var FocusWrapper = primary ? FocusHandler : component;
      // don't pass any props to 'div'
      var wrapperProps = primary ? _extends({ uri: uri, location: location, component: component }, domProps) : domProps;

      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        BaseContext.Provider,
        { value: { baseuri: uri, basepath: basepath } },
        react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
          FocusWrapper,
          wrapperProps,
          clone
        )
      );
    } else {
      // Not sure if we want this, would require index routes at every level
      // warning(
      //   false,
      //   `<Router basepath="${basepath}">\n\nNothing matched:\n\t${
      //     location.pathname
      //   }\n\nPaths checked: \n\t${routes
      //     .map(route => route.path)
      //     .join(
      //       "\n\t"
      //     )}\n\nTo get rid of this warning, add a default NotFound component as child of Router:
      //   \n\tlet NotFound = () => <div>Not Found!</div>
      //   \n\t<Router>\n\t  <NotFound default/>\n\t  {/* ... */}\n\t</Router>`
      // );
      return null;
    }
  };

  return RouterImpl;
}((react__WEBPACK_IMPORTED_MODULE_0___default().PureComponent));

RouterImpl.defaultProps = {
  primary: true
};


var FocusContext = createNamedContext("Focus");

var FocusHandler = function FocusHandler(_ref3) {
  var uri = _ref3.uri,
      location = _ref3.location,
      component = _ref3.component,
      domProps = _objectWithoutProperties(_ref3, ["uri", "location", "component"]);

  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    FocusContext.Consumer,
    null,
    function (requestFocus) {
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(FocusHandlerImpl, _extends({}, domProps, {
        component: component,
        requestFocus: requestFocus,
        uri: uri,
        location: location
      }));
    }
  );
};

// don't focus on initial render
var initialRender = true;
var focusHandlerCount = 0;

var FocusHandlerImpl = function (_React$Component2) {
  _inherits(FocusHandlerImpl, _React$Component2);

  function FocusHandlerImpl() {
    var _temp2, _this4, _ret2;

    _classCallCheck(this, FocusHandlerImpl);

    for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      args[_key2] = arguments[_key2];
    }

    return _ret2 = (_temp2 = (_this4 = _possibleConstructorReturn(this, _React$Component2.call.apply(_React$Component2, [this].concat(args))), _this4), _this4.state = {}, _this4.requestFocus = function (node) {
      if (!_this4.state.shouldFocus && node) {
        node.focus();
      }
    }, _temp2), _possibleConstructorReturn(_this4, _ret2);
  }

  FocusHandlerImpl.getDerivedStateFromProps = function getDerivedStateFromProps(nextProps, prevState) {
    var initial = prevState.uri == null;
    if (initial) {
      return _extends({
        shouldFocus: true
      }, nextProps);
    } else {
      var myURIChanged = nextProps.uri !== prevState.uri;
      var navigatedUpToMe = prevState.location.pathname !== nextProps.location.pathname && nextProps.location.pathname === nextProps.uri;
      return _extends({
        shouldFocus: myURIChanged || navigatedUpToMe
      }, nextProps);
    }
  };

  FocusHandlerImpl.prototype.componentDidMount = function componentDidMount() {
    focusHandlerCount++;
    this.focus();
  };

  FocusHandlerImpl.prototype.componentWillUnmount = function componentWillUnmount() {
    focusHandlerCount--;
    if (focusHandlerCount === 0) {
      initialRender = true;
    }
  };

  FocusHandlerImpl.prototype.componentDidUpdate = function componentDidUpdate(prevProps, prevState) {
    if (prevProps.location !== this.props.location && this.state.shouldFocus) {
      this.focus();
    }
  };

  FocusHandlerImpl.prototype.focus = function focus() {
    if (false) // removed by dead control flow
{}

    var requestFocus = this.props.requestFocus;


    if (requestFocus) {
      requestFocus(this.node);
    } else {
      if (initialRender) {
        initialRender = false;
      } else if (this.node) {
        // React polyfills [autofocus] and it fires earlier than cDM,
        // so we were stealing focus away, this line prevents that.
        if (!this.node.contains(document.activeElement)) {
          this.node.focus();
        }
      }
    }
  };

  FocusHandlerImpl.prototype.render = function render() {
    var _this5 = this;

    var _props2 = this.props,
        children = _props2.children,
        style = _props2.style,
        requestFocus = _props2.requestFocus,
        _props2$component = _props2.component,
        Comp = _props2$component === undefined ? "div" : _props2$component,
        uri = _props2.uri,
        location = _props2.location,
        domProps = _objectWithoutProperties(_props2, ["children", "style", "requestFocus", "component", "uri", "location"]);

    return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
      Comp,
      _extends({
        style: _extends({ outline: "none" }, style),
        tabIndex: "-1",
        ref: function ref(n) {
          return _this5.node = n;
        }
      }, domProps),
      react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        FocusContext.Provider,
        { value: this.requestFocus },
        this.props.children
      )
    );
  };

  return FocusHandlerImpl;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component));

(0,react_lifecycles_compat__WEBPACK_IMPORTED_MODULE_3__.polyfill)(FocusHandlerImpl);

var k = function k() {};

////////////////////////////////////////////////////////////////////////////////
var forwardRef = (react__WEBPACK_IMPORTED_MODULE_0___default().forwardRef);

if (typeof forwardRef === "undefined") {
  forwardRef = function forwardRef(C) {
    return C;
  };
}

var Link = forwardRef(function (_ref4, ref) {
  var innerRef = _ref4.innerRef,
      props = _objectWithoutProperties(_ref4, ["innerRef"]);

  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    BaseContext.Consumer,
    null,
    function (_ref5) {
      var basepath = _ref5.basepath,
          baseuri = _ref5.baseuri;
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        Location,
        null,
        function (_ref6) {
          var location = _ref6.location,
              navigate = _ref6.navigate;

          var to = props.to,
              state = props.state,
              replace = props.replace,
              _props$getProps = props.getProps,
              getProps = _props$getProps === undefined ? k : _props$getProps,
              anchorProps = _objectWithoutProperties(props, ["to", "state", "replace", "getProps"]);

          var href = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(to, baseuri);
          var encodedHref = encodeURI(href);
          var isCurrent = location.pathname === encodedHref;
          var isPartiallyCurrent = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.startsWith)(location.pathname, encodedHref);

          return react__WEBPACK_IMPORTED_MODULE_0___default().createElement("a", _extends({
            ref: ref || innerRef,
            "aria-current": isCurrent ? "page" : undefined
          }, anchorProps, getProps({ isCurrent: isCurrent, isPartiallyCurrent: isPartiallyCurrent, href: href, location: location }), {
            href: href,
            onClick: function onClick(event) {
              if (anchorProps.onClick) anchorProps.onClick(event);
              if (shouldNavigate(event)) {
                event.preventDefault();
                var shouldReplace = replace;
                if (typeof replace !== "boolean" && isCurrent) {
                  var _location$state = _extends({}, location.state),
                      key = _location$state.key,
                      restState = _objectWithoutProperties(_location$state, ["key"]);

                  shouldReplace = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.shallowCompare)(_extends({}, state), restState);
                }
                navigate(href, {
                  state: state,
                  replace: shouldReplace
                });
              }
            }
          }));
        }
      );
    }
  );
});

Link.displayName = "Link";

 true ? Link.propTypes = {
  to: (prop_types__WEBPACK_IMPORTED_MODULE_6___default().string).isRequired
} : 0;

////////////////////////////////////////////////////////////////////////////////
function RedirectRequest(uri) {
  this.uri = uri;
}

var isRedirect = function isRedirect(o) {
  return o instanceof RedirectRequest;
};

var redirectTo = function redirectTo(to) {
  throw new RedirectRequest(to);
};

var RedirectImpl = function (_React$Component3) {
  _inherits(RedirectImpl, _React$Component3);

  function RedirectImpl() {
    _classCallCheck(this, RedirectImpl);

    return _possibleConstructorReturn(this, _React$Component3.apply(this, arguments));
  }

  // Support React < 16 with this hook
  RedirectImpl.prototype.componentDidMount = function componentDidMount() {
    var _props3 = this.props,
        navigate = _props3.navigate,
        to = _props3.to,
        from = _props3.from,
        _props3$replace = _props3.replace,
        replace = _props3$replace === undefined ? true : _props3$replace,
        state = _props3.state,
        noThrow = _props3.noThrow,
        baseuri = _props3.baseuri,
        props = _objectWithoutProperties(_props3, ["navigate", "to", "from", "replace", "state", "noThrow", "baseuri"]);

    Promise.resolve().then(function () {
      var resolvedTo = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(to, baseuri);
      navigate((0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.insertParams)(resolvedTo, props), { replace: replace, state: state });
    });
  };

  RedirectImpl.prototype.render = function render() {
    var _props4 = this.props,
        navigate = _props4.navigate,
        to = _props4.to,
        from = _props4.from,
        replace = _props4.replace,
        state = _props4.state,
        noThrow = _props4.noThrow,
        baseuri = _props4.baseuri,
        props = _objectWithoutProperties(_props4, ["navigate", "to", "from", "replace", "state", "noThrow", "baseuri"]);

    var resolvedTo = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(to, baseuri);
    if (!noThrow) redirectTo((0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.insertParams)(resolvedTo, props));
    return null;
  };

  return RedirectImpl;
}((react__WEBPACK_IMPORTED_MODULE_0___default().Component));

var Redirect = function Redirect(props) {
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    BaseContext.Consumer,
    null,
    function (_ref7) {
      var baseuri = _ref7.baseuri;
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        Location,
        null,
        function (locationContext) {
          return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(RedirectImpl, _extends({}, locationContext, { baseuri: baseuri }, props));
        }
      );
    }
  );
};

 true ? Redirect.propTypes = {
  from: (prop_types__WEBPACK_IMPORTED_MODULE_6___default().string),
  to: (prop_types__WEBPACK_IMPORTED_MODULE_6___default().string).isRequired
} : 0;

////////////////////////////////////////////////////////////////////////////////
var Match = function Match(_ref8) {
  var path = _ref8.path,
      children = _ref8.children;
  return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
    BaseContext.Consumer,
    null,
    function (_ref9) {
      var baseuri = _ref9.baseuri;
      return react__WEBPACK_IMPORTED_MODULE_0___default().createElement(
        Location,
        null,
        function (_ref10) {
          var navigate = _ref10.navigate,
              location = _ref10.location;

          var resolvedPath = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(path, baseuri);
          var result = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.match)(resolvedPath, location.pathname);
          return children({
            navigate: navigate,
            location: location,
            match: result ? _extends({}, result.params, {
              uri: result.uri,
              path: path
            }) : null
          });
        }
      );
    }
  );
};

////////////////////////////////////////////////////////////////////////////////
// Hooks

var useLocation = function useLocation() {
  var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(LocationContext);

  if (!context) {
    throw new Error("useLocation hook was used but a LocationContext.Provider was not found in the parent tree. Make sure this is used in a component that is a child of Router");
  }

  return context.location;
};

var useNavigate = function useNavigate() {
  var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(LocationContext);

  if (!context) {
    throw new Error("useNavigate hook was used but a LocationContext.Provider was not found in the parent tree. Make sure this is used in a component that is a child of Router");
  }

  return context.navigate;
};

var useParams = function useParams() {
  var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(BaseContext);

  if (!context) {
    throw new Error("useParams hook was used but a LocationContext.Provider was not found in the parent tree. Make sure this is used in a component that is a child of Router");
  }

  var location = useLocation();

  var results = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.match)(context.basepath, location.pathname);

  return results ? results.params : null;
};

var useMatch = function useMatch(path) {
  if (!path) {
    throw new Error("useMatch(path: string) requires an argument of a string to match against");
  }
  var context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(BaseContext);

  if (!context) {
    throw new Error("useMatch hook was used but a LocationContext.Provider was not found in the parent tree. Make sure this is used in a component that is a child of Router");
  }

  var location = useLocation();

  var resolvedPath = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.resolve)(path, context.baseuri);
  var result = (0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.match)(resolvedPath, location.pathname);
  return result ? _extends({}, result.params, {
    uri: result.uri,
    path: path
  }) : null;
};

////////////////////////////////////////////////////////////////////////////////
// Junk
var stripSlashes = function stripSlashes(str) {
  return str.replace(/(^\/+|\/+$)/g, "");
};

var createRoute = function createRoute(basepath) {
  return function (element) {
    if (!element) {
      return null;
    }

    if (element.type === (react__WEBPACK_IMPORTED_MODULE_0___default().Fragment) && element.props.children) {
      return react__WEBPACK_IMPORTED_MODULE_0___default().Children.map(element.props.children, createRoute(basepath));
    }
    !(element.props.path || element.props.default || element.type === Redirect) ?  true ? invariant__WEBPACK_IMPORTED_MODULE_1___default()(false, "<Router>: Children of <Router> must have a `path` or `default` prop, or be a `<Redirect>`. None found on element type `" + element.type + "`") : 0 : void 0;

    !!(element.type === Redirect && (!element.props.from || !element.props.to)) ?  true ? invariant__WEBPACK_IMPORTED_MODULE_1___default()(false, "<Redirect from=\"" + element.props.from + "\" to=\"" + element.props.to + "\"/> requires both \"from\" and \"to\" props when inside a <Router>.") : 0 : void 0;

    !!(element.type === Redirect && !(0,_lib_utils__WEBPACK_IMPORTED_MODULE_4__.validateRedirect)(element.props.from, element.props.to)) ?  true ? invariant__WEBPACK_IMPORTED_MODULE_1___default()(false, "<Redirect from=\"" + element.props.from + " to=\"" + element.props.to + "\"/> has mismatched dynamic segments, ensure both paths have the exact same dynamic segments.") : 0 : void 0;

    if (element.props.default) {
      return { value: element, default: true };
    }

    var elementPath = element.type === Redirect ? element.props.from : element.props.path;

    var path = elementPath === "/" ? basepath : stripSlashes(basepath) + "/" + stripSlashes(elementPath);

    return {
      value: element,
      default: element.props.default,
      path: element.props.children ? stripSlashes(path) + "/*" : path
    };
  };
};

var shouldNavigate = function shouldNavigate(event) {
  return !event.defaultPrevented && event.button === 0 && !(event.metaKey || event.altKey || event.ctrlKey || event.shiftKey);
};

////////////////////////////////////////////////////////////////////////


/***/ }),

/***/ "../node_modules/@reach/router/es/lib/history.js":
/*!*******************************************************!*\
  !*** ../node_modules/@reach/router/es/lib/history.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createHistory: () => (/* binding */ createHistory),
/* harmony export */   createMemorySource: () => (/* binding */ createMemorySource),
/* harmony export */   globalHistory: () => (/* binding */ globalHistory),
/* harmony export */   navigate: () => (/* binding */ navigate)
/* harmony export */ });
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var getLocation = function getLocation(source) {
  var _source$location = source.location,
      search = _source$location.search,
      hash = _source$location.hash,
      href = _source$location.href,
      origin = _source$location.origin,
      protocol = _source$location.protocol,
      host = _source$location.host,
      hostname = _source$location.hostname,
      port = _source$location.port;
  var pathname = source.location.pathname;


  if (!pathname && href && canUseDOM) {
    var url = new URL(href);
    pathname = url.pathname;
  }

  return {
    pathname: encodeURI(decodeURI(pathname)),
    search: search,
    hash: hash,
    href: href,
    origin: origin,
    protocol: protocol,
    host: host,
    hostname: hostname,
    port: port,
    state: source.history.state,
    key: source.history.state && source.history.state.key || "initial"
  };
};

var createHistory = function createHistory(source, options) {
  var listeners = [];
  var location = getLocation(source);
  var transitioning = false;
  var resolveTransition = function resolveTransition() {};

  return {
    get location() {
      return location;
    },

    get transitioning() {
      return transitioning;
    },

    _onTransitionComplete: function _onTransitionComplete() {
      transitioning = false;
      resolveTransition();
    },
    listen: function listen(listener) {
      listeners.push(listener);

      var popstateListener = function popstateListener() {
        location = getLocation(source);
        listener({ location: location, action: "POP" });
      };

      source.addEventListener("popstate", popstateListener);

      return function () {
        source.removeEventListener("popstate", popstateListener);
        listeners = listeners.filter(function (fn) {
          return fn !== listener;
        });
      };
    },
    navigate: function navigate(to) {
      var _ref = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
          state = _ref.state,
          _ref$replace = _ref.replace,
          replace = _ref$replace === undefined ? false : _ref$replace;

      if (typeof to === "number") {
        source.history.go(to);
      } else {
        state = _extends({}, state, { key: Date.now() + "" });
        // try...catch iOS Safari limits to 100 pushState calls
        try {
          if (transitioning || replace) {
            source.history.replaceState(state, null, to);
          } else {
            source.history.pushState(state, null, to);
          }
        } catch (e) {
          source.location[replace ? "replace" : "assign"](to);
        }
      }

      location = getLocation(source);
      transitioning = true;
      var transition = new Promise(function (res) {
        return resolveTransition = res;
      });
      listeners.forEach(function (listener) {
        return listener({ location: location, action: "PUSH" });
      });
      return transition;
    }
  };
};

////////////////////////////////////////////////////////////////////////////////
// Stores history entries in memory for testing or other platforms like Native
var createMemorySource = function createMemorySource() {
  var initialPath = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : "/";

  var searchIndex = initialPath.indexOf("?");
  var initialLocation = {
    pathname: searchIndex > -1 ? initialPath.substr(0, searchIndex) : initialPath,
    search: searchIndex > -1 ? initialPath.substr(searchIndex) : ""
  };
  var index = 0;
  var stack = [initialLocation];
  var states = [null];

  return {
    get location() {
      return stack[index];
    },
    addEventListener: function addEventListener(name, fn) {},
    removeEventListener: function removeEventListener(name, fn) {},

    history: {
      get entries() {
        return stack;
      },
      get index() {
        return index;
      },
      get state() {
        return states[index];
      },
      pushState: function pushState(state, _, uri) {
        var _uri$split = uri.split("?"),
            pathname = _uri$split[0],
            _uri$split$ = _uri$split[1],
            search = _uri$split$ === undefined ? "" : _uri$split$;

        index++;
        stack.push({ pathname: pathname, search: search.length ? "?" + search : search });
        states.push(state);
      },
      replaceState: function replaceState(state, _, uri) {
        var _uri$split2 = uri.split("?"),
            pathname = _uri$split2[0],
            _uri$split2$ = _uri$split2[1],
            search = _uri$split2$ === undefined ? "" : _uri$split2$;

        stack[index] = { pathname: pathname, search: search };
        states[index] = state;
      },
      go: function go(to) {
        var newIndex = index + to;

        if (newIndex < 0 || newIndex > states.length - 1) {
          return;
        }

        index = newIndex;
      }
    }
  };
};

////////////////////////////////////////////////////////////////////////////////
// global history - uses window.history as the source if available, otherwise a
// memory history
var canUseDOM = !!(typeof window !== "undefined" && window.document && window.document.createElement);
var getSource = function getSource() {
  return canUseDOM ? window : createMemorySource();
};

var globalHistory = createHistory(getSource());
var navigate = globalHistory.navigate;

////////////////////////////////////////////////////////////////////////////////



/***/ }),

/***/ "../node_modules/@reach/router/es/lib/utils.js":
/*!*****************************************************!*\
  !*** ../node_modules/@reach/router/es/lib/utils.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   insertParams: () => (/* binding */ insertParams),
/* harmony export */   match: () => (/* binding */ match),
/* harmony export */   pick: () => (/* binding */ pick),
/* harmony export */   resolve: () => (/* binding */ resolve),
/* harmony export */   shallowCompare: () => (/* binding */ shallowCompare),
/* harmony export */   startsWith: () => (/* binding */ startsWith),
/* harmony export */   validateRedirect: () => (/* binding */ validateRedirect)
/* harmony export */ });
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! invariant */ "../node_modules/invariant/browser.js");
/* harmony import */ var invariant__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(invariant__WEBPACK_IMPORTED_MODULE_0__);


////////////////////////////////////////////////////////////////////////////////
// startsWith(string, search) - Check if `string` starts with `search`
var startsWith = function startsWith(string, search) {
  return string.substr(0, search.length) === search;
};

////////////////////////////////////////////////////////////////////////////////
// pick(routes, uri)
//
// Ranks and picks the best route to match. Each segment gets the highest
// amount of points, then the type of segment gets an additional amount of
// points where
//
//     static > dynamic > splat > root
//
// This way we don't have to worry about the order of our routes, let the
// computers do it.
//
// A route looks like this
//
//     { path, default, value }
//
// And a returned match looks like:
//
//     { route, params, uri }
//
// I know, I should use TypeScript not comments for these types.
var pick = function pick(routes, uri) {
  var match = void 0;
  var default_ = void 0;

  var _uri$split = uri.split("?"),
      uriPathname = _uri$split[0];

  var uriSegments = segmentize(uriPathname);
  var isRootUri = uriSegments[0] === "";
  var ranked = rankRoutes(routes);

  for (var i = 0, l = ranked.length; i < l; i++) {
    var missed = false;
    var route = ranked[i].route;

    if (route.default) {
      default_ = {
        route: route,
        params: {},
        uri: uri
      };
      continue;
    }

    var routeSegments = segmentize(route.path);
    var params = {};
    var max = Math.max(uriSegments.length, routeSegments.length);
    var index = 0;

    for (; index < max; index++) {
      var routeSegment = routeSegments[index];
      var uriSegment = uriSegments[index];

      if (isSplat(routeSegment)) {
        // Hit a splat, just grab the rest, and return a match
        // uri:   /files/documents/work
        // route: /files/*
        var param = routeSegment.slice(1) || "*";
        params[param] = uriSegments.slice(index).map(decodeURIComponent).join("/");
        break;
      }

      if (uriSegment === undefined) {
        // URI is shorter than the route, no match
        // uri:   /users
        // route: /users/:userId
        missed = true;
        break;
      }

      var dynamicMatch = paramRe.exec(routeSegment);

      if (dynamicMatch && !isRootUri) {
        var matchIsNotReserved = reservedNames.indexOf(dynamicMatch[1]) === -1;
        !matchIsNotReserved ?  true ? invariant__WEBPACK_IMPORTED_MODULE_0___default()(false, "<Router> dynamic segment \"" + dynamicMatch[1] + "\" is a reserved name. Please use a different name in path \"" + route.path + "\".") : 0 : void 0;
        var value = decodeURIComponent(uriSegment);
        params[dynamicMatch[1]] = value;
      } else if (routeSegment !== uriSegment) {
        // Current segments don't match, not dynamic, not splat, so no match
        // uri:   /users/123/settings
        // route: /users/:id/profile
        missed = true;
        break;
      }
    }

    if (!missed) {
      match = {
        route: route,
        params: params,
        uri: "/" + uriSegments.slice(0, index).join("/")
      };
      break;
    }
  }

  return match || default_ || null;
};

////////////////////////////////////////////////////////////////////////////////
// match(path, uri) - Matches just one path to a uri, also lol
var match = function match(path, uri) {
  return pick([{ path: path }], uri);
};

////////////////////////////////////////////////////////////////////////////////
// resolve(to, basepath)
//
// Resolves URIs as though every path is a directory, no files.  Relative URIs
// in the browser can feel awkward because not only can you be "in a directory"
// you can be "at a file", too. For example
//
//     browserSpecResolve('foo', '/bar/') => /bar/foo
//     browserSpecResolve('foo', '/bar') => /foo
//
// But on the command line of a file system, it's not as complicated, you can't
// `cd` from a file, only directories.  This way, links have to know less about
// their current path. To go deeper you can do this:
//
//     <Link to="deeper"/>
//     // instead of
//     <Link to=`{${props.uri}/deeper}`/>
//
// Just like `cd`, if you want to go deeper from the command line, you do this:
//
//     cd deeper
//     # not
//     cd $(pwd)/deeper
//
// By treating every path as a directory, linking to relative paths should
// require less contextual information and (fingers crossed) be more intuitive.
var resolve = function resolve(to, base) {
  // /foo/bar, /baz/qux => /foo/bar
  if (startsWith(to, "/")) {
    return to;
  }

  var _to$split = to.split("?"),
      toPathname = _to$split[0],
      toQuery = _to$split[1];

  var _base$split = base.split("?"),
      basePathname = _base$split[0];

  var toSegments = segmentize(toPathname);
  var baseSegments = segmentize(basePathname);

  // ?a=b, /users?b=c => /users?a=b
  if (toSegments[0] === "") {
    return addQuery(basePathname, toQuery);
  }

  // profile, /users/789 => /users/789/profile
  if (!startsWith(toSegments[0], ".")) {
    var pathname = baseSegments.concat(toSegments).join("/");
    return addQuery((basePathname === "/" ? "" : "/") + pathname, toQuery);
  }

  // ./         /users/123  =>  /users/123
  // ../        /users/123  =>  /users
  // ../..      /users/123  =>  /
  // ../../one  /a/b/c/d    =>  /a/b/one
  // .././one   /a/b/c/d    =>  /a/b/c/one
  var allSegments = baseSegments.concat(toSegments);
  var segments = [];
  for (var i = 0, l = allSegments.length; i < l; i++) {
    var segment = allSegments[i];
    if (segment === "..") segments.pop();else if (segment !== ".") segments.push(segment);
  }

  return addQuery("/" + segments.join("/"), toQuery);
};

////////////////////////////////////////////////////////////////////////////////
// insertParams(path, params)

var insertParams = function insertParams(path, params) {
  var _path$split = path.split("?"),
      pathBase = _path$split[0],
      _path$split$ = _path$split[1],
      query = _path$split$ === undefined ? "" : _path$split$;

  var segments = segmentize(pathBase);
  var constructedPath = "/" + segments.map(function (segment) {
    var match = paramRe.exec(segment);
    return match ? params[match[1]] : segment;
  }).join("/");
  var _params$location = params.location;
  _params$location = _params$location === undefined ? {} : _params$location;
  var _params$location$sear = _params$location.search,
      search = _params$location$sear === undefined ? "" : _params$location$sear;

  var searchSplit = search.split("?")[1] || "";
  constructedPath = addQuery(constructedPath, query, searchSplit);
  return constructedPath;
};

var validateRedirect = function validateRedirect(from, to) {
  var filter = function filter(segment) {
    return isDynamic(segment);
  };
  var fromString = segmentize(from).filter(filter).sort().join("/");
  var toString = segmentize(to).filter(filter).sort().join("/");
  return fromString === toString;
};

////////////////////////////////////////////////////////////////////////////////
// Junk
var paramRe = /^:(.+)/;

var SEGMENT_POINTS = 4;
var STATIC_POINTS = 3;
var DYNAMIC_POINTS = 2;
var SPLAT_PENALTY = 1;
var ROOT_POINTS = 1;

var isRootSegment = function isRootSegment(segment) {
  return segment === "";
};
var isDynamic = function isDynamic(segment) {
  return paramRe.test(segment);
};
var isSplat = function isSplat(segment) {
  return segment && segment[0] === "*";
};

var rankRoute = function rankRoute(route, index) {
  var score = route.default ? 0 : segmentize(route.path).reduce(function (score, segment) {
    score += SEGMENT_POINTS;
    if (isRootSegment(segment)) score += ROOT_POINTS;else if (isDynamic(segment)) score += DYNAMIC_POINTS;else if (isSplat(segment)) score -= SEGMENT_POINTS + SPLAT_PENALTY;else score += STATIC_POINTS;
    return score;
  }, 0);
  return { route: route, score: score, index: index };
};

var rankRoutes = function rankRoutes(routes) {
  return routes.map(rankRoute).sort(function (a, b) {
    return a.score < b.score ? 1 : a.score > b.score ? -1 : a.index - b.index;
  });
};

var segmentize = function segmentize(uri) {
  return uri
  // strip starting/ending slashes
  .replace(/(^\/+|\/+$)/g, "").split("/");
};

var addQuery = function addQuery(pathname) {
  for (var _len = arguments.length, query = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
    query[_key - 1] = arguments[_key];
  }

  query = query.filter(function (q) {
    return q && q.length > 0;
  });
  return pathname + (query && query.length > 0 ? "?" + query.join("&") : "");
};

var reservedNames = ["uri", "path"];

/**
 * Shallow compares two objects.
 * @param {Object} obj1 The first object to compare.
 * @param {Object} obj2 The second object to compare.
 */
var shallowCompare = function shallowCompare(obj1, obj2) {
  var obj1Keys = Object.keys(obj1);
  return obj1Keys.length === Object.keys(obj2).length && obj1Keys.every(function (key) {
    return obj2.hasOwnProperty(key) && obj1[key] === obj2[key];
  });
};

////////////////////////////////////////////////////////////////////////////////


/***/ }),

/***/ "../node_modules/create-react-context/lib/implementation.js":
/*!******************************************************************!*\
  !*** ../node_modules/create-react-context/lib/implementation.js ***!
  \******************************************************************/
/***/ ((module, exports, __webpack_require__) => {

"use strict";


exports.__esModule = true;

var _react = __webpack_require__(/*! react */ "react");

var _react2 = _interopRequireDefault(_react);

var _propTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");

var _propTypes2 = _interopRequireDefault(_propTypes);

var _gud = __webpack_require__(/*! gud */ "../node_modules/gud/index.js");

var _gud2 = _interopRequireDefault(_gud);

var _warning = __webpack_require__(/*! warning */ "../node_modules/warning/warning.js");

var _warning2 = _interopRequireDefault(_warning);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var MAX_SIGNED_31_BIT_INT = 1073741823;

// Inlined Object.is polyfill.
// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
function objectIs(x, y) {
  if (x === y) {
    return x !== 0 || 1 / x === 1 / y;
  } else {
    return x !== x && y !== y;
  }
}

function createEventEmitter(value) {
  var handlers = [];
  return {
    on: function on(handler) {
      handlers.push(handler);
    },
    off: function off(handler) {
      handlers = handlers.filter(function (h) {
        return h !== handler;
      });
    },
    get: function get() {
      return value;
    },
    set: function set(newValue, changedBits) {
      value = newValue;
      handlers.forEach(function (handler) {
        return handler(value, changedBits);
      });
    }
  };
}

function onlyChild(children) {
  return Array.isArray(children) ? children[0] : children;
}

function createReactContext(defaultValue, calculateChangedBits) {
  var _Provider$childContex, _Consumer$contextType;

  var contextProp = '__create-react-context-' + (0, _gud2.default)() + '__';

  var Provider = function (_Component) {
    _inherits(Provider, _Component);

    function Provider() {
      var _temp, _this, _ret;

      _classCallCheck(this, Provider);

      for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      return _ret = (_temp = (_this = _possibleConstructorReturn(this, _Component.call.apply(_Component, [this].concat(args))), _this), _this.emitter = createEventEmitter(_this.props.value), _temp), _possibleConstructorReturn(_this, _ret);
    }

    Provider.prototype.getChildContext = function getChildContext() {
      var _ref;

      return _ref = {}, _ref[contextProp] = this.emitter, _ref;
    };

    Provider.prototype.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
      if (this.props.value !== nextProps.value) {
        var oldValue = this.props.value;
        var newValue = nextProps.value;
        var changedBits = void 0;

        if (objectIs(oldValue, newValue)) {
          changedBits = 0; // No change
        } else {
          changedBits = typeof calculateChangedBits === 'function' ? calculateChangedBits(oldValue, newValue) : MAX_SIGNED_31_BIT_INT;
          if (true) {
            (0, _warning2.default)((changedBits & MAX_SIGNED_31_BIT_INT) === changedBits, 'calculateChangedBits: Expected the return value to be a ' + '31-bit integer. Instead received: %s', changedBits);
          }

          changedBits |= 0;

          if (changedBits !== 0) {
            this.emitter.set(nextProps.value, changedBits);
          }
        }
      }
    };

    Provider.prototype.render = function render() {
      return this.props.children;
    };

    return Provider;
  }(_react.Component);

  Provider.childContextTypes = (_Provider$childContex = {}, _Provider$childContex[contextProp] = _propTypes2.default.object.isRequired, _Provider$childContex);

  var Consumer = function (_Component2) {
    _inherits(Consumer, _Component2);

    function Consumer() {
      var _temp2, _this2, _ret2;

      _classCallCheck(this, Consumer);

      for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      return _ret2 = (_temp2 = (_this2 = _possibleConstructorReturn(this, _Component2.call.apply(_Component2, [this].concat(args))), _this2), _this2.state = {
        value: _this2.getValue()
      }, _this2.onUpdate = function (newValue, changedBits) {
        var observedBits = _this2.observedBits | 0;
        if ((observedBits & changedBits) !== 0) {
          _this2.setState({ value: _this2.getValue() });
        }
      }, _temp2), _possibleConstructorReturn(_this2, _ret2);
    }

    Consumer.prototype.componentWillReceiveProps = function componentWillReceiveProps(nextProps) {
      var observedBits = nextProps.observedBits;

      this.observedBits = observedBits === undefined || observedBits === null ? MAX_SIGNED_31_BIT_INT // Subscribe to all changes by default
      : observedBits;
    };

    Consumer.prototype.componentDidMount = function componentDidMount() {
      if (this.context[contextProp]) {
        this.context[contextProp].on(this.onUpdate);
      }
      var observedBits = this.props.observedBits;

      this.observedBits = observedBits === undefined || observedBits === null ? MAX_SIGNED_31_BIT_INT // Subscribe to all changes by default
      : observedBits;
    };

    Consumer.prototype.componentWillUnmount = function componentWillUnmount() {
      if (this.context[contextProp]) {
        this.context[contextProp].off(this.onUpdate);
      }
    };

    Consumer.prototype.getValue = function getValue() {
      if (this.context[contextProp]) {
        return this.context[contextProp].get();
      } else {
        return defaultValue;
      }
    };

    Consumer.prototype.render = function render() {
      return onlyChild(this.props.children)(this.state.value);
    };

    return Consumer;
  }(_react.Component);

  Consumer.contextTypes = (_Consumer$contextType = {}, _Consumer$contextType[contextProp] = _propTypes2.default.object, _Consumer$contextType);


  return {
    Provider: Provider,
    Consumer: Consumer
  };
}

exports["default"] = createReactContext;
module.exports = exports['default'];

/***/ }),

/***/ "../node_modules/create-react-context/lib/index.js":
/*!*********************************************************!*\
  !*** ../node_modules/create-react-context/lib/index.js ***!
  \*********************************************************/
/***/ ((module, exports, __webpack_require__) => {

"use strict";


exports.__esModule = true;

var _react = __webpack_require__(/*! react */ "react");

var _react2 = _interopRequireDefault(_react);

var _implementation = __webpack_require__(/*! ./implementation */ "../node_modules/create-react-context/lib/implementation.js");

var _implementation2 = _interopRequireDefault(_implementation);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports["default"] = _react2.default.createContext || _implementation2.default;
module.exports = exports['default'];

/***/ }),

/***/ "../node_modules/gud/index.js":
/*!************************************!*\
  !*** ../node_modules/gud/index.js ***!
  \************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
// @flow


var key = '__global_unique_id__';

module.exports = function() {
  return __webpack_require__.g[key] = (__webpack_require__.g[key] || 0) + 1;
};


/***/ }),

/***/ "../node_modules/invariant/browser.js":
/*!********************************************!*\
  !*** ../node_modules/invariant/browser.js ***!
  \********************************************/
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



/**
 * Use invariant() to assert state which your program assumes to be true.
 *
 * Provide sprintf-style format (only %s is supported) and arguments
 * to provide information about what broke and what you were
 * expecting.
 *
 * The invariant message will be stripped in production, but the invariant
 * will remain to ensure logic does not differ in production.
 */

var invariant = function(condition, format, a, b, c, d, e, f) {
  if (true) {
    if (format === undefined) {
      throw new Error('invariant requires an error message argument');
    }
  }

  if (!condition) {
    var error;
    if (format === undefined) {
      error = new Error(
        'Minified exception occurred; use the non-minified dev environment ' +
        'for the full error message and additional helpful warnings.'
      );
    } else {
      var args = [a, b, c, d, e, f];
      var argIndex = 0;
      error = new Error(
        format.replace(/%s/g, function() { return args[argIndex++]; })
      );
      error.name = 'Invariant Violation';
    }

    error.framesToPop = 1; // we don't care about invariant's own frame
    throw error;
  }
};

module.exports = invariant;


/***/ }),

/***/ "../node_modules/object-assign/index.js":
/*!**********************************************!*\
  !*** ../node_modules/object-assign/index.js ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
/*
object-assign
(c) Sindre Sorhus
@license MIT
*/


/* eslint-disable no-unused-vars */
var getOwnPropertySymbols = Object.getOwnPropertySymbols;
var hasOwnProperty = Object.prototype.hasOwnProperty;
var propIsEnumerable = Object.prototype.propertyIsEnumerable;

function toObject(val) {
	if (val === null || val === undefined) {
		throw new TypeError('Object.assign cannot be called with null or undefined');
	}

	return Object(val);
}

function shouldUseNative() {
	try {
		if (!Object.assign) {
			return false;
		}

		// Detect buggy property enumeration order in older V8 versions.

		// https://bugs.chromium.org/p/v8/issues/detail?id=4118
		var test1 = new String('abc');  // eslint-disable-line no-new-wrappers
		test1[5] = 'de';
		if (Object.getOwnPropertyNames(test1)[0] === '5') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test2 = {};
		for (var i = 0; i < 10; i++) {
			test2['_' + String.fromCharCode(i)] = i;
		}
		var order2 = Object.getOwnPropertyNames(test2).map(function (n) {
			return test2[n];
		});
		if (order2.join('') !== '0123456789') {
			return false;
		}

		// https://bugs.chromium.org/p/v8/issues/detail?id=3056
		var test3 = {};
		'abcdefghijklmnopqrst'.split('').forEach(function (letter) {
			test3[letter] = letter;
		});
		if (Object.keys(Object.assign({}, test3)).join('') !==
				'abcdefghijklmnopqrst') {
			return false;
		}

		return true;
	} catch (err) {
		// We don't expect any of the above to throw, but better to be safe.
		return false;
	}
}

module.exports = shouldUseNative() ? Object.assign : function (target, source) {
	var from;
	var to = toObject(target);
	var symbols;

	for (var s = 1; s < arguments.length; s++) {
		from = Object(arguments[s]);

		for (var key in from) {
			if (hasOwnProperty.call(from, key)) {
				to[key] = from[key];
			}
		}

		if (getOwnPropertySymbols) {
			symbols = getOwnPropertySymbols(from);
			for (var i = 0; i < symbols.length; i++) {
				if (propIsEnumerable.call(from, symbols[i])) {
					to[symbols[i]] = from[symbols[i]];
				}
			}
		}
	}

	return to;
};


/***/ }),

/***/ "../node_modules/prop-types/checkPropTypes.js":
/*!****************************************************!*\
  !*** ../node_modules/prop-types/checkPropTypes.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var printWarning = function() {};

if (true) {
  var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
  var loggedTypeFailures = {};
  var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");

  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) { /**/ }
  };
}

/**
 * Assert that the values match with the type specs.
 * Error messages are memorized and will only be shown once.
 *
 * @param {object} typeSpecs Map of name to a ReactPropType
 * @param {object} values Runtime values that need to be type-checked
 * @param {string} location e.g. "prop", "context", "child context"
 * @param {string} componentName Name of the component for error messages.
 * @param {?Function} getStack Returns the component stack.
 * @private
 */
function checkPropTypes(typeSpecs, values, location, componentName, getStack) {
  if (true) {
    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error;
        // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.
        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            var err = Error(
              (componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' +
              'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' +
              'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.'
            );
            err.name = 'Invariant Violation';
            throw err;
          }
          error = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, ReactPropTypesSecret);
        } catch (ex) {
          error = ex;
        }
        if (error && !(error instanceof Error)) {
          printWarning(
            (componentName || 'React class') + ': type specification of ' +
            location + ' `' + typeSpecName + '` is invalid; the type checker ' +
            'function must return `null` or an `Error` but returned a ' + typeof error + '. ' +
            'You may have forgotten to pass an argument to the type checker ' +
            'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' +
            'shape all require an argument).'
          );
        }
        if (error instanceof Error && !(error.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error.message] = true;

          var stack = getStack ? getStack() : '';

          printWarning(
            'Failed ' + location + ' type: ' + error.message + (stack != null ? stack : '')
          );
        }
      }
    }
  }
}

/**
 * Resets warning cache when testing.
 *
 * @private
 */
checkPropTypes.resetWarningCache = function() {
  if (true) {
    loggedTypeFailures = {};
  }
}

module.exports = checkPropTypes;


/***/ }),

/***/ "../node_modules/prop-types/factoryWithTypeCheckers.js":
/*!*************************************************************!*\
  !*** ../node_modules/prop-types/factoryWithTypeCheckers.js ***!
  \*************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");
var assign = __webpack_require__(/*! object-assign */ "../node_modules/object-assign/index.js");

var ReactPropTypesSecret = __webpack_require__(/*! ./lib/ReactPropTypesSecret */ "../node_modules/prop-types/lib/ReactPropTypesSecret.js");
var has = __webpack_require__(/*! ./lib/has */ "../node_modules/prop-types/lib/has.js");
var checkPropTypes = __webpack_require__(/*! ./checkPropTypes */ "../node_modules/prop-types/checkPropTypes.js");

var printWarning = function() {};

if (true) {
  printWarning = function(text) {
    var message = 'Warning: ' + text;
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  };
}

function emptyFunctionThatReturnsNull() {
  return null;
}

module.exports = function(isValidElement, throwOnDirectAccess) {
  /* global Symbol */
  var ITERATOR_SYMBOL = typeof Symbol === 'function' && Symbol.iterator;
  var FAUX_ITERATOR_SYMBOL = '@@iterator'; // Before Symbol spec.

  /**
   * Returns the iterator method function contained on the iterable object.
   *
   * Be sure to invoke the function with the iterable as context:
   *
   *     var iteratorFn = getIteratorFn(myIterable);
   *     if (iteratorFn) {
   *       var iterator = iteratorFn.call(myIterable);
   *       ...
   *     }
   *
   * @param {?object} maybeIterable
   * @return {?function}
   */
  function getIteratorFn(maybeIterable) {
    var iteratorFn = maybeIterable && (ITERATOR_SYMBOL && maybeIterable[ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL]);
    if (typeof iteratorFn === 'function') {
      return iteratorFn;
    }
  }

  /**
   * Collection of methods that allow declaration and validation of props that are
   * supplied to React components. Example usage:
   *
   *   var Props = require('ReactPropTypes');
   *   var MyArticle = React.createClass({
   *     propTypes: {
   *       // An optional string prop named "description".
   *       description: Props.string,
   *
   *       // A required enum prop named "category".
   *       category: Props.oneOf(['News','Photos']).isRequired,
   *
   *       // A prop named "dialog" that requires an instance of Dialog.
   *       dialog: Props.instanceOf(Dialog).isRequired
   *     },
   *     render: function() { ... }
   *   });
   *
   * A more formal specification of how these methods are used:
   *
   *   type := array|bool|func|object|number|string|oneOf([...])|instanceOf(...)
   *   decl := ReactPropTypes.{type}(.isRequired)?
   *
   * Each and every declaration produces a function with the same signature. This
   * allows the creation of custom validation functions. For example:
   *
   *  var MyLink = React.createClass({
   *    propTypes: {
   *      // An optional string or URI prop named "href".
   *      href: function(props, propName, componentName) {
   *        var propValue = props[propName];
   *        if (propValue != null && typeof propValue !== 'string' &&
   *            !(propValue instanceof URI)) {
   *          return new Error(
   *            'Expected a string or an URI for ' + propName + ' in ' +
   *            componentName
   *          );
   *        }
   *      }
   *    },
   *    render: function() {...}
   *  });
   *
   * @internal
   */

  var ANONYMOUS = '<<anonymous>>';

  // Important!
  // Keep this list in sync with production version in `./factoryWithThrowingShims.js`.
  var ReactPropTypes = {
    array: createPrimitiveTypeChecker('array'),
    bigint: createPrimitiveTypeChecker('bigint'),
    bool: createPrimitiveTypeChecker('boolean'),
    func: createPrimitiveTypeChecker('function'),
    number: createPrimitiveTypeChecker('number'),
    object: createPrimitiveTypeChecker('object'),
    string: createPrimitiveTypeChecker('string'),
    symbol: createPrimitiveTypeChecker('symbol'),

    any: createAnyTypeChecker(),
    arrayOf: createArrayOfTypeChecker,
    element: createElementTypeChecker(),
    elementType: createElementTypeTypeChecker(),
    instanceOf: createInstanceTypeChecker,
    node: createNodeChecker(),
    objectOf: createObjectOfTypeChecker,
    oneOf: createEnumTypeChecker,
    oneOfType: createUnionTypeChecker,
    shape: createShapeTypeChecker,
    exact: createStrictShapeTypeChecker,
  };

  /**
   * inlined Object.is polyfill to avoid requiring consumers ship their own
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/is
   */
  /*eslint-disable no-self-compare*/
  function is(x, y) {
    // SameValue algorithm
    if (x === y) {
      // Steps 1-5, 7-10
      // Steps 6.b-6.e: +0 != -0
      return x !== 0 || 1 / x === 1 / y;
    } else {
      // Step 6.a: NaN == NaN
      return x !== x && y !== y;
    }
  }
  /*eslint-enable no-self-compare*/

  /**
   * We use an Error-like object for backward compatibility as people may call
   * PropTypes directly and inspect their output. However, we don't use real
   * Errors anymore. We don't inspect their stack anyway, and creating them
   * is prohibitively expensive if they are created too often, such as what
   * happens in oneOfType() for any type before the one that matched.
   */
  function PropTypeError(message, data) {
    this.message = message;
    this.data = data && typeof data === 'object' ? data: {};
    this.stack = '';
  }
  // Make `instanceof Error` still work for returned errors.
  PropTypeError.prototype = Error.prototype;

  function createChainableTypeChecker(validate) {
    if (true) {
      var manualPropTypeCallCache = {};
      var manualPropTypeWarningCount = 0;
    }
    function checkType(isRequired, props, propName, componentName, location, propFullName, secret) {
      componentName = componentName || ANONYMOUS;
      propFullName = propFullName || propName;

      if (secret !== ReactPropTypesSecret) {
        if (throwOnDirectAccess) {
          // New behavior only for users of `prop-types` package
          var err = new Error(
            'Calling PropTypes validators directly is not supported by the `prop-types` package. ' +
            'Use `PropTypes.checkPropTypes()` to call them. ' +
            'Read more at http://fb.me/use-check-prop-types'
          );
          err.name = 'Invariant Violation';
          throw err;
        } else if ( true && typeof console !== 'undefined') {
          // Old behavior for people using React.PropTypes
          var cacheKey = componentName + ':' + propName;
          if (
            !manualPropTypeCallCache[cacheKey] &&
            // Avoid spamming the console because they are often not actionable except for lib authors
            manualPropTypeWarningCount < 3
          ) {
            printWarning(
              'You are manually calling a React.PropTypes validation ' +
              'function for the `' + propFullName + '` prop on `' + componentName + '`. This is deprecated ' +
              'and will throw in the standalone `prop-types` package. ' +
              'You may be seeing this warning due to a third-party PropTypes ' +
              'library. See https://fb.me/react-warning-dont-call-proptypes ' + 'for details.'
            );
            manualPropTypeCallCache[cacheKey] = true;
            manualPropTypeWarningCount++;
          }
        }
      }
      if (props[propName] == null) {
        if (isRequired) {
          if (props[propName] === null) {
            return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required ' + ('in `' + componentName + '`, but its value is `null`.'));
          }
          return new PropTypeError('The ' + location + ' `' + propFullName + '` is marked as required in ' + ('`' + componentName + '`, but its value is `undefined`.'));
        }
        return null;
      } else {
        return validate(props, propName, componentName, location, propFullName);
      }
    }

    var chainedCheckType = checkType.bind(null, false);
    chainedCheckType.isRequired = checkType.bind(null, true);

    return chainedCheckType;
  }

  function createPrimitiveTypeChecker(expectedType) {
    function validate(props, propName, componentName, location, propFullName, secret) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== expectedType) {
        // `propValue` being instance of, say, date/regexp, pass the 'object'
        // check, but we can offer a more precise error message here rather than
        // 'of type `object`'.
        var preciseType = getPreciseType(propValue);

        return new PropTypeError(
          'Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + preciseType + '` supplied to `' + componentName + '`, expected ') + ('`' + expectedType + '`.'),
          {expectedType: expectedType}
        );
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createAnyTypeChecker() {
    return createChainableTypeChecker(emptyFunctionThatReturnsNull);
  }

  function createArrayOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside arrayOf.');
      }
      var propValue = props[propName];
      if (!Array.isArray(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an array.'));
      }
      for (var i = 0; i < propValue.length; i++) {
        var error = typeChecker(propValue, i, componentName, location, propFullName + '[' + i + ']', ReactPropTypesSecret);
        if (error instanceof Error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!isValidElement(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createElementTypeTypeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      if (!ReactIs.isValidElementType(propValue)) {
        var propType = getPropType(propValue);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected a single ReactElement type.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createInstanceTypeChecker(expectedClass) {
    function validate(props, propName, componentName, location, propFullName) {
      if (!(props[propName] instanceof expectedClass)) {
        var expectedClassName = expectedClass.name || ANONYMOUS;
        var actualClassName = getClassName(props[propName]);
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + actualClassName + '` supplied to `' + componentName + '`, expected ') + ('instance of `' + expectedClassName + '`.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createEnumTypeChecker(expectedValues) {
    if (!Array.isArray(expectedValues)) {
      if (true) {
        if (arguments.length > 1) {
          printWarning(
            'Invalid arguments supplied to oneOf, expected an array, got ' + arguments.length + ' arguments. ' +
            'A common mistake is to write oneOf(x, y, z) instead of oneOf([x, y, z]).'
          );
        } else {
          printWarning('Invalid argument supplied to oneOf, expected an array.');
        }
      }
      return emptyFunctionThatReturnsNull;
    }

    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      for (var i = 0; i < expectedValues.length; i++) {
        if (is(propValue, expectedValues[i])) {
          return null;
        }
      }

      var valuesString = JSON.stringify(expectedValues, function replacer(key, value) {
        var type = getPreciseType(value);
        if (type === 'symbol') {
          return String(value);
        }
        return value;
      });
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of value `' + String(propValue) + '` ' + ('supplied to `' + componentName + '`, expected one of ' + valuesString + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createObjectOfTypeChecker(typeChecker) {
    function validate(props, propName, componentName, location, propFullName) {
      if (typeof typeChecker !== 'function') {
        return new PropTypeError('Property `' + propFullName + '` of component `' + componentName + '` has invalid PropType notation inside objectOf.');
      }
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type ' + ('`' + propType + '` supplied to `' + componentName + '`, expected an object.'));
      }
      for (var key in propValue) {
        if (has(propValue, key)) {
          var error = typeChecker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
          if (error instanceof Error) {
            return error;
          }
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createUnionTypeChecker(arrayOfTypeCheckers) {
    if (!Array.isArray(arrayOfTypeCheckers)) {
       true ? printWarning('Invalid argument supplied to oneOfType, expected an instance of array.') : 0;
      return emptyFunctionThatReturnsNull;
    }

    for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
      var checker = arrayOfTypeCheckers[i];
      if (typeof checker !== 'function') {
        printWarning(
          'Invalid argument supplied to oneOfType. Expected an array of check functions, but ' +
          'received ' + getPostfixForTypeWarning(checker) + ' at index ' + i + '.'
        );
        return emptyFunctionThatReturnsNull;
      }
    }

    function validate(props, propName, componentName, location, propFullName) {
      var expectedTypes = [];
      for (var i = 0; i < arrayOfTypeCheckers.length; i++) {
        var checker = arrayOfTypeCheckers[i];
        var checkerResult = checker(props, propName, componentName, location, propFullName, ReactPropTypesSecret);
        if (checkerResult == null) {
          return null;
        }
        if (checkerResult.data && has(checkerResult.data, 'expectedType')) {
          expectedTypes.push(checkerResult.data.expectedType);
        }
      }
      var expectedTypesMessage = (expectedTypes.length > 0) ? ', expected one of type [' + expectedTypes.join(', ') + ']': '';
      return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`' + expectedTypesMessage + '.'));
    }
    return createChainableTypeChecker(validate);
  }

  function createNodeChecker() {
    function validate(props, propName, componentName, location, propFullName) {
      if (!isNode(props[propName])) {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` supplied to ' + ('`' + componentName + '`, expected a ReactNode.'));
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function invalidValidatorError(componentName, location, propFullName, key, type) {
    return new PropTypeError(
      (componentName || 'React class') + ': ' + location + ' type `' + propFullName + '.' + key + '` is invalid; ' +
      'it must be a function, usually from the `prop-types` package, but received `' + type + '`.'
    );
  }

  function createShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      for (var key in shapeTypes) {
        var checker = shapeTypes[key];
        if (typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }
    return createChainableTypeChecker(validate);
  }

  function createStrictShapeTypeChecker(shapeTypes) {
    function validate(props, propName, componentName, location, propFullName) {
      var propValue = props[propName];
      var propType = getPropType(propValue);
      if (propType !== 'object') {
        return new PropTypeError('Invalid ' + location + ' `' + propFullName + '` of type `' + propType + '` ' + ('supplied to `' + componentName + '`, expected `object`.'));
      }
      // We need to check all keys in case some are required but missing from props.
      var allKeys = assign({}, props[propName], shapeTypes);
      for (var key in allKeys) {
        var checker = shapeTypes[key];
        if (has(shapeTypes, key) && typeof checker !== 'function') {
          return invalidValidatorError(componentName, location, propFullName, key, getPreciseType(checker));
        }
        if (!checker) {
          return new PropTypeError(
            'Invalid ' + location + ' `' + propFullName + '` key `' + key + '` supplied to `' + componentName + '`.' +
            '\nBad object: ' + JSON.stringify(props[propName], null, '  ') +
            '\nValid keys: ' + JSON.stringify(Object.keys(shapeTypes), null, '  ')
          );
        }
        var error = checker(propValue, key, componentName, location, propFullName + '.' + key, ReactPropTypesSecret);
        if (error) {
          return error;
        }
      }
      return null;
    }

    return createChainableTypeChecker(validate);
  }

  function isNode(propValue) {
    switch (typeof propValue) {
      case 'number':
      case 'string':
      case 'undefined':
        return true;
      case 'boolean':
        return !propValue;
      case 'object':
        if (Array.isArray(propValue)) {
          return propValue.every(isNode);
        }
        if (propValue === null || isValidElement(propValue)) {
          return true;
        }

        var iteratorFn = getIteratorFn(propValue);
        if (iteratorFn) {
          var iterator = iteratorFn.call(propValue);
          var step;
          if (iteratorFn !== propValue.entries) {
            while (!(step = iterator.next()).done) {
              if (!isNode(step.value)) {
                return false;
              }
            }
          } else {
            // Iterator will provide entry [k,v] tuples rather than values.
            while (!(step = iterator.next()).done) {
              var entry = step.value;
              if (entry) {
                if (!isNode(entry[1])) {
                  return false;
                }
              }
            }
          }
        } else {
          return false;
        }

        return true;
      default:
        return false;
    }
  }

  function isSymbol(propType, propValue) {
    // Native Symbol.
    if (propType === 'symbol') {
      return true;
    }

    // falsy value can't be a Symbol
    if (!propValue) {
      return false;
    }

    // 19.4.3.5 Symbol.prototype[@@toStringTag] === 'Symbol'
    if (propValue['@@toStringTag'] === 'Symbol') {
      return true;
    }

    // Fallback for non-spec compliant Symbols which are polyfilled.
    if (typeof Symbol === 'function' && propValue instanceof Symbol) {
      return true;
    }

    return false;
  }

  // Equivalent of `typeof` but with special handling for array and regexp.
  function getPropType(propValue) {
    var propType = typeof propValue;
    if (Array.isArray(propValue)) {
      return 'array';
    }
    if (propValue instanceof RegExp) {
      // Old webkits (at least until Android 4.0) return 'function' rather than
      // 'object' for typeof a RegExp. We'll normalize this here so that /bla/
      // passes PropTypes.object.
      return 'object';
    }
    if (isSymbol(propType, propValue)) {
      return 'symbol';
    }
    return propType;
  }

  // This handles more types than `getPropType`. Only used for error messages.
  // See `createPrimitiveTypeChecker`.
  function getPreciseType(propValue) {
    if (typeof propValue === 'undefined' || propValue === null) {
      return '' + propValue;
    }
    var propType = getPropType(propValue);
    if (propType === 'object') {
      if (propValue instanceof Date) {
        return 'date';
      } else if (propValue instanceof RegExp) {
        return 'regexp';
      }
    }
    return propType;
  }

  // Returns a string that is postfixed to a warning about an invalid type.
  // For example, "undefined" or "of type array"
  function getPostfixForTypeWarning(value) {
    var type = getPreciseType(value);
    switch (type) {
      case 'array':
      case 'object':
        return 'an ' + type;
      case 'boolean':
      case 'date':
      case 'regexp':
        return 'a ' + type;
      default:
        return type;
    }
  }

  // Returns class name of the object, if any.
  function getClassName(propValue) {
    if (!propValue.constructor || !propValue.constructor.name) {
      return ANONYMOUS;
    }
    return propValue.constructor.name;
  }

  ReactPropTypes.checkPropTypes = checkPropTypes;
  ReactPropTypes.resetWarningCache = checkPropTypes.resetWarningCache;
  ReactPropTypes.PropTypes = ReactPropTypes;

  return ReactPropTypes;
};


/***/ }),

/***/ "../node_modules/prop-types/index.js":
/*!*******************************************!*\
  !*** ../node_modules/prop-types/index.js ***!
  \*******************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

if (true) {
  var ReactIs = __webpack_require__(/*! react-is */ "../node_modules/prop-types/node_modules/react-is/index.js");

  // By explicitly using `prop-types` you are opting into new development behavior.
  // http://fb.me/prop-types-in-prod
  var throwOnDirectAccess = true;
  module.exports = __webpack_require__(/*! ./factoryWithTypeCheckers */ "../node_modules/prop-types/factoryWithTypeCheckers.js")(ReactIs.isElement, throwOnDirectAccess);
} else // removed by dead control flow
{}


/***/ }),

/***/ "../node_modules/prop-types/lib/ReactPropTypesSecret.js":
/*!**************************************************************!*\
  !*** ../node_modules/prop-types/lib/ReactPropTypesSecret.js ***!
  \**************************************************************/
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



var ReactPropTypesSecret = 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED';

module.exports = ReactPropTypesSecret;


/***/ }),

/***/ "../node_modules/prop-types/lib/has.js":
/*!*********************************************!*\
  !*** ../node_modules/prop-types/lib/has.js ***!
  \*********************************************/
/***/ ((module) => {

module.exports = Function.call.bind(Object.prototype.hasOwnProperty);


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js":
/*!************************************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";
/** @license React v16.13.1
 * react-is.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */





if (true) {
  (function() {
'use strict';

// The Symbol used to tag the ReactElement-like types. If there is no native Symbol
// nor polyfill, then a plain number is used for performance.
var hasSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = hasSymbol ? Symbol.for('react.element') : 0xeac7;
var REACT_PORTAL_TYPE = hasSymbol ? Symbol.for('react.portal') : 0xeaca;
var REACT_FRAGMENT_TYPE = hasSymbol ? Symbol.for('react.fragment') : 0xeacb;
var REACT_STRICT_MODE_TYPE = hasSymbol ? Symbol.for('react.strict_mode') : 0xeacc;
var REACT_PROFILER_TYPE = hasSymbol ? Symbol.for('react.profiler') : 0xead2;
var REACT_PROVIDER_TYPE = hasSymbol ? Symbol.for('react.provider') : 0xeacd;
var REACT_CONTEXT_TYPE = hasSymbol ? Symbol.for('react.context') : 0xeace; // TODO: We don't use AsyncMode or ConcurrentMode anymore. They were temporary
// (unstable) APIs that have been removed. Can we remove the symbols?

var REACT_ASYNC_MODE_TYPE = hasSymbol ? Symbol.for('react.async_mode') : 0xeacf;
var REACT_CONCURRENT_MODE_TYPE = hasSymbol ? Symbol.for('react.concurrent_mode') : 0xeacf;
var REACT_FORWARD_REF_TYPE = hasSymbol ? Symbol.for('react.forward_ref') : 0xead0;
var REACT_SUSPENSE_TYPE = hasSymbol ? Symbol.for('react.suspense') : 0xead1;
var REACT_SUSPENSE_LIST_TYPE = hasSymbol ? Symbol.for('react.suspense_list') : 0xead8;
var REACT_MEMO_TYPE = hasSymbol ? Symbol.for('react.memo') : 0xead3;
var REACT_LAZY_TYPE = hasSymbol ? Symbol.for('react.lazy') : 0xead4;
var REACT_BLOCK_TYPE = hasSymbol ? Symbol.for('react.block') : 0xead9;
var REACT_FUNDAMENTAL_TYPE = hasSymbol ? Symbol.for('react.fundamental') : 0xead5;
var REACT_RESPONDER_TYPE = hasSymbol ? Symbol.for('react.responder') : 0xead6;
var REACT_SCOPE_TYPE = hasSymbol ? Symbol.for('react.scope') : 0xead7;

function isValidElementType(type) {
  return typeof type === 'string' || typeof type === 'function' || // Note: its typeof might be other than 'symbol' or 'number' if it's a polyfill.
  type === REACT_FRAGMENT_TYPE || type === REACT_CONCURRENT_MODE_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || typeof type === 'object' && type !== null && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_FUNDAMENTAL_TYPE || type.$$typeof === REACT_RESPONDER_TYPE || type.$$typeof === REACT_SCOPE_TYPE || type.$$typeof === REACT_BLOCK_TYPE);
}

function typeOf(object) {
  if (typeof object === 'object' && object !== null) {
    var $$typeof = object.$$typeof;

    switch ($$typeof) {
      case REACT_ELEMENT_TYPE:
        var type = object.type;

        switch (type) {
          case REACT_ASYNC_MODE_TYPE:
          case REACT_CONCURRENT_MODE_TYPE:
          case REACT_FRAGMENT_TYPE:
          case REACT_PROFILER_TYPE:
          case REACT_STRICT_MODE_TYPE:
          case REACT_SUSPENSE_TYPE:
            return type;

          default:
            var $$typeofType = type && type.$$typeof;

            switch ($$typeofType) {
              case REACT_CONTEXT_TYPE:
              case REACT_FORWARD_REF_TYPE:
              case REACT_LAZY_TYPE:
              case REACT_MEMO_TYPE:
              case REACT_PROVIDER_TYPE:
                return $$typeofType;

              default:
                return $$typeof;
            }

        }

      case REACT_PORTAL_TYPE:
        return $$typeof;
    }
  }

  return undefined;
} // AsyncMode is deprecated along with isAsyncMode

var AsyncMode = REACT_ASYNC_MODE_TYPE;
var ConcurrentMode = REACT_CONCURRENT_MODE_TYPE;
var ContextConsumer = REACT_CONTEXT_TYPE;
var ContextProvider = REACT_PROVIDER_TYPE;
var Element = REACT_ELEMENT_TYPE;
var ForwardRef = REACT_FORWARD_REF_TYPE;
var Fragment = REACT_FRAGMENT_TYPE;
var Lazy = REACT_LAZY_TYPE;
var Memo = REACT_MEMO_TYPE;
var Portal = REACT_PORTAL_TYPE;
var Profiler = REACT_PROFILER_TYPE;
var StrictMode = REACT_STRICT_MODE_TYPE;
var Suspense = REACT_SUSPENSE_TYPE;
var hasWarnedAboutDeprecatedIsAsyncMode = false; // AsyncMode should be deprecated

function isAsyncMode(object) {
  {
    if (!hasWarnedAboutDeprecatedIsAsyncMode) {
      hasWarnedAboutDeprecatedIsAsyncMode = true; // Using console['warn'] to evade Babel and ESLint

      console['warn']('The ReactIs.isAsyncMode() alias has been deprecated, ' + 'and will be removed in React 17+. Update your code to use ' + 'ReactIs.isConcurrentMode() instead. It has the exact same API.');
    }
  }

  return isConcurrentMode(object) || typeOf(object) === REACT_ASYNC_MODE_TYPE;
}
function isConcurrentMode(object) {
  return typeOf(object) === REACT_CONCURRENT_MODE_TYPE;
}
function isContextConsumer(object) {
  return typeOf(object) === REACT_CONTEXT_TYPE;
}
function isContextProvider(object) {
  return typeOf(object) === REACT_PROVIDER_TYPE;
}
function isElement(object) {
  return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
}
function isForwardRef(object) {
  return typeOf(object) === REACT_FORWARD_REF_TYPE;
}
function isFragment(object) {
  return typeOf(object) === REACT_FRAGMENT_TYPE;
}
function isLazy(object) {
  return typeOf(object) === REACT_LAZY_TYPE;
}
function isMemo(object) {
  return typeOf(object) === REACT_MEMO_TYPE;
}
function isPortal(object) {
  return typeOf(object) === REACT_PORTAL_TYPE;
}
function isProfiler(object) {
  return typeOf(object) === REACT_PROFILER_TYPE;
}
function isStrictMode(object) {
  return typeOf(object) === REACT_STRICT_MODE_TYPE;
}
function isSuspense(object) {
  return typeOf(object) === REACT_SUSPENSE_TYPE;
}

exports.AsyncMode = AsyncMode;
exports.ConcurrentMode = ConcurrentMode;
exports.ContextConsumer = ContextConsumer;
exports.ContextProvider = ContextProvider;
exports.Element = Element;
exports.ForwardRef = ForwardRef;
exports.Fragment = Fragment;
exports.Lazy = Lazy;
exports.Memo = Memo;
exports.Portal = Portal;
exports.Profiler = Profiler;
exports.StrictMode = StrictMode;
exports.Suspense = Suspense;
exports.isAsyncMode = isAsyncMode;
exports.isConcurrentMode = isConcurrentMode;
exports.isContextConsumer = isContextConsumer;
exports.isContextProvider = isContextProvider;
exports.isElement = isElement;
exports.isForwardRef = isForwardRef;
exports.isFragment = isFragment;
exports.isLazy = isLazy;
exports.isMemo = isMemo;
exports.isPortal = isPortal;
exports.isProfiler = isProfiler;
exports.isStrictMode = isStrictMode;
exports.isSuspense = isSuspense;
exports.isValidElementType = isValidElementType;
exports.typeOf = typeOf;
  })();
}


/***/ }),

/***/ "../node_modules/prop-types/node_modules/react-is/index.js":
/*!*****************************************************************!*\
  !*** ../node_modules/prop-types/node_modules/react-is/index.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-is.development.js */ "../node_modules/prop-types/node_modules/react-is/cjs/react-is.development.js");
}


/***/ }),

/***/ "../node_modules/react-lifecycles-compat/react-lifecycles-compat.es.js":
/*!*****************************************************************************!*\
  !*** ../node_modules/react-lifecycles-compat/react-lifecycles-compat.es.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   polyfill: () => (/* binding */ polyfill)
/* harmony export */ });
/**
 * Copyright (c) 2013-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

function componentWillMount() {
  // Call this.constructor.gDSFP to support sub-classes.
  var state = this.constructor.getDerivedStateFromProps(this.props, this.state);
  if (state !== null && state !== undefined) {
    this.setState(state);
  }
}

function componentWillReceiveProps(nextProps) {
  // Call this.constructor.gDSFP to support sub-classes.
  // Use the setState() updater to ensure state isn't stale in certain edge cases.
  function updater(prevState) {
    var state = this.constructor.getDerivedStateFromProps(nextProps, prevState);
    return state !== null && state !== undefined ? state : null;
  }
  // Binding "this" is important for shallow renderer support.
  this.setState(updater.bind(this));
}

function componentWillUpdate(nextProps, nextState) {
  try {
    var prevProps = this.props;
    var prevState = this.state;
    this.props = nextProps;
    this.state = nextState;
    this.__reactInternalSnapshotFlag = true;
    this.__reactInternalSnapshot = this.getSnapshotBeforeUpdate(
      prevProps,
      prevState
    );
  } finally {
    this.props = prevProps;
    this.state = prevState;
  }
}

// React may warn about cWM/cWRP/cWU methods being deprecated.
// Add a flag to suppress these warnings for this special case.
componentWillMount.__suppressDeprecationWarning = true;
componentWillReceiveProps.__suppressDeprecationWarning = true;
componentWillUpdate.__suppressDeprecationWarning = true;

function polyfill(Component) {
  var prototype = Component.prototype;

  if (!prototype || !prototype.isReactComponent) {
    throw new Error('Can only polyfill class components');
  }

  if (
    typeof Component.getDerivedStateFromProps !== 'function' &&
    typeof prototype.getSnapshotBeforeUpdate !== 'function'
  ) {
    return Component;
  }

  // If new component APIs are defined, "unsafe" lifecycles won't be called.
  // Error if any of these lifecycles are present,
  // Because they would work differently between older and newer (16.3+) versions of React.
  var foundWillMountName = null;
  var foundWillReceivePropsName = null;
  var foundWillUpdateName = null;
  if (typeof prototype.componentWillMount === 'function') {
    foundWillMountName = 'componentWillMount';
  } else if (typeof prototype.UNSAFE_componentWillMount === 'function') {
    foundWillMountName = 'UNSAFE_componentWillMount';
  }
  if (typeof prototype.componentWillReceiveProps === 'function') {
    foundWillReceivePropsName = 'componentWillReceiveProps';
  } else if (typeof prototype.UNSAFE_componentWillReceiveProps === 'function') {
    foundWillReceivePropsName = 'UNSAFE_componentWillReceiveProps';
  }
  if (typeof prototype.componentWillUpdate === 'function') {
    foundWillUpdateName = 'componentWillUpdate';
  } else if (typeof prototype.UNSAFE_componentWillUpdate === 'function') {
    foundWillUpdateName = 'UNSAFE_componentWillUpdate';
  }
  if (
    foundWillMountName !== null ||
    foundWillReceivePropsName !== null ||
    foundWillUpdateName !== null
  ) {
    var componentName = Component.displayName || Component.name;
    var newApiName =
      typeof Component.getDerivedStateFromProps === 'function'
        ? 'getDerivedStateFromProps()'
        : 'getSnapshotBeforeUpdate()';

    throw Error(
      'Unsafe legacy lifecycles will not be called for components using new component APIs.\n\n' +
        componentName +
        ' uses ' +
        newApiName +
        ' but also contains the following legacy lifecycles:' +
        (foundWillMountName !== null ? '\n  ' + foundWillMountName : '') +
        (foundWillReceivePropsName !== null
          ? '\n  ' + foundWillReceivePropsName
          : '') +
        (foundWillUpdateName !== null ? '\n  ' + foundWillUpdateName : '') +
        '\n\nThe above lifecycles should be removed. Learn more about this warning here:\n' +
        'https://fb.me/react-async-component-lifecycle-hooks'
    );
  }

  // React <= 16.2 does not support static getDerivedStateFromProps.
  // As a workaround, use cWM and cWRP to invoke the new static lifecycle.
  // Newer versions of React will ignore these lifecycles if gDSFP exists.
  if (typeof Component.getDerivedStateFromProps === 'function') {
    prototype.componentWillMount = componentWillMount;
    prototype.componentWillReceiveProps = componentWillReceiveProps;
  }

  // React <= 16.2 does not support getSnapshotBeforeUpdate.
  // As a workaround, use cWU to invoke the new lifecycle.
  // Newer versions of React will ignore that lifecycle if gSBU exists.
  if (typeof prototype.getSnapshotBeforeUpdate === 'function') {
    if (typeof prototype.componentDidUpdate !== 'function') {
      throw new Error(
        'Cannot polyfill getSnapshotBeforeUpdate() for components that do not define componentDidUpdate() on the prototype'
      );
    }

    prototype.componentWillUpdate = componentWillUpdate;

    var componentDidUpdate = prototype.componentDidUpdate;

    prototype.componentDidUpdate = function componentDidUpdatePolyfill(
      prevProps,
      prevState,
      maybeSnapshot
    ) {
      // 16.3+ will not execute our will-update method;
      // It will pass a snapshot value to did-update though.
      // Older versions will require our polyfilled will-update value.
      // We need to handle both cases, but can't just check for the presence of "maybeSnapshot",
      // Because for <= 15.x versions this might be a "prevContext" object.
      // We also can't just check "__reactInternalSnapshot",
      // Because get-snapshot might return a falsy value.
      // So check for the explicit __reactInternalSnapshotFlag flag to determine behavior.
      var snapshot = this.__reactInternalSnapshotFlag
        ? this.__reactInternalSnapshot
        : maybeSnapshot;

      componentDidUpdate.call(this, prevProps, prevState, snapshot);
    };
  }

  return Component;
}




/***/ }),

/***/ "../node_modules/warning/warning.js":
/*!******************************************!*\
  !*** ../node_modules/warning/warning.js ***!
  \******************************************/
/***/ ((module) => {

"use strict";
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



/**
 * Similar to invariant but only logs a warning if the condition is not met.
 * This can be used to log issues in development environments in critical
 * paths. Removing the logging code for production environments will keep the
 * same logic and follow the same code paths.
 */

var __DEV__ = "development" !== 'production';

var warning = function() {};

if (__DEV__) {
  var printWarning = function printWarning(format, args) {
    var len = arguments.length;
    args = new Array(len > 1 ? len - 1 : 0);
    for (var key = 1; key < len; key++) {
      args[key - 1] = arguments[key];
    }
    var argIndex = 0;
    var message = 'Warning: ' +
      format.replace(/%s/g, function() {
        return args[argIndex++];
      });
    if (typeof console !== 'undefined') {
      console.error(message);
    }
    try {
      // --- Welcome to debugging React ---
      // This error was thrown as a convenience so that you can use this stack
      // to find the callsite that caused this warning to fire.
      throw new Error(message);
    } catch (x) {}
  }

  warning = function(condition, format, args) {
    var len = arguments.length;
    args = new Array(len > 2 ? len - 2 : 0);
    for (var key = 2; key < len; key++) {
      args[key - 2] = arguments[key];
    }
    if (format === undefined) {
      throw new Error(
          '`warning(condition, format, ...args)` requires a warning ' +
          'message argument'
      );
    }
    if (!condition) {
      printWarning.apply(null, [format].concat(args));
    }
  };
}

module.exports = warning;


/***/ }),

/***/ "@elementor/router":
/*!**********************************************!*\
  !*** external "elementorAppPackages.router" ***!
  \**********************************************/
/***/ ((module) => {

"use strict";
module.exports = elementorAppPackages.router;

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = React;

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
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
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!****************************************!*\
  !*** ../app/assets/js/app-packages.js ***!
  \****************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _router = _interopRequireDefault(__webpack_require__(/*! ./router */ "../app/assets/js/router.js"));
var _package = __webpack_require__(/*! ./package */ "../app/assets/js/package.js");
var _package2 = _interopRequireDefault(__webpack_require__(/*! ../../modules/site-editor/assets/js/package */ "../app/modules/site-editor/assets/js/package.js"));
/**
 * Temporary solution for share components.
 * TODO.
 */

// Make router available for use within packages.

// Alphabetical order.

window.elementorAppPackages = {
  appUi: _package.appUi,
  components: _package.components,
  hooks: _package.hooks,
  router: _router.default,
  siteEditor: _package2.default
};
})();

/******/ })()
;
//# sourceMappingURL=app-packages.js.map