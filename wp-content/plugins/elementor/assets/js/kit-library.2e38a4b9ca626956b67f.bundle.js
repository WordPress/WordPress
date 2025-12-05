(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["kit-library"],{

/***/ "../app/assets/js/hooks/use-cloud-kits-quota.js":
/*!******************************************************!*\
  !*** ../app/assets/js/hooks/use-cloud-kits-quota.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useCloudKitsQuota;
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _cloudKits = __webpack_require__(/*! ../utils/cloud-kits.js */ "../app/assets/js/utils/cloud-kits.js");
var KEY = exports.KEY = 'cloud-kits-quota';

/**
 * Hook to fetch cloud kits quota data
 *
 * @param {Object} options - React Query options
 * @return {Object} Query result with quota data
 */
function useCloudKitsQuota() {
  var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return (0, _reactQuery.useQuery)([KEY], _cloudKits.fetchCloudKitsQuota, options);
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/app.js":
/*!***************************************************!*\
  !*** ../app/modules/kit-library/assets/js/app.js ***!
  \***************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = App;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _favorites = _interopRequireDefault(__webpack_require__(/*! ./pages/favorites/favorites */ "../app/modules/kit-library/assets/js/pages/favorites/favorites.js"));
var _index = _interopRequireDefault(__webpack_require__(/*! ./pages/index */ "../app/modules/kit-library/assets/js/pages/index/index.js"));
var _cloud = _interopRequireDefault(__webpack_require__(/*! ./pages/cloud/cloud */ "../app/modules/kit-library/assets/js/pages/cloud/cloud.js"));
var _overview = _interopRequireDefault(__webpack_require__(/*! ./pages/overview/overview */ "../app/modules/kit-library/assets/js/pages/overview/overview.js"));
var _preview = _interopRequireDefault(__webpack_require__(/*! ./pages/preview/preview */ "../app/modules/kit-library/assets/js/pages/preview/preview.js"));
var _lastFilterContext = __webpack_require__(/*! ./context/last-filter-context */ "../app/modules/kit-library/assets/js/context/last-filter-context.js");
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _devtools = __webpack_require__(/*! react-query/devtools */ "../node_modules/react-query/devtools/index.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _settingsContext = __webpack_require__(/*! ./context/settings-context */ "../app/modules/kit-library/assets/js/context/settings-context.js");
var _connectStateContext = __webpack_require__(/*! ./context/connect-state-context */ "../app/modules/kit-library/assets/js/context/connect-state-context.js");
var _trackingContext = __webpack_require__(/*! ./context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
var queryClient = new _reactQuery.QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      retry: false,
      staleTime: 1000 * 60 * 30 // 30 minutes
    }
  }
});
function AppContent() {
  return /*#__PURE__*/_react.default.createElement(_settingsContext.SettingsProvider, {
    value: elementorAppConfig['kit-library']
  }, /*#__PURE__*/_react.default.createElement(_connectStateContext.ConnectStateProvider, null, /*#__PURE__*/_react.default.createElement(_trackingContext.TrackingProvider, null, /*#__PURE__*/_react.default.createElement(_lastFilterContext.LastFilterProvider, null, /*#__PURE__*/_react.default.createElement(_router.Router, null, /*#__PURE__*/_react.default.createElement(_index.default, {
    path: "/"
  }), /*#__PURE__*/_react.default.createElement(_favorites.default, {
    path: "/favorites"
  }), /*#__PURE__*/_react.default.createElement(_preview.default, {
    path: "/preview/:id"
  }), /*#__PURE__*/_react.default.createElement(_overview.default, {
    path: "/overview/:id"
  }), /*#__PURE__*/_react.default.createElement(_cloud.default, {
    path: "/cloud"
  }))))));
}
function App() {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library"
  }, /*#__PURE__*/_react.default.createElement(_reactQuery.QueryClientProvider, {
    client: queryClient
  }, /*#__PURE__*/_react.default.createElement(AppContent, null), elementorCommon.config.isElementorDebug && /*#__PURE__*/_react.default.createElement(_devtools.ReactQueryDevtools, {
    initialIsOpen: false
  })));
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/apply-kit-dialog.js":
/*!***************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/apply-kit-dialog.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ApplyKitDialog;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ApplyKitDialog(props) {
  var navigate = (0, _router.useNavigate)();
  var tracking = (0, _trackingContext.useTracking)();
  var startImportProcess = (0, _react.useCallback)(function () {
    var _elementorCommon;
    var applyAll = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var url = '';
    if ((_elementorCommon = elementorCommon) !== null && _elementorCommon !== void 0 && (_elementorCommon = _elementorCommon.config) !== null && _elementorCommon !== void 0 && _elementorCommon.experimentalFeatures['import-export-customization']) {
      url = "import-customization?referrer=kit-library&id=".concat(props.id, "&file_url=").concat(encodeURIComponent(props.downloadLink));
      if (applyAll) {
        url += '&action_type=apply-all';
      }
    } else {
      url = '/import/process' + "?id=".concat(props.id) + "&file_url=".concat(encodeURIComponent(props.downloadLink)) + "&nonce=".concat(props.nonce, "&referrer=kit-library");
      if (applyAll) {
        url += '&action_type=apply-all';
      }
    }
    tracking.trackKitdemoApplyAllOrCustomize(applyAll, function () {
      return navigate(url);
    });
  }, [props.downloadLink, props.nonce, props.id, tracking, navigate]);
  return /*#__PURE__*/_react.default.createElement(_appUi.Dialog
  // Translators: %s is the kit name.
  , {
    title: __('Apply %s?', 'elementor').replace('%s', props.title),
    text: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('You can use everything in this kit, or Customize to only include some items.', 'elementor'), /*#__PURE__*/_react.default.createElement("br", null), /*#__PURE__*/_react.default.createElement("br", null), __('By applying the entire kit, you\'ll override any styles, settings or content already on your site.', 'elementor')),
    approveButtonText: __('Apply All', 'elementor'),
    approveButtonColor: "primary",
    approveButtonOnClick: function approveButtonOnClick() {
      return startImportProcess(true);
    },
    dismissButtonText: __('Customize', 'elementor'),
    dismissButtonOnClick: function dismissButtonOnClick() {
      return startImportProcess(false);
    },
    onClose: props.onClose
  });
}
ApplyKitDialog.propTypes = {
  id: PropTypes.string.isRequired,
  downloadLink: PropTypes.string.isRequired,
  nonce: PropTypes.string.isRequired,
  onClose: PropTypes.func.isRequired,
  title: PropTypes.string
};
ApplyKitDialog.defaultProps = {
  title: 'Kit'
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/badge.js":
/*!****************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/badge.js ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Badge;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
__webpack_require__(/*! ./badge.scss */ "../app/modules/kit-library/assets/js/components/badge.scss");
function Badge(props) {
  return /*#__PURE__*/_react.default.createElement("span", {
    className: "eps-badge eps-badge--".concat(props.variant, " ").concat(props.className),
    style: props.style
  }, props.children);
}
Badge.propTypes = {
  children: PropTypes.node,
  className: PropTypes.string,
  style: PropTypes.object,
  variant: PropTypes.oneOf(['sm', 'md'])
};
Badge.defaultProps = {
  className: '',
  style: {},
  variant: 'md'
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/badge.scss":
/*!******************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/badge.scss ***!
  \******************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/collapse.js":
/*!*******************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/collapse.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Collapse;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
__webpack_require__(/*! ./collapse.scss */ "../app/modules/kit-library/assets/js/components/collapse.scss");
function Collapse(props) {
  // The state of the collapse managed by the parent component to let the parent control if the collapse is open or closed by default.
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-collapse ".concat(props.className),
    "data-open": props.isOpen || undefined /* Set `undefined` when 'isOpen' equals `false` to avoid showing the attr "data-open" */
  }, /*#__PURE__*/_react.default.createElement("button", {
    className: "eps-collapse__title",
    onClick: function onClick() {
      var _props$onClick;
      props.onChange(function (value) {
        return !value;
      });
      (_props$onClick = props.onClick) === null || _props$onClick === void 0 || _props$onClick.call(props, props.isOpen, props.title);
    }
  }, /*#__PURE__*/_react.default.createElement("span", null, props.title), /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-chevron-right eps-collapse__icon"
  })), /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-collapse__content"
  }, props.children));
}
Collapse.propTypes = {
  isOpen: PropTypes.bool,
  onChange: PropTypes.func,
  className: PropTypes.string,
  title: PropTypes.node,
  onClick: PropTypes.func,
  children: PropTypes.oneOfType([PropTypes.node, PropTypes.arrayOf(PropTypes.node)])
};
Collapse.defaultProps = {
  className: '',
  isOpen: false
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/collapse.scss":
/*!*********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/collapse.scss ***!
  \*********************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/connect-dialog.js":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/connect-dialog.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ConnectDialog;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _settingsContext = __webpack_require__(/*! ../context/settings-context */ "../app/modules/kit-library/assets/js/context/settings-context.js");
var _React = _react.default,
  useEffect = _React.useEffect,
  useRef = _React.useRef;
function ConnectDialog(props) {
  var _useSettingsContext = (0, _settingsContext.useSettingsContext)(),
    settings = _useSettingsContext.settings;
  var approveButtonRef = useRef();
  useEffect(function () {
    jQuery(approveButtonRef.current).elementorConnect({
      success: function success(e, data) {
        return props.onSuccess(data);
      },
      error: function error() {
        return props.onError(__('Unable to connect', 'elementor'));
      },
      parseUrl: function parseUrl(url) {
        return url.replace('%%page%%', props.pageId);
      }
    });
  }, []);
  return /*#__PURE__*/_react.default.createElement(_appUi.Dialog, {
    title: __('Connect to Template Library', 'elementor'),
    text: __('Access this template and our entire library by creating a free personal account', 'elementor'),
    approveButtonText: __('Get Started', 'elementor'),
    approveButtonUrl: settings.library_connect_url,
    approveButtonOnClick: function approveButtonOnClick() {
      return props.onClose();
    },
    approveButtonColor: "primary",
    approveButtonRef: approveButtonRef,
    dismissButtonText: __('Cancel', 'elementor'),
    dismissButtonOnClick: function dismissButtonOnClick() {
      return props.onClose();
    },
    onClose: function onClose() {
      return props.onClose();
    }
  });
}
ConnectDialog.propTypes = {
  onClose: PropTypes.func.isRequired,
  onError: PropTypes.func.isRequired,
  onSuccess: PropTypes.func.isRequired,
  pageId: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/envato-promotion.js":
/*!***************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/envato-promotion.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = EnvatoPromotion;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./envato-promotion.scss */ "../app/modules/kit-library/assets/js/components/envato-promotion.scss");
function EnvatoPromotion(props) {
  var eventTracking = function eventTracking(command) {
    var eventType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      page_source: 'home page',
      element_position: 'library_bottom_promotion',
      category: props.category && ('/favorites' === props.category ? 'favorites' : 'all kits'),
      event_type: eventType
    });
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    className: "e-kit-library-promotion",
    variant: "xl"
  }, __('Looking for more Website Templates?', 'elementor'), " ", ' ', /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    variant: "underlined",
    color: "link",
    url: "https://go.elementor.com/app-envato-kits/",
    target: "_blank",
    rel: "noreferrer",
    text: __('Check out Elementor Website Templates on ThemeForest', 'elementor'),
    onClick: function onClick() {
      return eventTracking('kit-library/check-kits-on-theme-forest');
    }
  }));
}
EnvatoPromotion.propTypes = {
  category: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/envato-promotion.scss":
/*!*****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/envato-promotion.scss ***!
  \*****************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/error-screen.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/error-screen.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ErrorScreen;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
__webpack_require__(/*! ./error-screen.scss */ "../app/modules/kit-library/assets/js/components/error-screen.scss");
/* eslint-disable jsx-a11y/alt-text */

