"use strict";
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["onboarding"],{

/***/ "../app/modules/onboarding/assets/js/app.js":
/*!**************************************************!*\
  !*** ../app/modules/onboarding/assets/js/app.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = App;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _router2 = _interopRequireDefault(__webpack_require__(/*! @elementor/router */ "@elementor/router"));
var _context = __webpack_require__(/*! ./context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _account = _interopRequireDefault(__webpack_require__(/*! ./pages/account */ "../app/modules/onboarding/assets/js/pages/account.js"));
var _helloTheme = _interopRequireDefault(__webpack_require__(/*! ./pages/hello-theme */ "../app/modules/onboarding/assets/js/pages/hello-theme.js"));
var _siteName = _interopRequireDefault(__webpack_require__(/*! ./pages/site-name */ "../app/modules/onboarding/assets/js/pages/site-name.js"));
var _siteLogo = _interopRequireDefault(__webpack_require__(/*! ./pages/site-logo */ "../app/modules/onboarding/assets/js/pages/site-logo.js"));
var _goodToGo = _interopRequireDefault(__webpack_require__(/*! ./pages/good-to-go */ "../app/modules/onboarding/assets/js/pages/good-to-go.js"));
var _uploadAndInstallPro = _interopRequireDefault(__webpack_require__(/*! ./pages/upload-and-install-pro */ "../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js"));
var _chooseFeatures = _interopRequireDefault(__webpack_require__(/*! ./pages/choose-features */ "../app/modules/onboarding/assets/js/pages/choose-features.js"));
var _onboardingEventTracking = __webpack_require__(/*! ./utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function App() {
  // Send an AJAX request to update the database option which makes sure the Onboarding process only runs once,
  // for new Elementor sites.
  (0, _react.useEffect)(function () {
    var _elementorAppConfig;
    _onboardingEventTracking.OnboardingEventTracking.initiateCoreOnboarding();

    // This is to prevent dark theme in onboarding app from the frontend and not backend
    var darkThemeClassName = 'eps-theme-dark';
    var hasDarkMode = document.body.classList.contains(darkThemeClassName);
    if (hasDarkMode) {
      document.body.classList.remove(darkThemeClassName);
    }
    if (!((_elementorAppConfig = elementorAppConfig) !== null && _elementorAppConfig !== void 0 && (_elementorAppConfig = _elementorAppConfig.onboarding) !== null && _elementorAppConfig !== void 0 && _elementorAppConfig.onboardingAlreadyRan)) {
      var formData = new FormData();
      formData.append('_nonce', elementorCommon.config.ajax.nonce);
      formData.append('action', 'elementor_update_onboarding_option');
      fetch(elementorCommon.config.ajax.url, {
        method: 'POST',
        body: formData
      });
    }
    elementorAppConfig.return_url = elementorAppConfig.admin_url;
    return function () {
      if (hasDarkMode) {
        document.body.classList.add(darkThemeClassName);
      }
    };
  }, []);
  return /*#__PURE__*/_react.default.createElement(_context.ContextProvider, null, /*#__PURE__*/_react.default.createElement(_router.LocationProvider, {
    history: _router2.default.appHistory
  }, /*#__PURE__*/_react.default.createElement(_router.Router, null, /*#__PURE__*/_react.default.createElement(_account.default, {
    default: true
  }), /*#__PURE__*/_react.default.createElement(_helloTheme.default, {
    path: "hello"
  }), /*#__PURE__*/_react.default.createElement(_chooseFeatures.default, {
    path: "chooseFeatures"
  }), /*#__PURE__*/_react.default.createElement(_siteName.default, {
    path: "siteName"
  }), /*#__PURE__*/_react.default.createElement(_siteLogo.default, {
    path: "siteLogo"
  }), /*#__PURE__*/_react.default.createElement(_goodToGo.default, {
    path: "goodToGo"
  }), /*#__PURE__*/_react.default.createElement(_uploadAndInstallPro.default, {
    path: "uploadAndInstallPro"
  }))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/button.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/button.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Button;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _objectWithoutProperties2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "../node_modules/@babel/runtime/helpers/objectWithoutProperties.js"));
var _excluded = ["elRef"];
function Button(props) {
  var buttonSettings = props.buttonSettings,
    type = props.type;
  var buttonClasses = 'e-onboarding__button';
  if (type) {
    buttonClasses += " e-onboarding__button-".concat(type);
  }
  if (buttonSettings.className) {
    buttonSettings.className += ' ' + buttonClasses;
  } else {
    buttonSettings.className = buttonClasses;
  }
  var elRef = buttonSettings.elRef,
    buttonProps = (0, _objectWithoutProperties2.default)(buttonSettings, _excluded);
  if (buttonSettings.href) {
    return /*#__PURE__*/_react.default.createElement("a", (0, _extends2.default)({
      ref: elRef
    }, buttonProps), buttonSettings.text);
  }
  return /*#__PURE__*/_react.default.createElement("div", (0, _extends2.default)({
    ref: elRef
  }, buttonProps), buttonSettings.text);
}
Button.propTypes = {
  buttonSettings: PropTypes.object.isRequired,
  type: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/card.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/card.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Card;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Card(_ref) {
  var image = _ref.image,
    imageAlt = _ref.imageAlt,
    text = _ref.text,
    link = _ref.link,
    name = _ref.name,
    clickAction = _ref.clickAction,
    _ref$target = _ref.target,
    target = _ref$target === void 0 ? '_self' : _ref$target;
  var onClick = function onClick() {
    elementorCommon.events.dispatchEvent({
      event: 'starting canvas click',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        selection: name
      }
    });
    if (clickAction) {
      clickAction();
    }
  };
  return /*#__PURE__*/_react.default.createElement("a", {
    target: target,
    className: "e-onboarding__card",
    href: link,
    onClick: onClick
  }, /*#__PURE__*/_react.default.createElement("img", {
    className: "e-onboarding__card-image",
    src: image,
    alt: imageAlt
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__card-text"
  }, text));
}
Card.propTypes = {
  image: PropTypes.string.isRequired,
  imageAlt: PropTypes.string.isRequired,
  text: PropTypes.string.isRequired,
  link: PropTypes.string.isRequired,
  name: PropTypes.string.isRequired,
  clickAction: PropTypes.func,
  target: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/checklist-item.js":
/*!************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/checklist-item.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ChecklistItem;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function ChecklistItem(props) {
  return /*#__PURE__*/_react.default.createElement("li", {
    className: "e-onboarding__checklist-item"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-check-circle"
  }), props.children);
}
ChecklistItem.propTypes = {
  children: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/checklist.js":
/*!*******************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/checklist.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Checklist;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Checklist(props) {
  return /*#__PURE__*/_react.default.createElement("ul", {
    className: "e-onboarding__checklist"
  }, props.children);
}
Checklist.propTypes = {
  children: PropTypes.any.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/go-pro-popover.js":
/*!************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/go-pro-popover.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoProPopover;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _popoverDialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/popover-dialog/popover-dialog */ "../app/assets/js/ui/popover-dialog/popover-dialog.js"));
var _checklist = _interopRequireDefault(__webpack_require__(/*! ./checklist */ "../app/modules/onboarding/assets/js/components/checklist.js"));
var _checklistItem = _interopRequireDefault(__webpack_require__(/*! ./checklist-item */ "../app/modules/onboarding/assets/js/components/checklist-item.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function GoProPopover(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState;
  var upgradeButtonRef = (0, _react.useRef)(null);

  // Handle the Pro Upload popup window.
  var alreadyHaveProButtonRef = (0, _react.useCallback)(function (alreadyHaveProButton) {
    if (!alreadyHaveProButton) {
      return;
    }
    if (!state.currentStep || '' === state.currentStep) {
      return;
    }
    var existingHandler = alreadyHaveProButton._elementorProHandler;
    if (existingHandler) {
      alreadyHaveProButton.removeEventListener('click', existingHandler);
    }
    var clickHandler = function clickHandler(event) {
      event.preventDefault();
      if (!state.currentStep || '' === state.currentStep) {
        return;
      }
      var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'upgrade_already_pro');
      _onboardingEventTracking.OnboardingEventTracking.cancelDelayedNoClickEvent();
      if (stepNumber) {
        _onboardingEventTracking.OnboardingEventTracking.sendTopUpgrade(stepNumber, 'already_pro_user');
      }
      elementorCommon.events.dispatchEvent({
        event: 'already have pro',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });

      // Open the Pro Upload screen in a popup.
      window.open(alreadyHaveProButton.href + '&mode=popup', 'elementorUploadPro', "toolbar=no, menubar=no, width=728, height=531, top=100, left=100");

      // Run the callback for when the upload succeeds.
      elementorCommon.elements.$body.on('elementor/upload-and-install-pro/success', function () {
        updateState({
          hasPro: true,
          proNotice: {
            color: 'success',
            children: __('Pro is now active! You can continue.', 'elementor')
          }
        });
      });
    };
    alreadyHaveProButton._elementorProHandler = clickHandler;
    alreadyHaveProButton.addEventListener('click', clickHandler);
  }, [state.currentStep, updateState]);
  var getElProButton = {
    text: elementorAppConfig.onboarding.experiment ? __('Upgrade now', 'elementor') : __('Upgrade Now', 'elementor'),
    className: 'e-onboarding__go-pro-cta',
    target: '_blank',
    href: 'https://elementor.com/pro/?utm_source=onboarding-wizard&utm_campaign=gopro&utm_medium=wp-dash&utm_content=top-bar-dropdown&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
    tabIndex: 0,
    elRef: function elRef(buttonElement) {
      if (!buttonElement) {
        return;
      }
      upgradeButtonRef.current = buttonElement;
    },
    onClick: function onClick() {
      if (!state.currentStep || '' === state.currentStep) {
        return;
      }
      var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'upgrade_now');
      _onboardingEventTracking.OnboardingEventTracking.cancelDelayedNoClickEvent();
      if (stepNumber) {
        _onboardingEventTracking.OnboardingEventTracking.sendTopUpgrade(stepNumber, 'on_tooltip');
      }
      elementorCommon.events.dispatchEvent({
        event: 'get elementor pro',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
    }
  };
  var targetRef = props.goProButtonRef || upgradeButtonRef;
  return /*#__PURE__*/_react.default.createElement(_popoverDialog.default, {
    targetRef: targetRef,
    wrapperClass: "e-onboarding__go-pro"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-content"
  }, /*#__PURE__*/_react.default.createElement("h2", {
    className: "e-onboarding__go-pro-title"
  }, __('Ready to Get Elementor Pro?', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklist.default, null, /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('90+ Basic & Pro widgets', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('300+ Basic & Pro templates', 'elementor')), /*#__PURE__*/_react.default.createElement(_checklistItem.default, null, __('Premium Support', 'elementor'))), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, __('And so much more!', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: getElProButton
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__go-pro-paragraph"
  }, /*#__PURE__*/_react.default.createElement("a", {
    tabIndex: "0",
    className: "e-onboarding__go-pro-already-have",
    ref: alreadyHaveProButtonRef,
    href: elementorAppConfig.onboarding.urls.uploadPro,
    rel: "opener"
  }, __('Already have Elementor Pro?', 'elementor')))));
}
GoProPopover.propTypes = {
  buttonsConfig: _propTypes.default.array.isRequired,
  goProButtonRef: _propTypes.default.object
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/good-to-go-content-a.js":
/*!******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/good-to-go-content-a.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoodToGoContentA;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _card = _interopRequireDefault(__webpack_require__(/*! ./card */ "../app/modules/onboarding/assets/js/components/card.js"));
var _footerButtons = _interopRequireDefault(__webpack_require__(/*! ./layout/footer-buttons */ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function GoodToGoContentA(_ref) {
  var skipButton = _ref.skipButton;
  var kitLibraryLink = elementorAppConfig.onboarding.urls.kitLibrary + '&referrer=onboarding';
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("h1", {
    className: "e-onboarding__page-content-section-title"
  }, elementorAppConfig.onboarding.experiment ? __('Welcome aboard! What\'s next?', 'elementor') : __('That\'s a wrap! What\'s next?', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-section-text"
  }, __('There are three ways to get started with Elementor:', 'elementor')), /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "e-onboarding__cards-grid e-onboarding__page-content"
  }, /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "blank",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Blank_Canvas.svg',
    imageAlt: __('Click here to create a new page and open it in Elementor Editor', 'elementor'),
    text: __('Edit a blank canvas with the Elementor Editor', 'elementor'),
    link: elementorAppConfig.onboarding.urls.createNewPage,
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('blank_canvas');
    }
  }), /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "template",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Library.svg',
    imageAlt: __('Click here to go to Elementor\'s Website Templates', 'elementor'),
    text: __('Choose a professionally-designed template or import your own', 'elementor'),
    link: kitLibraryLink,
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('kit_library');
      location.href = kitLibraryLink;
      location.reload();
    }
  }), /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "site-planner",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Site_Planner.svg',
    imageAlt: __('Click here to go to Elementor\'s Site Planner', 'elementor'),
    text: __('Create a professional site in minutes using AI', 'elementor'),
    link: elementorAppConfig.onboarding.urls.sitePlanner,
    target: "_blank",
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('site_planner');
    }
  })), /*#__PURE__*/_react.default.createElement(_footerButtons.default, {
    skipButton: _objectSpread(_objectSpread({}, skipButton), {}, {
      target: '_self'
    }),
    className: "e-onboarding__good-to-go-footer"
  }));
}
GoodToGoContentA.propTypes = {
  skipButton: _propTypes.default.object.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/good-to-go-content-b.js":
/*!******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/good-to-go-content-b.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoodToGoContentB;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _card = _interopRequireDefault(__webpack_require__(/*! ./card */ "../app/modules/onboarding/assets/js/components/card.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function GoodToGoContentB(_ref) {
  var skipButton = _ref.skipButton;
  var kitLibraryLink = elementorAppConfig.onboarding.urls.kitLibrary + '&referrer=onboarding';
  var handleBlankCanvasClick = function handleBlankCanvasClick(event) {
    _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('blank_canvas');
    if (skipButton.href) {
      event.preventDefault();
      window.location.href = skipButton.href;
    }
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("h1", {
    className: "e-onboarding__page-content-section-title"
  }, __('How would you like to create your website?', 'elementor')), /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "center",
    className: "e-onboarding__cards-grid e-onboarding__page-content e-onboarding__cards-grid--good-to-go-variant-b"
  }, /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "template",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Library.svg',
    imageAlt: __('Click here to go to Elementor\'s Website Templates', 'elementor'),
    text: __('Choose a professionally-designed template or import your own', 'elementor'),
    link: kitLibraryLink,
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('kit_library');
      location.href = kitLibraryLink;
      location.reload();
    }
  }), /*#__PURE__*/_react.default.createElement(_card.default, {
    name: "site-planner",
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Site_Planner.svg',
    imageAlt: __('Click here to go to Elementor\'s Site Planner', 'elementor'),
    text: __('Create a professional site in minutes using AI', 'elementor'),
    link: elementorAppConfig.onboarding.urls.sitePlanner,
    target: "_blank",
    clickAction: function clickAction() {
      _onboardingEventTracking.OnboardingEventTracking.handleSiteStarterChoice('site_planner');
    }
  })), /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "e-onboarding__footer e-onboarding__good-to-go-footer"
  }, /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: {
      text: skipButton.text,
      href: skipButton.href,
      target: '_self',
      onClick: handleBlankCanvasClick
    },
    type: "skip"
  })));
}
GoodToGoContentB.propTypes = {
  skipButton: _propTypes.default.object.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/footer-buttons.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FooterButtons;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _button = _interopRequireDefault(__webpack_require__(/*! ../button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _skipButton = _interopRequireDefault(__webpack_require__(/*! ../skip-button */ "../app/modules/onboarding/assets/js/components/skip-button.js"));
function FooterButtons(_ref) {
  var actionButton = _ref.actionButton,
    skipButton = _ref.skipButton,
    className = _ref.className;
  var classNames = 'e-onboarding__footer';
  if (className) {
    classNames += ' ' + className;
  }
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: classNames
  }, actionButton && /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: actionButton,
    type: "action"
  }), skipButton && /*#__PURE__*/_react.default.createElement(_skipButton.default, {
    button: skipButton
  }));
}
FooterButtons.propTypes = {
  actionButton: PropTypes.object,
  skipButton: PropTypes.object,
  className: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/header.js":
/*!***********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/header.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Header;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _goProPopover = _interopRequireDefault(__webpack_require__(/*! ../go-pro-popover */ "../app/modules/onboarding/assets/js/components/go-pro-popover.js"));
var _headerButtons = _interopRequireDefault(__webpack_require__(/*! elementor-app/layout/header-buttons */ "../app/assets/js/layout/header-buttons.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Header(props) {
  (0, _usePageTitle.default)({
    title: props.title
  });
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state;
  var trackXButtonExit = function trackXButtonExit() {
    var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.sendExitButtonEvent(stepNumber || state.currentStep);
  };
  var onClose = function onClose() {
    trackXButtonExit();
    elementorCommon.events.dispatchEvent({
      event: 'close modal',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
    setTimeout(function () {
      window.top.location = elementorAppConfig.admin_url;
    }, 100);
  };
  return /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "eps-app__header e-onboarding__header"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__logo-title-wrapper e-onboarding__header-logo"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eps-app__logo eicon-elementor"
  }), /*#__PURE__*/_react.default.createElement("img", {
    src: elementorCommon.config.urls.assets + 'images/logo-platform.svg',
    alt: __('Elementor Logo', 'elementor')
  })), /*#__PURE__*/_react.default.createElement(_headerButtons.default, {
    buttons: props.buttons,
    onClose: onClose
  }), !state.hasPro && /*#__PURE__*/_react.default.createElement(_goProPopover.default, {
    buttonsConfig: props.buttons,
    goProButtonRef: props.goProButtonRef
  }));
}
Header.propTypes = {
  title: _propTypes.default.string,
  buttons: _propTypes.default.arrayOf(_propTypes.default.object),
  goProButtonRef: _propTypes.default.object
};
Header.defaultProps = {
  buttons: []
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/layout.js":
/*!***********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/layout.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Layout;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _header = _interopRequireDefault(__webpack_require__(/*! ./header */ "../app/modules/onboarding/assets/js/components/layout/header.js"));
var _progressBar = _interopRequireDefault(__webpack_require__(/*! ../progress-bar/progress-bar */ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _connect = _interopRequireDefault(__webpack_require__(/*! ../../utils/connect */ "../app/modules/onboarding/assets/js/utils/connect.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function getCurrentStepForTracking(pageId, currentStep) {
  return pageId || currentStep || 'account';
}
function shouldResetupButtonTracking(buttonRef, pageId, currentStep) {
  if (!buttonRef) {
    return false;
  }
  var currentStepForTracking = getCurrentStepForTracking(pageId, currentStep);
  var currentTrackedStep = buttonRef.dataset.onboardingStep;
  return currentTrackedStep !== currentStepForTracking;
}
function Layout(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState;
  var stepNumber = (0, _react.useMemo)(function () {
    return _onboardingEventTracking.OnboardingEventTracking.getStepNumber(props.pageId);
  }, [props.pageId]);
  var goProButtonRef = (0, _react.useRef)();
  var setupTopbarUpgradeTracking = (0, _react.useCallback)(function (buttonElement) {
    if (!buttonElement) {
      return;
    }
    var currentStep = getCurrentStepForTracking(props.pageId, state.currentStep);
    goProButtonRef.current = buttonElement;
    return _onboardingEventTracking.OnboardingEventTracking.setupSingleUpgradeButton(buttonElement, currentStep);
  }, [state.currentStep, props.pageId]);
  var handleTopbarConnectSuccess = (0, _react.useCallback)(function () {
    updateState({
      isLibraryConnected: true
    });
  }, [updateState]);
  (0, _react.useEffect)(function () {
    // Send modal load event for current step.
    elementorCommon.events.dispatchEvent({
      event: 'modal load',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: props.pageId,
        user_state: elementorCommon.config.library_connect.is_connected ? 'logged' : 'anon'
      }
    });
    if (goProButtonRef.current) {
      setupTopbarUpgradeTracking(goProButtonRef.current);
    }
    updateState({
      currentStep: props.pageId,
      nextStep: props.nextStep || '',
      proNotice: null
    });
  }, [setupTopbarUpgradeTracking, stepNumber, props.pageId, props.nextStep, updateState]);
  (0, _react.useEffect)(function () {
    if (shouldResetupButtonTracking(goProButtonRef.current, props.pageId, state.currentStep)) {
      setupTopbarUpgradeTracking(goProButtonRef.current);
    }
  }, [state.currentStep, props.pageId, setupTopbarUpgradeTracking]);
  var headerButtons = [],
    createAccountButton = {
      id: 'create-account',
      text: __('Create Account', 'elementor'),
      hideText: false,
      elRef: (0, _react.useRef)(),
      url: elementorAppConfig.onboarding.urls.signUp + elementorAppConfig.onboarding.utms.connectTopBar,
      target: '_blank',
      rel: 'opener',
      onClick: function onClick() {
        _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('CREATE_MY_ACCOUNT', {
          currentStep: stepNumber,
          createAccountClicked: 'topbar'
        });
        elementorCommon.events.dispatchEvent({
          event: 'create account',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: 'header'
          }
        });
      }
    };
  if (state.isLibraryConnected) {
    headerButtons.push({
      id: 'my-elementor',
      text: __('My Elementor', 'elementor'),
      hideText: false,
      icon: 'eicon-user-circle-o',
      url: 'https://my.elementor.com/websites/?utm_source=onboarding-wizard&utm_medium=wp-dash&utm_campaign=my-account&utm_content=top-bar&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
      target: '_blank',
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'my elementor click',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: 'header'
          }
        });
      }
    });
  } else {
    headerButtons.push(createAccountButton);
  }
  if (!state.hasPro) {
    headerButtons.push({
      id: 'go-pro',
      text: __('Upgrade', 'elementor'),
      hideText: false,
      className: 'eps-button__go-pro-btn',
      url: 'https://elementor.com/pro/?utm_source=onboarding-wizard&utm_campaign=gopro&utm_medium=wp-dash&utm_content=top-bar&utm_term=' + elementorAppConfig.onboarding.onboardingVersion,
      target: '_blank',
      elRef: setupTopbarUpgradeTracking,
      onClick: function onClick() {
        var currentStep = getCurrentStepForTracking(props.pageId, state.currentStep);
        var currentStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(currentStep);
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(currentStepNumber, 'upgrade_topbar');
        elementorCommon.events.dispatchEvent({
          event: 'go pro',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: currentStep
          }
        });
      }
    });
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__lightbox"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app e-onboarding"
  }, !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: createAccountButton.elRef,
    successCallback: handleTopbarConnectSuccess
  }), /*#__PURE__*/_react.default.createElement(_header.default, {
    title: __('Getting Started', 'elementor'),
    buttons: headerButtons,
    goProButtonRef: goProButtonRef
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: 'eps-app__main e-onboarding__page-' + props.pageId
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-onboarding__content"
  }, /*#__PURE__*/_react.default.createElement(_progressBar.default, null), props.children))));
}
Layout.propTypes = {
  pageId: _propTypes.default.string.isRequired,
  nextStep: _propTypes.default.string,
  className: _propTypes.default.string,
  children: _propTypes.default.any.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js":
/*!************************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/layout/page-content-layout.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PageContentLayout;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _grid = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/grid/grid */ "../app/assets/js/ui/grid/grid.js"));
var _notice = _interopRequireDefault(__webpack_require__(/*! ../notice */ "../app/modules/onboarding/assets/js/components/notice.js"));
var _footerButtons = _interopRequireDefault(__webpack_require__(/*! ./footer-buttons */ "../app/modules/onboarding/assets/js/components/layout/footer-buttons.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function PageContentLayout(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state;
  var printNotices = function printNotices() {
    return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, props.noticeState && /*#__PURE__*/_react.default.createElement(_notice.default, {
      noticeState: props.noticeState
    }), state.proNotice && /*#__PURE__*/_react.default.createElement(_notice.default, {
      noticeState: state.proNotice
    }));
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_grid.default, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "e-onboarding__page-content"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-start"
  }, /*#__PURE__*/_react.default.createElement("h1", {
    className: "e-onboarding__page-content-section-title"
  }, props.title, props.secondLineTitle && /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("br", null), props.secondLineTitle)), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-section-text"
  }, props.children)), props.image && /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-end"
  }, /*#__PURE__*/_react.default.createElement("img", {
    src: props.image,
    alt: "Information"
  }))), props.noticeState && /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice-container"
  }, props.noticeState || state.proNotice ? printNotices() : /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice-empty-spacer"
  })), /*#__PURE__*/_react.default.createElement(_footerButtons.default, {
    actionButton: props.actionButton,
    skipButton: props.skipButton
  }));
}
PageContentLayout.propTypes = {
  title: _propTypes.default.string,
  secondLineTitle: _propTypes.default.string,
  children: _propTypes.default.any,
  image: _propTypes.default.string,
  actionButton: _propTypes.default.object,
  skipButton: _propTypes.default.object,
  noticeState: _propTypes.default.any
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/message.js":
/*!*****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/message.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Message;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function Message(_ref) {
  var tier = _ref.tier;
  /* Translators: %s: Plan name */
  var translatedString = __('Based on the features you chose, we recommend the %s plan, or higher', 'elementor');
  var _translatedString$spl = translatedString.split('%s'),
    _translatedString$spl2 = (0, _slicedToArray2.default)(_translatedString$spl, 2),
    messageFirstPart = _translatedString$spl2[0],
    messageSecondPart = _translatedString$spl2[1];
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, messageFirstPart, /*#__PURE__*/_react.default.createElement("strong", null, tier), messageSecondPart);
}
Message.propTypes = {
  tier: PropTypes.string.isRequired
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/notice.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/notice.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Notice;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
function Notice(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__notice e-onboarding__notice--".concat(props.noticeState.type)
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: props.noticeState.icon
  }), /*#__PURE__*/_react.default.createElement("span", {
    className: "e-onboarding__notice-text"
  }, props.noticeState.message));
}
Notice.propTypes = {
  noticeState: PropTypes.object
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js":
/*!****************************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ProgressBarItem;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ProgressBarItem(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    stepCompleted = 'completed' === state.steps[props.id],
    stepSkipped = 'skipped' === state.steps[props.id];
  var itemClasses = 'e-onboarding__progress-bar-item';
  if (props.id === state.currentStep) {
    itemClasses += ' e-onboarding__progress-bar-item--active';
  } else if (stepCompleted) {
    itemClasses += ' e-onboarding__progress-bar-item--completed';
  } else if (stepSkipped) {
    itemClasses += ' e-onboarding__progress-bar-item--skipped';
  }
  return (
    /*#__PURE__*/
    // eslint-disable-next-line jsx-a11y/click-events-have-key-events, jsx-a11y/no-static-element-interactions
    _react.default.createElement("div", {
      onClick: props.onClick,
      className: itemClasses
    }, /*#__PURE__*/_react.default.createElement("div", {
      className: "e-onboarding__progress-bar-item-icon"
    }, stepCompleted ? /*#__PURE__*/_react.default.createElement("i", {
      className: "eicon-check"
    }) : props.index + 1), props.title)
  );
}
ProgressBarItem.propTypes = {
  index: PropTypes.number.isRequired,
  id: PropTypes.string.isRequired,
  title: PropTypes.string.isRequired,
  route: PropTypes.string,
  onClick: PropTypes.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js":
/*!***********************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/progress-bar/progress-bar.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ProgressBar;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _context = __webpack_require__(/*! ../../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _progressBarItem = _interopRequireDefault(__webpack_require__(/*! ./progress-bar-item */ "../app/modules/onboarding/assets/js/components/progress-bar/progress-bar-item.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ProgressBar() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    navigate = (0, _router.useNavigate)(),
    progressBarItemsConfig = [{
      id: 'account',
      title: __('Elementor Account', 'elementor'),
      route: 'account'
    }];

  // If hello theme is already activated when onboarding starts, don't show this step in the onboarding.
  if (!elementorAppConfig.onboarding.helloActivated) {
    progressBarItemsConfig.push({
      id: 'hello',
      title: __('Hello Biz Theme', 'elementor'),
      route: 'hello'
    });
  }
  if (elementorAppConfig.onboarding.experiment) {
    progressBarItemsConfig.push({
      id: 'chooseFeatures',
      title: __('Choose Features', 'elementor'),
      route: 'chooseFeatures'
    });
  } else {
    progressBarItemsConfig.push({
      id: 'siteName',
      title: __('Site Name', 'elementor'),
      route: 'site-name'
    }, {
      id: 'siteLogo',
      title: __('Site Logo', 'elementor'),
      route: 'site-logo'
    });
  }
  progressBarItemsConfig.push({
    id: 'goodToGo',
    title: __('Good to Go', 'elementor'),
    route: 'good-to-go'
  });
  var progressBarItems = progressBarItemsConfig.map(function (itemConfig, index) {
    itemConfig.index = index;
    if (state.steps[itemConfig.id]) {
      itemConfig.onClick = function () {
        var currentStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
        var nextStepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(itemConfig.id);
        if (4 === currentStepNumber) {
          _onboardingEventTracking.OnboardingEventTracking.trackStepAction(4, 'stepper_clicks', {
            from_step: currentStepNumber,
            to_step: nextStepNumber
          });
          _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(4);
        }
        elementorCommon.events.dispatchEvent({
          event: 'step click',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            next_step: itemConfig.id
          }
        });
        navigate('/onboarding/' + itemConfig.id);
      };
    }
    return /*#__PURE__*/_react.default.createElement(_progressBarItem.default, (0, _extends2.default)({
      key: itemConfig.id
    }, itemConfig));
  });
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__progress-bar"
  }, progressBarItems);
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/skip-button.js":
/*!*********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/skip-button.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SkipButton;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _button = _interopRequireDefault(__webpack_require__(/*! ./button */ "../app/modules/onboarding/assets/js/components/button.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SkipButton(props) {
  var button = props.button,
    className = props.className,
    _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    navigate = (0, _router.useNavigate)(),
    skipStep = function skipStep() {
      var mutatedState = JSON.parse(JSON.stringify(state));
      mutatedState.steps[state.currentStep] = 'skipped';
      updateState(mutatedState);
      if (state.nextStep) {
        navigate('onboarding/' + state.nextStep);
      }
    },
    action = button.action || skipStep;

  // Make sure the 'action' prop doesn't get printed on the button markup which causes an error.
  delete button.action;

  // Handle both href and non-href skip buttons properly
  button.onClick = function (event) {
    var stepNumber = _onboardingEventTracking.OnboardingEventTracking.getStepNumber(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.trackStepAction(stepNumber, 'skipped');
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(stepNumber);
    _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('SKIP', {
      currentStep: stepNumber
    });
    if (4 === stepNumber) {
      _onboardingEventTracking.OnboardingEventTracking.storeExitEventForLater('step4_skip_button', stepNumber);
    }
    elementorCommon.events.dispatchEvent({
      event: 'skip',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
    if (button.href) {
      event.preventDefault();
      setTimeout(function () {
        window.location.href = button.href;
      }, 100);
    } else {
      action();
    }
  };
  return /*#__PURE__*/_react.default.createElement(_button.default, {
    buttonSettings: button,
    className: className,
    type: "skip"
  });
}
SkipButton.propTypes = {
  button: PropTypes.object.isRequired,
  className: PropTypes.string
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/theme-selection-card.js":
/*!******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/theme-selection-card.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ThemeSelectionCard;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function ThemeSelectionCard(_ref) {
  var themeSlug = _ref.themeSlug,
    title = _ref.title,
    description = _ref.description,
    illustration = _ref.illustration,
    isSelected = _ref.isSelected,
    isLoading = _ref.isLoading,
    onSelect = _ref.onSelect,
    ariaLabel = _ref['aria-label'],
    role = _ref.role,
    tabIndex = _ref.tabIndex,
    onKeyDown = _ref.onKeyDown;
  var handleClick = function handleClick() {
    if (onSelect && !isLoading) {
      onSelect(themeSlug);
    }
  };
  var handleKeyDown = function handleKeyDown(e) {
    if (('Enter' === e.key || ' ' === e.key) && onSelect && !isLoading) {
      e.preventDefault();
      onSelect(themeSlug);
    }
  };
  var handleKeyDownEvent = onKeyDown || handleKeyDown;
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__theme-card ".concat(isSelected ? 'e-onboarding__theme-card--selected' : '', " ").concat(isLoading ? 'e-onboarding__theme-card--loading' : ''),
    "data-theme": themeSlug,
    onClick: handleClick,
    role: role || 'button',
    tabIndex: tabIndex !== undefined ? tabIndex : 0,
    onKeyDown: handleKeyDownEvent,
    "aria-label": ariaLabel
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__theme-card-illustration ".concat((illustration === null || illustration === void 0 ? void 0 : illustration.className) || '')
  }, illustration === null || illustration === void 0 ? void 0 : illustration.svg), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__theme-card-content"
  }, /*#__PURE__*/_react.default.createElement("h3", {
    className: "e-onboarding__theme-card-title"
  }, title), /*#__PURE__*/_react.default.createElement("p", {
    className: "e-onboarding__theme-card-description"
  }, description)));
}
ThemeSelectionCard.propTypes = {
  themeSlug: _propTypes.default.string.isRequired,
  title: _propTypes.default.string.isRequired,
  description: _propTypes.default.string.isRequired,
  illustration: _propTypes.default.shape({
    svg: _propTypes.default.element.isRequired,
    className: _propTypes.default.string
  }).isRequired,
  isSelected: _propTypes.default.bool,
  isLoading: _propTypes.default.bool,
  onSelect: _propTypes.default.func,
  'aria-label': _propTypes.default.string,
  role: _propTypes.default.string,
  tabIndex: _propTypes.default.number,
  onKeyDown: _propTypes.default.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/theme-selection-content-a.js":
/*!***********************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/theme-selection-content-a.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ThemeSelectionContentA;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ./layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
function ThemeSelectionContentA(_ref) {
  var actionButton = _ref.actionButton,
    skipButton = _ref.skipButton,
    noticeState = _ref.noticeState;
  return /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Hello_Biz.svg',
    title: __('Every site starts with a theme.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Hello Biz by Elementor helps you launch your professional business website - fast.', 'elementor')), !elementorAppConfig.onboarding.experiment && /*#__PURE__*/_react.default.createElement("p", null, __('Here\'s why:', 'elementor')), /*#__PURE__*/_react.default.createElement("ul", {
    className: "e-onboarding__feature-list"
  }, /*#__PURE__*/_react.default.createElement("li", null, __('Get online faster', 'elementor')), /*#__PURE__*/_react.default.createElement("li", null, __('Lightweight and fast loading', 'elementor')), /*#__PURE__*/_react.default.createElement("li", null, __('Great for SEO', 'elementor'))));
}
ThemeSelectionContentA.propTypes = {
  actionButton: _propTypes.default.object.isRequired,
  skipButton: _propTypes.default.object.isRequired,
  noticeState: _propTypes.default.object
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/theme-selection-content-b.js":
/*!***********************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/theme-selection-content-b.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ThemeSelectionContentB;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ./layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _themeSelectionCard = _interopRequireDefault(__webpack_require__(/*! ./theme-selection-card */ "../app/modules/onboarding/assets/js/components/theme-selection-card.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var getThemeData = function getThemeData() {
  return [{
    slug: 'hello-theme',
    title: __('Hello', 'elementor'),
    description: __('It\'s fast, flexible, and beginner-friendly, offering a solid foundation for customizable designs.', 'elementor'),
    illustration: {
      svg: /*#__PURE__*/_react.default.createElement("svg", {
        width: "305",
        height: "164",
        viewBox: "0 0 305 164",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
      }, /*#__PURE__*/_react.default.createElement("rect", {
        x: "97.5146",
        y: "130.5",
        width: "0.398535",
        height: "0.398535",
        transform: "rotate(-2.18781 97.5146 130.5)",
        fill: "#cccccc"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M16.1259 137.899L14.1372 138.024L19.5181 223.129L21.5068 223.004L16.1259 137.899Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M9.44682 233.715L14.0914 19.0745L212.074 19.1003L207.454 233.751L9.44682 233.715Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M9.44682 233.715L14.0914 19.0745L212.074 19.1003L207.454 233.751L9.44682 233.715Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M202.793 233.962L207.437 19.3215L219.305 19.5368L214.672 234.188L202.793 233.962Z",
        fill: "#F0F0FF"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M208.415 20.3405L218.287 20.5147L213.696 233.17L203.718 232.989L208.31 20.3445L208.415 20.3405ZM206.367 18.319L201.679 234.896L215.648 235.16L220.275 18.5267L206.367 18.319Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "103.898",
        y: "130.257",
        width: "0.398535",
        height: "0.398535",
        transform: "rotate(-2.18781 103.898 130.257)",
        fill: "#cccccc"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M22.8358 87.0786L22.9487 86.042L23.2435 87.0044L22.8358 87.0786Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M23.2303 79.285L23.2173 78.9453C23.2173 78.9453 23.3276 79.0701 23.329 79.1053C23.3303 79.1404 23.2632 79.2251 23.2303 79.285Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M75.7296 51.8251C79.0103 51.4182 81.0516 52.654 81.113 55.4904L72.8915 55.218C72.8158 54.8197 72.8209 54.4104 72.9065 54.0141C72.9921 53.6179 73.1565 53.243 73.3899 52.9115C73.6798 52.574 74.0386 52.3024 74.4421 52.115C74.8457 51.9276 75.2847 51.8288 75.7296 51.8251Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M206.102 83.8532C206.837 83.8597 207.565 84.011 208.242 84.2987C208.919 84.5863 209.533 85.0045 210.049 85.5294C210.565 86.0544 210.972 86.6757 211.247 87.358C211.523 88.0404 211.661 88.7703 211.655 89.5061L211.405 121.035C211.398 121.77 211.247 122.498 210.959 123.175C210.672 123.852 210.253 124.466 209.728 124.982C209.204 125.498 208.582 125.905 207.9 126.18C207.218 126.456 206.488 126.594 205.752 126.588C205.016 126.583 204.287 126.433 203.609 126.145C202.931 125.858 202.317 125.44 201.801 124.915C201.285 124.39 200.878 123.768 200.603 123.085C200.328 122.402 200.19 121.671 200.199 120.935L200.46 89.4059C200.474 87.922 201.075 86.5039 202.133 85.4629C203.19 84.4219 204.618 83.843 206.102 83.8532Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("mask", {
        id: "mask0_222_51325",
        style: {
          maskType: 'alpha'
        },
        maskUnits: "userSpaceOnUse",
        x: "131",
        y: "42",
        width: "212",
        height: "172"
      }, /*#__PURE__*/_react.default.createElement("path", {
        d: "M173.627 46.9854C173.627 46.9854 108.276 62.8966 141.236 154.779C174.197 246.662 296.933 204.108 296.933 204.108C296.933 204.108 378.131 184.124 324.212 99.8914C273.072 20.0029 173.627 46.9854 173.627 46.9854Z",
        fill: "#F0F0FF"
      })), /*#__PURE__*/_react.default.createElement("g", {
        mask: "url(#mask0_222_51325)"
      }, /*#__PURE__*/_react.default.createElement("path", {
        d: "M206.169 18.7754L14.5165 18.7754L10.0801 234.106L201.288 234.106L206.169 18.7754Z",
        fill: "#DCDBFF"
      })), /*#__PURE__*/_react.default.createElement("rect", {
        x: "199.67",
        y: "83.749",
        width: "12.0793",
        height: "43.2336",
        rx: "6.03966",
        fill: "white",
        stroke: "black",
        strokeWidth: "2"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M36.6708 86.7568C36.9784 86.8337 37.2592 86.9072 37.5801 86.9306C39.793 87.1045 43.9213 87.1312 46.1142 86.9306C46.5487 86.8905 46.8897 86.8671 47.1905 86.5061V77.8249L47.4412 77.5742H52.2849C52.3417 77.5742 52.539 77.7948 52.7028 77.7414V101.117H47.1905V91.8512L46.9398 91.6005H36.6675V101.117H31.1553V77.8249L31.406 77.5742H36.4168L36.6675 77.8249V86.7568H36.6708Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M75.253 93.6032H62.1426C61.3738 93.6032 62.5605 95.2846 62.7544 95.4952C64.2854 97.1599 67.3039 97.3404 69.3028 96.585C70.1085 96.2808 70.5932 95.6757 71.3219 95.4284L71.6762 95.5922L74.0696 98.5004C73.1403 99.6302 71.7364 100.539 70.319 100.937C64.5494 102.555 57.0081 100.088 56.3864 93.3458C55.0627 79.0221 77.3355 79.3664 75.2563 93.5999L75.253 93.6032ZM61.9755 90.4309H69.9915C70.4762 90.4309 69.9513 89.2977 69.8343 89.0871C68.3368 86.4029 64.4759 86.1355 62.5571 88.5088C62.2931 88.8364 61.3471 90.2571 61.9755 90.4343V90.4309Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M105.946 82.7993C113.083 82.0639 118.826 87.4825 116.917 94.7697C114.283 104.805 96.1218 103.605 97.4856 90.5378C97.9235 86.3493 101.915 83.2171 105.95 82.8026L105.946 82.7993ZM106.441 87.2986C101.654 88.0474 101.597 96.2405 106.611 96.8991C113.604 97.8183 113.534 86.1888 106.441 87.2986Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M78.843 76.2373H83.8538L84.1045 76.488V100.867L83.8538 101.118H78.843L78.5923 100.867V76.488L78.843 76.2373Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M88.8616 76.2373H93.8724L94.1231 76.488V100.867L93.8724 101.118H88.8616L88.6143 100.867V76.488L88.8616 76.2373Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M49.8644 111.634C49.3229 111.57 48.6008 111.848 48.1997 111.383C48.7045 110.648 49.8811 110.564 49.8644 111.634Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        fillRule: "evenodd",
        clipRule: "evenodd",
        d: "M32.9312 62.6564C31.7733 60.9235 31.1553 58.8862 31.1553 56.8021C31.1553 54.0074 32.2655 51.3272 34.2416 49.351C36.2178 47.3748 38.898 46.2646 41.6927 46.2646C43.7768 46.2646 45.8142 46.8827 47.547 48.0405C49.2799 49.1984 50.6305 50.8441 51.4281 52.7696C52.2256 54.6951 52.4343 56.8138 52.0277 58.8579C51.6211 60.9019 50.6176 62.7795 49.1438 64.2532C47.6702 65.7269 45.7926 66.7305 43.7485 67.1371C41.7044 67.5437 39.5857 67.335 37.6602 66.5374C35.7348 65.7399 34.089 64.3893 32.9312 62.6564ZM39.0588 52.4112H37.3028V61.1926H39.0588V52.4112ZM46.0827 52.4112H40.8148V54.1671H46.0827V52.4112ZM46.0827 55.9231H40.8148V57.6791H46.0827V55.9231ZM46.0827 59.4367H40.8148V61.1926H46.0827V59.4367Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M122.785 115.106V115.565H31.1553V115.106H122.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M122.785 121.979V122.437H31.1553V121.979H122.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M122.785 128.851V129.309H31.1553V128.851H122.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M31.1553 143.759C31.1553 140.216 34.027 137.345 37.5694 137.345H80.1774C83.7198 137.345 86.5915 140.216 86.5915 143.759C86.5915 147.301 83.7198 150.173 80.1774 150.173H37.5694C34.027 150.173 31.1553 147.301 31.1553 143.759Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M15.4765 14.9994L219.902 14.9155L214.999 233.424L10.5366 233.509L15.4765 14.9994ZM13.4435 13.0282L8.48565 235.552L217.096 235.465L222.051 12.8688L13.4398 12.9319L13.4435 13.0282Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M14.6611 22.8996V12.8203L221.745 13.4923V22.8996H14.6611Z",
        fill: "black"
      }))
    }
  }, {
    slug: 'hello-biz',
    title: __('Hello Biz', 'elementor'),
    description: __('It offers premium Elementor tools, and a responsive foundation for startups and portfolios.', 'elementor'),
    illustration: {
      svg: /*#__PURE__*/_react.default.createElement("svg", {
        width: "231",
        height: "170",
        viewBox: "0 0 231 170",
        fill: "none",
        xmlns: "http://www.w3.org/2000/svg"
      }, /*#__PURE__*/_react.default.createElement("rect", {
        x: "97.5146",
        y: "125.526",
        width: "0.398535",
        height: "0.398535",
        transform: "rotate(-2.18781 97.5146 125.526)",
        fill: "#cccccc"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M16.1264 132.926L14.1377 133.052L19.5186 218.157L21.5073 218.031L16.1264 132.926Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M9.44682 228.741L14.0914 14.1008L212.074 14.1267L207.454 228.778L9.44682 228.741Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M9.44682 228.741L14.0914 14.1008L212.074 14.1267L207.454 228.778L9.44682 228.741Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M202.793 228.988L207.437 14.3479L219.305 14.5632L214.672 229.215L202.793 228.988Z",
        fill: "#F0F0FF"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "103.898",
        y: "125.283",
        width: "0.398535",
        height: "0.398535",
        transform: "rotate(-2.18781 103.898 125.283)",
        fill: "#cccccc"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M22.8358 82.105L22.9487 81.0684L23.2435 82.0307L22.8358 82.105Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M23.2303 74.3114L23.2173 73.9717C23.2173 73.9717 23.3276 74.0965 23.329 74.1316C23.3303 74.1668 23.2632 74.2515 23.2303 74.3114Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M75.7296 46.8515C79.0103 46.4446 81.0516 47.6804 81.113 50.5168L72.8915 50.2443C72.8158 49.8461 72.8209 49.4367 72.9065 49.0405C72.9921 48.6443 73.1565 48.2693 73.3899 47.9379C73.6798 47.6004 74.0386 47.3288 74.4421 47.1414C74.8457 46.954 75.2847 46.8551 75.7296 46.8515Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M206.102 78.8795C206.837 78.886 207.565 79.0374 208.242 79.325C208.919 79.6126 209.533 80.0309 210.049 80.5558C210.565 81.0807 210.972 81.7021 211.247 82.3844C211.523 83.0667 211.661 83.7966 211.655 84.5325L211.405 116.061C211.398 116.797 211.247 117.524 210.959 118.201C210.672 118.879 210.253 119.493 209.728 120.008C209.204 120.524 208.582 120.931 207.9 121.207C207.218 121.482 206.488 121.621 205.752 121.614C205.016 121.609 204.287 121.459 203.609 121.172C202.931 120.885 202.317 120.467 201.801 119.941C201.285 119.416 200.878 118.794 200.603 118.111C200.328 117.428 200.19 116.697 200.199 115.961L200.46 84.4323C200.474 82.9483 201.075 81.5303 202.133 80.4893C203.19 79.4483 204.618 78.8693 206.102 78.8795Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "199.67",
        y: "78.7754",
        width: "12.0793",
        height: "43.2336",
        rx: "6.03966",
        fill: "white",
        stroke: "black",
        strokeWidth: "2"
      }), /*#__PURE__*/_react.default.createElement("g", {
        clipPath: "url(#clip0_222_51393)"
      }, /*#__PURE__*/_react.default.createElement("path", {
        d: "M148.416 69.1784C147.281 70.2262 147.566 73.0909 149.384 73.5034C154.284 74.618 154.125 68.5235 150.444 68.543C150.03 68.543 148.693 68.9248 148.419 69.1784H148.416Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M136.46 69.3849C133.193 69.0199 129.309 69.6552 125.972 69.3849V94.1866H136.46C137.095 94.1866 139.629 93.568 140.317 93.2921C145.046 91.3916 145.796 83.4912 140.795 81.6436V81.0947C143.074 80.3005 143.262 76.9007 143.033 74.819C142.675 71.5836 139.461 69.7221 136.46 69.3849Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M36.755 69.6631H32.0005V93.9075H36.755V83.8753H48.2218V93.9075H52.9763V69.6631H48.2218V79.6952H37.1745L36.755 79.2772V69.6631Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M82.902 69.6631H75.91L75.9072 73.0099L78.1474 73.0071V93.9075H82.902V69.6631Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M92.4108 69.6631H85.4188L85.416 73.0099L87.6562 73.0071V93.9075H92.4108V69.6631Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M61.0871 86.1052H74.3718C75.0402 85.5311 74.8332 84.3217 74.7549 83.4912C74.1928 77.4051 69.0299 74.1 63.1427 75.4739C55.4012 77.2825 54.33 89.5412 61.0004 93.2949C65.7549 95.9729 73.4097 94.3427 74.2319 88.3318H70.0367C69.9892 88.3318 69.9584 88.8473 69.634 89.1733C66.6331 92.183 61.3472 90.55 61.0871 86.1024V86.1052Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M103.531 75.3088C93.0484 76.8165 93.1882 93.1939 103.768 94.434C119.883 96.3234 119.33 73.0349 103.531 75.3088Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M153.381 75.7939H146.109C146.146 78.1013 145.385 80.3586 148.626 79.138V93.9076H153.381V75.7939Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M170.721 75.7939H157.156C156.488 76.5519 156.477 78.628 157.156 79.4167H165.267L165.55 79.974L157.229 89.7999L157.017 93.9076H171V90.2848H162.051C162.054 89.7163 162.565 89.198 162.895 88.7577C165.217 85.6673 168.19 82.7663 170.575 79.6898L170.721 75.7939Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M136.46 69.3849C139.464 69.7221 142.678 71.5836 143.033 74.819C143.262 76.9007 143.075 80.3005 140.795 81.0947V81.6436C145.793 83.494 145.047 91.3943 140.314 93.2921C139.626 93.568 137.093 94.1866 136.458 94.1866H125.97V69.3849C129.306 69.6552 133.191 69.0199 136.458 69.3849H136.46ZM130.447 79.6957H136.46C137.834 79.6957 138.337 77.2852 138.292 76.204C138.25 75.237 137.568 73.2863 136.46 73.2863H130.447V79.6957ZM130.447 90.2852H137.02C137.82 90.2852 139.162 88.694 139.333 87.8524C140.488 82.1703 134.41 83.9538 131.018 83.4327L130.447 83.7365V90.2852Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M36.755 69.6631V79.2772L37.1745 79.6952H48.2218V69.6631H52.9763V93.9075H48.2218V83.8753H36.755V93.9075H32.0005V69.6631H36.755Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M61.0871 86.1043C61.3472 90.5519 66.6331 92.1849 69.634 89.1753C69.9584 88.852 69.9892 88.3337 70.0367 88.3337H74.2319C73.4097 94.3446 65.7577 95.9721 61.0004 93.2968C54.33 89.5431 55.4012 77.2816 63.1427 75.4758C69.0299 74.102 74.1928 77.407 74.7549 83.4932C74.8304 84.3236 75.0374 85.5331 74.3718 86.1071H61.0871V86.1043ZM70.3164 83.039C69.4886 77.538 62.1638 77.7804 61.0871 83.039H70.3164Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M103.531 75.3088C119.327 73.0349 119.884 96.3262 103.769 94.434C93.1914 93.1939 93.0515 76.8164 103.531 75.3088ZM104.641 79.2046C98.9136 80.1633 99.0842 89.7886 104.599 90.5382C113.32 91.7225 112.825 77.8364 104.641 79.2046Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M170.721 75.7939L170.575 79.6898C168.19 82.7663 165.217 85.67 162.895 88.7577C162.565 89.198 162.054 89.7163 162.051 90.2848H171V93.9076H157.017L157.229 89.7999L165.55 79.974L165.267 79.4167H157.156C156.477 78.628 156.488 76.5519 157.156 75.7939H170.721Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M92.4108 69.6631V93.9075H87.6562V73.0071L85.416 73.0099L85.4188 69.6631H92.4108Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M82.902 69.6631V93.9075H78.1474V73.0071L75.9072 73.0099L75.91 69.6631H82.902Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M153.381 75.7939V93.9076H148.626V79.138C145.385 80.3586 146.146 78.1013 146.109 75.7939H153.381Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M148.416 69.1784C148.693 68.9248 150.027 68.5458 150.441 68.543C154.122 68.5235 154.281 74.618 149.381 73.5034C147.566 73.0909 147.278 70.2234 148.414 69.1784H148.416Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M130.447 90.2851V83.7364L131.018 83.4326C134.408 83.9537 140.488 82.1702 139.333 87.8523C139.162 88.6939 137.82 90.2851 137.02 90.2851H130.447Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M130.447 79.6956V73.2861H136.46C137.568 73.2861 138.25 75.234 138.292 76.2038C138.337 77.2878 137.834 79.6956 136.46 79.6956H130.447Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M70.3163 83.0392H61.0869C62.1637 77.7807 69.4884 77.5383 70.3163 83.0392Z",
        fill: "white"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M104.641 79.2054C112.824 77.8371 113.319 91.7205 104.599 90.5389C99.0839 89.7893 98.9133 80.164 104.641 79.2054Z",
        fill: "white"
      })), /*#__PURE__*/_react.default.createElement("path", {
        fillRule: "evenodd",
        clipRule: "evenodd",
        d: "M32.9307 57.6828C31.7728 55.9499 31.1548 53.9126 31.1548 51.8285C31.1548 49.0338 32.265 46.3535 34.2411 44.3774C36.2173 42.4012 38.8976 41.291 41.6922 41.291C43.7764 41.291 45.8137 41.909 47.5465 43.0669C49.2794 44.2248 50.63 45.8705 51.4276 47.796C52.2251 49.7214 52.4338 51.8402 52.0272 53.8842C51.6206 55.9283 50.6171 57.8059 49.1433 59.2796C47.6697 60.7533 45.7921 61.7569 43.748 62.1635C41.7039 62.5701 39.5852 62.3614 37.6597 61.5638C35.7343 60.7663 34.0885 59.4157 32.9307 57.6828ZM39.0583 47.4375H37.3023V56.219H39.0583V47.4375ZM46.0822 47.4375H40.8143V49.1935H46.0822V47.4375ZM46.0822 50.9495H40.8143V52.7055H46.0822V50.9495ZM46.0822 54.463H40.8143V56.219H46.0822V54.463Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M122.785 107.133V107.591H31.1548V107.133H122.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M122.785 114.877V115.335H31.1548V114.877H122.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M31.1548 130.785C31.1548 127.243 34.0265 124.371 37.5689 124.371H80.1769C83.7193 124.371 86.591 127.243 86.591 130.785C86.591 134.328 83.7193 137.199 80.1769 137.199H37.5689C34.0265 137.199 31.1548 134.328 31.1548 130.785Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M15.4765 10.0258L219.902 9.94184L214.999 228.45L10.5366 228.535L15.4765 10.0258ZM13.4435 8.05454L8.48565 230.578L217.096 230.491L222.051 7.89518L13.4398 7.95826L13.4435 8.05454Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("path", {
        d: "M14.6611 17.926V7.84668L221.745 8.51863V17.926H14.6611Z",
        fill: "black"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "29.0005",
        y: "148.543",
        width: "38",
        height: "38",
        rx: "6",
        fill: "#DCDBFF"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "71.0005",
        y: "148.543",
        width: "38",
        height: "38",
        rx: "6",
        fill: "#DCDBFF"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "114",
        y: "148.543",
        width: "38",
        height: "38",
        rx: "6",
        fill: "#DCDBFF"
      }), /*#__PURE__*/_react.default.createElement("rect", {
        x: "156",
        y: "148.543",
        width: "38",
        height: "38",
        rx: "6",
        fill: "#DCDBFF"
      })),
      className: 'e-onboarding__theme-card-illustration--hello-biz'
    }
  }];
};
function ThemeSelectionContentB(_ref) {
  var actionButton = _ref.actionButton,
    skipButton = _ref.skipButton,
    noticeState = _ref.noticeState,
    selectedTheme = _ref.selectedTheme,
    onThemeSelect = _ref.onThemeSelect,
    onThemeInstallSuccess = _ref.onThemeInstallSuccess,
    onThemeInstallError = _ref.onThemeInstallError;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isInstalling = _useState2[0],
    setIsInstalling = _useState2[1];
  var _useState3 = (0, _react.useState)(null),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    installingTheme = _useState4[0],
    setInstallingTheme = _useState4[1];
  var resetInstallationState = (0, _react.useCallback)(function () {
    setIsInstalling(false);
    setInstallingTheme(null);
  }, []);
  var activateTheme = (0, _react.useCallback)(function (themeSlug) {
    var themeSlugForAPI = 'hello-theme' === themeSlug ? 'hello-elementor' : 'hello-biz';
    var formData = new FormData();
    formData.append('_nonce', elementorCommon.config.ajax.nonce);
    formData.append('action', 'elementor_activate_hello_theme');
    formData.append('theme_slug', themeSlugForAPI);
    fetch(elementorCommon.config.ajax.url, {
      method: 'POST',
      body: formData
    }).then(function (response) {
      return response.json();
    }).then(function (data) {
      resetInstallationState();
      if (data.success && onThemeInstallSuccess) {
        onThemeInstallSuccess();
      } else if (onThemeInstallError) {
        onThemeInstallError();
      }
    }).catch(function () {
      resetInstallationState();
      if (onThemeInstallError) {
        onThemeInstallError();
      }
    });
  }, [onThemeInstallSuccess, onThemeInstallError, resetInstallationState]);
  var installTheme = (0, _react.useCallback)(function (themeSlug) {
    var themeSlugForAPI = 'hello-theme' === themeSlug ? 'hello-elementor' : 'hello-biz';
    wp.updates.ajax('install-theme', {
      slug: themeSlugForAPI,
      success: function success() {
        return activateTheme(themeSlug);
      },
      error: function error() {
        resetInstallationState();
        if (onThemeInstallError) {
          onThemeInstallError();
        }
      }
    });
  }, [activateTheme, onThemeInstallError, resetInstallationState]);
  var startThemeInstallation = (0, _react.useCallback)(function (themeSlug) {
    setIsInstalling(true);
    setInstallingTheme(themeSlug);
    installTheme(themeSlug);
  }, [installTheme]);
  var handleThemeSelect = (0, _react.useCallback)(function (themeSlug) {
    if (onThemeSelect) {
      onThemeSelect(themeSlug);
    }
    startThemeInstallation(themeSlug);
  }, [onThemeSelect, startThemeInstallation]);
  var themeData = getThemeData();
  return /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    title: __('Which theme would you like?', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__page-content-theme-variant-b"
  }, /*#__PURE__*/_react.default.createElement("p", {
    className: "e-onboarding__theme-selection-description"
  }, __('The theme delivers fast setup, intuitive tools, and business-focused widgets.', 'elementor')), /*#__PURE__*/_react.default.createElement("p", {
    className: "e-onboarding__theme-selection-subtitle"
  }, __('You can switch your theme later on', 'elementor')), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__theme-cards"
  }, themeData.map(function (theme) {
    return /*#__PURE__*/_react.default.createElement(_themeSelectionCard.default, {
      key: theme.slug,
      themeSlug: theme.slug,
      title: theme.title,
      description: theme.description,
      illustration: theme.illustration,
      isSelected: selectedTheme === theme.slug,
      isLoading: isInstalling && installingTheme === theme.slug,
      onSelect: handleThemeSelect,
      "aria-label": "Select ".concat(theme.title, " theme: ").concat(theme.description),
      role: "button",
      tabIndex: 0
    });
  }))));
}
ThemeSelectionContentB.propTypes = {
  actionButton: _propTypes.default.object.isRequired,
  skipButton: _propTypes.default.object.isRequired,
  noticeState: _propTypes.default.object,
  selectedTheme: _propTypes.default.string,
  onThemeSelect: _propTypes.default.func,
  onThemeInstallSuccess: _propTypes.default.func,
  onThemeInstallError: _propTypes.default.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/context/context.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/context/context.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ContextProvider = ContextProvider;
exports.OnboardingContext = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var OnboardingContext = exports.OnboardingContext = (0, _react.createContext)({});
function ContextProvider(props) {
  var onboardingConfig = elementorAppConfig.onboarding,
    initialState = {
      // eslint-disable-next-line camelcase
      hasPro: elementorAppConfig.hasPro,
      isLibraryConnected: onboardingConfig.isLibraryConnected,
      isHelloThemeInstalled: onboardingConfig.helloInstalled,
      isHelloThemeActivated: onboardingConfig.helloActivated,
      siteName: onboardingConfig.siteName,
      siteLogo: onboardingConfig.siteLogo,
      proNotice: '',
      currentStep: '',
      nextStep: '',
      steps: {
        account: false,
        hello: false,
        chooseFeatures: false,
        siteName: false,
        siteLogo: false,
        goodToGo: false
      }
    },
    _useState = (0, _react.useState)(initialState),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    state = _useState2[0],
    setState = _useState2[1],
    updateState = (0, _react.useCallback)(function (newState) {
      setState(function (prev) {
        return _objectSpread(_objectSpread({}, prev), newState);
      });
    }, [setState]),
    getStateObjectToUpdate = function getStateObjectToUpdate(stateObject, mainChangedPropertyKey, subChangedPropertyKey, subChangedPropertyValue) {
      var mutatedStateCopy = JSON.parse(JSON.stringify(stateObject));
      mutatedStateCopy[mainChangedPropertyKey][subChangedPropertyKey] = subChangedPropertyValue;
      return mutatedStateCopy;
    };
  return /*#__PURE__*/_react.default.createElement(OnboardingContext.Provider, {
    value: {
      state: state,
      setState: setState,
      updateState: updateState,
      getStateObjectToUpdate: getStateObjectToUpdate
    }
  }, props.children);
}
ContextProvider.propTypes = {
  children: PropTypes.any
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/account.js":
/*!************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/account.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Account;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _connect = _interopRequireDefault(__webpack_require__(/*! ../utils/connect */ "../app/modules/onboarding/assets/js/utils/connect.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _utils = __webpack_require__(/*! ../utils/utils */ "../app/modules/onboarding/assets/js/utils/utils.js");
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Account() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    nextStep = getNextStep(),
    navigate = (0, _router.useNavigate)(),
    pageId = 'account',
    actionButtonRef = (0, _react.useRef)(),
    alreadyHaveAccountLinkRef = (0, _react.useRef)();
  (0, _react.useEffect)(function () {
    if (!state.isLibraryConnected) {
      var _elementorCommon$even;
      (0, _utils.safeDispatchEvent)('view_account_setup', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even = elementorCommon.eventsManager) === null || _elementorCommon$even === void 0 || (_elementorCommon$even = _elementorCommon$even.config) === null || _elementorCommon$even === void 0 || (_elementorCommon$even = _elementorCommon$even.triggers) === null || _elementorCommon$even === void 0 ? void 0 : _elementorCommon$even.pageLoaded) || 'page_loaded',
        step_number: 1,
        step_name: 'account_setup',
        is_library_connected: (state === null || state === void 0 ? void 0 : state.isLibraryConnected) || false
      });
    }
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(1);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip setup', 'elementor'),
      action: function action() {
        var _elementorCommon$even2;
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'skip');
        _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('SKIP', {
          currentStep: 1
        });
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(1);
        (0, _utils.safeDispatchEvent)('skip_setup', {
          location: 'plugin_onboarding',
          trigger: ((_elementorCommon$even2 = elementorCommon.eventsManager) === null || _elementorCommon$even2 === void 0 || (_elementorCommon$even2 = _elementorCommon$even2.config) === null || _elementorCommon$even2 === void 0 || (_elementorCommon$even2 = _elementorCommon$even2.triggers) === null || _elementorCommon$even2 === void 0 ? void 0 : _elementorCommon$even2.click) || 'click',
          step_number: 1,
          step_name: 'account_setup'
        });
        updateState(getStateObjectToUpdate(state, 'steps', pageId, 'skipped'));
        navigate('onboarding/' + nextStep);
      }
    };
  }
  var pageTexts = {};
  if (state.isLibraryConnected) {
    pageTexts = {
      firstLine: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('To get the most out of Elementor, we\'ll help you take your', 'elementor'), " ", /*#__PURE__*/_react.default.createElement("br", null), " ", __('first steps:', 'elementor')),
      listItems: elementorAppConfig.onboarding.experiment ? [__('Set your site\'s theme', 'elementor'), __('Choose additional features', 'elementor'), __('Choose how to start creating', 'elementor')] : [__('Set your site\'s theme', 'elementor'), __('Give your site a name & logo', 'elementor'), __('Choose how to start creating', 'elementor')]
    };
  } else {
    pageTexts = elementorAppConfig.onboarding.experiment ? {
      firstLine: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('To get the most of Elementor, we\'ll connect your account.', 'elementor'), "  ", /*#__PURE__*/_react.default.createElement("br", null), " ", __('Then you can:', 'elementor')),
      listItems: [__('Access dozens of professionally designed templates', 'elementor'), __('Manage all your sites from the My Elementor dashboard', 'elementor'), __('Unlock tools that streamline your workflow and site setup', 'elementor')]
    } : {
      firstLine: __('To get the most out of Elementor, we’ll connect your account.', 'elementor') + ' ' + __('Then you can:', 'elementor'),
      listItems: [__('Choose from countless professional templates', 'elementor'), __('Manage your site with our handy dashboard', 'elementor'), __('Take part in the community forum, share & grow together', 'elementor')]
    };
  }

  // If the user is not connected, the on-click action is handled by the <Connect> component, so there is no onclick
  // property.
  var actionButton = {
    role: 'button'
  };
  if (state.isLibraryConnected) {
    actionButton.text = __('Let’s do it', 'elementor');
    actionButton.onClick = function () {
      elementorCommon.events.dispatchEvent({
        event: 'next',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
      updateState(getStateObjectToUpdate(state, 'steps', pageId, 'completed'));
      navigate('onboarding/' + nextStep);
    };
  } else {
    actionButton.text = __('Start setup', 'elementor');
    actionButton.href = elementorAppConfig.onboarding.urls.signUp + elementorAppConfig.onboarding.utms.connectCta;
    actionButton.ref = actionButtonRef;
    actionButton.onClick = function () {
      var _elementorCommon$even3;
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'create');
      _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('CREATE_MY_ACCOUNT', {
        currentStep: 1,
        createAccountClicked: 'main_cta'
      });
      (0, _utils.safeDispatchEvent)('new_account_connect', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even3 = elementorCommon.eventsManager) === null || _elementorCommon$even3 === void 0 || (_elementorCommon$even3 = _elementorCommon$even3.config) === null || _elementorCommon$even3 === void 0 || (_elementorCommon$even3 = _elementorCommon$even3.triggers) === null || _elementorCommon$even3 === void 0 ? void 0 : _elementorCommon$even3.click) || 'click',
        step_number: 1,
        step_name: 'account_setup',
        button_text: 'Start setup'
      });
    };
  }
  var connectSuccessCallback = function connectSuccessCallback() {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
    stateToUpdate.isLibraryConnected = true;
    updateState(stateToUpdate);
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'success',
        action: 'connect account'
      }
    });
    setNoticeState({
      type: 'success',
      icon: 'eicon-check-circle-o',
      message: 'Alrighty - your account is connected.'
    });
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(1);
    navigate('onboarding/' + nextStep);
  };
  function getNextStep() {
    if (!state.isHelloThemeActivated) {
      return 'hello';
    }
    return elementorAppConfig.onboarding.experiment ? 'chooseFeatures' : 'siteName';
  }
  var connectFailureCallback = function connectFailureCallback() {
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: 'connect account'
      }
    });
    _onboardingEventTracking.OnboardingEventTracking.sendConnectionFailureEvents();
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: __('Oops, the connection failed. Try again.', 'elementor')
    });
    navigate('onboarding/' + nextStep);
  };
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Account.svg',
    title: elementorAppConfig.onboarding.experiment ? __('You\'re here!', 'elementor') : __('You\'re here! Let\'s set things up.', 'elementor'),
    secondLineTitle: elementorAppConfig.onboarding.experiment ? __(' Let\'s get connected.', 'elementor') : '',
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, actionButton.ref && !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: actionButton.ref,
    successCallback: function successCallback(event, data) {
      return connectSuccessCallback(event, data);
    },
    errorCallback: connectFailureCallback
  }), /*#__PURE__*/_react.default.createElement("span", null, pageTexts.firstLine), /*#__PURE__*/_react.default.createElement("ul", null, pageTexts.listItems.map(function (listItem, index) {
    return /*#__PURE__*/_react.default.createElement("li", {
      key: 'listItem' + index
    }, listItem);
  }))), !state.isLibraryConnected && /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__footnote"
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Already have an account?', 'elementor') + ' ', /*#__PURE__*/_react.default.createElement("a", {
    ref: alreadyHaveAccountLinkRef,
    href: elementorAppConfig.onboarding.urls.connect + elementorAppConfig.onboarding.utms.connectCtaLink,
    onClick: function onClick() {
      var _elementorCommon$even4;
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(1, 'connect');
      _onboardingEventTracking.OnboardingEventTracking.sendEventOrStore('STEP1_CLICKED_CONNECT', {
        currentStep: state.currentStep
      });
      (0, _utils.safeDispatchEvent)('existing_account_connect', {
        location: 'plugin_onboarding',
        trigger: ((_elementorCommon$even4 = elementorCommon.eventsManager) === null || _elementorCommon$even4 === void 0 || (_elementorCommon$even4 = _elementorCommon$even4.config) === null || _elementorCommon$even4 === void 0 || (_elementorCommon$even4 = _elementorCommon$even4.triggers) === null || _elementorCommon$even4 === void 0 ? void 0 : _elementorCommon$even4.click) || 'click',
        step_number: 1,
        step_name: 'account_setup',
        button_text: 'Click here to connect'
      });
    }
  }, __('Click here to connect', 'elementor'))), /*#__PURE__*/_react.default.createElement(_connect.default, {
    buttonRef: alreadyHaveAccountLinkRef,
    successCallback: connectSuccessCallback,
    errorCallback: connectFailureCallback
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/choose-features.js":
/*!********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/choose-features.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ChooseFeatures;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _message = _interopRequireDefault(__webpack_require__(/*! ../components/message */ "../app/modules/onboarding/assets/js/components/message.js"));
var _utils = __webpack_require__(/*! ../utils/utils */ "../app/modules/onboarding/assets/js/utils/utils.js");
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
var _useButtonAction2 = _interopRequireDefault(__webpack_require__(/*! ../utils/use-button-action */ "../app/modules/onboarding/assets/js/utils/use-button-action.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ChooseFeatures() {
  var _useAjax = (0, _useAjax2.default)(),
    setAjax = _useAjax.setAjax,
    tiers = {
      advanced: __('Advanced', 'elementor'),
      essential: __('Essential', 'elementor')
    },
    _useState = (0, _react.useState)({
      essential: [],
      advanced: []
    }),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    selectedFeatures = _useState2[0],
    setSelectedFeatures = _useState2[1],
    _useState3 = (0, _react.useState)(tiers.essential),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    tierName = _useState4[0],
    setTierName = _useState4[1],
    pageId = 'chooseFeatures',
    nextStep = 'goodToGo',
    _useButtonAction = (0, _useButtonAction2.default)(pageId, nextStep),
    state = _useButtonAction.state,
    handleAction = _useButtonAction.handleAction,
    actionButton = {
      text: __('Upgrade Now', 'elementor'),
      href: elementorAppConfig.onboarding.urls.upgrade,
      target: '_blank',
      onClick: function onClick() {
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'pro_features_checked', {
          features: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'upgrade_now', {
          pro_features_checked: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });
        _onboardingEventTracking.OnboardingEventTracking.sendUpgradeNowStep3(selectedFeatures, state.currentStep);
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(3);
        setAjax({
          data: {
            action: 'elementor_save_onboarding_features',
            data: JSON.stringify({
              features: selectedFeatures
            })
          }
        });
        handleAction('completed');
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor'),
      action: function action() {
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'pro_features_checked', {
          features: _onboardingEventTracking.OnboardingEventTracking.extractSelectedFeatureKeys(selectedFeatures)
        });
        _onboardingEventTracking.OnboardingEventTracking.trackStepAction(3, 'skipped');
        setAjax({
          data: {
            action: 'elementor_save_onboarding_features',
            data: JSON.stringify({
              features: selectedFeatures
            })
          }
        });
        handleAction('skipped');
      }
    };
  }
  if (!isFeatureSelected(selectedFeatures)) {
    actionButton.className = 'e-onboarding__button--disabled';
  }
  (0, _react.useEffect)(function () {
    if (selectedFeatures.advanced.length > 0) {
      setTierName(tiers.advanced);
    } else {
      setTierName(tiers.essential);
    }
  }, [selectedFeatures, tiers.advanced, tiers.essential]);
  (0, _react.useEffect)(function () {
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(3);
  }, [state.currentStep]);
  function isFeatureSelected(features) {
    return !!features.advanced.length || !!features.essential.length;
  }
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Elevate your website with additional Pro features.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton
  }, /*#__PURE__*/_react.default.createElement("p", null, __('Which Elementor Pro features do you need to bring your creative vision to life?', 'elementor')), /*#__PURE__*/_react.default.createElement("form", {
    className: "e-onboarding__choose-features-section"
  }, _utils.options.map(function (option, index) {
    var itemId = "".concat(option.plan, "-").concat(index);
    return /*#__PURE__*/_react.default.createElement("label", {
      key: itemId,
      className: "e-onboarding__choose-features-section__label",
      htmlFor: itemId
    }, /*#__PURE__*/_react.default.createElement("input", {
      className: "e-onboarding__choose-features-section__checkbox",
      type: "checkbox",
      onChange: function onChange(event) {
        return (0, _utils.setSelectedFeatureList)({
          checked: event.currentTarget.checked,
          id: event.target.value,
          text: option.text,
          selectedFeatures: selectedFeatures,
          setSelectedFeatures: setSelectedFeatures
        });
      },
      id: itemId,
      value: itemId
    }), option.text);
  })), /*#__PURE__*/_react.default.createElement("p", {
    className: "e-onboarding__choose-features-section__message"
  }, isFeatureSelected(selectedFeatures) && /*#__PURE__*/_react.default.createElement(_message.default, {
    tier: tierName
  }))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/good-to-go.js":
