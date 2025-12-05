/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../app/assets/js/event-track/apps-event-tracking.js":
/*!***********************************************************!*\
  !*** ../app/assets/js/event-track/apps-event-tracking.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.appsEventTrackingDispatch = exports.AppsEventTracking = void 0;
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _eventsConfig = _interopRequireDefault(__webpack_require__(/*! ../../../../core/common/modules/events-manager/assets/js/events-config */ "../core/common/modules/events-manager/assets/js/events-config.js"));
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0, _defineProperty2.default)(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _createForOfIteratorHelper(r, e) { var t = "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (!t) { if (Array.isArray(r) || (t = _unsupportedIterableToArray(r)) || e && r && "number" == typeof r.length) { t && (r = t); var _n = 0, F = function F() {}; return { s: F, n: function n() { return _n >= r.length ? { done: !0 } : { done: !1, value: r[_n++] }; }, e: function e(r) { throw r; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var o, a = !0, u = !1; return { s: function s() { t = t.call(r); }, n: function n() { var r = t.next(); return a = r.done, r; }, e: function e(r) { u = !0, o = r; }, f: function f() { try { a || null == t.return || t.return(); } finally { if (u) throw o; } } }; }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
var EVENTS_MAP = {
  PAGE_VIEWS_WEBSITE_TEMPLATES: 'page_views_website_templates',
  KITS_CLOUD_UPGRADE_CLICKED: 'kits_cloud_upgrade_clicked',
  EXPORT_KIT_CUSTOMIZATION: 'export_kit_customization',
  IMPORT_KIT_CUSTOMIZATION: 'import_kit_customization',
  KIT_IMPORT_STATUS: 'kit_import_status',
  KIT_CLOUD_LIBRARY_APPLY: 'kit_cloud_library_apply',
  KIT_CLOUD_LIBRARY_DELETE: 'kit_cloud_library_delete',
  IMPORT_EXPORT_ADMIN_ACTION: 'ie_admin_action',
  KIT_IMPORT_UPLOAD_FILE: 'kit_import_upload_file'
};
var appsEventTrackingDispatch = exports.appsEventTrackingDispatch = function appsEventTrackingDispatch(command, eventParams) {
  // Add existing eventParams key value pair to the data/details object.
  var objectCreator = function objectCreator(array, obj) {
    var _iterator = _createForOfIteratorHelper(array),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var key = _step.value;
        if (eventParams.hasOwnProperty(key) && eventParams[key] !== null) {
          obj[key] = eventParams[key];
        }
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
    return obj;
  };
  var dataKeys = [];
  var detailsKeys = ['layout', 'site_part', 'error', 'document_name', 'document_type', 'view_type_clicked', 'tag', 'sort_direction', 'sort_type', 'action', 'grid_location', 'kit_name', 'page_source', 'element_position', 'element', 'event_type', 'modal_type', 'method', 'status', 'step', 'item', 'category', 'element_location', 'search_term', 'section', 'site_area'];
  var data = {};
  var details = {};
  var init = function init() {
    objectCreator(detailsKeys, details);
    objectCreator(dataKeys, data);
    var commandSplit = command.split('/');
    data.placement = commandSplit[0];
    data.event = commandSplit[1];

    // If 'details' is not empty, add the details object to the data object.
    if (Object.keys(details).length) {
      data.details = details;
    }
  };
  init();
  $e.run(command, data);
};
var AppsEventTracking = exports.AppsEventTracking = /*#__PURE__*/function () {
  function AppsEventTracking() {
    (0, _classCallCheck2.default)(this, AppsEventTracking);
  }
  return (0, _createClass2.default)(AppsEventTracking, null, [{
    key: "dispatchEvent",
    value: function dispatchEvent(eventName, payload) {
      return elementorCommon.eventsManager.dispatchEvent(eventName, payload);
    }
  }, {
    key: "sendPageViewsWebsiteTemplates",
    value: function sendPageViewsWebsiteTemplates(page) {
      return this.dispatchEvent(EVENTS_MAP.PAGE_VIEWS_WEBSITE_TEMPLATES, {
        trigger: _eventsConfig.default.triggers.pageLoaded,
        page_loaded: page,
        secondary_location: page
      });
    }
  }, {
    key: "sendKitsCloudUpgradeClicked",
    value: function sendKitsCloudUpgradeClicked(upgradeLocation) {
      return this.dispatchEvent(EVENTS_MAP.KITS_CLOUD_UPGRADE_CLICKED, {
        trigger: _eventsConfig.default.triggers.click,
        secondary_location: upgradeLocation,
        upgrade_location: upgradeLocation
      });
    }
  }, {
    key: "sendExportKitCustomization",
    value: function sendExportKitCustomization(payload) {
      return this.dispatchEvent(EVENTS_MAP.EXPORT_KIT_CUSTOMIZATION, _objectSpread({
        trigger: _eventsConfig.default.triggers.click
      }, payload));
    }
  }, {
    key: "sendImportKitCustomization",
    value: function sendImportKitCustomization(payload) {
      return this.dispatchEvent(EVENTS_MAP.IMPORT_KIT_CUSTOMIZATION, _objectSpread({
        trigger: _eventsConfig.default.triggers.click
      }, payload));
    }
  }, {
    key: "sendKitImportStatus",
    value: function sendKitImportStatus() {
      var error = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var isError = !!error;
      return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_STATUS, _objectSpread({
        kit_import_status: !isError
      }, isError && {
        kit_import_error: error.message
      }));
    }
  }, {
    key: "sendKitCloudLibraryApply",
    value: function sendKitCloudLibraryApply(kitId, kitApplyUrl) {
      return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_APPLY, _objectSpread({
        trigger: _eventsConfig.default.triggers.click,
        kit_cloud_id: kitId
      }, kitApplyUrl && {
        kit_apply_url: kitApplyUrl
      }));
    }
  }, {
    key: "sendKitCloudLibraryDelete",
    value: function sendKitCloudLibraryDelete() {
      return this.dispatchEvent(EVENTS_MAP.KIT_CLOUD_LIBRARY_DELETE, {
        trigger: _eventsConfig.default.triggers.click
      });
    }
  }, {
    key: "sendImportExportAdminAction",
    value: function sendImportExportAdminAction(actionType) {
      return this.dispatchEvent(EVENTS_MAP.IMPORT_EXPORT_ADMIN_ACTION, {
        trigger: _eventsConfig.default.triggers.click,
        action_type: actionType
      });
    }
  }, {
    key: "sendKitImportUploadFile",
    value: function sendKitImportUploadFile(status) {
      return this.dispatchEvent(EVENTS_MAP.KIT_IMPORT_UPLOAD_FILE, {
        kit_import_upload_file_status: status
      });
    }
  }]);
}();

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/error/import-export-error.js":
/*!************************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/error/import-export-error.js ***!
  \************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.ImportExportError = void 0;
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _possibleConstructorReturn2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "../node_modules/@babel/runtime/helpers/possibleConstructorReturn.js"));
var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js"));
var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/inherits */ "../node_modules/@babel/runtime/helpers/inherits.js"));
var _wrapNativeSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/wrapNativeSuper */ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var ImportExportError = exports.ImportExportError = /*#__PURE__*/function (_Error) {
  function ImportExportError(errorMessage, errorCode) {
    var _this;
    (0, _classCallCheck2.default)(this, ImportExportError);
    _this = _callSuper(this, ImportExportError, ['string' === typeof errorMessage ? errorMessage : '']);
    _this.code = errorCode || 'general';
    _this.details = errorMessage;
    return _this;
  }
  (0, _inherits2.default)(ImportExportError, _Error);
  return (0, _createClass2.default)(ImportExportError);
}(/*#__PURE__*/(0, _wrapNativeSuper2.default)(Error));

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/revert-kit-handler.js":
/*!*****************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/revert-kit-handler.js ***!
  \*****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.RevertKitHandler = void 0;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var _apiRequest = __webpack_require__(/*! ./utils/api-request */ "../app/modules/import-export-customization/assets/js/shared/utils/api-request.js");
