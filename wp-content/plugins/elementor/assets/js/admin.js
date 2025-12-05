/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "../assets/dev/js/admin/maintenance-mode.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/admin/maintenance-mode.js ***!
  \**************************************************/
/***/ ((module) => {

"use strict";


module.exports = elementorModules.ViewModule.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        modeSelect: '.elementor_maintenance_mode_mode select',
        maintenanceModeTable: '#tab-maintenance_mode table',
        maintenanceModeDescriptions: '.elementor-maintenance-mode-description',
        excludeModeSelect: '.elementor_maintenance_mode_exclude_mode select',
        excludeRolesArea: '.elementor_maintenance_mode_exclude_roles',
        templateSelect: '.elementor_maintenance_mode_template_id select',
        editTemplateButton: '.elementor-edit-template',
        maintenanceModeError: '.elementor-maintenance-mode-error'
      },
      classes: {
        isEnabled: 'elementor-maintenance-mode-is-enabled'
      }
    };
  },
  getDefaultElements: function getDefaultElements() {
    var elements = {},
      selectors = this.getSettings('selectors');
    elements.$modeSelect = jQuery(selectors.modeSelect);
    elements.$maintenanceModeTable = elements.$modeSelect.parents(selectors.maintenanceModeTable);
    elements.$excludeModeSelect = elements.$maintenanceModeTable.find(selectors.excludeModeSelect);
    elements.$excludeRolesArea = elements.$maintenanceModeTable.find(selectors.excludeRolesArea);
    elements.$templateSelect = elements.$maintenanceModeTable.find(selectors.templateSelect);
    elements.$editTemplateButton = elements.$maintenanceModeTable.find(selectors.editTemplateButton);
    elements.$maintenanceModeDescriptions = elements.$maintenanceModeTable.find(selectors.maintenanceModeDescriptions);
    elements.$maintenanceModeError = elements.$maintenanceModeTable.find(selectors.maintenanceModeError);
    return elements;
  },
  handleModeSelectChange: function handleModeSelectChange() {
    var settings = this.getSettings(),
      elements = this.elements;
    elements.$maintenanceModeTable.toggleClass(settings.classes.isEnabled, !!elements.$modeSelect.val());
    elements.$maintenanceModeDescriptions.hide();
    elements.$maintenanceModeDescriptions.filter('[data-value="' + elements.$modeSelect.val() + '"]').show();
  },
  handleExcludeModeSelectChange: function handleExcludeModeSelectChange() {
    var elements = this.elements;
    elements.$excludeRolesArea.toggle('custom' === elements.$excludeModeSelect.val());
  },
  handleTemplateSelectChange: function handleTemplateSelectChange() {
    var elements = this.elements;
    var templateID = elements.$templateSelect.val();
    if (!templateID) {
      elements.$editTemplateButton.hide();
      elements.$maintenanceModeError.show();
      return;
    }
    var editUrl = elementorAdmin.config.home_url + '?p=' + templateID + '&elementor';
    elements.$editTemplateButton.prop('href', editUrl).show();
    elements.$maintenanceModeError.hide();
  },
  bindEvents: function bindEvents() {
    var elements = this.elements;
    elements.$modeSelect.on('change', this.handleModeSelectChange.bind(this));
    elements.$excludeModeSelect.on('change', this.handleExcludeModeSelectChange.bind(this));
    elements.$templateSelect.on('change', this.handleTemplateSelectChange.bind(this));
  },
  onAdminInit: function onAdminInit() {
    this.handleModeSelectChange();
    this.handleExcludeModeSelectChange();
    this.handleTemplateSelectChange();
  },
  onInit: function onInit() {
    elementorModules.ViewModule.prototype.onInit.apply(this, arguments);
    elementorCommon.elements.$window.on('elementor/admin/init', this.onAdminInit);
  }
});

/***/ }),

/***/ "../assets/dev/js/admin/menu-handler.js":
/*!**********************************************!*\
  !*** ../assets/dev/js/admin/menu-handler.js ***!
  \**********************************************/
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
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var MenuHandler = exports["default"] = /*#__PURE__*/function (_elementorModules$Vie) {
  function MenuHandler() {
    (0, _classCallCheck2.default)(this, MenuHandler);
    return _callSuper(this, MenuHandler, arguments);
  }
  (0, _inherits2.default)(MenuHandler, _elementorModules$Vie);
  return (0, _createClass2.default)(MenuHandler, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          currentSubmenuItems: '#adminmenu .current'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var settings = this.getSettings();
      return {
        $currentSubmenuItems: jQuery(settings.selectors.currentSubmenuItems),
        $adminPageMenuLink: jQuery("a[href=\"".concat(settings.path, "\"]"))
      };
    }

    // This method highlights the currently visited submenu item for the slug provided as an argument to this handler.
    // This method also accepts a jQuery instance of a custom submenu item to highlight. If provided, the provided
    // item will be the one highlighted.
  }, {
    key: "highlightSubMenuItem",
    value: function highlightSubMenuItem() {
      var $element = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var $submenuItem = $element || this.elements.$adminPageMenuLink;
      if (this.elements.$currentSubmenuItems.length) {
        this.elements.$currentSubmenuItems.removeClass('current');
      }
      $submenuItem.addClass('current');

      // Need to add the 'current' class to the link element's parent `<li>` element as well.
      $submenuItem.parent().addClass('current');
    }
  }, {
    key: "highlightTopLevelMenuItem",
    value: function highlightTopLevelMenuItem($elementToHighlight) {
      var $elementToRemove = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var activeClasses = 'wp-has-current-submenu wp-menu-open current';
      $elementToHighlight.parent().addClass(activeClasses).removeClass('wp-not-current-submenu');
      if ($elementToRemove) {
        $elementToRemove.removeClass(activeClasses);
      }
    }
  }, {
    key: "onInit",
    value: function onInit() {
      _superPropGet(MenuHandler, "onInit", this, 3)([]);
      var settings = this.getSettings();
      if (window.location.href.includes(settings.path)) {
        this.highlightSubMenuItem();
      }
    }
  }]);
}(elementorModules.ViewModule);

/***/ }),