/*!***************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/good-to-go.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = GoodToGo;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _goodToGoContentA = _interopRequireDefault(__webpack_require__(/*! ../components/good-to-go-content-a */ "../app/modules/onboarding/assets/js/components/good-to-go-content-a.js"));
var _goodToGoContentB = _interopRequireDefault(__webpack_require__(/*! ../components/good-to-go-content-b */ "../app/modules/onboarding/assets/js/components/good-to-go-content-b.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function GoodToGo() {
  var pageId = 'goodToGo';
  var _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    variant = _useState2[0],
    setVariant = _useState2[1];
  (0, _react.useEffect)(function () {
    _onboardingEventTracking.OnboardingEventTracking.checkAndSendReturnToStep4();
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(4);
    var storedVariant = localStorage.getItem(_onboardingEventTracking.ONBOARDING_STORAGE_KEYS.GOOD_TO_GO_VARIANT);
    setVariant(storedVariant);
  }, []);
  var ContentComponent = 'B' === variant ? _goodToGoContentB.default : _goodToGoContentA.default;
  var skipButton = 'B' === variant ? {
    text: __('Continue with blank canvas', 'elementor'),
    href: elementorAppConfig.onboarding.urls.createNewPage
  } : {
    text: __('Skip', 'elementor'),
    href: elementorAppConfig.onboarding.urls.createNewPage
  };
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId
  }, /*#__PURE__*/_react.default.createElement(ContentComponent, {
    skipButton: skipButton
  }));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/hello-theme.js":