var RevertKitHandler = exports.RevertKitHandler = /*#__PURE__*/function () {
  function RevertKitHandler() {
    var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
      revertButton = _ref.revertButton,
      onError = _ref.onError;
    (0, _classCallCheck2.default)(this, RevertKitHandler);
    this.revertButton = revertButton;
    this.onError = onError || this.defaultErrorHandler.bind(this);
  }
  return (0, _createClass2.default)(RevertKitHandler, [{
    key: "revertKit",
    value: function () {
      var _revertKit = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
        var activeKitName, confirmed, referrerKitId, _yield$this$callRever, data, _t;
        return _regenerator.default.wrap(function (_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              activeKitName = this.getActiveKitName();
              _context.next = 1;
              return this.showConfirmationDialog(activeKitName);
            case 1:
              confirmed = _context.sent;
              if (confirmed) {
                _context.next = 2;
                break;
              }
              return _context.abrupt("return");
            case 2:
              _context.prev = 2;
              referrerKitId = this.getReferrerKitId();
              this.saveToCache(referrerKitId, activeKitName);
              _context.next = 3;
              return this.callRevertAPI();
            case 3:
              _yield$this$callRever = _context.sent;
              data = _yield$this$callRever.data;
              this.handleRevertResponse(data);
              _context.next = 5;
              break;
            case 4:
              _context.prev = 4;
              _t = _context["catch"](2);
              this.onError(_t);
            case 5:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[2, 4]]);
      }));
      function revertKit() {
        return _revertKit.apply(this, arguments);
      }
      return revertKit;
    }()
  }, {
    key: "callRevertAPI",
    value: function () {
      var _callRevertAPI = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee2() {
        var result;
        return _regenerator.default.wrap(function (_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              _context2.next = 1;
              return (0, _apiRequest.apiRequest)({
                path: RevertKitHandler.API_PATH
              });
            case 1:
              result = _context2.sent;
              return _context2.abrupt("return", result);
            case 2:
            case "end":
              return _context2.stop();
          }
        }, _callee2);
      }));
      function callRevertAPI() {
        return _callRevertAPI.apply(this, arguments);
      }
      return callRevertAPI;
    }()
  }, {
    key: "handleRevertResponse",
    value: function handleRevertResponse(data) {
      if (!data.revert_completed) {
        this.handleRevertNoSessions(data);
        return;
      }
      this.showRevertCompletedDialog(data);
    }
  }, {
    key: "showRevertCompletedDialog",
    value: function showRevertCompletedDialog() {
      var referrerKitId = this.getReferrerKitId();
      if (referrerKitId) {
        this.showReferrerKitDialog(referrerKitId);
        return;
      }
      this.showRevertSuccessDialog();
    }
  }, {
    key: "handleRevertNoSessions",
    value: function handleRevertNoSessions(responseData) {
      elementorCommon.dialogsManager.createWidget('alert', {
        message: responseData.message || __('No import sessions available to revert.', 'elementor')
      }).show();
    }
  }, {
    key: "showConfirmationDialog",
    value: function () {
      var _showConfirmationDialog = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee3(activeKitName) {
        return _regenerator.default.wrap(function (_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              return _context3.abrupt("return", new Promise(function (resolve) {
                elementorCommon.dialogsManager.createWidget('confirm', {
                  headerMessage: __('Are you sure?', 'elementor'),
                  /* Translators: %s: Kit name */
                  message: __("Removing %s will permanently delete changes made to the Website Template's content and site settings", 'elementor').replace('%s', activeKitName),
                  strings: {
                    confirm: __('Delete', 'elementor'),
                    cancel: __('Cancel', 'elementor')
                  },
                  onConfirm: function onConfirm() {
                    return resolve(true);
                  },
                  onCancel: function onCancel() {
                    return resolve(false);
                  }
                }).show();
              }));
            case 1:
            case "end":
              return _context3.stop();
          }
        }, _callee3);
      }));
      function showConfirmationDialog(_x) {
        return _showConfirmationDialog.apply(this, arguments);
      }
      return showConfirmationDialog;
    }()
  }, {
    key: "createSuccessHeaderMessage",
    value: function createSuccessHeaderMessage() {
      var _this$getDataFromCach = this.getDataFromCache(),
        activeKitName = _this$getDataFromCach.activeKitName;

      /* Translators: %s: Kit name */
      return __('%s was successfully deleted', 'elementor').replace('%s', activeKitName);
    }
  }, {
    key: "showRevertSuccessDialog",
    value: function showRevertSuccessDialog() {
      elementorCommon.dialogsManager.createWidget('confirm', {
        id: RevertKitHandler.DIALOG_ID,
        headerMessage: this.createSuccessHeaderMessage(),
        message: __('Try a different Website Template or build your site from scratch.', 'elementor'),
        strings: {
          confirm: __('OK', 'elementor'),
          cancel: __('Library', 'elementor')
        },
        onConfirm: function onConfirm() {
          location.reload();
        },
        onCancel: function onCancel() {
          location.href = elementorImportExport.appUrl;
        }
      }).show();
      this.clearCache();
    }
  }, {
    key: "showReferrerKitDialog",
    value: function showReferrerKitDialog(referrerKitId) {
      elementorCommon.dialogsManager.createWidget('confirm', {
        id: RevertKitHandler.DIALOG_ID,
        headerMessage: this.createSuccessHeaderMessage(),
        message: __("You're ready to apply a new Kit!", 'elementor'),
        strings: {
          confirm: __('Continue to new Kit', 'elementor'),
          cancel: __('Close', 'elementor')
        },
        onConfirm: function onConfirm() {
          location.href = elementorImportExport.appUrl + '/preview/' + referrerKitId;
        },
        onCancel: function onCancel() {
          location.reload();
        }
      }).show();
      this.clearCache();
    }
  }, {
    key: "maybeShowReferrerKitDialog",
    value: function maybeShowReferrerKitDialog() {
      var _this$getDataFromCach2 = this.getDataFromCache(),
        referrerKitId = _this$getDataFromCach2.referrerKitId;
      if (undefined === referrerKitId) {
        return;
      }
      if (0 === referrerKitId.length) {
        this.showRevertSuccessDialog();
        return;
      }
      this.showReferrerKitDialog(referrerKitId);
    }
  }, {
    key: "getActiveKitName",
    value: function getActiveKitName() {
      var lastKit = elementorImportExport.lastImportedSession;
      if (lastKit.kit_title) {
        return lastKit.kit_title;
      }
      if (lastKit.kit_name) {
        return this.convertNameToTitle(lastKit.kit_name);
      }
      return __('Your Kit', 'elementor');
    }
  }, {
    key: "convertNameToTitle",
    value: function convertNameToTitle(name) {
      var words = name.split(RevertKitHandler.NAME_SEPARATOR_PATTERN).filter(function (word) {
        return word.length > 0;
      });
      if (0 === words.length) {
        return __('Your Kit', 'elementor');
      }
      return words.map(function (word) {
        return word[0].toUpperCase() + word.substring(1);
      }).join(' ');
    }
  }, {
    key: "getReferrerKitId",
    value: function getReferrerKitId() {
      var urlParams = new URLSearchParams(window.location.search);
      var pageReferrerKit = urlParams.get(RevertKitHandler.URL_PARAM_REFERRER_KIT);
      if (pageReferrerKit) {
        return pageReferrerKit;
      }
      if (!this.revertButton) {
        return '';
      }
      return new URL(this.revertButton.href).searchParams.get(RevertKitHandler.URL_PARAM_REFERRER_KIT) || '';
    }
  }, {
    key: "saveToCache",
    value: function saveToCache(referrerKitId, activeKitName) {
      sessionStorage.setItem(RevertKitHandler.KIT_DATA_KEY, JSON.stringify({
        referrerKitId: referrerKitId || '',
        activeKitName: activeKitName || ''
      }));
    }
  }, {
    key: "getDataFromCache",
    value: function getDataFromCache() {
      try {
        return JSON.parse(sessionStorage.getItem(RevertKitHandler.KIT_DATA_KEY)) || {};
      } catch (e) {
        return {};
      }
    }
  }, {
    key: "clearCache",
    value: function clearCache() {
      sessionStorage.removeItem(RevertKitHandler.KIT_DATA_KEY);
    }
  }, {
    key: "defaultErrorHandler",
    value: function defaultErrorHandler() {
      elementorCommon.dialogsManager.createWidget('alert', {
        message: __('An error occurred while reverting the kit. Please try again.', 'elementor')
      }).show();
    }
  }]);
}();
(0, _defineProperty2.default)(RevertKitHandler, "API_PATH", 'revert');
(0, _defineProperty2.default)(RevertKitHandler, "DIALOG_ID", 'e-revert-kit-deleted-dialog');
(0, _defineProperty2.default)(RevertKitHandler, "URL_PARAM_REFERRER_KIT", 'referrer_kit');
(0, _defineProperty2.default)(RevertKitHandler, "KIT_DATA_KEY", 'elementor-kit-data');
(0, _defineProperty2.default)(RevertKitHandler, "NAME_SEPARATOR_PATTERN", /[-_]+/);

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/utils/api-fetch-config.js":
/*!*********************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/utils/api-fetch-config.js ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.configureApiFetch = configureApiFetch;
exports.resetApiFetchConfig = resetApiFetchConfig;
var _apiFetch = _interopRequireDefault(__webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch"));
var _importExportError = __webpack_require__(/*! ../error/import-export-error */ "../app/modules/import-export-customization/assets/js/shared/error/import-export-error.js");
var isApiConfigured = false;
function configureApiFetch() {
  var _window$elementorAppC;
  if (isApiConfigured) {
    return;
  }
  var config = (_window$elementorAppC = window.elementorAppConfig) === null || _window$elementorAppC === void 0 ? void 0 : _window$elementorAppC['import-export-customization'];
  if (!config) {
    throw new _importExportError.ImportExportError('Configuration not found. Please refresh the page.', 'config_missing');
  }
  if (config.restNonce) {
    _apiFetch.default.use(_apiFetch.default.createNonceMiddleware(config.restNonce));
  }
  if (config.restUrl) {
    _apiFetch.default.use(_apiFetch.default.createRootURLMiddleware(config.restUrl));
  }
  isApiConfigured = true;
}
function resetApiFetchConfig() {
  isApiConfigured = false;
}