/***/ "../assets/dev/js/admin/new-template/template-controls.js":
/*!****************************************************************!*\
  !*** ../assets/dev/js/admin/new-template/template-controls.js ***!
  \****************************************************************/
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
var TemplateControls = exports["default"] = /*#__PURE__*/function () {
  function TemplateControls() {
    (0, _classCallCheck2.default)(this, TemplateControls);
  }
  return (0, _createClass2.default)(TemplateControls, [{
    key: "setDynamicControlsVisibility",
    value: function setDynamicControlsVisibility(lookupControlIdPrefix, controls) {
      if (undefined === controls) {
        return;
      }
      var controlsArray = Object.entries(controls);
      for (var _i = 0, _controlsArray = controlsArray; _i < _controlsArray.length; _i++) {
        var _controlsArray$_i = (0, _slicedToArray2.default)(_controlsArray[_i], 2),
          controlId = _controlsArray$_i[0],
          controlSettings = _controlsArray$_i[1];
        this.setVisibilityForControl(lookupControlIdPrefix, controlSettings, controlId);
      }
    }
  }, {
    key: "setVisibilityForControl",
    value: function setVisibilityForControl(lookupControlIdPrefix, controlSettings, controlId) {
      var _controlSettings$cond,
        _this = this;
      var conditions = Object.entries((_controlSettings$cond = controlSettings.conditions) !== null && _controlSettings$cond !== void 0 ? _controlSettings$cond : {});
      conditions.forEach(function (condition) {
        _this.changeVisibilityBasedOnCondition(lookupControlIdPrefix, condition, controlId);
      });
    }
  }, {
    key: "changeVisibilityBasedOnCondition",
    value: function changeVisibilityBasedOnCondition(lookupControlIdPrefix, condition, controlId) {
      var _condition = (0, _slicedToArray2.default)(condition, 2),
        conditionKey = _condition[0],
        conditionValue = _condition[1];
      var targetControlWrapper = document.getElementById(lookupControlIdPrefix + controlId + '__wrapper');
      var lookupControl = document.getElementById(lookupControlIdPrefix + conditionKey);
      targetControlWrapper.classList.toggle('elementor-hidden', !lookupControl || conditionValue !== lookupControl.value);
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/editor/utils/files-upload-handler.js":
/*!*************************************************************!*\
  !*** ../assets/dev/js/editor/utils/files-upload-handler.js ***!
  \*************************************************************/
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
var FilesUploadHandler = exports["default"] = /*#__PURE__*/function () {
  function FilesUploadHandler() {
    (0, _classCallCheck2.default)(this, FilesUploadHandler);
  }
  return (0, _createClass2.default)(FilesUploadHandler, null, [{
    key: "isUploadEnabled",
    value: function isUploadEnabled(mediaType) {
      var unfilteredFilesTypes = ['svg', 'application/json'];
      if (!unfilteredFilesTypes.includes(mediaType)) {
        return true;
      }
      return elementorCommon.config.filesUpload.unfilteredFiles;
    }
  }, {
    key: "setUploadTypeCaller",
    value: function setUploadTypeCaller(frame) {
      frame.uploader.uploader.param('uploadTypeCaller', 'elementor-wp-media-upload');
    }
  }, {
    key: "getUnfilteredFilesNonAdminDialog",
    value: function getUnfilteredFilesNonAdminDialog() {
      return elementorCommon.dialogsManager.createWidget('alert', {
        id: 'e-unfiltered-files-disabled-dialog',
        headerMessage: __('Sorry, you can\'t upload that file yet', 'elementor'),
        message: __('This is because JSON files may pose a security risk.', 'elementor') + '<br><br>' + __('To upload them anyway, ask the site administrator to enable unfiltered file uploads.', 'elementor'),
        strings: {
          confirm: __('Got it', 'elementor')
        }
      });
    }
  }, {
    key: "getUnfilteredFilesNotEnabledDialog",
    value: function getUnfilteredFilesNotEnabledDialog(callback) {
      var elementorInstance = window.elementorAdmin || window.elementor;
      if (!elementorInstance.config.user.is_administrator) {
        return this.getUnfilteredFilesNonAdminDialog();
      }
      var onConfirm = function onConfirm() {
        elementorCommon.ajax.addRequest('enable_unfiltered_files_upload', {}, true);
        elementorCommon.config.filesUpload.unfilteredFiles = true;
        callback();
      };
      return elementorInstance.helpers.getSimpleDialog('e-enable-unfiltered-files-dialog', __('Enable Unfiltered File Uploads', 'elementor'), __('Before you enable unfiltered files upload, note that such files include a security risk. Elementor does run a process to remove possible malicious code, but there is still risk involved when using such files.', 'elementor'), __('Enable', 'elementor'), onConfirm);
    }
  }, {
    key: "getUnfilteredFilesNotEnabledImportTemplateDialog",
    value: function getUnfilteredFilesNotEnabledImportTemplateDialog(callback) {
      if (!(window.elementorAdmin || window.elementor).config.user.is_administrator) {
        return this.getUnfilteredFilesNonAdminDialog();
      }
      return elementorCommon.dialogsManager.createWidget('confirm', {
        id: 'e-enable-unfiltered-files-dialog-import-template',
        headerMessage: __('Enable Unfiltered File Uploads', 'elementor'),
        message: __('Before you enable unfiltered files upload, note that such files include a security risk. Elementor does run a process to remove possible malicious code, but there is still risk involved when using such files.', 'elementor') + '<br /><br />' + __('If you do not enable uploading unfiltered files, any SVG or JSON (including lottie) files used in the uploaded template will not be imported.', 'elementor'),
        position: {
          my: 'center center',
          at: 'center center'
        },
        strings: {
          confirm: __('Enable and Import', 'elementor'),
          cancel: __('Import Without Enabling', 'elementor')
        },
        onConfirm: function onConfirm() {
          elementorCommon.ajax.addRequest('enable_unfiltered_files_upload', {
            success: function success() {
              // This utility is used in both the admin and the Editor.
              elementorCommon.config.filesUpload.unfilteredFiles = true;
              callback();
            }
          }, true);
        },
        onCancel: function onCancel() {
          return callback();
        }
      });
    }
  }]);
}();

/***/ }),

/***/ "../assets/dev/js/utils/events.js":
/*!****************************************!*\
  !*** ../assets/dev/js/utils/events.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.Events = void 0;
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var Events = exports.Events = /*#__PURE__*/function () {
  function Events() {
    (0, _classCallCheck2.default)(this, Events);
  }
  return (0, _createClass2.default)(Events, null, [{
    key: "dispatch",
    value:
    /**
     * Dispatch an Elementor event.
     *
     * Will dispatch both native event & jQuery event (as BC).
     * By default, `bcEvent` is `null`.
     *
     * @param {Object}      context - The context that will dispatch the event.
     * @param {string}      event   - Event to dispatch.
     * @param {*}           data    - Data to pass to the event, default to `null`.
     * @param {string|null} bcEvent - BC event to dispatch, default to `null`.
     *
     * @return {void}
     */
    function dispatch(context, event) {
      var data = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var bcEvent = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
      // Make sure to use the native context if it's a jQuery instance.
      context = context instanceof jQuery ? context[0] : context;

      // Dispatch the BC event only if exists.
      if (bcEvent) {
        context.dispatchEvent(new CustomEvent(bcEvent, {
          detail: data
        }));
      }

      // jQuery's `.on()` listens also to native custom events, so there is no need
      // to dispatch also a jQuery event.
      context.dispatchEvent(new CustomEvent(event, {
        detail: data
      }));
    }
  }]);
}();
var _default = exports["default"] = Events;

/***/ }),

/***/ "../assets/dev/js/utils/json-upload-warning-message.js":
/*!*************************************************************!*\
  !*** ../assets/dev/js/utils/json-upload-warning-message.js ***!
  \*************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.showJsonUploadWarningMessageIfNeeded = showJsonUploadWarningMessageIfNeeded;
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var genericMessageIntroductionKey = 'upload_json_warning_generic_message';
/**
 * @type {import('../utils/introduction').default | null}
 */
var genericWarningModal = null;
function showJsonUploadWarningMessageIfNeeded(_ref) {
  var introductionMap = _ref.introductionMap,
    IntroductionClass = _ref.IntroductionClass,
    _ref$waitForSetViewed = _ref.waitForSetViewed,
    waitForSetViewed = _ref$waitForSetViewed === void 0 ? false : _ref$waitForSetViewed;
  if (!genericWarningModal) {
    genericWarningModal = createGenericWarningModal(IntroductionClass);
  }
  genericWarningModal.setIntroductionMap(introductionMap);
  if (genericWarningModal.introductionViewed) {
    return Promise.resolve();
  }
  var dialog = genericWarningModal.getDialog();
  return new Promise(function (resolve, reject) {
    dialog.onHide = function () {
      // When closing the dialog (esc, click on background, etc.) we need to reject the promise.
      reject();
    };
    dialog.onConfirm = /*#__PURE__*/(0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee() {
      return _regenerator.default.wrap(function (_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (!dialog.getElements('checkbox-dont-show-again').prop('checked')) {
              _context.next = 3;
              break;
            }
            if (!waitForSetViewed) {
              _context.next = 2;
              break;
            }
            _context.next = 1;
            return genericWarningModal.setViewed();
          case 1:
            _context.next = 3;
            break;
          case 2:
            genericWarningModal.setViewed();
          case 3:
            // Hack: we need to resolve the promise and then hide it manually, to avoid rejecting the promise.
            // this because when closing the dialog (esc, click on background, etc.) it does not trigger `onCancel` method.
            resolve();
            dialog.hide();
          case 4:
          case "end":
            return _context.stop();
        }
      }, _callee);
    }));
    dialog.onCancel = function () {
      dialog.hide();
    };
    genericWarningModal.show();
  });
}

/**
 * @param {import('../utils/introduction').default.prototype.constructor} IntroductionClass
 *
 * @return {import('../utils/introduction').default}
 */