var ErrorScreenButton = function ErrorScreenButton(props) {
  var onClick = function onClick() {
    if (props.action) {
      props.action();
    }
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: props.text,
    onClick: onClick,
    url: props.url,
    target: props.target,
    color: props.color || 'link',
    variant: props.variant || ''
  });
};
ErrorScreenButton.propTypes = {
  text: _propTypes.default.string,
  action: _propTypes.default.func,
  url: _propTypes.default.string,
  target: _propTypes.default.string,
  color: _propTypes.default.oneOf(['primary', 'secondary', 'cta', 'link', 'disabled']),
  variant: _propTypes.default.oneOf(['contained', 'underlined', 'outlined', ''])
};
function ErrorScreen(props) {
  return /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "center",
    direction: "column",
    className: "e-kit-library__error-screen"
  }, /*#__PURE__*/_react.default.createElement("img", {
    src: "".concat(elementorAppConfig.assets_url, "images/no-search-results.svg")
  }), /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "display-1",
    className: "e-kit-library__error-screen-title"
  }, props.title), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    variant: "xl",
    className: "e-kit-library__error-screen-description"
  }, props.description, " ", ' ', !props.newLineButton && /*#__PURE__*/_react.default.createElement(ErrorScreenButton, props.button)), props.newLineButton && /*#__PURE__*/_react.default.createElement(ErrorScreenButton, props.button));
}
ErrorScreen.propTypes = {
  title: _propTypes.default.string,
  description: _propTypes.default.string,
  newLineButton: _propTypes.default.bool,
  button: _propTypes.default.shape({
    text: _propTypes.default.string,
    action: _propTypes.default.func,
    url: _propTypes.default.string,
    target: _propTypes.default.string,
    category: _propTypes.default.string,
    color: _propTypes.default.oneOf(['primary', 'secondary', 'cta', 'link', 'disabled']),
    variant: _propTypes.default.oneOf(['contained', 'underlined', 'outlined', ''])
  })
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/error-screen.scss":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/error-screen.scss ***!
  \*************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/favorites-actions.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/favorites-actions.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FavoritesActions;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _useKitFavoritesMutations = __webpack_require__(/*! ../hooks/use-kit-favorites-mutations */ "../app/modules/kit-library/assets/js/hooks/use-kit-favorites-mutations.js");
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
__webpack_require__(/*! ./favorites-actions.scss */ "../app/modules/kit-library/assets/js/components/favorites-actions.scss");
function FavoritesActions(props) {
  var _useKitFavoritesMutat = (0, _useKitFavoritesMutations.useKitFavoritesMutations)(),
    addToFavorites = _useKitFavoritesMutat.addToFavorites,
    removeFromFavorites = _useKitFavoritesMutat.removeFromFavorites,
    isLoading = _useKitFavoritesMutat.isLoading;
  var tracking = (0, _trackingContext.useTracking)();
  var loadingClasses = isLoading ? 'e-kit-library__kit-favorite-actions--loading' : '';
  var eventTracking = function eventTracking(kitName, source, action) {
    var gridLocation = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var searchTerm = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    (0, _appsEventTracking.appsEventTrackingDispatch)('kit-library/favorite-icon', {
      grid_location: gridLocation,
      search_term: searchTerm,
      kit_name: kitName,
      page_source: source && ('/' === source ? 'home page' : 'overview'),
      element_location: source && 'overview' === source ? 'app_sidebar' : null,
      action: action
    });
  };
  var handleRemoveFromFavorites = function handleRemoveFromFavorites() {
    if (isLoading) {
      return;
    }
    eventTracking(props === null || props === void 0 ? void 0 : props.name, props === null || props === void 0 ? void 0 : props.source, 'uncheck');
    tracking.trackKitlibFavoriteClicked(props.id, props === null || props === void 0 ? void 0 : props.name, false, function () {
      return removeFromFavorites.mutate(props.id);
    });
  };
  var handleAddToFavorites = function handleAddToFavorites() {
    if (isLoading) {
      return;
    }
    eventTracking(props === null || props === void 0 ? void 0 : props.name, props === null || props === void 0 ? void 0 : props.source, 'check', props === null || props === void 0 ? void 0 : props.index, props === null || props === void 0 ? void 0 : props.queryParams);
    tracking.trackKitlibFavoriteClicked(props.id, props === null || props === void 0 ? void 0 : props.name, true, function () {
      return addToFavorites.mutate(props.id);
    });
  };
  return props.isFavorite ? /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: __('Remove from Favorites', 'elementor'),
    hideText: true,
    icon: "eicon-heart",
    className: "e-kit-library__kit-favorite-actions e-kit-library__kit-favorite-actions--active ".concat(loadingClasses),
    onClick: handleRemoveFromFavorites
  }) : /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: __('Add to Favorites', 'elementor'),
    hideText: true,
    icon: "eicon-heart-o",
    className: "e-kit-library__kit-favorite-actions ".concat(loadingClasses),
    onClick: handleAddToFavorites
  });
}
FavoritesActions.propTypes = {
  isFavorite: PropTypes.bool,
  id: PropTypes.string,
  name: PropTypes.string,
  source: PropTypes.string,
  index: PropTypes.number,
  queryParams: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/favorites-actions.scss":
/*!******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/favorites-actions.scss ***!
  \******************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/filter-indication-text.js":
/*!*********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/filter-indication-text.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FilterIndicationText;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _useSelectedTaxonomies = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-selected-taxonomies */ "../app/modules/kit-library/assets/js/hooks/use-selected-taxonomies.js"));
var _badge = _interopRequireDefault(__webpack_require__(/*! ./badge */ "../app/modules/kit-library/assets/js/components/badge.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./filter-indication-text.scss */ "../app/modules/kit-library/assets/js/components/filter-indication-text.scss");
var _taxonomyTransformer = __webpack_require__(/*! ../models/taxonomy-transformer */ "../app/modules/kit-library/assets/js/models/taxonomy-transformer.js");
function FilterIndicationText(props) {
  var selectedTaxonomies = (0, _useSelectedTaxonomies.default)(props.queryParams.taxonomies);
  var eventTracking = function eventTracking(taxonomy) {
    var eventType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)('kit-library/clear-filter', {
      tag: taxonomy,
      page_source: 'home page',
      event_type: eventType
    });
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    className: "e-kit-library__filter-indication"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    className: "e-kit-library__filter-indication-text"
  },
  // Translators: %s is the number of kits in the results
  (0, _i18n.sprintf)((0, _i18n._n)('Showing %s result for', 'Showing %s results for', props.resultCount, 'elementor'), !props.resultCount ? __('no', 'elementor') : props.resultCount), ' ', props.queryParams.search && "\"".concat(props.queryParams.search, "\""), ' ', selectedTaxonomies.length > 0 && /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, selectedTaxonomies.map(function (taxonomy) {
    return /*#__PURE__*/_react.default.createElement(_badge.default, {
      key: taxonomy,
      className: "e-kit-library__filter-indication-badge"
    }, _taxonomyTransformer.NewPlanTexts[taxonomy] || taxonomy, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
      text: __('Remove', 'elementor'),
      hideText: true,
      icon: "eicon-editor-close",
      className: "e-kit-library__filter-indication-badge-remove",
      onClick: function onClick() {
        eventTracking(taxonomy);
        props.onRemoveTag(taxonomy);
      }
    }));
  }))), /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__filter-indication-button",
    text: __('Clear all', 'elementor'),
    variant: "underlined",
    onClick: function onClick() {
      eventTracking('all');
      props.onClear();
    }
  }));
}
FilterIndicationText.propTypes = {
  queryParams: PropTypes.shape({
    search: PropTypes.string,
    taxonomies: PropTypes.objectOf(PropTypes.arrayOf(PropTypes.string)),
    favorite: PropTypes.bool
  }),
  resultCount: PropTypes.number.isRequired,
  onClear: PropTypes.func.isRequired,
  onRemoveTag: PropTypes.func.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/filter-indication-text.scss":
/*!***********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/filter-indication-text.scss ***!
  \***********************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/item-header.js":
/*!**********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/item-header.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ItemHeader;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _kitDialog = _interopRequireDefault(__webpack_require__(/*! ./kit-dialog */ "../app/modules/kit-library/assets/js/components/kit-dialog.js"));
var _connectDialog = _interopRequireDefault(__webpack_require__(/*! ./connect-dialog */ "../app/modules/kit-library/assets/js/components/connect-dialog.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ./layout/header */ "../app/modules/kit-library/assets/js/components/layout/header.js"));
var _headerBackButton = _interopRequireDefault(__webpack_require__(/*! ./layout/header-back-button */ "../app/modules/kit-library/assets/js/components/layout/header-back-button.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _useDownloadLinkMutation = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-download-link-mutation */ "../app/modules/kit-library/assets/js/hooks/use-download-link-mutation.js"));
var _useKitCallToAction3 = _interopRequireWildcard(__webpack_require__(/*! ../hooks/use-kit-call-to-action */ "../app/modules/kit-library/assets/js/hooks/use-kit-call-to-action.js"));
var _useAddKitPromotionUtm = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-add-kit-promotion-utm */ "../app/modules/kit-library/assets/js/hooks/use-add-kit-promotion-utm.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _settingsContext = __webpack_require__(/*! ../context/settings-context */ "../app/modules/kit-library/assets/js/context/settings-context.js");
var _tiers = __webpack_require__(/*! elementor-utils/tiers */ "../assets/dev/js/utils/tiers.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
__webpack_require__(/*! ./item-header.scss */ "../app/modules/kit-library/assets/js/components/item-header.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/**
 * Returns the right call to action button.
 *
 * @param {Kit}      model
 * @param {Object}   root0
 * @param {Function} root0.apply
 * @param {Function} root0.onConnect
 * @param {Function} root0.onClick
 * @param {boolean}  root0.isApplyLoading
 * @param {Function} root0.onUpgrade
 * @return {Object} result
 */
function useKitCallToActionButton(model, _ref) {
  var apply = _ref.apply,
    isApplyLoading = _ref.isApplyLoading,
    onConnect = _ref.onConnect,
    _onClick = _ref.onClick,
    _ref$onUpgrade = _ref.onUpgrade,
    onUpgrade = _ref$onUpgrade === void 0 ? function () {} : _ref$onUpgrade;
  var _useKitCallToAction = (0, _useKitCallToAction3.default)(model.accessTier),
    type = _useKitCallToAction.type,
    subscriptionPlan = _useKitCallToAction.subscriptionPlan;
  var promotionUrl = (0, _useAddKitPromotionUtm.default)(subscriptionPlan.promotion_url, model.id, model.title);
  var _useSettingsContext = (0, _settingsContext.useSettingsContext)(),
    settings = _useSettingsContext.settings;
  return (0, _react.useMemo)(function () {
    if (type === _useKitCallToAction3.TYPE_CONNECT) {
      return {
        id: 'connect',
        text: __('Apply', 'elementor'),
        // The label is Apply kit but the this is connect button
        hideText: false,
        variant: 'contained',
        color: 'primary',
        size: 'sm',
        onClick: function onClick(e) {
          onConnect(e);
          _onClick === null || _onClick === void 0 || _onClick(e);
        },
        includeHeaderBtnClass: false
      };
    }
    if (type === _useKitCallToAction3.TYPE_PROMOTION && subscriptionPlan) {
      return {
        id: 'promotion',
        text: settings.is_pro ? 'Upgrade' : "Go ".concat(subscriptionPlan.label),
        hideText: false,
        variant: 'contained',
        color: 'cta',
        size: 'sm',
        url: promotionUrl,
        target: '_blank',
        onClick: function onClick(e) {
          onUpgrade === null || onUpgrade === void 0 || onUpgrade(e);
        },
        includeHeaderBtnClass: false
      };
    }
    return {
      id: 'apply',
      text: __('Apply', 'elementor'),
      className: 'e-kit-library__apply-button',
      icon: isApplyLoading ? 'eicon-loading eicon-animation-spin' : '',
      hideText: false,
      variant: 'contained',
      color: isApplyLoading ? 'disabled' : 'primary',
      size: 'sm',
      onClick: function onClick(e) {
        if (!isApplyLoading) {
          apply(e);
        }
        _onClick === null || _onClick === void 0 || _onClick(e);
      },
      includeHeaderBtnClass: false
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [type, subscriptionPlan, isApplyLoading, apply]);
}
function ItemHeader(props) {
  var _useSettingsContext2 = (0, _settingsContext.useSettingsContext)(),
    updateSettings = _useSettingsContext2.updateSettings;
  var tracking = (0, _trackingContext.useTracking)();
  var resetConnect = function resetConnect() {
    var _elementorCommon;
    var lc = (_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 || (_elementorCommon = _elementorCommon.config) === null || _elementorCommon === void 0 ? void 0 : _elementorCommon.library_connect;
    if (!lc) {
      return;
    }
    lc.is_connected = false;
    lc.current_access_level = 0;
    lc.current_access_tier = _tiers.TIERS.free;
    lc.plan_type = _tiers.TIERS.free;
    updateSettings({
      is_library_connected: false,
      access_level: 0,
      access_tier: _tiers.TIERS.free
    });
  };
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isConnectDialogOpen = _useState2[0],
    setIsConnectDialogOpen = _useState2[1];
  var _useState3 = (0, _react.useState)(null),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    downloadLinkData = _useState4[0],
    setDownloadLinkData = _useState4[1];
  var _useState5 = (0, _react.useState)(false),
    _useState6 = (0, _slicedToArray2.default)(_useState5, 2),
    error = _useState6[0],
    setError = _useState6[1];
  var handleKitError = function handleKitError(_ref2) {
    var code = _ref2.code;
    if (401 === code) {
      resetConnect();
      setIsConnectDialogOpen(true);
      return;
    }
    setError({
      code: code,
      message: __('Something went wrong.', 'elementor')
    });
  };
  var kitData = {
    kitName: props.model.title,
    pageId: props.pageId
  };
  var _useDownloadLinkMutat = (0, _useDownloadLinkMutation.default)(props.model, {
      onSuccess: function onSuccess(_ref3) {
        var data = _ref3.data;
        return setDownloadLinkData(data);
      },
      onError: handleKitError
    }),
    apply = _useDownloadLinkMutat.mutate,
    isApplyLoading = _useDownloadLinkMutat.isLoading;
  var _useDownloadLinkMutat2 = (0, _useDownloadLinkMutation.default)(props.model, {
      onSuccess: function onSuccess(response) {
        try {
          var _response$data;
          var linkUrl = response === null || response === void 0 || (_response$data = response.data) === null || _response$data === void 0 || (_response$data = _response$data.data) === null || _response$data === void 0 ? void 0 : _response$data.download_link;
          if (linkUrl) {
            window.open(linkUrl, '_blank');
          }
        } catch (e) {
          setError({
            message: __('Something went wrong.', 'elementor')
          });
        }
      },
      onError: handleKitError
    }),
    fetchDownloadLink = _useDownloadLinkMutat2.mutate,
    isDownloadLoading = _useDownloadLinkMutat2.isLoading;
  var _useKitCallToAction2 = (0, _useKitCallToAction3.default)(props.model.accessTier),
    subscriptionPlan = _useKitCallToAction2.subscriptionPlan;
  var applyButton = useKitCallToActionButton(props.model, {
    onConnect: function onConnect() {
      return setIsConnectDialogOpen(true);
    },
    apply: apply,
    isApplyLoading: isApplyLoading,
    onClick: function onClick() {
      (0, _appsEventTracking.appsEventTrackingDispatch)('kit-library/apply-kit', {
        kit_name: props.model.title,
        element_position: 'app_header',
        page_source: props.pageId,
        event_type: 'click'
      });
      tracking.trackKitdemoApplyClicked(props.model.id, props.model.title, props.model.accessTier);
    },
    onUpgrade: function onUpgrade() {
      tracking.trackKitdemoUpgradeClicked(props.model.id, props.model.title, subscriptionPlan.label);
    }
  });
  var downloadButton = (0, _react.useMemo)(function () {
    return {
      id: 'download',
      text: __('Download Website', 'elementor'),
      hideText: true,
      icon: 'eicon-file-download',
      tooltip: __('Download Website ZIP', 'elementor'),
      color: isDownloadLoading ? 'disabled' : 'secondary',
      includeHeaderBtnClass: false,
      onClick: function onClick(e) {
        if (isDownloadLoading) {
          return;
        }
        tracking.trackKitdemoDownloadClicked(props.model.id, props.model.title, function () {
          return fetchDownloadLink(e);
        });
      }
    };
  }, [isDownloadLoading, fetchDownloadLink, tracking, props.model.id, props.model.title]);
  var buttons = (0, _react.useMemo)(function () {
    return [downloadButton, applyButton].concat((0, _toConsumableArray2.default)(props.buttons));
  }, [props.buttons, applyButton, downloadButton]);
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, error && /*#__PURE__*/_react.default.createElement(_appUi.Dialog, {
    title: error.message,
    text: __('Go to the pages screen to make sure your kit pages have been imported successfully. If not, try again.', 'elementor'),
    approveButtonText: __('Go to pages', 'elementor'),
    approveButtonColor: "primary",
    approveButtonUrl: elementorAppConfig.admin_url + 'edit.php?post_type=page',
    approveButtonOnClick: function approveButtonOnClick() {
      return setError(false);
    },
    dismissButtonText: __('Got it', 'elementor'),
    dismissButtonOnClick: function dismissButtonOnClick() {
      return setError(false);
    },
    onClose: function onClose() {
      return setError(false);
    }
  }), downloadLinkData && /*#__PURE__*/_react.default.createElement(_kitDialog.default, {
    id: props.model.id,
    downloadLinkData: downloadLinkData,
    onClose: function onClose() {
      return setDownloadLinkData(null);
    }
  }), isConnectDialogOpen && /*#__PURE__*/_react.default.createElement(_connectDialog.default, {
    pageId: props.pageId,
    onClose: function onClose() {
      return setIsConnectDialogOpen(false);
    },
    onSuccess: function onSuccess(data) {
      var accessLevel = data.kits_access_level || data.access_level || 0;
      var accessTier = data.access_tier;
      elementorCommon.config.library_connect.is_connected = true;
      elementorCommon.config.library_connect.current_access_level = accessLevel;
      elementorCommon.config.library_connect.current_access_tier = accessTier;
      elementorCommon.config.library_connect.plan_type = data.plan_type;
      updateSettings({
        is_library_connected: true,
        access_level: accessLevel,
        // BC: Check for 'access_level' prop
        access_tier: accessTier
      });
      if (data.access_level < props.model.accessLevel) {
        return;
      }
      if (!(0, _tiers.isTierAtLeast)(accessTier, props.model.accessTier)) {
        return;
      }
      apply();
    },
    onError: function onError(message) {
      return setError({
        message: message
      });
    }
  }), /*#__PURE__*/_react.default.createElement(_header.default, (0, _extends2.default)({
    startColumn: /*#__PURE__*/_react.default.createElement(_headerBackButton.default, (0, _extends2.default)({}, kitData, {
      kitId: props.model.id
    })),
    centerColumn: props.centerColumn,
    buttons: buttons
  }, kitData)));
}
ItemHeader.propTypes = {
  model: PropTypes.instanceOf(_kit.default).isRequired,
  centerColumn: PropTypes.node,
  buttons: PropTypes.arrayOf(PropTypes.object),
  pageId: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/item-header.scss":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/item-header.scss ***!
  \************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-already-applied-dialog.js":
/*!*************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-already-applied-dialog.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KitAlreadyAppliedDialog;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
function KitAlreadyAppliedDialog(props) {
  var tracking = (0, _trackingContext.useTracking)();
  var getRemoveKitUrl = function getRemoveKitUrl() {
    var elementorToolsUrl = elementorAppConfig['import-export'].tools_url;
    var url = new URL(elementorToolsUrl);
    url.searchParams.append('referrer_kit', props.id);
    url.hash = 'tab-import-export-kit';
    return url.toString();
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Dialog, {
    title: __('You\'ve already applied a Website Templates.', 'elementor'),
    text: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('Applying two Website Templates on the same website will mix global styles and colors and hurt your site\'s performance.', 'elementor'), /*#__PURE__*/_react.default.createElement("br", null), /*#__PURE__*/_react.default.createElement("br", null), __('Remove the existing Website Template before applying a new one.', 'elementor')),
    approveButtonText: __('Remove existing', 'elementor'),
    approveButtonColor: "primary",
    approveButtonOnClick: function approveButtonOnClick() {
      return tracking.trackKitdemoApplyRemoveExisting(true, function () {
        location.href = getRemoveKitUrl();
      });
    },
    dismissButtonText: __('Apply anyway', 'elementor'),
    dismissButtonOnClick: function dismissButtonOnClick() {
      return tracking.trackKitdemoApplyRemoveExisting(false, props.dismissButtonOnClick);
    },
    onClose: props.onClose
  });
}
KitAlreadyAppliedDialog.propTypes = {
  id: PropTypes.string.isRequired,
  dismissButtonOnClick: PropTypes.func.isRequired,
  onClose: PropTypes.func.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-cloud-delete-dialog.js":
/*!**********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-cloud-delete-dialog.js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KitCloudDeleteDialog;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function KitCloudDeleteDialog(_ref) {
  var kit = _ref.kit,
    show = _ref.show,
    onCancelClick = _ref.onCancelClick,
    onDeleteClick = _ref.onDeleteClick,
    isLoading = _ref.isLoading;
  if (!kit || !show) {
    return null;
  }
  var handleDeleteClick = function handleDeleteClick() {
    if (!isLoading) {
      onDeleteClick();
    }
  };
  var handleCancelClick = function handleCancelClick() {
    if (!isLoading) {
      onCancelClick();
    }
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Dialog, {
    title: __('Delete this Website Template?', 'elementor'),
    text: /* Translators: %s: Kit title. */(0, _i18n.sprintf)(__('Removing "%s" will permanently delete this website template from your library.', 'elementor'), (kit === null || kit === void 0 ? void 0 : kit.title) || ''),
    onClose: handleCancelClick,
    dismissButtonText: __('Cancel', 'elementor'),
    dismissButtonOnClick: handleCancelClick,
    approveButtonText: isLoading ? '' : __('Delete', 'elementor'),
    approveButtonOnClick: handleDeleteClick,
    approveButtonColor: "danger"
  });
}
KitCloudDeleteDialog.propTypes = {
  onDeleteClick: _propTypes.default.func.isRequired,
  onCancelClick: _propTypes.default.func.isRequired,
  show: _propTypes.default.bool.isRequired,
  isLoading: _propTypes.default.bool.isRequired,
  kit: _propTypes.default.shape({
    id: _propTypes.default.string,
    title: _propTypes.default.string
  })
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-dialog.js":
/*!*********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-dialog.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KitDialog;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _kitAlreadyAppliedDialog = _interopRequireDefault(__webpack_require__(/*! ./kit-already-applied-dialog */ "../app/modules/kit-library/assets/js/components/kit-already-applied-dialog.js"));
var _applyKitDialog = _interopRequireDefault(__webpack_require__(/*! ./apply-kit-dialog */ "../app/modules/kit-library/assets/js/components/apply-kit-dialog.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function KitDialog(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    applyAnyway = _useState2[0],
    setApplyAnyway = _useState2[1];
  var kitAlreadyApplied = !!elementorAppConfig['import-export'].lastImportedSession.session_id;
  if (kitAlreadyApplied && !applyAnyway) {
    return /*#__PURE__*/_react.default.createElement(_kitAlreadyAppliedDialog.default, {
      id: props.id,
      dismissButtonOnClick: function dismissButtonOnClick() {
        return setApplyAnyway(true);
      },
      onClose: props.onClose
    });
  }
  return /*#__PURE__*/_react.default.createElement(_applyKitDialog.default, {
    id: props.id,
    downloadLink: props.downloadLinkData.data.download_link,
    nonce: props.downloadLinkData.meta.nonce,
    onClose: props.onClose
  });
}
KitDialog.propTypes = {
  id: PropTypes.string.isRequired,
  downloadLinkData: PropTypes.object.isRequired,
  onClose: PropTypes.func.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-list-cloud-item.js":
/*!******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-list-cloud-item.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _useKit = __webpack_require__(/*! ../../../../import-export/assets/js/hooks/use-kit */ "../app/modules/import-export/assets/js/hooks/use-kit.js");
var _tooltip = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/tooltip */ "../app/assets/js/molecules/tooltip.js"));
__webpack_require__(/*! ./kit-list-item.scss */ "../app/modules/kit-library/assets/js/components/kit-list-item.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var PLACEHOLDER_IMAGE_SRC = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzQ1IiBoZWlnaHQ9IjMzMCIgdmlld0JveD0iMCAwIDM0NSAzMzAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIzNDUiIGhlaWdodD0iMzMwIiBmaWxsPSIjRjRGNUY4Ii8+CjxwYXRoIGQ9Ik0xNjQuMjY3IDE2Ny42QzE2Ni40NzIgMTYxLjc2MSAxNzAuMjEzIDE1Ni42MjUgMTc1LjA5NCAxNTIuNzM1QzE3OS45NzUgMTQ4Ljg0NiAxODUuODE2IDE0Ni4zNDYgMTkyIDE0NS41QzE5MS4xNTUgMTUxLjY4NCAxODguNjU0IDE1Ny41MjUgMTg0Ljc2NCAxNjIuNDA2QzE4MC44NzQgMTY3LjI4OCAxNzUuNzM5IDE3MS4wMjggMTY5LjkgMTczLjIzM00xNjkuNDY3IDE1OC41QzE3My42NzcgMTYwLjQ0MyAxNzcuMDU3IDE2My44MjMgMTc5IDE2OC4wMzNNMTUzIDE4NC41VjE3NS44MzNDMTUzIDE3NC4xMTkgMTUzLjUwOCAxNzIuNDQ0IDE1NC40NjEgMTcxLjAxOEMxNTUuNDEzIDE2OS41OTMgMTU2Ljc2NiAxNjguNDgyIDE1OC4zNSAxNjcuODI2QzE1OS45MzQgMTY3LjE3IDE2MS42NzYgMTY2Ljk5OSAxNjMuMzU3IDE2Ny4zMzNDMTY1LjAzOSAxNjcuNjY4IDE2Ni41ODMgMTY4LjQ5MyAxNjcuNzk1IDE2OS43MDVDMTY5LjAwNyAxNzAuOTE3IDE2OS44MzIgMTcyLjQ2MSAxNzAuMTY3IDE3NC4xNDNDMTcwLjUwMSAxNzUuODI0IDE3MC4zMyAxNzcuNTY2IDE2OS42NzQgMTc5LjE1QzE2OS4wMTggMTgwLjczNCAxNjcuOTA3IDE4Mi4wODcgMTY2LjQ4MiAxODMuMDM5QzE2NS4wNTYgMTgzLjk5MiAxNjMuMzgxIDE4NC41IDE2MS42NjcgMTg0LjVIMTUzWiIgc3Ryb2tlPSIjQUJBQkFCIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K';
var PopoverItem = function PopoverItem(_ref) {
  var _ref$className = _ref.className,
    className = _ref$className === void 0 ? '' : _ref$className,
    icon = _ref.icon,
    title = _ref.title,
    onClick = _ref.onClick;
  var handleClick = function handleClick() {
    onClick();
  };
  var handleKeyDown = function handleKeyDown(event) {
    if ('Enter' === event.key || ' ' === event.key) {
      event.preventDefault();
      onClick();
    }
  };
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__kit-item-actions-popover-item ".concat(className),
    role: "button",
    tabIndex: 0,
    onClick: handleClick,
    onKeyDown: handleKeyDown
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: icon
  }), /*#__PURE__*/_react.default.createElement("span", null, title));
};
PopoverItem.propTypes = {
  className: _propTypes.default.string,
  icon: _propTypes.default.string.isRequired,
  title: _propTypes.default.string.isRequired,
  onClick: _propTypes.default.func.isRequired
};
var KitActionsPopover = function KitActionsPopover(_ref2) {
  var isOpen = _ref2.isOpen,
    onClose = _ref2.onClose,
    onDelete = _ref2.onDelete,
    _ref2$className = _ref2.className,
    className = _ref2$className === void 0 ? 'e-kit-library__kit-item-actions-popover' : _ref2$className;
  if (!isOpen) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement(_appUi.Popover, {
    className: className,
    closeFunction: onClose,
    arrowPosition: "none"
  }, /*#__PURE__*/_react.default.createElement(PopoverItem, {
    className: "e-kit-library__kit-item-actions-popover-item--danger",
    icon: "eicon-library-delete",
    title: (0, _i18n.__)('Delete', 'elementor'),
    onClick: onDelete
  }));
};
KitActionsPopover.propTypes = {
  isOpen: _propTypes.default.bool.isRequired,
  onClose: _propTypes.default.func.isRequired,
  onDelete: _propTypes.default.func.isRequired,
  className: _propTypes.default.string
};
var KitListCloudItem = function KitListCloudItem(props) {
  var _props$model;
  var navigate = (0, _router.useNavigate)();
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isPopoverOpen = _useState2[0],
    setIsPopoverOpen = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    imageError = _useState4[0],
    setImageError = _useState4[1];
  var isLocked = 'locked' === ((_props$model = props.model) === null || _props$model === void 0 ? void 0 : _props$model.status);
  var imageSrc = !props.model.thumbnailUrl || imageError ? PLACEHOLDER_IMAGE_SRC : props.model.thumbnailUrl;
  (0, _react.useEffect)(function () {
    setImageError(false);
  }, [props.model.thumbnailUrl]);
  var handleImageError = function handleImageError() {
    if (props.model.thumbnailUrl && !imageError) {
      setImageError(true);
    }
  };
  var handleDelete = function handleDelete() {
    setIsPopoverOpen(false);
    _appsEventTracking.AppsEventTracking.sendKitCloudLibraryDelete(props.model.id);
    props.onDelete();
  };
  var cardContent = /*#__PURE__*/_react.default.createElement(_appUi.Card, {
    className: "e-kit-library__kit-item ".concat(isLocked ? 'e-kit-library__kit-item--locked' : '')
  }, /*#__PURE__*/_react.default.createElement(_appUi.CardHeader, {
    className: "e-kit-library__kit-item-header"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    title: props.model.title,
    variant: "h5",
    className: "eps-card__headline"
  }, isLocked && /*#__PURE__*/_react.default.createElement(_tooltip.default, {
    tag: "span",
    title: (0, _i18n.__)('Your library is currently over the new quota. Upgrade your plan within 90 days to keep all website templates', 'elementor')
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-lock e-kit-library__kit-item-lock-icon",
    "aria-hidden": "true"
  })), props.model.title), /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: (0, _i18n.__)('Actions', 'elementor'),
    hideText: true,
    icon: "eicon-ellipsis-v",
    className: "e-kit-library__kit-item-actions-menu",
    onClick: function onClick(event) {
      event.stopPropagation();
      setIsPopoverOpen(true);
    }
  }), /*#__PURE__*/_react.default.createElement(KitActionsPopover, {
    isOpen: isPopoverOpen,
    onClose: function onClose() {
      return setIsPopoverOpen(false);
    },
    onDelete: handleDelete
  })), /*#__PURE__*/_react.default.createElement(_appUi.CardBody, null, /*#__PURE__*/_react.default.createElement(_appUi.CardImage, {
    alt: props.model.title,
    src: imageSrc,
    onError: handleImageError
  }, /*#__PURE__*/_react.default.createElement(_appUi.CardOverlay, null, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    direction: "column",
    className: "e-kit-library__kit-item-cloud-overlay"
  }, isLocked ? /*#__PURE__*/_react.default.createElement(_tooltip.default, {
    tag: "span",
    title: (0, _i18n.__)('Your library is currently over the new quota. Upgrade your plan within 90 days to keep all website templates', 'elementor')
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-lock e-kit-library__kit-item-lock-icon",
    "aria-hidden": "true"
  })) : /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "eps-button e-kit-library__kit-item-cloud-overlay-import-button eps-button--primary eps-button--sm eps-button--contained",
    text: (0, _i18n.__)('Apply', 'elementor'),
    icon: "eicon-library-download",
    onClick: function onClick() {
      var _elementorCommon;
      _appsEventTracking.AppsEventTracking.sendKitCloudLibraryApply(props.model.id);
      var url = (_elementorCommon = elementorCommon) !== null && _elementorCommon !== void 0 && (_elementorCommon = _elementorCommon.config) !== null && _elementorCommon !== void 0 && _elementorCommon.experimentalFeatures['import-export-customization'] ? "import-customization?referrer=".concat(_useKit.KIT_SOURCE_MAP.CLOUD, "&id=").concat(props.model.id) : "import?referrer=kit-library&source=".concat(_useKit.KIT_SOURCE_MAP.CLOUD, "&kit_id=").concat(props.model.id);
      navigate(url, {
        replace: true
      });
    }
  }))))));
  return cardContent;
};
KitListCloudItem.propTypes = {
  model: _propTypes.default.instanceOf(_kit.default).isRequired,
  index: _propTypes.default.number,
  source: _propTypes.default.string,
  onDelete: _propTypes.default.func.isRequired
};
var _default = exports["default"] = _react.default.memo(KitListCloudItem);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-list-cloud.js":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-list-cloud.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KitListCloud;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _kitListCloudItem = _interopRequireDefault(__webpack_require__(/*! ./kit-list-cloud-item */ "../app/modules/kit-library/assets/js/components/kit-list-cloud-item.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _useKitCloudMutation = __webpack_require__(/*! ../hooks/use-kit-cloud-mutation */ "../app/modules/kit-library/assets/js/hooks/use-kit-cloud-mutation.js");
var _kitCloudDeleteDialog = _interopRequireDefault(__webpack_require__(/*! ./kit-cloud-delete-dialog */ "../app/modules/kit-library/assets/js/components/kit-cloud-delete-dialog.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function KitListCloud(props) {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isDeleteModalOpen = _useState2[0],
    setIsDeleteModalOpen = _useState2[1];
  var _useKitCloudMutations = (0, _useKitCloudMutation.useKitCloudMutations)(),
    remove = _useKitCloudMutations.remove,
    isLoading = _useKitCloudMutations.isLoading;
  var _useState3 = (0, _react.useState)(),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    kit = _useState4[0],
    setKit = _useState4[1];
  var resetKit = (0, _react.useCallback)(function () {
    setKit(null);
    setIsDeleteModalOpen(false);
  }, []);
  var handleDelete = (0, _react.useCallback)(/*#__PURE__*/(0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          _context.prev = 0;
          _context.next = 1;
          return remove.mutate(kit.id);
        case 1:
          _context.prev = 1;
          resetKit();
          return _context.finish(1);
        case 2:
        case "end":
          return _context.stop();
      }
    }, _callee, null, [[0,, 1, 2]]);
  })), [kit, remove, resetKit]);
  return /*#__PURE__*/_react.default.createElement(_appUi.CssGrid, {
    spacing: 24,
    colMinWidth: 290
  }, /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, props.data.map(function (model, index) {
    return /*#__PURE__*/_react.default.createElement(_kitListCloudItem.default, {
      key: model.id,
      model: model,
      index: index,
      source: props.source,
      onDelete: function onDelete() {
        setKit(model);
        setIsDeleteModalOpen(true);
      }
    });
  })), /*#__PURE__*/_react.default.createElement(_kitCloudDeleteDialog.default, {
    kit: kit,
    show: isDeleteModalOpen,
    onDeleteClick: handleDelete,
    onCancelClick: resetKit,
    isLoading: isLoading
  }));
}
KitListCloud.propTypes = {
  data: PropTypes.arrayOf(PropTypes.instanceOf(_kit.default)),
  source: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-list-item.js":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-list-item.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _badge = _interopRequireDefault(__webpack_require__(/*! ./badge */ "../app/modules/kit-library/assets/js/components/badge.js"));
var _favoritesActions = _interopRequireDefault(__webpack_require__(/*! ../components/favorites-actions */ "../app/modules/kit-library/assets/js/components/favorites-actions.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _useKitCallToAction2 = _interopRequireWildcard(__webpack_require__(/*! ../hooks/use-kit-call-to-action */ "../app/modules/kit-library/assets/js/hooks/use-kit-call-to-action.js"));
var _useAddKitPromotionUtm = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-add-kit-promotion-utm */ "../app/modules/kit-library/assets/js/hooks/use-add-kit-promotion-utm.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
__webpack_require__(/*! ./kit-list-item.scss */ "../app/modules/kit-library/assets/js/components/kit-list-item.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var KitListItem = function KitListItem(props) {
  var _useKitCallToAction = (0, _useKitCallToAction2.default)(props.model.accessTier),
    type = _useKitCallToAction.type,
    subscriptionPlan = _useKitCallToAction.subscriptionPlan;
  var promotionUrl = (0, _useAddKitPromotionUtm.default)(subscriptionPlan.promotion_url, props.model.id, props.model.title);
  var ctaText = (0, _i18n.__)('Upgrade', 'elementor');
  var showPromotion = _useKitCallToAction2.TYPE_PROMOTION === type;
  var tracking = (0, _trackingContext.useTracking)();
  var eventTracking = function eventTracking(command) {
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      kit_name: props.model.title,
      grid_location: props.index,
      search_term: props.queryParams,
      page_source: props.source && '/' === props.source ? 'all kits' : 'favorites'
    });
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Card, {
    className: "e-kit-library__kit-item"
  }, /*#__PURE__*/_react.default.createElement(_appUi.CardHeader, null, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    title: props.model.title,
    variant: "h5",
    className: "eps-card__headline"
  }, props.model.title), /*#__PURE__*/_react.default.createElement(_favoritesActions.default, {
    id: props.model.id,
    isFavorite: props.model.isFavorite,
    index: props.index,
    name: props.model.title,
    queryParams: props.queryParams,
    source: props.source
  })), /*#__PURE__*/_react.default.createElement(_appUi.CardBody, null, /*#__PURE__*/_react.default.createElement(_appUi.CardImage, {
    alt: props.model.title,
    src: props.model.thumbnailUrl || ''
  }, /*#__PURE__*/_react.default.createElement(_badge.default, {
    variant: "sm",
    className: "e-kit-library__kit-item-subscription-plan-badge ".concat(subscriptionPlan.isPromoted ? 'promoted' : '')
  }, subscriptionPlan.label), /*#__PURE__*/_react.default.createElement(_appUi.CardOverlay, null, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    direction: "column",
    className: "e-kit-library__kit-item-overlay"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__kit-item-overlay-overview-button",
    text: (0, _i18n.__)('View Demo', 'elementor'),
    icon: "eicon-preview-medium",
    url: "/kit-library/preview/".concat(props.model.id),
    onClick: function onClick() {
      eventTracking('kit-library/check-out-kit');
      tracking.trackKitdemoClicked(props.model.id, props.model.title, props.index, subscriptionPlan.label);
    }
  }), showPromotion && /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__kit-item-overlay-promotion-button",
    text: ctaText,
    icon: "eicon-external-link-square",
    url: promotionUrl,
    target: "_blank",
    onClick: function onClick() {
      return tracking.trackKitdemoUpgradeClicked(props.model.id, props.model.title, subscriptionPlan.label);
    }
  }))))));
};
KitListItem.propTypes = {
  model: PropTypes.instanceOf(_kit.default).isRequired,
  index: PropTypes.number,
  queryParams: PropTypes.string,
  source: PropTypes.string
};
var _default = exports["default"] = _react.default.memo(KitListItem);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-list-item.scss":
/*!**************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-list-item.scss ***!
  \**************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/kit-list.js":
/*!*******************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/kit-list.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KitList;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _kitListItem = _interopRequireDefault(__webpack_require__(/*! ./kit-list-item */ "../app/modules/kit-library/assets/js/components/kit-list-item.js"));
var _newPageKitListItem = _interopRequireDefault(__webpack_require__(/*! ../../../../onboarding/assets/js/components/new-page-kit-list-item */ "../app/modules/onboarding/assets/js/components/new-page-kit-list-item.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
function KitList(props) {
  var _location$pathname$sp;
  var location = (0, _router.useLocation)();
  var referrer = new URLSearchParams((_location$pathname$sp = location.pathname.split('?')) === null || _location$pathname$sp === void 0 ? void 0 : _location$pathname$sp[1]).get('referrer');
  return /*#__PURE__*/_react.default.createElement(_appUi.CssGrid, {
    spacing: 24,
    colMinWidth: 290
  }, 'onboarding' === referrer && /*#__PURE__*/_react.default.createElement(_newPageKitListItem.default, null), props.data.map(function (model, index) {
    var _props$queryParams;
    return (
      /*#__PURE__*/
      // The + 1 was added in order to start the map.index from 1 and not from 0.
      _react.default.createElement(_kitListItem.default, {
        key: model.id,
        model: model,
        index: index + 1,
        queryParams: (_props$queryParams = props.queryParams) === null || _props$queryParams === void 0 ? void 0 : _props$queryParams.search,
        source: props.source
      })
    );
  }));
}
KitList.propTypes = {
  data: PropTypes.arrayOf(PropTypes.instanceOf(_kit.default)),
  queryParams: PropTypes.shape({
    search: PropTypes.string
  }),
  source: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/layout/header-back-button.js":
/*!************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/layout/header-back-button.js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = HeaderBackButton;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _lastFilterContext = __webpack_require__(/*! ../../context/last-filter-context */ "../app/modules/kit-library/assets/js/context/last-filter-context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
__webpack_require__(/*! ./header-back-button.scss */ "../app/modules/kit-library/assets/js/components/layout/header-back-button.scss");
function HeaderBackButton(props) {
  var navigate = (0, _router.useNavigate)(),
    _useLastFilterContext = (0, _lastFilterContext.useLastFilterContext)(),
    lastFilter = _useLastFilterContext.lastFilter,
    eventTracking = function eventTracking(command) {
      var eventType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'click';
      (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
        page_source: props.pageId,
        kit_name: props.kitName,
        element_position: 'app_header',
        event_type: eventType
      });
    },
    tracking = (0, _trackingContext.useTracking)();
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__header-back-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__header-back",
    icon: "eicon-chevron-left",
    text: __('Back to Library', 'elementor'),
    onClick: function onClick() {
      eventTracking('kit-library/back-to-library');
      tracking.trackKitdemoOverviewBack(props.kitId, props.kitName, function () {
        return navigate(wp.url.addQueryArgs('/kit-library', lastFilter));
      });
    }
  }));
}
HeaderBackButton.propTypes = {
  pageId: PropTypes.string.isRequired,
  kitName: PropTypes.string.isRequired,
  kitId: PropTypes.string.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/layout/header-back-button.scss":
/*!**************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/layout/header-back-button.scss ***!
  \**************************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/layout/header.js":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/layout/header.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Header;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _headerButtons = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/header-buttons */ "../app/assets/js/layout/header-buttons.js"));
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
function Header(props) {
  var eventTracking = function eventTracking(command) {
      var source = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'home page';
      var kitName = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var eventType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
      return (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
        page_source: source,
        element_position: 'app_header',
        kit_name: kitName,
        event_type: eventType
      });
    },
    onClose = function onClose() {
      eventTracking('kit-library/close', props === null || props === void 0 ? void 0 : props.pageId, props === null || props === void 0 ? void 0 : props.kitName);
      window.top.location = elementorAppConfig.admin_url;
    };
  return /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "space-between",
    className: "eps-app__header"
  }, props.startColumn || /*#__PURE__*/_react.default.createElement("a", {
    className: "eps-app__logo-title-wrapper",
    href: "#/kit-library",
    onClick: function onClick() {
      return eventTracking('kit-library/logo');
    }
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eps-app__logo eicon-elementor"
  }), /*#__PURE__*/_react.default.createElement("h1", {
    className: "eps-app__title"
  }, __('Website Templates', 'elementor'))), props.centerColumn || /*#__PURE__*/_react.default.createElement("span", null), props.endColumn || /*#__PURE__*/_react.default.createElement("div", {
    style: {
      flex: 1
    }
  }, /*#__PURE__*/_react.default.createElement(_headerButtons.default, {
    buttons: props.buttons,
    onClose: onClose
  })));
}
Header.propTypes = {
  startColumn: PropTypes.node,
  endColumn: PropTypes.node,
  centerColumn: PropTypes.node,
  buttons: PropTypes.arrayOf(PropTypes.object),
  kitName: PropTypes.string,
  pageId: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/layout/index.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/layout/index.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Index;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _sidebar = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/sidebar */ "../app/assets/js/layout/sidebar.js"));