/***/ }),

/***/ "../app/modules/import-export-customization/assets/js/shared/utils/api-request.js":
/*!****************************************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/shared/utils/api-request.js ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.apiRequest = apiRequest;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _apiFetch = _interopRequireDefault(__webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch"));
var _importExportError = __webpack_require__(/*! ../error/import-export-error */ "../app/modules/import-export-customization/assets/js/shared/error/import-export-error.js");
var _apiFetchConfig = __webpack_require__(/*! ./api-fetch-config */ "../app/modules/import-export-customization/assets/js/shared/utils/api-fetch-config.js");
var HTTP_STATUS = {
  REQUEST_TIMEOUT: 408
};
var HTTP_METHODS = {
  CREATABLE: 'POST'
};
function apiRequest(_x) {
  return _apiRequest.apply(this, arguments);
}
function _apiRequest() {
  _apiRequest = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(_ref) {
    var _ref$data, data, path, _ref$method, method, requestOptions, _t;
    return _regenerator.default.wrap(function (_context) {
      while (1) switch (_context.prev = _context.next) {
        case 0:
          _ref$data = _ref.data, data = _ref$data === void 0 ? null : _ref$data, path = _ref.path, _ref$method = _ref.method, method = _ref$method === void 0 ? HTTP_METHODS.CREATABLE : _ref$method;
          (0, _apiFetchConfig.configureApiFetch)();
          _context.prev = 1;
          requestOptions = {
            path: "/elementor/v1/import-export-customization/".concat(path),
            method: method
          };
          if (data && HTTP_METHODS.CREATABLE === method) {
            requestOptions.data = data;
          }
          _context.next = 2;
          return (0, _apiFetch.default)(requestOptions);
        case 2:
          return _context.abrupt("return", _context.sent);
        case 3:
          _context.prev = 3;
          _t = _context["catch"](1);
          handleApiFetchError(_t);
        case 4:
        case "end":
          return _context.stop();
      }
    }, _callee, null, [[1, 3]]);
  }));
  return _apiRequest.apply(this, arguments);
}
function handleApiFetchError(error) {
  var errorMessage = (error === null || error === void 0 ? void 0 : error.message) || 'An unknown error occurred';
  var errorCode = 'general';
  if (error !== null && error !== void 0 && error.code) {
    errorCode = error.code;
  } else if (error !== null && error !== void 0 && error.status) {
    errorCode = HTTP_STATUS.REQUEST_TIMEOUT === error.status ? 'timeout' : error.status;
  }
  throw new _importExportError.ImportExportError(errorMessage, errorCode);
}