function createGenericWarningModal(IntroductionClass) {
  var _introduction$getDial, _introduction$getDial2;
  var dialogId = 'e-generic-warning-modal-for-json-upload';
  var introduction = new IntroductionClass({
    introductionKey: genericMessageIntroductionKey,
    dialogType: 'confirm',
    dialogOptions: {
      id: dialogId,
      headerMessage: __('Warning: JSON files may be unsafe', 'elementor'),
      message: __('Uploading JSON files from unknown sources can be harmful and put your site at risk. For maximum safety, only install JSON files from trusted sources.', 'elementor'),
      effects: {
        show: 'fadeIn',
        hide: 'fadeOut'
      },
      hide: {
        onBackgroundClick: true,
        onButtonClick: false
      },
      strings: {
        confirm: __('Continue', 'elementor'),
        cancel: __('Cancel', 'elementor')
      }
    }
  });
  var _createCheckboxAndLab = createCheckboxAndLabel(dialogId),
    checkbox = _createCheckboxAndLab.checkbox,
    label = _createCheckboxAndLab.label;
  introduction.getDialog().addElement('checkbox-dont-show-again', checkbox);
  (_introduction$getDial = introduction.getDialog().getElements('message')) === null || _introduction$getDial === void 0 || (_introduction$getDial2 = _introduction$getDial.append) === null || _introduction$getDial2 === void 0 || _introduction$getDial2.call(_introduction$getDial, label); // `getElements` return JQuery object.

  return introduction;
}
function createCheckboxAndLabel(dialogId) {
  var checkboxId = "".concat(dialogId, "-dont-show-again");
  var checkbox = document.createElement('input');
  checkbox.type = 'checkbox';
  checkbox.name = checkboxId;
  checkbox.id = checkboxId;
  var label = document.createElement('label');
  label.htmlFor = checkboxId;
  label.textContent = __('Do not show this message again', 'elementor');
  label.style.display = 'block';
  label.style.marginTop = '20px';
  label.style.marginBottom = '20px';
  label.prepend(checkbox);
  return {
    checkbox: checkbox,
    label: label
  };
}

/***/ }),

/***/ "../core/common/assets/js/utils/environment.js":
/*!*****************************************************!*\
  !*** ../core/common/assets/js/utils/environment.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var matchUserAgent = function matchUserAgent(UserAgentStr) {
    return userAgent.indexOf(UserAgentStr) >= 0;
  },
  userAgent = navigator.userAgent,
  // Solution influenced by https://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser

  // Opera 8.0+
  isOpera = !!window.opr && !!opr.addons || !!window.opera || matchUserAgent(' OPR/'),
  // Firefox 1.0+
  isFirefox = matchUserAgent('Firefox'),
  // Safari 3.0+ "[object HTMLElementConstructor]"
  isSafari = /^((?!chrome|android).)*safari/i.test(userAgent) || /constructor/i.test(window.HTMLElement) || function (p) {
    return '[object SafariRemoteNotification]' === p.toString();
  }(!window.safari || typeof safari !== 'undefined' && safari.pushNotification),
  // Internet Explorer 6-11
  isIE = /Trident|MSIE/.test(userAgent) && (/* @cc_on!@*/ false || !!document.documentMode),
  // Edge 20+
  isEdge = !isIE && !!window.StyleMedia || matchUserAgent('Edg'),
  // Google Chrome (Not accurate)
  isChrome = !!window.chrome && matchUserAgent('Chrome') && !(isEdge || isOpera),
  // Blink engine
  isBlink = matchUserAgent('Chrome') && !!window.CSS,
  // Apple Webkit engine
  isAppleWebkit = matchUserAgent('AppleWebKit') && !isBlink,
  isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0,
  environment = {
    isTouchDevice: isTouchDevice,
    appleWebkit: isAppleWebkit,
    blink: isBlink,
    chrome: isChrome,
    edge: isEdge,
    firefox: isFirefox,
    ie: isIE,
    mac: matchUserAgent('Macintosh'),
    opera: isOpera,
    safari: isSafari,
    webkit: matchUserAgent('AppleWebKit')
  };
var _default = exports["default"] = environment;

/***/ }),