/*!****************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/hello-theme.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = HelloTheme;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _themeSelectionContentA = _interopRequireDefault(__webpack_require__(/*! ../components/theme-selection-content-a */ "../app/modules/onboarding/assets/js/components/theme-selection-content-a.js"));
var _themeSelectionContentB = _interopRequireDefault(__webpack_require__(/*! ../components/theme-selection-content-b */ "../app/modules/onboarding/assets/js/components/theme-selection-content-b.js"));
var _onboardingEventTracking = __webpack_require__(/*! ../utils/onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable @wordpress/i18n-ellipsis */

function HelloTheme() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useAjax = (0, _useAjax2.default)(),
    activateHelloThemeAjaxState = _useAjax.ajaxState,
    setActivateHelloThemeAjaxState = _useAjax.setAjax,
    _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    helloInstalledInOnboarding = _useState2[0],
    setHelloInstalledInOnboarding = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isInstalling = _useState4[0],
    setIsInstalling = _useState4[1],
    _useState5 = (0, _react.useState)(null),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    selectedTheme = _useState6[0],
    setSelectedTheme = _useState6[1],
    noticeStateSuccess = {
      type: 'success',
      icon: 'eicon-check-circle-o',
      message: __('Your site’s got Hello theme. High-five!', 'elementor')
    },
    _useState7 = (0, _react.useState)(state.isHelloThemeActivated ? noticeStateSuccess : null),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    noticeState = _useState8[0],
    setNoticeState = _useState8[1],
    _useState9 = (0, _react.useState)([]),
    _useState0 = (0, _slicedToArray2.default)(_useState9, 2),
    activeTimeouts = _useState0[0],
    setActiveTimeouts = _useState0[1],
    continueWithHelloThemeText = state.isHelloThemeActivated ? __('Next', 'elementor') : __('Continue with Hello Biz Theme', 'elementor'),
    _useState1 = (0, _react.useState)(continueWithHelloThemeText),
    _useState10 = (0, _slicedToArray2.default)(_useState1, 2),
    actionButtonText = _useState10[0],
    setActionButtonText = _useState10[1],
    navigate = (0, _router.useNavigate)(),
    pageId = 'hello',
    nextStep = elementorAppConfig.onboarding.experiment ? 'chooseFeatures' : 'siteName',
    goToNextScreen = function goToNextScreen() {
      return navigate('onboarding/' + nextStep);
    };

  /**
   * Setup
   *
   * If Hello Theme is already activated when onboarding starts, This screen is unneeded and is marked as 'completed'
   * and skipped.
   */
  (0, _react.useEffect)(function () {
    if (!helloInstalledInOnboarding && state.isHelloThemeActivated) {
      var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
      updateState(stateToUpdate);
      goToNextScreen();
    }
    _onboardingEventTracking.OnboardingEventTracking.setupAllUpgradeButtons(state.currentStep);
    _onboardingEventTracking.OnboardingEventTracking.onStepLoad(2);
  }, [getStateObjectToUpdate, goToNextScreen, helloInstalledInOnboarding, pageId, state, updateState]);
  var resetScreenContent = function resetScreenContent() {
    // Clear any active timeouts for changing the action button text during installation.
    activeTimeouts.forEach(function (timeoutID) {
      return clearTimeout(timeoutID);
    });
    setActiveTimeouts([]);
    setIsInstalling(false);
    setActionButtonText(continueWithHelloThemeText);
  };

  /**
   * Callbacks
   */
  var onHelloThemeActivationSuccess = (0, _react.useCallback)(function () {
    setIsInstalling(false);
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'success',
        action: 'hello theme activation'
      }
    });
    setNoticeState(noticeStateSuccess);
    setActionButtonText(__('Next', 'elementor'));
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
    stateToUpdate.isHelloThemeActivated = true;
    updateState(stateToUpdate);
    setHelloInstalledInOnboarding(true);
    _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
    goToNextScreen();
  }, [getStateObjectToUpdate, goToNextScreen, noticeStateSuccess, state, updateState]);
  var onErrorInstallHelloTheme = function onErrorInstallHelloTheme() {
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: 'hello theme install'
      }
    });
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: __('There was a problem installing Hello Biz Theme.', 'elementor')
    });
    resetScreenContent();
  };
  var activateHelloTheme = function activateHelloTheme() {
    setIsInstalling(true);
    updateState({
      isHelloThemeInstalled: true
    });
    var themeSlug = 'hello-theme' === selectedTheme ? 'hello-elementor' : 'hello-biz';
    setActivateHelloThemeAjaxState({
      data: {
        action: 'elementor_activate_hello_theme',
        theme_slug: themeSlug
      }
    });
  };
  var installHelloTheme = function installHelloTheme() {
    if (!isInstalling) {
      setIsInstalling(true);
    }
    var themeSlug = 'hello-theme' === selectedTheme ? 'hello-elementor' : 'hello-biz';
    wp.updates.ajax('install-theme', {
      slug: themeSlug,
      success: function success() {
        return activateHelloTheme();
      },
      error: function error() {
        return onErrorInstallHelloTheme();
      }
    });
  };
  var sendNextButtonEvent = function sendNextButtonEvent() {
    elementorCommon.events.dispatchEvent({
      event: 'next',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
  };
  var handleThemeSelection = function handleThemeSelection(themeSlug) {
    setSelectedTheme(themeSlug);
    var themeValue = 'hello-theme' === themeSlug ? 'hello' : 'hellobiz';
    _onboardingEventTracking.OnboardingEventTracking.trackStepAction(2, "select_theme_".concat(themeSlug.replace('-', '_')), {
      theme: themeValue
    });
    _onboardingEventTracking.OnboardingEventTracking.sendThemeChoiceEvent(state.currentStep, themeValue);
  };

  /**
   * Action Button
   */
  var actionButton = {
    text: actionButtonText,
    role: 'button'
  };
  if (isInstalling) {
    actionButton.className = 'e-onboarding__button--processing';
  }
  if (state.isHelloThemeActivated) {
    actionButton.onClick = function () {
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(2, 'continue_hello_biz');
      sendNextButtonEvent();
      _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
      goToNextScreen();
    };
  } else {
    actionButton.onClick = function () {
      _onboardingEventTracking.OnboardingEventTracking.trackStepAction(2, 'continue_hello_biz');
      _onboardingEventTracking.OnboardingEventTracking.sendHelloBizContinue(state.currentStep);
      sendNextButtonEvent();
      if (state.isHelloThemeInstalled && !state.isHelloThemeActivated) {
        activateHelloTheme();
      } else if (!state.isHelloThemeInstalled) {
        installHelloTheme();
      } else {
        _onboardingEventTracking.OnboardingEventTracking.sendStepEndState(2);
        goToNextScreen();
      }
    };
  }

  /**
   * Skip Button
   */
  var skipButton = {};
  if (isInstalling) {
    skipButton.className = 'e-onboarding__button-skip--disabled';
  }
  if ('completed' !== state.steps[pageId]) {
    skipButton.text = __('Skip', 'elementor');
  }

  /**
   * Set timeouts for updating the 'Next' button text if the Hello Theme installation is taking too long.
   */
  (0, _react.useEffect)(function () {
    if (isInstalling) {
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      })));
    }
    var actionTextTimeouts = [];
    var timeout4 = setTimeout(function () {
      if (!isInstalling) {
        return;
      }
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      }), /*#__PURE__*/_react.default.createElement("span", {
        className: "e-onboarding__action-button-text"
      }, __('Hold on, this can take a minute...', 'elementor'))));
    }, 4000);
    actionTextTimeouts.push(timeout4);
    var timeout30 = setTimeout(function () {
      if (!isInstalling) {
        return;
      }
      setActionButtonText(/*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
        className: "eicon-loading eicon-animation-spin",
        "aria-hidden": "true"
      }), /*#__PURE__*/_react.default.createElement("span", {
        className: "e-onboarding__action-button-text"
      }, __('Okay, now we\'re really close...', 'elementor'))));
    }, 30000);
    actionTextTimeouts.push(timeout30);
    setActiveTimeouts(actionTextTimeouts);
  }, [isInstalling]);
  (0, _react.useEffect)(function () {
    if ('initial' !== activateHelloThemeAjaxState.status) {
      var _activateHelloThemeAj;
      if ('success' === activateHelloThemeAjaxState.status && (_activateHelloThemeAj = activateHelloThemeAjaxState.response) !== null && _activateHelloThemeAj !== void 0 && _activateHelloThemeAj.helloThemeActivated) {
        onHelloThemeActivationSuccess();
      } else if ('error' === activateHelloThemeAjaxState.status) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'hello theme activation'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('There was a problem activating Hello Biz Theme.', 'elementor')
        });

        // Clear any active timeouts for changing the action button text during installation.
        resetScreenContent();
      }
    }
  }, [activateHelloThemeAjaxState.status]);
  var variant = localStorage.getItem(_onboardingEventTracking.ONBOARDING_STORAGE_KEYS.THEME_SELECTION_VARIANT);
  var ContentComponent = 'B' === variant ? _themeSelectionContentB.default : _themeSelectionContentA.default;
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(ContentComponent, {
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState,
    selectedTheme: selectedTheme,
    onThemeSelect: handleThemeSelection,
    onThemeInstallSuccess: onHelloThemeActivationSuccess,
    onThemeInstallError: onErrorInstallHelloTheme
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__footnote"
  }, '* ' + __('You can switch your theme later on', 'elementor')));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/site-logo.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/site-logo.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SiteLogo;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax3 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! elementor-app/organisms/drop-zone */ "../app/assets/js/organisms/drop-zone.js"));