function Index(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__lightbox"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app"
  }, props.header, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-app__main"
  }, props.sidebar && /*#__PURE__*/_react.default.createElement(_sidebar.default, null, props.sidebar), props.children)));
}
Index.propTypes = {
  header: PropTypes.node,
  sidebar: PropTypes.node,
  children: PropTypes.node
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/page-loader.js":
/*!**********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/page-loader.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PageLoader;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
__webpack_require__(/*! ./page-loader.scss */ "../app/modules/kit-library/assets/js/components/page-loader.scss");
function PageLoader(props) {
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__page-loader ".concat(props.className)
  }, /*#__PURE__*/_react.default.createElement(_appUi.Icon, {
    className: "eicon-loading eicon-animation-spin"
  }));
}
PageLoader.propTypes = {
  className: PropTypes.string
};
PageLoader.defaultProps = {
  className: ''
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/page-loader.scss":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/page-loader.scss ***!
  \************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/quota-bar.js":
/*!********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/quota-bar.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = QuotaBar;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
__webpack_require__(/*! ./quota-bar.scss */ "../app/modules/kit-library/assets/js/components/quota-bar.scss");
var QUOTA_BAR_CLASSNAME = 'e-kit-library__quota-bar';
var BYTES_TO_GB = 1024 * 1024 * 1024;
var BYTES_TO_MB = 1024 * 1024;
var convertBytesToGB = function convertBytesToGB(bytes) {
  return Math.round(bytes / BYTES_TO_GB * 100) / 100; // Round to 2 decimal places
};
var convertBytesToMB = function convertBytesToMB(bytes) {
  return Math.round(bytes / BYTES_TO_MB * 100) / 100; // Round to 2 decimal places
};
var formatDisplayValues = function formatDisplayValues(used, total, unit) {
  if ('B' === unit) {
    var totalInGB = convertBytesToGB(total);
    var usedInGB = convertBytesToGB(used);
    var usedInMB = convertBytesToMB(used);
    if (used === total) {
      return {
        used: usedInGB,
        usedUnit: 'GB',
        total: totalInGB,
        totalUnit: 'GB'
      };
    }
    if (usedInGB < 1) {
      return {
        used: usedInMB,
        usedUnit: 'MB',
        total: totalInGB,
        totalUnit: 'GB'
      };
    }
    return {
      used: usedInGB,
      usedUnit: 'GB',
      total: totalInGB,
      totalUnit: 'GB'
    };
  }
  return {
    used: used,
    usedUnit: unit,
    total: total,
    totalUnit: unit
  };
};
function QuotaBar(_ref) {
  var _ref$used = _ref.used,
    used = _ref$used === void 0 ? 0 : _ref$used,
    _ref$total = _ref.total,
    total = _ref$total === void 0 ? 15 : _ref$total,
    _ref$unit = _ref.unit,
    unit = _ref$unit === void 0 ? 'GB' : _ref$unit,
    _ref$label = _ref.label,
    label = _ref$label === void 0 ? 'Storage' : _ref$label;
  var displayValues = formatDisplayValues(used, total, unit);
  var usagePercentage = total > 0 ? Math.min(used / total * 100, 100) : 0;
  var getUsageState = function getUsageState() {
    if (0 === usagePercentage) {
      return 'empty';
    }
    if (usagePercentage >= 100) {
      return 'alert';
    }
    if (usagePercentage >= 80) {
      return 'warning';
    }
    return 'normal';
  };
  var getProgressBarClass = function getProgressBarClass() {
    var state = getUsageState();
    return "".concat(QUOTA_BAR_CLASSNAME, "__progress-bar ").concat(QUOTA_BAR_CLASSNAME, "__progress-bar--").concat(state);
  };
  var getProgressContainerClass = function getProgressContainerClass() {
    var state = getUsageState();
    return "".concat(QUOTA_BAR_CLASSNAME, "__progress-container ").concat(QUOTA_BAR_CLASSNAME, "__progress-container--").concat(state);
  };
  var shouldShowUpgradeMessage = function shouldShowUpgradeMessage() {
    var state = getUsageState();
    return 'warning' === state || 'alert' === state;
  };
  var getUsageText = function getUsageText() {
    var state = getUsageState();
    if ('warning' === state || 'alert' === state) {
      return "".concat(label, ": ").concat(Math.round(usagePercentage), "%");
    }
    return label;
  };
  return /*#__PURE__*/_react.default.createElement("div", {
    className: QUOTA_BAR_CLASSNAME
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__container")
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__header")
  }, /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__label"),
    variant: "xs",
    tag: "span"
  }, getUsageText()), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__count"),
    variant: "xs",
    tag: "span"
  }, displayValues.used, " ", displayValues.usedUnit, " ", __('of', 'elementor'), " ", displayValues.total, " ", displayValues.totalUnit)), /*#__PURE__*/_react.default.createElement("div", {
    className: getProgressContainerClass()
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: getProgressBarClass(),
    style: {
      width: "".concat(usagePercentage, "%")
    }
  })), shouldShowUpgradeMessage() && /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__upgrade-message")
  }, /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    variant: "xs",
    tag: "span"
  }, __('To get more space', 'elementor')), /*#__PURE__*/_react.default.createElement("a", {
    className: "".concat(QUOTA_BAR_CLASSNAME, "__upgrade-link"),
    href: "https://go.elementor.com/go-pro-cloud-website-templates-library-advanced/",
    target: "_blank",
    rel: "noopener noreferrer"
  }, __('Upgrade now', 'elementor')))));
}
QuotaBar.propTypes = {
  used: _propTypes.default.number,
  total: _propTypes.default.number,
  unit: _propTypes.default.string,
  label: _propTypes.default.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/quota-bar.scss":
/*!**********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/quota-bar.scss ***!
  \**********************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/quota-notification.js":
/*!*****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/quota-notification.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var sprintf = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["sprintf"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = QuotaNotification;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
__webpack_require__(/*! ./quota-notification.scss */ "../app/modules/kit-library/assets/js/components/quota-notification.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var QUOTA_NOTIFICATION_CLASSNAME = 'e-kit-library__quota-notification';
function QuotaNotification(_ref) {
  var usagePercentage = _ref.usagePercentage;
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isDismissed = _useState2[0],
    setIsDismissed = _useState2[1];
  var handleDismiss = function handleDismiss() {
    setIsDismissed(true);
  };
  var getNotificationState = function getNotificationState() {
    if (usagePercentage >= 100) {
      return 'alert';
    }
    if (usagePercentage >= 80) {
      return 'warning';
    }
    return null;
  };
  var getNotificationContent = function getNotificationContent() {
    var state = getNotificationState();
    if ('alert' === state) {
      return {
        icon: 'eicon-alert',
        message: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("strong", null, __('Website template storage is full.', 'elementor')), ' ', __('Get more space ', 'elementor')),
        actions: [{
          text: __('Upgrade now', 'elementor'),
          href: 'https://go.elementor.com/go-pro-cloud-website-templates-library-advanced/',
          type: 'link'
        }]
      };
    }
    if ('warning' === state) {
      return {
        icon: 'eicon-warning-full',
        message: /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement("strong", null, /* Translators: %s: usage percentage */
        sprintf(__('Website template storage is %1$s%% full.', 'elementor'), Math.round(usagePercentage))), ' ', __('Get more space ', 'elementor')),
        actions: [{
          text: __('Upgrade now', 'elementor'),
          href: 'https://go.elementor.com/go-pro-cloud-website-templates-library-advanced/',
          type: 'link'
        }]
      };
    }
    return null;
  };
  var state = getNotificationState();
  var content = getNotificationContent();
  if (!state || !content || isDismissed) {
    return null;
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, " ").concat(QUOTA_NOTIFICATION_CLASSNAME, "--").concat(state)
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, "__content")
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, "__icon ").concat(content.icon)
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, "__message")
  }, /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    tag: "span"
  }, content.message), content.actions.map(function (action, index) {
    return /*#__PURE__*/_react.default.createElement("span", {
      key: index
    }, index > 0 && /*#__PURE__*/_react.default.createElement(_appUi.Text, {
      tag: "span"
    }, " ", __('or', 'elementor'), " "), /*#__PURE__*/_react.default.createElement("a", {
      className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, "__action-link"),
      href: action.href,
      target: "_blank",
      rel: "noopener noreferrer"
    }, action.text));
  }))), /*#__PURE__*/_react.default.createElement("button", {
    className: "".concat(QUOTA_NOTIFICATION_CLASSNAME, "__dismiss"),
    onClick: handleDismiss,
    "aria-label": __('Dismiss notification', 'elementor')
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-close"
  })));
}
QuotaNotification.propTypes = {
  usagePercentage: _propTypes.default.number.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/quota-notification.scss":
/*!*******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/quota-notification.scss ***!
  \*******************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/search-input.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/search-input.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SearchInput;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _useDebouncedCallback = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-debounced-callback */ "../app/modules/kit-library/assets/js/hooks/use-debounced-callback.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
__webpack_require__(/*! ./search-input.scss */ "../app/modules/kit-library/assets/js/components/search-input.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SearchInput(props) {
  var _useState = (0, _react.useState)(props.value || ''),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    localValue = _useState2[0],
    setLocalValue = _useState2[1];
  var debouncedOnChange = (0, _useDebouncedCallback.default)(function (value) {
    return props.onChange(value);
  }, props.debounceTimeout);
  (0, _react.useEffect)(function () {
    if (props.value !== localValue) {
      setLocalValue(props.value);
    }
  }, [props.value]);
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-search-input__container ".concat(props.className)
  }, /*#__PURE__*/_react.default.createElement("input", {
    className: "eps-search-input eps-search-input--".concat(props.size),
    placeholder: props.placeholder,
    value: localValue,
    onChange: function onChange(e) {
      setLocalValue(e.target.value);
      debouncedOnChange(e.target.value);
    }
  }), /*#__PURE__*/_react.default.createElement(_appUi.Icon, {
    className: "eicon-search-bold eps-search-input__icon eps-search-input__icon--".concat(props.size)
  }), props.value && /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: __('Clear', 'elementor'),
    hideText: true,
    className: "eicon-close-circle eps-search-input__clear-icon eps-search-input__clear-icon--".concat(props.size),
    onClick: function onClick() {
      return props.onChange('');
    }
  }));
}
SearchInput.propTypes = {
  placeholder: PropTypes.string,
  value: PropTypes.string.isRequired,
  onChange: PropTypes.func.isRequired,
  className: PropTypes.string,
  size: PropTypes.oneOf(['md', 'sm']),
  debounceTimeout: PropTypes.number
};
SearchInput.defaultProps = {
  className: '',
  size: 'md',
  debounceTimeout: 300
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/search-input.scss":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/search-input.scss ***!
  \*************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/sort-select.js":
/*!**********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/sort-select.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = SortSelect;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
__webpack_require__(/*! ./sort-select.scss */ "../app/modules/kit-library/assets/js/components/sort-select.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function SortSelect(props) {
  var getSelectedOptionDetails = function getSelectedOptionDetails(value) {
    return props.options.find(function (option) {
      return option.value === value;
    });
  };
  var _useState = (0, _react.useState)(getSelectedOptionDetails(props.value.by)),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    selectedSortBy = _useState2[0],
    setSelectedSortBy = _useState2[1];
  (0, _react.useEffect)(function () {
    var _selectedSortBy$defau;
    props.onChange({
      by: selectedSortBy.value,
      direction: (_selectedSortBy$defau = selectedSortBy.defaultOrder) !== null && _selectedSortBy$defau !== void 0 ? _selectedSortBy$defau : props.value.direction
    });
  }, [selectedSortBy]);
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-sort-select"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "eps-sort-select__select-wrapper"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Select, {
    options: props.options,
    value: props.value.by,
    onChange: function onChange(e) {
      var _props$onChangeSortVa;
      var value = e.target.value;
      setSelectedSortBy(getSelectedOptionDetails(value));
      (_props$onChangeSortVa = props.onChangeSortValue) === null || _props$onChangeSortVa === void 0 || _props$onChangeSortVa.call(props, value);
    },
    className: "eps-sort-select__select",
    onClick: function onClick() {
      var _props$onSortSelectOp;
      props.onChange({
        by: props.value.by,
        direction: props.value.direction
      });
      (_props$onSortSelectOp = props.onSortSelectOpen) === null || _props$onSortSelectOp === void 0 || _props$onSortSelectOp.call(props);
    }
  })), !selectedSortBy.orderDisabled && /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: 'asc' === props.value.direction ? __('Sort Descending', 'elementor') : __('Sort Ascending', 'elementor'),
    hideText: true,
    icon: 'asc' === props.value.direction ? 'eicon-arrow-up' : 'eicon-arrow-down',
    className: "eps-sort-select__button",
    onClick: function onClick() {
      var direction = props.value.direction && 'asc' === props.value.direction ? 'desc' : 'asc';
      if (props.onChangeSortDirection) {
        props.onChangeSortDirection(direction);
      }
      props.onChange({
        by: props.value.by,
        direction: direction
      });
    }
  }));
}
SortSelect.propTypes = {
  options: PropTypes.arrayOf(PropTypes.shape({
    label: PropTypes.string.isRequired,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired
  })).isRequired,
  value: PropTypes.shape({
    direction: PropTypes.oneOf(['asc', 'desc']).isRequired,
    by: PropTypes.string.isRequired
  }).isRequired,
  onChange: PropTypes.func.isRequired,
  onChangeSortValue: PropTypes.func,
  onSortSelectOpen: PropTypes.func,
  onChangeSortDirection: PropTypes.func
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/sort-select.scss":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/sort-select.scss ***!
  \************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/tags-filter.scss":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/tags-filter.scss ***!
  \************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/taxonomies-filter-list.js":
/*!*********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/taxonomies-filter-list.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _taxonomy = _interopRequireDefault(__webpack_require__(/*! ../models/taxonomy */ "../app/modules/kit-library/assets/js/models/taxonomy.js"));
var _collapse = _interopRequireDefault(__webpack_require__(/*! ./collapse */ "../app/modules/kit-library/assets/js/components/collapse.js"));
var _searchInput = _interopRequireDefault(__webpack_require__(/*! ./search-input */ "../app/modules/kit-library/assets/js/components/search-input.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var MIN_TAGS_LENGTH_FOR_SEARCH_INPUT = 15;
var TaxonomiesFilterList = function TaxonomiesFilterList(props) {
  var _useState = (0, _react.useState)(props.taxonomiesByType.isOpenByDefault),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isOpen = _useState2[0],
    setIsOpen = _useState2[1];
  var _useState3 = (0, _react.useState)(''),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    search = _useState4[0],
    setSearch = _useState4[1];
  var tracking = (0, _trackingContext.useTracking)();
  var taxonomies = (0, _react.useMemo)(function () {
    if (!search) {
      return props.taxonomiesByType.data;
    }
    var lowerCaseSearch = search.toLowerCase();
    return props.taxonomiesByType.data.filter(function (tag) {
      return tag.text.toLowerCase().includes(lowerCaseSearch);
    });
  }, [props.taxonomiesByType.data, search]);
  var eventTracking = function eventTracking(command, section, action, item) {
    var category = props.category && ('/favorites' === props.category ? 'favorites' : 'all kits');
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      page_source: 'home page',
      element_location: 'app_sidebar',
      category: category,
      section: section,
      item: item,
      action: action ? 'checked' : 'unchecked'
    });
  };
  return /*#__PURE__*/_react.default.createElement(_collapse.default, {
    className: "e-kit-library__tags-filter-list",
    title: props.taxonomiesByType.label,
    isOpen: isOpen,
    onChange: setIsOpen,
    onClick: function onClick(collapseState, title) {
      var _props$onCollapseChan;
      (_props$onCollapseChan = props.onCollapseChange) === null || _props$onCollapseChan === void 0 || _props$onCollapseChan.call(props, collapseState, title);
    }
  }, props.taxonomiesByType.data.length >= MIN_TAGS_LENGTH_FOR_SEARCH_INPUT && /*#__PURE__*/_react.default.createElement(_searchInput.default, {
    size: "sm",
    className: "e-kit-library__tags-filter-list-search"
    // Translators: %s is the taxonomy type.
    ,
    placeholder: (0, _i18n.sprintf)(__('Search %s...', 'elementor'), props.taxonomiesByType.label),
    value: search,
    onChange: function onChange(searchTerm) {
      setSearch(searchTerm);
      if (searchTerm) {
        var _props$onChange;
        (_props$onChange = props.onChange) === null || _props$onChange === void 0 || _props$onChange.call(props, searchTerm);
      }
    }
  }), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__tags-filter-list-container"
  }, 0 === taxonomies.length && /*#__PURE__*/_react.default.createElement(_appUi.Text, null, __('No Results Found', 'elementor')), taxonomies.map(function (taxonomy) {
    var _props$selected$taxon;
    return (
      /*#__PURE__*/
      // eslint-disable-next-line jsx-a11y/label-has-associated-control
      _react.default.createElement("label", {
        key: taxonomy.text,
        className: "e-kit-library__tags-filter-list-item"
      }, /*#__PURE__*/_react.default.createElement(_appUi.Checkbox, {
        checked: !!((_props$selected$taxon = props.selected[taxonomy.type]) !== null && _props$selected$taxon !== void 0 && _props$selected$taxon.includes(taxonomy.id || taxonomy.text)),
        onChange: function onChange(e) {
          var checked = e.target.checked;
          var callback = function callback() {
            props.onSelect(taxonomy.type, function (prev) {
              return checked ? [].concat((0, _toConsumableArray2.default)(prev), [taxonomy.id || taxonomy.text]) : prev.filter(function (tagId) {
                return ![taxonomy.id, taxonomy.text].includes(tagId);
              });
            });
          };
          eventTracking('kit-library/filter', taxonomy.type, checked, taxonomy.text);
          if ('categories' === taxonomy.type && checked) {
            tracking.trackKitlibCategorySelected(taxonomy.text, callback);
          } else if ('tags' === taxonomy.type && checked) {
            tracking.trackKitlibTagSelected(taxonomy.text, callback);
          } else if ('subscription_plans' === taxonomy.type && checked) {
            tracking.trackKitlibPlanFilterSelected(taxonomy.text, callback);
          } else {
            callback();
          }
        }
      }), taxonomy.text)
    );
  })));
};
TaxonomiesFilterList.propTypes = {
  taxonomiesByType: PropTypes.shape({
    key: PropTypes.string,
    label: PropTypes.string,
    data: PropTypes.arrayOf(PropTypes.instanceOf(_taxonomy.default)),
    isOpenByDefault: PropTypes.bool
  }),
  selected: PropTypes.objectOf(PropTypes.arrayOf(PropTypes.string)),
  onSelect: PropTypes.func,
  onCollapseChange: PropTypes.func,
  category: PropTypes.string,
  onChange: PropTypes.func
};
var _default = exports["default"] = _react.default.memo(TaxonomiesFilterList);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/components/taxonomies-filter.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/components/taxonomies-filter.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = TaxonomiesFilter;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _taxonomiesFilterList = _interopRequireDefault(__webpack_require__(/*! ./taxonomies-filter-list */ "../app/modules/kit-library/assets/js/components/taxonomies-filter-list.js"));
var _taxonomy = _interopRequireDefault(__webpack_require__(/*! ../models/taxonomy */ "../app/modules/kit-library/assets/js/models/taxonomy.js"));
var _taxonomyTransformer = __webpack_require__(/*! ../models/taxonomy-transformer */ "../app/modules/kit-library/assets/js/models/taxonomy-transformer.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./tags-filter.scss */ "../app/modules/kit-library/assets/js/components/tags-filter.scss");
var _React = _react.default,
  useMemo = _React.useMemo;
function TaxonomiesFilter(props) {
  var taxonomiesByType = useMemo(function () {
      return (0, _taxonomyTransformer.getTaxonomyFilterItems)(props.taxonomies);
    }, [props.taxonomies]),
    eventTracking = function eventTracking(command, search, section) {
      var eventType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
      return (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
        page_source: 'home page',
        element_location: 'app_sidebar',
        category: props.category && ('/favorites' === props.category ? 'favorites' : 'all kits'),
        section: section,
        search_term: search,
        event_type: eventType
      });
    };
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__tags-filter"
  }, taxonomiesByType.map(function (group) {
    return /*#__PURE__*/_react.default.createElement(_taxonomiesFilterList.default, {
      key: group.key,
      taxonomiesByType: group,
      selected: props.selected,
      onSelect: props.onSelect,
      onCollapseChange: function onCollapseChange(collapseState, title) {
        var command = collapseState ? 'kit-library/collapse' : 'kit-library/expand';
        eventTracking(command, null, title);
      },
      onChange: function onChange(search) {
        eventTracking('kit-library/filter', search, group.label, 'search');
      },
      category: props.category
    });
  }));
}
TaxonomiesFilter.propTypes = {
  selected: PropTypes.objectOf(PropTypes.arrayOf(PropTypes.string)),
  onSelect: PropTypes.func,
  taxonomies: PropTypes.arrayOf(PropTypes.instanceOf(_taxonomy.default)),
  category: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/context/connect-state-context.js":
/*!*****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/context/connect-state-context.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ConnectStateContext = void 0;
exports.ConnectStateProvider = ConnectStateProvider;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var ConnectStateContext = exports.ConnectStateContext = (0, _react.createContext)();
function ConnectStateProvider(_ref) {
  var children = _ref.children;
  var _useState = (0, _react.useState)(elementorCommon.config.library_connect.is_connected),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isConnected = _useState2[0],
    setIsConnected = _useState2[1];
  var _useState3 = (0, _react.useState)(false),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    isConnecting = _useState4[0],
    setIsConnecting = _useState4[1];
  var handleConnectSuccess = (0, _react.useCallback)(function (callback) {
    setIsConnecting(true);
    setIsConnected(true);
    elementorCommon.config.library_connect.is_connected = true;
    if (callback) {
      callback();
    }
  }, []);
  var handleConnectError = (0, _react.useCallback)(function (callback) {
    setIsConnected(false);
    setIsConnecting(false);
    elementorCommon.config.library_connect.is_connected = false;
    if (callback) {
      callback();
    }
  }, []);
  var setConnecting = (0, _react.useCallback)(function (connecting) {
    setIsConnecting(connecting);
  }, []);
  var value = {
    isConnected: isConnected,
    isConnecting: isConnecting,
    setConnecting: setConnecting,
    handleConnectSuccess: handleConnectSuccess,
    handleConnectError: handleConnectError
  };
  return /*#__PURE__*/_react.default.createElement(ConnectStateContext.Provider, {
    value: value
  }, children);
}
ConnectStateProvider.propTypes = {
  children: _propTypes.default.node.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/context/last-filter-context.js":
/*!***************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/context/last-filter-context.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.LastFilterProvider = LastFilterProvider;
exports.useLastFilterContext = useLastFilterContext;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var LastFilterContext = (0, _react.createContext)({});

/**
 * Consume the context
 *
 * @return {{}} context value
 */
function useLastFilterContext() {
  return (0, _react.useContext)(LastFilterContext);
}

/**
 * Settings Provider
 *
 * @param {*} props
 * @return {JSX.Element} element
 * @function Object() { [native code] }
 */
function LastFilterProvider(props) {
  var _useState = (0, _react.useState)({}),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    lastFilter = _useState2[0],
    setLastFilter = _useState2[1];
  return /*#__PURE__*/_react.default.createElement(LastFilterContext.Provider, {
    value: {
      lastFilter: lastFilter,
      setLastFilter: setLastFilter
    }
  }, props.children);
}
LastFilterProvider.propTypes = {
  children: PropTypes.any
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/context/settings-context.js":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/context/settings-context.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.SettingsProvider = SettingsProvider;
exports.useSettingsContext = useSettingsContext;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var SettingsContext = (0, _react.createContext)({});

/**
 * Consume the context
 *
 * @return {{emptyTrashDays: number}} context value
 */
function useSettingsContext() {
  return (0, _react.useContext)(SettingsContext);
}

/**
 * Settings Provider
 *
 * @param {*} props
 * @return {JSX.Element} element
 * @function Object() { [native code] }
 */
function SettingsProvider(props) {
  var _useState = (0, _react.useState)({}),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    settings = _useState2[0],
    setSettings = _useState2[1];
  var updateSettings = (0, _react.useCallback)(function (newSettings) {
    setSettings(function (prev) {
      return _objectSpread(_objectSpread({}, prev), newSettings);
    });
  }, [setSettings]);
  (0, _react.useEffect)(function () {
    setSettings(props.value);
  }, [setSettings]);
  return /*#__PURE__*/_react.default.createElement(SettingsContext.Provider, {
    value: {
      settings: settings,
      setSettings: setSettings,
      updateSettings: updateSettings
    }
  }, props.children);
}
SettingsProvider.propTypes = {
  children: PropTypes.any,
  value: PropTypes.object.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/context/tracking-context.js":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/context/tracking-context.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useTracking = exports["default"] = exports.TrackingProvider = void 0;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _useKitLibraryTracking = _interopRequireDefault(__webpack_require__(/*! ../hooks/use-kit-library-tracking */ "../app/modules/kit-library/assets/js/hooks/use-kit-library-tracking.js"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var TrackingContext = (0, _react.createContext)();
var TrackingProvider = exports.TrackingProvider = function TrackingProvider(_ref) {
  var children = _ref.children;
  var tracking = (0, _useKitLibraryTracking.default)();
  (0, _react.useEffect)(function () {
    var urlParams = new URLSearchParams(window.location.search);
    var source = urlParams.get('source') || 'direct';
    tracking.trackKitlibOpened(source);
  }, []);
  return /*#__PURE__*/_react.default.createElement(TrackingContext.Provider, {
    value: tracking
  }, children);
};
var useTracking = exports.useTracking = function useTracking() {
  var context = (0, _react.useContext)(TrackingContext);
  if (!context) {
    throw new Error('useTracking must be used within a TrackingProvider');
  }
  return context;
};
var _default = exports["default"] = TrackingContext;
TrackingProvider.propTypes = {
  children: _propTypes.default.node
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-add-kit-promotion-utm.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-add-kit-promotion-utm.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useAddKitPromotionUTM;
function useAddKitPromotionUTM(promotionUrl, kitId, kitTitle) {
  if (!promotionUrl) {
    return '';
  }
  var url;
  try {
    url = new URL(promotionUrl);
  } catch (e) {
    return '';
  }
  if (kitTitle && 'string' === typeof kitTitle) {
    var cleanTitle = kitTitle.trim().replace(/\s+/g, '-').replace(/[^\w-]/g, '').toLowerCase();
    url.searchParams.set('utm_term', cleanTitle);
  }
  if (kitId && 'string' === typeof kitId) {
    url.searchParams.set('utm_content', kitId);
  }
  return url.toString();
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-cloud-kits.js":
/*!********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-cloud-kits.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useCloudKits;
exports.defaultQueryParams = void 0;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _react = __webpack_require__(/*! react */ "react");
var _utils = __webpack_require__(/*! ../utils */ "../app/modules/kit-library/assets/js/utils.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var KEY = exports.KEY = 'cloud-kits';

/**
 * The default query params
 *
 * @type {Object}
 */
var defaultQueryParams = exports.defaultQueryParams = {
  search: '',
  referrer: null
};
var kitsPipeFunctions = {
  /**
   * Filter by search term.
   *
   * @param {Array<*>} data
   * @param {*}        queryParams
   * @return {Array} filtered data
   */
  searchFilter: function searchFilter(data, queryParams) {
    if (!queryParams.search) {
      return data;
    }
    return data.filter(function (item) {
      var keywords = [item.title];
      var searchTerm = queryParams.search.toLowerCase();
      return keywords.some(function (keyword) {
        return keyword.toLowerCase().includes(searchTerm);
      });
    });
  }
};

/**
 * Fetch kits
 *
 * @return {*} kits
 */
function fetchKits() {
  return $e.data.get('cloud-kits/index', {}, {
    refresh: true
  }).then(function (response) {
    return response.data;
  }).then(function (_ref) {
    var data = _ref.data;
    return data.map(function (item) {
      return _kit.default.createFromResponse(item);
    });
  });
}

/**
 * Main function.
 *
 * @param {*} initialQueryParams
 * @return {Object} query
 */
function useCloudKits() {
  var initialQueryParams = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    force = _useState2[0],
    setForce = _useState2[1];
  var _useState3 = (0, _react.useState)(function () {
      return _objectSpread(_objectSpread({
        ready: false
      }, defaultQueryParams), initialQueryParams);
    }),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    queryParams = _useState4[0],
    setQueryParams = _useState4[1];
  var forceRefetch = (0, _react.useCallback)(function () {
    return setForce(true);
  }, [setForce]);
  var clearQueryParams = (0, _react.useCallback)(function () {
    return setQueryParams(_objectSpread(_objectSpread({
      ready: true
    }, defaultQueryParams), initialQueryParams));
  }, [setQueryParams]);
  var query = (0, _reactQuery.useQuery)([KEY], function () {
    return fetchKits(force);
  });
  var data = (0, _react.useMemo)(function () {
    return !query.data ? [] : _utils.pipe.apply(void 0, (0, _toConsumableArray2.default)(Object.values(kitsPipeFunctions)))((0, _toConsumableArray2.default)(query.data), queryParams);
  }, [query.data, queryParams]);
  var isFilterActive = (0, _react.useMemo)(function () {
    return !!queryParams.search;
  }, [queryParams]);
  (0, _react.useEffect)(function () {
    if (!force) {
      return;
    }
    query.refetch().then(function () {
      return setForce(false);
    });
  }, [force]);
  return _objectSpread(_objectSpread({}, query), {}, {
    data: data,
    queryParams: queryParams,
    setQueryParams: setQueryParams,
    clearQueryParams: clearQueryParams,
    forceRefetch: forceRefetch,
    isFilterActive: isFilterActive
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-connect-state.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-connect-state.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useConnectState;
var _react = __webpack_require__(/*! react */ "react");
var _connectStateContext = __webpack_require__(/*! ../context/connect-state-context */ "../app/modules/kit-library/assets/js/context/connect-state-context.js");
function useConnectState() {
  var context = (0, _react.useContext)(_connectStateContext.ConnectStateContext);
  if (!context) {
    throw new Error('useConnectState must be used within a ConnectStateProvider');
  }
  return context;
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-content-types.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-content-types.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useContentTypes;
var _contentType = _interopRequireDefault(__webpack_require__(/*! ../models/content-type */ "../app/modules/kit-library/assets/js/models/content-type.js"));
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _settingsContext = __webpack_require__(/*! ../context/settings-context */ "../app/modules/kit-library/assets/js/context/settings-context.js");
var _tiers = __webpack_require__(/*! elementor-utils/tiers */ "../assets/dev/js/utils/tiers.js");
var KEY = exports.KEY = 'content-types';

/**
 * The data should come from the server, this is a temp solution that helps to demonstrate that data comes from the server
 * but for now this is a local data.
 *
 * @return {import('react-query').UseQueryResult<Promise.constructor, unknown>} result
 */
function useContentTypes() {
  var _useSettingsContext = (0, _settingsContext.useSettingsContext)(),
    settings = _useSettingsContext.settings;
  return (0, _reactQuery.useQuery)([KEY, settings], function () {
    return fetchContentTypes(settings);
  });
}

/**
 * @param {Object} settings - Current settings
 *
 * @return {Promise.constructor} content types
 */
function fetchContentTypes(settings) {
  var contentTypes = [{
    id: 'page',
    label: __('Pages', 'elementor'),
    doc_types: ['wp-page'],
    order: 0
  }, {
    id: 'site-parts',
    label: __('Site Parts', 'elementor'),
    doc_types: ['archive', 'error-404', 'footer', 'header', 'search-results', 'single-page', 'single-post',
    // WooCommerce types
    'product', 'product-archive',
    // Legacy Types
    '404', 'single'],
    order: 1
  }];

  // BC: When user has old Pro version which doesn't override the `free` access_tier.
  var userAccessTier = settings.access_tier;
  var hasActiveProLicense = settings.is_pro && settings.is_library_connected;
  var shouldFallbackToLegacy = hasActiveProLicense && userAccessTier === _tiers.TIERS.free;

  // Fallback to the last access_tier before the new tiers were introduced.
  // TODO: Remove when Pro with the new tiers is stable.
  if (shouldFallbackToLegacy) {
    userAccessTier = _tiers.TIERS['essential-oct2023'];
  }
  var tierThatSupportsPopups = _tiers.TIERS['essential-oct2023'];
  if ((0, _tiers.isTierAtLeast)(userAccessTier, tierThatSupportsPopups)) {
    contentTypes.push({
      id: 'popup',
      label: __('Popups', 'elementor'),
      doc_types: ['popup'],
      order: 2
    });
  }
  return Promise.resolve(contentTypes).then(function (data) {
    return data.map(function (contentType) {
      return _contentType.default.createFromResponse(contentType);
    });
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-debounced-callback.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-debounced-callback.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


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

/***/ "../app/modules/kit-library/assets/js/hooks/use-download-link-mutation.js":
/*!********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-download-link-mutation.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useDownloadLinkMutation;
var _react = __webpack_require__(/*! react */ "react");
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
function useDownloadLinkMutation(model, _ref) {
  var onError = _ref.onError,
    onSuccess = _ref.onSuccess;
  var downloadLink = (0, _react.useCallback)(function () {
    return $e.data.get('kits/download-link', {
      id: model.id
    }, {
      refresh: true
    });
  }, [model]);
  return (0, _reactQuery.useMutation)(downloadLink, {
    onSuccess: onSuccess,
    onError: onError
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit-call-to-action.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit-call-to-action.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.TYPE_PROMOTION = exports.TYPE_CONNECT = exports.TYPE_APPLY = void 0;
exports["default"] = useKitCallToAction;
var _react = __webpack_require__(/*! react */ "react");
var _settingsContext = __webpack_require__(/*! ../context/settings-context */ "../app/modules/kit-library/assets/js/context/settings-context.js");
var _tiers = __webpack_require__(/*! elementor-utils/tiers */ "../assets/dev/js/utils/tiers.js");
var _taxonomyTransformer = __webpack_require__(/*! ../models/taxonomy-transformer */ "../app/modules/kit-library/assets/js/models/taxonomy-transformer.js");
var TYPE_CONNECT = exports.TYPE_CONNECT = 'connect';
var TYPE_PROMOTION = exports.TYPE_PROMOTION = 'promotion';
var TYPE_APPLY = exports.TYPE_APPLY = 'apply';
function useKitCallToAction(kitAccessTier) {
  var _useSettingsContext = (0, _settingsContext.useSettingsContext)(),
    settings = _useSettingsContext.settings;

  // BC: When user has old Pro version which doesn't override the `free` access_tier.
  var userAccessTier = settings.access_tier;
  var tierKey = _taxonomyTransformer.TierToKeyMap[kitAccessTier];
  var hasActiveProLicense = settings.is_pro && settings.is_library_connected;
  var shouldFallbackToLegacy = hasActiveProLicense && userAccessTier === _tiers.TIERS.free;

  // Fallback to the last access_tier before the new tiers were introduced.
  // TODO: Remove when Pro with the new tiers is stable.
  if (shouldFallbackToLegacy) {
    userAccessTier = _tiers.TIERS['essential-oct2023'];
  }

  // SubscriptionPlan can be null when the context is not filled (can be happened when using back button in the browser.)
  var subscriptionPlan = (0, _react.useMemo)(function () {
    var _settings$subscriptio;
    return (_settings$subscriptio = settings.subscription_plans) === null || _settings$subscriptio === void 0 ? void 0 : _settings$subscriptio[kitAccessTier];
  }, [settings, kitAccessTier]);
  subscriptionPlan.label = _taxonomyTransformer.PromotionChipText[tierKey];
  subscriptionPlan.isPromoted = _tiers.TIERS.free !== kitAccessTier;
  var type = (0, _react.useMemo)(function () {
    // The user can apply this kit (the user access level is equal or greater then the kit access level).
    var isAuthorizeToApplyKit = (0, _tiers.isTierAtLeast)(userAccessTier, kitAccessTier);

    // The user in not connected and has pro plugin or the kit is a free kit.
    if (!settings.is_library_connected && (settings.is_pro || isAuthorizeToApplyKit)) {
      return TYPE_CONNECT;
    }

    // The user is connected or has only core plugin and cannot access this kit.
    if (!isAuthorizeToApplyKit) {
      return TYPE_PROMOTION;
    }

    // The user is connected and can access the kit.
    return TYPE_APPLY;
  }, [settings, kitAccessTier]);
  return {
    type: type,
    subscriptionPlan: subscriptionPlan
  };
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit-cloud-mutation.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit-cloud-mutation.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useKitCloudMutations = useKitCloudMutations;
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _useCloudKits = __webpack_require__(/*! ../hooks/use-cloud-kits */ "../app/modules/kit-library/assets/js/hooks/use-cloud-kits.js");
var _useCloudKitsQuota = __webpack_require__(/*! elementor-app/hooks/use-cloud-kits-quota */ "../app/assets/js/hooks/use-cloud-kits-quota.js");
function useKitCloudMutations() {
  var queryClient = (0, _reactQuery.useQueryClient)();
  var remove = (0, _reactQuery.useMutation)(function (id) {
    return $e.data.delete('cloud-kits/index', {
      id: id
    });
  }, {
    onSuccess: function onSuccess() {
      queryClient.invalidateQueries(_useCloudKits.KEY);
      queryClient.invalidateQueries(_useCloudKitsQuota.KEY);
    }
  });
  return {
    remove: remove,
    isLoading: remove.isLoading
  };
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit-document-by-type.js":
/*!******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit-document-by-type.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useKitDocumentByType;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _useContentTypes = _interopRequireDefault(__webpack_require__(/*! ./use-content-types */ "../app/modules/kit-library/assets/js/hooks/use-content-types.js"));
var _react = __webpack_require__(/*! react */ "react");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function useKitDocumentByType(kit) {
  var contentTypesQuery = (0, _useContentTypes.default)();
  var data = (0, _react.useMemo)(function () {
    if (!kit || !contentTypesQuery.data) {
      return [];
    }
    return kit.getDocumentsByTypes(contentTypesQuery.data).sort(function (a, b) {
      return a.order - b.order;
    });
  }, [kit, contentTypesQuery.data]);
  return _objectSpread(_objectSpread({}, contentTypesQuery), {}, {
    data: data
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit-favorites-mutations.js":
/*!*********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit-favorites-mutations.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useKitFavoritesMutations = useKitFavoritesMutations;
var _react = __webpack_require__(/*! react */ "react");
var _useKits = __webpack_require__(/*! ../hooks/use-kits */ "../app/modules/kit-library/assets/js/hooks/use-kits.js");
var _useKit = __webpack_require__(/*! ../hooks/use-kit */ "../app/modules/kit-library/assets/js/hooks/use-kit.js");
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
function useKitFavoritesMutations() {
  var queryClient = (0, _reactQuery.useQueryClient)();
  var onSuccess = (0, _react.useCallback)(function (_ref) {
    var data = _ref.data;
    var id = data.data.id;
    var isFavorite = data.data.is_favorite;

    // Update the kit list if the list exists.
    if (queryClient.getQueryData([_useKits.KEY])) {
      queryClient.setQueryData([_useKits.KEY], function (kits) {
        if (!kits) {
          return kits;
        }
        return kits.map(function (item) {
          if (item.id === id) {
            item.isFavorite = isFavorite;

            // Should return a new kit to trigger rerender.
            return item.clone();
          }
          return item;
        });
      });
    }

    // Update specific kit if the kit exists
    if (queryClient.getQueryData([_useKit.KEY, id])) {
      queryClient.setQueryData([_useKit.KEY, id], function (currentKit) {
        currentKit.isFavorite = isFavorite;

        // Should return a new kit to trigger rerender.
        return currentKit.clone();
      });
    }
  }, [queryClient]);
  var addToFavorites = (0, _reactQuery.useMutation)(function (id) {
    return $e.data.create('kits/favorites', {}, {
      id: id
    });
  }, {
    onSuccess: onSuccess
  });
  var removeFromFavorites = (0, _reactQuery.useMutation)(function (id) {
    return $e.data.delete('kits/favorites', {
      id: id
    });
  }, {
    onSuccess: onSuccess
  });
  return {
    addToFavorites: addToFavorites,
    removeFromFavorites: removeFromFavorites,
    isLoading: addToFavorites.isLoading || removeFromFavorites.isLoading
  };
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit-library-tracking.js":
/*!******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit-library-tracking.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.useKitLibraryTracking = exports["default"] = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _react = __webpack_require__(/*! react */ "react");
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var SESSION_TIMEOUT_MINUTES = 30;
var SESSION_TIMEOUT = SESSION_TIMEOUT_MINUTES * 60 * 1000;
var useKitLibraryTracking = exports.useKitLibraryTracking = function useKitLibraryTracking() {
  var sessionStartTime = (0, _react.useRef)(Date.now());
  var lastActivityTime = (0, _react.useRef)(Date.now());
  var sessionEndedRef = (0, _react.useRef)(false);
  var actionsCount = (0, _react.useRef)(0);
  var filtersCount = (0, _react.useRef)(0);
  var demoViews = (0, _react.useRef)(0);
  var kitApplied = (0, _react.useRef)(false);
  var _ref = elementorCommon || {},
    _ref$config = _ref.config,
    config = _ref$config === void 0 ? {} : _ref$config;
  var _config$editor_events = config.editor_events,
    editorEvents = _config$editor_events === void 0 ? {} : _config$editor_events;
  var _editorEvents$can_sen = editorEvents.can_send_events,
    canSendEvents = _editorEvents$can_sen === void 0 ? false : _editorEvents$can_sen;
  var isEventsManagerAvailable = (0, _react.useCallback)(function () {
    var _elementorCommon;
    return ((_elementorCommon = elementorCommon) === null || _elementorCommon === void 0 ? void 0 : _elementorCommon.eventsManager) && 'function' === typeof elementorCommon.eventsManager.dispatchEvent;
  }, []);
  var trackMixpanelEvent = (0, _react.useCallback)(function (eventName) {
    var properties = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    try {
      if (canSendEvents && isEventsManagerAvailable()) {
        elementorCommon.eventsManager.dispatchEvent(eventName, properties);
      }
    } finally {
      if (callback) {
        callback();
      }
    }
  }, [canSendEvents, isEventsManagerAvailable]);
  var updateActivity = (0, _react.useCallback)(function () {
    lastActivityTime.current = Date.now();
    sessionEndedRef.current = false;
  }, []);
  var addTriggerToProperties = (0, _react.useCallback)(function (properties, trigger) {
    if (!trigger) {
      return properties;
    }
    return _objectSpread(_objectSpread({}, properties), {}, {
      trigger: _eventsConfig.default.triggers[trigger] || trigger
    });
  }, []);
  var trackWithActivity = (0, _react.useCallback)(function (eventName, properties) {
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    updateActivity();
    actionsCount.current += 1;
    trackMixpanelEvent(eventName, properties, callback);
  }, [updateActivity, trackMixpanelEvent]);
  var trackKitlibOpened = (0, _react.useCallback)(function (source) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var properties = addTriggerToProperties({
      referrer_area: source
    }, trigger);
    trackWithActivity('kitlib_opened', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibCategorySelected = (0, _react.useCallback)(function (kitCategory) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    filtersCount.current += 1;
    var properties = addTriggerToProperties({
      kit_category: kitCategory
    }, trigger);
    trackWithActivity('kitlib_category_selected', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibTagSelected = (0, _react.useCallback)(function (kitTag) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    filtersCount.current += 1;
    var properties = addTriggerToProperties({
      kit_tag: kitTag
    }, trigger);
    trackWithActivity('kitlib_tag_selected', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibPlanFilterSelected = (0, _react.useCallback)(function (planType) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    filtersCount.current += 1;
    var properties = addTriggerToProperties({
      kit_plan_filter: planType
    }, trigger);
    trackWithActivity('kitlib_plan_filter_selected', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibSorterSelected = (0, _react.useCallback)(function (sortType) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'dropdownClick';
    var properties = addTriggerToProperties({
      kit_sorter: sortType
    }, trigger);
    trackWithActivity('kitlib_sorter_selected', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibSearchSubmitted = (0, _react.useCallback)(function (searchTerm) {
    var resultsCount = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var trigger = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var properties = addTriggerToProperties({
      kit_search_input: searchTerm,
      kit_search_result_count: resultsCount
    }, trigger);
    trackWithActivity('kitlib_search_submitted', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibFavoriteClicked = (0, _react.useCallback)(function (kitId, title, favorited) {
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title,
      kit_fav_status: favorited
    }, trigger);
    trackWithActivity('kitlib_favorite_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibFavoriteTab = (0, _react.useCallback)(function () {
    var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var trigger = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'click';
    var properties = addTriggerToProperties({
      page_url: window.location.href
    }, trigger);
    trackWithActivity('kitlib_favorite_tab', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoClicked = (0, _react.useCallback)(function (kitId, title) {
    var position = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var plan = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';
    var callback = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    var trigger = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title,
      kit_position: position,
      requires_pro: plan
    }, trigger);
    trackWithActivity('kitdemo_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoOpened = (0, _react.useCallback)(function (kitId, title) {
    var loadTime = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 'pageLoaded';
    demoViews.current += 1;
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title,
      kit_load_time: loadTime
    }, trigger);
    trackWithActivity('kitdemo_opened', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoApplyClicked = (0, _react.useCallback)(function (kitId, title) {
    var plan = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 'click';
    kitApplied.current = true;
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title,
      requires_pro: plan
    }, trigger);
    trackWithActivity('kitdemo_apply_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoApplyRemoveExisting = (0, _react.useCallback)(function (userChoice) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var properties = addTriggerToProperties({
      remove_existing_kit: userChoice
    }, trigger);
    trackWithActivity('kitdemo_apply_remove_existing', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoApplyAllOrCustomize = (0, _react.useCallback)(function (userChoice) {
    var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var trigger = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    var properties = addTriggerToProperties({
      apply_all: userChoice
    }, trigger);
    trackWithActivity('kitdemo_apply_all_or_customize', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoApplyCompleted = (0, _react.useCallback)(function (kitId) {
    var importTime = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var itemsImported = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    var properties = addTriggerToProperties({
      kit_id: kitId,
      import_time: importTime,
      items_imported: itemsImported
    }, trigger);
    trackWithActivity('kitdemo_apply_completed', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoApplyFailed = (0, _react.useCallback)(function (kitId) {
    var errorMessage = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var errorCode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    var properties = addTriggerToProperties({
      kit_id: kitId,
      error_message: errorMessage,
      error_code: errorCode
    }, trigger);
    trackWithActivity('kitdemo_apply_failed', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoDownloadClicked = (0, _react.useCallback)(function (kitId, title) {
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var trigger = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title
    }, trigger);
    trackWithActivity('kitdemo_download_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoUpgradeClicked = (0, _react.useCallback)(function (kitId, title) {
    var plan = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
    var callback = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var trigger = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title,
      kit_demo_upgrade_plan: plan
    }, trigger);
    trackWithActivity('kitdemo_upgrade_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoOverviewClicked = (0, _react.useCallback)(function (kitId, title) {
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var trigger = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title
    }, trigger);
    trackWithActivity('kitdemo_overview_clicked', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitdemoOverviewBack = (0, _react.useCallback)(function (kitId, title) {
    var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var trigger = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
    var properties = addTriggerToProperties({
      kit_id: kitId,
      kit_title: title
    }, trigger);
    trackWithActivity('kitdemo_overview_back', properties, callback);
  }, [addTriggerToProperties, trackWithActivity]);
  var trackKitlibSessionEnded = (0, _react.useCallback)(function () {
    var reason = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'timeout';
    if (sessionEndedRef.current) {
      return;
    }
    sessionEndedRef.current = true;
    var durationMs = Date.now() - sessionStartTime.current;
    var durationSeconds = Number((durationMs / 1000).toFixed(2));
    trackMixpanelEvent('kitlib_session_ended', {
      duration_s: durationSeconds,
      actions_count: actionsCount.current,
      filters_count: filtersCount.current,
      demo_views: demoViews.current,
      kit_applied: kitApplied.current,
      reason: reason
    });
  }, [trackMixpanelEvent]);

  // Session timeout monitoring
  (0, _react.useEffect)(function () {
    var checkSessionTimeout = function checkSessionTimeout() {
      var timeSinceLastActivity = Date.now() - lastActivityTime.current;
      if (timeSinceLastActivity > SESSION_TIMEOUT && !sessionEndedRef.current) {
        trackKitlibSessionEnded('timeout');
      }
    };
    var interval = setInterval(checkSessionTimeout, 60000); // Check every minute

    // Track session end on page unload
    var handleBeforeUnload = function handleBeforeUnload() {
      trackKitlibSessionEnded('page_unload');
    };
    window.addEventListener('beforeunload', handleBeforeUnload);
    return function () {
      clearInterval(interval);
      window.removeEventListener('beforeunload', handleBeforeUnload);
    };
  }, [trackKitlibSessionEnded]);

  // Track activity on user interactions
  (0, _react.useEffect)(function () {
    var handleUserActivity = function handleUserActivity() {
      updateActivity();
    };
    var events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    events.forEach(function (event) {
      document.addEventListener(event, handleUserActivity, true);
    });
    return function () {
      events.forEach(function (event) {
        document.removeEventListener(event, handleUserActivity, true);
      });
    };
  }, [updateActivity]);
  return {
    // Library events
    trackKitlibOpened: trackKitlibOpened,
    trackKitlibCategorySelected: trackKitlibCategorySelected,
    trackKitlibTagSelected: trackKitlibTagSelected,
    trackKitlibPlanFilterSelected: trackKitlibPlanFilterSelected,
    trackKitlibSorterSelected: trackKitlibSorterSelected,
    trackKitlibSearchSubmitted: trackKitlibSearchSubmitted,
    trackKitlibFavoriteClicked: trackKitlibFavoriteClicked,
    trackKitlibFavoriteTab: trackKitlibFavoriteTab,
    // Demo/Preview events
    trackKitdemoClicked: trackKitdemoClicked,
    trackKitdemoOpened: trackKitdemoOpened,
    trackKitdemoApplyClicked: trackKitdemoApplyClicked,
    trackKitdemoApplyRemoveExisting: trackKitdemoApplyRemoveExisting,
    trackKitdemoApplyAllOrCustomize: trackKitdemoApplyAllOrCustomize,
    trackKitdemoApplyCompleted: trackKitdemoApplyCompleted,
    trackKitdemoApplyFailed: trackKitdemoApplyFailed,
    trackKitdemoDownloadClicked: trackKitdemoDownloadClicked,
    trackKitdemoUpgradeClicked: trackKitdemoUpgradeClicked,
    trackKitdemoOverviewClicked: trackKitdemoOverviewClicked,
    trackKitdemoOverviewBack: trackKitdemoOverviewBack,
    // Session events
    trackKitlibSessionEnded: trackKitlibSessionEnded,
    // Utility
    updateActivity: updateActivity
  };
};
var _default = exports["default"] = useKitLibraryTracking;

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kit.js":
/*!*************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kit.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useKit;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _useKits = __webpack_require__(/*! ./use-kits */ "../app/modules/kit-library/assets/js/hooks/use-kits.js");
var _react = __webpack_require__(/*! react */ "react");
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var KEY = exports.KEY = 'kit';
function useKit(id) {
  // A function that returns existing data from the kit list for a placeholder data before the kit request will resolved.
  var placeholderDataCallback = usePlaceholderDataCallback(id);
  return (0, _reactQuery.useQuery)([KEY, id], fetchKitItem, {
    placeholderData: placeholderDataCallback
  });
}

/**
 * Return placeholder function for kit query.
 *
 * @param {*} id
 * @return {function(): (undefined|*)} placeholder
 */
function usePlaceholderDataCallback(id) {
  var queryClient = (0, _reactQuery.useQueryClient)();
  return (0, _react.useCallback)(function () {
    var _queryClient$getQuery;
    var placeholder = (_queryClient$getQuery = queryClient.getQueryData(_useKits.KEY)) === null || _queryClient$getQuery === void 0 ? void 0 : _queryClient$getQuery.find(function (kit) {
      return kit.id === id;
    });
    if (!placeholder) {
      return;
    }
    return placeholder;
  }, [queryClient, id]);
}

/**
 * Fetch kit
 *
 * @param {Object} root0
 * @param {Object} root0.queryKey
 * @param {*}      root0.queryKey.0
 * @param {string} root0.queryKey.1
 * @return {Promise<Kit>} kit
 */
// eslint-disable-next-line no-unused-vars
function fetchKitItem(_ref) {
  var _ref$queryKey = (0, _slicedToArray2.default)(_ref.queryKey, 2),
    _ = _ref$queryKey[0],
    id = _ref$queryKey[1];
  return $e.data.get('kits/index', {
    id: id
  }, {
    refresh: true
  }).then(function (response) {
    return response.data;
  }).then(function (_ref2) {
    var data = _ref2.data;
    return _kit.default.createFromResponse(data);
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-kits.js":
/*!**************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-kits.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useKits;
exports.defaultQueryParams = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _useSelectedTaxonomies = _interopRequireDefault(__webpack_require__(/*! ./use-selected-taxonomies */ "../app/modules/kit-library/assets/js/hooks/use-selected-taxonomies.js"));
var _taxonomy = __webpack_require__(/*! ../models/taxonomy */ "../app/modules/kit-library/assets/js/models/taxonomy.js");
var _taxonomyTransformer = __webpack_require__(/*! ../models/taxonomy-transformer */ "../app/modules/kit-library/assets/js/models/taxonomy-transformer.js");
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _react = __webpack_require__(/*! react */ "react");
var _utils = __webpack_require__(/*! ../utils */ "../app/modules/kit-library/assets/js/utils.js");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var KEY = exports.KEY = 'kits';

/**
 * The default query params
 *
 * @type {Object}
 */
var defaultQueryParams = exports.defaultQueryParams = {
  favorite: false,
  search: '',
  taxonomies: _taxonomy.TaxonomyTypes.reduce(function (current, _ref) {
    var key = _ref.key;
    return _objectSpread(_objectSpread({}, current), {}, (0, _defineProperty2.default)({}, key, []));
  }, {}),
  order: {
    direction: 'asc',
    by: 'featuredIndex'
  },
  referrer: null
};
var kitsPipeFunctions = {
  /**
   * Filter by favorite
   *
   * @param {Array<*>} data
   * @param {*}        queryParams
   * @return {Array} filtered data
   */
  favoriteFilter: function favoriteFilter(data, queryParams) {
    if (!queryParams.favorite) {
      return data;
    }
    return data.filter(function (item) {
      return item.isFavorite;
    });
  },
  /**
   * Filter by search term.
   *
   * @param {Array<*>} data
   * @param {*}        queryParams
   * @return {Array} filtered data
   */
  searchFilter: function searchFilter(data, queryParams) {
    if (!queryParams.search) {
      return data;
    }
    return data.filter(function (item) {
      var keywords = [].concat((0, _toConsumableArray2.default)(item.keywords), (0, _toConsumableArray2.default)(item.taxonomies), [item.title]);
      var searchTerm = queryParams.search.toLowerCase();
      return keywords.some(function (keyword) {
        return keyword.toLowerCase().includes(searchTerm);
      });
    });
  },
  /**
   * Filter by taxonomies.
   * In each taxonomy type it use the OR operator and between types it uses the AND operator.
   *
   * @param {Array<*>} data
   * @param {*}        queryParams
   * @return {Array} filtered data
   */
  taxonomiesFilter: function taxonomiesFilter(data, queryParams) {
    var taxonomyTypes = Object.keys(queryParams.taxonomies).filter(function (taxonomyType) {
      return queryParams.taxonomies[taxonomyType].length;
    });
    return !taxonomyTypes.length ? data : data.filter(function (kit) {
      return taxonomyTypes.some(function (taxonomyType) {
        return (0, _taxonomyTransformer.isKitInTaxonomy)(kit, taxonomyType, queryParams.taxonomies[taxonomyType]);
      });
    });
  },
  /**
   * Sort all the data by the "order" query param
   *
   * @param {Array<*>} data
   * @param {*}        queryParams
   * @return {Array} sorted data
   */
  sort: function sort(data, queryParams) {
    var order = queryParams.order;
    return data.sort(function (item1, item2) {
      if ('asc' === order.direction) {
        return item1[order.by] - item2[order.by];
      }
      return item2[order.by] - item1[order.by];
    });
  }
};

/**
 * Fetch kits
 *
 * @param {boolean} force
 * @return {*} kits
 */
function fetchKits(force) {
  return $e.data.get('kits/index', {
    force: force ? 1 : undefined
  }, {
    refresh: true
  }).then(function (response) {
    return response.data;
  }).then(function (_ref2) {
    var data = _ref2.data;
    return data.map(function (item) {
      return _kit.default.createFromResponse(item);
    });
  });
}

/**
 * Main function.
 *
 * @param {*} initialQueryParams
 * @return {Object} query
 */
function useKits() {
  var initialQueryParams = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    force = _useState2[0],
    setForce = _useState2[1];
  var _useState3 = (0, _react.useState)(function () {
      return _objectSpread(_objectSpread({
        ready: false
      }, defaultQueryParams), initialQueryParams);
    }),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    queryParams = _useState4[0],
    setQueryParams = _useState4[1];
  var forceRefetch = (0, _react.useCallback)(function () {
    return setForce(true);
  }, [setForce]);
  var clearQueryParams = (0, _react.useCallback)(function () {
    return setQueryParams(_objectSpread(_objectSpread({
      ready: true
    }, defaultQueryParams), initialQueryParams));
  }, [setQueryParams]);
  var query = (0, _reactQuery.useQuery)([KEY], function () {
    return fetchKits(force);
  });
  var data = (0, _react.useMemo)(function () {
    return !query.data ? [] : _utils.pipe.apply(void 0, (0, _toConsumableArray2.default)(Object.values(kitsPipeFunctions)))((0, _toConsumableArray2.default)(query.data), queryParams);
  }, [query.data, queryParams]);
  var selectedTaxonomies = (0, _useSelectedTaxonomies.default)(queryParams.taxonomies);
  var isFilterActive = (0, _react.useMemo)(function () {
    return !!queryParams.search || !!selectedTaxonomies.length;
  }, [queryParams]);
  (0, _react.useEffect)(function () {
    if (!force) {
      return;
    }
    query.refetch().then(function () {
      return setForce(false);
    });
  }, [force]);
  return _objectSpread(_objectSpread({}, query), {}, {
    data: data,
    queryParams: queryParams,
    setQueryParams: setQueryParams,
    clearQueryParams: clearQueryParams,
    forceRefetch: forceRefetch,
    isFilterActive: isFilterActive
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-menu-items.js":
/*!********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-menu-items.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useMenuItems;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _useCloudKitsEligibility = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-cloud-kits-eligibility */ "../app/assets/js/hooks/use-cloud-kits-eligibility.js"));
var _useConnectState2 = _interopRequireDefault(__webpack_require__(/*! ./use-connect-state */ "../app/modules/kit-library/assets/js/hooks/use-connect-state.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/**
 * Generate the menu items for the kit library pages.
 *
 * @param {string} path - The current page path
 * @return {Array} menu items
 */
function useMenuItems(path) {
  var _useConnectState = (0, _useConnectState2.default)(),
    isConnected = _useConnectState.isConnected;
  var _useCloudKitsEligibil = (0, _useCloudKitsEligibility.default)({
      enabled: isConnected
    }),
    cloudKitsData = _useCloudKitsEligibil.data;
  var isCloudKitsAvailable = cloudKitsData === null || cloudKitsData === void 0 ? void 0 : cloudKitsData.is_eligible;
  return (0, _react.useMemo)(function () {
    var page = path.replace('/', '');
    var myWebsiteTemplatesLabel = __('My Website Templates', 'elementor');
    if (!isConnected) {
      myWebsiteTemplatesLabel = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('My Website Templates', 'elementor'), /*#__PURE__*/_react.default.createElement("span", {
        className: "connect-badge"
      }, __('Connect', 'elementor')));
    } else if (isConnected && false === isCloudKitsAvailable) {
      myWebsiteTemplatesLabel = /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, __('My Website Templates', 'elementor'), /*#__PURE__*/_react.default.createElement("span", {
        className: "upgrade-badge"
      }, __('Upgrade', 'elementor')));
    }
    var menuItems = [{
      label: __('All Website Templates', 'elementor'),
      icon: 'eicon-filter',
      isActive: !page,
      url: '/kit-library',
      trackEventData: {
        command: 'kit-library/select-organizing-category',
        category: 'all'
      }
    }, {
      label: myWebsiteTemplatesLabel,
      icon: 'eicon-library-cloud-empty',
      isActive: 'cloud' === page,
      url: '/kit-library/cloud',
      trackEventData: {
        command: 'kit-library/select-organizing-category',
        category: 'cloud'
      }
    }, {
      label: __('Favorites', 'elementor'),
      icon: 'eicon-heart-o',
      isActive: 'favorites' === page,
      url: '/kit-library/favorites',
      trackEventData: {
        command: 'kit-library/select-organizing-category',
        category: 'favorites'
      }
    }];
    return menuItems;
  }, [path, isConnected, isCloudKitsAvailable]);
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-selected-taxonomies.js":
/*!*****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-selected-taxonomies.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = useSelectedTaxonomies;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _react = __webpack_require__(/*! react */ "react");
function useSelectedTaxonomies(taxonomiesFilter) {
  return (0, _react.useMemo)(function () {
    return Object.values(taxonomiesFilter).reduce(function (current, groupedTaxonomies) {
      return [].concat((0, _toConsumableArray2.default)(current), (0, _toConsumableArray2.default)(groupedTaxonomies));
    });
  }, [taxonomiesFilter]);
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/hooks/use-taxonomies.js":
/*!********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/hooks/use-taxonomies.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.KEY = void 0;
exports["default"] = useTaxonomies;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _taxonomy = _interopRequireDefault(__webpack_require__(/*! ../models/taxonomy */ "../app/modules/kit-library/assets/js/models/taxonomy.js"));
var _reactQuery = __webpack_require__(/*! react-query */ "../node_modules/react-query/es/index.js");
var _react = __webpack_require__(/*! react */ "react");
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var KEY = exports.KEY = 'tags';
function useTaxonomies() {
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    force = _useState2[0],
    setForce = _useState2[1];
  var forceRefetch = (0, _react.useCallback)(function () {
    return setForce(true);
  }, [setForce]);
  var query = (0, _reactQuery.useQuery)([KEY], function () {
    return fetchTaxonomies(force);
  });
  (0, _react.useEffect)(function () {
    if (!force) {
      return;
    }
    query.refetch().then(function () {
      return setForce(false);
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [force]);
  return _objectSpread(_objectSpread({}, query), {}, {
    forceRefetch: forceRefetch
  });
}
function fetchTaxonomies(force) {
  return $e.data.get('kit-taxonomies/index', {
    force: force ? 1 : undefined
  }, {
    refresh: true
  }).then(function (response) {
    return response.data;
  }).then(function (_ref) {
    var data = _ref.data;
    return data.map(function (taxonomy) {
      return _taxonomy.default.createFromResponse(taxonomy);
    });
  });
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/base-model.js":
/*!*****************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/base-model.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var BaseModel = exports["default"] = /*#__PURE__*/function () {
  function BaseModel() {
    (0, _classCallCheck2.default)(this, BaseModel);
  }
  return (0, _createClass2.default)(BaseModel, [{
    key: "clone",
    value:
    /**
     * Clone to object to avoid changing the reference.
     *
     * @return {BaseModel} cloned model
     */
    function clone() {
      var _this = this;
      var instance = new this.constructor();
      Object.keys(this).forEach(function (key) {
        instance[key] = _this[key];
      });
      return instance;
    }

    /**
     * Using init and not the default constructor because there is a problem to fill the instance
     * dynamically in the constructor.
     *
     * @param {*} data
     * @return {BaseModel} model
     */
  }, {
    key: "init",
    value: function init() {
      var _this2 = this;
      var data = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      Object.entries(data).forEach(function (_ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];
        _this2[key] = value;
      });
      return this;
    }
  }]);
}();

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/content-type.js":
/*!*******************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/content-type.js ***!
  \*******************************************************************/
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
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseModel = _interopRequireDefault(__webpack_require__(/*! ./base-model */ "../app/modules/kit-library/assets/js/models/base-model.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ContentType = exports["default"] = /*#__PURE__*/function (_BaseModel) {
  function ContentType() {
    var _this;
    (0, _classCallCheck2.default)(this, ContentType);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, ContentType, [].concat(args));
    (0, _defineProperty2.default)(_this, "id", '');
    (0, _defineProperty2.default)(_this, "label", '');
    (0, _defineProperty2.default)(_this, "documentTypes", []);
    (0, _defineProperty2.default)(_this, "documents", []);
    (0, _defineProperty2.default)(_this, "order", 0);
    return _this;
  }
  (0, _inherits2.default)(ContentType, _BaseModel);
  return (0, _createClass2.default)(ContentType, null, [{
    key: "createFromResponse",
    value: function createFromResponse(documentType) {
      return new ContentType().init({
        id: documentType.id,
        label: documentType.label,
        documentTypes: documentType.doc_types,
        order: documentType.order,
        documents: []
      });
    }
  }]);
}(_baseModel.default);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/document.js":
/*!***************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/document.js ***!
  \***************************************************************/
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
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseModel = _interopRequireDefault(__webpack_require__(/*! ./base-model */ "../app/modules/kit-library/assets/js/models/base-model.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var Document = exports["default"] = /*#__PURE__*/function (_BaseModel) {
  function Document() {
    var _this;
    (0, _classCallCheck2.default)(this, Document);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Document, [].concat(args));
    (0, _defineProperty2.default)(_this, "id", '');
    (0, _defineProperty2.default)(_this, "title", '');
    (0, _defineProperty2.default)(_this, "documentType", '');
    (0, _defineProperty2.default)(_this, "thumbnailUrl", '');
    (0, _defineProperty2.default)(_this, "previewUrl", '');
    return _this;
  }
  (0, _inherits2.default)(Document, _BaseModel);
  return (0, _createClass2.default)(Document, null, [{
    key: "createFromResponse",
    value:
    /**
     * Create a tag from server response
     *
     * @param {Document} document
     */
    function createFromResponse(document) {
      return new Document().init({
        id: document.id,
        title: document.title,
        documentType: document.doc_type,
        thumbnailUrl: document.thumbnail_url,
        previewUrl: document.preview_url
      });
    }
  }]);
}(_baseModel.default);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/kit.js":
/*!**********************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/kit.js ***!
  \**********************************************************/
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
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseModel = _interopRequireDefault(__webpack_require__(/*! ./base-model */ "../app/modules/kit-library/assets/js/models/base-model.js"));
var _document = _interopRequireDefault(__webpack_require__(/*! ./document */ "../app/modules/kit-library/assets/js/models/document.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
/**
 * @typedef {import('./content-type')} ContentType
 */
var Kit = exports["default"] = /*#__PURE__*/function (_BaseModel) {
  function Kit() {
    var _this;
    (0, _classCallCheck2.default)(this, Kit);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Kit, [].concat(args));
    (0, _defineProperty2.default)(_this, "id", '');
    (0, _defineProperty2.default)(_this, "title", '');
    (0, _defineProperty2.default)(_this, "description", '');
    (0, _defineProperty2.default)(_this, "isFavorite", false);
    (0, _defineProperty2.default)(_this, "thumbnailUrl", null);
    (0, _defineProperty2.default)(_this, "previewUrl", '');
    (0, _defineProperty2.default)(_this, "accessLevel", 0);
    (0, _defineProperty2.default)(_this, "trendIndex", null);
    (0, _defineProperty2.default)(_this, "popularityIndex", null);
    (0, _defineProperty2.default)(_this, "featuredIndex", null);
    (0, _defineProperty2.default)(_this, "createdAt", null);
    (0, _defineProperty2.default)(_this, "updatedAt", null);
    (0, _defineProperty2.default)(_this, "keywords", []);
    (0, _defineProperty2.default)(_this, "taxonomies", []);
    (0, _defineProperty2.default)(_this, "documents", []);
    return _this;
  }
  (0, _inherits2.default)(Kit, _BaseModel);
  return (0, _createClass2.default)(Kit, [{
    key: "getDocumentsByTypes",
    value:
    /**
     * Get content types as param and group all the documents based on it.
     *
     * @param {ContentType[]} contentTypes
     * @return {ContentType[]} content types
     */
    function getDocumentsByTypes(contentTypes) {
      var _this2 = this;
      return contentTypes.map(function (contentType) {
        contentType = contentType.clone();
        contentType.documents = _this2.documents.filter(function (document) {
          return contentType.documentTypes.includes(document.documentType);
        });
        return contentType;
      });
    }
  }], [{
    key: "createFromResponse",
    value:
    /**
     * Create a kit from server response
     *
     * @param {Kit} kit
     */
    function createFromResponse(kit) {
      return new Kit().init({
        id: kit.id,
        title: kit.title,
        description: kit.description,
        isFavorite: kit.is_favorite,
        thumbnailUrl: kit.thumbnail_url,
        previewUrl: kit.preview_url,
        accessLevel: kit.access_level,
        accessTier: kit.access_tier,
        trendIndex: kit.trend_index,
        popularityIndex: kit.popularity_index,
        featuredIndex: kit.featured_index,
        // TODO: Remove when the API is stable (when date params always exists)
        createdAt: kit.created_at ? new Date(kit.created_at) : null,
        updatedAt: kit.updated_at ? new Date(kit.updated_at) : null,
        //
        keywords: kit.keywords,
        taxonomies: kit.taxonomies,
        documents: kit.documents ? kit.documents.map(function (document) {
          return _document.default.createFromResponse(document);
        }) : [],
        status: kit.status || 'active'
      });
    }
  }]);
}(_baseModel.default);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/taxonomy-transformer.js":
/*!***************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/taxonomy-transformer.js ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.TierToKeyMap = exports.PromotionChipText = exports.OldPlanTexts = exports.NewPlanTexts = void 0;
exports.getTaxonomyFilterItems = getTaxonomyFilterItems;
exports.isKitInTaxonomy = isKitInTaxonomy;
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _tiers = __webpack_require__(/*! elementor-utils/tiers */ "../assets/dev/js/utils/tiers.js");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _taxonomy = _interopRequireWildcard(__webpack_require__(/*! ./taxonomy */ "../app/modules/kit-library/assets/js/models/taxonomy.js"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
var FREE = 'free',
  ESSENTIAL = 'essential',
  ADVANCED = 'advanced',
  PRO = 'pro',
  EXPERT = 'expert,',
  AGENCY = 'agency';
var OldPlanTexts = exports.OldPlanTexts = (0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)({}, FREE, (0, _i18n.__)('Free', 'elementor')), PRO, (0, _i18n.__)('Pro', 'elementor')), ADVANCED, (0, _i18n.__)('Advanced', 'elementor')), EXPERT, (0, _i18n.__)('Expert', 'elementor')), AGENCY, (0, _i18n.__)('Agency', 'elementor'));
var NewPlanTexts = exports.NewPlanTexts = (0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)({}, FREE, (0, _i18n.__)('Free', 'elementor')), ESSENTIAL, (0, _i18n.__)('Essential', 'elementor')), ADVANCED, (0, _i18n.__)('Advanced & Higher', 'elementor'));
var TaxonomyTransformMap = (0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)({}, PRO, ESSENTIAL), EXPERT, ADVANCED), AGENCY, ADVANCED);
var TierToKeyMap = exports.TierToKeyMap = (0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)({}, _tiers.TIERS.free, FREE), _tiers.TIERS.essential, ESSENTIAL), _tiers.TIERS['essential-oct2023'], ADVANCED), _tiers.TIERS.expert, ADVANCED), _tiers.TIERS.agency, ADVANCED);
var PromotionChipText = exports.PromotionChipText = (0, _defineProperty2.default)((0, _defineProperty2.default)((0, _defineProperty2.default)({}, FREE, (0, _i18n.__)('Free', 'elementor')), ESSENTIAL, (0, _i18n.__)('Essential', 'elementor')), ADVANCED, (0, _i18n.__)('Advanced', 'elementor'));
function getTaxonomyFilterItems(taxonomies) {
  taxonomies = taxonomies ? (0, _toConsumableArray2.default)(taxonomies) : [];
  var taxonomyFilterItems = taxonomies.reduce(function (map, taxonomy) {
    var formattedTaxonomy = _getFormattedTaxonomyItem(taxonomy),
      taxonomyType = _taxonomy.TaxonomyTypes.find(function (_ref) {
        var key = _ref.key;
        return key === formattedTaxonomy.type;
      });
    if (!taxonomyType) {
      return map;
    }
    if (!map[formattedTaxonomy.type]) {
      map[formattedTaxonomy.type] = _objectSpread({}, taxonomyType);
    }
    var data = map[formattedTaxonomy.type].data;
    if (!data.find(function (_ref2) {
      var text = _ref2.text;
      return text === formattedTaxonomy.text;
    })) {
      map[formattedTaxonomy.type].data.push(formattedTaxonomy);
    }
    return map;
  }, {});
  return _taxonomy.TaxonomyTypes.reduce(function (formattedTaxonomies, taxonomyItem) {
    var _taxonomyFilterItems$;
    if ((_taxonomyFilterItems$ = taxonomyFilterItems[taxonomyItem.key]) !== null && _taxonomyFilterItems$ !== void 0 && (_taxonomyFilterItems$ = _taxonomyFilterItems$.data) !== null && _taxonomyFilterItems$ !== void 0 && _taxonomyFilterItems$.length) {
      formattedTaxonomies.push(taxonomyFilterItems[taxonomyItem.key]);
    }
    return formattedTaxonomies;
  }, []);
}
function isKitInTaxonomy(kit, taxonomyType, taxonomies) {
  return _taxonomy.SUBSCRIPTION_PLAN === taxonomyType ? taxonomies.includes(TierToKeyMap[kit.accessTier]) : taxonomies.some(function (taxonomy) {
    return kit.taxonomies.includes(taxonomy);
  });
}
function _getFormattedTaxonomyItem(taxonomy) {
  switch (taxonomy.type) {
    case _taxonomy.SUBSCRIPTION_PLAN:
      return _getFormattedSubscriptionByPlanTaxonomy(taxonomy);
    default:
      return taxonomy;
  }
}
function _getTaxonomyIdByText(taxonomyText) {
  return Object.keys(OldPlanTexts).find(function (id) {
    return OldPlanTexts[id] === taxonomyText;
  });
}
function _getFormattedTaxonomyId(taxonomyId) {
  return TaxonomyTransformMap[taxonomyId] || taxonomyId;
}
function _getFormattedSubscriptionByPlanTaxonomy(taxonomy) {
  var transformedTaxonomy = new _taxonomy.default();
  transformedTaxonomy.id = _getFormattedTaxonomyId(_getTaxonomyIdByText(taxonomy.text));
  transformedTaxonomy.text = NewPlanTexts[transformedTaxonomy.id] || taxonomy.text;
  transformedTaxonomy.type = taxonomy.type;
  return transformedTaxonomy;
}

/***/ }),

/***/ "../app/modules/kit-library/assets/js/models/taxonomy.js":
/*!***************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/models/taxonomy.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.TaxonomyTypes = exports.TAG = exports.SUBSCRIPTION_PLAN = exports.FEATURE = exports.CATEGORY = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _baseModel = _interopRequireDefault(__webpack_require__(/*! ./base-model */ "../app/modules/kit-library/assets/js/models/base-model.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var CATEGORY = exports.CATEGORY = 'categories',
  TAG = exports.TAG = 'tags',
  FEATURE = exports.FEATURE = 'features',
  SUBSCRIPTION_PLAN = exports.SUBSCRIPTION_PLAN = 'subscription_plans';
var TaxonomyTypes = exports.TaxonomyTypes = [{
  key: 'categories',
  label: (0, _i18n.__)('Categories', 'elementor'),
  isOpenByDefault: true,
  data: []
}, {
  key: 'tags',
  label: (0, _i18n.__)('Tags', 'elementor'),
  data: []
}, {
  key: 'features',
  label: (0, _i18n.__)('Features', 'elementor'),
  data: []
}, {
  key: SUBSCRIPTION_PLAN,
  label: (0, _i18n.__)('Plan', 'elementor'),
  data: []
}];
var Taxonomy = exports["default"] = /*#__PURE__*/function (_BaseModel) {
  function Taxonomy() {
    var _this;
    (0, _classCallCheck2.default)(this, Taxonomy);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Taxonomy, [].concat(args));
    (0, _defineProperty2.default)(_this, "text", '');
    (0, _defineProperty2.default)(_this, "type", 'tag');
    (0, _defineProperty2.default)(_this, "id", null);
    return _this;
  }
  (0, _inherits2.default)(Taxonomy, _BaseModel);
  return (0, _createClass2.default)(Taxonomy, null, [{
    key: "createFromResponse",
    value:
    /**
     * Create a tag from server response
     *
     * @param {Taxonomy} taxonomy
     */
    function createFromResponse(taxonomy) {
      return new Taxonomy().init({
        text: taxonomy.text,
        type: taxonomy.type,
        id: taxonomy.id || null
      });
    }
  }]);
}(_baseModel.default);

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/cloud.js":
/*!*****************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/cloud.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Cloud;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _errorScreen = _interopRequireDefault(__webpack_require__(/*! ../../components/error-screen */ "../app/modules/kit-library/assets/js/components/error-screen.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ../index/index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ../index/index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _kitListCloud = _interopRequireDefault(__webpack_require__(/*! ../../components/kit-list-cloud */ "../app/modules/kit-library/assets/js/components/kit-list-cloud.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _quotaBar = _interopRequireDefault(__webpack_require__(/*! ../../components/quota-bar */ "../app/modules/kit-library/assets/js/components/quota-bar.js"));
var _quotaNotification = _interopRequireDefault(__webpack_require__(/*! ../../components/quota-notification */ "../app/modules/kit-library/assets/js/components/quota-notification.js"));
var _searchInput = _interopRequireDefault(__webpack_require__(/*! ../../components/search-input */ "../app/modules/kit-library/assets/js/components/search-input.js"));
var _useCloudKits2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-cloud-kits */ "../app/modules/kit-library/assets/js/hooks/use-cloud-kits.js"));
var _useCloudKitsEligibility = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-cloud-kits-eligibility */ "../app/assets/js/hooks/use-cloud-kits-eligibility.js"));
var _useCloudKitsQuota2 = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-cloud-kits-quota */ "../app/assets/js/hooks/use-cloud-kits-quota.js"));
var _useMenuItems = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-menu-items */ "../app/modules/kit-library/assets/js/hooks/use-menu-items.js"));
var _useConnectState2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-connect-state */ "../app/modules/kit-library/assets/js/hooks/use-connect-state.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _utils = __webpack_require__(/*! ../../utils */ "../app/modules/kit-library/assets/js/utils.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _connectScreen = _interopRequireDefault(__webpack_require__(/*! ./connect-screen */ "../app/modules/kit-library/assets/js/pages/cloud/connect-screen.js"));
var _upgradeScreen = _interopRequireDefault(__webpack_require__(/*! ./upgrade-screen */ "../app/modules/kit-library/assets/js/pages/cloud/upgrade-screen.js"));
var _deactivatedScreen = _interopRequireDefault(__webpack_require__(/*! ./deactivated-screen */ "../app/modules/kit-library/assets/js/pages/cloud/deactivated-screen.js"));
var _fullPageLoader = _interopRequireDefault(__webpack_require__(/*! ./full-page-loader */ "../app/modules/kit-library/assets/js/pages/cloud/full-page-loader.js"));
__webpack_require__(/*! ../index/index.scss */ "../app/modules/kit-library/assets/js/pages/index/index.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function Cloud(_ref) {
  var _elementorCommon;
  var _ref$path = _ref.path,
    path = _ref$path === void 0 ? '' : _ref$path,
    _ref$renderNoResultsC = _ref.renderNoResultsComponent,
    renderNoResultsComponent = _ref$renderNoResultsC === void 0 ? function (_ref2) {
      var defaultComponent = _ref2.defaultComponent;
      return defaultComponent;
    } : _ref$renderNoResultsC;
  (0, _usePageTitle.default)({
    title: __('Website Templates', 'elementor')
  });
  var _useConnectState = (0, _useConnectState2.default)(),
    isConnected = _useConnectState.isConnected,
    isConnecting = _useConnectState.isConnecting,
    setConnecting = _useConnectState.setConnecting,
    handleConnectSuccess = _useConnectState.handleConnectSuccess,
    handleConnectError = _useConnectState.handleConnectError;
  var _useCloudKits = (0, _useCloudKits2.default)(),
    data = _useCloudKits.data,
    isSuccess = _useCloudKits.isSuccess,
    isLoading = _useCloudKits.isLoading,
    isFetching = _useCloudKits.isFetching,
    isError = _useCloudKits.isError,
    queryParams = _useCloudKits.queryParams,
    setQueryParams = _useCloudKits.setQueryParams,
    clearQueryParams = _useCloudKits.clearQueryParams,
    forceRefetch = _useCloudKits.forceRefetch,
    isFilterActive = _useCloudKits.isFilterActive;
  var _useCloudKitsEligibil = (0, _useCloudKitsEligibility.default)({
      enabled: isConnected
    }),
    cloudKitsData = _useCloudKitsEligibil.data,
    isCheckingEligibility = _useCloudKitsEligibil.isLoading,
    refetchEligibility = _useCloudKitsEligibil.refetch;
  var _useCloudKitsQuota = (0, _useCloudKitsQuota2.default)({
      enabled: isConnected
    }),
    quotaData = _useCloudKitsQuota.data,
    isLoadingQuota = _useCloudKitsQuota.isLoading;
  var isCloudKitsAvailable = (cloudKitsData === null || cloudKitsData === void 0 ? void 0 : cloudKitsData.is_eligible) || false;
  var isDeactivated = quotaData && (0, _utils.isCloudKitsDeactivated)(quotaData);
  var exportUrl = (_elementorCommon = elementorCommon) !== null && _elementorCommon !== void 0 && (_elementorCommon = _elementorCommon.config) !== null && _elementorCommon !== void 0 && (_elementorCommon = _elementorCommon.experimentalFeatures) !== null && _elementorCommon !== void 0 && _elementorCommon['import-export-customization'] ? elementorAppConfig.base_url + '#/export-customization' : elementorAppConfig.base_url + '#/export';
  var menuItems = (0, _useMenuItems.default)(path);
  var onConnectSuccess = function onConnectSuccess() {
    refetchEligibility();
    forceRefetch();
    handleConnectSuccess();
  };
  var onConnectError = function onConnectError() {
    handleConnectError();
  };
  var shouldShowLoading = isConnecting || isCheckingEligibility || isConnected && isLoading;
  (0, _react.useEffect)(function () {
    if (isConnecting && !isCheckingEligibility && !isLoading) {
      setConnecting(false);
    }
  }, [isConnecting, isCheckingEligibility, isLoading, setConnecting]);
  (0, _react.useEffect)(function () {
    _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.kitLibrary.cloudKitLibrary);
  }, []);
  if (!isConnected) {
    return /*#__PURE__*/_react.default.createElement(_connectScreen.default, {
      onConnectSuccess: onConnectSuccess,
      onConnectError: onConnectError,
      menuItems: menuItems,
      forceRefetch: forceRefetch,
      isFetching: isFetching
    });
  }
  if (shouldShowLoading) {
    return /*#__PURE__*/_react.default.createElement(_fullPageLoader.default, {
      menuItems: menuItems,
      forceRefetch: forceRefetch,
      isFetching: isFetching
    });
  }
  if (isDeactivated && !shouldShowLoading) {
    return /*#__PURE__*/_react.default.createElement(_deactivatedScreen.default, {
      menuItems: menuItems,
      forceRefetch: forceRefetch,
      isFetching: isFetching
    });
  }
  if (!isCloudKitsAvailable && !shouldShowLoading) {
    return /*#__PURE__*/_react.default.createElement(_upgradeScreen.default, {
      menuItems: menuItems,
      forceRefetch: forceRefetch,
      isFetching: isFetching,
      cloudKitsData: cloudKitsData
    });
  }
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
      },
      isFetching: isFetching
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    className: "e-kit-library__index-layout-heading"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    item: true,
    className: "e-kit-library__index-layout-heading-search"
  }, /*#__PURE__*/_react.default.createElement(_searchInput.default
  // eslint-disable-next-line @wordpress/i18n-ellipsis
  , {
    placeholder: __('Search my Website Templates...', 'elementor'),
    value: queryParams.search,
    onChange: function onChange(value) {
      setQueryParams(function (prev) {
        return _objectSpread(_objectSpread({}, prev), {}, {
          search: value
        });
      });
    }
  })), /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    item: true,
    className: "e-kit-library__index-layout-heading-quota"
  }, !isLoadingQuota && (quotaData === null || quotaData === void 0 ? void 0 : quotaData.storage) && /*#__PURE__*/_react.default.createElement(_quotaBar.default, {
    used: quotaData.storage.currentUsage,
    total: quotaData.storage.threshold,
    unit: quotaData.storage.unit
  }))), !isLoadingQuota && (quotaData === null || quotaData === void 0 ? void 0 : quotaData.storage) && /*#__PURE__*/_react.default.createElement(_quotaNotification.default, {
    usagePercentage: Math.min(quotaData.storage.currentUsage / quotaData.storage.threshold * 100, 100)
  }), /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main"
  }, /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, isError && /*#__PURE__*/_react.default.createElement(_errorScreen.default, {
    title: __('Something went wrong.', 'elementor'),
    description: __('Nothing to worry about, use 🔄 on the top corner to try again. If the problem continues, head over to the Help Center.', 'elementor'),
    button: {
      text: __('Learn More', 'elementor'),
      url: 'https://go.elementor.com/app-kit-library-error/',
      target: '_blank'
    }
  }), isSuccess && 0 < data.length && /*#__PURE__*/_react.default.createElement(_kitListCloud.default, {
    data: data,
    source: path
  }), isSuccess && 0 === data.length && (queryParams.search ? /*#__PURE__*/_react.default.createElement(_errorScreen.default, {
    title: __('No Website Templates found for your search', 'elementor'),
    description: __('Try different keywords or ', 'elementor'),
    button: {
      text: __('Continue browsing.', 'elementor'),
      action: clearQueryParams
    }
  }) : renderNoResultsComponent({
    defaultComponent: /*#__PURE__*/_react.default.createElement(_errorScreen.default, {
      title: __('No Website Templates to show here yet', 'elementor'),
      description: __("Once you export a Website to the cloud, you'll find it here and be able to use it on all your sites.", 'elementor'),
      newLineButton: true,
      button: {
        text: __('Export this site', 'elementor'),
        url: exportUrl,
        target: '_blank',
        variant: 'contained',
        color: 'primary'
      }
    }),
    isFilterActive: isFilterActive
  }))))));
}
Cloud.propTypes = {
  path: _propTypes.default.string,
  renderNoResultsComponent: _propTypes.default.func
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/connect-screen.js":
/*!**************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/connect-screen.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = ConnectScreen;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ../index/index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ../index/index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _tiers = __webpack_require__(/*! elementor-utils/tiers */ "../assets/dev/js/utils/tiers.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ConnectScreen(_ref) {
  var _elementorAppConfig, _elementorAppConfig2, _elementorAppConfig3, _elementorAppConfig4;
  var onConnectSuccess = _ref.onConnectSuccess,
    onConnectError = _ref.onConnectError,
    menuItems = _ref.menuItems,
    forceRefetch = _ref.forceRefetch,
    isFetching = _ref.isFetching;
  var connectButtonRef = (0, _react.useRef)();
  (0, _react.useEffect)(function () {
    if (!connectButtonRef.current) {
      return;
    }
    jQuery(connectButtonRef.current).elementorConnect({
      popup: {
        width: 600,
        height: 700
      },
      success: function success(_event, data) {
        var isTrackingOptedInConnect = data.tracking_opted_in && elementorCommon.config.editor_events;
        elementorCommon.config.library_connect.is_connected = true;
        elementorCommon.config.library_connect.current_access_level = data.kits_access_level || data.access_level || 0;
        elementorCommon.config.library_connect.current_access_tier = data.access_tier;
        elementorCommon.config.library_connect.plan_type = data.plan_type;
        if (isTrackingOptedInConnect) {
          elementorCommon.config.editor_events.can_send_events = true;
        }
        onConnectSuccess === null || onConnectSuccess === void 0 || onConnectSuccess();
      },
      error: function error() {
        elementorCommon.config.library_connect.is_connected = false;
        elementorCommon.config.library_connect.current_access_level = 0;
        elementorCommon.config.library_connect.current_access_tier = '';
        elementorCommon.config.library_connect.plan_type = _tiers.TIERS.free;
        onConnectError === null || onConnectError === void 0 || onConnectError();
      }
    });
  }, [onConnectSuccess, onConnectError]);
  (0, _react.useEffect)(function () {
    _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.kitLibrary.cloudKitLibraryConnect);
  }, []);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
      },
      isFetching: isFetching
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main e-kit-library__connect-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "center",
    direction: "column",
    className: "e-kit-library__error-screen"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-library-cloud-connect",
    "aria-hidden": "true"
  }), /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "display-1",
    className: "e-kit-library__error-screen-title"
  }, (_elementorAppConfig = elementorAppConfig) === null || _elementorAppConfig === void 0 || (_elementorAppConfig = _elementorAppConfig['cloud-library']) === null || _elementorAppConfig === void 0 ? void 0 : _elementorAppConfig.library_connect_title_copy), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    variant: "xl",
    className: "e-kit-library__error-screen-description"
  }, (_elementorAppConfig2 = elementorAppConfig) === null || _elementorAppConfig2 === void 0 || (_elementorAppConfig2 = _elementorAppConfig2['cloud-library']) === null || _elementorAppConfig2 === void 0 || (_elementorAppConfig2 = _elementorAppConfig2.library_connect_sub_title_copy) === null || _elementorAppConfig2 === void 0 ? void 0 : _elementorAppConfig2.replace(/<br\s*\/?>/gi, '\n')), /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    elRef: connectButtonRef,
    text: (_elementorAppConfig3 = elementorAppConfig) === null || _elementorAppConfig3 === void 0 || (_elementorAppConfig3 = _elementorAppConfig3['cloud-library']) === null || _elementorAppConfig3 === void 0 || (_elementorAppConfig3 = _elementorAppConfig3.library_connect_button_copy) === null || _elementorAppConfig3 === void 0 ? void 0 : _elementorAppConfig3.replace(/&amp;/g, '&'),
    url: (_elementorAppConfig4 = elementorAppConfig) === null || _elementorAppConfig4 === void 0 || (_elementorAppConfig4 = _elementorAppConfig4['cloud-library']) === null || _elementorAppConfig4 === void 0 || (_elementorAppConfig4 = _elementorAppConfig4.library_connect_url) === null || _elementorAppConfig4 === void 0 ? void 0 : _elementorAppConfig4.replace(/&#038;/g, '&'),
    className: "e-kit-library__connect-button"
  })))));
}
ConnectScreen.propTypes = {
  onConnectSuccess: _propTypes.default.func,
  onConnectError: _propTypes.default.func,
  menuItems: _propTypes.default.array.isRequired,
  forceRefetch: _propTypes.default.func.isRequired,
  isFetching: _propTypes.default.bool.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/deactivated-icon.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/deactivated-icon.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _extends2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/extends */ "../node_modules/@babel/runtime/helpers/extends.js"));