/***/ }),

/***/ "../core/common/modules/events-manager/assets/js/events-config.js":
/*!************************************************************************!*\
  !*** ../core/common/modules/events-manager/assets/js/events-config.js ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var eventsConfig = {
  triggers: {
    click: 'Click',
    accordionClick: 'Accordion Click',
    toggleClick: 'Toggle Click',
    dropdownClick: 'Click Dropdown',
    editorLoaded: 'Editor Loaded',
    visible: 'Visible',
    pageLoaded: 'Page Loaded'
  },
  locations: {
    widgetPanel: 'Widget Panel',
    topBar: 'Top Bar',
    elementorEditor: 'Elementor Editor',
    templatesLibrary: {
      library: 'Templates Library'
    },
    app: {
      import: 'Import Kit',
      export: 'Export Kit',
      kitLibrary: 'Kit Library',
      cloudKitLibrary: 'Cloud Kit Library'
    },
    variables: 'Variables Panel',
    admin: 'WP admin'
  },
  secondaryLocations: {
    layout: 'Layout Section',
    basic: 'Basic Section',
    'pro-elements': 'Pro Section',
    general: 'General Section',
    'theme-elements': 'Site Section',
    'theme-elements-single': 'Single Section',
    'woocommerce-elements': 'WooCommerce Section',
    wordpress: 'WordPress Section',
    categories: 'Widgets Tab',
    global: 'Globals Tab',
    'whats-new': 'What\'s New',
    'document-settings': 'Document Settings icon',
    'preview-page': 'Preview Page',
    'publish-button': 'Publish Button',
    'widget-panel': 'Widget Panel Icon',
    finder: 'Finder',
    help: 'Help',
    elementorLogoDropdown: 'top_bar_elementor_logo_dropdown',
    elementorLogo: 'Elementor Logo',
    eLogoMenu: 'E-logo Menu',
    notes: 'Notes',
    siteSettings: 'Site Settings',
    structure: 'Structure',
    documentNameDropdown: 'Document Name dropdown',
    responsiveControls: 'Responsive controls',
    launchpad: 'launchpad',
    checklistHeader: 'Checklist Header',
    checklistSteps: 'Checklist Steps',
    userPreferences: 'User Preferences',
    contextMenu: 'Context Menu',
    templateLibrary: {
      saveModal: 'Save to Modal',
      moveModal: 'Move to Modal',
      bulkMoveModal: 'Bulk Move to Modal',
      copyModal: 'Copy to Modal',
      bulkCopyModal: 'Bulk Copy to Modal',
      saveModalSelectFolder: 'Save to Modal - select folder',
      saveModalSelectConnect: 'Save to Modal - connect',
      saveModalSelectUpgrade: 'Save to Modal - upgrade',
      importModal: 'Import Modal',
      newFolderModal: 'New Folder Modal',
      deleteDialog: 'Delete Dialog',
      deleteFolderDialog: 'Delete Folder Dialog',
      renameDialog: 'Rename Dialog',
      createFolderDialog: 'Create Folder Dialog',
      applySettingsDialog: 'Apply Settings Dialog',
      cloudTab: 'Cloud Tab',
      siteTab: 'Site Tab',
      cloudTabFolder: 'Cloud Tab - Folder',
      cloudTabConnect: 'Cloud Tab - Connect',
      cloudTabUpgrade: 'Cloud Tab - Upgrade',
      morePopup: 'Context Menu',
      quotaBar: 'Quota Bar'
    },
    kitLibrary: {
      cloudKitLibrary: 'kits_cloud_library',
      cloudKitLibraryConnect: 'kits_cloud_library_connect',
      cloudKitLibraryUpgrade: 'kits_cloud_library_upgrade',
      kitExportCustomization: 'kit_export_customization',
      kitExport: 'kit_export',
      kitExportCustomizationEdit: 'kit_export_customization_edit',
      kitExportSummary: 'kit_export_summary',
      kitImportUploadBox: 'kit_import_upload_box',
      kitImportCustomization: 'kit_import_customization',
      kitImportSummary: 'kit_import_summary'
    },
    variablesPopover: 'Variables Popover',
    admin: {
      pluginToolsTab: 'plugin_tools_tab',
      pluginWebsiteTemplatesTab: 'plugin_website_templates_tab'
    }
  },
  elements: {
    accordionSection: 'Accordion section',
    buttonIcon: 'Button Icon',
    mainCta: 'Main CTA',
    button: 'Button',
    link: 'Link',
    dropdown: 'Dropdown',
    toggle: 'Toggle',
    launchpadChecklist: 'Checklist popup'
  },
  names: {
    v1: {
      layout: 'v1_widgets_tab_layout_section',
      basic: 'v1_widgets_tab_basic_section',
      'pro-elements': 'v1_widgets_tab_pro_section',
      general: 'v1_widgets_tab_general_section',
      'theme-elements': 'v1_widgets_tab_site_section',
      'theme-elements-single': 'v1_widgets_tab_single_section',
      'woocommerce-elements': 'v1_widgets_tab_woocommerce_section',
      wordpress: 'v1_widgets_tab_wordpress_section',
      categories: 'v1_widgets_tab',
      global: 'v1_globals_tab'
    },
    topBar: {
      whatsNew: 'top_bar_whats_new',
      documentSettings: 'top_bar_document_settings_icon',
      previewPage: 'top_bar_preview_page',
      publishButton: 'top_bar_publish_button',
      widgetPanel: 'top_bar_widget_panel_icon',
      finder: 'top_bar_finder',
      help: 'top_bar_help',
      history: 'top_bar_elementor_logo_dropdown_history',
      userPreferences: 'top_bar_elementor_logo_dropdown_user_preferences',
      keyboardShortcuts: 'top_bar_elementor_logo_dropdown_keyboard_shortcuts',
      exitToWordpress: 'top_bar_elementor_logo_dropdown_exit_to_wordpress',
      themeBuilder: 'top_bar_elementor_logo_dropdown_theme_builder',
      notes: 'top_bar_notes',
      siteSettings: 'top_bar_site_setting',
      structure: 'top_bar_structure',
      documentNameDropdown: 'top_bar_document_name_dropdown',
      responsiveControls: 'top_bar_responsive_controls',
      launchpadOn: 'top_bar_checklist_icon_show',
      launchpadOff: 'top_bar_checklist_icon_hide',
      elementorLogoDropdown: 'open_e_menu',
      connectAccount: 'connect_account',
      accountConnected: 'account_connected'
    },
    // ChecklistSteps event names are generated dynamically, based on stepId and action type taken: title, action, done, undone, upgrade
    elementorEditor: {
      checklist: {
        checklistHeaderClose: 'checklist_header_close_icon',
        checklistFirstPopup: 'checklist popup triggered'
      },
      userPreferences: {
        checklistShow: 'checklist_userpreferences_toggle_show',
        checklistHide: 'checklist_userpreferences_toggle_hide'
      }
    },
    variables: {
      open: 'open_variables_popover',
      add: 'add_new_variable',
      connect: 'connect_variable',
      save: 'save_new_variable'
    }
  }
};
var _default = exports["default"] = eventsConfig;

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

/***/ "../node_modules/@babel/runtime/helpers/construct.js":
/*!***********************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/construct.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var isNativeReflectConstruct = __webpack_require__(/*! ./isNativeReflectConstruct.js */ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
function _construct(t, e, r) {
  if (isNativeReflectConstruct()) return Reflect.construct.apply(null, arguments);
  var o = [null];
  o.push.apply(o, e);
  var p = new (t.bind.apply(t, o))();
  return r && setPrototypeOf(p, r.prototype), p;
}
module.exports = _construct, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/isNativeFunction.js":
/*!******************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeFunction.js ***!
  \******************************************************************/