/***/ "../core/experiments/assets/js/admin/behaviors/experiments-messages.js":
/*!*****************************************************************************!*\
  !*** ../core/experiments/assets/js/admin/behaviors/experiments-messages.js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "../node_modules/@babel/runtime/helpers/classCallCheck.js"));
var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/createClass */ "../node_modules/@babel/runtime/helpers/createClass.js"));
var _defineProperty2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "../node_modules/@babel/runtime/helpers/defineProperty.js"));
var STATE_ACTIVE = 'active';
var STATE_INACTIVE = 'inactive';
var STATE_DEFAULT = 'default';
var ExperimentsMessages = exports["default"] = /*#__PURE__*/function () {
  function ExperimentsMessages(_ref) {
    var selects = _ref.selects,
      submit = _ref.submit;
    (0, _classCallCheck2.default)(this, ExperimentsMessages);
    (0, _defineProperty2.default)(this, "elements", {});
    this.elements = {
      /**
       * @type {HTMLSelectElement[]}
       */
      selects: selects,
      /**
       * @type {HTMLInputElement}
       */
      submit: submit
    };
  }
  return (0, _createClass2.default)(ExperimentsMessages, [{
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      this.elements.selects.forEach(function (select) {
        select.addEventListener('change', function (e) {
          return _this.onExperimentStateChange(e);
        });
      });
    }
  }, {
    key: "onExperimentStateChange",
    value: function onExperimentStateChange(e) {
      var experimentId = e.currentTarget.dataset.experimentId,
        experimentNewState = this.getExperimentActualState(experimentId);
      switch (experimentNewState) {
        case STATE_ACTIVE:
          if (this.shouldShowDependenciesDialog(experimentId)) {
            this.showDependenciesDialog(experimentId);
          }
          break;
        case STATE_INACTIVE:
          if (this.shouldShowDeactivationDialog(experimentId)) {
            this.showDeactivationDialog(experimentId);
          } else {
            this.deactivateDependantExperiments(experimentId);
          }
          break;
        default:
          break;
      }
    }
  }, {
    key: "getExperimentData",
    value: function getExperimentData(experimentId) {
      return elementorAdminConfig.experiments[experimentId];
    }
  }, {
    key: "getExperimentDependencies",
    value: function getExperimentDependencies(experimentId) {
      var _this2 = this;
      return this.getExperimentData(experimentId).dependencies.map(function (dependencyId) {
        return _this2.getExperimentData(dependencyId);
      });
    }
  }, {
    key: "getExperimentSelect",
    value: function getExperimentSelect(experimentId) {
      return this.elements.selects.find(function (select) {
        return select.matches("[data-experiment-id=\"".concat(experimentId, "\"]"));
      });
    }
  }, {
    key: "setExperimentState",
    value: function setExperimentState(experimentId, state) {
      this.getExperimentSelect(experimentId).value = state;
    }
  }, {
    key: "getExperimentActualState",
    value: function getExperimentActualState(experimentId) {
      var _this$getExperimentSe;
      var state = (_this$getExperimentSe = this.getExperimentSelect(experimentId)) === null || _this$getExperimentSe === void 0 ? void 0 : _this$getExperimentSe.value;
      if (!state) {
        return this.getExperimentData(experimentId).state;
      }
      if (state !== STATE_DEFAULT) {
        return state;
      }

      // Normalize the "default" state to the actual state value.
      return this.isExperimentActiveByDefault(experimentId) ? STATE_ACTIVE : STATE_INACTIVE;
    }
  }, {
    key: "isExperimentActive",
    value: function isExperimentActive(experimentId) {
      return this.getExperimentActualState(experimentId) === STATE_ACTIVE;
    }
  }, {
    key: "isExperimentActiveByDefault",
    value: function isExperimentActiveByDefault(experimentId) {
      return this.getExperimentData(experimentId).default === STATE_ACTIVE;
    }
  }, {
    key: "areAllDependenciesActive",
    value: function areAllDependenciesActive(dependencies) {
      var _this3 = this;
      return dependencies.every(function (dependency) {
        return _this3.isExperimentActive(dependency.name);
      });
    }
  }, {
    key: "deactivateDependantExperiments",
    value: function deactivateDependantExperiments(experimentId) {
      var _this4 = this;
      Object.entries(elementorAdminConfig.experiments).forEach(function (_ref2) {
        var _ref3 = (0, _slicedToArray2.default)(_ref2, 2),
          id = _ref3[0],
          experimentData = _ref3[1];
        var isDependant = experimentData.dependencies.includes(experimentId),
          isActive = _this4.getExperimentActualState(id) === STATE_ACTIVE;
        if (isDependant && isActive) {
          _this4.setExperimentState(id, STATE_INACTIVE);
        }
      });
    }
  }, {
    key: "shouldShowDependenciesDialog",
    value: function shouldShowDependenciesDialog(experimentId) {
      var dependencies = this.getExperimentDependencies(experimentId);
      return !this.areAllDependenciesActive(dependencies);
    }
  }, {
    key: "shouldShowDeactivationDialog",
    value: function shouldShowDeactivationDialog(experimentId) {
      var getExperimentData = this.getExperimentData(experimentId),
        isInitialStateActive = getExperimentData.state === STATE_ACTIVE || getExperimentData.state === STATE_DEFAULT && getExperimentData.default === STATE_ACTIVE,
        hasMessage = !!this.getMessage(experimentId, 'on_deactivate');
      return hasMessage && isInitialStateActive;
    }
  }, {
    key: "showDialog",
    value: function showDialog(dialog) {
      return elementorCommon.dialogsManager.createWidget('confirm', {
        id: 'e-experiments-messages-dialog',
        headerMessage: dialog.headerMessage,
        message: dialog.message,
        position: {
          my: 'center center',
          at: 'center center'
        },
        strings: {
          confirm: dialog.strings.confirm,
          cancel: dialog.strings.cancel
        },
        hide: {
          onOutsideClick: false,
          onBackgroundClick: false,
          onEscKeyPress: false
        },
        onConfirm: dialog.onConfirm,
        onCancel: dialog.onCancel
      }).show();
    }
  }, {
    key: "getSiteLanguageCode",
    value: function getSiteLanguageCode() {
      var languageCode = document.querySelector('html').getAttribute('lang');
      return languageCode !== null && languageCode !== void 0 ? languageCode : 'en'; // Fallback to English if no language code found.
    }
  }, {
    key: "formatDependenciesList",
    value: function formatDependenciesList(dependencies) {
      var dependenciesTitles = dependencies.map(function (d) {
        return d.title;
      });
      var languageCode = this.getSiteLanguageCode();
      return new Intl.ListFormat(languageCode).format(dependenciesTitles);
    }
  }, {
    key: "showDependenciesDialog",
    value: function showDependenciesDialog(experimentId) {
      var _this5 = this;
      var experiment = this.getExperimentData(experimentId),
        experimentName = experiment.title,
        dependenciesList = this.formatDependenciesList(this.getExperimentDependencies(experimentId));

      // Translators: %1$s: Experiment title, %2$s: Comma-separated dependencies list
      var message = __('In order to use %1$s, first you need to activate %2$s.', 'elementor').replace('%1$s', "<strong>".concat(experimentName, "</strong>")).replace('%2$s', dependenciesList);
      this.showDialog({
        message: message,
        headerMessage: __('First, activate another experiment.', 'elementor'),
        strings: {
          confirm: __('Activate', 'elementor'),
          cancel: __('Cancel', 'elementor')
        },
        onConfirm: function onConfirm() {
          _this5.getExperimentDependencies(experimentId).forEach(function (dependency) {
            _this5.setExperimentState(dependency.name, STATE_ACTIVE);
          });
          _this5.elements.submit.click();
        },
        onCancel: function onCancel() {
          _this5.setExperimentState(experimentId, STATE_INACTIVE);
        }
      });
    }
  }, {
    key: "showDeactivationDialog",
    value: function showDeactivationDialog(experimentId) {
      var _this6 = this;
      this.showDialog({
        message: this.getMessage(experimentId, 'on_deactivate'),
        headerMessage: __('Are you sure?', 'elementor'),
        strings: {
          confirm: __('Deactivate', 'elementor'),
          cancel: __('Cancel', 'elementor')
        },
        onConfirm: function onConfirm() {
          _this6.setExperimentState(experimentId, STATE_INACTIVE);
          _this6.deactivateDependantExperiments(experimentId);
          _this6.elements.submit.click();
        },
        onCancel: function onCancel() {
          _this6.setExperimentState(experimentId, STATE_ACTIVE);
        }
      });
    }
  }, {
    key: "getMessage",
    value: function getMessage(experimentId, messageId) {
      var _this$getExperimentDa;
      return (_this$getExperimentDa = this.getExperimentData(experimentId)) === null || _this$getExperimentDa === void 0 ? void 0 : _this$getExperimentDa.messages[messageId];
    }
  }]);
}();

/***/ }),

/***/ "../core/experiments/assets/js/admin/module.js":
/*!*****************************************************!*\
  !*** ../core/experiments/assets/js/admin/module.js ***!
  \*****************************************************/
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
var _experimentsMessages = _interopRequireDefault(__webpack_require__(/*! ./behaviors/experiments-messages */ "../core/experiments/assets/js/admin/behaviors/experiments-messages.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var ExperimentsModule = exports["default"] = /*#__PURE__*/function (_elementorModules$Vie) {
  function ExperimentsModule() {
    (0, _classCallCheck2.default)(this, ExperimentsModule);
    return _callSuper(this, ExperimentsModule, arguments);
  }
  (0, _inherits2.default)(ExperimentsModule, _elementorModules$Vie);
  return (0, _createClass2.default)(ExperimentsModule, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          experimentIndicators: '.e-experiment__title__indicator',
          experimentForm: '#elementor-settings-form',
          experimentSelects: '.e-experiment__select',
          experimentsButtons: '.e-experiment__button'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getSettings = this.getSettings(),
        selectors = _this$getSettings.selectors;
      return {
        $experimentIndicators: jQuery(selectors.experimentIndicators),
        $experimentForm: jQuery(selectors.experimentForm),
        $experimentSelects: jQuery(selectors.experimentSelects),
        $experimentsButtons: jQuery(selectors.experimentsButtons)
      };
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;
      this.elements.$experimentsButtons.on('click', function (event) {
        return _this.onExperimentsButtonsClick(event);
      });
    }
  }, {
    key: "onExperimentsButtonsClick",
    value: function onExperimentsButtonsClick(event) {
      var submitButton = jQuery(event.currentTarget);
      this.elements.$experimentSelects.val(submitButton.val());
      this.elements.$experimentForm.find('#submit').trigger('click');
    }
  }, {
    key: "addTipsy",
    value: function addTipsy($element) {
      $element.tipsy({
        gravity: 's',
        offset: 8,
        title: function title() {
          return this.getAttribute('data-tooltip');
        }
      });
    }
  }, {
    key: "addIndicatorsTooltips",
    value: function addIndicatorsTooltips() {
      var _this2 = this;
      this.elements.$experimentIndicators.each(function (index, experimentIndicator) {
        return _this2.addTipsy(jQuery(experimentIndicator));
      });
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _this3 = this;
      _superPropGet(ExperimentsModule, "onInit", this, 3)([]);
      this.experimentsDependency = new _experimentsMessages.default({
        selects: this.elements.$experimentSelects.toArray(),
        submit: this.elements.$experimentForm.find('#submit').get(0)
      });
      this.experimentsDependency.bindEvents();
      if (this.elements.$experimentIndicators.length) {
        import(/* webpackIgnore: true */"".concat(elementorCommon.config.urls.assets, "lib/tipsy/tipsy.min.js?ver=1.0.0")).then(function () {
          return _this3.addIndicatorsTooltips();
        });
      }
    }
  }]);
}(elementorModules.ViewModule);

/***/ }),