var React = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var DeactivatedIcon = function DeactivatedIcon(props) {
  return /*#__PURE__*/React.createElement("svg", (0, _extends2.default)({
    width: 409,
    height: 204,
    viewBox: "0 0 409 204",
    fill: "none",
    xmlns: "http://www.w3.org/2000/svg",
    xmlnsXlink: "http://www.w3.org/1999/xlink"
  }, props), /*#__PURE__*/React.createElement("g", {
    clipPath: "url(#clip0_9066_32193)"
  }, /*#__PURE__*/React.createElement("path", {
    d: "M324.425 167.25H81.7246L84.8596 19.5898H327.56L324.425 167.25Z",
    fill: "white"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M324.5 167.85H81.725C81.56 166.35 81.41 166.275 81.305 166.17C81.2473 166.114 81.2015 166.046 81.1706 165.971C81.1397 165.897 81.1242 165.816 81.125 165.735L84.26 19.5754C84.26 19.4973 84.2756 19.42 84.306 19.348C84.3363 19.2761 84.3807 19.2109 84.4366 19.1564C84.4926 19.1019 84.5588 19.0591 84.6316 19.0306C84.7043 19.0021 84.782 18.9884 84.86 18.9904H327.5C327.581 18.9896 327.661 19.005 327.736 19.036C327.811 19.0669 327.878 19.1126 327.935 19.1704C328.041 19.2845 328.1 19.4346 328.1 19.5904L324.965 165.765C325.025 166.08 324.755 166.35 324.5 167.85ZM82.415 166.65H323.915L327.02 20.1904H85.52L82.415 166.65Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M324.425 167.25H81.7246L84.8596 19.5898H327.56L324.425 167.25Z",
    fill: "white"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M327.5 18.6152V18.6162C327.63 18.6153 327.759 18.6398 327.879 18.6895C327.97 18.7271 328.055 18.778 328.131 18.8408L328.203 18.9082L328.21 18.915L328.209 18.916C328.38 19.0995 328.475 19.3402 328.475 19.5908V19.5986L325.34 165.752C325.355 165.875 325.343 165.988 325.319 166.095C325.294 166.207 325.251 166.339 325.209 166.476C325.121 166.759 324.994 167.184 324.87 167.913L324.816 168.226H81.3887L81.3525 167.892C81.2705 167.146 81.1944 166.784 81.1357 166.604C81.1081 166.519 81.0889 166.488 81.084 166.48C81.0775 166.471 81.0852 166.481 81.04 166.436L81.041 166.435C80.9484 166.343 80.874 166.235 80.8242 166.114C80.774 165.993 80.7486 165.863 80.75 165.731V165.728L83.8848 19.5674C83.8858 19.442 83.9112 19.3177 83.96 19.2021C84.0097 19.0841 84.0831 18.9772 84.1748 18.8877C84.2666 18.7983 84.3758 18.7284 84.4951 18.6816C84.6115 18.6361 84.7355 18.6142 84.8604 18.6162V18.6152H327.5ZM82.7979 166.275H323.548L326.638 20.5654H85.8867L82.7979 166.275Z",
    fill: "black",
    stroke: "black",
    strokeWidth: 0.75
  }), /*#__PURE__*/React.createElement("rect", {
    x: 62.8398,
    y: 70.7256,
    width: 129.6,
    height: 130.05,
    fill: "url(#pattern0_9066_32193)"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M327 20H86V27H327V20Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M379.517 66.4746C384.493 66.5767 389.147 67.4985 392.973 69.9111L393.34 70.1494C398.027 73.2721 400.399 77.8954 401.349 83.0469C402.264 88.0107 401.85 93.4321 400.92 98.3701H268.579C269.354 97.3672 270.032 96.4076 270.664 95.4883C271.555 94.1919 272.34 93.0016 273.194 91.8545C274.881 89.5905 276.807 87.5467 280.124 85.5752L280.128 85.5723C282.558 84.1054 292.59 78.7477 303.176 82.2373L303.177 82.2383C308.021 83.8273 312.135 87.1022 314.769 91.4678L315.227 92.2275L315.899 91.6484C320.102 88.0331 324.584 84.7564 329.304 81.8486L329.323 81.8369C339.795 74.9467 351.547 70.2358 363.879 67.9854C368.968 67.1385 374.379 66.3693 379.517 66.4746Z",
    fill: "white",
    stroke: "black",
    strokeWidth: 1.5
  }), /*#__PURE__*/React.createElement("path", {
    d: "M401.54 99.7197H267.02C266.906 99.7209 266.794 99.689 266.698 99.6277C266.602 99.5664 266.526 99.4785 266.48 99.3747C266.428 99.274 266.408 99.1604 266.421 99.0483C266.435 98.9361 266.481 98.8304 266.555 98.7447C267.865 97.1334 269.091 95.4562 270.23 93.7197C272.465 89.9075 275.608 86.7075 279.38 84.4047C282.26 82.6797 292.52 77.3247 303.53 80.9547C308.473 82.5478 312.713 85.8018 315.53 90.1647C319.651 86.6929 324.023 83.5293 328.61 80.6997C339.226 73.6882 351.158 68.9112 363.68 66.6597C374.075 64.9197 385.865 63.5247 394.115 69.0297C404.36 75.8547 404.135 89.2347 402.155 99.2397C402.126 99.3799 402.049 99.5053 401.936 99.5933C401.823 99.6813 401.683 99.7261 401.54 99.7197ZM268.265 98.5197H401C402.815 88.9347 402.905 76.3797 393.365 70.0197C385.49 64.7547 373.97 66.1347 363.8 67.8297C351.434 70.0599 339.65 74.7805 329.165 81.7047C324.416 84.6183 319.903 87.8983 315.665 91.5147C315.6 91.5721 315.523 91.6141 315.44 91.6374C315.357 91.6608 315.27 91.665 315.185 91.6497C315.098 91.6357 315.016 91.603 314.943 91.5538C314.871 91.5045 314.81 91.44 314.765 91.3647C312.101 86.9697 307.95 83.6754 303.065 82.0797C292.565 78.5997 282.68 83.7597 279.905 85.4247C276.284 87.6529 273.274 90.7455 271.145 94.4247C270.335 95.7147 269.405 97.0647 268.265 98.5197Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("rect", {
    x: 256.61,
    y: 29.9395,
    width: 67.83,
    height: 102.69,
    fill: "url(#pattern1_9066_32193)"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M114.534 142.455C125.859 142.165 134.638 149.891 138.123 159.657C141.561 169.291 140.771 180.648 138.615 191.851H7.4873C14.0371 189.906 18.5275 187.327 22.167 184.635C24.4859 182.919 26.4597 181.156 28.3643 179.526C30.2782 177.888 32.132 176.375 34.2617 175.093L34.2871 175.077C37.9279 172.683 42.0044 171.029 46.2842 170.21C55.9983 168.873 63.2438 175.597 69.2598 182.215L70.0039 183.034L70.4893 182.039C74.9672 172.844 80.0844 163.559 87.4814 155.984C94.8869 148.401 104.941 142.645 114.529 142.455H114.534Z",
    fill: "white",
    stroke: "black",
    strokeWidth: 1.5
  }), /*#__PURE__*/React.createElement("path", {
    d: "M139.235 193.201H1.26466C1.10553 193.215 0.947384 193.165 0.825016 193.062C0.702649 192.96 0.626081 192.813 0.612157 192.654C0.598234 192.494 0.648094 192.336 0.75077 192.214C0.853446 192.092 1.00053 192.015 1.15966 192.001C10.6712 190.413 19.4897 186.014 26.4797 179.371C28.6625 177.384 31.011 175.588 33.4997 174.001C37.3028 171.511 41.5586 169.794 46.0247 168.946C56.1497 167.536 63.4997 174.136 69.5897 180.706C73.6847 172.321 78.8896 162.841 86.4496 155.101C94.8496 146.506 105.32 141.286 114.5 141.106C125.615 140.896 135.395 147.991 139.4 159.211C142.49 167.866 142.625 178.516 139.82 192.706C139.795 192.844 139.723 192.969 139.616 193.059C139.51 193.15 139.375 193.2 139.235 193.201ZM6.49966 192.001H138.74C141.395 178.261 141.245 168.001 138.26 159.601C134.51 149.101 125.405 142.306 115.01 142.306H114.5C105.62 142.486 95.4646 147.556 87.3346 155.806C79.6546 163.666 74.4196 173.401 70.3247 181.891C70.2806 181.98 70.215 182.057 70.1339 182.115C70.0527 182.172 69.9585 182.209 69.8597 182.221C69.7642 182.234 69.6669 182.225 69.5757 182.194C69.4845 182.162 69.4019 182.11 69.3346 182.041C63.3347 175.486 56.1047 168.541 46.2197 170.041C41.9437 170.889 37.8701 172.549 34.2197 174.931C31.7589 176.501 29.44 178.283 27.2897 180.256C21.4457 185.841 14.2995 189.878 6.49966 192.001Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("rect", {
    x: 1.16016,
    y: 159.721,
    width: 74.7,
    height: 39.15,
    fill: "url(#pattern2_9066_32193)"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M351.575 47.475H350.66C350.975 41.37 350.885 38.58 349.49 37.11C348.095 35.64 345.335 35.43 339.62 35.43V34.5C350.78 34.5 351.35 33.945 351.935 22.5H352.865C352.55 28.605 352.64 31.38 354.035 32.85C355.43 34.32 358.19 34.5 363.905 34.5V35.415C352.745 35.385 352.175 36 351.575 47.475Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M365.928 48.5617C365.965 48.5274 365.966 48.4704 365.932 48.4344C365.897 48.3984 365.841 48.3969 365.804 48.4312C365.768 48.4654 365.767 48.5224 365.801 48.5584C365.835 48.5944 365.892 48.5959 365.928 48.5617Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M365.945 56.1301H365.03C365.21 52.5601 365.15 50.8201 364.355 49.9801C363.56 49.1401 361.91 49.0051 358.49 49.0051V48.0001C365.18 48.0001 365.45 47.7301 365.795 40.8301H366.71C366.53 44.4151 366.59 46.1551 367.385 46.9951C368.18 47.8351 369.83 47.9551 373.25 47.9551V48.8701C366.5 48.9601 366.29 49.2301 365.945 56.1301Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M35.7348 121.501C35.5847 121.501 35.4402 121.443 35.3314 121.34C35.2225 121.237 35.1575 121.095 35.1498 120.946C35.1436 120.867 35.1531 120.787 35.1777 120.712C35.2023 120.637 35.2416 120.568 35.2931 120.508C35.3447 120.448 35.4076 120.399 35.4783 120.363C35.5489 120.328 35.6259 120.306 35.7048 120.301C45.1943 119.723 54.4415 117.07 62.7948 112.531C61.4473 111.922 60.2425 111.037 59.2581 109.934C58.2738 108.831 57.5317 107.533 57.0798 106.126C56.8661 105.317 56.8614 104.468 57.0661 103.657C57.2707 102.846 57.678 102.101 58.2498 101.491C59.9194 99.935 62.1177 99.0718 64.3998 99.0756C68.2698 98.9706 71.8998 101.356 72.0348 104.116C72.1698 106.876 69.5898 109.246 67.5348 110.776C66.8148 111.331 66.0348 111.856 65.2848 112.381C69.5723 113.922 74.1785 114.36 78.6798 113.656L86.8248 111.781C86.9796 111.749 87.1408 111.778 87.2749 111.861C87.4089 111.945 87.5056 112.077 87.5448 112.231C87.5625 112.307 87.565 112.387 87.552 112.465C87.539 112.543 87.5108 112.617 87.469 112.684C87.4272 112.751 87.3727 112.809 87.3084 112.854C87.2442 112.9 87.1716 112.933 87.0948 112.951L78.9048 114.826C74.1613 115.576 69.305 115.111 64.7898 113.476L64.0398 113.161C55.3894 118.084 45.7171 120.938 35.7798 121.501H35.7348ZM64.5798 100.321H64.4148C62.4581 100.297 60.5675 101.028 59.1348 102.361C58.7017 102.829 58.3936 103.399 58.2389 104.017C58.0842 104.636 58.0879 105.284 58.2498 105.901C58.7748 108.181 60.8898 110.401 64.0098 111.901C65.0148 111.271 65.9748 110.611 66.8598 109.936C69.6498 107.791 70.9398 105.946 70.8498 104.296C70.7598 102.646 67.9998 100.321 64.5798 100.321Z",
    fill: "black"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M227.918 83.4521L219.466 83.9988L219.216 79.8446L218.535 72.8255C217.891 63.9799 210.155 57.3905 201.31 58.1067C192.464 58.8229 185.839 66.63 186.483 75.4397L187.021 82.4589L189.599 121.852L181.219 123.643L178.533 83.1393L177.996 76.1202C176.993 62.5832 187.164 50.6936 200.665 49.5834C214.166 48.4733 225.984 58.5723 226.987 72.0734L227.667 79.0926L227.918 83.2826V83.4521Z",
    fill: "white",
    stroke: "#0C0D0E",
    strokeWidth: 2.25,
    strokeMiterlimit: 10
  }), /*#__PURE__*/React.createElement("path", {
    d: "M176.061 141.262L234.507 136.428C236.584 136.249 238.159 134.386 238.016 132.309L235.76 102.263C235.617 100.293 233.934 98.8608 231.964 99.004L173.519 103.839C171.442 104.018 169.866 105.88 170.009 107.957L172.265 138.003C172.408 139.973 174.092 141.406 176.061 141.262Z",
    fill: "#0C0D0E",
    stroke: "#0C0D0E",
    strokeWidth: 3.375,
    strokeMiterlimit: 10
  }), /*#__PURE__*/React.createElement("path", {
    d: "M185.693 140.473L236.045 136.319C238.015 136.14 239.554 134.385 239.411 132.379L237.155 102.011C237.012 100.113 235.4 98.7519 233.502 98.8951L183.15 103.049C181.181 103.228 179.641 104.983 179.784 106.989L182.04 137.357C182.184 139.255 183.795 140.616 185.693 140.473Z",
    fill: "white",
    stroke: "#0C0D0E",
    strokeWidth: 2.25,
    strokeMiterlimit: 10
  }), /*#__PURE__*/React.createElement("g", {
    style: {
      mixBlendMode: 'multiply'
    }
  }, /*#__PURE__*/React.createElement("path", {
    d: "M199.911 139.544L235.508 136.607C236.904 136.5 237.979 134.888 237.836 133.098L235.723 105.522C235.579 103.803 234.433 102.514 233.073 102.621L197.475 105.558C196.079 105.666 195.004 107.277 195.148 109.068L197.261 136.643C197.404 138.362 198.55 139.651 199.911 139.544Z",
    fill: "#FBF2FB"
  }))), /*#__PURE__*/React.createElement("path", {
    d: "M83.4999 104.999L84 66L143.5 25.5005L196 25.4994L83.4999 104.999Z",
    fill: "white",
    stroke: "black",
    strokeWidth: 2.25
  }), /*#__PURE__*/React.createElement("path", {
    d: "M126.356 67.4886C125.206 68.2993 124.008 68.767 122.761 68.8917C121.518 68.9943 120.335 68.7839 119.212 68.2606C118.092 67.7153 117.138 66.8695 116.349 65.7234C115.548 64.5865 115.007 63.3812 114.729 62.1078C114.463 60.8252 114.438 59.5561 114.655 58.3004C114.876 57.0227 115.332 55.831 116.023 54.7252C116.704 53.6065 117.607 52.6509 118.731 51.8584C119.894 51.0386 121.097 50.5774 122.339 50.4747C123.573 50.3592 124.75 50.5741 125.87 51.1195C126.993 51.6428 127.949 52.4775 128.738 53.6236C129.53 54.7476 130.064 55.9574 130.339 57.2529C130.618 58.5263 130.647 59.8019 130.426 61.0796C130.208 62.3353 129.759 63.5225 129.078 64.6412C128.4 65.7378 127.493 66.687 126.356 67.4886ZM125.013 64.9249C125.723 64.4239 126.282 63.8172 126.689 63.1048C127.109 62.3833 127.374 61.6067 127.483 60.7749C127.597 59.921 127.546 59.067 127.331 58.213C127.12 57.3369 126.741 56.5113 126.194 55.7361C125.669 54.9647 125.076 54.3968 124.413 54.0325C123.75 53.6682 123.05 53.5133 122.315 53.5675C121.579 53.6218 120.837 53.9131 120.088 54.4415C119.377 54.9425 118.812 55.5537 118.392 56.2752C117.976 56.9747 117.711 57.7514 117.598 58.6052C117.488 59.437 117.534 60.2845 117.736 61.1477C117.952 62.0017 118.337 62.8228 118.893 63.611C119.421 64.3603 120.021 64.9236 120.694 65.3008C121.369 65.656 122.08 65.8129 122.824 65.7715C123.573 65.7082 124.302 65.4259 125.013 64.9249Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M107.473 80.4541L100.292 65.207L105.292 61.6817C106.261 60.9985 107.174 60.6063 108.031 60.5049C108.892 60.3816 109.666 60.493 110.355 60.839C111.048 61.163 111.616 61.6526 112.058 62.3077C112.723 63.2509 113.063 64.2399 113.076 65.2746C113.09 66.3094 112.807 67.3212 112.228 68.31C111.652 69.2768 110.809 70.1519 109.698 70.9353L107.275 72.6433L110.089 78.6094L107.473 80.4541ZM114.643 75.3984L108.691 70.6589L111.404 68.746L117.492 73.3898L114.643 75.3984ZM106.285 70.498L108.572 68.8857C109.399 68.3027 109.903 67.6185 110.084 66.833C110.278 66.0385 110.102 65.2536 109.556 64.4784C109.209 63.9874 108.765 63.7007 108.224 63.6184C107.686 63.514 107.062 63.7123 106.351 64.2133L104.064 65.8257L106.285 70.498Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M95.5492 88.8603L88.3682 73.6133L93.4653 70.0196C94.4343 69.3365 95.3473 68.9442 96.2043 68.8429C97.0613 68.7416 97.8343 68.8639 98.5231 69.21C99.2029 69.5431 99.7751 70.0392 100.24 70.6981C100.923 71.6671 101.274 72.6865 101.293 73.7562C101.316 74.8038 101.038 75.8221 100.458 76.8109C99.8828 77.7777 99.0394 78.6528 97.9282 79.4362L95.4281 81.1989L98.1655 87.0157L95.5492 88.8603ZM94.307 78.8268L96.6326 77.1871C97.4983 76.5768 98.0199 75.8899 98.1973 75.1265C98.3877 74.354 98.216 73.5755 97.6824 72.7912C97.3089 72.2615 96.8559 71.9619 96.3234 71.8924C95.7818 71.8101 95.1556 72.0194 94.445 72.5204L92.1194 74.1601L94.307 78.8268Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M118.585 51.6485C119.78 50.8061 121.026 50.3252 122.321 50.2183L122.32 50.2189C123.599 50.0994 124.821 50.322 125.981 50.8862L126.198 50.9931C127.273 51.5414 128.191 52.3714 128.952 53.4758L129.243 53.9121C129.896 54.9393 130.347 56.0354 130.594 57.1999C130.88 58.5051 130.909 59.8138 130.683 61.124C130.46 62.4125 129.998 63.6309 129.301 64.7764L129.3 64.777C128.603 65.9055 127.671 66.8805 126.507 67.7006C125.326 68.5336 124.086 69.0195 122.789 69.1491L122.785 69.1497C121.498 69.256 120.27 69.0375 119.106 68.4952L119.102 68.493C117.938 67.926 116.951 67.0486 116.14 65.8702C115.321 64.7078 114.766 63.4723 114.479 62.164L114.478 62.1609C114.206 60.8476 114.182 59.5458 114.405 58.2574C114.631 56.9475 115.098 55.7249 115.805 54.5916L116.079 54.1685C116.741 53.199 117.578 52.3589 118.585 51.6485ZM118.882 52.0699C117.786 52.843 116.908 53.7731 116.246 54.86L116.244 54.8626C115.571 55.9402 115.128 57.1006 114.912 58.3453C114.701 59.5672 114.723 60.8029 114.982 62.0537L114.983 62.0539C115.254 63.2914 115.78 64.465 116.563 65.5757L116.565 65.5781C117.33 66.6904 118.25 67.504 119.323 68.0278C120.406 68.532 121.544 68.7341 122.743 68.6352C123.938 68.5148 125.094 68.0665 126.21 67.2792C127.321 66.4961 128.203 65.5708 128.861 64.506C129.526 63.4147 129.963 62.2584 130.175 61.0361C130.39 59.7916 130.361 58.55 130.09 57.3091L130.089 57.3069C129.822 56.0471 129.303 54.8694 128.53 53.7729L128.529 53.7704C127.763 52.6575 126.841 51.8552 125.764 51.3536L125.759 51.3521C124.682 50.8274 123.553 50.6209 122.367 50.7319L122.363 50.7328C121.173 50.8312 120.013 51.2728 118.882 52.0699ZM119.942 54.2314C120.72 53.6826 121.507 53.3696 122.299 53.3112C123.085 53.2532 123.834 53.4196 124.54 53.8072C125.245 54.1945 125.866 54.7928 126.408 55.5884L126.611 55.8909C127.067 56.6033 127.392 57.3582 127.585 58.1539C127.807 59.0379 127.86 59.9237 127.742 60.8097C127.628 61.6746 127.351 62.4829 126.915 63.2336C126.488 63.9805 125.904 64.6153 125.164 65.1366C124.422 65.6597 123.65 65.9616 122.849 66.0295L122.841 66.0303C122.049 66.0742 121.293 65.9063 120.577 65.5301L120.57 65.5261C119.859 65.1269 119.231 64.5354 118.685 63.7607C118.113 62.9492 117.712 62.0989 117.489 61.2114L117.488 61.2069C117.279 60.3153 117.231 59.436 117.345 58.5717C117.463 57.6855 117.739 56.8754 118.174 56.1444C118.613 55.3905 119.203 54.7523 119.942 54.2314ZM119.106 63.4637C119.615 64.185 120.186 64.7179 120.816 65.073L121.056 65.1897C121.619 65.4409 122.203 65.5484 122.813 65.5145L122.812 65.5151C123.506 65.455 124.19 65.1926 124.867 64.7153C125.549 64.2347 126.081 63.6556 126.468 62.9779L126.469 62.9759C126.872 62.2841 127.125 61.5396 127.231 60.7414C127.34 59.9208 127.291 59.0997 127.083 58.2767L127.083 58.2745C126.88 57.4317 126.515 56.6355 125.986 55.8854L125.984 55.8821C125.477 55.1367 124.912 54.5999 124.292 54.2592C123.671 53.9183 123.021 53.7751 122.336 53.8256C121.658 53.8758 120.959 54.1449 120.239 54.6527C119.557 55.1332 119.018 55.7174 118.618 56.4054L118.617 56.4074C118.22 57.0752 117.966 57.8186 117.856 58.6399C117.751 59.439 117.795 60.2549 117.99 61.0893C118.197 61.9085 118.568 62.7001 119.106 63.4637Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M105.146 61.4721C106.135 60.775 107.086 60.3605 107.996 60.2513C108.899 60.122 109.726 60.2366 110.466 60.6064C111.201 60.9502 111.804 61.4698 112.271 62.16L112.515 62.5301C113.047 63.4009 113.324 64.3158 113.337 65.2719C113.351 66.3585 113.054 67.4164 112.453 68.4412L112.452 68.4432C112.347 68.6193 112.231 68.79 112.109 68.9597L117.931 73.3988L114.637 75.7211L114.486 75.6011L109.341 71.5053L107.603 72.7306L110.417 78.6962L107.372 80.8428L99.9687 65.1225L105.146 61.4721ZM111.795 69.3685C111.27 70.0078 110.622 70.6022 109.849 71.1473L109.78 71.1955L114.655 75.0776L117.058 73.383L111.795 69.3685ZM107.577 80.0676L109.766 78.5244L106.952 72.5587L108.921 71.171L108.263 70.6476L111.412 68.4273L111.699 68.6467C111.728 68.6053 111.76 68.5659 111.789 68.5242L112.009 68.1794C112.567 67.2271 112.834 66.2618 112.821 65.2795C112.808 64.3027 112.489 63.3628 111.85 62.457L111.847 62.4529C111.43 61.8351 110.897 61.3766 110.248 61.073L110.242 61.0704C109.606 60.7511 108.885 60.6443 108.07 60.7612L108.063 60.7622C107.262 60.8571 106.389 61.2266 105.443 61.8935L100.62 65.2944L107.577 80.0676ZM109.36 70.8612L109.552 70.726C110.267 70.2218 110.863 69.6802 111.346 69.1051L109.123 70.6721L109.36 70.8612ZM109.347 64.628C109.037 64.1883 108.652 63.9446 108.187 63.8739L108.177 63.8722C107.74 63.7873 107.188 63.9413 106.502 64.4249L104.393 65.9124L106.388 70.113L108.426 68.6765C109.215 68.1204 109.672 67.485 109.835 66.776L109.836 66.7732C110.01 66.0628 109.858 65.3531 109.347 64.628ZM109.769 64.331C110.35 65.1563 110.552 66.0165 110.337 66.8953C110.138 67.7557 109.587 68.4887 108.723 69.0978L106.186 70.8865L106.055 70.6094L103.834 65.9379L103.741 65.7411L106.205 64.0035C106.937 63.488 107.63 63.245 108.265 63.3643L108.491 63.4106C109.007 63.5442 109.434 63.8565 109.769 64.331Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    d: "M100.03 70.8484C99.589 70.2226 99.0496 69.7562 98.4113 69.4433L98.4094 69.4423C97.7709 69.1215 97.0489 69.0048 96.2358 69.101C95.4344 69.1958 94.5616 69.5656 93.6153 70.2328L88.6953 73.7016L95.6529 88.4748L97.8418 86.9316L95.1047 81.1151L97.7815 79.2278C98.8691 78.461 99.6849 77.6111 100.239 76.6812C100.796 75.73 101.059 74.7588 101.037 73.7638C101.019 72.7512 100.687 71.78 100.03 70.8484ZM94.2975 72.3117C95.0298 71.7955 95.7228 71.5441 96.3582 71.6386L96.5856 71.6816C97.1034 71.8105 97.5394 72.1416 97.8943 72.645L97.8967 72.6483C98.4628 73.4804 98.6609 74.3331 98.4496 75.1904L98.4482 75.1901C98.2513 76.0326 97.6798 76.7667 96.7821 77.3996L94.2056 79.2161L91.8877 74.2719L91.7959 74.0754L94.2975 72.3117ZM94.4105 78.4409L96.485 76.9783C97.3173 76.3916 97.7885 75.7531 97.9473 75.0702L97.9486 75.0668C98.1178 74.3801 97.9721 73.6772 97.473 72.942L97.3429 72.7714C97.0327 72.3985 96.6821 72.2011 96.2914 72.1502L96.2858 72.1492C95.8395 72.0815 95.2812 72.2489 94.5945 72.7331L92.446 74.2479L94.4105 78.4409ZM100.452 70.5513C101.161 71.5574 101.531 72.626 101.552 73.7524C101.576 74.8522 101.284 75.9176 100.682 76.9436C100.085 77.9473 99.2132 78.8491 98.0785 79.6491L95.7556 81.2869L98.4001 86.9075L98.4927 87.1034L95.448 89.25L88.0444 73.5298L93.3182 69.8115C94.3098 69.1124 95.263 68.697 96.1755 68.5891C97.0755 68.4827 97.8995 68.61 98.6381 68.9804L98.9035 69.121C99.5088 69.4678 100.025 69.9461 100.452 70.5513Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("path", {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M139.254 33.713C139.666 33.4358 140.202 33.372 140.646 33.5779L149.068 36.9906L151.39 29.4591C151.571 28.8508 152.127 28.453 152.748 28.4112C153.369 28.3695 153.949 28.7464 154.182 29.3269L160.068 43.2661C160.348 43.9266 160.107 44.6776 159.552 45.0754L142.342 56.7546C141.76 57.1681 140.965 57.0967 140.46 56.6038L129.667 45.9751C129.236 45.5465 129.094 44.8768 129.36 44.2905C129.625 43.7043 130.212 43.3598 130.827 43.4294L138.688 44.0517L138.617 34.9481C138.611 34.4494 138.843 33.9902 139.254 33.713Z",
    fill: "#0C0D0E"
  }), /*#__PURE__*/React.createElement("defs", null, /*#__PURE__*/React.createElement("pattern", {
    id: "pattern0_9066_32193",
    patternContentUnits: "objectBoundingBox",
    width: 1,
    height: 1
  }, /*#__PURE__*/React.createElement("use", {
    xlinkHref: "#image0_9066_32193",
    transform: "scale(0.00173611 0.0017301)"
  })), /*#__PURE__*/React.createElement("pattern", {
    id: "pattern1_9066_32193",
    patternContentUnits: "objectBoundingBox",
    width: 1,
    height: 1
  }, /*#__PURE__*/React.createElement("use", {
    xlinkHref: "#image1_9066_32193",
    transform: "scale(0.00309598 0.00204499)"
  })), /*#__PURE__*/React.createElement("pattern", {
    id: "pattern2_9066_32193",
    patternContentUnits: "objectBoundingBox",
    width: 1,
    height: 1
  }, /*#__PURE__*/React.createElement("use", {
    xlinkHref: "#image2_9066_32193",
    transform: "scale(0.00301205 0.00574713)"
  })), /*#__PURE__*/React.createElement("clipPath", {
    id: "clip0_9066_32193"
  }, /*#__PURE__*/React.createElement("rect", {
    width: 408,
    height: 204,
    fill: "white",
    transform: "translate(0.5)"
  })), /*#__PURE__*/React.createElement("image", {
    id: "image0_9066_32193",
    width: 576,
    height: 578,
    preserveAspectRatio: "none",
    xlinkHref: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAkAAAAJCCAYAAAAoUng9AAAACXBIWXMAAEtbAABLWwHvoCmtAAAgAElEQVR4Xu3d63Lruo4wWua8/zvn+9GHtbEwAZJK7MwLx6hK2RJB8CJHQjtz7f74/PwcAAA3+f92AQAA/xoFEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBRAAcB0FEABwHQUQAHAdBdAP+vj4+NzFAADv9/H56ZkMANzFN0C/wcfHx6dvgwDg9/ENEABwHd8A/eF8UwQAr+cbIADgOr4B+gf51ggA1hRAP2AWJE8Lk9P4HPf5+fnRxUan+QHgX6MA+kGnhcl0Gv/5+flRFTO7Auc0PwD8axRAP+A7hcbpt0dzjBi3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AQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDXUQABANdRAAEA11EAAQDX+X/Z+IWTCJcbtQAAAABJRU5ErkJggg=="
  }), /*#__PURE__*/React.createElement("image", {
    id: "image1_9066_32193",
    width: 323,
    height: 489,
    preserveAspectRatio: "none",
    xlinkHref: "data:image/png;base64,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DAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgEoMASoxBKjEEKASQ4BKDAEqMQSoxBCgEkOASgwBKjEEqMQQoBJDgKr+D2phk8xYkYdKAAAAAElFTkSuQmCC"
  }), /*#__PURE__*/React.createElement("image", {
    id: "image2_9066_32193",
    width: 332,
    height: 174,
    preserveAspectRatio: "none",
    xlinkHref: "data:image/png;base64,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"
  })));
};
var _default = exports["default"] = DeactivatedIcon;

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/deactivated-screen.js":
/*!******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/deactivated-screen.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = DeactivatedScreen;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ../index/index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ../index/index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _deactivatedIcon = _interopRequireDefault(__webpack_require__(/*! ./deactivated-icon */ "../app/modules/kit-library/assets/js/pages/cloud/deactivated-icon.js"));
function DeactivatedScreen(_ref) {
  var menuItems = _ref.menuItems,
    forceRefetch = _ref.forceRefetch,
    isFetching = _ref.isFetching;
  var renewUrl = 'https://go.elementor.com/go-pro-cloud-website-templates-library-advanced/';
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
      },
      isFetching: isFetching
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main e-kit-library__connect-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "center",
    direction: "column",
    className: "e-kit-library__error-screen"
  }, /*#__PURE__*/_react.default.createElement(_deactivatedIcon.default, null), /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "display-1",
    className: "e-kit-library__error-screen-title"
  }, __('Your library has been deactivated', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    variant: "xl",
    className: "e-kit-library__error-screen-description"
  }, __('Your subscription is currently deactivated, but you still have a 90 day window to keep all your templates safe. Upgrade within this time to continue enjoying them without interruption.', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: __('Upgrade now', 'elementor'),
    url: renewUrl,
    target: "_blank",
    className: "e-kit-library__upgrade-button"
  })))));
}
DeactivatedScreen.propTypes = {
  menuItems: _propTypes.default.array.isRequired,
  forceRefetch: _propTypes.default.func.isRequired,
  isFetching: _propTypes.default.bool.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/full-page-loader.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/full-page-loader.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = FullPageLoader;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ../index/index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ../index/index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _pageLoader = _interopRequireDefault(__webpack_require__(/*! ../../components/page-loader */ "../app/modules/kit-library/assets/js/components/page-loader.js"));
function FullPageLoader(_ref) {
  var menuItems = _ref.menuItems,
    forceRefetch = _ref.forceRefetch,
    isFetching = _ref.isFetching;
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
      },
      isFetching: isFetching
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main"
  }, /*#__PURE__*/_react.default.createElement(_pageLoader.default, null))));
}
FullPageLoader.propTypes = {
  menuItems: _propTypes.default.array.isRequired,
  forceRefetch: _propTypes.default.func.isRequired,
  isFetching: _propTypes.default.bool.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/cloud/upgrade-screen.js":
/*!**************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/cloud/upgrade-screen.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = UpgradeScreen;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ../index/index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ../index/index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function UpgradeScreen(_ref) {
  var menuItems = _ref.menuItems,
    forceRefetch = _ref.forceRefetch,
    isFetching = _ref.isFetching,
    cloudKitsData = _ref.cloudKitsData;
  var hasSubscription = '' !== (cloudKitsData === null || cloudKitsData === void 0 ? void 0 : cloudKitsData.subscription_id);
  var url = hasSubscription ? 'https://go.elementor.com/go-pro-cloud-website-templates-library-advanced/' : 'https://go.elementor.com/go-pro-cloud-website-templates-library/';
  (0, _react.useEffect)(function () {
    _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.kitLibrary.cloudKitLibraryUpgrade);
  }, []);
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
      },
      isFetching: isFetching
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main e-kit-library__connect-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "center",
    direction: "column",
    className: "e-kit-library__error-screen"
  }, /*#__PURE__*/_react.default.createElement("i", {
    className: "eicon-library-subscription-upgrade",
    "aria-hidden": "true"
  }), /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "display-1",
    className: "e-kit-library__error-screen-title"
  }, __('It\'s time to level up', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    variant: "xl",
    className: "e-kit-library__error-screen-description"
  }, __('Upgrade to Elementor Pro to import your own website template and save templates that you can reuse on any of your connected websites.', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    text: __('Upgrade now', 'elementor'),
    url: url,
    onClick: function onClick() {
      _appsEventTracking.AppsEventTracking.sendKitsCloudUpgradeClicked(elementorCommon.eventsManager.config.secondaryLocations.kitLibrary.cloudKitLibrary);
    },
    target: "_blank",
    className: "e-kit-library__upgrade-button"
  })))));
}
UpgradeScreen.propTypes = {
  menuItems: _propTypes.default.array.isRequired,
  forceRefetch: _propTypes.default.func.isRequired,
  isFetching: _propTypes.default.bool.isRequired,
  cloudKitsData: _propTypes.default.object.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/favorites/favorites.js":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/favorites/favorites.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Favorites;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _index = _interopRequireDefault(__webpack_require__(/*! ../index/index */ "../app/modules/kit-library/assets/js/pages/index/index.js"));
var _errorScreen = _interopRequireDefault(__webpack_require__(/*! ../../components/error-screen */ "../app/modules/kit-library/assets/js/components/error-screen.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _propTypes = _interopRequireDefault(__webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js"));
var _trackingContext = __webpack_require__(/*! ../../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function Favorites(props) {
  var navigate = (0, _router.useNavigate)();
  var tracking = (0, _trackingContext.useTracking)();

  // Track favorites tab opened when component mounts
  (0, _react.useEffect)(function () {
    tracking.trackKitlibFavoriteTab();
  }, [tracking]);
  var indexNotResultsFavorites = /*#__PURE__*/_react.default.createElement(_errorScreen.default
  // eslint-disable-next-line @wordpress/i18n-ellipsis
  , {
    title: __('No favorites here yet...', 'elementor'),
    description: __('Use the heart icon to save Website Templates that inspire you. You\'ll be able to find them here.', 'elementor'),
    button: {
      text: __('Continue browsing.', 'elementor'),
      action: function action() {
        return navigate('/kit-library');
      }
    }
  });
  return /*#__PURE__*/_react.default.createElement(_index.default, {
    path: props.path,
    initialQueryParams: {
      favorite: true
    },
    renderNoResultsComponent: function renderNoResultsComponent(_ref) {
      var defaultComponent = _ref.defaultComponent,
        isFilterActive = _ref.isFilterActive;
      if (!isFilterActive) {
        return indexNotResultsFavorites;
      }
      return defaultComponent;
    }
  });
}
Favorites.propTypes = {
  path: _propTypes.default.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/index/index-header.js":
/*!************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/index/index-header.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = IndexHeader;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _header = _interopRequireDefault(__webpack_require__(/*! ../../components/layout/header */ "../app/modules/kit-library/assets/js/components/layout/header.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _popoverDialog = _interopRequireDefault(__webpack_require__(/*! elementor-app/ui/popover-dialog/popover-dialog */ "../app/assets/js/ui/popover-dialog/popover-dialog.js"));
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./index-header.scss */ "../app/modules/kit-library/assets/js/pages/index/index-header.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function IndexHeader(props) {
  var _elementorAppConfig$u, _elementorAppConfig$u2;
  var navigate = (0, _router.useNavigate)();
  var _useState = (0, _react.useState)(false),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isInfoModalOpen = _useState2[0],
    setIsInfoModalOpen = _useState2[1];
  var importRef = (0, _react.useRef)();
  var eventTracking = function eventTracking(command) {
    var element = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var eventType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    var modalType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      element: element,
      event_type: eventType,
      page_source: 'home page',
      element_position: 'app_header',
      modal_type: modalType
    });
  };
  var _onClose = function onClose(e) {
    var element = e.target.classList.contains('eps-modal__overlay') ? 'overlay' : 'x';
    eventTracking('kit-library/modal-close', element, null, 'info');
  };
  var shouldShowImportButton = elementorAppConfig.user.is_administrator || ((_elementorAppConfig$u = (_elementorAppConfig$u2 = elementorAppConfig.user.restrictions) === null || _elementorAppConfig$u2 === void 0 ? void 0 : _elementorAppConfig$u2.includes('json-upload')) !== null && _elementorAppConfig$u !== void 0 ? _elementorAppConfig$u : false);
  var buttons = (0, _react.useMemo)(function () {
    return [{
      id: 'info',
      text: __('Info', 'elementor'),
      hideText: true,
      icon: 'eicon-info-circle-o',
      onClick: function onClick() {
        eventTracking('kit-library/seek-more-info');
        setIsInfoModalOpen(true);
      }
    }, {
      id: 'refetch',
      text: __('Refetch', 'elementor'),
      hideText: true,
      icon: "eicon-sync ".concat(props.isFetching ? 'eicon-animation-spin' : ''),
      onClick: function onClick() {
        eventTracking('kit-library/refetch');
        props.refetch();
      }
    }, shouldShowImportButton && {
      id: 'import',
      text: __('Import', 'elementor'),
      hideText: true,
      icon: 'eicon-upload-circle-o',
      elRef: importRef,
      onClick: function onClick() {
        eventTracking('kit-library/kit-import');
        navigate('/import?referrer=kit-library');
      }
    }];
  }, [props.isFetching, props.refetch, shouldShowImportButton]);
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, /*#__PURE__*/_react.default.createElement(_header.default, {
    buttons: buttons
  }), /*#__PURE__*/_react.default.createElement(_popoverDialog.default, {
    targetRef: importRef,
    wrapperClass: "e-kit-library__tooltip"
  }, __('Import Website Template', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.ModalProvider, {
    title: __('Welcome to the Library', 'elementor'),
    show: isInfoModalOpen,
    setShow: setIsInfoModalOpen,
    onOpen: function onOpen() {
      return eventTracking('kit-library/modal-open', null, 'load', 'info');
    },
    onClose: function onClose(e) {
      return _onClose(e);
    }
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library-header-info-modal-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "h3"
  }, __('What\'s a Website Template?', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Text, null, __('A Website Template is full, ready-made design that you can apply to your site. It includes all the pages, parts, settings and content that you\'d expect in a fully functional website.', 'elementor'))), /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library-header-info-modal-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "h3"
  }, __('What\'s going on in the Website Templates Library?', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Text, null, __('Search & filter for website templates by category and tags, or browse through individual website templates to see what\'s inside.', 'elementor'), /*#__PURE__*/_react.default.createElement("br", null), __('Once you\'ve picked a winner, apply it to your site!', 'elementor'))), /*#__PURE__*/_react.default.createElement("div", null, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "h3"
  }, __('Happy browsing!', 'elementor')), /*#__PURE__*/_react.default.createElement(_appUi.Text, null, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    url: "https://go.elementor.com/app-kit-library-how-to-use-kits/",
    target: "_blank",
    rel: "noreferrer",
    text: __('Learn more', 'elementor'),
    color: "link",
    onClick: function onClick() {
      eventTracking('kit-library/seek-more-info', 'text link', null, 'info');
    }
  }), ' ', __('about using templates', 'elementor')))));
}
IndexHeader.propTypes = {
  refetch: PropTypes.func.isRequired,
  isFetching: PropTypes.bool
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/index/index-header.scss":
/*!**************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/index/index-header.scss ***!
  \**************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/index/index-sidebar.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = IndexSidebar;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
function IndexSidebar(props) {
  var eventTracking = function eventTracking(command, category, source) {
    var eventType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
    return (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      category: category,
      source: source,
      element_location: 'app_sidebar',
      event_type: eventType
    });
  };
  return /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, props.menuItems.map(function (item) {
    return /*#__PURE__*/_react.default.createElement(_appUi.MenuItem, {
      key: item.label,
      text: item.label,
      className: "eps-menu-item__link ".concat(item.isActive ? 'eps-menu-item--active' : ''),
      icon: item.icon,
      url: item.url,
      onClick: function onClick() {
        return eventTracking(item.trackEventData.command, item.trackEventData.category, 'home page');
      }
    });
  }), props.tagsFilterSlot);
}
IndexSidebar.propTypes = {
  tagsFilterSlot: PropTypes.node,
  menuItems: PropTypes.arrayOf(PropTypes.shape({
    label: PropTypes.string,
    icon: PropTypes.string,
    isActive: PropTypes.bool,
    url: PropTypes.string
  }))
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/index/index.js":
/*!*****************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/index/index.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Index;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _toConsumableArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "../node_modules/@babel/runtime/helpers/toConsumableArray.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _content = _interopRequireDefault(__webpack_require__(/*! ../../../../../../assets/js/layout/content */ "../app/assets/js/layout/content.js"));
var _envatoPromotion = _interopRequireDefault(__webpack_require__(/*! ../../components/envato-promotion */ "../app/modules/kit-library/assets/js/components/envato-promotion.js"));
var _errorScreen = _interopRequireDefault(__webpack_require__(/*! ../../components/error-screen */ "../app/modules/kit-library/assets/js/components/error-screen.js"));
var _filterIndicationText = _interopRequireDefault(__webpack_require__(/*! ../../components/filter-indication-text */ "../app/modules/kit-library/assets/js/components/filter-indication-text.js"));
var _indexHeader = _interopRequireDefault(__webpack_require__(/*! ./index-header */ "../app/modules/kit-library/assets/js/pages/index/index-header.js"));
var _indexSidebar = _interopRequireDefault(__webpack_require__(/*! ./index-sidebar */ "../app/modules/kit-library/assets/js/pages/index/index-sidebar.js"));
var _kitList = _interopRequireDefault(__webpack_require__(/*! ../../components/kit-list */ "../app/modules/kit-library/assets/js/components/kit-list.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _pageLoader = _interopRequireDefault(__webpack_require__(/*! ../../components/page-loader */ "../app/modules/kit-library/assets/js/components/page-loader.js"));
var _searchInput = _interopRequireDefault(__webpack_require__(/*! ../../components/search-input */ "../app/modules/kit-library/assets/js/components/search-input.js"));
var _sortSelect = _interopRequireDefault(__webpack_require__(/*! ../../components/sort-select */ "../app/modules/kit-library/assets/js/components/sort-select.js"));
var _taxonomiesFilter = _interopRequireDefault(__webpack_require__(/*! ../../components/taxonomies-filter */ "../app/modules/kit-library/assets/js/components/taxonomies-filter.js"));
var _useKits2 = _interopRequireWildcard(__webpack_require__(/*! ../../hooks/use-kits */ "../app/modules/kit-library/assets/js/hooks/use-kits.js"));
var _useMenuItems = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-menu-items */ "../app/modules/kit-library/assets/js/hooks/use-menu-items.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _useTaxonomies2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-taxonomies */ "../app/modules/kit-library/assets/js/hooks/use-taxonomies.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _lastFilterContext = __webpack_require__(/*! ../../context/last-filter-context */ "../app/modules/kit-library/assets/js/context/last-filter-context.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
__webpack_require__(/*! ./index.scss */ "../app/modules/kit-library/assets/js/pages/index/index.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
/**
 * Generate select and unselect taxonomy functions.
 *
 * @param {Function} setQueryParams
 * @return {((function(*, *): *)|(function(*=): *))[]} taxonomy functions
 */
function useTaxonomiesSelection(setQueryParams) {
  var selectTaxonomy = (0, _react.useCallback)(function (type, callback) {
    return setQueryParams(function (prev) {
      var taxonomies = _objectSpread({}, prev.taxonomies);
      taxonomies[type] = callback(prev.taxonomies[type]);
      return _objectSpread(_objectSpread({}, prev), {}, {
        taxonomies: taxonomies
      });
    });
  }, [setQueryParams]);
  var unselectTaxonomy = (0, _react.useCallback)(function (taxonomy) {
    return setQueryParams(function (prev) {
      var taxonomies = Object.entries(prev.taxonomies).reduce(function (current, _ref) {
        var _ref2 = (0, _slicedToArray2.default)(_ref, 2),
          key = _ref2[0],
          groupedTaxonomies = _ref2[1];
        return _objectSpread(_objectSpread({}, current), {}, (0, _defineProperty2.default)({}, key, groupedTaxonomies.filter(function (item) {
          return item !== taxonomy;
        })));
      }, {});
      return _objectSpread(_objectSpread({}, prev), {}, {
        taxonomies: taxonomies
      });
    });
  }, [setQueryParams]);
  return [selectTaxonomy, unselectTaxonomy];
}

/**
 * Update and read the query param from the url
 *
 * @param {*}             queryParams
 * @param {*}             setQueryParams
 * @param {Array<string>} exclude
 */
function useRouterQueryParams(queryParams, setQueryParams) {
  var exclude = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : [];
  var location = (0, _router.useLocation)(),
    _useLastFilterContext = (0, _lastFilterContext.useLastFilterContext)(),
    setLastFilter = _useLastFilterContext.setLastFilter;
  (0, _react.useEffect)(function () {
    var filteredQueryParams = Object.fromEntries(Object.entries(queryParams).filter(function (_ref3) {
      var _ref4 = (0, _slicedToArray2.default)(_ref3, 2),
        key = _ref4[0],
        item = _ref4[1];
      return !exclude.includes(key) && item;
    }));
    setLastFilter(filteredQueryParams);
    history.replaceState(null, '', decodeURI("#".concat(wp.url.addQueryArgs(location.pathname.split('?')[0] || '/', filteredQueryParams))));
  }, [queryParams]);
  (0, _react.useEffect)(function () {
    var routerQueryParams = Object.keys(_useKits2.defaultQueryParams).reduce(function (current, key) {
      // TODO: Replace with `wp.url.getQueryArgs` when WordPress 5.7 is the min version
      var queryArg = wp.url.getQueryArg(location.pathname, key);
      if (!queryArg) {
        return current;
      }
      return _objectSpread(_objectSpread({}, current), {}, (0, _defineProperty2.default)({}, key, queryArg));
    }, {});
    setQueryParams(function (prev) {
      return _objectSpread(_objectSpread(_objectSpread({}, prev), routerQueryParams), {}, {
        taxonomies: _objectSpread(_objectSpread({}, prev.taxonomies), routerQueryParams.taxonomies),
        ready: true
      });
    });
  }, []);
}
function Index(props) {
  (0, _usePageTitle.default)({
    title: __('Website Templates', 'elementor')
  });
  var menuItems = (0, _useMenuItems.default)(props.path);
  var tracking = (0, _trackingContext.useTracking)();
  var _useKits = (0, _useKits2.default)(props.initialQueryParams),
    data = _useKits.data,
    isSuccess = _useKits.isSuccess,
    isLoading = _useKits.isLoading,
    isFetching = _useKits.isFetching,
    isError = _useKits.isError,
    queryParams = _useKits.queryParams,
    setQueryParams = _useKits.setQueryParams,
    clearQueryParams = _useKits.clearQueryParams,
    forceRefetch = _useKits.forceRefetch,
    isFilterActive = _useKits.isFilterActive;
  useRouterQueryParams(queryParams, setQueryParams, ['ready'].concat((0, _toConsumableArray2.default)(Object.keys(props.initialQueryParams))));
  (0, _react.useEffect)(function () {
    if (!queryParams.search) {
      return;
    }
    tracking.trackKitlibSearchSubmitted(queryParams.search, data.length);
  }, [queryParams.search, data.length, tracking]);
  var _useTaxonomies = (0, _useTaxonomies2.default)(),
    taxonomiesData = _useTaxonomies.data,
    forceRefetchTaxonomies = _useTaxonomies.forceRefetch,
    isFetchingTaxonomies = _useTaxonomies.isFetching;
  var _useTaxonomiesSelecti = useTaxonomiesSelection(setQueryParams),
    _useTaxonomiesSelecti2 = (0, _slicedToArray2.default)(_useTaxonomiesSelecti, 2),
    selectTaxonomy = _useTaxonomiesSelecti2[0],
    unselectTaxonomy = _useTaxonomiesSelecti2[1];
  var eventTracking = function eventTracking(command, elementPosition) {
    var search = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var direction = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var sortType = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    var action = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : null;
    var eventType = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      page_source: 'home page',
      element_position: elementPosition,
      search_term: search,
      sort_direction: direction,
      sort_type: sortType,
      event_type: eventType,
      action: action
    });
  };
  var options = [{
    label: __('Featured', 'elementor'),
    value: 'featuredIndex',
    defaultOrder: 'asc',
    orderDisabled: true
  }, {
    label: __('New', 'elementor'),
    value: 'createdAt',
    defaultOrder: 'desc'
  }, {
    label: __('Popular', 'elementor'),
    value: 'popularityIndex',
    defaultOrder: 'desc'
  }, {
    label: __('Trending', 'elementor'),
    value: 'trendIndex',
    defaultOrder: 'desc'
  }];
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    sidebar: /*#__PURE__*/_react.default.createElement(_indexSidebar.default, {
      tagsFilterSlot: /*#__PURE__*/_react.default.createElement(_taxonomiesFilter.default, {
        selected: queryParams.taxonomies,
        onSelect: selectTaxonomy,
        taxonomies: taxonomiesData,
        category: props.path
      }),
      menuItems: menuItems
    }),
    header: /*#__PURE__*/_react.default.createElement(_indexHeader.default, {
      refetch: function refetch() {
        forceRefetch();
        forceRefetchTaxonomies();
      },
      isFetching: isFetching || isFetchingTaxonomies
    })
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__index-layout-container"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    className: "e-kit-library__index-layout-heading"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    item: true,
    className: "e-kit-library__index-layout-heading-search"
  }, /*#__PURE__*/_react.default.createElement(_searchInput.default, {
    placeholder: __('Search all Website Templates...', 'elementor'),
    value: queryParams.search,
    onChange: function onChange(value) {
      eventTracking('kit-library/kit-free-search', 'top_area_search', value, null, null, null, 'search');
      setQueryParams(function (prev) {
        return _objectSpread(_objectSpread({}, prev), {}, {
          search: value
        });
      });
    }
  }), isFilterActive && /*#__PURE__*/_react.default.createElement(_filterIndicationText.default, {
    queryParams: queryParams,
    resultCount: data.length || 0,
    onClear: clearQueryParams,
    onRemoveTag: unselectTaxonomy
  })), /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    item: true,
    className: "e-kit-library__index-layout-heading-sort"
  }, /*#__PURE__*/_react.default.createElement(_sortSelect.default, {
    options: options,
    value: queryParams.order,
    onChange: function onChange(order) {
      return setQueryParams(function (prev) {
        return _objectSpread(_objectSpread({}, prev), {}, {
          order: order
        });
      });
    },
    onChangeSortDirection: function onChangeSortDirection(direction) {
      eventTracking('kit-library/change-sort-direction', 'top_area_sort', null, direction);
    },
    onChangeSortValue: function onChangeSortValue(value) {
      eventTracking('kit-library/change-sort-value', 'top_area_sort', null, null, value);
      var label = options.find(function (option) {
        return option.value === value;
      }).label;
      tracking.trackKitlibSorterSelected(label);
    },
    onSortSelectOpen: function onSortSelectOpen() {
      return eventTracking('kit-library/change-sort-type', 'top_area_sort', null, null, null, 'expand');
    }
  }))), /*#__PURE__*/_react.default.createElement(_content.default, {
    className: "e-kit-library__index-layout-main"
  }, /*#__PURE__*/_react.default.createElement(_react.default.Fragment, null, isLoading && /*#__PURE__*/_react.default.createElement(_pageLoader.default, null), isError && /*#__PURE__*/_react.default.createElement(_errorScreen.default, {
    title: __('Something went wrong.', 'elementor'),
    description: __('Nothing to worry about, use 🔄 on the top corner to try again. If the problem continues, head over to the Help Center.', 'elementor'),
    button: {
      text: __('Learn More', 'elementor'),
      url: 'https://go.elementor.com/app-kit-library-error/',
      target: '_blank'
    }
  }), isSuccess && 0 < data.length && queryParams.ready && /*#__PURE__*/_react.default.createElement(_kitList.default, {
    data: data,
    queryParams: queryParams,
    source: props.path
  }), isSuccess && 0 === data.length && queryParams.ready && props.renderNoResultsComponent({
    defaultComponent: /*#__PURE__*/_react.default.createElement(_errorScreen.default, {
      title: __('No results matched your search.', 'elementor'),
      description: __('Try different keywords or ', 'elementor'),
      button: {
        text: __('Continue browsing.', 'elementor'),
        action: clearQueryParams,
        category: props.path
      }
    }),
    isFilterActive: isFilterActive
  }), /*#__PURE__*/_react.default.createElement(_envatoPromotion.default, {
    category: props.path
  })))));
}
Index.propTypes = {
  path: PropTypes.string,
  initialQueryParams: PropTypes.object,
  renderNoResultsComponent: PropTypes.func
};
Index.defaultProps = {
  initialQueryParams: {},
  renderNoResultsComponent: function renderNoResultsComponent(_ref5) {
    var defaultComponent = _ref5.defaultComponent;
    return defaultComponent;
  }
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/index/index.scss":
/*!*******************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/index/index.scss ***!
  \*******************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview-content-group-item.js":
/*!******************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview-content-group-item.js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = OverviewContentGroupItem;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _document = _interopRequireDefault(__webpack_require__(/*! ../../models/document */ "../app/modules/kit-library/assets/js/models/document.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
function OverviewContentGroupItem(props) {
  var eventTracking = function eventTracking(command) {
    var eventType = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      kit_name: props.kitTitle,
      document_type: props.groupData.id,
      document_name: "".concat(props.groupData.label, "-").concat(props.document.title),
      page_source: 'overview',
      element_position: 'content_overview',
      event_type: eventType
    });
  };
  return /*#__PURE__*/_react.default.createElement(_appUi.Card, null, /*#__PURE__*/_react.default.createElement(_appUi.CardHeader, null, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    title: props.document.title,
    variant: "h5",
    className: "eps-card__headline"
  }, props.document.title)), /*#__PURE__*/_react.default.createElement(_appUi.CardBody, null, /*#__PURE__*/_react.default.createElement(_appUi.CardImage, {
    alt: props.document.title,
    src: props.document.thumbnailUrl || ''
  }, props.document.previewUrl && /*#__PURE__*/_react.default.createElement(_appUi.CardOverlay, null, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__kit-item-overlay-overview-button",
    text: __('View Demo', 'elementor'),
    icon: "eicon-preview-medium",
    url: "/kit-library/preview/".concat(props.kitId, "?document_id=").concat(props.document.id),
    onClick: function onClick() {
      return eventTracking('kit-library/view-demo-part');
    }
  })))));
}
OverviewContentGroupItem.propTypes = {
  document: PropTypes.instanceOf(_document.default).isRequired,
  kitId: PropTypes.string.isRequired,
  kitTitle: PropTypes.string.isRequired,
  groupData: PropTypes.shape({
    label: PropTypes.string,
    id: PropTypes.string
  }).isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview-content-group.js":
/*!*************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview-content-group.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = OverviewContentGroup;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _contentType = _interopRequireDefault(__webpack_require__(/*! ../../models/content-type */ "../app/modules/kit-library/assets/js/models/content-type.js"));
var _overviewContentGroupItem = _interopRequireDefault(__webpack_require__(/*! ./overview-content-group-item */ "../app/modules/kit-library/assets/js/pages/overview/overview-content-group-item.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
function OverviewContentGroup(props) {
  var _props$contentType;
  if (((_props$contentType = props.contentType) === null || _props$contentType === void 0 || (_props$contentType = _props$contentType.documents) === null || _props$contentType === void 0 ? void 0 : _props$contentType.length) <= 0) {
    return '';
  }
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__content-overview-group-item"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    variant: "h3",
    className: "e-kit-library__content-overview-group-title"
  }, props.contentType.label), /*#__PURE__*/_react.default.createElement(_appUi.CssGrid, {
    spacing: 24,
    colMinWidth: 250
  }, props.contentType.documents.map(function (document) {
    return /*#__PURE__*/_react.default.createElement(_overviewContentGroupItem.default, {
      key: document.id,
      document: document,
      kitId: props.kitId,
      kitTitle: props.kitTitle,
      groupData: props.contentType
    });
  })));
}
OverviewContentGroup.propTypes = {
  contentType: PropTypes.instanceOf(_contentType.default),
  kitId: PropTypes.string.isRequired,
  kitTitle: PropTypes.string.isRequired
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.js":
/*!*******************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = OverviewSidebar;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _collapse = _interopRequireDefault(__webpack_require__(/*! ../../components/collapse */ "../app/modules/kit-library/assets/js/components/collapse.js"));
var _contentType = _interopRequireDefault(__webpack_require__(/*! ../../models/content-type */ "../app/modules/kit-library/assets/js/models/content-type.js"));
var _favoritesActions = _interopRequireDefault(__webpack_require__(/*! ../../components/favorites-actions */ "../app/modules/kit-library/assets/js/components/favorites-actions.js"));
var _kit = _interopRequireDefault(__webpack_require__(/*! ../../models/kit */ "../app/modules/kit-library/assets/js/models/kit.js"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./overview-sidebar.scss */ "../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function OverviewSidebar(props) {
  var _props$groupedKitCont;
  var _useState = (0, _react.useState)(true),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isInformationCollapseOpen = _useState2[0],
    setIsInformationCollapseOpen = _useState2[1];
  var eventTracking = function eventTracking(command) {
    var section = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var kitName = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var tag = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
    var isCollapsed = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
    var eventType = arguments.length > 5 && arguments[5] !== undefined ? arguments[5] : 'click';
    var action = isCollapsed && isCollapsed ? 'collapse' : 'expand';
    if ('boolean' === typeof isCollapsed) {
      command = "kit-library/".concat(action);
    }
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      page_source: 'overview',
      element_location: 'app_sidebar',
      kit_name: kitName,
      tag: tag,
      section: section,
      event_type: eventType
    });
  };
  return /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__item-sidebar"
  }, /*#__PURE__*/_react.default.createElement("div", {
    className: "e-kit-library__item-sidebar-header"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h1",
    variant: "h5",
    className: "e-kit-library__item-sidebar-header-title"
  }, props.model.title), /*#__PURE__*/_react.default.createElement(_favoritesActions.default, {
    isFavorite: props.model.isFavorite,
    id: props.model.id
  })), /*#__PURE__*/_react.default.createElement(_appUi.CardImage, {
    className: "e-kit-library__item-sidebar-thumbnail",
    alt: props.model.title,
    src: props.model.thumbnailUrl || ''
  }), /*#__PURE__*/_react.default.createElement(_appUi.Text, {
    className: "e-kit-library__item-sidebar-description"
  }, props.model.description || ''), ((_props$groupedKitCont = props.groupedKitContent) === null || _props$groupedKitCont === void 0 ? void 0 : _props$groupedKitCont.length) > 0 && props.model.documents.length > 0 && /*#__PURE__*/_react.default.createElement(_collapse.default, {
    isOpen: isInformationCollapseOpen,
    onChange: setIsInformationCollapseOpen,
    title: __('WHAT\'S INSIDE', 'elementor'),
    className: "e-kit-library__item-sidebar-collapse-info",
    onClick: function onClick(collapseState, title) {
      eventTracking(null, title, null, null, collapseState);
    }
  }, props.groupedKitContent.map(function (contentType) {
    if (contentType.documents <= 0) {
      return '';
    }
    return /*#__PURE__*/_react.default.createElement(_appUi.Text, {
      className: "e-kit-library__item-information-text",
      key: contentType.id
    }, contentType.documents.length, " ", contentType.label);
  })));
}
OverviewSidebar.propTypes = {
  model: PropTypes.instanceOf(_kit.default).isRequired,
  index: PropTypes.number,
  groupedKitContent: PropTypes.arrayOf(PropTypes.instanceOf(_contentType.default))
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.scss":
/*!*********************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.scss ***!
  \*********************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview.js":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Overview;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _content = _interopRequireDefault(__webpack_require__(/*! elementor-app/layout/content */ "../app/assets/js/layout/content.js"));