/***/ ((module) => {

function _isNativeFunction(t) {
  try {
    return -1 !== Function.toString.call(t).indexOf("[native code]");
  } catch (n) {
    return "function" == typeof t;
  }
}
module.exports = _isNativeFunction, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ "../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js":
/*!**************************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/isNativeReflectConstruct.js ***!
  \**************************************************************************/
/***/ ((module) => {

function _isNativeReflectConstruct() {
  try {
    var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {}));
  } catch (t) {}
  return (module.exports = _isNativeReflectConstruct = function _isNativeReflectConstruct() {
    return !!t;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports)();
}
module.exports = _isNativeReflectConstruct, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "../node_modules/@babel/runtime/helpers/wrapNativeSuper.js":
/*!*****************************************************************!*\
  !*** ../node_modules/@babel/runtime/helpers/wrapNativeSuper.js ***!
  \*****************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var getPrototypeOf = __webpack_require__(/*! ./getPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/getPrototypeOf.js");
var setPrototypeOf = __webpack_require__(/*! ./setPrototypeOf.js */ "../node_modules/@babel/runtime/helpers/setPrototypeOf.js");
var isNativeFunction = __webpack_require__(/*! ./isNativeFunction.js */ "../node_modules/@babel/runtime/helpers/isNativeFunction.js");
var construct = __webpack_require__(/*! ./construct.js */ "../node_modules/@babel/runtime/helpers/construct.js");
function _wrapNativeSuper(t) {
  var r = "function" == typeof Map ? new Map() : void 0;
  return module.exports = _wrapNativeSuper = function _wrapNativeSuper(t) {
    if (null === t || !isNativeFunction(t)) return t;
    if ("function" != typeof t) throw new TypeError("Super expression must either be null or a function");
    if (void 0 !== r) {
      if (r.has(t)) return r.get(t);
      r.set(t, Wrapper);
    }
    function Wrapper() {
      return construct(t, arguments, getPrototypeOf(this).constructor);
    }
    return Wrapper.prototype = Object.create(t.prototype, {
      constructor: {
        value: Wrapper,
        enumerable: !1,
        writable: !0,
        configurable: !0
      }
    }), setPrototypeOf(Wrapper, t);
  }, module.exports.__esModule = true, module.exports["default"] = module.exports, _wrapNativeSuper(t);
}
module.exports = _wrapNativeSuper, module.exports.__esModule = true, module.exports["default"] = module.exports;

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

/***/ "@wordpress/api-fetch":
/*!******************************!*\
  !*** external "wp.apiFetch" ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = wp.apiFetch;

/***/ }),