var _unfilteredFilesDialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/organisms/unfiltered-files-dialog */ "../app/assets/js/organisms/unfiltered-files-dialog.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable jsx-a11y/no-static-element-interactions */
/* eslint-disable jsx-a11y/click-events-have-key-events */

function SiteLogo() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useState = (0, _react.useState)(state.siteLogo.id ? state.siteLogo : null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    file = _useState2[0],
    setFile = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isUploading = _useState4[0],
    setIsUploading = _useState4[1],
    _useState5 = (0, _react.useState)(false),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    showUnfilteredFilesDialog = _useState6[0],
    setShowUnfilteredFilesDialog = _useState6[1],
    _useState7 = (0, _react.useState)(),
    _useState8 = (0, _slicedToArray2.default)(_useState7, 2),
    fileSource = _useState8[0],
    setFileSource = _useState8[1],
    _useState9 = (0, _react.useState)(null),
    _useState0 = (0, _slicedToArray2.default)(_useState9, 2),
    noticeState = _useState0[0],
    setNoticeState = _useState0[1],
    _useAjax = (0, _useAjax3.default)(),
    updateLogoAjaxState = _useAjax.ajaxState,
    setUpdateLogoAjax = _useAjax.setAjax,
    _useAjax2 = (0, _useAjax3.default)(),
    uploadImageAjaxState = _useAjax2.ajaxState,
    setUploadImageAjax = _useAjax2.setAjax,
    pageId = 'siteLogo',
    nextStep = 'goodToGo',
    navigate = (0, _router.useNavigate)(),
    actionButton = {
      role: 'button',
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });
        if (file.id) {
          if (file.id !== state.siteLogo.id) {
            updateSiteLogo();
          } else {
            // If the currently displayed logo is already set as the site logo, just go to the next screen.
            var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
            updateState(stateToUpdate);
            navigate('onboarding/' + nextStep);
          }
        }
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor')
    };
  }
  if (isUploading) {
    actionButton.text = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("i", {
      className: "eicon-loading eicon-animation-spin",
      "aria-hidden": "true"
    }));
  } else {
    actionButton.text = __('Next', 'elementor');
  }
  if (!file) {
    actionButton.className = 'e-onboarding__button--disabled';
  }
  var updateSiteLogo = (0, _react.useCallback)(function () {
    setIsUploading(true);
    setUpdateLogoAjax({
      data: {
        action: 'elementor_update_site_logo',
        data: JSON.stringify({
          attachmentId: file.id
        })
      }
    });
  }, [file]);
  var uploadSiteLogo = function uploadSiteLogo(fileToUpload) {
    setIsUploading(true);
    setUploadImageAjax({
      data: {
        action: 'elementor_upload_site_logo',
        fileToUpload: fileToUpload
      }
    });
  };
  var dismissUnfilteredFilesCallback = function dismissUnfilteredFilesCallback() {
    setIsUploading(false);
    setFile(null);
    setShowUnfilteredFilesDialog(false);
  };
  var _onFileSelect = function onFileSelect(selectedFile) {
    setFileSource('drop');
    if ('image/svg+xml' === selectedFile.type && !elementorAppConfig.onboarding.isUnfilteredFilesEnabled) {
      setFile(selectedFile);
      setIsUploading(true);
      setShowUnfilteredFilesDialog(true);
    } else {
      setFile(selectedFile);
      setNoticeState(null);
      uploadSiteLogo(selectedFile);
    }
  };
  var onImageRemoveClick = function onImageRemoveClick() {
    elementorCommon.events.dispatchEvent({
      event: 'remove selected logo',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement
      }
    });
    setFile(null);
  };

  /**
   * Ajax Callbacks
   */
  // Run the callback for the new image upload AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== uploadImageAjaxState.status) {
      var _uploadImageAjaxState;
      if ('success' === uploadImageAjaxState.status && (_uploadImageAjaxState = uploadImageAjaxState.response) !== null && _uploadImageAjaxState !== void 0 && (_uploadImageAjaxState = _uploadImageAjaxState.imageAttachment) !== null && _uploadImageAjaxState !== void 0 && _uploadImageAjaxState.id) {
        elementorCommon.events.dispatchEvent({
          event: 'logo image uploaded',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            source: fileSource
          }
        });
        setIsUploading(false);
        setFile(uploadImageAjaxState.response.imageAttachment);
        if (noticeState) {
          setNoticeState(null);
        }
      } else if ('error' === uploadImageAjaxState.status) {
        setIsUploading(false);
        setFile(null);
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            action_state: 'failure',
            action: 'logo image upload'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: 'That didn\'t work. Try uploading your file again.'
        });
      }
    }
  }, [uploadImageAjaxState.status]);

  // Run the callback for the site logo update AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== updateLogoAjaxState.status) {
      var _updateLogoAjaxState$;
      if ('success' === updateLogoAjaxState.status && (_updateLogoAjaxState$ = updateLogoAjaxState.response) !== null && _updateLogoAjaxState$ !== void 0 && _updateLogoAjaxState$.siteLogoUpdated) {
        elementorCommon.events.dispatchEvent({
          event: 'logo image updated',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            source: fileSource
          }
        });
        setIsUploading(false);
        if (noticeState) {
          setNoticeState(null);
        }
        var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
        stateToUpdate.siteLogo = {
          id: file.id,
          url: file.url
        };
        updateState(stateToUpdate);
        navigate('onboarding/' + nextStep);
      } else if ('error' === updateLogoAjaxState.status) {
        setIsUploading(false);
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'update site logo'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: 'That didn\'t work. Try uploading your file again.'
        });
      }
    }
  }, [updateLogoAjaxState.status]);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Have a logo? Add it here.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("span", null, __('Otherwise, you can skip this and add one later.', 'elementor')), file && !showUnfilteredFilesDialog ? /*#__PURE__*/_react.default.createElement("div", {
    className: 'e-onboarding__logo-container' + (isUploading ? ' e-onboarding__is-uploading' : '')
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__logo-remove",
    onClick: function onClick() {
      return onImageRemoveClick();
    }
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-trash-o"
  })), /*#__PURE__*/_react.default.createElement("img", {
    src: file.url,
    alt: __('Potential Site Logo', 'elementor')
  })) : /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_dropZone.default, {
    className: "e-onboarding__drop-zone",
    heading: __('Drop image here', 'elementor'),
    secondaryText: __('or', 'elementor'),
    buttonText: __('Open Media Library', 'elementor'),
    buttonVariant: "outlined",
    buttonColor: "cta",
    icon: '',
    type: "wp-media",
    filetypes: ['jpg', 'jpeg', 'png', 'svg'],
    onFileSelect: function onFileSelect(selectedFile) {
      return _onFileSelect(selectedFile);
    },
    onWpMediaSelect: function onWpMediaSelect(frame) {
      // Get media attachment details from the frame state
      var attachment = frame.state().get('selection').first().toJSON();
      setFileSource('browse');
      setFile(attachment);
      setNoticeState(null);
    },
    onButtonClick: function onButtonClick() {
      elementorCommon.events.dispatchEvent({
        event: 'browse file click',
        version: '',
        details: {
          placement: elementorAppConfig.onboarding.eventPlacement,
          step: state.currentStep
        }
      });
    }
    // TODO: DEAL WITH ERROR
    ,
    onError: function onError(error) {
      if ('file_not_allowed' === error.id) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'logo upload format'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('This file type is not supported. Try a different type of file', 'elementor')
        });
      }
    }
  })), /*#__PURE__*/_react.default.createElement(_unfilteredFilesDialog.default, {
    show: showUnfilteredFilesDialog,
    setShow: setShowUnfilteredFilesDialog,
    confirmModalText: __('This allows Elementor to scan your SVGs for malicious content. If you do not wish to allow this, use a different image format.', 'elementor'),
    errorModalText: __('There was a problem with enabling SVG uploads. Try again, or use another image format.', 'elementor'),
    onReady: function onReady() {
      setShowUnfilteredFilesDialog(false);
      elementorAppConfig.onboarding.isUnfilteredFilesEnabled = true;
      uploadSiteLogo(file);
    },
    onDismiss: function onDismiss() {
      return dismissUnfilteredFilesCallback();
    },
    onCancel: function onCancel() {
      return dismissUnfilteredFilesCallback();
    }
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/site-name.js":
/*!**************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/site-name.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SiteName;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/layout */ "../app/modules/onboarding/assets/js/components/layout/layout.js"));
var _pageContentLayout = _interopRequireDefault(__webpack_require__(/*! ../components/layout/page-content-layout */ "../app/modules/onboarding/assets/js/components/layout/page-content-layout.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SiteName() {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate,
    _useAjax = (0, _useAjax2.default)(),
    ajaxState = _useAjax.ajaxState,
    setAjax = _useAjax.setAjax,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    _useState3 = (0, _react.useState)(state.siteName),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    siteNameInputValue = _useState4[0],
    setSiteNameInputValue = _useState4[1],
    pageId = 'siteName',
    nextStep = 'siteLogo',
    navigate = (0, _router.useNavigate)(),
    nameInputRef = (0, _react.useRef)(),
    actionButton = {
      text: __('Next', 'elementor'),
      onClick: function onClick() {
        elementorCommon.events.dispatchEvent({
          event: 'next',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep
          }
        });

        // Only run the site name update AJAX if the new name is different than the existing one and it isn't empty.
        if (nameInputRef.current.value !== state.siteName && '' !== nameInputRef.current.value) {
          setAjax({
            data: {
              action: 'elementor_update_site_name',
              data: JSON.stringify({
                siteName: nameInputRef.current.value
              })
            }
          });
        } else if (nameInputRef.current.value === state.siteName) {
          var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
          updateState(stateToUpdate);
          navigate('onboarding/' + nextStep);
        } else {
          var _stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'skipped');
          updateState(_stateToUpdate);
          navigate('onboarding/' + nextStep);
        }
      }
    };
  var skipButton;
  if ('completed' !== state.steps[pageId]) {
    skipButton = {
      text: __('Skip', 'elementor')
    };
  }
  if (!siteNameInputValue) {
    actionButton.className = 'e-onboarding__button--disabled';
  }

  // Run the callback for the site name update AJAX request.
  (0, _react.useEffect)(function () {
    if ('initial' !== ajaxState.status) {
      var _ajaxState$response;
      if ('success' === ajaxState.status && (_ajaxState$response = ajaxState.response) !== null && _ajaxState$response !== void 0 && _ajaxState$response.siteNameUpdated) {
        var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, 'completed');
        stateToUpdate.siteName = nameInputRef.current.value;
        updateState(stateToUpdate);
        navigate('onboarding/' + nextStep);
      } else if ('error' === ajaxState.status) {
        elementorCommon.events.dispatchEvent({
          event: 'indication prompt',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            action_state: 'failure',
            action: 'site name update'
          }
        });
        setNoticeState({
          type: 'error',
          icon: 'eicon-warning',
          message: __('Sorry, the name wasn\'t saved. Try again, or skip for now.', 'elementor')
        });
      }
    }
  }, [ajaxState.status]);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    pageId: pageId,
    nextStep: nextStep
  }, /*#__PURE__*/_react.default.createElement(_pageContentLayout.default, {
    image: elementorCommon.config.urls.assets + 'images/app/onboarding/Illustration_Setup.svg',
    title: __('Now, let\'s give your site a name.', 'elementor'),
    actionButton: actionButton,
    skipButton: skipButton,
    noticeState: noticeState
  }, /*#__PURE__*/_react.default.createElement("p", null, __('This is what your site is called on the WP dashboard, and can be changed later from the general settings - it\'s not your website\'s URL.', 'elementor')), /*#__PURE__*/_react.default.createElement("input", {
    className: "e-onboarding__text-input e-onboarding__site-name-input",
    type: "text",
    placeholder: "e.g. Eric's Space Shuttles",
    defaultValue: state.siteName || '',
    ref: nameInputRef,
    onChange: function onChange(event) {
      return setSiteNameInputValue(event.target.value);
    }
  })));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js":