var _elementorLoading = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/elementor-loading */ "../app/assets/js/molecules/elementor-loading.js"));
var _itemHeader = _interopRequireDefault(__webpack_require__(/*! ../../components/item-header */ "../app/modules/kit-library/assets/js/components/item-header.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _overviewContentGroup = _interopRequireDefault(__webpack_require__(/*! ./overview-content-group */ "../app/modules/kit-library/assets/js/pages/overview/overview-content-group.js"));
var _overviewSidebar = _interopRequireDefault(__webpack_require__(/*! ./overview-sidebar */ "../app/modules/kit-library/assets/js/pages/overview/overview-sidebar.js"));
var _useKit2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-kit */ "../app/modules/kit-library/assets/js/hooks/use-kit.js"));
var _useKitDocumentByType2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-kit-document-by-type */ "../app/modules/kit-library/assets/js/hooks/use-kit-document-by-type.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
__webpack_require__(/*! ./overview.scss */ "../app/modules/kit-library/assets/js/pages/overview/overview.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
function useHeaderButtons(id, kitName) {
  var navigate = (0, _router.useNavigate)();
  return (0, _react.useMemo)(function () {
    return [{
      id: 'view-demo',
      text: __('View Demo', 'elementor'),
      hideText: false,
      variant: 'outlined',
      color: 'secondary',
      size: 'sm',
      onClick: function onClick() {
        (0, _appsEventTracking.appsEventTrackingDispatch)('kit-library/view-demo-page', {
          kit_name: kitName,
          page_source: 'overview',
          element_position: 'app_header',
          view_type_clicked: 'demo'
        });
        navigate("/kit-library/preview/".concat(id));
      },
      includeHeaderBtnClass: false
    }];
  }, [id, kitName, navigate]);
}
function Overview(props) {
  var _useKit = (0, _useKit2.default)(props.id),
    kit = _useKit.data,
    isError = _useKit.isError,
    isLoading = _useKit.isLoading;
  var _useKitDocumentByType = (0, _useKitDocumentByType2.default)(kit),
    documentsByType = _useKitDocumentByType.data;
  var headerButtons = useHeaderButtons(props.id, kit && kit.title);
  (0, _usePageTitle.default)({
    title: kit ? "".concat(__('Kit Library', 'elementor'), " | ").concat(kit.title) // eslint-disable-next-line @wordpress/i18n-ellipsis
    : __('Loading...', 'elementor')
  });
  if (isError) {
    // Will be caught by the App error boundary.
    throw new Error();
  }
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_elementorLoading.default, null);
  }
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    header: /*#__PURE__*/_react.default.createElement(_itemHeader.default, {
      model: kit,
      buttons: headerButtons,
      pageId: "overview"
    }),
    sidebar: /*#__PURE__*/_react.default.createElement(_overviewSidebar.default, {
      model: kit,
      groupedKitContent: documentsByType
    })
  }, documentsByType.length > 0 && /*#__PURE__*/_react.default.createElement(_content.default, null, documentsByType.map(function (contentType) {
    return /*#__PURE__*/_react.default.createElement(_overviewContentGroup.default, {
      key: contentType.id,
      contentType: contentType,
      kitId: props.id,
      kitTitle: kit.title
    });
  })));
}
Overview.propTypes = {
  id: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/overview/overview.scss":
/*!*************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/overview/overview.scss ***!
  \*************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/preview/preview-iframe.js":
/*!****************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/preview/preview-iframe.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.PreviewIframe = PreviewIframe;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
/* eslint-disable jsx-a11y/iframe-has-title */

function PreviewIframe(props) {
  var ref = (0, _react.useRef)();
  (0, _react.useEffect)(function () {
    if (!ref.current) {
      return;
    }
    var listener = function listener() {
      return props.onLoaded();
    };
    ref.current.addEventListener('load', listener);
    return function () {
      return ref.current && ref.current.removeEventListener('load', listener);
    };
  }, [ref.current, props.previewUrl]);
  return /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    justify: "center",
    className: "e-kit-library__preview-iframe-container"
  }, /*#__PURE__*/_react.default.createElement("iframe", {
    className: "e-kit-library__preview-iframe",
    src: props.previewUrl,
    style: props.style,
    ref: ref
  }));
}
PreviewIframe.propTypes = {
  previewUrl: PropTypes.string.isRequired,
  style: PropTypes.object,
  onLoaded: PropTypes.func
};
PreviewIframe.defaultProps = {
  style: {
    width: '100%',
    height: '100%'
  },
  onLoaded: function onLoaded() {}
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.js":
/*!*****************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PreviewResponsiveControls;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _preview = __webpack_require__(/*! ./preview */ "../app/modules/kit-library/assets/js/pages/preview/preview.js");
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
__webpack_require__(/*! ./preview-responsive-controls.scss */ "../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.scss");
function PreviewResponsiveControls(props) {
  return /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    alignItems: "center",
    justify: "center",
    className: "e-kit-library__preview-responsive-controls"
  }, _preview.breakpoints.map(function (_ref) {
    var label = _ref.label,
      value = _ref.value;
    var className = 'e-kit-library__preview-responsive-controls-item';
    if (props.active === value) {
      className += ' e-kit-library__preview-responsive-controls-item--active';
    }
    return /*#__PURE__*/_react.default.createElement(_appUi.Button, {
      key: value,
      text: label,
      hideText: true,
      className: className,
      icon: "eicon-device-".concat(value),
      onClick: function onClick() {
        return props.onChange(value);
      }
    });
  }));
}
PreviewResponsiveControls.propTypes = {
  active: PropTypes.string,
  onChange: PropTypes.func.isRequired
};
PreviewResponsiveControls.defaultProps = {
  active: 'desktop'
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.scss":
/*!*******************************************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.scss ***!
  \*******************************************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/preview/preview.js":
/*!*********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/preview/preview.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];
/* provided dependency */ var PropTypes = __webpack_require__(/*! prop-types */ "../node_modules/prop-types/index.js");


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _typeof = __webpack_require__(/*! @babel/runtime/helpers/typeof */ "../node_modules/@babel/runtime/helpers/typeof.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.breakpoints = void 0;
exports["default"] = Preview;
var _react = _interopRequireWildcard(__webpack_require__(/*! react */ "react"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _elementorLoading = _interopRequireDefault(__webpack_require__(/*! elementor-app/molecules/elementor-loading */ "../app/assets/js/molecules/elementor-loading.js"));
var _itemHeader = _interopRequireDefault(__webpack_require__(/*! ../../components/item-header */ "../app/modules/kit-library/assets/js/components/item-header.js"));
var _layout = _interopRequireDefault(__webpack_require__(/*! ../../components/layout */ "../app/modules/kit-library/assets/js/components/layout/index.js"));
var _pageLoader = _interopRequireDefault(__webpack_require__(/*! ../../components/page-loader */ "../app/modules/kit-library/assets/js/components/page-loader.js"));
var _previewResponsiveControls = _interopRequireDefault(__webpack_require__(/*! ./preview-responsive-controls */ "../app/modules/kit-library/assets/js/pages/preview/preview-responsive-controls.js"));
var _useKit2 = _interopRequireDefault(__webpack_require__(/*! ../../hooks/use-kit */ "../app/modules/kit-library/assets/js/hooks/use-kit.js"));
var _usePageTitle = _interopRequireDefault(__webpack_require__(/*! elementor-app/hooks/use-page-title */ "../app/assets/js/hooks/use-page-title.js"));
var _previewIframe = __webpack_require__(/*! ./preview-iframe */ "../app/modules/kit-library/assets/js/pages/preview/preview-iframe.js");
var _router = __webpack_require__(/*! @reach/router */ "../node_modules/@reach/router/es/index.js");
var _appsEventTracking = __webpack_require__(/*! elementor-app/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _trackingContext = __webpack_require__(/*! ../../context/tracking-context */ "../app/modules/kit-library/assets/js/context/tracking-context.js");
__webpack_require__(/*! ./preview.scss */ "../app/modules/kit-library/assets/js/pages/preview/preview.scss");
function _interopRequireWildcard(e, t) { if ("function" == typeof WeakMap) var r = new WeakMap(), n = new WeakMap(); return (_interopRequireWildcard = function _interopRequireWildcard(e, t) { if (!t && e && e.__esModule) return e; var o, i, f = { __proto__: null, default: e }; if (null === e || "object" != _typeof(e) && "function" != typeof e) return f; if (o = t ? n : r) { if (o.has(e)) return o.get(e); o.set(e, f); } for (var _t in e) "default" !== _t && {}.hasOwnProperty.call(e, _t) && ((i = (o = Object.defineProperty) && Object.getOwnPropertyDescriptor(e, _t)) && (i.get || i.set) ? o(f, _t, i) : f[_t] = e[_t]); return f; })(e, t); }
var breakpoints = exports.breakpoints = [{
  value: 'desktop',
  label: __('Desktop', 'elementor'),
  style: {
    width: '100%',
    height: '100%'
  }
}, {
  value: 'tablet',
  label: __('Tablet', 'elementor'),
  style: {
    marginBlockStart: '30px',
    marginBlockEnd: '30px',
    width: '768px',
    height: '1024px'
  }
}, {
  value: 'mobile',
  label: __('Mobile', 'elementor'),
  style: {
    marginBlockStart: '30px',
    marginBlockEnd: '30px',
    width: '375px',
    height: '667px'
  }
}];
function useHeaderButtons(id, kitName) {
  var navigate = (0, _router.useNavigate)();
  var tracking = (0, _trackingContext.useTracking)();
  var eventTracking = function eventTracking(command, viewTypeClicked) {
    var eventType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      kit_name: kitName,
      element_position: 'app_header',
      page_source: 'view demo',
      view_type_clicked: viewTypeClicked,
      event_type: eventType
    });
  };
  return (0, _react.useMemo)(function () {
    return [{
      id: 'overview',
      text: __('Overview', 'elementor'),
      hideText: false,
      variant: 'outlined',
      color: 'secondary',
      size: 'sm',
      onClick: function onClick() {
        eventTracking('kit-library/view-overview-page', 'overview');
        tracking.trackKitdemoOverviewClicked(id, kitName, function () {
          return navigate("/kit-library/overview/".concat(id));
        });
      },
      includeHeaderBtnClass: false
    }];
  }, [id, kitName, tracking, navigate]);
}

/**
 * Get preview url.
 *
 * @param {*} data
 * @return {null|string} Preview URL
 */
function usePreviewUrl(data) {
  var location = (0, _router.useLocation)();
  return (0, _react.useMemo)(function () {
    var _location$pathname$sp, _data$documents$find;
    if (!data) {
      return null;
    }
    var documentId = new URLSearchParams((_location$pathname$sp = location.pathname.split('?')) === null || _location$pathname$sp === void 0 ? void 0 : _location$pathname$sp[1]).get('document_id'),
      utm = '?utm_source=kit-library&utm_medium=wp-dash&utm_campaign=preview',
      previewUrl = data.previewUrl ? data.previewUrl + utm : data.previewUrl;
    if (!documentId) {
      return previewUrl;
    }
    var documentPreviewUrl = ((_data$documents$find = data.documents.find(function (item) {
      return item.id === parseInt(documentId);
    })) === null || _data$documents$find === void 0 ? void 0 : _data$documents$find.previewUrl) || previewUrl;
    return documentPreviewUrl ? documentPreviewUrl + utm : documentPreviewUrl;
  }, [location, data]);
}
function Preview(props) {
  var _useKit = (0, _useKit2.default)(props.id),
    data = _useKit.data,
    isError = _useKit.isError,
    isLoading = _useKit.isLoading;
  var _useState = (0, _react.useState)(true),
    _useState2 = (0, _slicedToArray2.default)(_useState, 2),
    isIframeLoading = _useState2[0],
    setIsIframeLoading = _useState2[1];
  var headersButtons = useHeaderButtons(props.id, data && data.title);
  var previewUrl = usePreviewUrl(data);
  var _useState3 = (0, _react.useState)('desktop'),
    _useState4 = (0, _slicedToArray2.default)(_useState3, 2),
    activeDevice = _useState4[0],
    setActiveDevice = _useState4[1];
  var tracking = (0, _trackingContext.useTracking)();
  var loadStartTime = (0, _react.useRef)(Date.now());
  var hasTrackedOpen = (0, _react.useRef)(false);
  var iframeStyle = (0, _react.useMemo)(function () {
    return breakpoints.find(function (_ref) {
      var value = _ref.value;
      return value === activeDevice;
    }).style;
  }, [activeDevice]);
  (0, _react.useEffect)(function () {
    if (!isIframeLoading && data && !hasTrackedOpen.current) {
      var loadTime = Date.now() - loadStartTime.current;
      tracking.trackKitdemoOpened(props.id, data.title, loadTime);
      hasTrackedOpen.current = true;
    }
  }, [isIframeLoading, data, props.id, tracking]);
  var eventTracking = function eventTracking(command, layout) {
    var elementPosition = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    var eventType = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 'click';
    (0, _appsEventTracking.appsEventTrackingDispatch)(command, {
      kit_name: data.title,
      page_source: 'view demo',
      layout: layout,
      element_position: elementPosition,
      event_type: eventType
    });
  };
  var _onChange = function onChange(device) {
    setActiveDevice(device);
    eventTracking('kit-library/responsive-controls', device, 'app_header');
  };
  (0, _usePageTitle.default)({
    title: data ? "".concat(__('Kit Library', 'elementor'), " | ").concat(data.title) : __('Loading...', 'elementor')
  });
  if (isError) {
    // Will be caught by the App error boundary.
    throw new Error();
  }
  if (isLoading) {
    return /*#__PURE__*/_react.default.createElement(_elementorLoading.default, null);
  }
  return /*#__PURE__*/_react.default.createElement(_layout.default, {
    header: /*#__PURE__*/_react.default.createElement(_itemHeader.default, {
      model: data,
      buttons: headersButtons,
      centerColumn: /*#__PURE__*/_react.default.createElement(_previewResponsiveControls.default, {
        active: activeDevice,
        onChange: function onChange(device) {
          return _onChange(device);
        },
        kitName: data.title
      }),
      pageId: "demo"
    })
  }, isIframeLoading && /*#__PURE__*/_react.default.createElement(_pageLoader.default, {
    className: "e-kit-library__preview-loader"
  }), previewUrl && /*#__PURE__*/_react.default.createElement(_previewIframe.PreviewIframe, {
    previewUrl: previewUrl,
    style: iframeStyle,
    onLoaded: function onLoaded() {
      return setIsIframeLoading(false);
    }
  }));
}
Preview.propTypes = {
  id: PropTypes.string
};

/***/ }),