/***/ "../modules/floating-buttons/assets/js/floating-buttons/admin/floating-buttons.js":
/*!****************************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/floating-buttons/admin/floating-buttons.js ***!
  \****************************************************************************************/
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
var _menuHandler = _interopRequireDefault(__webpack_require__(/*! elementor-admin/menu-handler */ "../assets/dev/js/admin/menu-handler.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var FloatingButtonsHandler = exports["default"] = /*#__PURE__*/function (_AdminMenuHandler) {
  function FloatingButtonsHandler() {
    (0, _classCallCheck2.default)(this, FloatingButtonsHandler);
    return _callSuper(this, FloatingButtonsHandler, arguments);
  }
  (0, _inherits2.default)(FloatingButtonsHandler, _AdminMenuHandler);
  return (0, _createClass2.default)(FloatingButtonsHandler, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var pageName = 'e-floating-buttons',
        adminMenuSelectors = {
          // The escaping is done because jQuery requires it for selectors.
          contactPagesTablePage: 'a[href="edit.php?post_type=' + pageName + '"]',
          contactPagesAddNewPage: 'a[href="edit.php?post_type=elementor_library&page=' + pageName + '"]'
        };
      return {
        selectors: {
          addButton: '.page-title-action:first',
          templatesMenuItem: '.menu-icon-elementor_library',
          contactPagesMenuItem: "".concat(adminMenuSelectors.contactPagesTablePage, ", ").concat(adminMenuSelectors.contactPagesAddNewPage)
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors'),
        elements = _superPropGet(FloatingButtonsHandler, "getDefaultElements", this, 3)([]);
      elements.$templatesMenuItem = jQuery(selectors.templatesMenuItem);
      elements.$contactPagesMenuItem = jQuery(selectors.contactPagesMenuItem);
      return elements;
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _elementorAdminConfig;
      _superPropGet(FloatingButtonsHandler, "onInit", this, 3)([]);
      var settings = this.getSettings(),
        isContactPagesTablePage = !!window.location.href.includes(settings.paths.contactPagesTablePage),
        isContactPagesTrashPage = !!window.location.href.includes(settings.paths.contactPagesTrashPage),
        isLContactPagesCreateYourFirstPage = !!window.location.href.includes(settings.paths.contactPagesAddNewPage);

      // We need this because there is a complex bug in the WordPress admin menu that causes the Contact Menu to be broken
      // When the links page has at least one post and the contact page has none.
      if ((_elementorAdminConfig = elementorAdminConfig.urls) !== null && _elementorAdminConfig !== void 0 && _elementorAdminConfig.viewContactPageUrl) {
        this.elements.$templatesMenuItem.find('li.submenu-e-contact a').attr('href', elementorAdminConfig.urls.viewContactPageUrl);
      }
      if (isContactPagesTablePage || isContactPagesTrashPage || isLContactPagesCreateYourFirstPage) {
        this.highlightTopLevelMenuItem(this.elements.$templatesMenuItem, this.elements.$pagesMenuItemAndLink);
        this.highlightSubMenuItem(this.elements.$contactPagesMenuItem);
        jQuery(settings.selectors.addButton).attr('href', elementorAdminConfig.urls.addNewLinkUrlContact);
      }
    }
  }]);
}(_menuHandler.default);

/***/ }),

/***/ "../modules/floating-buttons/assets/js/floating-buttons/admin/module.js":
/*!******************************************************************************!*\
  !*** ../modules/floating-buttons/assets/js/floating-buttons/admin/module.js ***!
  \******************************************************************************/
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
var _floatingButtons = _interopRequireDefault(__webpack_require__(/*! ./floating-buttons */ "../modules/floating-buttons/assets/js/floating-buttons/admin/floating-buttons.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_elementorModules$Mod) {
  function _default() {
    var _this;
    (0, _classCallCheck2.default)(this, _default);
    _this = _callSuper(this, _default);
    elementorCommon.elements.$window.on('elementor/admin/init', function () {
      _this.runHandler();
    });
    return _this;
  }
  (0, _inherits2.default)(_default, _elementorModules$Mod);
  return (0, _createClass2.default)(_default, [{
    key: "runHandler",
    value: function runHandler() {
      var pageNameContact = 'e-floating-buttons',
        paths = {
          contactPagesTablePage: 'edit.php?post_type=' + pageNameContact,
          contactPagesAddNewPage: 'edit.php?post_type=elementor_library&page=' + pageNameContact,
          contactPagesTrashPage: 'edit.php?post_status=trash&post_type=' + pageNameContact
        },
        args = {
          paths: paths
        };

      // This class modifies elements in the WordPress admin that are rendered "wrong" by the WordPress core
      // and could not be modified in the backend.
      new _floatingButtons.default(args);
    }
  }]);
}(elementorModules.Module);

/***/ }),

/***/ "../modules/landing-pages/assets/js/admin/landing-pages.js":
/*!*****************************************************************!*\
  !*** ../modules/landing-pages/assets/js/admin/landing-pages.js ***!
  \*****************************************************************/
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
var _menuHandler = _interopRequireDefault(__webpack_require__(/*! elementor-admin/menu-handler */ "../assets/dev/js/admin/menu-handler.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _superPropGet(t, o, e, r) { var p = (0, _get2.default)((0, _getPrototypeOf2.default)(1 & r ? t.prototype : t), o, e); return 2 & r && "function" == typeof p ? function (t) { return p.apply(e, t); } : p; }
var LandingPagesHandler = exports["default"] = /*#__PURE__*/function (_AdminMenuHandler) {
  function LandingPagesHandler() {
    (0, _classCallCheck2.default)(this, LandingPagesHandler);
    return _callSuper(this, LandingPagesHandler, arguments);
  }
  (0, _inherits2.default)(LandingPagesHandler, _AdminMenuHandler);
  return (0, _createClass2.default)(LandingPagesHandler, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var pageName = 'e-landing-page',
        adminMenuSelectors = {
          // The escaping is done because jQuery requires it for selectors.
          landingPagesTablePage: 'a[href="edit.php?post_type=' + pageName + '"]',
          landingPagesAddNewPage: 'a[href="edit.php?post_type=elementor_library&page=' + pageName + '"]'
        };
      return {
        selectors: {
          addButton: '.page-title-action:first',
          pagesMenuItemAndLink: '#menu-pages, #menu-pages > a',
          landingPagesMenuItem: "".concat(adminMenuSelectors.landingPagesTablePage, ", ").concat(adminMenuSelectors.landingPagesAddNewPage),
          templatesMenuItem: '.menu-icon-elementor_library'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var selectors = this.getSettings('selectors'),
        elements = _superPropGet(LandingPagesHandler, "getDefaultElements", this, 3)([]);
      elements.$landingPagesMenuItem = jQuery(selectors.landingPagesMenuItem);
      elements.$templatesMenuItem = jQuery(selectors.templatesMenuItem);
      elements.$pagesMenuItemAndLink = jQuery(selectors.pagesMenuItemAndLink);
      return elements;
    }
  }, {
    key: "onInit",
    value: function onInit() {
      _superPropGet(LandingPagesHandler, "onInit", this, 3)([]);
      var settings = this.getSettings(),
        isLandingPagesTablePage = !!window.location.href.includes(settings.paths.landingPagesTablePage),
        isLandingPagesTrashPage = !!window.location.href.includes(settings.paths.landingPagesTrashPage),
        isLandingPagesCreateYourFirstPage = !!window.location.href.includes(settings.paths.landingPagesAddNewPage);

      // If the current page is a Landing Pages Page (the Posts Table page, "Create Your First.." page, or a native
      // WordPress dashboard page edit screen when using WordPress' Classic Editor).
      if (isLandingPagesTablePage || isLandingPagesTrashPage || isLandingPagesCreateYourFirstPage || settings.isLandingPageAdminEdit) {
        // Make sure the active admin top level menu item is 'Templates', and not 'Pages'.
        this.highlightTopLevelMenuItem(this.elements.$templatesMenuItem, this.elements.$pagesMenuItemAndLink);
        this.highlightSubMenuItem(this.elements.$landingPagesMenuItem);

        // Overwrite the 'Add New' button at the top of the page to open in Elementor with the library module open.
        jQuery(settings.selectors.addButton).attr('href', elementorAdminConfig.urls.addNewLandingPageUrl);
      }
    }
  }]);
}(_menuHandler.default);

/***/ }),