/*!***************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/pages/upload-and-install-pro.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = UploadAndInstallPro;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useAjax2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-ajax */ "../app/assets/js/hooks/use-ajax.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _dropZone = _interopRequireDefault(__webpack_require__(/*! ../../../../../assets/js/organisms/drop-zone */ "../app/assets/js/organisms/drop-zone.js"));
var _notice = _interopRequireDefault(__webpack_require__(/*! ../components/notice */ "../app/modules/onboarding/assets/js/components/notice.js"));
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _elementorLoading = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/elementor-loading */ "../app/assets/js/molecules/elementor-loading.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function UploadAndInstallPro() {
  (0, _usePageTitle.default)({
    title: __('Upload and Install Elementor Pro', 'elementor')
  });
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    _useAjax = (0, _useAjax2.default)(),
    installProZipAjaxState = _useAjax.ajaxState,
    setInstallProZipAjaxState = _useAjax.setAjax,
    _useState = (0, _react.useState)(null),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    noticeState = _useState2[0],
    setNoticeState = _useState2[1],
    _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isLoading = _useState4[0],
    setIsLoading = _useState4[1],
    _useState5 = (0, _react.useState)(),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    fileSource = _useState6[0],
    setFileSource = _useState6[1];
  var uploadProZip = (0, _react.useCallback)(function (file) {
    setIsLoading(true);
    setInstallProZipAjaxState({
      data: {
        action: 'elementor_upload_and_install_pro',
        fileToUpload: file
      }
    });
  }, []);
  var setErrorNotice = function setErrorNotice() {
    var error = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var step = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'upload';
    var errorMessage = (error === null || error === void 0 ? void 0 : error.message) || 'That didn\'t work. Try uploading your file again.';
    elementorCommon.events.dispatchEvent({
      event: 'indication prompt',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep,
        action_state: 'failure',
        action: step + ' pro',
        source: fileSource
      }
    });
    setNoticeState({
      type: 'error',
      icon: 'eicon-warning',
      message: errorMessage
    });
  };

  /**
   * Ajax Callbacks
   */
  // Run the callback that runs when the Pro Upload Ajax returns a response.
  (0, _react.useEffect)(function () {
    if ('initial' !== installProZipAjaxState.status) {
      var _installProZipAjaxSta;
      setIsLoading(false);
      if ('success' === installProZipAjaxState.status && (_installProZipAjaxSta = installProZipAjaxState.response) !== null && _installProZipAjaxSta !== void 0 && _installProZipAjaxSta.elementorProInstalled) {
        elementorCommon.events.dispatchEvent({
          event: 'pro uploaded',
          version: '',
          details: {
            placement: elementorAppConfig.onboarding.eventPlacement,
            step: state.currentStep,
            source: fileSource
          }
        });
        if (opener && opener !== window) {
          opener.jQuery('body').trigger('elementor/upload-and-install-pro/success');
          window.close();
          opener.focus();
        }
      } else if ('error' === installProZipAjaxState.status) {
        setErrorNotice('install');
      }
    }
  }, [installProZipAjaxState.status]);
  var onProUploadHelpLinkClick = function onProUploadHelpLinkClick() {
    elementorCommon.events.dispatchEvent({
      event: 'pro plugin upload help',
      version: '',
      details: {
        placement: elementorAppConfig.onboarding.eventPlacement,
        step: state.currentStep
      }
    });
  };
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_elementorLoading.default, {
      loadingText: __('Uploading', 'elementor')
    });
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app e-onboarding__upload-pro"
  }, /*#__PURE__*/_react.default.createElement(_content.default, null, /*#__PURE__*/_react.default.createElement(_dropZone.default, {
    className: "e-onboarding__upload-pro-drop-zone",
    onFileSelect: function onFileSelect(file, event, source) {
      setFileSource(source);
      uploadProZip(file);
    },
    onError: function onError(error) {
      return setErrorNotice(error, 'upload');
    },
    filetypes: ['zip'],
    buttonColor: "cta",
    buttonVariant: "contained",
    heading: __('Import your Elementor Pro plugin file', 'elementor'),
    text: __('Drag & Drop your .zip file here', 'elementor'),
    secondaryText: __('or', 'elementor'),
    buttonText: __('Browse', 'elementor')
  }), noticeState && /*#__PURE__*/_react.default.createElement(_notice.default, {
    noticeState: noticeState
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-onboarding__upload-pro-get-file"
  }, __('Don\'t know where to get the file from?', 'elementor') + ' ', /*#__PURE__*/_react.default.createElement("a", {
    onClick: function onClick() {
      return onProUploadHelpLinkClick();
    },
    href: 'https://my.elementor.com/subscriptions/' + elementorAppConfig.onboarding.utms.downloadPro,
    target: "_blank"
  }, __('Click here', 'elementor')))));
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/connect.js":
/*!************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/connect.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Connect;
var _react = __webpack_require__(/*! react */ "react");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _onboardingEventTracking = __webpack_require__(/*! ./onboarding-event-tracking */ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js");
function Connect(props) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate;
  var buttonRef = props.buttonRef,
    successCallback = props.successCallback,
    errorCallback = props.errorCallback;
  var handleCoreConnectionLogic = (0, _react.useCallback)(function (event, data) {
    var isTrackingOptedInConnect = data.tracking_opted_in && elementorCommon.config.editor_events;
    _onboardingEventTracking.OnboardingEventTracking.updateLibraryConnectConfig(data);
    if (isTrackingOptedInConnect) {
      elementorCommon.config.editor_events.can_send_events = true;
      _onboardingEventTracking.OnboardingEventTracking.sendConnectionSuccessEvents(data);
    }
  }, []);
  var defaultConnectSuccessCallback = (0, _react.useCallback)(function () {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', 'account', 'completed');
    stateToUpdate.isLibraryConnected = true;
    updateState(stateToUpdate);
  }, [state, getStateObjectToUpdate, updateState]);
  (0, _react.useEffect)(function () {
    jQuery(buttonRef.current).elementorConnect({
      success: function success(event, data) {
        handleCoreConnectionLogic(event, data);
        if (successCallback) {
          successCallback(event, data);
        } else {
          defaultConnectSuccessCallback();
        }
      },
      error: function error() {
        if (errorCallback) {
          errorCallback();
        }
      },
      popup: {
        width: 726,
        height: 534
      }
    });
  }, [buttonRef, successCallback, errorCallback, handleCoreConnectionLogic, defaultConnectSuccessCallback]);
  return null;
}
Connect.propTypes = {
  buttonRef: PropTypes.object.isRequired,
  successCallback: PropTypes.func,
  errorCallback: PropTypes.func
};

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "ONBOARDING_STEP_NAMES", ({
  enumerable: true,
  get: function get() {
    return _eventDispatcher.ONBOARDING_STEP_NAMES;
  }
}));
Object.defineProperty(exports, "ONBOARDING_STORAGE_KEYS", ({
  enumerable: true,
  get: function get() {
    return _storageManager.ONBOARDING_STORAGE_KEYS;
  }
}));
exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
var _storageManager = _interopRequireWildcard(__webpack_require__(/*! ./storage-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/storage-manager.js"));
var _eventDispatcher = _interopRequireWildcard(__webpack_require__(/*! ./event-dispatcher.js */ "../app/modules/onboarding/assets/js/utils/modules/event-dispatcher.js"));
var _timingManager = _interopRequireDefault(__webpack_require__(/*! ./timing-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/timing-manager.js"));
var _postOnboardingTracker = _interopRequireDefault(__webpack_require__(/*! ./post-onboarding-tracker.js */ "../app/modules/onboarding/assets/js/utils/modules/post-onboarding-tracker.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var OnboardingTracker = /*#__PURE__*/function () {
  function OnboardingTracker() {
    (0, _classCallCheck2.default)(this, OnboardingTracker);
    this.initializeEventConfigs();
    this.initializeEventListeners();
  }
  return (0, _createClass2.default)(OnboardingTracker, [{
    key: "initializeEventConfigs",
    value: function initializeEventConfigs() {
      this.EVENT_CONFIGS = {
        SKIP: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.SKIP,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_SKIP,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'skip_clicked'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              skip_timestamp: eventData.timestamp
            };
          }
        },
        TOP_UPGRADE: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.TOP_UPGRADE,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE,
          isArray: true,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'upgrade_interaction'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              upgrade_clicked: eventData.upgradeClicked
            };
          }
        },
        CREATE_MY_ACCOUNT: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CREATE_MY_ACCOUNT,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'upgrade_interaction'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep,
              create_account_clicked: eventData.createAccountClicked
            };
          }
        },
        CREATE_ACCOUNT_STATUS: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CREATE_ACCOUNT_STATUS,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_ACCOUNT_STATUS,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'create_flow_returns_status'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              onboarding_create_account_status: eventData.status
            };
          }
        },
        CONNECT_STATUS: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.CONNECT_STATUS,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CONNECT_STATUS,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'connect_flow_returns_status'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              onboarding_connect_status: eventData.status,
              tracking_opted_in: eventData.trackingOptedIn,
              user_tier: eventData.userTier
            };
          },
          stepOverride: 1,
          stepNameOverride: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT
        },
        STEP1_CLICKED_CONNECT: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_CLICKED_CONNECT,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: _eventsConfig.default.triggers.click
          },
          payloadBuilder: function payloadBuilder() {
            return {};
          },
          stepOverride: 1,
          stepNameOverride: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT
        },
        STEP1_END_STATE: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_END_STATE,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_END_STATE,
          isRawPayload: true,
          payloadBuilder: function payloadBuilder(eventData) {
            return eventData;
          }
        },
        EXIT_BUTTON: {
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.EXIT_BUTTON,
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT_BUTTON,
          basePayload: {
            location: 'plugin_onboarding',
            trigger: 'exit_button_clicked'
          },
          payloadBuilder: function payloadBuilder(eventData) {
            return {
              action_step: eventData.currentStep
            };
          }
        }
      };
    }
  }, {
    key: "initializeEventListeners",
    value: function initializeEventListeners() {
      var _this = this;
      if ('undefined' === typeof document) {
        return;
      }
      document.addEventListener('click', function (event) {
        var cardGridElement = event.target.closest('.e-onboarding__cards-grid');
        if (cardGridElement) {
          _this.handleStep4CardClick(event);
        }
      }, true);
      this.setupUrlChangeDetection();
    }
  }, {
    key: "setupUrlChangeDetection",
    value: function setupUrlChangeDetection() {
      var _this2 = this;
      var lastUrl = window.location.href;
      var urlChangeDetector = function urlChangeDetector() {
        var currentUrl = window.location.href;
        if (currentUrl !== lastUrl) {
          var isStep4 = currentUrl.includes('goodToGo') || currentUrl.includes('step4') || currentUrl.includes('site_starter');
          if (isStep4) {
            setTimeout(function () {
              _timingManager.default.trackStepStartTime(4);
              _this2.checkAndSendReturnToStep4();
            }, 100);
          }
          lastUrl = currentUrl;
        }
      };
      setInterval(urlChangeDetector, 500);
      window.addEventListener('popstate', function () {
        setTimeout(urlChangeDetector, 100);
      });
    }
  }, {
    key: "dispatchEvent",
    value: function dispatchEvent(eventName, payload) {
      return _eventDispatcher.default.dispatch(eventName, payload);
    }
  }, {
    key: "sendEventOrStore",
    value: function sendEventOrStore(eventType, eventData) {
      if ('TOP_UPGRADE' === eventType && 'no_click' !== eventData.upgradeClicked) {
        var stepNumber = this.getStepNumber(eventData.currentStep);
        this.markUpgradeClickSent(stepNumber);
      }
      if (_eventDispatcher.default.canSendEvents()) {
        return this.sendEventDirect(eventType, eventData);
      }
      this.storeEventForLater(eventType, eventData);
    }
  }, {
    key: "sendEventDirect",
    value: function sendEventDirect(eventType, eventData) {
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      if (config.isRawPayload) {
        return this.dispatchEvent(config.eventName, eventData);
      }
      var stepNumber = config.stepOverride || this.getStepNumber(eventData.currentStep);
      var stepName = config.stepNameOverride || this.getStepName(stepNumber);
      var eventPayload = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, _objectSpread(_objectSpread({}, config.basePayload), config.payloadBuilder(eventData)));
      return this.dispatchEvent(config.eventName, eventPayload);
    }
  }, {
    key: "storeEventForLater",
    value: function storeEventForLater(eventType, eventData) {
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      var dataWithTimestamp = _objectSpread(_objectSpread({}, eventData), {}, {
        timestamp: _timingManager.default.getCurrentTime()
      });
      if (config.isArray) {
        var existingEvents = _storageManager.default.getArray(config.storageKey);
        existingEvents.push(dataWithTimestamp);
        _storageManager.default.setObject(config.storageKey, existingEvents);
      } else {
        _storageManager.default.setObject(config.storageKey, dataWithTimestamp);
      }
    }
  }, {
    key: "sendStoredEvent",
    value: function sendStoredEvent(eventType) {
      var _this3 = this;
      var config = this.EVENT_CONFIGS[eventType];
      if (!config) {
        return;
      }
      var storedData = config.isArray ? _storageManager.default.getArray(config.storageKey) : _storageManager.default.getObject(config.storageKey);
      if (!storedData || config.isArray && 0 === storedData.length) {
        return;
      }
      var processEvent = function processEvent(eventData) {
        if (config.isRawPayload) {
          _this3.dispatchEvent(config.eventName, eventData);
          return;
        }
        var stepNumber = config.stepOverride || _this3.getStepNumber(eventData.currentStep);
        var stepName = config.stepNameOverride || _this3.getStepName(stepNumber);
        var eventPayload = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, _objectSpread(_objectSpread({}, config.basePayload), config.payloadBuilder(eventData)));
        _this3.dispatchEvent(config.eventName, eventPayload);
      };
      if (config.isArray) {
        storedData.forEach(processEvent);
      } else {
        processEvent(storedData);
      }
      _storageManager.default.remove(config.storageKey);
    }
  }, {
    key: "updateLibraryConnectConfig",
    value: function updateLibraryConnectConfig(data) {
      if (!elementorCommon.config.library_connect) {
        return;
      }
      elementorCommon.config.library_connect.is_connected = true;
      elementorCommon.config.library_connect.current_access_level = data.kits_access_level || data.access_level || 0;
      elementorCommon.config.library_connect.current_access_tier = data.access_tier;
      elementorCommon.config.library_connect.plan_type = data.plan_type;
      elementorCommon.config.library_connect.user_id = data.user_id || null;
    }
  }, {
    key: "sendUpgradeNowStep3",
    value: function sendUpgradeNowStep3(selectedFeatures, currentStep) {
      var proFeaturesChecked = this.extractSelectedFeatureKeys(selectedFeatures);
      return _eventDispatcher.default.dispatchStepEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.UPGRADE_NOW_S3, currentStep, _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES, {
        location: 'plugin_onboarding',
        trigger: _eventsConfig.default.triggers.click,
        pro_features_checked: proFeaturesChecked
      });
    }
  }, {
    key: "extractSelectedFeatureKeys",
    value: function extractSelectedFeatureKeys(selectedFeatures) {
      if (!selectedFeatures || !Array.isArray(selectedFeatures)) {
        return [];
      }
      return selectedFeatures.filter(function (feature) {
        return feature && feature.is_checked;
      }).map(function (feature) {
        return feature.key;
      }).filter(function (key) {
        return key;
      });
    }
  }, {
    key: "sendHelloBizContinue",
    value: function sendHelloBizContinue(stepNumber) {
      if (_eventDispatcher.default.canSendEvents()) {
        return _eventDispatcher.default.dispatchStepEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.HELLO_BIZ_CONTINUE, stepNumber, _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ, {
          location: 'plugin_onboarding',
          trigger: _eventsConfig.default.triggers.click
        });
      }
    }
  }, {
    key: "sendThemeChoiceEvent",
    value: function sendThemeChoiceEvent(currentStep, themeValue) {
      if (_eventDispatcher.default.canSendEvents()) {
        return _eventDispatcher.default.dispatchStepEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.THEME_CHOICE, 2, _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ, {
          location: 'plugin_onboarding',
          trigger: 'theme_selected',
          theme: themeValue
        });
      }
    }
  }, {
    key: "sendTopUpgrade",
    value: function sendTopUpgrade(currentStep, upgradeClicked) {
      return this.sendEventOrStore('TOP_UPGRADE', {
        currentStep: currentStep,
        upgradeClicked: upgradeClicked
      });
    }
  }, {
    key: "cancelDelayedNoClickEvent",
    value: function cancelDelayedNoClickEvent() {
      _storageManager.default.remove(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_NO_CLICK);
    }
  }, {
    key: "initiateCoreOnboarding",
    value: function initiateCoreOnboarding() {
      _storageManager.default.clearAllOnboardingData();
      _timingManager.default.clearStaleSessionData();
      _timingManager.default.initializeOnboardingStartTime();
    }
  }, {
    key: "sendCoreOnboardingInitiated",
    value: function sendCoreOnboardingInitiated() {
      var startTime = _timingManager.default.initializeOnboardingStartTime();
      var currentTime = _timingManager.default.getCurrentTime();
      var totalOnboardingTime = Math.round((currentTime - startTime) / 1000);
      var eventData = _timingManager.default.addTimingToEventData({
        location: 'plugin_onboarding',
        trigger: 'core_onboarding_initiated',
        step_number: 1,
        step_name: _eventDispatcher.ONBOARDING_STEP_NAMES.ONBOARDING_START,
        onboarding_start_time: startTime,
        total_onboarding_time_seconds: totalOnboardingTime
      });
      this.dispatchEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.CORE_ONBOARDING, eventData);
      _storageManager.default.remove(_storageManager.ONBOARDING_STORAGE_KEYS.INITIATED);
    }
  }, {
    key: "storeSiteStarterChoice",
    value: function storeSiteStarterChoice(siteStarter) {
      var choiceData = {
        site_starter: siteStarter,
        timestamp: _timingManager.default.getCurrentTime(),
        return_event_sent: false
      };
      _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, choiceData);
    }
  }, {
    key: "checkAndSendReturnToStep4",
    value: function checkAndSendReturnToStep4() {
      var choiceData = _storageManager.default.getObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE);
      if (!choiceData) {
        return;
      }
      if (!choiceData.return_event_sent) {
        var returnEventPayload = _eventDispatcher.default.createStepEventPayload(4, _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER, {
          location: 'plugin_onboarding',
          trigger: 'user_returns_to_onboarding',
          return_to_onboarding: choiceData.site_starter,
          original_choice_timestamp: choiceData.timestamp
        });
        this.dispatchEvent(_eventDispatcher.ONBOARDING_EVENTS_MAP.STEP4_RETURN_STEP4, returnEventPayload);
        choiceData.return_event_sent = true;
        _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, choiceData);
      }
    }
  }, {
    key: "handleSiteStarterChoice",
    value: function handleSiteStarterChoice(siteStarter) {
      _timingManager.default.trackStepStartTime(4);
      this.storeSiteStarterChoice(siteStarter);
      this.trackStepAction(4, 'site_starter', {
        source_type: siteStarter
      });
      this.sendStepEndState(4);
    }
  }, {
    key: "storeExitEventForLater",
    value: function storeExitEventForLater(exitType, currentStep) {
      var exitData = {
        exitType: exitType,
        currentStep: currentStep,
        timestamp: _timingManager.default.getCurrentTime()
      };
      _storageManager.default.setObject(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT, exitData);
    }
  }, {
    key: "checkAndSendEditorLoadedFromOnboarding",
    value: function checkAndSendEditorLoadedFromOnboarding() {
      return _postOnboardingTracker.default.checkAndSendEditorLoadedFromOnboarding();
    }
  }, {
    key: "sendExitButtonEvent",
    value: function sendExitButtonEvent(currentStep) {
      var stepNumber = this.getStepNumber(currentStep);
      this.trackStepAction(stepNumber, 'exit_button');
      this.sendStepEndState(stepNumber);
      return this.sendEventOrStore('EXIT_BUTTON', {
        currentStep: currentStep
      });
    }
  }, {
    key: "trackStepAction",
    value: function trackStepAction(stepNumber, action) {
      var additionalData = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
      var stepConfig = this.getStepConfig(stepNumber);
      if (stepConfig) {
        this.trackStepActionInternal(stepNumber, action, stepConfig.storageKey, additionalData);
      }
    }
  }, {
    key: "sendStepEndState",
    value: function sendStepEndState(stepNumber) {
      var stepConfig = this.getStepConfig(stepNumber);
      if (stepConfig) {
        this.sendStepEndStateInternal(stepNumber, stepConfig.storageKey, stepConfig.eventName, stepConfig.stepName, stepConfig.endStateProperty);
      }
    }
  }, {
    key: "trackStepActionInternal",
    value: function trackStepActionInternal(stepNumber, action, storageKey) {
      var additionalData = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
      // Always store the action, regardless of global timing availability
      var existingActions = _storageManager.default.getArray(storageKey);
      var actionData = _objectSpread({
        action: action,
        timestamp: _timingManager.default.getCurrentTime()
      }, additionalData);
      existingActions.push(actionData);
      _storageManager.default.setObject(storageKey, existingActions);
    }
  }, {
    key: "sendStepEndStateInternal",
    value: function sendStepEndStateInternal(stepNumber, storageKey, eventName, stepName, endStateProperty) {
      var actions = _storageManager.default.getArray(storageKey);
      if (0 === actions.length) {
        return;
      }
      var eventData = _eventDispatcher.default.createStepEventPayload(stepNumber, stepName, {
        location: 'plugin_onboarding',
        trigger: 'user_redirects_out_of_step'
      });
      eventData = _timingManager.default.addTimingToEventData(eventData, stepNumber);
      var filteredActions = actions.filter(function (action) {
        return 'upgrade_hover' !== action.action && 'upgrade_topbar' !== action.action && 'upgrade_now' !== action.action && 'upgrade_already_pro' !== action.action;
      });
      eventData[endStateProperty] = filteredActions;
      if (_eventDispatcher.default.canSendEvents()) {
        this.sendHoverEventsFromStepActions(actions, stepNumber);
        this.dispatchEvent(eventName, eventData);
        _storageManager.default.remove(storageKey);
        _timingManager.default.clearStepStartTime(stepNumber);
      } else if (1 === stepNumber) {
        this.storeStep1EndStateForLater(eventData, storageKey);
      } else {
        this.sendHoverEventsFromStepActions(actions, stepNumber);
        this.dispatchEvent(eventName, eventData);
        _storageManager.default.remove(storageKey);
        _timingManager.default.clearStepStartTime(stepNumber);
      }
    }
  }, {
    key: "getStepNumber",
    value: function getStepNumber(pageId) {
      if (this.isNumericPageId(pageId)) {
        return pageId;
      }
      if (this.isStringNumericPageId(pageId)) {
        return this.convertStringToNumber(pageId);
      }
      return this.mapPageIdToStepNumber(pageId);
    }
  }, {
    key: "isNumericPageId",
    value: function isNumericPageId(pageId) {
      return 'number' === typeof pageId;
    }
  }, {
    key: "isStringNumericPageId",
    value: function isStringNumericPageId(pageId) {
      return 'string' === typeof pageId && !isNaN(pageId);
    }
  }, {
    key: "convertStringToNumber",
    value: function convertStringToNumber(pageId) {
      return parseInt(pageId, 10);
    }
  }, {
    key: "mapPageIdToStepNumber",
    value: function mapPageIdToStepNumber(pageId) {
      var stepMappings = this.getStepMappings();
      var mappedStep = stepMappings[pageId];
      return mappedStep || null;
    }
  }, {
    key: "getStepMappings",
    value: function getStepMappings() {
      return {
        account: 1,
        connect: 1,
        hello: 2,
        hello_biz: 2,
        chooseFeatures: 3,
        pro_features: 3,
        site_starter: 4,
        goodToGo: 4,
        siteName: 5,
        siteLogo: 6
      };
    }
  }, {
    key: "getStepName",
    value: function getStepName(stepNumber) {
      var stepNames = {
        1: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT,
        2: _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ,
        3: _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES,
        4: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER,
        5: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_NAME,
        6: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_LOGO
      };
      return stepNames[stepNumber] || 'unknown';
    }
  }, {
    key: "getStepConfig",
    value: function getStepConfig(stepNumber) {
      var stepConfigs = {
        1: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP1_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.CONNECT,
          endStateProperty: 'step1_actions'
        },
        2: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP2_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.HELLO_BIZ,
          endStateProperty: 'step2_actions'
        },
        3: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP3_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.PRO_FEATURES,
          endStateProperty: 'step3_actions'
        },
        4: {
          storageKey: _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_ACTIONS,
          eventName: _eventDispatcher.ONBOARDING_EVENTS_MAP.STEP4_END_STATE,
          stepName: _eventDispatcher.ONBOARDING_STEP_NAMES.SITE_STARTER,
          endStateProperty: 'step4_actions'
        }
      };
      return stepConfigs[stepNumber] || null;
    }
  }, {
    key: "sendConnectionSuccessEvents",
    value: function sendConnectionSuccessEvents(data) {
      this.sendCoreOnboardingInitiated();
      this.sendAppropriateStatusEvent('success', data);
      this.sendAllStoredEvents();
    }
  }, {
    key: "sendConnectionFailureEvents",
    value: function sendConnectionFailureEvents() {
      this.sendAppropriateStatusEvent('fail');
    }
  }, {
    key: "sendAppropriateStatusEvent",
    value: function sendAppropriateStatusEvent(status) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var hasCreateAccountAction = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT);
      var hasConnectAction = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT);
      if (hasCreateAccountAction) {
        this.sendEventDirect('CREATE_ACCOUNT_STATUS', {
          status: status,
          currentStep: 1
        });
      } else if (hasConnectAction) {
        if (data) {
          this.sendEventDirect('CONNECT_STATUS', {
            status: status,
            trackingOptedIn: data.tracking_opted_in,
            userTier: data.access_tier
          });
        } else {
          this.sendEventDirect('CONNECT_STATUS', {
            status: status,
            trackingOptedIn: false,
            userTier: null
          });
        }
      } else if (data) {
        this.sendEventDirect('CONNECT_STATUS', {
          status: status,
          trackingOptedIn: data.tracking_opted_in,
          userTier: data.access_tier
        });
      } else {
        this.sendEventDirect('CONNECT_STATUS', {
          status: status,
          trackingOptedIn: false,
          userTier: null
        });
      }
    }
  }, {
    key: "sendAllStoredEvents",
    value: function sendAllStoredEvents() {
      this.sendStoredEvent('SKIP');
      this.sendStoredEvent('TOP_UPGRADE');
      this.sendStoredEvent('CREATE_MY_ACCOUNT');
      this.sendStoredEvent('CREATE_ACCOUNT_STATUS');
      this.sendStoredEvent('CONNECT_STATUS');
      this.sendStoredEvent('STEP1_CLICKED_CONNECT');
      this.sendStoredEvent('STEP1_END_STATE');
      this.sendStoredEvent('EXIT_BUTTON');
    }
  }, {
    key: "handleStep4CardClick",
    value: function handleStep4CardClick() {
      var hasPreviousClick = _storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK);
      if (hasPreviousClick) {
        this.checkAndSendReturnToStep4();
      } else {
        _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK, 'true');
      }
    }
  }, {
    key: "setupAllUpgradeButtons",
    value: function setupAllUpgradeButtons(currentStep) {
      var _this4 = this;
      var upgradeButtons = document.querySelectorAll('.elementor-button[href*="upgrade"], .e-btn[href*="upgrade"], .eps-button[href*="upgrade"]');
      upgradeButtons.forEach(function (button) {
        _this4.setupSingleUpgradeButton(button, currentStep);
      });
      return upgradeButtons.length;
    }
  }, {
    key: "setupSingleUpgradeButton",
    value: function setupSingleUpgradeButton(buttonElement, currentStep) {
      var _this5 = this;
      if (!this.isValidButtonElement(buttonElement)) {
        return null;
      }
      this.cleanupButtonTracking(buttonElement);
      if (this.isButtonAlreadyTrackedForStep(buttonElement, currentStep)) {
        return null;
      }
      this.markButtonAsTracked(buttonElement, currentStep);
      var eventHandlers = this.createUpgradeButtonEventHandlers(buttonElement, currentStep);
      this.attachEventHandlersToButton(buttonElement, eventHandlers);
      return function () {
        _this5.cleanupButtonTracking(buttonElement);
      };
    }
  }, {
    key: "isValidButtonElement",
    value: function isValidButtonElement(buttonElement) {
      return !!buttonElement;
    }
  }, {
    key: "isButtonAlreadyTrackedForStep",
    value: function isButtonAlreadyTrackedForStep(buttonElement, currentStep) {
      var existingStep = buttonElement.dataset.onboardingStep;
      return buttonElement.dataset.onboardingTracked && existingStep === currentStep;
    }
  }, {
    key: "markButtonAsTracked",
    value: function markButtonAsTracked(buttonElement, currentStep) {
      buttonElement.dataset.onboardingTracked = 'true';
      buttonElement.dataset.onboardingStep = currentStep;
    }
  }, {
    key: "createUpgradeButtonEventHandlers",
    value: function createUpgradeButtonEventHandlers(buttonElement, currentStep) {
      var _this6 = this;
      var hasClicked = false;
      var hasHovered = false;
      var handleMouseEnter = function handleMouseEnter() {
        if (!hasHovered) {
          hasHovered = true;
          _this6.trackUpgradeHoverAction(currentStep, buttonElement);
        }
      };
      var handleMouseLeave = function handleMouseLeave() {};
      var handleClick = function handleClick() {
        if (_this6.preventDuplicateClick(hasClicked)) {
          return;
        }
        hasClicked = true;
        _this6.sendUpgradeClickEvent(buttonElement, currentStep);
      };
      return {
        handleMouseEnter: handleMouseEnter,
        handleMouseLeave: handleMouseLeave,
        handleClick: handleClick
      };
    }
  }, {
    key: "preventDuplicateClick",
    value: function preventDuplicateClick(hasClicked) {
      return hasClicked;
    }
  }, {
    key: "sendUpgradeClickEvent",
    value: function sendUpgradeClickEvent(buttonElement, currentStep) {
      var upgradeClickedValue = this.determineUpgradeClickedValue(buttonElement);
      this.sendEventOrStore('TOP_UPGRADE', {
        currentStep: currentStep,
        upgradeClicked: upgradeClickedValue
      });
    }
  }, {
    key: "trackUpgradeHoverAction",
    value: function trackUpgradeHoverAction(currentStep, buttonElement) {
      var stepNumber = this.getStepNumber(currentStep);
      if (!stepNumber) {
        return;
      }
      var upgradeHoverValue = this.determineUpgradeClickedValue(buttonElement);
      this.trackStepAction(stepNumber, 'upgrade_hover', {
        upgrade_hovered: upgradeHoverValue,
        hover_timestamp: _timingManager.default.getCurrentTime()
      });
    }
  }, {
    key: "sendHoverEventsFromStepActions",
    value: function sendHoverEventsFromStepActions(actions, stepNumber) {
      var _this7 = this;
      var hoverActions = actions.filter(function (action) {
        return 'upgrade_hover' === action.action;
      });
      if (0 === hoverActions.length) {
        return;
      }
      var hasUpgradeClickInActions = actions.some(function (action) {
        return 'upgrade_topbar' === action.action || 'upgrade_tooltip' === action.action || 'upgrade_now' === action.action || 'upgrade_already_pro' === action.action;
      });
      var hasStoredClickEvent = this.hasExistingUpgradeClickEvent(stepNumber);
      var hasClickBeenSent = this.hasUpgradeClickBeenSent(stepNumber);
      if (hasUpgradeClickInActions || hasStoredClickEvent || hasClickBeenSent) {
        return;
      }
      hoverActions.forEach(function (hoverAction) {
        _this7.sendEventOrStore('TOP_UPGRADE', {
          currentStep: stepNumber,
          upgradeClicked: 'no_click',
          upgradeHovered: hoverAction.upgrade_hovered,
          hoverTimestamp: hoverAction.hover_timestamp
        });
      });
    }
  }, {
    key: "markUpgradeClickSent",
    value: function markUpgradeClickSent(stepNumber) {
      if (!this.sentUpgradeClicks) {
        this.sentUpgradeClicks = new Set();
      }
      this.sentUpgradeClicks.add(stepNumber);
    }
  }, {
    key: "hasUpgradeClickBeenSent",
    value: function hasUpgradeClickBeenSent(stepNumber) {
      return this.sentUpgradeClicks && this.sentUpgradeClicks.has(stepNumber);
    }
  }, {
    key: "hasExistingUpgradeClickEvent",
    value: function hasExistingUpgradeClickEvent(stepNumber) {
      var _this8 = this;
      var config = this.EVENT_CONFIGS.TOP_UPGRADE;
      var storedEvents = _storageManager.default.getArray(config.storageKey);
      return storedEvents.some(function (event) {
        var eventStepNumber = _this8.getStepNumber(event.currentStep);
        return eventStepNumber === stepNumber && event.upgradeClicked && 'no_click' !== event.upgradeClicked;
      });
    }
  }, {
    key: "attachEventHandlersToButton",
    value: function attachEventHandlersToButton(buttonElement, eventHandlers) {
      var handleMouseEnter = eventHandlers.handleMouseEnter,
        handleMouseLeave = eventHandlers.handleMouseLeave,
        handleClick = eventHandlers.handleClick;
      buttonElement._onboardingHandlers = {
        mouseenter: handleMouseEnter,
        mouseleave: handleMouseLeave,
        click: handleClick
      };
      buttonElement.addEventListener('mouseenter', handleMouseEnter);
      buttonElement.addEventListener('mouseleave', handleMouseLeave);
      buttonElement.addEventListener('click', handleClick);
    }
  }, {
    key: "cleanupButtonTracking",
    value: function cleanupButtonTracking(buttonElement) {
      if (!buttonElement) {
        return;
      }
      this.removeExistingEventHandlers(buttonElement);
      this.clearTrackingDataAttributes(buttonElement);
    }
  }, {
    key: "removeExistingEventHandlers",
    value: function removeExistingEventHandlers(buttonElement) {
      if (buttonElement._onboardingHandlers) {
        var handlers = buttonElement._onboardingHandlers;
        buttonElement.removeEventListener('mouseenter', handlers.mouseenter);
        buttonElement.removeEventListener('mouseleave', handlers.mouseleave);
        buttonElement.removeEventListener('click', handlers.click);
        delete buttonElement._onboardingHandlers;
      }
    }
  }, {
    key: "clearTrackingDataAttributes",
    value: function clearTrackingDataAttributes(buttonElement) {
      delete buttonElement.dataset.onboardingTracked;
      delete buttonElement.dataset.onboardingStep;
    }
  }, {
    key: "determineUpgradeClickedValue",
    value: function determineUpgradeClickedValue(buttonElement) {
      var _elementorCommon$conf, _elementorCommon$conf2;
      if ((_elementorCommon$conf = elementorCommon.config.library_connect) !== null && _elementorCommon$conf !== void 0 && _elementorCommon$conf.is_connected && 'pro' === ((_elementorCommon$conf2 = elementorCommon.config.library_connect) === null || _elementorCommon$conf2 === void 0 ? void 0 : _elementorCommon$conf2.current_access_tier)) {
        return 'already_pro_user';
      }
      if (buttonElement.closest('.e-app__popover') || buttonElement.closest('.elementor-tooltip') || buttonElement.closest('.e-onboarding__go-pro-content')) {
        return 'on_tooltip';
      }
      if (buttonElement.closest('.eps-app__header')) {
        return 'on_topbar';
      }
      return 'on_topbar';
    }
  }, {
    key: "trackExitAndSendEndState",
    value: function trackExitAndSendEndState(currentStep) {
      this.trackStepAction(currentStep, 'exit');
      this.sendStepEndState(currentStep);
    }
  }, {
    key: "storeStep1EndStateForLater",
    value: function storeStep1EndStateForLater(eventData, storageKey) {
      this.storeEventForLater('STEP1_END_STATE', eventData);
      _storageManager.default.remove(storageKey);
    }
  }, {
    key: "onStepLoad",
    value: function onStepLoad(currentStep) {
      var stepNumber = this.getStepNumber(currentStep);
      _timingManager.default.trackStepStartTime(stepNumber);
      if (2 === stepNumber || 'hello' === currentStep || 'hello_biz' === currentStep) {
        this.sendStoredStep1EventsOnStep2();
        this.sendThemeSelectionExperimentStarted();
      }
      if (4 === stepNumber || 'goodToGo' === currentStep) {
        this.checkAndSendReturnToStep4();
        this.sendGoodToGoExperimentStarted();
      }
    }
  }, {
    key: "sendStoredStep1EventsOnStep2",
    value: function sendStoredStep1EventsOnStep2() {
      this.sendStoredEvent('STEP1_CLICKED_CONNECT');
      var step1Actions = _storageManager.default.getArray(_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS);
      if (step1Actions.length > 0) {
        this.sendHoverEventsFromStepActions(step1Actions, 1);
      }
      this.sendStoredEvent('STEP1_END_STATE');
    }
  }, {
    key: "setupPostOnboardingClickTracking",
    value: function setupPostOnboardingClickTracking() {
      return _postOnboardingTracker.default.setupPostOnboardingClickTracking();
    }
  }, {
    key: "cleanupPostOnboardingTracking",
    value: function cleanupPostOnboardingTracking() {
      return _postOnboardingTracker.default.cleanupPostOnboardingTracking();
    }
  }, {
    key: "clearAllOnboardingStorage",
    value: function clearAllOnboardingStorage() {
      return _postOnboardingTracker.default.clearAllOnboardingStorage();
    }
  }, {
    key: "isThemeSelectionExperimentEnabled",
    value: function isThemeSelectionExperimentEnabled() {
      var _elementorAppConfig;
      return ((_elementorAppConfig = elementorAppConfig) === null || _elementorAppConfig === void 0 || (_elementorAppConfig = _elementorAppConfig.onboarding) === null || _elementorAppConfig === void 0 ? void 0 : _elementorAppConfig.themeSelectionExperimentEnabled) || false;
    }
  }, {
    key: "isGoodToGoExperimentEnabled",
    value: function isGoodToGoExperimentEnabled() {
      var _elementorAppConfig2;
      return ((_elementorAppConfig2 = elementorAppConfig) === null || _elementorAppConfig2 === void 0 || (_elementorAppConfig2 = _elementorAppConfig2.onboarding) === null || _elementorAppConfig2 === void 0 ? void 0 : _elementorAppConfig2.goodToGoExperimentEnabled) || false;
    }
  }, {
    key: "getThemeSelectionVariant",
    value: function getThemeSelectionVariant() {
      var stored = _storageManager.default.getString(_storageManager.ONBOARDING_STORAGE_KEYS.THEME_SELECTION_VARIANT);
      if (stored) {
        return stored;
      }
      return null;
    }
  }, {
    key: "getGoodToGoVariant",
    value: function getGoodToGoVariant() {
      var stored = _storageManager.default.getString(_storageManager.ONBOARDING_STORAGE_KEYS.GOOD_TO_GO_VARIANT);
      if (stored) {
        return stored;
      }
      return null;
    }
  }, {
    key: "assignThemeSelectionVariant",
    value: function assignThemeSelectionVariant() {
      if (!this.isThemeSelectionExperimentEnabled()) {
        return null;
      }
      var variant = Math.random() < 0.5 ? 'A' : 'B';
      _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.THEME_SELECTION_VARIANT, variant);
      return variant;
    }
  }, {
    key: "assignGoodToGoVariant",
    value: function assignGoodToGoVariant() {
      if (!this.isGoodToGoExperimentEnabled()) {
        return null;
      }
      var variant = Math.random() < 0.5 ? 'A' : 'B';
      _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.GOOD_TO_GO_VARIANT, variant);
      return variant;
    }
  }, {
    key: "sendThemeSelectionExperimentStarted",
    value: function sendThemeSelectionExperimentStarted() {
      if (_storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.THEME_SELECTION_EXPERIMENT_STARTED)) {
        return;
      }
      var variant = this.getThemeSelectionVariant();
      if (!variant) {
        variant = this.assignThemeSelectionVariant();
        if (!variant) {
          return;
        }
      }
      if (!_eventDispatcher.default.canSendEvents()) {
        return;
      }
      var eventData = {
        'Experiment name': 'core_onboarding_theme_selection',
        'Variant name': variant
      };
      _eventDispatcher.default.dispatch('$experiment_started', eventData);
      _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.THEME_SELECTION_EXPERIMENT_STARTED, 'true');
    }
  }, {
    key: "sendGoodToGoExperimentStarted",
    value: function sendGoodToGoExperimentStarted() {
      if (_storageManager.default.exists(_storageManager.ONBOARDING_STORAGE_KEYS.GOOD_TO_GO_EXPERIMENT_STARTED)) {
        return;
      }
      var variant = this.getGoodToGoVariant();
      if (!variant) {
        variant = this.assignGoodToGoVariant();
        if (!variant) {
          return;
        }
      }
      if (!_eventDispatcher.default.canSendEvents()) {
        return;
      }
      var eventData = {
        'Experiment name': 'core_onboarding_good_to_go',
        'Variant name': variant
      };
      _eventDispatcher.default.dispatch('$experiment_started', eventData);
      _storageManager.default.setString(_storageManager.ONBOARDING_STORAGE_KEYS.GOOD_TO_GO_EXPERIMENT_STARTED, 'true');
    }
  }]);
}();
var onboardingTracker = new OnboardingTracker();
var _default = exports["default"] = onboardingTracker;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/modules/timing-manager.js":
/*!***************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/modules/timing-manager.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.addTimingToEventData = addTimingToEventData;
exports.calculateStepTimeSpent = calculateStepTimeSpent;
exports.calculateTotalTimeSpent = calculateTotalTimeSpent;
exports.clearStaleSessionData = clearStaleSessionData;
exports.clearStepStartTime = clearStepStartTime;
exports.createTimeSpentData = createTimeSpentData;
exports["default"] = void 0;
exports.formatTimeForEvent = formatTimeForEvent;
exports.getCurrentTime = getCurrentTime;
exports.getOnboardingStartTime = getOnboardingStartTime;
exports.hasOnboardingStarted = hasOnboardingStarted;
exports.initializeOnboardingStartTime = initializeOnboardingStartTime;
exports.isWithinTimeThreshold = isWithinTimeThreshold;
exports.trackStepStartTime = trackStepStartTime;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _storageManager = _interopRequireWildcard(__webpack_require__(/*! ./storage-manager.js */ "../app/modules/onboarding/assets/js/utils/modules/storage-manager.js"));
var StorageManager = _storageManager;
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function getCurrentTime() {
  return Date.now();
}
function initializeOnboardingStartTime() {
  var startTime = getCurrentTime();
  StorageManager.setNumber(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME, startTime);
  StorageManager.setString(_storageManager.ONBOARDING_STORAGE_KEYS.INITIATED, 'true');
  return startTime;
}
function getOnboardingStartTime() {
  return StorageManager.getNumber(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME);
}
function hasOnboardingStarted() {
  return StorageManager.exists(_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME);
}
function trackStepStartTime(stepNumber) {
  var existingStartTime = StorageManager.getStepStartTime(stepNumber);
  if (existingStartTime) {
    return existingStartTime;
  }
  var currentTime = getCurrentTime();
  StorageManager.setStepStartTime(stepNumber, currentTime);
  return currentTime;
}
function calculateStepTimeSpent(stepNumber) {
  var stepStartTime = StorageManager.getStepStartTime(stepNumber);
  if (!stepStartTime) {
    return null;
  }
  var currentTime = getCurrentTime();
  var stepTimeSpent = Math.round((currentTime - stepStartTime) / 1000);
  return stepTimeSpent;
}
function clearStepStartTime(stepNumber) {
  StorageManager.clearStepStartTime(stepNumber);
}
function calculateTotalTimeSpent() {
  var startTime = getOnboardingStartTime();
  if (!startTime) {
    return null;
  }
  var currentTime = getCurrentTime();
  var timeSpent = Math.round((currentTime - startTime) / 1000);
  return {
    startTime: startTime,
    currentTime: currentTime,
    timeSpent: timeSpent
  };
}
function formatTimeForEvent(timeInSeconds) {
  if (null === timeInSeconds || timeInSeconds === undefined) {
    return null;
  }
  return "".concat(timeInSeconds, "s");
}
function createTimeSpentData() {
  var stepNumber = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  var totalTimeData = calculateTotalTimeSpent();
  var result = {};
  if (totalTimeData) {
    result.time_spent = formatTimeForEvent(totalTimeData.timeSpent);
    result.total_onboarding_time_seconds = totalTimeData.timeSpent;
    result.onboarding_start_time = totalTimeData.startTime;
  }
  if (stepNumber) {
    var stepTimeSpent = calculateStepTimeSpent(stepNumber);
    if (stepTimeSpent !== null) {
      result.step_time_spent = formatTimeForEvent(stepTimeSpent);
    }
  }
  return Object.keys(result).length > 0 ? result : null;
}
function addTimingToEventData(eventData) {
  var stepNumber = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var timingData = createTimeSpentData(stepNumber);
  if (timingData) {
    return _objectSpread(_objectSpread({}, eventData), timingData);
  }
  return eventData;
}
function clearStaleSessionData() {
  var recentStepStartTimes = [];
  var currentTime = getCurrentTime();
  var recentStepStartTimeThresholdMs = 5000;
  [_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_START_TIME].forEach(function (key) {
    var value = StorageManager.getString(key);
    if (value) {
      var timestamp = parseInt(value, 10);
      var age = currentTime - timestamp;
      if (age < recentStepStartTimeThresholdMs) {
        recentStepStartTimes.push(key);
      }
    }
  });
  var keysToRemove = [_storageManager.ONBOARDING_STORAGE_KEYS.START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.INITIATED, _storageManager.ONBOARDING_STORAGE_KEYS.STEP1_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_ACTIONS, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_SITE_STARTER_CHOICE, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_HAS_PREVIOUS_CLICK, _storageManager.ONBOARDING_STORAGE_KEYS.EDITOR_LOAD_TRACKED, _storageManager.ONBOARDING_STORAGE_KEYS.POST_ONBOARDING_CLICK_COUNT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_SKIP, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CONNECT_STATUS, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_ACCOUNT_STATUS, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_CREATE_MY_ACCOUNT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_NO_CLICK, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_CLICKED_CONNECT, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_STEP1_END_STATE, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_EXIT_BUTTON, _storageManager.ONBOARDING_STORAGE_KEYS.PENDING_TOP_UPGRADE_MOUSEOVER];
  keysToRemove.forEach(function (key) {
    if (!recentStepStartTimes.includes(key)) {
      StorageManager.remove(key);
    }
  });
  [_storageManager.ONBOARDING_STORAGE_KEYS.STEP1_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP2_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP3_START_TIME, _storageManager.ONBOARDING_STORAGE_KEYS.STEP4_START_TIME].forEach(function (key) {
    if (!recentStepStartTimes.includes(key)) {
      StorageManager.remove(key);
    }
  });
}
function isWithinTimeThreshold(timestamp) {
  var thresholdMs = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 5000;
  var currentTime = getCurrentTime();
  return currentTime - timestamp < thresholdMs;
}
var TimingManager = {
  getCurrentTime: getCurrentTime,
  initializeOnboardingStartTime: initializeOnboardingStartTime,
  getOnboardingStartTime: getOnboardingStartTime,
  hasOnboardingStarted: hasOnboardingStarted,
  trackStepStartTime: trackStepStartTime,
  calculateStepTimeSpent: calculateStepTimeSpent,
  clearStepStartTime: clearStepStartTime,
  calculateTotalTimeSpent: calculateTotalTimeSpent,
  formatTimeForEvent: formatTimeForEvent,
  createTimeSpentData: createTimeSpentData,
  addTimingToEventData: addTimingToEventData,
  clearStaleSessionData: clearStaleSessionData,
  isWithinTimeThreshold: isWithinTimeThreshold
};
var _default = exports["default"] = TimingManager;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js":
/*!******************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/onboarding-event-tracking.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
Object.defineProperty(exports, "ONBOARDING_STEP_NAMES", ({
  enumerable: true,
  get: function get() {
    return _onboardingTracker.ONBOARDING_STEP_NAMES;
  }
}));
Object.defineProperty(exports, "ONBOARDING_STORAGE_KEYS", ({
  enumerable: true,
  get: function get() {
    return _onboardingTracker.ONBOARDING_STORAGE_KEYS;
  }
}));
exports.OnboardingEventTracking = void 0;
var _onboardingTracker = _interopRequireWildcard(__webpack_require__(/*! ./modules/onboarding-tracker.js */ "../app/modules/onboarding/assets/js/utils/modules/onboarding-tracker.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var OnboardingEventTracking = exports.OnboardingEventTracking = _onboardingTracker.default;

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/use-button-action.js":
/*!**********************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/use-button-action.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useButtonAction;
var _react = __webpack_require__(/*! react */ "react");
var _context = __webpack_require__(/*! ../context/context */ "../app/modules/onboarding/assets/js/context/context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
function useButtonAction(pageId, nextPage) {
  var _useContext = (0, _react.useContext)(_context.OnboardingContext),
    state = _useContext.state,
    updateState = _useContext.updateState,
    getStateObjectToUpdate = _useContext.getStateObjectToUpdate;
  var navigate = (0, _router.useNavigate)();
  var handleAction = function handleAction(action) {
    var stateToUpdate = getStateObjectToUpdate(state, 'steps', pageId, action);
    updateState(stateToUpdate);
    navigate('onboarding/' + nextPage);
  };
  return {
    state: state,
    handleAction: handleAction
  };
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/utils/utils.js":
/*!**********************************************************!*\
  !*** ../app/modules/onboarding/assets/js/utils/utils.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.setSelectedFeatureList = exports.safeDispatchEvent = exports.options = void 0;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * Checkboxes data.
 */
var options = exports.options = [{
  plan: 'essential',
  text: __('Templates & Theme Builder', 'elementor')
}, {
  plan: 'advanced',
  text: __('WooCommerce Builder', 'elementor')
}, {
  plan: 'essential',
  text: __('Lead Collection & Form Builder', 'elementor')
}, {
  plan: 'essential',
  text: __('Dynamic Content', 'elementor')
}, {
  plan: 'advanced',
  text: __('Popup Builder', 'elementor')
}, {
  plan: 'advanced',
  text: __('Custom Code & CSS', 'elementor')
}, {
  plan: 'essential',
  text: __('Motion Effects & Animations', 'elementor')
}, {
  plan: 'advanced',
  text: __('Notes & Collaboration', 'elementor')
}];

/**
 * Set the selected feature list.
 * @param {Object}   param0
 * @param {boolean}  param0.checked
 * @param {string}   param0.id
 * @param {string}   param0.text
 * @param {Object}   param0.selectedFeatures
 * @param {Function} param0.setSelectedFeatures
 */
var setSelectedFeatureList = exports.setSelectedFeatureList = function setSelectedFeatureList(_ref) {
  var checked = _ref.checked,
    id = _ref.id,
    text = _ref.text,
    selectedFeatures = _ref.selectedFeatures,
    setSelectedFeatures = _ref.setSelectedFeatures;
  var tier = id.split('-')[0];
  if (checked) {
    setSelectedFeatures(_objectSpread(_objectSpread({}, selectedFeatures), {}, (0, _defineProperty2.default)({}, tier, [].concat((0, _toConsumableArray2.default)(selectedFeatures[tier]), [text]))));
  } else {
    setSelectedFeatures(_objectSpread(_objectSpread({}, selectedFeatures), {}, (0, _defineProperty2.default)({}, tier, selectedFeatures[tier].filter(function (item) {
      return item !== text;
    }))));
  }
};
var safeDispatchEvent = exports.safeDispatchEvent = function safeDispatchEvent(eventName, eventData) {
  try {
    var _elementorCommon, _elementorCommon$disp;
    (_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 || (_elementorCommon = _elementorCommon.eventsManager) === null || _elementorCommon === void 0 || (_elementorCommon$disp = _elementorCommon.dispatchEvent) === null || _elementorCommon$disp === void 0 || _elementorCommon$disp.call(_elementorCommon, eventName, eventData);
  } catch (error) {
    // Silently fail - don't let tracking break the user experience
  }
};

/***/ })

}]);
//# sourceMappingURL=onboarding.a7a34522c0205e4ea1ea.bundle.js.map