/***/ "../app/modules/kit-library/assets/js/pages/preview/preview.scss":
/*!***********************************************************************!*\
  !*** ../app/modules/kit-library/assets/js/pages/preview/preview.scss ***!
  \***********************************************************************/
/***/ (() => {



/***/ }),

/***/ "../app/modules/kit-library/assets/js/utils.js":
/*!*****************************************************!*\
  !*** ../app/modules/kit-library/assets/js/utils.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.isCloudKitsDeactivated = isCloudKitsDeactivated;
exports.pipe = pipe;
/**
 * A util function to transform data throw transform functions
 *
 * @param {Array<Function>} functions
 * @return {function(*=, ...[*]): *} function
 */
function pipe() {
  for (var _len = arguments.length, functions = new Array(_len), _key = 0; _key < _len; _key++) {
    functions[_key] = arguments[_key];
  }
  return function (value) {
    for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
      args[_key2 - 1] = arguments[_key2];
    }
    return functions.reduce(function (currentValue, currentFunction) {
      return currentFunction.apply(void 0, [currentValue].concat(args));
    }, value);
  };
}

/**
 * Check if cloud kits are deactivated based on storage quota
 *
 * @param {Object} quotaData - The quota data from the API
 * @return {boolean} True if deactivated, false otherwise
 */