/***/ "../modules/landing-pages/assets/js/admin/module.js":
/*!**********************************************************!*\
  !*** ../modules/landing-pages/assets/js/admin/module.js ***!
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
var _landingPages = _interopRequireDefault(__webpack_require__(/*! ./landing-pages */ "../modules/landing-pages/assets/js/admin/landing-pages.js"));
function _callSuper(t, o, e) { return o = (0, _getPrototypeOf2.default)(o), (0, _possibleConstructorReturn2.default)(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], (0, _getPrototypeOf2.default)(t).constructor) : o.apply(t, e)); }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
var _default = exports["default"] = /*#__PURE__*/function (_elementorModules$Mod) {
  function _default() {
    var _this;
    (0, _classCallCheck2.default)(this, _default);
    _this = _callSuper(this, _default);
    elementorCommon.elements.$window.on('elementor/admin/init', function () {
      _this.runHandler();
    });
    return _this;
  }
  (0, _inherits2.default)(_default, _elementorModules$Mod);
  return (0, _createClass2.default)(_default, [{
    key: "runHandler",
    value: function runHandler() {
      var _elementorAdmin$confi, _elementorAdmin$confi2;
      var pageName = 'e-landing-page',
        paths = {
          landingPagesTablePage: 'edit.php?post_type=' + pageName,
          landingPagesAddNewPage: 'edit.php?post_type=elementor_library&page=' + pageName,
          landingPagesTrashPage: 'edit.php?post_status=trash&post_type=' + pageName
        },
        args = {
          path: (_elementorAdmin$confi = elementorAdmin.config.landingPages) !== null && _elementorAdmin$confi !== void 0 && _elementorAdmin$confi.landingPagesHasPages ? paths.landingPagesTablePage : paths.landingPagesAddNewPage,
          isLandingPageAdminEdit: (_elementorAdmin$confi2 = elementorAdmin.config.landingPages) === null || _elementorAdmin$confi2 === void 0 ? void 0 : _elementorAdmin$confi2.isLandingPageAdminEdit,
          paths: paths
        };

      // This class modifies elements in the WordPress admin that are rendered "wrong" by the WordPress core
      // and could not be modified in the backend.
      new _landingPages.default(args);
    }
  }]);
}(elementorModules.Module);

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
/*!***************************************!*\
  !*** ../assets/dev/js/admin/admin.js ***!
  \***************************************/