/***/ "@wordpress/i18n":
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ ((module) => {

"use strict";
module.exports = wp.i18n;

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
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*********************************************************************!*\
  !*** ../app/modules/import-export-customization/assets/js/admin.js ***!
  \*********************************************************************/


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _appsEventTracking = __webpack_require__(/*! ../../../../assets/js/event-track/apps-event-tracking */ "../app/assets/js/event-track/apps-event-tracking.js");
var _revertKitHandler = __webpack_require__(/*! ./shared/revert-kit-handler */ "../app/modules/import-export-customization/assets/js/shared/revert-kit-handler.js");
var Admin = /*#__PURE__*/function () {
  function Admin() {
    var _this = this;
    (0, _classCallCheck2.default)(this, Admin);
    var urlParams = new URLSearchParams(window.location.search);
    if ('elementor-tools' === urlParams.get('page')) {
      this.sendPageToolsViewedEvent();
      elementorAdmin.elements.$settingsTabs.on('focus', function () {
        var location = window.location.hash.slice(1);
        _this.maybeSendImportExportLocationEvent(location);
      });
      this.maybeSendImportExportLocationEvent(window.location.hash.slice(1));
    }
    this.revertButton = document.getElementById('elementor-import-export__revert_kit');
    this.importFroLibraryButton = document.getElementById('elementor-import-export__import_from_library');
    this.importButton = document.getElementById('elementor-import-export__import');
    this.exportButton = document.getElementById('elementor-import-export__export');
    this.initializeRevertHandler();
    this.bindEventListeners();
  }
  return (0, _createClass2.default)(Admin, [{
    key: "initializeRevertHandler",
    value: function initializeRevertHandler() {
      if (this.revertButton) {
        this.revertHandler = new _revertKitHandler.RevertKitHandler({
          revertButton: this.revertButton
        });
        this.revertHandler.maybeShowReferrerKitDialog();
      }
    }
  }, {
    key: "bindEventListeners",
    value: function bindEventListeners() {
      if (this.revertButton) {
        this.revertButton.addEventListener('click', this.onRevertButtonClick.bind(this));
      }
      if (this.importFroLibraryButton) {
        this.importFroLibraryButton.addEventListener('click', this.onImportFromLibraryButtonClick.bind(this));
      }
      if (this.importButton) {
        this.importButton.addEventListener('click', this.onImportButtonClick.bind(this));
      }
      if (this.exportButton) {
        this.exportButton.addEventListener('click', this.onExportButtonClick.bind(this));
      }
    }
  }, {
    key: "sendPageToolsViewedEvent",
    value: function sendPageToolsViewedEvent() {
      _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.admin.pluginToolsTab);
    }
  }, {
    key: "maybeSendImportExportLocationEvent",
    value: function maybeSendImportExportLocationEvent(location) {
      if ('tab-import-export-kit' === location) {
        _appsEventTracking.AppsEventTracking.sendPageViewsWebsiteTemplates(elementorCommon.eventsManager.config.secondaryLocations.admin.pluginWebsiteTemplatesTab);
      }
    }
  }, {
    key: "onRevertButtonClick",
    value: function onRevertButtonClick(event) {
      var _this$revertHandler;
      event.preventDefault();
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Revert');
      (_this$revertHandler = this.revertHandler) === null || _this$revertHandler === void 0 || _this$revertHandler.revertKit();
    }
  }, {
    key: "onExportButtonClick",
    value: function onExportButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Export');
    }
  }, {
    key: "onImportButtonClick",
    value: function onImportButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Import');
    }
  }, {
    key: "onImportFromLibraryButtonClick",
    value: function onImportFromLibraryButtonClick() {
      _appsEventTracking.AppsEventTracking.sendImportExportAdminAction('Import from Library');
    }
  }]);
}();
window.addEventListener('load', function () {
  new Admin();
});
})();

/******/ })()
;
//# sourceMappingURL=import-export-customization-admin.js.map