function isCloudKitsDeactivated(quotaData) {
  if (!(quotaData !== null && quotaData !== void 0 && quotaData.storage)) {
    return false;
  }
  var _quotaData$storage = quotaData.storage,
    _quotaData$storage$cu = _quotaData$storage.currentUsage,
    currentUsage = _quotaData$storage$cu === void 0 ? 0 : _quotaData$storage$cu,
    _quotaData$storage$su = _quotaData$storage.subscriptionId,
    subscriptionId = _quotaData$storage$su === void 0 ? '' : _quotaData$storage$su;
  var hasStorageUsage = currentUsage > 0;
  var hasNoSubscription = '' === subscriptionId;
  return hasStorageUsage && hasNoSubscription;
}

/***/ }),

/***/ "../app/modules/onboarding/assets/js/components/new-page-kit-list-item.js":
/*!********************************************************************************!*\
  !*** ../app/modules/onboarding/assets/js/components/new-page-kit-list-item.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _react = _interopRequireDefault(__webpack_require__(/*! react */ "react"));
var _appUi = __webpack_require__(/*! @elementor/app-ui */ "@elementor/app-ui");
__webpack_require__(/*! ../../../../kit-library/assets/js/components/kit-list-item.scss */ "../app/modules/kit-library/assets/js/components/kit-list-item.scss");
var NewPageKitListItem = function NewPageKitListItem() {
  return /*#__PURE__*/_react.default.createElement(_appUi.Card, {
    className: "e-onboarding__kit-library-card e-kit-library__kit-item"
  }, /*#__PURE__*/_react.default.createElement(_appUi.CardHeader, null, /*#__PURE__*/_react.default.createElement(_appUi.Heading, {
    tag: "h3",
    title: __('Blank Canvas', 'elementor'),
    variant: "h5",
    className: "eps-card__headline"
  }, __('Blank Canvas', 'elementor'))), /*#__PURE__*/_react.default.createElement(_appUi.CardBody, null, /*#__PURE__*/_react.default.createElement(_appUi.CardImage, {
    alt: __('Blank Canvas', 'elementor'),
    src: elementorCommon.config.urls.assets + 'images/app/onboarding/Blank_Preview.jpg' || 0
  }, /*#__PURE__*/_react.default.createElement(_appUi.CardOverlay, null, /*#__PURE__*/_react.default.createElement(_appUi.Grid, {
    container: true,
    direction: "column",
    className: "e-kit-library__kit-item-overlay"
  }, /*#__PURE__*/_react.default.createElement(_appUi.Button, {
    className: "e-kit-library__kit-item-overlay-overview-button",
    text: __('Create New Elementor Page', 'elementor'),
    icon: "eicon-single-page",
    url: elementorAppConfig.onboarding.urls.createNewPage
  }))))));
};
var _default = exports["default"] = _react.default.memo(NewPageKitListItem);