/* provided dependency */ var __ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n")["__"];


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime/helpers/interopRequireDefault */ "../node_modules/@babel/runtime/helpers/interopRequireDefault.js");
var _regenerator = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/regenerator */ "../node_modules/@babel/runtime/regenerator/index.js"));
var _asyncToGenerator2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "../node_modules/@babel/runtime/helpers/asyncToGenerator.js"));
var _slicedToArray2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "../node_modules/@babel/runtime/helpers/slicedToArray.js"));
var _module = _interopRequireDefault(__webpack_require__(/*! elementor/modules/landing-pages/assets/js/admin/module */ "../modules/landing-pages/assets/js/admin/module.js"));
var _module2 = _interopRequireDefault(__webpack_require__(/*! elementor/core/experiments/assets/js/admin/module */ "../core/experiments/assets/js/admin/module.js"));
var _environment = _interopRequireDefault(__webpack_require__(/*! ../../../../core/common/assets/js/utils/environment */ "../core/common/assets/js/utils/environment.js"));
var _events = _interopRequireDefault(__webpack_require__(/*! elementor-utils/events */ "../assets/dev/js/utils/events.js"));
var _filesUploadHandler = _interopRequireDefault(__webpack_require__(/*! ../editor/utils/files-upload-handler */ "../assets/dev/js/editor/utils/files-upload-handler.js"));
var _templateControls = _interopRequireDefault(__webpack_require__(/*! ./new-template/template-controls.js */ "../assets/dev/js/admin/new-template/template-controls.js"));
var _jsonUploadWarningMessage = __webpack_require__(/*! elementor-utils/json-upload-warning-message */ "../assets/dev/js/utils/json-upload-warning-message.js");
var _module3 = _interopRequireDefault(__webpack_require__(/*! elementor/modules/floating-buttons/assets/js/floating-buttons/admin/module */ "../modules/floating-buttons/assets/js/floating-buttons/admin/module.js"));
(function ($) {
  var ElementorAdmin = elementorModules.ViewModule.extend({
    maintenanceMode: null,
    config: elementorAdminConfig,
    getDefaultElements: function getDefaultElements() {
      var elements = {
        $switchMode: $('#elementor-switch-mode'),
        $goToEditLink: $('#elementor-go-to-edit-page-link'),
        $switchModeInput: $('#elementor-switch-mode-input'),
        $switchModeButton: $('#elementor-switch-mode-button'),
        $elementorLoader: $('.elementor-loader'),
        $builderEditor: $('#elementor-editor'),
        $importButton: $('#elementor-import-template-trigger'),
        $importNowButton: $('#e-import-template-action'),
        $importArea: $('#elementor-import-template-area'),
        $importForm: $('#elementor-import-template-form'),
        $importFormFileInput: $('#elementor-import-template-form input[type="file"]'),
        $settingsForm: $('#elementor-settings-form'),
        $settingsTabsWrapper: $('#elementor-settings-tabs-wrapper'),
        $menuGetHelpLink: $('a[href="admin.php?page=go_knowledge_base_site"]'),
        $menuGoProLink: $('a[href="admin.php?page=go_elementor_pro"]'),
        $reMigrateGlobalsButton: $('.elementor-re-migrate-globals-button')
      };
      elements.$settingsFormPages = elements.$settingsForm.find('.elementor-settings-form-page');
      elements.$activeSettingsPage = elements.$settingsFormPages.filter('.elementor-active');
      elements.$settingsTabs = elements.$settingsTabsWrapper.children();
      elements.$activeSettingsTab = elements.$settingsTabs.filter('.nav-tab-active');
      return elements;
    },
    toggleStatus: function toggleStatus() {
      var isElementorMode = this.isElementorMode();
      elementorCommon.elements.$body.toggleClass('elementor-editor-active', isElementorMode).toggleClass('elementor-editor-inactive', !isElementorMode);
    },
    bindEvents: function bindEvents() {
      var self = this;
      self.elements.$switchModeButton.on('click', function (event) {
        event.preventDefault();
        if (self.isElementorMode()) {
          elementorCommon.dialogsManager.createWidget('confirm', {
            message: __('Please note that you are switching to WordPress default editor. Your current layout, design and content might break.', 'elementor'),
            headerMessage: __('Back to WordPress Editor', 'elementor'),
            strings: {
              confirm: __('Continue', 'elementor'),
              cancel: __('Cancel', 'elementor')
            },
            defaultOption: 'confirm',
            onConfirm: function onConfirm() {
              self.elements.$switchModeInput.val('');
              self.toggleStatus();
            }
          }).show();
        } else {
          self.elements.$switchModeInput.val(true);
          var $wpTitle = $('#title');
          if (!$wpTitle.val()) {
            $wpTitle.val('Elementor #' + $('#post_ID').val());
          }
          if (wp.autosave) {
            wp.autosave.server.triggerSave();
          }
          self.animateLoader();
          $(document).on('heartbeat-tick.autosave', function () {
            elementorCommon.elements.$window.off('beforeunload.edit-post');
            location.href = self.elements.$goToEditLink.attr('href');
          });
          self.toggleStatus();
        }
      });
      self.elements.$goToEditLink.on('click', function () {
        self.animateLoader();
      });
      $('.e-notice--dismissible').on('click', '.e-notice__dismiss, .e-notice-dismiss', function (event) {
        event.preventDefault();
        var $wrapperElm = $(this).closest('.e-notice--dismissible');
        $.post(ajaxurl, {
          action: 'elementor_set_admin_notice_viewed',
          notice_id: $wrapperElm.data('notice_id'),
          _wpnonce: $wrapperElm.data('nonce')
        });
        $wrapperElm.fadeTo(100, 0, function () {
          $wrapperElm.slideUp(100, function () {
            $wrapperElm.remove();
          });
        });
      });
      $('.e-notice--cta.e-notice--dismissible[data-notice_id="plugin_image_optimization"] a.e-button--cta').on('click', function () {
        elementorCommon.ajax.addRequest('elementor_image_optimization_campaign', {
          data: {
            source: 'io-wp-media-library-install'
          }
        });
      });
      $('.e-a-apps .e-a-item[data-plugin="image-optimization/image-optimization.php"] a.e-btn').on('click', function () {
        elementorCommon.ajax.addRequest('elementor_image_optimization_campaign', {
          data: {
            source: 'io-esetting-addons-install'
          }
        });
      });
      $('.e-notice--cta.e-notice--dismissible[data-notice_id="site_mailer_promotion"] a.e-button--cta').on('click', function () {
        var isWcNotice = $(this).closest('.e-notice').hasClass('sm-notice-wc');
        elementorCommon.ajax.addRequest('elementor_core_site_mailer_campaign', {
          data: {
            source: isWcNotice ? 'sm-core-woo-install' : 'sm-core-form-install'
          }
        });
      });
      $('#elementor-clear-cache-button').on('click', function (event) {
        event.preventDefault();
        var $thisButton = $(this);
        $thisButton.removeClass('success').addClass('loading');
        $.post(ajaxurl, {
          action: 'elementor_clear_cache',
          _nonce: $thisButton.data('nonce')
        }).done(function () {
          $thisButton.removeClass('loading').addClass('success');
        });
      });
      $('#elementor-library-sync-button').on('click', function (event) {
        event.preventDefault();
        var $thisButton = $(this);
        $thisButton.removeClass('success').addClass('loading');
        $.post(ajaxurl, {
          action: 'elementor_reset_library',
          _nonce: $thisButton.data('nonce')
        }).done(function () {
          $thisButton.removeClass('loading').addClass('success');
        });
      });
      $('#elementor-recreate-kit-button').on('click', function (event) {
        event.preventDefault();
        var $thisButton = $(this);
        $thisButton.removeClass('success error').addClass('loading').next('.e-recreate-kit-error-message').remove();
        $.post(ajaxurl, {
          action: 'elementor_recreate_kit',
          _nonce: $thisButton.data('nonce')
        }).done(function () {
          $thisButton.removeClass('loading').addClass('success');
        }).fail(function (_ref) {
          var _responseJSON$data;
          var responseJSON = _ref.responseJSON;
          $thisButton.removeClass('loading').addClass('error');
          if ((_responseJSON$data = responseJSON.data) !== null && _responseJSON$data !== void 0 && _responseJSON$data.message) {
            $thisButton.after("<div class=\"e-recreate-kit-error-message\">".concat(responseJSON.data.message, "</div>"));
          }
        });
      });
      $('#elementor-replace-url-button').on('click', function (event) {
        event.preventDefault();
        var $this = $(this),
          $tr = $this.parents('tr'),
          $from = $tr.find('[name="from"]'),
          $to = $tr.find('[name="to"]');
        $this.removeClass('success').addClass('loading');
        $.post(ajaxurl, {
          action: 'elementor_replace_url',
          from: $from.val(),
          to: $to.val(),
          _nonce: $this.data('nonce')
        }).done(function (response) {
          $this.removeClass('loading');
          if (response.success) {
            $this.addClass('success');
          }
          elementorCommon.dialogsManager.createWidget('alert', {
            message: response.data
          }).show();
        });
      });
      $('#elementor_upgrade_fa_button').on('click', function (event) {
        event.preventDefault();
        var $updateButton = $(this);
        $updateButton.addClass('loading');
        elementorCommon.dialogsManager.createWidget('confirm', {
          id: 'confirm_fa_migration_admin_modal',
          message: __('I understand that by upgrading to Font Awesome 5,', 'elementor') + '<br>' + __('I acknowledge that some changes may affect my website and that this action cannot be undone.', 'elementor'),
          headerMessage: __('Font Awesome 5 Migration', 'elementor'),
          strings: {
            confirm: __('Continue', 'elementor'),
            cancel: __('Cancel', 'elementor')
          },
          defaultOption: 'confirm',
          onConfirm: function onConfirm() {
            $updateButton.removeClass('error').addClass('loading');
            var _$updateButton$data = $updateButton.data(),
              _nonce = _$updateButton$data._nonce,
              action = _$updateButton$data.action,
              redirectUrl = _$updateButton$data.redirectUrl;
            $.post(ajaxurl, {
              action: action,
              _nonce: _nonce
            }).done(function (response) {
              $updateButton.removeClass('loading').addClass('success');
              var messageElement = document.createElement('p');
              messageElement.appendChild(document.createTextNode(response.data.message));
              $('#elementor_upgrade_fa_button').parent().append(messageElement);
              if (redirectUrl) {
                location.href = decodeURIComponent(redirectUrl);
                return;
              }
              history.go(-1);
            }).fail(function () {
              $updateButton.removeClass('loading').addClass('error');
            });
          },
          onCancel: function onCancel() {
            $updateButton.removeClass('loading').addClass('error');
          }
        }).show();
      });
      self.elements.$settingsTabs.on({
        click: function click(event) {
          event.preventDefault();
          event.currentTarget.focus(); // Safari does not focus the tab automatically
        },
        focus: function focus() {
          // Using focus event to enable navigation by tab key
          var hrefWithoutHash = location.href.replace(/#.*/, '');
          history.pushState({}, '', hrefWithoutHash + this.hash);
          self.goToSettingsTabFromHash();
        }
      });
      $('select.elementor-rollback-select').on('change', function () {
        var $this = $(this),
          $rollbackButton = $this.next('.elementor-rollback-button'),
          placeholderText = $rollbackButton.data('placeholder-text'),
          placeholderUrl = $rollbackButton.data('placeholder-url');
        $rollbackButton.html(placeholderText.replace('{VERSION}', $this.val()));
        $rollbackButton.attr('href', placeholderUrl.replace('VERSION', $this.val()));
      }).trigger('change');
      $('.elementor-rollback-button').on('click', function (event) {
        event.preventDefault();
        var $this = $(this);
        elementorCommon.dialogsManager.createWidget('confirm', {
          headerMessage: __('Rollback to Previous Version', 'elementor'),
          message: __('Are you sure you want to reinstall previous version?', 'elementor'),
          strings: {
            confirm: __('Continue', 'elementor'),
            cancel: __('Cancel', 'elementor')
          },
          onConfirm: function onConfirm() {
            $this.addClass('loading');
            location.href = $this.attr('href');
          }
        }).show();
      });
      self.elements.$reMigrateGlobalsButton.on('click', function (event) {
        event.preventDefault();
        var $this = $(event.currentTarget);
        elementorCommon.dialogsManager.createWidget('confirm', {
          headerMessage: __('Migrate to v3.0', 'elementor'),
          message: __('Please note that this process will revert all changes made to Global Colors and Fonts since upgrading to v3.x.', 'elementor'),
          strings: {
            confirm: __('Continue', 'elementor'),
            cancel: __('Cancel', 'elementor')
          },
          onConfirm: function onConfirm() {
            $this.removeClass('success').addClass('loading');
            elementorCommon.ajax.addRequest('re_migrate_globals', {
              success: function success() {
                return $this.removeClass('loading').addClass('success');
              }
            });
          }
        }).show();
      });
      $('.elementor_google_font select').on('change', function () {
        $('.elementor_font_display').toggle('1' === $(this).val());
      }).trigger('change');
    },
    onInit: function onInit() {
      elementorModules.ViewModule.prototype.onInit.apply(this, arguments);
      this.initTemplatesImport();
      this.initMaintenanceMode();
      this.goToSettingsTabFromHash();
      this.openLinksInNewTab();
      this.addUserAgentClasses();
      this.roleManager.init();
      if (elementorCommon.config.experimentalFeatures['landing-pages']) {
        new _module.default();
      }
      if (elementorCommon.config.experimentalFeatures.container) {
        new _module3.default();
      }
      this.templateControls = new _templateControls.default();
      new _module2.default();
    },
    addUserAgentClasses: function addUserAgentClasses() {
      var body = document.querySelector('body');
      Object.entries(_environment.default).forEach(function (_ref2) {
        var _ref3 = (0, _slicedToArray2.default)(_ref2, 2),
          key = _ref3[0],
          value = _ref3[1];
        if (!value) {
          return;
        }
        body.classList.add('e--ua-' + key);
      });
    },
    /**
     * Open Links in New Tab
     *
     * Adds a `target="_blank"` attribute to the Admin Dashboard menu items specified in the `elements` array,
     * if the elements are found in the DOM. The items in the `elements` array should be jQuery instances.
     *
     * @since 3.6.0
     */
    openLinksInNewTab: function openLinksInNewTab() {
      var elements = [this.elements.$menuGetHelpLink, this.elements.$menuGoProLink];
      elements.forEach(function ($element) {
        // Only add the attribute if the element is found.
        if ($element.length) {
          $element.attr('target', '_blank');
        }
      });
    },
    initTemplatesImport: function initTemplatesImport() {
      var _elementorAdminConfig, _elementorAdminConfig2;
      var canImport = elementorAdminConfig.user.is_administrator || ((_elementorAdminConfig = (_elementorAdminConfig2 = elementorAdminConfig.user.restrictions) === null || _elementorAdminConfig2 === void 0 ? void 0 : _elementorAdminConfig2.includes('json-upload')) !== null && _elementorAdminConfig !== void 0 ? _elementorAdminConfig : false);
      if (!canImport || !elementorCommon.elements.$body.hasClass('post-type-elementor_library')) {
        return;
      }
      if (0 === this.elements.$importNowButton.length) {
        return;
      }
      var self = this,
        $importForm = self.elements.$importForm,
        $importButton = self.elements.$importButton,
        $importArea = self.elements.$importArea,
        $importNowButton = self.elements.$importNowButton,
        $importFormFileInput = self.elements.$importFormFileInput;
      self.elements.$formAnchor = $('.wp-header-end');
      $('#wpbody-content').find('.page-title-action').last().after($importButton);
      self.elements.$formAnchor.after($importArea);
      $importButton.on('click', function () {
        $('#elementor-import-template-area').toggle();
      });
      var messages = {
        jsonUploadWarning: {
          shown: false
        },
        enableUnfilteredFiles: {
          shown: false
        }
      };
      var originalButtonValue = $importNowButton[0].value;
      $importForm.on('submit', /*#__PURE__*/function () {
        var _ref4 = (0, _asyncToGenerator2.default)(/*#__PURE__*/_regenerator.default.mark(function _callee(event) {
          var hasImportedFiles, areUnfilteredFilesEnabled, enableUnfilteredFilesModal, _t;
          return _regenerator.default.wrap(function (_context) {
            while (1) switch (_context.prev = _context.next) {
              case 0:
                $importNowButton[0].disabled = true;
                $importNowButton[0].value = __('Importing...', 'elementor');
                if (messages.jsonUploadWarning.shown) {
                  _context.next = 5;
                  break;
                }
                event.preventDefault();
                _context.prev = 1;
                _context.next = 2;
                return (0, _jsonUploadWarningMessage.showJsonUploadWarningMessageIfNeeded)({
                  IntroductionClass: window.elementorModules.admin.utils.Introduction,
                  introductionMap: window.elementorAdmin.config.user.introduction,
                  waitForSetViewed: true
                });
              case 2:
                messages.jsonUploadWarning.shown = true;
                $importForm.trigger('submit');
                _context.next = 4;
                break;
              case 3:
                _context.prev = 3;
                _t = _context["catch"](1);
                $importNowButton[0].disabled = false;
                $importNowButton[0].value = originalButtonValue;
              case 4:
                return _context.abrupt("return");
              case 5:
                hasImportedFiles = $importFormFileInput[0].files.length;
                areUnfilteredFilesEnabled = elementorCommon.config.filesUpload.unfilteredFiles;
                if (!(hasImportedFiles && !areUnfilteredFilesEnabled && !messages.enableUnfilteredFiles.shown)) {
                  _context.next = 6;
                  break;
                }
                event.preventDefault();
                enableUnfilteredFilesModal = _filesUploadHandler.default.getUnfilteredFilesNotEnabledImportTemplateDialog(function () {
                  messages.enableUnfilteredFiles.shown = true;
                  $importForm.trigger('submit');
                });
                enableUnfilteredFilesModal.show();
                return _context.abrupt("return");
              case 6:
                messages.jsonUploadWarning.shown = false;
                messages.enableUnfilteredFiles.shown = false;
              case 7:
              case "end":
                return _context.stop();
            }
          }, _callee, null, [[1, 3]]);
        }));
        return function (_x) {
          return _ref4.apply(this, arguments);
        };
      }());
    },
    initMaintenanceMode: function initMaintenanceMode() {
      var MaintenanceMode = __webpack_require__(/*! elementor-admin/maintenance-mode */ "../assets/dev/js/admin/maintenance-mode.js");
      this.maintenanceMode = new MaintenanceMode();
    },
    isElementorMode: function isElementorMode() {
      return !!this.elements.$switchModeInput.val();
    },
    animateLoader: function animateLoader() {
      this.elements.$goToEditLink.addClass('elementor-animate');
    },
    goToSettingsTabFromHash: function goToSettingsTabFromHash() {
      var hash = location.hash.slice(1);
      if (hash) {
        this.goToSettingsTab(hash);
      }
    },
    goToSettingsTab: function goToSettingsTab(tabName) {
      var $pages = this.elements.$settingsFormPages;
      if (!$pages.length) {
        return;
      }
      var $activePage = $pages.filter('#' + tabName);
      this.elements.$activeSettingsPage.removeClass('elementor-active');
      this.elements.$activeSettingsTab.removeClass('nav-tab-active');
      var $activeTab = this.elements.$settingsTabs.filter('#elementor-settings-' + tabName);
      $activePage.addClass('elementor-active');
      $activeTab.addClass('nav-tab-active');
      this.elements.$settingsForm.attr('action', 'options.php#' + tabName);
      this.elements.$activeSettingsPage = $activePage;
      this.elements.$activeSettingsTab = $activeTab;
    },
    translate: function translate(stringKey, templateArgs) {
      return elementorCommon.translate(stringKey, null, templateArgs, this.config.i18n);
    },
    roleManager: {
      selectors: {
        body: 'elementor-role-manager',
        row: '.elementor-role-row',
        label: '.elementor-role-label',
        excludedIndicator: '.elementor-role-excluded-indicator',
        excludedField: 'input[name="elementor_exclude_user_roles[]"]',
        controlsContainer: '.elementor-role-controls',
        toggleHandle: '.elementor-role-toggle',
        arrowUp: 'dashicons-arrow-up',
        arrowDown: 'dashicons-arrow-down'
      },
      toggle: function toggle($trigger) {
        var self = this,
          $row = $trigger.closest(self.selectors.row),
          $toggleHandleIcon = $row.find(self.selectors.toggleHandle).find('.dashicons'),
          $controls = $row.find(self.selectors.controlsContainer);
        $controls.toggleClass('hidden');
        if ($controls.hasClass('hidden')) {
          $toggleHandleIcon.removeClass(self.selectors.arrowUp).addClass(self.selectors.arrowDown);
        } else {
          $toggleHandleIcon.removeClass(self.selectors.arrowDown).addClass(self.selectors.arrowUp);
        }
        self.updateLabel($row);
      },
      updateLabel: function updateLabel($row) {
        var self = this,
          $indicator = $row.find(self.selectors.excludedIndicator),
          excluded = $row.find(self.selectors.excludedField).is(':checked');
        if (excluded) {
          $indicator.html($indicator.data('excluded-label'));
        } else {
          $indicator.html('');
        }
        self.setAdvancedState($row, excluded);
      },
      setAdvancedState: function setAdvancedState($row, state) {
        var self = this,
          $controls = $row.find('input[type="checkbox"]').not(self.selectors.excludedField);
        $controls.each(function (index, input) {
          $(input).prop('disabled', state);
        });
      },
      bind: function bind() {
        var self = this;
        $(document).on('click', self.selectors.label + ',' + self.selectors.toggleHandle, function (event) {
          event.stopPropagation();
          event.preventDefault();
          self.toggle($(this));
        }).on('change', self.selectors.excludedField, function () {
          self.updateLabel($(this).closest(self.selectors.row));
        });
      },
      init: function init() {
        var self = this;
        if (!$('body[class*="' + self.selectors.body + '"]').length) {
          return;
        }
        self.bind();
        $(self.selectors.row).each(function (index, row) {
          self.updateLabel($(row));
        });
      }
    }
  });
  $(function () {
    window.elementorAdmin = new ElementorAdmin();
    _events.default.dispatch(elementorCommon.elements.$window, 'elementor/admin/init');
  });
})(jQuery);
})();

/******/ })()
;
//# sourceMappingURL=admin.js.map