/***/ }),

/***/ "../assets/dev/js/utils/tiers.js":
/*!***************************************!*\
  !*** ../assets/dev/js/utils/tiers.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.isTierAtLeast = exports.TIERS_PRIORITY = exports.TIERS = void 0;
var TIERS_PRIORITY = exports.TIERS_PRIORITY = Object.freeze(['free', 'essential', 'essential-oct2023', 'advanced', 'expert', 'agency']);

/**
 * @type {Readonly<{
 *     free: string;
 *     essential: string;
 *     'essential-oct2023': string;
 *     advanced: string;
 *     expert: string;
 *     agency: string;
 * }>}
 */
var TIERS = exports.TIERS = Object.freeze(TIERS_PRIORITY.reduce(function (acc, tier) {
  acc[tier] = tier;
  return acc;
}, {}));
var isTierAtLeast = exports.isTierAtLeast = function isTierAtLeast(currentTier, expectedTier) {
  var currentTierIndex = TIERS_PRIORITY.indexOf(currentTier);
  var expectedTierIndex = TIERS_PRIORITY.indexOf(expectedTier);
  if (-1 === currentTierIndex || -1 === expectedTierIndex) {
    return false;
  }
  return currentTierIndex >= expectedTierIndex;
};

/***/ })

}]);
//# sourceMappingURL=kit-library.2e38a4b9ca626956b67f.bundle.js.map